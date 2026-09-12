
@extends('layouts.app')

@section('title', 'Reservasi Saya')

@section('content')
<div class="bg-[#F7F3EC] min-h-screen py-10">
    <div class="max-w-5xl mx-auto px-4">

        {{-- Page header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase mb-2">
                    Reservasi Lapangan
                </p>
                <h1 class="text-3xl font-serif font-semibold text-neutral-900 mb-2">
                    Reservasi Saya
                </h1>
                <p class="text-sm text-neutral-500 max-w-xl">
                    Daftar reservasi yang pernah kamu ajukan, beserta status persetujuannya.
                </p>
            </div>

            <a
                href="{{ route('reservation.create') }}"
                class="shrink-0 px-6 py-2.5 rounded-full text-sm font-medium bg-amber-700 text-white hover:bg-amber-800 text-center"
            >
                + Ajukan Reservasi
            </a>
        </div>

        {{-- Reservation list --}}
        @if (count($reservations) === 0)
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-sm p-10 text-center">
                <p class="text-neutral-500 text-sm mb-4">
                    Kamu belum punya reservasi. Yuk ajukan reservasi lapangan pertamamu.
                </p>
                <a
                    href="{{ route('reservation.create') }}"
                    class="inline-block px-6 py-2.5 rounded-full text-sm font-medium bg-amber-700 text-white hover:bg-amber-800"
                >
                    Ajukan Reservasi
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($reservations as $reservation)
                    @php
                        $statusStyles = match ($reservation['status']) {
                            'confirmed' => 'bg-emerald-100 text-emerald-700',
                            'pending'   => 'bg-amber-100 text-amber-700',
                            'rejected'  => 'bg-red-100 text-red-700',
                            default     => 'bg-neutral-100 text-neutral-500',
                        };

                        $statusLabel = match ($reservation['status']) {
                            'confirmed' => 'Dikonfirmasi',
                            'pending'   => 'Menunggu Persetujuan',
                            'rejected'  => 'Ditolak',
                            default     => '-',
                        };
                    @endphp

                    <div class="bg-white rounded-2xl border border-neutral-200 shadow-sm p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <p class="font-semibold text-neutral-900">{{ $reservation['court'] }}</p>
                            <p class="text-sm text-neutral-500">
                                {{ $reservation['date'] }} &middot; {{ $reservation['time'] }}
                            </p>
                        </div>

                        <span class="w-fit px-3 py-1 rounded-full text-xs font-medium {{ $statusStyles }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</div>
@endsection