<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

import OwnerInterface from './Dashboard/OwnerInterface.vue';
import TenantInterface from './Dashboard/TenantInterface.vue';

const { props } = usePage();

const isOwner = computed(() => {
    return props.auth.user.role === 'Owner';
});

</script>

<template>

    <Head title="Dashboard" />
    <DashboardLayout>
        <div class="relative">

            <OwnerInterface v-if="isOwner" v-bind="props" />

            <TenantInterface v-else :auth="props.auth" />

        </div>
    </DashboardLayout>
</template>
