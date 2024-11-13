<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const Contracts = [
    {status: 'Nuevo', beingDate: '19-01-2023',endDate: '10-10-2025', tenant: 'Juan Manuel Lopez Uribe', owner: 'Derian Tarango', mount: '$5,000', type: 'Departamento', address: 'Calle 123 # 123-123', city: 'Tijuana', state: 'Baja California', country: 'Mexico'},
    {status: 'Nuevo', beingDate: '19-01-2023',endDate: '10-10-2025', tenant: 'Juan Manuel Lopez Uribe', owner: 'Derian Tarango', mount: '$5,000', type: 'Departamento', address: 'Calle 123 # 123-123', city: 'Tijuana', state: 'Baja California', country: 'Mexico'},
    {status: 'Nuevo', beingDate: '19-01-2023',endDate: '10-10-2025', tenant: 'Juan Manuel Lopez Uribe', owner: 'Derian Tarango', mount: '$5,000', type: 'Departamento', address: 'Calle 123 # 123-123', city: 'Tijuana', state: 'Baja California', country: 'Mexico'},
];

</script>

<template>
    <Head title="Contracts" />
        <DashboardLayout>
            <section class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-8 w-full justify-center items-center text-center pt-8">
              <nav class="col-span-2 block bg-gray-100 rounded-lg shadow-lg p-2 h-[550px]">
                <div class="flex justify-between items-center p-2 text-grayt-200">
                    <h1 class="text-lg font-bold w-full flex">Contract Details</h1>
                </div>
                <div>

                </div>
              </nav>
              <nav class="block bg-gray-100 rounded-lg shadow-lg p-2 ">
                <div class="flex justify-between items-center p-2">
                    <h1 class="text-lg font-bold w-full flex">All Contracts</h1>
                </div>
                <div class="block justify-center text-start p-2  overflow-y-auto h-[500px]">
                    <div v-for="Contract in Contracts" class="bg-white rounded-lg shadow-lg p-2 m-2">
                        <nav class="block sm:flex justify-between py-2">
                            <h1 class="text-sm text-wrap text-black font-black"> {{ Contract.type }}</h1>
                            <h1 class="text-sm text-pretty text-primary-black font-black"> {{ Contract.mount }}</h1>
                        </nav>
                        <span class="text-sm font-bold text-start underline">Address:</span><p class="text-xs"> {{ Contract.address }}, {{ Contract.city }} {{ Contract.state }}</p>
                        <span class="text-sm font-bold text-start underline">Tenant:</span><p class="text-xs"> {{ Contract.tenant }}</p>
                        <span class="text-sm font-bold text-start underline">Owner:</span><p class="text-xs"> {{ Contract.owner }}</p>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 justify-center items-center text-center ">
                            <div class="items-center">
                                <span class="text-sm font-bold text-start underline">Status:</span><p class="text-xs"> {{ Contract.status }}</p>
                            </div>
                            <div class="items-center">
                                <span class="text-sm font-bold text-start underline">Being Date:</span><p class="text-xs"> {{ Contract.beingDate }}</p>
                            </div>
                            <div class="items-center">
                                <span class="text-sm font-bold text-start underline">End Date:</span><p class="text-xs"> {{ Contract.endDate }}</p>
                            </div>
                        </div>
                    </div>
                </div>
              </nav>
            </section>
        <!-- <div class="w-full justify-center items-center p-8">
            <table class="table-auto w-full">
                    <thead>
                        <tr>
                        <th>Song</th>
                        <th>Artist</th>
                        <th>Year</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        <td>The Sliding Mr. Bones (Next Stop, Pottersville)</td>
                        <td>Malcolm Lockyer</td>
                        <td>1961</td>
                        </tr>
                        <tr>
                        <td>Witchy Woman</td>
                        <td>The Eagles</td>
                        <td>1972</td>
                        </tr>
                        <tr>
                        <td>Shining Star</td>
                        <td>Earth, Wind, and Fire</td>
                        <td>1975</td>
                        </tr>
                    </tbody>

            </table>
        </div> -->
        </DashboardLayout>
</template>