<x-app-layout>
    {{-- Content Header --}}
    <div class="px-2 sm:px-4 py-3 lg:py-5 border-b dark:border-blue-800">
        <h1 class="text-2xl font-semibold">Riwayat Feedback</h1>
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
                                Feedback Bimbingan
                            </h2>
                        </header>
                        <div class="mt-3 bg-gray-100 dark:bg-dark p-3 rounded">
                            <div class="grid gap-2 md:grid-cols-2">
                                <div>
                                    <x-input-label for="materi" :value="'Judul Materi'" />
                                    <x-input-text id="materi" name="materi" type="text"
                                        class="mt-2 block w-full cursor-not-allowed"
                                        :value="old('materi', $feedback->jadwal->materi)" required
                                        disabled readonly />
                                </div>
                                <div>
                                    <x-input-label for="jadwal" :value="'Jadwal'" />
                                    <x-input-text id="jadwal" name="jadwal" type="date"
                                        class="mt-2 block w-full cursor-not-allowed" :value="old('tanggal', $feedback->jadwal->tanggal)" required
                                        disabled readonly />
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 bg-gray-100 dark:bg-dark p-3 rounded">
                            <div class="grid gap-2 md:grid-cols-2">
                                <div>
                                    <x-input-label for="senso" :value="'Pembimbing'" />
                                    <x-input-text id="senso" name="senso" type="text"
                                        class="mt-2 block w-full cursor-not-allowed" value="{{ $feedback->senso->name }}" required disabled readonly />
                                </div>
                                {{-- <div>
                                    <x-input-label for="keluhan" :value="'Keluhan'" />
                                    <textarea name="keluhan" id="keluhan" cols="5" rows="5" readonly disabled
                                        class="block w-full mt-2 cursor-not-allowed items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">Trauma</textarea>
                                </div> --}}
                                <div>
                                    <x-input-label for="feedback" :value="'Feedback'" />
                                    <textarea name="feedback" id="feedback" cols="5" rows="5" readonly disabled
                                        class="block w-full mt-2 cursor-not-allowed items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">{{ $feedback->feedback }}</textarea>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
