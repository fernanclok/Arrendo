<script setup>
import axios from 'axios';
import CustomButton from '@/Components/CustomButton.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Notification from '@/Components/NotificationCard.vue'; // Asegúrate de tener la ruta correcta
import { useForm } from '@inertiajs/vue3';
import { Head, usePage, Link } from '@inertiajs/vue3';
import { ref, computed, onMounted, defineEmits, watch } from 'vue';

// Datos iniciales

const user = usePage().props.auth.user;
const tenants = ref([]);
const properties = ref([]);
const contracts = ref([]);
const filterStatus = ref('all'); // valor por defecto
const selectedPropertyId = ref(null);

const notification = ref({
  show: false,
  type: '',
  title: '',
  message: '',
});

const showNotification = (data) => {
  notification.value = { ...data, show: true };
};

const closeNotification = () => {
  notification.value.show = false;
};

// Funciones para obtener datos
const getTenants = async (propertyId) => {
  try {
    const response = await axios.get('/api/contracts/get/user_tenant', {
      params: { property_id: propertyId }
    });
    console.log(response.data);
    tenants.value = response.data;
  } catch (error) {
    console.error(error);
  }
};

const getProperties = () => {
  axios.get('/api/contracts/get/properties', { params: { user_id: user.id } })
    .then(response => {
      properties.value = response.data;
    })
    .catch(error => {
      console.error(error);
    });
};

const getContracts = async () => {
  try {
    const response = await axios.get('/api/contracts', { params: { user_id: user.id } });
    contracts.value = response.data;
  } catch (error) {
    console.error(error);
  }
};

// Filtrar contratos
const filteredContracts = computed(() => {
  if (filterStatus.value === 'all') {
    return contracts.value;
  }
  return contracts.value.filter(contract => contract.status === filterStatus.value);
});

// Actualizar filtro
const filter = (status) => {
  filterStatus.value = status;
};



// Crear contrato
const form = useForm({
  property_id: '',
  tenant_user_id: '',
  start_date: '',
  end_date: '',
  rental_amount: '2500',
  contract_files: [],
  owner_user_id: user.id,
  status: 'Active',
  errors: {},
});

const validateForm = () => {
  form.errors = {};
  // Validar fechas
  const startDate = new Date(form.start_date);
  const endDate = new Date(form.end_date);
  if (startDate > endDate) {
    form.errors.start_date = 'Start date cannot be after end date';
    form.errors.end_date = 'End date cannot be before start date';
  }

  return Object.keys(form.errors).length === 0;
}

// Observar cambios en las fechas y validar en tiempo real
watch([() => form.start_date, () => form.end_date], () => {
  validateForm();
});
// Watch para detectar cambios en la propiedad seleccionada
watch(selectedPropertyId, (newPropertyId) => {
  if (newPropertyId) {
    getTenants(newPropertyId);
  }
});

const handleFileUpload = (event) => {
  const files = event.target.files;
  for (let i = 0; i < files.length; i++) {
    const file = files[i];
    const reader = new FileReader();
    reader.onload = (e) => {
      form.contract_files.push({
        file: file,
        preview: e.target.result
      });
    };
    reader.readAsDataURL(file);
  }
};

const removeFile = (index) => {
  form.contract_files.splice(index, 1);
};


// Aquí creamos un emisor para emitir eventos
const emit = defineEmits(['show_notification']); // Definimos el evento a emitir

// Función para manejar el envío del formulario
const submitForm = async () => {


  const formData = new FormData();
  form.contract_files.forEach(fileObj => {
    formData.append('contract_files[]', fileObj.file);
  });
  formData.append('property_id', selectedPropertyId.value);
  formData.append('tenant_user_id', form.tenant_user_id);
  formData.append('start_date', form.start_date);
  formData.append('end_date', form.end_date);
  formData.append('rental_amount', form.rental_amount);
  formData.append('owner_user_id', form.owner_user_id);
  formData.append('status', form.status);

  try {
    await axios.post('/api/contracts/create', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    showNotification({
        title: 'Success',
        message: `Contracts requested successfully! Check the status in <a href="/all-contracts" class="text-green-700 underline">Manage Contracts</a>.`,
        type: 'success'
        });
    getContracts();
  } catch (error) {
    console.error('Error al crear el contrato', error);
    showNotification({
        title: 'Error',
        message: `Contracts requested Fails! Try again later.`,
        type: 'error'
        });
  }
};


// Llamar a getContracts cuando el componente se monte
onMounted(() => {
  getTenants();
  getProperties();
  getContracts();
});
</script>


<template>
    <Head title="Contracts" />
        <DashboardLayout>
            <section class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-8 w-full justify-center items-center text-center pt-8">
              <nav class="col-span-2 block  rounded-lg p-2 h-full">
                <div id="" class="">
                    <form @submit.prevent="submitForm" class="mt-2 space-y-4 bg-gray-100 p-8 rounded-lg">
                    <nav :class="{
                        'grid gap-4 w-full':true,
                        'grid-cols-1': form.contract_files.length == 0,
                        'grid-cols-1 sm:grid-cols-2': form.contract_files.length > 0,
                    }">
                        <div class="w-full">
                        <InputLabel for="contract_file"  class="flex flex-col items-center justify-center w-full h-[200px] border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 ">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <i class="mdi mdi-cloud-arrow-up-outline text-4xl text-gray-400"></i>
                            <p class="text-sm text-gray-500 font-semibold">Click to upload</p>
                            <p class="text-sm text-gray-500 font-semibold">PDF, PNG, JPG</p>
                            </div>
                            <input type="file" id="contract_file"  @change="handleFileUpload"  class="hidden" accept=".png, .jpg, .jpeg, .pdf" multiple />
                        </InputLabel>
                        <InputError class="mt-2" :message="form.errors.contract_file" />         
                        </div>
                        <!-- Mostrar archivos cargados con vista previa -->
                        <div :class="{
                        'flex-wrap justify-center items center gap-2 h-[200px] w-full p-4 overflow-y-scroll':true,
                        'hidden': form.contract_files.length == 0,
                        'flex': form.contract_files.length > 0,
                        }">
                            <div v-for="(file, index) in form.contract_files" :key="index" class="flex items-center justify-between w-full mt-2 p-2 bg-primary rounded-lg overflow-hidden">
                                <button @click="removeFile(index)" type="button" class=" text-white mr-2 font-semibold h-full text-sm"><i class="mdi mdi-close p-2"></i></button>
                                <!-- Vista previa -->
                                <div v-if="file.preview" class="mt-2 w-full h-16">
                                <div v-if="file.file.type.includes('image')" class="flex items-center  w-full h-full rounded-md">
                                    <i  class="mdi mdi-image text-6xl text-white "></i>
                                    <p class="text-sm text-white font-semibold ml-2">{{ file.file.name }}</p>
                                </div>
                                <div v-else class="flex items-center  w-full h-full rounded-md  text-wrap ">
                                    <i  class="mdi mdi-file-pdf-box text-6xl text-white "></i>
                                    <p class="text-sm text-white font-semibold ml-2 ">{{ file.file.name }}</p>
                                </div>
                                </div>
                            </div>
                            </div>
                    </nav>
                    <nav class="flex justify-center space-x-2 w-full">
                        <div class="flex flex-col justify-start items-start text-start w-full">
                        <InputLabel for="property_id" value="Select property" />
                        <select id="property_id" v-model="selectedPropertyId" class="w-full rounded-lg border-gray-300 text-black">
                            <option disabled value="">Please select one</option>
                            <option v-for="property in properties" :key="property.id" :value="property.id" class="text-black">
                            {{ property.street }}, {{ property.city }}, {{ property.state }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.property_id" />
                        </div>
                        <div class="flex flex-col justify-start items-start text-start w-full">
                        <InputLabel for="tenant_user_id" value="Select tenant" />
                        <select id="tenant_user_id" v-model="form.tenant_user_id" class="w-full rounded-lg border-gray-300 text-black">
                            <option disabled value="">Please select one</option>
                            <option v-for="tenant in tenants" :key="tenant.id" :value="tenant.id" class="text-black">
                            {{ tenant.first_name }} {{ tenant.last_name }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.tenant_user_id" />
                        </div>
                    </nav>
                    <nav class="flex justify-center space-x-2 w-full">
                        <div class="flex flex-col justify-start items-start text-start w-full">
                        <InputLabel for="start_date" value="Start Date" />
                        <TextInput
                            id="start_date"
                            type="date"
                            class="w-full rounded-lg border-gray-300 text-black"
                            v-model="form.start_date"
                            required
                            autofocus
                            autocomplete="start_date"
                        />
                        <InputError class="mt-2" :message="form.errors.start_date" />
                        </div>
                        <div class="flex flex-col justify-start items-start text-start w-full">
                        <InputLabel for="end_date" value="End Date" /> 
                        <TextInput
                            id="end_date"
                            type="date"
                            class="w-full rounded-lg border-gray-300 text-black"
                            v-model="form.end_date"
                            required
                            autofocus
                            autocomplete="end_date"
                        />
                        <InputError class="mt-2" :message="form.errors.end_date" />
                        </div>
                    </nav>
                    <div class="flex flex-col justify-start items-start text-start">
                        <InputLabel for="rental_amount" value="Rental Amount" />
                        <input
                        id="rental_amount"
                        type="number"
                        class="mt-1 block w-full rounded-lg border-gray-300 text-black"
                        v-model="form.rental_amount"
                        required
                        autofocus
                        autocomplete="rental_amount"
                        @input="form.rental_amount = String($event.target.value)"
                        />
                        <InputError class="mt-2" :message="form.errors.rental_amount" />
                    </div>
                    <div class="flex items-center justify-between pb-8">
                    <CustomButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Create Contract
                    </CustomButton>
                    </div>
                    </form>
                </div>
              </nav>
              <nav class="block bg-gray-100 rounded-lg shadow-lg p-2 h-[600px]">
                <div class="block justify-between items-center p-2">
                    <div class="flex justify-center text-center items-center">
                        <h1 class="text-lg font-bold w-full flex">Preview</h1> 
                        <Link href="/all-contracts" class="w-full">
                            <CustomButton> Manage Contracts</CustomButton>
                        </Link>
                    </div>
                    <nav class="flex justify-center text-sm w-full text-gray-500">
                        <button @click="filter('all')" class="p-2 flex justify-center text-center items-center group rounded-lg">
                            <i class="mdi mdi-circle pr-2 text-blue-700 group-hover:text-blue-500"></i>
                            <p class="text-xs font-bold text-blue-700 group-hover:text-black hover:underline underline-offset-2">All</p>
                        </button>
                        <button @click="filter('Active')" class="p-1 flex justify-center text-center items-center group rounded-lg">
                            <i class="mdi mdi-circle pr-2 text-green-700 group-hover:text-green-500"></i>
                            <p class="text-xs font-bold text-green-700 group-hover:text-black hover:underline underline-offset-2">Active</p>
                        </button>
                        <button @click="filter('Pending Renewal')" class="p-1 flex justify-center text-center items-center group rounded-lg">
                            <i class="mdi mdi-circle px-2 text-orange-800"></i>
                            <p class="text-xs font-bold text-yellow-700 group-hover:text-black hover:underline underline-offset-2">Pending Renewal</p>
                        </button>
                        <button @click="filter('Terminated')" class="p-2 flex justify-center text-center items-center group rounded-lg">
                            <i class="mdi mdi-circle p-2 text-red-500"></i>
                            <p class="text-xs font-bold text-red-500 group-hover:text-black hover:underline underline-offset-2">Terminated</p>
                        </button>
                    </nav>

                </div>
                <div class="block justify-center text-start p-2  overflow-y-auto h-[480px] ">
                    <div v-if="(contracts.length > 0 && filteredContracts.length > 0)">
                        <div v-for="(contract, index) in filteredContracts" :key="contract.id" class="bg-white rounded-lg shadow-lg p-2 m-2">
                          <nav class="block sm:flex justify-between py-2">
                            <p class="text-sm justify-start text-start items-start font-bold w-full px-4 py-1 rounded-md">{{ contract.property.property_code }}</p>  
                            <p :class="{
                                        'text-sm justify-end text-end items-end font-bold w-full px-4 py-1 rounded-md':true,
                                        'bg-gradient-to-l from-green-500 to-white from-10%': contract.status == 'Active',
                                        'bg-gradient-to-l from-yellow-500 to-white from-10%': contract.status == 'Pending Renewal',
                                        'bg-gradient-to-l from-red-500 to-white from-10%': contract.status == 'Terminated'
                                        }"> {{ contract.status }}</p>
                          </nav>
                          <nav class="block sm:flex justify-between py-2 px-4">
                                <h1 class="text-sm text-wrap text-gray-500 font-black"> {{ contract.property.street }} {{ contract.property.number }}, {{ contract.property.city }} {{ contract.property.state }}</h1>
                                <h1 class="text-sm text-pretty text-primary-black font-black"> {{ contract.rental_amount }}</h1>
                            </nav>
                        <div class="block sm:flex justify-start w-full items-center text-center px-2"> <span class="text-sm font-bold text-start  px-2">Tenant:</span><p class="text-xs"> {{ contract.tenant_user.first_name }} {{ contract.tenant_user.last_name }}</p></div>

                            <div class="grid grid-cols-3 md:grid-cols-3 gap-2 justify-center items-center text-center py-2">
                                <div class="items-center">
                                    <Link :href="`/contracts-details/${contract.id}`">
                                    <CustomButton>Details</CustomButton>
                                    </Link>
                                </div>
                                <div class="items-center">
                                    <span class="text-sm font-bold text-start ">Start Date:</span><p class="text-xs"> {{ contract.start_date }}</p>
                                </div>
                                <div class="items-center">
                                    <span class="text-sm font-bold text-start ">End Date:</span><p class="text-xs"> {{ contract.end_date }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else :class="{
                        'w-full flex justify-center':true,
                        }">
                        <p class="text-sm text-gray-500 font-bold">No contracts found</p>
                    </div>
                </div>
                
              </nav>
              
            </section>
            <Notification class="absolute bottom-4 right-4"
                v-if="notification.show"
                :type="notification.type"
                :title="notification.title"
                :message="notification.message"
                @close="closeNotification"
                />
        </DashboardLayout>
</template>

