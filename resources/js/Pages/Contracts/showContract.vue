<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';


const Contracts = [
    {status: 'Active', beingDate: '19-01-2023',endDate: '10-10-2025', tenant: 'Juan Manuel Lopez Uribe', owner: 'Derian Tarango', mount: '$5,000', type: 'Departamento', address: 'Calle 123 # 123-123', city: 'Tijuana', state: 'Baja California', country: 'Mexico'},
    {status: 'Renewal', beingDate: '19-01-2023',endDate: '10-10-2025', tenant: 'Juan Manuel Lopez Uribe', owner: 'Derian Tarango', mount: '$5,000', type: 'Departamento', address: 'Calle 123 # 123-123', city: 'Tijuana', state: 'Baja California', country: 'Mexico'},
    {status: 'Renewal', beingDate: '19-01-2023',endDate: '10-10-2025', tenant: 'Juan Manuel Lopez Uribe', owner: 'Derian Tarango', mount: '$5,000', type: 'Departamento', address: 'Calle 123 # 123-123', city: 'Tijuana', state: 'Baja California', country: 'Mexico'},
    {status: 'Finished', beingDate: '19-01-2023',endDate: '10-10-2025', tenant: 'Juan Manuel Lopez Uribe', owner: 'Derian Tarango', mount: '$5,000', type: 'Departamento', address: 'Calle 123 # 123-123', city: 'Tijuana', state: 'Baja California', country: 'Mexico'}
];
const filter = () =>{

}

</script>

<template>
    <Head title="Contracts" />
        <DashboardLayout>
            <section class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-8 w-full justify-center items-center text-center pt-8">
              <nav class="col-span-2 block bg-gray-100 rounded-lg shadow-lg p-2 h-[600px]">
                <div class="flex justify-between items-center p-2 text-grayt-200">
                    <h1 class="text-lg font-bold w-full flex">Contract Details</h1>
                </div>
                <div>

                </div>
              </nav>
              <nav class="block bg-gray-100 rounded-lg shadow-lg p-2 h-[600px]">
                <div class="block justify-between items-center p-2">
                    <h1 class="text-lg font-bold w-full flex">Contracts</h1>
                    <nav class="flex justify-end text-sm w-full text-gray-500">
                        <button @click="filter" class="p-2 flex justify-center text-center items-center group rounded-lg"><i class="fa fa-circle pr-2 text-blue-700 group-hover:text-blue-500"></i><p class="text-xs font-bold text-blue-700 group-hover:text-black hover:underline underline-offset-2">All</p></button>
                        <button @click="filter" class="p-1 flex justify-center text-center items-center  group rounded-lg"><i class="fa fa-circle pr-2 text-green-700 group-hover:text-green-500"></i><p class="text-xs font-bold text-green-700 group-hover:text-black hover:underline underline-offset-2">Active</p></button>
                        <button @click="filter" class="p-1 flex justify-center text-center items-center  group rounded-lg"><i class="fa fa-circle px-2 text-orange-800"></i><p class="text-xs font-bold text-yellow-700 group-hover:text-black hover:underline underline-offset-2">Renewal</p></button>
                        <button @click="filter" class="p-2 flex justify-center text-center items-center  group rounded-lg"><i class="fa fa-circle p-2 text-red-500"></i><p class="text-xs font-bold text-red-500 group-hover:text-black hover:underline underline-offset-2">Finished</p></button>
                        
                    </nav>
                </div>
                <!---Agregar estilos a la barra del scroll-->
                <div class="block justify-center text-start p-2  overflow-y-auto h-[500px] ">
                    <div v-for="Contract in Contracts" class="bg-white rounded-lg shadow-lg p-2 m-2">
                        <nav class="block sm:flex justify-between py-2">
                            <h1 class="text-sm text-wrap text-black font-black"> {{ Contract.type }}</h1>
                            <div class="block">
                                <p :class="{
                                    'text-sm justify-end text-end items-end font-bold w-full px-4 rounded-md':true,
                                    'bg-gradient-to-l from-green-500 to-white from-10%': Contract.status == 'Active',
                                    'bg-gradient-to-l from-yellow-500 to-white from-10%': Contract.status == 'Renewal',
                                    'bg-gradient-to-l from-red-500 to-white from-10%': Contract.status == 'Finished'


                                    }"> {{ Contract.status }}</p>
                                <h1 class="text-sm text-pretty text-primary-black font-black"> {{ Contract.mount }}</h1>
                            </div>
                        </nav>
                       <div class="block sm:flex justify-start w-full items-center text-center"> <span class="text-sm font-bold text-start  px-2">Tenant:</span><p class="text-xs"> {{ Contract.tenant }}</p></div>

                        <div class="grid grid-cols-3 gap-2 justify-center items-center text-center py-2">
                            <div class="items-center">
                            </div>
                            <div class="items-center">
                                <span class="text-sm font-bold text-start ">Being Date:</span><p class="text-xs"> {{ Contract.beingDate }}</p>
                            </div>
                            <div class="items-center">
                                <span class="text-sm font-bold text-start ">End Date:</span><p class="text-xs"> {{ Contract.endDate }}</p>
                            </div>
                        </div>
                    </div>
                </div>
              </nav>
            </section>
        </DashboardLayout>
</template>