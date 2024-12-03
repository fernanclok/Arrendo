<script setup>
import { Head, usePage,router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';

// Estado inicial del formulario

const user = usePage().props.auth.user;
const emit = defineEmits(['requestCreated']);

const form = ref({
    tenant_user_id: user.id,
    type: '',
    description: '',
    priority: '',
    evidence: null,
});

const property = ref(null);
const imagePreview = ref(null);
const fileInput = ref(null);

// Estado de las notificaciones
const notification = ref({
    type1: '', // 'success' o 'error'
    message: '',
    visible: false,
});

// Función para mostrar notificaciones
const showNotification = (type, message) => {
    notification.value = { type, message, visible: true };
    setTimeout(() => {
        notification.value.visible = false;
    }, 3000); // Oculta la notificación después de 3 segundos
};

const handleFileChange = (event) => {
    const file = event.target.files[0]; // Obtén el primer archivo seleccionado
    if (file) {
        // Libera la URL anterior si existe
        if (imagePreview.value) {
            URL.revokeObjectURL(imagePreview.value);
        }

        // Generar una nueva URL para previsualizar la imagen
        imagePreview.value = URL.createObjectURL(file);
        form.value.evidence = file;
    }
};

const removeFile = () => {
    if (imagePreview.value) {
        URL.revokeObjectURL(imagePreview.value);
    }
    form.value.evidence = null;
    imagePreview.value = null;
    if (fileInput.value) {
        fileInput.value.value = ''; 
    }
};

const fetchProperty = async () => {
    try {
        const response = await axios.get('/api/maintenance/getPropertyByTenant', {
            params: {
                tenant_user_id: user.id, // Enviar como parámetro de consulta
            },
        });
        console.log('API Response:', response.data); // Log de la respuesta
        if (response.data && response.data.property) {
    property.value = response.data.property;
} else {

    property.value = null;
    showNotification('error', 'No property found for your account.');
}
    } catch (error) {
        console.error('Error fetching property:', error);
        property.value = null;
        showNotification('error', 'Could not fetch property. Please try again later.');
    }
};
onMounted(() => {
    fetchProperty();
});

const submitForm = async () => {

    if (!property.value || !property.value.id) {
        showNotification('error', 'No property selected. Please try again.');
        return;
    }

    const formData = new FormData();
    formData.append('property_id', property.value.id);
    formData.append('tenant_user_id', form.value.tenant_user_id);
    formData.append('type', form.value.type);
    formData.append('description', form.value.description);
    formData.append('priority', form.value.priority);
    if (form.value.evidence) {
        formData.append('evidence', form.value.evidence);
    }
    try {
        const response = await axios.post('/api/maintenance/store', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
        showNotification('success', response.data.message || 'Maintenance request submitted successfully!');
        emit('requestCreated');
        
    } catch (error) {
        const errorMessage = error.response?.data?.message || 'There was an error submitting the request.';
        showNotification('error', errorMessage);
        console.error(error);
    }
};

</script>

<template>
    
        
        <section class="p-8">
            <h1 class="text-2xl font-bold mb-4">Create Maintenance Request</h1>
            <!-- Notificación -->
            <div v-if="notification.visible":class="[  'fixed top-5 right-5 px-4 py-2 rounded shadow-lg text-white',  notification.type === 'success' ? 'bg-green-500' : 'bg-red-500'  ]">
                {{ notification.message }}
            </div>
            <form class="space-y-4" enctype="multipart/form-data" @submit.prevent="submitForm">
                <input v-if="property" type="hidden" :value="property.id" name="property_id" />
                <div v-if="property && property.id" class="flex items-center gap-2">
                    <label class="text-gray-1000">My property:</label>
                    <p class="text-gray-500">
                        {{ property.street }},{{ property.number }}, {{ property.city }}, {{ property.state }}
                    </p>
                </div>
                <div v-else>
                    <p class="text-red-500">No property available for your account.</p>
                </div>
                <!-- Type -->
                <div>
                    <label for="type" class="block text-gray-1000">Type of problem</label>
                    <select id="type" v-model="form.type"class="w-full border-gray-300 focus:border-green-700 focus:ring-green-700 rounded-md">        
                        <option value="" disabled>Select Type of problem</option>
                        <option value="Electrical">Electrical</option>
                        <option value="Flooring">Flooring</option>
                        <option value="Plumbing">Plumbing</option>
                        <option value="Pest Control">Pest Control</option>
                        <option value="Roofing">Roofing</option>
                        <option value="Structural">Structural</option>
                    </select>
                </div>
                <!-- Descripción -->
                <div>
                    <label for="description" class="block text-gray-1000">Description</label>
                    <textarea
                        id="description"
                        v-model="form.description"
                        class="w-full border-gray-300 focus:border-green-700 focus:ring-green-700 rounded-md"
                        rows="4"
                        placeholder="Describe the issue..."
                    ></textarea>
                </div>
                <!-- Prioridad -->
                <div>
                    <label for="priority" class="block text-gray-1000">Priority</label>
                    <select id="priority" v-model="form.priority"class="w-full border-gray-300 focus:border-green-700 focus:ring-green-700 rounded-md">        
                        <option value="" disabled>Select Priority</option>
                        <option value="High">High</option>
                        <option value="Medium">Medium</option>
                        <option value="Low">Low</option>
                    </select>
                </div>
                <!-- Evidencia -->
                <div class="flex items-center space-x-4">
                    <div class="flex-1">
                        <label for="evidence" class="block text-gray-1000">Evidence</label>
                        <label for="evidence"class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 cursor-pointer"> 
                            Upload Evidence
                        </label>
                        <input id="evidence" type="file" accept=".jpg,.jpeg,.png"  @change="handleFileChange" class="hidden" ref="fileInput" />
                    </div>
                    <!-- Vista previa -->
                    <div v-if="imagePreview" class="w-64">
                        <p class="text-gray-1000 flex justify-end mt-1">Preview:</p>
                        <img :src="imagePreview" alt="Evidence Preview" class="w-64 h-64 object-contain border rounded" />
                        <div class="flex justify-end mt-1">
                            <button @click="removeFile" type="button" class="text-sm text-red-500">
                                Remove File
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Botón para enviar -->
                <div>
                    <button type="submit" :disabled="!form.description || !form.priority || !form.evidence" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 bg-primary text-white hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:ring-green-500 disabled:bg-gray-300 disabled:text-gray-700 disabled:cursor-not-allowed">  
                            Submit Request
                    </button>
                </div>
            </form>
        </section>
    
</template>
