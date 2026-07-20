<x-app-layout>
    @include('kesehatan.partials.modals.tambah-rm')
    {{-- Content Header --}}
    <div class="flex justify-center px-2 sm:px-4 py-3 lg:py-5 border-b dark:border-blue-800">
        @include('kesehatan.partials.stepper')
    </div>
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
    <div class="px-2 sm:px-4 py-3 lg:py-5 border-t dark:border-blue-800">
        <div class="flex flex-col gap-2 max-w-screen-xl mx-auto">
            <div class="p-4 bg-white dark:bg-darker sm:rounded-lg w-full">
                <div class="w-full">
                    <section>
                        <header>
                            <div class="flex justify-between items-center">
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                    Data Rekam Medis
                                </h2>
                                <div class="flex items-end mt-2 gap-2 lg:mt-0 ml-0 lg:ml-auto">
                                    <form action="{{ route('kesehatan.riwayat-pasien.rekam-medis.download', $user->id) }}" method="GET">
                                        @csrf
                                        <x-secondary-button type="submit" class="p-2">Unduh Pdf</x-secondary-button>
                                    </form>
                                    <x-button type="button"
                                        x-on:click.prevent="$dispatch('open-modal', 'tambah-rm-baru')" type="button"
                                        class="p-2">
                                        Tambah
                                    </x-button>
                                </div>
                            </div>
                        </header>
                        <form action="{{ route('kesehatan.filter.rm') }}" class="flex gap-2 mt-1" method="post"
                            id="form-filter-rm">
                            @csrf
                            <div>
                                <select name="daysrm" id="daysrm"
                                    class="block bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 w-full dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option selected hidden>Filter</option>
                                    <option value="">Not Filtered</option>
                                    <option value="7day">7 Days</option>
                                    <option value="14day">14 Days</option>
                                    <option value="30day">30 Days</option>
                                </select>
                            </div>
                            <div class="flex flex-col sm:flex-row justify-between">
                                <div class="flex flex-wrap gap-1">
                                    <div class="w-auto md:w-[300px]">
                                        <div class="space-y-1">
                                            <div class="relative">
                                                <div
                                                    class="absolute inset-y-0 start-0 flex items-center ps-2 pointer-events-none">
                                                    <span
                                                        class="icon-[material-symbols--search] w-4 h-4 text-gray-500 dark:text-gray-400"></span>
                                                </div>
                                                <x-input-text type="search" id="filter-rm" name="filter-rm"
                                                    class="w-full p-2 ps-8" placeholder="Keluhan atau diagnosa" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="text" value="{{ $user->id }}" name="userid" hidden>
                                <button type="submit" class="hidden" id="button-filter-rm"></button>
                            </div>
                        </form>
                        <div class="relative overflow-x-auto shadow-md rounded-lg mt-2">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-dark dark:text-gray-400">
                                    <tr class="whitespace-nowrap">
                                        <th scope="col" class="p-3">
                                            #
                                        </th>
                                        <th scope="col" class="p-3">
                                            Keluhan
                                        </th>
                                        <th scope="col" class="p-3">
                                            Pemeriksaan Fisik
                                        </th>
                                        <th scope="col" class="p-3">
                                            Diagnosa
                                        </th>
                                        <th scope="col" class="p-3">
                                            Waktu
                                        </th>
                                        <th scope="col" class="p-3">

                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="result">
                                    <x-kesehatan.table-rm :data="$rm"/>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>
            </div>
            <div class="p-4 bg-white dark:bg-darker sm:rounded-lg w-full">
                <div class="w-full">
                    <section>
                        <header>
                            <div class="flex justify-between items-center">
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                    Data Konsultasi Konseling
                                </h2>
                                <div class="flex items-end mt-2 lg:mt-0 ml-0 lg:ml-auto">
                                    <x-button type="button" onclick="location.href = '/konseling/kamera-konsultasi';"
                                        class="p-2">
                                        Tambah
                                    </x-button>
                                </div>
                            </div>
                        </header>
                        <form action="{{ route('kesehatan.filter.ks') }}" class="flex gap-2 mt-1" method="post"
                            id="form-filter-ks">
                            @csrf
                            <div>
                                <select name="daysks" id="daysks"
                                    class="block bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 w-full dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option selected hidden>Filter</option>
                                    <option value="">Not Filtered</option>
                                    <option value="7day">7 Days</option>
                                    <option value="14day">14 Days</option>
                                    <option value="30day">30 Days</option>
                                </select>
                            </div>
                            <div class="flex flex-col sm:flex-row justify-between">
                                <div class="flex flex-wrap gap-1">
                                    <div class="w-auto md:w-[300px]">
                                        <div class="space-y-1">
                                            <div class="relative">
                                                <div
                                                    class="absolute inset-y-0 start-0 flex items-center ps-2 pointer-events-none">
                                                    <span
                                                        class="icon-[material-symbols--search] w-4 h-4 text-gray-500 dark:text-gray-400"></span>
                                                </div>
                                                <x-input-text type="search" id="filter-ks" name="filter-ks"
                                                    class="w-full p-2 ps-8" placeholder="Keluhan atau diagnosa" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="text" value="{{ $user->id }}" name="userid" hidden>
                                <button type="submit" class="hidden" id="button-filter-ks"></button>
                            </div>
                        </form>
                        <div class="relative overflow-x-auto shadow-md rounded-lg mt-2">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-dark dark:text-gray-400">
                                    <tr class="whitespace-nowrap">
                                        <th scope="col" class="p-3">
                                            #
                                        </th>
                                        <th scope="col" class="p-3">
                                            Keluhan
                                        </th>
                                        <th scope="col" class="p-3">
                                            Metode
                                        </th>
                                        <th scope="col" class="p-3">
                                            Diagnosa
                                        </th>
                                        <th scope="col" class="p-3">
                                            Prognosis
                                        </th>
                                        <th scope="col" class="p-3">
                                            Tanggal
                                        </th>
                                        <th scope="col" class="p-3">

                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="result">
                                    <x-kesehatan.table-konseling :data="$ks" />
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>
            </div>
            {{-- pagination --}}
            @if ($keyPagination == 'rm')
                <nav class="flex items-center flex-column flex-wrap md:flex-row justify-between mt-2"
                    aria-label="Table navigation">
                    <span
                        class="text-sm font-normal text-gray-500 dark:text-gray-400 mb-4 md:mb-0 block w-full md:inline md:w-auto">Showing
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $rm->firstItem() }} -
                            {{ $rm->lastItem() }}</span> of <span
                            class="font-semibold text-gray-900 dark:text-white">{{ $rm->total() }}</span></span>
                    <ul class="inline-flex -space-x-px text-sm h-8">
                        <li>
                            @if ($rm->onFirstPage())
                                <span
                                    class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-s-lg dark:bg-darker dark:border-gray-700 dark:text-gray-400 dark:hover:bg-[#1a304a] dark:hover:text-white">
                                    <del>
                                        Previous
                                    </del>
                                </span>
                            @else
                                <a href="{{ $rm->previousPageUrl() }}"
                                    class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-darker dark:border-gray-700 dark:text-gray-400 dark:hover:bg-[#1a304a] dark:hover:text-white">Previous</a>
                            @endif
                        </li>
                        @foreach ($rm->getUrlRange($rm->currentPage() - 1, $rm->currentPage() + 1) as $num => $url)
                            <li>
                                <a href="{{ $url }}"
                                    class="flex items-center justify-center px-3 h-8 leading-tight border border-gray-300 {{ $num == $rm->currentPage() ? 'text-blue-600 bg-blue-50 dark:bg-[#1a304a] dark:border-gray-700 dark:text-white dark:hover:bg-[#1a304a] dark:hover:text-white' : 'hover:text-blue-700 hover:bg-blue-50 border-gray-300 bg-white text-gray-500 dark:bg-darker dark:border-gray-700 dark:text-gray-400 dark:hover:bg-[#1a304a] dark:hover:text-white' }}">{{ $num }}</a>
                            </li>
                        @endforeach
                        <li>
                            @if ($rm->hasMorePages())
                                <a href="{{ $rm->nextPageUrl() }}"
                                    class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-darker dark:border-gray-700 dark:text-gray-400 dark:hover:bg-[#1a304a] dark:hover:text-white">Next</a>
                            @else
                                <span
                                    class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg dark:bg-darker dark:border-gray-700 dark:text-gray-400 dark:hover:bg-[#1a304a] dark:hover:text-white">
                                    <del>
                                        Next
                                    </del>
                                </span>
                            @endif
                        </li>
                    </ul>
                </nav>
            @endif
            @if ($keyPagination == 'ks')
                <nav class="flex items-center flex-column flex-wrap md:flex-row justify-between"
                    aria-label="Table navigation">
                    <span
                        class="text-sm font-normal text-gray-500 dark:text-gray-400 mb-4 md:mb-0 block w-full md:inline md:w-auto">Showing
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $ks->firstItem() }} -
                            {{ $ks->lastItem() }}</span> of <span
                            class="font-semibold text-gray-900 dark:text-white">{{ $ks->total() }}</span></span>
                    <ul class="inline-flex -space-x-px text-sm h-8">
                        <li>
                            @if ($ks->onFirstPage())
                                <span
                                    class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-s-lg dark:bg-darker dark:border-gray-700 dark:text-gray-400 dark:hover:bg-[#1a304a] dark:hover:text-white">
                                    <del>
                                        Previous
                                    </del>
                                </span>
                            @else
                                <a href="{{ $ks->previousPageUrl() }}"
                                    class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-darker dark:border-gray-700 dark:text-gray-400 dark:hover:bg-[#1a304a] dark:hover:text-white">Previous</a>
                            @endif
                        </li>
                        @foreach ($ks->getUrlRange($ks->currentPage() - 1, $ks->currentPage() + 1) as $num => $url)
                            <li>
                                <a href="{{ $url }}"
                                    class="flex items-center justify-center px-3 h-8 leading-tight border border-gray-300 {{ $num == $ks->currentPage() ? 'text-blue-600 bg-blue-50 dark:bg-[#1a304a] dark:border-gray-700 dark:text-white dark:hover:bg-[#1a304a] dark:hover:text-white' : 'hover:text-blue-700 hover:bg-blue-50 border-gray-300 bg-white text-gray-500 dark:bg-darker dark:border-gray-700 dark:text-gray-400 dark:hover:bg-[#1a304a] dark:hover:text-white' }}">{{ $num }}</a>
                            </li>
                        @endforeach
                        <li>
                            @if ($ks->hasMorePages())
                                <a href="{{ $ks->nextPageUrl() }}"
                                    class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-darker dark:border-gray-700 dark:text-gray-400 dark:hover:bg-[#1a304a] dark:hover:text-white">Next</a>
                            @else
                                <span
                                    class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg dark:bg-darker dark:border-gray-700 dark:text-gray-400 dark:hover:bg-[#1a304a] dark:hover:text-white">
                                    <del>
                                        Next
                                    </del>
                                </span>
                            @endif
                        </li>
                    </ul>
                </nav>
            @endif
        </div>
    </div>
    <div class="px-2 sm:px-4 text-right pb-3 mt-2">
        @include('kesehatan.partials.modals.batal')
        <div class="max-w-screen-xl mx-auto flex gap-2 justify-end">
            <x-danger-button class="py-2 px-4" type="button"
                x-on:click.prevent="$dispatch('open-modal', 'batal');">{{ __('Batal') }}</x-danger-button>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="{{ asset('src/js/kesehatan/surat-laporan.js') }}"></script>
</x-app-layout>
