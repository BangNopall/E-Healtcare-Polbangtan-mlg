<form action="{{ route('lainnya.karyawan.filter') }}" method="post" id="form-filter">
    @csrf
    <div class="flex flex-col sm:flex-row justify-between">
        <div class="w-full sm:w-[300px]">
            <div class="space-y-1">
                <x-input-label for="user" class="mb-2">Cari Data Karyawan</x-input-label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-2 pointer-events-none">
                        <span class="icon-[material-symbols--search] w-4 h-4 text-gray-500 dark:text-gray-400"></span>
                    </div>
                    <x-input-text type="search" id="filter" name="filter" class="w-full p-2 ps-8"
                        placeholder="Cari pengguna dan email" />
                </div>
            </div>
            <button type="submit" class="hidden" id="button-filter"></button>
        </div>
        {{-- @isset($x) --}}
        <div class="flex items-end mt-2 lg:mt-0 ml-0 lg:ml-auto">
            <x-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'tambah')" type="button"
                class="p-2">
                Tambah
            </x-button>
        </div>
        {{-- @endisset --}}
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
        $('#filter').on('input', function() {
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
