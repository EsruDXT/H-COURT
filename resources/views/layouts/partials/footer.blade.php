<footer class="mt-20 border-t border-black/5">
    <div class="max-w-7xl mx-auto px-6 py-10 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="leading-none">
                <span class="block font-serif font-extrabold text-lg text-navy tracking-tight">H-COURT</span>
                <span class="block text-[9px] font-semibold tracking-widest text-navy/70 mt-0.5">
                    RESERVASI LAPANGAN SEKOLAH
                </span>
            </div>
            <span class="text-sm text-gray-500">© {{ date('Y') }} H-COURT. Sistem reservasi lapangan sekolah.</span>
        </div>
        <div class="flex items-center gap-6 text-sm text-gray-500">
            <a href="{{ route('schedule.index') }}" class="hover:text-ink">Jadwal Lapangan</a>
            <a href="{{ Route::has('reservasi.index') ? route('reservasi.index') : route('login') }}" class="hover:text-ink">Reservasi</a>
            <a href="{{ route('login') }}" class="hover:text-ink">Masuk</a>
        </div>
    </div>
</footer>