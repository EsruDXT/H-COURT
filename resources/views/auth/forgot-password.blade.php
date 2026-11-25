<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi - H-COURT</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[#EDE8DC] flex items-center justify-center p-6">
    <main class="w-full max-w-md rounded-2xl bg-white p-8 shadow-lg">
        <a href="{{ route('login') }}" class="text-sm text-[#A9754A]">← Kembali ke login</a>
        <p class="mt-6 text-xs font-semibold tracking-widest text-[#A9754A]">PEMULIHAN AKUN</p>
        <h1 class="mt-2 text-2xl font-bold text-gray-900">Lupa kata sandi?</h1>
        <p class="mt-2 mb-6 text-sm leading-6 text-gray-500">Masukkan email akun H-COURT. Jika terdaftar, kami akan mengirimkan tautan untuk membuat kata sandi baru.</p>

        @if (session('status'))
            <div class="mb-4 rounded-lg bg-green-50 p-3 text-sm text-green-700">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">
                @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:border-[#A9754A] focus:outline-none focus:ring-2 focus:ring-[#A9754A]/20">
            </div>
            <button class="w-full rounded-lg bg-[#A9754A] py-3 font-semibold text-white hover:bg-[#96643D]">Kirim tautan reset</button>
        </form>
    </main>
</body>
</html>
