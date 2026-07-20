<x-app-layout>
    {{-- Content Header --}}
    <div class="px-2 sm:px-4 py-3 lg:py-5 border-b dark:border-blue-800">
        <h1 class="text-2xl font-semibold">Data Sensuh</h1>
        <span class="text-gray-700 dark:text-gray-300 text-sm mt-2">Muhammad Naufal Mathara</span>
    </div>
    {{-- Main Content --}}
    <div class="px-2 sm:px-4 py-3 lg:py-5">
        <div class="flex flex-col md:flex-row justify-center items-center sm:items-start gap-2 mt-2">
            <div class="p-4 bg-white dark:bg-darker sm:rounded-lg w-full md:w-auto">
                <div class="w-full md:max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Akun Profil') }}
                            </h2>
                        </header>
                        <div class="my-3">
                            @if ($user->avatar_url == null)
                                <div class="object-cover flex justify-center w-full md:w-[250px] p-1 rounded-md">
                                    <img src="https://placehold.co/500x500" alt="Preview"
                                        class="object-cover w-[200px] p-1 rounded-md ring-2 ring-indigo-300 dark:ring-darkerhover">
                                </div>
                            @else
                                <div class="object-cover flex justify-center w-full md:w-[250px] p-1 rounded-md">
                                    <img src="{{ Storage::url('images/' . $user->avatar_url) }}" alt="Preview"
                                        class="object-cover w-[200px] p-1 rounded-md ring-2 ring-indigo-300 dark:ring-darkerhover">
                                </div>
                            @endif
                        </div>
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="name" :value="'Nama Lengkap'" />
                                <x-input-text id="name" name="name" type="text"
                                    class="mt-2 block w-full cursor-not-allowed" :value="old('name', isset($user) ? $user->name : '')" disabled readonly
                                    placeholder="Kosong" />
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            <div class="flex flex-col gap-2 w-full md:w-[930px]">
                <div class="p-4 bg-white dark:bg-darker sm:rounded-lg">
                    <div class="w-full">
                        <section>
                            <header>
                                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {{ __('Informasi Mahasiswa') }}
                                </h2>
                            </header>
                            <div class="mt-2 space-y-4">
                                <div>
                                    <x-input-label for="nim" :value="'NIM'" />
                                    <x-input-text id="nim" name="nim" type="number"
                                        class="mt-2 block w-full cursor-not-allowed" :value="old('nim', isset($cdmi) ? $cdmi->nim : '')" disabled readonly
                                        placeholder="Kosong" />
                                </div>
                                <div>
                                    <x-input-label for="prodi" :value="'Program Studi'" />
                                    <x-input-text id="prodi" name="prodi" type="text"
                                        class="mt-2 block w-full cursor-not-allowed" :value="old('prodi', isset($cdmi) ? $cdmi->prodi->name : '')" disabled readonly
                                        placeholder="Kosong" />
                                </div>
                                <div>
                                    <x-input-label for="blok" :value="'Blok Gedung'" />
                                    <x-input-text id="blok" name="blok" disabled readonly type="text"
                                        class="mt-2 block w-full cursor-not-allowed" :value="old('blok', isset($cdmi) ? $cdmi->blok->name : '')"
                                        placeholder="Kosong" />
                                </div>
                                <div>
                                    <x-input-label for="no_ruangan" :value="'Nomor Ruangan'" />
                                    <x-input-text id="no_ruangan" disabled readonly name="no_ruangan" type="number"
                                        class="mt-2 block w-full cursor-not-allowed" :value="old('no_ruangan', isset($cdmi) ? $cdmi->no_ruangan : '')"
                                        placeholder="Kosong" />
                                </div>
                            </div>
                        </section>
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
                                Data Anak Asuh Sensuh
                            </h2>
                        </header>
                        <div class="mt-3 bg-gray-100 dark:bg-dark p-3 rounded">
                            <form action="{{ route('konseling.daftarsiswaAsuh', $user->id) }}" method="post">
                                @csrf
                                <div class="flex flex-col sm:flex-row gap-2 justify-between">
                                    <div class="w-auto sm:w-[500px]">
                                        <x-input-label for="name" class="mb-2">Nama Mahasiswa</x-input-label>
                                        <div x-data="selectConfigs()" x-init="fetchOptions()"
                                            class="flex flex-col items-center relative">
                                            <x-input-text @click.away="close()" x-model="filter"
                                                x-transition:leave="transition ease-in duration-100"
                                                x-transition:leave-start="opacity-100"
                                                x-transition:leave-end="opacity-0" @mousedown="open()"
                                                @keydown.enter.stop.prevent="selectOption()"
                                                @keydown.arrow-up.prevent="focusPrevOption()"
                                                @keydown.arrow-down.prevent="focusNextOption()" type="search"
                                                id="name" name="name" class="p-2"
                                                placeholder="Nama Mahasiswa" />
                                            <div x-show="isOpen()"
                                                class="absolute shadow-lg bg-white dark:bg-darker top-100 z-40 w-full left-0 top-11 rounded max-h-[150px] overflow-y-auto">
                                                <div class="flex flex-col w-full">
                                                    <template x-for="(option, index) in filteredOptions()"
                                                        :key="index">
                                                        <div @click="onOptionClick(index)"
                                                            :class="classOption(option.id, index)"
                                                            :aria-selected="focusedOptionIndex === index">
                                                            <div
                                                                class="w-full items-center p-2 pl-2 border-transparent border-l-2 relative hover:border-teal-100 dark:hover:border-darkerhover">
                                                                <div class="w-full items-center flex">
                                                                    <span x-text="option.name"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="text" id="user_id" class="hidden" name="user_id">
                                    </div>
                                    <div class="flex items-end">
                                        <x-button type="submit" class="inline-flex items-center px-3 py-2">
                                            <svg class="mr-1 -ml-1 w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd"
                                                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            Tambah
                                        </x-button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="mt-3 bg-gray-100 dark:bg-dark p-3 rounded">
                            <div class="overflow-x-auto shadow-md sm:rounded-lg">
                                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                    <thead
                                        class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-darker dark:text-gray-400">
                                        <tr class="whitespace-nowrap">
                                            <th scope="col" class="px-4 py-3">
                                                #
                                            </th>
                                            <th scope="col" class="px-4 py-3">
                                                NIM
                                            </th>
                                            <th scope="col" class="px-4 py-3">
                                                Nama
                                            </th>
                                            <th scope="col" class="px-4 py-3">
                                                Email
                                            </th>
                                            <th scope="col" class="px-4 py-3">
                                                Prodi
                                            </th>
                                            <th scope="col" class="px-4 py-3">

                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="result">
                                        <x-konseling.table-data-anak-senso :data="$anakSenso" />
                                    </tbody>
                                </table>
                            </div>
                            {{-- pagination --}}
                            {{-- <nav class="flex items-center flex-column flex-wrap md:flex-row justify-between mt-2"
                                aria-label="Table navigation">
                                <span
                                    class="text-sm font-normal text-gray-500 dark:text-gray-400 mb-4 md:mb-0 block w-full md:inline md:w-auto">Showing
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $sensos->firstItem() }} -
                                        {{ $sensos->lastItem() }}</span> of <span
                                        class="font-semibold text-gray-900 dark:text-white">{{ $sensos->total() }}</span></span>
                                <ul class="inline-flex -space-x-px text-sm h-8">
                                    <li>
                                        @if ($sensos->onFirstPage())
                                            <span
                                                class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-s-lg dark:bg-darker dark:border-gray-700 dark:text-gray-400 dark:hover:bg-[#1a304a] dark:hover:text-white">
                                                <del>
                                                    Previous
                                                </del>
                                            </span>
                                        @else
                                            <a href="{{ $sensos->previousPageUrl() }}"
                                                class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-darker dark:border-gray-700 dark:text-gray-400 dark:hover:bg-[#1a304a] dark:hover:text-white">Previous</a>
                                        @endif
                                    </li>
                                    @foreach ($sensos->getUrlRange($sensos->currentPage() - 1, $sensos->currentPage() + 1) as $num => $url)
                                        <li>
                                            <a href="{{ $url }}"
                                                class="flex items-center justify-center px-3 h-8 leading-tight border border-gray-300 {{ $num == $sensos->currentPage() ? 'text-blue-600 bg-blue-50 dark:bg-[#1a304a] dark:border-gray-700 dark:text-white dark:hover:bg-[#1a304a] dark:hover:text-white' : 'hover:text-blue-700 hover:bg-blue-50 border-gray-300 bg-white text-gray-500 dark:bg-darker dark:border-gray-700 dark:text-gray-400 dark:hover:bg-[#1a304a] dark:hover:text-white' }}">{{ $num }}</a>
                                        </li>
                                    @endforeach
                                    <li>
                                        @if ($sensos->hasMorePages())
                                            <a href="{{ $sensos->nextPageUrl() }}"
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
                    </section>
                </div>
            </div>
        </div>
    </div>
    <div id="url_dataSiswaNoSenso" class="hidden"></div>
    <script>
        const url = '{{ route('api.userNoSensoNoAnakAsuh') }}';
        const element = document.getElementById('url_dataSiswaNoSenso');
        element.textContent = url;
    </script>
    <script src="{{ asset('src/js/konseling/detail-data-senso.js') }}"></script>
</x-app-layout>
