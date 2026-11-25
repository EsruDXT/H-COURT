@extends('layouts.admin')

@section('title', 'Jadwal Operasional - H-COURT')

@section('content')
<section class="mb-8">
    <p class="mb-2 text-sm font-semibold tracking-widest text-brand">PENGATURAN LAPANGAN</p>
    <h2 class="font-serif text-3xl font-bold text-gray-900">Jadwal Operasional</h2>
    <p class="mt-2 text-sm text-gray-500">Atur jam buka, jam tutup, dan hari libur untuk {{ $court->name }}.</p>
</section>

<form method="POST" action="{{ route('admin.courts.operating-hours.update', $court) }}" class="mb-10 rounded-xl border border-stone-200 bg-white p-6">
    @csrf
    @method('PUT')
    <h3 class="mb-5 text-lg font-semibold text-gray-900">Jam operasional mingguan</h3>
    <div class="space-y-3">
        @foreach ($days as $day => $label)
            @php $item = $hours->get($day); @endphp
            <div class="grid grid-cols-1 items-center gap-3 border-b border-stone-100 pb-3 sm:grid-cols-[120px_1fr_1fr_120px]">
                <p class="font-medium text-gray-800">{{ $label }}</p>
                <div>
                    <label class="mb-1 block text-xs text-gray-500">Buka</label>
                    <input type="time" name="hours[{{ $day }}][opens_at]" value="{{ old("hours.$day.opens_at", $item?->opens_at ? substr($item->opens_at, 0, 5) : '') }}"
                        class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs text-gray-500">Tutup</label>
                    <input type="time" name="hours[{{ $day }}][closes_at]" value="{{ old("hours.$day.closes_at", $item?->closes_at ? substr($item->closes_at, 0, 5) : '') }}"
                        class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                </div>
                <label class="flex items-center gap-2 text-sm text-gray-600">
                    <input type="hidden" name="hours[{{ $day }}][is_closed]" value="0">
                    <input type="checkbox" name="hours[{{ $day }}][is_closed]" value="1" @checked(old("hours.$day.is_closed", $item?->is_closed)) class="rounded border-stone-300 text-brand">
                    Tutup
                </label>
            </div>
        @endforeach
    </div>
    <div class="mt-6 flex flex-wrap gap-3">
        <a href="{{ route('admin.courts.index') }}" class="rounded-lg border border-stone-300 px-5 py-2.5 text-sm text-gray-600">Kembali</a>
        <button class="rounded-lg bg-navy px-5 py-2.5 text-sm font-semibold text-white">Simpan jadwal</button>
    </div>
</form>

<section class="rounded-xl border border-stone-200 bg-white p-6">
    <h3 class="text-lg font-semibold text-gray-900">Penutupan khusus / pemeliharaan</h3>
    <p class="mb-5 mt-1 text-sm text-gray-500">Tanggal yang ditambahkan di sini tidak dapat dipilih untuk reservasi.</p>
    <form method="POST" action="{{ route('admin.courts.closures.store', $court) }}" class="mb-6 grid grid-cols-1 gap-3 sm:grid-cols-[1fr_2fr_auto]">
        @csrf
        <input type="date" name="closure_date" min="{{ today()->toDateString() }}" required class="rounded-lg border border-stone-300 px-3 py-2 text-sm">
        <input type="text" name="reason" maxlength="200" placeholder="Alasan, mis. pemeliharaan lapangan" class="rounded-lg border border-stone-300 px-3 py-2 text-sm">
        <button class="rounded-lg bg-brand px-4 py-2 text-sm font-semibold text-white">Tambah tanggal</button>
    </form>

    <div class="divide-y divide-stone-100">
        @forelse ($closures as $closure)
            <div class="flex flex-wrap items-center justify-between gap-3 py-3">
                <div>
                    <p class="font-medium text-gray-800">{{ $closure->closure_date->locale('id')->translatedFormat('l, d F Y') }}</p>
                    <p class="text-sm text-gray-500">{{ $closure->reason ?: 'Ditutup' }}</p>
                </div>
                <form method="POST" action="{{ route('admin.courts.closures.destroy', [$court, $closure]) }}" onsubmit="return confirm('Hapus tanggal penutupan ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="text-sm font-semibold text-red-600 underline">Hapus</button>
                </form>
            </div>
        @empty
            <p class="py-4 text-sm text-gray-500">Belum ada tanggal penutupan khusus.</p>
        @endforelse
    </div>
</section>
@endsection
