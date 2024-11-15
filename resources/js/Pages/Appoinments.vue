<script setup>
import { ref, onMounted } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <DashboardLayout>
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <h1 class="text-2xl font-semibold text-gray-900 mb-6">Your Appointments</h1>
              
              <div v-if="appointments.length === 0" class="text-gray-600">
                You have no appointments scheduled.
              </div>
              
              <div v-else class="space-y-4">
                <div v-for="appointment in appointments" :key="appointment.id"
                     class="bg-gray-50 p-4 rounded-lg shadow flex flex-col space-y-4">
                  <!-- Header -->
                  <div class="flex items-center justify-between cursor-pointer" @click="toggleAccordion(appointment)">
                    <div class="flex items-center space-x-4">
                      <CalendarIcon class="text-blue-500" />
                      <div>
                        <p class="font-medium text-gray-900">{{ appointment.date }} at {{ appointment.time }}</p>
                        <p class="text-sm text-gray-600">{{ appointment.doctor }}</p>
                      </div>
                    </div>
                    <div class="flex items-center space-x-4">
                      <span :class="{
                        'px-2 py-1 text-xs font-semibold rounded-full': true,
                        'bg-green-100 text-green-800': appointment.status === 'Confirmed',
                        'bg-yellow-100 text-yellow-800': appointment.status === 'Pending',
                        'bg-red-100 text-red-800': appointment.status === 'Cancelled'
                      }">
                        {{ appointment.status }}
                      </span>
                      <button @click.stop="editAppointment(appointment.id)"
                              class="text-blue-600 hover:text-blue-800"
                              :disabled="appointment.status === 'Cancelled'">
                        <PencilIcon class="h-5 w-5" />
                      </button>
                      <button @click.stop="cancelAppointment(appointment.id)"
                              class="text-red-600 hover:text-red-800"
                              :disabled="appointment.status === 'Cancelled'">
                        <XIcon class="h-5 w-5" />
                      </button>
                      <i class="mdi mdi-chevron-down"></i>
                    </div>
                  </div>
                  <!-- Additional Information (Accordion Content) -->
                  <div v-if="appointment.isOpen" class="mt-4 text-gray-700">
                    <p><strong>Location:</strong> {{ appointment.location }}</p>
                    <p><strong>Notes:</strong> {{ appointment.notes }}</p>
                    <!-- Agrega más detalles aquí según sea necesario -->
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
  export default {
    data() {
      return {
        appointments: [
          {
            id: 1,
            date: '2024-11-14',
            time: '10:00 AM',
            doctor: 'Dr. John Doe',
            status: 'Confirmed',
            isOpen: false,
            location: 'Room 101',
            notes: 'Please bring previous reports.'
          },
          {
            id: 2,
            date: '2024-11-17',
            time: '11:00 AM',
            doctor: 'Dr. John Doe',
            status: 'Pending',
            isOpen: false,
            location: 'Room 102',
            notes: 'Please bring previous reports.'
          },
         
        ]
      };
    },
    methods: {
      toggleAccordion(appointment) {
        // Alterna la propiedad isOpen del appointment
        appointment.isOpen = !appointment.isOpen;
      },
      editAppointment(id) {
        // Lógica para editar la cita
        console.log('Edit appointment with ID:', id);
      },
      cancelAppointment(id) {
        // Lógica para cancelar la cita
        console.log('Cancel appointment with ID:', id);
      }
    }
  };
  </script>
