<header class="max-w-7xl mx-auto px-6 pt-6">
    <div class="flex items-center justify-between gap-4">

        {{-- Logo --}}
        <a href="{{ route('landing') }}" class="shrink-0 leading-none">
            <span class="block font-serif font-extrabold text-2xl md:text-3xl text-navy tracking-tight">H-COURT</span>
            <span class="block text-[10px] md:text-xs font-semibold tracking-widest text-navy/80 mt-0.5">
                RESERVASI LAPANGAN SEKOLAH
            </span>
        </a>

        {{-- Nav pill --}}
        <nav class="hidden md:flex items-center bg-white rounded-full p-1.5 shadow-sm">
            @php
                $navLinks = [
                    ['label' => 'Beranda', 'route' => 'landing'],
                    ['label' => 'Jadwal Lapangan', 'route' => 'jadwal.index'],
                    ['label' => 'Reservasi', 'route' => 'reservasi.index'],
                ];
            @endphp

            @foreach ($navLinks as $link)
                @php
                    $isActive = request()->routeIs($link['route']);
                @endphp
                <a href="{{ Route::has($link['route']) ? route($link['route']) : '#' }}"
                   class="px-6 py-2.5 rounded-full text-sm font-medium transition-colors
                          {{ $isActive ? 'bg-navy text-white' : 'text-gray-500 hover:text-ink' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>

        {{-- Auth button --}}
        <div class="shrink-0">
            @auth
                <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : '#' }}"
                   class="inline-block bg-brand hover:bg-brand-dark text-white font-semibold text-sm px-7 py-3 rounded-full transition-colors">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="inline-block bg-brand hover:bg-brand-dark text-white font-semibold text-sm px-8 py-3 rounded-full transition-colors">
                    Masuk
                </a>
            @endauth
        </div>
    </div>

    {{-- Mobile nav --}}
    <nav class="md:hidden flex items-center justify-center gap-1 bg-white rounded-full p-1.5 shadow-sm mt-4">
        @foreach ($navLinks as $link)
            @php
                $isActive = request()->routeIs($link['route']);
            @endphp
            <a href="{{ Route::has($link['route']) ? route($link['route']) : '#' }}"
               class="px-4 py-2 rounded-full text-xs font-medium transition-colors
                      {{ $isActive ? 'bg-navy text-white' : 'text-gray-500' }}">
                {{ $link['label'] }}
            </a>
        @endforeach
    </nav>
</header>