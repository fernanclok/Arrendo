<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { ref, onMounted, watch } from 'vue';
import NavLink from "@/Components/NavLink.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NotificationCard from "@/Components/NotificationCard.vue";
import NotificationDropdown from "@/Components/Dropdowns/NotificationDropdown.vue";


const { props } = usePage();
const navLinks = ref(props.navLinks);

const isSidebarOpen = ref(false);
const isDropdownOpen = ref(false);

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
    localStorage.setItem('isSidebarOpen', JSON.stringify(isSidebarOpen.value));
};

const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value;
};

// Al montar el componente, carga el estado desde localStorage
onMounted(() => {
    const savedSidebarState = localStorage.getItem('isSidebarOpen');
    if (savedSidebarState !== null) {
        isSidebarOpen.value = JSON.parse(savedSidebarState);
    }
});

// Guarda el estado en localStorage cada vez que cambie
watch(isSidebarOpen, (newValue) => {
    localStorage.setItem('isSidebarOpen', JSON.stringify(newValue));
});
</script>

<template>
    <div class="flex flex-col min-h-screen">
        <aside id="logo-sidebar" :class="{
            'fixed top-0 left-0 z-40 h-screen p-2 transition-all duration-300 bg-gray-100 border-r': true,
            'w-fit': isSidebarOpen,   // Ancho normal cuando está abierto
            'w-fit': !isSidebarOpen,  // Ancho reducido cuando está colapsado
        }" aria-label="Sidebar">
            <ul class="pt-6 h-[90%]">

                <li v-for="link in navLinks" :key="link.route"
                    class="flex items-center p-2 transition-all duration-300">
                    <NavLink :href="route(link.route)" :active="route().current(link.route)">
                        <i :class="`mdi mdi-${link.icon} text-md`"></i>
                        <span v-if="isSidebarOpen" class="ml-2">{{ link.label }}</span>
                    </NavLink>

                </li>

            </ul>

            <!-- Contenedor de tres puntos en la parte inferior -->
            <div :class="{
                'justify-center items-center text-center w-full align-bottom mb-4': true,
                'block': !isSidebarOpen,
                'flex': isSidebarOpen
            }">
                <button @click="toggleSidebar"
                    class="p-2 rounded-full hover:bg-gray-200 transition-colors flex items-center space-x-1">
                    <i :class="{
                        'mdi mdi-arrow-collapse-left text-lg flex sm:hidden': true,
                        'mdi mdi-arrow-collapse-right': !isSidebarOpen
                    }"></i>
                </button>
                <button @click="toggleDropdown"
                    class="p-2 rounded-full hover:bg-gray-200 transition-colors flex items-center space-x-4">
                    <i class="mdi mdi-dots-horizontal text-2xl text-gray-600"></i>
                </button>

            </div>

            <!-- Menú desplegable al hacer clic en los tres puntos -->
            <div v-if="isDropdownOpen" class="absolute bottom-12 left-12 w-36 bg-white rounded-md shadow-xl">
                <div class="py-2">
                    <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                    <DropdownLink :href="route('logout')" method="post" as="button">
                        Log Out
                    </DropdownLink>
                </div>
            </div>
        </aside>

        <!-- Contenido principal -->
        <nav :class="{
            'transition-all duration-300 p-8': true,
            'ml-20 sm:ml-40': isSidebarOpen,  // Margen cuando el sidebar está abierto
            'ml-20 sm:ml-16': !isSidebarOpen  // Margen ajustado cuando el sidebar está colapsado
        }">
            <div class="flex items-center transition-all duration-300 justify-between rtl:justify-end">
                <button @click="toggleSidebar" :aria-controls="'logo-sidebar'" :aria-expanded="isSidebarOpen"
                    type="button"
                    class="hidden sm:inline-flex items-center p-2 text-sm text-gray-500 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 ">
                    <span class="sr-only">
                        {{ isSidebarOpen ? 'Cerrar sidebar' : 'Abrir sidebar' }}
                    </span>
                    <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                        <path clip-rule="evenodd" fill-rule="evenodd"
                            d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z" />
                    </svg>
                </button>
            </div>

            <!-- Notifications -->
            <div class="absolute right-10 top-6">
                <NotificationDropdown :auth="props.auth" />
            </div>

            <div>
                <header class="bg-white shadow" v-if="$slots.header">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <slot name="header" />
                    </div>
                </header>
                <main>
                    <!-- notification -->
                    <div v-if="notifications.length > 0" class="fixed right-0 bottom-0 flex flex-col items-end z-50">
                        <div v-for="noti in notifications" :key="noti" v-show="noti.show">
                            <NotificationCard :type="noti.type" :title="noti.title" :message="noti.message"
                                @close="noti.show = false" />
                        </div>
                    </div>
                    <slot />
                </main>
            </div>
        </nav>
    </div>
</template>

<script>
export default {
    mounted() {
        this.emmiter.on("show_notification", (data) => {
            this.showNotification(data);
        });
    },
    data() {
        return {
            notifications: [],
        };
    },
    methods: {
        showNotification(data) {
            var notification = {};
            notification.show = true;
            notification.title = data.title;
            notification.message = data.message;
            notification.type = data.type;

            this.notifications.push(notification);
        },
    },
}
</script>
