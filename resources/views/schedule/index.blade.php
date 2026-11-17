
@extends('layouts.app')

@section('title', 'Jadwal Lapangan')

@section('content')
<div class="bg-[#F7F3EC] min-h-screen py-10">
    <div class="max-w-6xl mx-auto px-4">

        {{-- Page Header --}}
        <p class="text-xs font-semibold tracking-wide text-neutral-500 uppercase mb-2">
            Jadwal Lapangan
        </p>

        <h1 class="text-3xl font-serif font-semibold text-neutral-900 mb-2">
            Cek ketersediaan sebelum reservasi
        </h1>

        <p class="text-sm text-neutral-500 max-w-2xl mb-8">
            Lihat ketersediaan jadwal lapangan futsal, basket, dan badminton
            sebelum mengajukan reservasi.
        </p>

        <div class="bg-white rounded-2xl border border-neutral-200 shadow-sm p-6">

            {{-- Pilihan Kategori --}}
            <div class="flex flex-wrap gap-2 mb-6">

                @foreach ($courtTypes as $slug => $label)
                    <a
                        href="{{ route('schedule.index', ['category' => $slug]) }}"
                        class="px-5 py-2 rounded-full text-sm font-medium transition
                            {{ $selectedCategory === $slug
                                ? 'bg-neutral-900 text-white'
                                : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200' }}"
                    >
                        {{ $label }}
                    </a>
                @endforeach

            </div>

            {{-- Informasi Kategori --}}
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-neutral-900">
                    Jadwal {{ $courtTypes[$selectedCategory] ?? 'Lapangan' }}
                </h2>

                <p class="text-sm text-neutral-500">
                    Berikut jadwal seluruh lapangan dalam kategori ini.
                </p>
            </div>
            {{-- Navigasi Minggu --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">

    <a
        href="{{ route('schedule.index', [
            'category' => $selectedCategory,
            'week' => $weekOffset - 1
        ]) }}"
        class="px-4 py-2 rounded-lg border border-neutral-300 text-sm
               text-neutral-700 hover:bg-neutral-100 transition
               {{ $weekOffset <= 0 ? 'pointer-events-none opacity-40' : '' }}"
    >
        ← Minggu sebelumnya
    </a>

    <div class="text-center">
        <p class="font-semibold text-neutral-900">
            {{ $weekLabel }}
        </p>
        <p class="text-xs text-neutral-500">
            Jadwal minggu ke-{{ $weekOffset + 1 }}
        </p>
    </div>

    <a
        href="{{ route('schedule.index', [
            'category' => $selectedCategory,
            'week' => $weekOffset + 1
        ]) }}"
        class="px-4 py-2 rounded-lg bg-neutral-900 text-white text-sm
               hover:bg-neutral-700 transition"
    >
        Minggu selanjutnya →
    </a>

</div>
            {{-- Keterangan Status --}}
            <div class="flex flex-wrap items-center gap-5 text-xs text-neutral-600 mb-6">

                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-600"></span>
                    Tersedia
                </div>

                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                    Menunggu konfirmasi
                </div>

                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-neutral-400"></span>
                    Sudah terisi
                </div>

            </div>

            {{-- Daftar Lapangan --}}
            @forelse ($courts as $court)

                <div class="mb-8 last:mb-0">

                    {{-- Nama Lapangan --}}
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">

                        <div>
                            <h3 class="font-semibold text-neutral-900">
                                {{ $court->name }}
                            </h3>

                            <p class="text-xs text-neutral-500">
                                {{ $courtTypes[$selectedCategory] }}
                            </p>
                        </div>

                        <a
                            href="{{ route('reservation.create', ['court' => $court->slug]) }}"
                            class="text-sm font-medium text-amber-700 hover:text-amber-900"
                        >
                            Reservasi Lapangan →
                        </a>

                    </div>

                    {{-- Tabel Jadwal --}}
                    <div class="overflow-x-auto rounded-xl border border-neutral-200">

                        <table class="w-full text-sm border-collapse min-w-[600px] table-fixed">

                            <thead>
                                <tr class="bg-neutral-900 text-white">

                                    <th class="py-3 px-4 text-left font-medium w-24">
                                        Jam
                                    </th>

                                    @foreach ($days as $day)
                                        <th class="py-3 px-4 text-center font-medium">
                                            <div>{{ $day }}</div>
                                            <div class="text-xs font-normal text-neutral-400 mt-1">
                                                {{ $dates[$day] ?? '' }}
                                            </div>
                                        </th>
                                    @endforeach

                                </tr>
                            </thead>

                            <tbody>

                                @foreach ($timeSlots as $time)

                                    <tr class="border-t border-neutral-100">

                                        <td class="py-3 px-4 text-neutral-700 font-medium whitespace-nowrap">
                                            {{ $time }}
                                        </td>

                                        @foreach ($days as $day)

                                            @php
                                                $status = $courtSchedules[$court->id][$time][$day] ?? 'available';

                                                $styles = match ($status) {
                                                    'available' => 'bg-emerald-100 text-emerald-700',
                                                    'pending' => 'bg-amber-100 text-amber-700',
                                                    'booked' => 'bg-neutral-200 text-neutral-600',
                                                    default => 'bg-neutral-100 text-neutral-500',
                                                };

                                                $labelText = match ($status) {
                                                    'available' => 'Kosong',
                                                    'pending' => 'Menunggu',
                                                    'booked' => 'Terisi',
                                                    default => '-',
                                                };
                                            @endphp

                                            <td class="py-2 px-2 text-center">
                                                <span class="block rounded-lg py-2 text-xs font-medium {{ $styles }}">
                                                    {{ $labelText }}
                                                </span>
                                            </td>

                                        @endforeach

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            @empty

                <div class="py-12 text-center">
                    <p class="text-neutral-500 text-sm mb-4">
                        Belum ada lapangan aktif untuk kategori ini.
                    </p>

                    <a
                        href="{{ route('schedule.index', ['category' => 'futsal']) }}"
                        class="text-sm font-medium text-amber-700 hover:text-amber-900"
                    >
                        Kembali ke jadwal futsal
                    </a>
                </div>

            @endforelse

        </div>
    </div>
</div>
@endsection
