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
                      const filterRS1Text = this.filterRS1
                          .toLowerCase()
                          .trim();
                      const nama_rs1 = option.nama_rs.toLowerCase();
                      return nama_rs1.includes(filterRS1Text);
                  })
                : [];
        },

        onOptionClickRS1(index) {
            this.focusedOptionIndexRS1 = index;
            this.selectOptionRS1();
            document.getElementById("rs_name_rawatinap").value = this.selected.nama_rs;
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

let configsRS1;

configsRS1 = selectConfigsRS1();
configsRS1.fetchOptionsRS1();

function tambahRS1() {
    const RS1Nama = document.getElementById("RS1_id").value;
    const kuantitasRS1 = document.getElementById("jumlah_RS1").value;

    const RS1 = configsRS1.options.results.find(
        (option) => option.nama_RS1.toLowerCase() == RS1Nama.toLowerCase()
    );

    if (!RS1) {
        console.error(`Tidak ada RS1 dengan nama ${RS1Nama}`);
        // Optionally clear input fields or display an error message to the user
        return;
    }

    // Membuat elemen list baru
    const li = document.createElement("li");
    li.textContent = `${RS1Nama} ${kuantitasRS1}x`;

    // Membuat input hidden untuk menyimpan id RS1 dan kuantitasRS1
    const inputRS1 = document.createElement("input");
    inputRS1.type = "hidden";
    inputRS1.name = "RS1_id[]";
    inputRS.value = RS.id;

    const inputNama = document.createElement("input");
    inputNama.type = "hidden";
    inputNama.name = "nama_RS[]";
    inputNama.value = RS.nama_RS;

    const inputKuantitasRS = document.createElement("input");
    inputKuantitasAlat.type = "hidden";
    inputKuantitasAlat.name = "jumlah_alat[]";
    inputKuantitasAlat.value = kuantitasAlat;

    // Menambahkan elemen list baru ke daftar permintaan alat
    const daftarPermintaanAlat = document.getElementById(
        "daftar-permintaan-alat"
    );
    const listItem = document.createElement("li");
    listItem.className = "py-1";
    listItem.appendChild(inputAlat);
    listItem.appendChild(inputNama);
    listItem.appendChild(inputKuantitasAlat);
    listItem.appendChild(
        document.createTextNode(`${AlatNama} x ${kuantitasAlat}`)
    );

    daftarPermintaanAlat.appendChild(listItem);

    // Mengosongkan input alat dan kuantitasAlat
    document.getElementById("alat_id").value = "";
    document.getElementById("jumlah_alat").value = "";
}

// Panggil 'init' saat halaman dimuat
window.onload = init;