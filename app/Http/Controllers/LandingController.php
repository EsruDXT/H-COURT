<?php

namespace App\Http\Controllers;

use App\Models\Court;
use App\Models\Reservation;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function index(): View
    {
        $today = now()->toDateString();

        $activeCourts = Court::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        $availableSlots = 0;
        $courts = [];

        foreach ($activeCourts as $court) {
            $slots = [];

            foreach ($court->slotAvailability($today) as $jam => $status) {
                $slots[] = ['jam' => $jam, 'tersedia' => $status === 'available'];
            }

            $availableSlots += count(array_filter($slots, fn (array $slot): bool => $slot['tersedia']));

            $courts[] = [
                'slug' => $court->slug,
                'nama' => $court->name,
                'lokasi' => $court->location,
                'gambar' => $court->imageUrl(),
                'slots' => $slots,
            ];
        }

        $monthlyReservations = Reservation::query()
            ->whereIn('status', Reservation::BLOCKING_STATUSES)
            ->whereBetween('reservation_date', [
                now()->startOfMonth()->toDateString(),
                now()->endOfMonth()->toDateString(),
            ])
            ->count();

        $stats = [
            ['value' => (string) $activeCourts->count(), 'label' => 'Lapangan terdaftar'],
            ['value' => (string) $availableSlots, 'label' => 'Slot tersedia hari ini'],
            ['value' => (string) $monthlyReservations, 'label' => 'Reservasi bulan ini'],
            ['value' => (string) Reservation::conflictCount(), 'label' => 'Jadwal bentrok'],
        ];

        $reason = [
            [
                'title' => 'Catatan manual, mudah hilang',
                'desc' => 'Buku peminjaman rusak, hilang, atau lupa dibawa petugas. H-COURT menyimpan setiap reservasi dalam satu sistem terpusat.',
            ],
            [
                'title' => 'Jadwal tidak terlihat siapa pun',
                'desc' => 'Siswa harus datang langsung untuk tahu jadwal kosong. Sekarang ketersediaan bisa dicek kapan saja, dari mana saja.',
            ],
            [
                'title' => 'Reservasi sering bentrok',
                'desc' => 'Dua kelompok datang di jam yang sama. Sistem menolak otomatis slot yang sudah terisi sebelum reservasi diajukan.',
            ],
        ];

        return view('landing.index', compact('stats', 'courts', 'reason'));
    }
}
