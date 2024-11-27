<script setup>
import CustomButton from '@/Components/CustomButton.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
// import SecondaryButton from '@/Components/SecondaryButton.vue';
// import Modal from '@/Components/Modal.vue'; 

const user = usePage().props.auth.user;
const contracts = ref([]);
const properties = ref([]);
const filterProperty = ref(''); 

// obtener las propiedades
const getProperties = () => {
            axios.get('/api/properties', {
                params: {
                    user_id: user.id
                }
            })
                .then(response => {
                    properties.value = response.data;
                })
                .catch(error => {
                    console.error(error);
                });
        };


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

// Filtrar contratos por propiedad
const filteredContracts = computed(() => {
    if (filterProperty.value === '') {
        return contracts.value; // retornar todos los contratos
    }
    return contracts.value.filter(contract => contract.property_id === parseInt(filterProperty.value)); // filtrar por propiedad seleccionada
});

// Actualizar filtro
const filter = (propertyId) => {
    filterProperty.value = propertyId;
};

// Modal
// const openDetails = (request) => {
//     selectedRequest.value = {...request}; 
//     isModalOpen.value = true;
// };
// const closeModal = () => {
//     isModalOpen.value = false; 
// };
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
    getProperties();
});
</script>

<template>
    <Head title="Contracts" />
    <DashboardLayout>
      <div class="p-6">
       <!-- Filtros -->
       <div class="block justify-between items-center mb-6">
          <h1 class="text-2xl font-bold text-gray-800 mb-3">Owner's Contracts</h1>
          <div class="flex justify-center items-center space-x-4 w-full">
            <select id="property_id" v-model="filterProperty" @change="filter(filterProperty)" class="w-full rounded-lg border-gray-300 text-black">
              <option value="">Please select one</option>
              <option v-for="property in properties" :key="property.id" :value="property.id" class="text-black">
                {{ property.street }}, {{ property.city }}, {{ property.state }}
              </option>
            </select>
            <Link href="/contracts">
              <CustomButton><i class="mdi mdi-file-edit text-base"></i></CustomButton>
            </Link>
          </div>
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
              <tr v-for="contract in filteredContracts" :key="contract.id" class="border-b hover:bg-gray-50">
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
                <td v-if="(contract.status == 'Active' )" class="px-6 py-4 flex justify-center items-center space-x-2">
                    <Link :href="`/contracts-details/${contract.id}`">
                    <CustomButton>Details</CustomButton>
                    </Link>
                    <CustomButton>Terminate</CustomButton>
                </td>
                <td v-else-if="(contract.status == 'Terminated')" class="px-6 py-4 space-x-2 flex justify-center items-center">
                    <Link :href="`/contracts-details/${contract.id}`">
                    <CustomButton>Details</CustomButton>
                    </Link>
                   
                </td>
                <td v-else-if="(contract.status == 'Pending Renewal')" class="px-6 py-4 space-x-2 flex justify-center items-center">
                    <Link :href="`/contracts-details/${contract.id}`">
                    <CustomButton>Details</CustomButton>
                    </Link>
                    <CustomButton @click="renewalContract(contract.id)" :key="contract.id">Renewal</CustomButton>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
 
    </DashboardLayout>
  </template>
  