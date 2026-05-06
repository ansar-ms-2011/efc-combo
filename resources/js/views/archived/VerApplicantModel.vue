<template>
    <BaseDialog :model-value="show" @update:model-value="$emit('close')" title="Verify Applicant Details" maxWidth="w-[100%]">
        <template #header-right>
            <button @click="closeModal" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 focus:outline-none">
                <span class="text-2xl">&times;</span>
            </button>
        </template>
        <div
            class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-[60vh] min-h-[500px] overflow-hidden modal-container"
            :style="{ minHeight: 'calc(100vh - 200px)' }"
        >
            <div class="lg:col-span-4 relative overflow-y-auto pr-2 custom-scrollbar space-y-4">
                <div class="grid grid-cols-2 gap-x-4 gap-y-4">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-500 uppercase">Certificate Type</label>
                        <div class="flex gap-2 h-10">
                            <label class="flex-1 flex items-center justify-center bg-gray-50 px-2 rounded border shadow-sm cursor-not-allowed opacity-70">
                                <input type="radio" v-model="localFormData.identity_type" value="domicile" disabled class="w-3 h-3 text-blue-600" />
                                <span class="ml-2 text-xs font-medium">Domicile</span>
                            </label>
                            <label class="flex-1 flex items-center justify-center bg-gray-50 px-2 rounded border shadow-sm cursor-not-allowed opacity-70">
                                <input type="radio" v-model="localFormData.identity_type" value="state" disabled class="w-3 h-3 text-blue-600" />
                                <span class="ml-2 text-xs font-medium">State</span>
                            </label>
                        </div>
                    </div>
                    <div class="space-y-1 col-span-2">
                        <label class="block text-sm font-nastaleeq text-gray-500">Tehsil</label>
                        <input
                            type="text"
                            :value="localFormData.tehsil_name"
                            readonly
                            class="form-input w-full h-10 bg-gray-100 font-nastaleeq text-sm px-4 text-green-800 border-gray-200"
                        />
                    </div>
                    <div class="space-y-1">
                        <label class="block text-sm font-bold font-nastaleeq text-gray-500 uppercase">Name </label>
                        <input type="text" v-model="localFormData.name" readonly class="form-input w-full h-10 bg-gray-100 cursor-not-allowed text-sm" />
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-500 font-nastaleeq uppercase"> Father Name </label>
                        <input type="text" v-model="localFormData.father_name" readonly class="form-input w-full h-10 bg-gray-100 cursor-not-allowed text-sm" />
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-500 uppercase">Refugee Status</label>
                        <div class="flex items-center h-10 border rounded px-3 bg-gray-50 opacity-70">
                            <input type="checkbox" v-model="localFormData.is_refugee" disabled class="w-4 h-4 text-blue-600" />
                            <label class="ml-2 text-sm font-medium text-gray-700">Is Refugee</label>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-500 uppercase">{{ localFormData.is_refugee ? 'Refugee Number' : 'CNIC Number' }}</label>
                        <input
                            type="text"
                            :value="localFormData.is_refugee ? localFormData.refugee_number : localFormData.cnic"
                            readonly
                            class="form-input w-full h-10 bg-gray-100 cursor-not-allowed text-sm font-mono"
                        />
                    </div>
                    <div class="space-y-1 col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase">Misal No </label>
                        <input
                            type="text"
                            v-model="localFormData.misal_no"
                            readonly
                            :class="isAlreadyVerified ? 'bg-gray-100 cursor-not-allowed' : 'bg-white border-blue-200 focus:border-blue-500'"
                            class="form-input w-full h-10 text-sm font-bold"
                            placeholder="Enter Misal No"
                        />
                    </div>
                    <div v-if="isRejecting" class="space-y-1 col-span-2">
                        <label class="block text-xs font-bold mb-1 text-gray-500">REJECTION REASON</label>
                        <textarea
                            v-model="verificationRemarks"
                            class="w-full p-2 border-2 border-red-400 rounded-md text-sm outline-none"
                            placeholder=" Enter Rejection Reason..."
                        ></textarea>
                    </div>
                </div>
            </div>

            <!-- Scanned Documents Viewer -->
            <div class="lg:col-span-8 border-l lg:pl-6 flex flex-col h-full overflow-hidden">
                <div class="flex justify-between items-center mb-4 flex-shrink-0">
                    <h3 class="text-lg font-semibold text-blue-800 tracking-tight">Scanned Document Attachment</h3>
                </div>

                <div class="flex-1 border-2 border-dashed rounded-lg overflow-hidden flex flex-col min-h-0 bg-gray-50 shadow-inner">
                    <div
                        v-if="props.applicant?.pdf_path && props.applicant.pdf_path !== 'pending'"
                        class="p-2 h-full flex flex-col overflow-y-auto custom-scrollbar"
                    >                        <div
                            class="bg-gray-800 text-white px-4 py-1.5 rounded-t-md flex justify-between items-center text-[10px] font-bold uppercase tracking-widest sticky top-0 z-10"
                        >
                            <span>
                                <i
                                    class="fa mr-1"
                                    :class="props.applicant.pdf_path.toLowerCase().endsWith('.pdf') ? 'fa-file-pdf text-red-400' : 'fa-image text-blue-400'"
                                ></i>
                                {{ props.applicant.pdf_path.split('/').pop() }}
                            </span>
                        </div>

                        <!-- Content Area -->
                        <div class="flex-1 bg-white border border-t-0 rounded-b-md shadow-sm overflow-y-auto relative custom-scrollbar" style="height: 550px">
                            <iframe
                                v-if="props.applicant.pdf_path.toLowerCase().endsWith('.pdf')"
                                :src="'http://localhost:8000/storage/' + props.applicant.pdf_path + '#toolbar=0&navpanes=0'"
                                class="w-full h-full border-none"
                            >
                            </iframe>

                            <div v-else class="w-full bg-gray-200">
                                <img
                                    :src="'http://localhost:8000/storage/' + props.applicant.pdf_path + '#toolbar=0&navpanes=0'"
                                    class="w-full block shadow-2xl mx-auto"
                                    style="height: auto !important; max-height: none !important"
                                    alt="Scanned Document"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="h-full flex flex-col items-center justify-center text-gray-400 p-8">
                        <div class="relative mb-4">
                            <i class="fa fa-file-o text-6xl opacity-20"></i>
                            <i class="fa fa-exclamation-triangle absolute bottom-0 right-0 text-2xl text-orange-400"></i>
                        </div>
                        <p class="font-semibold">No document attached yet</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Section -->
        <template #footer>
            <div class="flex flex-col w-full gap-3">
                <div class="flex gap-3 justify-end w-full">
                    <div v-if="currentStatus === 'verified'" class="flex items-center gap-2">
                        <div class="bg-green-100 text-green-700 px-6 py-2 rounded border font-bold"><i class="fa fa-check-double"></i> Already Verified</div>
                        <button @click="closeModal" class="px-6 py-2 bg-gray-200 rounded">Close</button>
                    </div>

                    <template v-else>
                        <button
                            @click="isRejecting ? (isRejecting = false) : closeModal()"
                            class="px-6 py-2 bg-gray-200 text-gray-700 rounded transition font-medium"
                        >
                            Cancel
                        </button>

                        <button
                            v-if="!isRejecting"
                            @click="isRejecting = true"
                            class="px-8 py-2 bg-red-100 text-red-600 border border-red-200 rounded hover:bg-red-600 hover:text-white transition font-medium"
                        >
                            Reject
                        </button>

                        <button
                            v-else
                            @click="handleVerify('rejected')"
                            :disabled="verifying"
                            class="px-8 py-2 bg-red-600 text-white rounded shadow transition"
                        >
                            Confirm Reject
                        </button>

                        <button
                            v-if="!isRejecting"
                            @click="handleVerify('verified')"
                            :disabled="verifying"
                            class="px-8 py-2 bg-green-600 hover:bg-green-700 text-white rounded flex items-center gap-2 shadow transition font-medium"
                        >
                            <i v-if="verifying" class="fa fa-spinner fa-spin"></i>
                            Verify Application
                        </button>
                    </template>
                </div>
            </div>
        </template>
    </BaseDialog>
</template>

<script setup>
    import { ref, reactive, watch, computed } from 'vue';
    import Swal from 'sweetalert2';
    import axios from 'axios';
    import BaseDialog from '@/components/BaseDialog.vue';
    const dynamicHeight = ref(window.innerHeight);

    const props = defineProps({
        show: { type: Boolean, default: false },
        applicant: { type: Object, default: null }, // DB Record
        tehsils: { type: Array, default: () => [] },
    });

    const emit = defineEmits(['close', 'verified']);

    const localFormData = reactive({
        id: null,
        name: '',
        father_name: '',
        cnic: '',
        is_refugee: false,
        refugee_number: '',
        from_year: '',
        to_year: '',
        identity_type: 'domicile',
        tehsil_id: '',
        tehsil_name: '',
        misal_no: '',
    });

    const scannedDocuments = ref([]);
    const verifying = ref(false);
    const isRejecting = ref(false);
    const verificationRemarks = ref('');
    const currentStatus = computed(() => props.applicant?.verification?.status);

    watch(
        () => props.applicant,
        (newVal) => {
            if (newVal) {
                localFormData.id = newVal.id;
                localFormData.name = newVal.applicant?.full_name || '';
                localFormData.father_name = newVal.applicant?.father_name || '';
                localFormData.cnic = newVal.applicant?.identity_number || '';
                localFormData.is_refugee = newVal.applicant?.identity_type === 'refugee';
                localFormData.identity_type = newVal.type || 'domicile';
                localFormData.tehsil_id = newVal.applicant?.tehsil_id || '';
                localFormData.tehsil_name = newVal.applicant?.tehsil?.name || 'N/A';

                localFormData.misal_no = newVal.misal_no || '';

                if (newVal.applicant?.refugee_details) {
                    localFormData.refugee_number = newVal.applicant.identity_number;
                }

                if (newVal.documents) {
                    scannedDocuments.value = newVal.documents.map((doc) => ({
                        id: doc.id,
                        data: doc.file_path,
                        isPDF: doc.file_type === 'pdf' || doc.file_path.endsWith('.pdf'),
                        name: doc.file_name,
                    }));
                }
            }
        },
        { immediate: true },
    );

    const handleVerify = async (status) => {
        if (status === 'rejected' && !verificationRemarks.value.trim()) {
            Swal.fire('Error', 'Enter Rejection reason!', 'error');
            return;
        }
        verifying.value = true;
        try {
            const response = await axios.post('/api/verify-application', {
                certificate_id: localFormData.id,
                status: status,
                remarks: verificationRemarks.value || 'Verified from portal',
                tehsil_id: localFormData.tehsil_id,
                misal_no: localFormData.misal_no,
                dataEnter_by: props.applicant?.data_entered_by ?? null,
            });

            if (response.data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.data.message,
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    showConfirmButton: false,
                });
                emit('verified');
                closeModal();
            }
        } catch (err) {
            const errorMsg = err.response?.data?.message || 'Verification failed. Please try again.';
            Swal.fire('Error', errorMsg, 'error');
        } finally {
            verifying.value = false;
        }
    };

    const closeModal = () => {
        emit('close');
    };

    const isAlreadyVerified = computed(() => !!currentStatus.value && currentStatus.value !== 'pending');
</script>

<style scoped>
    @import '@/assets/css/urdu-font.css';
    .fa-spinner {
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    .custom-scrollbar::-webkit-scrollbar {
        height: 5px;
        width: 5px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    input[readonly] {
        cursor: not-allowed;
    }
    :deep(.base-dialog-content) {
        overflow: hidden !important;
    }
</style>
