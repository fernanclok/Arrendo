<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
import CustomButton from '@/Components/CustomButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { ref } from 'vue';

</script>

<template>

    <Head title="My Properties" />

    <DashboardLayout>
        <div class="p-2">
            <h1 class="text-3xl font-bold text-gray-800">My Properties</h1>
        </div>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-end items-center mb-6">
                            <CustomButton @click="toggleCreate"
                                class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                                Add Property
                            </CustomButton>
                        </div>

                        <div v-if="isAddingProperty"
                            class="mb-6 bg-gray-100 p-4 rounded-lg min-h-screen flex flex-col items-center justify-center">
                            <!-- Stepper Header -->
                            <div class="w-full max-w-4xl mb-6">
                                <div class="flex justify-between">
                                    <span :class="['step', { 'text-primary font-bold': currentStep === 1 }]">Main
                                        information</span>
                                    <span
                                        :class="['step', { 'text-primary font-bold': currentStep === 2 }]">Pictures</span>
                                    <span
                                        :class="['step', { 'text-primary font-bold': currentStep === 3 }]">Extras</span>
                                </div>
                                <div class="h-1 bg-gray-200 my-2">
                                    <div class="h-1 bg-primary" :style="{ width: (currentStep / 3) * 100 + '%' }"></div>
                                </div>
                            </div>
                            <!---<h2 class="text-lg font-semibold mb-4">Add New Property</h2>-->
                            <form @submit.prevent="handleCreateProperty" class="grid gap-8 w-full max-w-4xl">

                                <!-- Paso 1 -->
                                <div v-if="currentStep === 1"
                                    class="bg-white rounded-lg shadow-md p-6 w-full max-w-4xl">
                                    <h2 class="text-3xl font-semibold text-gray-800 mb-4 text-center">¡Let’s start
                                        creating your post!</h2>
                                    <p class="text-gray-600 text-center mb-6">
                                        Complete your property information step by step.
                                    </p>

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
                                        <InputLabel for="colony" value="Colony" />
                                        <TextInput type="text" id="colony" v-model="newProperty.colony"
                                            class="mt-1 block w-full mx-auto" />
                                        <InputError class="mt-2" :message="newProperty.errors.colony" />
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
                                        <TextInput type="text" id="postal_code" v-model="newProperty.postal_code"
                                            required class="mt-1 block w-full mx-auto" />
                                        <InputError class="mt-2" :message="newProperty.errors.postal_code" />
                                    </div>
                                    <div>
                                        <InputLabel for="zone" value="Zone" />
                                        <select id="zone" v-model="newProperty.zone_id" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                            <option v-for="zone in zones" :key="zone.id" :value="zone.id">{{ zone.name
                                                }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="newProperty.errors.zone_id" />
                                    </div>

                                    <h3 class="text-lg font-medium text-gray-800 mt-8 mb-4">Main Features</h3>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <InputLabel for="total_rooms" value="Total Rooms" />
                                            <TextInput type="number" id="total_rooms" v-model="newProperty.total_rooms"
                                                required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" />
                                            <InputError class="mt-2" :message="newProperty.errors.total_rooms" />
                                        </div>
                                        <div>
                                            <InputLabel for="total_bathrooms" value="Total Bathrooms" />
                                            <TextInput type="number" id="total_bathrooms"
                                                v-model="newProperty.total_bathrooms" required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" />
                                            <InputError class="mt-2" :message="newProperty.errors.total_bathrooms" />
                                        </div>
                                        <div>
                                            <InputLabel for="half_bathrooms" value="Half Bathrooms" />
                                            <TextInput type="number" id="half_bathrooms"
                                                v-model="newProperty.half_bathrooms"
                                                class="mt-1 block w-full mx-auto" />
                                            <InputError class="mt-2" :message="newProperty.errors.half_bathrooms" />
                                        </div>
                                        <!--<div>
                                        <InputLabel for="have_parking" value="Parking Available" />
                                        <select id="have_parking" v-model="newProperty.have_parking" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                        <InputError class="mt-2" :message="newProperty.errors.have_parking" />
                                    </div>-->
                                        <div>
                                            <InputLabel for="parking" value="Parking" />
                                            <TextInput type="number" id="parking" v-model="newProperty.parking"
                                                class="mt-1 block w-full mx-auto" />
                                            <InputError class="mt-2" :message="newProperty.errors.levels" />
                                        </div>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-800 mt-8 mb-4">Antiquity</h3>
                                        <InputLabel for="antiquity" value="Antiquity (years)" />
                                        <TextInput type="number" id="antiquity" v-model="newProperty.antiquity"
                                            class="mt-1 block w-full mx-auto" />
                                        <InputError class="mt-2" :message="newProperty.errors.antiquity" />
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-800 mt-8 mb-4">Surface</h3>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <InputLabel for="surface_built" value="Surface Built (m²)" />
                                            <TextInput type="number" id="surface_built"
                                                v-model="newProperty.surface_built" class="mt-1 block w-full mx-auto" />
                                            <InputError class="mt-2" :message="newProperty.errors.surface_built" />
                                        </div>
                                        <div>
                                            <InputLabel for="total_m2" value="Total Area (m²)" />
                                            <TextInput type="number" id="total_m2" v-model="newProperty.total_m2"
                                                required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" />
                                            <InputError class="mt-2" :message="newProperty.errors.total_m2" />
                                        </div>
                                        <!--<div>
                                        <InputLabel for="total_surface" value="Total Surface (m²)" />
                                        <TextInput type="number" id="total_surface" v-model="newProperty.total_surface" 
                                            class="mt-1 block w-full mx-auto" />
                                        <InputError class="mt-2" :message="newProperty.errors.total_surface" />
                                    </div>-->
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-800 mt-8 mb-4">Price</h3>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <InputLabel for="property_price" value="Property Price" />
                                            <TextInput type="number" id="property_price"
                                                v-model="newProperty.property_price" required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" />
                                            <InputError class="mt-2" :message="newProperty.errors.property_price" />
                                        </div>
                                        <div>
                                            <InputLabel for="maintenance" value="Maintenance" />
                                            <TextInput type="number" id="maintenance" v-model="newProperty.maintenance"
                                                class="mt-1 block w-full mx-auto" />
                                            <InputError class="mt-2" :message="newProperty.errors.maintenance" />
                                        </div>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-800 mt-8 mb-4">Describe the property
                                        </h3>
                                        <InputLabel for="availability" value="Availability" />
                                        <select id="availability" v-model="newProperty.availability" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                            <option value="Available">Available</option>
                                            <option value="Not Available">Not Available</option>
                                        </select>
                                        <InputError class="mt-2" :message="newProperty.errors.availability" />
                                    </div>
                                    <div>
                                        <InputLabel for="property_details" value="Property Details" />
                                        <textarea id="property_details" v-model="newProperty.property_details" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"></textarea>
                                        <InputError class="mt-2" :message="newProperty.errors.property_details" />
                                    </div>

                                    <div class="flex justify-end mt-6">
                                        <CustomButton type="cancel" @click="isAddingProperty = false"
                                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-3">
                                            Cancel
                                        </CustomButton>
                                        <button @click="() => { if (validateCurrentStep()) goToNextStep(); }"
                                            class="px-6 py-2 bg-primary text-white rounded-lg">
                                            Next
                                        </button>
                                    </div>
                                </div>
                                <!---------------------------------------------------->


                                <!-- Paso 2 -->
                                <div v-if="currentStep === 2"
                                    class="bg-white rounded-lg shadow-md p-6 w-full max-w-4xl">
                                    <h2 class="text-3xl font-semibold text-gray-800 mb-4 text-center">
                                        ¡Share photos and videos of the property!
                                    </h2>
                                    <p class="text-gray-600 text-center mb-6">
                                        Upload between 5 and 20 photos. Once uploaded, drag and drop to change its
                                        order.
                                    </p>

                                    <div class="">


                                        <!-- Contenedor del área de carga -->
                                        <label for="property_photos"
                                            class="w-full border-2 border-dashed border-gray-300 rounded-lg flex flex-col items-center justify-center h-48 bg-gradient-to-br from-gray-50 to-gray-100 hover:from-gray-100 hover:to-gray-200 transition-all cursor-pointer">
                                            <!-- Ícono -->
                                            <div class="flex flex-col items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="h-14 w-14 text-blue-500 mb-2" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 15a4 4 0 004 4h10a4 4 0 004-4M7 10l5-5m0 0l5 5m-5-5v12" />
                                                </svg>
                                                <!-- Texto -->
                                                <p class="text-blue-600 font-semibold">Drag & Drop or Click to Upload
                                                </p>
                                                <p class="text-gray-400 text-sm">Only images (JPG, PNG)</p>
                                            </div>
                                            <!-- Input invisible -->
                                            <input type="file" id="property_photos" @change="handleFileUpload" multiple
                                                accept="image/*" class="hidden" />
                                        </label>
                                    </div>

                                    <div class="flex justify-between mt-6">
                                        <button @click="goToPreviousStep"
                                            class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg">Back</button>
                                        <button @click="goToNextStep"
                                            class="px-6 py-2 bg-primary text-white rounded-lg ">Next</button>

                                    </div>
                                </div>
                                <!---------------------------------------------------->


                                <!-- Paso 3 -->
                                <div v-if="currentStep === 3"
                                    class="bg-white rounded-lg shadow-md p-6 w-full max-w-4xl">
                                    <h2 class="text-3xl font-semibold text-gray-800 mb-4 text-center">Add the comforts
                                        of your property!</h2>
                                    <p class="text-gray-600 text-center mb-6">
                                        These optional fields improve the ranking of your publication.
                                    </p>


                                    <h3 class="text-xl font-semibold text-gray-800 mt-4">Additional</h3>
                                    <div class="grid grid-cols-2 gap-4">
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
                                            <InputLabel for="state_conservation" value="State of Conservation" />
                                            <TextInput type="text" id="state_conservation"
                                                v-model="newProperty.state_conservation"
                                                class="mt-1 block w-full mx-auto" />
                                            <InputError class="mt-2" :message="newProperty.errors.state_conservation" />
                                        </div>
                                        <div>
                                            <InputLabel for="closets" value="Closets" />
                                            <TextInput type="number" id="closets" v-model="newProperty.closets"
                                                class="mt-1 block w-full mx-auto" />
                                            <InputError class="mt-2" :message="newProperty.errors.closets" />
                                        </div>
                                        <div>
                                            <InputLabel for="wineries" value="Wineries" />
                                            <TextInput type="number" id="wineries" v-model="newProperty.wineries"
                                                class="mt-1 block w-full mx-auto" />
                                            <InputError class="mt-2" :message="newProperty.errors.wineries" />
                                        </div>
                                        <div>
                                            <InputLabel for="levels" value="Levels" />
                                            <TextInput type="number" id="levels" v-model="newProperty.levels"
                                                class="mt-1 block w-full mx-auto" />
                                            <InputError class="mt-2" :message="newProperty.errors.levels" />
                                        </div>
                                    </div>


                                    <!-- Nueva sección: Extras -->
                                    <h3 class="text-xl font-semibold text-gray-800 mt-4">Amenities</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <!-- Características generales -->
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-700 mb-2">General Features</h4>
                                            <div v-for="option in general_features" :key="option.value"
                                                class="flex items-center space-x-2">
                                                <input type="checkbox" :id="`general-${option.value}`"
                                                    :value="option.value" v-model="selectedgeneral_features"
                                                    class="h-4 w-4 rounded border-gray-300 focus:ring-blue-500">
                                                <label :for="`general-${option.value}`" class="ml-2 text-gray-600">{{
                                                    option.label }}</label>
                                            </div>
                                        </div>

                                        <!-- Servicios -->
                                        <div>
                                            <h4 class="font-semibold text-gray-700 mb-2">Services</h4>
                                            <div v-for="option in services" :key="option.value"
                                                class="flex items-center space-x-2">
                                                <input type="checkbox" :id="`services-${option.value}`"
                                                    :value="option.value" v-model="selectedServices"
                                                    class="h-4 w-4 rounded border-gray-300 focus:ring-blue-500">
                                                <label :for="`services-${option.value}`" class="text-gray-600">{{
                                                    option.label }}</label>
                                            </div>
                                        </div>

                                        <!-- Exteriores -->
                                        <div>
                                            <h4 class="font-semibold text-gray-700 mb-2">Exteriors</h4>
                                            <div v-for="option in exteriors" :key="option.value"
                                                class="flex items-center space-x-2">
                                                <input type="checkbox" :id="`exteriors-${option.value}`"
                                                    :value="option.value" v-model="selectedExteriors"
                                                    class="h-4 w-4 rounded border-gray-300 focus:ring-blue-500">
                                                <label :for="`exteriors-${option.value}`" class="text-gray-600">{{
                                                    option.label }}</label>
                                            </div>
                                        </div>

                                        <!-- Ambientales -->
                                        <div>
                                            <h4 class="font-semibold text-gray-700 mb-2">Environmental</h4>
                                            <div v-for="option in environmentals" :key="option.value"
                                                class="flex items-center space-x-2">
                                                <input type="checkbox" :id="`environmentals-${option.value}`"
                                                    :value="option.value" v-model="selectedEnvironmentals"
                                                    class="h-4 w-4 rounded border-gray-300 focus:ring-blue-500">
                                                <label :for="`environmentals-${option.value}`" class="text-gray-600">{{
                                                    option.label }}</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-span-2 flex justify-end space-x-2 mt-5">
                                        <button @click="goToPreviousStep"
                                            class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg">Back</button>
                                        <CustomButton type="cancel" @click="resetStep"
                                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                            Cancel
                                        </CustomButton>
                                        <CustomButton type="submit"
                                            class="bg-primary text-white hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:ring-green-500">
                                            Add Property
                                        </CustomButton>
                                    </div>
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
                                <!--<div class="col-span-2 flex justify-end space-x-2">
                                    <CustomButton type="cancel" @click="isAddingProperty = false"
                                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                        Cancel
                                    </CustomButton>
                                    <CustomButton type="submit"
                                        class="bg-primary text-white hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:ring-green-500">
                                        Add Property
                                    </CustomButton>
                                </div>
                            -->
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
                                    class="w-full h-48 object-cover"
                                    v-if="property.property_photos_path && property.property_photos_path.length" />
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
                            <div class="bg-white p-6 rounded-lg max-w-3xl w-full shadow-xl">
                                <!-- Header Section -->
                                <div class="relative flex justify-between items-center mb-6">
                                    <h2 class="text-3xl font-semibold text-gray-800 text-center w-full">Property Details
                                    </h2>
                                    <button @click="showDetails = false"
                                        class="absolute top-0 right-0 text-gray-500 hover:text-gray-700 focus:outline-none mr-4 mt-4">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Property Information Section -->
                                <div class="flex justify-center my-5 p-6 overflow-y-auto max-h-[70vh]">
                                    <table
                                        class="table-auto w-full max-w-6xl bg-white rounded-lg shadow-md text-gray-700 border border-gray-300">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="px-4 py-2 border">Field</th>
                                                <th class="px-4 py-2 border">Value</th>
                                                <th class="px-4 py-2 border">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="px-4 py-2 border">Street</td>
                                                <td class="px-4 py-2 border">{{ activeProperty.street }}</td>
                                                <th class="px-4 py-2 border text-center"><!--<button
                                        @click="handleDeleteField(key)"
                                        class="px-3 py-1 bg-red-500 text-white font-semibold rounded hover:bg-red-600 focus:outline-none focus:ring focus:ring-red-300"
                                    >
                                        Eliminar
                                    </button>--></th>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-2 border">Number</td>
                                                <td class="px-4 py-2 border">{{ activeProperty.number }}</td>
                                                <th class="px-4 py-2 border text-center"><!--<button
                                        @click="handleDeleteField(key)"
                                        class="px-3 py-1 bg-red-500 text-white font-semibold rounded hover:bg-red-600 focus:outline-none focus:ring focus:ring-red-300"
                                    >
                                        Eliminar
                                    </button>--></th>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-2 border">Colony</td>
                                                <td class="px-4 py-2 border">{{ activeProperty.colony }}</td>
                                                <th class="px-4 py-2 border text-center"><!--<button
                                        @click="handleDeleteField(key)"
                                        class="px-3 py-1 bg-red-500 text-white font-semibold rounded hover:bg-red-600 focus:outline-none focus:ring focus:ring-red-300"
                                    >
                                        Eliminar
                                    </button>--></th>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-2 border">City</td>
                                                <td class="px-4 py-2 border">{{ activeProperty.city }}</td>
                                                <th class="px-4 py-2 border text-center"><!--<button
                                        @click="handleDeleteField(key)"
                                        class="px-3 py-1 bg-red-500 text-white font-semibold rounded hover:bg-red-600 focus:outline-none focus:ring focus:ring-red-300"
                                    >
                                        Eliminar
                                    </button>--></th>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-2 border">State</td>
                                                <td class="px-4 py-2 border">{{ activeProperty.state }}</td>
                                                <th class="px-4 py-2 border text-center"><!--<button
                                        @click="handleDeleteField(key)"
                                        class="px-3 py-1 bg-red-500 text-white font-semibold rounded hover:bg-red-600 focus:outline-none focus:ring focus:ring-red-300"
                                    >
                                        Eliminar
                                    </button>--></th>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-2 border">Postal Code</td>
                                                <td class="px-4 py-2 border">{{ activeProperty.postal_code }}</td>
                                                <th class="px-4 py-2 border text-center"><!--<button
                                        @click="handleDeleteField(key)"
                                        class="px-3 py-1 bg-red-500 text-white font-semibold rounded hover:bg-red-600 focus:outline-none focus:ring focus:ring-red-300"
                                    >
                                        Eliminar
                                    </button>--></th>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-2 border">Availability</td>
                                                <td class="px-4 py-2 border">{{ activeProperty.availability }}</td>
                                                <th class="px-4 py-2 border text-center"><!--<button
                                        @click="handleDeleteField(key)"
                                        class="px-3 py-1 bg-red-500 text-white font-semibold rounded hover:bg-red-600 focus:outline-none focus:ring focus:ring-red-300"
                                    >
                                        Eliminar
                                    </button>--></th>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-2 border">Bedrooms</td>
                                                <td class="px-4 py-2 border">{{ activeProperty.total_rooms }}</td>
                                                <th class="px-4 py-2 border text-center"><!--<button
                                        @click="handleDeleteField(key)"
                                        class="px-3 py-1 bg-red-500 text-white font-semibold rounded hover:bg-red-600 focus:outline-none focus:ring focus:ring-red-300"
                                    >
                                        Eliminar
                                    </button>--></th>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-2 border">Bathrooms</td>
                                                <td class="px-4 py-2 border">{{ activeProperty.total_bathrooms }}</td>
                                                <th class="px-4 py-2 border text-center"><button
                                                        @click="handleDeleteField(key)"
                                                        class="px-3 py-1 bg-red-500 text-white font-semibold rounded hover:bg-red-600 focus:outline-none focus:ring focus:ring-red-300">
                                                        Delete
                                                    </button></th>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-2 border">Half Bathrooms</td>
                                                <td class="px-4 py-2 border">{{ activeProperty.half_bathrooms }}</td>
                                                <th class="px-4 py-2 border text-center"><button
                                                        @click="handleDeleteField(key)"
                                                        class="px-3 py-1 bg-red-500 text-white font-semibold rounded hover:bg-red-600 focus:outline-none focus:ring focus:ring-red-300">
                                                        Delete
                                                    </button></th>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-2 border">Closets</td>
                                                <td class="px-4 py-2 border">{{ activeProperty.closets }}</td>
                                                <th class="px-4 py-2 border text-center"><!--<button
                                        @click="handleDeleteField(key)"
                                        class="px-3 py-1 bg-red-500 text-white font-semibold rounded hover:bg-red-600 focus:outline-none focus:ring focus:ring-red-300"
                                    >
                                        Eliminar
                                    </button>--></th>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-2 border">Parking</td>
                                                <td class="px-4 py-2 border">{{ activeProperty.have_parking }}</td>
                                                <th class="px-4 py-2 border text-center"><!--<button
                                        @click="handleDeleteField(key)"
                                        class="px-3 py-1 bg-red-500 text-white font-semibold rounded hover:bg-red-600 focus:outline-none focus:ring focus:ring-red-300"
                                    >
                                        Eliminar
                                    </button>--></th>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-2 border">Wineries</td>
                                                <td class="px-4 py-2 border">{{ activeProperty.wineries }}</td>
                                                <th class="px-4 py-2 border text-center"><button
                                                        @click="handleDeleteField(key)"
                                                        class="px-3 py-1 bg-red-500 text-white font-semibold rounded hover:bg-red-600 focus:outline-none focus:ring focus:ring-red-300">
                                                        Delete
                                                    </button></th>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-2 border">Levels</td>
                                                <td class="px-4 py-2 border">{{ activeProperty.levels }}</td>
                                                <th class="px-4 py-2 border text-center"><button
                                                        @click="handleDeleteField(key)"
                                                        class="px-3 py-1 bg-red-500 text-white font-semibold rounded hover:bg-red-600 focus:outline-none focus:ring focus:ring-red-300">
                                                        Delete
                                                    </button></th>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-2 border">Price</td>
                                                <td class="px-4 py-2 border">${{ activeProperty.property_price }}</td>
                                                <th class="px-4 py-2 border text-center"><!--<button
                                        @click="handleDeleteField(key)"
                                        class="px-3 py-1 bg-red-500 text-white font-semibold rounded hover:bg-red-600 focus:outline-none focus:ring focus:ring-red-300"
                                    >
                                        Eliminar
                                    </button>--></th>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-2 border">Maintenance</td>
                                                <td class="px-4 py-2 border">${{ activeProperty.maintenance }}</td>
                                                <th class="px-4 py-2 border text-center"><!--<button
                                        @click="handleDeleteField(key)"
                                        class="px-3 py-1 bg-red-500 text-white font-semibold rounded hover:bg-red-600 focus:outline-none focus:ring focus:ring-red-300"
                                    >
                                        Eliminar
                                    </button>--></th>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-2 border">Surface Built</td>
                                                <td class="px-4 py-2 border">{{ activeProperty.surface_built }} m²</td>
                                                <th class="px-4 py-2 border text-center"><!--<button
                                        @click="handleDeleteField(key)"
                                        class="px-3 py-1 bg-red-500 text-white font-semibold rounded hover:bg-red-600 focus:outline-none focus:ring focus:ring-red-300"
                                    >
                                        Eliminar
                                    </button>--></th>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-2 border">Total Surface</td>
                                                <td class="px-4 py-2 border">{{ activeProperty.total_m2 }} m²</td>
                                                <th class="px-4 py-2 border text-center"><button
                                                        @click="handleDeleteField(key)"
                                                        class="px-3 py-1 bg-red-500 text-white font-semibold rounded hover:bg-red-600 focus:outline-none focus:ring focus:ring-red-300">
                                                        Delete
                                                    </button></th>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-2 border">Accept Mascots</td>
                                                <td class="px-4 py-2 border">{{ activeProperty.accept_mascots }}</td>
                                                <th class="px-4 py-2 border text-center"><button
                                                        @click="handleDeleteField(key)"
                                                        class="px-3 py-1 bg-red-500 text-white font-semibold rounded hover:bg-red-600 focus:outline-none focus:ring focus:ring-red-300">
                                                        Delete
                                                    </button></th>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-2 border">Antiquity</td>
                                                <td class="px-4 py-2 border">{{ activeProperty.antiquity }} years</td>
                                                <th class="px-4 py-2 border text-center"><!--<button
                                        @click="handleDeleteField(key)"
                                        class="px-3 py-1 bg-red-500 text-white font-semibold rounded hover:bg-red-600 focus:outline-none focus:ring focus:ring-red-300"
                                    >
                                        Eliminar
                                    </button>--></th>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-2 border">State of Conservation</td>
                                                <td class="px-4 py-2 border">{{ activeProperty.state_conservation }}
                                                </td>
                                                <th class="px-4 py-2 border text-center"><!--<button
                                        @click="handleDeleteField(key)"
                                        class="px-3 py-1 bg-red-500 text-white font-semibold rounded hover:bg-red-600 focus:outline-none focus:ring focus:ring-red-300"
                                    >
                                        Eliminar
                                    </button>--></th>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-2 border">Property Details</td>
                                                <td class="px-4 py-2 border">{{ activeProperty.property_details }}</td>
                                                <th class="px-4 py-2 border text-center"><!--<button
                                        @click="handleDeleteField(key)"
                                        class="px-3 py-1 bg-red-500 text-white font-semibold rounded hover:bg-red-600 focus:outline-none focus:ring focus:ring-red-300"
                                    >
                                        Eliminar
                                    </button>--></th>
                                            </tr>
                                            <!-- <tr>
                                    <td class="px-4 py-2 border">Services</td>
                                    <td class="px-4 py-2 border">{{ activeProperty.services }}</td>
                                    <th class="px-4 py-2 border text-center"><button
                                        @click="handleDeleteField(key)"
                                        class="px-3 py-1 bg-red-500 text-white font-semibold rounded hover:bg-red-600 focus:outline-none focus:ring focus:ring-red-300"
                                    >
                                        Eliminar
                                    </button></th>
                                </tr>-->
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Botones de acción (Editar y Eliminar) fuera del contenedor -->
                                <div class="mt-4 flex justify-end space-x-4 ml-10 mr-10">
                                    <CustomButton type="primary" @click="toggleEdit(activeProperty.id)"
                                        class="bg-yellow-500 text-white hover:bg-yellow-600">
                                        Edit
                                    </CustomButton>
                                    <CustomButton type="danger" @click="handleDeleteProperty(activeProperty.id)"
                                        class="bg-red-500 text-white hover:bg-red-600">
                                        Delete
                                    </CustomButton>
                                </div>


                                <!-- Formulario de edición (cuando isEditingProperty sea true) -->
                                <div v-if="isEditingProperty"
                                    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                                    <div class="bg-white p-8 rounded-lg max-w-4xl w-full shadow-xl">
                                        <!-- Header del formulario -->
                                        <div class="flex justify-between items-center p-6 border-b border-gray-200">
                                            <h2 class="text-3xl font-semibold text-gray-800 text-center w-full">Edit
                                                Property</h2>
                                            <button @click="isEditingProperty = false"
                                                class="text-gray-500 hover:text-gray-700 focus:outline-none">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>

                                        <!-- Contenido del formulario Edit -->
                                        <div class="p-6 overflow-y-auto max-h-[70vh]">
                                            <form @submit.prevent="handleEditProperty"
                                                class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                                <!-- Campos del formulario -->
                                                <div class="flex items-center space-x-2">
                                                    <div class="flex-1">
                                                        <InputLabel for="street" value="Street" />
                                                        <TextInput type="text" id="street" v-model="newProperty.street"
                                                            required
                                                            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-blue-500" />
                                                    </div>
                                                    <!--<button 
                                                @click="handleClearField('street')" 
                                                type="button" 
                                                class="p-2 bg-red-500 text-white rounded-lg hover:bg-red-600 focus:outline-none">
                                                <i class="fa fa-trash"></i>
                                            </button>-->
                                                </div>

                                                <div class="flex flex-col">
                                                    <InputLabel for="number" value="Number" />
                                                    <TextInput type="text" id="number" v-model="newProperty.number"
                                                        required
                                                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-blue-500" />
                                                    <buttom>delete</buttom>
                                                </div>
                                                <div class="flex flex-col">
                                                    <InputLabel for="colony" value="Colony" />
                                                    <TextInput type="text" id="colony" v-model="newProperty.colony"
                                                        required
                                                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-blue-500" />
                                                </div>
                                                <div class="flex flex-col">
                                                    <InputLabel for="city" value="City" />
                                                    <TextInput type="text" id="city" v-model="newProperty.city" required
                                                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-blue-500" />
                                                </div>
                                                <div class="flex flex-col">
                                                    <InputLabel for="state" value="State" />
                                                    <TextInput type="text" id="state" v-model="newProperty.state"
                                                        required
                                                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-blue-500" />
                                                </div>
                                                <div class="flex flex-col">
                                                    <InputLabel for="postal_code" value="Postal Code" />
                                                    <TextInput type="text" id="postal_code"
                                                        v-model="newProperty.postal_code" required
                                                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-blue-500" />
                                                </div>
                                                <div class="flex flex-col">
                                                    <InputLabel for="availability" value="Availability" />
                                                    <TextInput type="text" id="availability"
                                                        v-model="newProperty.availability" required
                                                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-blue-500" />
                                                </div>
                                                <div class="flex flex-col">
                                                    <InputLabel for="total_rooms" value="Bedrooms" />
                                                    <TextInput type="number" id="total_rooms"
                                                        v-model="newProperty.total_rooms" required
                                                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-blue-500" />
                                                </div>
                                                <div class="flex flex-col">
                                                    <InputLabel for="total_bathrooms" value="Bathrooms" />
                                                    <TextInput type="number" id="total_bathrooms"
                                                        v-model="newProperty.total_bathrooms" required
                                                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-blue-500" />
                                                </div>
                                                <div class="flex flex-col">
                                                    <InputLabel for="half_bathrooms" value="Half Bathrooms" />
                                                    <TextInput type="number" id="half_bathrooms"
                                                        v-model="newProperty.half_bathrooms" required
                                                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-blue-500" />
                                                </div>
                                                <div class="flex flex-col">
                                                    <InputLabel for="closets" value="Closets" />
                                                    <TextInput type="number" id="closets" v-model="newProperty.closets"
                                                        required
                                                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-blue-500" />
                                                </div>
                                                <div class="flex flex-col">
                                                    <InputLabel for="have_parking" value="Parking" />
                                                    <TextInput type="text" id="have_parking"
                                                        v-model="newProperty.have_parking" required
                                                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-blue-500" />
                                                </div>
                                                <div class="flex flex-col">
                                                    <InputLabel for="parking" value="Parking" />
                                                    <TextInput type="number" id="parking" v-model="newProperty.parking"
                                                        required
                                                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-blue-500" />
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <div class="flex-1">
                                                        <InputLabel for="wineries" value="Wineries" />
                                                        <TextInput type="number" id="wineries"
                                                            v-model="newProperty.wineries" required
                                                            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-blue-500" />
                                                    </div>
                                                    <button @click="handleClearField('wineries')" type="button"
                                                        class="p-2 bg-red-500 text-white rounded-lg hover:bg-red-600 focus:outline-none">Delete
                                                    </button>
                                                </div>
                                                <div class="flex flex-col">
                                                    <InputLabel for="levels" value="Levels" />
                                                    <TextInput type="number" id="levels" v-model="newProperty.levels"
                                                        required
                                                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-blue-500" />
                                                </div>
                                                <div class="flex flex-col">
                                                    <InputLabel for="property_price" value="Property Price" />
                                                    <TextInput type="number" id="property_price"
                                                        v-model="newProperty.property_price" required
                                                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-blue-500" />
                                                </div>
                                                <div class="flex flex-col">
                                                    <InputLabel for="maintenance" value="Maintenance" />
                                                    <TextInput type="number" id="maintenance"
                                                        v-model="newProperty.maintenance" required
                                                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-blue-500" />
                                                </div>
                                                <div class="flex flex-col">
                                                    <InputLabel for="surface_built" value="Surface Built" />
                                                    <TextInput type="number" id="surface_built"
                                                        v-model="newProperty.surface_built" required
                                                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-blue-500" />
                                                </div>
                                                <div class="flex flex-col">
                                                    <InputLabel for="total_m2" value="Total m2" />
                                                    <TextInput type="number" id="total_m2"
                                                        v-model="newProperty.total_m2" required
                                                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-blue-500" />
                                                </div>
                                                <div class="flex flex-col">
                                                    <InputLabel for="accept_mascots" value="Accept Mascots" />
                                                    <TextInput type="text" id="accept_mascots"
                                                        v-model="newProperty.accept_mascots" required
                                                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-blue-500" />
                                                </div>
                                                <div class="flex flex-col">
                                                    <InputLabel for="antiquity" value="Antiquity" />
                                                    <TextInput type="number" id="antiquity"
                                                        v-model="newProperty.antiquity" required
                                                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-blue-500" />
                                                </div>
                                                <div class="sm:col-span-2 flex flex-col">
                                                    <InputLabel for="property_details" value="Property Details" />
                                                    <textarea id="property_details"
                                                        v-model="newProperty.property_details" required
                                                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-blue-500"></textarea>
                                                </div>
                                            </form>
                                        </div>

                                        <!-- Footer con botones -->
                                        <div class="flex justify-end p-6 border-t border-gray-200 space-x-4">
                                            <button @click="isEditingProperty = false" type="button"
                                                class="py-2 px-4 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                                Cancel
                                            </button>
                                            <button @click="handleEditProperty" type="submit"
                                                class="py-2 px-4 bg-green-500 text-white rounded-lg hover:bg-green-600">
                                                Save Changes
                                            </button>
                                        </div>
                                    </div>
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
            activeProperty: [],
            showDetails: false,
            isAddingProperty: false,
            isEditingProperty: false,
            currentStep: 1,
            newProperty: {
                general_features: [],
                services: [],
                exteriors: [],
                environmentals: [],

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

                colony: '',
                half_bathrooms: 0,
                surface_built: null,
                total_surface: null,
                antiquity: null,
                maintenance: null,
                state_conservation: '',
                wineries: null,
                closets: null,
                levels: null,
                parking: null,
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
                    zone_id: null,

                    colony: null,
                    half_bathrooms: null,
                    surface_built: null,
                    total_surface: null,
                    antiquity: null,
                    maintenance: null,
                    state_conservation: null,
                    wineries: null,
                    closets: null,
                    levels: null,
                    parking: null,
                }

            },

            general_features: [
                { value: "Disabled Access", label: "Disabled Access" },
                { value: "Swimming Pool", label: "Swimming Pool" },
                { value: "Jacuzzi", label: "Jacuzzi" },
                { value: "Pets", label: "Pets" },
                { value: "Furnished", label: "Furnished" },
                { value: "Equipped Kitchen", label: "Equipped Kitchen" },
                { value: "Private Security", label: "Private Security" },
                { value: "Terrace", label: "Terrace" },
            ],
            services: [
                { value: "Air Conditioning", label: "Air Conditioning" },
                { value: "Gym", label: "Gym" },
                { value: "Internet/Wifi", label: "Internet/Wifi" },
                { value: "Room Service", label: "Room Service" },
                { value: "Natural Gas", label: "Natural Gas" },
            ],
            exteriors: [
                { value: "Grill", label: "Grill" },
                { value: "Private Garden", label: "Private Garden" },
                { value: "Parking Roofed", label: "Parking Roofed" },
                { value: "Balcony", label: "Balcony" },
            ],
            environmentals: [
                { value: "Room Tv", label: "Room Tv" },
                { value: "Studio", label: "Studio" },
                { value: "Multipurpose Room", label: "Multipurpose Room" },
                { value: "Laundry Room", label: "Laundry Room" },
            ],
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
            axios.get(`/api/properties/${id}`)
                .then(response => {
                    this.activeProperty = response.data;
                    this.showDetails = true;
                })
                .catch(error => {
                    console.error(error);
                });
        },
        handleClearField(field) {
            if (confirm(`Are you sure you want to clear the value for ${field}?`)) {
                delete this.newProperty[field]; // Elimina el campo del objeto
            }
        },
        toggleEdit(id) {
            axios.get(`/api/properties/${id}`)
                .then(response => {
                    this.newProperty = { ...response.data };  // Cargar los datos de la propiedad
                    console.log(this.newProperty); // Verificar los datos cargados
                    this.isEditingProperty = true;  // Mostrar el formulario de edición
                })
                .catch(error => {
                    console.error(error);
                });
        },
        handleEditProperty() {
            const propertyData = {
                id: this.newProperty.id,
                street: this.newProperty.street || '',
                number: this.newProperty.number || '',
                city: this.newProperty.city || '',
                state: this.newProperty.state || '',
                postal_code: this.newProperty.postal_code || '',
                availability: this.newProperty.availability || 'Available',
                total_bathrooms: parseFloat(this.newProperty.total_bathrooms) || 0,
                total_rooms: parseInt(this.newProperty.total_rooms, 10) || 0,
                total_m2: parseInt(this.newProperty.total_m2, 10) || 0,
                have_parking: Boolean(this.newProperty.have_parking),
                accept_mascots: Boolean(this.newProperty.accept_mascots),
                property_price: parseFloat(this.newProperty.property_price) || 0,
                property_details: this.newProperty.property_details || '',
                colony: this.newProperty.colony || '',
                half_bathrooms: parseInt(this.newProperty.half_bathrooms, 10) || 0,
                surface_built: parseInt(this.newProperty.surface_built, 10) || 0,
                maintenance: parseFloat(this.newProperty.maintenance) || 0,
                antiquity: parseInt(this.newProperty.antiquity, 10) || 0,
                levels: parseInt(this.newProperty.levels, 10) || 0,
                wineries: parseInt(this.newProperty.wineries, 10) || 0,
                state_conservation: this.newProperty.state_conservation || '',
                parking: parseInt(this.newProperty.parking, 10) || 0,
            };

            axios.put(`/api/properties/${propertyData.id}`, propertyData)
                .then(response => {
                    console.log('Property updated successfully:', response.data);

                    // Actualizar la propiedad activa con los nuevos datos
                    this.activeProperty = response.data;

                    // Actualizar la lista de propiedades local
                    const index = this.properties.findIndex(property => property.id === response.data.id);
                    if (index !== -1) {
                        this.properties[index] = response.data;
                    }

                    // Cerrar el formulario de edición
                    this.isEditingProperty = false;
                })
                .catch(error => {
                    console.error('Error:', error.response.data.errors);
                });
        },

        handleDeleteProperty(id) {
            if (confirm('Are you sure you want to delete this property?')) {
                axios.delete(`/api/properties/${id}`)
                    .then(response => {
                        // Eliminar la propiedad de la lista localmente
                        this.properties = this.properties.filter(property => property.id !== id);
                        alert(response.data.message);  // Mostrar mensaje de éxito
                        this.showDetails = false; // Cerrar la vista de detalles
                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
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
                half_bathrooms: 0,
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
        },
        resetStep() {
            this.isAddingProperty = false;
            this.currentStep = 1;
        },
        validateCurrentStep() {
            const errors = {};

            if (this.currentStep === 1) {
                // Validaciones del paso 1
                if (!this.newProperty.street) errors.street = "Street is required.";
                if (!this.newProperty.number) errors.number = "Number is required.";
                if (!this.newProperty.city) errors.city = "City is required.";
                if (!this.newProperty.state) errors.state = "State is required.";
                if (!this.newProperty.postal_code) errors.postal_code = "Postal Code is required.";
                if (!this.newProperty.zone_id) errors.zone_id = "Zone is required.";
                if (!this.newProperty.total_rooms) errors.total_rooms = "Total Rooms is required.";
                if (!this.newProperty.total_bathrooms) errors.total_bathrooms = "Total Bathrooms is required.";
                if (!this.newProperty.property_price) errors.property_price = "Property Price is required.";
                if (!this.newProperty.total_m2) errors.total_m2 = "Total m2 is required.";
                if (!this.newProperty.property_details) errors.property_details = "Property Details is required.";
            }

            if (this.currentStep === 2) {
                // Validaciones del paso 2
                if (!this.newProperty.property_photos || this.newProperty.property_photos.length < 1) {
                    errors.property_photos = "You must upload at least 1 photo.";
                }
            }

            if (this.currentStep === 3) {
                // Validaciones del paso 3 (si se requieren)
                if (!this.newProperty.accept_mascots) errors.accept_mascots = "Accept Mascots selection is required.";
            }

            this.newProperty.errors = errors;

            // Si no hay errores, la validación pasa
            return Object.keys(errors).length === 0;
        },
        goToNextStep() {
            if (this.validateCurrentStep()) {
                this.currentStep++;
            } else {

            }
        },
        goToPreviousStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
            }
        },
    }
}

// Función para agregar la propiedad (último paso)
const addProperty = () => {
    alert("¡Propiedad agregada exitosamente!");
};

</script>