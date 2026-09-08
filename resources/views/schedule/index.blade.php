
@extends('layouts.app')

@section('title', 'Jadwal Lapangan')

@section('content')
<div class="bg-[#F7F3EC] min-h-screen py-10">
    <div class="max-w-6xl mx-auto px-4">
        <p class="text-xs font-semibold tracking-wide text-neutral-500 uppercase mb-2">
            Jadwal Lapangan
        </p>
        <h1 class="text-3xl font-serif font-semibold text-neutral-900 mb-2">
            Cek ketersediaan sebelum reservasi
        </h1>
        <p class="text-sm text-neutral-500 max-w-2xl mb-8">
            Semua jadwal penggunaan lapangan futsal, basket, dan badminton
            ditampilkan dalam satu tampilan supaya kamu tahu jam mana yang
            masih kosong.
        </p>

        <div class="bg-white rounded-2xl border border-neutral-200 shadow-sm p-6">

            <div class="inline-flex bg-neutral-100 rounded-full p-1 w-fit mb-6">
                @foreach ($courtTypes as $slug => $label)
                    <a
                        href="{{ route('schedule.index') }}"
                        class="px-4 py-2 text-sm rounded-full font-medium transition
                            {{ $loop->first
                                ? 'bg-neutral-900 text-white'
                                : 'text-neutral-600 hover:text-neutral-900' }}"
                    >
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <div class="flex flex-wrap items-center gap-5 text-xs text-neutral-600 mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-600"></span>
                    Tersedia
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                    Menunggu konfirmasi
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-neutral-300"></span>
                    Sudah terisi
                </div>
            </div>

            <div class="overflow-x-auto rounded-xl border border-neutral-200">
                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr class="bg-neutral-900 text-white">
                            <th class="py-3 px-4 text-left font-medium w-24">Jam</th>
                            @foreach ($days as $day)
                                <th class="py-3 px-4 text-center font-medium">
                                    {{ $day }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($timeSlots as $time)
                            <tr class="border-t border-neutral-100">
                                <td class="py-3 px-4 text-neutral-700 font-medium">
                                    {{ $time }}
                                </td>

                                @foreach ($days as $day)
                                    @php
                                        $status = $schedule[$time][$day] ?? 'available';

                                        $styles = match ($status) {
                                            'available' => 'bg-emerald-100 text-emerald-700',
                                            'pending'   => 'bg-amber-100 text-amber-700',
                                            'booked'    => 'bg-neutral-200 text-neutral-500',
                                            default     => 'bg-neutral-100 text-neutral-500',
                                        };

                                        $labelText = $status === 'available' ? 'Kosong' : 'Terisi';
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
    </div>
</div>
@endsection