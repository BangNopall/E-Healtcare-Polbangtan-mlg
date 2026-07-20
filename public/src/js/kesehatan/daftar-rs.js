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
            return this.selected ? this.selected.nama_rs : this.filter;
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
            const url = document.getElementById("url_histori_rs").textContent;
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
                      const nama_rs = option.nama_rs.toLowerCase();
                      return nama_rs.includes(filterText);
                  })
                : {};
        },
        onOptionClick(index) {
            this.focusedOptionIndex = index;
            this.selectOption();
            if (this.selected) {
                document.getElementById("rs_id").value = this.selected.id;
            }
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
        resetRS() {
            // Mengatur ulang nilai-nilai dalam objek configs
            this.filter = "";
            this.show = false;
            this.selected = null;
            this.focusedOptionIndex = null;
            this.options = null;

            // Mengatur ulang HTML
            document.getElementById("nama_rs").value = "";
            document.getElementById("rs_id").value = "";
            document.getElementById("nama_rs_span").textContent = "";

            // Mengambil opsi baru
            this.fetchOptions();
        },
    };
}

let configs;

function init() {
    configs = selectConfigs();
    configs.fetchOptions();

    // Menetapkan `configs` sebagai variabel global untuk debugging
    window.configs = configs;
    // Mengikat tombol reset ke fungsi `resetRS` di dalam `configs`
    document
        .querySelector('button[type="button"]')
        .addEventListener("click", () => {
            configs.resetRS();
        });
}

// Panggil 'init' saat halaman dimuat
window.onload = init;

// function resetRS() {
//     // Mengatur ulang nilai-nilai dalam objek configs
//     configs.filter = "";
//     configs.show = false;
//     configs.selected = null;
//     configs.focusedOptionIndex = null;
//     configs.options = null;

//     // Mengatur ulang HTML
//     document.getElementById("nama_rs").value = "";
//     document.getElementById("rs_id").value = "";
//     document.getElementById("nama_rs_span").textContent = "";

//     // Mengambil opsi baru
//     configs.fetchOptions();
// }
