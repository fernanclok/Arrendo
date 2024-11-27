<template>
    <div class="relative flex flex-col min-w-0 break-words bg-white w-full mb-6 shadow-lg rounded">
        <!-- Header -->
        <div class="rounded-t mb-0 px-4 py-3 border-0">
            <div class="flex flex-wrap items-center">
                <div class="relative w-full px-4 max-w-full flex-grow flex-1">
                    <h3 class="font-semibold text-base text-gray-700">
                        Properties Status
                    </h3>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="block w-full overflow-x-auto">
            <table class="items-center w-full bg-transparent border-collapse">
                <thead>
                    <tr>
                        <th
                            class="px-6 bg-gray-50 text-gray-500 align-middle border border-solid border-gray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                            Property
                        </th>
                        <th
                            class="px-6 bg-gray-50 text-gray-500 align-middle border border-solid border-gray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                            Tenant
                        </th>
                        <th
                            class="px-6 bg-gray-50 text-gray-500 align-middle border border-solid border-gray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                            Monthly Rent
                        </th>
                        <th
                            class="px-6 bg-gray-50 text-gray-500 align-middle border border-solid border-gray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                            Contract Ends
                        </th>
                        <th
                            class="px-6 bg-gray-50 text-gray-500 align-middle border border-solid border-gray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                            Status
                        </th>
                        <th
                            class="px-6 bg-gray-50 text-gray-500 align-middle border border-solid border-gray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="property in paginatedProperties" :key="property.property_id">
                        <th
                            class="truncate max-w-xs border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-left">
                            {{ property.property_address }}
                        </th>
                        <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                            <div class="flex items-center">
                                <i class="mdi mdi-account-circle mr-2 text-gray-400"></i>
                                {{ property.tenant_name || 'No tenant' }}
                            </div>
                        </td>
                        <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                            ${{ (property.rental_amount && !isNaN(property.rental_amount)) ?
                                parseFloat(property.rental_amount).toFixed(2) : 'N/A' }}
                        </td>
                        <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                            <div class="flex items-center">
                                <i class="mdi mdi-calendar mr-2"
                                    :class="getContractDateColor(property.contract_end)"></i>
                                {{ property.contract_end || 'N/A' }}
                            </div>
                        </td>
                        <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                            <div class="flex flex-col gap-2">
                                <span :class="getStatusClasses(property.contract_status)">
                                    <i class="mdi" :class="getStatusIcon(property.contract_status)"></i>
                                    {{ property.contract_status }}
                                </span>
                            </div>
                        </td>
                        <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-ss whitespace-nowrap p-4">
                            <div class="flex items-center gap-2">
                                <button @click="openViewModal(property)" class="text-gray-500 hover:text-gray-700">
                                    <i class="mdi mdi-eye"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        <div class="px-4 py-3 flex justify-between items-center">
            <button @click="previousPage" :disabled="currentPage <= 1"
                class="bg-indigo-500 text-white px-3 py-1 rounded">Previous</button>
            <span>Page {{ currentPage }} of {{ totalPages }}</span>
            <button @click="nextPage" :disabled="currentPage >= totalPages"
                class="bg-indigo-500 text-white px-3 py-1 rounded">Next</button>
        </div>
    </div>

    <!-- Modal -->
    <div v-if="isModalOpen" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-1/3 p-6">
            <h3 class="text-lg font-semibold mb-4">{{ isViewOnly ? 'View Property' : 'Edit Property' }}</h3>
            <form @submit.prevent="saveChanges">
                <div class="mb-4">
                    <label for="propertyName" class="block text-sm font-medium text-gray-700">Property Name</label>
                    <input type="text" id="propertyName" v-model="selectedProperty.property_address"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        :disabled="isViewOnly" />
                </div>
                <div class="mb-4">
                    <label for="monthlyRent" class="block text-sm font-medium text-gray-700">Monthly Rent</label>
                    <input type="number" id="monthlyRent" v-model="selectedProperty.rental_amount"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        :disabled="isViewOnly" />
                </div>
                <div class="mb-4">
                    <label for="contractEnd" class="block text-sm font-medium text-gray-700">Contract Ends</label>
                    <input type="date" id="contractEnd" v-model="selectedProperty.contract_end"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        :disabled="isViewOnly" />
                </div>
                <div class="mb-4">
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select id="status" v-model="selectedProperty.contract_status"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        :disabled="isViewOnly">
                        <option value="Active">Active</option>
                        <option value="Pending Renewal">Pending Renewal</option>
                        <option value="Terminated">Terminated</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="tenantName" class="block text-sm font-medium text-gray-700">Tenant Name</label>
                    <input type="text" id="tenantName" v-model="selectedProperty.tenant_name"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        :disabled="isViewOnly" />
                </div>
                <!-- Buttons -->
                <div class="flex justify-end">
                    <button type="button" @click="closeModal" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">
                        {{ isViewOnly ? 'Close' : 'Cancel' }}
                    </button>
                    <button v-if="!isViewOnly" type="submit" class="bg-indigo-500 text-white px-4 py-2 rounded">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

</template>

<script>
export default {
    name: 'PropertiesDashboard',
    props: {
        propertiesData: Array
    },
    data() {
        return {
            currentPage: 1, // Página actual
            rowsPerPage: 10, // Número de filas por página
            isModalOpen: false, // Controla si el modal está abierto o no
            selectedProperty: null,
            isViewOnly: false
        };
    },
    computed: {
        // Filtrar las propiedades que se mostrarán en la página actual
        paginatedProperties() {
            const startIndex = (this.currentPage - 1) * this.rowsPerPage;
            const endIndex = startIndex + this.rowsPerPage;
            return this.propertiesData.slice(startIndex, endIndex);
        },
        // Calcular el número total de páginas
        totalPages() {
            return Math.ceil(this.propertiesData.length / this.rowsPerPage);
        }
    },
    methods: {
        getStatusClasses(status) {
            const baseClasses = "px-2 py-1 text-xs rounded-full flex items-center gap-1 ";
            switch (status) {
                case "Active":
                    return baseClasses + "bg-green-100 text-green-800";
                case "Terminated":
                    return baseClasses + "bg-red-100 text-red-800";
                case "Pending Renewal":
                    return baseClasses + "bg-yellow-100 text-yellow-800";
                default:
                    return baseClasses + "bg-gray-100 text-gray-800";
            }
        },
        getStatusIcon(status) {
            switch (status) {
                case "Active":
                    return "mdi-check-circle";
                case "Terminated":
                    return "mdi-home-circle";
                case "Pending Renewal":
                    return "mdi-clock";
                default:
                    return "mdi-information";
            }
        },
        getContractDateColor(date) {
            if (!date) return "text-gray-400";
            const daysUntilEnd = Math.ceil((new Date(date) - new Date()) / (1000 * 60 * 60 * 24));
            if (daysUntilEnd < 30) return "text-red-500";
            if (daysUntilEnd < 90) return "text-yellow-500";
            return "text-green-500";
        },
        // Cambiar a la siguiente página
        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
            }
        },
        // Cambiar a la página anterior
        previousPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
            }
        },
        openEditModal(property) {
            this.selectedProperty = { ...property }; // Clona la propiedad para editar
            this.isViewOnly = false; // Modo edición
            this.isModalOpen = true;
        },

        openViewModal(property) {
            this.selectedProperty = { ...property }; // Clona la propiedad para ver
            this.isViewOnly = true; // Modo solo visualización
            this.isModalOpen = true;
        },
        closeModal() {
            this.isModalOpen = false;
            this.selectedProperty = null;
        },
        saveChanges() {
            const index = this.propertiesData.findIndex(
                (p) => p.property_id === this.selectedProperty.property_id
            );
            if (index !== -1) {
                this.$set(this.propertiesData, index, this.selectedProperty); // Actualiza la propiedad
            }
            this.closeModal(); // Cierra el modal
        },
    }
}
</script>
