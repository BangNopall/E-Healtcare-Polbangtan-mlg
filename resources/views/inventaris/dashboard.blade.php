<x-app-layout>
    {{-- Content Header --}}
    <div class="flex items-center justify-between px-4 py-4 border-b lg:py-6 dark:border-blue-800">
        <h1 class="text-2xl font-semibold">Inventaris Overview</h1>
    </div>
    {{-- Main Content --}}
    <div class="px-2 sm:px-4 py-3 lg:py-5 border-b dark:border-blue-600">
        {{-- filter obat --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="bg-white dark:bg-darker p-4 rounded-lg">
                <div class="text-sm mb-3 flex items-center">
                    <span class="icon-[solar--box-linear] me-2"></span>
                    Total Item
                </div>
                <span class="text-xl font-semibold">{{ $total_item }}</span>
                <div class="mt-3 text-xs text-yellow-400 font-light">Item terdaftar.</div>
            </div>
            <div class="bg-white dark:bg-darker p-4 rounded-lg">
                <div class="text-sm mb-3 flex items-center">
                    <span class="icon-[fluent--book-number-16-regular] me-2"></span>
                    Total Kuantitas
                </div>
                <span class="text-xl font-semibold">{{ $total_stok }}</span>
                <div class="mt-3 text-xs text-yellow-400">Kuantitas seluruh item.</div>
            </div>
            <div class="bg-white dark:bg-darker p-4 rounded-lg">
                <div class="text-sm mb-3 flex items-center">
                    <span class="icon-[ion--log-out-outline] me-2"></span>
                    Stok Keluar
                </div>
                <div class="flex justify-between text-red-500 items-center">
                    <span class="text-xl font-semibold">{{ $rata_stockout_bulat }}%</span>
                    <span class="text-sm font-semibold">Bulan ini</span>
                </div>
                <div class="mt-3 text-xs text-yellow-400">Berdasarkan seluruh item.</div>
            </div>
            <div class="bg-white dark:bg-darker p-4 rounded-lg">
                <div class="text-sm mb-3 flex items-center">
                    <span class="icon-[ion--log-in-outline] me-2"></span>
                    Stok Masuk
                </div>
                <div class="flex justify-between text-green-500 items-center">
                    <span class="text-xl font-semibold">{{ $rata_stock_in_bulat }}%</span>
                    <span class="text-sm font-semibold">Bulan ini</span>
                </div>
                <div class="mt-3 text-xs text-yellow-400">Berdasarkan seluruh item.</div>
            </div>
        </div>
    </div>
    <div class="px-2 sm:px-4 py-3 lg:py-5">
        {{-- filter obat --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            {{-- obat --}}
            <div class="bg-white dark:bg-darker p-4 rounded-lg">
                <div class="flex items-center gap-5">
                    <div class="flex items-center gap-2 text-xl font-semibold">
                        <span class="icon-[icon-park-outline--medicine-chest]"></span>
                        <span class="">Obat</span>
                    </div>
                    <div class="h-[50px] w-[2px] bg-gray-200 dark:bg-gray-400"></div>
                    <div class="flex items-center gap-1 font-semibold text-xl">
                        <span>{{ $data_obat }}</span>
                        <span>Item</span>
                    </div>
                </div>
                <div class="bg-gray-100 dark:bg-dark p-2 rounded-md mt-2">
                    <div class="text-sm">Stok Masuk per Bulan</div>
                    <div class="w-full mt-1" style="width: 100%;">
                        <canvas id="obatstockin" :class="{ 'dark': isDark }"></canvas>
                    </div>
                </div>
                <div class="bg-gray-100 dark:bg-dark p-2 rounded-md mt-2">
                    <div class="text-sm">Ringkasan Stok</div>
                    <div class="w-full mt-1">
                        <canvas id="ringkasstokobat" :class="{ 'dark': isDark }"></canvas>
                    </div>
                    <div class="mt-1 text-sm flex flex-col">
                        <span class="text-green-500">- At-Stock : >5</span>
                        <span class="text-yellow-500">- Under-Stock : <5 </span>
                                <span class="text-red-500">- Out-of-Stock : 0</span>
                    </div>
                </div>
            </div>
            {{-- alat terpakai --}}
            <div class="bg-white dark:bg-darker p-4 rounded-lg">
                <div class="flex items-center gap-5">
                    <div class="flex items-center gap-2 text-xl font-semibold">
                        <span class="icon-[mdi--toolbox-outline]"></span>
                        <span class="">Alat Terpakai</span>
                    </div>
                    <div class="h-[50px] w-[3px] bg-gray-200 dark:bg-gray-400"></div>
                    <div class="flex items-center gap-1 font-semibold text-xl">
                        <span>{{ $data_alat_terpakai }}</span>
                        <span>Item</span>
                    </div>
                </div>
                <div class="bg-gray-100 dark:bg-dark p-2 rounded-md mt-2">
                    <div class="text-sm">Stok Masuk per Bulan</div>
                    <div class="w-full mt-1" style="width: 100%;">
                        <canvas id="alatstockin" :class="{ 'dark': isDark }"></canvas>
                    </div>
                </div>
                <div class="bg-gray-100 dark:bg-dark p-2 rounded-md mt-2">
                    <div class="text-sm">Ringkasan Stok</div>
                    <div class="w-full mt-1">
                        <canvas id="ringkasstokalat" :class="{ 'dark': isDark }"></canvas>
                    </div>
                    <div class="mt-1 text-sm flex flex-col">
                        <span class="text-green-500">- At-Stock : >5</span>
                        <span class="text-yellow-500">- Under-Stock : <5 </span>
                                <span class="text-red-500">- Out-of-Stock : 0</span>
                    </div>
                </div>
            </div>
            {{-- alat tersisa --}}
            <div class="bg-white dark:bg-darker p-4 rounded-lg">
                <div class="flex items-center gap-5">
                    <div class="flex items-center gap-2 text-xl font-semibold">
                        <span class="icon-[iconamoon--box]"></span>
                        <span class="">Alat Tersisa</span>
                    </div>
                    <div class="h-[50px] w-[3px] bg-gray-200 dark:bg-gray-400"></div>
                    <div class="flex items-center gap-1 font-semibold text-xl">
                        <span>{{ $data_alat_tersisa }}</span>
                        <span>Item</span>
                    </div>
                </div>
                <div class="bg-gray-100 dark:bg-dark p-2 rounded-md mt-2">
                    <div class="text-sm">Stok Masuk per Bulan</div>
                    <div class="w-full mt-1" style="width: 100%;">
                        <canvas id="tersisastockin" :class="{ 'dark': isDark }"></canvas>
                    </div>
                </div>
                <div class="bg-gray-100 dark:bg-dark p-2 rounded-md mt-2">
                    <div class="text-sm">Ringkasan Stok</div>
                    <div class="w-full mt-1">
                        <canvas id="ringkasstoktersisa" :class="{ 'dark': isDark }"></canvas>
                    </div>
                    <div class="mt-1 text-sm flex flex-col">
                        <span class="text-green-500">- At-Stock : >5</span>
                        <span class="text-yellow-500">- Under-Stock : <5 </span>
                                <span class="text-red-500">- Out-of-Stock : 0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('chartjs/dist/chart.umd.js') }}" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="module" src="{{ asset('src/js/inventaris/dashboard-admin.js') }}"></script>
</x-app-layout>
