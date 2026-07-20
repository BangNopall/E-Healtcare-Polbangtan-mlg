<x-modal name="deposit-detail">
    <div class="relative p-4 sm:p-5 w-full max-w-2xl h-full md:h-auto">
        <!-- Modal content -->
        <div class="relative">
            <!-- Modal header -->
            <div class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Deposit {{ $data->kode_obat }}
                </h3>
                <button type="button" x-on:click="$dispatch('close')"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-dark dark:hover:text-white">
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <form action="{{ route('inventaris.obat.deposit', $data->id) }}" method="post" id="additemform">
                @csrf
                {{-- <input type="hidden" name="obat_id" value="{{ $data->id }}"> --}}
                <div class="grid gap-4 mb-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="kuantitas" class="mb-2">Kuantitas</x-input-label>
                        <x-input-text type="number" name="Qty" id="kuantitas" class="p-2.5"
                            placeholder="10" required="" value="{{ old('Qty') }}"/>
                    </div>
                    <div>
                        <x-input-label for="production_date" class="mb-2">Produksi</x-input-label>
                        <x-input-text type="date" name="production_date" id="production_date" class="p-2.5"
                            placeholder="20-02-2024" required="" value="{{ old('production_date') }}"/>
                    </div>
                    <div>
                        <x-input-label for="expired_date" class="mb-2">Expired</x-input-label>
                        <x-input-text type="date" name="expired_date" id="expired_date" class="p-2.5"
                            placeholder="20-02-2024" required="" value="{{ old('expired_date') }}"/>
                    </div>
                </div>
                <x-button type="submit" class="inline-flex items-center px-4 py-2">
                    <svg class="mr-1 -ml-1 w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                    Deposit
                </x-button>
            </form>
        </div>
    </div>
</x-modal>
