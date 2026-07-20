<x-modal name="detail-item">
    <div class="p-6">
        <!-- Modal header -->
        <div class="flex justify-between mb-4 rounded-t">
            <div class="text-lg text-gray-900 md:text-xl dark:text-white">
                <h3 class="font-semibold ">
                    Buang Item
                </h3>
            </div>
            <div>
                <button type="button" x-on:click="$dispatch('close')"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 inline-flex dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-toggle="readProductModal">
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
        </div>
        <dl>
            <dt class="mb-2 font-semibold leading-none text-gray-900 dark:text-white">Deskripsi</dt>
            <dd class="mb-4 font-light text-gray-500 sm:mb-5 dark:text-gray-400 whitespace-normal">
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim doloribus tenetur odit quaerat possimus consectetur est dolor iusto magni veniam?
            </dd>
            <dt class="mb-2 font-semibold leading-none text-gray-900 dark:text-white">Kuantitas</dt>
            <dd class="mb-4 font-light text-gray-500 sm:mb-5 dark:text-gray-400">100</dd>
        </dl>
        <div class="flex justify-between items-center">
            <button type="sumbit"
                class="inline-flex items-center text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-500 dark:hover:bg-red-600 dark:focus:ring-red-900">
                <svg aria-hidden="true" class="w-5 h-5 mr-1.5 -ml-1" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                        clip-rule="evenodd"></path>
                </svg>
                Hapus
            </button>
        </div>
    </div>
</x-modal>
