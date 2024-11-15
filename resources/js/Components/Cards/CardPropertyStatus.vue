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
                <div class="relative w-full px-4 max-w-full flex-grow flex-1 text-right">
                    <button
                        class="bg-indigo-500 text-white active:bg-indigo-600 text-xs font-bold uppercase px-3 py-1 rounded outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150"
                        type="button">
                        See all
                    </button>
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
                            Address
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
                    <tr v-for="property in properties" :key="property.id">
                        <th
                            class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-left">
                            {{ property.name }}
                        </th>
                        <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                            {{ property.address }}
                        </td>
                        <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                            <div class="flex items-center">
                                <i class="fas fa-user mr-2 text-gray-400"></i>
                                {{ property.tenant || 'No tenant' }}
                            </div>
                        </td>
                        <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                            ${{ property.monthlyRent.toFixed(2) }}
                        </td>
                        <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                            <div class="flex items-center">
                                <i class="far fa-calendar mr-2" :class="getContractDateColor(property.contractEnd)"></i>
                                {{ property.contractEnd || 'N/A' }}
                            </div>
                        </td>
                        <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                            <div class="flex flex-col gap-2">
                                <!-- Property Status -->
                                <span :class="getStatusClasses(property.status)">
                                    <i class="fas" :class="getStatusIcon(property.status)"></i>
                                    {{ property.status }}
                                </span>
                                <!-- Payment Status (if applicable)
                  <span v-if="property.paymentStatus" :class="getPaymentStatusClasses(property.paymentStatus)">
                    <i class="fas" :class="getPaymentIcon(property.paymentStatus)"></i>
                    {{ property.paymentStatus }}
                  </span> -->
                            </div>
                        </td>
                        <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                            <div class="flex items-center gap-2">
                                <button class="text-blue-500 hover:text-blue-700">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-gray-500 hover:text-gray-700">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    name: 'PropertiesDashboard',
    data() {
        return {
            properties: [
                {
                    id: 1,
                    name: "Seaside Apartment",
                    address: "123 Ocean Ave, Beach District",
                    tenant: "John Smith",
                    monthlyRent: 1200.00,
                    contractEnd: "2024-12-31",
                    status: "Active",
                    paymentStatus: "Paid"
                },
                {
                    id: 2,
                    name: "Downtown Studio",
                    address: "456 Main St, City Center",
                    tenant: "Mary Johnson",
                    monthlyRent: 800.00,
                    contractEnd: "2024-08-15",
                    status: "Pending Renewal",
                    paymentStatus: "Pending"
                },
                {
                    id: 3,
                    name: "Executive Suite",
                    address: "789 Business Blvd",
                    tenant: null,
                    monthlyRent: 1500.00,
                    contractEnd: null,
                    status: "Available",
                    paymentStatus: null
                },
                {
                    id: 4,
                    name: "Urban Loft",
                    address: "101 Downtown St, City Center",
                    tenant: "David Brown",
                    monthlyRent: 950.00,
                    contractEnd: "2025-01-10",
                    status: "Active",
                    paymentStatus: "Paid"
                },
                {
                    id: 5,
                    name: "Suburban House",
                    address: "204 Maple Ln, Suburbia",
                    tenant: "Emily White",
                    monthlyRent: 1100.00,
                    contractEnd: "2024-07-20",
                    status: "Pending Renewal",
                    paymentStatus: "Pending"
                },
                {
                    id: 6,
                    name: "Luxury Villa",
                    address: "9 Palm Ave, Ocean Side",
                    tenant: "Michael Green",
                    monthlyRent: 2000.00,
                    contractEnd: "2025-02-15",
                    status: "Active",
                    paymentStatus: "Paid"
                },
                {
                    id: 7,
                    name: "Penthouse Suite",
                    address: "12 Skyline Dr, Uptown",
                    tenant: "Sophia Black",
                    monthlyRent: 2200.00,
                    contractEnd: "2024-09-05",
                    status: "Pending Renewal",
                    paymentStatus: "Pending"
                },
                {
                    id: 8,
                    name: "Garden Cottage",
                    address: "305 Country Rd, Outskirts",
                    tenant: null,
                    monthlyRent: 750.00,
                    contractEnd: null,
                    status: "Available",
                    paymentStatus: null
                }
            ]
        }
    },
    methods: {
        getStatusClasses(status) {
            const baseClasses = "px-2 py-1 text-xs rounded-full flex items-center gap-1 ";
            switch (status) {
                case "Active":
                    return baseClasses + "bg-green-100 text-green-800";
                case "Available":
                    return baseClasses + "bg-blue-100 text-blue-800";
                case "Pending Renewal":
                    return baseClasses + "bg-yellow-100 text-yellow-800";
                default:
                    return baseClasses + "bg-gray-100 text-gray-800";
            }
        },
        getStatusIcon(status) {
            switch (status) {
                case "Active":
                    return "fa-check-circle";
                case "Available":
                    return "fa-home";
                case "Pending Renewal":
                    return "fa-clock";
                default:
                    return "fa-info-circle";
            }
        },
        getContractDateColor(date) {
            if (!date) return "text-gray-400";
            const daysUntilEnd = Math.ceil((new Date(date) - new Date()) / (1000 * 60 * 60 * 24));
            if (daysUntilEnd < 30) return "text-red-500";
            if (daysUntilEnd < 90) return "text-yellow-500";
            return "text-green-500";
        }
    }
}
</script>
