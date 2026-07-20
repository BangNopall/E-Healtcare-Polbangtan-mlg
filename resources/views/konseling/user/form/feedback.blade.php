<x-app-layout>
    {{-- Content Header --}}
    <div class="flex justify-center px-2 sm:px-4 py-3 lg:py-5 border-b dark:border-blue-800">
        @include('konseling.partials.stepper')
    </div>
    {{-- Main Content --}}
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
                        <form action="{{ route('user.konseling.store-feedback') }}" method="POST">
                            @csrf
                            {{-- @method('PATCH') --}}
                            <div class="mt-3 bg-gray-100 dark:bg-dark p-3 rounded">
                                <div class="grid gap-2 md:grid-cols-2">
                                    <div>
                                        <x-input-label for="materi" :value="'Judul Materi'" />
                                        <x-input-text id="materi" name="materi" type="text"
                                            class="mt-2 block w-full cursor-not-allowed"
                                            value="{{ $jadwal->materi }}" required disabled readonly />
                                    </div>
                                    <div>
                                        <x-input-label for="jadwal" :value="'Jadwal'" />
                                        <x-input-text id="jadwal" name="jadwal" type="text"
                                            class="mt-2 block w-full cursor-not-allowed" value="{{ \Carbon\Carbon::parse($jadwal->tanggal)->isoFormat('D MMMM Y') }}" required
                                            disabled readonly />
                                    </div>
                                    <div class="hidden">
                                        <input type="text" class="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                                        <input type="text" class="hidden" name="senso_id" value="{{ $senso->id }}">
                                        <input type="text" class="hidden" name="siswa_id" value="{{ auth()->id() }}">
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 bg-gray-100 dark:bg-dark p-3 rounded">
                                <div class="grid gap-2 md:grid-cols-2 mt-2">
                                    {{-- <div>
                                        <x-input-label for="keluhan" :value="'Keluhan'" />
                                        <textarea name="keluhan" id="keluhan" cols="5" rows="5" required
                                            class="block w-full mt-2 items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"></textarea>
                                        <x-input-error class="mt-1" :messages="$errors->get('keluhan')" />
                                    </div> --}}
                                    <div>
                                        <x-input-label for="senso" :value="'Pembimbing'" />
                                        <x-input-text id="senso" name="senso" type="text"
                                            class="mt-2 block w-full cursor-not-allowed" value="{{ $senso->name }}" required disabled readonly />
                                    </div>
                                    <div>
                                        <x-input-label for="feedback" :value="'Feedback'" />
                                        <textarea name="feedback" id="feedback" cols="5" rows="5" required
                                            class="block w-full mt-2 items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"></textarea>
                                        <x-input-error class="mt-1" :messages="$errors->get('feedback')" />
                                    </div>
                                </div>
                                <div class="flex items-start my-3">
                                    <div class="flex items-center h-5">
                                        <input id="remember-surat" type="checkbox" value=""
                                            class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-600 dark:ring-offset-gray-800"
                                            required />
                                    </div>
                                    <label for="remember-surat"
                                        class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Saya setuju
                                        bahwa
                                        data ini benar dan sesuai. <span class="text-red-500">Tidak dapat melakukan
                                            perubahan data setelah di simpan!</span></label>
                                </div>
                                <x-button class="py-2 px-4" type="submit">{{ __('Simpan') }}</x-button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
    <div class="px-2 sm:px-4 text-right pb-3">
        @include('konseling.partials.modals.batal')
        <div class="max-w-screen-xl mx-auto flex gap-2 justify-end">
            <x-danger-button class="py-2 px-4" type="button"
                x-on:click.prevent="$dispatch('open-modal', 'batal');">{{ __('Batal') }}</x-danger-button>
        </div>
    </div>
</x-app-layout>
