<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import AdminNavbar from "@/Components/Navbars/AdminNavbar.vue";
import HeaderStats from "@/Components/Headers/HeaderStats.vue";
import { computed } from 'vue';
import Charts from './Dashboard/Charts.vue';
import Settings from './Dashboard/Settings.vue';
import FooterDashboard from '@/Components/Footers/FooterDashboard.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const { props } = usePage();

console.log(props.auth);

const component = computed(() => {

    switch (props.childComponent) {
        case 'Settings':
            return Settings;
        case 'Charts':
            return Charts;
        default:
            return Charts;
    }
});
</script>

<template>

    <Head title="Dashboard" />
    <DashboardLayout :auth="props.auth">
        <div class="relative md:ml- bg-blueGray-100">
            <header-stats :card-stats="props.cardStats" />
            <div class="px-4 md:px-10 mx-auto w-full -m-24">
                <component :is="component" :monthly-income="props.monthlyIncome" :occupancyData="props.occupancyData" :maintenanceRequests="props.requests" :propertiesData="props.propertiesData" :auth="props.auth"/>
                <FooterDashboard />
            </div>
        </div>
    </DashboardLayout>
</template>
