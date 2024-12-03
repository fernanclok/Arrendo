<template> <!-- Header Section -->
    <div class="p-2">
            <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
          </div>
    <div class="relative bg-primary ml-3 md:pt-10 pb-32 pt-12">
        <div class="px-4 md:px-10 mx-auto w-full sm:mt-10 xs:mt-10">


            <!-- Cards Section -->
            <div>
                <div class="flex flex-wrap">
                    <div v-for="(card, index) in cardStats" :key="index" class="w-full lg:w-6/12 xl:w-3/12 px-4">
                        <CardStats :statSubtitle="card.statSubtitle" :statTitle="card.statTitle"
                            :statArrow="card.statArrow || ''" :statPercent="card.statPercent"
                            :statPercentColor="card.statPercentColor" :statDescripiron="card.statDescripiron"
                            :statIconName="card.statIconName" :statIconColor="card.statIconColor" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>



<script>
import CardStats from "@/Components/Cards/CardStats.vue";
import { Link } from "@inertiajs/vue3";
import NotificationDropdown from "../Dropdowns/NotificationDropdown.vue";

export default {
    props: {
        auth: {
            type: Object,
            required: true,
            default: () => ({}),
        },
        cardStats: {
            type: Array,
            required: true,
            default: () => [],
        },
    },
    components: {
        CardStats,
        Link,
        NotificationDropdown,
    },
};
</script>
