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
      
      <img src="{{ asset('images/Lap_Futsal.jpg') }}" alt="Futsal" class="w-full h-full object-cover">
    </div>

    <!-- Kanan: Form -->
    <div class="md:w-3/5 p-8 md:p-10">

      <span class="inline-block text-xs font-medium tracking-wide text-gray-500 border border-[#D9CBAE] rounded-md px-4 py-2 mb-6">
        MULAI SEKARANG
      </span>

      <h1 class="text-3xl font-bold text-gray-900">Buat akun baru</h1>
      <p class="text-gray-500 mt-1 mb-6">Buat akun dan masuk ke akun kamu</p>

      
      <div class="grid grid-cols-3 gap-3 mb-6">
        <button type="button" class="py-2.5 rounded-lg text-sm font-medium bg-[#E9DEC4] border border-[#A9754A] text-gray-800">Siswa/i</button>
        <button type="button" class="py-2.5 rounded-lg text-sm font-medium bg-white border border-[#E3DCC9] text-gray-500">Umum</button>
        <button type="button" class="py-2.5 rounded-lg text-sm font-medium bg-white border border-[#E3DCC9] text-gray-500">Pengelola</button>
      </div>

      <form class="space-y-5">
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-gray-700 mb-1.5">Nama Lengkap</label>
            <input type="text" placeholder="Masukkan nama lengkap anda"
              class="w-full px-4 py-2.5 rounded-lg bg-[#F7F2E7] border border-[#E3DCC9] text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#A9754A]">
          </div>
          <div>
            <label class="block text-sm text-gray-700 mb-1.5">Kelas / Instansi</label>
            <input type="text" placeholder="Masukkan kelas anda"
              class="w-full px-4 py-2.5 rounded-lg bg-[#F7F2E7] border border-[#E3DCC9] text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#A9754A]">
          </div>
        </div>

        <div>
          <label class="block text-sm text-gray-700 mb-1.5">Email</label>
          <input type="email" placeholder="Masukkan email anda"
            class="w-full px-4 py-2.5 rounded-lg bg-[#F7F2E7] border border-[#E3DCC9] text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#A9754A]">
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-gray-700 mb-1.5">Kata sandi</label>
            <input type="password" placeholder="Masukkan kata sandi anda"
              class="w-full px-4 py-2.5 rounded-lg bg-[#F7F2E7] border border-[#E3DCC9] text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#A9754A]">
          </div>
          <div>
            <label class="block text-sm text-gray-700 mb-1.5">Konfirmasi kata sandi</label>
            <input type="password" placeholder="Konfirmasi kata sandi anda"
              class="w-full px-4 py-2.5 rounded-lg bg-[#F7F2E7] border border-[#E3DCC9] text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#A9754A]">
          </div>
        </div>

        <label class="flex items-center gap-2 text-sm text-gray-600">
          <input type="checkbox" class="rounded border-[#D9CBAE] text-[#A9754A] focus:ring-[#A9754A]">
          lorem ipsum dolor sit amet
        </label>

        <button type="submit"
          class="w-full bg-[#A9754A] hover:bg-[#96643D] text-white font-semibold py-3 rounded-lg transition">
          Buat Akun
        </button>

        <p class="text-center text-sm text-gray-500">
          Sudah punya akun?
          <a href="#" class="text-[#A9754A] font-medium underline">Masuk di sini</a>
        </p>
      </form>
    </div>
  </div>

</body>
</html>