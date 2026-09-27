document.addEventListener("DOMContentLoaded", () => {
    const cameraSelect = document.getElementById("cameraSelect");
    const btnstop = document.getElementById("btnstop");
    const tokenInput = document.getElementById("token");
    const form = document.getElementById("form");
    const readerElement = document.getElementById("reader");

    if (!readerElement || !cameraSelect || !btnstop || !tokenInput || !form) {
        return;
    }

    const qrCodeReader = new Html5Qrcode("reader");
    const beepSound = new Audio("/audio/beep.mp3");
    const config = { fps: 10, qrbox: { width: 250, height: 250 }, aspectRatio: 1.0 };

    let isScanning = false;
    let currentCameraId = null;

    const playBeep = () => {
        try {
            beepSound.play().catch(() => {});
        } catch (e) {}
    };

    const qrCodeSuccessCallback = async (decodedText) => {
        try {
            let parsed = JSON.parse(decodedText);
            if (parsed && parsed.token) {
                playBeep();
                try {
                    await qrCodeReader.stop();
                    isScanning = false;
                } catch (e) {}
                tokenInput.value = parsed.token;
                form.submit();
            } else {
                console.warn("QR code valid JSON tapi tidak memiliki atribut token:", parsed);
            }
        } catch (err) {
            console.warn("Format QR bukan JSON valid:", err);
        }
    };

    const startScanner = async (cameraConfig) => {
        try {
            if (isScanning) {
                try {
                    await qrCodeReader.stop();
                } catch (e) {}
                isScanning = false;
            }
            await qrCodeReader.start(cameraConfig, config, qrCodeSuccessCallback, () => {});
            isScanning = true;
            btnstop.innerText = "Stop Scan";
            btnstop.classList.remove("bg-green-600", "hover:bg-green-700");
            btnstop.classList.add("bg-blue-600", "hover:bg-blue-700");
            btnstop.disabled = false;
        } catch (err) {
            console.error("Gagal memulai scanner kamera:", err);
            // Fallback: jika kamera belakang gagal pada start awal, coba kamera depan
            if (typeof cameraConfig === "object" && cameraConfig.facingMode === "environment") {
                try {
                    await qrCodeReader.start({ facingMode: "user" }, config, qrCodeSuccessCallback, () => {});
                    isScanning = true;
                    btnstop.innerText = "Stop Scan";
                    btnstop.disabled = false;
                } catch (fallbackErr) {
                    console.error("Fallback kamera selfie juga gagal:", fallbackErr);
                }
            }
        }
    };

    // 1. Mulai kamera pertama kali (prioritas kamera belakang)
    startScanner({ facingMode: "environment" });

    // 2. Deteksi kamera perangkat dan aktifkan dropdown
    Html5Qrcode.getCameras()
        .then((cameras) => {
            if (cameras && cameras.length > 0) {
                cameraSelect.innerHTML = "";
                const defaultOption = document.createElement("option");
                defaultOption.text = cameras.length > 1 ? "Pilih Kamera" : "Kamera Terdeteksi";
                defaultOption.value = "";
                defaultOption.disabled = true;
                defaultOption.selected = true;
                cameraSelect.appendChild(defaultOption);

                cameras.forEach((cam, index) => {
                    const option = document.createElement("option");
                    option.value = cam.id;
                    option.text = cam.label || `Kamera ${index + 1}`;
                    cameraSelect.appendChild(option);
                });

                cameraSelect.disabled = false;

                cameraSelect.addEventListener("change", async function () {
                    const selectedId = cameraSelect.value;
                    if (!selectedId) return;

                    currentCameraId = selectedId;
                    cameraSelect.disabled = true;
                    btnstop.disabled = true;

                    await startScanner(selectedId);

                    cameraSelect.disabled = false;
                    btnstop.disabled = false;
                });
            } else {
                cameraSelect.innerHTML = "<option selected disabled>Tidak ada kamera terdeteksi</option>";
                cameraSelect.disabled = true;
            }
        })
        .catch((err) => {
            console.warn("Tidak dapat mengambil daftar kamera:", err);
            cameraSelect.innerHTML = "<option selected disabled>Izin kamera dibutuhkan</option>";
            cameraSelect.disabled = true;
        });

    // 3. Tombol Toggle Stop / Mulai Scan
    btnstop.addEventListener("click", async function () {
        if (isScanning) {
            try {
                await qrCodeReader.stop();
                isScanning = false;
                btnstop.innerText = "Mulai Scan";
                btnstop.classList.remove("bg-blue-600", "hover:bg-blue-700");
                btnstop.classList.add("bg-green-600", "hover:bg-green-700");
            } catch (err) {
                console.error("Gagal menghentikan scanner:", err);
            }
        } else {
            btnstop.disabled = true;
            if (currentCameraId) {
                await startScanner(currentCameraId);
            } else {
                await startScanner({ facingMode: "environment" });
            }
            btnstop.disabled = false;
        }
    });
});
