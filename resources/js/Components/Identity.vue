<script>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import CustomButton from '@/Components/CustomButton.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';

export default {
    props: {
        propertyId: {
            type: Number, // Define el tipo de la prop según tu necesidad
            required: true
        },
        appointmentId: {
            type: Number,
            required: true
        },
        appointments: {
            type: Array,
            required: true
        }
    },
    // mounted() {
    //     console.log('Property ID:', this.propertyId); // Funciona en el lifecycle hook
    // },
    components: {
        InputError,
        InputLabel,
        CustomButton,
    },
    methods: {
    },
    setup(props, { emit }) {
        const user = usePage().props.auth.user;
        const localAppointments = ref([...props.appointments]); // Copia inmutable

        function handleGetAppointments() {
            axios
                .get('/api/appointments', {
                    params: {
                        user_id: user.id,
                    },
                })
                .then((response) => {
                    props.appointments = response.data.map((appt) => ({
                        ...appt,
                        isOpen: false, // Inicializa 'isOpen' como false para cada cita
                    }));
                })
                .catch((error) => {
                    console.error(error);
                });
        }

        const form2 = useForm({
            property_id: props.propertyId,
            tenant_user_id: user.id,
            application_date: new Date().toISOString().split("T")[0], 
            status: "Pending"
        });

        // Formulario para manejar los archivos
        const form = useForm({
            application_files: [], // Inicializar como array
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
            const formData2 = new FormData();
            let applicationId = 0;
            let documentPath = '';

            // Preparar los datos para el primer POST
            formData2.append('property_id', form2.property_id);
            formData2.append('tenant_user_id', form2.tenant_user_id);
            formData2.append('application_date', form2.application_date);
            formData2.append('status', form2.status);

            try {
                // Primer POST: aplicación
                const response = await axios.post('api/properties/applicate', formData2, {
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });
                applicationId = response.data.application // Ver qué devuelve el servidor
            } catch (error) {
                console.error('Error al enviar la aplicación:', error.response.data);
                return; // Detener la ejecución si la aplicación falla
            }

            // Preparar los datos para el segundo POST (documentos)
            const formData = new FormData();
            form.application_files.forEach(fileObj => {
                formData.append('application_files[]', fileObj.file);
            });
            formData.append('application_id', applicationId); // Usar el ID de la aplicación obtenida previamente

            try {
                // Segundo POST: documentos
                const response = await axios.post('/api/properties/document-application', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
                console.log('Documentos enviados exitosamente');
                documentPath = response.data.Documents.document_path;
                // alert('Documentos y aplicación enviados con éxito');
            } catch (error) {
                console.error('Error al enviar los documentos:', error.response.data);
            }

            const newFormData = new FormData();
            newFormData.append('tenant_user_id', user.id);
            newFormData.append('document_path', documentPath);

            try {
                const response = await axios.post('/api/properties/pass-user-documents', newFormData, {
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });
                emit('close-modal');
                emit('refresh-appointments'); // Notifica al padre
                console.log('User document path updated');
            } catch (error) {
                console.log(error);
            }
            const newFormData2 = new FormData();
            newFormData2.append('appointment_id', props.appointmentId);
            newFormData2.append('status', 'Applicated');
            try {
                const response = await axios.put('/api/appointments/update', newFormData2, {
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });
                console.log('Appointment status updated');
            } catch (error) {
                console.log(error);
            }
        };

        return {
            form,
            form2,
            handleFileUpload,
            removeFile,
            submitForm,
            emit
        };
    }
};
</script>

<template>
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
                    <InputError class="mt-2" :message="form.errors.contract_file" />
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
        <div class="px-4 py-4  bg-opacity-50 sm:px-6 sm:flex sm:flex-row-reverse">
            <button type="button"
                class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white transition-colors duration-200 bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm"
                @click="submitForm()">
                <i class="mr-2 mdi mdi-check"></i> Apply to this Listing
            </button>
        </div>
    </div>
</template>