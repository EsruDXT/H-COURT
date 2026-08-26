
<footer class="bg-ink text-gray-300">
    <div class="max-w-7xl mx-auto px-6 py-14 grid grid-cols-1 md:grid-cols-4 gap-10">

        {{-- Brand --}}
        <div class="md:col-span-2">
            <div class="flex items-center justify-center w-24 h-12 bg-gray-300 rounded-lg mb-4">
                <span class="font-black tracking-wide text-sm text-ink">LOGO</span>
            </div>
            <p class="text-sm text-gray-400 max-w-sm leading-relaxed">
                H-COURT membantu siswa dan pengelola lapangan sekolah mengatur
                jadwal serta reservasi futsal, basket, dan badminton tanpa
                perlu catatan manual di ruang piket.
            </p>
        </div>

        {{-- Navigasi --}}
        <div>
            <h4 class="text-white font-semibold mb-4 text-sm tracking-wide uppercase">Navigasi</h4>
            {{-- TODO: kembalikan route('jadwal.index') & route('reservasi.index') kalau sudah aktif --}}
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('landing') }}" class="hover:text-white transition">Beranda</a></li>
                <li><a href="#" class="hover:text-white transition">Jadwal Lapangan</a></li>
                <li><a href="#" class="hover:text-white transition">Reservasi</a></li>
            </ul>
        </div>

        {{-- Akun --}}
        <div>
            <h4 class="text-white font-semibold mb-4 text-sm tracking-wide uppercase">Akun</h4>
            {{-- TODO: kembalikan blok @guest/@else + route('login')/register()/dashboard() kalau AuthController sudah aktif --}}
            <ul class="space-y-2 text-sm">
                <li><a href="#" class="hover:text-white transition">Masuk</a></li>
                <li><a href="#" class="hover:text-white transition">Daftar</a></li>
            </ul>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="max-w-7xl mx-auto px-6 py-5 flex flex-col md:flex-row items-center justify-between gap-2 text-xs text-gray-500">
            <p>&copy; {{ date('Y') }} H-COURT. Seluruh hak cipta dilindungi.</p>
            <p>Dibuat untuk memudahkan reservasi lapangan sekolah.</p>
        </div>
    </div>
</footer>