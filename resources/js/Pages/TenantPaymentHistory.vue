<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import CustomButton from '@/Components/CustomButton.vue';

const user = usePage().props.auth.user;
const payments = ref([])
const selectedPayment = ref(null)
const showAllPayments = ref(false); // Estado para controlar la vista de pagos
  // Simulating an API to fetch tenant payments
  const getTenantPayments = async () => {
     try{
      const response = await axios.get('/api/payment-history/get/tenant', {
       params: {
         user_id: user.id
       }
     })
      payments.value = response.data
     }catch(error){
       console.error('Error:', error)
     }
  }
  

// Computed properties para ordenar y agrupar los pagos
const sortedPayments = computed(() => {
  return [...payments.value].sort((a, b) => new Date(b.payment_date) - new Date(a.payment_date));
});

const paymentsThisMonth = computed(() => {
  const now = new Date();
  const currentMonth = now.getMonth();
  const currentYear = now.getFullYear();
  return sortedPayments.value.filter(payment => {
    const paymentDate = new Date(payment.payment_date);
    return paymentDate.getMonth() === currentMonth && paymentDate.getFullYear() === currentYear;
  });
});

const monthlyPayments = computed(() => {
  const monthlyTotals = {};
  payments.value.forEach(payment => {
    const date = new Date(payment.payment_date);
    const monthYear = date.toLocaleString('en-US', { month: 'long', year: 'numeric' });
    monthlyTotals[monthYear] = (monthlyTotals[monthYear] || 0) + parseFloat(payment.amount_paid);
  });
  return monthlyTotals;
});

const totalPaid = computed(() => {
  return payments.value.reduce((total, payment) => total + parseFloat(payment.amount_paid), 0);
});

const openPaymentDetails = (payment) => {
  selectedPayment.value = payment;
};

// Función para formatear fechas
const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

onMounted(() => {
  getTenantPayments();
});
</script>
<template>
    <Head title="Payment History" />
    <DashboardLayout>
    <div class="min-h-screen p-6">
      <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl font-bold mb-8 text-gray-800 border-b pb-4">Payment History</h1>
        
        <!-- Financial Summary -->
        <div class="bg-white rounded-lg p-6 shadow-lg mb-8">
          <h2 class="text-2xl font-semibold mb-4 text-gray-700">Financial Summary</h2>
          <div class="space-y-2">
            <div v-for="(amount, month) in monthlyPayments" :key="month" class="flex justify-between items-center">
              <span class="text-gray-600">{{ month }}:</span>
              <span class="font-semibold">${{ amount.toFixed(2) }}</span>
            </div>
            <div class="border-t pt-2 mt-2 flex justify-between items-center">
              <span class="text-lg font-bold text-gray-700">Total Paid:</span>
              <span class="text-2xl font-bold text-green-600">${{ totalPaid.toFixed(2) }}</span>
            </div>
          </div>
        </div>
  
        <!-- Payment List -->
        <div class="bg-white rounded-lg p-6 shadow-lg">
          <h2 class="text-2xl font-semibold mb-6 text-gray-700 flex items-center justify-between">
            <span>{{ showAllPayments ? 'All Payments' : 'Payments This Month' }}</span>
            <span class="bg-green-500 text-white text-sm px-3 py-1 rounded-full">
              {{ showAllPayments ? payments.length : paymentsThisMonth.length }} payments
            </span>
          </h2>
          
          <div v-if="paymentsThisMonth.length < 0" class="space-y-4">
            <p class="text-gray-700">No payments this month.</p>
          </div>
          <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="payment in (showAllPayments ? sortedPayments : paymentsThisMonth)"
              :key="payment.id"
              class="bg-green-50 rounded-lg p-4 shadow-md transition-all duration-200 hover:shadow-lg"
            >
              <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-green-600">Invoice Paid #{{ payment.id }}</h3>
                <span class="text-sm text-gray-500">{{ formatDate(payment.payment_date) }}</span>
              </div>
              <p class="mt-2 text-gray-700"><strong>Amount:</strong> ${{ payment.amount_paid }}</p>
              <button 
                @click="openPaymentDetails(payment)"
                class="mt-3 px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition-colors duration-200 text-sm font-medium"
              >
                Show details
              </button>
            </div>
          </div>
          <CustomButton 
            @click="showAllPayments = !showAllPayments"
            class="mb-2 mt-4 px-4 py-2 text-gray-900 roundedtransition-colors duration-200 text-lg font-medium"
          >
            {{ showAllPayments ? 'Only this month' : 'Show All Payments' }}
          </CustomButton>
        </div>
      </div>
  
      <!-- Payment Details Modal -->
      <div v-if="selectedPayment" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full">
          <h2 class="text-2xl font-bold mb-4 text-gray-800 border-b pb-2">Payment Details</h2>
          <p class="mb-2"><strong class="text-gray-700">ID:</strong> {{ selectedPayment.id }}</p>
          <p class="mb-2"><strong class="text-gray-700">Invoice:</strong> {{ selectedPayment.invoice_id }}</p>
          <p class="mb-2"><strong class="text-gray-700">Payment Date:</strong> {{ formatDate(selectedPayment.payment_date) }}</p>
          <p class="mb-2"><strong class="text-gray-700">Amount:</strong> ${{ selectedPayment.amount_paid }}</p>
          <p class="mb-2"><strong class="text-gray-700">Created:</strong> {{ formatDate(selectedPayment.created_at) }}</p>
          <button 
            @click="selectedPayment = null" 
            class="mt-6 px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition-colors duration-200 w-full"
          >
            Close
          </button>
        </div>
      </div>
    </div>
</DashboardLayout>
  </template>