<x-app-layout>
    {{-- Content Header --}}
    <div class="px-2 sm:px-4 py-3 lg:py-5 border-b dark:border-blue-800">
        <h1 class="text-2xl font-semibold">Riwayat Konsultasi</h1>
    </div>
    {{-- Main Content --}}
    <div class="px-2 sm:px-4 py-3 lg:py-5 border-t dark:border-blue-800">
        <div class="flex justify-center flex-col md:flex-row gap-2 max-w-screen-xl mx-auto">
            <div class="p-4 bg-white dark:bg-darker sm:rounded-lg w-full">
                <div class="w-full">
                    <section>
                        <header>
                            <div class="flex justify-between">
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                    Konsultasi Konseling
                                </h2>
                            </div>
                        </header>
                        <div class="mt-3 bg-gray-100 dark:bg-dark p-3 rounded">
                            <div class="grid gap-2 md:grid-cols-2">
                                <div>
                                    <x-input-label for="tanggal" :value="'Tanggal'" />
                                    <x-input-text id="tanggal" name="tanggal" type="date"
                                        class="mt-2 block w-full cursor-not-allowed" disabled readonly
                                        value="{{ $dataPsikolog->tanggal }}" />
                                </div>
                                <div>
                                    <x-input-label for="metode" :value="'Metode Psikologi'" />
                                    <x-input-text id="metode" name="metode" type="text"
                                        class="mt-2 block w-full cursor-not-allowed" disabled readonly
                                        value="{{ $dataPsikolog->metode_psikologi }}" />
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 bg-gray-100 dark:bg-dark p-3 rounded">
                            <div class="grid gap-2 md:grid-cols-2">
                                <div>
                                    <x-input-label for="keluhan" :value="'Keluhan'" />
                                    <textarea name="keluhan" id="keluhan" cols="5" rows="5" disabled readonly
                                        class="block w-full mt-2 cursor-not-allowed items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">{{ $dataPsikolog->keluhan }}</textarea>
                                </div>
                                <div>
                                    <x-input-label for="diagnosa" :value="'Diagnosa'" />
                                    <textarea name="diagnosa" id="diagnosa" cols="5" rows="5" disabled readonly
                                        class="block w-full mt-2 cursor-not-allowed items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">{{ $dataPsikolog->diagnosa }}</textarea>
                                </div>
                                <div>
                                    <x-input-label for="prognosis" :value="'Prognosis'" />
                                    <textarea name="prognosis" id="prognosis" cols="5" rows="5" disabled readonly
                                        class="block w-full mt-2 cursor-not-allowed items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">{{ $dataPsikolog->prognosis }}</textarea>
                                </div>
                                <div>
                                    <x-input-label for="intervensi" :value="'Intervensi'" />
                                    <textarea name="intervensi" id="intervensi" cols="5" rows="5" disabled readonly
                                        class="block w-full mt-2 cursor-not-allowed items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">{{ $dataPsikolog->intervensi }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 bg-gray-100 dark:bg-dark p-3 rounded">
                            <div class="grid gap-2 md:grid-cols-2">
                                <div>
                                    <x-input-label for="saran" :value="'Saran'" />
                                    <textarea name="saran" id="cek_fisik" cols="5" rows="5" disabled readonly
                                        class="block w-full mt-2 cursor-not-allowed items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">{{ $dataPsikolog->saran }}</textarea>
                                </div>
                                <div>
                                    <x-input-label for="rencana_tindak_lanjut" :value="'Rencana Tindak Lanjut'" />
                                    <textarea name="rencana_tindak_lanjut" id="rencana_tindak_lanjut" cols="5" rows="5" disabled readonly
                                        class="block w-full mt-2 cursor-not-allowed items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">{{ $dataPsikolog->rencana_tindak_lanjut }}</textarea>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
