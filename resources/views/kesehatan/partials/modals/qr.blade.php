<x-modal name="qr">
    <div class="flex justify-center items-center relative p-4 sm:p-5 w-full h-full md:h-auto">
        <!-- Modal content -->
        <div class="relative text-centerw-full">
            <h2 class="text-2xl font-semibold mb-4 text-gray-800 dark:text-white text-center">Kode QR</h2>
            <div class="flex flex-col gap-3 justify-center items-center mb-4">
                <div class="bg-cover bg-white p-3 object-fill inisvg">
                    {{ $QrCode }}
                </div>
            </div>
            <p class="text-gray-600 dark:text-white text-sm text-center">Pindai kode QR untuk menjalani prosedur Klinik
                Polbangtan-mlg</p>
            <div class="mt-5 flex justify-center">
                <x-secondary-button x-on:click="$dispatch('close')" type="button" class="py-3 text-lg px-6">
                    Tutup
                </x-secondary-button>
            </div>
        </div>
    </div>
</x-modal>
