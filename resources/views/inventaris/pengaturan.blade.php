<x-app-layout>
    {{-- Modals --}}
    @include('inventaris.partials.modals.tambah-kategori')
    {{-- Content Header --}}
    <div class="flex items-center justify-between px-2 sm:px-4 py-3 lg:py-5 border-b dark:border-blue-800">
        <h1 class="text-2xl font-semibold">Pengaturan Inventaris</h1>
    </div>
    {{-- Main Content --}}
    <div x-data="{ activeTab: 'kategori' }" class="px-2 sm:px-4 py-3 lg:py-5">
        <div class="md:flex">
            <ul
                class="flex-column space-y space-y-4 text-sm font-medium text-gray-500 dark:text-gray-400 md:me-4 mb-4 md:mb-0">
                <li>
                    <button @click="activeTab = 'kategori'"
                        :class="{ 'text-white bg-blue-700 dark:bg-blue-600': activeTab === 'kategori', 'text-gray-500 dark:text-gray-400 hover:text-gray-900 bg-white hover:bg-gray-200 dark:bg-darker dark:hover:bg-darkerhover dark:hover:text-white': activeTab !== 'kategori' }"
                        class="inline-flex items-center px-4 py-3 w-full text-center whitespace-nowrap transition-colors duration-200 rounded-lg text-sm font-medium">
                        <span class="icon-[tabler--category] w-5 h-5 me-1 "></span>
                        Kategori
                    </button>
                </li>
                <li>
                <li>
                    <button @click="activeTab = 'export'"
                        :class="{ 'text-white bg-blue-700 dark:bg-blue-600': activeTab === 'export', 'text-gray-500 dark:text-gray-400 hover:text-gray-900 bg-white hover:bg-gray-200 dark:bg-darker dark:hover:bg-darkerhover dark:hover:text-white': activeTab !== 'export' }"
                        class="inline-flex items-center px-4 py-3 w-full text-center whitespace-nowrap transition-colors duration-200 rounded-lg text-sm font-medium">
                        <span class="icon-[solar--export-linear] w-5 h-5 me-1"></span>
                        Export
                    </button>
                </li>
                <li>
                    <a
                        class="inline-flex items-center px-4 py-3 text-gray-400 rounded-lg cursor-not-allowed bg-white w-full dark:bg-darker dark:text-gray-500">
                        <span class="icon-[fe--disabled] w-5 h-5 me-1 text-gray-400 dark:text-gray-500"></span>
                        -
                    </a>
                </li>
                <li>
                    <a
                        class="inline-flex items-center px-4 py-3 text-gray-400 rounded-lg cursor-not-allowed bg-white w-full dark:bg-darker dark:text-gray-500">
                        <span class="icon-[fe--disabled] w-5 h-5 me-1 text-gray-400 dark:text-gray-500"></span>
                        -
                    </a>
                </li>
            </ul>
            @include('inventaris.partials.kategori-tab')
            @include('inventaris.partials.export-tab')
        </div>
    </div>
    <script src="{{ asset('src/js/inventaris/pengaturan.js') }}"></script>
</x-app-layout>
