document.addEventListener("DOMContentLoaded", function () {
    var selectElement = document.getElementById("kategori");
    var descriptionTextarea = document.getElementById("description");

    // Tampilkan atau sembunyikan textarea deskripsi berdasarkan pilihan dropdown
    selectElement.addEventListener("change", function () {
        if (selectElement.value === "withdraw") {
            descriptionTextarea.style.display = "block";
        } else {
            descriptionTextarea.style.display = "none";
        }
    });
});

document
    .getElementById("updateImageButton")
    .addEventListener("click", function () {
        document.getElementById("dropzone-file").click();
    });

function previewFile() {
    const previewContainer = document.getElementById("preview-container");
    const previewImage = document.getElementById("preview-image");
    const fileInput = document.getElementById("dropzone-file").files[0];
    const reader = new FileReader();

    reader.onloadend = function () {
        previewImage.src = reader.result;
        previewContainer.classList.remove("hidden");
        document.getElementById("inputfilefoto").classList.add("hidden");
    };

    if (fileInput) {
        reader.readAsDataURL(fileInput);
    } else {
        previewImage.src = "#";
        previewContainer.classList.add("hidden");
    }
}

function removeImage() {
    const previewContainer = document.getElementById("preview-container");
    const previewImage = document.getElementById("preview-image");
    const fileInput = document.getElementById("dropzone-file");

    // Hapus gambar pratinjau
    previewImage.src = "#";
    previewContainer.classList.add("hidden");
    document.getElementById("inputfilefoto").classList.remove("hidden");

    // Bersihkan nilai input file
    fileInput.value = "";
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
            return this.selected ? this.selected.nama_satuan : this.filter;
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
            const url = document.getElementById(
                "url_get_kategori_obat"
            ).textContent;
            fetch(url)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error("Gagal mengambil data kategori obat");
                    }
                    return response.json();
                })
                .then((data) => {
                    // console.log(data);
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
                      const nama_satuan = option.nama_satuan.toLowerCase();
                      return nama_satuan.includes(filterText);
                  })
                : {};
        },
        onOptionClick(index) {
            this.focusedOptionIndex = index;
            this.selectOption();
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

function selectConfigUsers() {
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
            return this.selected ? this.selected.name : this.filter;
        },
        classOption(id, index) {
            const isSelected = this.selected
                ? id == this.selected.id
                : false;
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
            const url = document.getElementById("get_user_url").textContent;
            fetch(url)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error("Gagal mengambil data user!");
                    }
                    return response.json();
                })
                .then((data) => {
                    // console.log(data);
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
                      const name = option.name.toLowerCase();
                      return name.includes(filterText);
                  })
                : {};
        },
        onOptionClick(index) {
            this.focusedOptionIndex = index;
            this.selectOption();
            document.getElementById("user_id").value = this.selected.id;
            document.getElementById("user_id_wd").value = this.selected.id;
        },
        selectOption() {
            if (!this.isOpen()) {
                return;
            }
            this.focusedOptionIndex = this.focusedOptionIndex ?? 0;
            const selected = this.filteredOptions()[this.focusedOptionIndex];
            if (
                this.selected &&
                this.selected.id == selected.id
            ) {
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

function selectConfigKategoris() {
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
            return this.selected ? this.selected.nama_kategori : this.filter;
        },
        classOption(id, index) {
            const isSelected = this.selected
                ? id == this.selected.id
                : false;
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
            const url = document.getElementById("url_kategori_notkategori").textContent;
            fetch(url)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error("Gagal mengambil data kategori alat!");
                    }
                    return response.json();
                })
                .then((data) => {
                    // console.log(data);
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
                      const nama_kategori = option.nama_kategori.toLowerCase();
                      return nama_kategori.includes(filterText);
                  })
                : {};
        },
        onOptionClick(index) {
            this.focusedOptionIndex = index;
            this.selectOption();
            document.getElementById("kategori_id").value = this.selected.id;
        },
        selectOption() {
            if (!this.isOpen()) {
                return;
            }
            this.focusedOptionIndex = this.focusedOptionIndex ?? 0;
            const selected = this.filteredOptions()[this.focusedOptionIndex];
            if (
                this.selected &&
                this.selected.id == selected.id
            ) {
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
