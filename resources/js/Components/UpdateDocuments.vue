
<script>
import { usePage, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import CustomButton from '@/Components/CustomButton.vue';

export default {
    components: {
        InputError,
        InputLabel,
        CustomButton,
    },
    setup() {
        const user = usePage().props.auth.user;
        
        const form = useForm({
            application_files: []
        });

        const handleFileUpload = (event) => {
            const files = event.target.files;
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();
                reader.onload = (e) => {
                    form.application_files.push({
                        file: file,
                        preview: e.target.result
                    });
                };
                reader.readAsDataURL(file); // Convertir a base64
            }
        };

        const removeFile = (index) => {
            form.application_files.splice(index, 1);
        };

        const submitForm = async () => {
            const formData = new FormData();

            formData.append('tenant_user_id', user.id);
            
            form.application_files.forEach(fileObj => {
                formData.append('application_files[]', fileObj.file);
            });

            await axios.post('/api/properties/profile-documents', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            })
            .then((response) => {
                form.application_files = [];
            })
            .catch((error) => {
                console.error(error);
                console.log(formData);
            });
        };

        return {
            form,
            handleFileUpload,
            removeFile,
            submitForm,
        };
    },
}
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">Update Documents</h2>

            <p class="mt-1 text-sm text-gray-600">
                Here you can update your personal documents.
            </p>
        </header>
    </section>

    <div>
        <div id="content" class="">
            <form @submit.prevent="submitForm" class="mt-2 space-y-4 bg-gray-100 p-8 rounded-lg">
                <nav :class="{
                'grid gap-4 w-full': true,
                'grid-cols-1': form.application_files.length == 0,
                'grid-cols-1 sm:grid-cols-2': form.application_files.length > 0,
            }">
                    <div class="w-full">
                        <InputLabel for="contract_file"
                            class="flex flex-col items-center justify-center w-full h-[200px] border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 ">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i class="mdi mdi-cloud-arrow-up-outline text-4xl text-gray-400"></i>
                                <p class="text-sm text-gray-500 font-semibold">Click to upload</p>
                                <p class="text-sm text-gray-500 font-semibold">PDF, PNG, JPG</p>
                            </div>
                            <input type="file" id="contract_file" @change="handleFileUpload" class="hidden"
                                accept=".png, .jpg, .jpeg, .pdf" multiple />
                        </InputLabel>
                        <InputError class="mt-2" :message="form.errors.application_files" />
                    </div>
                    <!-- Mostrar archivos cargados con vista previa -->
                    <div :class="{
                    'flex-wrap justify-center items center gap-2 h-[200px] w-full p-4 overflow-y-scroll': true,
                    'hidden': form.application_files.length == 0,
                    'flex': form.application_files.length > 0,
                }">
                        <div v-for="(file, index) in form.application_files" :key="index"
                            class="flex items-center justify-between w-full mt-2 p-2 bg-primary rounded-lg overflow-hidden">
                            <button @click="removeFile(index)" type="button"
                                class=" text-white mr-2 font-semibold h-full text-sm"><i
                                    class="mdi mdi-close p-2"></i></button>
                            <!-- Vista previa -->
                            <div v-if="file.preview" class="mt-2 w-full h-16">
                                <div v-if="file.file.type.includes('image')"
                                    class="flex items-center  w-full h-full rounded-md">
                                    <i class="mdi mdi-image text-6xl text-white "></i>
                                    <p class="text-sm text-white font-semibold ml-2">{{ file.file.name }}</p>
                                </div>
                                <div v-else class="flex items-center  w-full h-full rounded-md  text-wrap ">
                                    <i class="mdi mdi-file-pdf-box text-6xl text-white "></i>
                                    <p class="text-sm text-white font-semibold ml-2 ">{{ file.file.name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>
            </form>
            <button v-if="form.application_files.length > 0" type="button"
                class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white transition-colors duration-200 bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm mt-4"
                @click="submitForm()">
                <i class="mr-2 mdi mdi-check"></i> Update Documents
            </button>
        </div>
    </div>
</template>
