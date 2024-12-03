<script setup>
import CustomButton from '@/Components/CustomButton.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Notification from '@/Components/NotificationCard.vue';
import axios from 'axios';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Modal from '@/Components/Modal.vue'; 
import { Head, Link, usePage, useForm } from '@inertiajs/vue3';
import { ref, onMounted, computed,watch } from 'vue';

const user = usePage().props.auth.user;
const contracts = ref([]);
const properties = ref([]);
const isRenewalModalOpen = ref(false);
const isTerminateModalOpen = ref(false);
const filterProperty = ref(''); 
const selectedContractId = ref(null);
const currentPage = ref(1);
const contractsPerPage = 6;
const paginatedContracts = ref([]);

const notification = ref({
  show: false,
  type: '',
  title: '',
  message: '',
});

const showNotification = (data) => {
  notification.value = { ...data, show: true };
};

const closeNotification = () => {
  notification.value.show = false;
};

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

// Abrir modal de renovación
const openRenewalModal = (contractId) => {
   selectedContractId.value = contractId;
   isRenewalModalOpen.value = true;
};

// Cerrar modal de renovación
const closeRenewalModal = () => {
    isRenewalModalOpen.value = false;
    selectedContractId.value = null;
};

// Abrir modal de terminación
const openTerminateModal = (contractId) => {
    selectedContractId.value = contractId;
    isTerminateModalOpen.value = true;
};

// Cerrar modal de terminación
const closeTerminateModal = () => {
    isTerminateModalOpen.value = false;
    selectedContractId.value = null;
};

const emit = defineEmits(['show_notification']); // Definimos el evento a emitir
const form = useForm({
  contract_id: '',
  renewal_start_date: '',
  renewal_end_date: '',
  renewal_rental_amount: '2500',
  renewal_status: 'Active',
});
// Renovar contrato
const renewalContract = async (contractId) => {
  const formData = new FormData();
  formData.append('contract_id', contractId);
  formData.append('renewal_start_date', form.renewal_start_date);
  formData.append('renewal_end_date', form.renewal_end_date);
  formData.append('renewal_rental_amount', form.renewal_rental_amount);
  formData.append('renewal_status', form.renewal_status);

  try {
    const response = await axios.post(`/api/contracts/${contractId}/renew`, formData);
    // Actualizar la lista de contratos
    showNotification({
      title: 'Success',
      message: `Contract renewal successfully!`,
      type: 'success'
    });
    getContracts();
    //resetear formulario
    form.renewal_start_date = '';
    form.renewal_end_date = '';
    form.renewal_rental_amount = '2500';
    closeRenewalModal();
  } catch (error) {
    console.error(error);
    showNotification({
      title: 'Error',
      message: `Contract renewal requested Fails! Try again later.`,
      type: 'error'
    });
  }
};

// Terminar contrato
const terminateContract = async (contractId) => {
    try {
        const response = await axios.put(`/api/contracts/terminate/${contractId}`);
        // Actualizar la lista de contratos
        showNotification({
        title: 'Success',
        message: `Contracts Terminated successfully!`,
        type: 'success'
        });
        closeTerminateModal();
        getContracts();

    } catch (error) {
        console.error(error);
    }
};

// Paginación
const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
    }
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
    }
};

const totalPages = computed(() => {
    return Math.ceil(filteredContracts.value.length / contractsPerPage);
});

// Actualizar la lista de contratos paginados cuando cambie la página actual, los contratos o el criterio de búsqueda
watch([filteredContracts, currentPage], () => {
    const start = (currentPage.value - 1) * contractsPerPage;
    const end = currentPage.value * contractsPerPage;
    paginatedContracts.value = filteredContracts.value.slice(start, end);
});

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
       <div  class="block justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4"> {{ user.first_name }} {{ user.last_name }} Contracts</h1>  
          <div class="block sm:flex justify-center items-center text-center gap-8 w-full">
            <h1 class="text-lg text-gray-400  font-bold">Filter</h1>
              <select id="property_id" v-model="filterProperty" @change="filter(filterProperty)" class="w-full rounded-lg  border-gray-300 text-black">
                <option value="">Please select one</option>
                <option v-for="property in properties" :key="property.id" :value="property.id" class="text-black">
                  {{ property.street }}, {{ property.city }}, {{ property.state }}
                </option>
              </select>
              <Link href="/contracts">
                <CustomButton class="w-full"><i class="mdi mdi-file-edit text-xl"></i></CustomButton>
              </Link>
          </div>
        </div>
        <!-- Tabla de Contratos -->
        <div class="overflow-x-auto  bg-white shadow-lg rounded-lg">
          <table class="min-w-full table-auto">
            <thead class="bg-gray-100 text-center text-sm text-gray-600">
              <tr>
                <th class="px-6 py-4 font-semibold">Contract Code</th>
                <th class="px-6 py-4 font-semibold">Property</th>
                <th class="px-6 py-4 font-semibold">Property Address</th>
                <th class="px-6 py-4 font-semibold">Tenant</th>
                <th class="px-6 py-4 font-semibold">State</th>
                <th class="px-6 py-4 font-semibold">Start Date</th>
                <th class="px-6 py-4 font-semibold">End Date</th>
                <th class="px-6 py-4 font-semibold">Renewal Amount</th>
                <th class="px-6 py-4 font-semibold flex justify-center items-center text-center ">Accions</th>
              </tr>
            </thead>
            <tbody class="text-sm text-gray-700">
              <tr v-for="contract in paginatedContracts" :key="contract.id" class="border-b hover:bg-gray-50">
                <td class="px-6 py-4">{{ contract.contract_code }}</td>
                <td class="px-6 py-4">{{ contract.property.property_code }}</td>
                <td class="px-6 py-4">{{ contract.property.street }} {{ contract.property.number }}, {{ contract.property.city }} {{ contract.property.state }}, {{ contract.property.postal_code }}</td>
                <td class="px-6 py-4">{{ contract.tenant_user.first_name }} {{ contract.tenant_user.last_name }}</td>
                <td class="px-6 py-4">
                  <span :class="{
                    'bg-green-400 font-bold p-2 rounded-xl text-white': contract.status === 'Active',
                    'bg-red-400 font-bold rounded-xl text-white p-2': contract.status === 'Terminated',
                    'bg-yellow-600 font-bold rounded-xl text-white p-2': contract.status === 'Pending Renewal'
                  }">{{ contract.status }}</span>
                </td>
                <td class="px-6 py-4">{{ contract.start_date }}</td>
                <td class="px-6 py-4 text-red-500">{{ contract.end_date }}</td>
                <td class="px-6 py-4">${{ contract.rental_amount }} MXN</td>
                <td v-if="(contract.status == 'Active' )" class="px-6 flex justify-center items-center space-x-2">
                    <Link :href="`/contracts-details/${contract.id}`">
                      <SecondaryButton>Details</SecondaryButton>
                    </Link>
                    <CustomButton  @click="openTerminateModal(contract.id)" class="bg-red-500 hover:bg-red-700 text-white">Terminate</CustomButton>
                </td>
                <td v-else-if="(contract.status == 'Terminated')" class="px-6 py-4 space-x-2 flex justify-center items-center">
                    <Link :href="`/contracts-details/${contract.id}`">
                      <SecondaryButton>Details</SecondaryButton>
                    </Link>
                   
                </td>
                <td v-else-if="(contract.status == 'Pending Renewal')" class="px-6 py-4 space-x-2 flex justify-center items-center">
                    <Link :href="`/contracts-details/${contract.id}`">
                      <SecondaryButton>Details</SecondaryButton>
                    </Link>
                    <CustomButton @click="openRenewalModal(contract.id)" :key="contract.id" class="bg-yellow-500 hover:bg-yellow-700">Renewal</CustomButton>
                    <CustomButton @click="openTerminateModal(contract.id)" class="bg-red-500 hover:bg-red-700 text-white">Terminate</CustomButton>
                </td>
              </tr>
            </tbody>
          </table>
          <div class="flex justify-between items-center">
  <!-- Botón de "Previous" -->
  <SecondaryButton
    @click="prevPage" 
    :disabled="currentPage === 1" 
    :class="{
      'px-6 py-3  rounded-b-lg font-medium': true, 
      'opacity-50 cursor-not-allowed': currentPage === 1
    }" 
    class="transition-all duration-200 ease-in-out hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
  >
    Previous
  </SecondaryButton>

  <!-- Contador de páginas -->
  <div class="flex space-x-2 items-center">
    <span class="text-gray-600 font-medium">Page {{ currentPage }} of {{ totalPages }}</span>
  </div>

  <!-- Botón de "Next" -->
  <SecondaryButton
    @click="nextPage" 
    :disabled="currentPage === totalPages" 
    class="px-6 py-3  rounded-r-lg font-medium transition-all duration-200 ease-in-out " 
    :class="{'opacity-50 cursor-not-allowed': currentPage === totalPages}"
  >
    Next
  </SecondaryButton>
</div>
        </div>
      </div>
      <Notification class="absolute bottom-4 right-4"
                v-if="notification.show"
                :type="notification.type"
                :title="notification.title"
                :message="notification.message"
                @close="closeNotification"
                />
      <!-- Modal para renovar contrato -->
        <Modal :show="isRenewalModalOpen" @close="closeRenewalModal">
          <template #default>
            <nav class="p-8 bg-gray-100 shadow-lg rounded-lg">
              <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-900">Renewal Contracts</h1>
              </div>
              <div class="">
                <form  @submit.prevent="renewalContract(selectedContractId)" class="mt-2 space-y-4 p-8 rounded-lg">
                  <nav class="flex justify-center space-x-2 w-full">
                        <div class="flex flex-col justify-start items-start text-start w-full">
                        <InputLabel for="renewal_start_date" value="New Start Date" class="text-gray-900"/>
                        <TextInput
                            id="renewal_start_date"
                            type="date"
                            class="w-full rounded-lg border-gray-300 text-black"
                            v-model="form.renewal_start_date"
                            required
                            autofocus
                            autocomplete="start_date"
                        />
                        <InputError class="mt-2" :message="form.errors.renewal_start_date" />
                        </div>
                        <div class="flex flex-col justify-start items-start text-start w-full">
                        <InputLabel for="renewal_end_date" value="New End Date" class="text-gray-900"/> 
                        <TextInput
                            id="renewal_end_date"
                            type="date"
                            class="w-full rounded-lg border-gray-300 text-gray-900"
                            v-model="form.renewal_end_date"
                            required
                            autofocus
                            autocomplete="end_date"
                        />
                        <InputError class="mt-2" :message="form.errors.renewal_end_date" />
                        </div>
                    </nav>
                    <div class="flex flex-col justify-start items-start text-start">
                        <InputLabel for="renewal_rental_amount" value="New Rental Amount" class="text-gray-900" />
                        <input
                        id="renewal_rental_amount"
                        type="number"
                        class="mt-1 block w-full rounded-lg border-gray-300 text-black"
                        v-model="form.renewal_rental_amount"
                        required
                        autofocus
                        autocomplete="rental_amount"
                        @input="form.renewal_rental_amount = String($event.target.value)"
                        />
                        <InputError class="mt-2" :message="form.errors.renewal_rental_amount" />
                    </div>
                    <div class="flex justify-end items-center space-x-4 mt-6">
                      <SecondaryButton @click="closeRenewalModal">Cancel</SecondaryButton>
                      <CustomButton  :class="{ 'opacity-25': form.processing, 'bg-yellow-500 text-white': true }" :disabled="form.processing">Renewal</CustomButton>
                    </div>
                </form>
              </div>
            </nav>
          </template>
        </Modal>

      <!-- Modal para terminar contrato -->
        <Modal :show="isTerminateModalOpen" @close="closeTerminateModal">
          <template #default>
            <nav class="p-8 bg-gray-100">
              <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-900">Terminate Contract</h1>
              </div>
              <div class="p-6">
                <p class="text-red-300">Are you sure you want to end this contract?</p>
                <div class="flex justify-end items-center space-x-4 mt-6">
                  <SecondaryButton @click="closeTerminateModal">Cancel</SecondaryButton>
                  <CustomButton @click="terminateContract(selectedContractId)" class="bg-red-500 hover:bg-red-700 text-gray-900">Terminate</CustomButton>
                </div>
              </div>
            </nav>
          </template>
        </Modal>
    </DashboardLayout>
  </template>
  