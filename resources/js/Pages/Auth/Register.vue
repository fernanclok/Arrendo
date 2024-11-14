<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/CustomButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    password: '',
    password_confirmation: '',
    phone: '',
    role: '',
    emergency_contact_name: '',
    emergency_contact_phone: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <InputLabel for="first_name" value="First Name" />

                <TextInput
                    id="first_name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.first_name"
                    required
                    autofocus
                    autocomplete="given-name"
                />

                <InputError class="mt-2" :message="form.errors.first_name" />
            </div>

            <div>
                <InputLabel for="last_name" value="Last Name" />

                <TextInput
                    id="last_name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.last_name"
                    required
                    autocomplete="family-name"
                />

                <InputError class="mt-2" :message="form.errors.last_name" />
            </div>

            <div class="col-span-1 md:col-span-2">
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div>
                <InputLabel for="password_confirmation" value="Confirm Password" />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div>
                <InputLabel for="phone" value="Phone" />

                <TextInput
                    id="phone"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.phone"
                    required
                    autocomplete="tel"
                />

                <InputError class="mt-2" :message="form.errors.phone" />
            </div>

            <div>
                <InputLabel for="role" value="Role" />

                <select
                    id="role"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-700 focus:ring focus:ring-green-700 focus:ring-opacity-50"
                    v-model="form.role"
                    required
                >
                    <option value="" disabled>Select a role</option>
                    <option value="Owner">Owner</option>
                    <option value="Tenant">Tenant</option>
                </select>

                <InputError class="mt-2" :message="form.errors.role" />
            </div>

            <div>
                <InputLabel for="emergency_contact_name" value="Emergency Contact Name" />

                <TextInput
                    id="emergency_contact_name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.emergency_contact_name"
                    required
                />

                <InputError class="mt-2" :message="form.errors.emergency_contact_name" />
            </div>

            <div>
                <InputLabel for="emergency_contact_phone" value="Emergency Contact Phone" />

                <TextInput
                    id="emergency_contact_phone"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.emergency_contact_phone"
                    required
                    autocomplete="tel"
                />

                <InputError class="mt-2" :message="form.errors.emergency_contact_phone" />
            </div>

            <div class="col-span-1 md:col-span-2 flex items-center justify-end mt-4">
                <Link
                    :href="route('login')"
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Already registered?
                </Link>

                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Register
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>