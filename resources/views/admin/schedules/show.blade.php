@extends('layouts.app')

@section('title', $court->name)

@section('content')
<div class="bg-[#F7F3EC] min-h-screen py-10">
    <div class="max-w-6xl mx-auto px-4">

        <a href="{{ route('schedule.index', ['court' => $court->slug]) }}" class="text-sm text-neutral-500 hover:text-neutral-900">
            &larr; Kembali ke jadwal
        </a>

        <div class="bg-white rounded-2xl border border-neutral-200 shadow-sm overflow-hidden mt-4 mb-8 md:flex">
            <img src="{{ $court->imageUrl() }}" alt="{{ $court->name }}" class="w-full md:w-80 h-56 md:h-auto object-cover">

            <div class="p-6 md:p-8">
                <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase mb-2">
                    {{ ucfirst($court->category) }}
                </p>
                <h1 class="text-3xl font-serif font-semibold text-neutral-900 mb-3">{{ $court->name }}</h1>
                <p class="text-sm text-neutral-500">{{ $court->location }}</p>
                <p class="text-sm text-neutral-500 mb-6">
                    Jam operasional {{ substr($court->opens_at, 0, 5) }} - {{ substr($court->closes_at, 0, 5) }}
                </p>

                <a href="{{ route('reservation.create', ['court' => $court->slug]) }}"
                   class="inline-block px-6 py-2.5 rounded-full text-sm font-medium bg-amber-700 text-white hover:bg-amber-800">
                    Reservasi lapangan ini
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-neutral-200 shadow-sm p-6">
            <h2 class="text-lg font-semibold text-neutral-900 mb-4">Jadwal minggu ini</h2>
            @include('schedule._table')
        </div>
    </div>
</div>
@endsection
