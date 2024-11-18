<script>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import CustomButton from '@/Components/CustomButton.vue';
import axios from 'axios';

export default {
  components: {
    InputError,
    InputLabel,
    TextInput,
    CustomButton,
  },
  setup() {
    const user = usePage().props.auth.user;

    const form = useForm({
      property_id: '',
      tenant_user_id: '',
      start_date: '',
      end_date: '',
      rental_amount: '1000', // Inicializar como cadena
      status: 'active',
    });

    const properties = [
      { id: 1, title: 'Apartamento moderno', location: 'Centro de la ciudad', price: '10,000', type: 'Departamento' },
      { id: 2, title: 'Casa familiar', location: 'Zona residencial', price: '15,000', type: 'Casa' },
      { id: 3, title: 'Loft industrial', location: 'Distrito artístico', price: '8,500', type: 'Departamento' },
    ];

    const tenants = [
      { id: 1, name: 'María García' },
      { id: 2, name: 'Juan Pérez' },
      { id: 3, name: 'Ana Martínez' },
      { id: 4, name: 'Carlos López' },
      { id: 5, name: 'Sofía Rodríguez' },
      { id: 6, name: 'Pedro Sánchez' },
    ];

    const submitForm = () => {
      form.post(route('contracts.store'), {
        onSuccess: () => {
          // Manejar el éxito, por ejemplo, redirigir o mostrar un mensaje
          console.log('Contrato creado exitosamente');
        },
        onError: (errors) => {
          // Manejar los errores
          console.log(errors);
        },
      });
    };

    return {
      form,
      properties,
      tenants,
      submitForm,
    };
  },
};
</script>
<template>
  <div id="content" class="">
    <form @submit.prevent="form.post(route('contracts.store'))" class="mt-6 space-y-6">
      <nav class="flex justify-center space-x-2 w-full">
        <div class="w-full">
          <InputLabel for="property_id" value="Select property" />
          <select id="property_id" v-model="form.property_id" class="w-full rounded-lg border-gray-300 text-black">
            <option v-for="property in properties" :key="property.id" :value="property.id" class="text-black">
              {{ property.title }}
            </option>
          </select>
          <InputError class="mt-2" :message="form.errors.property_id" />
        </div>
        <div class="w-full">
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
        <div class="w-full">
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
        <div class="w-full">
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
      <div>
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
      <div class="flex items-center justify-end mt-4">
      <CustomButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
        Create Contract
      </CustomButton>
    </div>
    </form>
    
  </div>
</template>