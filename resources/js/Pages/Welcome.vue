<script setup>
import { Head, Link } from '@inertiajs/vue3';
import CustomButton from '@/Components/CustomButton.vue';
import TextInput from '@/Components/TextInput.vue';
</script>

<template>

  <Head title="Welcome" />

  <div class="flex flex-col min-h-screen">

    <!-- header nvbar -->
    <header class="border-b">
      <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
          <Link href="/" class="flex items-center space-x-2">
          <span class="text-xl font-bold">Arrendo</span>
          </Link>
          <nav class="hidden md:flex space-x-4">
            <Link v-for="item in navItems" :key="item.href" :href="item.href"
              class="text-sm font-medium hover:underline">
            {{ item.label }}
            </Link>
          </nav>

          <div class="flex items-center space-x-2">
          <Link href="registro-propiedad"
            class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 bg-white text-gray-800 hover:bg-gray-100 focus:bg-gray-100 active:bg-gray-200 focus:ring-gray-300">
          Publicar
          </Link>
          <Link href="/login"
            class="inline-flex items-center px-3 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 bg-primary text-white hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:ring-green-500">
          Log In
          </Link>
          </div>


        </div>
      </div>
    </header>

    <!-- main content -->
    <main class="flex-grow">
      <section class="bg-gradient-to-r bg-primary text-white py-20">
        <div class="container mx-auto px-4 text-center">
          <h1 class="text-4xl md:text-6xl font-bold mb-4">Find your ideal home</h1>
          <p class="text-xl md:text-2xl mb-8">Thousands of houses for rent awaiting you!</p>
          <div class="max-w-4xl mx-auto bg-secondary rounded-lg p-4 shadow-lg">
            <div class="flex flex-col md:flex-row gap-4">
              <select class="w-full md:w-[180px] text-black">
                <option v-for="type in propertyTypes" :key="type.value" :value="type.value" class="text-black">
                  {{ type.label }}
                </option>
              </select>
              <TextInput v-model="location" placeholder="Location" class="flex-grow text-black" />
              <CustomButton type="primary" class="w-full md:w-auto" @click="searchProperties">
                Buscar
              </CustomButton>
            </div>
          </div>
        </div>
      </section>


      <section class="py-20">
        <div class="container mx-auto px-4">
          <h2 class="text-3xl font-bold text-center mb-12">Featured Properties</h2>
          <!-- propiedades destacadas -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div v-for="property in featuredProperties" :key="property.title" class="bg-white rounded-lg shadow-lg">
              <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQZtRykGihh_JvnEOQvO6I7yMkN3T45h2LhDw&s"
                alt="Property" class="w-full h-48 object-cover rounded-t-lg" />
              <div class="p-4">
                <h3 class="text-xl font-bold">{{ property.title }}</h3>
                <p class="text-gray-500">{{ property.location }}</p>
                <p class="text-lg font-bold mt-2">${{ property.price }}</p>
                <p class="text-sm text-gray-500">{{ property.type }}</p>
              </div>
            </div>
          </div>
          <div class="text-center mt-8">
            <a href="/" class="hover:underline">
              See all properties
              <ArrowRight class="ml-2 h-4 w-4" />
            </a>
          </div>
        </div>
      </section>

      <section class="bg-gray-100 py-20">
        <div class="container mx-auto px-4">
          <h2 class="text-3xl font-bold text-center mb-12">Why choose Arrendo?</h2>
          <!-- features -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div v-for="feature in features" :key="feature.title" class="bg-white rounded-lg shadow-lg p-4">
              <h3 class="text-xl font-bold mt-4">{{ feature.title }}</h3>
              <p class="text-gray-500">{{ feature.description }}</p>
            </div>
          </div>
        </div>
      </section>

      <section class="py-20">
        <div class="container mx-auto px-4">
          <h2 class="text-3xl font-bold text-center mb-12">What the tenants say about us</h2>
          <!-- testimonios -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div v-for="testimonial in testimonials" :key="testimonial.name" class="bg-white rounded-lg shadow-lg p-4">
              <p class="italic text-gray-500">{{ testimonial.content }}</p>
              <p class="font-semibold mt-4">{{ testimonial.name }}</p>
            </div>
          </div>
        </div>
      </section>

      <section class="bg-primary text-white py-20">
        <div class="container mx-auto px-4 text-center">
          <h2 class="text-3xl font-bold mb-4">Do You Have a Property to Rent?</h2>
          <p class="text-xl mb-8">Join Thousands of Owners Who Trust in Us</p>
          <a href="" class="hover:underline">
            Share my Property
            <ArrowRight class="ml-2 h-5 w-5" />
          </a>
        </div>
      </section>
    </main>
  </div>

  <footer class="text-black  py-12">
    <div class="container mx-auto px-4">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <div>
          <h3 class="text-lg font-semibold mb-4">Arrendo</h3>
          <p class="text-sm text-gray-400">A New Way to Rent Houses</p>
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
          <h4 class="text-lg font-semibold mb-4">Follow Us</h4>
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
        <p class="text-sm text-gray-400">&copy; {{ new Date().getFullYear() }} Arrendo. All Rights Reserved</p>
      </div>
    </div>
  </footer>
</template>

<script>
const navItems = [
  { href: '/', label: 'Inicio' },
  { href: '/properties', label: 'Properties' },
  { href: '/propietarios', label: 'Para propietarios' },
  { href: '/contact', label: 'Contact' },
]

const propertyTypes = [
  { value: 'House', label: 'House' },
  { value: 'Department', label: 'Department' },
  { value: 'All', label: 'All' },
]

const featuredProperties = [
  { title: "Apartamento moderno", location: "Centro de la ciudad", price: "10,000", type: "Departamento" },
  { title: "Casa familiar", location: "Zona residencial", price: "15,000", type: "Casa" },
  { title: "Loft industrial", location: "Distrito artístico", price: "8,500", type: "Departamento" },
]

const features = [
  { title: "Amplia selección", description: "Miles de propiedades verificadas para elegir" },
  { title: "Proceso sencillo", description: "Búsqueda, visita y renta en pocos pasos" },
  { title: "Soporte 24/7", description: "Estamos aquí para ayudarte en todo momento" },
]

const testimonials = [
  { name: "María García", content: "Encontré mi departamento ideal en cuestión de días. ¡El proceso fue muy fácil!" },
  { name: "Juan Pérez", content: "Excelente atención al cliente. Resolvieron todas mis dudas rápidamente." },
  { name: "Ana Martínez", content: "Las fotos y descripciones son muy precisas. No tuve sorpresas al mudarme." },
  { name: "Carlos López", content: "¡Recomiendo Arrendo a todos mis amigos! ¡Es la mejor plataforma para encontrar tu hogar!" },
  { name: "Sofía Rodríguez", content: "¡Gracias a Arrendo encontré la casa de mis sueños! ¡No puedo estar más feliz!" },
  { name: "Pedro Sánchez", content: "¡El proceso de renta fue muy rápido y sencillo! ¡Gracias Arrendo!" },
]
</script>
