<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';

const invoices = ref([]);

// Muestra los recibos por usuario
const fetchInvoices = async () => {
    try {
        const response = await axios.get('/api/my-invoices');
        invoices.value = response.data; 
    } catch (error) {
        console.error('Error fetching invoices:', error);
    }
};


//Funcion para pagar
const InvoicePaid = async (invoiceId) => {
    try {
        await axios.patch(`/api/invoices/${invoiceId}/invoice-paid`);
        fetchInvoices();
    } catch (error) {
        console.error('Error marking invoice as paid:', error);
    }
};

onMounted(fetchInvoices);
</script>

<template>
  <Head title="My Invoices" />
  <DashboardLayout>
    <div class="shadow-lg rounded-lg overflow-hidden mx-4 md:mx-10">
      <table class="w-full table-fixed">
        <thead>
          <tr class="bg-gray-100">
            <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">#</th>
            <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Date</th>
            <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Contract</th>
            <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Name</th>
            <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Issue Date</th>
            <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Total Amount</th>
            <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Payment Status</th>
            <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white">
          <tr v-for="(invoice, index) in invoices" :key="invoice.invoice_id">
            <td class="py-4 px-6">{{ index + 1 }}</td>
            <td class="py-4 px-6">{{ invoice.invoice_date }}</td>
            <td class="py-4 px-6">{{ invoice.contract_id }}</td>
            <td class="py-4 px-6">{{ invoice.tenant_name }}</td>
            <td class="py-4 px-6">{{ invoice.invoice_date }}</td>
            <td class="py-4 px-6">{{ invoice.invoice_total }}</td>
            <td class="py-4 px-6">{{ invoice.payment_status }}</td>
            <td class="py-4 px-6">
              <button 
                @click="InvoicePaid(invoice.invoice_id)" 
                class="bg-green-500 text-white px-4 py-2 rounded"
              >
                Pagado
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </DashboardLayout>
</template>
