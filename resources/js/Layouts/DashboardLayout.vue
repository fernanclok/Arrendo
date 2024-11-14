<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { ref } from 'vue'

// const Items = [
//   { href: '/', label: 'Dashboard', icon: 'home' },
//   { href: '/', label: 'Contracts', icon: 'file' },
//   { href: '/propietarios', label: 'Retrial', icon: 'user' },
//   { href: '/', label: 'Propieties', icon: 'building' }
// ];

const { props } = usePage();

const navLinks = ref(props.navLinks);
console.log(navLinks);


const isSidebarOpen = ref(false)
const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value
}
</script>
<template>
    <div class="flex flex-col min-h-screen">
    
        <aside id="logo-sidebar" :class="{
            'fixed top-0 left-0 z-40 w-44 h-screen p-2 transition-transform bg-primary-black border-r border-gray-200 dark:border-gray-700': true,
            'translate-x-0': !isSidebarOpen,
            '-translate-x-full': isSidebarOpen,
            'hidden sm:block': isSidebarOpen
        }" aria-label="Sidebar">
            <div class="h-full p-2 overflow-y-auto bg-primary-black">
                <nav class="flex items-center justify-center border-b border-gray-500 p-6">
                    <img src="/log.jpg" class="h-8 me-3 rounded-full" alt="Arrendo Logo" />
                    <h1 class="text-gray-300 text-sm font-bold block text-wrap">{{ $page.props.auth.user.name }} {{ $page.props.auth.user.name }}</h1>
                </nav>
                <ul class="space-y-2 pb-2">
                    <li v-for="link in navLinks" :key="link" class="flex items-center p-2 text-gray-300 rounded-md hover:bg-secondary focus:ring-white group">
                        <Link :href="route(link)" :active="route().current(link)">
                            {{ link.charAt(0).toUpperCase() + link.slice(1) }}
                        </Link>
                    </li>
                </ul>
                <div class="border border-gray-500 my-2"></div>
                <ul>
                    <li v-for="action in navLinks" :key="action" class="flex items-center p-2 text-gray-300 rounded-md hover:bg-secondary focus:ring-white group">
                        <Link :href="route(action)" class="text-sm font-thin group-hover:text-gray-900 flex items-center">
                            <i :class="`fa fa-${action.icon} mr-2`"></i>
                            {{ action.charAt(0).toUpperCase() + action.slice(1) }}
                        </Link>
                    </li>
                </ul>
                <button @click="toggleSidebar"
                    :aria-controls="'logo-sidebar'"
                    :aria-expanded="isSidebarOpen"
                    type="button" class="inline-flex my-4 w-fit items-center p-2 text-sm text-gray-200 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 hover:text-gray-900">
                    <span class="sr-only">
                        {{ isSidebarOpen ? 'Cerrar sidebar' : 'Abrir sidebar' }}
                    </span>
                    <svg :class="{
                        'text-white font-bold w-full h-6': isSidebarOpen,
                        'transform rotate-180': !isSidebarOpen,
                    }" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 16 4-4-4-4m6 8 4-4-4-4"/>
                    </svg>
                </button>
            </div>
        </aside>

        <nav :class="{
            'transition-all duration-300 p-8 ': true,
            'sm:ml-44': !isSidebarOpen,  // Cuando el sidebar está cerrado, mover el contenido
            'sm:ml-0': isSidebarOpen
        }">
            <div :class="{
                'flex items-center transition-all duration-300 justify-between rtl:justify-end': true,
            }">

                <button @click="toggleSidebar" :aria-controls="'logo-sidebar'" :aria-expanded="isSidebarOpen"
                    type="button"
                    class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
                    <span class="sr-only">{{ isSidebarOpen ? 'Cerrar sidebar' : 'Abrir sidebar' }}</span>
                    <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path clip-rule="evenodd" fill-rule="evenodd"
                            d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
                        </path>
                    </svg>
                </button>

                <!-- Search bar
                <div class="flex justify-center items-center ">
                    <TextInput v-model="location" placeholder="Search" class="flex-grow text-black mr-2" />
                    <button class="p-2 bg-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                        @click="searchProperties">
                        <i class="fa fa-search text-gray-800"></i>
                    </button>
                </div> -->
            </div>

            <div>
                <div class="border-gray-200 transition-all duration-300 ">
                    <header class="bg-white shadow" v-if="$slots.header">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            <slot name="header" />
                        </div>
                    </header>
                    <main class="">
                        <slot />
                    </main>
                </div>
            </div>
        </nav>
    </div>
</template>
