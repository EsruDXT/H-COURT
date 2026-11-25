@extends('layouts.app')

@section('title', 'Reservasi Saya')

@section('content')
<div class="min-h-screen bg-[#F7F3EC] py-10">
    <div class="mx-auto max-w-5xl px-4">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-amber-700">Reservasi Lapangan</p>
                <h1 class="mb-2 font-serif text-3xl font-semibold text-neutral-900">Reservasi Saya</h1>
                <p class="max-w-xl text-sm text-neutral-500">Daftar reservasi yang pernah kamu ajukan, beserta status persetujuannya.</p>
            </div>
            <a href="{{ route('reservation.create') }}" class="shrink-0 rounded-full bg-amber-700 px-6 py-2.5 text-center text-sm font-medium text-white hover:bg-amber-800">+ Ajukan Reservasi</a>
        </div>

        @if (session('reservation_submitted'))
            @php
                $submitted = session('reservation_submitted');
            @endphp
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-white p-5 shadow-sm">
                <p class="mb-1 font-semibold text-emerald-700">Reservasi berhasil diajukan!</p>
                <p class="mb-3 text-sm text-neutral-500">Pengelola akan meninjau permintaanmu.</p>
                <p class="text-sm text-neutral-800">{{ $submitted['court'] }} &middot; {{ $submitted['date'] }} &middot; {{ $submitted['time'] }}
                    <span class="ml-2 rounded-full bg-amber-100 px-3 py-1 text-xs font-medium text-amber-700">{{ $submitted['status'] }}</span>
                </p>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-6 rounded-xl bg-emerald-50 p-3 text-sm text-emerald-700">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-6 rounded-xl bg-red-50 p-3 text-sm text-red-700">
                @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        @forelse ($reservations as $reservation)
            @php
                $statusStyles = match ($reservation->status) {
                    'confirmed' => 'bg-emerald-100 text-emerald-700',
                    'pending' => 'bg-amber-100 text-amber-700',
                    'rejected' => 'bg-red-100 text-red-700',
                    'cancelled' => 'bg-neutral-200 text-neutral-600',
                    default => 'bg-neutral-100 text-neutral-500',
                };
            @endphp
            <article class="mb-4 flex flex-col gap-4 rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="font-semibold text-neutral-900">{{ $reservation->court->name }}</p>
                    <p class="mt-1 text-sm text-neutral-500">{{ $reservation->reservation_date->locale('id')->translatedFormat('l, d F Y') }} &middot; {{ $reservation->timeRange() }}</p>
                    <p class="mt-2 text-xs text-neutral-400">Diajukan {{ $reservation->created_at->locale('id')->translatedFormat('d F Y, H:i') }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusStyles }}">{{ $reservation->statusLabel() }}</span>
                    @if ($reservation->canBeCancelledByUser())
                        <form method="POST" action="{{ route('reservation.cancel', $reservation) }}" onsubmit="return confirm('Yakin ingin membatalkan reservasi ini?')">
                            @csrf
                            @method('PATCH')
                            <button class="rounded-full border border-red-200 px-4 py-2 text-xs font-semibold text-red-600 hover:bg-red-50">Batalkan reservasi</button>
                        </form>
                    @endif
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-neutral-200 bg-white p-10 text-center">
                <p class="mb-4 text-sm text-neutral-500">Kamu belum punya reservasi. Yuk ajukan reservasi lapangan pertamamu.</p>
                <a href="{{ route('reservation.create') }}" class="inline-block rounded-full bg-amber-700 px-6 py-2.5 text-sm font-medium text-white">Ajukan Reservasi</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
