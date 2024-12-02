<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';

// Declarar variables reactivas
const invoices = ref([]);
const selectedMonth = ref(new Date().getMonth() + 1); // Mes actual
const selectedYear = ref(new Date().getFullYear()); // Año actual

// Obtener los recobos por mes y año
const fetchInvoices = async () => {
  try {
    const response = await fetch(`/api/Invoices/invoices?month=${selectedMonth.value}&year=${selectedYear.value}`);
    if (response.ok) {
      const data = await response.json();
      invoices.value = data;
    } else {
      console.error('Hubo un error al obtener los recibos:', await response.json());
    }
  } catch (error) {
    console.error('Error:', error);
  }
};

const InvoicePaid = async (invoiceId) => {
  try {
    await axios.patch(`/api/Invoices/invoices/${invoiceId}/invoice-paid`);
    fetchInvoices();
  } catch (error) {
    console.error('Error al marcar como pagado:', error);
  }
};

const generatePDF = async (invoiceId) => {
    try {
        const response = await axios.get(`/api/Invoices/invoices/${invoiceId}/pdf`, {
            responseType: 'blob',
        });

        // URL del Archivo descargado
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `invoice-${invoiceId}.pdf`);
        document.body.appendChild(link);
        link.click();
    } catch (error) {
        console.error('Error generating PDF:', error);
    }
};

// Mostrar los recibos
onMounted(() => {
  fetchInvoices();
});

// Visualiza los recibos al obtener el mes y el año
watch([selectedMonth, selectedYear], fetchInvoices);
</script>

<template>
  <Head title="Invoices" />
  <DashboardLayout>
    <div class="shadow-lg rounded-lg overflow-hidden mx-4 md:mx-10">
      <h1 class="text-2xl font-semibold text-gray-900">Invoices Owner</h1>
      <div class="mb-4 flex gap-4 items-center">
        <div>
          <label for="month" class="block text-sm font-medium text-gray-700">Mes</label>
          <select 
            id="month" 
            v-model="selectedMonth" 
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
          >
            <option v-for="month in 12" :key="month" :value="month">
              {{ new Date(0, month - 1).toLocaleString('es', { month: 'long' }) }}
            </option>
          </select>
        </div>
        <div>
          <label for="year" class="block text-sm font-medium text-gray-700">Año</label>
          <select 
            id="year" 
            v-model="selectedYear" 
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
          >
            <option v-for="year in 5" :key="year" :value="new Date().getFullYear() - (5 - year)">
              {{ new Date().getFullYear() - (5 - year) }}
            </option>
          </select>
        </div>
      </div>
      <table class="w-full table-fixed">
        <thead>
          <tr class="bg-gray-100">
            <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Contract</th>
            <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Issue Date</th>
            <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Total Amount</th>
            <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Payment Status</th>
            <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="invoice in invoices" :key="invoice.id">
            <td class="py-4 px-6 text-gray-600">{{ invoice.contract_id }}</td>
            <td class="py-4 px-6 text-gray-600">{{ invoice.issue_date }}</td>
            <td class="py-4 px-6 text-gray-600">{{ invoice.total_amount }}</td>
            <td class="py-4 px-6 text-gray-600">{{ invoice.payment_status }}</td>
            <td class="py-4 px-6">
              <button 
                @click="InvoicePaid(invoice.id)" 
                class="bg-green-500 text-white px-2 py-1 rounded text-sm"
              >
                Payment
              </button>
              
              <button 
                @click="generatePDF(invoice.id)" 
                class="bg-blue-500 text-white px-2 py-1 rounded text-sm"
              >
                PDF
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </DashboardLayout>
</template>
