<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import { ref } from 'vue';

// Estado inicial del formulario
const { props } = usePage();

const form = ref({
    description: '',
    priority: '',
    evidence: null,
});

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
    form.value.evidence = event.target.files[0];
};

const submitForm = () => {
    const formData = new FormData();
    formData.append('property_id', props.Property.id);
    formData.append('description', form.value.description);
    formData.append('priority', form.value.priority);
    if (form.value.evidence) {
        formData.append('evidence', form.value.evidence);
    }

    router.post('/maintenance/store', formData, {
        onSuccess: () => {
            showNotification('success', 'Maintenance request submitted successfully!');
        },
        onError: (errors) => {
            showNotification('error', 'There was an error submitting the request. Please try again.');
            console.error(errors);
        },
    });
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
                    <p class="text-gray-600">{{ props.Property.name }}</p>
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
                <div>
                    <label for="evidence" class="block text-gray-700">Evidence</label>
                    <input
                        id="evidence"
                        type="file"
                        accept=".jpg,.jpeg,.png,.gif"
                        @change="handleFileChange"
                        class="w-full border-gray-300 rounded shadow-sm"
                    />
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

