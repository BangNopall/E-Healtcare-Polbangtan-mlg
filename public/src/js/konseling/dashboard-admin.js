(async function () {
    // Fetch data from API
    const response = await fetch("/get_konseling");
    const apiData = await response.json();

    console.log(apiData);

    new Chart(document.getElementById("konseling"), {
        type: "doughnut",
        data: {
            labels: ["Feedback Sensuh", "Konsultasi"],
            datasets: [
                {
                    label: "Data",
                    data: [apiData.fb, apiData.ks],
                    backgroundColor: ["rgb(255, 99, 132)", "rgb(75, 192, 192)"],
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
