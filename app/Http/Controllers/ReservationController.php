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
    public const TIME_SLOTS = [
        '13:00',
        '14:00',
        '15:00',
        '16:00',
        '17:00'
    ];

    public const PURPOSES = [
        'Latihan rutin',
        'Lomba / turnamen',
        'Pertandingan umum'
    ];

    // ==========================================
    // MENAMPILKAN RESERVASI MILIK USER
    // ==========================================

    public function index(Request $request): View
    {
        $user = $request->user();

        $reservations = Reservation::query()
            ->with('court')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('reservation.index', compact('reservations'));
    }


    // ==========================================
    // FORM PEMBUATAN RESERVASI
    // ==========================================

    public function create(Request $request): View
    {
        $courts = Court::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        $days = $this->upcomingDays();

        $court = $courts->firstWhere(
            'slug',
            $request->query('court')
        );

        $day = $days->firstWhere(
            'value',
            $request->query('date')
        );

        $bookedTimes = ($court && $day)
            ? $court->bookedStartTimes($day['value'])
            : [];

        $requestedTime = $request->query('time');

        $time = in_array($requestedTime, self::TIME_SLOTS, true)
            && !in_array($requestedTime, $bookedTimes, true)
            ? $requestedTime
            : null;

        $step = match (true) {
            $court === null => 1,
            $day === null => 2,
            $time === null => 3,
            default => 4,
        };

        return view('reservation.create', [
            'step' => $step,
            'courts' => $courts,
            'court' => $court,
            'days' => $days,
            'day' => $day,
            'timeSlots' => self::TIME_SLOTS,
            'bookedTimes' => $bookedTimes,
            'time' => $time,

            'endTime' => $time
                ? CarbonImmutable::createFromFormat(
                    'H:i',
                    $time
                )->addHour()->format('H:i')
                : null,

            'purposes' => self::PURPOSES,

            'stepLabels' => [
                'Pilih lapangan',
                'Pilih hari',
                'Pilih jam',
                'Data peminjam'
            ],
        ]);
    }


    // ==========================================
    // MENYIMPAN RESERVASI
    // ==========================================

    public function store(
        StoreReservationRequest $request
    ): RedirectResponse {

        $data = $request->validated();

        $user = $request->user();

        $court = Court::query()
            ->where('slug', $data['court'])
            ->where('is_active', true)
            ->firstOrFail();

        $start = CarbonImmutable::createFromFormat(
            'H:i',
            $data['time']
        );

        $reservation = DB::transaction(
            function () use ($user, $data, $court, $start): ?Reservation {

                $isTaken = Reservation::query()
                    ->where('court_id', $court->id)
                    ->whereDate(
                        'reservation_date',
                        $data['date']
                    )
                    ->where('start_time', $data['time'])
                    ->whereIn(
                        'status',
                        Reservation::BLOCKING_STATUSES
                    )
                    ->lockForUpdate()
                    ->exists();

                if ($isTaken) {
                    return null;
                }

                return $user->reservations()->create([

                    'court_id' => $court->id,

                    'reservation_date' => $data['date'],

                    'start_time' => $data['time'],

                    'end_time' => $start
                        ->addHour()
                        ->format('H:i'),

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
                ->withErrors([
                    'time' => 'Slot ini baru saja terisi. Silakan pilih jam lain.'
                ]);
        }

        return redirect()
            ->route('reservation.index')
            ->with('reservation_submitted', [

                'court' => $court->name,

                'date' => $reservation->reservation_date
                    ->locale('id')
                    ->translatedFormat('l, d F Y'),

                'time' => $reservation->timeRange(),

                'status' => 'Menunggu Persetujuan',

            ]);
    }


    // ==========================================
    // MEMBUAT DAFTAR 6 HARI KE DEPAN
    // ==========================================

    /**
     * @return Collection<int, array{
     *     label: string,
     *     date: string,
     *     value: string,
     *     full: string
     * }>
     */

    protected function upcomingDays(): Collection
    {
        return collect(range(0, 5))->map(
            function (int $offset): array {

                $date = CarbonImmutable::today()
                    ->addDays($offset)
                    ->locale('id');

                return [

                    'label' => mb_strtoupper(
                        $date->translatedFormat('D')
                    ),

                    'date' => $date->format('d'),

                    'value' => $date->toDateString(),

                    'full' => $date->translatedFormat(
                        'l, d F Y'
                    ),

                ];
            }
        );
    }
}
