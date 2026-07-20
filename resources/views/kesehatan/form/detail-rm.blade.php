<x-app-layout>
    {{-- Main Content --}}
    <div class="px-2 sm:px-4 py-3 lg:py-5">
        <div class="flex flex-col md:flex-row justify-center items-center sm:items-start gap-2">
            <div class="p-4 bg-white dark:bg-darker sm:rounded-lg w-full md:w-auto">
                <div class="w-full md:max-w-xl">
                    @include('kesehatan.partials.profil-laporan')
                </div>
            </div>
            <div class="flex flex-col gap-2 w-full md:w-[930px]">
                <div class="p-4 bg-white dark:bg-darker sm:rounded-lg">
                    <div class="w-full">
                        @include('kesehatan.partials.info-personal-laporan')
                    </div>
                </div>
                {{-- @if ($user->cdmi == 1) --}}
                <div class="p-4 bg-white dark:bg-darker sm:rounded-lg">
                    <div class="w-full">
                        @include('kesehatan.partials.info-mahasiswa-laporan')
                    </div>
                </div>
                {{-- @endif --}}
                <div class="p-4 bg-white dark:bg-darker sm:rounded-lg">
                    <div class="w-full">
                        @include('kesehatan.partials.rpd-laporan')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="px-2 sm:px-4 py-3 lg:py-5 border-t dark:border-blue-800">
        <div class="flex justify-center flex-col md:flex-row gap-2 max-w-screen-xl mx-auto">
            {{-- MEMBUAT SURAT --}}
            <div class="p-4 bg-white dark:bg-darker sm:rounded-lg w-full">
                <div class="w-full">
                    <section>
                        <header>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                Data Rekam Medis
                            </h2>
                        </header>
                        <div class="mt-3 bg-gray-100 dark:bg-dark p-3 rounded">
                            <h2 class="text-md font-semibold underline">Pemeriksaan Pasien</h2>
                            <div class="grid gap-2 grid-cols-1 mt-2">
                                <div>
                                    <x-input-label for="keluhan" class="" :value="'Keluhan'" />
                                    <textarea name="keluhan" id="keluhan" cols="7" rows="7" disabled readonly
                                        class="block w-full mt-2 cursor-not-allowed items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">{{ $rm->keluhan }}</textarea>
                                </div>
                                <div>
                                    <x-input-label for="pemeriksaan" :value="'Pemeriksaan Fisik'" />
                                    <x-input-text id="pemeriksaan" name="pemeriksaan" type="text"
                                        class="mt-1 p-2 block w-full cursor-not-allowed" value="{{ $rm->pemeriksaan }}"
                                        disabled readonly />
                                </div>
                                <div>
                                    <x-input-label for="diagnosa" :value="'Diagnosa'" />
                                    <x-input-text id="diagnosa" name="diagnosa" type="text"
                                        class="mt-1 p-2 block w-full cursor-not-allowed" value="{{ $rm->diagnosa }}"
                                        disabled readonly />
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 bg-gray-100 dark:bg-dark p-3 rounded">
                            <h2 class="text-md font-semibold underline">Terapi</h2>
                            <ul class="list-decimal px-3 mt-1 text-sm text-gray-700 dark:text-gray-300">
                                @foreach ($tindakan['nama_obat'] as $index => $nama_obat)
                                    <li>{{ $nama_obat }} {{ $tindakan['jumlah_obat'][$index] }}x</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="p-3 mt-3 bg-gray-100 dark:bg-dark rounded">
                            <h2 class="text-md font-semibold underline">Intervensi</h2>
                            <ul class="list-decimal px-3 mt-1 text-sm text-gray-700 dark:text-gray-300">
                                @foreach ($tindakan['nama_alat'] as $index => $nama_alat)
                                    <li>{{ $nama_alat }} {{ $tindakan['jumlah_alat'][$index] }}x</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="mt-3 bg-gray-100 dark:bg-dark p-3 rounded">
                            <h2 class="text-md font-semibold underline">Perawatan Medis</h2>
                            <div class="grid gap-2 grid-cols-1 mt-2">
                                <div>
                                    <x-input-label for="rawatjalan" :value="'Rawat jalan'" />
                                    <x-input-text id="rawatjalan" name="rawatjalan" type="text"
                                        class="mt-1 p-2 block w-full cursor-not-allowed" value="{{ $rm->rawatjalan }}"
                                        disabled readonly />
                                </div>
                                <div>
                                    <x-input-label for="rujukan" :value="'Rujukan'" />
                                    <x-input-text id="rujukan" name="rujukan" type="text"
                                        class="mt-1 p-2 block w-full cursor-not-allowed"
                                        value="{{ $rm->rs_name_rujukan }}" disabled readonly />
                                </div>
                                <div>
                                    <x-input-label for="rawatinap" :value="'Rawat Inap'" />
                                    <x-input-text id="rawatinap" name="rawatinap" type="text"
                                        class="mt-1 p-2 block w-full cursor-not-allowed"
                                        value="{{ $rm->rs_name_rawatinap }}" disabled readonly />
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
    <div class="px-2 sm:px-4 py-3 lg:py-5 border-t dark:border-blue-800">
        <div id="accordion-flush" data-accordion="collapse" data-active-classes="bg-transparent"
            data-inactive-classes="text-gray-500 dark:text-gray-400">
            <h2 id="accordion-flush-heading-2">
                <button type="button"
                    class="flex items-center justify-between w-full rounded p-3 font-medium rtl:text-right text-gray-500 border-b border-gray-200 dark:border-gray-700 dark:text-gray-400 gap-3"
                    data-accordion-target="#accordion-flush-body-2" aria-expanded="false"
                    aria-controls="accordion-flush-body-2">
                    <span>Membuat Surat Laporan?</span>
                    <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5 5 1 1 5" />
                    </svg>
                </button>
            </h2>
            <div id="accordion-flush-body-2" class="hidden" aria-labelledby="accordion-flush-heading-2">
                <div class="p-4 bg-white dark:bg-darker sm:rounded-lg w-full mt-2">
                    <div class="w-full">
                        <section>
                            <header>
                                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    Tipe Surat Laporan
                                </h2>
                            </header>
                            <form action="{{ route('kesehatan.riwayat-pasien.tipe-surat', $rm->id) }}" method="post">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                                    <div
                                        class="flex items-center ps-4 border border-gray-200 rounded dark:border-gray-700 mt-3">
                                        <input id="tipe-1" type="radio" value="surat_keterangan_obat"
                                            name="tipe" required
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <label for="tipe-1"
                                            class="w-full py-4 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Surat
                                            Keterangan Obat</label>
                                    </div>
                                    <div
                                        class="flex items-center ps-4 border border-gray-200 rounded dark:border-gray-700 mt-3">
                                        <input id="tipe-2" type="radio" value="surat_keterangan_sakit"
                                            name="tipe" required
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <label for="tipe-2"
                                            class="w-full py-4 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Surat
                                            Keterangan Sakit</label>
                                    </div>
                                    <div
                                        class="flex items-center ps-4 border border-gray-200 rounded dark:border-gray-700 mt-3">
                                        <input id="tipe-3" type="radio" value="surat_keterangan_sehat"
                                            name="tipe" required
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <label for="tipe-3"
                                            class="w-full py-4 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Surat
                                            Keterangan Sehat</label>
                                    </div>
                                    <div
                                        class="flex items-center ps-4 border border-gray-200 rounded dark:border-gray-700 mt-3">
                                        <input id="tipe-4" type="radio" value="surat_rujukan" name="tipe"
                                            required
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <label for="tipe-4"
                                            class="w-full py-4 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Surat
                                            Rujukan</label>
                                    </div>
                                </div>
                                <x-button class="py-2 px-4 mt-2" type="submit">{{ __('Buat') }}</x-button>
                            </form>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="px-2 sm:px-4 text-right py-3">
        <div class="max-w-screen-xl mx-auto flex justify-end">
            <a href="/medical/form/riwayat-pasien/{{ $user->id }}">
                <x-button class="py-2 px-4" type="button">{{ __('Kembali') }}</x-button>
            </a>
        </div>
    </div>
</x-app-layout>
