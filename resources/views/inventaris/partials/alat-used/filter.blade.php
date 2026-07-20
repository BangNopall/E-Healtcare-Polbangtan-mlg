<form action="{{ route('inventaris.alat.filter') }}" method="post" id="form-filter">
    @csrf
    <div class="flex flex-col lg:flex-row justify-between">
        <div class="flex flex-wrap gap-1">
            <div class="space-y-1">
                <x-input-label for="nama_alat" class="mb-2">Nama Alat</x-input-label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-2 pointer-events-none">
                        <span class="icon-[material-symbols--search] w-4 h-4 text-gray-500 dark:text-gray-400"></span>
                    </div>
                    <x-input-text type="search" id="nama_alat" name="nama_alat" class="p-2 ps-8"
                        placeholder="Nama item" />
                </div>
            </div>
            <div class="space-y-1">
                <x-input-label for="itemID" class="mb-2">Item ID</x-input-label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-2 pointer-events-none">
                        <span class="icon-[material-symbols--search] w-4 h-4 text-gray-500 dark:text-gray-400"></span>
                    </div>
                    <x-input-text type="search" id="itemID" name="itemID" class="p-2 ps-8" placeholder="Item ID" />
                </div>
            </div>
            <div class="space-y-1">
                <x-input-label for="kategori_alat" class="mb-2">Kategori</x-input-label>
                <select id="kategori_alat" name="kategori_alat"
                    class="block p-2 items-center text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-darker dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option selected hidden>Pilih</option>
                    <option value="">Not Filtered</option>
                    @foreach ($kategori as $kategori_alat)
                        <option value="{{ $kategori_alat['id'] }}"
                            {{ old('kategori_alatID') == $kategori_alat['id'] ? 'selected' : '' }}>
                            {{ $kategori_alat['nama_kategori'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="hidden" id="button-filter"></button>
        </div>
        <div class="flex items-end mt-2 lg:mt-0 ml-0 lg:ml-auto">
            <x-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'tambah-item')" type="button"
                class="p-2">
                Tambah Item
            </x-button>
        </div>
    </div>
</form>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        var typingTimer; // Timer untuk debouncing
        var doneTypingInterval = 1000; // Waktu tunggu (dalam milidetik)

        // Fungsi untuk mengirim permintaan ke server
        function sendRequest() {
            var formData = $('#form-filter').serialize(); // Mengambil data formulir

            // Kirim permintaan AJAX
            $.ajax({
                type: "POST",
                url: $('#form-filter').attr('action'), // URL dari formulir
                data: formData, // Data yang dikirimkan
                success: function(response) {
                    // Update tabel dengan data yang diterima dari server
                    if (response.table) {
                        $('#result').html(response.table);
                    } else {
                        console.error("Tidak ada data tabel dalam respons JSON");
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }

        // Ketika pengguna mengetik di input form
        $('#nama_alat, #itemID, #kategori_alat').on('input', function() {
            clearTimeout(typingTimer); // Hentikan timer yang sedang berjalan
            typingTimer = setTimeout(sendRequest, doneTypingInterval); // Mulai ulang hitung mundur
        });

        // Ketika pengguna menekan tombol Enter
        $('#form-filter').on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                clearTimeout(typingTimer); // Hentikan timer yang sedang berjalan
                sendRequest(); // Kirim permintaan ke server
            }
        });
    });
</script>
