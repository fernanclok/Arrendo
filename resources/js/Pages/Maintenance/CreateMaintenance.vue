<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head, usePage,router } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';

// Estado inicial del formulario
const { props } = usePage();

const user = usePage().props.auth.user;

const form = ref({
    tenant_user_id: user.id,
    description: '',
    priority: '',
    evidence: null,
});

// Referencia para la vista previa de la imagen
const imagePreview = ref(null);

// Referencia al input de archivo
const fileInput = ref(null);

// Estado de las notificaciones
const notification = ref({
    type: '', // 'success' o 'error'
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
    // Libera la URL de la imagen actual
    if (imagePreview.value) {
        URL.revokeObjectURL(imagePreview.value);
    }

    // Limpiar la referencia de la imagen y el archivo en el formulario
    form.value.evidence = null;
    imagePreview.value = null;

    // Reiniciar el valor del input de archivo
    if (fileInput.value) {
        fileInput.value.value = ''; // Restablecer el valor del input
    }
};

const submitForm = async () => {
    const formData = new FormData();
    formData.append('property_id', props.Property.id);
    formData.append('tenant_user_id', form.value.tenant_user_id);
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

        router.get('/maintenance');
    } catch (error) {
        const errorMessage = error.response?.data?.message || 'There was an error submitting the request.';
        showNotification('error', errorMessage);
        console.error(error);
    }
};
</script>

<template>
    <DashboardLayout>
        <Head title="Create Maintenance Request" />
        <section class="p-8">
            <h1 class="text-2xl font-bold mb-4">Create Maintenance Request</h1>

            <!-- Notificación -->
            <div
                v-if="notification.visible"
                :class="[ 
                    'fixed top-5 right-5 px-4 py-2 rounded shadow-lg text-white',
                    notification.type === 'success' ? 'bg-green-500' : 'bg-red-500'
                ]"
            >
                {{ notification.message }}
            </div>

            <form class="space-y-4" enctype="multipart/form-data" @submit.prevent="submitForm">
                <input type="hidden" :value="props.Property.id" name="property_id" />

                <!-- Propiedad asociada -->
                <div>
                    <label class="block text-gray-700">Property</label>
                    <p class="text-gray-600">{{ props.Property.street }}</p>
                </div>

                <!-- Descripción -->
                <div>
                    <label for="description" class="block text-gray-700">Description</label>
                    <textarea
                        id="description"
                        v-model="form.description"
                        class="w-full border-gray-300 rounded shadow-sm"
                        rows="4"
                        placeholder="Describe the issue..."
                    ></textarea>
                </div>

                <!-- Prioridad -->
                <div>
                    <label for="priority" class="block text-gray-700">Priority</label>
                    <select
                        id="priority"
                        v-model="form.priority"
                        class="w-full border-gray-300 rounded shadow-sm"
                    >
                        <option value="" disabled>Select Priority</option>
                        <option value="High">High</option>
                        <option value="Medium">Medium</option>
                        <option value="Low">Low</option>
                    </select>
                </div>

                <!-- Evidencia -->
                <div class="flex items-center space-x-4">
                    <div class="flex-1">
                        <label for="evidence" class="block text-gray-700">Evidence</label>
                        <input
                            id="evidence"
                            type="file"
                            accept=".jpg,.jpeg,.png,.gif"
                            @change="handleFileChange"
                            class="w-full border-gray-300 rounded shadow-sm"
                            ref="fileInput"
                        />
                    </div>

                    <!-- Vista previa -->
                    <div v-if="imagePreview" class="w-64">
                        <p class="text-gray-700">Preview:</p>
                        <img :src="imagePreview" alt="Evidence Preview" class="w-64 h-64 object-contain border rounded" />
                        <button @click="removeFile" type="button" class="mt-2 text-sm text-red-500">Remove File</button>
                    </div>
                </div>

                <!-- Botón para enviar -->
                <div>
                    <button
                        type="submit"
                        :disabled="!form.description || !form.priority || !form.evidence"
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 disabled:bg-gray-300"
                    >
                        Submit Request
                    </button>
                </div>
            </form>
        </section>
    </DashboardLayout>
</template>
