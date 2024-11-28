<script>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import CustomButton from '@/Components/CustomButton.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';

export default {
    components: {
        InputError,
        InputLabel,
        CustomButton,
    },
    methods: {
        async submitDocuments() {
            const formData = new FormData();

            // Agregar documentos al formData
            this.form.contract_files.forEach((fileObj) => {
                formData.append('document_path', fileObj.file);
                formData.append('document_type', 'Something'); // Por defecto
                formData.append('application_id', 1); // Por defecto
                formData.append('upload_date', new Date().toISOString());
                formData.append('document_status', 'Active'); // Por defecto
            });

            try {
                const response = await axios.post('/api/properties/document-application', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                });
                alert('Documentos subidos exitosamente');
                // Limpia los documentos del formulario
                this.form.contract_files = "";
            } catch (error) {
                console.error('Error al subir los documentos:', error.response?.data || error.message);
            }
        }
    },
    setup() {
        const user = usePage().props.auth.user;

        const form = useForm({
            application_id: "1", // Valor predeterminado
            document_type: "Something", // Valor predeterminado
            document_path: "", // Ruta del documento cargado (simulado con la vista previa)
            upload_date: new Date().toISOString().split("T")[0], // Fecha actual (formato YYYY-MM-DD)
            document_status: "Active", // Estado predeterminado
            contract_files: [], // Archivos cargados
        });

        const handleFileUpload = (event) => {
            const files = event.target.files;
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();
                reader.onload = (e) => {
                    form.contract_files.push({
                        file: file,
                        preview: e.target.result, // Simula la ruta del archivo
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
            form.contract_files.forEach((fileObj, index) => {
                formData.append(`documents[${index}][application_id]`, form.application_id);
                formData.append(`documents[${index}][document_type]`, form.document_type);
                formData.append(`documents[${index}][document_status]`, form.document_status);
                formData.append(`documents[${index}][upload_date]`, form.upload_date);
                formData.append(`documents[${index}][document_path]`, fileObj.preview); // Vista previa como simulación
                formData.append(`documents[${index}][file]`, fileObj.file); // Archivo real
            });

            try {
                const response = await axios.post('/api/properties/document-application', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                });
                console.log('Documentos enviados:', formData);
                alert('Documentos enviados exitosamente');
            } catch (error) {
                console.error('Error al enviar los documentos', error.response?.data);
            }
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
        <form @submit.prevent="submitForm" class="mt-2 space-y-4 bg-gray-100 p-8 rounded-lg">
            <nav :class="{
                'grid gap-4 w-full': true,
                'grid-cols-1': form.contract_files.length == 0,
                'grid-cols-1 sm:grid-cols-2': form.contract_files.length > 0,
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
                    <InputError class="mt-2" :message="form.errors.contract_file" />
                </div>
                <!-- Mostrar archivos cargados con vista previa -->
                <div :class="{
                    'flex-wrap justify-center items center gap-2 h-[200px] w-full p-4 overflow-y-scroll': true,
                    'hidden': form.contract_files.length == 0,
                    'flex': form.contract_files.length > 0,
                }">
                    <div v-for="(file, index) in form.contract_files" :key="index"
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
    </div>
</template>