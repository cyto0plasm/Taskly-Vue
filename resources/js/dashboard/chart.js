import Chart from "chart.js/auto";

document.addEventListener("DOMContentLoaded", () => {
    const canvas = document.getElementById("projectsLineChart");
    if (!canvas) return;

    const ctx = canvas.getContext("2d");
    const totalProjects = JSON.parse(canvas.dataset.projects || "{}");

    // Create gradient for the area under the line
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, "rgba(75,192,192,0.4)");
    gradient.addColorStop(1, "rgba(75,192,192,0)");

    new Chart(ctx, {
        type: "line",
        data: {
            labels: Object.keys(totalProjects).map((k) =>
                k.replace("_", " ").toUpperCase()
            ),
            datasets: [
                {
                    label: "Project Status",
                    data: Object.values(totalProjects),
                    fill: true, // fill area under the line
                    backgroundColor: gradient,
                    borderColor: "rgba(75,192,192,1)",
                    borderWidth: 2,
                    tension: 0.4, // smooth curves
                    pointRadius: 5,
                    pointBackgroundColor: "rgba(75,192,192,1)",
                    pointHoverRadius: 7,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: "top" },
                tooltip: {
                    enabled: true,
                    mode: "index",
                    intersect: false,
                    callbacks: {
                        label: function (context) {
                            return `${context.dataset.label}: ${context.parsed.y}`;
                        },
                    },
                },
                title: {
                    display: true,
                    text: "Projects Overview",
                },
            },
            scales: {
                y: { beginAtZero: true },
                x: { grid: { display: false } },
            },
            interaction: { mode: "nearest", axis: "x", intersect: false },
        },
    });
});
