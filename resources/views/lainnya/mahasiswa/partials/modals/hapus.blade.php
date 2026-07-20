{{-- hapus --}}
<x-modal name="hapus-{{ $mhs->id }}">
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
            <span class="icon-[ph--trash-bold] text-gray-400 dark:text-gray-300 w-11 h-11 mb-3.5 mx-auto"></span>
            <p class="mb-4 text-gray-500 dark:text-gray-300">Apakah Anda yakin menghapus <span class="font-semibold">{{ $mhs->name }}</span></p>
            <div class="flex justify-center items-center space-x-4">
                <x-secondary-button x-on:click="$dispatch('close')" type="button" class="py-2 px-3">
                    Batal
                </x-secondary-button>
                <form action="{{ route('lainnya.mahasiswa.destroy', $mhs->id) }}" method="POST">
                    @csrf
                    <x-danger-button class="py-2 px-3" type="submit">
                        Hapus
                    </x-danger-button>
                </form>
            </div>
        </div>
    </div>
</x-modal>
