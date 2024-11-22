<script>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm, usePage} from '@inertiajs/vue3';
import CustomButton from '@/Components/CustomButton.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';

export default {
  components: {
    InputError,
    InputLabel,
    TextInput,
    CustomButton,
    Checkbox,
  },
  setup() {
    const user = usePage().props.auth.user;
    const tenants = ref([]);
    const properties = ref([]);
    const getTenants = async () => {
      try {
        const response = await axios.get('/api/contracts/user_tenant');
        tenants.value = response.data;
      } catch (error) {
        console.error(error);
      }
    };
   const getProperties = () => {
            axios.get('/api/properties', {
                params: {
                    user_id: user.id
                }
            })
                .then(response => {
                    properties.value = response.data;
                })
                .catch(error => {
                    console.error(error);
                });
        };
    onMounted(() => {
      getTenants();
      getProperties();
    });

    const form = useForm({
      property_id: '',
      tenant_user_id: '',
      start_date: '',
      end_date: '',
      rental_amount: '2500', // Inicializar como cadena
      contract_files: [], // Inicializar como array
      owner_user_id: user.id,
      status: 'Active',
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
        reader.readAsDataURL(file); // Convertir a base64
      }
    };

    const removeFile = (index) => {
      form.contract_files.splice(index, 1);
    };

    const submitForm = async () => {
      const formData = new FormData();
      form.contract_files.forEach(fileObj => {
        formData.append('contract_files[]', fileObj.file);
      });
      formData.append('property_id', form.property_id);
      formData.append('tenant_user_id', form.tenant_user_id);
      formData.append('start_date', form.start_date);
      formData.append('end_date', form.end_date);
      formData.append('rental_amount', form.rental_amount);
      formData.append('owner_user_id', form.owner_user_id);
      formData.append('status', form.status);

      try {
        const response = await axios.post('/api/contracts/create', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });
        console.log('Contrato creado exitosamente', response.data);
      } catch (error) {
        console.error('Error al crear el contrato', error.response.data);
      }
    };

    return {
      form,
      handleFileUpload,
      removeFile,
      tenants,
      properties,
      submitForm,
    };
  },
};
</script>
<template>
  <div id="content" class="">
    <form @submit.prevent="submitForm" class="mt-2 space-y-4 bg-gray-100 p-8 rounded-lg">
      <nav class="grid grid-cols-1 sm:grid-cols-2 gap-4 w-full">
        <div class="w-full">
          <InputLabel for="contract_file"  class="flex flex-col items-center justify-center w-full h-[200px] border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 ">
            <div class="flex flex-col items-center justify-center pt-5 pb-6">
              <i class="mdi mdi-cloud-arrow-up-outline text-4xl text-gray-400"></i>
              <p class="text-sm text-gray-500 font-semibold">Click to upload</p>
              <p class="text-sm text-gray-500 font-semibold">PDF, PNG, JPG</p>
            </div>
            <input type="file" id="contract_file"  @change="handleFileUpload" class="hidden" accept=".png, .jpg, .jpeg, .pdf" multiple />
          </InputLabel>
          <InputError class="mt-2" :message="form.errors.contract_file" />         
        </div>
         <!-- Mostrar archivos cargados con vista previa -->
         <div class="flex flex-wrap justify-center items center gap-2 h-[200px] p-4 overflow-y-scroll">
              <div v-for="(file, index) in form.contract_files" :key="index" class="flex items-center justify-between w-full mt-2 p-2 bg-primary rounded-lg">
                <button @click="removeFile(index)" type="button" class=" text-white mr-2 font-semibold h-full text-sm"><i class="mdi mdi-close p-2"></i></button>
                <!-- Vista previa -->
                <div v-if="file.preview" class="mt-2 w-full h-16">
                  <div v-if="file.file.type.includes('image')" class="flex items-center  w-full h-full rounded-md">
                    <i  class="mdi mdi-image text-6xl text-white "></i>
                    <p class="text-sm text-white font-semibold ml-2">{{ file.file.name }}</p>
                  </div>
                  <div v-else class="flex items-center  w-full h-full rounded-md">
                    <i  class="mdi mdi-file-pdf-box text-6xl text-white "></i>
                    <p class="text-sm text-white font-semibold ml-2">{{ file.file.name }}</p>
                  </div>
                </div>
              </div>
            </div>
      </nav>
      <nav class="flex justify-center space-x-2 w-full">
        <div class="flex flex-col justify-start items-start text-start w-full">
          <InputLabel for="property_id" value="Select property" />
          <select id="property_id" v-model="form.property_id" class="w-full rounded-lg border-gray-300 text-black">
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
        <!-- <div class="flex flex-col justify-start items-start text-start space-y-2">
        <InputLabel for="checkFiles" value="Did you upload this files?" />
          <div class="flex justify-center items-center text-center space-x-2"><Checkbox :value="true"  aria-required="true"/><p class="text-sm text-gray-500">Contract as PDF</p></div>
          <div class="flex justify-center items-center text-center space-x-2"><Checkbox :value="true" aria-required="true"/><p class="text-sm text-gray-500">Tenant ID</p></div>
          <div class="flex justify-center items-center text-center space-x-2"><Checkbox :value="true"  aria-required="true"/><p class="text-sm text-gray-500">Aval ID</p></div>
      </div> -->
      <CustomButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
        Create Contract
      </CustomButton>
    </div>
    </form>
    
  </div>
</template>