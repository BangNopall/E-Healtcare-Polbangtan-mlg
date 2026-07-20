<x-app-layout>
    {{-- Content Header --}}
    <div class="flex items-center justify-between px-2 sm:px-4 py-3 lg:py-5 border-b dark:border-blue-800">
        <h1 class="text-2xl font-semibold">Medical Overview</h1>
    </div>
    {{-- Main Content --}}
    <div class="px-2 sm:px-4 py-3 lg:py-5 border-b dark:border-blue-600">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 w-full">
            <div class="bg-white dark:bg-darker p-4 rounded-lg">
                <div class="text-sm mb-3 flex items-center">
                    <span class="icon-[solar--user-linear] me-2"></span>
                    Total Mahasiswa
                </div>
                <span class="text-xl font-semibold">{{ $mahasiswa }}</span>
                <div class="mt-3 text-xs text-yellow-400 font-light">Mahasiswa terdaftar.</div>
            </div>
            <div class="bg-white dark:bg-darker p-4 rounded-lg">
                <div class="text-sm mb-3 flex items-center">
                    <span class="icon-[mdi--doctor] me-2"></span>
                    Total Psikiater
                </div>
                <span class="text-xl font-semibold">{{ $psikiater }}</span>
                <div class="mt-3 text-xs text-yellow-400">Psikiater terdaftar.</div>
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
        <div class="flex flex-col md:flex-row gap-3">
            {{-- Konseling --}}
            <div class="bg-white dark:bg-darker p-4 rounded-lg w-full">
                <div class="flex items-center gap-2 text-lg font-medium">
                    <span class="icon-[fluent--person-feedback-28-regular]"></span>
                    <span class="">Riwayat Konseling</span>
                </div>
                <div class="bg-gray-100 dark:bg-dark p-2 rounded-md mt-2">
                    <div class="text-sm">Riwayat Data</div>
                    <div class="w-full mt-1" style="width: 100%;">
                        <canvas id="konseling" :class="{ 'dark': isDark }"></canvas>
                    </div>
                </div>
            </div>
            <div class="flex flex-col gap-3 w-full">
                <div class="bg-white dark:bg-darker p-4 rounded-lg lg:col-span-2">
                    <div class="bg-gray-100 dark:bg-dark p-2 rounded mb-2">
                        <div class="font-semibold text-md">Jadwal Konseling Hari ini</div>
                        <div class="text-gray-700 dark:text-gray-300 text-sm pt-1">
                            @if ($materitoday)
                                {{ $materitoday->materi }}
                            @else
                                Belum Tersedia
                            @endif
                            
                        </div>
                    </div>
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 shadow-md rounded">
                        <div
                            class="text-sm font-medium text-gray-900 uppercase bg-gray-100 p-2 dark:bg-dark dark:text-gray-100">
                            PRESENSI SENSUH HARI INI
                        </div>
                        <tbody>
                            <tr class="bg-white border-b dark:bg-darker dark:border-gray-700 whitespace-nowrap text-xs">
                                <th scope="row" class="p-2">
                                    IZIN
                                </th>
                                <td class="p-2 flex justify-end">
                                    <span class="badge bg-orange-500 text-xs text-white p-1 rounded">
                                        {{ $izin }} <small>Siswa</small>
                                    </span>
                                </td>
                            </tr>
                            <tr class="bg-white border-b dark:bg-darker dark:border-gray-700 whitespace-nowrap text-xs">
                                <th scope="row" class="p-2">
                                    SAKIT
                                </th>
                                <td class="p-2 flex justify-end">
                                    <span class="badge bg-yellow-500 text-xs text-white p-1 rounded">
                                        {{ $sakit }} <small>Siswa</small>
                                    </span>
                                </td>
                            </tr>
                            <tr class="bg-white border-b dark:bg-darker dark:border-gray-700 whitespace-nowrap text-xs">
                                <th scope="row" class="p-2">
                                    ALPHA
                                </th>
                                <td class="p-2 flex justify-end">
                                    <span class="badge bg-red-500 text-xs text-white p-1 rounded">
                                        {{ $alpha }} <small>Siswa</small>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="bg-white dark:bg-darker p-4 rounded-lg">
                    <div class="flex items-center gap-2 text-lg font-medium">
                        <span class="icon-[uiw--date]"></span>
                        <span class="">Riwayat Jadwal Terakhir</span>
                    </div>
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 shadow-md rounded mt-2">
                        <thead class="text-xs text-gray-900 uppercase bg-gray-100 dark:bg-dark dark:text-gray-100">
                            <tr class="whitespace-nowrap">
                                <th scope="col" class="p-2 w-[30px]">
                                    #
                                </th>
                                <th scope="col" class="p-2">
                                    Judul Materi
                                </th>
                                <th scope="col" class="p-2">
                                    Tanggal
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lastjadwal as $item)
                                <tr class="bg-white border-b dark:bg-darker dark:border-gray-700 whitespace-nowrap">
                                    <td class="p-2">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="p-2">
                                        {{ $item->materi }}
                                    </td>
                                    <td class="p-2">
                                        {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
    <script src="{{ asset('chartjs/dist/chart.umd.js') }}" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="module" src="{{ asset('src/js/konseling/dashboard-admin.js') }}"></script>
</x-app-layout>
