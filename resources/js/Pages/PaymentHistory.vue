<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
</script>

<template>

    <Head title="Payment History" />

    <DashboardLayout>
        <div class="min-h-screen p-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Payment History</h1>
            <!-- Filtros de búsqueda -->
            <div class="max-w-6xl mx-auto mb-6 space-y-4">
              <div class="block">
                <label for="search-query">Search by ID or Date:</label>
                <input type="text" v-model="searchQuery" placeholder="Buscar por ID, fecha de pago o factura"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
            </div>
                    <select v-model="selectedTenant"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                    <option value="">All tenats</option>
                    <option v-for="tenant in tenants" :key="tenant.id" :value="tenant.id">
                        {{ tenant.first_name }} {{ tenant.last_name }}
                    </option>
                </select>
            </div>

            <div class="max-w-6xl mx-auto flex justify-between md:grid-cols-3 gap-6">
                <!-- Pagados -->
                <div class="bg-green-100 rounded-lg p-4 shadow-md w-full border-l-4 border-green-600 ">
                    <h2 class="text-xl font-bold mb-4 flex items-center justify-between">
                        <span>Paid</span>
                    <span class="bg-green-500 text-white text-sm px-2 py-1 rounded-full">
                        {{ filteredPayments.length }}
                      </span>
                    </h2>
                      <div class="h-[500px] overflow-y-scroll">
                          <div
                          v-for="payment in filteredPayments"
                          :key="payment.id"
                          class="bg-white rounded-lg p-4 shadow mb-4 hover:shadow-lg transition-shadow duration-200 "
                        >
                          <div class="flex justify-between items-center">
                            <h3 class="text-2xl text-green-600">✔</h3>
                            <span class="text-sm text-gray-500">No. {{ payment.id }}</span>
                          </div>
                          <p><strong>Invoice:</strong> {{ payment.invoice_id }}</p>
                          <p><strong>Paidd Date</strong> {{ formatDate(payment.payment_date) }}</p>
                          <p><strong>Amount:</strong> ${{ payment.amount_paid }}</p>
                          <p><strong>Owner:</strong> {{ payment.owner }}</p>
                          <p class="text-sm text-gray-500 mt-2">
                            Created: {{ formatDate(payment.created_at) }}
                          </p>
                        </div>
                      </div>  
                </div>

                <!-- Atrasados -->
                <div class="bg-red-100 rounded-lg p-4 shadow-md border-l-4 border-red-600 w-full">
                    <h2 class="text-xl font-bold mb-4 flex items-center justify-between">
                        <span>Atrasado</span>
                          <span class="bg-red-500 text-white text-sm px-2 py-1 rounded-full">
                    {{ filteredUnpaidPayments.length }}
                  </span>
                </h2>
                <div class="h-[500px] overflow-y-scroll">
                <div
                  v-for="payment in filteredUnpaidPayments"
                  :key="payment.id"
                  class="bg-white rounded-lg p-4 shadow mb-4 hover:shadow-lg transition-shadow duration-200"
                >
                  <div class="flex justify-between items-center">
                    <h3 class="text-2xl text-red-600">⚠</h3>
                    <span class="text-sm text-gray-500">No: {{ payment.id }}</span>
                  </div>
                  <p><strong>Invoice:</strong> {{ payment.id }}</p>
                  <p><strong>End Date:</strong> {{ formatDate(payment.issue_date) }}</p>
                  <p><strong>Amount:</strong> ${{ payment.total_amount }}</p>
                  <p><strong>Owner:</strong> {{ payment.owner }}</p>
                  <p class="text-sm text-gray-500 mt-2">
                    Created: {{ formatDate(payment.created_at) }}
                  </p>
                </div>
                </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>

<script>
import axios from 'axios';

export default {
    props: ['user'],
    data() {
        return {
            payments: [],
            tenants: [],
            unPaidPayments: [],
            searchQuery: '',
            selectedTenant: '',
        };
    },
    computed: {
      filteredPayments() {
        return this.payments.filter((payment) => {
            const invoiceId = payment.invoice_id.toString();
            const paymentDate = payment.payment_date;
            return invoiceId.includes(this.searchQuery) || paymentDate.includes(this.searchQuery);
        });
    },
    filteredUnpaidPayments() {
            return this.unPaidPayments.filter((payment) => {
                const invoiceId = payment.id.toString();
                const issueDate = payment.issue_date;
                const matchesSearchQuery = invoiceId.includes(this.searchQuery) || issueDate.includes(this.searchQuery);
                return matchesSearchQuery;
            });
        },
  },
    methods: {
        fetchPayments() {
            axios.get('/api/payment-history', {
                params: {
                    user_id: this.user.id,
                },
            }).then((response) => {
                this.payments = response.data;
                console.log(this.payments);
            });
        },
        formatDate(date) {
            return new Date(date).toLocaleDateString();
        },
        fetchTenants() {
            axios.get('/api/payment-history/get/tenants-history',{
                params: {
                    user_id: this.user.id,
                },
            }).then((response) => {
                this.tenants = response.data;
                console.log(this.tenants);
            });
        },
        fetchUnpaidPayments() {
            axios.get('/api/payment-history/get/unpaid-invoices', {
                params: {
                    user_id: this.user.id,
                },
            }).then((response) => {
                this.unPaidPayments = response.data;
                console.log(this.unPaidPayments);
            });
        },
    },
    mounted() {
        this.fetchPayments();
        this.fetchTenants();
        this.fetchUnpaidPayments();
    },
}
</script>