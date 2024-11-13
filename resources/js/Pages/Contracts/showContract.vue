<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const Contracts = [
    {status: 'Nuevo', beingDate: '19-01-2023',endDate: '10-10-2025', tenant: 'Juan Manuel Lopez Uribe', owner: 'Derian Tarango', mount: '$5,000', type: 'Departamento', address: 'Calle 123 # 123-123', city: 'Tijuana', state: 'Baja California', country: 'Mexico'},
];

</script>

<template>
    <Head title="Contracts" />
        <DashboardLayout>
            <section class="grid grid-flow-row-dense grid-cols-1 sm:grid-cols-3  grid-rows-1 sm:grid-rows-3  gap-1 sm:gap-8 w-full justify-center items-center text-center pt-8">
              <nav class="col-span-2 block bg-gray-100 rounded-lg shadow-lg p-2">
                <div class="flex justify-between items-center p-2 text-grayt-200">
                    <h1 class="text-lg font-bold w-full flex">Contract Details</h1>
                </div>
                <div>

                </div>
              </nav>
              <nav class="block bg-gray-100 rounded-lg shadow-lg p-2">
                <div class="flex justify-between items-center p-2">
                    <h1 class="text-lg font-bold w-full flex">All Contracts</h1>
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