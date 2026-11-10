@extends('layouts.app')

@section('title', 'Reservasi Lapangan')

@section('content')
    <div class="bg-[#F7F3EC] min-h-screen py-10">
        <div class="max-w-5xl mx-auto px-4">

            {{-- HEADER --}}
            <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase mb-2">
                Reservasi Lapangan
            </p>

            <h1 class="text-3xl font-serif font-semibold text-neutral-900 mb-2">
                Ajukan reservasi kamu
            </h1>

            <p class="text-sm text-neutral-500 max-w-2xl mb-8">
                Pilih lapangan, tentukan hari dan jam, lengkapi data peminjam,
                lalu ajukan. Pengelola akan meninjau dan mengonfirmasi.
            </p>

            {{-- NOTIFIKASI ERROR --}}
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                    <p class="text-sm font-semibold text-red-700 mb-2">
                        Terdapat kesalahan:
                    </p>
                    <ul class="list-disc pl-5 text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- STEP INDICATOR --}}
            <div class="flex items-center gap-3 mb-8 flex-wrap">

                @foreach ($stepLabels as $index => $label)

                    @php $number = $index + 1; @endphp

                    <div class="flex items-center gap-3">

                        <div class="flex items-center gap-2">

                            <div @class([
                                'w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold border',
                                'bg-emerald-600 border-emerald-600 text-white' => $number < $step,
                                'bg-amber-700 border-amber-700 text-white' => $number === $step,
                                'bg-white border-neutral-300 text-neutral-400' => $number > $step,
                            ])>

                                @if ($number < $step)
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                @else
                                    {{ $number }}
                                @endif

                            </div>

                            <span @class([
                                'text-sm',
                                'text-neutral-900 font-semibold' => $number === $step,
                                'text-neutral-400' => $number !== $step,
                            ])>
                                {{ $label }}
                            </span>

                        </div>

                        @unless ($loop->last)
                            <div class="w-10 h-px bg-neutral-300"></div>
                        @endunless

                    </div>

                @endforeach

            </div>


            {{-- ====================================== --}}
            {{-- LANGKAH 1: PILIH LAPANGAN --}}
            {{-- ====================================== --}}

            @if ($step === 1)

                <form method="GET" action="{{ route('reservation.create') }}">

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">

                        @forelse ($courts as $item)

                            <label class="cursor-pointer">

                                <input type="radio" name="court" value="{{ $item->slug }}" class="peer sr-only" required>

                                <div
                                    class="bg-white rounded-2xl border-2 border-transparent overflow-hidden transition hover:border-neutral-200 peer-checked:border-amber-700">

                                    <div class="h-40 bg-neutral-200">

                                        @if ($item->image)
                                            <img src="{{ asset($item->image) }}" alt="{{ $item->name }}"
                                                class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-neutral-400">
                                                Tidak ada gambar
                                            </div>
                                        @endif

                                    </div>

                                    <div class="p-4">

                                        <p class="font-semibold text-neutral-900">
                                            {{ $item->name }}
                                        </p>

                                        <p class="text-xs text-neutral-500 mt-1">
                                            {{ $item->location }}
                                        </p>

                                        <p class="text-xs text-amber-700 mt-2 font-medium">
                                            {{ ucfirst($item->category) }}
                                        </p>

                                    </div>

                                </div>

                            </label>

                        @empty

                            <div class="sm:col-span-3 bg-white rounded-xl p-8 text-center">
                                <p class="text-neutral-500">
                                    Belum ada lapangan yang tersedia.
                                </p>
                            </div>

                        @endforelse

                    </div>

                    <div class="flex justify-end">

                        <button type="submit"
                            class="px-6 py-2.5 rounded-full text-sm font-medium bg-amber-700 text-white hover:bg-amber-800 transition">
                            Lanjutkan
                        </button>

                    </div>

                </form>

            @endif


            {{-- ====================================== --}}
            {{-- LANGKAH 2: PILIH HARI --}}
            {{-- ====================================== --}}

            @if ($step === 2)

                <form method="GET" action="{{ route('reservation.create') }}"
                    class="bg-white rounded-2xl border border-neutral-200 shadow-sm p-6">

                    <input type="hidden" name="court" value="{{ $court->slug }}">

                    <span class="inline-block text-xs font-medium bg-amber-100 text-amber-800 px-3 py-1 rounded-full mb-6">
                        Lapangan: {{ $court->name }}
                    </span>

                    <p class="text-xs font-semibold text-neutral-500 uppercase mb-3">
                        Pilih hari reservasi
                    </p>

                    <div class="grid grid-cols-3 sm:grid-cols-6 gap-3 mb-2">

                        @foreach ($days as $item)

                            <label class="cursor-pointer">

                                <input type="radio" name="date" value="{{ $item['value'] }}" class="peer sr-only" required>

                                <div
                                    class="rounded-xl border border-neutral-200 py-4 text-center transition hover:border-neutral-300 peer-checked:border-amber-700 peer-checked:bg-amber-50">

                                    <p class="text-[10px] text-neutral-400 font-medium">
                                        {{ $item['label'] }}
                                    </p>

                                    <p class="text-lg font-semibold text-neutral-800">
                                        {{ $item['date'] }}
                                    </p>

                                </div>

                            </label>

                        @endforeach

                    </div>

                    <div class="flex justify-between mt-8">

                        <a href="{{ route('reservation.create') }}"
                            class="px-6 py-2.5 rounded-full text-sm font-medium border border-neutral-300 text-neutral-700 hover:bg-neutral-50">
                            Kembali
                        </a>

                        <button type="submit"
                            class="px-6 py-2.5 rounded-full text-sm font-medium bg-amber-700 text-white hover:bg-amber-800">
                            Lanjutkan
                        </button>

                    </div>

                </form>

            @endif


            {{-- ====================================== --}}
            {{-- LANGKAH 3: PILIH JAM --}}
            {{-- ====================================== --}}

            @if ($step === 3)

                <form method="GET" action="{{ route('reservation.create') }}"
                    class="bg-white rounded-2xl border border-neutral-200 shadow-sm p-6">

                    <input type="hidden" name="court" value="{{ $court->slug }}">
                    <input type="hidden" name="date" value="{{ $day['value'] }}">

                    <div class="flex flex-wrap gap-2 mb-6">

                        <span class="inline-block text-xs font-medium bg-amber-100 text-amber-800 px-3 py-1 rounded-full">
                            Lapangan: {{ $court->name }}
                        </span>

                        <span class="inline-block text-xs font-medium bg-amber-100 text-amber-800 px-3 py-1 rounded-full">
                            {{ $day['full'] }}
                        </span>

                    </div>

                    <p class="text-xs font-semibold text-neutral-500 uppercase mb-3">
                        Pilih jam (durasi 1 jam)
                    </p>

                    <div class="grid grid-cols-3 sm:grid-cols-5 gap-3 mb-2">

                        @foreach ($timeSlots as $slot)

                            @php
                                $isBooked = in_array($slot, $bookedTimes, true);
                            @endphp

                            <label @class([
                                'cursor-pointer' => !$isBooked,
                                'cursor-not-allowed' => $isBooked,
                            ])>

                                <input type="radio" name="time" value="{{ $slot }}" class="peer sr-only" @disabled($isBooked)
                                    required>

                                <div
                                    class="rounded-xl border border-neutral-200 py-4 text-center text-sm font-medium text-neutral-700 transition hover:border-neutral-300 peer-checked:border-amber-700 peer-checked:bg-amber-50 peer-checked:text-amber-800 peer-disabled:bg-neutral-200 peer-disabled:text-neutral-400 peer-disabled:line-through">

                                    {{ $slot }}

                                    @if ($isBooked)
                                        <span class="block text-[10px] mt-1">
                                            Terisi
                                        </span>
                                    @endif

                                </div>

                            </label>

                        @endforeach

                    </div>

                    @error('time')
                        <p class="text-sm text-red-600 mt-3">{{ $message }}</p>
                    @enderror

                    <div class="flex justify-between mt-8">

                        <a href="{{ route('reservation.create', ['court' => $court->slug]) }}"
                            class="px-6 py-2.5 rounded-full text-sm font-medium border border-neutral-300 text-neutral-700 hover:bg-neutral-50">
                            Kembali
                        </a>

                        <button type="submit"
                            class="px-6 py-2.5 rounded-full text-sm font-medium bg-amber-700 text-white hover:bg-amber-800">
                            Lanjutkan
                        </button>

                    </div>

                </form>

            @endif


            {{-- ====================================== --}}
            {{-- LANGKAH 4: DATA PEMINJAM --}}
            {{-- ====================================== --}}

            @if ($step === 4)

                    <div class="bg-white rounded-2xl border border-neutral-200 shadow-sm p-6">

                        {{-- RINGKASAN RESERVASI --}}

                        <div class="mb-8">

                            <h2 class="text-lg font-semibold text-neutral-900 mb-4">
                                Ringkasan Reservasi
                            </h2>

                            <div class="bg-amber-50 rounded-xl p-5 space-y-3">

                                <div class="flex justify-between gap-4 text-sm">
                                    <span class="text-neutral-500">Lapangan</span>
                                    <span class="font-semibold text-neutral-900 text-right">
                                        {{ $court->name }}
                                    </span>
                                </div>

                                <div class="flex justify-between gap-4 text-sm">
                                    <span class="text-neutral-500">Tanggal</span>
                                    <span class="font-semibold text-neutral-900 text-right">
                                        {{ $day['full'] }}
                                    </span>
                                </div>

                                <div class="flex justify-between gap-4 text-sm">
                                    <span class="text-neutral-500">Jam</span>
                                    <span class="font-semibold text-neutral-900 text-right">
                                        {{ $time }} - {{ $endTime }}
                                    </span>
                                </div>

                            </div>

                        </div>


                        {{-- FORM DATA PEMINJAM --}}

                        <h2 class="text-lg font-semibold text-neutral-900 mb-2">
                            Data Peminjam
                        </h2>

                        <p class="text-sm text-neutral-500 mb-6">
                            Lengkapi informasi berikut untuk mengajukan reservasi.
                        </p>

                        <form action="{{ route('reservation.store') }}" method="POST" class="space-y-5">

                            @csrf

                            <input type="hidden" name="court" value="{{ $court->slug }}">
                            <input type="hidden" name="date" value="{{ $day['value'] }}">
                            <input type="hidden" name="time" value="{{ $time }}">


                            {{-- NAMA LENGKAP --}}

                            <div>

                                <label class="block text-sm font-medium text-neutral-700 mb-2">
                                    Nama Lengkap
                                </label>

                                <input type="text" name="full_name" value="{{ old('full_name', auth()->user()->name) }}"
                                    placeholder="Masukkan nama lengkap" required
                                    class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-[#F7F3EC] text-sm focus:outline-none focus:ring-2 focus:ring-amber-700">

                                @error('full_name')
                                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                @enderror

                            </div>


                            {{-- KELAS / INSTANSI --}}

                            <div>

                                <label class="block text-sm font-medium text-neutral-700 mb-2">
                                    Kelas / Instansi
                                </label>

                                <input type="text" name="class_institution"
                                    value="{{ old('class_institution', auth()->user()->kelas_instansi ?? '') }}"
                                    placeholder="Masukkan kelas atau instansi" required
                                    class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-[#F7F3EC] text-sm focus:outline-none focus:ring-2 focus:ring-amber-700">

                                @error('class_institution')
                                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                @enderror

                            </div>


                            {{-- NOMOR HP --}}

                            <div>

                                <label class="block text-sm font-medium text-neutral-700 mb-2">
                                    Nomor WhatsApp / HP
                                </label>

                                <input type="tel" name="phone_number" value="{{ old('phone_number') }}"
                                    placeholder="Contoh: 081234567890" required
                                    class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-[#F7F3EC] text-sm focus:outline-none focus:ring-2 focus:ring-amber-700">

                                @error('phone_number')
                                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                @enderror

                            </div>


                            {{-- JUMLAH PESERTA --}}

                            <div>

                                <label class="block text-sm font-medium text-neutral-700 mb-2">
                                    Jumlah Peserta
                                </label>

                                <input type="number" name="participant_count" value="{{ old('participant_count', 1) }}" min="1"
                                    required placeholder="Masukkan jumlah peserta"
                                    class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-[#F7F3EC] text-sm focus:outline-none focus:ring-2 focus:ring-amber-700">

                                @error('participant_count')
                                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                @enderror

                            </div>


                            {{-- TUJUAN PENGGUNAAN --}}

                            <div>

                                <label class="block text-sm font-medium text-neutral-700 mb-2">
                                    Tujuan Penggunaan
                                </label>

                                <select name="purpose" required
                                    class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-[#F7F3EC] text-sm focus:outline-none focus:ring-2 focus:ring-amber-700">

                                    <option value="">
                                        Pilih tujuan penggunaan
                                    </option>

                                    @foreach ($purposes as $purpose)

                                        <option value="{{ $purpose }}" @selected(old('purpose') === $purpose)>
                                            {{ $purpose }}
                                        </option>

                                    @endforeach

                                </select>

                                @error('purpose')
                                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                @enderror

                            </div>


                            {{-- CATATAN --}}

                            <div>

                                <label class="block text-sm font-medium text-neutral-700 mb-2">
                                    Catatan Tambahan (Opsional)
                                </label>

                                <textarea name="notes" rows="4" placeholder="Tambahkan informasi jika diperlukan..."
                                    class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-[#F7F3EC] text-sm focus:outline-none focus:ring-2 focus:ring-amber-700">{{ old('notes') }}</textarea>

                                @error('notes')
                                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                @enderror

                            </div>


                            {{-- TOMBOL AKSI --}}

                            <div class="flex justify-between items-center gap-3 pt-4">

                                <a href="{{ route('reservation.create', [
                    'court' => $court->slug,
                    'date' => $day['value']
                ]) }}"
                                    class="px-6 py-3 rounded-full text-sm font-medium border border-neutral-300 text-neutral-700 hover:bg-neutral-50 transition">
                                    Kembali
                                </a>

                                <button type="submit"
                                    class="px-7 py-3 rounded-full text-sm font-semibold bg-amber-700 text-white hover:bg-amber-800 transition">
                                    Ajukan Reservasi
                                </button>

                            </div>

                        </form>

                    </div>

            @endif

        </div>
    </div>
@endsection