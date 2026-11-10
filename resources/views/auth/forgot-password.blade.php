<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#EDE8DC] min-h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-md">

        <h1 class="text-2xl font-bold mb-2">
            Lupa Kata Sandi?
        </h1>

        <p class="text-gray-500 mb-6">
            Silakan hubungi administrator untuk bantuan
            mengatur ulang kata sandi akun kamu.
        </p>

        <a href="{{ route('login') }}"
           class="block text-center bg-[#A9754A] text-white py-3 rounded-lg">
            Kembali ke Login
        </a>

    </div>

</body>
</html>