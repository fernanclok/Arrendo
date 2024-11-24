<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import CustomButton from '@/Components/CustomButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue'; 
import { Head, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

// Variables reactivas
const user = usePage().props.auth.user;
const maintenanceRequests = ref([]);
const isModalOpen = ref(false); 
const selectedRequest = ref({}); 

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

            <!-- Solicitudes -->
            <div class="grid grid-cols-3 gap-4">
                <div 
                    v-if="maintenanceRequests.length > 0" 
                    v-for="request in maintenanceRequests" 
                    :key="request.id" 
                    class="p-4 border rounded-lg shadow flex flex-col justify-between"
                >
                    <div>
                        <h2 class="text-lg font-bold">{{ request.description }}</h2>
                        <p class="text-sm text-gray-1000">Date of dispatch: {{ request.report_date }}</p>
                        <p class="text-sm text-gray-1000">Status: {{ request.status }}</p>
                        <p :class="{
                            'text-white px-1 py-1 rounded': true,
                            'bg-red-400': request.priority === 'High',
                            'bg-yellow-400': request.priority === 'Medium',
                            'bg-green-400': request.priority === 'Low'}">
                            Priroty: {{ request.priority }}
                        </p>
                    </div>

                    <!-- Botón para abrir detalles -->
                    <div class="flex justify-end mt-4">
                        <SecondaryButton @click="openDetails(request)">
                            View Details
                        </SecondaryButton>
                    </div>
                </div>

                <div v-else>
                    <p class="text-center text-gray-500">No maintenance requests found</p>
                </div>
            </div>
        </section>

        <!-- Modal -->
        <Modal :show="isModalOpen" @close="closeModal">
            <template #default>
                <div class="p-6">
                    <h2 class="text-xl font-bold mb-4">Request Details</h2>
                    <!-- Campo para editar Description -->
                    <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="description" v-model="selectedRequest.description" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>  
                    </div>
                    <p><strong>Report Date:</strong> {{ selectedRequest?.report_date }}</p>
                    <!-- Campo para editar Priority -->
                    <div class="mb-4">
                        <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                        <select
                        id="priority" v-model="selectedRequest.priority" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" >    
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                        </select>
                    </div>
                    <p><strong>Property:</strong> {{ selectedRequest?.property?.street || 'N/A' }}</p>
                    <!-- Mostrar imagen de Evidence -->
                    <div v-if="selectedRequest.evidence" class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Uploaded Image</label>
                        <img
                            :src="selectedRequest.evidence"
                            alt="Uploaded Evidence"
                            class="mt-2 max-w-full h-auto rounded-md border" />
                    </div>
                    <div class="flex justify-end gap-8 mt-4">
                        <SecondaryButton @click="closeModal">
                            Close
                        </SecondaryButton>
                        <!-- Botón Guardar -->
                        <SecondaryButton @click="saveChanges">
                            Save
                        </SecondaryButton>
                    </div>
                </div>
            </template>
        </Modal>
    </DashboardLayout>
</template>
