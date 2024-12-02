<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import CustomButton from '@/Components/CustomButton.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import Identity from '../Components/Identity.vue';
import { usePage, useForm } from '@inertiajs/vue3';
</script>

<template>

    <Head title="Appointments" />

    <DashboardLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden">
                    <div class="p-6">
                        <h1 class="text-2xl font-semibold text-gray-900 mb-6">Your Appointments</h1>

                        <div v-if="appointments.length === 0"
                            class="flex items-center justify-center h-64 text-gray-600 text-lg">
                            You have no appointments scheduled.
                        </div>

                        <div v-else class="space-y-4">
                            <div v-for="appointment in appointments" :key="appointment.id"
                                class="bg-gray-50 p-4 rounded-lg shadow flex flex-col space-y-4">
                                <!-- Header -->
                                <div class="flex items-center justify-between cursor-pointer"
                                    @click="toggleAccordion(appointment)">
                                    <div class="flex items-center space-x-4">
                                        <div>
                                            <p class="font-medium text-gray-900">
                                                {{ formatDateTime(appointment.requested_date) }}
                                            </p>
                                            <p class="text-sm text-gray-600">Property: {{ appointment.property.street
                                                }}, {{ appointment.property.city }}, {{ appointment.property.state }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <span :class="{
                                            'px-2 py-1 text-xs font-semibold rounded-full': true,
                                            'bg-yellow-100 text-yellow-800': appointment.status === 'Pending',
                                            'bg-blue-100 text-blue-800': appointment.status === 'Confirmed',
                                            'bg-red-100 text-red-800': appointment.status === 'Rejected',
                                            'bg-green-100 text-green-800': appointment.status === 'Approved',
                                            'bg-gray-100 text-gray-800': appointment.status === 'Cancelled',
                                            'bg-green-500 text-green-800': appointment.status === 'Applicated',
                                        }">
                                            {{ appointment.status }}
                                        </span>
                                        <button @click.stop="editAppointment(appointment.id)"
                                            class="text-blue-600 hover:text-blue-800"
                                            :disabled="appointment.status === 'Cancelled'">
                                        </button>
                                        <button @click.stop="cancelAppointment(appointment.id)"
                                            class="text-red-600 hover:text-red-800"
                                            :disabled="appointment.status === 'Cancelled'">
                                        </button>
                                        <i
                                            :class="appointment.isOpen ? 'mdi mdi-chevron-up' : 'mdi mdi-chevron-down'"></i>
                                    </div>
                                </div>

                                <!-- Additional Information (Accordion Content) -->
                                <div v-if="appointment.isOpen" class="mt-4 text-gray-700">
                                    <p><strong>Requested Date:</strong> {{ formatDateTime(appointment.requested_date) }}
                                    </p>
                                    <p v-if="appointment.confirmation_date"><strong>Owner approved your appointment
                                            at:</strong> {{
                                        formatDateTime(appointment.confirmation_date)
                                        }}</p>
                                    <p><strong>Status:</strong> {{ appointment.status }}</p>
                                    <p class="text-lg font-medium mt-4">Property Details</p>
                                    <ul class="list-disc ml-6 space-y-1">
                                        <li><strong>Availability:</strong> {{ appointment.property.availability }}</li>
                                        <li><i class="mr-2 mdi mdi-shower"></i><strong
                                                class="text-gray-500">Bathrooms:</strong> {{
                                                    appointment.property.total_bathrooms }}</li>
                                        <li><i class="mr-2 mdi mdi-bed"></i><strong
                                                class="text-gray-500">Rooms:</strong> {{
                                                    appointment.property.total_rooms }}</li>
                                        <li><i class="mr-2 mdi mdi-ruler"></i><strong class="text-gray-500">Size
                                                (m²):</strong> {{
                                                    appointment.property.total_m2 }}</li>
                                        <li><i class="mr-2 mdi mdi-car"></i><strong
                                                class="text-gray-500">Parking:</strong> {{
                                                    appointment.property.have_parking ? "Yes" : "No" }}</li>
                                        <li><i class="mr-2 mdi mdi-currency-usd"></i><strong
                                                class="text-gray-500">Price:</strong> {{
                                                    appointment.property.property_price }}</li>
                                    </ul>

                                    <!-- Mostrar solo si el estado es Approved -->
                                    <div v-if="appointment.status === 'Approved'">
                                        <h1 class="text-xl font-semibold text-gray-900 mt-2">you are been approved!</h1>
                                        <p class='text-sm mb-2'>Continue with the application?</p>
                                        <CustomButton type="cancel"
                                        class='mr-2'
                                            @click="cancelAppointment(appointment)">
                                            <i class="mr-2 mdi mdi-cancel"></i> No
                                        </CustomButton>
                                        <CustomButton type="primary"
                                        class='mr-2'
                                            @click.stop="handleApplication(appointment)">
                                            <i class="mr-2 mdi mdi-check"></i> Yes
                                        </CustomButton>
                                    </div>
                                </div>

                                <!-- Modal -->
                                <div v-if="showDetails" class="fixed inset-0 z-50 overflow-y-auto"
                                    aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                    <div
                                        class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                        <div class="fixed inset-0 transition-opacity bg-black bg-opacity-50 backdrop-blur-sm"
                                            aria-hidden="true" @click="showDetails = false"></div>

                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"
                                            aria-hidden="true">&#8203;</span>

                                        <div
                                            class="inline-block w-full max-w-4xl overflow-hidden text-left align-bottom transition-all transform bg-gradient-to-br bg-white rounded-lg shadow-2xl sm:my-8 sm:align-middle sm:w-full animate-modal-appear">
                                            <div class="px-4 pt-5 pb-4 bg-opacity-30 sm:p-6 sm:pb-4">
                                                <div class="sm:flex sm:items-start">
                                                    <div class="w-full mt-3 text-center sm:mt-0 sm:text-left">
                                                        <div class="flex items-center justify-between mb-4">
                                                            <h3 class="text-2xl font-bold leading-6"
                                                                id="modal-title">
                                                                Upload the necesary documents
                                                            </h3>
                                                            <button @click="showDetails = false"
                                                                class="text-gray-400 transition-colors duration-200 hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                                                <span class="sr-only">Close</span>
                                                                <i class="text-2xl mdi mdi-close"
                                                                    aria-hidden="true"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <Identity :appointments="appointments" :propertyId="selectedAppointment.property.id" :appointmentId="selectedAppointment.id" @close-modal="closeModal" @close-appointment="toggleAccordion(appointment)" @refresh-appointments="handleGetAppointments"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>

<script>
export default {
    setup() {
        const user = usePage().props.auth.user;
    },
    props: ['user'],
    data() {
        return {
            appointments: [],
            propertyId: '',
            showDetails: false, // estado del modal
            selectedAppointment: null, // cita seleccionada
            form: {
                contract_files: [],
            },
        };
    },
    methods: {
        async submitFormWithExistingDocument(appointment) {
            try {
                // Verifica el valor de document_path antes del primer POST
                if (!this.user.document_path) {
                    console.error('Document path is not set!');
                    return; // Sal de la función si el path no está definido
                }

                // Prepara los datos para la solicitud inicial
                const formData2 = new FormData();
                formData2.append('property_id', appointment.property.id);
                formData2.append('tenant_user_id', this.user.id);
                formData2.append('application_date', new Date().toISOString().split("T")[0]);
                formData2.append('status', "Pending");
                formData2.append('document_path', this.user.document_path);

                // Primer POST: enviar la aplicación
                const response = await axios.post('api/properties/applicate', formData2, {
                    headers: { 'Content-Type': 'application/json' },
                });

                const applicationId = response.data.application;
                console.log('Application submitted successfully:', response.data);

                // Prepara los datos para el segundo POST
                const formData = new FormData();
                formData.append('application_id', applicationId);
                // console.log('Document path:', user.document_path);
                formData.append('document_path', this.user.document_path);

                // Segundo POST: enviar documentos
                const docResponse = await axios.post('/api/properties/pass-documents', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                });

                console.log('Document submitted successfully:', docResponse.data);

                const newFormData2 = new FormData();
                newFormData2.append('appointment_id', appointment.id);
                newFormData2.append('status', 'Applicated');

                const statusResponse = await axios.put('/api/appointments/update', newFormData2, {
                    headers: {
                        'Content-Type': 'application/json',
                    },
                });
                console.log('Appointment status updated:', statusResponse.data);

                this.closeModal();
                this.emmiter.emit('show_notification', {
                        title: 'Success',
                        message: 'Application submitted successfully!',
                        type: 'success',
                    });
                this.handleGetAppointments();
            } catch (error) {
                console.error('Error:', error.response ? error.response.data : error.message);
                this.closeModal();
                this.emmiter.emit('show_notification', {
                        title: 'Error',
                        message: 'An error occurred while submitting the application.',
                        type: 'error',
                    });
            }
        },
        handleApplication(appointment) {
            // Verifica si el usuario ya tiene documentos cargados
            if (this.user.document_path) {
                // Usa el documento existente y procede directamente
                this.submitFormWithExistingDocument(appointment);
            } else {
                // Abre el modal para cargar documentos
                this.handleModalToggle(appointment);
            }
        },
        toggleAccordion(appointment) {
            // Alterna el estado de 'isOpen' para el acordeón
            this.appointments.forEach((appt) => {
                if (appt.id === appointment.id) {
                    appt.isOpen = !appt.isOpen;
                } else {
                    appt.isOpen = false; // Colapsa los demás
                }
            });

            // Opcional: Sincronizar con el modal (cerrar modal si el acordeón se colapsa)
            if (!appointment.isOpen) {
                this.showDetails = false;
                this.selectedAppointment = null;
            }
        },
        async cancelAppointment(appointment) {
            try {
                const response = await axios.put('/api/appointments/update', {
                    appointment_id: appointment.id,
                    status: 'Cancelled',
                });
                console.log('Appointment cancelled:', response.data);
                this.handleGetAppointments();
            } catch (error) {
                console.error('Error:', error.response ? error.response.data : error.message);
            }
        },
        handleModalToggle(appointment) {
            // Alterna el estado del modal
            if (this.selectedAppointment === appointment) {
                this.showDetails = false;
                this.selectedAppointment = null;
            } else {
                this.selectedAppointment = appointment;
                this.showDetails = true;
            }
        },
        closeModal() {
            this.showDetails = false;
            this.selectedAppointment = null;

            // Opcional: Cierra también el acordeón relacionado
            this.appointments.forEach((appt) => {
                appt.isOpen = false;
            });
        },
        handleGetAppointments() {
            axios
                .get('/api/appointments', {
                    params: {
                        user_id: this.user.id,
                    },
                })
                .then((response) => {
                    this.appointments = response.data.map((appt) => ({
                        ...appt,
                        isOpen: false, // Inicializa 'isOpen' como false para cada cita
                    }));
                })
                .catch((error) => {
                    console.error(error);
                });
        },
        editAppointment(id) {
            console.log(`Editing appointment with ID: ${id}`);
        },
        formatDateTime(date) {
            return new Date(date).toLocaleString();
        },
    },
    mounted() {
        this.handleGetAppointments();
    },
    components: {
        InputError,
        InputLabel,
        TextInput,
        CustomButton,
        Checkbox,
    },
};

</script>
