<x-app-layout>
    {{-- Content Header --}}
    <div class="flex items-center justify-between px-2 sm:px-4 py-3 lg:py-5 border-b dark:border-blue-800">
        <h1 class="text-2xl font-semibold">Riwayat Kontrol</h1>
    </div>
    {{-- Main Content --}}
    <div class="px-2 sm:px-4 py-3 lg:py-5">
        <div class="bg-white dark:bg-darker rounded-lg p-3 max-w-screen-xl mx-auto">
            <ol class="relative text-gray-500 border-s border-gray-200 dark:border-blue-800">
                <li class="ms-6">
                    <div class="flex flex-col lg:flex-row gap-3 lg:gap-5">
                        <div class="flex flex-col justify-center items-start lg:items-center w-[140px]">
                            <i
                                class="ri-medal-2-line absolute flex items-center text-gray-900 dark:text-white justify-center w-8 h-8 bg-gray-100 dark:bg-dark rounded-full -start-4 ring-4 ring-white dark:ring-darker"></i>
                            <div
                                class="font-medium text-sm text-left whitespace-nowrap lg:whitespace-normal lg:text-center text-gray-900 dark:text-gray-100">
                                Top 5 Diagnosa
                                {{-- ambil 5 diagnosa terbanyak berdasarkan diagnosa pasien (mengikuti huruf kapitalnya jg) --}}
                            </div>
                        </div>
                        <div
                            class="relative overflow-x-auto border-2 border-gray-200 dark:border-blue-800 rounded sm:rounded-lg w-full">
                            <table class="w-full text-sm text-left">
                                <thead
                                    class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-50 dark:bg-dark whitespace-nowrap">
                                    <tr class="">
                                        <th scope="col" class="p-3">
                                            TOP
                                        </th>
                                        <th scope="col" class="p-3">
                                            DIAGNOSA
                                        </th>
                                        <th scope="col" class="p-3">
                                            PASIEN
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <x-kesehatan.table-top-diagnosa />
                                </tbody>
                            </table>
                        </div>
                        {{-- <div
                            class="relative overflow-x-auto border-2 border-gray-200 dark:border-blue-800 rounded sm:rounded-lg w-full">
                            <table class="w-full text-sm text-left">
                                <thead
                                    class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-50 dark:bg-dark whitespace-nowrap">
                                    <tr class="">
                                        <th scope="col" class="p-3">
                                            NO
                                        </th>
                                        <th scope="col" class="p-3">
                                            SURAT ID
                                        </th>
                                        <th scope="col" class="p-3">
                                            NAMA
                                        </th>
                                        <th scope="col" class="p-3">
                                            POSISI
                                        </th>
                                        <th scope="col" class="p-3">
                                            DIAGNOSA
                                        </th>
                                        <th scope="col" class="p-3">
                                            TANGGAL
                                        </th>
                                        <th scope="col" class="p-3 text-center">
                                            AKSI
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                            <div class="text-center my-5 text-xs w-full">Surat belum tersedia.</div>
                        </div> --}}
                    </div>
                </li>
            </ol>
        </div>
    </div>
    <div class="px-2 sm:px-4 py-3 lg:py-5 border-y dark:border-blue-800">
        <div class="bg-white dark:bg-darker p-4 rounded-lg">
            @include('kesehatan..partials.filter-riwayat-kontrol')
        </div>
        <div class="overflow-x-auto shadow-md sm:rounded-lg mt-2">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-darker dark:text-gray-400">
                    <tr class="whitespace-nowrap">
                        <th scope="col" class="px-4 py-3">
                            #
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Pengguna
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Email
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Diagnosa terakhir
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Keluhan Terakhir
                        </th>
                        <th scope="col" class="px-4 py-3">

                        </th>
                    </tr>
                </thead>
                <tbody id="result">
                    <x-kesehatan.table-pasien-kontrol />
                </tbody>
            </table>
        </div>
        {{-- pagination --}}
        {{-- <nav class="flex items-center flex-column flex-wrap md:flex-row justify-between mt-2"
            aria-label="Table navigation">
            <span
                class="text-sm font-normal text-gray-500 dark:text-gray-400 mb-4 md:mb-0 block w-full md:inline md:w-auto">Showing
                <span class="font-semibold text-gray-900 dark:text-white">{{ $data->firstItem() }} -
                    {{ $data->lastItem() }}</span> of <span
                    class="font-semibold text-gray-900 dark:text-white">{{ $data->total() }}</span></span>
            <ul class="inline-flex -space-x-px text-sm h-8">
                <li>
                    @if ($data->onFirstPage())
                        <span
                            class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-s-lg dark:bg-darker dark:border-gray-700 dark:text-gray-400 dark:hover:bg-[#1a304a] dark:hover:text-white">
                            <del>
                                Previous
                            </del>
                        </span>
                    @else
                        <a href="{{ $data->previousPageUrl() }}"
                            class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-darker dark:border-gray-700 dark:text-gray-400 dark:hover:bg-[#1a304a] dark:hover:text-white">Previous</a>
                    @endif
                </li>
                @foreach ($data->getUrlRange($data->currentPage() - 1, $data->currentPage() + 1) as $num => $url)
                    <li>
                        <a href="{{ $url }}"
                            class="flex items-center justify-center px-3 h-8 leading-tight border border-gray-300 {{ $num == $data->currentPage() ? 'text-blue-600 bg-blue-50 dark:bg-[#1a304a] dark:border-gray-700 dark:text-white dark:hover:bg-[#1a304a] dark:hover:text-white' : 'hover:text-blue-700 hover:bg-blue-50 border-gray-300 bg-white text-gray-500 dark:bg-darker dark:border-gray-700 dark:text-gray-400 dark:hover:bg-[#1a304a] dark:hover:text-white' }}">{{ $num }}</a>
                    </li>
                @endforeach
                <li>
                    @if ($data->hasMorePages())
                        <a href="{{ $data->nextPageUrl() }}"
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
        </nav> --}}
    </div>
</x-app-layout>
