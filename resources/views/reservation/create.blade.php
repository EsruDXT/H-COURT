
@extends('layouts.app')

@section('title', 'Reservasi Lapangan')

@section('content')
<div
    x-data="reservationWizard()"
    class="bg-[#F7F3EC] min-h-screen py-10"
>
    <div class="max-w-5xl mx-auto px-4">

        {{-- Header teks halaman --}}
        <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase mb-2">
            Reservasi Lapangan
        </p>
        <h1 class="text-3xl font-serif font-semibold text-neutral-900 mb-2" x-text="step === 4 ? 'Reservasi berhasil' : 'Ajukan reservasi kamu'">
        </h1>
        <p class="text-sm text-neutral-500 max-w-2xl mb-8" x-show="step !== 4">
            Pilih lapangan, tentukan hari dan jam, lengkapi data peminjam, lalu ajukan.
            Pengelola akan meninjau dan mengonfirmasi.
        </p>
        <p class="text-sm text-neutral-500 max-w-2xl mb-8" x-show="step === 4" x-cloak>
            Lapangan kamu sudah berhasil diajukan dan langsung terkonfirmasi.
        </p>

        {{-- Step indicator --}}
        <div class="flex items-center gap-3 mb-8 flex-wrap">
            <template x-for="(label, index) in stepLabels" :key="index">
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2">
                        <div
                            class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold border"
                            :class="{
                                'bg-emerald-600 border-emerald-600 text-white': (index + 1) < step,
                                'bg-amber-700 border-amber-700 text-white': (index + 1) === step,
                                'bg-white border-neutral-300 text-neutral-400': (index + 1) > step,
                            }"
                        >
                            <span x-show="(index + 1) >= step" x-text="index + 1"></span>
                            <svg x-show="(index + 1) < step" x-cloak class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span
                            class="text-sm"
                            :class="(index + 1) === step ? 'text-neutral-900 font-semibold' : 'text-neutral-400'"
                            x-text="label"
                        ></span>
                    </div>
                    <div class="w-10 h-px bg-neutral-300" x-show="index < stepLabels.length - 1"></div>
                </div>
            </template>
        </div>

        {{-- ================= STEP 1: Pilih Lapangan ================= --}}
        <div x-show="step === 1" x-cloak>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
                @foreach ($courts as $court)
                    <button
                        type="button"
                        @click="selectCourt('{{ $court['slug'] }}', '{{ $court['name'] }}')"
                        class="text-left bg-white rounded-2xl border-2 overflow-hidden transition"
                        :class="form.court === '{{ $court['slug'] }}' ? 'border-amber-700' : 'border-transparent hover:border-neutral-200'"
                    >
                        <div class="h-32 bg-neutral-200">
                            <img
                                src="{{ asset($court['image']) }}"
                                alt="{{ $court['name'] }}"
                                class="w-full h-full object-cover"
                                onerror="this.style.display='none'"
                            >
                        </div>
                        <div class="p-4">
                            <p class="font-semibold text-neutral-900">{{ $court['name'] }}</p>
                            <p class="text-xs text-neutral-500">{{ $court['type'] }}</p>
                        </div>
                    </button>
                @endforeach
            </div>

            <div class="flex justify-end">
                <button
                    type="button"
                    @click="goTo(2)"
                    :disabled="!form.court"
                    class="px-6 py-2.5 rounded-full text-sm font-medium transition"
                    :class="form.court
                        ? 'bg-amber-700 text-white hover:bg-amber-800'
                        : 'bg-neutral-200 text-neutral-400 cursor-not-allowed'"
                >
                    Lanjutkan
                </button>
            </div>
        </div>

        {{-- ================= STEP 2: Pilih Jadwal ================= --}}
        <div x-show="step === 2" x-cloak>
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-sm p-6">

                <span class="inline-block text-xs font-medium bg-amber-100 text-amber-800 px-3 py-1 rounded-full mb-6">
                    Lapangan: <span x-text="form.court_label"></span>
                </span>

                <p class="text-xs font-semibold text-neutral-500 uppercase mb-2">Pilih hari</p>
                <div class="grid grid-cols-3 sm:grid-cols-6 gap-3 mb-6">
                    @foreach ($days as $day)
                        <button
                            type="button"
                            @click="form.date = '{{ $day['value'] }}'; form.date_label = '{{ $day['full'] }}'"
                            class="rounded-xl border py-3 text-center transition"
                            :class="form.date === '{{ $day['value'] }}' ? 'border-amber-700 bg-amber-50' : 'border-neutral-200 hover:border-neutral-300'"
                        >
                            <p class="text-[10px] text-neutral-400 font-medium">{{ $day['label'] }}</p>
                            <p class="text-sm font-semibold text-neutral-800">{{ $day['date'] }}</p>
                        </button>
                    @endforeach
                </div>

                <p class="text-xs font-semibold text-neutral-500 uppercase mb-2">Pilih jam (durasi 1 jam)</p>
                <div class="grid grid-cols-3 sm:grid-cols-5 gap-3 mb-2">
                    @foreach ($timeSlots as $time)
                        <button
                            type="button"
                            @click="form.time = '{{ $time }}'"
                            class="rounded-xl border py-3 text-center text-sm font-medium transition"
                            :class="form.time === '{{ $time }}' ? 'border-amber-700 bg-amber-50 text-amber-800' : 'border-neutral-200 hover:border-neutral-300 text-neutral-700'"
                        >
                            {{ $time }}
                        </button>
                    @endforeach
                </div>

                <div class="flex justify-between mt-8">
                    <button
                        type="button"
                        @click="goTo(1)"
                        class="px-6 py-2.5 rounded-full text-sm font-medium border border-neutral-300 text-neutral-700 hover:bg-neutral-50"
                    >
                        Kembali
                    </button>
                    <button
                        type="button"
                        @click="goTo(3)"
                        :disabled="!form.date || !form.time"
                        class="px-6 py-2.5 rounded-full text-sm font-medium transition"
                        :class="(form.date && form.time)
                            ? 'bg-amber-700 text-white hover:bg-amber-800'
                            : 'bg-neutral-200 text-neutral-400 cursor-not-allowed'"
                    >
                        Lanjutkan
                    </button>
                </div>
            </div>
        </div>

        {{-- ================= STEP 3: Data Peminjam ================= --}}
        <div x-show="step === 3" x-cloak>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Form --}}
                <div class="lg:col-span-2 bg-white rounded-2xl border border-neutral-200 shadow-sm p-6">
                    <h2 class="font-semibold text-neutral-900 mb-4">Data Peminjam</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-medium text-neutral-500 mb-1">Nama Lengkap</label>
                            <input
                                type="text" x-model="form.nama_lengkap" placeholder="Masukkan nama lengkap anda"
                                class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-600"
                            >
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-neutral-500 mb-1">Kelas / Instansi</label>
                            <input
                                type="text" x-model="form.kelas" placeholder="Masukkan kelas anda"
                                class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-600"
                            >
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-neutral-500 mb-1">Nomor HP / WhatsApp</label>
                            <input
                                type="text" x-model="form.nomor_hp" placeholder="Masukkan nomor HP anda"
                                class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-600"
                            >
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-neutral-500 mb-1">Jumlah Peserta</label>
                            <input
                                type="number" min="1" x-model="form.jumlah" placeholder="Masukkan jumlah peserta anda"
                                class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-600"
                            >
                        </div>
                    </div>

                    <label class="block text-xs font-medium text-neutral-500 mb-2">Keperluan</label>
                    <div class="flex flex-wrap gap-2 mb-4">
                        <template x-for="option in keperluanOptions" :key="option">
                            <button
                                type="button"
                                @click="form.keperluan = option"
                                class="px-4 py-1.5 rounded-full text-xs font-medium border transition"
                                :class="form.keperluan === option
                                    ? 'bg-amber-700 border-amber-700 text-white'
                                    : 'border-neutral-300 text-neutral-600 hover:bg-neutral-50'"
                                x-text="option"
                            ></button>
                        </template>
                    </div>

                    <label class="block text-xs font-medium text-neutral-500 mb-1">Catatan tambahan (opsional)</label>
                    <textarea
                        x-model="form.catatan" rows="3" placeholder="Tulis catatan tambahan jika ada..."
                        class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-600"
                    ></textarea>

                    <div class="flex justify-between mt-6">
                        <button
                            type="button"
                            @click="goTo(2)"
                            class="px-6 py-2.5 rounded-full text-sm font-medium border border-neutral-300 text-neutral-700 hover:bg-neutral-50"
                        >
                            Kembali
                        </button>
                        <button
                            type="button"
                            @click="submitReservation()"
                            :disabled="submitting || !isStep3Valid()"
                            class="px-6 py-2.5 rounded-full text-sm font-medium transition"
                            :class="(!submitting && isStep3Valid())
                                ? 'bg-amber-700 text-white hover:bg-amber-800'
                                : 'bg-neutral-200 text-neutral-400 cursor-not-allowed'"
                        >
                            <span x-show="!submitting">Ajukan Reservasi</span>
                            <span x-show="submitting" x-cloak>Mengirim...</span>
                        </button>
                    </div>

                    <p x-show="errorMessage" x-cloak class="text-xs text-red-600 mt-3" x-text="errorMessage"></p>
                </div>

                {{-- Ringkasan --}}
                <div class="bg-white rounded-2xl border border-neutral-200 shadow-sm p-5 h-fit">
                    <p class="text-xs font-semibold text-neutral-500 uppercase mb-3">Ringkasan Reservasi</p>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-neutral-500">Lapangan</dt>
                            <dd class="font-medium text-neutral-900" x-text="form.court_label"></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-neutral-500">Tanggal</dt>
                            <dd class="font-medium text-neutral-900" x-text="form.date_label"></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-neutral-500">Waktu</dt>
                            <dd class="font-medium text-neutral-900" x-text="form.time ? form.time + ' - ' + addOneHour(form.time) : ''"></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-neutral-500">Durasi</dt>
                            <dd class="font-medium text-neutral-900">1 jam</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        {{-- ================= STEP 4: Konfirmasi ================= --}}
        <div x-show="step === 4" x-cloak class="bg-white rounded-2xl border border-neutral-200 shadow-sm p-10 text-center">
            <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-5">
                <svg class="w-8 h-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <h2 class="text-xl font-semibold text-neutral-900 mb-2">Reservasi Berhasil Diajukan!</h2>
            <p class="text-sm text-neutral-500 max-w-md mx-auto mb-8">
                Pengelola akan meninjau permintaanmu. Kamu akan mendapatkan notifikasi begitu
                reservasi diterima atau ditolak.
            </p>

            <div class="max-w-sm mx-auto bg-neutral-50 rounded-xl border border-neutral-200 p-5 text-left text-sm mb-8">
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-neutral-500">Lapangan</dt>
                        <dd class="font-medium text-neutral-900" x-text="result.lapangan"></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-neutral-500">Tanggal</dt>
                        <dd class="font-medium text-neutral-900" x-text="result.tanggal"></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-neutral-500">Waktu</dt>
                        <dd class="font-medium text-neutral-900" x-text="result.waktu"></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-neutral-500">Status</dt>
                        <dd class="font-medium text-amber-600" x-text="result.status"></dd>
                    </div>
                </dl>
            </div>

            <div class="flex items-center justify-center gap-3">
                <button
                    type="button"
                    @click="resetWizard()"
                    class="px-6 py-2.5 rounded-full text-sm font-medium bg-amber-700 text-white hover:bg-amber-800"
                >
                    Ajukan reservasi lain
                </button>
                <a
                    href="{{ route('landing') }}"
                    class="px-6 py-2.5 rounded-full text-sm font-medium border border-neutral-300 text-neutral-700 hover:bg-neutral-50"
                >
                    Kembali ke beranda
                </a>
            </div>
        </div>

    </div>
</div>

{{-- Hapus script Alpine ini kalau Alpine.js sudah di-load global di layouts.app --}}
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script>
    function reservationWizard() {
        return {
            step: 1,
            submitting: false,
            errorMessage: '',
            stepLabels: ['Pilih lapangan', 'Pilih jadwal', 'Data peminjam', 'Konfirmasi'],
            keperluanOptions: ['Latihan rutin', 'Lomba / turnamen', 'Pertandingan umum'],
            form: {
                court: '',
                court_label: '',
                date: '',
                date_label: '',
                time: '',
                nama_lengkap: '',
                kelas: '',
                nomor_hp: '',
                jumlah: '',
                keperluan: '',
                catatan: '',
            },
            result: {
                lapangan: '',
                tanggal: '',
                waktu: '',
                status: '',
            },

            selectCourt(slug, label) {
                this.form.court = slug;
                this.form.court_label = label;
            },

            goTo(target) {
                this.step = target;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            addOneHour(time) {
                if (!time) return '';
                const [h, m] = time.split(':').map(Number);
                const next = (h + 1).toString().padStart(2, '0');
                return `${next}:${m.toString().padStart(2, '0')}`;
            },

            isStep3Valid() {
                return this.form.nama_lengkap && this.form.kelas && this.form.nomor_hp
                    && this.form.jumlah && this.form.keperluan;
            },

            async submitReservation() {
                if (!this.isStep3Valid()) return;
                this.submitting = true;
                this.errorMessage = '';

                try {
                    const response = await fetch('{{ route('reservation.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(this.form),
                    });

                    if (!response.ok) {
                        throw new Error('Gagal mengajukan reservasi. Coba lagi.');
                    }

                    const json = await response.json();
                    this.result = json.data;
                    this.goTo(4);
                } catch (err) {
                    this.errorMessage = err.message || 'Terjadi kesalahan, coba lagi.';
                } finally {
                    this.submitting = false;
                }
            },

            resetWizard() {
                this.step = 1;
                this.form = {
                    court: '', court_label: '', date: '', date_label: '', time: '',
                    nama_lengkap: '', kelas: '', nomor_hp: '', jumlah: '', keperluan: '', catatan: '',
                };
                this.result = { lapangan: '', tanggal: '', waktu: '', status: '' };
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },
        }
    }
</script>
@endsection