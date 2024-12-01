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
import { usePage } from '@inertiajs/vue3';
</script>

<template>

    <Head title="Appointments" />

    <DashboardLayout>
        <div class="py-2">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden">
                    <div class="p-6">
                        <h1 class="text-3xl font-bold mb-6">Your Appointments</h1>

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
                                            'bg-green-100 text-green-800': appointment.status === 'Confirmed',
                                            'bg-yellow-100 text-yellow-800': appointment.status === 'Pending',
                                            'bg-red-100 text-red-800': appointment.status === 'Cancelled',
                                            'bg-green-100 text-green-800': appointment.status === 'Approved',
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
                                        <!-- <li><strong>Name:</strong> {{ appointment.property.name }}</li> -->
                                        <!-- <li><strong>Address:</strong> {{ appointment.property.address }}</li> -->
                                        <!-- <li><strong>Rental Rate:</strong> ${{ appointment.property.rental_rate }}</li> -->
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
                                        <h1 class="text-2xl font-semibold text-gray-900 mb-6">You want to proceed to the
                                            application?</h1>
                                        <button type="button"
                                            class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 transition-colors duration-200 bg-white border border-red-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                            @click="">
                                            <i class="mr-2 mdi mdi-cancel"></i> No
                                        </button>
                                        <button type="button"
                                            class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white transition-colors duration-200 bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm"
                                            @click="openDetailsModal(appointment)">
                                            <i class="mr-2 mdi mdi-check"></i> Yes
                                        </button>
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
                                            class="inline-block w-full max-w-4xl overflow-hidden text-left align-bottom transition-all transform bg-gradient-to-br from-gray-900 to-gray-800 rounded-lg shadow-2xl sm:my-8 sm:align-middle sm:w-full animate-modal-appear">
                                            <div class="px-4 pt-5 pb-4 bg-opacity-30 sm:p-6 sm:pb-4">
                                                <div class="sm:flex sm:items-start">
                                                    <div class="w-full mt-3 text-center sm:mt-0 sm:text-left">
                                                        <div class="flex items-center justify-between mb-4">
                                                            <h3 class="text-2xl font-bold leading-6 text-white"
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
                                                <!-- {{ console.log({id: selectedAppointment.property.id}) }} -->
                                                <Identity :propertyId="selectedAppointment.property.id" />
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
    setup(){
        const user = usePage().props.auth.user;
    },
    props: ['user'],
    data() {
        return {
            appointments: [],
            propertyId : '',
            showDetails: false, // estado del modal
            selectedAppointment: null, // para almacenar la cita seleccionada
            form: {
                contract_files: [],
            },
        };
    },
    methods: {
        toggleAccordion(appointment) {
            appointment.isOpen = !appointment.isOpen;
        },
        openDetailsModal(appointment) {
            this.selectedAppointment = appointment; // Guarda la cita seleccionada
            this.showDetails = true; // Abre el modal
        },
        closeModal() {
            this.showDetails = false; // Cierra el modal
            this.selectedAppointment = null; // Limpia la cita seleccionada
        },
        toggleAccordion(appointment) {
            this.appointments.forEach((appt) => {
                if (appt.id === appointment.id) {
                    appt.isOpen = !appt.isOpen;
                } else {
                    appt.isOpen = false;
                }
            });
        },
        handleGetAppointments() {
            axios.get('/api/appointments', {
                params: {
                    user_id: this.user.id
                }
            })
                .then((response) => {
                    this.appointments = response.data;

                    // console.log(response.data);
                })
                .catch((error) => {
                    console.error(error);
                });
        },
        editAppointment(id) {
            console.log(`Editing appointment with ID: ${id}`);
        },
        cancelAppointment(id) {
            console.log(`Cancelling appointment with ID: ${id}`);
        },
        formatDateTime(date) {
            return new Date(date).toLocaleString();
        }
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
