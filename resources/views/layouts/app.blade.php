<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'H-COURT — Reservasi Lapangan Sekolah')</title>

    {{-- Google Fonts: Inter (sans) untuk body/nav, Lora (serif) untuk heading besar --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Lora:ital,wght@0,500;0,600;0,700;1,600&display=swap" rel="stylesheet">

    {{-- Tailwind lewat Vite. Pastikan resources/css/app.css sudah ada @tailwind base/components/utilities --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cream font-sans text-ink antialiased">

    @include('layouts.partials.navbar')

    <main>
        @yield('content')
    </main>

    @include('layouts.partials.footer')

</body>
</html>