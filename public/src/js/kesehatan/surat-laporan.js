$(document).ready(function () {
    var typingTimer; // Timer untuk debouncing
    var doneTypingInterval = 1000; // Waktu tunggu (dalam milidetik)

    // Fungsi untuk mengirim permintaan ke server
    function sendRequest() {
        var formData = $("#form-filter-rm").serialize(); // Mengambil data formulir

        // Kirim permintaan AJAX
        $.ajax({
            type: "POST",
            url: $("#form-filter-rm").attr("action"), // URL dari formulir
            data: formData, // Data yang dikirimkan
            success: function (response) {
                // Update tabel dengan data yang diterima dari server
                if (response.table) {
                    $("#result-rm").html(response.table);
                } else {
                    console.error("Tidak ada data tabel dalam respons JSON");
                }
            },
            error: function (xhr, status, error) {
                console.error(error);
            },
        });
    }

    // Ketika pengguna mengetik di input form
    $("#filter-rm, #daysrm").on("input", function () {
        clearTimeout(typingTimer); // Hentikan timer yang sedang berjalan
        typingTimer = setTimeout(sendRequest, doneTypingInterval); // Mulai ulang hitung mundur
    });

    // Ketika pengguna menekan tombol Enter
    $("#form-filter-rm").on("keyup keypress", function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            clearTimeout(typingTimer); // Hentikan timer yang sedang berjalan
            sendRequest(); // Kirim permintaan ke server
        }
    });
});

$(document).ready(function () {
    var typingTimer; // Timer untuk debouncing
    var doneTypingInterval = 1000; // Waktu tunggu (dalam milidetik)

    // Fungsi untuk mengirim permintaan ke server
    function sendRequest() {
        var formData = $("#form-filter-ks").serialize(); // Mengambil data formulir

        // Kirim permintaan AJAX
        $.ajax({
            type: "POST",
            url: $("#form-filter-ks").attr("action"), // URL dari formulir
            data: formData, // Data yang dikirimkan
            success: function (response) {
                // Update tabel dengan data yang diterima dari server
                if (response.table) {
                    $("#result-ks").html(response.table);
                } else {
                    console.error("Tidak ada data tabel dalam respons JSON");
                }
            },
            error: function (xhr, status, error) {
                console.error(error);
            },
        });
    }

    // Ketika pengguna mengetik di input form
    $("#filter-ks, #daysks").on("input", function () {
        clearTimeout(typingTimer); // Hentikan timer yang sedang berjalan
        typingTimer = setTimeout(sendRequest, doneTypingInterval); // Mulai ulang hitung mundur
    });

    // Ketika pengguna menekan tombol Enter
    $("#form-filter-ks").on("keyup keypress", function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            clearTimeout(typingTimer); // Hentikan timer yang sedang berjalan
            sendRequest(); // Kirim permintaan ke server
        }
    });
});
