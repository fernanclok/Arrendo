<script setup>

import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head, useForm} from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3'

const maintenanceRequests = [
    {
        status: 'Pending',
        date: '2023-11-01',
        description: 'Reparación de tuberías',
        priority: 'High',
        property: 'Calle 123 #123',
    },
    {
        status: 'In Progress',
        date: '2023-11-05',
        description: 'Cambio de cerraduras',
        priority: 'Medium',
        property: 'Avenida 456 #456',
    },
    {
        status: 'Completed',
        date: '2023-11-10',
        description: 'Pintura exterior',
        priority: 'Low',
        property: 'Boulevard 789 #789',
    },
];

// Función para navegar a la página de creación
const newmaintenance = () => {
    console.log("Navigating to /maintenance/create"); // Depuración
    router.visit('/maintenance/create');};
</script>

<template>
    <DashboardLayout>
        <Head title="Maintenance Requests" />
        <section class="p-8">
            <h1 class="text-2xl font-bold mb-4">Your maintenance requests</h1>
            <div>
                <div class="flex justify-end mb-4">
                    <button @click="newmaintenance"  class="bg-blue-500 text-white px-4 py-2 rounded">
                        New maintenance request
                    </button>
                </div>
                <div class="grid grid-cols-1 gap-4">
                    <div
                        v-for="request in maintenanceRequests"
                        :key="request.date + request.property"
                        class="p-4 border rounded-lg shadow"
                    >
                        <h2 class="text-lg font-bold">{{ request.description }}</h2>
                        <p class="text-sm text-gray-600">Fecha: {{ request.date }}</p>
                        <p class="text-sm text-gray-600">Prioridad: {{ request.priority }}</p>
                        <p class="text-sm text-gray-600">Propiedad: {{ request.property }}</p>
                        <span
                            :class="{
                                'text-white px-2 py-1 rounded text-sm': true,
                                'bg-red-500': request.priority === 'High',
                                'bg-yellow-500': request.priority === 'Medium',
                                'bg-green-500': request.priority === 'Low',
                            }"
                        >
                            {{ request.priority }}
                        </span>
                    </div>
                </div>
            </div>
        </section>
    </DashboardLayout>
</template>