<script setup>
import CustomButton from '@/Components/CustomButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

// Datos falsos para las propiedades
const properties = ref([
    {
        id: 1,
        title: 'Beautiful Family House',
        description: 'A beautiful house located in a serene environment.',
        price: 1200,
        image: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQZtRykGihh_JvnEOQvO6I7yMkN3T45h2LhDw&s',
        address: '123 Main St',
        rooms: 3,
        bathrooms: 2,
    },
    {
        id: 2,
        title: 'Modern Apartment',
        description: 'A modern apartment with all the amenities you need.',
        price: 900,
        image: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQZtRykGihh_JvnEOQvO6I7yMkN3T45h2LhDw&s',
        address: '456 Elm St',
        rooms: 2,
        bathrooms: 1,
    },
    {
        id: 3,
        title: 'Cozy Cottage',
        description: 'A cozy cottage perfect for a small family.',
        price: 700,
        image: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQZtRykGihh_JvnEOQvO6I7yMkN3T45h2LhDw&s',
        address: '789 Oak St',
        rooms: 2,
        bathrooms: 1,
    },
    {
        id: 4,
        title: 'Cozy Cottage',
        description: 'A cozy cottage perfect for a small family.',
        price: 700,
        image: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQZtRykGihh_JvnEOQvO6I7yMkN3T45h2LhDw&s',
        address: '789 Oak St',
        rooms: 2,
        bathrooms: 1,
    },
    {
        id: 5,
        title: 'Cozy Cottage',
        description: 'A cozy cottage perfect for a small family.',
        price: 700,
        image: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQZtRykGihh_JvnEOQvO6I7yMkN3T45h2LhDw&s',
        address: '789 Oak St',
        rooms: 2,
        bathrooms: 1,
    },
    {
        id: 6,
        title: 'Cozy Cottage',
        description: 'A cozy cottage perfect for a small family.',
        price: 700,
        image: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQZtRykGihh_JvnEOQvO6I7yMkN3T45h2LhDw&s',
        address: '789 Oak St',
        rooms: 2,
        bathrooms: 1,
    },
]);

</script>

<template>
    <div class="flex flex-col min-h-screen">
        <Head title="properties" />

        <!-- Header Navbar -->
        <header class="shadow">
            <div class="container mx-auto px-4 py-4">
                <div class="flex items-center justify-between">
                    <Link to="/" class="flex items-center space-x-2">
                        <span class="text-xl font-bold">Arrendo</span>
                    </Link>
                    <nav class="hidden md:flex space-x-4">
                        <Link v-for="item in navItems" :key="item.href" :href="item.href" class="text-sm font-medium hover:underline">
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
        <main  :class="{ 'flex': showDetails, 'block': !showDetails }" class="flex-grow relative overflow-hidden p-6">
            <!-- Sección izquierda con el filtro y las propiedades -->
            <div :class="{ 'w-full': !showDetails, 'w-full md:w-3/4': showDetails }" class="p-4 h-full overflow-y-auto">
                <h1 class="text-3xl font-bold mb-6">Properties in Rent</h1>

                <div class="mb-6">
                    <div class="flex gap-2 mt-1">
                        <select id="zoneSelect" v-model="propertiesSpecifications.selectedZone" placeholder="Select a specific area to look for properties" class="flex-1 px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="" disabled>Select an area</option>
                            <option v-for="zone in zones" :key="zone.id" :value="zone.id">{{ zone.name }}</option>
                        </select>

                        <TextInput id="maximumPrice" v-model="propertiesSpecifications.maxPrice" type="number" placeholder="Maximum Price" class="flex-1 px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" />

                        <CustomButton v-if="!showFilters" @click="toggleFilters" type="primary" class="py-2 ml-2">
                            More Filters
                        </CustomButton>
                        <CustomButton v-else @click="toggleFilters" type="primary" class="py-2 ml-2">
                            Hide Filters
                        </CustomButton>

                        <CustomButton @click="refreshFilters" type="primary" class="py-2 ml-2">
                            <i class="mdi mdi-refresh"></i>
                        </CustomButton>
                    </div>
                    <transition
                        enter-active-class="transition-all duration-300 ease-out"
                        enter-from-class="max-h-0 opacity-0"
                        enter-to-class="max-h-[300px] opacity-100"
                        leave-active-class="transition-all duration-300 ease-in"
                        leave-from-class="max-h-[300px] opacity-100"
                        leave-to-class="max-h-0 opacity-0"
                    >
                        <div v-if="showFilters" class="overflow-hidden">
                            <div class="bg-gray-50 p-2 rounded-lg shadow-md mt-4">
                                <h3 class="text-lg font-semibold text-gray-700 mb-4">Additional Filters</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <TextInput type="number" v-model="propertiesSpecifications.bedrooms" placeholder="Rooms Number" 
                                            class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" />
                                    
                                    <TextInput type="number" v-model="propertiesSpecifications.bathrooms" placeholder="Bathrooms Number" 
                                            class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" />
                                    
                                    <TextInput id="m2" v-model="propertiesSpecifications.m2" type="number" placeholder="Max M²" 
                                            class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" />
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div class="flex items-center px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm">
                                        <input type="checkbox" id="allowPets" v-model="propertiesSpecifications.allowPets" class="mr-2 text-indigo-500 focus:ring-indigo-500">
                                        <label for="allowPets" class="text-sm font-medium text-gray-600">Pets Allowed</label>
                                    </div>
                                    
                                    <div class="flex items-center px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm">
                                        <input type="checkbox" id="parking" v-model="propertiesSpecifications.parking" class="mr-2 text-indigo-500 focus:ring-indigo-500">
                                        <label for="parking" class="text-sm font-medium text-gray-600">Parking Lot</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>
                </div>

                <!-- Sección de tarjetas de propiedades con scroll interno en móviles -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 h-full overflow-y-auto md:h-auto md:overflow-visible">
                    <div
                        v-for="property in properties"
                        :key="property.id"
                        :class="[
                            'bg-white shadow-md rounded-lg overflow-hidden h-full md:h-auto transform transition duration-300',
                            { 'scale-105 shadow-xl': activePropertyId === property.id }
                        ]"
                    >
                        <img :src="property.image" alt="Property" class="w-full h-48 object-cover" />
                        <div class="p-4">
                            <h2 class="text-xl font-semibold mb-2">{{ property.title }}</h2>
                            <p class="text-gray-600 mb-2">{{ property.address }}</p>
                            <div class="flex justify-between items-center mb-2">
                                <span class="flex items-center">
                                    <!-- SVG Icon for rooms -->
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16h6M4 6h16M4 6a2 2 0 012-2h12a2 2 0 012 2M4 6v12a2 2 0 002 2h12a2 2 0 002-2V6"></path>
                                    </svg>
                                    {{ property.rooms }} rooms
                                </span>
                                <span class="flex items-center">
                                    <!-- SVG Icon for bathrooms -->
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
                            <CustomButton v-if="property.id != activePropertyId" type="primary" @click="toggleDetails(property.id)">
                                View Details
                            </CustomButton>
                            <CustomButton v-else type="primary" @click="showDetails = false">
                                Hide Details
                            </CustomButton>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Sección derecha del modal -->
            <div class="absolute top-0 right-0 h-full w-full md:w-1/4 bg-gray-100 p-4 overflow-y-auto transition-transform duration-300 transform"
                :class="{ 'translate-x-full': !showDetails, 'translate-x-0': showDetails }">
                <h2 class="text-2xl font-bold mb-4">Right Section</h2>
                <!-- properties details -->
                <CustomButton @click="showDetails = false">Close</CustomButton>
            </div>
        </main>

        <!-- Footer -->
        <footer class="text-black py-12 shadow-inner mt-auto ">
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
                                <router-link :to="link.href" class="text-sm text-gray-400 hover:text-white">
                                    {{ link.label }}
                                </router-link>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Síguenos</h4>
                        <div class="flex space-x-4">
                            <a v-for="social in socialLinks" :key="social.name" :href="social.href"
                                class="text-gray-400 hover:text-white">
                                <span class="sr-only">{{ social.name }}</span>
                                <component :is="social.icon" class="h-6 w-6" />
                            </a>
                        </div>
                    </div>
                </div>
                <div class="border-t border-primary pt-8 text-center">
                    <p class="text-sm text-gray-400">&copy; {{ new Date().getFullYear() }} RentaFácil. Todos los derechos reservados.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script>
const navItems = [
    { href: '/', label: 'Home' },
    { href: '/properties', label: 'Properties' },
    { href: '/propietarios', label: 'Para propietarios' },
    { href: '/contact', label: 'Contact' },
]

export default {
    data() {
        return {
            showDetails: false,
            showFilters: false,
            activePropertyId: null,
            zones: [
                { id: 1, name: 'Centro' },
                { id: 2, name: 'Otay' },
                { id: 3, name: 'Playas de Tijuana' },
                { id: 4, name: 'Cerro Colorado' },
                { id: 5, name: 'La Presa' },
                { id: 6, name: 'La Mesa' },
                { id: 7, name: 'San Antonio de las Buenas' },
                { id: 8, name: 'Sanchéz Taboada' },
                { id: 9, name: 'La Presa Rural' },
                { id: 10, name: 'Sierra Tijuanense' },
            ],
            propertiesSpecifications: {
                selectedZone: '',
                maxPrice: null,
                m2: null,
                bedrooms: null,
                bathrooms: null,
                allowPets: false,
                parking: false
            }
        };
    },
    methods: {
        toggleDetails(propertyId) {
            this.showDetails = !this.showDetails;
            if (this.activePropertyId === propertyId) {
                this.showDetails = false;
                this.activePropertyId = null;
            } else {
                this.activePropertyId = propertyId;
                this.showDetails = true;
            }
        },
        toggleFilters() {
            this.showFilters = !this.showFilters;
        },
        filtrarPropiedades() {
            const filters = {
                selectedZone: this.selectedZone,
                maxPrice: this.maxPrice,
                bedrooms: this.bedrooms,
                bathrooms: this.bathrooms,
                allowPets: this.allowPets,
            };
            
            console.log("Applying filters:", filters);
        },
        refreshFilters() {
            this.propertiesSpecifications = {
                selectedZone: '',
                maxPrice: null,
                m2: null,
                bedrooms: null,
                bathrooms: null,
                allowPets: false,
                parking: false
            };
        }
    },
    watch: {
        showDetails(newVal) {
            if (!newVal) {
                this.activePropertyId = null;
            }
        }
    }
};
</script>