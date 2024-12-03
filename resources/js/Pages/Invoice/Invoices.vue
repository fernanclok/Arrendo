<script setup>
import CustomButton from '@/Components/CustomButton.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';

// Declarar variables reactivas
const invoices = ref([]);
const selectedMonth = ref(new Date().getMonth() + 1); // Mes actual
const selectedYear = ref(new Date().getFullYear()); // Año actual
const ispayopen = ref(false);
const selectedInvoice = ref(null);

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
    closePaymentmodal();
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

const openEvidence = (evidence) => {
  window.open(`/${evidence}`, '_blank');
};

// Abrir modal de terminación
const openPaymentmodal = (invoiceID) => {
    selectedInvoice.value = invoiceID;
    ispayopen.value = true;
};

const closePaymentmodal = () => {
    ispayopen.value = false;
    selectedInvoice.value = null;
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
    <nav class="p-8  bg-gray-100 rounded-lg shadow-lg">
      <h1 class="text-2xl font-semibold text-gray-900">Invoices Owner</h1>
        <div class="mb-4 flex gap-4 items-center">
            <div class="w-full">
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
          <div class="w-full">
            <label for="year" class="block text-sm font-medium text-gray-700">Año</label>
            <select 
              id="year" 
              v-model="selectedYear" 
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
            >
              <option v-for="year in 10" :key="year" :value="new Date().getFullYear() - (5 - year)">
                {{ new Date().getFullYear() - (5 - year) }}
              </option>
            </select>
          </div>
      </div>
    </nav>
    <div class="shadow-lg rounded-lg overflow-hidden mx-4 md:mx-10 mt-8">
      <table class="w-full table-fixed">
        <thead>
          <tr class="bg-gray-100">
            <th class="w-1/4 py-4 px-6 text-center text-gray-600 font-bold uppercase ">Contract</th>
            <th class="w-1/4 py-4 px-6 text-center text-gray-600 font-bold uppercase">Issue Date</th>
            <th class="w-1/4 py-4 px-6 text-center text-gray-600 font-bold uppercase">Total Amount</th>
            <th class="w-1/4 py-4 px-6 text-center text-gray-600 font-bold uppercase">Payment Status</th>
            <th class="w-1/4 py-4 px-6 text-center text-gray-600 font-bold uppercase">Evidences</th>
            <th class="w-1/4 py-4 px-6 text-center text-gray-600 font-bold uppercase">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="invoice in invoices" :key="invoice.id">
            <td class="py-4 px-6 text-gray-600 text-center">{{ invoice.contract.contract_code }}</td>
            <td class="py-4 px-6 text-gray-600 text-center">{{ invoice.issue_date }}</td>
            <td class="py-4 px-6 text-gray-600 text-center">{{ invoice.total_amount }}</td>
            <td class="py-4 px-6 text-gray-900 text-center"><span class="bg-yellow-400 p-2 rounded-lg shadow-lg ">{{ invoice.payment_status }}</span></td>
            <td class="py-4 px-6 text-gray-600 text-center">
              <div v-if="invoice.evidence_path">
                <div v-for="(evidence, index) in JSON.parse(invoice.evidence_path)" :key="index" class="mb-2">
                  <CustomButton
                    @click="openEvidence(evidence)"
                  >
                    <i class="mdi mdi-link-variant text-xs"> Ver evidencia {{ index + 1 }}</i>
                  </CustomButton>
                </div>
              </div>
              <span v-else class="text-gray-500">No hay evidencias</span>
            </td>
            <td class="py-4 px-6 space-x-3 text-center">
              <SecondaryButton
                @click="openPaymentmodal(invoice.id)"
                class=" text-gray-900 px-2 py-1 rounded text-xl"
              >
                Payment
              </SecondaryButton>
              
              <CustomButton @click="generatePDF(invoice.id)">
                <i class="mdi mdi-file text-xs"> PDF</i>
              </CustomButton>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
      <!-- Modal para terminar contrato -->
      <Modal :show="ispayopen" @close="closePaymentmodal">
          <template #default>
            <nav class="p-8 bg-gray-100">
              <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-900">Payment verification</h1>
              </div>
              <div class="p-6">
                <p class="text-red-600">This invoice was payed?</p>
                <div class="flex justify-end items-center space-x-4 mt-6">
                  <SecondaryButton @click="closePaymentmodal">No</SecondaryButton>
                  <CustomButton @click="InvoicePaid(selectedInvoice)" class="bg-red-500 hover:bg-red-700 text-gray-900">yes</CustomButton>
                </div>
              </div>
            </nav>
          </template>
        </Modal>
  </DashboardLayout>
</template>
