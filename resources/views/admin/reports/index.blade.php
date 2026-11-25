@extends('layouts.admin')

@section('title', 'Laporan Statistik - H-COURT')

@section('content')
<section class="mb-8">
    <p class="mb-2 text-sm font-semibold tracking-widest text-brand">ANALISIS RESERVASI</p>
    <h2 class="font-serif text-3xl font-bold text-gray-900">Laporan Statistik</h2>
    <p class="mt-2 text-sm text-gray-500">Ringkasan permintaan reservasi berdasarkan tanggal penggunaan lapangan.</p>
</section>

<form method="GET" action="{{ route('admin.reports.index') }}" class="mb-6 flex flex-wrap items-end gap-3 rounded-xl border border-stone-200 bg-white p-5">
    <div>
        <label class="mb-1 block text-xs font-medium text-gray-500">Dari tanggal</label>
        <input type="date" name="from" value="{{ $from }}" required class="rounded-lg border border-stone-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="mb-1 block text-xs font-medium text-gray-500">Sampai tanggal</label>
        <input type="date" name="to" value="{{ $to }}" min="{{ $from }}" required class="rounded-lg border border-stone-300 px-3 py-2 text-sm">
    </div>
    <button class="rounded-lg bg-navy px-5 py-2.5 text-sm font-semibold text-white">Terapkan filter</button>
    <a href="{{ route('admin.reports.export', ['from' => $from, 'to' => $to]) }}" class="rounded-lg bg-brand px-5 py-2.5 text-sm font-semibold text-white">Ekspor CSV</a>
</form>

<div class="mb-8 grid grid-cols-2 gap-4 lg:grid-cols-5">
    @foreach ([
        ['Semua reservasi', $stats['total']],
        ['Menunggu', $stats['pending']],
        ['Diterima', $stats['confirmed']],
        ['Ditolak', $stats['rejected']],
        ['Dibatalkan', $stats['cancelled']],
    ] as [$label, $value])
        <div class="rounded-xl border border-stone-200 bg-white p-5">
            <p class="text-xs text-gray-500">{{ $label }}</p>
            <p class="mt-2 font-serif text-3xl font-bold text-gray-900">{{ $value }}</p>
        </div>
    @endforeach
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <section class="rounded-xl border border-stone-200 bg-white p-6">
        <h3 class="mb-1 text-lg font-semibold text-gray-900">Penggunaan per lapangan</h3>
        <p class="mb-5 text-xs text-gray-500">Jumlah reservasi dan yang berstatus diterima dalam periode terpilih.</p>
        <div class="space-y-5">
            @forelse ($courtStats as $item)
                @php $width = $stats['total'] > 0 ? (int) round(($item->confirmed / $stats['total']) * 100) : 0; @endphp
                <div>
                    <div class="mb-1 flex justify-between gap-3 text-sm">
                        <span class="font-medium text-gray-800">{{ $item->court?->name ?? 'Lapangan dihapus' }}</span>
                        <span class="text-gray-500">{{ $item->confirmed }} diterima / {{ $item->total }} total</span>
                    </div>
                    <div class="h-2 overflow-hidden rounded-full bg-stone-100">
                        <div class="h-full rounded-full bg-emerald-600" style="width: {{ $width }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Belum ada data pada periode ini.</p>
            @endforelse
        </div>
    </section>

    <section class="rounded-xl border border-stone-200 bg-white p-6">
        <h3 class="mb-1 text-lg font-semibold text-gray-900">Jam mulai paling ramai</h3>
        <p class="mb-5 text-xs text-gray-500">Dihitung dari reservasi yang sudah diterima.</p>
        <div class="space-y-4">
            @forelse ($popularHours as $item)
                <div>
                    <div class="mb-1 flex justify-between text-sm">
                        <span class="font-medium text-gray-800">{{ substr($item->start_time, 0, 5) }}</span>
                        <span class="text-gray-500">{{ $item->total }} reservasi</span>
                    </div>
                    <div class="h-2 overflow-hidden rounded-full bg-stone-100">
                        <div class="h-full rounded-full bg-amber-600" style="width: {{ (int) round(($item->total / $maxPopularHour) * 100) }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Belum ada reservasi diterima pada periode ini.</p>
            @endforelse
        </div>
    </section>
</div>

<section class="mt-6 rounded-xl border border-stone-200 bg-white p-6">
    <h3 class="text-lg font-semibold text-gray-900">Catatan perhitungan</h3>
    <p class="mt-2 text-sm leading-6 text-gray-600">Periode laporan menggunakan <strong>tanggal penggunaan lapangan</strong> (`reservation_date`). Reservasi dihitung menurut status terakhirnya. Data persetujuan pada dashboard harian menggunakan waktu tinjauan admin (`reviewed_at`), sehingga arti kedua statistik tersebut memang berbeda.</p>
</section>
@endsection
