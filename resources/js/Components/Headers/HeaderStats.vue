<template>
    <div class="relative bg-primary md:pt-10 pb-32 pt-12">
        <div class="px-4 md:px-10 mx-auto w-full sm:mt-10 xs:mt-10">
            <!-- Brand -->
            <Link class="text-white text-sm uppercase lg:inline-block font-semibold p-4" href="/dashboard">
            Dashboard
            </Link>
            <Link href="/dashboard/settings" class="text-white text-sm uppercase lg:inline-block font-semibold p-4">
            settings
            </Link>
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

export default {
    props: {
        cardStats: {
            type: Array,
            required: true,
            default: () => [],
        },
    },
    components: {
        CardStats,
        Link
    },
};
</script>
