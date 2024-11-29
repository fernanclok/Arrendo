<script setup>
import { ref, onMounted } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import CustomButton from '@/Components/CustomButton.vue';
import TextInput from '@/Components/TextInput.vue';
import axios from 'axios';
import { Head, usePage } from '@inertiajs/vue3';
</script>

<template>

    <Head title="Search Properties" />

    <DashboardLayout>
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
                                <b>Maximum Price:</b> {{ displayPrice }}
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
                                        <CustomButton @click="refreshFilters" type="primary" class="py-2 w-32">
                                            <i class="mdi mdi-refresh mr-2"></i> REFRESH
                                        </CustomButton>
                                        <CustomButton @click="filterProperties" type="primary" class="py-2 w-32">
                                            <i class="mdi mdi-magnify mr-2"></i> SEARCH
                                        </CustomButton>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>
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
                            <p class="text-gray-600 mb-2">{{ property.city }} {{ property.state }} , {{ property.street
                                }}, {{ property.number }}</p>
                            <div class="flex justify-between items-center mb-2">
                                <span class="flex items-center">
                                    <!-- SVG Icon for rooms -->
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 10h.01M12 10h.01M16 10h.01M9 16h6M4 6h16M4 6a2 2 0 012-2h12a2 2 0 012 2M4 6v12a2 2 0 002 2h12a2 2 0 002-2V6">
                                        </path>
                                    </svg>
                                    {{ property.total_rooms }} rooms
                                </span>
                                <span class="flex items-center">
                                    <!-- SVG Icon for bathrooms -->
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 1.343-3 3v4h6v-4c0-1.657-1.343-3-3-3zM5 20h14a2 2 0 002-2v-5a2 2 0 00-2-2H5a2 2 0 00-2 2v5a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    {{ property.total_bathrooms }} bathrooms
                                </span>
                            </div>
                        </div>
                        <div class="bg-gray-100 px-4 py-3 flex justify-between items-center">
                            <span class="text-lg font-bold flex items-center">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 1.343-3 3v4h6v-4c0-1.657-1.343-3-3-3zM5 20h14a2 2 0 002-2v-5a2 2 0 00-2-2H5a2 2 0 00-2 2v5a2 2 0 002 2z">
                                    </path>
                                </svg>
                                ${{ property.property_price }}
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
                    <div v-else class="text-center text-gray-500 col-span-3">
                        <p>No properties found with the applied filters.</p>
                    </div>
                </div>
            </div>

            <div v-if="showDetails" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
                role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 backdrop-blur-sm"
                        aria-hidden="true" @click="showDetails = false"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div
                        class="inline-block w-full max-w-4xl overflow-hidden text-left align-bottom transition-all transform bg-gradient-to-br from-gray-900 to-gray-800 rounded-lg shadow-2xl sm:my-8 sm:align-middle sm:w-full animate-modal-appear">
                        <div class="px-4 pt-5 pb-4 bg-opacity-30 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="w-full mt-3 text-center sm:mt-0 sm:text-left">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-2xl font-bold leading-6 text-white" id="modal-title">
                                            {{ selectedProperty.street }}, {{ selectedProperty.number }}
                                        </h3>
                                        <button @click="showDetails = false"
                                            class="text-gray-400 transition-colors duration-200 hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                            <span class="sr-only">Close</span>
                                            <i class="text-2xl mdi mdi-close" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                    <p class="mb-4 text-sm text-gray-300">
                                        <i class="mr-2 mdi mdi-map-marker"></i>
                                        {{ selectedProperty.zone_name }}, {{ selectedProperty.city }}, {{
                                        selectedProperty.state }} - CP {{ selectedProperty.postal_code }}
                                    </p>

                                    <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-2">
                                        <div class="p-4 bg-gray-700 bg-opacity-50 rounded-lg">
                                            <h4 class="mb-2 text-lg font-semibold text-white">Key Specifications</h4>
                                            <ul class="space-y-2 text-gray-300">
                                                <li><i class="mr-2 mdi mdi-currency-usd"></i><strong>Price:</strong> ${{
                                                    selectedProperty.property_price }}</li>
                                                <li><i class="mr-2 mdi mdi-bed"></i><strong>Rooms:</strong> {{
                                                    selectedProperty.total_rooms }}</li>
                                                <li><i class="mr-2 mdi mdi-shower"></i><strong>Bathrooms:</strong> {{
                                                    selectedProperty.total_bathrooms }}</li>
                                                <li><i class="mr-2 mdi mdi-ruler"></i><strong>Square Meters:</strong> {{
                                                    selectedProperty.total_m2 }}</li>
                                                <li><i class="mr-2 mdi mdi-car"></i><strong>Parking:</strong> {{
                                                    selectedProperty.have_parking ? "Yes" : "No" }}</li>
                                                <li><i class="mr-2 mdi mdi-paw"></i><strong>Pets Allowed:</strong> {{
                                                    selectedProperty.accept_mascots ? "Yes" : "No" }}</li>
                                            </ul>
                                        </div>
                                        <div class="p-4 bg-gray-700 bg-opacity-50 rounded-lg">
                                            <h4 class="mb-2 text-lg font-semibold text-white">Details</h4>
                                            <p class="text-gray-300">{{ selectedProperty.property_details }}</p>
                                        </div>
                                    </div>

                                    <div v-if="selectedProperty.property_photos_path" class="mb-6">
                                        <h4 class="mb-2 text-lg font-semibold text-white">Photos</h4>
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
                        <div class="px-4 py-3 bg-gray-700 bg-opacity-50 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button"
                                class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white transition-colors duration-200 bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm"
                                @click="applyToListing(selectedProperty.id)">
                                <i class="mr-2 mdi mdi-check"></i> Apply to this Listing
                            </button>
                            <button type="button"
                                class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 transition-colors duration-200 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                @click="scheduleAppointment = true">
                                <i class="mr-2 mdi mdi-calendar"></i> Schedule a Visit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <div v-if="scheduleAppointment"
            class="fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center">
            <div class="bg-white p-6 rounded-lg">
                <h2 class="text-2xl font-bold mb-2">Schedule an Appointment</h2>
                <p class="text-gray-600 mb-2">The owner will be notified of your request.</p>
                <form @submit.prevent="ApplyToAnAppointment(selectedProperty.id)">
                    <div class="mb-4">
                        <label for="requested_date" class="block text-sm font-medium text-gray-700">Requested
                            Date</label>
                        <input type="datetime-local" v-model="appointmentForm.requested_date" id="requested_date"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-700 focus:ring focus:ring-green-700 focus:ring-opacity-50"
                            :min="minDateTime" :max="maxDateTime"
                            @change="checkDisabledDate(appointmentForm.requested_date)">
                        <p v-if="isDateDisabled" class="text-red-500 text-sm mt-2">
                            This date is already booked. Please select a different date.
                        </p>
                        <p class="text-gray-600 text-sm mt-2">
                            Please try to select a time with at least one hour difference from the booked times.
                        </p>
                    </div>
                    <div class="flex justify-end">
                        <CustomButton type="cancel" @click="scheduleAppointment = false">Close</CustomButton>
                        <button type="submit" :class="{
                            'ml-2 inline-flex items-center px-3 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 bg-primary text-white hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:ring-green-500': !isDateDisabled,
                            'ml-2 inline-flex items-center px-3 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 bg-gray-200 text-gray-700 cursor-not-allowed': isDateDisabled
                        }" :disabled="isDateDisabled">Submit</button>
                    </div>
                </form>
            </div>
        </div>

    </DashboardLayout>
</template>

<script>
export default {
    data() {
        const now = new Date();
        const minDateTime = new Date(now.getTime() - now.getTimezoneOffset() * 60000).toISOString().slice(0, 16);

        const maxDate = new Date(now);
        maxDate.setMonth(now.getMonth() + 6);
        const maxDateTime = new Date(maxDate.getTime() - maxDate.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
        return {
            showDetails: false,
            scheduleAppointment: false,
            showFilters: false,
            activePropertyId: null,
            priceOptions: ["5000", "7000", "10000", "+10000"],
            selectedPrice: 0,
            isRefreshing: false,
            properties: [],
            appointmentForm: {
                requested_date: '',
            },
            selectedProperty: {},
            isDateDisabled: false,
            minDateTime,
            maxDateTime,
            user: null,
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
                    this.selectedProperty = response.data;
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
            this.selectedPrice = 0;

            this.getProperties();

            setTimeout(() => {
                this.isRefreshing = false;
            }, 0);
        },
        async applyToListing(propertyId) {
            try {
                const requestData = {
                    property_id: propertyId,
                    tenant_user_id: this.user.id, // ID del usuario autenticado
                    application_date: new Date().toISOString().split('T')[0], // Fecha actual en formato YYYY-MM-DD
                    status: 'Pending', // Estado inicial
                };

                const response = await axios.post('/api/properties/applicate', requestData);
                alert("Application sent successfully!"); // Mensaje para éxito
            } catch (error) {
                // Verifica si el error es de validación u otro tipo
                if (error.response && error.response.status === 409) {
                    // Mostrar el mensaje del backend para errores específicos
                    alert(error.response.data.message || "You have already applied to this property.");
                } else {
                    // Mensaje genérico para otros errores
                    console.error("Error applying to listing:", error.response?.data || error);
                    alert("Failed to apply to the listing. Please try again.");
                }
            }
        },
        ApplyToAnAppointment(propertyId) {
            const form = {
                property_id: propertyId,
                tenant_user_id: this.user.id,
                requested_date: this.appointmentForm.requested_date,
                status: 'Pending'
            };
            axios.post('/api/properties/appointment', form)
                .then(response => {
                    this.emmiter.emit('show_notification', {
                        title: 'Success',
                        message: `Appointment requested successfully! Check the status in <a href="/appointments" class="text-green-700 underline">Appointments</a>.`,
                        type: 'success'
                    });
                    this.scheduleAppointment = false;
                })
                .catch(error => {
                    console.error("Error applying to listing:", error.response?.data || error);
                    this.emmiter.emit('show_notification', {
                        title: 'Error',
                        message: "Failed to apply. Please try again.",
                        type: 'error'
                    });
                });
        },
        checkDisabledDate(selectedDate) {
            const selectedDateObj = new Date(selectedDate);
            const selectedDateTijuana = new Date(
                selectedDateObj.toLocaleString("en-US", { timeZone: "America/Tijuana" })
            );

            this.isDateDisabled = this.selectedProperty.appointments.some((appointment) => {
                const appointmentDateObj = new Date(
                    new Date(appointment).toLocaleString("en-US", { timeZone: "America/Tijuana" })
                );

                const diffInMs = Math.abs(selectedDateTijuana - appointmentDateObj);
                const diffInMinutes = diffInMs / (1000 * 60);

                return diffInMinutes < 60;
            });
        }
    },
    watch: {
        showDetails(newVal) {
            if (!newVal) {
                this.activePropertyId = null;
            }
        },
        selectedPrice(newVal) {
            if (!this.isRefreshing) {
                this.filterProperties();
            }
        },
        'propertiesSpecifications.selectedZone': function () {
            if (!this.isRefreshing) {
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
        this.user = usePage().props.auth.user;
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
