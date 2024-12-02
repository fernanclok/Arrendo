<template>
    <div class="relative">
        <header-stats :card-stats="cardStats" :auth="auth"/>
        <div class="px-4 md:px-10 mx-auto w-full -m-24">
            <component :is="component" :monthly-income="monthlyIncome" :occupancyData="occupancyData"
                :maintenanceRequests="requests" :propertiesData="propertiesData" :auth="auth" />
        </div>
    </div>
</template>
<script>
import { computed } from 'vue';
import Settings from '@/Pages/Dashboard/Settings.vue';
import Charts from '@/Pages/Dashboard/Charts.vue';
import HeaderStats from '@/Components/Headers/HeaderStats.vue';
import FooterDashboard from '@/Components/Footers/FooterDashboard.vue';


export default {
    components: {
        HeaderStats,
        Settings,
        Charts,
        FooterDashboard
    },
    props: {
        auth: { type: Object, required: true, default: () => [] },
        cardStats: { type: Array, required: true, default: () => [] },
        monthlyIncome: { type: Array, required: true },
        occupancyData: { type: Object, required: true },
        requests: { type: Array, required: true, default: () => [] },
        propertiesData: { type: Object, required: true },
        childComponent: { type: String, required: true },
    },

    setup(props) {

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

        return {
            component
        }
    },
    mounted() {

    },
}



</script>
