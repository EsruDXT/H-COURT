<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'H-Court - Pengelola')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: '#A8722F',
                        navy: '#1E2534',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-[#F4F1EA] min-h-screen text-gray-800">

    <div class="max-w-6xl mx-auto px-6 py-10">

        @include('layouts.partials.admin-header')

        @if (session('status'))
            <div class="bg-green-100 text-green-700 p-3 rounded-lg mb-6 text-sm">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-6 text-sm">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')

    </div>

</body>

</html>