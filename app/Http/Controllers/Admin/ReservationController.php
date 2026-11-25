<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ReservationController as PublicReservationController;
use App\Http\Requests\Admin\UpdateReservationRequest;
use App\Models\Court;
use App\Models\Reservation;
use App\Notifications\ReservationActivityNotification;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function edit(Reservation $reservation): View
    {
        $reservation->load(['court', 'user']);
        $courts = Court::query()->orderBy('id')->get();
        $timeSlots = collect($courts)
            ->flatMap(fn (Court $court) => $court->timeSlotsForDate($reservation->reservation_date->toDateString()))
            ->push($reservation->startHm())
            ->unique()->sort()->values()->all();

        return view('admin.reservation.edit', [
            'reservation' => $reservation,
            'courts' => $courts,
            'timeSlots' => $timeSlots,
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
            ->addHours((int) $data['duration'])->format('H:i');

        $result = DB::transaction(function () use ($request, $reservation, $data, $end): array {
            // Ambil lock lapangan dalam urutan ID yang konsisten sebelum lock reservasi.
            $courtIds = collect([(int) $reservation->court_id, (int) $data['court_id']])->unique()->sort()->values();
            $lockedCourts = Court::query()->whereIn('id', $courtIds)->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            $lockedReservation = Reservation::query()->whereKey($reservation->id)->lockForUpdate()->firstOrFail();
            $court = $lockedCourts->get((int) $data['court_id']);

            if (! $court) {
                return ['error' => 'Lapangan tujuan tidak ditemukan.'];
            }

            if (in_array($data['status'], Reservation::BLOCKING_STATUSES, true)) {
                $window = $court->operatingWindowForDate($data['reservation_date']);
                if ($window === null || ! in_array($data['start_time'], $court->timeSlotsForDate($data['reservation_date']), true)
                    || $end > $window['closes_at']) {
                    return ['error' => 'Jadwal baru berada di luar jam operasional lapangan atau lapangan sedang tutup.'];
                }

                if ($court->hasConflict($data['reservation_date'], $data['start_time'], $end, $lockedReservation->id)) {
                    return ['error' => 'Jadwal bentrok dengan reservasi lain pada lapangan dan jam tersebut.'];
                }
            }

            $oldStatus = $lockedReservation->status;
            $lockedReservation->court_id = $court->id;
            $lockedReservation->reservation_date = $data['reservation_date'];
            $lockedReservation->start_time = $data['start_time'];
            $lockedReservation->end_time = $end;
            $lockedReservation->purpose = $data['purpose'];
            $lockedReservation->status = $data['status'];

            if ($oldStatus !== $data['status']) {
                if (in_array($data['status'], [Reservation::STATUS_CONFIRMED, Reservation::STATUS_REJECTED], true)) {
                    $lockedReservation->reviewed_at = now();
                    $lockedReservation->reviewed_by = $request->user()->id;
                    $lockedReservation->cancelled_at = null;
                    $lockedReservation->cancelled_by = null;
                } elseif ($data['status'] === Reservation::STATUS_CANCELLED) {
                    $lockedReservation->cancelled_at = now();
                    $lockedReservation->cancelled_by = $request->user()->id;
                } elseif ($data['status'] === Reservation::STATUS_PENDING) {
                    $lockedReservation->reviewed_at = null;
                    $lockedReservation->reviewed_by = null;
                    $lockedReservation->cancelled_at = null;
                    $lockedReservation->cancelled_by = null;
                }
            }

            $lockedReservation->save();

            return [
                'error' => null,
                'reservation' => $lockedReservation->fresh(['user', 'court']),
                'status_changed' => $oldStatus !== $data['status'],
                'new_status' => $data['status'],
            ];
        });

        if ($result['error'] !== null) {
            return back()->withInput()->withErrors(['start_time' => $result['error']]);
        }

        if ($result['status_changed']) {
            $this->notifyUserOfStatus($result['reservation'], $result['new_status']);
        }

        return redirect()->route('admin.dashboard')->with('status', 'Reservasi berhasil diperbarui.');
    }

    public function approve(Request $request, Reservation $reservation): RedirectResponse
    {
        $result = DB::transaction(function () use ($request, $reservation): array {
            $court = Court::query()->whereKey($reservation->court_id)->lockForUpdate()->firstOrFail();
            $locked = Reservation::query()->whereKey($reservation->id)->lockForUpdate()->firstOrFail();

            if ($locked->status !== Reservation::STATUS_PENDING) {
                return ['error' => 'Hanya reservasi berstatus menunggu yang bisa diterima.'];
            }

            $date = $locked->reservation_date->toDateString();
            $window = $court->operatingWindowForDate($date);
            if ($window === null || ! in_array($locked->startHm(), $court->timeSlotsForDate($date), true)
                || $locked->endHm() > $window['closes_at']) {
                return ['error' => 'Tidak bisa diterima: jadwal sudah berada di luar jam operasional lapangan.'];
            }

            if ($court->hasConflict($date, $locked->startHm(), $locked->endHm(), $locked->id)) {
                return ['error' => 'Tidak bisa diterima: jadwal bentrok dengan reservasi lain.'];
            }

            $locked->status = Reservation::STATUS_CONFIRMED;
            $locked->reviewed_at = now();
            $locked->reviewed_by = $request->user()->id;
            $locked->cancelled_at = null;
            $locked->cancelled_by = null;
            $locked->save();

            return ['error' => null, 'reservation' => $locked->fresh(['user', 'court'])];
        });

        if ($result['error'] !== null) {
            return back()->withErrors(['status' => $result['error']]);
        }

        $this->notifyUserOfStatus($result['reservation'], Reservation::STATUS_CONFIRMED);

        return back()->with('status', 'Reservasi '.$result['reservation']->borrower_name.' diterima.');
    }

    public function reject(Request $request, Reservation $reservation): RedirectResponse
    {
        $locked = DB::transaction(function () use ($request, $reservation): ?Reservation {
            $item = Reservation::query()->whereKey($reservation->id)->lockForUpdate()->firstOrFail();
            if ($item->status !== Reservation::STATUS_PENDING) {
                return null;
            }

            $item->status = Reservation::STATUS_REJECTED;
            $item->reviewed_at = now();
            $item->reviewed_by = $request->user()->id;
            $item->save();

            return $item->fresh(['user', 'court']);
        });

        if ($locked === null) {
            return back()->withErrors(['status' => 'Hanya reservasi berstatus menunggu yang bisa ditolak.']);
        }

        $this->notifyUserOfStatus($locked, Reservation::STATUS_REJECTED);

        return back()->with('status', 'Reservasi '.$locked->borrower_name.' ditolak.');
    }

    private function notifyUserOfStatus(Reservation $reservation, string $status): void
    {
        $messages = [
            Reservation::STATUS_CONFIRMED => ['Reservasi diterima', 'Reservasimu telah disetujui oleh pengelola.'],
            Reservation::STATUS_REJECTED => ['Reservasi ditolak', 'Maaf, reservasimu belum dapat disetujui.'],
            Reservation::STATUS_CANCELLED => ['Reservasi dibatalkan', 'Reservasimu telah dibatalkan oleh pengelola.'],
        ];

        if (isset($messages[$status]) && $reservation->user) {
            [$title, $detail] = $messages[$status];
            $message = $detail.' ('.$reservation->court?->name.', '
                .$reservation->reservation_date->locale('id')->translatedFormat('d F Y').', '
                .$reservation->timeRange().')';
            $reservation->user->notify(new ReservationActivityNotification(
                $title,
                $message,
                route('reservation.index', absolute: false),
                $reservation->id,
            ));
        }
    }
}
