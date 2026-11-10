<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Buat Akun Baru</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#EDE8DC] min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-5xl bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col md:flex-row">

        <!-- Foto Lapangan -->
        <div class="md:w-2/5 h-56 md:h-auto">

            <img src="{{ asset('images/Lap_Futsal.jpg') }}"
                alt="Futsal"
                class="w-full h-full object-cover">

        </div>

        <!-- Form Register -->
        <div class="md:w-3/5 p-8 md:p-10">

            <span class="inline-block text-xs font-medium tracking-wide text-gray-500 border border-[#D9CBAE] rounded-md px-4 py-2 mb-6">

                MULAI SEKARANG

            </span>

            <h1 class="text-3xl font-bold text-gray-900">
                Buat akun baru
            </h1>

            <p class="text-gray-500 mt-1 mb-6">
                Buat akun dan mulai reservasi lapangan kamu.
            </p>

            <!-- Error -->
            @if($errors->any())
                <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4 text-sm">

                    <ul class="list-disc pl-5">

                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach

                    </ul>

                </div>
            @endif

            <!-- Pilihan Role -->
            <div class="grid grid-cols-2 gap-3 mb-6">

                <button
                    type="button"
                    id="btn-siswa"
                    class="role-btn py-2.5 rounded-lg text-sm font-medium bg-[#E9DEC4] border border-[#A9754A] text-gray-800 transition">

                    Siswa/i

                </button>

                <button
                    type="button"
                    id="btn-umum"
                    class="role-btn py-2.5 rounded-lg text-sm font-medium bg-white border border-[#E3DCC9] text-gray-500 transition">

                    Umum

                </button>

            </div>

            <form action="{{ route('register') }}" method="POST" class="space-y-5">

                @csrf

                <input
                    type="hidden"
                    name="role"
                    id="role-input"
                    value="{{ old('role', 'siswa') }}">

                <!-- Nama dan Kelas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div>

                        <label class="block text-sm text-gray-700 mb-1.5">
                            Nama Lengkap
                        </label>

                        <input
                            type="text"
                            name="nama"
                            value="{{ old('nama') }}"
                            placeholder="Masukkan nama lengkap"
                            required
                            class="w-full px-4 py-2.5 rounded-lg bg-[#F7F2E7] border border-[#E3DCC9] text-sm focus:outline-none focus:ring-2 focus:ring-[#A9754A]">

                    </div>

                    <div>

                        <label id="label-dinamis" class="block text-sm text-gray-700 mb-1.5">
                            Kelas
                        </label>

                        <input
                            type="text"
                            id="input-dinamis"
                            name="kelas_instansi"
                            value="{{ old('kelas_instansi') }}"
                            placeholder="Masukkan kelas anda"
                            required
                            class="w-full px-4 py-2.5 rounded-lg bg-[#F7F2E7] border border-[#E3DCC9] text-sm focus:outline-none focus:ring-2 focus:ring-[#A9754A]">

                    </div>

                </div>

                <!-- Email -->
                <div>

                    <label class="block text-sm text-gray-700 mb-1.5">
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Masukkan email anda"
                        required
                        class="w-full px-4 py-2.5 rounded-lg bg-[#F7F2E7] border border-[#E3DCC9] text-sm focus:outline-none focus:ring-2 focus:ring-[#A9754A]">

                </div>

                <!-- Password -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div>

                        <label class="block text-sm text-gray-700 mb-1.5">
                            Kata Sandi
                        </label>

                        <input
                            type="password"
                            name="password"
                            placeholder="Masukkan kata sandi"
                            required
                            class="w-full px-4 py-2.5 rounded-lg bg-[#F7F2E7] border border-[#E3DCC9] text-sm focus:outline-none focus:ring-2 focus:ring-[#A9754A]">

                    </div>

                    <div>

                        <label class="block text-sm text-gray-700 mb-1.5">
                            Konfirmasi Kata Sandi
                        </label>

                        <input
                            type="password"
                            name="password_confirmation"
                            placeholder="Ulangi kata sandi"
                            required
                            class="w-full px-4 py-2.5 rounded-lg bg-[#F7F2E7] border border-[#E3DCC9] text-sm focus:outline-none focus:ring-2 focus:ring-[#A9754A]">

                    </div>

                </div>

                <!-- Persetujuan -->
                <label class="flex items-center gap-2 text-sm text-gray-600">

                    <input
                        type="checkbox"
                        name="agreement"
                        value="1"
                        required
                        class="rounded border-[#D9CBAE] text-[#A9754A]">

                    Saya menyetujui ketentuan penggunaan layanan.

                </label>

                <!-- Tombol -->
                <button
                    type="submit"
                    class="w-full bg-[#A9754A] hover:bg-[#96643D] text-white font-semibold py-3 rounded-lg transition">

                    Buat Akun

                </button>

                <p class="text-center text-sm text-gray-500">

                    Sudah punya akun?

                    <a href="{{ route('login') }}"
                        class="text-[#A9754A] font-medium underline">

                        Masuk di sini

                    </a>

                </p>

            </form>

        </div>

    </div>

    <!-- JavaScript Native -->
    <script>

        const btnSiswa = document.getElementById('btn-siswa');
        const btnUmum = document.getElementById('btn-umum');

        const roleInput = document.getElementById('role-input');

        const labelDinamis = document.getElementById('label-dinamis');
        const inputDinamis = document.getElementById('input-dinamis');

        function setRole(role) {

            roleInput.value = role;

            if (role === 'siswa') {

                btnSiswa.className =
                    'role-btn py-2.5 rounded-lg text-sm font-medium bg-[#E9DEC4] border border-[#A9754A] text-gray-800 transition';

                btnUmum.className =
                    'role-btn py-2.5 rounded-lg text-sm font-medium bg-white border border-[#E3DCC9] text-gray-500 transition';

                labelDinamis.textContent = 'Kelas';

                inputDinamis.placeholder = 'Masukkan kelas anda';

            } else {

                btnUmum.className =
                    'role-btn py-2.5 rounded-lg text-sm font-medium bg-[#E9DEC4] border border-[#A9754A] text-gray-800 transition';

                btnSiswa.className =
                    'role-btn py-2.5 rounded-lg text-sm font-medium bg-white border border-[#E3DCC9] text-gray-500 transition';

                labelDinamis.textContent = 'Instansi / Komunitas';

                inputDinamis.placeholder = 'Masukkan nama instansi anda';

            }

        }

        btnSiswa.addEventListener('click', function () {
            setRole('siswa');
        });

        btnUmum.addEventListener('click', function () {
            setRole('umum');
        });

        // Mempertahankan pilihan role setelah validasi gagal
        setRole(roleInput.value);

    </script>

</body>
</html>