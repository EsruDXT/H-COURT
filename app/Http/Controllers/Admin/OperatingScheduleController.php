<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Court;
use App\Models\CourtClosure;
use App\Models\Reservation;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OperatingScheduleController extends Controller
{
    private const DAYS = [
        0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu',
        4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu',
    ];

    public function edit(Court $court): View
    {
        // Jadwal awal mengikuti opens_at/closes_at yang sudah tersimpan di data lapangan.
        foreach (array_keys(self::DAYS) as $day) {
            $court->operatingHours()->firstOrCreate(
                ['day_of_week' => $day],
                ['opens_at' => $court->opens_at, 'closes_at' => $court->closes_at, 'is_closed' => false],
            );
        }

        $hours = $court->operatingHours()->orderBy('day_of_week')->get()->keyBy('day_of_week');
        $closures = $court->closures()->whereDate('closure_date', '>=', today())->orderBy('closure_date')->get();
        $days = self::DAYS;

        return view('admin.operating-hours.edit', compact('court', 'hours', 'closures', 'days'));
    }

    public function update(Request $request, Court $court): RedirectResponse
    {
        $validated = $request->validate([
            'hours' => ['required', 'array', 'size:7'],
            'hours.*.is_closed' => ['required', 'boolean'],
            'hours.*.opens_at' => ['nullable', 'date_format:H:i'],
            'hours.*.closes_at' => ['nullable', 'date_format:H:i'],
        ]);

        foreach (self::DAYS as $day => $label) {
            $item = $validated['hours'][$day] ?? null;
            if ($item === null) {
                return back()->withInput()->withErrors(['hours' => "Jadwal {$label} belum dikirim."]);
            }

            if (! (bool) $item['is_closed']) {
                if (empty($item['opens_at']) || empty($item['closes_at'])) {
                    return back()->withInput()->withErrors(['hours' => "Jam buka dan tutup {$label} wajib diisi."]);
                }

                if ($item['closes_at'] <= $item['opens_at']) {
                    return back()->withInput()->withErrors(['hours' => "Jam tutup {$label} harus setelah jam buka."]);
                }
            }
        }

        // Kunci baris lapangan agar perubahan jam tidak berlomba dengan proses booking.
        $conflictingReservationId = DB::transaction(function () use ($court, $validated): ?int {
            $lockedCourt = Court::query()->whereKey($court->id)->lockForUpdate()->firstOrFail();

            // Jangan izinkan perubahan jadwal membuat reservasi aktif yang sudah ada menjadi di luar jam operasional.
            $affectedReservation = $lockedCourt->reservations()
                ->whereIn('status', Reservation::BLOCKING_STATUSES)
                ->whereDate('reservation_date', '>=', today()->toDateString())
                ->get()
                ->first(function (Reservation $reservation) use ($validated): bool {
                    $day = CarbonImmutable::parse($reservation->reservation_date->toDateString())->dayOfWeek;
                    $hours = $validated['hours'][$day] ?? null;

                    if ($hours === null || (bool) $hours['is_closed']) {
                        return true;
                    }

                    return $reservation->startHm() < $hours['opens_at']
                        || $reservation->endHm() > $hours['closes_at'];
                });

            if ($affectedReservation !== null) {
                return $affectedReservation->id;
            }

            foreach (self::DAYS as $day => $label) {
                $item = $validated['hours'][$day];
                $closed = (bool) $item['is_closed'];

                $lockedCourt->operatingHours()->updateOrCreate(
                    ['day_of_week' => $day],
                    [
                        'opens_at' => $closed ? null : $item['opens_at'],
                        'closes_at' => $closed ? null : $item['closes_at'],
                        'is_closed' => $closed,
                    ],
                );
            }

            return null;
        });

        if ($conflictingReservationId !== null) {
            return back()->withInput()->withErrors([
                'hours' => 'Jadwal tidak dapat diubah karena akan menutup atau memotong reservasi aktif #'.$conflictingReservationId.'. Ubah atau batalkan reservasi tersebut terlebih dahulu.',
            ]);
        }

        return back()->with('status', 'Jadwal operasional berhasil diperbarui.');
    }

    public function storeClosure(Request $request, Court $court): RedirectResponse
    {
        $validated = $request->validate([
            'closure_date' => [
                'required', 'date', 'after_or_equal:today',
                Rule::unique('court_closures', 'closure_date')->where('court_id', $court->id),
            ],
            'reason' => ['nullable', 'string', 'max:200'],
        ], [
            'closure_date.unique' => 'Tanggal tersebut sudah ditandai tutup untuk lapangan ini.',
        ]);

        $created = DB::transaction(function () use ($court, $validated): bool {
            $lockedCourt = Court::query()->whereKey($court->id)->lockForUpdate()->firstOrFail();

            $hasActiveReservation = $lockedCourt->reservations()
                ->whereDate('reservation_date', $validated['closure_date'])
                ->whereIn('status', Reservation::BLOCKING_STATUSES)
                ->exists();

            if ($hasActiveReservation) {
                return false;
            }

            $lockedCourt->closures()->create($validated);

            return true;
        });

        if (! $created) {
            return back()->withInput()->withErrors([
                'closure_date' => 'Tanggal tersebut masih memiliki reservasi menunggu atau diterima. Selesaikan reservasi terlebih dahulu sebelum menutup lapangan.',
            ]);
        }

        return back()->with('status', 'Tanggal penutupan berhasil ditambahkan.');
    }

    public function destroyClosure(Court $court, CourtClosure $closure): RedirectResponse
    {
        abort_unless($closure->court_id === $court->id, 404);
        $closure->delete();

        return back()->with('status', 'Tanggal penutupan berhasil dihapus.');
    }
}
