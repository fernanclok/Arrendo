<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head,usePage } from '@inertiajs/vue3';
import CustomButton from '@/Components/CustomButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue'; 
import axios from 'axios';
import { ref, onMounted, computed } from 'vue';

// Variables reactivas
const properties = ref([]);
const maintenanceRequests = ref([]); // Solicitudes de mantenimiento
const selectedProperty = ref(null); // Propiedad seleccionada
const user = usePage().props.auth.user; // Obtenemos el usuario logeado desde las props
const isModalOpen = ref(false); // Estado del modal
const selectedRequest = ref({}); // Solicitud seleccionada para mostrar en el modal



const fetchProperties = () => {
    axios
        .get('/api/properties', { params: { user_id: user.id } }) // API para obtener las propiedades del usuario
        .then((response) => {
            properties.value = response.data; // Guardamos las propiedades en el array
        })
        .catch((error) => {
            console.error(error);
        });
};
// Obtener solicitudes de mantenimiento por propiedad
const fetchMaintenanceRequests = (propertyId) => {
    selectedProperty.value = properties.value.find((prop) => prop.id === propertyId); // Guardar propiedad seleccionada
    axios
        .get('/api/maintenance/maintenancesReqOwner', { params: { property_id: propertyId } })
        .then((response) => {
            maintenanceRequests.value = response.data;
        })
        .catch((error) => {
            console.error(error);
        });
};
onMounted(() => {
    fetchProperties(); 
    fetchMaintenanceRequests();
});
//modal 
const openDetails = (request) => {
    selectedRequest.value = { ...request }; 
    isModalOpen.value = true; 
};
const closeModal = () => {
    isModalOpen.value = false;
    selectedRequest.value = null; // Reiniciar la solicitud seleccionada
};
// Guardar cambios en el status de la solicitud
const saveChanges = () => {
    if (!selectedRequest.value) return;
    const updatedData = {
        status: selectedRequest.value.status, // Estado
        owner_note: selectedRequest.value.owner_note, // Nota del owner
        maintenance_cost: selectedRequest.value.maintenance_cost, // Costo de mantenimiento
    };
    axios
        .put(`/api/maintenance/maintenancesReqOwner/${selectedRequest.value.id}`, updatedData)
        .then(() => {
            // Actualizar la lista de solicitudes después de guardar
            fetchMaintenanceRequests(selectedProperty.value.id);
            closeModal(); // Cerrar el modal
        })
        .catch((error) => {
            console.error("Error updating request:", error);
        });
};

const activeFilter = ref('all'); // Filtro activo
const filteredRequests = computed(() => {
    if (activeFilter.value === 'all') {
        return maintenanceRequests.value;
    }
    return maintenanceRequests.value.filter(request => request.status === activeFilter.value);
});

// Función para cambiar el filtro activo
const filterRequests = (filter) => {
    activeFilter.value = filter;
};

</script>

<template>
    <DashboardLayout>
        <Head title="Maintenance Requests Jobs" />
        <section class="p-8">
            <h1 class="text-2xl font-bold mb-4">Properties with Maintenance Jobs Owner</h1>
            <!-- Botones de filtro -->
            <div class="flex justify-center text-sm w-full text-gray-500 mb-4">
                <button
                    @click="filterRequests('all')"
                    :class="{'p-2 flex justify-center items-center group rounded-lg': true,'text-blue-700 group-hover:text-black': activeFilter === 'all'}">    
                    <i class="mdi mdi-circle pr-2 text-blue-700 group-hover:text-blue-500"></i>
                    <p class="text-xs font-bold group-hover:underline underline-offset-2">All</p>
                </button>
                <button
                    @click="filterRequests('Pending')"
                    :class="{'p-2 flex justify-center items-center group rounded-lg': true,'text-yellow-700 group-hover:text-black': activeFilter === 'Pending' }">
                    <i class="mdi mdi-circle pr-2 text-yellow-700 group-hover:text-yellow-500"></i>
                    <p class="text-xs font-bold group-hover:underline underline-offset-2">Pending</p>
                </button>
                <button
                    @click="filterRequests('In Progress')"
                    :class="{'p-2 flex justify-center items-center group rounded-lg': true,'text-green-700 group-hover:text-black': activeFilter === 'In Progress'}">  
                    <i class="mdi mdi-circle pr-2 text-green-700 group-hover:text-green-500"></i>
                    <p class="text-xs font-bold group-hover:underline underline-offset-2">In Progress</p>
                </button>
                <button
                    @click="filterRequests('Completed')"
                    :class="{ 'p-2 flex justify-center items-center group rounded-lg': true,'text-red-700 group-hover:text-black': activeFilter === 'Completed' }" >      
                    <i class="mdi mdi-circle pr-2 text-red-700 group-hover:text-red-500"></i>
                    <p class="text-xs font-bold group-hover:underline underline-offset-2">Completed</p>
                </button>
            </div>
            <!-- Lista de propiedades -->
            <div v-if="properties.length === 0" class="text-center text-gray-600">
                No properties available for maintenance jobs.
            </div>
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="property in properties":key="property.id"class="bg-white shadow-md rounded-lg overflow-hidden h-full flex flex-col" >
                    <div class="p-4 flex-grow">
                        <div class="flex justify-between items-center mb-2">
                                        <h2 class="text-xl font-semibold">{{ property.street }}, {{ property.number }}, {{ property.city }}, {{ property.state}}
                                        </h2>
                                        <div :class="{
                                            'px-2 py-1 text-xs font-semibold rounded-full': true,
                                            'bg-green-100 text-green-800': property.availability === 'Available',
                                            'bg-blue-100 text-blue-800': property.availability === 'Rented',
                                            'bg-yellow-100 text-yellow-800': property.availability === 'Under Maintenance'
                                        }" class="ml-2">
                                            {{ property.availability }}
                                        </div>
                                    </div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round"stroke-linejoin="round"stroke-width="2"d="M8 10h.01M12 10h.01M16 10h.01M9 16h6M4 6h16M4 6a2 2 0 012-2h12a2 2 0 012 2M4 6v12a2 2 0 002 2h12a2 2 0 002-2V6"></path>
                                </svg>
                                {{ property.total_rooms }} rooms
                            </span>
                            <span class="flex items-center">
                                <svg  class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path     stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3v4h6v-4c0-1.657-1.343-3-3-3zM5 20h14a2 2 0 002-2v-5a2 2 0 00-2-2H5a2 2 0 00-2 2v5a2 2 0 002 2z"></path>
                                </svg>
                                {{ property.total_bathrooms }} bathrooms
                            </span>
                        </div>
                        <p class="text-gray-500 text-sm">{{ property.property_details }}</p>
                    </div>
                    <div class="bg-gray-100 px-4 py-3 flex justify-end">
                        <CustomButton @click="fetchMaintenanceRequests(property.id)" type="primary">
                            View Maintenance Jobs
                        </CustomButton>
                    </div>
                </div>
            </div>
            <!-- Solicitudes de mantenimiento para la propiedad seleccionada -->
            <div v-if="selectedProperty" class="mt-8 bg-gray-100 rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4">
                    Maintenance Requests for {{ selectedProperty.street }}, {{ selectedProperty.number }}, {{ selectedProperty.city }}, {{ selectedProperty.state }}
                </h2>
                <div v-if="maintenanceRequests.length === 0" class="text-center text-gray-600">
                    No maintenance requests available for this property.
                </div>
                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div
                        v-for="request in filteredRequests"
                        :key="request.id"
                        class="bg-white shadow-md rounded-lg p-4 flex flex-col justify-between"
                    >
                        <div>
                            <div class="flex justify-center items-center mb-2">
                                <h2 class="text-lg font-bold">Type: {{ request.type }}</h2>
                            </div>
                            <div class="flex justify-center items-center mb-2">
                                <h2 class="text-sm text-gray-600 mb-2">{{ request.description }}</h2>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600 mb-2">
                                        <strong class="block text-gray-800">Reported Date:</strong>
                                        {{ new Date(request.report_date).toLocaleDateString() || 'N/A' }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-600">
                                        <strong class="block text-gray-800">Dispatch Date:</strong>
                                        {{ request.date_review ? new Date(request.date_review).toLocaleDateString() : 'Not dispatched yet' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">
                                        <strong class="block text-gray-800">Maintenance Cost:</strong>
                                        <span class="font-semibold text-green-700">
                                            {{ request.maintenance_cost ? `$${parseFloat(request.maintenance_cost).toFixed(2)}` : 'N/A' }}
                                        </span>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-gray-600 text-sm mb-2">
                                        <strong class="block text-gray-800 mb-2">Priority:</strong>
                                        <span
                                            :class="{ 
                                                'text-white px-1 py-1 rounded': true, 
                                                'bg-red-400': request.priority === 'High',
                                                'bg-yellow-300': request.priority === 'Medium', 
                                                'bg-green-300': request.priority === 'Low',
                                            }"
                                        >
                                            {{ request.priority }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-between items-center mt-4">
                            <p 
                                :class="{
                                    'text-sm font-bold px-4 py-1 rounded-md': true,
                                    'bg-yellow-100 text-yellow-800': request.status === 'Pending',
                                    'bg-green-100 text-green-800': request.status === 'In Progress',
                                    'bg-red-100 text-red-800': request.status === 'Completed',
                                }"
                            >
                                <strong>Status:</strong> {{ request.status }}
                            </p>
                            <SecondaryButton @click="openDetails(request)">
                                View Details
                            </SecondaryButton>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal -->
            <Modal :show="isModalOpen" @close="closeModal">
                <template #default>
                    <div class="p-6">
                        <h2 class="text-xl font-bold mb-6 text-center">Request Details</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <p class="text-sm text-gray-600">
                                    <strong class="block text-gray-800">Property:</strong> 
                                    {{ selectedProperty.street }}, {{ selectedProperty.number }}, {{ selectedProperty.postal_code }}, {{ selectedProperty.city }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">
                                    <strong class="block text-gray-800">Reported by:</strong> 
                                    {{ selectedRequest?.tenant_user?.first_name }} {{ selectedRequest?.tenant_user?.last_name }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">
                                    <strong class="block text-gray-800">Report Date:</strong>
                                    {{ new Date(selectedRequest?.report_date).toLocaleDateString() || 'No report date' }}
                                </p>
                            </div>
                            <div class="text-right mb-4">
                                <p class="text-sm text-gray-600">
                                    <strong class="block text-gray-800">Priority:</strong>
                                    <span 
                                        :class="{ 
                                            'text-white px-1 py-1 rounded': true, 
                                            'bg-red-400': selectedRequest?.priority === 'High',
                                            'bg-yellow-300': selectedRequest?.priority === 'Medium', 
                                            'bg-green-300': selectedRequest?.priority === 'Low',
                                        }"
                                    >
                                        {{ selectedRequest?.priority }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select 
                                id="status" 
                                v-model="selectedRequest.status" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            >
                                <option value="Pending">Pending</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="owner_note" class="block text-sm font-medium text-gray-700">Owner Note</label>
                            <textarea
                                id="owner_note"
                                v-model="selectedRequest.owner_note"
                                rows="3"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                placeholder="Add a note for the tenant..."
                            ></textarea>
                        </div>
                        <div class="mb-4">
                            <label for="maintenance_cost" class="block text-sm font-medium text-gray-700">Maintenance Cost</label>
                            <input
                                type="number"
                                id="maintenance_cost"
                                v-model="selectedRequest.maintenance_cost"
                                step="0.01"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                placeholder="Enter maintenance cost..."
                            />
                        </div>
                        <div v-if="selectedRequest?.evidence" class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Evidence</label>
                            <img
                                :src="selectedRequest.evidence"
                                alt="Uploaded Evidence"
                                class="mt-2 max-w-full h-auto rounded-md border"
                            />
                        </div>
                        <!-- Botones para guardar o cerrar -->
                        <div class="flex justify-end gap-4 mt-4">
                            <SecondaryButton @click="closeModal" type="secondary">
                                Close
                            </SecondaryButton>
                            <CustomButton @click="saveChanges" type="primary">
                                Save Changes
                            </CustomButton>
                        </div>
                    </div>
                </template>
            </Modal>
        </section>
    </DashboardLayout>
</template>
