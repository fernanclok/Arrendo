<template>
    <div class="relative flex flex-col min-w-0 break-words bg-white w-full mb-6 shadow-lg rounded">
        <div class="rounded-t mb-0 px-4 py-3 border-0">
            <div class="flex flex-wrap items-center">
                <div class="relative w-full px-4 max-w-full flex-grow flex-1">
                    <h3 class="font-semibold text-base text-blueGray-700">
                        Maintenance Requests
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
        <div class="block w-full overflow-x-auto">
            <!-- Projects table -->
            <table class="items-center w-full bg-transparent border-collapse">
                <thead class="thead-light">
                    <tr>
                        <th
                            class="px-6 bg-blueGray-50 text-blueGray-500 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                            Property
                        </th>
                        <th
                            class="px-6 bg-blueGray-50 text-blueGray-500 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                            Date
                        </th>
                        <th
                            class="px-6 bg-blueGray-50 text-blueGray-500 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                            Priority
                        </th>
                        <th
                            class="px-6 bg-blueGray-50 text-blueGray-500 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left min-w-140-px">
                            Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="request in maintenanceRequests" :key="request.property">
                        <th
                            class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-left">
                            {{ request.id }}
                        </th>
                        <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                            {{ request.report_date }}
                        </td>
                        <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                            {{ request.priority }}
                        </td>
                        <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                            <span v-if="request.status" :class="getPaymentStatusClasses(request.status)">
                                <i class="fas" :class="getPaymentIcon(request.status)"></i>
                                {{ request.status }}
                            </span>
                        </td>
                    </tr>
                </tbody>

            </table>
        </div>
    </div>
</template>


<script>
export default {
        props: {
            maintenanceRequests: Array,
        },

    methods: {
        getPaymentStatusClasses(status) {
            const baseClasses = "px-2 py-1 text-xs rounded-full flex items-center gap-1 ";
            switch (status) {
                case "Resolved":
                    return baseClasses + "bg-green-100 text-green-800";
                case "In Progress":
                    return baseClasses + "bg-yellow-100 text-yellow-800";
                case "Pending":
                    return baseClasses + "bg-orange-100 text-orange-800";
                default:
                    return baseClasses + "bg-gray-100 text-gray-800";
            }
        },

        getPaymentIcon(status) {
            switch (status) {
                case "Resolved":
                    return "fa-check-circle";
                case "In Progress":
                    return "fa-spinner fa-spin";
                case "Pending":
                    return "fa-hourglass-half";
                default:
                    return "";
            }
        }
    }
};
</script>
