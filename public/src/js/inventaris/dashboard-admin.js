// OBAT
(async function () {
    // Fetch data from API
    const response = await fetch("/get_data_obat_bulan");
    const apiData = await response.json();

    // Convert API data to the format expected by Chart.js
    const data = [
        { month: "Januari", count: apiData.January },
        { month: "Februari", count: apiData.February },
        { month: "Maret", count: apiData.March },
        { month: "April", count: apiData.April },
        { month: "Mei", count: apiData.May },
        { month: "Juni", count: apiData.June },
    ];

    new Chart(document.getElementById("obatstockin"), {
        type: "line",
        data: {
            labels: data.map((row) => row.month),
            datasets: [
                {
                    label: "Kuantitas",
                    data: data.map((row) => row.count),
                    backgroundColor: "#f87979",
                    borderColor: "rgba(255, 99, 132, 1)",
                    borderWidth: 1,
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
(async function () {
    // Mendapatkan data dari API
    const response = await fetch('/get_data_obat_ringkas');
    const dataJson = await response.json();

    // Menggunakan data dari API untuk mengisi grafik
    const labels = ["At-Stock", "Under-Stock", "Out-of-Stock"];
    const data = {
        labels: labels,
        datasets: [
            {
                label: "Item",
                data: [dataJson.atstock, dataJson.lowstock, dataJson.zerostock], // Menggunakan data dari API
                backgroundColor: [
                    "rgba(60, 179, 113, 0.2)",
                    "rgba(255, 159, 64, 0.2)",
                    "rgba(255, 0, 0, 0.2)",
                ],
                borderColor: [
                    "rgb(60, 179, 113)",
                    "rgb(255, 159, 64)",
                    "rgb(255, 0, 0)",
                ],
                borderWidth: 1,
            },
        ],
    };

    new Chart(document.getElementById("ringkasstokobat"), {
        type: "bar",
        data: data,
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
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: (context) =>
                            context.chart.canvas.classList.contains("dark")
                                ? "white"
                                : "black",
                    },
                },
                x: {
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
// ALAT TERPAKAI
(async function () {
    const response = await fetch("/get_data_alat_bulan");
    const apiData = await response.json();

    // Convert API data to the format expected by Chart.js
    const data = [
        { month: "Januari", count: apiData.January },
        { month: "Februari", count: apiData.February },
        { month: "Maret", count: apiData.March },
        { month: "April", count: apiData.April },
        { month: "Mei", count: apiData.May },
        { month: "Juni", count: apiData.June },
    ];

    new Chart(document.getElementById("alatstockin"), {
        type: "line",
        data: {
            labels: data.map((row) => row.month),
            datasets: [
                {
                    label: "Kuantitas",
                    data: data.map((row) => row.count),
                    backgroundColor: "rgba(252, 190, 62, 0.8)",
                    borderColor: "rgba(252, 190, 62, 1)",
                    borderWidth: 1,
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
(async function () {
    const response = await fetch('/get_data_alat_ringkas');
    const dataJson = await response.json();

    // Menggunakan data dari API untuk mengisi grafik
    const labels = ["At-Stock", "Under-Stock", "Out-of-Stock"];
    const data = {
        labels: labels,
        datasets: [
            {
                label: "Item",
                data: [dataJson.atstock, dataJson.lowstock, dataJson.zerostock], // Menggunakan data dari API
                backgroundColor: [
                    "rgba(60, 179, 113, 0.2)",
                    "rgba(255, 159, 64, 0.2)",
                    "rgba(255, 0, 0, 0.2)",
                ],
                borderColor: [
                    "rgb(60, 179, 113)",
                    "rgb(255, 159, 64)",
                    "rgb(255, 0, 0)",
                ],
                borderWidth: 1,
            },
        ],
    };

    new Chart(document.getElementById("ringkasstokalat"), {
        type: "bar",
        data: data,
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
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: (context) =>
                            context.chart.canvas.classList.contains("dark")
                                ? "white"
                                : "black",
                    },
                },
                x: {
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
// ALAT TERSISA
(async function () {
    const response = await fetch("/get_data_consumable_bulan");
    const apiData = await response.json();

    // Convert API data to the format expected by Chart.js
    const data = [
        { month: "Januari", count: apiData.January },
        { month: "Februari", count: apiData.February },
        { month: "Maret", count: apiData.March },
        { month: "April", count: apiData.April },
        { month: "Mei", count: apiData.May },
        { month: "Juni", count: apiData.June },
    ];

    new Chart(document.getElementById("tersisastockin"), {
        type: "line",
        data: {
            labels: data.map((row) => row.month),
            datasets: [
                {
                    label: "Kuantitas",
                    data: data.map((row) => row.count),
                    backgroundColor: "rgba(65, 197, 66, 0.8)",
                    borderColor: "rgba(65, 197, 66, 1)",
                    borderWidth: 1,
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
(async function () {
    const response = await fetch('/get_data_consumable_ringkas');
    const dataJson = await response.json();

    // Menggunakan data dari API untuk mengisi grafik
    const labels = ["At-Stock", "Under-Stock", "Out-of-Stock"];
    const data = {
        labels: labels,
        datasets: [
            {
                label: "Item",
                data: [dataJson.atstock, dataJson.lowstock, dataJson.zerostock], // Menggunakan data dari API
                backgroundColor: [
                    "rgba(60, 179, 113, 0.2)",
                    "rgba(255, 159, 64, 0.2)",
                    "rgba(255, 0, 0, 0.2)",
                ],
                borderColor: [
                    "rgb(60, 179, 113)",
                    "rgb(255, 159, 64)",
                    "rgb(255, 0, 0)",
                ],
                borderWidth: 1,
            },
        ],
    };

    new Chart(document.getElementById("ringkasstoktersisa"), {
        type: "bar",
        data: data,
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
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: (context) =>
                            context.chart.canvas.classList.contains("dark")
                                ? "white"
                                : "black",
                    },
                },
                x: {
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
