<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import axios from 'axios';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import CustomButton from '@/Components/CustomButton.vue';
import Notification from '@/Components/NotificationCard.vue'; 
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';
import { useForm } from '@inertiajs/vue3';
import { Head, usePage } from '@inertiajs/vue3';

const user = usePage().props.auth.user;
const invoices = ref([]);
const isEvidencesopen = ref(false);
const selectedInvoice = ref(null);
const currentPage = ref(1);
const invoicesperPage = 8;
const paginatedInvoices = ref([]);


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
// Crear contrato
const form = useForm({
  evidence_file: [],
  
});
// Muestra los recibos por usuario
const fetchInvoices = async () => {
    try {
        const response = await axios.get('/api/Invoices/tenatn-invoices', {
            params: {
                user_id: user.id,
            },
        });
        invoices.value = response.data;
    } catch (error) {
        console.error('Error fetching invoices:', error);
    }
};

const generatePDF = async (invoiceId) => {
    try {
        const response = await axios.get(`/api/Invoices/invoices/${invoiceId}/pdf`, {
            responseType: 'blob',
        });

        // URL del Archivo descargado
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `invoice-${invoiceId}.pdf`);
        document.body.appendChild(link);
        link.click();
        // Actualizar la propiedad pdfGenerated
        const invoice = invoices.value.find(inv => inv.invoice_id === invoiceId);
        if (invoice) {
            invoice.pdfGenerated = true;
        }

    } catch (error) {
        console.error('Error generating PDF:', error);
    }
};
// Abrir modal de terminación
const openEvidencesModal = (invoiceID) => {
    selectedInvoice.value = invoiceID;
    isEvidencesopen.value = true;
};

const closeEvidencesModal = () => {
  isEvidencesopen.value = false;
    selectedInvoice.value = null;
};
// function to upload evidence
const handleFileUpload = (event) => {
  const files = event.target.files;
  for (let i = 0; i < files.length; i++) {
    const file = files[i];
    const reader = new FileReader();
    reader.onload = (e) => {
      form.evidence_file.push({
        file: file,
        preview: e.target.result
      });
    };
    reader.readAsDataURL(file);
  }
};

const removeFile = (index) => {
  form.evidence_file.splice(index, 1);
};

// Función para manejar el envío del formulario
const submitForm = async (inv) => {


const formData = new FormData();
form.evidence_file.forEach(fileObj => {
  formData.append('evidence_file[]', fileObj.file);
});

try {
    await axios.post(`/api/Invoices/invoices/${selectedInvoice.value}/update-evidence`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });
    showNotification({
      title: 'Success',
      message: 'Evidences sent successfully! Wait for the approval',
      type: 'success'
    });
    // Cerrar el modal
    fetchInvoices();
    closeEvidencesModal();
    // Limpiar el formulario
    form.evidence_file = [];
  } catch (error) {
    console.error('Error uploading evidence:', error);
  }
};

// Paginación
const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
    }
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
    }
};

const totalPages = computed(() => {
    return Math.ceil(invoices.value.length / invoicesperPage);
});

// Actualizar la lista de contratos paginados cuando cambie la página actual, los contratos o el criterio de búsqueda
watch([invoices, currentPage], () => {
    const start = (currentPage.value - 1) * invoicesperPage;
    const end = currentPage.value * invoicesperPage;
    paginatedInvoices.value = invoices.value.slice(start, end);
});


onMounted(fetchInvoices);
</script>

<template>
  <Head title="My Invoices" />
  <DashboardLayout>
    <div class="shadow-lg rounded-lg overflow-hidden  mx-4 md:mx-10">
      <table class="w-full table-fixed ">
        <thead>
          <tr class="bg-gray-100">
            <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">#</th>
            <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Date</th>
            <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Contract</th>
            <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Name</th>
            <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Issue Date</th>
            <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Total Amount</th>
            <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Payment Status</th>
            <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Actions</th>
            <th></th>
          </tr>
        </thead>
        <tbody class="bg-white overflow-auto-scroll">
          <tr v-for="(invoice, index) in paginatedInvoices" :key="invoice.invoice_id">
            <td class="py-4 px-6">{{ index + 1 }}</td>
            <td class="py-4 px-6">{{ invoice.invoice_date }}</td>
            <td class="py-4 px-6">{{ invoice.contract_code }}</td>
            <td class="py-4 px-6">{{ invoice.tenant_name }}</td>
            <td class="py-4 px-6 text-red-500">{{ invoice.issue_date }}</td>
            <td class="py-4 px-6">{{ invoice.invoice_total }}</td>
            <td class="py-4 px-6 text-gray-900 text-center"><span :class="{
              'bg-yellow-400 p-2 rounded-lg shadow-lg ': invoice.payment_status === 'Pending',
              'bg-green-400 p-2 rounded-lg shadow-lg ': invoice.payment_status === 'Paid',
            }">{{ invoice.payment_status }}</span></td>
            <td>
              <div v-if="!invoice.evidence" class="py-4 px-6 block sm:flex gap-3 w-full">
                <CustomButton @click="generatePDF(invoice.invoice_id)">
                  <i class="mdi mdi-file text-xs"> PDF</i>
                </CustomButton>
                <CustomButton @click="openEvidencesModal(invoice.invoice_id)">
                  <i class="mdi mdi-upload text-xs"> Upload Evidence</i>
                </CustomButton>
              </div>
              <div v-else-if="invoice.payment_status === 'Paid'" class="py-4 px-6 block sm:flex gap-3">
                <span class="bg-green-400 p-2 rounded-lg shadow-lg font-extrabold">Approved</span>
              </div>
              <div v-else class="py-4 px-6 block sm:flex gap-3">
                <span class="underline bg-gray-100 p-2 rounded-lg shadow-lg font-extrabold">Wait for approval</span>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      <div class="flex justify-between items-center">
        <!-- Botón de "Previous" -->
        <SecondaryButton
          @click="prevPage" 
          :disabled="currentPage === 1" 
          :class="{
            'px-6 py-3  rounded-b-lg font-medium': true, 
            'opacity-50 cursor-not-allowed': currentPage === 1
          }" 
          class="transition-all duration-200 ease-in-out hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          Previous
        </SecondaryButton>

        <!-- Contador de páginas -->
        <div class="flex space-x-2 items-center">
          <span class="text-gray-600 font-medium">Page {{ currentPage }} of {{ totalPages }}</span>
        </div>

        <!-- Botón de "Next" -->
        <SecondaryButton
          @click="nextPage" 
          :disabled="currentPage === totalPages" 
          class="px-6 py-3  rounded-r-lg font-medium transition-all duration-200 ease-in-out " 
          :class="{'opacity-50 cursor-not-allowed': currentPage === totalPages}"
        >
          Next
        </SecondaryButton>
      </div>
    </div>
    <!-- Modal para terminar contrato -->
    <Modal :show="isEvidencesopen" @close="closeEvidencesModal">
          <template #default>
            <nav class="p-8 bg-gray-100">
              <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-900">Upload Evidences</h1>
              </div>
              <form @submit.prevent="submitForm" class="mt-2 space-y-4 bg-gray-100 p-8 rounded-lg">
                    <nav :class="{
                        'grid gap-4 w-full':true,
                        'grid-cols-1': form.evidence_file.length == 0,
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
                        <InputError class="mt-2" :message="form.errors.evidence_file" />         
                        </div>
                        <!-- Mostrar archivos cargados con vista previa -->
                        <div :class="{
                        'flex-wrap justify-center items center gap-2 h-[200px] w-full p-4 overflow-y-scroll':true,
                        'hidden': form.evidence_file.length == 0,
                        'flex': form.evidence_file.length > 0,
                        }">
                            <div v-for="(file, index) in form.evidence_file" :key="index" class="flex items-center justify-between w-full mt-2 p-2 bg-primary rounded-lg overflow-hidden">
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
              <div class="flex justify-end items-center space-x-4 mt-6">
                  <SecondaryButton @click="closeEvidencesModal">Cancel</SecondaryButton>
                  <CustomButton type="submit">Submit</CustomButton>
                </div>
              </form>
            </nav>
          </template>
        </Modal>
        <Notification class="absolute bottom-4 right-4"
                v-if="notification.show"
                :type="notification.type"
                :title="notification.title"
                :message="notification.message"
                @close="closeNotification"
                />
  </DashboardLayout>
</template>
