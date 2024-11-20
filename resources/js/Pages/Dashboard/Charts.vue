<template>
    <div>
        <div class="flex flex-wrap">
            <div class="w-full xl:w-7/12 mb-12 xl:mb-0 px-4">
                <card-line-chart :monthly-income="monthlyIncome" />
            </div>
            <div class="w-full xl:w-5/12 px-4">
                <card-bar-chart :occupancyData="occupancyData"/>
            </div>
        </div>
        <div class="flex flex-wrap mt-4">
            <div class="w-full xl:w-8/12 mb-12 xl:mb-0 px-4">
                <card-property-status :propertiesData="propertiesData"/>
            </div>
            <div class="w-full xl:w-4/12 px-4">
                <card-maintence-request :maintenance-requests="maintenanceRequests"/>
            </div>
        </div>
    </div>
</template>
<script>
import CardLineChart from "@/Components/Cards/CardLineChart.vue";
import CardBarChart from "@/Components/Cards/CardBarChart.vue";
import CardPropertyStatus from "@/Components/Cards/CardPropertyStatus.vue";
import CardMaintenceRequest from "@/Components/Cards/CardMaintenceRequest.vue";

export default {
    name: "dashboard-page",
    components: {
        CardLineChart,
        CardBarChart,
        CardPropertyStatus,
        CardMaintenceRequest
    },
    props: {
        monthlyIncome: {
            type: Array,
            default: () => [],
        },
        occupancyData: {
            type: Array,
            default: () => [],
        },
        maintenanceRequests: {
            type: Array,
            default: () => [],
        },
        propertiesData:{
            type: Array,
            default: () => [],
        }

    },
};

</script>
