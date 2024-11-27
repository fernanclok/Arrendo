<template>
    <DashboardLayout>
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <h1 class="text-2xl font-semibold text-gray-900 mb-6">Notificaciones</h1>
  
              <div v-if="solicitantes.length === 0" class="text-gray-600">
                No hay solicitudes disponibles.
              </div>
  
              <div v-else class="space-y-4">
                <div v-for="solicitante in solicitantes" :key="solicitante.id" class="bg-gray-50 p-4 rounded-lg shadow flex flex-col space-y-4">
                  <!-- Cabecera del solicitante -->
                  <div class="flex items-center justify-between cursor-pointer" @click="toggleInfo(solicitante.id)">
                    <div class="flex items-center space-x-4">
                      <div>
                        <p class="font-medium text-gray-900">
                          {{ solicitante.tenant_user.first_name }} {{ solicitante.tenant_user.last_name }}
                        </p>
                        <p class="text-sm text-gray-600">
                          <strong>Propiedad:</strong> {{ solicitante.property.street }}
                        </p>
                      </div>
                    </div>
                    <div class="flex items-center space-x-4">
                      <!-- Muestra el estado de la solicitud -->
                      <span class="text-sm font-semibold" :class="{
                          'text-green-600': solicitante.status === 'Aprobada',
                          'text-red-600': solicitante.status === 'Rechazada',
                          'text-gray-600': solicitante.status === 'Pendiente'
                        }">
                        {{ solicitante.status }}
                      </span>
                      <i :class="solicitante.isExpanded ? 'mdi mdi-chevron-up' : 'mdi mdi-chevron-down'"></i>
                    </div>
                  </div>
  
                  <!-- Información adicional (Contenido del acordeón) -->
                  <div v-if="solicitante.isExpanded" class="mt-4 text-gray-700">
                    <p><strong>Propiedad:</strong> {{ solicitante.property.street }}</p>
                    <p><strong>Status:</strong> {{ solicitante.status }}</p>
                    <p><strong>Fecha de solicitud:</strong> {{ solicitante.application_date }}</p>
  
                    <p class="mt-2"><strong>Información de la propiedad:</strong></p>
                    <ul class="list-disc ml-6 space-y-1">
                      <li><strong>Ubicación:</strong> {{ solicitante.property.street }}</li>
                      <li><strong>Fotografía:</strong> <img :src="solicitante.property.photo_path" alt="Imagen de la propiedad" class="w-32 h-32 object-cover"/></li>
                    </ul>
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
  import axios from "axios";
  import DashboardLayout from "@/Layouts/DashboardLayout.vue";
  
  export default {
    components: { DashboardLayout },
    data() {
      return {
        solicitantes: [], // Datos obtenidos de la API
      };
    },
    created() {
      this.fetchSolicitantes();
    },
    methods: {
      async fetchSolicitantes() {
        try {
          const response = await axios.get("/api/rental-applications");
          this.solicitantes = response.data.map((solicitante) => ({
            ...solicitante,
            isExpanded: false, // Añade propiedad `isExpanded` para alternar
          }));
        } catch (error) {
          console.error("Error fetching solicitantes:", error);
        }
      },
      toggleInfo(id) {
        this.solicitantes = this.solicitantes.map((solicitante) =>
          solicitante.id === id
            ? { ...solicitante, isExpanded: !solicitante.isExpanded }
            : solicitante
        );
      },
    },
  };
  </script>
  
  <style scoped>
  /* Estilo para mejorar la vista */
  .card-header {
    background-color: #f8f8f8;
    padding: 10px 15px;
    font-weight: bold;
    color: #555;
  }
  
  .card-body {
    padding: 15px;
    font-size: 0.9rem;
    color: #555;
  }
  
  button {
    padding: 8px 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 0.9rem;
  }
  
  .btn-primary {
    background-color: #007bff;
    color: white;
  }
  
  .btn-primary:hover {
    background-color: #0056b3;
  }
  
  .btn-success {
    background-color: #28a745;
    color: white;
    margin-right: 10px;
  }
  
  .btn-success:hover {
    background-color: #218838;
  }
  
  .btn-danger {
    background-color: #dc3545;
    color: white;
  }
  
  .btn-danger:hover {
    background-color: #c82333;
  }
  
  .mb-4 {
    margin-bottom: 1rem;
  }
  </style>
  