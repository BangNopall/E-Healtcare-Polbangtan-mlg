<x-modal name="tambah">
    <div class="relative p-4 sm:p-5 w-full max-w-2xl h-full md:h-auto">
        <!-- Modal content -->
        <div class="relative">
            <!-- Modal header -->
            <div class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Tambah Sensuh
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
            <form action="{{ route('konseling.create-senso') }}" method="post" id="additemform">
                @csrf
                <div class="mb-4">
                    <div>
                        <x-input-label for="name" class="mb-2">Nama Mahasiswa</x-input-label>
                        <div x-data="selectConfigs()" x-init="fetchOptions()"
                            class="flex flex-col items-center relative">
                            <x-input-text @click.away="close()" x-model="filter"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                @mousedown="open()" @keydown.enter.stop.prevent="selectOption()"
                                @keydown.arrow-up.prevent="focusPrevOption()"
                                @keydown.arrow-down.prevent="focusNextOption()" type="search" id="name"
                                name="name" class="p-2" placeholder="Nama Mahasiswa" />
                            <div x-show="isOpen()"
                                class="shadow-lg bg-white dark:bg-darker w-full mt-2 rounded max-h-[150px] overflow-y-auto">
                                <div class="flex flex-col w-full">
                                    <template x-for="(option, index) in filteredOptions()" :key="index">
                                        <div @click="onOptionClick(index)" :class="classOption(option.id, index)"
                                            :aria-selected="focusedOptionIndex === index">
                                            <div
                                                class="w-full items-center p-2 pl-2 border-transparent border-l-2 relative hover:border-teal-100 dark:hover:border-darkerhover">
                                                <div class="w-full items-center flex">
                                                    <span x-text="option.name"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <input type="text" id="user_id" class="hidden" name="user_id">
                    </div>
                </div>
                <x-button type="submit" class="inline-flex items-center px-4 py-2">
                    <svg class="mr-1 -ml-1 w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                    Tambah
                </x-button>
            </form>
        </div>
    </div>
</x-modal>
