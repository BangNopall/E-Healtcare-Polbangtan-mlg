<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name', 'E-Klinik Polbangtan-mlg') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="stylesheet" href="{{ asset('src/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('src/css/main2.css') }}">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div x-data="setup()" x-init="$refs.loading.classList.add('hidden'); setColors(color);" :class="{ 'dark': isDark }">
        <!-- Loading screen -->
        <div class="inset-0 bg-gray-800 fixed flex w-full h-full items-center justify-center duration-300 transition-opacity"
                style="z-index: 6000" x-ref="loading">
                <!-- Include your loading component content here -->
                @include('layouts.components.loading')
        </div>
        <div class="flex flex-col items-center justify-center min-h-screen p-4 space-y-4 antialiased text-gray-900 bg-gray-100 dark:bg-dark dark:text-light">
            <!-- Brand -->
            <div class="flex flex-row gap-2 items-center">
                <a href="https://asramapolbangtan-mlg.com">
                    <img src="{{ asset('img/logo-200.png') }}" class="w-[90px] rounded-full shadow-lg border-2 border-blue-700" alt="">
                </a>
                <a href="https://kesehatan.asramapolbangtan-mlg.com">
                    <img src="{{ asset('img/logo-klinik.jpeg') }}" class="w-[90px] rounded-full shadow-lg border-2 border-blue-700" alt="">
                </a>
            </div>
            <a href="/"
                class="inline-block mb-6 text-3xl font-bold tracking-wider uppercase text-blue-700 dark:text-light">
                {{ config('app.name') }}
            </a>
            <main class="w-full flex flex-col items-center justify-center">
                {{ $slot }}
            </main>
        </div>
        <!-- Toggle dark mode button -->
        <div class="fixed bottom-5 left-5">
            @include('auth.layouts.button-darkmode')
        </div>
    </div>
</body>
<script src="{{ asset('src/js/auth.js') }}"></script>
</html>

