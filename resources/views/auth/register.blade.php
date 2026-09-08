<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Masuk ke Akun</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#EDE8DC] min-h-screen flex items-center justify-center p-6">

  <div class="w-full max-w-5xl bg-white rounded-2xl border border-gray-900 overflow-hidden flex flex-col md:flex-row">

    <!-- Kiri: Gambar -->
    <div class="md:w-2/5 h-56 md:h-auto">
      <img src="{{ asset('images/Lap_Basket.jpg') }}" alt="Basket" class="w-full h-full object-cover">
    </div>

    <!-- Kanan: Form -->
    <div class="md:w-3/5 p-8 md:p-10">

      <span class="inline-block w-full text-xs font-medium tracking-wide text-gray-500 border border-[#D9CBAE] rounded-md px-4 py-2 mb-6">
        SELAMAT DATANG KEMBALI
      </span>

      <h1 class="text-3xl font-bold text-gray-900">Masuk ke akun kamu</h1>
      <p class="text-gray-500 mt-1 mb-6">Masuk untuk melihat jadwal lapangan dan mengelola reservasimu.</p>

      <!-- Pesan error umum (mis. "Email atau kata sandi salah" dari backend Laravel) -->
      <div id="generalError"
        class="mb-4 text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg px-4 py-2 {{ $errors->any() ? '' : 'hidden' }}">
        {{ $errors->first() }}
      </div>

      <!-- Sesuaikan nama route login pada "action" jika berbeda di routes/web.php -->
      <form id="signInForm" class="space-y-5" method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        <div>
          <label for="email" class="block text-sm text-gray-700 mb-1.5">Email</label>
          <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email anda"
            class="w-full px-4 py-2.5 rounded-lg bg-[#F7F2E7] border border-[#E3DCC9] text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#A9754A] @error('email') border-red-400 focus:ring-red-400 @enderror">
          <p id="emailError" class="text-xs text-red-600 mt-1 {{ $errors->has('email') ? '' : 'hidden' }}">@error('email'){{ $message }}@enderror</p>
        </div>

        <div>
          <label for="password" class="block text-sm text-gray-700 mb-1.5">Kata Sandi</label>
          <input type="password" id="password" name="password" placeholder="Masukkan kata sandi anda"
            class="w-full px-4 py-2.5 rounded-lg bg-[#F7F2E7] border border-[#E3DCC9] text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#A9754A] @error('password') border-red-400 focus:ring-red-400 @enderror">
          <p id="passwordError" class="text-xs text-red-600 mt-1 {{ $errors->has('password') ? '' : 'hidden' }}">@error('password'){{ $message }}@enderror</p>
        </div>

        <div class="flex items-center justify-between text-sm">
          <label class="flex items-center gap-2 text-gray-600">
            <input type="checkbox" id="rememberMe" name="remember" @checked(old('remember')) class="rounded border-[#D9CBAE] text-[#A9754A] focus:ring-[#A9754A]">
            Ingat saya
          </label>
          <a href="{{ route('password.request') }}" class="text-[#A9754A] font-medium underline">lupa kata sandi?</a>
        </div>

        <button type="submit" id="submitBtn"
          class="w-full bg-[#A9754A] hover:bg-[#96643D] disabled:opacity-60 disabled:cursor-not-allowed text-white font-semibold py-3 rounded-lg transition">
          <span id="submitText">Masuk</span>
        </button>

        <p class="text-center text-sm text-gray-500">
          Belum punya akun?
          <a href="{{ route('register') }}" class="text-[#A9754A] font-medium underline">Daftar di sini</a>
        </p>
      </form>
    </div>
  </div>

  <script src="{{ asset('js/login.js') }}"></script>
</body>
</html>