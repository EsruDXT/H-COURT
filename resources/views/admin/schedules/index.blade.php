    @extends('layouts.app')

    @section('title', 'Jadwal Lapangan')

    @section('content')
    <div class="bg-[#F7F3EC] min-h-screen py-10">
        <div class="max-w-6xl mx-auto px-4">
            <p class="text-xs font-semibold tracking-wide text-neutral-500 uppercase mb-2">
                Jadwal Lapangan
            </p>
            <h1 class="text-3xl font-serif font-semibold text-neutral-900 mb-2">
                Cek ketersediaan sebelum reservasi
            </h1>
            <p class="text-sm text-neutral-500 max-w-2xl mb-8">
                Semua jadwal penggunaan lapangan futsal, basket, dan badminton
                ditampilkan dalam satu tampilan supaya kamu tahu jam mana yang
                masih kosong.
            </p>

            <div class="bg-white rounded-2xl border border-neutral-200 shadow-sm p-6">

                @if ($court)
                    <div class="inline-flex bg-neutral-100 rounded-full p-1 w-fit mb-6">
                        @foreach ($courtTypes as $slug => $label)
                            <a
                                href="{{ route('schedule.index', ['court' => $slug]) }}"
                                class="px-4 py-2 text-sm rounded-full font-medium transition
                                    {{ $court->slug === $slug
                                        ? 'bg-neutral-900 text-white'
                                        : 'text-neutral-600 hover:text-neutral-900' }}"
                            >
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>

                    @include('schedule._table')

                    <div class="flex flex-wrap gap-3 mt-6">
                        <a href="{{ route('schedule.show', $court->slug) }}"
                        class="px-6 py-2.5 rounded-full text-sm font-medium border border-neutral-300 text-neutral-700 hover:bg-neutral-50">
                            Detail lapangan
                        </a>
                        <a href="{{ route('reservation.create', ['court' => $court->slug]) }}"
                        class="px-6 py-2.5 rounded-full text-sm font-medium bg-amber-700 text-white hover:bg-amber-800">
                            Reservasi lapangan ini
                        </a>
                    </div>
                @else
                    <p class="text-sm text-neutral-500">Belum ada lapangan yang aktif.</p>
                @endif
            </div>
        </div>
    </div>
    @endsection
