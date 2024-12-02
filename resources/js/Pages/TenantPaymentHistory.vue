<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head} from '@inertiajs/vue3';
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
            <span>Payment History</span>
            <span class="bg-green-500 text-white text-sm px-3 py-1 rounded-full">
              {{ payments.length }} payments
            </span>
          </h2>
          <div class="space-y-4">
            <div
              v-for="payment in sortedPayments"
              :key="payment.id"
              class="bg-green-50 rounded-lg p-4 shadow-md transition-all duration-200 hover:shadow-lg"
            >
              <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-green-600">Payment #{{ payment.id }}</h3>
                <span class="text-sm text-gray-500">{{ formatDate(payment.payment_date) }}</span>
              </div>
              <p class="mt-2 text-gray-700"><strong>Amount:</strong> ${{ payment.amount_paid.toFixed(2) }}</p>
              <button 
                @click="openPaymentDetails(payment)"
                class="mt-3 px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition-colors duration-200 text-sm font-medium"
              >
                Show details
              </button>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Payment Details Modal -->
      <div v-if="selectedPayment" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full">
          <h2 class="text-2xl font-bold mb-4 text-gray-800 border-b pb-2">Payment Details</h2>
          <p class="mb-2"><strong class="text-gray-700">ID:</strong> {{ selectedPayment.id }}</p>
          <p class="mb-2"><strong class="text-gray-700">Invoice:</strong> {{ selectedPayment.invoice_id }}</p>
          <p class="mb-2"><strong class="text-gray-700">Payment Date:</strong> {{ formatDate(selectedPayment.payment_date) }}</p>
          <p class="mb-2"><strong class="text-gray-700">Amount:</strong> ${{ selectedPayment.amount_paid.toFixed(2) }}</p>
          <p class="mb-2"><strong class="text-gray-700">Created:</strong> {{ formatDate(selectedPayment.created_at) }}</p>
          <p class="mb-2"><strong class="text-gray-700">Updated:</strong> {{ formatDate(selectedPayment.updated_at) }}</p>
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
  
  <script>
  import { ref, computed, onMounted } from 'vue'
  
  // Simulating tenant ID (in a real application, this would come from the user's session)
  const TENANT_ID = 1
  
  // Simulating an API to fetch tenant payments
  const fetchTenantPayments = () => {
    return new Promise((resolve) => {
      setTimeout(() => {
        resolve([
          { 
            id: 1,
            invoice_id: 'INV-001',
            payment_date: '2024-01-15',
            amount_paid: 1000,
            created_at: '2024-01-15T10:00:00',
            updated_at: '2024-01-15T10:00:00',
            tenant_id: TENANT_ID
          },
          { 
            id: 2,
            invoice_id: 'INV-002',
            payment_date: '2024-01-30',
            amount_paid: 1500,
            created_at: '2024-01-30T09:00:00',
            updated_at: '2024-01-30T15:30:00',
            tenant_id: TENANT_ID
          },
          { 
            id: 3,
            invoice_id: 'INV-003',
            payment_date: '2024-02-05',
            amount_paid: 800,
            created_at: '2024-02-05T11:00:00',
            updated_at: '2024-02-05T11:00:00',
            tenant_id: TENANT_ID
          },
          { 
            id: 4,
            invoice_id: 'INV-004',
            payment_date: '2024-03-10',
            amount_paid: 1200,
            created_at: '2024-03-10T14:00:00',
            updated_at: '2024-03-10T14:00:00',
            tenant_id: TENANT_ID
          },
          { 
            id: 5,
            invoice_id: 'INV-005',
            payment_date: '2024-04-01',
            amount_paid: 1100,
            created_at: '2024-04-01T09:30:00',
            updated_at: '2024-04-01T09:30:00',
            tenant_id: TENANT_ID
          }
        ])
      }, 1000)
    })
  }
  
  const payments = ref([])
  const selectedPayment = ref(null)
  
  onMounted(async () => {
    payments.value = await fetchTenantPayments()
  })
  
  const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  }
  
  const sortedPayments = computed(() => {
    return [...payments.value].sort((a, b) => new Date(b.payment_date) - new Date(a.payment_date))
  })
  
  const monthlyPayments = computed(() => {
    const monthlyTotals = {}
    payments.value.forEach(payment => {
      const date = new Date(payment.payment_date)
      const monthYear = date.toLocaleString('en-US', { month: 'long', year: 'numeric' })
      monthlyTotals[monthYear] = (monthlyTotals[monthYear] || 0) + payment.amount_paid
    })
    return monthlyTotals
  })
  
  const totalPaid = computed(() => {
    return payments.value.reduce((total, payment) => total + payment.amount_paid, 0)
  })
  
  const openPaymentDetails = (payment) => {
    selectedPayment.value = payment
  }
  </script>