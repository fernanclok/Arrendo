<template>
    <div class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded bg-blueGray-700">
        <div class="rounded-t mb-0 px-4 py-3 bg-transparent">
            <div class="flex flex-wrap items-center">
                <div class="relative w-full max-w-full flex-grow flex-1">
                    <h6 class="uppercase text-blueGray-100 mb-1 text-xs font-semibold">
                        Overview
                    </h6>
                    <h2 class="text-white text-xl font-semibold">
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

    mounted() {
        this.$nextTick(() => {
            // Etiquetas de los meses (siempre estarán en este orden)
            const monthNames = [
                "January", "February", "March", "April",
                "May", "June", "July", "August",
                "September", "October", "November", "December",
            ];

            // Crear una estructura base de datos para asegurarnos de que todos los meses estén presentes
            const currentYear = new Date().getFullYear();
            const previousYear = currentYear - 1;

            const baseData = (year) => monthNames.map((_, index) => ({
                year: year,
                month: index + 1, // 1 = January, 2 = February...
                total_income: 0, // Valor por defecto
            }));

            // Combinar los datos base con los datos del backend
            const completeData = [...baseData(previousYear), ...baseData(currentYear)];

            // console.log(completeData, 'completeData');
            this.monthlyIncome.forEach(item => {
                const match = completeData.find(data =>
                    Number(data.year) === Number(item.year) &&
                    Number(data.month) === Number(item.month)
                );
                if (match) {
                    match.total_income = item.total_income;
                } else {
                    console.warn('No match found for:', item);
                }
            });

            // Separar datos por año
            const previousYearData = completeData
                .filter(item => item.year === previousYear)
                .map(item => item.total_income);

            const currentYearData = completeData
                .filter(item => item.year === currentYear)
                .map(item => item.total_income);

            // Configurar el gráfico
            const config = {
                type: "line",
                data: {
                    labels: monthNames, // Siempre de enero a diciembre
                    datasets: [
                        {
                            label: previousYear,
                            backgroundColor: "#fff",
                            borderColor: "#fff",
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
                                color: "white",
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

            // Crear el gráfico
            const ctx = document.getElementById("line-chart").getContext("2d");
            window.myLine = new Chart(ctx, config);
        });
    },
};

</script>
