<template>
    <DashboardLayout>
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white shadow-md rounded-lg">
            <div class="p-6 border-b border-gray-200">
              <h1 class="text-3xl font-bold text-gray-800">Application Evaluation</h1>
              <p class="text-gray-600 mt-2">
                Review and manage applications efficiently.
              </p>
            </div>
            <div class="p-6">
              <!-- Tabs for filtering -->
              <div class="flex justify-start space-x-4 mb-6">
                <button
                  v-for="tab in tabs"
                  :key="tab"
                  @click="currentTab = tab"
                  :class="{
                    'bg-blue-500 text-white': currentTab === tab,
                    'bg-gray-100 text-gray-800': currentTab !== tab
                  }"
                  class="px-4 py-2 rounded-lg"
                >
                  {{ tab }}
                </button>
              </div>
  
              <!-- Filtered content -->
              <div v-for="solicitante in filteredSolicitantes" :key="solicitante.id" class="mb-6">
                <div class="bg-gray-100 p-4 rounded-lg shadow-sm">
                  <div class="flex justify-between items-center">
                    <div>
                      <p class="text-lg font-semibold text-gray-800">
                        {{ solicitante.tenant_user.first_name }} {{ solicitante.tenant_user.last_name }}
                      </p>
                      <p class="text-sm text-gray-600">
                        <strong>Property:</strong> {{ solicitante.property.street }}
                      </p>
                    </div>
                    <button
                      @click="toggleInfo(solicitante.id)"
                      class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition"
                    >
                      {{ solicitante.isExpanded ? 'Less' : 'More' }}
                    </button>
                  </div>
                </div>
                <div
                  v-if="solicitante.isExpanded"
                  class="mt-4 p-4 bg-white border border-gray-200 rounded-lg"
                >
                  <p><strong>Applicant:</strong> {{ solicitante.tenant_user.first_name }} {{ solicitante.tenant_user.last_name }}</p>
                  <p><strong>Property:</strong> {{ solicitante.property.street }}</p>
                  <p><strong>Requested:</strong> {{ solicitante.application_date }}</p>
                  <p class="mt-2"><strong>Applicant Information:</strong></p>
                  <ul class="list-disc list-inside text-gray-700">
                    <li><strong>Email:</strong> {{ solicitante.tenant_user.email }}</li>
                    <li><strong>Phone:</strong> {{ solicitante.tenant_user.phone }}</li>
                  </ul>
                  <div class="mt-4 flex space-x-3">
                    <button
                      v-if="currentTab === 'Pending'"
                      @click="approveRequest(solicitante.id)"
                      class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition"
                    >
                      Approve
                    </button>
                    <button
                      v-if="currentTab === 'Pending'"
                      @click="rejectRequest(solicitante.id)"
                      class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition"
                    >
                      Reject
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
  import axios from "axios";
  import DashboardLayout from "@/Layouts/DashboardLayout.vue";
  
  export default {
    components: { DashboardLayout },
    data() {
      return {
        solicitantes: [], // Data fetched from API
        currentTab: "Pending", // Default selected tab
        tabs: ["Pending", "Approved", "Rejected"], // Tab options
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
            isExpanded: false, // Add `isExpanded` property to toggle
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
      async approveRequest(id) {
        try {
          const response = await axios.post(`/api/rental-applications/${id}/approve`);
          this.updateStatus(id, response.data.status || "Approved");
        } catch (error) {
          console.error("Error approving request:", error);
        }
      },
      async rejectRequest(id) {
        try {
          const response = await axios.post(`/api/rental-applications/${id}/reject`);
          this.updateStatus(id, response.data.status || "Rejected");
        } catch (error) {
          console.error("Error rejecting request:", error);
        }
      },
      updateStatus(id, newStatus) {
        this.solicitantes = this.solicitantes.map((solicitante) =>
          solicitante.id === id ? { ...solicitante, status: newStatus } : solicitante
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
  