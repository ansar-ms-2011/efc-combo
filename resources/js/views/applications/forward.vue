<template>
    <div class="px-3">
        <div class="max-w-7xl mx-auto bg-white rounded-lg shadow border p-6">

            <ViewForm :hide-print-button="true" @loaded="onViewFormLoaded" />
            <div class="border-t-2 p-1" v-if="viewFormLoaded">
                <form v-if="viewFormLoaded" class="space-y-6 mt-2" @submit.prevent="handleSubmit">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <!-- Forward To -->
                                <div v-if="canForward()">
                                    <label class="block text-sm font-medium mb-1">
                                        Action <span class="text-red-500">*</span>
                                    </label>

                                    <!-- use radio button for forward and object -->
                                    <div class="flex gap-6">
                                        <label class="flex items-center gap-2 text-green-700">
                                            <input type="radio" v-model="form.action" value="forward"
                                                class="form-radio">
                                            <span
                                                v-if="(store.user?.role === 'DEO' || store.user?.role === 'Center In-charge')">
                                                <span v-if="appCurrentStatus==='pending'">Forward to Assistant Commissioner</span>
                                                <span v-if="appCurrentStatus==='objected'">Restore</span>
                                            </span>
                                            <span
                                                v-else-if="store.user?.role === 'AC' || store.user?.role === 'ACR'">Forward to District Commissioner
                                            </span>
                                            <span v-if="store.user?.role === 'DC'">Approve</span>
                                        </label>

                                        <label class="flex items-center gap-2 text-red-700" v-if="canObject()">
                                            <input type="radio" v-model="form.action" value="objected"
                                                class="form-radio">
                                            <!-- <span>Not Approved</span> -->
                                            <span>Not Approved / Object</span>
                                        </label>
                                        <p v-if="errors.action" class="text-red-500 text-xs">
                                            {{ errors.action[0] }}
                                        </p>
                                    </div>
                                </div>
                                <!-- View Documents Button -->
                                <div v-if="viewFormLoaded && canViewDocuments()">
                                    <button @click.stop="showDocumentsModal = true" type="button"
                                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded shadow flex items-center gap-2">
                                        <i class="fa fa-file-alt"></i>
                                        <span v-if="['AC', 'ACR', 'DC'].includes(store.user.role)">Verify attached
                                            Documents</span>
                                        <span v-else>View Attached Documents</span>
                                    </button>
                                </div>
                            </div>
                            <!-- Remarks -->
                            <div>
                                <label class="block text-sm font-medium mb-2">Remarks</label>
                                <textarea v-model="form.remarks" placeholder="Remarks" class="form-input w-full mb-1"
                                    rows="3"></textarea>
                                <p v-if="errors.remarks" class="text-red-500 text-xs">
                                    {{ errors.remarks[0] }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" :disabled="isSubmitting" @click="showConfirmDialog"
                            class="px-6 py-2 bg-green-600 hover:bg-green-700 disabled:border-gray-100 text-white rounded shadow">
                            <span v-if="!isSubmitting">Save</span>
                            <span v-else>
                                <i class="fa fa-spinner fa-spin"></i> Saving...
                            </span>
                        </button>
                        <router-link to="/applications/all"
                            class="px-6 py-2 bg-red-100 hover:bg-red-500 text-red-600 hover:text-white rounded shadow">
                            Cancel
                        </router-link>
                    </div>
                </form>
            </div>
        </div>

        <!-- Documents Modal -->
        <div v-if="showDocumentsModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4"
            @click.self="showDocumentsModal = false">
            <div class="bg-white rounded-lg shadow-xl overflow-hidden flex flex-col ">
                <div class="flex justify-between items-center px-4 py-2 border-b bg-gray-300  flex-shrink-0">
                    <h3 class="text-lg font-semibold">Application Documents</h3>
                    <button @click="showDocumentsModal = false" class="hover:text-gray-400 text-2xl">
                        &times;
                    </button>
                </div>

                <div class="flex-1 overflow-auto p-4">
                    <div v-if="documents.length === 0" class="text-center py-8">
                        <i class="fa-solid fa-folder-open text-6xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600">No documents available</p>
                    </div>

                    <div v-else :class="documents.length < 4
                        ? 'flex  justify-center gap-4'
                        : 'grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 h-[60vh]'">

                        <div v-for="document in documents" :key="document.id"
                            class="border rounded-lg  transition-all duration-200 w-full">


                            <div class="w-full bg-gray-100 cursor-pointer flex items-center justify-center mb-3 rounded relative group"
                                @click="openDocument(document)">

                                <img v-if="isImageFile(document.original_name)"
                                    :src="getDocumentURL(document.file_path)"
                                    class="w-full h-60 object-cover rounded-t-lg border-2 border-transparent group-hover:border-blue-500 transition group-hover:opacity-40"
                                    alt="Document Image" />

                                <div v-if="isImageFile(document.original_name)"
                                    class="absolute inset-0 flex items-center justify-center opacity-1 group-hover:opacity-100 transition">
                                    <div class="bg-white/70 p-2 rounded-full">
                                        <IconEye class="text-gray-700" />
                                    </div>
                                </div>

                                <!-- PDF -->
                                <div v-else-if="document.original_name?.toLowerCase().endsWith('.pdf')"
                                    class="flex flex-col items-center">
                                    <i class="fa-solid fa-file-pdf text-red-600 text-4xl mb-1"></i>
                                    <span class="text-xs font-bold text-red-600">PDF</span>
                                </div>

                                <!-- Other -->
                                <div v-else class="flex flex-col items-center text-gray-400">
                                    <i class="fa-solid fa-file text-4xl mb-1"></i>
                                    <span class="text-xs font-bold">FILE</span>
                                </div>
                            </div>
                            <h4 class="font-bold text-sm mx-1 text-gray-900 truncate mb-1">{{ document.name
                            }}</h4>
                            <h4 class="font-bold text-sm mx-1 text-gray-900 truncate mb-1 font-nastaleeq">{{
                                document.urdu_name
                                }}</h4>
                            <p class="text-xs text-gray-500 truncate">{{ document.document_type }}</p>
                            <div class="mt-1  px-1 space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold"
                                        :class="document.ac_acr_verified ? 'text-green-600' : 'text-gray-400'">
                                        AC/ACR: {{ document.ac_acr_verified ? '✓' : '✗' }}
                                    </span>
                                    <span class="text-xs font-semibold"
                                        :class="document.dc_verified ? 'text-green-600' : 'text-gray-400'">
                                        DC: {{ document.dc_verified ? '✓' : '✗' }}
                                    </span>

                                </div>
                                <div class="flex items-center justify-center">
                                    <label v-if="canVerifyDocuments()"
                                        class="flex items-center justify-end cursor-pointer gap-1" @click.stop>
                                        <span>Verify:</span>
                                        <input type="checkbox" v-model="selectedDocuments" :value="document.id"
                                            class="hidden">
                                        <span
                                            class="w-5 h-5 border-2 border-blue-600 rounded flex items-center justify-center"
                                            :class="{ 'bg-blue-600': selectedDocuments.includes(document.id) }">
                                            <i v-if="selectedDocuments.includes(document.id)"
                                                class="fa-solid fa-check text-white text-xs"></i>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center p-4 border-t flex-shrink-0 justify-center gap-4">
                    <!-- Count - left side -->
                    <span v-if="store?.user?.role === 'AC' || store?.user?.role === 'DC'" class=" text-sm font-semibold"
                        :class="selectedCount > 0 ? 'text-green-600' : 'text-gray-400'">
                        Verified documents {{ selectedCount }} out of {{ totalDocuments }}
                    </span>


                    <!-- OK Button - center -->
                    <div class="flex justify-center flex-1">
                        <button @click="showDocumentsModal = false"
                            class="px-4 py-2 bg-primary text-white rounded hover:bg-blue-800 w-32">
                            OK
                        </button>
                    </div>
                    <!-- ✅ Check All -->
                    <label v-if="canVerifyDocuments()" class="flex items-center gap-2 cursor-pointer">
                        <span class="text-sm font-medium">Verify All</span>
                        <input type="checkbox" :checked="isAllSelected" @change="toggleCheckAll">
                    </label>
                </div>
            </div>
        </div>

        <!-- Single Document Viewer -->
        <div v-if="showDocumentViewer && currentDocument"
            class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-50 p-4"
            @click.self="showDocumentViewer = false">
            <div class="bg-white rounded-lg shadow-xl overflow-y-auto">
                <div class="flex justify-between items-center px-4 py-2 border-b bg-gray-200 ">
                    <h3 class="text-lg font-semibold">{{ currentDocument.original_name }}</h3>
                    <button @click="showDocumentViewer = false" class="hover:text-gray-400 text-2xl">
                        &times;
                    </button>
                </div>
                <div class="overflow-auto  p-2 min-w-[70vw] max-w-[400px] max-h-[600px]">
                    <div v-if="currentDocument.original_name?.toLowerCase().endsWith('.pdf')" class="w-full">
                        <iframe :src="getDocumentURL(currentDocument.file_path)" class="w-full h-full border-0"
                            title="PDF Viewer"></iframe>
                    </div>
                    <div v-else-if="isImageFile(currentDocument.original_name)" class="flex justify-center">
                        <img :src="getDocumentURL(currentDocument.file_path)" :alt="currentDocument.original_name"
                            class="max-w-full  object-contain" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { useRoute } from 'vue-router';
import { useAppStore } from '@/stores/index';
import ViewForm from '../viewform/domicile/viewform.vue';
import IconEye from '@/components/icon/icon-eye.vue';

const store = useAppStore();
const route = useRoute();
const appCurrentStatus = ref('');
const viewFormLoaded = ref(false);
const showDocumentsModal = ref(false);
const showDocumentViewer = ref(false);
const documents = ref([]);
const appObject = ref(null);
const currentDocument = ref(null);
const isSubmitting = ref(false);
const errors = ref([]);
const selectedDocuments = ref([]);

watch(selectedDocuments, () => {
    console.log('Selected documents:', selectedDocuments.value);
}, { immediate: true });


const canViewDocuments = () => {
    if (!store.user || !store.user.roles) return false;
    const allowedRoles = ['Super Admin', 'AC', 'ACR', 'DC', 'DEO', 'Center In-charge'];
    return store.user.roles.some(role => allowedRoles.includes(role.name));
};

const canVerifyDocuments = () => {
    if (!store.user || !store.user.roles) return false;
    const allowedRoles = ['AC', 'ACR', 'DC'];
    return store.user.roles.some(role => allowedRoles.includes(role.name));
};

const form = ref({
    application_id: route.params.id,
    action: '',
    remarks: ''
});

// This will be called when ViewForm emits 'loaded'
const onViewFormLoaded = () => {
    viewFormLoaded.value = true;
    fetchDocuments();
};

const fetchDocuments = async () => {
    try {
        // Use the show endpoint instead of documents endpoint to get properly formatted URLs
        const response = await axios.get(`/api/applications/${route.params.id}`);
        if (response.data.success && response.data.data.application.documents) {
            appObject.value = response.data.data.application;
            documents.value = response.data.data.application.documents.filter(doc => doc.app_doc_id > 0);
            appCurrentStatus.value = response.data.data.application.current_status;
            console.log('Fetched documents:', documents.value);
        }
    } catch (error) {
        console.error('Error fetching documents:', error);
    }
};

const openDocument = (document) => {
    currentDocument.value = document;
    showDocumentViewer.value = true;
};

const isImageFile = (fileName) => {
    if (!fileName) return false;
    const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
    const extension = fileName.split('.').pop().toLowerCase();
    return imageExtensions.includes(extension);
};

const getDocumentURL = (filePath) => {
    if (!filePath) return '';
    // If already a complete URL, return as is
    if (filePath.startsWith('http://') || filePath.startsWith('https://')) return filePath;
    // Backend returns /storage/ URLs from Storage::url()
    // Prepend the backend base URL
    const backendURL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000';
    return `${backendURL}${filePath}`;
};

const getRedirectPath = () => {
    const role = store?.user?.role;
    const redirectMap = {
        'DEO': '/applications/pending',
        'Center In-charge': '/applications/pending',
        'AC': '/applications/submitted',
        'ACR': '/applications/submitted',
        'DC': '/applications/verified',
        'Super Admin': '/applications/all'
    };
    return redirectMap[role] ?? '/applications/all';
};

const canForward = () => {
    const role = store?.user?.role;
    const appStatus = appCurrentStatus.value;

    return (['DEO', 'Center In-charge'].includes(role) && ['pending', 'approved', 'ready_for_delivery', 'objected'].includes(appStatus)) ||
        (['AC', 'ACR'].includes(role) && appStatus === 'submitted') ||
        (role === 'DC' && appStatus === 'verified');
};

const canObject = () => {
    const role = store?.user?.role;
    const appStatus = appCurrentStatus.value;

    return (['AC', 'ACR'].includes(role) && appStatus === 'submitted') ||
        (role === 'DC' && appStatus === 'verified');
};

// show confirm dialog if form.action is objected
const showConfirmDialog = () => {
    errors.value = [];
    if (form.value.action === 'objected') {
        Swal.fire({
            icon: 'warning',
            title: 'Are you sure?',
            text: 'This action will reject the application',
            showCancelButton: true,
            confirmButtonText: 'Yes, Reject it!',
            cancelButtonText: 'No, cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                handleSubmit();
            }
        });
    } else {
        handleSubmit();
    }
};

const handleSubmit = async () => {
    if (selectedDocuments.value.length < documents.value.length && form.value.action === 'forward' && canVerifyDocuments()) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Please verify all documents before forwarding'
        });
        return;
    }
    try {
        isSubmitting.value = true;

        // Use FormData for file upload
        const formData = new FormData();
        formData.append('app_uuid', appObject.value.uuid);
        formData.append('action', form.value.action);
        formData.append('remarks', form.value.remarks || '');
        const response = await axios.post('/api/forward-application', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        Swal.fire({
            icon: 'success',
            title: 'Successful!',
            text: 'Action saved successfully!',
            padding: '2em',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        }).then(() => {
            window.location.href = getRedirectPath();
        });
    } catch (error) {
        console.error(error);

        // Backend validation error (422) or server error (500)
        if ((error.response?.status === 422 || error.response?.status === 500) && error.response?.data) {
            errors.value = error.response.data.errors || {};

            const message =
                error.response.data.errors?.esign?.[0] ||
                error.response.data.message ||
                'Validation or Server Error';


            Swal.fire({
                icon: 'warning',            // yellow warning
                title: error.response?.status === 422 ? 'Validation Warning' : 'Missing',
                text: message
            });

        } else {
            // Generic network / other errors
            const message = error.response?.data?.message || 'Something went wrong';
            Swal.fire({
                icon: 'error',   // red error
                title: 'Error',
                text: message
            });
        }
    } finally {
        isSubmitting.value = false;
    }
};

const totalDocuments = computed(() => documents.value.length);
const selectedCount = computed(() => selectedDocuments.value.length);
onMounted(() => {
    // getApplicationStatus();
});

const isAllSelected = computed(() => {
    return documents.value.length > 0 &&
        selectedDocuments.value.length === documents.value.length;
});

const toggleCheckAll = () => {
    if (isAllSelected.value) {
        selectedDocuments.value = [];
    } else {
        selectedDocuments.value = documents.value.map(doc => doc.id);
    }
};
</script>
