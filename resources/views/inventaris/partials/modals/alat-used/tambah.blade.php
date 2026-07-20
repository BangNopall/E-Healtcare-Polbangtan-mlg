<x-modal name="tambah-item">
    <div class="relative p-4 sm:p-5 w-full max-w-2xl h-full md:h-auto">
        <!-- Modal content -->
        <div class="relative">
            <!-- Modal header -->
            <div class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Tambah Item
                </h3>
                <button type="button" x-on:click="$dispatch('close')"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-dark dark:hover:text-white">
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <form action="{{ route('inventaris.alat.store') }}" method="post" id="additemform">
                @csrf
                <div class="grid gap-4 mb-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="nama_alat" class="mb-2">Nama</x-input-label>
                        <x-input-text type="text" name="nama_alat" id="nama_alat" class="p-2.5"
                            placeholder="Nama Item" required="" value="{{ old('nama_alat') }}" />
                    </div>
                    <div>
                        <x-input-label for="satuan_alat" class="mb-2">Kategori</x-input-label>
                        <div x-data="selectConfigs()" x-init="fetchOptions()"
                            class="flex flex-col items-center relative">
                            <x-input-text @click.away="close()" x-model="filter"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                @mousedown="open()" @keydown.enter.stop.prevent="selectOption()"
                                @keydown.arrow-up.prevent="focusPrevOption()"
                                @keydown.arrow-down.prevent="focusNextOption()" type="search" id="satuan_alat"
                                name="nama_kategori" class="p-2" placeholder="Kategori" />
                            <div x-show="isOpen()"
                                class="absolute shadow-lg bg-white dark:bg-darker top-100 z-40 w-full left-0 top-11 rounded max-h-[150px] overflow-y-auto">
                                <div class="flex flex-col w-full">
                                    <template x-for="(option, index) in filteredOptions()" :key="index">
                                        <div @click="onOptionClick(index)" :class="classOption(option.id, index)"
                                            :aria-selected="focusedOptionIndex === index">
                                            <div
                                                class="w-full items-center p-2 pl-2 border-transparent border-l-2 relative hover:border-teal-100 dark:hover:border-darkerhover">
                                                <div class="w-full items-center flex">
                                                    <span x-text="option.nama_kategori"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <input type="text" id="kategori_id" class="hidden" name="kategori_id">
                    </div>
                    <div>
                        <x-input-label class="mb-2" for="foto">
                            Foto Alat (Opsional)
                        </x-input-label>
                        <input
                            class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-darker dark:border-gray-600 dark:placeholder-blue-600"
                            aria-describedby="Upload foto" id="foto" type="file" accept=".jpg,.jpeg,.png">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="foto_file">
                            PNG, JPG, JPEG</p>
                    </div>
                </div>
                <x-button type="submit" class="inline-flex items-center px-4 py-2">
                    <svg class="mr-1 -ml-1 w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                    Tambah Item
                </x-button>
            </form>
        </div>
    </div>
    <div id="url_kategori_alat" class="hidden"></div>
    <script>
        const url = '{{ route('api.getKategoriAlat') }}';
        const element = document.getElementById('url_kategori_alat');
        element.textContent = url;
    </script>
</x-modal>
