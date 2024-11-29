<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
</script>

<template>

        <!-- header nvbar -->
        <header class="border-b">
      <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
          <Link to="/" class="flex items-center space-x-2">
          <span class="text-xl font-bold">Arrendo</span>
          </Link>
          <nav class="hidden md:flex space-x-4">
            <Link v-for="item in navItems" :key="item.href" :href="item.href"
              class="text-sm font-medium hover:underline">
            {{ item.label }}
            </Link>
          </nav>

         <!-- <div class="flex items-center space-x-2">
          <Link href="registro-propiedad"
            class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 bg-white text-gray-800 hover:bg-gray-100 focus:bg-gray-100 active:bg-gray-200 focus:ring-gray-300">
          Publicar
          </Link>
          <Link href="/login"
            class="inline-flex items-center px-3 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 bg-primary text-white hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:ring-green-500">
          Log In
          </Link>
          </div>-->
          
        </div>
      </div>
    </header>

  <div class="min-h-screen bg-gray-100 flex flex-col items-center justify-center">
    <!-- Header de pasos -->
    <div class="w-full max-w-4xl mb-6">
      <div class="flex justify-between mt-20">
        <span :class="['step', { 'text-primary font-bold': currentStep === 1 }]">Principales</span>
        <span :class="['step', { 'text-primary font-bold': currentStep === 2 }]">Multimedia</span>
        <span :class="['step', { 'text-primary font-bold': currentStep === 3 }]">Extras</span>
        <span :class="['step', { 'text-primary font-bold': currentStep === 4 }]">Publicar</span>
      </div>
      <div class="h-1 bg-gray-200 my-2">
        <div class="h-1 bg-primary" :style="{ width: (currentStep / 4) * 100 + '%' }"></div>
      </div>
    </div>

    <!-- Paso 1 -->
    <div v-if="currentStep === 1" class="bg-white rounded-lg shadow-md p-6 w-full max-w-4xl mb-20">
      <h2 class="text-3xl font-semibold text-gray-800 mb-4 text-center">¡Empecemos a crear tu aviso!</h2>
        <p class="text-gray-600 text-center mb-6">
          Completa la información de tu propiedad paso a paso.
        </p>

      <!-- Formulario paso 1 -->
              <form @submit.prevent="validateForm" class="space-y-6">
          <div class="form-group">
            <label for="calle" class="block text-sm font-medium text-gray-700">Calle y número</label>
            <input
              type="text"
              id="calle"
              v-model="form.calle"
              class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-primary focus:border-primary"
              :class="{ 'border-red-500': errors.calle }"
              placeholder="Ingresa la calle y número"
            />
            <p v-if="errors.calle" class="text-red-500 text-sm mt-1">{{ errors.calle }}</p>
          </div>
  
          <div class="form-group">
            <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
            <select
              id="estado"
              v-model="form.estado"
              class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-primary focus:border-primary"
              :class="{ 'border-red-500': errors.estado }"
            >
              <option value="">Selecciona un estado</option>
              <option value="Baja California">Baja California</option>
              <option value="Jalisco">Jalisco</option>
            </select>
            <p v-if="errors.estado" class="text-red-500 text-sm mt-1">{{ errors.estado }}</p>
          </div>
  
          <div class="form-group">
            <label for="ciudad" class="block text-sm font-medium text-gray-700">Ciudad o Municipio</label>
            <select
              id="ciudad"
              v-model="form.ciudad"
              class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-primary focus:border-primary"
              :class="{ 'border-red-500': errors.ciudad }"
            >
              <option value="">Selecciona una ciudad</option>
              <option value="Tijuana">Tijuana</option>
              <option value="Guadalajara">Guadalajara</option>
            </select>
            <p v-if="errors.ciudad" class="text-red-500 text-sm mt-1">{{ errors.ciudad }}</p>
          </div>
  
          <div class="form-group">
            <label for="colonia" class="block text-sm font-medium text-gray-700">Colonia</label>
            <input
              type="text"
              id="colonia"
              v-model="form.colonia"
              class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-primary focus:border-primary"
              :class="{ 'border-red-500': errors.colonia }"
              placeholder="Ingresa la colonia"
            />
            <p v-if="errors.colonia" class="text-red-500 text-sm mt-1">{{ errors.colonia }}</p>

            <!-- Características Principales -->
        <div>
          <h3 class="text-lg font-medium text-gray-800 mt-8 mb-4">Características principales</h3>
          <div class="grid grid-cols-2 gap-4">
            <div v-for="(label, field) in principales" :key="field">
              <label :for="field" class="block text-sm font-medium text-gray-700">{{ label }}</label>
              <input
                type="number"
                :id="field"
                v-model="form[field]"
                min="0"
                class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-primary focus:border-primary"
              />
            </div>
          </div>
        </div>

        <!-- Superficie -->
        <div>
          <h3 class="text-lg font-medium text-gray-800 mt-8 mb-4">Superficie</h3>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label for="superficieConstruida" class="block text-sm font-medium text-gray-700">Superficie construida</label>
              <input
                type="number"
                id="superficieConstruida"
                v-model="form.superficieConstruida"
                class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-primary focus:border-primary"
                placeholder="m²"
              />
            </div>
            <div>
              <label for="superficieTotal" class="block text-sm font-medium text-gray-700">Superficie total</label>
              <input
                type="number"
                id="superficieTotal"
                v-model="form.superficieTotal"
                class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-primary focus:border-primary"
                placeholder="m²"
              />
            </div>
          </div>
        </div>

        <!-- Antigüedad -->
        <div>
          <h3 class="text-lg font-medium text-gray-800 mt-8 mb-4">Antigüedad</h3>
          <div class="space-y-2">
            <div v-for="option in antiguedadOptions" :key="option.value" class="flex items-center">
              <input
                type="radio"
                :id="option.value"
                name="antiguedad"
                :value="option.value"
                v-model="form.antiguedad"
                class="h-4 w-4 text-primary focus:ring-primary border-gray-300"
              />
              <label :for="option.value" class="ml-2 text-sm text-gray-700">{{ option.label }}</label>
            </div>
          </div>
        </div>

        <!-- Precio -->
        <div>
          <h3 class="text-lg font-medium text-gray-800 mt-8 mb-4">Precio</h3>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label for="precio" class="block text-sm font-medium text-gray-700">Precio del inmueble</label>
              <input
                type="number"
                id="precio"
                v-model="form.precio"
                class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-primary focus:border-primary"
                placeholder="$"
              />
            </div>
            <div>
              <label for="mantenimiento" class="block text-sm font-medium text-gray-700">Mantenimiento </label>
              <input
                type="number"
                id="mantenimiento"
                v-model="form.mantenimiento"
                class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-primary focus:border-primary"
                placeholder="$"
              />
            </div>
          </div>
        </div>

        <!-- Describe el Inmueble -->
        <div>
          <h3 class="text-lg font-medium text-gray-800 mt-8 mb-4">Describe el inmueble</h3>
          <div class="space-y-4">
            <div>
              <label for="titulo" class="block text-sm font-medium text-gray-700">Título</label>
              <input
                type="text"
                id="titulo"
                v-model="form.titulo"
                class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-primary focus:border-primary"
                placeholder="Completa el título de tu aviso"
              />
            </div>
            <div>
              <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
              <textarea
                id="descripcion"
                v-model="form.descripcion"
                class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-primary focus:border-primary"
                placeholder="Escribe un mínimo de 150 caracteres"
                rows="4"
              ></textarea>
            </div>
          </div>
        </div>
      </div>
     </form>

      <div class="flex justify-end space-x-4 mt-6">
        <button @click="goToNextStep" class="px-6 py-2 bg-primary text-white rounded-lg">Continuar</button>
      </div>
    </div>

    <!-- Paso 2 -->
    <div v-if="currentStep === 2" class="bg-white rounded-lg shadow-md p-6 w-full max-w-4xl">
      <h2 class="text-3xl font-semibold text-gray-800 mb-4 text-center">
          ¡Comparte fotos y videos del inmueble!
        </h2>
        <p class="text-gray-600 text-center mb-6">
          Carga entre 5 y 20 fotos. Una vez cargadas, arrastra y suelta para cambiar su orden.
        </p>
      
      
              <!-- Upload Section -->
        <form @submit.prevent="enviarFotos" class="space-y-6">
          <div class="border-2 border-dashed border-primary p-6 rounded-lg text-center">
            <p class="text-orange-600 mb-4">Arrastra o agrega las fotos del inmueble</p>
            <input
              type="file"
              multiple
              @change="handleFileUpload"
              accept="image/*"
              class="hidden"
              ref="fileInput"
            />

            
            <button
              type="button"
              class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-green-700"
              @click="abrirFileInput"
            >
              Subir fotos
            </button>
          </div>
  

        </form>

      <div class="flex justify-between mt-6">
        <button @click="goToPreviousStep" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg">Atrás</button>
        <button @click="goToNextStep" class="px-6 py-2 bg-primary text-white rounded-lg">Continuar</button>
      </div>
    </div>

    <!-- Paso 3 -->
    <div v-if="currentStep === 3" class="bg-white rounded-lg shadow-md p-6 w-full max-w-4xl mb-20">
      <h2 class="text-3xl font-semibold text-gray-800 mb-4 text-center">¡Agrega las comodidades de tu propiedad!</h2>
        <p class="text-gray-600 text-center mb-6">
          Estos campos opcionales mejoran el posicionamiento de tu publicación.
        </p>
      <!-- Contenido paso 3 -->


      <form @submit.prevent="validateForm" class="space-y-6">
          <!-- Accordion para Categorías -->
          <div v-for="(categoria, index) in categorias" :key="index" class="accordion border-b border-gray-300">
            <button
              type="button"
              class="accordion-header w-full text-left py-3 flex justify-between items-center text-gray-800 font-medium focus:outline-none"
              @click="toggleAccordion(index)"
            >
              {{ categoria.nombre }}
              <span :class="accordionStates[index] ? 'rotate-180' : ''" class="transform transition-transform">
                ▼
              </span>
            </button>
            <div v-if="accordionStates[index]" class="accordion-body transition-all ease-in-out">
              <div class="grid grid-cols-2 gap-2 mt-2">
                <div v-for="opcion in categoria.opciones" :key="opcion" class="flex items-center">
                  <input
                    type="checkbox"
                    :id="opcion"
                    :value="opcion"
                    v-model="form[categoria.nombre]"
                    class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded"
                  />
                  <label :for="opcion" class="ml-2 text-sm text-gray-700">{{ opcion }}</label>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Campos adicionales -->
          <h3 class="text-xl font-semibold text-gray-800 mt-4">Adicionales</h3>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label for="estado-conservacion" class="block text-sm font-medium text-gray-700">Estado de conservación</label>
              <select
                id="estado-conservacion"
                v-model="form.estadoConservacion"
                class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-primary focus:border-primary"
              >
                <option value="">Selecciona una opción</option>
                <option value="Excelente">Excelente</option>
                <option value="Bueno">Bueno</option>
                <option value="Regular">Regular</option>
                <option value="Malo">Malo</option>
                <option value="Remodelado">Remodelado</option>
                <option value="Para remodelar">Para remodelar</option>
              </select>
            </div>
            <div>
              <label for="bodegas" class="block text-sm font-medium text-gray-700">Bodega(s)</label>
              <input
                type="number"
                id="bodegas"
                v-model="form.bodegas"
                class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-primary focus:border-primary"
              />
            </div>
            <div>
              <label for="closets" class="block text-sm font-medium text-gray-700">Clóset(s)</label>
              <input
                type="number"
                id="closets"
                v-model="form.closets"
                class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-primary focus:border-primary"
              />
            </div>
            <div>
              <label for="niveles" class="block text-sm font-medium text-gray-700">Niveles construidos</label>
              <input
                type="number"
                id="niveles"
                v-model="form.niveles"
                class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-primary focus:border-primary"
              />
            </div>
          </div>
        </form>


      <div class="flex justify-between mt-6">
        <button @click="goToPreviousStep" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg">Atrás</button>
        <button @click="goToNextStep" class="px-6 py-2 bg-primary text-white rounded-lg">Continuar</button>
      </div>
    </div>

    <!-- Paso 4 -->
    <div v-if="currentStep === 4" class="bg-white rounded-lg shadow-md p-6 w-full max-w-4xl">
      <h2 class="text-2xl font-semibold mb-4">Publicar</h2>
      <!-- Contenido paso 4 -->
      <div class="flex justify-between mt-6">
        <button @click="goToPreviousStep" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg">Atrás</button>
        <button @click="submitForm" class="px-6 py-2 bg-green-600 text-white rounded-lg">Publicar</button>
      </div>
    </div>
  </div>

  <footer class="text-black  py-12">
    <div class="container mx-auto px-4">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <div>
          <h3 class="text-lg font-semibold mb-4">RentaFácil</h3>
          <p class="text-sm text-gray-400">Simplificando el proceso de renta de propiedades desde 2010.</p>
        </div>
        <div v-for="(column, index) in footerColumns" :key="index">
          <h4 class="text-lg font-semibold mb-4">{{ column.title }}</h4>
          <ul class="space-y-2">
            <li v-for="link in column.links" :key="link.href">
              <router-link :to="link.href" class="text-sm text-gray-400 hover:text-white">{{ link.label }}</router-link>
            </li>
          </ul>
        </div>
        <div>
          <h4 class="text-lg font-semibold mb-4">Síguenos</h4>
          <div class="flex space-x-4">
            <a v-for="social in socialLinks" :key="social.name" :href="social.href"
              class="text-gray-400 hover:text-white">
              <span class="sr-only">{{ social.name }}</span>
              <component :is="social.icon" class="h-6 w-6" />
            </a>
          </div>
        </div>
      </div>
      <div class="mt-8 border-t border-gray-800 pt-8 text-center">
        <p class="text-sm text-gray-400">&copy; {{ new Date().getFullYear() }} RentaFácil. Todos los derechos
          reservados.</p>
      </div>
    </div>
  </footer>


</template>

<script>
const navItems = [
  { href: '/', label: 'Inicio' },
  { href: '/properties', label: 'Properties' },
  // { href: '/propietarios', label: 'For Owners' },
  { href: '/contact', label: 'Contact Us' },
]

export default {
    data() {
      return {
        form:{
          calle: "",
          estado: "",
          ciudad: "",
          colonia: "",
          recamaras: 0,
          banos: 0,
          medioBano: 0,
          estacionamientos: 0,
          superficieConstruida: "",
          superficieTotal: "",
          antiguedad: "estrenar",
          precio: "",
          mantenimiento: "",
          titulo: "",
          descripcion: "",

          estadoConservacion: "",
          bodegas: 0,
          closets: 0,
          niveles: 0,
          "Características generales": [],
          Servicios: [],
          Exteriores: [],
          Ambientes: [],
        },
        files: [],
        errors: {},
        principales: {
        recamaras: "Recámaras",
        banos: "Baños",
        medioBano: "Medio baño",
        estacionamientos: "Estacionamientos",
      },
      antiguedadOptions: [
        { value: "estrenar", label: "A estrenar" },
        { value: "usado", label: "Años de antigüedad" },
        ],
        categorias: [
          {
            nombre: "Características generales",
            opciones: [
              "Acceso discapacitados",
              "Alberca",
              "Jacuzzi",
              "Mascotas",
              "Amueblado",
              "Cocina equipada",
              "Seguridad privada",
              "Terraza",
            ],
          },
          {
            nombre: "Servicios",
            opciones: [
              "Aire acondicionado",
              "Gimnasio",
              "Internet/WiFi",
              "Cuarto de servicio",
              "Gas natural",
            ],
          },
          {
            nombre: "Exteriores",
            opciones: ["Asador", "Jardín privado", "Estacionamiento techado", "Balcón"],
          },
          {
            nombre: "Ambientes",
            opciones: ["Cuarto de TV", "Estudio", "Salón de usos múltiples", "Área de lavado"],
          },
        ],
        accordionStates: [false, false, false, false], // Estado de cada categoría
      };
      
    },
    methods: {
      abrirFileInput() {
        this.$refs.fileInput.click();
      },
      handleFileUpload(event) {
        const uploadedFiles = Array.from(event.target.files);
        this.files = uploadedFiles.map(file => ({
          name: file.name,
          preview: URL.createObjectURL(file),
        }));
      },
      enviarFotos() {
        if (this.files.length < 5) {
          alert("Por favor, sube al menos 5 fotos.");
          return;
        }
        alert("Fotos enviadas correctamente.");
      },
      guardarSalir() {
        alert("Guardando cambios...");
      },

      toggleAccordion(index) {
        this.accordionStates.splice(index, 1, !this.accordionStates[index]);
      },
      validateForm() {
        if (!this.form.estadoConservacion) {
          alert("Por favor selecciona el estado de conservación");
          return;
        }
        alert("Formulario válido.");
      },
      guardarSalir() {
        alert("Guardando y saliendo...");
      },
    },
  };

// Control del paso actual
const currentStep = ref(1);

// Datos del formulario
const form = ref({
  calle: "",
  estado: "",
  ciudad: "",
  colonia: "",
  recamaras: 0,
  banos: 0,
  medioBano: 0,
  estacionamientos: 0,
  superficieConstruida: "",
  superficieTotal: "",
  antiguedad: "estrenar",
  precio: "",
  mantenimiento: "",
  titulo: "",
  descripcion: "",

  imagenes: [],

  extras: {
    piscina: false,
    jardin: false,
    gimnasio: false,
  },

  publicar: false,
});

// Errores del formulario
const errors = ref({});

// Validar formulario y avanzar al siguiente paso
const goToNextStep = () => {
  errors.value = {};

  if (currentStep.value === 1) {
    if (!form.value.titulo) errors.value.titulo = "El título es obligatorio.";
    if (form.value.descripcion.length < 150) {
      errors.value.descripcion = "La descripción debe tener al menos 150 caracteres.";
    }
  }

  if (Object.keys(errors.value).length === 0) {
    currentStep.value = Math.min(currentStep.value + 1, 4);
  }
};

// Retroceder al paso anterior
const goToPreviousStep = () => {
  currentStep.value = Math.max(currentStep.value - 1, 1);
};

// Manejar subida de imágenes
const handleImageUpload = (event) => {
  form.value.imagenes = Array.from(event.target.files);
  console.log("Imágenes seleccionadas:", form.value.imagenes);
};

// Publicar formulario (Paso final)
const submitForm = () => {
  console.log("Formulario enviado:", form.value);
  alert("¡Publicación completada con éxito!");
};
</script>

<style scoped>
.step {
  font-size: 0.875rem;
  color: #6b7280;
}
.text-primary {
  color: #10b981;
}
.bg-primary {
  background-color: #10b981;
}
</style>
