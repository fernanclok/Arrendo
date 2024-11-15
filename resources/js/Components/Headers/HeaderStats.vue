<template>
  <!-- Header -->
  <div class="relative bg-primary md:pt-32 pb-32 pt-12">
    <div class="px-4 md:px-10 mx-auto w-full sm:mt-10 xs:mt-10">
      <div>
        <!-- Card stats -->
        <div class="flex flex-wrap">
          <div class="w-full lg:w-6/12 xl:w-3/12 px-4">
            <card-stats
              statSubtitle="Active Properties"
              statTitle="10"
              statPercent="5"
              statPercentColor="text-emerald-500"
              statDescripiron="Change since last month"
              statIconName="fas fa-home"
              statIconColor="bg-blue-500"
            />
          </div>
          <div class="w-full lg:w-6/12 xl:w-3/12 px-4">
            <card-stats
              statSubtitle="Ocupacy Rate"
              statTitle="85%"
              statArrow="up"
              statPercent="10"
              statPercentColor="text-emeral-500"
              statDescripiron="Compared to last month"
              statIconName="fas fa-chart-line"
              statIconColor="bg-green-500"
            />
          </div>
          <div class="w-full lg:w-6/12 xl:w-3/12 px-4">
            <card-stats
              statSubtitle="Estimated Income"
              statTitle="$12,000"
              statArrow="up"
              statPercent="15"
              statPercentColor="text-emeral-500"
              statDescripiron="Estimated this month"
              statIconName="fas fa-dollar-sign"
              statIconColor="bg-yellow-500"
            />
          </div>
          <div class="w-full lg:w-6/12 xl:w-3/12 px-4">
            <card-stats
              statSubtitle="Pending Payments"
              statTitle="3"
              statArrow="down"
              statPercent="20"
              statPercentColor="text-red-500"
              statDescripiron="Overdue payments"
              statIconName="fas fa-exclamation-circle"
              statIconColor="bg-red-500"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import CardStats from "@/Components/Cards/CardStats.vue";

export default {
  components: {
    CardStats,
  },
};
</script>
