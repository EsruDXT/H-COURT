<header class="mb-12 flex flex-wrap items-center justify-between gap-4">
    <div>
        <h1 class="font-serif text-2xl font-bold leading-none text-navy">
            H-COURT
        </h1>
        <p class="mt-1 text-[11px] tracking-[0.2em] text-gray-500">
            RESERVASI LAPANGAN SEKOLAH
        </p>
    </div>

    <nav class="flex flex-wrap items-center gap-1 rounded-full border border-stone-200 bg-white p-1">
        @php
            $adminNavLinks = [
                ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'match' => 'admin.dashboard'],
                ['label' => 'Kelola Lapangan', 'route' => 'admin.courts.index', 'match' => 'admin.courts.*'],
                ['label' => 'Laporan', 'route' => 'admin.reports.index', 'match' => 'admin.reports.*'],
            ];

            $unreadCount = auth()->user()->unreadNotifications()->count();
        @endphp

        @foreach ($adminNavLinks as $link)
            <a href="{{ route($link['route']) }}"
                class="rounded-full px-4 py-2.5 text-sm font-semibold transition
                {{ request()->routeIs($link['match'])
                    ? 'bg-brand text-white'
                    : 'text-gray-500 hover:bg-stone-100 hover:text-gray-700' }}">
                {{ $link['label'] }}
            </a>
        @endforeach

        {{-- Notifikasi --}}
        <a href="{{ route('notifications.index') }}"
            class="rounded-full px-4 py-2.5 text-sm font-semibold transition
            {{ request()->routeIs('notifications.*')
                ? 'bg-brand text-white'
                : 'text-gray-600 hover:bg-stone-100 hover:text-gray-700' }}">

            Notifikasi

            @if ($unreadCount > 0)
                <span class="ml-1 rounded-full px-1.5 py-0.5 text-[10px] font-bold
                    {{ request()->routeIs('notifications.*')
                        ? 'bg-white text-brand'
                        : 'bg-red-600 text-white' }}">
                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                </span>
            @endif
        </a>
    </nav>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
            class="rounded-full bg-navy px-7 py-3 text-sm font-semibold text-white transition hover:bg-slate-700">
            Keluar
        </button>
    </form>
</header>