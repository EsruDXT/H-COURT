<header class="mx-auto max-w-7xl px-6 pt-6">
    <div class="flex items-center justify-between gap-4">
        <a href="{{ route('landing') }}" class="shrink-0 leading-none">
            <span class="block font-serif text-2xl font-extrabold tracking-tight text-navy md:text-3xl">H-COURT</span>
            <span class="mt-0.5 block text-[10px] font-semibold tracking-widest text-navy/80 md:text-xs">RESERVASI LAPANGAN SEKOLAH</span>
        </a>

        <nav class="hidden items-center rounded-full bg-white p-1.5 shadow-sm md:flex">
            @php
                $navLinks = [
                    ['label' => 'Beranda', 'route' => 'landing'],
                    ['label' => 'Jadwal Lapangan', 'route' => 'schedule.index'],
                    ['label' => 'Reservasi', 'route' => 'reservation.index'],
                ];
            @endphp
            @foreach ($navLinks as $link)
                <a href="{{ route($link['route']) }}" class="rounded-full px-6 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs($link['route']) ? 'bg-navy text-white' : 'text-gray-500 hover:text-ink' }}">{{ $link['label'] }}</a>
            @endforeach
        </nav>

        <div class="flex shrink-0 items-center gap-2">
            @auth
                <a href="{{ route('notifications.index') }}" class="relative rounded-full border border-neutral-200 bg-white px-4 py-2.5 text-sm font-semibold text-navy" aria-label="Notifikasi">
                    Notifikasi
                    @php
                        $unreadCount = auth()->user()->unreadNotifications()->count();
                    @endphp
                    @if ($unreadCount > 0)
                        <span class="ml-1 inline-flex min-w-5 items-center justify-center rounded-full bg-red-600 px-1.5 py-0.5 text-[10px] text-white">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                    @endif
                </a>
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="rounded-full bg-brand px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-brand-dark">Dashboard</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-full bg-navy px-5 py-3 text-sm font-semibold text-white">Keluar</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="rounded-full bg-brand px-8 py-3 text-sm font-semibold text-white transition-colors hover:bg-brand-dark">Masuk</a>
            @endauth
        </div>
    </div>

    <nav class="mt-4 flex items-center justify-center gap-1 rounded-full bg-white p-1.5 shadow-sm md:hidden">
        @foreach ($navLinks as $link)
            <a href="{{ route($link['route']) }}" class="rounded-full px-4 py-2 text-xs font-medium {{ request()->routeIs($link['route']) ? 'bg-navy text-white' : 'text-gray-500' }}">{{ $link['label'] }}</a>
        @endforeach
    </nav>
</header>
