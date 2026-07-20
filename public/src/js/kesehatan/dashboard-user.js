(async function () {
    // Fetch data from API
    const response = await fetch("/user/get_surat_user");
    const apiData = await response.json();

    // Convert API data to the format expected by Chart.js
    const data = [
        { month: "Surat Keterangan Berobat", count: apiData.skb },
        { month: "Surat Keterangan Sakit", count: apiData.sks },
        { month: "Surat Rujukan", count: apiData.sr },
        { month: "Surat Keterangan Sehat", count: apiData.skse },
    ];

    new Chart(document.getElementById("surat"), {
        type: "pie",
        data: {
            labels: data.map((row) => row.month),
            datasets: [
                {
                    label: "Surat Keterangan Berobat",
                    data: data.map((row) => row.count),
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
