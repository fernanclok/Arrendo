<script setup>
import CustomButton from '@/Components/CustomButton.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
// Detalles de contrato

// Fecha de inicio y fin de contrato y nombre del tenant de contrato
// Informacion de la propiedad     ||    Informacion del Tenant
// Imagenes y Documentos subidos

const contract = ref({});
const loading = ref(true);
console.log(contract)
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
    return /\.(jpg|jpeg|png|gif)$/i.test(file);
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
        <h1 class="text-4xl font-bold text-gray-800 text-center mb-8">Detalles del Contrato</h1>
      </div>
  
      <nav v-if="loading" class="w-full h-full flex justify-center items-center text-center mt-8">
        <i class="mdi mdi-loading text-gray-400 text-4xl animate-spin"></i>
        <p class="text-gray-400 text-xl ml-2">Cargando...</p>
      </nav>
  
      <nav v-else class="p-8">
        <!-- Contract Information -->
        <div class="bg-white shadow-lg rounded-lg p-6 space-y-6 border border-gray-200 ">
            <h2 class="text-3xl font-semibold text-gray-800">{{ contract.tenant_user.first_name }} {{ contract.tenant_user.last_name }}</h2>
            <div class="flex justify-between text-gray-600 text-sm">
              <div>
                <p class="font-medium text-gray-700">Fecha de inicio</p>
                <p class="text-gray-500">{{ contract.start_date }}</p>
              </div>
              <div>
                <p class="font-medium text-gray-700">Fecha de fin</p>
                <p class="text-red-600">{{ contract.end_date }}</p>
              </div>
            </div>
          </div>
        <section class="py-8">
          <!-- Property Details -->
          <div class="bg-white shadow-lg rounded-lg p-6 space-y-6 border border-gray-200">
            <h2 class="text-3xl font-semibold text-gray-800">Detalles de la Propiedad</h2>
            <div class="space-y-4">
              <p class="font-medium text-gray-700">Dirección</p>
              <p class="text-gray-500">{{ contract.property.street }} {{ contract.property.number }}, {{ contract.property.city }} {{ contract.property.state }}, {{ contract.property.postal_code }}</p>
            </div>
            <div class="space-y-4">
              <p class="font-medium text-gray-700">Especificaciones</p>
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-gray-500">
                <p><i class="mdi mdi-bed px-2" /> {{ contract.property.total_rooms }} Habitaciones</p>
                <p><i class="mdi mdi-bathtub px-2" /> {{ contract.property.total_bathrooms }} Baños</p>
                <p><i class="mdi mdi-select-place px-2" /> {{ contract.property.total_m2 }} m²</p>
              </div>
            </div>
            <div class="space-y-4">
              <p class="font-medium text-gray-700">Monto de Renta</p>
              <p class="text-gray-500">${{ contract.rental_amount }} MXN</p>
            </div>
            <div>
              <p class="font-medium text-gray-700">Imágenes</p>
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <img v-for="(image, index) in contract.property.property_photos_path" :key="index" :src="`/${image}`" class="w-full h-40 object-cover rounded-lg shadow-md" alt="Property Image" />
              </div>
            </div>
          </div>
        </section>
  
        <!-- Tenant Details -->
        <section class="bg-white shadow-lg rounded-lg p-6 space-y-6 mb-8 border border-gray-200">
          <h2 class="text-3xl font-semibold text-gray-800">Detalles del Inquilino</h2>
          <p class="text-xl font-medium text-gray-800">{{ contract.tenant_user.first_name }} {{ contract.tenant_user.last_name }}</p>
          <div class="space-y-4 mt-4">
            <div>
              <p class="font-medium text-gray-700">Correo Electrónico</p>
              <p class="text-gray-500"><i class="mdi mdi-mail px-2" />{{ contract.tenant_user.email }}</p>
            </div>
            <div>
              <p class="font-medium text-gray-700">Teléfono</p>
              <p class="text-gray-500"><i class="mdi mdi-phone px-2" />{{ contract.tenant_user.phone }}</p>
            </div>
            <div>
              <p class="font-medium text-gray-700">Contacto de Emergencia</p>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-gray-500">
                <p><strong>Nombre:</strong> {{ contract.tenant_user.emergency_contact_name }}</p>
                <p><i class="mdi mdi-phone px-2" />{{ contract.tenant_user.emergency_contact_phone }}</p>
              </div>
            </div>
          </div>
        </section>
  
        <!-- Contract Files -->
        <section>
          <div class="bg-primary text-white text-xl font-bold p-4 rounded-lg shadow-lg mb-4">
            <h2>Archivos del Contrato</h2>
          </div>
          <div class="flex flex-wrap justify-center gap-6">
            <div v-for="(file, index) in contract.contract_path" :key="index" class="bg-white shadow-lg rounded-lg p-6 w-64 border border-gray-200">
              <div v-if="isImage(file)" class="flex flex-col items-center">
                <img :src="`/${file}`" alt="Contract Image" class="w-full h-40 object-cover rounded-lg shadow-md mb-4" />
                <a :href="`/${file}`" target="_blank" class="text-primary font-medium hover:underline">Ver Imagen</a>
              </div>
              <div v-else-if="isPDF(file)" class="flex flex-col items-center">
                <i class="mdi mdi-file-pdf-box text-red-600 text-9xl mb-4"></i>
                <a :href="`/${file}`" target="_blank" class="text-primary font-medium hover:underline">Ver Contrato</a>
              </div>
            </div>
          </div>
        </section>
  
        <!-- Go Back Button -->
        <div class="mt-8 flex justify-center">
        <Link  href="/contracts">
            <CustomButton class="bg-primary text-white py-3 px-8 rounded-lg shadow-md hover:bg-green-600 transition">
            <p class="font-semibold">Volver a Contratos</p>
          </CustomButton>
        </Link>
        </div>
      </nav>
    </DashboardLayout>
  </template>
  