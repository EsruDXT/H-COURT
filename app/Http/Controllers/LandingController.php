<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class LandingController extends Controller
{

    public function index(): View
    {

        $stats = [
            ['value' => '3', 'label' => 'Lapangan terdaftar'],
            ['value' => '12', 'label' => 'Slot tersedia hari ini'],
            ['value' => '10-15', 'label' => 'Reservasi per bulan'],
            ['value' => '0', 'label' => 'Jadwal bentrok'],
        ];

        $courts = [
            [
                'slug'   => 'futsal',
                'nama'   => 'Lapangan Futsal',
                'lokasi' => 'Outdoor - sintesis',
                'gambar' => asset('images/courts/futsal.jpg'),
                'slots'  => [
                    ['jam' => '14:00', 'tersedia' => true],
                    ['jam' => '15:00', 'tersedia' => true],
                    ['jam' => '16:00', 'tersedia' => true],
                    ['jam' => '15:00', 'tersedia' => false],
                ],
            ],
            [
                'slug'   => 'basket',
                'nama'   => 'Lapangan Basket',
                'lokasi' => 'Indoor - gedung olahraga',
                'gambar' => asset('images/courts/basket.jpg'),
                'slots'  => [
                    ['jam' => '14:00', 'tersedia' => false],
                    ['jam' => '15:00', 'tersedia' => true],
                    ['jam' => '16:00', 'tersedia' => false],
                    ['jam' => '17:00', 'tersedia' => true],
                ],
            ],
            [
                'slug'   => 'badminton',
                'nama'   => 'Lapangan Badminton',
                'lokasi' => 'Indoor - gedung olahraga',
                'gambar' => asset('images/courts/badminton.jpg'),
                'slots'  => [
                    ['jam' => '14:00', 'tersedia' => false],
                    ['jam' => '15:00', 'tersedia' => true],
                    ['jam' => '16:00', 'tersedia' => false],
                    ['jam' => '17:00', 'tersedia' => true],
                ],
            ],
        ];

        $reason = [
            [
                'title' => 'Catatan manual, mudah hilang',
                'desc'  => 'Buku peminjaman rusak, hilang, atau lupa dibawa petugas. H-COURT menyimpan setiap reservasi dalam satu sistem terpusat.',
            ],
            [
                'title' => 'Jadwal tidak terlihat siapa pun',
                'desc'  => 'Siswa harus datang langsung untuk tahu jadwal kosong. Sekarang ketersediaan bisa dicek kapan saja, dari mana saja.',
            ],
            [
                'title' => 'Reservasi sering bentrok',
                'desc'  => 'Dua kelompok datang di jam yang sama. Sistem menolak otomatis slot yang sudah terisi sebelum reservasi diajukan.',
            ],
        ];

        return view('landing.index', compact('stats', 'courts', 'reason'));
    }
}