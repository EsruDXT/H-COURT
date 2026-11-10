<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ReservationController as PublicReservationController;
use App\Http\Requests\Admin\UpdateReservationRequest;
use App\Models\Court;
use App\Models\Reservation;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function edit(Reservation $reservation): View
    {
        $reservation->load(['court', 'user']);

        return view('admin.reservation.edit', [
            'reservation' => $reservation,
            'courts' => Court::query()->orderBy('id')->get(),
            'timeSlots' => Reservation::TIME_SLOTS,
            'durations' => range(1, Reservation::MAX_DURATION),
            'purposes' => PublicReservationController::PURPOSES,
            'statuses' => [
                Reservation::STATUS_PENDING => 'Menunggu',
                Reservation::STATUS_CONFIRMED => 'Diterima',
                Reservation::STATUS_REJECTED => 'Ditolak',
                Reservation::STATUS_CANCELLED => 'Dibatalkan',
            ],
        ]);
    }

    public function update(UpdateReservationRequest $request, Reservation $reservation): RedirectResponse
    {
        $data = $request->validated();

        $end = CarbonImmutable::createFromFormat('H:i', $data['start_time'])
            ->addHours((int) $data['duration'])
            ->format('H:i');

        $court = Court::findOrFail($data['court_id']);

        $conflict = in_array($data['status'], Reservation::BLOCKING_STATUSES, true)
            && $court->hasConflict($data['reservation_date'], $data['start_time'], $end, $reservation->id);

        if ($conflict) {
            return back()->withInput()->withErrors([
                'start_time' => 'Jadwal bentrok dengan reservasi lain pada lapangan dan jam tersebut.',
            ]);
        }

        $reservation->update([
            'court_id' => $court->id,
            'reservation_date' => $data['reservation_date'],
            'start_time' => $data['start_time'],
            'end_time' => $end,
            'purpose' => $data['purpose'],
            'status' => $data['status'],
        ]);

        return redirect()->route('admin.dashboard')->with('status', 'Reservasi berhasil diperbarui.');
    }

    public function approve(Reservation $reservation): RedirectResponse
    {
        if ($reservation->status !== Reservation::STATUS_PENDING) {
            return back()->withErrors(['status' => 'Hanya reservasi berstatus menunggu yang bisa diterima.']);
        }

        $conflict = $reservation->court->hasConflict(
            $reservation->reservation_date->toDateString(),
            $reservation->startHm(),
            $reservation->endHm(),
            $reservation->id,
        );

        if ($conflict) {
            return back()->withErrors(['status' => 'Tidak bisa diterima: jadwal bentrok dengan reservasi lain.']);
        }

        $reservation->update(['status' => Reservation::STATUS_CONFIRMED]);

        return back()->with('status', 'Reservasi '.$reservation->borrower_name.' diterima.');
    }

    public function reject(Reservation $reservation): RedirectResponse
    {
        if ($reservation->status !== Reservation::STATUS_PENDING) {
            return back()->withErrors(['status' => 'Hanya reservasi berstatus menunggu yang bisa ditolak.']);
        }

        $reservation->update(['status' => Reservation::STATUS_REJECTED]);

        return back()->with('status', 'Reservasi '.$reservation->borrower_name.' ditolak.');
    }
}
