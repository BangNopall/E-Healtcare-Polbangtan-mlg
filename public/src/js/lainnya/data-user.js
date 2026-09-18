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
        document.getElementById("inputfilefoto").classList.remove("hidden");
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
