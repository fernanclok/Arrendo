<template>
    <div class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded bg-white">
        <div class="rounded-t mb-0 px-4 py-3 bg-transparent">
            <div class="flex flex-wrap items-center">
                <div class="relative w-full max-w-full flex-grow flex-1">
                    <h6 class="uppercase text-blueGray-400 mb-1 text-xs font-semibold">
                        Overview
                    </h6>
                    <h2 class="text-xl font-semibold">
                        Total Rental Income by Month
                    </h2>
                </div>
            </div>
        </div>
        <div class="p-4 flex-auto">
            <!-- Chart -->
            <div class="relative h-350-px">
                <canvas id="line-chart"></canvas>
            </div>
        </div>
    </div>
</template>

<script>
import { Chart } from "chart.js/auto";

export default {
    props: {
        monthlyIncome: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            chart: null, // Referencia al gráfico
        };
    },
    watch: {
        // Observar cambios en los datos
        monthlyIncome: {
            handler() {
                this.updateChart();
            },
            deep: true,
        },
    },
    mounted() {
        this.$nextTick(() => {
            this.renderChart(); // Renderizar el gráfico inicialmente
        });
    },
    methods: {
        renderChart() {
            const { previousYearData, currentYearData, monthNames, previousYear, currentYear } =
                this.prepareChartData();

            const config = {
                type: "line",
                data: {
                    labels: monthNames,
                    datasets: [
                        {
                            label: previousYear,
                            backgroundColor: "#94a3b8",
                            borderColor: "#94a3b8",
                            data: previousYearData,
                            fill: false,
                        },
                        {
                            label: currentYear,
                            backgroundColor: "#4c51bf",
                            borderColor: "#4c51bf",
                            data: currentYearData,
                            fill: false,
                        },
                    ],
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        legend: {
                            labels: {
                                color: "black",
                            },
                            align: "end",
                            position: "bottom",
                        },
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: "rgba(255,255,255,.7)",
                            },
                        },
                        y: {
                            ticks: {
                                color: "rgba(255,255,255,.7)",
                            },
                            grid: {
                                color: "rgba(255, 255, 255, 0.15)",
                            },
                        },
                    },
                },
            };

            const ctx = document.getElementById("line-chart").getContext("2d");
            this.chart = new Chart(ctx, config);
        },
        updateChart() {
            if (this.chart) {
                const { previousYearData, currentYearData } = this.prepareChartData();
                this.chart.data.datasets[0].data = previousYearData;
                this.chart.data.datasets[1].data = currentYearData;
                this.chart.update(); // Actualizar el gráfico
            }
        },
        prepareChartData() {
            const monthNames = [
                "January", "February", "March", "April",
                "May", "June", "July", "August",
                "September", "October", "November", "December",
            ];

            const currentYear = new Date().getFullYear();
            const previousYear = currentYear - 1;

            const baseData = (year) =>
                monthNames.map((_, index) => ({
                    year: year,
                    month: index + 1,
                    total_income: 0,
                }));

            const completeData = [...baseData(previousYear), ...baseData(currentYear)];

            this.monthlyIncome.forEach((item) => {
                const match = completeData.find(
                    (data) =>
                        Number(data.year) === Number(item.year) &&
                        Number(data.month) === Number(item.month)
                );
                if (match) {
                    match.total_income = item.total_income;
                }
            });

            const previousYearData = completeData
                .filter((item) => item.year === previousYear)
                .map((item) => item.total_income);

            const currentYearData = completeData
                .filter((item) => item.year === currentYear)
                .map((item) => item.total_income);

            return { previousYearData, currentYearData, monthNames, previousYear, currentYear };
        },
    },
};
</script>
