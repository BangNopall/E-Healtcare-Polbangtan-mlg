<div id="default-modal" tabindex="-1" aria-hidden="false"
    class="overflow-y-auto p-2 overflow-x-hidden flex fixed top-0 bg-black/50 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 sm:p-5 w-full max-w-xl h-auto bg-white dark:bg-darker rounded-md shadow-md">
        <!-- Modal content -->
        <div class="relative">
            <!-- Modal header -->
            <div class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Perbaruhi Data Item
                </h3>
            </div>
            <!-- Modal body -->
            <form action="{{ route('inventaris.consumable.update', $data->id) }}" method="post" id="additemform">
                @csrf
                <div class="space-y-2">
                    <div>
                        <x-input-label for="satuan_obat" class="mb-2">Kategori</x-input-label>
                        <div x-data="selectConfigKategoris()" x-init="fetchOptions()"
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
                        <x-input-label for="nama_obat" class="mb-2">Pengguna</x-input-label>
                        <x-input-text type="text" name="user" id="user" class="p-2.5 cursor-not-allowed"
                            value="{{ auth()->user()->name }}" disabled />
                    </div>
                </div>
                <x-button type="submit" class="inline-flex items-center mt-5 px-4 py-2">
                    Perbaruhi
                </x-button>
            </form>
        </div>
    </div>
</div>
<div class="hidden" id="url_kategori_notkategori">{{ route('api.getKategoriConsumables') }}</div>
