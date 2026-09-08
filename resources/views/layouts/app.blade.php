<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'H-COURT — Reservasi Lapangan Sekolah')</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Lora:ital,wght@0,500;0,600;0,700;1,600&display=swap" rel="stylesheet">

    {{-- Tailwind (CDN for quick preview — swap for your Vite build if you already compile Tailwind) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        cream: '#F3EFE5',
                        navy: {
                            DEFAULT: '#121A2B',
                            light: '#1B2438',
                        },
                        brand: {
                            DEFAULT: '#BE7A43',
                            dark: '#A6672F',
                            light: '#D79A66',
                        },
                        ink: '#1E2430',
                    },
                    fontFamily: {
                        sans: ['Poppins', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        serif: ['Lora', 'ui-serif', 'Georgia', 'serif'],
                    },
                },
            },
        }
    </script>

    <style>
        body { background-color: #F3EFE5; }
    </style>

    @stack('styles')
</head>
<body class="font-sans text-ink antialiased">

    @include('layouts.partials.header')

    <main>
        @yield('content')
    </main>

    @include('layouts.partials.footer')

    @stack('scripts')
</body>
</html>