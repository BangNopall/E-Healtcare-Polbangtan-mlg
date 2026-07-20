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
                        <div class="mt-3 bg-gray-100 dark:bg-dark p-3 rounded">
                            <div class="grid gap-2 md:grid-cols-2">
                                <div>
                                    <x-input-label for="materi" :value="'Judul Materi'" />
                                    <x-input-text id="materi" name="materi" type="text"
                                        class="mt-2 block w-full cursor-not-allowed"
                                        value="{{ $feedback->jadwal->materi }}" required disabled readonly />
                                </div>
                                <div>
                                    <x-input-label for="jadwal" :value="'Jadwal'" />
                                    <x-input-text id="jadwal" name="jadwal" type="date"
                                        class="mt-2 block w-full cursor-not-allowed"
                                        value="{{ $feedback->jadwal->tanggal }}" required disabled readonly />
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 bg-gray-100 dark:bg-dark p-3 rounded">
                            <div class="grid gap-2 md:grid-cols-2 mt-2">
                                <div>
                                    <x-input-label for="senso" :value="'Pembimbing'" />
                                    <x-input-text id="senso" name="senso" type="text"
                                        class="mt-2 block w-full cursor-not-allowed"
                                        value="{{ $feedback->senso->name }}" required disabled readonly />
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
    <div class="px-2 sm:px-4 text-right pb-3">
        <div class="max-w-screen-xl mx-auto flex gap-2 justify-end">
            <a href="{{ route('user.konseling.link-feedback') }}">
                <x-button class="py-2 px-4" type="button">{{ __('Halaman Riwayat') }}</x-button>
            </a>
        </div>
    </div>
</x-app-layout>
