<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
import CustomButton from '@/Components/CustomButton.vue';
</script>

<template>

    <Head title="Appointments" />

    <DashboardLayout>
        <div class="p-2">
        <h1 class="text-3xl font-bold text-gray-800">Appointments Request</h1>
    </div>
        <div class="py-2">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden">
                    <div class="p-6">
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
                                    <!-- if the status is rejected show a message with the rejected_reason -->
                                    <p v-if="appointment.status === 'Rejected'"
                                        class="px-2 py-1 font-semibold bg-red-100 rounded-full text-center"><strong
                                            class="text-red-800">Rejection Reason:</strong> {{
                                                appointment.rejected_reason }}</p>
                                    <p><strong>Requested Date:</strong> {{ formatDateTime(appointment.requested_date) }}
                                    </p>
                                    <p v-if="appointment.confirmation_date"><strong>You modified this appointment
                                            at:</strong> {{
                                                formatDateTime(appointment.confirmation_date)
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
                                    <div class="mt-4"
                                        v-if="appointment.status !== 'Rejected' && appointment.status !== 'Cancelled' && appointment.status !== 'Approved' && appointment.status !== 'Applicated'">
                                        <CustomButton type="primary" @click="openChangeStatusModal(appointment)"
                                            class="py-2">
                                            change status</CustomButton>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="selectedAppointment"
                            class="fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center overflow-hidden">
                            <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
                                <h2 class="text-2xl font-bold mb-4 text-center">Update Appointment Status</h2>
                                <p class="text-gray-600 mb-4 text-center">The tenant will be notified of the updated
                                    status.</p>
                                <form @submit.prevent="updateStatus(selectedAppointment.id)">
                                    <div class="space-y-4">
                                        <div>
                                            <label for="status" class="block text-sm font-medium text-gray-700">New
                                                Status:</label>
                                            <select id="status" name="status" v-model="appointmentForm.status"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-700 focus:ring focus:ring-green-700 focus:ring-opacity-50">
                                                <option value="Pending">Pending</option>
                                                <option value="Confirmed">Confirmed</option>
                                                <option value="Approved">Approved</option>
                                                <option value="Rejected">Rejected</option>
                                            </select>
                                        </div>
                                        <div v-if="appointmentForm.status === 'Rejected'" class="mt-4">
                                            <label for="rejected_reason"
                                                class="block text-sm font-medium text-gray-700">Rejection
                                                Reason:</label>
                                            <textarea id="rejected_reason" v-model="appointmentForm.rejected_reason"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-700 focus:ring focus:ring-green-700 focus:ring-opacity-50"
                                                rows="3" placeholder="Provide a reason for rejection"></textarea>
                                            <p class="text-sm text-gray-600 mt-2">If you reject the appointment, the
                                                tenant will be notified and will be able to request a new appointment.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex justify-end pt-2">
                                        <CustomButton type="cancel" @click="resetForm()">Close
                                        </CustomButton>
                                        <button type="submit"
                                            class="ml-2 inline-flex items-center px-3 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 bg-primary text-white hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:ring-green-500">
                                            Submit
                                        </button>
                                    </div>
                                </form>
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
            appointmentForm: {
                appointment_id: '',
                status: '',
                rejected_reason: '',
            },
            selectedAppointment: false,
        };
    },
    methods: {

        sendNotification(senderId, receiverId, notificationType, message) {
            axios.post('/api/notifications', {
                sender_id: senderId,
                receiver_id: receiverId,
                notification_type: notificationType,
                message: message,
                sent_date: new Date().toISOString(),
                read_status: false,
            })
                .then(() => {
                    console.log('Notification sent successfully');
                })
                .catch((error) => {
                    console.error('Error sending notification:', error);
                });
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
        openChangeStatusModal(appointment) {
            this.appointmentForm.appointment_id = appointment.id;
            this.appointmentForm.status = appointment.status;
            this.selectedAppointment = true;
        },
        updateStatus(id) {
            const data = {
                appointment_id: this.appointmentForm.appointment_id,
                status: this.appointmentForm.status,
            };

            if (this.appointmentForm.status === 'Rejected') {
                data.rejected_reason = this.appointmentForm.rejected_reason;
            }

            axios.put('/api/appointments/update', data)
                .then(() => {
                    this.handleGetAppointments(); // Recarga la lista de citas
                    this.selectedAppointment = null; // Cierra el modal
                    this.appointmentForm.status = ''; // Reinicia el formulario
                    this.appointmentForm.rejected_reason = ''; // Limpia el motivo de rechazo
                    this.emmiter.emit('show_notification', {
                        title: 'Success',
                        message: 'Appointment status updated successfully.',
                        type: 'success',
                    });

                    // Enviar notificación sobre el cambio de estado
                    const notificationMessage = `The status of your appointment has been changed to ${this.appointmentForm.status}.`;
                    this.sendNotification(
                        this.user.id,
                        this.appointmentForm.user_id, // ID del usuario al que se le envía la notificación
                        'Status Update',
                        notificationMessage
                    );
                })
                .catch(() => {
                    this.emmiter.emit('show_notification', {
                        title: 'Error',
                        message: 'An error occurred while updating the appointment status.',
                        type: 'error',
                    });
                    this.selectedAppointment = null; // Asegúrate de cerrar el modal en caso de error
                    this.appointmentForm.status = ''; // Limpia el formulario
                    this.appointmentForm.rejected_reason = '';
                });
        },
        resetForm() {
            this.selectedAppointment = null;
            this.appointmentForm.status = '';
            this.appointmentForm.rejected_reason = '';
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
