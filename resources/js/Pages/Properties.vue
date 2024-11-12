<script setup>
import CustomButton from '@/Components/CustomButton.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="properties" />

    <!-- Header Navbar -->
    <header class="border-b">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <Link href="/" class="flex items-center space-x-2">
                    <span class="text-xl font-bold">Arrendo</span>
                </Link>
                <nav class="hidden md:flex space-x-4">
                    <Link v-for="item in navItems" :key="item.href" :href="item.href"
                        class="text-sm font-medium hover:underline">
                        {{ item.label }}
                    </Link>
                </nav>
                <Link href="/login"
                    class="inline-flex items-center px-3 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 bg-primary text-white hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:ring-blue-500">
                    Log In
                </Link>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="relative flex">
        <div class="container mx-auto p-4 transition-transform duration-300 w-full"
            :class="{ 'md:w-2/3 lg:w-3/4 transform': showDetailsModal }">
            <h1 class="text-3xl font-bold mb-6">Properties in Rent</h1>

            <div class="mb-6">
                <label for="precioMaximo" class="block text-sm font-medium text-gray-700">Filter by maximum price:</label>
                <div class="flex gap-2 mt-1">
                    <input id="precioMaximo" v-model="precioMaximo" type="number" placeholder="Maximum price"
                        class="flex-1 px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" />
                    <CustomButton @click="filtrarPropiedades" type="primary" class="py-2 ml-2">
                        Filter
                    </CustomButton>
                </div>
            </div>

            <!-- Properties Grid -->
            <div :class="{'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6': !showDetailsModal, 'flex flex-col gap-4': showDetailsModal}">
                <div v-for="property in properties" :key="property.id"
                    class="bg-white shadow-md rounded-lg overflow-hidden">
                    <img :src="property.image" alt="Property" class="w-full h-48 object-cover" />
                    <div class="p-4">
                        <h2 class="text-xl font-semibold mb-2">{{ property.title }}</h2>
                        <p class="text-gray-600 mb-2">{{ property.address }}</p>
                        <div class="flex justify-between items-center mb-2">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16h6M4 6h16M4 6a2 2 0 012-2h12a2 2 0 012 2M4 6v12a2 2 0 002 2h12a2 2 0 002-2V6"></path>
                                </svg>
                                {{ property.rooms }} rooms
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3v4h6v-4c0-1.657-1.343-3-3-3zM5 20h14a2 2 0 002-2v-5a2 2 0 00-2-2H5a2 2 0 00-2 2v5a2 2 0 002 2z"></path>
                                </svg>
                                {{ property.bathrooms }} bathrooms
                            </span>
                        </div>
                    </div>
                    <div class="bg-gray-100 px-4 py-3 flex justify-between items-center">
                        <span class="text-lg font-bold flex items-center">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3v4h6v-4c0-1.657-1.343-3-3-3zM5 20h14a2 2 0 002-2v-5a2 2 0 00-2-2H5a2 2 0 00-2 2v5a2 2 0 002 2z"></path>
                            </svg>
                            ${{ property.price }}
                        </span>
                        <CustomButton type="primary" class="py-2" @click="openDetailsModal">
                            View Details
                        </CustomButton>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de detalles -->
        <div class="fixed inset-y-0 right-0 w-full md:w-1/3 bg-white shadow-lg transition-transform duration-300 transform"
            :class="{'translate-x-full': !showDetailsModal, 'translate-x-0': showDetailsModal}">
            <div class="p-4">
                <h1 class="text-2xl font-bold mb-4">Details</h1>
                <p>Aquí puedes mostrar más detalles sobre la propiedad seleccionada.</p>
                <CustomButton type="primary" @click="closeDetailsModal">Close</CustomButton>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="text-black py-12 shadow-inner">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">RentaFácil</h3>
                    <p class="text-sm text-gray-400">Simplificando el proceso de renta de propiedades desde 2010.</p>
                </div>
                <div v-for="(column, index) in footerColumns" :key="index">
                    <h4 class="text-lg font-semibold mb-4">{{ column.title }}</h4>
                    <ul class="space-y-2">
                        <li v-for="link in column.links" :key="link.href">
                            <router-link :to="link.href" class="text-sm text-gray-400 hover:text-white">{{ link.label }}</router-link>
                        </li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Síguenos</h4>
                    <div class="flex space-x-4">
                        <a v-for="social in socialLinks" :key="social.name" :href="social.href" class="text-gray-400 hover:text-white">
                            <span class="sr-only">{{ social.name }}</span>
                            <component :is="social.icon" class="h-6 w-6" />
                        </a>
                    </div>
                </div>
            </div>
            <div class="mt-8 border-t border-gray-800 pt-8 text-center">
                <p class="text-sm text-gray-400">&copy; {{ new Date().getFullYear() }} RentaFácil. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>
</template>

<script>
const navItems = [
    { href: '/', label: 'Home' },
    { href: '/properties', label: 'Properties' },
    { href: '/propietarios', label: 'Para propietarios' },
    { href: '/contact', label: 'Contact' },
];

const properties = [
    {
        id: 1,
        title: 'Beautiful Family House',
        price: 1200,
        image: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQZtRykGihh_JvnEOQvO6I7yMkN3T45h2LhDw&s',
        address: '123 Main St, City',
        rooms: 3,
        bathrooms: 2
    },
    {
        id: 2,
        title: 'Modern Apartment',
        price: 900,
        image: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQZtRykGihh_JvnEOQvO6I7yMkN3T45h2LhDw&s',
        address: '456 Elm St, City',
        rooms: 2,
        bathrooms: 1
    },
    {
        id: 3,
        title: 'Cozy Studio',
        price: 600,
        image: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQZtRykGihh_JvnEOQvO6I7yMkN3T45h2LhDw&s',
        address: '789 Oak St, City',
        rooms: 1,
        bathrooms: 1
    },
];

const socialLinks = [
    { name: 'Facebook', href: 'https://facebook.com', icon: 'FacebookIcon' },
    { name: 'Twitter', href: 'https://twitter.com', icon: 'TwitterIcon' },
];

const footerColumns = [
    {
        title: 'Company',
        links: [{ href: '/', label: 'About Us' }, { href: '/contact', label: 'Contact' }]
    },
    {
        title: 'Support',
        links: [{ href: '/help', label: 'FAQ' }, { href: '/contact', label: 'Support' }]
    },
];

export default {
    data() {
        return {
            navItems,
            properties,
            socialLinks,
            footerColumns,
            showDetailsModal: false,
            precioMaximo: '',
        };
    },
    methods: {
        openDetailsModal() {
            this.showDetailsModal = true;
        },
        closeDetailsModal() {
            this.showDetailsModal = false;
        },
        filtrarPropiedades() {
            console.log(`Filtrando propiedades con precio máximo: ${this.precioMaximo}`);
        },
    },
};
</script>

<style scoped>
</style>
