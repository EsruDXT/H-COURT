@extends('layouts.admin')

@section('title', 'H-COURT - Dashboard Pengelola')

@section('content')
<section class="mb-8">
    <p class="mb-2 text-sm font-medium tracking-[0.15em] text-brand">Dashboard pengelola</p>
    <h2 class="mb-3 font-serif text-4xl font-bold text-gray-900">Kelola semua reservasi lapangan</h2>
    <p class="text-gray-500">Tinjau, terima, atau tolak reservasi yang masuk dari satu tempat.</p>
</section>

<section class="mb-8 grid grid-cols-2 gap-4 md:grid-cols-4">
    <div class="rounded-xl border border-stone-200 bg-white p-5"><p class="mb-2 text-sm text-gray-500">Menunggu persetujuan</p><p class="font-serif text-3xl font-bold text-brand">{{ $stats['pending'] }}</p></div>
    <div class="rounded-xl border border-stone-200 bg-white p-5"><p class="mb-2 text-sm text-gray-500">Disetujui hari ini</p><p class="font-serif text-3xl font-bold text-green-700">{{ $stats['confirmed_today'] }}</p></div>
    <div class="rounded-xl border border-stone-200 bg-white p-5"><p class="mb-2 text-sm text-gray-500">Reservasi aktif bulan ini</p><p class="font-serif text-3xl font-bold text-gray-900">{{ $stats['this_month'] }}</p></div>
    <div class="rounded-xl border border-stone-200 bg-white p-5"><p class="mb-2 text-sm text-gray-500">Lapangan aktif</p><p class="font-serif text-3xl font-bold text-gray-900">{{ $stats['active_courts'] }}</p></div>
</section>

@php
    $tabs = [null => 'Semua', 'pending' => 'Menunggu', 'confirmed' => 'Diterima', 'rejected' => 'Ditolak', 'cancelled' => 'Dibatalkan'];
@endphp
<div class="mb-6 flex flex-wrap items-center gap-3">
    @foreach ($tabs as $key => $label)
        <a href="{{ route('admin.dashboard', $key ? ['status' => $key] : []) }}" class="rounded-full px-5 py-2 text-sm font-medium {{ $status === $key ? 'bg-navy text-white' : 'border border-stone-300 bg-white text-gray-500' }}">{{ $label }}</a>
    @endforeach
</div>

<div class="overflow-x-auto rounded-xl border border-stone-200 bg-white">
    <table class="w-full min-w-[800px] text-left">
        <thead><tr class="bg-navy text-sm text-white">
            <th class="px-6 py-4 font-medium">Peminjam</th><th class="px-6 py-4 font-medium">Lapangan</th><th class="px-6 py-4 font-medium">Tanggal & Jam</th><th class="px-6 py-4 font-medium">Keperluan</th><th class="px-6 py-4 font-medium">Status</th><th class="px-6 py-4 font-medium">Tindakan</th>
        </tr></thead>
        <tbody class="divide-y divide-stone-100">
        @forelse ($reservations as $reservation)
            @php $badge = match ($reservation->status) { 'confirmed' => 'bg-green-100 text-green-700', 'pending' => 'bg-amber-100 text-amber-700', 'rejected' => 'bg-red-100 text-red-500', default => 'bg-neutral-200 text-neutral-600' }; @endphp
            <tr>
                <td class="px-6 py-5"><p class="font-semibold text-gray-900">{{ $reservation->borrower_name }}</p><p class="text-sm text-gray-400">{{ $reservation->class_institution }}</p></td>
                <td class="px-6 py-5">{{ $reservation->court->name }}</td>
                <td class="px-6 py-5">{{ $reservation->reservation_date->locale('id')->translatedFormat('d M Y') }}<span class="block text-sm text-gray-400">{{ $reservation->timeRange() }} ({{ $reservation->durationInHours() }} jam)</span></td>
                <td class="px-6 py-5">{{ $reservation->purpose }}</td>
                <td class="px-6 py-5"><span class="rounded-full px-3 py-1 text-xs font-semibold {{ $badge }}">{{ $reservation->statusLabel() }}</span></td>
                <td class="px-6 py-5"><div class="flex gap-2">
                    @if ($reservation->status === 'pending')
                        <form method="POST" action="{{ route('admin.reservations.approve', $reservation) }}">@csrf @method('PATCH')<button class="rounded-full bg-green-100 px-3 py-1.5 text-xs font-semibold text-green-700">Terima</button></form>
                        <form method="POST" action="{{ route('admin.reservations.reject', $reservation) }}">@csrf @method('PATCH')<button class="rounded-full bg-red-100 px-3 py-1.5 text-xs font-semibold text-red-500">Tolak</button></form>
                    @endif
                    <a href="{{ route('admin.reservations.edit', $reservation) }}" class="rounded-full bg-amber-100 px-3 py-1.5 text-xs font-semibold text-amber-700">Edit</a>
                </div></td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">Belum ada reservasi.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $reservations->links() }}</div>
@endsection
