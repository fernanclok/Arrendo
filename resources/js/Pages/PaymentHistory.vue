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
                <input type="text" v-model="searchQuery" placeholder="Buscar por ID, fecha de pago o factura"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                <select v-model="selectedOwner"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                    <option value="">Todos los propietarios</option>
                    <option v-for="owner in uniqueOwners" :key="owner" :value="owner">
                        {{ owner }}
                    </option>
                </select>
            </div>

            <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Pagados -->
                <div class="bg-green-100 rounded-lg p-4 shadow-md border-l-4 border-green-600">
                    <h2 class="text-xl font-bold mb-4 flex items-center justify-between">
                        <span>Pagado</span>
                    </h2>
                    <!-- <span class="bg-green-500 text-white text-sm px-2 py-1 rounded-full">
              {{ filteredPagados.length }}
            </span>
          </h2>
          <div
            v-for="payment in filteredPagados"
            :key="payment.id"
            class="bg-white rounded-lg p-4 shadow mb-4 hover:shadow-lg transition-shadow duration-200"
          >
            <div class="flex justify-between items-center">
              <h3 class="text-2xl text-green-600">✔</h3>
              <span class="text-sm text-gray-500">ID: {{ payment.id }}</span>
            </div>
            <p><strong>Factura:</strong> {{ payment.invoice_id }}</p>
            <p><strong>Fecha de pago:</strong> {{ formatDate(payment.payment_date) }}</p>
            <p><strong>Monto:</strong> ${{ payment.amount_paid.toFixed(2) }}</p>
            <p><strong>Propietario:</strong> {{ payment.owner }}</p>
            <p class="text-sm text-gray-500 mt-2">
              Creado: {{ formatDate(payment.created_at) }}
            </p>
            <p class="text-sm text-gray-500">
              Actualizado: {{ formatDate(payment.updated_at) }}
            </p>
          </div> -->
                </div>

                <!-- Pendientes -->
                <div class="bg-yellow-100 rounded-lg p-4 shadow-md border-l-4 border-yellow-600">
                    <h2 class="text-xl font-bold mb-4 flex items-center justify-between">
                        <span>Pendiente</span>
                    </h2>
                    <!-- <span class="bg-yellow-500 text-white text-sm px-2 py-1 rounded-full">
              {{ filteredPendientes.length }}
            </span>
          </h2>
          <div
            v-for="payment in filteredPendientes"
            :key="payment.id"
            class="bg-white rounded-lg p-4 shadow mb-4 hover:shadow-lg transition-shadow duration-200"
          >
            <div class="flex justify-between items-center">
              <h3 class="text-2xl text-yellow-600">⏳</h3>
              <span class="text-sm text-gray-500">ID: {{ payment.id }}</span>
            </div>
            <p><strong>Factura:</strong> {{ payment.invoice_id }}</p>
            <p><strong>Fecha programada:</strong> {{ formatDate(payment.payment_date) }}</p>
            <p><strong>Monto:</strong> ${{ payment.amount_paid.toFixed(2) }}</p>
            <p><strong>Propietario:</strong> {{ payment.owner }}</p>
            <p class="text-sm text-gray-500 mt-2">
              Creado: {{ formatDate(payment.created_at) }}
            </p>
            <p class="text-sm text-gray-500">
              Actualizado: {{ formatDate(payment.updated_at) }}
            </p>
          </div> -->
                </div>

                <!-- Atrasados -->
                <div class="bg-red-100 rounded-lg p-4 shadow-md border-l-4 border-red-600">
                    <h2 class="text-xl font-bold mb-4 flex items-center justify-between">
                        <span>Atrasado</span>
                    </h2>
                    <!-- <span class="bg-red-500 text-white text-sm px-2 py-1 rounded-full">
              {{ filteredAtrasados.length }}
            </span>
          </h2>
          <div
            v-for="payment in filteredAtrasados"
            :key="payment.id"
            class="bg-white rounded-lg p-4 shadow mb-4 hover:shadow-lg transition-shadow duration-200"
          >
            <div class="flex justify-between items-center">
              <h3 class="text-2xl text-red-600">⚠</h3>
              <span class="text-sm text-gray-500">ID: {{ payment.id }}</span>
            </div>
            <p><strong>Factura:</strong> {{ payment.invoice_id }}</p>
            <p><strong>Fecha vencida:</strong> {{ formatDate(payment.payment_date) }}</p>
            <p><strong>Monto:</strong> ${{ payment.amount_paid.toFixed(2) }}</p>
            <p><strong>Propietario:</strong> {{ payment.owner }}</p>
            <p class="text-sm text-gray-500 mt-2">
              Creado: {{ formatDate(payment.created_at) }}
            </p>
            <p class="text-sm text-gray-500">
              Actualizado: {{ formatDate(payment.updated_at) }}
            </p>
          </div> -->
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
            searchQuery: '',
            selectedOwner: '',
        };
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
    },
    mounted() {
        this.fetchPayments();
    },
}
</script>