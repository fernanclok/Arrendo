<template>
    <div class="relative">
        <!-- Notifications Button -->
        <button
            @click="toggleDropdown"
            class="relative flex items-center gap-2 text-white bg-primary hover:bg-primary-dark font-semibold py-2 px-4 rounded-lg transition">
            <i class="fas fa-bell"></i>
            Notifications
            <span
                class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center">
                {{ unreadNotificationsCount }}
            </span>
        </button>

        <!-- Dropdown -->
        <div v-if="dropdownOpen" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg z-20">
            <div class="p-4">
                <h2 class="text-gray-800 text-lg font-semibold border-b pb-2">
                    Notifications
                </h2>

                <!-- Loading Spinner -->
                <div v-if="isLoading" class="flex items-center justify-center py-4">
                    <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
                </div>

                <!-- Notifications List -->
                <ul v-else class="divide-y divide-gray-200 max-h-64 overflow-y-auto">
                    <li
                        v-for="notification in limitedNotifications"
                        :key="notification.id"
                        class="flex items-start gap-3 px-4 py-3 hover:bg-gray-100 transition">
                        <!-- Notification Info -->
                        <div class="flex-1">
                            <p
                                :class="{
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
                            <button
                                v-if="!notification.read_status"
                                @click="markAsRead(notification.id)"
                                class="text-blue-500 hover:text-blue-700 transition">
                                <i class="mdi mdi-check-circle-outline text-xl"></i>
                            </button>
                            <button
                                v-else
                                @click="markAsUnread(notification.id)"
                                class="text-red-500 hover:text-red-700 transition">
                                <i class="mdi mdi-close-circle-outline text-xl"></i>
                            </button>
                        </div>
                    </li>
                </ul>

                <!-- View More Option -->
                <div class="text-center mt-3">
                    <button
                        @click="showAllNotifications"
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
        <div
            v-if="modalOpen"
            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">All Notifications</h2>
                <ul class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                    <li
                        v-for="notification in notifications"
                        :key="notification.id"
                        class="flex items-start gap-3 px-4 py-3 hover:bg-gray-100 transition">
                        <div class="flex-1">
                            <span class="text-xs text-gray-400">
                                {{ notification.notification_type }}
                            </span>
                            <p
                                :class="{
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
                            <button
                                v-if="!notification.read_status"
                                @click="markAsRead(notification.id)"
                                class="text-blue-500 hover:text-blue-700 transition">
                                <i class="mdi mdi-check-circle-outline text-xl"></i>
                            </button>
                            <button
                                v-else
                                @click="markAsUnread(notification.id)"
                                class="text-red-500 hover:text-red-700 transition">
                                <i class="mdi mdi-close-circle-outline text-xl"></i>
                            </button>
                        </div>
                    </li>
                </ul>
                <div class="text-right mt-4">
                    <button
                        @click="closeModal"
                        class="text-white bg-primary hover:bg-primary-dark py-2 px-4 rounded-lg transition">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

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
    },
};
</script>
