<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Models\Court;
use App\Models\Reservation;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public const TIME_SLOTS = Reservation::TIME_SLOTS;

    public const PURPOSES = [
        'Latihan rutin',
        'Lomba / turnamen',
        'Pertandingan umum',
    ];

    public function index(Request $request): View
    {
        $reservations = Reservation::query()
            ->with('court')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return view('reservation.index', compact('reservations'));
    }

    public function create(Request $request): View
    {
        $courts = Court::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        $days = $this->upcomingDays();

        $court = $courts->firstWhere('slug', $request->query('court'));
        $day = $days->firstWhere('value', $request->query('date'));

        $slotStatuses = ($court && $day) ? $court->slotAvailability($day['value']) : [];
        $blocked = array_keys(array_filter($slotStatuses, fn (string $s): bool => $s !== 'available'));

        $time = in_array($request->query('time'), self::TIME_SLOTS, true)
            ? $request->query('time')
            : null;

        $requestedDuration = (int) $request->query('duration');
        $duration = ($requestedDuration >= 1 && $requestedDuration <= Reservation::MAX_DURATION)
            ? $requestedDuration
            : null;

        $slotError = null;
        $endTime = null;

        if ($court && $day && $time && $duration) {
            $endTime = CarbonImmutable::createFromFormat('H:i', $time)->addHours($duration)->format('H:i');
            $needed = array_filter(self::TIME_SLOTS, fn (string $s): bool => $s >= $time && $s < $endTime);

            if ($endTime > Reservation::DAY_END) {
                $slotError = 'Durasi melewati jam operasional (maksimal sampai '.Reservation::DAY_END.').';
            } elseif (array_intersect($needed, $blocked) !== []) {
                $slotError = 'Ada jam pada rentang itu yang sudah terisi. Pilih jam atau durasi lain.';
            }

            if ($slotError !== null) {
                $time = null;
                $duration = null;
                $endTime = null;
            }
        } elseif ($time !== null && in_array($time, $blocked, true)) {
            $time = null;
        }

        $step = match (true) {
            $court === null => 1,
            $day === null => 2,
            $time === null || $duration === null => 3,
            default => 4,
        };

        return view('reservation.create', [
            'step' => $step,
            'courts' => $courts,
            'court' => $court,
            'days' => $days,
            'day' => $day,
            'timeSlots' => self::TIME_SLOTS,
            'slotStatuses' => $slotStatuses,
            'bookedTimes' => $blocked,
            'time' => $time,
            'duration' => $duration,
            'durations' => range(1, Reservation::MAX_DURATION),
            'slotError' => $slotError,
            'endTime' => $endTime,
            'purposes' => self::PURPOSES,
            'stepLabels' => [
                'Pilih lapangan',
                'Pilih hari',
                'Pilih jam & durasi',
                'Data peminjam',
            ],
        ]);
    }

    public function store(StoreReservationRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $court = Court::query()
            ->where('slug', $data['court'])
            ->where('is_active', true)
            ->firstOrFail();

        $end = CarbonImmutable::createFromFormat('H:i', $data['time'])
            ->addHours((int) $data['duration'])
            ->format('H:i');

        $reservation = DB::transaction(
            function () use ($request, $data, $court, $end): ?Reservation {
                if ($court->hasConflict($data['date'], $data['time'], $end)) {
                    return null;
                }

                return $request->user()->reservations()->create([
                    'court_id' => $court->id,
                    'reservation_date' => $data['date'],
                    'start_time' => $data['time'],
                    'end_time' => $end,
                    'borrower_name' => $data['full_name'],
                    'class_institution' => $data['class_institution'],
                    'phone_number' => $data['phone_number'],
                    'participant_count' => $data['participant_count'],
                    'purpose' => $data['purpose'],
                    'notes' => $data['notes'] ?? null,
                    'status' => Reservation::STATUS_PENDING,
                ]);
            }
        );

        if ($reservation === null) {
            return back()
                ->withInput()
                ->withErrors(['time' => 'Jam tersebut baru saja terisi. Silakan pilih jam atau durasi lain.']);
        }

        return redirect()
            ->route('reservation.index')
            ->with('reservation_submitted', [
                'court' => $court->name,
                'date' => $reservation->reservation_date->locale('id')->translatedFormat('l, d F Y'),
                'time' => $reservation->timeRange(),
                'status' => 'Menunggu Persetujuan',
            ]);
    }

    /**
     * @return Collection<int, array{label: string, date: string, value: string, full: string}>
     */
    protected function upcomingDays(): Collection
    {
        return collect(range(0, 5))->map(function (int $offset): array {
            $date = CarbonImmutable::today()->addDays($offset)->locale('id');

            return [
                'label' => mb_strtoupper($date->translatedFormat('D')),
                'date' => $date->format('d'),
                'value' => $date->toDateString(),
                'full' => $date->translatedFormat('l, d F Y'),
            ];
        });
    }
}
