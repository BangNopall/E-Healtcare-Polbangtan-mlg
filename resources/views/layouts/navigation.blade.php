<header class="relative bg-white dark:bg-darker">
    <div class="flex items-center justify-between p-2 border-b dark:border-blue-800">
        <!-- Mobile menu button -->
        <button @click="isMobileMainMenuOpen = !isMobileMainMenuOpen"
            class="p-1 text-blue-400 transition-colors duration-200 rounded-md bg-blue-50 hover:text-blue-600 hover:bg-blue-100 dark:hover:text-light dark:hover:bg-blue-700 dark:bg-dark lg:hidden focus:outline-none focus:ring">
            <span class="sr-only">Open main manu</span>
            <span aria-hidden="true">
                <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </span>
        </button>
        <!-- Brand -->
        <div class="shrink-0 flex items-center">
            <a href="{{ route('inventaris.obat.index') }}">
                <x-application-logo class="block h-10 rounded-lg w-10 fill-current text-gray-800 dark:text-gray-200" />
            </a>
        </div>
        <!-- Mobile sub menu button -->
        <button @click="isMobileSubMenuOpen = !isMobileSubMenuOpen"
            class="p-1 text-blue-400 transition-colors duration-200 rounded-md bg-blue-50 hover:text-blue-600 hover:bg-blue-100 dark:hover:text-light dark:hover:bg-blue-700 dark:bg-dark lg:hidden focus:outline-none focus:ring">
            <span class="sr-only">Open sub manu</span>
            <span aria-hidden="true">
                <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                </svg>
            </span>
        </button>

        <!-- Desktop Right buttons -->
        <nav aria-label="Secondary" class="hidden space-x-2 lg:flex lg:items-center lg:justify-center">
            @include('layouts.components.button-submenu-dekstop')

            <!-- User avatar button -->
            <div class="relative lg:flex lg:items-center lg:justify-center" x-data="{ open: false }">
                @include('layouts.components.avatar')

                <!-- User dropdown menu -->
                @include('layouts.components.profile-dropdown')
            </div>
        </nav>

        <!-- Mobile sub menu -->
        <nav x-transition:enter="transition duration-200 ease-in-out transform sm:duration-500"
            x-transition:enter-start="-translate-y-full opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="transition duration-300 ease-in-out transform sm:duration-500"
            x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="-translate-y-full opacity-0"
            x-show="isMobileSubMenuOpen" @click.away="isMobileSubMenuOpen = false"
            class="absolute flex items-center p-4 bg-white rounded-md shadow-lg dark:bg-darker top-16 inset-x-4 lg:hidden z-50"
            aria-label="Secondary">
            <div class="space-x-2">
                @include('layouts.components.button-submenu-mobile')
            </div>

            <!-- User avatar button -->
            <div class="relative ml-auto" x-data="{ open: false }">
                @include('layouts.components.avatar')

                <!-- User dropdown menu -->
                @include('layouts.components.profile-dropdown')
            </div>
        </nav>
    </div>
    <!-- Mobile main manu -->
    <div class="border-b lg:hidden dark:border-blue-800" x-show="isMobileMainMenuOpen"
        @click.away="isMobileMainMenuOpen = false">
        <nav aria-label="Main" class="px-2 py-4 space-y-2">
            <!-- Dashboards links -->
            <div x-data="{{ Request::is('inventaris', 'medical', 'konseling', 'user/medical', 'user/konseling') ? '{ isActive: true, open: true }' : '{ isActive: false, open: false }' }}">
                <!-- active & hover classes 'bg-blue-100 dark:bg-blue-600' -->
                <a href="#" @click="$event.preventDefault(); open = !open"
                    class="flex items-center p-2 text-gray-500 transition-colors rounded-md dark:text-light hover:bg-blue-100 dark:hover:bg-blue-600"
                    :class="{ 'bg-blue-100 dark:bg-blue-600': isActive || open }" role="button" aria-haspopup="true"
                    :aria-expanded="(open || isActive) ? 'true' : 'false'">
                    <span aria-hidden="true">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </span>
                    <span class="ml-2 text-sm"> Dashboards </span>
                    <span class="ml-auto" aria-hidden="true">
                        <!-- active class 'rotate-180' -->
                        <svg class="w-4 h-4 transition-transform transform" :class="{ 'rotate-180': open }"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </span>
                </a>
                {{-- dashboard admin, dokter, pskiater --}}
                @if (
                    (Auth::check() && Auth::user()->role == 'Admin') ||
                        Auth::user()->role == 'Dokter' ||
                        Auth::user()->role == 'Psikolog' ||
                        Auth::user()->role == 'Perawat')
                    <div role="menu" x-show="open" class="mt-2 space-y-2 px-7" aria-label="Dashboards">
                        <a href="/inventaris/" role="menuitem"
                            class="{{ Request::is('inventaris') ? 'text-gray-700 dark:text-light' : 'text-gray-400 hover:text-gray-700 dark:text-gray-400' }} block p-2 text-sm transition-colors duration-200 rounded-md  dark:hover:text-light">
                            Inventaris
                        </a>
                        <a href="/medical/" role="menuitem"
                            class="{{ Request::is('medical') ? 'text-gray-700 dark:text-light' : 'text-gray-400 hover:text-gray-700 dark:text-gray-400' }} block p-2 text-sm transition-colors duration-200 rounded-md  dark:hover:text-light">
                            Medical
                        </a>
                        <a href="/konseling/" role="menuitem"
                            class="{{ Request::is('konseling') ? 'text-gray-700 dark:text-light' : 'text-gray-400 hover:text-gray-700 dark:text-gray-400' }} block p-2 text-sm transition-colors duration-200 rounded-md  dark:hover:text-light">
                            Konseling
                        </a>
                    </div>
                @endif
                {{-- dashboard user --}}
                @if ((Auth::check() && Auth::user()->role == 'Mahasiswa') || Auth::user()->role == 'Karyawan')
                    <div role="menu" x-show="open" class="mt-2 space-y-2 px-7" aria-label="Dashboards">
                        <a href="/user/medical/" role="menuitem"
                            class="{{ Request::is('user/medical') ? 'text-gray-700 dark:text-light' : 'text-gray-400 hover:text-gray-700 dark:text-gray-400' }} block p-2 text-sm transition-colors duration-200 rounded-md  dark:hover:text-light">
                            Medical
                        </a>
                    </div>
                @endif
            </div>

            <!-- Inventaris links -->
            {{-- invetaris admin, dokter, pskiater --}}
            @if (
                (Auth::check() && Auth::user()->role == 'Admin') ||
                    Auth::user()->role == 'Dokter' ||
                    Auth::user()->role == 'Psikolog' ||
                    Auth::user()->role == 'Perawat')
                <div x-data="{{ Request::is('inventaris/*') ? '{ isActive: true, open: true }' : '{ isActive: false, open: false }' }}">
                    <!-- active classes 'bg-blue-100 dark:bg-blue-600' -->
                    <a href="#" @click="$event.preventDefault(); open = !open"
                        class="flex items-center p-2 text-gray-500 transition-colors rounded-md dark:text-light hover:bg-blue-100 dark:hover:bg-blue-600"
                        :class="{ 'bg-blue-100 dark:bg-blue-600': isActive || open }" role="button"
                        aria-haspopup="true" :aria-expanded="(open || isActive) ? 'true' : 'false'">
                        <span class="icon-[material-symbols--inventory-2-outline] w-5 h-5"></span>
                        <span class="ml-2 text-sm"> Inventaris </span>
                        <span aria-hidden="true" class="ml-auto">
                            <!-- active class 'rotate-180' -->
                            <svg class="w-4 h-4 transition-transform transform" :class="{ 'rotate-180': open }"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </span>
                    </a>
                    <div x-show="open" class="mt-2 px-7" role="menu" arial-label="Components">
                        <!-- active & hover classes 'text-gray-700 dark:text-light' -->
                        <!-- inActive classes 'text-gray-400 dark:text-gray-400' -->
                        <a href="{{ route('inventaris.obat.index') }}" role="menuitem"
                            class="{{ Request::is('inventaris/obat/*') ? 'text-gray-700 dark:text-light' : 'text-gray-400 hover:text-gray-700 dark:text-gray-400' }} block p-2 text-sm transition-colors duration-200 rounded-md  dark:hover:text-light">
                            Stok Obat
                        </a>
                        <a href="{{ route('inventaris.alat.index') }}" role="menuitem"
                            class="{{ Request::is('inventaris/alat*') ? 'text-gray-700 dark:text-light' : 'text-gray-400 hover:text-gray-700 dark:text-gray-400' }} block p-2 text-sm transition-colors duration-200 rounded-md  dark:hover:text-light">
                            Stok Alat Terpakai
                        </a>
                        <a href="{{ route('inventaris.consumable.index') }}" role="menuitem"
                            class="{{ Request::is('inventaris/consumable*') ? 'text-gray-700 dark:text-light' : 'text-gray-400 hover:text-gray-700 dark:text-gray-400' }} block p-2 text-sm transition-colors duration-200 rounded-md  dark:hover:text-light">
                            Stok Alat Tersisa
                        </a>
                        <a href="{{ route('inventaris.pengaturanInventaris') }}" role="menuitem"
                            class="{{ Request::is('inventaris/pengaturan*') ? 'text-gray-700 dark:text-light' : 'text-gray-400 hover:text-gray-700 dark:text-gray-400' }} block p-2 text-sm transition-colors duration-200 rounded-md  dark:hover:text-light">
                            Pengaturan
                        </a>
                    </div>
                </div>
            @endif

            <!-- Kesehatan links -->
            <div x-data="{{ Request::is('medical/*', 'user/medical/*') ? '{ isActive: true, open: true }' : '{ isActive: false, open: false }' }}">
                <!-- active classes 'bg-blue-100 dark:bg-blue-600' -->
                <a href="#" @click="$event.preventDefault(); open = !open"
                    class="flex items-center p-2 text-gray-500 transition-colors rounded-md dark:text-light hover:bg-blue-100 dark:hover:bg-blue-600"
                    :class="{ 'bg-blue-100 dark:bg-blue-600': isActive || open }" role="button" aria-haspopup="true"
                    :aria-expanded="(open || isActive) ? 'true' : 'false'">
                    <span class="icon-[mingcute--hospital-line] w-5 h-5"></span>
                    <span class="ml-2 text-sm"> Medical </span>
                    <span aria-hidden="true" class="ml-auto">
                        <!-- active class 'rotate-180' -->
                        <svg class="w-4 h-4 transition-transform transform" :class="{ 'rotate-180': open }"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </span>
                </a>
                {{-- kesehatan admin, dokter, pskiater --}}
                @if (
                    (Auth::check() && Auth::user()->role == 'Admin') ||
                        Auth::user()->role == 'Dokter' ||
                        Auth::user()->role == 'Psikolog' ||
                        Auth::user()->role == 'Perawat')
                    <div x-show="open" class="mt-2 space-y-2 px-7" role="menu" arial-label="kesehatan">
                        <!-- active & hover classes 'text-gray-700 dark:text-light' -->
                        <!-- inActive classes 'text-gray-400 dark:text-gray-400' -->
                        <a href="/medical/kamera" role="menuitem"
                            class="{{ Request::is('medical/kamera*') ? 'text-gray-700 dark:text-light' : 'text-gray-400 hover:text-gray-700 dark:text-gray-400' }} block p-2 text-sm transition-colors duration-200 rounded-md  dark:hover:text-light">
                            Scan QR
                        </a>
                        <a href="/medical/data-kontrol-pasien" role="menuitem"
                            class="{{ Request::is('medical/data-kontrol-pasien*') ? 'text-gray-700 dark:text-light' : 'text-gray-400 hover:text-gray-700 dark:text-gray-400' }} block p-2 text-sm transition-colors duration-200 rounded-md  dark:hover:text-light">
                            Data Kontrol Pasien
                        </a>
                        <a href="/medical/request-rujukan-konseling" role="menuitem"
                            class="{{ Request::is('medical/request-rujukan-konseling*') ? 'text-gray-700 dark:text-light' : 'text-gray-400 hover:text-gray-700 dark:text-gray-400' }} block p-2 text-sm transition-colors duration-200 rounded-md  dark:hover:text-light">
                            Rujukan Konseling
                        </a>
                        <a href="/medical/riwayat-surat" role="menuitem"
                            class="{{ Request::is('medical/riwayat-surat*') ? 'text-gray-700 dark:text-light' : 'text-gray-400 hover:text-gray-700 dark:text-gray-400' }} block p-2 text-sm transition-colors duration-200 rounded-md  dark:hover:text-light">
                            Riwayat Surat
                        </a>
                        <a href="/medical/export-rekammedis" role="menuitem"
                            class="{{ Request::is('medical/riwayat-surat*') ? 'text-gray-700 dark:text-light' : 'text-gray-400 hover:text-gray-700 dark:text-gray-400' }} block p-2 text-sm transition-colors duration-200 rounded-md  dark:hover:text-light">
                            Export Rekam Medis
                        </a>
                    </div>
                @endif
                {{-- kesehatan mahasiswa dan karyawan --}}
                @if ((Auth::check() && Auth::user()->role == 'Mahasiswa') || Auth::user()->role == 'Karyawan')
                    <div x-show="open" class="mt-2 space-y-2 px-7" role="menu" arial-label="kesehatan">
                        <!-- active & hover classes 'text-gray-700 dark:text-light' -->
                        <!-- inActive classes 'text-gray-400 dark:text-gray-400' -->
                        <a href="{{ route('user.kesehatan.kodeqr') }}" role="menuitem"
                            class="{{ Request::is('user/medical*') ? 'text-gray-700 dark:text-light' : 'text-gray-400 hover:text-gray-700 dark:text-gray-400' }} block p-2 text-sm transition-colors duration-200 rounded-md  dark:hover:text-light">
                            Kode QR
                        </a>
                        <a href="/user/medical/riwayat-laporan" role="menuitem"
                            class="{{ Request::is('user/medical/riwayat-laporan*') ? 'text-gray-700 dark:text-light' : 'text-gray-400 hover:text-gray-700 dark:text-gray-400' }} block p-2 text-sm transition-colors duration-200 rounded-md  dark:hover:text-light">
                            Riwayat Laporan
                        </a>
                    </div>
                @endif
            </div>

            <!-- Konseling links -->
            {{-- konseling admin, dokter, psikiater, mahasiswa --}}
            @if (
                (Auth::check() && Auth::user()->role == 'Admin') ||
                    Auth::user()->role == 'Dokter' ||
                    Auth::user()->role == 'Psikolog' ||
                    Auth::user()->role == 'Perawat' ||
                    Auth::user()->role == 'Mahasiswa')
                <div x-data="{{ Request::is('konseling/*', 'user/konseling/*') ? '{ isActive: true, open: true }' : '{ isActive: false, open: false }' }}">
                    <!-- active & hover classes 'bg-blue-100 dark:bg-blue-600' -->
                    <a href="#" @click="$event.preventDefault(); open = !open"
                        class="flex items-center p-2 text-gray-500 transition-colors rounded-md dark:text-light hover:bg-blue-100 dark:hover:bg-blue-600"
                        :class="{ 'bg-blue-100 dark:bg-blue-600': isActive || open }" role="button"
                        aria-haspopup="true" :aria-expanded="(open || isActive) ? 'true' : 'false'">
                        <span class="icon-[icon-park-twotone--brain] w-5 h-5"></span>
                        <span class="ml-2 text-sm"> Konseling </span>
                        <span aria-hidden="true" class="ml-auto">
                            <!-- active class 'rotate-180' -->
                            <svg class="w-4 h-4 transition-transform transform" :class="{ 'rotate-180': open }"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </span>
                    </a>
                    {{-- konseling admin, dokter, pskiater --}}
                    @if (
                        (Auth::check() && Auth::user()->role == 'Admin') ||
                            Auth::user()->role == 'Dokter' ||
                            Auth::user()->role == 'Psikolog' ||
                            Auth::user()->role == 'Perawat')
                        <div x-show="open" class="mt-2 space-y-2 px-7" role="menu" arial-label="konseling">
                            <!-- active & hover classes 'text-gray-700 dark:text-light' -->
                            <!-- inActive classes 'text-gray-400 dark:text-gray-400' -->
                            <a href="{{ route('konseling.jadwal-bimbingan') }}" role="menuitem"
                                class="{{ Request::is('konseling/jadwal-bimbingan*') ? 'text-gray-700 dark:text-light' : 'text-gray-400 hover:text-gray-700 dark:text-gray-400' }} block p-2 text-sm transition-colors duration-200 rounded-md  dark:hover:text-light">
                                Jadwal
                            </a>
                            <a href="/konseling/kamera-bimbingan" role="menuitem"
                                class="{{ Request::is('konseling/kamera-bimbingan*') ? 'text-gray-700 dark:text-light' : 'text-gray-400 hover:text-gray-700 dark:text-gray-400' }} block p-2 text-sm transition-colors duration-200 rounded-md  dark:hover:text-light">
                                Kamera Bimbingan
                            </a>
                            <a href="/konseling/kamera-konsultasi" role="menuitem"
                                class="{{ Request::is('konseling/kamera-konsultas*') ? 'text-gray-700 dark:text-light' : 'text-gray-400 hover:text-gray-700 dark:text-gray-400' }} block p-2 text-sm transition-colors duration-200 rounded-md  dark:hover:text-light">
                                Kamera Konsultasi
                            </a>
                            <a href="/konseling/riwayat-feedback" role="menuitem"
                                class="{{ Request::is('konseling/riwayat-feedback*') ? 'text-gray-700 dark:text-light' : 'text-gray-400 hover:text-gray-700 dark:text-gray-400' }} block p-2 text-sm transition-colors duration-200 rounded-md  dark:hover:text-light">
                                Riwayat Feedback
                            </a>
                            <a href="/konseling/riwayat-konsultasi" role="menuitem"
                                class="{{ Request::is('konseling/riwayat-konsultasi*') ? 'text-gray-700 dark:text-light' : 'text-gray-400 hover:text-gray-700 dark:text-gray-400' }} block p-2 text-sm transition-colors duration-200 rounded-md  dark:hover:text-light">
                                Riwayat Konsultasi
                            </a>
                            <a href="/konseling/hasil-surat-rujukan" role="menuitem"
                                class="{{ Request::is('konseling/hasil-surat-rujukan*') ? 'text-gray-700 dark:text-light' : 'text-gray-400 hover:text-gray-700 dark:text-gray-400' }} block p-2 text-sm transition-colors duration-200 rounded-md  dark:hover:text-light">
                                Hasil Surat Rujukan
                            </a>
                            <a href="/konseling/data-sensuh" role="menuitem"
                                class="{{ Request::is('konseling/data-sensuh*') ? 'text-gray-700 dark:text-light' : 'text-gray-400 hover:text-gray-700 dark:text-gray-400' }} block p-2 text-sm transition-colors duration-200 rounded-md  dark:hover:text-light">
                                Data Sensu
                            </a>
                            <a href="/konseling/export-konseling" role="menuitem"
                                class="{{ Request::is('konseling/export-konseling*') ? 'text-gray-700 dark:text-light' : 'text-gray-400 hover:text-gray-700 dark:text-gray-400' }} block p-2 text-sm transition-colors duration-200 rounded-md  dark:hover:text-light">
                                Export Konseling
                            </a>
                        </div>
                    @endif
                    {{-- konseling mahasiswa --}}
                    @if (Auth::check() && Auth::user()->role == 'Mahasiswa')
                        <div x-show="open" class="mt-2 space-y-2 px-7" role="menu" arial-label="kesehatan">
                            <!-- active & hover classes 'text-gray-700 dark:text-light' -->
                            <!-- inActive classes 'text-gray-400 dark:text-gray-400' -->
                            @if (Auth::check() && Auth::user()->senso == 1)
                                <a href="{{ route('user.konseling.kodeqr-bimbingan') }}" role="menuitem"
                                    class="{{ Request::is('user/konseling/kodeqr-bimbingan*') ? 'text-gray-700 dark:text-light' : 'text-gray-400 hover:text-gray-700 dark:text-gray-400' }} block p-2 text-sm transition-colors duration-200 rounded-md  dark:hover:text-light">
                                    QR Bimbingan
                                </a>
                            @endif
                            <a href="/user/konseling/link-feedback" role="menuitem"
                                class="{{ Request::is('user/konseling/link-feedback*') ? 'text-gray-700 dark:text-light' : 'text-gray-400 hover:text-gray-700 dark:text-gray-400' }} block p-2 text-sm transition-colors duration-200 rounded-md  dark:hover:text-light">
                                Feedback
                            </a>
                            <a href="/user/konseling/kodeqr-konsultasi" role="menuitem"
                                class="{{ Request::is('user/konseling/kodeqr-konsultasi*') ? 'text-gray-700 dark:text-light' : 'text-gray-400 hover:text-gray-700 dark:text-gray-400' }} block p-2 text-sm transition-colors duration-200 rounded-md  dark:hover:text-light">
                                QR Konsultasi
                            </a>
                            <a href="/user/konseling/riwayat-konsultasi" role="menuitem"
                                class="{{ Request::is('user/konseling/riwayat-konsultasi*') ? 'text-gray-700 dark:text-light' : 'text-gray-400 hover:text-gray-700 dark:text-gray-400' }} block p-2 text-sm transition-colors duration-200 rounded-md  dark:hover:text-light">
                                Riwayat Konsultasi
                            </a>
                        </div>
                    @endif
                </div>
            @endif

            {{-- lainnya admin, dokter, pskiater --}}
            @if (
                (Auth::check() && Auth::user()->role == 'Admin'))
                <div x-data="{{ Request::is('lainnya/*') ? '{ isActive: true, open: true }' : '{ isActive: false, open: false }' }}">
                    <!-- active & hover classes 'bg-blue-100 dark:bg-blue-600' -->
                    <a href="#" @click="$event.preventDefault(); open = !open"
                        class="flex items-center p-2 text-gray-500 transition-colors rounded-md dark:text-light hover:bg-blue-100 dark:hover:bg-blue-600"
                        :class="{ 'bg-blue-100 dark:bg-blue-600': isActive || open }" role="button"
                        aria-haspopup="true" :aria-expanded="(open || isActive) ? 'true' : 'false'">
                        <span class="icon-[ic--outline-miscellaneous-services] w-5 h-5"></span>
                        <span class="ml-2 text-sm"> Lain - lainnya </span>
                        <span aria-hidden="true" class="ml-auto">
                            <!-- active class 'rotate-180' -->
                            <svg class="w-4 h-4 transition-transform transform" :class="{ 'rotate-180': open }"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </span>
                    </a>
                    <div x-show="open" class="mt-2 space-y-2 px-7" role="menu" aria-label="Authentication">
                        <!-- active & hover classes 'text-gray-700 dark:text-light' -->
                        <!-- inActive classes 'text-gray-400 dark:text-gray-400' -->
                        <a href="{{ route('lainnya.mahasiswa.index') }}" role="menuitem"
                            class="{{ Request::is('lainnya/mahasiswa*') ? 'text-gray-700 dark:text-light' : 'text-gray-400 hover:text-gray-700 dark:text-gray-400' }} block p-2 text-sm transition-colors duration-200 rounded-md  dark:hover:text-light">
                            Data Mahasiswa
                        </a>
                        <a href="{{ route('lainnya.karyawan.index') }}" role="menuitem"
                            class="{{ Request::is('lainnya/karyawan*') ? 'text-gray-700 dark:text-light' : 'text-gray-400 hover:text-gray-700 dark:text-gray-400' }} block p-2 text-sm transition-colors duration-200 rounded-md  dark:hover:text-light">
                            Data Karyawan
                        </a>
                        <a href="{{ route('lainnya.petugas.index') }}" role="menuitem"
                            class="{{ Request::is('lainnya/petugas*') ? 'text-gray-700 dark:text-light' : 'text-gray-400 hover:text-gray-700 dark:text-gray-400' }} block p-2 text-sm transition-colors duration-200 rounded-md  dark:hover:text-light">
                            Data Petugas
                        </a>
                    </div>
                </div>
            @endif
        </nav>
    </div>
</header>
