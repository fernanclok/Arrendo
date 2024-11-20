<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
import CustomButton from '@/Components/CustomButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
</script>

<template>

    <Head title="My Properties" />

    <DashboardLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h1 class="text-2xl font-semibold text-gray-900">My Properties</h1>
                            <CustomButton @click="toggleCreate"
                                class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                                Add Property
                            </CustomButton>
                        </div>

                        <div v-if="isAddingProperty" class="mb-6 bg-gray-100 p-4 rounded-lg">
                            <h2 class="text-lg font-semibold mb-4">Add New Property</h2>
                            <form @submit.prevent="handleCreateProperty" class="grid grid-cols-2 gap-4">
                                <div>
                                    <InputLabel for="street" value="Street" />
                                    <TextInput type="text" id="street" v-model="newProperty.street" required
                                        class="mt-1 block w-full mx-auto" />
                                    <InputError class="mt-2" :message="newProperty.errors.street" />
                                </div>
                                <div>
                                    <InputLabel for="number" value="Number" />
                                    <TextInput type="text" id="number" v-model="newProperty.number" required
                                        class="mt-1 block w-full mx-auto" />
                                    <InputError class="mt-2" :message="newProperty.errors.number" />
                                </div>
                                <div>
                                    <InputLabel for="city" value="City" />
                                    <TextInput type="text" id="city" v-model="newProperty.city" required
                                        class="mt-1 block w-full mx-auto" />
                                    <InputError class="mt-2" :message="newProperty.errors.city" />
                                </div>
                                <div>
                                    <InputLabel for="state" value="State" />
                                    <TextInput type="text" id="state" v-model="newProperty.state" required
                                        class="mt-1 block w-full mx-auto" />
                                    <InputError class="mt-2" :message="newProperty.errors.state" />
                                </div>
                                <div>
                                    <InputLabel for="postal_code" value="Postal Code" />
                                    <TextInput type="text" id="postal_code" v-model="newProperty.postal_code" required
                                        class="mt-1 block w-full mx-auto" />
                                    <InputError class="mt-2" :message="newProperty.errors.postal_code" />
                                </div>
                                <div>
                                    <InputLabel for="availability" value="Availability" />
                                    <select id="availability" v-model="newProperty.availability" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <option value="Available">Available</option>
                                        <option value="Not Available">Not Available</option>
                                    </select>
                                    <InputError class="mt-2" :message="newProperty.errors.availability" />
                                </div>
                                <div>
                                    <InputLabel for="total_bathrooms" value="Total Bathrooms" />
                                    <TextInput type="number" id="total_bathrooms" v-model="newProperty.total_bathrooms"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" />
                                    <InputError class="mt-2" :message="newProperty.errors.total_bathrooms" />
                                </div>
                                <div>
                                    <InputLabel for="total_rooms" value="Total Rooms" />
                                    <TextInput type="number" id="total_rooms" v-model="newProperty.total_rooms" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" />
                                    <InputError class="mt-2" :message="newProperty.errors.total_rooms" />
                                </div>
                                <div>
                                    <InputLabel for="total_m2" value="Total Area (m²)" />
                                    <TextInput type="number" id="total_m2" v-model="newProperty.total_m2" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" />
                                    <InputError class="mt-2" :message="newProperty.errors.total_m2" />
                                </div>
                                <div>
                                    <InputLabel for="have_parking" value="Parking Available" />
                                    <select id="have_parking" v-model="newProperty.have_parking" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                    <InputError class="mt-2" :message="newProperty.errors.have_parking" />
                                </div>
                                <div>
                                    <InputLabel for="accept_mascots" value="Accept Mascots" />
                                    <select id="accept_mascots" v-model="newProperty.accept_mascots" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                    <InputError class="mt-2" :message="newProperty.errors.accept_mascots" />
                                </div>
                                <div>
                                    <InputLabel for="property_price" value="Property Price" />
                                    <TextInput type="number" id="property_price" v-model="newProperty.property_price"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" />
                                    <InputError class="mt-2" :message="newProperty.errors.property_price" />
                                </div>
                                <div>
                                    <InputLabel for="property_details" value="Property Details" />
                                    <textarea id="property_details" v-model="newProperty.property_details" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"></textarea>
                                    <InputError class="mt-2" :message="newProperty.errors.property_details" />
                                </div>
                                <div>
                                    <InputLabel for="zone" value="Zone" />
                                    <select id="zone" v-model="newProperty.zone_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <option v-for="zone in zones" :key="zone.id" :value="zone.id">{{ zone.name }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="newProperty.errors.zone_id" />
                                </div>
                                <div>
                                    <InputLabel for="property_photos" value="Property Photos" />
                                    <input type="file" id="property_photos" @change="handleFileUpload" multiple
                                        accept="image/*"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" />
                                    <InputError class="mt-2" :message="newProperty.errors.property_photos" />
                                </div>
                                <!-- Contenedor para vistas previas -->
                                <div class="mt-4 grid grid-cols-3 gap-4">
                                    <div v-for="(photo, index) in newProperty.property_photos" :key="index"
                                        class="relative">
                                        <img :src="photo.preview" alt="Property Preview"
                                            class="w-full h-32 object-cover rounded-md shadow-md" />
                                        <button @click="removePhoto(index)"
                                            class="absolute top-0 right-0 bg-red-500 text-white rounded-full p-1">
                                            &times;
                                        </button>
                                    </div>
                                </div>
                                <div class="col-span-2 flex justify-end space-x-2">
                                    <CustomButton type="cancel" @click="isAddingProperty = false"
                                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                        Cancel
                                    </CustomButton>
                                    <CustomButton type="submit"
                                        class="bg-primary text-white hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:ring-green-500">
                                        Add Property
                                    </CustomButton>
                                </div>
                            </form>
                        </div>


                        <div v-if="properties.length === 0"
                            class="flex items-center justify-center h-64 text-gray-600 text-lg">
                            You have no properties listed.
                        </div>

                        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 auto-rows-fr">
                            <div v-for="property in properties" :key="property.id"
                                class="bg-white shadow-md rounded-lg overflow-hidden h-full transform transition duration-300 flex flex-col">
                                <img :src="property.property_photos_path[0]" alt="Property Photo"
                                    class="w-full h-48 object-cover" v-if="property.property_photos_path.length" />
                                <div class="p-4 flex-grow">
                                    <div class="flex justify-between items-center mb-2">
                                        <h2 class="text-xl font-semibold">{{ property.street }}, {{ property.city }}
                                        </h2>
                                        <div :class="{
                                            'px-2 py-1 text-xs font-semibold rounded-full': true,
                                            'bg-green-100 text-green-800': property.availability === 'Available',
                                            'bg-blue-100 text-blue-800': property.availability === 'Rented',
                                            'bg-yellow-100 text-yellow-800': property.availability === 'Under Maintenance'
                                        }" class="ml-2">
                                            {{ property.availability }}
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 10h.01M12 10h.01M16 10h.01M9 16h6M4 6h16M4 6a2 2 0 012-2h12a2 2 0 012 2M4 6v12a2 2 0 002 2h12a2 2 0 002-2V6">
                                                </path>
                                            </svg>
                                            {{ property.total_rooms }} rooms
                                        </span>
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8c-1.657 0-3 1.343-3 3v4h6v-4c0-1.657-1.343-3-3-3zM5 20h14a2 2 0 002-2v-5a2 2 0 00-2-2H5a2 2 0 00-2 2v5a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                            {{ property.total_bathrooms }} bathrooms
                                        </span>
                                    </div>
                                    <div class="text-gray-500 text-sm">
                                        {{ property.property_details }}
                                    </div>
                                </div>
                                <div class="bg-gray-100 px-4 py-3 flex justify-between items-center space-x-2">
                                    <span class="text-lg font-bold">
                                        ${{ property.property_price }}
                                    </span>
                                    <div class="flex-shrink-0">
                                        <CustomButton type="primary" @click="toggleDetails(property.id)">
                                            View Details
                                        </CustomButton>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- modal details -->
                        <div v-if="showDetails"
                            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                            <div class="bg-white p-6 rounded-lg max-w-3xl w-full">
                                <div class="flex justify-between items-center mb-4">
                                    <h2 class="text-xl font-semibold">Property Details</h2>
                                    <button @click="showDetails = false" class="text-gray-500 hover:text-gray-700">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
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
import axios from 'axios'

export default {
    props: ['user'],
    data() {
        return {
            properties: [],
            zones: [],
            activePropertyId: null,
            showDetails: false,
            isAddingProperty: false,
            newProperty: {
                street: '',
                number: '',
                city: '',
                state: '',
                postal_code: '',
                availability: 'Available',
                total_bathrooms: 0,
                total_rooms: 0,
                total_m2: 0,
                have_parking: 0,
                accept_mascots: 0,
                property_price: 0,
                property_details: '',
                property_photos: [],
                zone_id: null,
                errors: {
                    street: null,
                    number: null,
                    city: null,
                    state: null,
                    postal_code: null,
                    availability: null,
                    total_bathrooms: null,
                    total_rooms: null,
                    total_m2: null,
                    have_parking: null,
                    accept_mascots: null,
                    property_price: null,
                    property_details: null,
                    property_photos: null,
                    zone_id: null
                }

            }
        }
    },
    mounted() {
        this.handleGetProperties();
    },
    methods: {
        toggleCreate() {
            this.resetNewProperty();
            this.isAddingProperty = true;
            this.handleGetZones();
        },
        toggleDetails(id) {
            this.activePropertyId = id;
            this.showDetails = true;
        },
        handleGetZones() {
            axios.get('/api/zones')
                .then(response => {
                    this.zones = response.data;
                })
                .catch(error => {
                    console.error(error);
                });
        },
        handleFileUpload(event) {
            const files = event.target.files;
            this.newProperty.property_photos = [
                ...this.newProperty.property_photos,
                ...Array.from(files).map(file => ({
                    file,
                    preview: URL.createObjectURL(file),
                }))
            ];
        },
        removePhoto(index) {
            this.newProperty.property_photos.splice(index, 1);
        },
        handleGetProperties() {
            axios.get('/api/properties', {
                params: {
                    user_id: this.user.id
                }
            })
                .then(response => {
                    this.properties = response.data;
                })
                .catch(error => {
                    console.error(error);
                });
        },
        handleCreateProperty() {
            const formData = new FormData();
            for (const key in this.newProperty) {
                if (key === 'property_photos') {
                    for (let i = 0; i < this.newProperty.property_photos.length; i++) {
                        formData.append('property_photos[]', this.newProperty.property_photos[i].file);
                    }
                } else if (key !== 'errors') {
                    formData.append(key, this.newProperty[key]);
                }
            }

            formData.append('user_id', this.user.id);

            axios.post('/api/properties/create', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
                .then(response => {
                    this.properties.push(response.data);
                    this.isAddingProperty = false;
                    this.handleGetProperties();
                    this.resetNewProperty();
                })
                .catch(error => {
                    console.error(error);
                    if (error.response.status === 422) {
                        this.newProperty.errors = error.response.data.errors;
                    }
                });
        },
        resetNewProperty() {
            this.newProperty = {
                street: '',
                number: '',
                city: '',
                state: '',
                postal_code: '',
                availability: 'Available',
                total_bathrooms: 0,
                total_rooms: 0,
                total_m2: 0,
                have_parking: 0,
                accept_mascots: 0,
                property_price: 0,
                property_details: '',
                property_photos: [],
                zone_id: null,
                errors: {
                    street: null,
                    number: null,
                    city: null,
                    state: null,
                    postal_code: null,
                    availability: null,
                    total_bathrooms: null,
                    total_rooms: null,
                    total_m2: null,
                    have_parking: null,
                    accept_mascots: null,
                    property_price: null,
                    property_details: null,
                    property_photos: null,
                    zone_id: null
                }
            };
        }
    }
}
</script>