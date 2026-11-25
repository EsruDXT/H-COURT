<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Models\Court;
use App\Models\Reservation;
use App\Models\User;
use App\Notifications\ReservationActivityNotification;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public const PURPOSES = ['Latihan rutin', 'Lomba / turnamen', 'Pertandingan umum'];

    public function index(Request $request): View
    {
        $reservations = Reservation::query()->with('court')
            ->where('user_id', $request->user()->id)->latest()->get();

        return view('reservation.index', compact('reservations'));
    }

    public function create(Request $request): View
    {
        $courts = Court::query()->where('is_active', true)->orderBy('category')->orderBy('name')->get();
        $days = $this->upcomingDays();
        $court = $courts->firstWhere('slug', $request->query('court'));
        $day = $days->firstWhere('value', $request->query('date'));
        $timeSlots = ($court && $day) ? $court->timeSlotsForDate($day['value']) : [];
        $slotStatuses = ($court && $day) ? $court->slotAvailability($day['value']) : [];
        $blocked = array_keys(array_filter($slotStatuses, fn (string $status): bool => $status !== 'available'));

        $requestedTime = $request->query('time');
        $time = is_string($requestedTime) && in_array($requestedTime, $timeSlots, true) ? $requestedTime : null;
        $requestedDuration = (int) $request->query('duration', 1);
        $duration = ($requestedDuration >= 1 && $requestedDuration <= Reservation::MAX_DURATION) ? $requestedDuration : null;
        $slotError = null;
        $endTime = null;
        $window = ($court && $day) ? $court->operatingWindowForDate($day['value']) : null;

        if ($court && $day && $window === null) {
            $slotError = 'Lapangan tidak beroperasi pada tanggal yang dipilih.';
        } elseif ($court && $day && $time && $duration) {
            $endTime = CarbonImmutable::createFromFormat('H:i', $time)->addHours($duration)->format('H:i');
            $needed = array_values(array_filter($timeSlots, fn (string $slot): bool => $slot >= $time && $slot < $endTime));

            if ($endTime > $window['closes_at'] || count($needed) !== $duration) {
                $slotError = 'Durasi melewati jam operasional lapangan. Pilih jam atau durasi lain.';
            } elseif (array_intersect($needed, $blocked) !== []) {
                $slotError = 'Ada jam pada rentang tersebut yang sudah terisi atau sudah lewat.';
            }

            if ($slotError !== null) {
                $time = null;
                $duration = null;
                $endTime = null;
            }
        } elseif ($requestedTime !== null && $time === null) {
            $slotError = 'Jam yang dipilih tidak tersedia pada jadwal operasional lapangan.';
        }

        $step = match (true) {
            $court === null => 1,
            $day === null => 2,
            $time === null || $duration === null => 3,
            default => 4,
        };

        return view('reservation.create', [
            'step' => $step, 'courts' => $courts, 'court' => $court, 'days' => $days, 'day' => $day,
            'timeSlots' => $timeSlots, 'slotStatuses' => $slotStatuses, 'bookedTimes' => $blocked,
            'time' => $time, 'duration' => $duration, 'durations' => range(1, Reservation::MAX_DURATION),
            'slotError' => $slotError, 'endTime' => $endTime, 'purposes' => self::PURPOSES,
            'stepLabels' => ['Pilih lapangan', 'Pilih hari', 'Pilih jam & durasi', 'Data peminjam'],
        ]);
    }

    public function store(StoreReservationRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $court = Court::query()->where('slug', $data['court'])->where('is_active', true)->firstOrFail();
        $end = CarbonImmutable::createFromFormat('H:i', $data['time'])->addHours((int) $data['duration'])->format('H:i');

        $reservation = DB::transaction(function () use ($request, $data, $court, $end): ?Reservation {
            // Parent-row lock prevents two concurrent bookings for an empty time range.
            $lockedCourt = Court::query()->whereKey($court->id)->lockForUpdate()->firstOrFail();
            $window = $lockedCourt->operatingWindowForDate($data['date']);

            if (! $lockedCourt->is_active || $window === null
                || ! in_array($data['time'], $lockedCourt->timeSlotsForDate($data['date']), true)
                || $end > $window['closes_at']) {
                return null;
            }

            if ($lockedCourt->hasConflict($data['date'], $data['time'], $end)) {
                return null;
            }

            return $request->user()->reservations()->create([
                'court_id' => $lockedCourt->id, 'reservation_date' => $data['date'],
                'start_time' => $data['time'], 'end_time' => $end,
                'borrower_name' => $data['full_name'], 'class_institution' => $data['class_institution'],
                'phone_number' => $data['phone_number'], 'participant_count' => $data['participant_count'],
                'purpose' => $data['purpose'], 'notes' => $data['notes'] ?? null,
                'status' => Reservation::STATUS_PENDING,
            ]);
        });

        if ($reservation === null) {
            return back()->withInput()->withErrors(['time' => 'Jadwal baru saja berubah atau sudah terisi. Muat ulang jadwal dan pilih waktu lain.']);
        }

        $reservation->load('court');
        $this->notifyAdmins($reservation, 'Reservasi baru', $reservation->borrower_name.' mengajukan reservasi '.$reservation->court->name.'.');

        return redirect()->route('reservation.index')->with('reservation_submitted', [
            'court' => $reservation->court->name,
            'date' => $reservation->reservation_date->locale('id')->translatedFormat('l, d F Y'),
            'time' => $reservation->timeRange(), 'status' => 'Menunggu Persetujuan',
        ]);
    }

    public function cancel(Request $request, Reservation $reservation): RedirectResponse
    {
        $owned = $request->user()->reservations()->whereKey($reservation->id)->firstOrFail();

        $result = DB::transaction(function () use ($owned, $request): string {
            Court::query()->whereKey($owned->court_id)->lockForUpdate()->firstOrFail();
            $locked = Reservation::query()->whereKey($owned->id)->lockForUpdate()->firstOrFail();

            if (! $locked->canBeCancelledByUser()) {
                return 'not-cancellable';
            }

            $locked->status = Reservation::STATUS_CANCELLED;
            $locked->cancelled_at = now();
            $locked->cancelled_by = $request->user()->id;
            $locked->save();

            return 'cancelled';
        });

        if ($result !== 'cancelled') {
            return back()->withErrors(['reservation' => 'Reservasi ini tidak dapat dibatalkan. Pembatalan hanya tersedia sebelum waktu penggunaan dimulai untuk reservasi menunggu atau diterima.']);
        }

        $owned->load('court');
        $this->notifyAdmins($owned, 'Reservasi dibatalkan pengguna', $owned->borrower_name.' membatalkan reservasi '.$owned->court->name.'.');

        return redirect()->route('reservation.index')->with('success', 'Reservasi berhasil dibatalkan.');
    }

    protected function notifyAdmins(Reservation $reservation, string $title, string $message): void
    {
        User::query()->where('role', User::ROLE_ADMIN)->get()->each(
            fn (User $admin) => $admin->notify(new ReservationActivityNotification($title, $message, route('admin.dashboard'), $reservation->id))
        );
    }

    /** @return Collection<int, array{label: string, date: string, value: string, full: string}> */
    protected function upcomingDays(): Collection
    {
        return collect(range(0, 5))->map(function (int $offset): array {
            $date = CarbonImmutable::today()->addDays($offset)->locale('id');

            return [
                'label' => mb_strtoupper($date->translatedFormat('D')),
                'date' => $date->format('d'), 'value' => $date->toDateString(),
                'full' => $date->translatedFormat('l, d F Y'),
            ];
        });
    }
}
