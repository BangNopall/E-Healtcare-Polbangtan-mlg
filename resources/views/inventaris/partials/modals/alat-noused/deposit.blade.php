<x-modal name="deposit-alat">
    <div class="relative p-4 sm:p-5 w-full max-w-2xl h-full md:h-auto">
        <!-- Modal content -->
        <div class="relative">
            <!-- Modal header -->
            <div class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Deposit {{ $data->kode_alat }}
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
            <form action="{{ route('inventaris.consumable.deposit', $data->id) }}" method="post" id="additemform">
                @csrf
                {{-- <input type="hidden" name="obat_id" value="{{ $data->id }}"> --}}
                <div class="grid gap-4 mb-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="user" class="mb-2">Pengguna</x-input-label>
                        <div x-data="selectConfigUsers()" x-init="fetchOptions()"
                            class="flex flex-col items-center relative">
                            <x-input-text @click.away="close()" x-model="filter"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                @mousedown="open()" @keydown.enter.stop.prevent="selectOption()"
                                @keydown.arrow-up.prevent="focusPrevOption()"
                                @keydown.arrow-down.prevent="focusNextOption()" type="user" id="user"
                                name="user" class="p-2.5" placeholder="Pengguna"/>
                            <div x-show="isOpen()"
                                class="absolute shadow-lg bg-white dark:bg-darker top-100 z-40 w-full left-0 top-11 rounded max-h-[150px] overflow-y-auto">
                                <div class="flex flex-col w-full">
                                    <template x-for="(option, index) in filteredOptions()" :key="index">
                                        <div @click="onOptionClick(index)" :class="classOption(option.id, index)"
                                            :aria-selected="focusedOptionIndex === index">
                                            <div
                                                class="w-full items-center p-2 pl-2 border-transparent border-l-2 relative hover:border-teal-100 dark:hover:border-darkerhover">
                                                <div class="w-full items-center flex text-sm">
                                                    <span x-text="option.name"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <input type="text" class="hidden" id="user_id" name="user_id">
                    </div>
                    <div>
                        <x-input-label for="kuantitas" class="mb-2">Kuantitas</x-input-label>
                        <x-input-text type="number" name="Qty" id="kuantitas" class="p-2.5" placeholder="10"
                            required="" value="{{ old('Qty') }}" />
                    </div>
                    <div>
                        <x-input-label for="tanggal" class="mb-2">Tanggal</x-input-label>
                        <x-input-text type="date" name="date" id="tanggal" class="p-2.5"
                            placeholder="{{ date('Y-m-d') }}" required="" value="{{ date('Y-m-d') }}" />
                    </div>
                    <div>
                        <x-input-label for="waktu" class="mb-2">Waktu</x-input-label>
                        <x-input-text type="time" name="time" id="waktu" class="p-2.5"
                            placeholder="{{ date('H:i') }}" required="" value="{{ date('H:i') }}" />
                    </div>
                </div>
                <x-button type="submit" class="inline-flex items-center px-4 py-2">
                    <svg class="mr-1 -ml-1 w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                    Deposit
                </x-button>
            </form>
        </div>
    </div>
    <div class="hidden" id="get_user_url"></div>
    <script>
        const url = "{{ route('api.get_users') }}"
        // console.log(url);
        const element = document.getElementById('get_user_url');
        element.innerHTML = url;
    </script>
</x-modal>
