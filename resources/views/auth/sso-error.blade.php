<x-guest-layout>
    <div class="w-full max-w-lg p-6 space-y-6 bg-white rounded-xl shadow-lg dark:bg-darker border border-gray-100 dark:border-gray-700">
        <!-- Error Status & Header -->
        <div class="flex flex-col items-center text-center space-y-3">
            <div class="p-3 bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400 rounded-full inline-flex items-center justify-center">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300">
                    HTTP 403 &bull; Akses Ditolak
                </span>
                <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-light">
                    {{ $title }}
                </h1>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-300 max-w-md leading-relaxed">
                {{ $message }}
            </p>
        </div>

        <!-- Technical Context Box -->
        <div class="p-4 rounded-lg bg-gray-50 dark:bg-dark text-xs space-y-2 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300">
            <div class="font-semibold text-gray-800 dark:text-gray-200 uppercase tracking-wider text-[10px] pb-1 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <span>Detail Permintaan Masuk</span>
                <span class="font-mono text-red-600 dark:text-red-400 font-bold">{{ $errorCode }}</span>
            </div>
            @if (!empty($identifier))
                <div class="flex justify-between items-center py-1">
                    <span class="text-gray-500 dark:text-gray-400">Identitas Pengguna:</span>
                    <span class="font-mono font-medium text-gray-900 dark:text-light">{{ $identifier }}</span>
                </div>
            @endif
            @if (!empty($role))
                <div class="flex justify-between items-center py-1">
                    <span class="text-gray-500 dark:text-gray-400">Peran Sistem:</span>
                    <span class="capitalize font-medium text-gray-900 dark:text-light">{{ $role }}</span>
                </div>
            @endif
            <div class="flex justify-between items-center py-1">
                <span class="text-gray-500 dark:text-gray-400">Waktu Percobaan:</span>
                <span>{{ now()->timezone('Asia/Jakarta')->translatedFormat('d F Y, H:i:s') }} WIB</span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-2 pt-2">
            <a href="{{ config('sso.management_url', 'http://localhost:8000') }}"
               class="w-full inline-flex items-center justify-center px-4 py-2.5 font-medium text-center text-white transition-colors duration-200 rounded-md bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 dark:focus:ring-offset-darker shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke E-Management Asrama
            </a>

            <a href="{{ route('login') }}"
               class="w-full inline-flex items-center justify-center px-4 py-2 font-medium text-center text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 transition-colors duration-200 rounded-md border border-gray-300 dark:border-gray-600 text-sm">
                Login Manual E-Klinik
            </a>
        </div>

        <!-- Footer Note -->
        <div class="text-center text-xs text-gray-500 dark:text-gray-400">
            Butuh bantuan? Hubungi Unit Pengelola Asrama & Layanan Kesehatan Polbangtan Malang.
        </div>
    </div>
</x-guest-layout>
