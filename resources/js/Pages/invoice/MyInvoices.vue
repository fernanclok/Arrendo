<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

// Declarar una variable para almacenar las facturas
const invoices = ref([]);

// Función para obtener todas las facturas desde la API
const fetchInvoices = async () => {
  try {
    const response = await fetch('/api/invoices'); // Llamada a la API para obtener todas las facturas
    if (response.ok) {
      const data = await response.json();
      invoices.value = data; // Asignar los datos recibidos a la variable invoices
    } else {
      console.error('Error al obtener las facturas:', await response.json());
    }
  } catch (error) {
    console.error('Error:', error);
  }
};

// Cargar las facturas al montar el componente
onMounted(() => {
  fetchInvoices();
});
</script>

<template>
  <Head title="Invoices" />
  <DashboardLayout>
    <div class="shadow-lg rounded-lg overflow-hidden mx-4 md:mx-10">
      <table class="w-full table-fixed">
        <thead>
          <tr class="bg-gray-100">
            <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Contract ID</th>
            <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Issue Date</th>
            <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Total Amount</th>
            <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Payment Status</th>
          </tr>
        </thead>
        <tbody>
          <!-- Iterar sobre las facturas y mostrarlas en la tabla -->
          <tr v-for="invoice in invoices" :key="invoice.id">
            <td class="py-4 px-6 text-gray-600">{{ invoice.contract_id }}</td>
            <td class="py-4 px-6 text-gray-600">{{ invoice.issue_date }}</td>
            <td class="py-4 px-6 text-gray-600">{{ invoice.total_amount }}</td>
            <td class="py-4 px-6 text-gray-600">{{ invoice.payment_status }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </DashboardLayout>
</template>