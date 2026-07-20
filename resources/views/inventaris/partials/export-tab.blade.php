<div x-show="activeTab === 'export'"
    class="p-6 bg-white text-medium text-gray-500 dark:text-gray-400 dark:bg-darker rounded-lg w-full">
    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Export Tab</h3>
    <p class="mb-2 text-sm">
        Anda dapat melakukan pengambilan data dengan mengunduh berdasarkan filter dibawah.
    </p>
        <form method="POST" action="/inventaris/pengaturan/print">
            @csrf
            <div class="flex flex-col sm:flex-row gap-2">
                <div>
                    <x-input-label for="kuantitas" class="mb-1" required>Jenis Data</x-input-label>
                    <select id="countries" name="jenis_data" required
                        class="block bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 w-full dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 p-2">
                        <option hidden value="">Pilih</option>
                        <option value="obat">Obat</option>
                        <option value="alat_terpakai">Alat Terpakai</option>
                        <option value="alat_tersisa">Alat Tersisa</option>
                    </select>
                </div>
            </div>
            <x-button class="p-2 mt-3" type="submit">Unduh Data</x-button>
        </form>
</div>
