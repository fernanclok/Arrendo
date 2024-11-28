<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
import CustomButton from '@/Components/CustomButton.vue';
</script>

<template>

    <Head title="Appointments" />

    <DashboardLayout>
        <div class="py-2">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden">
                    <div class="p-6">
                        <h1 class="text-2xl font-semibold text-gray-900 mb-6">Appointments Request</h1>

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
                                            <p class="font-medium text-gray-900">{{
                                                formatDateTime(appointment.requested_date) }}</p>
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
                                        }">
                                            {{ appointment.status }}
                                        </span>
                                        <button @click.stop="editAppointment(appointment.id)"
                                            class="text-blue-600 hover:text-blue-800"
                                            :disabled="appointment.status === 'Cancelled'"></button>
                                        <button @click.stop="cancelAppointment(appointment.id)"
                                            class="text-red-600 hover:text-red-800"
                                            :disabled="appointment.status === 'Cancelled'"></button>
                                        <i
                                            :class="appointment.isOpen ? 'mdi mdi-chevron-up' : 'mdi mdi-chevron-down'"></i>
                                    </div>
                                </div>

                                <!-- Additional Information (Accordion Content) -->
                                <div v-if="appointment.isOpen" class="mt-4 text-gray-700">
                                    <p><strong>Requested Date:</strong> {{ formatDateTime(appointment.requested_date) }}
                                    </p>
                                    <p><strong>Confirmation Date:</strong> {{
                                        appointment.confirmation_date ? formatDateTime(appointment.confirmation_date) :
                                            'Not confirmed'
                                    }}</p>
                                    <p><strong>Status:</strong> {{ appointment.status }}</p>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-lg font-medium mt-4">Property Details</p>
                                            <ul class="list-disc ml-6 space-y-1">
                                                <li><i class="mr-2 mdi mdi-currency-usd"></i><strong
                                                        class="text-gray-500">Price:</strong> {{
                                                            appointment.property.property_price }}</li>
                                                <li><i class="mr-2 mdi mdi-bed"></i><strong
                                                        class="text-gray-500">Rooms:</strong> {{
                                                            appointment.property.total_rooms }}</li>
                                                <li><i class="mr-2 mdi mdi-shower"></i><strong
                                                        class="text-gray-500">Bathrooms:</strong> {{
                                                            appointment.property.total_bathrooms }}</li>
                                                <li><i class="mr-2 mdi mdi-ruler"></i><strong
                                                        class="text-gray-500">Square Meters:</strong> {{
                                                            appointment.property.total_m2 }}</li>
                                                <li><i class="mr-2 mdi mdi-car"></i><strong
                                                        class="text-gray-500">Parking:</strong> {{
                                                            appointment.property.have_parking ? "Yes" : "No" }}</li>
                                                <li><i class="mr-2 mdi mdi-paw"></i><strong class="text-gray-500">Pets
                                                        Allowed:</strong> {{ appointment.property.accept_mascots ? "Yes"
                                                            : "No" }}</li>
                                            </ul>
                                        </div>
                                        <div>
                                            <p class="text-lg font-medium mt-4">Tenant Details</p>
                                            <ul class="list-disc ml-6 space-y-1">
                                                <li><i class="mr-2 mdi mdi-account"></i><strong
                                                        class="text-gray-500">Fullname:</strong> {{
                                                            appointment.user.first_name }} {{ appointment.user.last_name }}</li>
                                                <li><i class="mr-2 mdi mdi-email"></i><strong
                                                        class="text-gray-500">Email:</strong> {{ appointment.user.email
                                                    }}</li>
                                                <li><i class="mr-2 mdi mdi-phone"></i><strong
                                                        class="text-gray-500">Phone:</strong> {{ appointment.user.phone
                                                    }}</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <CustomButton type="primary" @click="changeStatusModal = true" class="py-2">
                                            change status</CustomButton>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="changeStatusModal"
                            class="fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center overflow-y-auto">
                            <div class="bg-white p-6 rounded-lg">
                                <h2 class="text-2xl font-bold mb-2">Update appointment status</h2>
                                <p class="text-gray-600 mb-2">The tenant will be notified of the updated status.</p>
                                <!-- select de status -->
                                <div class="flex items-center space-x-4">
                                    <label for="status" class="text-gray-600">Status:</label>
                                    <select name="status" id="status" class="border border-gray-300 rounded-lg">
                                        <option value="Confirmed">Confirmed</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Cancelled">Cancelled</option>
                                    </select>
                                </div>
                                <button @click="changeStatusModal = false"
                                    class="text-white bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg">Cancel</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>

<script>
import axios from 'axios';

export default {
    props: ['user'],
    data() {
        return {
            appointments: [],
            changeStatusModal: false,
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
        handleGetAppointments() {
            axios.get('/api/appointments/requests', {
                params: {
                    user_id: this.user.id
                }
            })
                .then((response) => {
                    this.appointments = response.data;
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
            const options = {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            };
            const dateTime = new Date(date).toLocaleString('en-GB', options);
            const hours = new Date(date).getHours();
            const period = hours >= 12 ? 'PM' : 'AM';
            return `${dateTime} ${period}`;
        },
    },
    mounted() {
        this.handleGetAppointments();
    },
};
</script>
