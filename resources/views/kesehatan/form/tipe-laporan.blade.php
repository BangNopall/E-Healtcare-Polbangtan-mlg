<x-app-layout>
    {{-- Content Header --}}
    <div class="flex justify-center px-2 sm:px-4 py-3 lg:py-5 border-b dark:border-blue-800">
        @include('kesehatan.partials.stepper')
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
                    @if ($user->role == 'Karyawan')
                        <span>Data Karyawan</span>
                    @else
                        <span>Data Mahasiswa</span>
                    @endif
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
                            @include('kesehatan.partials.profil-laporan')
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 w-full md:w-[930px]">
                        <div class="p-4 bg-white dark:bg-darker sm:rounded-lg">
                            <div class="w-full">
                                @include('kesehatan.partials.info-personal-laporan')
                            </div>
                        </div>
                        @if ($user->cdmi == 1)
                            <div class="p-4 bg-white dark:bg-darker sm:rounded-lg">
                                <div class="w-full">
                                    @include('kesehatan.partials.info-mahasiswa-laporan')
                                </div>
                            </div>
                        @endif
                        <div class="p-4 bg-white dark:bg-darker sm:rounded-lg">
                            <div class="w-full">
                                @include('kesehatan.partials.rpd-laporan')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form action="{{ route('kesehatan.tipe-form.request-surat', $user->id) }}" method="post">
        @csrf
        <div class="flex justify-center px-2 sm:px-4 py-3 lg:py-5 border-t dark:border-blue-800">
            <div class="p-4 bg-white dark:bg-darker sm:rounded-lg w-full">
                <div class="w-full">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                Tipe Surat Laporan
                            </h2>
                        </header>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                            <div
                                class="flex items-center ps-4 border border-gray-200 rounded dark:border-gray-700 mt-3">
                                <input id="tipe-1" type="radio" value="surat_keterangan_obat" name="tipe"
                                    required
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label for="tipe-1"
                                    class="w-full py-4 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Surat
                                    Keterangan Obat</label>
                            </div>
                            <div
                                class="flex items-center ps-4 border border-gray-200 rounded dark:border-gray-700 mt-3">
                                <input id="tipe-2" type="radio" value="surat_keterangan_sakit" name="tipe"
                                    required
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label for="tipe-2"
                                    class="w-full py-4 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Surat
                                    Keterangan Sakit</label>
                            </div>
                            <div
                                class="flex items-center ps-4 border border-gray-200 rounded dark:border-gray-700 mt-3">
                                <input id="tipe-3" type="radio" value="surat_rujukan" name="tipe" required
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label for="tipe-3"
                                    class="w-full py-4 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Surat
                                    Rujukan</label>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
        <div class="px-2 sm:px-4 text-right pb-3">
            @include('kesehatan.partials.modals.batal')
            <x-danger-button class="py-2 px-4" type="button"
                x-on:click.prevent="$dispatch('open-modal', 'batal');">{{ __('Batal') }}</x-danger-button>
            <x-button class="py-2 px-4" type="submit">{{ __('Lanjut') }}</x-button>
        </div>
    </form>
</x-app-layout>
