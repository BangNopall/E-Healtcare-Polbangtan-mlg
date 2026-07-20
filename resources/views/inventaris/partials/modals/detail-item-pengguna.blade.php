<x-modal name="detail-item-pengguna">
    <div class="p-6">
        <!-- Modal header -->
        <div class="flex justify-between mb-4 rounded-t">
            <div class="text-lg text-gray-900 md:text-xl dark:text-white">
                <h3 class="font-semibold ">
                    Detail Item
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
            <dt class="mb-2 font-semibold leading-none text-gray-900 dark:text-white">Pengguna</dt>
            <dd class="mb-4 font-light text-gray-500 sm:mb-5 dark:text-gray-400">Nopal</dd>
            <dt class="mb-2 font-semibold leading-none text-gray-900 dark:text-white">Kuantitas</dt>
            <dd class="mb-4 font-light text-gray-500 sm:mb-5 dark:text-gray-400">1</dd>
            <dt class="mb-2 font-semibold leading-none text-gray-900 dark:text-white">Tanggal</dt>
            <dd class="mb-4 font-light text-gray-500 sm:mb-5 dark:text-gray-400 whitespace-normal">
                Produksi : 20-20-2024 <br>
                Kadaluarsa : 20-20-2024
            </dd>
            Aku sek bingung isi e cak
        </dl>
    </div>
</x-modal>
