<script setup>
import CustomButton from '@/Components/CustomButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import { ref } from 'vue';
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
                        <Link v-for="item in navItems" :key="item.href" :href="item.href"
                            class="text-sm font-medium hover:underline">
                        {{ item.label }}
                        </Link>
                    </nav>

                    <div class="flex items-center space-x-2">
                        <Link href="/login"
                            class="inline-flex items-center px-3 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 bg-primary text-white hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:ring-green-500">
                        Log In
                        </Link>
                    </div>

                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main :class="{ 'flex': showDetails, 'block': !showDetails }" class="flex-grow relative overflow-hidden p-6">
            <div :class="{ 'w-full': !showDetails, 'w-full md:w-3/4': showDetails }" class="p-4 h-full overflow-y-auto">
                <h1 class="text-3xl font-bold mb-6">Properties in Rent</h1>
                <div class="mb-6">
                    
                    <div class="flex gap-2 mt-1">
                        <select id="zoneSelect" v-model="propertiesSpecifications.selectedZone"
                            class="flex-[3] px-3 py-2 border-gray-300 focus:border-green-700 focus:ring-green-700 rounded-md shadow-sm">
                            <option value="" disabled>Select a Location</option>
                            <option v-for="zone in zones" :key="zone.id" :value="zone.id">{{ zone.name }}</option>
                        </select>

                        <div class="flex-[2] flex flex-col space-y-1">
                            <div class="text-sm font-medium text-gray-600">
                                <b>Maximum Price:</b> {{ displayPrice }} $MXN
                            </div>
                            <input type="range" v-model="selectedPrice" :min="0" :max="priceOptions.length - 1" step="1"
                                class="w-full focus:ring-green-500" />
                            <div class="flex justify-between text-sm text-gray-500">
                                <span v-for="(price, index) in priceOptions" :key="index">
                                    {{ price }} 
                                </span>
                            </div>
                        </div>

                        <CustomButton v-if="!showFilters" @click="toggleFilters" type="primary" class="py-2 ml-2">
                            More Filters
                        </CustomButton>
                        <CustomButton v-else @click="toggleFilters" type="primary" class="py-2 ml-2">
                            Hide Filters
                        </CustomButton>
                        <CustomButton @click="refreshFilters" type="primary" class="py-2 w-32">
                            <i class="mdi mdi-refresh mr-2"></i> REFRESH FILTERS 
                        </CustomButton>
                    </div>

                    <transition enter-active-class="transition-all duration-300 ease-out"
                        enter-from-class="max-h-0 opacity-0" enter-to-class="max-h-[300px] opacity-100"
                        leave-active-class="transition-all duration-300 ease-in"
                        leave-from-class="max-h-[300px] opacity-100" leave-to-class="max-h-0 opacity-0">
                        <div v-if="showFilters" class="overflow-hidden">
                            <div class="bg-gray-50 p-2 rounded-lg shadow-md mt-4">
                                <h3 class="text-lg font-semibold text-gray-700 mb-4">Additional Filters</h3>

                                <div class="grid grid-cols-12 gap-4 mb-4">
                                    <div class="col-span-11 grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <TextInput type="number" v-model="propertiesSpecifications.bedrooms"
                                            placeholder="Bedrooms Number" />
                                        <TextInput type="number" v-model="propertiesSpecifications.bathrooms"
                                            placeholder="Bathrooms Number" step="0.5" />
                                        <TextInput type="number" v-model="propertiesSpecifications.m2"
                                            placeholder="Max M²" />

                                        <div class="col-span-3 grid grid-cols-2 gap-4">
                                            <div
                                                class="flex items-center px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm">
                                                <input type="checkbox" id="allowPets"
                                                    v-model="propertiesSpecifications.allowPets"
                                                    class="mr-2 text-green-500 focus:ring-green-500" />
                                                <label for="allowPets" class="text-sm font-medium text-gray-600">Pets
                                                    Allowed</label>
                                            </div>
                                            <div
                                                class="flex items-center px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm">
                                                <input type="checkbox" id="parking"
                                                    v-model="propertiesSpecifications.parking"
                                                    class="mr-2 text-green-500 focus:ring-green-500" />
                                                <label for="parking" class="text-sm font-medium text-gray-600">Parking
                                                    Lot</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-span-1 flex flex-col items-center justify-center space-y-4">
                                        <CustomButton @click="filterProperties" type="primary" class="py-2 w-32">
                                            <i class="mdi mdi-magnify mr-2"></i> SEARCH
                                        </CustomButton>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>
                </div>

                <div v-if="!hasInitialFilterChanged">
                    <div class="bg-white shadow-md rounded-lg overflow-hidden h-full md:h-auto transform transition duration-300 mb-6">
                        <div class="p-2 text-center">
                            <h2 class="text-xl font-bold mb-2">No filters applied</h2>
                            <p class="text-gray-600 mb-2">You are currently watching all the properties.</p>
                        </div>
                    </div>
                </div>
                <!-- Sección de tarjetas de propiedades con scroll interno en móviles -->
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 h-full overflow-y-auto md:h-auto md:overflow-visible">
                    <div v-if="properties && properties.length > 0" v-for="property in properties" :key="property.id"
                        :class="[
                            'bg-white shadow-md rounded-lg overflow-hidden h-full md:h-auto transform transition duration-300',
                            { 'scale-105 shadow-xl': activePropertyId === property.id }
                        ]">
                        <img :src="property.property_photos_path[0]" alt="Property Photo"
                            class="w-full h-48 object-cover" v-if="property.property_photos_path.length" />
                        <div class="p-4">
                            <h2 class="text-xl font-bold mb-2">{{ property.zone_name }}</h2>
                            <p class="text-gray-600 mb-2">{{ property.city }}, {{ property.state }} , 
                                {{ property.colony }} {{ property.street}} {{ property.number }}</p>
                            <div class="flex justify-between items-center mb-2">
                                <span class="flex items-center">
                                    <!-- SVG Icon for rooms -->
                                    <icon class="mdi mdi-bed mr-2"></icon>
                                    {{ property.total_rooms }} rooms
                                </span>
                                <span v-if="property.half_bathrooms > 0" class="flex items-center">
                                    <!-- SVG Icon for bathrooms -->
                                    <icon class="mdi mdi-toilet mr-2"></icon>{{ property.total_bathrooms }} bathrooms +
                                    {{ property.half_bathrooms }} (<icon class="mdi mdi-fraction-one-half mr-1">)</icon>
                                </span>
                                <span v-else class="flex items-center">
                                    <!-- SVG Icon for bathrooms -->
                                    <icon class="mdi mdi-toilet mr-2"></icon>
                                    {{ property.total_bathrooms }} bathrooms
                                </span>
                                <span v-if="property.rental_rate != null" class="flex items-center">
                                    <!-- SVG Icon for bathrooms -->
                                    <icon class="mdi mdi-star mr-2"></icon>
                                    {{ property.rental_rate }} 
                                </span>
                                <span v-else class="flex items-center">
                                    <!-- SVG Icon for bathrooms -->
                                    <icon class="mdi mdi-star mr-2"></icon>
                                    No rated yet
                                </span>
                            </div>
                        </div>
                        <div class="bg-gray-100 px-4 py-3 flex justify-between items-center">
                            <span class="text-lg font-bold flex items-center">
                                <icon class="mdi mdi-cash mr-2"></icon>
                                {{ property.property_price }} $MXN
                            </span>
                            <CustomButton v-if="property.id != activePropertyId" type="primary"
                                @click="toggleDetails(property.id)">
                                View Details
                            </CustomButton>
                            <CustomButton v-else type="primary" @click="showDetails = false">
                                Hide Details
                            </CustomButton>
                        </div>
                    </div>
                    <div v-else class="text-center text-gray-500 col-span-3 mt-60">
                        <p>No properties found with the applied filters.</p>
                    </div>
                </div>
            </div>

            <div v-if="showDetails" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
                role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity bg-black bg-opacity-50 backdrop-blur-sm"
                        aria-hidden="true" @click="showDetails = false"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div
                        class="inline-block w-full max-w-4xl overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-2xl sm:my-8 sm:align-middle sm:w-full animate-modal-appear">
                        <div class="px-4 pt-5 pb-4 bg-opacity-30 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="w-full mt-3 text-center sm:mt-0 sm:text-left">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-2xl font-bold leading-6" id="modal-title">
                                           {{ selectedProperty.colony }} {{ selectedProperty.street }}, {{ selectedProperty.number }}
                                        </h3>
                                        <button @click="showDetails = false"
                                            class="text-gray-400 transition-colors duration-200 hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                            <span class="sr-only">Close</span>
                                            <i class="text-2xl mdi mdi-close" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                    <p class="mb-4 text-sm text-gray-500">
                                        <i class="mr-2 mdi mdi-map-marker"></i>
                                        {{ selectedProperty.zone_name }}, {{ selectedProperty.city }}, {{
                                            selectedProperty.state }} - CP {{ selectedProperty.postal_code }}
                                    </p>

                                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                        <div class="p-4 rounded-lg">
                                            <h4 class="mb-2 text-lg font-semibold">Key Specifications</h4>
                                            <ul class="space-y-2">
                                                <li><i class="mr-2 mdi mdi-currency-usd"></i><strong
                                                        class="text-gray-500">Price:</strong> {{
                                                            selectedProperty.property_price }} MXN$</li>
                                                <li><i class="mr-2 mdi mdi-bed"></i><strong
                                                        class="text-gray-500">Rooms:</strong> {{
                                                            selectedProperty.total_rooms }}</li>
                                                <li><i class="mr-2 mdi mdi-shower"></i><strong
                                                        class="text-gray-500">Bathrooms:</strong> {{
                                                            selectedProperty.total_bathrooms }}</li>
                                                <li><i class="mr-2 mdi mdi-fraction-one-half"></i><strong
                                                        class="text-gray-500">Half bathrooms:</strong> {{
                                                            selectedProperty.half_bathrooms }}</li>
                                                <li><i class="mr-2 mdi mdi-ruler"></i><strong
                                                        class="text-gray-500">Square Meters:</strong> {{
                                                            selectedProperty.total_m2 }}</li>
                                                <li><i class="mr-2 mdi mdi-car"></i><strong
                                                        class="text-gray-500">Parking:</strong> {{
                                                            selectedProperty.have_parking ? "Yes" : "No" }}</li>
                                                <li><i class="mr-2 mdi mdi-paw"></i><strong class="text-gray-500">Pets
                                                        Allowed:</strong> {{
                                                            selectedProperty.accept_mascots ? "Yes" : "No" }}</li>
                                            </ul>
                                        </div>
                                        <div class="p-4 rounded-lg">
                                            <h4 class="mb-2 text-lg font-semibold">Details</h4>
                                            <p class="text-gray-500">{{ selectedProperty.property_details }}</p>
                                            <div class=" mt-1 mb-4">
                                                <h3 class="text-lg font-semibold text-gray-700 mb-2">Comments ({{ selectedProperty.comments.length }})</h3>
                                                <div v-if="selectedProperty.comments && selectedProperty.comments.length > 0" class="space-y-4 overflow-y-auto max-h-40">
                                                    <div 
                                                        v-for="comment in selectedProperty.comments" 
                                                        :key="comment.id" 
                                                        class="bg-gray-50 border border-gray-200 rounded-lg p-4 shadow-sm"
                                                    >
                                                        <div class="flex items-center justify-between mb-2">
                                                            <span class="text-sm text-gray-500">{{ new Date(comment.created_at).toLocaleDateString() }}</span>
                                                            <div class="flex">
                                                                <icon 
                                                                    v-for="n in 5" 
                                                                    :key="n"
                                                                    :class="n <= comment.comment_rate ? 'mdi mdi-star' : 'mdi mdi-star-outline'" 
                                                                    class="text-yellow-500 text-lg"
                                                                ></icon>
                                                            </div>
                                                        </div>
                                                        <div class="mb-2">
                                                            <p class="text-gray-700 font-medium">{{ comment.user.first_name }} {{ comment.user.last_name }}</p>
                                                        </div>
                                                        <p class="text-gray-600 whitespace-normal break-words">{{ comment.comment }}</p>
                                                    </div>
                                                </div>
                                                <div v-else class="text-gray-500 text-center mt-4">
                                                    <p>No comments found for this property.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="selectedProperty.property_photos_path" class="mb-6">
                                        <h4 class="mb-2 text-lg font-semibold">Photos</h4>
                                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
                                            <div v-for="(url, key) in selectedProperty.property_photos_path" :key="key"
                                                class="relative overflow-hidden rounded-lg group aspect-w-16 aspect-h-9">
                                                <img :src="url" :alt="`Property photo ${key + 1}`"
                                                    class="object-cover w-full h-full transition-transform duration-300 transform group-hover:scale-110" />
                                                <div
                                                    class="absolute inset-0 transition-opacity duration-300 bg-black bg-opacity-0 group-hover:bg-opacity-50">
                                                </div>
                                                <div
                                                    class="absolute inset-0 flex items-center justify-center transition-opacity duration-300 opacity-0 group-hover:opacity-100">
                                                    <i class="text-3xl text-white mdi mdi-magnify"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-3 bg-gray-200 bg-opacity-50 sm:px-6 sm:flex sm:flex-row-reverse">
                            <Link href="/login"
                            class="inline-flex items-center px-3 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 bg-primary text-white hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:ring-green-500">
                        Log In to Schedule a Visit
                        </Link>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="text-black py-12 shadow-inner mt-auto ">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Arrendo</h3>
                        <p class="text-sm text-gray-400">A New Way to Rent Houses</p>
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
                        <h4 class="text-lg font-semibold mb-4">Follow Us</h4>
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
                    <p class="text-sm text-gray-400">&copy; {{ new Date().getFullYear() }} Arrendo. All Rights Reserved
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script>
const navItems = [
    { href: '/', label: 'Home' },
    { href: '/properties', label: 'Properties' },
    // { href: '/propietarios', label: 'For Owners' },
    { href: '/contact', label: 'Contact Us' },
]

export default {
    data() {
        return {
            hasInitialFilterChanged: false,
            showDetails: false,
            showFilters: false,
            activePropertyId: null,
            priceOptions: ["5000", "7000", "10000", "+10000"],
            selectedPrice: 3,
            isRefreshing: false,
            selectedProperty: {},
            properties: [],
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
                maxPrice: '',
                m2: '',
                bedrooms: '',
                bathrooms: '',
                allowPets: false,
                parking: false
            }
        };
    },
    methods: {
        async toggleDetails(propertyId) {
            if (this.activePropertyId === propertyId) {
                this.showDetails = false;
                this.activePropertyId = null;
            } else {
                try {
                    const response = await axios.get(`/api/properties/getPropertyDetails/${propertyId}`);
                    this.selectedProperty = response.data; // Guardamos los detalles de la propiedad
                    this.activePropertyId = propertyId;
                    this.showDetails = true;
                } catch (error) {
                    console.error("Error fetching property details:", error);
                    alert("Failed to fetch property details. Please try again.");
                }
            }
        },
        toggleFilters() {
            this.showFilters = !this.showFilters;
        },
        getProperties() {
            axios.get('/api/properties/getProperties')
                .then(response => {
                    this.properties = response.data;
                })
                .catch(error => {
                    console.error(error);
                });
        },
        filterProperties() {
            this.hasInitialFilterChanged = true;

            var selectedZoneName = this.zones.find(zone => zone.id == this.propertiesSpecifications.selectedZone);

            var filters = {
                selectedZone: selectedZoneName ? selectedZoneName.name : '',
                maxPrice: this.displayPrice,
                m2: this.propertiesSpecifications.m2,
                bedrooms: this.propertiesSpecifications.bedrooms,
                bathrooms: this.propertiesSpecifications.bathrooms,
                allowPets: this.propertiesSpecifications.allowPets,
                parking: this.propertiesSpecifications.parking
            };
            axios.get('/api/properties/filter/', { params: filters })
                .then(response => {
                    this.properties = response.data;
                })
                .catch(error => {
                    console.error(error);
                });
        },
        refreshFilters() {
            this.isRefreshing = true; 
            this.propertiesSpecifications = {
                selectedZone: '',
                maxPrice: '',
                m2: '',
                bedrooms: '',
                bathrooms: '',
                allowPets: false,
                parking: false,
            };
            this.selectedPrice = 3;

            this.getProperties();

            setTimeout(() => {
                this.isRefreshing = false;
                this.hasInitialFilterChanged = false;
            }, 0); 
        },
    },
    watch: {
        showDetails(newVal) {
            if (!newVal) {
                this.activePropertyId = null;
            }
        },
        selectedPrice(newVal) {
            if (!this.isRefreshing) {
                this.hasInitialFilterChanged = true;
                this.filterProperties();
            }
        },
        'propertiesSpecifications.selectedZone': function() {
            if (!this.isRefreshing) {
                this.hasInitialFilterChanged = true;
                this.filterProperties();
            }
        },
        'propertiesSpecifications.maxPrice': function () {
            if (!this.isRefreshing) {
                this.filterProperties();
            }
        }
    },
    computed: {
        displayPrice() {
            return this.priceOptions[this.selectedPrice];
        }
    },
    mounted() {
        this.getProperties();
    }
};
</script>

<style>
input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    height: 20px;
    width: 20px;
    border-radius: 50%;
    background-color: #10b981; 
    border: 2px solid #10b981;
    cursor: pointer;
    transition: transform 0.2s ease-in-out;
}

input[type="range"]::-webkit-slider-thumb:hover {
    transform: scale(1.1);
}

input[type="range"]::-moz-range-thumb {
    height: 10px;
    width: 10px;
    border-radius: 50%;
    background-color: #10b981;
    border: 2px solid #10b981;
    cursor: pointer;
    transition: transform 0.2s ease-in-out;
}

input[type="range"]::-moz-range-thumb:hover {
    transform: scale(1.1);
}

input[type="range"] {
    -webkit-appearance: none;
    appearance: none;
    width: 100%;
    height: 6px;
    background: #e5e7eb;
    border-radius: 3px;
    outline: none;
}

input[type="range"]:focus {
    outline: none;
}
</style>
