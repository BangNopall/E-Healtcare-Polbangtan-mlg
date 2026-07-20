<x-app-layout>
    {{-- Content Header --}}
    <div class="flex justify-center px-2 sm:px-4 py-3 lg:py-5 border-b dark:border-blue-800">
        @include('konseling.partials.stepper')
    </div>
    {{-- Main Content --}}
    <div class="px-2 sm:px-4 py-3 lg:py-5">
        <div id="accordion-flush" data-accordion="collapse" data-active-classes="bg-transparent"
            data-inactive-classes="text-gray-500 dark:text-gray-400">
            <h2 id="accordion-flush-heading-2">
                <button type="button"
                    class="flex items-center justify-between w-full rounded p-5 font-medium rtl:text-right text-gray-500 border-b border-gray-200 dark:border-gray-700 dark:text-gray-400 gap-3"
                    data-accordion-target="#accordion-flush-body-2" aria-expanded="false"
                    aria-controls="accordion-flush-body-2">
                    <span>Data Mahasiswa</span>
                    <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5 5 1 1 5" />
                    </svg>
                </button>
            </h2>
            <div id="accordion-flush-body-2" class="hidden" aria-labelledby="accordion-flush-heading-2">
                <div class="flex flex-col md:flex-row justify-center items-center sm:items-start gap-2 mt-2">
                    <div class="p-4 bg-white dark:bg-darker sm:rounded-lg w-full md:w-auto">
                        <div class="w-full md:max-w-xl">
                            @include('konseling.partials.profil-konseling')
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 w-full md:w-[930px]">
                        <div class="p-4 bg-white dark:bg-darker sm:rounded-lg">
                            <div class="w-full">
                                @include('konseling.partials.info-mahasiswa-konseling')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="px-2 sm:px-4 py-3 lg:py-5 border-t dark:border-blue-800">
        <div class="flex justify-center flex-col md:flex-row gap-2 max-w-screen-xl mx-auto">
            <div class="p-4 bg-white dark:bg-darker sm:rounded-lg w-full">
                <div class="w-full">
                    <section>
                        <header>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                Konsultasi Konseling
                            </h2>
                        </header>
                        <form action="{{ route('konseling.storeKonsultasi', $user->id) }}" method="POST">
                            @csrf
                            {{-- @method('PATCH') --}}
                            <div class="mt-3 bg-gray-100 dark:bg-dark p-3 rounded">
                                <div class="grid gap-2 md:grid-cols-2">
                                    <div>
                                        <x-input-label for="tanggal" :value="'Tanggal'" />
                                        <x-input-text id="tanggal" name="tanggal" type="date"
                                            class="mt-2 block w-full" required value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"/>
                                        <x-input-error class="mt-1" :messages="$errors->get('tanggal')" />
                                    </div>
                                    <div>
                                        <x-input-label for="metode_psikologi" :value="'Metode Psikologi'" />
                                        <x-input-text id="metode_psikologi" name="metode_psikologi" type="text"
                                            class="mt-2 block w-full" required placeholder="Metode" />
                                        <x-input-error class="mt-1" :messages="$errors->get('metode_psikologi')" />
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 bg-gray-100 dark:bg-dark p-3 rounded">
                                <div class="grid gap-2 md:grid-cols-2">
                                    <div>
                                        <x-input-label for="keluhan" :value="'Keluhan'" />
                                        <textarea name="keluhan" id="keluhan" cols="5" rows="5" required
                                            class="block w-full mt-2 items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"></textarea>
                                        <x-input-error class="mt-1" :messages="$errors->get('keluhan')" />
                                    </div>
                                    <div>
                                        <x-input-label for="diagnosa" :value="'Diagnosa'" />
                                        <textarea name="diagnosa" id="diagnosa" cols="5" rows="5" required
                                            class="block w-full mt-2 items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"></textarea>
                                        <x-input-error class="mt-1" :messages="$errors->get('diagnosa')" />
                                    </div>
                                    <div>
                                        <x-input-label for="prognosis" :value="'Prognosis'" />
                                        <textarea name="prognosis" id="prognosis" cols="5" rows="5" required
                                            class="block w-full mt-2 items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"></textarea>
                                        <x-input-error class="mt-1" :messages="$errors->get('prognosis')" />
                                    </div>
                                    <div>
                                        <x-input-label for="intervensi" :value="'Intervensi'" />
                                        <textarea name="intervensi" id="intervensi" cols="5" rows="5" required
                                            class="block w-full mt-2 items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"></textarea>
                                        <x-input-error class="mt-1" :messages="$errors->get('intervensi')" />
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 bg-gray-100 dark:bg-dark p-3 rounded">
                                <div class="grid gap-2 md:grid-cols-2">
                                    <div>
                                        <x-input-label for="saran" :value="'Saran'" />
                                        <textarea name="saran" id="cek_fisik" cols="5" rows="5" required
                                            class="block w-full mt-2 items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"></textarea>
                                        <x-input-error class="mt-1" :messages="$errors->get('saran')" />
                                    </div>
                                    <div>
                                        <x-input-label for="rencana_tindak_lanjut" :value="'Rencana Tindak Lanjut'" />
                                        <textarea name="rencana_tindak_lanjut" id="rencana_tindak_lanjut" cols="5" rows="5" required
                                            class="block w-full mt-2 items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"></textarea>
                                        <x-input-error class="mt-1" :messages="$errors->get('rencana_tindak_lanjut')" />
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-start my-3">
                                <div class="flex items-center h-5">
                                    <input id="remember" type="checkbox" value=""
                                        class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-600 dark:ring-offset-gray-800"
                                        required />
                                </div>
                                <label for="remember"
                                    class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Saya
                                    setuju bahwa
                                    data ini benar dan sesuai. <span class="text-red-500">Tidak dapat melakukan
                                        perubahan data setelah di simpan!</span></label>
                            </div>
                            <x-button class="py-2 px-4" type="submit">{{ __('Simpan') }}</x-button>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
    <div class="px-2 sm:px-4 text-right pb-3">
        @include('konseling.partials.modals.batal')
        <div class="max-w-screen-xl mx-auto flex gap-2 justify-end">
            <x-danger-button class="py-2 px-4" type="button"
                x-on:click.prevent="$dispatch('open-modal', 'batal');">{{ __('Batal') }}</x-danger-button>
        </div>
    </div>
</x-app-layout>
