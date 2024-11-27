<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import CustomButton from '@/Components/CustomButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue'; 
import { Head, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, onMounted,computed } from 'vue';
import { router } from '@inertiajs/vue3';

// Variables reactivas
const user = usePage().props.auth.user;
const maintenanceRequests = ref([]);
const isModalOpen = ref(false); 
const selectedRequest = ref({}); 

const activeFilter = ref('all');
// Función para obtener solicitudes de mantenimiento
const fetchMaintenanceRequests = async () => {
    try {
        const response = await axios.get('/api/maintenance', {
            params: { user_id: user.id },
        });
        maintenanceRequests.value = response.data;
    } catch (error) {
        console.error('Error fetching maintenance requests:', error);
    }
};
// Propiedad computada para filtrar solicitudes según el estado
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
// Obtener solicitudes al montar
onMounted(fetchMaintenanceRequests);
// Función para navegar a la página de creación
const newMaintenance = () => {
    router.visit('/maintenance/new');
};
// Modal
const openDetails = (request) => {
    selectedRequest.value = {...request}; 
    isModalOpen.value = true;
};
const closeModal = () => {
    isModalOpen.value = false; 
};
//Funcion para guardar cambios en modal
const saveChanges = async () => {
    try {  
        // Validar si hay datos editados
        if (!selectedRequest.value.description || !selectedRequest.value.priority) {
            alert('Description and Priority are required.');
            return;
        }
        // Enviar los datos al backend
        await axios.patch(`/api/maintenance/${selectedRequest.value.id}`, {
            description: selectedRequest.value.description,
            priority: selectedRequest.value.priority,
        });
        // Actualizar la lista de solicitudes después de guardar los cambios
        await fetchMaintenanceRequests();
        // Cerrar el modal
        closeModal();
        alert('Changes saved successfully!');
    } catch (error) {
        console.error('Error saving changes:', error);
        alert('An error occurred while saving changes.');
    }
};
</script>

<template>
    <DashboardLayout>
        <Head title="Maintenance Requests" />
        <section class="p-8">
            <h1 class="text-2xl font-bold mb-4">Your Maintenance Requests</h1>
            <!-- Botón para nueva solicitud -->
            <div class="flex justify-end mb-7">
                <CustomButton @click="newMaintenance">
                    New Maintenance Request
                </CustomButton>
            </div>
            <!-- Botones de filtro -->
            <div class="flex justify-center text-sm w-full text-gray-500 mb-4">
                <button
                    @click="filterRequests('all')"
                    :class="{'p-2 flex justify-center items-center group rounded-lg': true, 'text-blue-700 group-hover:text-black': activeFilter === 'all'}">    
                    <i class="mdi mdi-circle pr-2 text-blue-700 group-hover:text-blue-500"></i>
                    <p class="text-xs font-bold group-hover:underline underline-offset-2">All</p>
                </button>
                <button
                    @click="filterRequests('Pending')"
                    :class="{'p-2 flex justify-center items-center group rounded-lg': true, 'text-yellow-700 group-hover:text-black': activeFilter === 'Pending'}">
                    <i class="mdi mdi-circle pr-2 text-yellow-700 group-hover:text-yellow-500"></i>
                    <p class="text-xs font-bold group-hover:underline underline-offset-2">Pending</p>
                </button>
                <button
                    @click="filterRequests('In Progress')"
                    :class="{'p-2 flex justify-center items-center group rounded-lg': true, 'text-green-700 group-hover:text-black': activeFilter === 'In Progress'}">  
                    <i class="mdi mdi-circle pr-2 text-green-700 group-hover:text-green-500"></i>
                    <p class="text-xs font-bold group-hover:underline underline-offset-2">In Progress</p>
                </button>
                <button
                    @click="filterRequests('Completed')"
                    :class="{'p-2 flex justify-center items-center group rounded-lg': true, 'text-red-700 group-hover:text-black': activeFilter === 'Completed'}">      
                    <i class="mdi mdi-circle pr-2 text-red-700 group-hover:text-red-500"></i>
                    <p class="text-xs font-bold group-hover:underline underline-offset-2">Completed</p>
                </button>
            </div>
           <!-- Solicitudes -->
            <div v-if="maintenanceRequests.length === 0" class="text-center text-gray-500">
                No maintenance requests found.
            </div>
            <div v-else class="bg-gray-100 shadow-md rounded-lg p-6">
                <!-- Contenedor de tarjetas -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div 
                        v-for="request in filteredRequests" 
                        :key="request.id" 
                        class="bg-white shadow-md rounded-lg p-4 flex flex-col justify-between"
                    >
                        <div>
                            <div class="flex justify-center items-center mb-2">
                                <h2 class="text-lg font-bold">Request Number:{{ request.id }}</h2>
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
                                <div >
                                    <p class="text-sm text-gray-600 mb-1">
                                        <strong class="block text-gray-800">Maintenance Cost:</strong>
                                        <span class="font-semibold text-green-700">
                                            {{ request.maintenance_cost ? `$${parseFloat(request.maintenance_cost).toFixed(2)}` : 'N/A' }}
                                        </span>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-gray-600 text-sm mb-2 ">
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

        </section>
        <!-- Modal -->
        <Modal :show="isModalOpen" @close="closeModal">
            <template #default>
                <div class="p-6">
                    <h2 class="text-xl font-bold mb-6 text-center">Request Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <p class="text-sm text-gray-600">
                                <strong class="block text-gray-800">Property:</strong> 
                                {{ selectedRequest?.property?.street || 'N/A' }}
                            </p>
                        </div>
                        <div class="text-right" >
                            <p class="text-sm text-gray-600">
                                <strong class="block text-gray-800">Owner Note:</strong> 
                                {{ selectedRequest?.owner_note || 'No notes provided' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">
                                <strong class="block text-gray-800">Report Date:</strong>
                                {{ new Date(selectedRequest?.report_date).toLocaleDateString() || 'No report date' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600">
                            <strong class="block text-gray-800">Dispatch Date:</strong>
                            {{ new Date(selectedRequest?.date_review).toLocaleDateString() || 'Not dispatched yet' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-4">
                                <strong class="block text-gray-800">Maintenance Cost:</strong>
                                <span class="font-semibold text-green-700">
                                    {{ selectedRequest?.maintenance_cost ? `$${parseFloat(selectedRequest.maintenance_cost).toFixed(2)}` : 'N/A' }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea 
                            id="description" 
                            v-model="selectedRequest.description" 
                            rows="4" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                        ></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                        <select 
                            id="priority" 
                            v-model="selectedRequest.priority" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                        >
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                        </select>
                    </div> 
                    <div v-if="selectedRequest.evidence" class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Uploaded Image</label>
                        <img 
                            :src="selectedRequest.evidence" 
                            alt="Uploaded Evidence" 
                            class="mt-2 max-w-full h-auto rounded-md border"
                        />
                    </div>
                    <div class="flex justify-end gap-4 mt-4">
                        <SecondaryButton @click="closeModal">
                            Close
                        </SecondaryButton>
                        <CustomButton @click="saveChanges">
                            Save Changes
                        </CustomButton>
                    </div>
                </div>
            </template>
        </Modal>
    </DashboardLayout>
</template>

