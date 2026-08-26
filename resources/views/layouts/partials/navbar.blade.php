{{--
    Navbar H-COURT
    Dipanggil dari layouts/app.blade.php lewat @include('layouts.partials.navbar')

    TODO buat kamu isi nanti:
    - Ganti kotak "LOGO" di bawah dengan <img src="{{ asset('images/logo.png') }}" ...>
    - Kalau mau link "Masuk" beda untuk admin, sesuaikan kondisinya
--}}

<header class="bg-cream">
    <div class="max-w-7xl mx-auto px-6 py-6 flex items-center justify-between gap-4">

        {{-- LOGO (sengaja dikosongkan dulu, tinggal ganti jadi <img>) --}}
        <a href="{{ route('landing') }}" class="flex items-center justify-center w-24 h-12 bg-gray-300 rounded-lg shrink-0">
            <span class="font-black tracking-wide text-sm text-ink">LOGO</span>
        </a>

        {{-- Menu tengah (desktop) --}}
        <nav class="hidden md:flex items-center bg-white rounded-full shadow-sm border border-gray-200 p-1">
            <a href="{{ route('landing') }}"
               class="px-6 py-2.5 rounded-full text-sm font-semibold transition bg-ink text-white">
                Beranda
            </a>
            {{-- TODO: kembalikan ke route('jadwal.index') kalau JadwalController sudah aktif --}}
            <a href="#"
               class="px-6 py-2.5 rounded-full text-sm font-semibold transition text-gray-500 hover:text-ink">
                Jadwal Lapangan
            </a>
            {{-- TODO: kembalikan ke route('reservasi.index') kalau ReservasiController sudah aktif --}}
            <a href="#"
               class="px-6 py-2.5 rounded-full text-sm font-semibold transition text-gray-500 hover:text-ink">
                Reservasi
            </a>
        </nav>

        {{-- Tombol kanan --}}
        <div class="flex items-center gap-3">
            {{-- TODO: kembalikan blok @auth/@else ini kalau AuthController & route login/dashboard sudah aktif --}}
            <a href="#"
               class="bg-brand hover:bg-brand-dark text-white font-semibold px-7 py-3 rounded-xl transition text-sm">
                Masuk
            </a>

            {{-- Tombol menu mobile --}}
            <button id="mobile-menu-btn" type="button"
                    class="md:hidden inline-flex items-center justify-center w-11 h-11 rounded-lg border border-gray-300 text-ink">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Menu mobile (dropdown sederhana, tanpa Alpine.js) --}}
    <nav id="mobile-menu" class="hidden md:hidden px-6 pb-4 flex flex-col gap-2">
        <a href="{{ route('landing') }}" class="px-4 py-2.5 rounded-lg text-sm font-semibold bg-ink text-white">Beranda</a>
        {{-- TODO: kembalikan route('jadwal.index') & route('reservasi.index') kalau sudah aktif --}}
        <a href="#" class="px-4 py-2.5 rounded-lg text-sm font-semibold bg-white text-gray-600">Jadwal Lapangan</a>
        <a href="#" class="px-4 py-2.5 rounded-lg text-sm font-semibold bg-white text-gray-600">Reservasi</a>
    </nav>
</header>

<script>
    // Toggle menu mobile tanpa dependensi tambahan
    document.getElementById('mobile-menu-btn')?.addEventListener('click', function () {
        document.getElementById('mobile-menu')?.classList.toggle('hidden');
    });
</script>