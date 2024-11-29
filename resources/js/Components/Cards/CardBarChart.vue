<template>
    <div class="relative flex flex-col min-w-0 break-words bg-white w-full mb-6 shadow-lg rounded">
        <div class="rounded-t mb-0 px-4 py-3 bg-transparent">
            <div class="flex flex-wrap items-center">
                <div class="relative w-full max-w-full flex-grow flex-1">
                    <h6 class="uppercase text-blueGray-400 mb-1 text-xs font-semibold">
                        Occupancy
                    </h6>
                    <h2 class="text-blueGray-700 text-xl font-semibold">
                        Properties Occupancy by Month
                    </h2>
                </div>
            </div>
        </div>
        <div class="p-4 flex-auto">
            <div class="relative h-350-px">
                <canvas id="bar-chart"></canvas>
            </div>
        </div>
    </div>
</template>

<script>
import Chart from "chart.js/auto";
export default {
    props: {
        occupancyData: {
            type: Array,
            required: true,
        },
    },
    mounted() {
        this.renderChart();
    },
    methods: {
        renderChart() {

            const monthNames = [
                "January", "February", "March", "April",
                "May", "June", "July", "August",
                "September", "October", "November", "December",
            ];


            const labels = this.occupancyData.map((item) => monthNames[item.month - 1]);
            const notAvailableData = this.occupancyData.map((item) => item.not_available);
            const availableData = this.occupancyData.map((item) => item.available);

            const config = {
                type: "bar",
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: "Available",
                            backgroundColor: "#4caf50",
                            borderColor: "#4caf50",
                            data: availableData,
                            barThickness: 8,
                        },
                        {
                            label: "Rented",
                            backgroundColor: "#f44336",
                            borderColor: "#f44336",
                            data: notAvailableData,
                            barThickness: 8,
                        },
                    ],
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        legend: {
                            labels: {
                                color: "rgba(0,0,0,.4)",
                            },
                            align: "end",
                            position: "bottom",
                        },
                        tooltip: {
                            mode: "index",
                            intersect: false,
                        },
                    },
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: "Month",
                            },
                            grid: {
                                borderDash: [2],
                                color: "rgba(33, 37, 41, 0.3)",
                            },
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: "Number of Properties",
                            },
                            grid: {
                                borderDash: [2],
                                color: "rgba(33, 37, 41, 0.2)",
                            },
                        },
                    },
                },
            };

            const ctx = document.getElementById("bar-chart").getContext("2d");
            new Chart(ctx, config);
        },
    },
};
</script>
