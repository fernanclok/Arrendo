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
                    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
                        <div class="max-w-4xl mx-auto">
                            <h1 class="text-4xl font-extrabold mb-8 text-center">Your Appointments</h1>

                            <div v-if="appointments.length === 0" class="bg-white rounded-xl shadow-lg p-8 text-center">
                                <CalendarIcon class="h-16 w-16 mx-auto mb-4" />
                                <p class="text-xl text-gray-600">You have no appointments scheduled.</p>
                            </div>

                            <div v-else class="space-y-6">
                                <div v-for="appointment in appointments" :key="appointment.id"
                                    class="bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-2xl transform hover:-translate-y-1">
                                    <div class="p-6">
                                        <!-- Header -->
                                        <div class="flex items-center justify-between cursor-pointer"
                                            @click="toggleAccordion(appointment)">
                                            <div class="flex items-center space-x-4">
                                                <div class="flex-shrink-0">
                                                    <div class="rounded-full p-3">
                                                        <CalendarIcon class="h-8 w-8" />
                                                    </div>
                                                </div>
                                                <div>
                                                    <p class="text-xl font-semibold">
                                                        {{ formatDateTime(appointment.requested_date) }}
                                                    </p>
                                                    <p class="text-sm">
                                                        {{ appointment.property.street }}, {{ appointment.property.city
                                                        }}, {{ appointment.property.state }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-4">
                                                <span :class="{
                                                    'px-4 py-2 text-sm font-semibold rounded-full shadow-sm': true,
                                                    'bg-green-100 text-green-800': appointment.status === 'Confirmed',
                                                    'bg-yellow-100 text-yellow-800': appointment.status === 'Pending',
                                                    'bg-red-100 text-red-800': appointment.status === 'Cancelled',
                                                    'bg-green-100 text-green-800': appointment.status === 'Approved',
                                                }">
                                                    {{ appointment.status }}
                                                </span>
                                                <ChevronDownIcon v-if="!appointment.isOpen"
                                                    class="h-6 w-6 text-green-700 text-green-700 transition-transform duration-300" />
                                                <ChevronUpIcon v-else
                                                    class="h-6 w-6 text-green-700 text-green-700 transition-transform duration-300" />
                                            </div>
                                        </div>

                                        <!-- Additional Information (Accordion Content) -->
                                        <div v-if="appointment.isOpen"
                                            class="mt-6 text-gray-700 space-y-6 transition-all duration-500 ease-in-out">
                                            <div
                                                class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-green-50 rounded-lg p-4">
                                                <div>
                                                    <p class="font-medium text-green-900">Requested Date:</p>
                                                    <p class="text-green-700">{{
                                                        formatDateTime(appointment.requested_date) }}</p>
                                                </div>
                                                <div v-if="appointment.confirmation_date">
                                                    <p class="font-medium text-green-900">Owner Approved At:</p>
                                                    <p class="text-green-700">{{
                                                        formatDateTime(appointment.confirmation_date) }}</p>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-green-900">Status:</p>
                                                    <p class="text-green-700">{{ appointment.status }}</p>
                                                </div>
                                            </div>

                                            <div class="bg-white rounded-lg shadow-inner p-6">
                                                <h3 class="text-2xl font-semibold text-green-900 mb-4">Property Details
                                                </h3>
                                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                                                    <div
                                                        class="flex items-center space-x-3 bg-green-200 rounded-lg p-3 transition-all duration-300 hover:bg-green-100">
                                                        <HomeIcon class="h-6 w-6 text-green-700 text-green-700" />
                                                        <span class="text-green-900">{{
                                                            appointment.property.availability }}</span>
                                                    </div>
                                                    <div
                                                        class="flex items-center space-x-3 bg-green-200 rounded-lg p-3 transition-all duration-300 hover:bg-green-100">
                                                        <ShowerHeadIcon class="h-6 w-6 text-green-700 text-green-700" />
                                                        <span class="text-green-900">{{
                                                            appointment.property.total_bathrooms }} Bathrooms</span>
                                                    </div>
                                                    <div
                                                        class="flex items-center space-x-3 bg-green-200 rounded-lg p-3 transition-all duration-300 hover:bg-green-100">
                                                        <BedDoubleIcon class="h-6 w-6 text-green-700 text-green-700" />
                                                        <span class="text-green-900">{{ appointment.property.total_rooms
                                                            }} Rooms</span>
                                                    </div>
                                                    <div
                                                        class="flex items-center space-x-3 bg-green-200 rounded-lg p-3 transition-all duration-300 hover:bg-green-100">
                                                        <RulerIcon class="h-6 w-6 text-green-700 text-green-700" />
                                                        <span class="text-green-900">{{ appointment.property.total_m2 }}
                                                            m²</span>
                                                    </div>
                                                    <div
                                                        class="flex items-center space-x-3 bg-green-200 rounded-lg p-3 transition-all duration-300 hover:bg-green-100">
                                                        <CarIcon class="h-6 w-6 text-green-700 text-green-700" />
                                                        <span class="text-green-900">Parking: {{
                                                            appointment.property.have_parking ? "Yes" : "No" }}</span>
                                                    </div>
                                                    <div
                                                        class="flex items-center space-x-3 bg-green-200 rounded-lg p-3 transition-all duration-300 hover:bg-green-100">
                                                        <DollarSignIcon class="h-6 w-6 text-green-700 text-green-700" />
                                                        <span class="text-green-900">{{
                                                            appointment.property.property_price }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Mostrar solo si el estado es Approved -->
                                            <div v-if="appointment.status === 'Approved'"
                                                class="mt-6 bg-green-50 rounded-lg p-6">
                                                <h3 class="text-2xl font-semibold text-green-900 mb-4">Ready to Proceed?
                                                </h3>
                                                <p class="text-green-700 mb-4">Your appointment has been approved. Would
                                                    you like to move forward with the application?</p>
                                                <div class="flex space-x-4">
                                                    <button type="button"
                                                        class="flex-1 inline-flex justify-center items-center px-6 py-3 border border-gray-300 rounded-md shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200"
                                                        @click="cancelAppointment(appointment)">
                                                        <XIcon class="mr-2 h-5 w-5 text-gray-500 text-green-700" />
                                                        Decline
                                                    </button>
                                                    <button type="button"
                                                        class="flex-1 inline-flex justify-center items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-green-700 hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200"
                                                        @click.stop="handleApplication(appointment)">
                                                        <CheckIcon class="mr-2 h-5 w-5 text-green-700" />
                                                        Proceed
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal -->
                            <div v-if="showDetails" class="fixed inset-0 z-50 overflow-y-auto"
                                aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                <div
                                    class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                    <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 backdrop-blur-sm"
                                        aria-hidden="true" @click="showDetails = false"></div>

                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen"
                                        aria-hidden="true">&#8203;</span>

                                    <div
                                        class="inline-block w-full max-w-4xl overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-2xl sm:my-8 sm:align-middle sm:w-full animate-modal-appear">
                                        <div class="px-4 pt-5 pb-4 bg-opacity-30 sm:p-6 sm:pb-4">
                                            <div class="sm:flex sm:items-start">
                                                <div class="w-full mt-3 text-center sm:mt-0 sm:text-left">
                                                    <div class="flex items-center justify-between mb-4">
                                                        <h3 class="text-2xl font-bold leading-6 text-black"
                                                            id="modal-title">
                                                            Upload the necesary documents
                                                        </h3>
                                                        <button @click="showDetails = false"
                                                            class="text-gray-400 transition-colors duration-200 hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                                            <span class="sr-only">Close</span>
                                                            <i class="text-2xl mdi mdi-close" aria-hidden="true"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <Identity :appointments="appointments"
                                                :propertyId="selectedAppointment.property.id"
                                                :appointmentId="selectedAppointment.id" @close-modal="closeModal"
                                                @close-appointment="toggleAccordion(appointment)"
                                                @refresh-appointments="handleGetAppointments" />
                                        </div>
                                    </div>
                                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                        <Identity :appointments="appointments"
                                            :propertyId="selectedAppointment.property.id"
                                            :appointmentId="selectedAppointment.id" @close-modal="closeModal"
                                            @close-appointment="toggleAccordion(appointment)"
                                            @refresh-appointments="handleGetAppointments" />
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
                this.handleGetAppointments();
            } catch (error) {
                console.error('Error:', error.response ? error.response.data : error.message);
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
            this.$emit('close');
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
