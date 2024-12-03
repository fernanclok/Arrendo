<template>
    <div class="relative">

        <!-- Notifications Button -->
        <button @click="toggleDropdown"
            class="relative flex items-center justify-center bg-transparent hover:bg-primary-dark p-3 rounded-full transition">
            <i class="mdi mdi-bell text-xl text-green-900"></i>
            <span
                class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center">
                {{ unreadNotificationsCount }}
            </span>
        </button>


        <!-- Dropdown -->
        <div v-if="dropdownOpen" class="absolute right-0 -mt-3 w-80 bg-white rounded-lg shadow-lg z-20">
            <div class="p-4">
                <h2 class="text-gray-800 text-lg font-semibold border-b pb-2">
                    Notifications
                </h2>
                <div class="flex gap-2">
                    <!-- Botón "Todas las notificaciones" -->
                    <button @click="filter = 'all'"
                        :class="filter === 'all' ? 'bg-[#2A3C32] text-white' : 'bg-[#E8E4D9] text-[#2A3C32]'"
                        class="px-3 py-1 rounded-lg text-sm font-semibold transition">
                        Todas
                    </button>

                    <!-- Botón "No leídas" -->
                    <button @click="filter = 'unread'"
                        :class="filter === 'unread' ? 'bg-[#3F594A] text-white' : 'bg-[#F7F7F7] text-[#4B4B4B]'"
                        class="px-3 py-1 rounded-lg text-sm font-semibold transition">
                        No leídas
                    </button>
                </div>


                <!-- Loading Spinner -->
                <div v-if="isLoading" class="flex items-center justify-center py-4">
                    <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
                </div>

                <!-- Notifications List -->
                <ul v-else class="divide-y divide-gray-200 max-h-64 overflow-y-auto">
                    <li v-for="notification in filteredNotifications.slice(0, 10)" :key="notification.id"
                        class="flex items-start gap-3 px-4 py-3 hover:bg-gray-100 transition"
                        :class="{ 'animate-bounce': notification.new }">
                        <!-- Notification Info -->
                        <div class="flex-1">
                            <p :class="{
                                'text-gray-500': notification.read_status,
                                'text-gray-800 font-medium': !notification.read_status
                            }">
                                {{ notification.message }}
                            </p>
                            <span class="text-xs text-gray-400">
                                {{ new Date(notification.sent_date).toLocaleString() }}
                            </span>
                        </div>
                        <!-- Mark as Read -->
                        <div>
                            <button v-if="!notification.read_status" @click="markAsRead(notification.id)"
                                class="text-blue-500 hover:text-blue-700 transition">
                                <i class="mdi mdi-check-circle-outline text-xl"></i>
                            </button>
                            <button v-else @click="markAsUnread(notification.id)"
                                class="text-red-500 hover:text-red-700 transition">
                                <i class="mdi mdi-close-circle-outline text-xl"></i>
                            </button>
                        </div>
                    </li>
                </ul>


                <!-- View More Option -->
                <div class="text-center mt-3">
                    <button @click="showAllNotifications"
                        class="text-blue-600 hover:text-blue-800 font-medium transition">
                        View All Notifications
                    </button>
                </div>

                <!-- No Notifications Message -->
                <p v-if="!isLoading && notifications.length === 0" class="text-gray-500 text-center py-4">
                    No notifications available
                </p>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="modalOpen" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">All Notifications</h2>
                <ul class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                    <li v-for="notification in notifications" :key="notification.id"
                        class="flex items-start gap-3 px-4 py-3 hover:bg-gray-100 transition">
                        <div class="flex-1">
                            <span class="text-xs text-gray-400">
                                {{ notification.notification_type }}
                            </span>
                            <p :class="{
                                'text-gray-500': notification.read_status,
                                'text-gray-800 font-medium': !notification.read_status
                            }">
                                {{ notification.message }}
                            </p>
                            <span class="text-xs text-gray-400">
                                {{ new Date(notification.sent_date).toLocaleString() }}
                            </span>
                        </div>
                        <!-- Mark as Read/Unread -->
                        <div>
                            <button v-if="!notification.read_status" @click="markAsRead(notification.id)"
                                class="text-blue-500 hover:text-blue-700 transition">
                                <i class="mdi mdi-check-circle-outline text-xl"></i>
                            </button>
                            <button v-else @click="markAsUnread(notification.id)"
                                class="text-red-500 hover:text-red-700 transition">
                                <i class="mdi mdi-close-circle-outline text-xl"></i>
                            </button>
                        </div>
                    </li>
                </ul>
                <div class="text-right mt-4">
                    <button @click="closeModal"
                        class="text-white bg-primary hover:bg-primary-dark py-2 px-4 rounded-lg transition">
                        Close
                    </button>
                </div>
            </div>
        </div>

        <!-- Toast Notifications -->
        <div class="fixed top-4 right-4 space-y-2 z-50">
            <transition-group name="toast" tag="div">
                <div v-for="(toast, index) in toasts" :key="index"
                    class="flex items-center gap-3 bg-white shadow-lg rounded-xl border-l-4 border-green-800 p-4 max-w-sm transform transition-all duration-300 ease-in-out hover:shadow-2xl">
                    <!-- Icon -->
                    <div class="flex-shrink-0">
                        <i class="mdi mdi-information-outline text-green-500 text-2xl"></i>
                    </div>
                    <!-- Notification Content -->
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800 truncate">{{ toast.message }}</p>
                        <p v-if="toast.details" class="text-sm text-gray-600 truncate">{{ toast.details }}</p>
                    </div>
                    <!-- Close Button -->
                    <button @click="removeToast(index)" class="flex-shrink-0 text-gray-400 hover:text-gray-600">
                        <i class="mdi mdi-close"></i>
                    </button>
                </div>
            </transition-group>
        </div>

    </div>
</template>

<script>
import axios from "axios";
import Echo from "laravel-echo";
import Pusher from "pusher-js";
import { socket } from '@/bootstrap.js';

export default {
    props: {
        auth: {
            type: Object,
            required: true,
            default: () => ({}),
        },
    },
    data() {
        return {
            notifications: [], // Notifications array
            dropdownOpen: false, // State for dropdown visibility
            modalOpen: false, // State for modal visibility
            isLoading: false, // Loading indicator
            toasts: [], // Toast notifications
            filter: "all", // Filter for notifications
        };
    },
    computed: {
        // Count only unread notifications
        unreadNotificationsCount() {
            return this.notifications.filter(notification => !notification.read_status).length;
        },
        // Limit notifications to the first 10 for the dropdown
        limitedNotifications() {
            return this.notifications.slice(0, 10);
        },

        filteredNotifications() {
            if (this.filter === 'unread') {
                return this.notifications.filter(notification => !notification.read_status);
            }
            return this.notifications;
        },
    },
    methods: {
        // Fetch notifications from the backend
        async fetchNotifications() {
            this.isLoading = true;
            const userId = this.auth.user.id;
            try {
                const response = await axios.get(`/api/notifications/${userId}`);
                this.notifications = response.data;
            } catch (error) {
                console.error("Error fetching notifications:", error);
            } finally {
                this.isLoading = false;
            }
        },
        // Mark a notification as read
        async markAsRead(notificationId) {
            try {
                await axios.put(`/api/notifications/${notificationId}/read`);
                const notification = this.notifications.find((n) => n.id === notificationId);
                if (notification) {
                    notification.read_status = true;
                }
            } catch (error) {
                console.error("Error marking notification as read:", error);
            }
        },
        // Mark a notification as unread
        async markAsUnread(notificationId) {
            try {
                await axios.put(`/api/notifications/${notificationId}/unread`);
                const notification = this.notifications.find((n) => n.id === notificationId);
                if (notification) {
                    notification.read_status = false;
                }
            } catch (error) {
                console.error("Error marking notification as unread:", error);
            }
        },

        // Mostrar una notificación flotante
        showToast(message) {
            const toast = { message };
            this.toasts.push(toast);
            setTimeout(() => {
                this.toasts.shift();
            }, 5000); // Ocultar después de 5 segundos
        },

        removeToast(index) {
            this.toasts.splice(index, 1);
        },


        // Toggle dropdown visibility
        toggleDropdown() {
            this.dropdownOpen = !this.dropdownOpen;
        },
        // Show all notifications in the modal
        showAllNotifications() {
            this.modalOpen = true;
        },
        // Close the modal
        closeModal() {
            this.modalOpen = false;
        },
    },
    mounted() {
        this.fetchNotifications();

        // Verifica si Pusher y su conexión están configurados correctamente
        if (
            window.Echo &&
            window.Echo.connector &&
            window.Echo.connector.pusher &&
            window.Echo.connector.pusher.connection.state === 'connected'
        ) {
            console.log("Pusher está conectado. Suscribiéndose al canal...");

            window.Echo.private(`notifications.${this.auth.user.id}`)
                .listen('.NewNotification', (event) => {
                    if (event.notification.receiver_id === this.auth.user.id) {
                        this.notifications.push(event.notification);
                        this.showToast(event.notification.message);
                    }
                })
                .error((error) => {
                    console.error("Error escuchando el evento con Pusher:", error);
                });
        } else {

            // Configurar Socket.IO solo si Pusher no está funcionando
            socket.on('localNotification', (data) => {
                if (data.receiver_id === this.auth.user.id) {
                    this.notifications.push({ id: data.id, message: data.message });
                    this.showToast(data.message);
                }
            });

            socket.on('error', (error) => {
                console.error("Socket.IO error:", error);
                this.showToast("Error recibiendo notificaciones.");
            });

            socket.connect(); // Asegúrate de conectar el socket
        }
    }

};
</script>

<style scoped>
/* Transición personalizada para la entrada y salida */
.toast-enter-active,
.toast-leave-active {
    transition: all 0.5s ease;
}

.toast-enter-from {
    opacity: 0;
    transform: translateY(-20px);
}

.toast-enter-to {
    opacity: 1;
    transform: translateY(0);
}

.toast-leave-from {
    opacity: 1;
    transform: translateY(0);
}

.toast-leave-to {
    opacity: 0;
    transform: translateY(-20px);
}
</style>
