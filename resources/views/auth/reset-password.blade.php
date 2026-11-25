<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Ulang Kata Sandi - H-COURT</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[#EDE8DC] flex items-center justify-center p-6">
    <main class="w-full max-w-md rounded-2xl bg-white p-8 shadow-lg">
        <p class="text-xs font-semibold tracking-widest text-[#A9754A]">PEMULIHAN AKUN</p>
        <h1 class="mt-2 text-2xl font-bold text-gray-900">Buat kata sandi baru</h1>
        <p class="mt-2 mb-6 text-sm text-gray-500">Gunakan kata sandi minimal 8 karakter.</p>

        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">
                @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div>
                <label for="email" class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required autocomplete="email"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:border-[#A9754A] focus:outline-none">
            </div>
            <div>
                <label for="password" class="mb-1 block text-sm font-medium text-gray-700">Kata sandi baru</label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:border-[#A9754A] focus:outline-none">
            </div>
            <div>
                <label for="password_confirmation" class="mb-1 block text-sm font-medium text-gray-700">Konfirmasi kata sandi</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:border-[#A9754A] focus:outline-none">
            </div>
            <button class="w-full rounded-lg bg-[#A9754A] py-3 font-semibold text-white hover:bg-[#96643D]">Simpan kata sandi baru</button>
        </form>
    </main>
</body>
</html>
