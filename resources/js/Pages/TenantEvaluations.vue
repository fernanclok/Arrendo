<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
</script>

<template>
    <DashboardLayout>
        <div class="container">
            <h1>Evaluación de Solicitudes</h1>
            <div v-for="solicitante in solicitantes" :key="solicitante.id" class="card mb-4">
                <div class="card-header">
                    <div class="flex justify-between items-center">
                        <p><strong>Nombre:</strong> {{ solicitante.nombre }}</p>
                        <button @click="toggleInfo(solicitante.id)" class="btn btn-primary">
                            {{ solicitante.isExpanded ? 'Menos' : 'Más' }}
                        </button>
                    </div>
                </div>
                <div v-if="solicitante.isExpanded" class="card-body">
                    <p><strong>Historial de arrendamiento:</strong> {{ solicitante.historialArrendamiento }}</p>
                    <p><strong>Ingresos:</strong> {{ solicitante.ingresos }}</p>
                    <p><strong>Referencias:</strong> {{ solicitante.referencias }}</p>
                    <p><strong>Documentos:</strong>
                        <a :href="solicitante.documentosUrl" target="_blank">Ver documentos</a>
                    </p>
                    <div class="mt-3">
                        <button class="btn btn-success">Aprobar</button>
                        <button class="btn btn-danger">Rechazar</button>
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>

<script>
export default {
  data() {
    return {
      solicitantes: [
        {
          id: 1,
          nombre: 'Juan Pérez',
          historialArrendamiento: '2 años',
          ingresos: '$30,000',
          referencias: 'Excelente',
          documentosUrl: '#',
          isExpanded: false, // Para controlar si está expandido
        },
        {
          id: 2,
          nombre: 'María López',
          historialArrendamiento: '1 año',
          ingresos: '$20,000',
          referencias: 'Buenas',
          documentosUrl: '#',
          isExpanded: false,
        },
        {
          id: 3,
          nombre: 'Carlos Ruiz',
          historialArrendamiento: '3 años',
          ingresos: '$40,000',
          referencias: 'Excelente',
          documentosUrl: '#',
          isExpanded: false,
        },
      ],
    };
  },
  methods: {
    toggleInfo(id) {
      // Alternar la propiedad isExpanded del solicitante seleccionado
      this.solicitantes = this.solicitantes.map((solicitante) =>
        solicitante.id === id
          ? { ...solicitante, isExpanded: !solicitante.isExpanded }
          : solicitante
      );
    },
  },
};
</script>

<style scoped>
.container {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

h1 {
  font-size: 1.8rem;
  font-weight: bold;
  color: #333;
  margin-bottom: 20px;
}

.card {
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.card-header {
  background-color: #f8f8f8;
  padding: 10px 15px;
  font-weight: bold;
  color: #555;
}

.card-body {
  padding: 15px;
  font-size: 0.9rem;
  color: #555;
}

button {
  padding: 8px 12px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 0.9rem;
}

.btn-primary {
  background-color: #007bff;
  color: white;
}

.btn-primary:hover {
  background-color: #0056b3;
}

.btn-success {
  background-color: #28a745;
  color: white;
  margin-right: 10px;
}

.btn-success:hover {
  background-color: #218838;
}

.btn-danger {
  background-color: #dc3545;
  color: white;
}

.btn-danger:hover {
  background-color: #c82333;
}

.mb-4 {
  margin-bottom: 1rem;
}
</style>