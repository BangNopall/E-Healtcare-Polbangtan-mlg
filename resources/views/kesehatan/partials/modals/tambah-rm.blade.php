<x-modal name="tambah-rm-baru">
    <div class="relative p-4 sm:p-5 w-full max-w-2xl h-full md:h-auto">
        <!-- Modal content -->
        <div class="relative">
            <!-- Modal header -->
            <div class="flex justify-between items-center pb-4 mb-2 rounded-t border-b dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Tambah Rekam Medis
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
            <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="default-tab"
                    data-tabs-toggle="#default-tab-content" role="tablist">
                    <li class="me-2" role="rm">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg" id="pemeriksaan-tab"
                            data-tabs-target="#pemeriksaan" type="button" role="tab" aria-controls="pemeriksaan"
                            aria-selected="false">Pemeriksaan</button>
                    </li>
                    <li class="me-2" role="rm">
                        <button
                            class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                            id="terapi-tab" data-tabs-target="#terapi" type="button" role="tab"
                            aria-controls="terapi" aria-selected="false">Terapi</button>
                    </li>
                    <li class="me-2" role="rm">
                        <button
                            class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                            id="intervensi-tab" data-tabs-target="#intervensi" type="button" role="tab"
                            aria-controls="intervensi" aria-selected="false">Intervensi</button>
                    </li>
                    <li role="rm">
                        <button
                            class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                            id="perawatan-tab" data-tabs-target="#perawatan" type="button" role="tab"
                            aria-controls="perawatan" aria-selected="false">Perawatan Medis</button>
                    </li>
                </ul>
            </div>
            <form action="{{ route('kesehatan.riwayat-pasien.store', $user->id) }}" method="post">
                @csrf
                <div id="default-tab-content">
                    <div class="hidden p-3 rounded-lg bg-gray-100 dark:bg-dark" id="pemeriksaan" role="tabpanel"
                        aria-labelledby="pemeriksaan-tab">
                        <div class="grid gap-2 grid-cols-1">
                            <div>
                                <x-input-label for="keluhan" class="mb-2">Keluhan</x-input-label>
                                <textarea name="keluhan" id="cek_fisik" cols="3" rows="3" placeholder="..."
                                    class="block w-full mt-2 items-center text-sm text-gray-900 border p-2 border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">{{ old('keluhan') }}</textarea>
                            </div>
                            <div>
                                <x-input-label for="pemeriksaan" class="mb-2">Pemeriksaan Fisik</x-input-label>
                                <x-input-text type="text" name="pemeriksaan" class="p-2" id="pemeriksaan"
                                    placeholder="..." value="{{ old('pemeriksaan') }}" />
                            </div>
                            <div>
                                <x-input-label for="diagnosa" class="mb-2">Diagnosa</x-input-label>
                                <x-input-text type="text" name="diagnosa" class="p-2" id="diagnosa"
                                    placeholder="..." value="{{ old('diagnosa') }}" />
                            </div>
                        </div>
                    </div>
                    <div class="hidden p-3 rounded-lg bg-gray-100 dark:bg-dark" id="terapi" role="tabpanel"
                        aria-labelledby="terapi-tab">
                        <div class="">
                            <div class="grid gap-2 grid-cols-1 md:grid-cols-2" id="select-obat">
                                <div>
                                    <x-input-label for="obat_id" class="mb-2">Pilih
                                        Obat</x-input-label>
                                    <div x-data="selectConfigs()" x-init="fetchOptions()"
                                        class="flex flex-col items-center relative">
                                        <x-input-text @click.away="close()" x-model="filter"
                                            x-transition:leave="transition ease-in duration-100"
                                            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                            @mousedown="open()" @keydown.enter.stop.prevent="selectOption()"
                                            @keydown.arrow-up.prevent="focusPrevOption()"
                                            @keydown.arrow-down.prevent="focusNextOption()" type="search"
                                            id="obat_id" class="p-2" placeholder="Cari..." />
                                        <div x-show="isOpen()"
                                            class="absolute shadow-lg bg-white dark:bg-darker top-100 z-40 w-full left-0 top-11 rounded max-h-[180px] overflow-y-auto">
                                            <div class="flex flex-col w-full">
                                                <template x-for="(option, index) in filteredOptions()"
                                                    :key="index">
                                                    <div @click="onOptionClick(index)"
                                                        :class="classOption(option.id, index)"
                                                        :aria-selected="focusedOptionIndex === index">
                                                        <div
                                                            class="w-full items-center p-2 pl-2 border-transparent border-l-2 relative hover:border-teal-100 dark:hover:border-darkerhover">
                                                            <div class="w-full items-center flex">
                                                                <span x-text="option.nama_obat"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <x-input-label for="jumlah_obat" :value="'jumlah_obat'" />
                                    <x-input-text id="jumlah_obat" type="number" class="mt-2 block w-full"
                                        :value="''" placeholder="10" />
                                    <x-input-error class="mt-1" :messages="$errors->get('jumlah_obat')" />
                                </div>
                            </div>
                            <x-button class="py-2 px-3 text-sm mt-2" type="button" id="tambah-obat"
                                onclick="tambahObat()">{{ __('Tambah') }}</x-button>
                            <p class="text-orange-400 text-sm pt-1">Pastikan stok obat tersedia!</p>
                        </div>
                        <div
                            class="p-2 border rounded mt-2 bg-gray-100 dark:bg-dark border-gray-200 dark:border-gray-700">
                            <h2 class="text-md font-medium">Permintaan Obat</h2>
                            <ul id="daftar-permintaan-obat"
                                class="list-decimal px-3 text-sm text-gray-700 dark:text-gray-300">
                                <!-- List item will be dynamically added here -->
                                @if (old('obat_id'))
                                    @foreach (old('obat_id') as $key => $obat_id)
                                        <li class="py-1">
                                            <input type="hidden" name="obat_id[]" value="{{ $obat_id }}">
                                            <input type="hidden" name="nama_obat[]"
                                                value="{{ old('nama_obat')[$key] }}">
                                            <input type="hidden" name="jumlah_obat[]"
                                                value="{{ old('jumlah_obat')[$key] }}">
                                            <span>{{ old('nama_obat')[$key] }} x {{ old('jumlah_obat')[$key] }}</span>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="hidden p-3 rounded-lg bg-gray-100 dark:bg-dark" id="intervensi" role="tabpanel"
                        aria-labelledby="intervensi-tab">
                        <div class="">
                            <div class="grid gap-2 grid-cols-1 md:grid-cols-2" id="select-obat">
                                <div>
                                    <x-input-label for="alat_id" class="mb-2">Pilih Alat</x-input-label>
                                    <div x-data="selectConfigsAlat()" x-init="fetchOptionsAlat()"
                                        class="flex flex-col items-center relative">
                                        <x-input-text @click.away="closeAlat()" x-model="filterAlat"
                                            x-transition:leave="transition ease-in duration-100"
                                            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                            @mousedown="open()" @keydown.enter.stop.prevent="selectOptionAlat()"
                                            @keydown.arrow-up.prevent="focusPrevOptionAlat()"
                                            @keydown.arrow-down.prevent="focusNextOptionAlat()" type="search"
                                            id="alat_id" class="p-2" placeholder="Cari..." />
                                        <input type="hidden" id="identity-alat" value="">
                                        <div x-show="isOpenAlat()"
                                            class="absolute shadow-lg bg-white dark:bg-darker top-100 z-40 w-full left-0 top-11 rounded max-h-[180px] overflow-y-auto">
                                            <div class="flex flex-col w-full">
                                                <template x-for="(option, index) in filterAlatOptions()"
                                                    :key="index">
                                                    <div @click="onOptionClickAlat(index)"
                                                        :class="classOptionAlat(option.id, index)"
                                                        :aria-selected="focusedOptionIndexAlat === index">
                                                        <div
                                                            class="w-full items-center p-2 pl-2 border-transparent border-l-2 relative hover:border-teal-100 dark:hover:border-darkerhover">
                                                            <div class="w-full items-center flex">
                                                                <span x-text="option.nama_alat"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <x-input-label for="jumlah_alat" :value="'jumlah_alat'" />
                                    <x-input-text id="jumlah_alat" type="number" class="mt-2 block w-full"
                                        :value="''" placeholder="10" />
                                    <x-input-error class="mt-1" :messages="$errors->get('jumlah_alat')" />
                                </div>
                            </div>
                            <x-button class="py-2 px-3 text-sm mt-2" type="button" id="tambah-alat"
                                onclick="tambahAlat()">{{ __('Tambah') }}</x-button>
                            <p class="text-orange-400 text-sm pt-1">Pastikan stok alat tersedia!</p>
                        </div>
                        <div
                            class="p-2 border rounded mt-2 bg-gray-100 dark:bg-dark border-gray-200 dark:border-gray-700">
                            <h2 class="text-md font-medium">Permintaan Alat</h2>
                            <ul id="daftar-permintaan-alat"
                                class="list-decimal px-3 text-sm text-gray-700 dark:text-gray-300">
                                <!-- List item will be dynamically added here -->
                                @if (old('alat_id'))
                                    @foreach (old('alat_id') as $key => $alat_id)
                                        <li class="py-1">
                                            <input type="hidden" name="alat_id[]" value="{{ $alat_id }}">
                                            <input type="hidden" name="nama_alat[]"
                                                value="{{ old('nama_alat')[$key] }}">
                                            <input type="hidden" name="jumlah_alat[]"
                                                value="{{ old('jumlah_alat')[$key] }}">
                                            <input type="hidden" name="identity_alat[]"
                                                value="{{ old('identity_alat')[$key] }}">
                                            <span>{{ old('nama_alat')[$key] }} x {{ old('jumlah_alat')[$key] }}</span>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="hidden p-3 rounded-lg bg-gray-100 dark:bg-dark" id="perawatan" role="tabpanel"
                        aria-labelledby="rujukan-tab">
                        <div class="grid gap-2 grid-cols-1">
                            <div>
                                <x-input-label for="rawatjalan" class="mb-2">Rawat Jalan</x-input-label>
                                <x-input-text type="text" name="rawatjalan" class="p-2" id="rawatjalan"
                                    placeholder="..." value="{{ old('rawatjalan') }}" />
                            </div>
                            <div>
                                <x-input-label for="rs_name_rujukan" class="mb-2">Rujukan</x-input-label>
                                <div x-data="selectConfigsRS()" x-init="fetchOptionsRS()"
                                    class="flex flex-col items-center relative">
                                    <x-input-text @click.away="closeRS()" x-model="filterRS"
                                        x-transition:leave="transition ease-in duration-100"
                                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                        @mousedown="open()" @keydown.enter.stop.prevent="selectOptionRS()"
                                        @keydown.arrow-up.prevent="focusPrevOptionRS()"
                                        @keydown.arrow-down.prevent="focusNextOptionRS()" type="search"
                                        id="rs_name_rujukan" name="rs_name_rujukan" class="p-2"
                                        placeholder="Cari RS/Puskesmas..." value="{{ old('rs_name_rujukan') }}"/>
                                    <div x-show="isOpenRS()"
                                        class="absolute shadow-lg bg-white dark:bg-darker top-100 z-40 w-full left-0 top-11 rounded max-h-[120px] overflow-y-auto">
                                        <div class="flex flex-col w-full">
                                            <template x-for="(option, index) in filterRSOptions()"
                                                :key="index">
                                                <div @click="onOptionClickRS(index)"
                                                    :class="classOptionRS(option.id, index)"
                                                    :aria-selected="focusedOptionIndexRS === index">
                                                    <div
                                                        class="w-full items-center p-2 pl-2 border-transparent border-l-2 relative hover:border-teal-100 dark:hover:border-darkerhover">
                                                        <div class="w-full items-center flex">
                                                            <span x-text="option.nama_rs"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <x-input-label for="rs_name_rujukan" class="mb-2">Rawat Inap</x-input-label>
                                <div x-data="selectConfigsRS1()" x-init="fetchOptionsRS1()"
                                    class="flex flex-col items-center relative">
                                    <x-input-text @click.away="closeRS1()" x-model="filterRS1"
                                        x-transition:leave="transition ease-in duration-100"
                                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                        @mousedown="open()" @keydown.enter.stop.prevent="selectOptionRS1()"
                                        @keydown.arrow-up.prevent="focusPrevOptionRS1()"
                                        @keydown.arrow-down.prevent="focusNextOptionRS1()" type="search"
                                        id="rs_name_rawatinap" name="rs_name_rawatinap" class="p-2" value="{{ old('rs_name_rawatinap') }}"
                                        placeholder="Cari RS1/Puskesmas..."/>
                                    <div x-show="isOpenRS1()"
                                        class="absolute shadow-lg bg-white dark:bg-darker top-100 z-40 w-full left-0 top-11 rounded max-h-[120px] overflow-y-auto">
                                        <div class="flex flex-col w-full">
                                            <template x-for="(option, index) in filterRS1Options()"
                                                :key="index">
                                                <div @click="onOptionClickRS1(index)"
                                                    :class="classOptionRS1(option.id, index)"
                                                    :aria-selected="focusedOptionIndexRS1 === index">
                                                    <div
                                                        class="w-full items-center p-2 pl-2 border-transparent border-l-2 relative hover:border-teal-100 dark:hover:border-darkerhover">
                                                        <div class="w-full items-center flex">
                                                            <span x-text="option.nama_rs"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <x-button class="py-2 px-3 text-sm mt-2" type="submit">Simpan</x-button>
            </form>
        </div>
    </div>
    <div id="url_obat" class="hidden"></div>
    <div id="url_alat" class="hidden"></div>
    <div id="url_rs" class="hidden"></div>
    <script>
        const url = '{{ route('api.getDaftarObat') }}';
        const element = document.getElementById('url_obat');
        element.textContent = url;

        const url_alat = '{{ route('api.getDaftarAlat') }}';
        const element_alat = document.getElementById('url_alat');
        element_alat.textContent = url_alat;

        const url_rs = '{{ route('api.daftarRS') }}';
        const element_rs = document.getElementById('url_rs');
        element_rs.textContent = url_rs;
    </script>
    <script src="{{ asset('src/js/kesehatan/rm.js') }}"></script>
</x-modal>
