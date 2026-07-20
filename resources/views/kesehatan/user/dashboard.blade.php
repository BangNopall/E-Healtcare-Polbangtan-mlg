<x-app-layout>
    {{-- Content Header --}}
    <div class="flex items-center justify-between px-2 sm:px-4 py-3 lg:py-5 border-b dark:border-blue-800">
        <h1 class="text-2xl font-semibold">Kesehatan Overview</h1>
    </div>
    {{-- Main Content --}}
    <div class="px-2 sm:px-4 py-3 lg:py-5 border-b dark:border-blue-600">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 w-full">
            <div class="bg-white dark:bg-darker p-4 rounded-lg">
                <div class="text-sm mb-3 flex items-center">
                    <span class="icon-[solar--user-linear] me-2"></span>
                    Total Pasien
                </div>
                <span class="text-xl font-semibold">{{ $pasien }}</span>
                <div class="mt-3 text-xs text-yellow-400 font-light">Pasien terdaftar.</div>
            </div>
            <div class="bg-white dark:bg-darker p-4 rounded-lg">
                <div class="text-sm mb-3 flex items-center">
                    <span class="icon-[mdi--doctor] me-2"></span>
                    Total Dokter
                </div>
                <span class="text-xl font-semibold">{{ $dokter }}</span>
                <div class="mt-3 text-xs text-yellow-400">Dokter terdaftar.</div>
            </div>
            <div class="bg-white dark:bg-darker p-4 rounded-lg">
                <div class="flex justify-center items-center">
                    <div class="flex flex-col gap-1 text-center">
                        <div class="flex items-center justify-center">
                            @if (Auth::user()->avatar_url != null)
                                <img class="w-10 h-10 rounded-full"
                                    src="{{ asset('storage/images/' . Auth::user()->avatar_url) }}"
                                    alt="{{ Auth::user()->name }}" />
                            @else
                                <img class="w-10 h-10 rounded-full" :src="generateAvatar('{{ Auth::user()->email }}')"
                                    alt="{{ Auth::user()->name }}" />
                            @endif
                        </div>
                        <div class="flex flex-col">
                            <span class="font-semibold">{{ Auth::user()->name }}</span>
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ Auth::user()->role }}</span>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="px-2 sm:px-4 py-3 lg:py-5">
        <div class="flex flex-col md:flex-row gap-1">
            <div class="bg-white dark:bg-darker p-4 rounded-lg w-full">
                <div class="">Kesehatan Surat Overview</div>
                <div class="bg-gray-100 dark:bg-dark p-2 rounded-md mt-2">
                    <div class="w-full mt-1" style="width: 100%;">
                        <canvas id="surat" :class="{ 'dark': isDark }"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('chartjs/dist/chart.umd.js') }}" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="module" src="{{ asset('src/js/kesehatan/dashboard-user.js') }}"></script>
</x-app-layout>
