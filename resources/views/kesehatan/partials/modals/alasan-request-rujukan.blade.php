{{-- alasan --}}
<x-modal name="alasan-{{ $request->id }}">
    <div class="flex justify-center items-center relative p-4 w-full h-full md:h-auto">
        <!-- Modal content -->
        <div class="relative p-4 text-center sm:p-5 w-full">
            <button type="button"
                class="text-gray-400 absolute top-2.5 right-2.5 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-darker dark:hover:text-white"
                x-on:click="$dispatch('close')">
                <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd"></path>
                </svg>
                <span class="sr-only">Close modal</span>
            </button>
            <div>
                <x-input-label for="alasan" :value="'Alasan Penolakan'" />
                <textarea name="alasan" id="alasan" cols="4" rows="4" required disabled readonly
                    class="block w-full mt-2 cursor-not-allowed items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">@isset($request->alasan_penolakan){{ $request->alasan_penolakan }}@endisset</textarea>
            </div>
            <div class="flex justify-center items-center space-x-4 mt-2">
                <x-secondary-button x-on:click="$dispatch('close')" type="button" class="py-2 px-3">
                    Tutup
                </x-secondary-button>
            </div>
        </div>
    </div>
</x-modal>