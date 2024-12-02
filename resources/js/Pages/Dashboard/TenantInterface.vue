<template>
    <div class="mx-auto px-6 py-8">
        <!-- Header -->
        <div class="bg-primary text-white p-6 rounded-lg shadow-lg mb-8 relative">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-bold">Payment Summary</h1>
                    <p class="text-sm text-gray-200 mt-1">Check your payment status and history</p>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
                <!-- Total Paid Card -->
                <div class="bg-white p-5 rounded-lg shadow-md flex items-center">
                    <div class="flex-shrink-0 bg-green-500 text-white p-3 rounded-full">
                        <i class="mdi mdi-check text-3xl"></i>
                    </div>
                    <div class="ml-4">
                        <h5 class="text-gray-600 font-semibold text-sm uppercase">Total Paid</h5>
                        <p class="text-gray-900 text-2xl font-bold">${{ totalPaid.toFixed(2) }}</p>
                    </div>
                </div>

                <!-- Total Pending Card -->
                <div class="bg-white p-5 rounded-lg shadow-md flex items-center">
                    <div class="flex-shrink-0 bg-yellow-500 text-white p-3 rounded-full">
                        <i class="mdi mdi-alert-circle text-3xl"></i>
                    </div>
                    <div class="ml-4">
                        <h5 class="text-gray-600 font-semibold text-sm uppercase">Total Pending</h5>
                        <p class="text-gray-900 text-2xl font-bold">${{ totalPending.toFixed(2) }}</p>
                    </div>
                </div>

                <!-- Next Payment Card -->
                <div class="bg-white p-5 rounded-lg shadow-md flex flex-col">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 text-white p-3 rounded-full">
                            <i class="mdi mdi-calendar-check text-3xl"></i>
                        </div>
                        <div class="ml-4">
                            <h5 class="text-gray-600 font-semibold text-sm uppercase">Next Payment</h5>
                            <p class="text-gray-900 text-2xl font-bold">
                                ${{ nextPayment?.amount?.toFixed(2) || '0.00' }}
                            </p>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mt-3">Date: {{ nextPaymentDate ? formatDate(nextPaymentDate) :
                        'Noscheduled payments' }}</p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div v-if="isLoading" class="text-center text-gray-600">
            Loading...
        </div>
        <div v-else class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <!-- Payment History -->
            <div class="col-span-2 bg-white rounded-lg shadow-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Payment Status</h3>
                    <button
                        class="bg-green-800 text-white text-xs font-bold uppercase px-4 py-2 rounded hover:bg-primary transition">
                        See All
                    </button>
                </div>

                <!-- Filtro por Status -->
                <div class="mb-4">
                    <select v-model="filters.paymentStatus"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">All Status</option>
                        <option value="Paid">Paid</option>
                        <option value="Pending">Pending</option>
                        <option value="Overdue">Overdue</option>
                    </select>
                </div>

                <!-- Tabla -->
                <div class="overflow-x-auto">
                    <table class="w-full table-auto border-collapse text-left">
                        <thead class="bg-green-100 text-green-800">
                            <tr>
                                <th class="px-4 py-3 text-sm font-semibold border-b">Payment Date</th>
                                <th class="px-4 py-3 text-sm font-semibold border-b">Amount</th>
                                <th class="px-4 py-3 text-sm font-semibold border-b">Status</th>
                                <th class="px-4 py-3 text-sm font-semibold border-b">Contract ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="payment in filteredPayments" :key="payment.id" class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700">{{ payment.invoice_date }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">${{ payment.invoice_total }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span :class="getStatusClasses(payment.payment_status)">
                                        <i class="mdi" :class="getStatusIcon(payment.payment_status)"></i>
                                        {{ payment.payment_status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ payment.contract_id }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6 flex justify-between items-center">
                    <button @click="previousPage" :disabled="currentPage <= 1"
                        class="bg-primary text-white px-4 py-2 rounded disabled:opacity-50 hover:bg-green-800">
                        Previous
                    </button>
                    <span class="text-gray-700">Page {{ currentPage }} of {{ totalPages }}</span>
                    <button @click="nextPage" :disabled="currentPage >= totalPages"
                        class="bg-primary text-white px-4 py-2 rounded disabled:opacity-50 hover:bg-green-800">
                        Next
                    </button>
                </div>
            </div>


            <!-- Sidebar -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <!-- Rented Property -->
                <div class="mb-6 rounded-md shadow-md">
                    <h3 class="text-lg font-semibold text-gray-700 mb-3">Rented Property</h3>
                    <p class="text-sm text-gray-600">{{ property.property_address || 'No address available' }}</p>
                </div>

                <!-- Payment Calendar -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-3">Payment Calendar</h3>
                    <div class="flex justify-center">
                        <v-calendar class="w-full h-96" :attributes="calendarAttributes" :first-day-of-week="2"
                            @dayclick="onDayClick" :timezone="timezone">
                            <template #date="{ date }">
                                <span class="text-sm">{{ formatDate(date) }}</span>
                            </template>
                        </v-calendar>
                    </div>
                </div>
            </div>
        </div>
        <!-- Contratos y Comentarios organizados -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-5">
            <!-- Contract Table -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-700 mb-4 flex justify-between">Contracts</h3>
                <div class="overflow-x-auto">
                    <table class="w-full table-auto text-left">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-sm font-medium text-gray-500">Contract ID</th>
                                <th class="px-4 py-2 text-sm font-medium text-gray-500">Property</th>
                                <th class="px-4 py-2 text-sm font-medium text-gray-500">Status</th>
                                <th class="px-4 py-2 text-sm font-medium text-gray-500">Start Date</th>
                                <th class="px-4 py-2 text-sm font-medium text-gray-500">End Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="contract in paginatedContracts" :key="contract.id" class="hover:bg-gray-100">
                                <td class="px-4 py-2 text-sm text-gray-700">{{ contract.id }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ contract.property.city }}</td>
                                <td class="px-4 py-2 text-sm">
                                    <span :class="getStatusClasses(contract.status)">
                                        <i class="mdi" :class="getStatusIcon(contract.status)"></i>
                                        {{ contract.status }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ formatDate(contract.start_date) }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ formatDate(contract.end_date) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination Controls -->
                <div class="mt-4 flex justify-between items-center">
                    <button @click="previousContractPage" :disabled="currentContractPage <= 1"
                        class="bg-primary text-white px-4 py-2 rounded disabled:opacity-50">
                        Previous
                    </button>
                    <span>Page {{ currentContractPage }} of {{ totalContractPages }}</span>
                    <button @click="nextContractPage" :disabled="currentContractPage >= totalContractPages"
                        class="bg-primary text-white px-4 py-2 rounded disabled:opacity-50">
                        Next
                    </button>
                </div>
            </div>
            <!-- Contratos y Comentarios organizados -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-5">
                <!-- Contract Table -->
                <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4 flex justify-between">Contracts</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto text-left">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-sm font-medium text-gray-500">Contract ID</th>
                                    <th class="px-4 py-2 text-sm font-medium text-gray-500">Property</th>
                                    <th class="px-4 py-2 text-sm font-medium text-gray-500">Status</th>
                                    <th class="px-4 py-2 text-sm font-medium text-gray-500">Start Date</th>
                                    <th class="px-4 py-2 text-sm font-medium text-gray-500">End Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="contract in paginatedContracts" :key="contract.id" class="hover:bg-gray-100">
                                    <td class="px-4 py-2 text-sm text-gray-700">{{ contract.id }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-700">{{ contract.property }}</td>
                                    <td class="px-4 py-2 text-sm">
                                        <span :class="getStatusClasses(contract.status)">
                                            <i class="mdi" :class="getStatusIcon(contract.status)"></i>
                                            {{ contract.status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-700">{{ formatDate(contract.startDate) }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-700">{{ formatDate(contract.endDate) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination Controls -->
                    <div class="mt-4 flex justify-between items-center">
                        <button @click="previousContractPage" :disabled="currentContractPage <= 1"
                            class="bg-primary text-white px-4 py-2 rounded disabled:opacity-50">
                            Previous
                        </button>
                        <span>Page {{ currentContractPage }} of {{ totalContractPages }}</span>
                        <button @click="nextContractPage" :disabled="currentContractPage >= totalContractPages"
                            class="bg-primary text-white px-4 py-2 rounded disabled:opacity-50">
                            Next
                        </button>
                    </div>
                </div>

                <!-- Comments section-->
                <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Comments</h3>

                    <!-- Input para agregar comentario -->
                    <div class="flex items-center space-x-4 mb-6">
                        <input type="text"
                            class="flex-1 border border-gray-300 rounded-lg p-2 focus:ring focus:ring-green-500 focus:outline-none"
                            placeholder="Leave a comment..." v-model="newComment">
                        <div class="flex items-center space-x-1">
                            <button v-for="n in 5" :key="n" @click="setRating(n)"
                                :class="n <= rating ? 'text-yellow-500' : 'text-gray-400 hover:text-yellow-500'">
                                <icon :class="n <= rating ? 'mdi mdi-star' : 'mdi mdi-star-outline'"></icon>
                            </button>
                        </div>
                        <button
                            class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 disabled:opacity-50"
                            @click="addComment" :disabled="!newComment || !rating">
                            <i class="mdi mdi-send"></i>
                        </button>
                    </div>

    <!-- Comments section-->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
    <h3 class="text-lg font-semibold text-gray-700 mb-4">Comments</h3>

    <!-- Input para agregar comentario -->
    <div v-if="property.property_id">
        <div class="flex items-center space-x-4 mb-6">
            <input 
                type="text" 
                class="flex-1 border border-gray-300 rounded-lg p-2 focus:ring focus:ring-green-500 focus:outline-none" 
                placeholder="Leave a comment..." 
                v-model="newComment"
            >
            <div class="flex items-center space-x-1">
                <button 
                    v-for="n in 5" 
                    :key="n" 
                    @click="setRating(n)" 
                    :class="n <= rating ? 'text-yellow-500' : 'text-gray-400 hover:text-yellow-500'"
                >
                    <icon :class="n <= rating ? 'mdi mdi-star' : 'mdi mdi-star-outline'"></icon>
                </button>
            </div>
            <button 
                class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 disabled:opacity-50" 
                @click="addComment"
                :disabled="!newComment || !rating"
            >
                <i class="mdi mdi-send"></i>
            </button>
        </div>

        <!-- Contenedor de comentarios -->
        <div class="space-y-4 overflow-y-auto max-h-60">
            <div 
                v-for="comment in comments" 
                :key="comment.id" 
                class="bg-gray-50 border border-gray-200 rounded-lg p-4 shadow-sm"
            >
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-gray-500">{{ new Date(comment.created_at).toLocaleDateString() }}</span>
                <div class="flex">
                    <icon 
                        v-for="n in 5" 
                        :key="n"
                        :class="n <= comment.comment_rate ? 'mdi mdi-star' : 'mdi mdi-star-outline'" 
                        class="text-yellow-500 text-lg"
                    ></icon>
                </div>
                </div>
                <div class="mb-2">
                    <p class="text-gray-700 font-medium">{{ comment.first_name }} {{ comment.last_name }}</p>
                </div>
                <p class="text-gray-600 whitespace-normal break-words">{{ comment.comment }}</p>
            </div>
        </div>
    </div>
    <div v-else>
        <p class="text-gray-500">You need to be tenant of a property to leave a comment</p>
    </div>
</div>

</div>
    </div>
</template>



<script>

import { format, parseISO, isValid } from 'date-fns';
import { es } from 'date-fns/locale';
import 'v-calendar/dist/style.css';
import VCalendar from 'v-calendar';

import axios from 'axios';
import NotificationDropdown from '../../Components/Dropdowns/NotificationDropdown.vue';


export default {
    name: 'PaymentHistory',
    props: {
        auth: { type: Object, required: true, default: () => ({}) },
        initialPayments: {
            type: Array,
            default: () => []
        },
        color: {
            type: String,
            default: "light",
            validator(value) {
                return ["light", "dark"].indexOf(value) !== -1;
            },
        },
    },
    components: {
        NotificationDropdown
    },
    data() {
        return {
            newComment: '',
            rating: 0,
            comments: [],
            currentPage: 1,
            rowsPerPage: 10,
            selectedPayment: null,
            paymentsData: [],
            currentDate: new Date(),
            property: [],
            currentContractPage: 1,
            rowsPerContractPage: 5,
            contractsData: [],
            isLoading: false,
            filters: {
                paymentStatus: '', // Filtro por estado
            },
            timezone: 'UTC',
        }
    },
    computed: {
        paginatedPayments() {
            const startIndex = (this.currentPage - 1) * this.rowsPerPage;
            return this.paymentsData.slice(startIndex, startIndex + this.rowsPerPage);
        },

        filteredPayments() {
            return this.paymentsData
                .filter(payment =>
                    !this.filters.paymentStatus || payment.payment_status === this.filters.paymentStatus
                )
                .slice((this.currentPage - 1) * this.rowsPerPage, this.currentPage * this.rowsPerPage);
        },

        totalPages() {
            return Math.ceil(this.paymentsData.length / this.rowsPerPage);
        },

        nextPayment() {

            if (!Array.isArray(this.paymentsData) || this.paymentsData.length === 0) {
                return null;
            }
            const pendingPayments = this.paymentsData
                .filter(payment => payment.payment_status === "Pending")
                .sort((a, b) => parseISO(b.invoice_date) - parseISO(a.invoice_date));

            if (pendingPayments.length > 0) {
                const formattedDate = format(parseISO(pendingPayments[0].invoice_date), 'yyyy-MM-dd', { locale: es });

                return {
                    amount: parseFloat(pendingPayments[0].invoice_total),
                    date: formattedDate,
                };
            }

            return null;
        },

        nextPaymentDate() {
            return this.nextPayment ? this.nextPayment.date : null;
        },

        calendarAttributes() {
            const nextPaymentDate = this.nextPaymentDate;

            // Ordenar los pagos por la fecha de la factura en orden descendente
            const latestPayment = this.paymentsData
                .map(payment => {
                    const paymentDate = parseISO(payment.invoice_date);
                    const adjustedDate = nextPaymentDate
                        ? (nextPaymentDate ? parseISO(nextPaymentDate) : null)
                        : paymentDate;

                    if (!adjustedDate) {
                        return null;
                    }
                    const formattedDate = format(adjustedDate, 'yyyy-MM-dd', { locale: es });
                    return {
                        dates: formattedDate,
                        highlight: payment.payment_status === 'Paid' ? 'green' : payment.payment_status === 'Pending' ? 'yellow' : 'red',
                        popover: {
                            label: `$${payment.invoice_total} - ${payment.payment_status}`,
                        },


                    };
                })
                .filter(item => item !== null) // Filtra elementos nulos
                .sort((a, b) => new Date(b.dates) - new Date(a.dates)); // Ordena por la fecha, de más reciente a más antiguo

            // Si hay pagos, retornar solo el más reciente
            if (latestPayment.length > 0) {
                return [latestPayment[0]]; // Solo mostrar el primer pago (el más reciente)
            }

            return []; // Si no hay pagos, retornar un arreglo vacío
        },

        totalPaid() {
            return this.paymentsData
                .filter(payment => payment.payment_status === 'Paid') // Cambié 'status' a 'payment_status'
                .reduce((total, payment) => total + parseFloat(payment.invoice_total), 0);
        },

        // Total Pendiente (Pending)
        totalPending() {
            return this.paymentsData
                .filter(payment => payment.payment_status === 'Pending') // Cambié 'status' a 'payment_status'
                .reduce((total, payment) => total + parseFloat(payment.invoice_total), 0);
        },
        totalOverdue() {
            return this.paymentsData
                .filter(payment => payment.status === 'Overdue')
                .reduce((sum, payment) => sum + parseFloat(payment.invoice_total), 0);
        },

        projectedIncome() {
            return this.totalPaid + this.totalPending;
        },

        paginatedContracts() {
            const startIndex = (this.currentContractPage - 1) * this.rowsPerContractPage;
            return this.contractsData.slice(startIndex, startIndex + this.rowsPerContractPage);
        },
        totalContractPages() {
            return Math.ceil(this.contractsData.length / this.rowsPerContractPage);
        },
    },
    methods: {
        getComments() {
            axios.get(`/api/comments/${this.auth.user.id}`)
                .then(response => {
                    this.comments = response.data;
                })
                .catch(error => {
                    console.error('Error fetching comments:', error);
                });
        },
        setRating(star) {
            this.rating = star;
        },
        addComment() {
            if (this.newComment && this.rating > 0) {
                const newComment = {
                    comment: this.newComment,
                    rating: this.rating,
                    property_id: this.property.property_id,
                    user_id: this.auth.user.id,
                };

                axios.post('/api/comments', newComment)
                    .then(response => {
                        this.comments = response.data.reverse();
                        this.newComment = '';
                        this.rating = 0;

                    })
                    .catch(error => {
                        console.error('Error adding comment:', error);
                    });
            }
        },
        formatDate(dateString) {
            // Si la fecha ya viene ajustada, solo formatea
            const date = parseISO(dateString);
            return format(date, 'dd MMM yyyy', { locale: es });
        },
        getStatusColor(status) {
            // Lógica para devolver el color según el estado
            if (status === "active") return "green";
            if (status === "inactive") return "red";
            return "gray";
        },
        getStatusClasses(status) {
            switch (status) {
                // Estatus de pagos
                case 'Paid':
                    return 'bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs';
                case 'Pending':
                    return 'bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs';
                case 'Overdue':
                    return 'bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs';

                // Estatus de contratos
                case 'Active':
                    return 'bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs';
                case 'Pending Renewal':
                    return 'bg-orange-100 text-orange-800 px-2 py-1 rounded-full text-xs';
                case 'Terminate':
                    return 'bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs';

                default:
                    return 'bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs';
            }
        },
        getStatusIcon(status) {
            switch (status) {
                // Estatus de pagos
                case 'Paid':
                    return 'mdi-check-circle';
                case 'Pending':
                    return 'mdi-alert-circle';
                case 'Overdue':
                    return 'mdi-alert';

                // Estatus de contratos
                case 'Active':
                    return 'mdi-check';
                case 'Pending Renewal':
                    return 'mdi-update';
                case 'Terminate':
                    return 'mdi-cancel';

                default:
                    return 'mdi-help-circle';
            }
        },
        previousPage() {
            if (this.currentPage > 1) this.currentPage--;
        },
        nextPage() {
            if (this.currentPage < this.totalPages) this.currentPage++;
        },
        openViewModal(payment) {
            this.selectedPayment = payment;

        },
        onDayClick(day) {
            const clickedDate = day.date.toISOString().split('T')[0]; // Solo la fecha sin la hora
            const payment = this.paymentsData.find(p =>
                new Date(p.date).toISOString().split('T')[0] === clickedDate
            );
            if (payment) {
                console.log('Payment clicked:', payment);
            }
        },

        async fetchPaymentsData() {
            this.isLoading = true;
            try {
                const userId = this.auth?.user?.id;
                if (!userId) {
                    console.error("User ID is not available");
                    return;
                }

                const response = await axios.get(`/api/payment-history/${userId}`);
                this.paymentsData = response.data;
            } catch (error) {
                console.error("Error fetching payments data:", error);
            } finally {
                this.isLoading = false;
            }

        },

        async fetchProperty() {
            this.isLoading = true;
            try {
                const userId = this.auth?.user?.id;
                if (!userId) {
                    console.error("User ID is not available");
                    return;
                }
                const response = await axios.get(`/api/rented-property/${userId}`);
                this.property = response.data
            } catch (error) {
                console.error("Error al obtener la propiedad rentada:", error);
            }
            finally {
                this.isLoading = false;
            }
        },


        async fetchContractsData() {
            this.isLoading = true;
            try {

                const userId = this.auth?.user?.id;
                if (!userId) {
                    console.error("User ID is not available");
                    return;
                }
                const response = await axios.get(`/api/tenant/contracts/${userId}`);
                this.contractsData = response.data;
                console.log('Contracts:', this.contractsData);
            } catch (error) {
                console.error(error);
            } finally {
                this.isLoading = false;
            }
        },

        previousContractPage() {
            if (this.currentContractPage > 1) this.currentContractPage--;
        },
        nextContractPage() {
            if (this.currentContractPage < this.totalContractPages) this.currentContractPage++;
        },

    },

    mounted() {
        this.fetchPaymentsData();
        this.refreshInterval = setInterval(this.fetchPayments, 30000);
        this.fetchContractsData();
        this.fetchProperty();
        this.getComments();
    },
};
</script>
