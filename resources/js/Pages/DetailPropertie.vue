<template>

    <header class="shadow">
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

                <div class="flex items-center space-x-2">
                    <Link href="registro-propiedad"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 bg-white text-gray-800 hover:bg-gray-100 focus:bg-gray-100 active:bg-gray-200 focus:ring-gray-300">
                    Publicar
                    </Link>
                    <Link href="/login"
                        class="inline-flex items-center px-3 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 bg-primary text-white hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:ring-blue-500">
                    Log In
                    </Link>
                </div>

            </div>
        </div>
    </header>

    <div class="container mx-auto px-8 py-8 space-y-12">
        <!-- Galería de imágenes -->
        <div class="space-y-4">
            <div class="main-image">
                <img :src="property.photos[0]" alt="Foto principal"
                    class="w-full h-96 object-cover rounded-lg shadow" />
            </div>
            <div class="thumbnails grid grid-cols-3 gap-4">
                <img v-for="(photo, index) in property.photos.slice(1)" :key="index" :src="photo" alt="Foto adicional"
                    class="w-full h-32 object-cover rounded-lg shadow" />
            </div>
        </div>

        <!-- Información de la propiedad -->
        <div class="info space-y-8">
            <!-- Información principal -->
            <div class="text-center space-y-2">
                <h1 class="text-4xl font-bold">{{ property.title }}</h1>
                <p class="text-xl text-gray-700">{{ property.generalInfo }}</p>
                <p class="text-3xl font-bold text-green-600">{{ property.price }}</p>
            </div>

            <!-- Detalles de la propiedad -->
            <div class="details grid grid-cols-2 md:grid-cols-4 gap-8 text-center bg-gray-100 p-8 rounded-lg shadow">
                <div>
                    <p class="text-2xl font-bold">{{ property.details.totalArea }} m²</p>
                    <p class="text-sm text-gray-500">Total</p>
                </div>
                <div>
                    <p class="text-2xl font-bold">{{ property.details.builtArea }} m²</p>
                    <p class="text-sm text-gray-500">Construcción</p>
                </div>
                <div>
                    <p class="text-2xl font-bold">{{ property.details.bathrooms }}</p>
                    <p class="text-sm text-gray-500">Baños</p>
                </div>
                <div>
                    <p class="text-2xl font-bold">{{ property.details.parkingSpots }}</p>
                    <p class="text-sm text-gray-500">Estacionamientos</p>
                </div>
                <div class="col-span-2 md:col-span-4">
                    <p class="text-2xl font-bold">{{ property.details.age }}</p>
                    <p class="text-sm text-gray-500">Antigüedad</p>
                </div>
            </div>

            <!-- Descripción -->
            <div class="description space-y-2">
                <h2 class="text-2xl font-semibold">Descripción</h2>
                <p class="text-lg text-gray-700">{{ property.description }}</p>
            </div>
        </div>

        <!-- Formulario de contacto -->
        <div class="contact bg-gray-100 p-8 rounded-lg shadow">
            <h3 class="text-2xl font-bold mb-6">Contacta al anunciante</h3>
            <form class="space-y-6">
                <div>
                    <label for="name" class="block text-lg font-semibold">Nombre</label>
                    <input id="name" type="text" placeholder="Tu nombre"
                        class="w-full border-gray-300 rounded-lg shadow-sm p-3 text-lg" />
                </div>
                <div>
                    <label for="email" class="block text-lg font-semibold">Email</label>
                    <input id="email" type="email" placeholder="Tu email"
                        class="w-full border-gray-300 rounded-lg shadow-sm p-3 text-lg" />
                </div>
                <div>
                    <label for="message" class="block text-lg font-semibold">Mensaje</label>
                    <textarea id="message" rows="6" placeholder="Hola, me interesa esta propiedad..."
                        class="w-full border-gray-300 rounded-lg shadow-sm p-3 text-lg"></textarea>
                </div>
                <button type="submit"
                    class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 text-lg font-bold">
                    Contactar
                </button>
            </form>
        </div>
    </div>

    <footer>
        <div>
            <H1>hello</H1>
            mucho texto
        </div>
    </footer>
</template>

<script setup>
const property = {
    title: "Casa con Local y Oficinas T",
    generalInfo: "Casa - 300m² - 3 recámaras - 1 estacionamiento",
    price: "MN 5,950,000",
    details: {
        totalArea: 362,
        builtArea: 300,
        bathrooms: 2,
        parkingSpots: 1,
        age: "30 años",
    },
    description:
        "Casa con cochera para 3 autos, sala, comedor, cerca de Plaza Dorada.",
    photos: [
        "https://via.placeholder.com/800x400",
        "https://via.placeholder.com/400x300",
        "https://via.placeholder.com/400x300",
        "https://via.placeholder.com/400x300",
    ],
};
</script>

<style scoped>
.container {
    max-width: 70%;
}

.main-image img {
    transition: transform 0.3s ease-in-out;
}

.main-image img:hover {
    transform: scale(1.02);
}

.contact input,
.contact textarea {
    border: 1px solid #ddd;
}
</style>