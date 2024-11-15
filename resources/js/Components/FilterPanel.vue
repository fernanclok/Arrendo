<script setup>
import CustomButton from '@/Components/CustomButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

</script>

<template>
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
  </template>
  
  <script>
  export default {
    data() {
      return {
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
        showFilters: false,
        propertiesSpecifications: {
          selectedZone: '',
          maxPrice: '',
          bedrooms: '',
          bathrooms: '',
          m2: '',
          allowPets: false,
          parking: false,
        },
      };
    },
    methods: {
      toggleFilters() {
        this.showFilters = !this.showFilters;
      },
      refreshFilters() {
        this.propertiesSpecifications = {
          selectedZone: '',
          maxPrice: '',
          bedrooms: '',
          bathrooms: '',
          m2: '',
          allowPets: false,
          parking: false,
        };
      },
    },
  };
  </script>