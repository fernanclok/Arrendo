<script>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import CustomButton from '@/Components/CustomButton.vue';
import Checkbox from '@/Components/Checkbox.vue';
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

    const form = useForm({
      property_id: '',
      tenant_user_id: '',
      start_date: '',
      end_date: '',
      rental_amount: '1000', // Inicializar como cadena
      contract_files: [], // Inicializar como array
      owner_user_id: user.id,
      status: 'active',
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

    const submitForm = () => {
      const formData = new FormData();
      form.contract_files.forEach(fileObj => {
        formData.append('contract_files[]', fileObj.file);
      });
      form.post(route('contracts.store'), {
        data: formData,
        onSuccess: () => {
          console.log('Contrato creado exitosamente');
        },
        onError: (errors) => {
          console.log(errors);
        },
      });
    };

    return {
      form,
      handleFileUpload,
      removeFile,
      submitForm,
    };
  },
};
</script>
<template>
  <div id="content" class="">
    <form @submit.prevent="form.post(route('contracts.store'))" class="mt-2 space-y-6">
      <nav class="flex justify-center space-x-2 w-full">
        <div class="w-full">
          <InputLabel for="contract_file" value="Upload Contract" />
          <InputLabel for="contract_file"  class="flex flex-col items-center justify-center w-full h-44 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 ">
            <div class="flex flex-col items-center justify-center pt-5 pb-6">
              <i class="mdi mdi-cloud-arrow-up-outline text-4xl text-gray-400"></i>
              <p class="text-sm text-gray-500 font-semibold">Click to upload</p>
              <p class="text-sm text-gray-500 font-semibold">PDF, PNG, JPG</p>
            </div>
            <input type="file" id="contract_file"  @change="handleFileUpload" class="hidden" accept=".png, .jpg, .jpeg, .pdf" multiple />
          </InputLabel>
          <InputError class="mt-2" :message="form.errors.contract_file" />
           <!-- Mostrar archivos cargados con vista previa -->
           <div class="flex flex-wrap justify-center items center gap-2">
              <div v-for="(file, index) in form.contract_files" :key="index" class="flex items-center justify-between w-full mt-2 p-2 bg-gray-100 rounded-lg">
                <button @click="removeFile(index)" type="button" class="text-white mr-2 bg-red-600 rounded-lg hover:bg-red-800  font-semibold h-full text-sm"><i class="mdi mdi-close p-2"></i></button>
                <div class="flex items-center">
                  <i class="mdi mdi-file-document-outline text-2xl text-gray-500"></i>
                  <p class="text-sm text-gray-500 font-semibold ml-2">{{ file.file.name }}</p>
                </div>
                <!-- Vista previa -->
                <div v-if="file.preview" class="mt-2 w-24 h-16">
                  <img v-if="file.file.type.includes('image')" :src="file.preview" alt="Preview" class="object-cover w-full h-full rounded-md" />
                <i v-else-if="file.file.type === 'application/pdf'" class="mdi mdi-file-pdf-box text-6xl text-red-500 "></i>
                </div>
              </div>
            </div>
           
        </div>
      </nav>
      <nav class="flex justify-center space-x-2 w-full">
        <div class="flex flex-col justify-start items-start text-start w-full">
          <InputLabel for="property_id" value="Select property" />
          <select id="property_id" v-model="form.property_id" class="w-full rounded-lg border-gray-300 text-black">
            <option v-for="property in properties" :key="property.id" :value="property.id" class="text-black">
              {{ property.title }}
            </option>
          </select>
          <InputError class="mt-2" :message="form.errors.property_id" />
        </div>
        <div class="flex flex-col justify-start items-start text-start w-full">
          <InputLabel for="tenant_user_id" value="Select tenant" />
          <select id="tenant_user_id" v-model="form.tenant_user_id" class="w-full rounded-lg border-gray-300 text-black">
            <option v-for="tenant in tenants" :key="tenant.id" :value="tenant.id" class="text-black">
              {{ tenant.name }}
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
          class="mt-1 block w-full"
          v-model="form.rental_amount"
          required
          autofocus
          autocomplete="rental_amount"
          @input="form.rental_amount = String($event.target.value)"
        />
        <InputError class="mt-2" :message="form.errors.rental_amount" />
      </div>
      <div class="flex flex-col justify-start items-start text-start">
        <InputLabel for="checkFiles" value="Did you upload this files?" />
        <Checkbox> </Checkbox>
      </div>
      <div class="flex items-center justify-end mt-4">
      <CustomButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
        Create Contract
      </CustomButton>
    </div>
    </form>
    
  </div>
</template>