<x-app-layout>
    {{-- Content Header --}}
    <div class="flex items-center justify-between px-2 sm:px-4 py-3 lg:py-5 border-b dark:border-blue-800">
        <h1 class="text-2xl font-semibold">Riwayat Surat Laporan</h1>
    </div>
    {{-- Main Content --}}
    <div class="px-2 sm:px-4 py-3 lg:py-5">
        <div class="bg-white dark:bg-darker rounded-lg p-3 max-w-screen-xl mx-auto">
            <ol class="relative text-gray-500 border-s border-gray-200 dark:border-blue-800">
                {{-- surat keterangan berobat --}}
                <li class="mb-8 lg:mb-10 ms-6">
                    <div class="flex flex-col lg:flex-row gap-3 lg:gap-5">
                        <div class="flex flex-col justify-center items-start lg:items-center w-[140px]">
                            <i
                                class="ri-capsule-fill absolute flex items-center text-gray-900 dark:text-white justify-center w-8 h-8 bg-gray-100 dark:bg-dark rounded-full -start-4 ring-4 ring-white dark:ring-darker"></i>
                            <div
                                class="font-medium text-sm text-left whitespace-nowrap lg:whitespace-normal lg:text-center text-gray-900 dark:text-gray-100">
                                Surat Keterangan Berobat
                            </div>
                        </div>
                        @if ($skbs->count() > 0)
                            <div
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
                                                Diagnosa
                                            </th>
                                            <th scope="col" class="p-3">
                                                TANGGAL
                                            </th>
                                            <th scope="col" class="p-3 text-center">
                                                AKSI
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <x-kesehatan.table-skb-user :data="$skbs" />
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div
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
                                                Diagnosa
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
                            </div>
                        @endif
                    </div>
                </li>
                {{-- surat keterangan sehat --}}
                <li class="mb-8 lg:mb-10 ms-6">
                    <div class="flex flex-col lg:flex-row gap-3 lg:gap-5">
                        <div class="flex flex-col justify-center items-start lg:items-center w-[140px]">
                            <i
                                class="ri-home-heart-line absolute flex items-center text-gray-900 dark:text-white justify-center w-8 h-8 bg-gray-100 dark:bg-dark rounded-full -start-4 ring-4 ring-white dark:ring-darker"></i>
                            <div
                                class="font-medium text-sm text-left whitespace-nowrap lg:whitespace-normal lg:text-center text-gray-900 dark:text-gray-100">
                                Surat Keterangan Sehat
                            </div>
                        </div>
                        @if ($skses->count() > 0)
                            <div
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
                                                KETERANGAN
                                            </th>
                                            <th scope="col" class="p-3">
                                                TANGGAL
                                            </th>
                                            <th scope="col" class="p-3 text-center">
                                                AKSI
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <x-kesehatan.table-skse-user :data="$skses" />
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div
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
                                                KETERANGAN
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
                            </div>
                        @endif
                    </div>
                </li>
                {{-- surat keterangan sakit --}}
                <li class="mb-8 lg:mb-10 ms-6">
                    <div class="flex flex-col lg:flex-row gap-3 lg:gap-5">
                        <div class="flex flex-col justify-center items-start lg:items-center w-[140px]">
                            <i
                                class="ri-file-list-3-line absolute flex items-center text-gray-900 dark:text-white justify-center w-8 h-8 bg-gray-100 dark:bg-dark rounded-full -start-4 ring-4 ring-white dark:ring-darker"></i>
                            <div
                                class="font-medium text-sm text-left whitespace-nowrap lg:whitespace-normal lg:text-center text-gray-900 dark:text-gray-100">
                                Surat Keterangan Sakit
                            </div>
                        </div>
                        @if ($skss->count() > 0)
                            <div
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
                                                IZIN SAKIT
                                            </th>
                                            <th scope="col" class="p-3">
                                                MULAI
                                            </th>
                                            <th scope="col" class="p-3">
                                                AKHIR
                                            </th>
                                            <th scope="col" class="p-3 text-center">
                                                AKSI
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <x-kesehatan.table-sks-user :data="$skss" />
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div
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
                                                IZIN SAKIT
                                            </th>
                                            <th scope="col" class="p-3">
                                                MULAI
                                            </th>
                                            <th scope="col" class="p-3">
                                                AKHIR
                                            </th>
                                            <th scope="col" class="p-3 text-center">
                                                AKSI
                                            </th>
                                        </tr>
                                    </thead>
                                </table>
                                <div class="text-center my-5 text-xs w-full">Surat belum tersedia.</div>
                            </div>
                        @endif
                    </div>
                </li>
                {{-- surat rujukan --}}
                <li class="ms-6">
                    <div class="flex flex-col lg:flex-row gap-3 lg:gap-5">
                        <div class="flex flex-col justify-center items-start lg:items-center w-[140px]">
                            <i
                                class="ri-hospital-line absolute flex items-center text-gray-900 dark:text-white justify-center w-8 h-8 bg-gray-100 dark:bg-dark rounded-full -start-4 ring-4 ring-white dark:ring-darker"></i>
                            <div
                                class="font-medium text-sm text-left whitespace-nowrap lg:whitespace-normal lg:text-center text-gray-900 dark:text-gray-100">
                                Surat Rujukan
                            </div>
                        </div>
                        @if ($srss->count() > 0)
                            <div
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
                                                DOKTER RUJUK
                                            </th>
                                            <th scope="col" class="p-3">
                                                RS/PUSKESMAS
                                            </th>
                                            <th scope="col" class="p-3">
                                                TANGGAL
                                            </th>
                                            <th scope="col" class="p-3 text-center">
                                                AKSI
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <x-kesehatan.table-sr-user :data="$srss" />
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div
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
                                                DOKTER RUJUK
                                            </th>
                                            <th scope="col" class="p-3">
                                                RS/PUSKESMAS
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
                            </div>
                        @endif
                    </div>
                </li>
            </ol>
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
    </div>
</x-app-layout>
