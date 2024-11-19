<script setup>
import { ref, onMounted } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <DashboardLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h1 class="text-2xl font-semibold text-gray-900 mb-6">Your Appointments</h1>

                        <div v-if="appointments.length === 0" class="text-gray-600">
                            You have no appointments scheduled.
                        </div>

                        <div v-else class="space-y-4">
                            <div v-for="appointment in appointments" :key="appointment.id"
                                class="bg-gray-50 p-4 rounded-lg shadow flex flex-col space-y-4">
                                <!-- Header -->
                                <div class="flex items-center justify-between cursor-pointer"
                                    @click="toggleAccordion(appointment)">
                                    <div class="flex items-center space-x-4">
                                        <CalendarIcon class="text-blue-500" />
                                        <div>
                                            <p class="font-medium text-gray-900">
                                                {{ formatDateTime(appointment.requested_date) }}
                                            </p>
                                            <p class="text-sm text-gray-600">Property: {{ appointment.property.name }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <span :class="{
                                            'px-2 py-1 text-xs font-semibold rounded-full': true,
                                            'bg-green-100 text-green-800': appointment.status === 'Confirmed',
                                            'bg-yellow-100 text-yellow-800': appointment.status === 'Pending',
                                            'bg-red-100 text-red-800': appointment.status === 'Cancelled',
                                        }">
                                            {{ appointment.status }}
                                        </span>
                                        <button @click.stop="editAppointment(appointment.id)"
                                            class="text-blue-600 hover:text-blue-800"
                                            :disabled="appointment.status === 'Cancelled'">
                                            <PencilIcon class="h-5 w-5" />
                                        </button>
                                        <button @click.stop="cancelAppointment(appointment.id)"
                                            class="text-red-600 hover:text-red-800"
                                            :disabled="appointment.status === 'Cancelled'">
                                            <XIcon class="h-5 w-5" />
                                        </button>
                                        <i
                                            :class="appointment.isOpen ? 'mdi mdi-chevron-up' : 'mdi mdi-chevron-down'"></i>
                                    </div>
                                </div>

                                <!-- Additional Information (Accordion Content) -->
                                <div v-if="appointment.isOpen" class="mt-4 text-gray-700">
                                    <p><strong>Requested Date:</strong> {{ formatDateTime(appointment.requested_date) }}
                                    </p>
                                    <p><strong>Confirmation Date:</strong> {{
                                        formatDateTime(appointment.confirmation_date) }}</p>
                                    <p><strong>Status:</strong> {{ appointment.status }}</p>
                                    <p class="text-lg font-medium mt-4">Property Details</p>
                                    <ul class="list-disc ml-6 space-y-1">
                                        <li><strong>Name:</strong> {{ appointment.property.name }}</li>
                                        <li><strong>Address:</strong> {{ appointment.property.address }}</li>
                                        <li><strong>Rental Rate:</strong> ${{ appointment.property.rental_rate }}</li>
                                        <li><strong>Availability:</strong> {{ appointment.property.availability }}</li>
                                        <li><strong>Total Bathrooms:</strong> {{ appointment.property.total_bathrooms }}
                                        </li>
                                        <li><strong>Total Rooms:</strong> {{ appointment.property.total_rooms }}</li>
                                        <li><strong>Size (m²):</strong> {{ appointment.property.total_m2 }}</li>
                                        <li><strong>Parking Available:</strong> {{ appointment.property.have_parking ?
                                            'Yes' : 'No' }}</li>
                                        <li><strong>Price:</strong> ${{ appointment.property.property_price }}</li>
                                    </ul>
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
    data() {
        return {
            appointments: [
                {
                    id: 1,
                    requested_date: '2024-11-16T10:00:00Z',
                    confirmation_date: '2024-11-15T12:00:00Z',
                    status: 'Confirmed',
                    isOpen: false, // Estado del acordeón
                    property: {
                        name: 'Sunny Apartment',
                        address: '123 Sunshine St.',
                        rental_rate: 1200.0,
                        availability: 'Available',
                        total_bathrooms: 2,
                        total_rooms: 3,
                        total_m2: 120,
                        have_parking: true,
                        property_price: 250000.0,
                    },
                },
                {
                    id: 2,
                    requested_date: '2024-11-17T12:00:00Z',
                    confirmation_date: null,
                    status: 'Pending',
                    isOpen: false,
                    property: {
                        name: 'Modern Condo',
                        address: '456 Modern Ave.',
                        rental_rate: 1500.0,
                        availability: 'Available',
                        total_bathrooms: 3,
                        total_rooms: 4,
                        total_m2: 150,
                        have_parking: true,
                        property_price: 350000.0,
                    },
                },
            ],
        };
    },
    methods: {
        toggleAccordion(appointment) {
            this.appointments.forEach((appt) => {
                if (appt.id === appointment.id) {
                    appt.isOpen = !appt.isOpen;
                } else {
                    appt.isOpen = false;
                }
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
        },
    },
};
</script>
