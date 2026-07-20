// Array of days in Indonesian
const days = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];

// Array of months in Indonesian
const months = [
    "Januari",
    "Februari",
    "Maret",
    "April",
    "Mei",
    "Juni",
    "Juli",
    "Agustus",
    "September",
    "Oktober",
    "November",
    "Desember",
];

// Get today's date
const today = new Date();

// Get the day, date, month, and year
const dayName = days[today.getDay()];
const day = today.getDate();
const month = months[today.getMonth()];
const year = today.getFullYear();

// Format the date in dd mm yyyy format
const formattedDate = `${day} ${month} ${year}`;

// Set the content of the elements with id "today" and "datenow"
document.getElementById("today").textContent = dayName;
document.getElementById("datenow").textContent = formattedDate;


function selectConfigs() {
    return {
        filter: "",
        show: false,
        selected: null,
        focusedOptionIndex: null,
        options: null,
        close() {
            this.show = false;
            this.filter = this.selectedName();
            this.focusedOptionIndex = this.selected
                ? this.focusedOptionIndex
                : null;
        },
        open() {
            this.show = true;
            this.filter = "";
        },
        toggle() {
            if (this.show) {
                this.close();
            } else {
                this.open();
            }
        },
        isOpen() {
            return this.show === true;
        },
        selectedName() {
            return this.selected ? this.selected.nama_obat : this.filter;
        },
        classOption(id, index) {
            const isSelected = this.selected ? id == this.selected.id : false;
            const isFocused = index == this.focusedOptionIndex;
            return {
                "cursor-pointer w-full border-blue-600 border-b hover:bg-blue-50 dark:hover:bg-dark": true,
                "bg-blue-100 dark:bg-dark": isSelected,
                "bg-blue-50 dark:bg-dark": isFocused,
            };
        },
        fetchOptions() {
            this.options = {
                results: [],
            };
            const url = document.getElementById("url_obat").textContent;
            fetch(url)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error("Gagal mengambil data kategori obat!");
                    }
                    return response.json();
                })
                .then((data) => {
                    this.options.results = data;
                })
                .catch((error) => {
                    console.error("Error:", error);
                });
        },
        filteredOptions() {
            return this.options
                ? this.options.results.filter((option) => {
                      const filterText = this.filter.toLowerCase().trim();
                      const nama_obat = option.nama_obat.toLowerCase();
                      return nama_obat.includes(filterText);
                  })
                : {};
        },
        onOptionClick(index) {
            this.focusedOptionIndex = index;
            this.selectOption();
            document.getElementById("obat_id").value = this.selected.id;
        },
        selectOption() {
            if (!this.isOpen()) {
                return;
            }
            this.focusedOptionIndex = this.focusedOptionIndex ?? 0;
            const selected = this.filteredOptions()[this.focusedOptionIndex];
            if (this.selected && this.selected.id == selected.id) {
                this.filter = "";
                this.selected = null;
            } else {
                this.selected = selected;
                this.filter = this.selectedName();
            }
            this.close();
        },
        focusPrevOption() {
            if (!this.isOpen()) {
                return;
            }
            const optionsNum = Object.keys(this.filteredOptions()).length - 1;
            if (
                this.focusedOptionIndex > 0 &&
                this.focusedOptionIndex <= optionsNum
            ) {
                this.focusedOptionIndex--;
            } else if (this.focusedOptionIndex == 0) {
                this.focusedOptionIndex = optionsNum;
            }
        },
        focusNextOption() {
            const optionsNum = Object.keys(this.filteredOptions()).length - 1;
            if (!this.isOpen()) {
                this.open();
            }
            if (
                this.focusedOptionIndex == null ||
                this.focusedOptionIndex == optionsNum
            ) {
                this.focusedOptionIndex = 0;
            } else if (
                this.focusedOptionIndex >= 0 &&
                this.focusedOptionIndex < optionsNum
            ) {
                this.focusedOptionIndex++;
            }
        },
    };
}

let configs;

function init() {
    configs = selectConfigs();
    configs.fetchOptions();
}

function tambahObat() {
    const obatNama = document.getElementById("obat_id").value;
    const kuantitas = document.getElementById("kuantitas").value;

    const obat = configs.options.results.find(
        (option) => option.nama_obat.toLowerCase() == obatNama.toLowerCase()
    );

    if (!obat) {
        console.error(`Tidak ada obat dengan nama ${obatNama}`);
        return;
    }

    // Membuat elemen list baru
    const li = document.createElement("li");
    li.textContent = `${obatNama} ${kuantitas}x`;

    // membuat input hidden untuk menyimpan id obat dan kuantitas
    const inputObat = document.createElement("input");
    inputObat.type = "hidden";
    inputObat.name = "obat_id[]";
    inputObat.value = obat.id;

    const inputKuantitas = document.createElement("input");
    inputKuantitas.type = "hidden";
    inputKuantitas.name = "kuantitas[]";
    inputKuantitas.value = kuantitas;

    // Menambahkan elemen list baru ke daftar permintaan obat
    const daftarPermintaanObat = document.getElementById(
        "daftar-permintaan-obat"
    );
    daftarPermintaanObat.appendChild(li);
    daftarPermintaanObat.appendChild(inputObat);
    daftarPermintaanObat.appendChild(inputKuantitas);

    // Mengosongkan input obat dan kuantitas
    document.getElementById("obat_id").value = "";
    document.getElementById("kuantitas").value = "";
}

// Panggil 'init' saat halaman dimuat
window.onload = init;
