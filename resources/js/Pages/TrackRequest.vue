<template>
  <DashboardLayout>
    <div class="p-2">
      <h1 class="text-3xl font-bold text-gray-800">My applications</h1>
    </div>
    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="rounded-lg">

          <div class="p-6 justify-center items-center">
            <div class="flex justify-center space-x-4 mb-6">
              <button v-for="tab in tabs" :key="tab" @click="currentTab = tab" :class="{
                'inline-block p-4 border-b-2 rounded-t-lg text-xl text-green-700 border-green-700 underline': currentTab === tab,
                'inline-block p-4 border-b-2 rounded-t-lg text-xl hover:text-gray-600 hover:border-gray-300 text-gray-500 border-transparent': currentTab !== tab
              }" class="px-4 py-2 rounded-lg" :style="currentTab === tab ? { 'text-underline-offset': '7px' } : {}">
                {{ tab }}
              </button>
            </div>

            <!-- Filtered content -->
            <div v-if="filteredSolicitantes.length === 0" class="text-center text-gray-500">
              <p>Nothing to show</p>
            </div>
            <div v-else class="space-y-4">
              <div v-for="solicitante in filteredSolicitantes" :key="solicitante.id"
                class="bg-gray-50 p-4 rounded-lg shadow flex flex-col space-y-4">
                <!-- Applicant Header -->
                <div class="flex items-center justify-between cursor-pointer" @click="toggleInfo(solicitante.id)">
                  <div class="flex items-center space-x-4">
                    <div>
                      <p class="font-medium text-gray-900">
                        {{ solicitante.tenant_user.first_name }} {{ solicitante.tenant_user.last_name }}
                      </p>
                      <p class="text-sm text-gray-600">
                        <strong>Property:</strong> {{ solicitante.property.street }}
                      </p>
                    </div>
                  </div>
                  <div class="flex items-center space-x-4">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full" :class="{
                      'bg-green-100 text-green-600': solicitante.status === 'Approved',
                      'bg-red-100 text-red-600': solicitante.status === 'Rejected',
                      'bg-yellow-100 text-yellow-800': solicitante.status === 'Pending'
                    }">
                      {{ solicitante.status }}
                    </span>
                    <i :class="solicitante.isExpanded ? 'mdi mdi-chevron-up' : 'mdi mdi-chevron-down'"></i>
                  </div>
                </div>

                <!-- Expanded Details -->
                <div v-if="solicitante.isExpanded" class="mt-4 text-gray-700">
                  <p><strong>Applicant:</strong> {{ solicitante.tenant_user.first_name }} {{
                    solicitante.tenant_user.last_name }}</p>
                  <p><strong>Property:</strong> {{ solicitante.property.street }}</p>
                  <p><strong>Requested:</strong> {{ solicitante.application_date }}</p>
                  <p v-if="solicitante.status === 'Approved'">
                    <strong>Approved on:</strong> {{ formatDate(solicitante.updated_at) }}
                  </p>
                  <p v-if="solicitante.status === 'Rejected'">
                    <strong>Rejected on:</strong> {{ formatDate(solicitante.updated_at) }}
                  </p>
                  <p><strong>Documents:</strong></p>
                  <ul>
                    <li v-for="(file, index) in solicitante.documents" :key="index">
                      <a :href="getDocumentUrl(file)" target="_blank" rel="noopener noreferrer"
                        class="text-blue-500 underline hover:text-blue-700">
                        Show Document {{ index + 1 }}
                      </a>
                    </li>
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
import { Head } from "@inertiajs/vue3";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";

export default {
  components: { DashboardLayout, Head },
  data() {
    return {
      solicitantes: [], // Solicitudes del tenant logueado
      currentTab: "Pending", // Tab inicial
      tabs: ["Pending", "Approved", "Rejected"], // Tabs disponibles
      selectedFile: null, // Ruta del archivo seleccionado
      selectedFileType: null, // Tipo de archivo seleccionado
    };
  },
  computed: {
    filteredSolicitantes() {
      return this.solicitantes
        .filter((solicitante) => solicitante.status === this.currentTab)
        .sort((a, b) => {
          const dateA = new Date(a.application_date);
          const dateB = new Date(b.application_date);
          return this.currentTab === "Pending" ? dateA - dateB : dateB - dateA;
        });
    },
  },
  created() {
    this.fetchSolicitantes();
  },
  methods: {
    async fetchSolicitantes() {
      try {
        // Endpoint ajustado para que devuelva solo las solicitudes del usuario logueado
        const response = await axios.get("/api/rental-applications");
        this.solicitantes = response.data.map((solicitante) => ({
          ...solicitante,
          isExpanded: false, // Estado inicial de expansión
          documents: JSON.parse(solicitante.tenant_user.document_path || "[]"),
        }));
        console.log("Solicitantes:", this.solicitantes);
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
    formatDate(date) {
      return new Date(date).toLocaleDateString();
    },
    getFileName(filePath) {
      return filePath.split().pop();
    },
    showDocument(filePath) {
      // Determinar el tipo de archivo
      const fileType = filePath.endsWith(".pdf") ? "pdf" : "image";
      // Configurar las propiedades del modal
      this.selectedFile = `/storage/${filePath}`; // Ajusta el path según tu servidor Laravel
      this.selectedFileType = fileType;
      this.showModal = true;
    },
    getDocumentUrl(filePath) {
      let file = filePath.replace('application_files/', '');
      console.log("File path:", file);
      return `/api/properties/file/${file}`; // Cambia la base del path según tu configuración
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