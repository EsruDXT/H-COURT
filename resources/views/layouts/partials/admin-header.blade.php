{{-- resources/views/layouts/partials/admin-header.blade.php --}}
<header class="flex flex-wrap items-center justify-between gap-4 mb-12">
    <div>
        <h1 class="font-serif text-2xl font-bold text-navy leading-none">H-COURT</h1>
        <p class="text-[11px] tracking-[0.2em] text-gray-500 mt-1">RESERVASI LAPANGAN SEKOLAH</p>
    </div>

    <nav class="flex items-center bg-white rounded-full p-1 border border-stone-200">
        @php
            $adminNavLinks = [
                ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['label' => 'Kelola Lapangan', 'route' => 'admin.courts'],
            ];
        @endphp

        @foreach ($adminNavLinks as $link)
            @php $isActive = request()->routeIs($link['route']); @endphp
            <a href="{{ route($link['route']) }}"
               class="px-6 py-2.5 rounded-full font-semibold text-sm
                      {{ $isActive ? 'bg-brand text-white' : 'text-gray-500 hover:text-gray-700' }}">
                {{ $link['label'] }}
            </a>
        @endforeach
    </nav>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="px-7 py-3 rounded-full bg-navy text-white font-semibold text-sm">
            Keluar
        </button>
    </form>
</header>