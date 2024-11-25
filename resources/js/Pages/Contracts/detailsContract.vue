<script setup>
import CustomButton from '@/Components/CustomButton.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
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
const backContracts = () => {
    window.location.href = '/contracts';
};
// Obtener los detalles del contrato cuando el componente se monta
onMounted(() => {
    getContractDetails();
});
</script>

<template>
    <Head title="Contracts" />
        <DashboardLayout>
            <div>
                <h1 class="text-2xl font-bold text-center">Detalles de contrato </h1>
            </div>
            <nav v-if="loading" class="w-full h-full flex justify-center items-center text-center mt-8">
                    <i class="mdi mdi-loading text-gray-400 text-4xl animate-spin"></i>
                    <p class="text-gray-400 text-xl ml-2">Loading ...</p>
                </nav>
                <nav v-else>  
             <section class="flex w-full justify-center sm:justify-start items-start text-start p-4">
               <div class="flex flex-col justify-start items-center text-center bg-primary p-4 rounded-lg shadow-lg w-full">
                  
                <h1 class="text-3xl text-white font-bold text-start px-4">{{ contract.tenant_user.first_name }} {{ contract.tenant_user.last_name }}</h1>
                    <div class="flex justify-center items-center text-center space-x-12 py-2">
                        <h1 class="text-sm text-gray-200 font-bold text-start"><p class="text-white text-xs text-center">Start Date:</p>{{ contract.start_date }}</h1>
                        <h1 class="text-sm text-red-300 font-bold text-start"><p class="text-white text-xs text-center">End Date:</p> {{ contract.end_date }}</h1>
                    </div>
               </div>
            </section>
           <section class="grid grid-cols-1 sm:grid-cols-2 gap-8 p-4 ">
                <nav class="block justify-center items-center text-center bg-gray-50 shadow-lg rounded-lg">
                    <div class="p-2">
                     <h1 class="text-white font-black text-xl text-start rounded-lg p-2 bg-gradient-to-r from-orange-500 via-gray-50 to-gay-50 w-full">Property Details</h1>
                        <p class="p-2 text-gray-900 font-black mt-4">Address</p>
                        <p class="p-2 text-gray-500 font-bold"><i class="mdi mdi-map-marker-right px-3" />{{ contract.property.street }} {{ contract.property.number }}, {{ contract.property.city }} {{ contract.property.state }}, {{ contract.property.postal_code }}</p>
                        <p class="p-2 text-gray-900 font-black">Specifications</p>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 justify-center items-center text-center">
                            <p class="p-2 text-gray-500 font-bold"><i class="mdi mdi-bed px-3" />{{ contract.property.total_rooms }} Rooms</p>
                            <p class="p-2 text-gray-500 font-bold"><i class="mdi mdi-bathtub px-3" />{{ contract.property.total_bathrooms }} Bathrooms</p>
                            <p class="p-2 text-gray-500 font-bold"><i class="mdi mdi-select-place px-3" />{{ contract.property.total_m2 }} m2</p>
                        </div>
                        <div>
                            <h1 class="p-2 text-gray-900 font-black"> Rental Amount</h1>
                            <p class="p-2 text-gray-500 font-bold">${{ contract.rental_amount }} MXN</p>
                        </div>
                        <div class="">
                            <p class="p-2 text-gray-900 font-black">Images</p>
                            <div class="block sm:flex justify-center items-center text-center p-4 ">
                                <img v-for="(image, index) in contract.property.property_photos_path" :key="index" :src="`/${image}`" class="w-56 h-36 rounded-lg shadow-lg m-2 " alt="Property Image">
                            </div>
                        </div>
                    </div>
                </nav>
                <nav class="block justify-center items-center text-center bg-gray-50 shadow-lg rounded-lg">
                    <div>
                     <h1 class="text-white font-black text-xl text-start rounded-lg p-2 bg-gradient-to-r from-green-500 via-gray-50 to-gay-50">Tenant Details</h1>
                        <p class="p-4 text-xl text-gray-900 font-black">{{ contract.tenant_user.first_name }} {{ contract.tenant_user.last_name }}</p>
                        <h1 class="p-2 text-gray-900 font-black">Email</h1>
                        <p class="p-2 text-gray-500 font-bold"><i class="mdi mdi-mail px-3" />{{ contract.tenant_user.email }}</p>
                    </div>
                    <div class="block justify-center text-center items-center">
                        <h1 class="p-2 text-gray-900 font-black">Contac Information</h1>
                        <p class="p-2 text-gray-500 font-bold"><i class="mdi mdi-phone px-3" />{{ contract.tenant_user.phone }}</p>
                    </div>
                    <div class="block justify-center text-center items-center">
                        <h1 class="p-2 text-gray-900 font-black">Emergency Contact Information</h1>
                        <div class="block sm:flex justify-center items-center text-center">
                            <p class="p-2 text-gray-500 font-bold"><span class="text-gray-900 font-bold">Name:</span> {{ contract.tenant_user.emergency_contact_name }}</p>
                            <p class="p-2 text-gray-500 font-bold"><i class="mdi mdi-phone px-3" />{{ contract.tenant_user.emergency_contact_phone }}</p>
                        </div>
                    </div>
                </nav>
                
           </section>
           <nav class="">
                    <div class="bg-primary w-full flex justify-center items-center text-center rounded-lg shadow-lg">
                        <h1 class="text-white text-xl font-bold  p-4">Contract files</h1>
                    </div>
                    <div class="flex justify-center items-center text-center w-full h-fit">
                        <div class="p-4 mt-4 block sm:flex justify-between items-center text-center w-full space-x-3">
                                <nav v-for="(file, index) in contract.contract_path" :key="index" class="mb-4 w-full">
                                    <div v-if="isImage(file)" class="w-full h-full flex justify-center items-center text-center bg-primary p-4 shadow-lg rounded-lg hover:bg-green-800 group cursor-pointer">
                                        <div class="flex flex-col items-center">
                                            <img :src="`/${file}`" alt="Contract Image" class="w-fit h-40 rounded-lg shadow-md" />
                                            <a :href="`/${file}`" target="_blank" class="text-white hover:underline font-bold group-hover:underline mt-2">View Image</a>
                                        </div>
                                    </div>
                                    <div v-else-if="isPDF(file)" class="w-full bg-primary p-8 shadow-lg rounded-lg hover:bg-green-800">
                                        <a :href="`/${file}`" target="_blank" class="text-white hover:underline block justify-center items-center text-center">
                                            <i class="mdi mdi-file-pdf-box text-white text-9xl"></i>
                                            <p class="font-bold text-sm">View Contract</p></a>
                                    </div>
                            </nav>
                        </div>
                    </div>
            </nav>
            <CustomButton @click="backContracts" class="bg-primary flex justify-center items-center text-center rounded-lg shadow-lg">
                <p class="text-white text-sm font-bold">Go back contracts</p>
            </CustomButton>
        </nav>

        </DashboardLayout>
</template>

