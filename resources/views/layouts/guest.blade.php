<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex bg-white">
        <!-- Left Side - Image -->
        <div class="hidden lg:flex lg:w-1/2 relative bg-gray-900">
            <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?q=80&w=2070&auto=format&fit=crop"
                alt="Food Background" class="absolute inset-0 w-full h-full object-cover opacity-80">
            <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-transparent"></div>
            <div class="relative z-10 p-12 flex flex-col justify-between h-full text-white">
                <div>
                    <a href="/">
                        <x-application-logo class="w-20 h-20 fill-current text-white" />
                    </a>
                    <h1 class="text-4xl font-bold mt-8">Welcome Back</h1>
                    <p class="mt-4 text-lg text-gray-300">Experience the best cloud kitchen service in town. Fresh,
                        fast, and delicious.</p>
                </div>
                <div>
                    <p class="text-sm opacity-70">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center items-center p-8 bg-gray-50">
            <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-xl">
                <div class="flex flex-col items-center mb-6 lg:hidden">
                    <a href="/">
                        <x-application-logo class="w-16 h-16 fill-current text-gray-800" />
                    </a>
                </div>

                {{ $slot }}
            </div>
        </div>
    </div>
</body>

</html>