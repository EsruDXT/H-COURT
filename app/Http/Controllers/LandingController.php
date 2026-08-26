<?php

namespace App\Http\Controllers;

class LandingController extends Controller
{
    public function index()
    {
        // TODO: nanti ganti dengan query asli ke database, contoh:
        // $stats = [
        //     ['value' => Court::count(), 'label' => 'Lapangan terdaftar'],
        //     ['value' => Schedule::whereDate('date', today())->available()->count(), 'label' => 'Slot tersedia hari ini'],
        //     ...
        // ];
        // $lapangan = Court::with('schedules')->get();

        $stats = [
            ['value' => '3', 'label' => 'Lapangan terdaftar'],
            ['value' => '12', 'label' => 'Slot tersedia hari ini'],
            ['value' => '10-15', 'label' => 'Reservasi per bulan'],
            ['value' => '0', 'label' => 'Jadwal bentrok'],
        ];

        // Semua gambar sementara pakai 1 file placeholder yang sama:
        // public/images/court-placeholder.jpg
        // Nanti kalau mau ganti foto asli, tinggal timpa/replace file ini
        // dengan foto beneran (nama file boleh sama atau beda, tinggal
        // sesuaikan path di bawah).
        $placeholder = asset('images/court-placeholder.jpg');

        $lapangan = [
            [
                'nama' => 'Lapangan Futsal',
                'tipe' => 'Outdoor - sintesis',
                'gambar' => $placeholder,
                'slot' => [
                    ['jam' => '14:00', 'terisi' => true],
                    ['jam' => '15:00', 'terisi' => false],
                    ['jam' => '16:00', 'terisi' => false],
                    ['jam' => '17:00', 'terisi' => true],
                ],
            ],
            [
                'nama' => 'Lapangan Basket',
                'tipe' => 'Indoor - gedung olahraga',
                'gambar' => $placeholder,
                'slot' => [
                    ['jam' => '14:00', 'terisi' => true],
                    ['jam' => '15:00', 'terisi' => false],
                    ['jam' => '16:00', 'terisi' => true],
                    ['jam' => '17:00', 'terisi' => false],
                ],
            ],
            [
                'nama' => 'Lapangan Badminton',
                'tipe' => 'Indoor - gedung olahraga',
                'gambar' => $placeholder,
                'slot' => [
                    ['jam' => '14:00', 'terisi' => true],
                    ['jam' => '15:00', 'terisi' => false],
                    ['jam' => '16:00', 'terisi' => true],
                    ['jam' => '17:00', 'terisi' => false],
                ],
            ],
        ];

        // TODO: sesuaikan teks ini dengan poin masalah asli dari sekolah kamu
        $alasan = [
            [
                'judul' => 'Catatan manual, mudah hilang',
                'deskripsi' => 'Buku peminjaman rusak, hilang, atau lupa dibawa petugas. H-COURT menyimpan setiap reservasi dalam satu sistem terpusat.',
            ],
            [
                'judul' => 'Jadwal tidak terlihat siapa pun',
                'deskripsi' => 'Siswa harus datang langsung untuk tahu jadwal kosong. Sekarang ketersediaan bisa dicek kapan saja, dari mana saja.',
            ],
            [
                'judul' => 'Reservasi sering bentrok',
                'deskripsi' => 'Dua kelompok datang di jam yang sama. Sistem menolak otomatis slot yang sudah terisi sebelum reservasi diajukan.',
            ],
        ];

        // Foto suasana juga pakai placeholder yang sama biar cuma 1 file yang perlu diganti
        $fotoSuasana = $placeholder;

        return view('landing.index', compact('stats', 'lapangan', 'alasan', 'fotoSuasana'));
    }
}