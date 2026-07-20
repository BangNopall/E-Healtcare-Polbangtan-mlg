<x-app-layout>
    {{-- Content Header --}}
    <div class="px-2 sm:px-4 py-3 lg:py-5 border-b dark:border-blue-800">
        <h1 class="text-2xl font-semibold">Riwayat Konsultasi</h1>
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
                            <div class="flex justify-between">
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                    Konsultasi Konseling
                                </h2>
                                <div class="flex items-end mt-2 lg:mt-0 ml-0 lg:ml-auto">
                                    <form action="{{ route('konseling.request-surat-rujukan', $dataPsikolog->id) }}" method="POST">
                                        @csrf
                                        <x-button type="submit" class="p-2">
                                            Request Rujukan
                                        </x-button>
                                    </form>
                                </div>
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
