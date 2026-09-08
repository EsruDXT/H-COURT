@extends('layouts.app')

@section('title', 'H-COURT — Reservasi Lapangan Sekolah')

@section('content')

    <div class="max-w-7xl mx-auto px-6">

        <section class="mt-6 bg-navy rounded-3xl px-8 py-16 md:px-16 md:py-20 relative overflow-hidden">
            <p class="text-brand-light font-semibold tracking-wide text-sm mb-4">
                Reservasi Lapangan Sekolah
            </p>

            <h1 class="text-white font-sans font-extrabold text-4xl md:text-5xl leading-tight max-w-2xl">
                Pesan lapangan, <span class="text-brand-light italic font-extrabold">tanpa antre</span> ke ruang piket.
            </h1>

            <p class="text-gray-300 mt-6 max-w-xl leading-relaxed">
                Lihat jadwal futsal, basket, dan badminton secara real-time, lalu ajukan reservasi
                langsung dari HP kamu — tidak perlu buku catatan lagi.
            </p>

            <div class="flex flex-wrap gap-4 mt-8">
                <a href="{{ Route::has('reservation.create') ? route('reservation.create') : route('login') }}"
                   class="bg-brand hover:bg-brand-dark text-white font-semibold text-sm px-7 py-3.5 rounded-lg transition-colors">
                    Reservasi Lapangan
                </a>
                <a href="{{ route('schedule.index') }}"
                   class="border border-white/70 text-white font-semibold text-sm px-7 py-3.5 rounded-lg hover:bg-white/10 transition-colors">
                    Cek jadwal lapangan
                </a>
            </div>
        </section>

        <section class="-mt--6 relative z-10 px-4 md:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach ($stats as $stat)
                    <div class="bg-white rounded-2xl border border-black/5 shadow-sm px-6 py-6 text-center md:text-left">
                        <p class="font-serif font-semibold text-3xl md:text-4xl text-ink">{{ $stat['value'] }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="mt-20">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div>
                    <p class="text-brand font-semibold tracking-wide text-sm mb-2">Pilih Lapangan</p>
                    <h2 class="font-serif font-bold text-3xl md:text-4xl text-ink">Ketersediaan hari ini</h2>
                </div>
                <p class="text-gray-500 text-sm max-w-xs md:text-right">
                    Status diperbarui begitu pengelola menerima atau menolak reservasi.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-6 mt-8">
                @forelse ($lapangans as $lapangan)
                    <div class="bg-white rounded-2xl border border-black/5 shadow-sm overflow-hidden">
                        <img src="{{ $lapangan['gambar'] }}"
                             alt="{{ $lapangan['nama'] }}"
                             class="w-full h-44 object-cover">

                        <div class="p-5">
                            <h3 class="font-semibold text-lg text-ink">{{ $lapangan['nama'] }}</h3>
                            <p class="text-sm text-gray-500 mt-0.5">{{ $lapangan['lokasi'] }}</p>

                            <div class="flex flex-wrap gap-2 mt-4">
                                @foreach ($lapangan['slots'] as $slot)
                                    <span @class([
                                            'px-3 py-1.5 rounded-lg text-xs font-medium border',
                                            'border-gray-300 text-gray-700' => $slot['tersedia'],
                                            'border-gray-200 text-gray-300 line-through' => ! $slot['tersedia'],
                                          ])>
                                        {{ $slot['jam'] }}
                                    </span>
                                @endforeach
                            </div>

                            <a href="{{ route('schedule.show', $lapangan['slug']) }}"
                               class="block text-center border border-ink/80 text-ink font-medium text-sm mt-5 py-3 rounded-lg hover:bg-ink hover:text-white transition-colors">
                                Lihat jadwal lengkap
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 col-span-3">Belum ada lapangan terdaftar.</p>
                @endforelse
            </div>
        </section>

        <section class="mt-24">
            <p class="text-brand font-semibold tracking-wide text-sm mb-2">Kenapa H-COURT</p>
            <h2 class="font-serif font-bold text-3xl md:text-4xl text-ink mb-8">Dibuat untuk masalah nyata</h2>

            <div class="grid md:grid-cols-2 gap-8 items-stretch">
                <div class="rounded-2xl overflow-hidden min-h-80 md:min-h-full">
                    <img src="{{ asset('images/misc/suasana-lapangan.png') }}"
                         alt="Suasana lapangan H-COURT"
                         class="w-full h-full object-cover">
                </div>

                <div class="bg-white rounded-2xl border border-black/5 shadow-sm divide-y divide-gray-100">
                    @foreach ($alasan as $item)
                        <div class="p-6 md:p-7">
                            <p class="text-brand font-semibold text-sm mb-2">Sebelumnya</p>
                            <h3 class="font-semibold text-lg text-ink mb-2">{{ $item['title'] }}</h3>
                            <p class="text-sm text-gray-500 leading-relaxed">{{ $item['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="mt-16 mb-20 bg-navy rounded-3xl px-8 py-8 md:px-12 md:py-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <h3 class="text-white font-serif font-semibold text-xl md:text-2xl text-center md:text-left">
                Siap reservasi lapanganmu untuk<br class="hidden md:block"> latihan minggu ini?
            </h3>
            <a href="{{ Route::has('reservation.create') ? route('reservation.create') : route('login') }}"
               class="shrink-0 bg-brand hover:bg-brand-dark text-white font-semibold text-sm px-8 py-3.5 rounded-lg transition-colors">
                Mulai Reservasi
            </a>
        </section>

    </div>

@endsection