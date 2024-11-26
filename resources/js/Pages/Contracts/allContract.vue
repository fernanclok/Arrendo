<script setup>
import CustomButton from '@/Components/CustomButton.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { Container } from 'postcss';

const user = usePage().props.auth.user;
const contracts = ref([]);
console.log(contracts)

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

// Renovar contrato
// const renewalContract = async (contractId) => {
//     try {
//         const response = await axios.post(`/api/contracts/${contractId}/renewal`);
//         // Actualizar la lista de contratos
//         getContracts();
//     } catch (error) {
//         console.error(error);
//     }
// };
const renewalContract = (contractId) => {
    console.log(contractId);
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
        <!-- Filtros -->
        <div class="block justify-between items-center mb-6">
          <h1 class="text-2xl font-bold text-gray-800">Contracts</h1>
          <div class="flex justify-center items-center  space-x-4 w-full">
            <select class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary w-full">
              <option value="">Tenant Filter</option>
              <option value="Active">Juan</option>
              <option value="Terminated">Pedro</option>
            </select>
            <select class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary w-full">
              <option value="">Property Filter</option>
              <option value="Active">Active</option>
              <option value="Terminated">Terminated</option>
              <option value="Pending Renewal">Pending Renewal</option>
            </select>
            <button class="px-4 py-2 bg-primary text-white rounded-md hover:bg-green-600 transition">Aplicar</button>
          </div>
        </div>
  
        <!-- Tabla de Contratos -->
        <div class="overflow-x-auto bg-white shadow-lg rounded-lg">
          <table class="min-w-full table-auto">
            <thead class="bg-gray-100 text-left text-sm text-gray-600">
              <tr>
                <th class="px-6 py-4 font-semibold">Propiedad</th>
                <th class="px-6 py-4 font-semibold">Usuario</th>
                <th class="px-6 py-4 font-semibold">Estado</th>
                <th class="px-6 py-4 font-semibold">Fecha de Inicio</th>
                <th class="px-6 py-4 font-semibold">Fecha de Fin</th>
                <th class="px-6 py-4 font-semibold">Monto de Renta</th>
                <th class="px-6 py-4 font-semibold">Acciones</th>
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
                <td v-if="(contract.status == 'Active' || contract.status == 'Terminated' )" class="px-6 py-4 flex justify-center items-center">
                    <Link :href="`/contracts-details/${contract.id}`">
                    <CustomButton>Details</CustomButton>
                    </Link>
                </td>
                <td v-else class="px-6 py-4 space-x-2 flex justify-center items-center">
                    <CustomButton @click="renewalContract(contract.id)" :key="contract.id">Renewal</CustomButton>
                    <Link :href="`/contracts-details/${contract.id}`">
                    <CustomButton>Details</CustomButton>
                    </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </DashboardLayout>
  </template>
  