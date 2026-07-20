<x-app-layout>
    {{-- Main Content --}}
    @include('inventaris.partials.modals.deposit-detail', ['data' => $data])
    @include('inventaris.partials.modals.withdraw-detail', ['data' => $data])
    @empty($data->satuan_id)
        @include('inventaris.partials.modals.notkategori', ['data' => $data, 'kategori' => $kategoriObat])
    @endempty
    <div class="py-4 px-2 sm:px-4">
        <div class="flex flex-col lg:flex-row gap-5">
            <div class="w-full lg:w-[500px]">
                {{-- judul mobile view --}}
                <div class="bg-white dark:bg-darker block lg:hidden p-4 rounded-md w-full">
                    <div class="flex items-center">
                        <h1 class="leading-normal break-all text-ellipsis text-2xl sm:text-3xl font-semibold">
                            {{ $data->nama_obat }}
                        </h1>
                    </div>
                    <div class="flex flex-wrap items-center text-xs mt-2 gap-1">
                        <div class="flex mr-6 items-center">
                            <span class="icon-[la--orcid] w-4 h-4"></span>
                            <span class="ml-1">{{ $data->kode_obat }}</span>
                        </div>
                        <div class="flex mr-6 items-center">
                            <span class="icon-[material-symbols--category-outline] w-4 h-4"></span>
                            <span class="ml-1">
                                @isset($data->satuan_id)
                                    {{ $data->SatuanObat->nama_satuan }}
                                @endisset
                            </span>
                        </div>
                        <div class="flex mr-6 items-center">
                            <span class="icon-[mdi--user] w-4 h-4"></span>
                            <span class="ml-1">{{ $data->User->name }}</span>
                        </div>
                        <div class="flex mr-6 items-center">
                            <span class="icon-[mdi--box-eye] w-4 h-4"></span>
                            <span class="ml-1">{{ strval($data->stok) }}</span>

                        </div>
                    </div>
                    <div class="flex gap-2 mt-2 items-center">
                        <x-button type="button" x-on:click.prevent="$dispatch('open-modal', 'deposit-detail')"
                            class="px-4 py-2">Deposit</x-button>
                        <x-button type="button" x-on:click.prevent="$dispatch('open-modal', 'withdraw-detail')"
                            class="px-4 py-2">Withdraw</x-button>
                    </div>
                </div>
                <div
                    class="min-w-[100px] w-full h-[300px] flex-shrink-0 rounded-md bg-white p-1 dark:bg-darker mt-5 lg:mt-0">
                    <form action="{{ route('inventaris.obat.update-foto', $data->id) }}" method="post"
                        class="w-full h-full" enctype="multipart/form-data">
                        @csrf
                        <div class="relative w-full h-full" id="inputfilefoto">
                            @if ($data->foto_obat == null)
                                <label for="dropzone-file"
                                    class="flex flex-col items-center justify-center w-full h-full border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-darker hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <span
                                            class="icon-[ion--cloud-upload-outline] w-8 h-8 mb-4 text-gray-500 dark:text-gray-400"></span>
                                        <p
                                            class="mb-2 text-sm text-gray-500 dark:text-gray-400 font-semibold text-center">
                                            Tekan
                                            untuk unggah foto</p>
                                    </div>
                                </label>
                            @else
                                <button type="button" id="updateImageButton"
                                    class="w-full h-full object-contain rounded-md">
                                    <img src="{{ asset('storage/foto_obat/' . $data->foto_obat) }}" alt="Preview"
                                        class="w-full h-full object-contain rounded-md" />
                                    {{-- <span class="icon-[material-symbols--change-circle-outline] w-6 h-6"></span> --}}
                                </button>
                            @endif
                            <input id="dropzone-file" type="file" accept="image/*" class="hidden" name="foto_obat"
                                onchange="previewFile()" />
                        </div>
                        <div id="preview-container" class="relative w-full h-full hidden">
                            <img id="preview-image" src="#" alt="Preview"
                                class="w-full h-full object-contain rounded-md" />
                            <x-button type="button" x-on:click.prevent="removeImage()"
                                class="absolute top-2 right-2 p-1.5 rounded-full flex justify-center items-center shadow-md">
                                <span class="icon-[la--trash] w-6 h-6"></span>
                            </x-button>
                            <x-button type="submit"
                                class="absolute bottom-2 right-2 p-1.5 rounded-full flex justify-center items-center shadow-md">
                                <span class="icon-[line-md--uploading-loop] w-6 h-6"></span>
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="w-full">
                {{-- judul desktop view --}}
                <div class="bg-white dark:bg-darker hidden lg:block p-4 rounded-md w-full">
                    <div class="flex items-center">
                        <h1 class="leading-normal break-all text-ellipsis text-3xl font-semibold">
                            {{ $data->nama_obat }}
                        </h1>
                    </div>
                    <div class="flex flex-wrap mt-2 text-sm">
                        <div class="flex mr-6 items-center">
                            <span class="icon-[la--orcid] w-4 h-4"></span>
                            <span class="ml-1">{{ $data->kode_obat }}</span>
                        </div>
                        <div class="flex mr-6 items-center">
                            <span class="icon-[material-symbols--category-outline] w-4 h-4"></span>
                            <span class="ml-1">
                                @isset($data->satuan_id)
                                    {{ $data->SatuanObat->nama_satuan }}
                                @endisset
                            </span>
                        </div>
                        <div class="flex mr-6 items-center">
                            <span class="icon-[mdi--user] w-4 h-4"></span>
                            <span class="ml-1">{{ $data->User->name }}</span>
                        </div>
                        <div class="flex mr-6 items-center">
                            <span class="icon-[mdi--box-eye] w-4 h-4"></span>
                            <span class="ml-1">{{ strval($data->stok) }}</span>

                        </div>
                    </div>
                    <div class="flex gap-2 mt-2 items-center">
                        <x-button type="button" x-on:click.prevent="$dispatch('open-modal', 'deposit-detail')"
                            class="px-4 py-2">Deposit</x-button>
                        <x-button class="px-4 py-2" type="button"
                            x-on:click.prevent="$dispatch('open-modal', 'withdraw-detail')">Withdraw</x-button>
                    </div>
                </div>
                {{-- Riwayat Penggunaan --}}
                <div
                    class="bg-white p-4 dark:bg-darker w-full rounded-md rounded-b-none border-b-2 dark:border-blue-600 mt-0 lg:mt-5">
                    <div class="flex items-center">
                        <span class="icon-[material-symbols-light--table] mr-3 w-6 h-6"></span>
                        <h2 class="text-md sm:text-lg font-medium">Riwayat Penggunaan</h2>
                    </div>
                </div>
                {{-- Riwayat Item --}}
                <div
                    class="bg-white p-4 dark:bg-darker w-full text-gray-600 dark:text-gray-400 text-sm rounded-md rounded-t-none">
                    <div class="relative overflow-x-auto rounded-lg">
                        <x-table color="tabel" :headers="['Pengguna', 'Kuantitas', 'Tanggal', 'Waktu', 'Aksi']">
                            @foreach ($obatLogs as $obatLog)
                                <tr
                                    class="bg-white border-b dark:bg-dark dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-[#1a304a] whitespace-nowrap transition-colors duration-150">
                                    <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $obatLog->User->name }}
                                    </td>
                                    <td class="px-4 py-2">
                                        {{ $obatLog->Qty }}
                                    </td>
                                    <td class="px-4 py-2">
                                        {{ $obatLog->created_at->format('Y-m-d') }}
                                    </td>
                                    <td class="px-4 py-2">
                                        {{ \Carbon\Carbon::parse($obatLog->created_at)->format('H:i') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('inventaris.obat.logpengguna', $obatLog->id) }}" target="_blank"
                                            rel="noopener noreferrer">
                                            <span class="icon-[mdi--user-arrow-left] w-6 h-6"></span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </x-table>
                    </div>
                    @if ($paginationControl == 'inventoryLogs')
                        <nav class="flex items-center flex-column flex-wrap md:flex-row justify-between pt-2"
                            aria-label="Table navigation">
                            <span
                                class="text-sm font-normal text-gray-500 dark:text-gray-400 mb-4 md:mb-0 block w-full md:inline md:w-auto">Showing
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $obatLogs->firstItem() }}
                                    - {{ $obatLogs->lastItem() }}</span> of <span
                                    class="font-semibold text-gray-900 dark:text-white">{{ $obatLogs->total() }}</span></span>
                            <ul class="inline-flex -space-x-px text-sm h-8">
                                <li>
                                    @if ($inventoryLogs->onFirstPage())
                                        <span
                                            class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-s-lg">
                                            <del>
                                                Previous
                                            </del>
                                        </span>
                                    @else
                                        <a href="{{ $inventoryLogs->previousPageUrl() }}"
                                            class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700">Previous</a>
                                    @endif
                                </li>
                                @foreach ($inventoryLogs->getUrlRange($inventoryLogs->currentPage() - 1, $inventoryLogs->currentPage() + 1) as $num => $url)
                                    <li>
                                        <a href="{{ $url }}"
                                            class="flex items-center justify-center px-3 h-8 leading-tight border border-gray-300 {{ $num == $inventoryLogs->currentPage() ? 'text-blue-600 bg-blue-50 hover:text-blue-700' : 'bg-white text-gray-500 hover:text-gray-700' }} hover:bg-gray-100">{{ $num }}</a>
                                    </li>
                                @endforeach
                                <li>
                                    @if ($inventoryLogs->hasMorePages())
                                        <a href="{{ $inventoryLogs->nextPageUrl() }}"
                                            class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700">Next</a>
                                    @else
                                        <span
                                            class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg">
                                            <del>
                                                Next
                                            </del>
                                        </span>
                                    @endif
                                </li>
                            </ul>
                        </nav>
                    @elseif ($paginationControl == 'obatLogs')
                        <nav class="flex items-center flex-column flex-wrap md:flex-row justify-between pt-2"
                            aria-label="Table navigation">
                            <span
                                class="text-sm font-normal text-gray-500 dark:text-gray-400 mb-4 md:mb-0 block w-full md:inline md:w-auto">Showing
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $obatLogs->firstItem() }}
                                    - {{ $obatLogs->lastItem() }}</span> of <span
                                    class="font-semibold text-gray-900 dark:text-white">{{ $obatLogs->total() }}</span></span>
                            <ul class="inline-flex -space-x-px text-sm h-8">
                                <li>
                                    @if ($obatLogs->onFirstPage())
                                        <span
                                            class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-s-lg">
                                            <del>
                                                Previous
                                            </del>
                                        </span>
                                    @else
                                        <a href="{{ $obatLogs->previousPageUrl() }}"
                                            class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700">Previous</a>
                                    @endif
                                </li>
                                @foreach ($obatLogs->getUrlRange($obatLogs->currentPage() - 1, $obatLogs->currentPage() + 1) as $num => $url)
                                    <li>
                                        <a href="{{ $url }}"
                                            class="flex items-center justify-center px-3 h-8 leading-tight border border-gray-300 {{ $num == $obatLogs->currentPage() ? 'text-blue-600 bg-blue-50 hover:text-blue-700' : 'bg-white text-gray-500 hover:text-gray-700' }} hover:bg-gray-100">{{ $num }}</a>
                                    </li>
                                @endforeach
                                <li>
                                    @if ($obatLogs->hasMorePages())
                                        <a href="{{ $obatLogs->nextPageUrl() }}"
                                            class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700">Next</a>
                                    @else
                                        <span
                                            class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg">
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
                <div
                    class="bg-white p-4 dark:bg-darker w-full rounded-md rounded-b-none border-b-2 dark:border-blue-600 mt-0 lg:mt-5">
                    <div class="flex items-center">
                        <span class="icon-[material-symbols-light--table] mr-3 w-6 h-6"></span>
                        <h2 class="text-md sm:text-lg font-medium">Riwayat Item</h2>
                    </div>
                </div>
                <div
                    class="bg-white p-4 dark:bg-darker w-full text-gray-600 dark:text-gray-400 text-sm rounded-md rounded-t-none">
                    <div class="relative overflow-x-auto rounded-lg">
                        <x-table color="tabel" :headers="['Pengguna', 'Tipe', 'Kuantitas', 'Produksi', 'Expired', 'Aksi']">
                            @foreach ($inventoryLogs as $inventoryLog)
                                <tr
                                    class="bg-white border-b dark:bg-dark dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-[#1a304a] whitespace-nowrap transition-colors duration-150">
                                    <td class="px-3 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $inventoryLog->User->name }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ $inventoryLog->type }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ $inventoryLog->Qty }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ $inventoryLog->production_date }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ $inventoryLog->expired_date }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{-- expired dan withdraw ajah --}}
                                        @if ($inventoryLog->type == 'expired' || $inventoryLog->type == 'withdraw')
                                            <form action="" method="post">
                                                @csrf
                                                @include('inventaris.partials.modals.detail-item')
                                                <button type="button"
                                                    x-on:click.prevent="$dispatch('open-modal', 'detail-item{{ $inventoryLog->id }}')"
                                                    class="font-medium text-blue-500 mr-2">
                                                    <span class="icon-[mdi--show-outline] w-6 h-6"></span>
                                                </button>
                                            </form>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </x-table>
                    </div>
                    {{-- {{ $inventoryLogs->links() }} --}}
                    @if ($paginationControl == 'inventoryLogs')
                        <nav class="flex items-center flex-column flex-wrap md:flex-row justify-between pt-2"
                            aria-label="Table navigation">
                            <span
                                class="text-sm font-normal text-gray-500 dark:text-gray-400 mb-4 md:mb-0 block w-full md:inline md:w-auto">Showing
                                <span
                                    class="font-semibold text-gray-900 dark:text-white">{{ $inventoryLogs->firstItem() }}
                                    - {{ $inventoryLogs->lastItem() }}</span> of <span
                                    class="font-semibold text-gray-900 dark:text-white">{{ $inventoryLogs->total() }}</span></span>
                            <ul class="inline-flex -space-x-px text-sm h-8">
                                <li>
                                    @if ($inventoryLogs->onFirstPage())
                                        <span
                                            class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-s-lg">
                                            <del>
                                                Previous
                                            </del>
                                        </span>
                                    @else
                                        <a href="{{ $inventoryLogs->previousPageUrl() }}"
                                            class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700">Previous</a>
                                    @endif
                                </li>
                                @foreach ($inventoryLogs->getUrlRange($inventoryLogs->currentPage() - 1, $inventoryLogs->currentPage() + 1) as $num => $url)
                                    <li>
                                        <a href="{{ $url }}"
                                            class="flex items-center justify-center px-3 h-8 leading-tight border border-gray-300 {{ $num == $inventoryLogs->currentPage() ? 'text-blue-600 bg-blue-50 hover:text-blue-700' : 'bg-white text-gray-500 hover:text-gray-700' }} hover:bg-gray-100">{{ $num }}</a>
                                    </li>
                                @endforeach
                                <li>
                                    @if ($inventoryLogs->hasMorePages())
                                        <a href="{{ $inventoryLogs->nextPageUrl() }}"
                                            class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700">Next</a>
                                    @else
                                        <span
                                            class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg">
                                            <del>
                                                Next
                                            </del>
                                        </span>
                                    @endif
                                </li>
                            </ul>
                        </nav>
                    @elseif ($paginationControl == 'obatLogs')
                        <nav class="flex items-center flex-column flex-wrap md:flex-row justify-between pt-2"
                            aria-label="Table navigation">
                            <span
                                class="text-sm font-normal text-gray-500 dark:text-gray-400 mb-4 md:mb-0 block w-full md:inline md:w-auto">Showing
                                <span
                                    class="font-semibold text-gray-900 dark:text-white">{{ $inventoryLogs->firstItem() }}
                                    - {{ $inventoryLogs->lastItem() }}</span> of <span
                                    class="font-semibold text-gray-900 dark:text-white">{{ $inventoryLogs->total() }}</span></span>
                            <ul class="inline-flex -space-x-px text-sm h-8">
                                <li>
                                    @if ($obatLogs->onFirstPage())
                                        <span
                                            class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-s-lg">
                                            <del>
                                                Previous
                                            </del>
                                        </span>
                                    @else
                                        <a href="{{ $obatLogs->previousPageUrl() }}"
                                            class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700">Previous</a>
                                    @endif
                                </li>
                                @foreach ($obatLogs->getUrlRange($obatLogs->currentPage() - 1, $obatLogs->currentPage() + 1) as $num => $url)
                                    <li>
                                        <a href="{{ $url }}"
                                            class="flex items-center justify-center px-3 h-8 leading-tight border border-gray-300 {{ $num == $obatLogs->currentPage() ? 'text-blue-600 bg-blue-50 hover:text-blue-700' : 'bg-white text-gray-500 hover:text-gray-700' }} hover:bg-gray-100">{{ $num }}</a>
                                    </li>
                                @endforeach
                                <li>
                                    @if ($obatLogs->hasMorePages())
                                        <a href="{{ $obatLogs->nextPageUrl() }}"
                                            class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700">Next</a>
                                    @else
                                        <span
                                            class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg">
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
        </div>
    </div>
    <div id="url_get_kategori_obat" class="hidden"></div>
    <script>
        const url = "{{ route('api.getKategoriObat') }}"
        document.getElementById('url_get_kategori_obat').innerHTML = url;
    </script>
    <script src="{{ asset('src/js/inventaris/detail-barang.js') }}"></script>
</x-app-layout>
