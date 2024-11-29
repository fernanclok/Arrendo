<template>
    <div class="container mx-auto px-6 py-8">
        <!-- Header -->
        <div class="bg-primary text-white p-6 rounded-lg shadow-lg mb-5">
            <h1 class="text-2xl font-bold">Payment Summary</h1>
            <p class="text-sm text-gray-200">Check your payment status and history</p>

            <!-- Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Total Paid Card -->
                <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
                    <div class="flex-shrink-0 bg-green-500 text-white p-4 rounded-full">
                        <i class="mdi mdi-check text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h5 class="text-gray-500 font-semibold text-sm uppercase">Total Paid</h5>
                        <p class="text-gray-800 text-xl font-bold">${{ totalPaid.toFixed(2) }}</p>
                    </div>
                </div>

                <!-- Total Pending Card -->
                <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
                    <div class="flex-shrink-0 bg-yellow-500 text-white p-4 rounded-full">
                        <i class="mdi mdi-alert-circle text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h5 class="text-gray-500 font-semibold text-sm uppercase">Total Pending</h5>
                        <p class="text-gray-800 text-xl font-bold">
                            ${{ totalPending.toFixed(2) }}</p>
                    </div>
                </div>

                <!-- Next Payment Card -->
                <div class="bg-white p-6 rounded-lg shadow-md flex flex-col">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 text-white p-4 rounded-full">
                            <i class="mdi mdi-calendar-check text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h5 class="text-gray-500 font-semibold text-sm uppercase">Next Payment</h5>
                            <p class="text-gray-800 text-xl font-bold">${{ nextPayment && nextPayment.amount ?
                                nextPayment.amount.toFixed(2) : '0.00' }}</p>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">Date: {{ nextPaymentDate ? formatDate(nextPaymentDate) : 'No scheduled payments' }}</p>
                </div>
            </div>

        </div>

        <div v-if="isLoading">Loading...</div>

        <!-- Main Content -->
        <div v-else class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <!-- Payment History -->
            <div class="col-span-2 bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4 flex justify-between">Payment History <button
                        class="bg-indigo-500 text-white active:bg-indigo-600 text-xs font-bold uppercase px-3 py-1 rounded outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150">
                        See all
                    </button></h3>

                <div class="overflow-x-auto">
                    <table class="w-full table-auto text-left">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-sm font-medium text-gray-500">Payment Date</th>
                                <th class="px-4 py-2 text-sm font-medium text-gray-500">Amount</th>
                                <th class="px-4 py-2 text-sm font-medium text-gray-500">Status</th>
                                <th class="px-4 py-2 text-sm font-medium text-gray-500">Contract ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="payment in paginatedPayments" :key="payment.id" class="hover:bg-gray-100">
                                <td class="px-4 py-2 text-sm text-gray-700">{{ payment?.invoice_date }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700">${{ payment.invoice_total }}</td>
                                <td class="px-4 py-2 text-sm">
                                    <span :class="getStatusClasses(payment.payment_status)">
                                        <i class="mdi" :class="getStatusIcon(payment.payment_status)"></i>
                                        {{ payment.payment_status }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ payment.contract_id }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination Controls -->
                <div class="mt-4 flex justify-between items-center">
                    <button @click="previousPage" :disabled="currentPage <= 1"
                        class="bg-primary text-white px-4 py-2 rounded disabled:opacity-50">
                        Previous
                    </button>
                    <span>Page {{ currentPage }} of {{ totalPages }}</span>
                    <button @click="nextPage" :disabled="currentPage >= totalPages"
                        class="bg-primary text-white px-4 py-2 rounded disabled:opacity-50">
                        Next
                    </button>
                </div>
            </div>

            <!-- Sidebar -->
            <div class=" bg-white ba-white rounded-lg shadow-md p-6">
                <!-- Notifications -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Rented Property</h3>
                    <ul>
                        <li class="text-sm text-gray-600 mb-2">
                            {{ property.property_address }}
                        </li>
                    </ul>
                </div>

                <!-- Calendar -->
                <div class="bg-white rounded-lg shadow-lg p-6 mt-5">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Payment Calendar</h3>
                    <v-calendar :attributes="calendarAttributes" :first-day-of-week="2" @dayclick="onDayClick">
                        <template #date="{ date }">
                            <span class="text-sm">{{ formatDate(date) }}</span>
                        </template>
                    </v-calendar>
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
                        <td class="px-4 py-2 text-sm text-gray-700">{{ contract.property }}</td>
                        <td class="px-4 py-2 text-sm">
                            <span :class="getStatusClasses(contract.status)">
                                <i class="mdi" :class="getStatusIcon(contract.status)"></i>
                                {{ contract.status }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ formatDate(contract.startDate) }}</td>
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

</div>
    </div>
</template>



<script>

import { format, parseISO } from 'date-fns';
import { es } from 'date-fns/locale';
import 'v-calendar/dist/style.css';
import VCalendar from 'v-calendar';

import axios from 'axios';


export default {
    name: 'PaymentHistory',
    props: {
        auth: { type: Object, required: true, default: () => [] },
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

        }
    },
    computed: {
        paginatedPayments() {
            const startIndex = (this.currentPage - 1) * this.rowsPerPage;
            return this.paymentsData.slice(startIndex, startIndex + this.rowsPerPage);
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
                const formattedDate = format(parseISO(pendingPayments[0].invoice_date), 'yyyy-MM-dd');

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
                    const formattedDate = format(adjustedDate, 'yyyy-MM-dd');

                    return {
                        dates: formattedDate,
                        highlight: 'green',
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
            axios.get(`/api/comments/2`)
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
                    property_id : this.property.property_id,
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
        formatDate(date) {
            return format(new Date(date), 'dd/MM/yyyy', { locale: es });
        },
        getStatusColor(status) {
            // Lógica para devolver el color según el estado
            if (status === "active") return "green";
            if (status === "inactive") return "red";
            return "gray";
        },
        getStatusClasses(status) {
            return {
                Paid: "text-green-500",
                Pending: "text-yellow-500",
                Overdue: "text-red-500",
            }[status] || "text-gray-500";
        },
        getStatusIcon(status) {
            return {
                Paid: "mdi-check-circle",
                Pending: "mdi-clock",
                Overdue: "mdi-alert",
            }[status] || "mdi-information";
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


        async fetchContracts() {
            try {
                const response = await axios.get('/api/contracts', {
                    params: {
                        user_id: this.auth.user.id,
                    }
                });
                this.contractsData = response.data;
            } catch (error) {
                console.error('Error fetching contracts:', error);
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
        this.fetchContracts();
        this.fetchProperty();
        this.getComments();
    },
};
</script>
