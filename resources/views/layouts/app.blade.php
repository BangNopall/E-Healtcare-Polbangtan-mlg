<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <link rel="shortcut icon" href="{{ asset('img/logo-klinik.jpeg') }}" type="image/x-icon">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- Css -->
    <link rel="stylesheet" href="{{ asset('src/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('remixicon/fonts/remixicon.css') }}">
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div x-data="setup()" x-init="$refs.loading.classList.add('hidden');" :class="{ 'dark': isDark }">
        <div class="flex h-screen antialiased text-gray-900 bg-gray-100 dark:bg-dark dark:text-light">
            <!-- Loading screen -->
            <div class="inset-0 bg-gray-800 fixed flex w-full h-full items-center justify-center duration-300 transition-opacity"
                style="z-index: 6000" x-ref="loading">
                <!-- Include your loading component content here -->
                @include('layouts.components.loading')
            </div>
            {{-- loading function --}}

            <!-- Sidebar -->
            @include('layouts.sidebar')

            {{-- Session Status Alert --}}
            @include('layouts.session-alert')

            <div class="relative flex flex-col flex-1 min-h-screen overflow-x-hidden overflow-y-auto">
                <!-- Navbar -->
                @include('layouts.navigation')
                <!-- Main content -->
                <main class="flex-grow ">
                    {{ $slot }}
                </main>
                <div
                    class="bg-white dark:bg-darker bottom-0 w-full left-0 px-4 border-t dark:border-blue-600 py-2 text-sm text-gray-500 dark:text-gray-300">
                    <div class="flex justify-between">
                        <span>© 2023-2024 E-Klinik Polbangtan-mlg</span>
                        <span>v{{ config('app.version') }}</span>
                    </div>
                </div>
            </div>
            <!-- Settings Panel -->
            @include('layouts.setting-panel')
            <!-- Notification panel -->
            @include('layouts.notification-panel')
            <!-- Search panel -->
            @include('layouts.search-panel')
        </div>
    </div>
</body>
<script src="{{ asset('src/js/alpine.js') }}"></script>
<script src="{{ asset('src/js/flowbite.min.js') }}"></script>
</html>
