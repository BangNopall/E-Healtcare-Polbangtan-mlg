// skb
(async function () {
    // Fetch data from API
    const response = await fetch("/get_sk");
    const apiData = await response.json();

    // jika data kosong, buat data dummy

    // Convert API data to the format expected by Chart.js
    const data = [apiData.skb, apiData.sksa, apiData.skse, apiData.sr];

    new Chart(document.getElementById("sk"), {
        type: "bar",
        data: {
            labels: [
                "Surat Ket. Berobat",
                "Surat Ket. Sakit",
                "Surat Ket. Sehat",
                "Surat Rujukan",
            ],
            datasets: [
                {
                    label: "Jumlah Surat",
                    data: data,
                    backgroundColor: [
                        "rgb(255, 99, 132)",
                        "rgb(75, 192, 192)",
                        "rgb(255, 205, 86)",
                        "rgb(201, 203, 207)",
                        "rgb(54, 162, 235)",
                    ],
                },
            ],
        },
        options: {
            plugins: {
                legend: {
                    labels: {
                        color: (context) =>
                            context.chart.canvas.classList.contains("dark")
                                ? "white"
                                : "black",
                    },
                },
            },
            scales: {
                x: {
                    ticks: {
                        color: (context) =>
                            context.chart.canvas.classList.contains("dark")
                                ? "white"
                                : "black",
                    },
                },
                y: {
                    ticks: {
                        color: (context) =>
                            context.chart.canvas.classList.contains("dark")
                                ? "white"
                                : "black",
                    },
                },
            },
        },
    });
})();
// penyakit/diagnosa
(async function () {
    // Fetch data from API
    const response = await fetch("/get_penyakit");
    const apiData = await response.json();

    // Helper function to convert "YYYY-MM" to "Month Name"
    function convertToMonthName(yyyymm) {
        const date = new Date(yyyymm + "-01"); // Convert "YYYY-MM" to Date object
        return date.toLocaleString("default", {
            month: "long",
            year: "numeric",
        });
    }

    // Prepare data for chart
    const labels = [
        ...new Set(apiData.map((row) => convertToMonthName(row.bulan))),
    ]; // Convert months to full month names
    const penyakitData = {};

    apiData.forEach((row) => {
        if (!penyakitData[row.diagnosa]) {
            penyakitData[row.diagnosa] = Array(labels.length).fill(0); // Inisialisasi array data untuk setiap penyakit
        }
        const monthIndex = labels.indexOf(convertToMonthName(row.bulan));
        penyakitData[row.diagnosa][monthIndex] = row.jumlah;
    });

    const datasets = Object.keys(penyakitData)
        .filter(
            (diagnosa) => penyakitData[diagnosa].reduce((a, b) => a + b, 0) > 1
        ) // Hanya include diagnosa dengan lebih dari satu kejadian
        .map((diagnosa) => ({
            label: diagnosa,
            data: penyakitData[diagnosa],
            fill: false,
            borderColor: getRandomColor(), // Function untuk mendapatkan warna random
        }));

    new Chart(document.getElementById("penyakit"), {
        type: "line",
        data: {
            labels: labels,
            datasets: datasets,
        },
        options: {
            plugins: {
                legend: {
                    labels: {
                        color: (context) =>
                            context.chart.canvas.classList.contains("dark")
                                ? "white"
                                : "black",
                    },
                },
            },
            scales: {
                x: {
                    ticks: {
                        color: (context) =>
                            context.chart.canvas.classList.contains("dark")
                                ? "white"
                                : "black",
                    },
                },
                y: {
                    ticks: {
                        color: (context) =>
                            context.chart.canvas.classList.contains("dark")
                                ? "white"
                                : "black",
                    },
                },
            },
        },
    });
})();

// Function to get a random color
function getRandomColor() {
    const letters = "0123456789ABCDEF";
    let color = "#";
    for (let i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}
