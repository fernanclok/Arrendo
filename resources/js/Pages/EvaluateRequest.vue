<script setup>
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import { Head } from '@inertiajs/vue3';
import CustomButton from '@/Components/CustomButton.vue';
</script>
<template>

  <Head title="Request Evaluation" />

  <DashboardLayout>
    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="rounded-lg">
          <div class="p-2">
            <h1 class="text-3xl font-bold text-gray-800">Property Request Evaluation</h1>
            <p class="text-gray-600 mt-2">
              Review and manage Property Request efficiently.
            </p>
          </div>
          <div class="p-6 justify-center items-center">
            <!-- Tabs for filtering -->
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
                <!-- Cabecera del solicitante -->
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
                    <!-- Muestra el estado de la solicitud -->
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

                <!-- Información adicional (Contenido del acordeón) -->
                <div v-if="solicitante.isExpanded" class="mt-4 text-gray-700">
                  <p><strong>Applicant:</strong> {{ solicitante.tenant_user.first_name }} {{
                    solicitante.tenant_user.last_name }}</p>
                  <p><strong>Property:</strong> {{ solicitante.property.street }}</p>
                  <p><strong>Requested:</strong> {{ solicitante.application_date }}</p>

                  <!-- Display the approval/rejection date -->
                  <p v-if="solicitante.status === 'Approved'">
                    <strong>Approved on:</strong> {{ solicitante.approved_at }}
                  </p>
                  <p v-if="solicitante.status === 'Rejected'">
                    <strong>Rejected on:</strong> {{ solicitante.rejected_at }}
                  </p>

                  <p class="mt-2"><strong>Applicant Information:</strong></p>
                  <ul class="list-disc list-inside text-gray-700">
                    <li><strong>Email:</strong> {{ solicitante.tenant_user.email }}</li>
                    <li><strong>Phone:</strong> {{ solicitante.tenant_user.phone }}</li>
                  </ul>
                  <div class="mt-4 flex space-x-3">
                    <CustomButton type='primary' v-if="currentTab === 'Pending'" @click="approveRequest(solicitante.id)"
                      class="py-2">
                      Approve
                    </CustomButton>
                    <CustomButton type='cancel' v-if="currentTab === 'Pending'" @click="rejectRequest(solicitante.id)"
                      class="py-2">
                      Reject
                    </CustomButton>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal para confirmar creación de contrato -->
      <div v-if="showContractModal" class="fixed inset-0 z-40 flex items-center justify-center bg-gray-800 bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg">
          <h2 class="text-lg font-bold mb-4">¿Desea crear un contrato?</h2>
          <div class="flex space-x-4">
            <button @click="handleContractDecision(true)"
              class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">
              Sí
            </button>
            <button @click="handleContractDecision(false)"
              class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">
              No
            </button>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      solicitantes: [],
      currentTab: "Pending",
      tabs: ["Pending", "Approved", "Rejected"],
      showContractModal: false,
      currentSolicitanteId: null,
    };
  },
  computed: {
    filteredSolicitantes() {
      return this.solicitantes.filter(
        (solicitante) => solicitante.status === this.currentTab
      );
    },
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
          isExpanded: false,
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
    approveRequest(id) {
      this.currentSolicitanteId = id;
      this.showContractModal = true;
    },
    async handleContractDecision(createContract) {
      if (createContract) {
        try {
          // Actualizar el estado a "Aprobado" antes de redirigir
          const response = await axios.post(`/api/rental-applications/${this.currentSolicitanteId}/approve`);
          this.updateStatus(
            this.currentSolicitanteId,
            response.data.status || "Approved",
            response.data.approved_at
          );

          // Redirigir a la página de creación de contrato
          this.$router.push(`/contracts/create/${this.currentSolicitanteId}`);
        } catch (error) {
          console.error("Error updating and redirecting:", error);
        }
      } else {
        this.showContractModal = false;
        try {
          // Actualizar el estado a "Aprobado"
          const response = await axios.post(`/api/rental-applications/${this.currentSolicitanteId}/approve`);
          this.updateStatus(
            this.currentSolicitanteId,
            response.data.status || "Approved",
            response.data.approved_at
          );
        } catch (error) {
          console.error("Error approving request:", error);
        }
      }
    },
    async rejectRequest(id) {
      try {
        const response = await axios.post(`/api/rental-applications/${id}/reject`);
        this.updateStatus(id, response.data.status || "Rejected", response.data.rejected_at);
      } catch (error) {
        console.error("Error rejecting request:", error);
      }
    },
    updateStatus(id, newStatus, date) {
      this.solicitantes = this.solicitantes.map((solicitante) =>
        solicitante.id === id
          ? { ...solicitante, status: newStatus, approved_at: date || null, rejected_at: date || null }
          : solicitante
      );
    },
  },
};
</script>


<style scoped>
.card {
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

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