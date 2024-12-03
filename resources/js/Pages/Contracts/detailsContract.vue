<script setup>
import CustomButton from '@/Components/CustomButton.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
// Detalles de contrato

// Fecha de inicio y fin de contrato y nombre del tenant de contrato
// Informacion de la propiedad     ||    Informacion del Tenant
// Imagenes y Documentos subidos
const user = usePage().props.auth.user;
const contract = ref({});
const terminated = ref(contract.value.terminations);
const loading = ref(true);

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};
// Obtener el id del contrato de la URL
const getContractIdFromUrl = () => {
    const pathSegments = window.location.pathname.split('/');
    return pathSegments[pathSegments.length - 1];
};


const contractId = getContractIdFromUrl();
// Función para obtener los detalles del contrato
const getContractDetails = async () => {
    try {
        const response = await axios.get(`/api/contracts/${contractId}`);
        contract.value = response.data;
        // Convertir property_photos_path en un array de URLs
        contract.value.property.property_photos_path = JSON.parse(contract.value.property.property_photos_path);
        // Convertir contract_path en un array de URLs si es necesario
        contract.value.contract_path = JSON.parse(contract.value.contract_path);
    } catch (error) {
        console.error(error);
    }finally {
        loading.value = false; // Ocultar el indicador de carga
    }
};
// Función para verificar si el archivo es una imagen
const isImage = (file) => {
    return /\.(jpg|jpeg|png)$/i.test(file);
};

// Función para verificar si el archivo es un PDF
const isPDF = (file) => {
    return /\.pdf$/i.test(file);
};

// Obtener los detalles del contrato cuando el componente se monta
onMounted(() => {
    getContractDetails();
});
</script>
<template>
    <Head title="Contracts" />
    <DashboardLayout>
      <div class=" bg-white">
        <h1 class="text-4xl font-bold text-gray-800 text-center mb-8">Contract Details</h1>
        
      </div>
  
      <nav v-if="loading" class="w-full h-full flex justify-center items-center text-center mt-8">
        <i class="mdi mdi-loading text-gray-400 text-4xl animate-spin"></i>
        <p class="text-gray-400 text-xl ml-2">Loading...</p>
      </nav>
  
      <nav v-else class="p-8">
        <!-- Contract Information -->
        <div class="bg-white shadow-lg rounded-lg p-6 space-y-6 border border-gray-200 ">
            <div class=" block sm:flex justify-between items-center text-center">
                <h2 class="text-3xl font-semibold text-gray-800">Tenant: {{ contract.tenant_user.first_name }} {{ contract.tenant_user.last_name }}</h2>
                <div class="flex justify-center items-center text-center gap-8 text-gray-600 text-sm">
              <div>
                <p class="font-medium text-gray-700">Start Date</p>
                <p class="text-gray-500">{{ contract.start_date }}</p>
              </div>
              <div>
                <p class="font-medium text-gray-700">End Date</p>
                <p class="text-red-600">{{ contract.end_date }}</p>
              </div>
            </div>
            <p :class="{
                                        'text-sm justify-end text-end items-end font-bold  px-4 py-1 rounded-md':true,
                                        'bg-gradient-to-l from-green-500 to-white from-10%': contract.status == 'Active',
                                        'bg-gradient-to-l from-yellow-500 to-white from-10%': contract.status == 'Pending Renewal',
                                        'bg-gradient-to-l from-red-500 to-white from-10%': contract.status == 'Terminated'
                                        }"> {{ contract.status }}</p>
            </div>
            <div v-for="termination in contract.terminations" :key="termination.id">
                    <div>
                      <p class="mt-4 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 "> 
                        You only need to pay the invoice up to.  <strong>{{ formatDate(termination.created_at) }}</strong></p> 
                    </div>
              </div>
            
          </div>
        <section class="py-8">
          <!-- Property Details -->
          <div class="bg-white shadow-lg rounded-lg p-6 space-y-6 border border-gray-200">
            <h2 class="text-3xl font-semibold text-gray-800">Property {{ contract.property.property_code }} Details</h2>
            <div class="space-y-4">
              <p class="font-medium text-gray-700">Address</p>
              <p class="text-gray-500">{{ contract.property.street }} {{ contract.property.number }}, {{ contract.property.city }} {{ contract.property.state }}, {{ contract.property.postal_code }}</p>
            </div>
            <div class="space-y-4">
              <p class="font-medium text-gray-700">Specifications</p>
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-gray-500">
                <p><i class="mdi mdi-bed px-2" /> {{ contract.property.total_rooms }} Rooms</p>
                <p><i class="mdi mdi-bathtub px-2" /> {{ contract.property.total_bathrooms }} Bathrooms</p>
                <p><i class="mdi mdi-select-place px-2" /> {{ contract.property.total_m2 }} m²</p>
              </div>
            </div>
            <div class="space-y-4">
              <p class="font-medium text-gray-700">Rental Amount</p>
              <p class="text-gray-500">${{ contract.rental_amount }} MXN</p>
            </div>
            <div>
              <p class="font-medium text-gray-700">Images</p>
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <img v-for="(image, index) in contract.property.property_photos_path" :key="index" :src="`/${image}`" class="w-full h-40 object-cover rounded-lg shadow-md" alt="Property Image" />
              </div>
            </div>
          </div>
        </section>
  
        <!-- Tenant Details -->
        <section class="bg-white shadow-lg rounded-lg p-6 space-y-6 mb-8 border border-gray-200">
          <h2 class="text-3xl font-semibold text-gray-800">Tenant Details</h2>
          <p class="text-xl font-medium text-gray-800">{{ contract.tenant_user.first_name }} {{ contract.tenant_user.last_name }}</p>
          <div class="space-y-4 mt-4">
            <div>
              <p class="font-medium text-gray-700">Email</p>
              <p class="text-gray-500"><i class="mdi mdi-mail px-2" />{{ contract.tenant_user.email }}</p>
            </div>
            <div>
              <p class="font-medium text-gray-700">Phone Number</p>
              <p class="text-gray-500"><i class="mdi mdi-phone px-2" />{{ contract.tenant_user.phone }}</p>
            </div>
            <div>
              <p class="font-medium text-gray-700">Emergency Contact</p>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-gray-500">
                <p><strong>Name:</strong> {{ contract.tenant_user.emergency_contact_name }}</p>
                <p><i class="mdi mdi-phone px-2" />{{ contract.tenant_user.emergency_contact_phone }}</p>
              </div>
            </div>
          </div>
        </section>
  
        <!-- Contract Files -->
        <section>
          <div class="bg-primary text-white text-xl font-bold p-4 rounded-lg shadow-lg mb-4">
            <h2>Contracts Files</h2>
          </div>
          <div class="flex flex-wrap justify-center gap-6">
            <div v-for="(file, index) in contract.contract_path" :key="index" class="bg-white shadow-lg rounded-lg p-6 w-64 border border-gray-200">
              <div v-if="isImage(file)" class="flex flex-col items-center">
                <img :src="`/${file}`" alt="Contract Image" class="w-full h-40 object-cover rounded-lg shadow-md mb-4" />
                <a :href="`/${file}`" target="_blank" class="text-primary font-medium hover:underline">Show Image</a>
              </div>
              <div v-else-if="isPDF(file)" class="flex flex-col items-center">
                <i class="mdi mdi-file-pdf-box text-red-600 text-9xl mb-4"></i>
                <a :href="`/${file}`" target="_blank" class="text-primary font-medium hover:underline">Show Contract</a>
              </div>
            </div>
          </div>
        </section>
  
        <!-- Go Back Button -->
        <div v-if="(user.role == 'Owner')" class="mt-8 flex justify-center">
        <Link  href="/all-contracts">
            <CustomButton class="bg-primary text-white py-3 px-8 rounded-lg shadow-md hover:bg-green-600 transition">
            <p class="font-semibold">Back to Contracts</p>
          </CustomButton>
        </Link>
        </div>
        <div v-else class="mt-8 flex justify-center">
        <Link  href="/all-contracts/tenant">
            <CustomButton class="bg-primary text-white py-3 px-8 rounded-lg shadow-md hover:bg-green-600 transition">
            <p class="font-semibold">Back to Contracts</p>
          </CustomButton>
        </Link>
        </div>
      </nav>
    </DashboardLayout>
  </template>
  