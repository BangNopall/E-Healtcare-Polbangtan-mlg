<x-app-layout>
    {{-- modals --}}
    @include('inventaris.partials.modals.alat-noused.tambah')
    {{-- Content Header --}}
    <div class="flex items-center justify-between px-2 sm:px-4 py-3 lg:py-5 border-b dark:border-blue-800">
        <h1 class="text-2xl font-semibold">Stok Alat Tersisa</h1>
    </div>
    {{-- Main Content --}}
    <div class="px-2 sm:px-4 py-3 lg:py-5 border-b dark:border-blue-600">
        {{-- filter obat --}}
        <div class="bg-white dark:bg-darker p-4 rounded-lg">
            @include('inventaris.partials.alat-noused.filter')
        </div>
    </div>
    <div class="px-2 sm:px-4 py-3 lg:py-5">
        <div class="relative overflow-x-auto shadow-md rounded-lg">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-darker dark:text-gray-400">
                    <tr class="whitespace-nowrap">
                        <th scope="col" class="px-4 py-3">
                            #
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Nama Item
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Item ID
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Kategori
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Kuantitas
                        </th>
                        <th scope="col" class="px-4 py-3">

                        </th>
                    </tr>
                </thead>
                <tbody id="result">
                    <x-inventaris.table-consumable :data="$data" />
                </tbody>
            </table>
        </div>
        {{-- pagination --}}
        <nav class="flex items-center flex-column flex-wrap md:flex-row justify-between mt-2"
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
        </nav>
    </div>

    <script src="{{ asset('src/js/inventaris/consumable.js') }}"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</x-app-layout>
