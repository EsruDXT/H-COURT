@extends('layouts.admin')

@section('title', 'H-Court - Kelola Lapangan')

@section('content')
<section class="mb-8">
    <p class="mb-2 text-sm font-semibold tracking-[0.15em] text-brand">KELOLA LAPANGAN</p>
    <h2 class="mb-3 font-serif text-4xl font-bold text-gray-900 md:text-5xl">Data Lapangan Olahraga</h2>
    <p class="mb-6 max-w-md text-gray-500">Tambah, ubah, nonaktifkan lapangan, dan atur jam operasionalnya.</p>
    <div class="flex flex-wrap items-center justify-between gap-4">
        <p class="text-sm text-gray-500"><span class="font-semibold text-gray-900">{{ $courts->count() }}</span> lapangan terdaftar</p>
        <a href="{{ route('admin.courts.create') }}" class="rounded-lg bg-navy px-6 py-3 text-sm font-semibold text-white">+ Tambah Lapangan</a>
    </div>
</section>

<div class="grid grid-cols-1 gap-6 md:grid-cols-3">
    @forelse ($courts as $court)
        <article class="overflow-hidden rounded-xl border border-stone-200 bg-white {{ $court->is_active ? '' : 'opacity-60' }}">
            <img src="{{ $court->imageUrl() }}" class="h-56 w-full object-cover" alt="{{ $court->name }}">
            <div class="p-5">
                <h3 class="mb-1 text-xl font-semibold text-gray-900">{{ $court->name }}</h3>
                @unless ($court->is_active)<p class="mb-2 text-xs font-semibold text-red-500">Nonaktif</p>@endunless
                <p class="mb-1 text-sm text-gray-500">{{ substr($court->opens_at, 0, 5) }}–{{ substr($court->closes_at, 0, 5) }} · {{ $court->location }}</p>
                <p class="mb-4 text-xs text-gray-400">{{ $court->reservations_count }} reservasi tercatat</p>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('admin.courts.edit', $court) }}" class="rounded-lg border border-stone-300 py-2.5 text-center text-sm font-medium text-gray-800">Edit</a>
                    <a href="{{ route('admin.courts.operating-hours.edit', $court) }}" class="rounded-lg bg-amber-100 py-2.5 text-center text-sm font-semibold text-amber-800">Jadwal operasional</a>
                </div>
                <form method="POST" action="{{ route('admin.courts.toggle', $court) }}" class="mt-2">
                    @csrf @method('PATCH')
                    <button class="w-full rounded-lg bg-stone-100 py-2.5 text-sm font-medium text-gray-700">{{ $court->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                </form>
                @if ($court->reservations_count === 0)
                    <form method="POST" action="{{ route('admin.courts.destroy', $court) }}" class="mt-2" onsubmit="return confirm('Yakin ingin menghapus lapangan ini?')">
                        @csrf @method('DELETE')
                        <button class="w-full rounded-lg bg-red-50 py-2.5 text-sm font-medium text-red-600">Hapus lapangan</button>
                    </form>
                @endif
            </div>
        </article>
    @empty
        <p class="text-gray-500 md:col-span-3">Belum ada lapangan terdaftar.</p>
    @endforelse
</div>
@endsection
