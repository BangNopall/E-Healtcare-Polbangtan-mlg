function updateDiagnosa() {
    // Mendapatkan nilai input dari elemen input
    var inputDiagnosa = document.getElementById("diagnosa").value;

    // Memperbarui teks pada elemen span dengan nilai input
    document.getElementById("cekDiagnosa").textContent = inputDiagnosa;
}

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

function selectConfigsAlat() {
    return {
        filterAlat: "",
        show: false,
        selected: null,
        focusedOptionIndexAlat: null,
        options: null,
        closeAlat() {
            this.show = false;
            this.filterAlat = this.selectedName();
            this.focusedOptionIndexAlat = this.selected
                ? this.focusedOptionIndexAlat
                : null;
        },
        open() {
            this.show = true;
            this.filterAlat = "";
        },
        toggle() {
            if (this.show) {
                this.closeAlat();
            } else {
                this.open();
            }
        },
        isOpenAlat() {
            return this.show === true;
        },
        selectedName() {
            return this.selected ? this.selected.nama_alat : this.filterAlat;
        },
        classOptionAlat(id, index) {
            const isSelected = this.selected ? id == this.selected.id : false;
            const isFocused = index == this.focusedOptionIndexAlat;
            return {
                "cursor-pointer w-full border-blue-600 border-b hover:bg-blue-50 dark:hover:bg-dark": true,
                "bg-blue-100 dark:bg-dark": isSelected,
                "bg-blue-50 dark:bg-dark": isFocused,
            };
        },
        fetchOptionsAlat() {
            this.options = {
                results: [],
            };
            const url = document.getElementById("url_alat").textContent;
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
        filterAlatOptions() {
            return this.options
                ? this.options.results.filter((option) => {
                      const filterAlatText = this.filterAlat
                          .toLowerCase()
                          .trim();
                      const nama_alat = option.nama_alat.toLowerCase();
                      return nama_alat.includes(filterAlatText);
                  })
                : [];
        },

        onOptionClickAlat(index) {
            this.focusedOptionIndexAlat = index;
            this.selectOptionAlat();
            document.getElementById("alat_id").value = this.selected.id;
            document.getElementById("identity-alat").value = this.selected.identity;
        },
        selectOptionAlat() {
            if (!this.isOpenAlat()) {
                return;
            }
            this.focusedOptionIndexAlat = this.focusedOptionIndexAlat ?? 0;
            const selected =
                this.filterAlatOptions()[this.focusedOptionIndexAlat];
            if (this.selected && this.selected.id == selected.id) {
                this.filterAlat = "";
                this.selected = null;
            } else {
                this.selected = selected;
                this.filterAlat = this.selectedName();
            }
            this.closeAlat();
        },
        focusPrevOptionAlat() {
            if (!this.isOpenAlat()) {
                return;
            }
            const optionsNum = Object.keys(this.filterAlatOptions()).length - 1;
            if (
                this.focusedOptionIndexAlat > 0 &&
                this.focusedOptionIndexAlat <= optionsNum
            ) {
                this.focusedOptionIndexAlat--;
            } else if (this.focusedOptionIndexAlat == 0) {
                this.focusedOptionIndexAlat = optionsNum;
            }
        },
        focusNextOptionAlat() {
            const optionsNum = Object.keys(this.filterAlatOptions()).length - 1;
            if (!this.isOpenAlat()) {
                this.open();
            }
            if (
                this.focusedOptionIndexAlat == null ||
                this.focusedOptionIndexAlat == optionsNum
            ) {
                this.focusedOptionIndexAlat = 0;
            } else if (
                this.focusedOptionIndexAlat >= 0 &&
                this.focusedOptionIndexAlat < optionsNum
            ) {
                this.focusedOptionIndexAlat++;
            }
        },
    };
}

function selectConfigsRS() {
    return {
        filterRS: "",
        show: false,
        selected: null,
        focusedOptionIndexRS: null,
        options: null,
        closeRS() {
            this.show = false;
            this.filterRS = this.selectedName();
            this.focusedOptionIndexRS = this.selected
                ? this.focusedOptionIndexRS
                : null;
        },
        open() {
            this.show = true;
            this.filterRS = "";
        },
        toggle() {
            if (this.show) {
                this.closeRS();
            } else {
                this.open();
            }
        },
        isOpenRS() {
            return this.show === true;
        },
        selectedName() {
            return this.selected ? this.selected.nama_rs : this.filterRS;
        },
        classOptionRS(id, index) {
            const isSelected = this.selected ? id == this.selected.id : false;
            const isFocused = index == this.focusedOptionIndexRS;
            return {
                "cursor-pointer w-full border-blue-600 border-b hover:bg-blue-50 dark:hover:bg-dark": true,
                "bg-blue-100 dark:bg-dark": isSelected,
                "bg-blue-50 dark:bg-dark": isFocused,
            };
        },
        fetchOptionsRS() {
            this.options = {
                results: [],
            };
            const url = document.getElementById("url_rs").textContent;
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
        filterRSOptions() {
            return this.options
                ? this.options.results.filter((option) => {
                      const filterRSText = this.filterRS.toLowerCase().trim();
                      const nama_rs = option.nama_rs.toLowerCase();
                      return nama_rs.includes(filterRSText);
                  })
                : [];
        },

        onOptionClickRS(index) {
            this.focusedOptionIndexRS = index;
            this.selectOptionRS();
            document.getElementById("rs_name_rujukan").value =
                this.selected.nama_rs;
        },
        selectOptionRS() {
            if (!this.isOpenRS()) {
                return;
            }
            this.focusedOptionIndexRS = this.focusedOptionIndexRS ?? 0;
            const selected = this.filterRSOptions()[this.focusedOptionIndexRS];
            if (this.selected && this.selected.id == selected.id) {
                this.filterRS = "";
                this.selected = null;
            } else {
                this.selected = selected;
                this.filterRS = this.selectedName();
            }
            this.closeRS();
        },
        focusPrevOptionRS() {
            if (!this.isOpenRS()) {
                return;
            }
            const optionsNum = Object.keys(this.filterRSOptions()).length - 1;
            if (
                this.focusedOptionIndexRS > 0 &&
                this.focusedOptionIndexRS <= optionsNum
            ) {
                this.focusedOptionIndexRS--;
            } else if (this.focusedOptionIndexRS == 0) {
                this.focusedOptionIndexRS = optionsNum;
            }
        },
        focusNextOptionRS() {
            const optionsNum = Object.keys(this.filterRSOptions()).length - 1;
            if (!this.isOpenRS()) {
                this.open();
            }
            if (
                this.focusedOptionIndexRS == null ||
                this.focusedOptionIndexRS == optionsNum
            ) {
                this.focusedOptionIndexRS = 0;
            } else if (
                this.focusedOptionIndexRS >= 0 &&
                this.focusedOptionIndexRS < optionsNum
            ) {
                this.focusedOptionIndexRS++;
            }
        },
    };
}

function selectConfigsRS1() {
    return {
        filterRS1: "",
        show: false,
        selected: null,
        focusedOptionIndexRS1: null,
        options: null,
        closeRS1() {
            this.show = false;
            this.filterRS1 = this.selectedName();
            this.focusedOptionIndexRS1 = this.selected
                ? this.focusedOptionIndexRS1
                : null;
        },
        open() {
            this.show = true;
            this.filterRS1 = "";
        },
        toggle() {
            if (this.show) {
                this.closeRS1();
            } else {
                this.open();
            }
        },
        isOpenRS1() {
            return this.show === true;
        },
        selectedName() {
            return this.selected ? this.selected.nama_rs : this.filterRS;
        },
        classOptionRS1(id, index) {
            const isSelected = this.selected ? id == this.selected.id : false;
            const isFocused = index == this.focusedOptionIndexRS1;
            return {
                "curs1or-pointer w-full border-blue-600 border-b hover:bg-blue-50 dark:hover:bg-dark": true,
                "bg-blue-100 dark:bg-dark": isSelected,
                "bg-blue-50 dark:bg-dark": isFocused,
            };
        },
        fetchOptionsRS1() {
            this.options = {
                results: [],
            };
            const url = document.getElementById("url_rs").textContent;
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
        filterRS1Options() {
            return this.options
                ? this.options.results.filter((option) => {
                      const filterRS1Text = this.filterRS1.toLowerCase().trim();
                      const nama_rs1 = option.nama_rs.toLowerCase();
                      return nama_rs1.includes(filterRS1Text);
                  })
                : [];
        },

        onOptionClickRS1(index) {
            this.focusedOptionIndexRS1 = index;
            this.selectOptionRS1();
            document.getElementById("rs_name_rawatinap").value =
                this.selected.nama_rs;
        },
        selectOptionRS1() {
            if (!this.isOpenRS1()) {
                return;
            }
            this.focusedOptionIndexRS1 = this.focusedOptionIndexRS1 ?? 0;
            const selected =
                this.filterRS1Options()[this.focusedOptionIndexRS1];
            if (this.selected && this.selected.id == selected.id) {
                this.filterRS1 = "";
                this.selected = null;
            } else {
                this.selected = selected;
                this.filterRS1 = this.selectedName();
            }
            this.closeRS1();
        },
        focusPrevOptionRS1() {
            if (!this.isOpenRS1()) {
                return;
            }
            const optionsNum = Object.keys(this.filterRS1Options()).length - 1;
            if (
                this.focusedOptionIndexRS1 > 0 &&
                this.focusedOptionIndexRS1 <= optionsNum
            ) {
                this.focusedOptionIndexRS1--;
            } else if (this.focusedOptionIndexRS1 == 0) {
                this.focusedOptionIndexRS1 = optionsNum;
            }
        },
        focusNextOptionRS1() {
            const optionsNum = Object.keys(this.filterRS1Options()).length - 1;
            if (!this.isOpenRS1()) {
                this.open();
            }
            if (
                this.focusedOptionIndexRS1 == null ||
                this.focusedOptionIndexRS1 == optionsNum
            ) {
                this.focusedOptionIndexRS1 = 0;
            } else if (
                this.focusedOptionIndexRS1 >= 0 &&
                this.focusedOptionIndexRS1 < optionsNum
            ) {
                this.focusedOptionIndexRS1++;
            }
        },
    };
}

let configs;
let configsAlat;
let configsRS;
let configsRS1;

function init() {
    configs = selectConfigs();
    configs.fetchOptions();
    configsAlat = selectConfigsAlat();
    configsAlat.fetchOptionsAlat();
    configsRS = selectConfigsRS();
    configsRS.fetchOptionsRS();
    configsRS1 = selectConfigsRS1();
    configsRS1.fetchOptionsRS1();
}

function tambahObat() {
    const obatNama = document.getElementById("obat_id").value;
    const kuantitas = document.getElementById("jumlah_obat").value;

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

    // Membuat input hidden untuk menyimpan id obat dan kuantitas
    const inputObat = document.createElement("input");
    inputObat.type = "hidden";
    inputObat.name = "obat_id[]";
    inputObat.value = obat.id;

    const inputNama = document.createElement("input");
    inputNama.type = "hidden";
    inputNama.name = "nama_obat[]";
    inputNama.value = obat.nama_obat;

    const inputKuantitas = document.createElement("input");
    inputKuantitas.type = "hidden";
    inputKuantitas.name = "jumlah_obat[]";
    inputKuantitas.value = kuantitas;

    // Menambahkan elemen list baru ke daftar permintaan obat
    const daftarPermintaanObat = document.getElementById(
        "daftar-permintaan-obat"
    );
    const listItem = document.createElement("li");
    listItem.className = "py-1";
    listItem.appendChild(inputObat);
    listItem.appendChild(inputNama);
    listItem.appendChild(inputKuantitas);
    listItem.appendChild(document.createTextNode(`${obatNama} x ${kuantitas}`));

    daftarPermintaanObat.appendChild(listItem);

    // Mengosongkan input obat dan kuantitas
    document.getElementById("obat_id").value = "";
    document.getElementById("jumlah_obat").value = "";
}

function tambahAlat() {
    const AlatNama = document.getElementById("alat_id").value;
    const kuantitasAlat = document.getElementById("jumlah_alat").value;
    const identityAlat = document.getElementById("identity-alat").value;

    const alat = configsAlat.options.results.find(
        (option) => option.nama_alat.toLowerCase() == AlatNama.toLowerCase()
    );

    if (!alat) {
        console.error(`Tidak ada alat dengan nama ${AlatNama}`);
        // Optionally clear input fields or display an error message to the user
        return;
    }

    // Membuat elemen list baru
    const li = document.createElement("li");
    li.textContent = `${AlatNama} ${kuantitasAlat}x`;

    // Membuat input hidden untuk menyimpan id alat dan kuantitasAlat
    const inputAlat = document.createElement("input");
    inputAlat.type = "hidden";
    inputAlat.name = "alat_id[]";
    inputAlat.value = alat.id;

    const inputNama = document.createElement("input");
    inputNama.type = "hidden";
    inputNama.name = "nama_alat[]";
    inputNama.value = alat.nama_alat;

    const inputKuantitasAlat = document.createElement("input");
    inputKuantitasAlat.type = "hidden";
    inputKuantitasAlat.name = "jumlah_alat[]";
    inputKuantitasAlat.value = kuantitasAlat;

    const inputIdentityAlat = document.createElement("input");
    inputIdentityAlat.type = "hidden";
    inputIdentityAlat.name = "identity_alat[]";
    inputIdentityAlat.value = identityAlat;

    // Menambahkan elemen list baru ke daftar permintaan alat
    const daftarPermintaanAlat = document.getElementById(
        "daftar-permintaan-alat"
    );
    const listItem = document.createElement("li");
    listItem.className = "py-1";
    listItem.appendChild(inputAlat);
    listItem.appendChild(inputNama);
    listItem.appendChild(inputKuantitasAlat);
    listItem.appendChild(inputIdentityAlat);
    listItem.appendChild(
        document.createTextNode(`${AlatNama} x ${kuantitasAlat}`)
    );

    daftarPermintaanAlat.appendChild(listItem);

    // Mengosongkan input alat dan kuantitasAlat
    document.getElementById("alat_id").value = "";
    document.getElementById("jumlah_alat").value = "";
    document.getElementById("identity-alat").value = "";
}
// Panggil 'init' saat halaman dimuat
window.onload = init;
