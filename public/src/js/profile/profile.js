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

function previewDoc() {
    const input = document.getElementById("dropdoc");
    const preview = document.getElementById("preview-doc");
    const docContent = document.getElementById("doc-content");
    const docName = document.getElementById("docname");

    if (input.files && input.files.length > 0) {
        const fileNames = Array.from(input.files)
            .map((file) => file.name)
            .join(", ");
        docName.textContent = fileNames;
        preview.classList.remove("hidden");
        docContent.classList.add("hidden");
    } else {
        preview.classList.add("hidden");
        docContent.classList.remove("hidden");
    }
}
