@extends('layouts.admin')

@section('title', 'H-Court - Edit Reservasi')

@section('content')

  <section class="mb-8">
    <p class="text-sm tracking-[0.15em] text-brand font-semibold mb-2">EDIT RESERVASI</p>
    <h2 class="font-serif text-4xl font-bold text-gray-900 mb-3">Reservasi {{ $reservation->borrower_name }}</h2>
    <p class="text-gray-500">{{ $reservation->class_institution }} &middot; {{ $reservation->phone_number }} &middot; {{ $reservation->participant_count }} peserta</p>
  </section>

  <form method="POST" action="{{ route('admin.reservations.update', $reservation) }}"
        class="bg-white rounded-xl border border-stone-200 p-8 max-w-2xl space-y-5">
    @csrf
    @method('PUT')

    <div>
      <label for="court_id" class="block text-sm font-medium text-gray-700 mb-1">Lapangan</label>
      <select id="court_id" name="court_id" class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white text-sm">
        @foreach ($courts as $court)
          <option value="{{ $court->id }}" @selected((int) old('court_id', $reservation->court_id) === $court->id)>{{ $court->name }}</option>
        @endforeach
      </select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label for="reservation_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
        <input id="reservation_date" type="date" name="reservation_date"
               value="{{ old('reservation_date', $reservation->reservation_date->toDateString()) }}"
               class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white text-sm">
      </div>
      <div>
        <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Jam mulai</label>
        <select id="start_time" name="start_time" class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white text-sm">
          @foreach ($timeSlots as $slot)
            <option value="{{ $slot }}" @selected(old('start_time', $reservation->startHm()) === $slot)>{{ $slot }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label for="duration" class="block text-sm font-medium text-gray-700 mb-1">Durasi</label>
        <select id="duration" name="duration" class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white text-sm">
          @foreach ($durations as $hours)
            <option value="{{ $hours }}" @selected((int) old('duration', $reservation->durationInHours()) === $hours)>{{ $hours }} jam</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label for="purpose" class="block text-sm font-medium text-gray-700 mb-1">Keperluan</label>
        <select id="purpose" name="purpose" class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white text-sm">
          @foreach ($purposes as $purpose)
            <option value="{{ $purpose }}" @selected(old('purpose', $reservation->purpose) === $purpose)>{{ $purpose }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
        <select id="status" name="status" class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white text-sm">
          @foreach ($statuses as $value => $label)
            <option value="{{ $value }}" @selected(old('status', $reservation->status) === $value)>{{ $label }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="flex gap-4 pt-2">
      <a href="{{ route('admin.dashboard') }}" class="px-6 py-2.5 rounded-lg border border-stone-300 text-gray-500 font-medium">Batal</a>
      <button type="submit" class="px-6 py-2.5 rounded-lg bg-brand text-white font-semibold">Simpan Perubahan</button>
    </div>
  </form>

@endsection
