@extends('layouts.app')

@section('title', 'H-COURT — Reservasi Lapangan Sekolah')

@section('content')

    <section class="max-w-7xl mx-auto px-6 pt-6">
        <div class="bg-ink rounded-3xl px-8 md:px-14 py-14 md:py-16 relative overflow-hidden">
            <p class="text-brand/90 font-semibold tracking-widest text-sm uppercase mb-4">
                Reservasi Lapangan Sekolah
            </p>

            <h1 class="font-sans font-extrabold text-4xl md:text-6xl leading-tight text-white max-w-3xl">
                Pesan lapangan, <span class="italic text-brand">tanpa antre</span> ke ruang piket.
            </h1>

            <p class="text-gray-300 max-w-xl mt-6 leading-relaxed">
                Lihat jadwal futsal, basket, dan badminton secara real-time,
                lalu ajukan reservasi langsung dari HP kamu — tidak perlu
                buku catatan lagi.
            </p>

            <div class="flex flex-wrap gap-4 mt-8">

                <a href="#" class="bg-brand hover:bg-brand-dark text-white font-semibold px-7 py-3.5 rounded-xl transition">
                    Reservasi Lapangan
                </a>

                <a href="#"
                    class="border border-white/40 hover:border-white text-white font-semibold px-7 py-3.5 rounded-xl transition">
                    Cek jadwal lapangan
                </a>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-gray-200 shadow-sm -mt-8 md:-mt-10 relative z-10 mx-2 md:mx-6
                        grid grid-cols-2 md:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-gray-200">
            @foreach ($stats as $stat)
                <div class="px-6 py-7 text-center">
                    <p class="font-serif text-4xl text-ink">{{ $stat['value'] }}</p>
                    <p class="text-sm text-gray-500 mt-1">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-6 pt-20">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-10">
            <div>
                <p class="text-brand font-semibold tracking-widest text-sm uppercase mb-2">Pilih Lapangan</p>
                <h2 class="font-serif font-bold text-3xl md:text-4xl text-ink">Ketersediaan hari ini</h2>
            </div>
            <p class="text-gray-500 text-sm max-w-sm md:text-right">
                Status diperbarui begitu pengelola menerima atau menolak reservasi.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach ($lapangan as $court)
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">

                    <img src="{{ $court['gambar'] }}" alt="Foto {{ $court['nama'] }}" class="w-full h-44 object-cover">

                    <div class="p-5">
                        <h3 class="font-bold text-lg text-ink">{{ $court['nama'] }}</h3>
                        <p class="text-sm text-gray-500 mb-4">{{ $court['tipe'] }}</p>

                        <div class="flex flex-wrap gap-2 mb-5">
                            @foreach ($court['slot'] as $slot)
                                        <span class="text-xs font-medium px-3 py-1.5 rounded-lg border
                                                        {{ $slot['terisi']
                                ? 'border-gray-200 text-gray-400 line-through bg-gray-50'
                                : 'border-gray-300 text-ink' }}">
                                            {{ $slot['jam'] }}
                                        </span>
                            @endforeach
                        </div>

                        <a href="#"
                            class="block text-center border border-gray-300 hover:border-ink text-ink font-semibold text-sm py-3 rounded-xl transition">
                            Lihat jadwal lengkap
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-6 pt-24">
        <p class="text-brand font-semibold tracking-widest text-sm uppercase mb-2">Kenapa H-Court</p>
        <h2 class="font-serif font-bold text-3xl md:text-4xl text-ink mb-10">Dibuat untuk masalah nyata</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch">

            <img src="{{ $fotoSuasana }}" alt="Suasana lapangan" class="w-full min-h-[320px] rounded-2xl object-cover">

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm divide-y divide-gray-100">
                @foreach ($alasan as $item)
                    <div class="p-6 md:p-7">
                        <p class="text-brand text-sm font-semibold mb-1">Sebelumnya</p>
                        <h3 class="font-bold text-lg text-ink mb-2">{{ $item['judul'] }}</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">{{ $item['deskripsi'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-6 py-20">
        <div class="bg-ink rounded-3xl px-8 md:px-12 py-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <h3 class="font-serif font-bold text-2xl md:text-3xl text-white text-center md:text-left">
                Siap reservasi lapanganmu untuk<br class="hidden md:block"> latihan minggu ini?
            </h3>

            <a href="#"
                class="bg-brand hover:bg-brand-dark text-white font-semibold px-7 py-3.5 rounded-xl transition whitespace-nowrap">
                Mulai Reservasi
            </a>
        </div>
    </section>

@endsection