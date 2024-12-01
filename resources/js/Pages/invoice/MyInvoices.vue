<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';

const user = usePage().props.auth.user;
const invoices = ref([]);

// Muestra los recibos por usuario
const fetchInvoices = async () => {
    try {
        const response = await axios.get('/api/invoices', {
            params: {
                user_id: user.id,
            },
        });
        invoices.value = response.data;
    } catch (error) {
        console.error('Error fetching invoices:', error);
    }
};

const generatePDF = async (invoiceId) => {
    try {
        const response = await axios.get(`/api/my-invoices/${invoiceId}/pdf`, {
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


onMounted(fetchInvoices);
</script>

<template>
  <Head title="My Invoices" />
  <DashboardLayout>
    <div class="shadow-lg rounded-lg overflow-hidden mx-4 md:mx-10">
      <h1 class="text-2xl font-semibold text-gray-900">My Invoices Tenant</h1>
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
          <tr v-for="(invoice, index) in invoices" :key="invoice.id">
            <td class="py-4 px-6">{{ index + 1 }}</td>
            <td class="py-4 px-6">{{ invoice.invoice_date }}</td>
            <td class="py-4 px-6">{{ invoice.contract_id }}</td>
            <td class="py-4 px-6">{{ invoice.user_id }}</td>
            <td class="py-4 px-6">{{ invoice.issue_date }}</td>
            <td class="py-4 px-6">{{ invoice.total_amount }}</td>
            <td class="py-4 px-6">{{ invoice.payment_status }}</td>
            <td class="py-4 px-6">
              <button 
                @click="generatePDF(invoice.id)" 
                class="bg-green-500 text-white px-4 py-2 rounded"
              >
                Generar Pdf
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </DashboardLayout>
</template>
