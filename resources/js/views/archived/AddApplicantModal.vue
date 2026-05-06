<template>
    <BaseDialog :model-value="show" @update:model-value="$emit('close')" :title="isEditMode ? 'Edit Applicant' : 'Add New Applicant'" maxWidth="w-[100%]">
        <template #header-right>
            <button @click="closeModal" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 focus:outline-none">
                <span class="text-2xl">&times;</span>
            </button>
        </template>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 overflow-hidden modal-container " :style="{ minHeight: 'calc(100vh - 200px)' }">
            <!--Form Fields -->
            <div class="lg:col-span-4 ml-5 relative overflow-y-auto pr-2 custom-scrollbar">
                <div class="grid grid-cols-2 gap-x-4 gap-y-3 ">
                    <div class="space-y-1">
                        <label class="block text-sm  text-gray-700">Certificate Type</label>
                        <div class="flex gap-2 h-10">
                            <label class="flex-1 flex items-center cursor-pointer bg-white px-2 rounded border hover:border-blue-500 transition shadow-sm">
                                <input type="radio" ref="domicileRef" v-model="localFormData.identity_type" value="domicile" class="w-4 h-4 text-blue-600" />
                                <span class="ml-2 text-xs font-medium">Domicile</span>
                            </label>
                            <label class="flex-1 flex items-center cursor-pointer bg-white px-2 rounded border hover:border-blue-500 transition shadow-sm">
                                <input type="radio" v-model="localFormData.identity_type" value="state" class="w-4 h-4 text-blue-600" />
                                <span class="ml-2 text-xs font-medium">State</span>
                            </label>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-sm font-medium  text-gray-700">Tehsil</label>
                        <select ref="tehsilRef" v-model="localFormData.tehsil_id" class="form-input w-full h-10  text-gray-500">
                            <option value="">Select Tehsil</option>

                            <option  v-for="t in filteredTehsils" :key="t.id" :value="t.id">
                                {{ t.name }}
                            </option>
                        </select>
                        <p v-if="allErrors.tehsil_id" class="text-red-500 text-[10px] italic font-semibold">
                            {{ Array.isArray(allErrors.tehsil_id) ? allErrors.tehsil_id[0] : allErrors.tehsil_id }}
                        </p>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-sm  font-medium text-gray-700">Name</label>
                        <input
                            type="text"
                            ref="nameInputRef"
                            v-model="localFormData.name"
                            :class="{ 'border-red-500': allErrors.name }"
                            class="form-input w-full h-10"
                            placeholder=" Enter Name "
                            @keydown.enter.prevent="focusNextField('fatherNameInputRef')"
                        />
                        <p v-if="allErrors.name" class="text-red-500 text-[10px] italic font-semibold">
                            {{ Array.isArray(allErrors.name) ? allErrors.name[0] : allErrors.name }}
                        </p>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Father Name</label>
                        <input
                            type="text"
                            ref="fatherNameInputRef"
                            v-model="localFormData.father_name"
                            :class="{ 'border-red-500': allErrors.father_name }"
                            class="form-input w-full h-10"
                            placeholder=" Enter Father Name  "
                            @keydown.enter.prevent="handleNameEnter"
                        />
                        <p v-if="allErrors.father_name" class="text-red-500 text-[10px] italic font-semibold">
                            {{ Array.isArray(allErrors.father_name) ? allErrors.father_name[0] : allErrors.father_name }}
                        </p>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Refugee Status</label>
                        <div class="flex items-center h-10 border rounded px-3 bg-white shadow-sm">
                            <input
                                ref="refugeeCheckRef"
                                type="checkbox"
                                id="is_refugee"
                                v-model="localFormData.is_refugee"
                                class="w-4 h-4 text-blue-600 rounded"
                            />
                            <label for="is_refugee" class="ml-2 text-sm font-medium text-gray-700 select-none cursor-pointer">Is Refugee</label>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">{{ localFormData.is_refugee ? 'Refugee Number' : 'CNIC' }}</label>
                        <template v-if="!localFormData.is_refugee">
                            <input
                                type="text"
                                ref="cnicInputRef"
                                v-model="localFormData.cnic"
                                :class="{ 'border-red-500': allErrors.cnic }"
                                class="form-input w-full h-10"
                                placeholder="12345-1234567-1"
                                @input="handleCNICInput"
                                maxlength="15"
                                @keydown.enter.prevent="focusNextField('misalNoRef')"
                            />
                            <p v-if="allErrors.cnic" class="text-red-500 text-[10px] italic font-semibold">
                                {{ Array.isArray(allErrors.cnic) ? allErrors.cnic[0] : allErrors.cnic }}
                            </p>
                        </template>
                        <template v-else>
                            <input
                                type="text"
                                ref="refugeeNumberRef"
                                v-model="localFormData.refugee_number"
                                :class="{ 'border-red-500': allErrors.refugee_number }"
                                class="form-input w-full h-10"
                                placeholder="Refugee No"
                                @input="lookupApplicant(localFormData.refugee_number)"
                                @keydown.enter.prevent="focusNextField('misalNoRef')"
                            />
                            <p v-if="allErrors.refugee_number" class="text-red-500 text-[10px] italic font-semibold">
                                {{ Array.isArray(allErrors.refugee_number) ? allErrors.refugee_number[0] : allErrors.refugee_number }}
                            </p>
                        </template>
                    </div>
                    <div class="space-y-1 col-span-1">
                        <label class="block text-sm font-medium text-gray-700">Misal No</label>
                        <input
                            type="text"
                            ref="misalNoRef"
                            v-model="localFormData.misal_no"
                            :class="{ 'border-red-500': allErrors.misal_no }"
                            class="form-input w-full h-10"
                            placeholder="Enter Misal No"
                            @keydown.enter.prevent="saveApplicant"
                        />
                        <p v-if="allErrors.misal_no" class="text-red-500 text-[10px] italic font-semibold">
                            {{ Array.isArray(allErrors.misal_no) ? allErrors.misal_no[0] : allErrors.misal_no }}
                        </p>
                        
                    </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Issue Date</label>
                    <VueDatePicker
                        v-model="localFormData.issue_date"
                        placeholder="DD-MM-YYYY"
                        auto-apply
                          class=" h-12"
                        model-type="yyyy-MM-dd"
                        :formats="{ input: 'dd-MM-yyyy' }"
                        :enable-time-picker="false"
                        text-input
                                position="bottom"

                       
                    />
                    <p v-if="allErrors.issue_date" class="text-red-500 text-[10px] italic font-semibold">
                        {{ Array.isArray(allErrors.issue_date) ? allErrors.issue_date[0] : allErrors.issue_date }}
                    </p>
                </div>
                </div>
            </div>

            <!-- Scanned Documents -->
            <div class="lg:col-span-8 border-l lg:pl-6 flex flex-col h-full overflow-hidden">
                <div class="flex justify-between items-center mb-4 flex-shrink-0">
                    <h3 class="text-lg font-semibold">Scanned Documents</h3>
                    <div class="flex gap-2">
                        <button
                            class="flex flex-col items-center justify-center px-2 py-1 rounded-lg text-xs font-medium border bg-white hover:bg-gray-50"
                            type="button"
                            @click="triggerFileUpload"
                        >
                            <i class="fa fa-upload text-lg mb-1"></i>
                            <span>Upload</span>
                        </button>
                        <input type="file" ref="fileInputRef" class="hidden" @change="handleManualUpload" />

                        <button
                            ref="imageBtnRef"
                            class="flex flex-col items-center justify-center px-2 py-1 rounded-lg text-xs font-medium border bg-white hover:bg-gray-50"
                            type="button"
                            @click="handlePhotoScan('image')"
                            @keydown.enter.prevent="handlePhotoScan('image')"
                        >
                            <IconScanImage class="w-6 h-6 mb-1" />
                            <span>Image</span>
                        </button>
                        <button
                            ref="pdfBtnRef"
                            class="flex flex-col items-center justify-center px-2 py-1 rounded-lg text-xs font-medium border bg-white hover:bg-gray-50"
                            type="button"
                            @click="handlePhotoScan('pdf')"
                            @keydown.enter.prevent="handlePhotoScan('pdf')"
                        >
                            <IconScanPdf class="w-6 h-6 mb-1" />
                            <span>PDF</span>
                        </button>
                    </div>
                </div>

                <!-- Viewer Area -->
                <div
                    class="flex-1 border-2 border-dashed rounded-lg bg-gray-50 overflow-hidden flex flex-col"
                    :class="scannedDocuments.length > 0 ? 'border-blue-300' : 'items-center justify-center text-gray-400'"
                >
                    <div v-if="scannedDocuments.length === 0" class="text-center p-8">
                        <i class="fa fa-file-o text-3xl mb-2 opacity-50"></i>
                        <p>No documents scanned yet</p>
                    </div>

                    <div v-else class="p-4 space-y-6 overflow-y-auto h-full bg-gray-200 custom-scrollbar">
                        <div
                            v-for="(doc, index) in scannedDocuments"
                            :key="index"
                            class="bg-white shadow-lg border rounded-md mx-auto w-full flex flex-col shrink-0"
                        >
                            <div class="bg-gray-800 text-white px-4 py-1.5 flex justify-between items-center">
                                <span class="text-[10px] font-bold uppercase tracking-widest">
                                    <i class="fa mr-2" :class="doc.isPDF ? 'fa-file-pdf text-red-400' : 'fa-image text-blue-400'"></i>
                                    {{ doc.name }}
                                </span>
                                <button
                                    @click="scannedDocuments.splice(index, 1)"
                                    class="bg-red-500 hover:bg-red-600 text-white w-6 h-6 rounded-full flex items-center justify-center shadow-md"
                                >
                                    <i class="fa fa-times text-xs"></i>
                                </button>
                            </div>

                            <div class="w-full bg-white overflow-y-auto custom-scrollbar" style="height: 490px; position: relative">
                                <iframe v-if="doc.isPDF" :src="doc.data + '#toolbar=0&navpanes=0'" class="w-full h-full border-none shadow-inner"> </iframe>

                                <img
                                    v-else
                                    :src="doc.data"
                                    class="w-full block"
                                    style="height: auto !important; max-height: none !important; width: 100%; display: block"
                                    alt="Scanned Document"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="apiErrors.documents && scannedDocuments.length === 0" class="mt-2 p-2 border rounded-md shadow-sm">
                    <p class="text-red-700 text-xs font-bold flex items-center">
                        <i class="fa fa-exclamation-triangle mr-2"></i>
                        {{ Array.isArray(apiErrors.documents) ? apiErrors.documents[0] : apiErrors.documents }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Buttons Section -->
        <template #footer>
            <div class="flex gap-3 justify-end w-full">
                <button
                    @click="saveApplicant"
                    :disabled="saving"
                    ref="saveBtnRef"
                    class="px-8 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded flex items-center gap-2 shadow-lg transition font-medium"
                >
                    <i v-if="saving" class="fa fa-spinner fa-spin"></i>
                    <i v-else class="fa fa-save"></i>
                    {{ saving ? 'Saving...' : 'Save Applicant' }}
                </button>
                <button @click="closeModal" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded transition font-medium">Cancel</button>
            </div>
        </template>
    </BaseDialog>
</template>

<script setup>
    import { ref, reactive, computed, watch, onMounted, nextTick } from 'vue';
    import Swal from 'sweetalert2';
    import BaseDialog from '@/components/BaseDialog.vue';
    import IconScanImage from '@/components/icon/icon-scan-image.vue';
    import IconScanPdf from '@/components/icon/icon-scan-pdf.vue';
    import { useAppStore } from '@/stores/index.ts';
    const store = useAppStore();

    // Focus Refs
    const domicileRef = ref(null);
    const nameInputRef = ref(null);
    const fatherNameInputRef = ref(null);
    const cnicInputRef = ref(null);
    const refugeeCheckRef = ref(null);
    const refugeeNumberRef = ref(null);
    const tehsilRef = ref(null);
    const misalNoRef = ref(null);
    const issueDateRef = ref(null);

    const filteredTehsils = computed(() => {
        const userDistrictId = store.user?.district_id;
        if (!userDistrictId) return [];

        let tehsilsList = [];

        store.regions.forEach((region) => {
            const district = region.districts?.find((d) => Number(d.id) === Number(userDistrictId));
            if (district && district.tehsils) {
                tehsilsList = district.tehsils;
            }
        });

        return tehsilsList;
    });

    const props = defineProps({
        show: { type: Boolean, default: false },
        applicant: { type: Object, default: null },
        allRecords: { type: Array, default: () => [] },
        apiErrors: { type: Object, default: () => ({}) },
        tehsils: { type: Array, default: () => [] },
    });

    const emit = defineEmits(['close', 'saved']);

    // State
    const isEditMode = computed(() => !!props.applicant);
    const saving = ref(false);
    const scannerAvailable = ref(false);
    const scanInProgress = ref(false);
    const scannedDocuments = ref([]);
    const currentDocumentIndex = ref(0);

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
        misal_no: '',
        issue_date: '',
    });

    const errors = reactive({ name: '', cnic: '', refugee_number: '' });

    const allErrors = computed(() => {
        return { ...errors, ...props.apiErrors };
    });

    // Methods
    const focusNextField = (refName) => {
        const refs = {
            domicileRef,
            nameInputRef,
            fatherNameInputRef,
            cnicInputRef,
            refugeeCheckRef,
            refugeeNumberRef,
            fromYearRef,
            toYearRef,
            tehsilRef,
            misalNoRef,
            issueDateRef,
        };
        nextTick(() => {
            refs[refName]?.value?.focus();
        });
    };

    const handleNameEnter = () => {
        if (localFormData.is_refugee) focusNextField('refugeeNumberRef');
        else focusNextField('cnicInputRef');
    };

    const formatCNICInput = (event) => {
        let value = event.target.value.replace(/[^\d]/g, '');
        if (value.length > 5) value = value.substring(0, 5) + '-' + value.substring(5);
        if (value.length > 13) value = value.substring(0, 13) + '-' + value.substring(13);
        localFormData.cnic = value;
    };

    //Form Reset Function
    const resetForm = () => {
        Object.assign(localFormData, {
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
            misal_no: '',
            issue_date: '',
        });
        scannedDocuments.value = [];
        currentDocumentIndex.value = 0;
        Object.keys(errors).forEach((key) => (errors[key] = ''));
    };

    // Applicant Lookup
    const lookupApplicant = (identity) => {
        if (!identity || identity.length < 3 || isEditMode.value) return;

        const cleanInput = String(identity).replace(/-/g, '').trim().toLowerCase(); //remove dashes

        const existing = props.allRecords.find((item) => {
            const dbIdentity = String(item.applicant?.identity_number || '')
                .replace(/-/g, '')
                .trim()
                .toLowerCase();
            return dbIdentity === cleanInput;
        });

        if (existing) {
            localFormData.name = existing.applicant?.full_name || '';
            localFormData.father_name = existing.applicant?.father_name || '';
            localFormData.tehsil_id = existing.applicant?.tehsil_id || '';
            localFormData.misal_no = existing.misal_no || '';
            localFormData.identity_type = existing.type || 'domicile';
            const dbType = existing.applicant?.identity_type?.toLowerCase();
            localFormData.is_refugee = dbType === 'refugee';
            if (localFormData.is_refugee) {
                localFormData.refugee_number = existing.applicant?.identity_number || '';
            } else {
                localFormData.cnic = existing.applicant?.identity_number || '';
            }
            console.log('Data Auto-filled from DB:', existing);
        }
    };
    const handleCNICInput = (event) => {
        formatCNICInput(event);
        lookupApplicant(localFormData.cnic);
    };

    watch(
        () => props.show,
        async (isShown) => {
            if (isShown) {
                if (props.applicant) {
                    const newVal = props.applicant;
                    localFormData.id = newVal.id;
                    localFormData.name = newVal.applicant?.full_name || '';
                    localFormData.father_name = newVal.applicant?.father_name || '';
                    localFormData.tehsil_id = newVal.applicant?.tehsil_id || '';
                    localFormData.misal_no = newVal.misal_no || '';
                    localFormData.issue_date = newVal.issue_date
                        ? newVal.issue_date.substring(0, 10) : '';
                    const identityType = newVal.applicant?.identity_type?.toLowerCase();
                    if (identityType === 'refugee') {
                        localFormData.is_refugee = true;
                        localFormData.identity_type = 'domicile';
                        localFormData.refugee_number = newVal.applicant?.identity_number || '';
                    } else {
                        localFormData.is_refugee = false;
                        localFormData.identity_type = newVal.type || 'domicile';
                        localFormData.cnic = newVal.applicant?.identity_number || '';
                    }

                    if (newVal.pdf_path && newVal.pdf_path !== 'pending') {
                        scannedDocuments.value = [
                            {
                                id: newVal.id,
                                data: `http://localhost:8000/storage/${newVal.pdf_path}`,
                                isPDF: newVal.pdf_path.toLowerCase().endsWith('.pdf'),
                                name: newVal.pdf_path.split('/').pop(),
                                isExisting: true,
                            },
                        ];
                    }
                } else {
                    resetForm();
                }
            }
        },
        { immediate: true },
    );

    const saveApplicant = async () => {
        if (saving.value) return;
        saving.value = true;
        try {
            const payload = {
                ...localFormData,
                name: localFormData.name,
                father_name: localFormData.father_name,
                is_refugee: localFormData.is_refugee ? 1 : 0,
                tehsil_id: localFormData.tehsil_id,
                misal_no: localFormData.misal_no,
                issue_date: localFormData.issue_date,
                documents: scannedDocuments.value.map((doc) => ({
                    file_name: doc.name,
                    file_type: doc.isPDF ? 'pdf' : 'jpg',
                    file_path: doc.data,
                })),
            };
            emit('saved', { payload, isEditMode: isEditMode.value, id: localFormData.id });
        } catch (err) {
            Swal.fire('Error', 'An error occurred during save', 'error');
        } finally {
            saving.value = false;
        }
    };

    // Scanner Logic
    onMounted(async () => {
        if (store.regions.length === 0) {
            await store.loadDropdowns();
        }
        if (!window.scanner) {
            const script = document.createElement('script');
            script.src = 'https://asprise.com/scannerjs/scanner.js';
            script.crossOrigin = 'anonymous';
            script.onload = () => {
                scannerAvailable.value = true;
            };
            document.head.appendChild(script);
        } else {
            scannerAvailable.value = true;
        }
    });

    const handlePhotoScan = (scanType) => {
        if (!scannerAvailable.value || scanInProgress.value) return;
        scanInProgress.value = true;

        window.scanner.scan(
            (successful, mesg, response) => {
                scanInProgress.value = false;
                if (!successful) return;

                const pureBase64 = extractBase64(response);
                if (pureBase64) {
                    const isPDF = scanType === 'pdf';
                    const finalData = `data:${isPDF ? 'application/pdf' : 'image/jpeg'};base64,${pureBase64}`;

                    scannedDocuments.value.push({
                        id: Date.now(),
                        data: finalData,
                        isPDF: isPDF,
                        name: `Scanned_${Date.now()}`,
                    });
                    console.log('Image Loaded Successfully');
                    nextTick(() => domicileRef.value?.focus());
                }
            },
            {
                modal: true,
                output_settings: [
                    {
                        type: 'return-base64',
                        format: scanType === 'pdf' ? 'pdf' : 'jpg',
                    },
                ],
            },
        );
    };

    const extractBase64 = (resp) => {
        try {
            const obj = typeof resp === 'string' ? JSON.parse(resp) : resp;
            let rawData = obj?.output?.[0]?.base64 || obj?.output?.[0]?.result?.[0] || obj?.output?.[0]?.data;

            if (rawData) {
                if (rawData.includes(',')) {
                    return rawData.split(',')[1];
                }
                return rawData.replace(/\r?\n/g, '').trim();
            }
            return null;
        } catch (e) {
            console.error('Extract Error:', e);
            return null;
        }
    };
    const closeModal = () => {
        emit('close');
    };

    //  File Upload
    const fileInputRef = ref(null);
    const triggerFileUpload = () => {
        fileInputRef.value.click();
    };

    const handleManualUpload = (event) => {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (e) => {                //reader.onload: file read successfully
            const isPDF = file.type === 'application/pdf';
            const base64Data = e.target.result;
            scannedDocuments.value = [
                {
                    id: Date.now(),
                    data: base64Data,
                    isPDF: isPDF,
                    name: file.name,
                },
            ];
            event.target.value = '';
            console.log('File Uploaded Successfully');
            nextTick(() => domicileRef.value?.focus());
        };
        reader.readAsDataURL(file); // file convert base64
    };
</script>

<style scoped>
    @import '@/assets/css/urdu-font.css';

    .custom-scrollbar::-webkit-scrollbar {
        width: 5px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
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
    :deep(.base-dialog-content) {
        overflow: hidden !important;
    }
    
</style>
