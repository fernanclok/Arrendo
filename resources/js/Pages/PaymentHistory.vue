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
        <input
          type="text"
          v-model="searchQuery"
          placeholder="Buscar por ID, fecha de pago o factura"
          class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
        />
        <select
          v-model="selectedOwner"
          class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
        >
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
            <span class="bg-green-500 text-white text-sm px-2 py-1 rounded-full">
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
          </div>
        </div>
  
        <!-- Pendientes -->
        <div class="bg-yellow-100 rounded-lg p-4 shadow-md border-l-4 border-yellow-600">
          <h2 class="text-xl font-bold mb-4 flex items-center justify-between">
            <span>Pendiente</span>
            <span class="bg-yellow-500 text-white text-sm px-2 py-1 rounded-full">
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
          </div>
        </div>
  
        <!-- Atrasados -->
        <div class="bg-red-100 rounded-lg p-4 shadow-md border-l-4 border-red-600">
          <h2 class="text-xl font-bold mb-4 flex items-center justify-between">
            <span>Atrasado</span>
            <span class="bg-red-500 text-white text-sm px-2 py-1 rounded-full">
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
          </div>
        </div>
      </div>
    </div>
</DashboardLayout>
  </template>
  
  <script>
  import { ref, computed, onMounted, watch } from 'vue'
  
  // Simulación de una API para obtener pagos
  const fetchPayments = () => {
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
            owner: 'Juan Pérez'
          },
          { 
            id: 2,
            invoice_id: 'INV-002',
            payment_date: '2024-01-30',
            amount_paid: 1500,
            created_at: '2024-01-30T09:00:00',
            updated_at: '2024-01-30T15:30:00',
            owner: 'María González'
          },
          { 
            id: 3,
            invoice_id: 'INV-003',
            payment_date: '2024-02-05',
            amount_paid: 800,
            created_at: '2024-02-05T11:00:00',
            updated_at: '2024-02-05T11:00:00',
            owner: 'Carlos Rodríguez'
          },
          { 
            id: 4,
            invoice_id: 'INV-004',
            payment_date: '2024-06-20',
            amount_paid: 0,
            created_at: '2024-02-15T10:00:00',
            updated_at: '2024-02-15T10:00:00',
            owner: 'Ana Martínez'
          },
          { 
            id: 5,
            invoice_id: 'INV-005',
            payment_date: '2024-07-01',
            amount_paid: 0,
            created_at: '2024-03-01T09:30:00',
            updated_at: '2024-03-01T09:30:00',
            owner: 'Luis Sánchez'
          },
          { 
            id: 6,
            invoice_id: 'INV-006',
            payment_date: '2024-07-15',
            amount_paid: 0,
            created_at: '2024-03-15T14:00:00',
            updated_at: '2024-03-15T14:00:00',
            owner: 'Elena Torres'
          },
          { 
            id: 7,
            invoice_id: 'INV-007',
            payment_date: '2023-12-31',
            amount_paid: 0,
            created_at: '2023-12-01T08:00:00',
            updated_at: '2023-12-01T08:00:00',
            owner: 'Roberto Gómez'
          },
          { 
            id: 8,
            invoice_id: 'INV-008',
            payment_date: '2024-01-15',
            amount_paid: 0,
            created_at: '2023-12-15T10:30:00',
            updated_at: '2023-12-15T10:30:00',
            owner: 'Carmen Ruiz'
          },
          { 
            id: 9,
            invoice_id: 'INV-009',
            payment_date: '2024-02-01',
            amount_paid: 0,
            created_at: '2024-01-02T11:45:00',
            updated_at: '2024-01-02T11:45:00',
            owner: 'Miguel Fernández'
          }
        ])
      }, 1000)
    })
  }
  
  const payments = ref([])
  const searchQuery = ref('')
  const selectedOwner = ref('')
  
  onMounted(async () => {
    payments.value = await fetchPayments()
  })
  
  const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('es-ES', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  }
  
  const uniqueOwners = computed(() => {
    return [...new Set(payments.value.map(payment => payment.owner))]
  })
  
  const filteredPayments = computed(() => {
    return payments.value
      .filter(payment => 
        matchesSearchQuery(payment) && matchesOwnerFilter(payment)
      )
      .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
  })
  
  const pagados = computed(() => 
    filteredPayments.value.filter(payment => payment.amount_paid > 0)
  )
  
  const pendientes = computed(() => {
    const now = new Date()
    return filteredPayments.value.filter(
      payment => !payment.amount_paid && new Date(payment.payment_date) >= now
    )
  })
  
  const atrasados = computed(() => {
    const now = new Date()
    return filteredPayments.value.filter(
      payment => !payment.amount_paid && new Date(payment.payment_date) < now
    )
  })
  
  const filteredPagados = computed(() => pagados.value)
  const filteredPendientes = computed(() => pendientes.value)
  const filteredAtrasados = computed(() => atrasados.value)
  
  function matchesSearchQuery(payment) {
    const query = searchQuery.value.toLowerCase()
    return (
      payment.id.toString().includes(query) ||
      payment.payment_date.toLowerCase().includes(query) ||
      payment.invoice_id.toLowerCase().includes(query)
    )
  }
  
  function matchesOwnerFilter(payment) {
    if (!selectedOwner.value) return true
    return payment.owner === selectedOwner.value
  }
  
  // Simular la adición de nuevos datos cada 10 segundos
  const addNewPayment = () => {
    const newId = payments.value.length + 1
    const newPayment = {
      id: newId,
      invoice_id: `INV-${String(newId).padStart(3, '0')}`,
      payment_date: new Date().toISOString().split('T')[0],
      amount_paid: Math.random() > 0.5 ? Math.floor(Math.random() * 1000) + 500 : 0,
      created_at: new Date().toISOString(),
      updated_at: new Date().toISOString(),
      owner: uniqueOwners.value[Math.floor(Math.random() * uniqueOwners.value.length)]
    }
    payments.value.unshift(newPayment)
  }
  
  setInterval(addNewPayment, 10000)
  
  // Observar cambios en searchQuery y selectedOwner
  watch([searchQuery, selectedOwner], () => {
    // La reactividad se encargará de actualizar los resultados filtrados
  })
  </script>