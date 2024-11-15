<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import AdminNavbar from "@/Components/Navbars/AdminNavbar.vue";
import HeaderStats from "@/Components/Headers/HeaderStats.vue";
import { computed } from 'vue';
import Charts from './Dashboard/Charts.vue';
import Settings from './Dashboard/Settings.vue';
import FooterDashboard from '@/Components/Footers/FooterDashboard.vue';

const { props } = usePage();

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
    <div class="">
        <div class="relative md:ml- bg-blueGray-100">
            <admin-navbar />
            <header-stats />
            <div class="px-4 md:px-10 mx-auto w-full -m-24">
                <component :is="component" />
                <FooterDashboard />
            </div>
        </div>
    </div>
</template>
