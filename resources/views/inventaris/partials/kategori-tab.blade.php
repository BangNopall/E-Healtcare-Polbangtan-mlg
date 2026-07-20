<div x-show="activeTab === 'kategori'"
    class="p-4 sm:p-6 bg-white text-medium text-gray-500 dark:text-gray-400 dark:bg-darker rounded-lg w-full">
    <div class="flex justify-between items-center">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Kategori Tab</h3>
    </div>
    <p class="mb-2 text-sm">
        Anda dapat melakukan pengaturan untuk merubah serta menghapus kategori satuan item inventaris.
    </p>

    <div id="accordion-open" data-accordion="open" data-active-classes="bg-gray-100 text-gray-800 dark:bg-dark dark:text-white">
        <h2 id="obat-heading">
            <button type="button"
                class="flex items-center justify-between w-full p-3 font-medium text-gray-500 border border-b-0 border-gray-200 rounded-t-xl dark:border-blue-800 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-dark gap-3"
                data-accordion-target="#obat-body" aria-expanded="false"
                aria-controls="obat-body">
                <span class="flex items-center">
                    <span class="icon-[material-symbols--category-outline] w-6 h-6 shrink-0 me-2"></span> Obat</span>
                <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="false"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5 5 1 1 5" />
                </svg>
            </button>
        </h2>
        <div id="obat-body" class="hidden" aria-labelledby="accordion-open-heading-1">
            <div class="p-3 border border-b-0 border-gray-200 dark:border-blue-800 dark:bg-darker">
                <x-button type="button" x-on:click.prevent="$dispatch('open-modal', 'tambah-obat')"
                    class="p-2 mb-2  flex justify-center items-center">
                    <span class="icon-[ic--baseline-plus] w-5 h-5"></span>
                </x-button>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-1 overflow-y-auto max-h-[400px]">
                    @foreach ($data as $kategori)
                        <div class="flex border border-blue-500">
                            <form action="{{ route('inventaris.kategori.obat.update', $kategori->id) }}" method="post"
                                class="flex w-full">
                                @csrf
                                <input type="text" id="small-input" name="nama_satuan" required
                                    class="block w-full p-2 text-gray-900 border border-gray-300 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 text-sm"
                                    value="{{ $kategori->nama_satuan }}">
                                <x-button type="submit"
                                    class="p-1.5 flex items-center rounded-none focus:ring-0 justify-center">
                                    <span class="icon-[iconamoon--check-bold] w-6 h-6"></span>
                                </x-button>
                            </form>
                            <form action="{{ route('inventaris.kategori.obat.delete', $kategori->id) }}" class="">
                                @include('inventaris.partials.modals.pengaturan.hapus-obat')
                                <x-danger-button type="button"
                                    x-on:click.prevent="$dispatch('open-modal', 'hapus-item-{{ $kategori->id }}')"
                                    class="p-1.5 w-full h-full flex items-center rounded-none justify-center">
                                    <span class="icon-[mdi--trash-outline] w-6 h-6"></span>
                                </x-danger-button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <h2 id="alatterpakai-heading">
            <button type="button"
                class="flex items-center justify-between w-full p-3 font-medium text-gray-500 border border-b-0 border-gray-200 dark:border-blue-800 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-dark gap-3"
                data-accordion-target="#alatterpakai-body" aria-expanded="false"
                aria-controls="alatterpakai-body">
                <span class="flex items-center">
                    <span class="icon-[material-symbols--category-outline] w-6 h-6 shrink-0 me-2"></span> Alat Terpakai
                </span>
                <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5 5 1 1 5" />
                </svg>
            </button>
        </h2>
        <div id="alatterpakai-body" class="hidden" aria-labelledby="alatterpakai-heading">
            <div class="p-3 border border-b-0 border-gray-200 dark:border-blue-800">
                <x-button type="button" x-on:click.prevent="$dispatch('open-modal', 'tambah-terpakai')"
                    class="p-2 mb-2  flex justify-center items-center">
                    <span class="icon-[ic--baseline-plus] w-5 h-5"></span>
                </x-button>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-1 overflow-y-auto max-h-[400px]">
                    @foreach ($dataAlat as $alat)
                        <div class="flex border border-blue-500">
                            <form action="{{ route('inventaris.kategori.alat.update', $alat->id) }}" method="post"
                                class="flex w-full">
                                @csrf
                                <input type="text" id="small-input" name="nama_kategori" required
                                    class="block w-full p-2 text-sm text-gray-900 border border-gray-300 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    value="{{ $alat->nama_kategori }}">
                                <x-button type="submit"
                                    class="p-1.5 flex items-center rounded-none focus:ring-0 justify-center">
                                    <span class="icon-[iconamoon--check-bold] w-6 h-6"></span>
                                </x-button>
                            </form>
                            <form action="{{ route('inventaris.kategori.alat.delete', $alat->id) }}" class="">
                                @include('inventaris.partials.modals.pengaturan.hapus-alat-terpakai')
                                <x-danger-button type="button"
                                    x-on:click.prevent="$dispatch('open-modal', 'hapus-alat-terpakai-{{ $alat->id }}')"
                                    class="p-1.5 w-full h-full flex items-center rounded-none focus:ring-0 justify-center">
                                    <span class="icon-[mdi--trash-outline] w-6 h-6"></span>
                                </x-danger-button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <h2 id="alattersisa-heading">
            <button type="button"
                class="flex items-center justify-between w-full p-3 font-medium text-gray-500 border border-gray-200 dark:border-blue-800 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-dark gap-3"
                data-accordion-target="#alattersisa-body" aria-expanded="false"
                aria-controls="alattersisa-body">
                <span class="flex items-center">
                    <span class="icon-[material-symbols--category-outline] w-6 h-6 shrink-0 me-2"></span> Alat Tersisa
                </span>
                <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5 5 1 1 5" />
                </svg>
            </button>
        </h2>
        <div id="alattersisa-body" class="hidden" aria-labelledby="alattersisa-heading">
            <div class="p-3 border border-t-0 border-gray-200 dark:border-blue-800">
                <x-button type="button" x-on:click.prevent="$dispatch('open-modal', 'tambah-tersisa')"
                    class="p-2 mb-2  flex justify-center items-center">
                    <span class="icon-[ic--baseline-plus] w-5 h-5"></span>
                </x-button>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-1 overflow-y-auto max-h-[400px]">
                    @foreach ($dataAlatSisa as $alatTersisa)
                        <div class="flex border border-blue-500">
                            <form action="{{ route('inventaris.kategori.consumable.update', $alatTersisa->id) }}"
                                method="post" class="flex w-full">
                                @csrf
                                <input type="text" id="small-input" name="nama_kategori_tersisa" required
                                    class="block w-full p-2 text-sm text-gray-900 border border-gray-300 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    value="{{ $alatTersisa->nama_kategori }}">
                                <x-button type="submit"
                                    class="p-1.5 flex items-center rounded-none focus:ring-0 justify-center">
                                    <span class="icon-[iconamoon--check-bold] w-6 h-6"></span>
                                </x-button>
                            </form>
                            <form action="{{ route('inventaris.kategori.consumable.delete', $alatTersisa->id) }}"
                                class="">
                                @include('inventaris.partials.modals.pengaturan.hapus-alat-tersisa')
                                <x-danger-button type="button"
                                    x-on:click.prevent="$dispatch('open-modal', 'hapus-alat-tersisa-{{ $alatTersisa->id }}')"
                                    class="p-1.5 w-full h-full flex items-center rounded-none focus:ring-0 justify-center">
                                    <span class="icon-[mdi--trash-outline] w-6 h-6"></span>
                                </x-danger-button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
