@extends('layouts.admin')

@section('title', 'H-Court - Dashboard Pengelola')

@section('content')

  <section class="mb-8">
    <p class="text-sm tracking-[0.15em] text-brand font-medium mb-2">Dashboard pengelola</p>
    <h2 class="font-serif text-4xl md:text-5xl font-bold text-gray-900 mb-3">Kelola semua reservasi lapangan</h2>
    <p class="text-gray-500">Tinjau, terima, atau tolak reservasi yang masuk dari satu tempat.</p>
  </section>

  <section class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white border border-stone-200 rounded-xl p-5">
      <p class="text-sm text-gray-500 mb-2">Menunggu persetujuan</p>
      <p class="font-serif text-3xl font-bold text-brand">{{ $stats['pending'] }}</p>
    </div>
    <div class="bg-white border border-stone-200 rounded-xl p-5">
      <p class="text-sm text-gray-500 mb-2">Diterima untuk hari ini</p>
      <p class="font-serif text-3xl font-bold text-green-700">{{ $stats['confirmed_today'] }}</p>
    </div>
    <div class="bg-white border border-stone-200 rounded-xl p-5">
      <p class="text-sm text-gray-500 mb-2">Reservasi bulan ini</p>
      <p class="font-serif text-3xl font-bold text-gray-900">{{ $stats['this_month'] }}</p>
    </div>
    <div class="bg-white border border-stone-200 rounded-xl p-5">
      <p class="text-sm text-gray-500 mb-2">Lapangan aktif</p>
      <p class="font-serif text-3xl font-bold text-gray-900">{{ $stats['active_courts'] }}</p>
    </div>
  </section>

  @php
    $tabs = [
      null => 'Semua',
      'pending' => 'Menunggu',
      'confirmed' => 'Diterima',
      'rejected' => 'Ditolak',
    ];
  @endphp

  <div class="flex flex-wrap items-center gap-3 mb-6">
    @foreach ($tabs as $key => $label)
      <a href="{{ route('admin.dashboard', $key ? ['status' => $key] : []) }}"
         class="px-5 py-2 rounded-full text-sm font-medium {{ $status === $key ? 'bg-navy text-white' : 'bg-white border border-stone-300 text-gray-500' }}">
        {{ $label }}
      </a>
    @endforeach
  </div>

  <div class="bg-white rounded-xl border border-stone-200 overflow-hidden overflow-x-auto">
    <table class="w-full text-left min-w-[800px]">
      <thead>
        <tr class="bg-navy text-white text-sm">
          <th class="px-6 py-4 font-medium">Peminjam</th>
          <th class="px-6 py-4 font-medium">Lapangan</th>
          <th class="px-6 py-4 font-medium">Tanggal & Jam</th>
          <th class="px-6 py-4 font-medium">Keperluan</th>
          <th class="px-6 py-4 font-medium">Status</th>
          <th class="px-6 py-4 font-medium">Tindakan</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-stone-100">
        @forelse ($reservations as $reservation)
          @php
            $badge = match ($reservation->status) {
              'confirmed' => 'bg-green-100 text-green-700',
              'pending' => 'bg-amber-100 text-amber-700',
              'rejected' => 'bg-red-100 text-red-500',
              default => 'bg-neutral-200 text-neutral-500',
            };
          @endphp
          <tr>
            <td class="px-6 py-5">
              <p class="font-semibold text-gray-900">{{ $reservation->borrower_name }}</p>
              <p class="text-sm text-gray-400">{{ $reservation->class_institution }}</p>
            </td>
            <td class="px-6 py-5">{{ $reservation->court->name }}</td>
            <td class="px-6 py-5">
              {{ $reservation->reservation_date->locale('id')->translatedFormat('d M Y') }}
              <span class="block text-sm text-gray-400">{{ $reservation->timeRange() }} ({{ $reservation->durationInHours() }} jam)</span>
            </td>
            <td class="px-6 py-5">{{ $reservation->purpose }}</td>
            <td class="px-6 py-5">
              <span class="text-xs font-semibold px-3 py-1 rounded-full {{ $badge }}">{{ $reservation->statusLabel() }}</span>
            </td>
            <td class="px-6 py-5">
              <div class="flex gap-2">
                @if ($reservation->status === 'pending')
                  <form method="POST" action="{{ route('admin.reservations.approve', $reservation) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded-full bg-green-100 text-green-700">Terima</button>
                  </form>
                  <form method="POST" action="{{ route('admin.reservations.reject', $reservation) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded-full bg-red-100 text-red-500">Tolak</button>
                  </form>
                @endif
                <a href="{{ route('admin.reservations.edit', $reservation) }}"
                   class="text-xs font-semibold px-3 py-1.5 rounded-full bg-amber-100 text-amber-700">Edit</a>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">Belum ada reservasi.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-6">{{ $reservations->links() }}</div>

@endsection
