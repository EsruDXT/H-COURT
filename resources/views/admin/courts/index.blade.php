@extends('layouts.admin')

@section('title', 'H-Court - Kelola Lapangan')

@section('content')

  <section class="mb-8">
    <p class="text-sm tracking-[0.15em] text-brand font-semibold mb-2">KELOLA LAPANGAN</p>
    <h2 class="font-serif text-4xl md:text-5xl font-bold text-gray-900 mb-3">Data Lapangan Olahraga</h2>
    <p class="text-gray-500 max-w-md mb-6">Tambah, ubah, atau nonaktifkan lapangan yang bisa direservasi siswa.</p>

    <div class="flex flex-wrap items-center justify-between gap-4">
      <p class="text-sm text-gray-500"><span class="font-semibold text-gray-900">{{ $courts->count() }}</span> lapangan terdaftar</p>
      <a href="{{ route('admin.courts.create') }}" class="px-6 py-3 rounded-lg bg-navy text-white font-semibold text-sm">+ Tambah Lapangan</a>
    </div>
  </section>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @forelse ($courts as $court)
      <div class="bg-white rounded-xl border border-stone-200 overflow-hidden {{ $court->is_active ? '' : 'opacity-60' }}">
        <img src="{{ $court->imageUrl() }}" class="w-full h-56 object-cover" alt="{{ $court->name }}">
        <div class="p-5">
          <h3 class="text-xl font-semibold text-gray-900 mb-1">{{ $court->name }}</h3>
          @unless ($court->is_active)
            <p class="text-xs font-semibold text-red-500 mb-2">Nonaktif</p>
          @endunless
          <div class="flex items-center gap-2 text-gray-500 text-sm mb-1">
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
            {{ substr($court->opens_at, 0, 5) }}-{{ substr($court->closes_at, 0, 5) }}
          </div>
          <div class="flex items-center gap-2 text-gray-500 text-sm mb-1">
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 21s-6-5.686-6-10a6 6 0 1112 0c0 4.314-6 10-6 10z"/><circle cx="12" cy="11" r="2"/></svg>
            {{ $court->location }}
          </div>
          <p class="text-xs text-gray-400 mb-4">{{ $court->reservations_count }} reservasi tercatat</p>

          <div class="flex gap-2">
            <a href="{{ route('admin.courts.edit', $court) }}" class="flex-1 text-center py-2.5 rounded-lg border border-stone-300 text-gray-800 font-medium text-sm">Edit</a>
            <form method="POST" action="{{ route('admin.courts.toggle', $court) }}" class="flex-1">
              @csrf
              @method('PATCH')
              <button type="submit" class="w-full py-2.5 rounded-lg bg-amber-100 text-amber-700 font-medium text-sm">
                {{ $court->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
              </button>
            </form>
          </div>

          <details class="mt-3">
            <summary class="cursor-pointer text-center py-2.5 rounded-lg bg-red-100 text-red-500 font-medium text-sm list-none">Hapus</summary>
            <form method="POST" action="{{ route('admin.courts.destroy', $court) }}" class="mt-2 text-center">
              @csrf
              @method('DELETE')
              <p class="text-xs text-gray-500 mb-2">Yakin ingin menghapus lapangan ini?</p>
              <button type="submit" class="px-5 py-2 rounded-lg bg-red-500 text-white font-semibold text-sm">Ya, hapus</button>
            </form>
          </details>
        </div>
      </div>
    @empty
      <p class="text-gray-500 md:col-span-3">Belum ada lapangan terdaftar.</p>
    @endforelse
  </div>

@endsection
