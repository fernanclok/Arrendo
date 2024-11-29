<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import axios from 'axios';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const user = usePage().props.auth.user;
const contracts = ref([]);

// Obtener contratos de la API
const getContracts = async () => {
    try {
        const response = await axios.get('/api/contracts',{
            params: {
                user_id: user.id,
            }
        });
        contracts.value = response.data;
    } catch (error) {
        console.error(error);
    }
};

// Llamar a getContracts cuando el componente se monte
onMounted(() => {
    getContracts();
});
</script>

<template>
    <Head title="Contracts" />
    <DashboardLayout>
      <div class="p-6">
        <div class="p-4">
            <h1 class="text-2xl font-semibold text-gray-800">My Contracts</h1>
        </div>
        <!-- Tabla de Contratos -->
        <div class="overflow-x-auto bg-white shadow-lg rounded-lg">
          <table class="min-w-full table-auto">
            <thead class="bg-gray-100 text-left text-sm text-gray-600">
              <tr>
                <th class="px-6 py-4 font-semibold">Property</th>
                <th class="px-6 py-4 font-semibold">Tenant</th>
                <th class="px-6 py-4 font-semibold">State</th>
                <th class="px-6 py-4 font-semibold">Start Date</th>
                <th class="px-6 py-4 font-semibold">End Date</th>
                <th class="px-6 py-4 font-semibold">Renewal Amount</th>
                <th class="px-6 py-4 font-semibold">Accions</th>
              </tr>
            </thead>
            <tbody class="text-sm text-gray-700">
              <tr v-for="contract in contracts" :key="contract.id" class="border-b hover:bg-gray-50">
                <td class="px-6 py-4">{{ contract.property.street }} {{ contract.property.number }}, {{ contract.property.city }} {{ contract.property.state }}, {{ contract.property.postal_code }}</td>
                <td class="px-6 py-4">{{ contract.tenant_user.first_name }} {{ contract.tenant_user.last_name }}</td>
                <td class="px-6 py-4">
                  <span :class="{
                    'text-green-600 font-bold': contract.status === 'Active',
                    'text-red-600 font-bold': contract.status === 'Terminated',
                    'text-yellow-600 font-bold': contract.status === 'Pending Renewal'
                  }">{{ contract.status }}</span>
                </td>
                <td class="px-6 py-4">{{ contract.start_date }}</td>
                <td class="px-6 py-4">{{ contract.end_date }}</td>
                <td class="px-6 py-4">${{ contract.rental_amount }} MXN</td>
                <td class="px-6 py-4 space-x-2 flex justify-center items-center">
                    <Link :href="`/contracts-details/${contract.id}`">
                      <SecondaryButton>Details</SecondaryButton>
                    </Link>
                   
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
  </DashboardLayout>
  </template>
  