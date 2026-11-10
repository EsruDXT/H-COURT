@php $court = $court ?? null; @endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data"
      class="bg-white rounded-xl border border-stone-200 p-8 max-w-2xl space-y-5">
  @csrf
  @if ($court)
    @method('PUT')
  @endif

  <div>
    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama lapangan</label>
    <input id="name" type="text" name="name" value="{{ old('name', $court?->name) }}" required
           class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white text-sm">
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
      <select id="category" name="category" class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white text-sm">
        @foreach ($categories as $value => $label)
          <option value="{{ $value }}" @selected(old('category', $court?->category) === $value)>{{ $label }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Lokasi / permukaan</label>
      <input id="location" type="text" name="location" value="{{ old('location', $court?->location) }}" required
             class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white text-sm">
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label for="opens_at" class="block text-sm font-medium text-gray-700 mb-1">Jam buka</label>
      <input id="opens_at" type="time" name="opens_at" value="{{ old('opens_at', $court ? substr($court->opens_at, 0, 5) : '07:00') }}" required
             class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white text-sm">
    </div>
    <div>
      <label for="closes_at" class="block text-sm font-medium text-gray-700 mb-1">Jam tutup</label>
      <input id="closes_at" type="time" name="closes_at" value="{{ old('closes_at', $court ? substr($court->closes_at, 0, 5) : '21:00') }}" required
             class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white text-sm">
    </div>
  </div>

  <div>
    <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Gambar (opsional, maks. 2 MB)</label>
    <input id="image" type="file" name="image" accept="image/*"
           class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white text-sm">
  </div>

  <div class="flex gap-4 pt-2">
    <a href="{{ route('admin.courts.index') }}" class="px-6 py-2.5 rounded-lg border border-stone-300 text-gray-500 font-medium">Batal</a>
    <button type="submit" class="px-6 py-2.5 rounded-lg bg-brand text-white font-semibold">{{ $submit }}</button>
  </div>
</form>
