<script setup>
    import { computed, onMounted, reactive, ref, watch } from 'vue';
    import Swal from 'sweetalert2';
    import FingerPrint from '@/views/applications/components/FingerPrint.vue';
    import { ErrorMessage, Field, FieldArray, useFieldArray } from 'vee-validate';
    import CameraDialog from '@/views/applications/Dialogs/CameraDialog.vue';
    import ImagePreviewDialog from '@/views/applications/Dialogs/ImagePreviewDialog.vue';
    import IconScanImage from '@/components/icon/icon-scan-image.vue';
    import IconScanPdf from '@/components/icon/icon-scan-pdf.vue';
    import IconFileUpload from '@/components/icon/icon-file-upload.vue';
    import { storeToRefs } from 'pinia';
    import { useAppStore } from '@/stores/index.ts';

    const { replace } = useFieldArray('application.documents');

    const props = defineProps({
        values: {
            type: Object,
            required: true
        },
        setFieldValue: {
            type: Function,
            required: true
        }
    });

    const { requiredDocuments, genders } = storeToRefs(useAppStore());
    const showCameraModal = ref(false);
    const photoPreview = ref(props.values.application.personal_image || props.values.application.personal_image_file);
    const showFilePreviewModal = ref(false);
    const fileToPreview = ref(null);
    const fileInput = ref(null);
    const inputImageUpload = ref(null);
    const scannerAvailable = ref(false);
    const currentDocument = ref(null);
    const showDocumentModal = ref(false);
    const showAllDocumentsModal = ref(false);
    const scanInProgress = ref(false);
    const filteredDocuments = ref([]);
    const selectedDocument = ref(null);
    const showImagePreview = ref(false);
    const internalChanged = ref(false);

    const filterDocs = () => {
        const certificateType = props.values.application.certificate_type;
        const applicationTypeId = props.values.application.application_type_id;
        const requiredDocs = props.values.application.id ? props.values.application.documents : requiredDocuments.value;
        const reasonTypeId = props.values.application.duplicate_details?.reason_type_id;

        if (!certificateType || !applicationTypeId) return;
        const serviceType = applicationTypeId === 1 ? 'new' : 'duplicate';

        // Filter required documents
        let filteredDocs = requiredDocs
            ?.filter(doc =>
                (doc.service_name === certificateType || doc.service_name === 'both') &&
                (doc.service_type === serviceType || doc.service_type === 'both') &&
                (serviceType === 'new' || reasonTypeId == null || doc.reason_type_id === reasonTypeId)
            ) || [];
        internalChanged.value = true;
        replace(filteredDocs);
        filteredDocuments.value = filteredDocs;

        console.log('certificateType:', certificateType, 'applicationTypeId:', applicationTypeId, 'requiredDocs:', requiredDocs, 'reasonTypeId:', reasonTypeId, 'filtered Docs:', filteredDocs);
    };

    defineExpose({
        'filterDocs': filterDocs
    });

    const loadScannerJS = () => {
        const script = document.createElement('script');
        script.src = 'https://asprise.com/scannerjs/scanner.js';
        script.crossOrigin = 'anonymous';
        script.onload = () => {
            // console.log('Scanner.js loaded dynamically');
            scannerAvailable.value = true;
        };
        script.onerror = () => {
            console.error('Failed to load Scanner.js');
            alert('Failed to load scanner. Please check your internet connection and try again.');
        };
        document.head.appendChild(script);
    };

    onMounted(() => {
        loadScannerJS();
    });

    watch(scannerAvailable, (newVal) => {
        console.log('Scanner.js is now available:', newVal);
    });

    const genderName = computed(() => {
        return genders.value.find(g => g.id === props.values.applicant.gender_id)?.name || '';
    });

    const newDocument = reactive({
        type: '',
        file: null,
        fileName: '',
        previewURL: null,
        photocopy: false,
        image: false,
        original: false,
        uploadMethod: 'file',
        scanFormat: 'pdf',
        category: 'domicile' // 'domicile' | 'state'
    });

    const handlePhotoUpload = (file) => {
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                photoPreview.value = e.target?.result || null;
                props.setFieldValue('application.personal_image_file', e.target?.result || null);
            };
            reader.readAsDataURL(file);
        }
    };

    const openCamera = async () => {
        showCameraModal.value = true;
    };


    const savePhoto = async (base64) => {
        photoPreview.value = base64;
        props.setFieldValue('application.personal_image_file', base64);
    };

    const selectedDocIndex = ref(null);
    const selectFile = (document, index) => {
        selectedDocIndex.value = index;
        selectedDocument.value = document;
        selectedDocument.value.upload_method = 'manual';
        fileInput.value.click();
    };
    const scanDocument = (document, index, scanType) => {
        selectedDocIndex.value = index;
        selectedDocument.value = document;
        selectedDocument.value.upload_method = 'scanner';
        // Open your scanner/camera modal here
        handlePhotoScan(scanType);
    };

    const handleDocFileChange = (event) => {

        const file = event.target.files?.[0];
        if (!file) return;

        const reader = new FileReader();

        reader.onload = () => {
            // if (!selectedDocument.value) return
            props.setFieldValue(`application.documents[${selectedDocIndex.value}].new_file`, reader.result);
            props.setFieldValue(`application.documents[${selectedDocIndex.value}].original_name`, file.name);
            props.setFieldValue(`application.documents[${selectedDocIndex.value}].mime_type`, file.type);

            // ✅ Reset AFTER everything is done
            event.target.value = '';
            selectedDocIndex.value = null;
            selectedDocument.value = null;
        };

        reader.readAsDataURL(file);
    };

    const closeFilePreviewModal = () => {
        showFilePreviewModal.value = false;
        fileToPreview.value = null;
    };

    const handlePhotoScan = (scanType) => {
        if (!scannerAvailable.value) {
            alert('Scanner is not available. Please wait for scanner to load or check your internet connection.');
            return;
        }

        if (scanInProgress.value) {
            alert('Scan already in progress. Please wait.');
            return;
        }

        scanInProgress.value = true;

        const scanCallback = function(successful, mesg, response) {
            scanInProgress.value = false;
            console.log('=== SCAN CALLBACK START ===');
            console.log('Successful:', successful);
            console.log('Message:', mesg);

            if (!successful) {
                console.error('Scan Failed: ' + mesg);
                if (mesg !== 'User cancelled scan') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Scan Failed',
                        text: mesg,
                        timer: 3000
                    });
                }
                return;
            }

            try {
                let parsedResponse = response;
                if (typeof response === 'string') {
                    try {
                        parsedResponse = JSON.parse(response);
                        console.log('Parsed JSON response:', parsedResponse);
                    } catch (e) {
                        console.log('Response is not JSON, treating as base64 string');
                        const isPDF = self.newDocument.scanFormat === 'pdf';
                        const mimeType = isPDF ? 'application/pdf' : 'image/jpeg';
                        const dataURL = `data:${mimeType};base64,${response}`;
                        processScannedDocument(dataURL, 0, isPDF);
                        return;
                    }
                }

                if (parsedResponse) {
                    const findBase64 = (obj, path = '') => {
                        if (!obj) return null;

                        if (typeof obj === 'string') {
                            if (obj.startsWith('data:')) {
                                return { data: obj, path: path };
                            }
                            if (obj.length > 100 && /^[A-Za-z0-9+/=\r\n]+$/.test(obj.replace(/\s/g, ''))) {
                                const isPDF = obj.startsWith('JVBERi0');
                                const mimeType = isPDF ? 'application/pdf' : 'image/jpeg';
                                return {
                                    data: `data:${mimeType};base64,${obj}`,
                                    path: path
                                };
                            }
                        }

                        if (typeof obj === 'object') {
                            for (const key in obj) {
                                const result = findBase64(obj[key], path ? `${path}.${key}` : key);
                                if (result) return result;
                            }
                        }

                        return null;
                    };

                    const found = findBase64(parsedResponse);
                    if (found) {
                        console.log(`Found base64 data at: ${found.path}`);
                        const isPDF = found.data.includes('application/pdf') || found.data.startsWith('JVBERi0');
                        processScannedDocument(found.data, 0, isPDF);
                        return;
                    }
                }
            } catch (error) {
                console.error('Error processing scan response:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Processing Error',
                    text: 'Error processing scanned document: ' + error.message,
                    timer: 3000
                });
            }

            console.log('=== SCAN CALLBACK END ===');
        };

        try {
            const scanOptions = {
                'modal': true,
                'show_ui': true,
                'twain_cap_set': {
                    'ICAP_PIXELTYPE': 2,
                    'ICAP_XRESOLUTION': 200,
                    'ICAP_YRESOLUTION': 200
                },
                'use_asprise_dialog': true,
                'show_scanner_ui': false
            };

            if (scanType === 'pdf') {
                scanOptions.output_settings = [{
                    'type': 'return-base64',
                    'format': 'pdf',
                    'pdf_compress': true,
                    'pdf_downsample': true
                }];
            } else {
                scanOptions.output_settings = [{
                    'type': 'return-base64',
                    'format': 'jpg',
                    'quality': 90,
                    'dpi': 200
                }];
            }
            console.log('Starting scan with options:', scanOptions);
            window.scanner.scan(scanCallback, scanOptions);

        } catch (error) {
            scanInProgress.value = false;
            console.error('Scanner initialization error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Scanner Error',
                text: 'Failed to initialize scanner: ' + error.message,
                timer: 3000
            });
        }
    };

    const processScannedDocument = (base64Data, pageNumber, isPDF = null) => {
        console.log('=== PROCESS SCANNED DOCUMENT START ===');
        console.log('Input data type:', typeof base64Data);
        console.log('Input preview (first 100 chars):', base64Data?.substring?.(0, 100));
        console.log('Is PDF param:', isPDF);
        console.log('Selected scan format:', newDocument.scanFormat);

        try {
            if (!base64Data) {
                throw new Error('No document data received');
            }

            let finalData = '';
            let isPdfFormat = false;
            let mimeType = '';
            let extension = '';
            let rawBase64 = '';

            if (typeof base64Data === 'string' && base64Data.startsWith('data:')) {
                console.log('Data is already a data URL');
                finalData = base64Data;

                const match = base64Data.match(/^data:(.+?);/);
                if (match) {
                    mimeType = match[1];
                    isPdfFormat = mimeType.includes('pdf');
                }

                const parts = base64Data.split('base64,');
                if (parts.length > 1) {
                    rawBase64 = parts[1];
                }
            } else if (typeof base64Data === 'string') {
                console.log('Data is raw base64 string');
                rawBase64 = base64Data.replace(/[\r\n\s]/g, '');

                if (rawBase64.startsWith('JVBERi0')) {
                    console.log('Detected PDF from magic bytes (JVBERi0)');
                    isPdfFormat = true;
                    mimeType = 'application/pdf';
                    extension = 'pdf';
                    finalData = `data:application/pdf;base64,${rawBase64}`;
                } else if (rawBase64.startsWith('/9j/')) {
                    console.log('Detected JPG from magic bytes (/9j/)');
                    isPdfFormat = false;
                    mimeType = 'image/jpeg';
                    extension = 'jpg';
                    finalData = `data:image/jpeg;base64,${rawBase64}`;
                } else if (rawBase64.startsWith('iVBORw0KGgo')) {
                    console.log('Detected PNG from magic bytes (iVBORw0KGgo)');
                    isPdfFormat = false;
                    mimeType = 'image/png';
                    extension = 'png';
                    finalData = `data:image/png;base64,${rawBase64}`;
                } else {
                    console.log('Could not detect format from content, using user selection');
                    isPdfFormat = newDocument.scanFormat === 'pdf';
                    mimeType = isPdfFormat ? 'application/pdf' : 'image/jpeg';
                    extension = isPdfFormat ? 'pdf' : 'jpg';
                    finalData = `data:${mimeType};base64,${rawBase64}`;
                }
            } else if (base64Data && typeof base64Data === 'object') {
                console.log('Data is an object:', base64Data.constructor.name);
                console.log('Object keys:', Object.keys(base64Data));

                let extractedBase64 = '';

                if (base64Data.base64 && typeof base64Data.base64 === 'string') {
                    extractedBase64 = base64Data.base64;
                } else if (base64Data.data && typeof base64Data.data === 'string') {
                    extractedBase64 = base64Data.data;
                } else if (base64Data.src && typeof base64Data.src === 'string') {
                    extractedBase64 = base64Data.src;
                } else if (base64Data.image && typeof base64Data.image === 'string') {
                    extractedBase64 = base64Data.image;
                } else if (Array.isArray(base64Data) && base64Data.length > 0) {
                    if (typeof base64Data[0] === 'string') {
                        extractedBase64 = base64Data[0];
                    } else if (base64Data[0] && base64Data[0].base64) {
                        extractedBase64 = base64Data[0].base64;
                    }
                }

                if (extractedBase64) {
                    console.log('Found base64 in object, extracting...');
                    return processScannedDocument(extractedBase64, pageNumber, isPDF);
                } else {
                    const jsonStr = JSON.stringify(base64Data);
                    const base64Match = jsonStr.match(/"([A-Za-z0-9+/=\r\n]+)"/);
                    if (base64Match && base64Match[1].length > 100) {
                        console.log('Found base64 in stringified object');
                        return processScannedDocument(base64Match[1], pageNumber, isPDF);
                    }
                    throw new Error('Could not extract base64 data from object');
                }
            } else {
                throw new Error(`Unsupported data format: ${typeof base64Data}`);
            }

            if (!finalData.includes('base64,')) {
                console.error('Failed to create valid data URL:', finalData);
                throw new Error('Failed to create valid data URL');
            }

            if (!rawBase64) {
                const base64Parts = finalData.split('base64,');
                if (base64Parts.length < 2) {
                    throw new Error('Invalid data URL format - missing base64 part');
                }
                rawBase64 = base64Parts[1];
            }

            if (!rawBase64 || rawBase64.length === 0) {
                throw new Error('Empty base64 data');
            }

            console.log('Processing complete:');
            console.log('Mime type:', mimeType);
            console.log('Is PDF:', isPdfFormat);
            console.log('Extension:', extension);
            console.log('Base64 length:', rawBase64.length);
            console.log('Data URL created:', finalData.substring(0, 100) + '...');

            if (!extension) {
                if (mimeType === 'application/pdf') {
                    extension = 'pdf';
                } else if (mimeType.includes('png')) {
                    extension = 'png';
                } else if (mimeType.includes('jpeg') || mimeType.includes('jpg')) {
                    extension = 'jpg';
                } else {
                    extension = isPdfFormat ? 'pdf' : 'jpg';
                }
            }

            const timestamp = new Date().getTime();
            const fileName = `scanned_document_${timestamp}.${extension}`;
            props.setFieldValue(`application.documents[${selectedDocIndex.value}].new_file`, finalData);
            props.setFieldValue(`application.documents[${selectedDocIndex.value}].original_name`, fileName);
            props.setFieldValue(`application.documents[${selectedDocIndex.value}].mime_type`, mimeType);

            Swal.fire({
                icon: 'success',
                title: 'Document Scanned successfully!',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000
            });

            console.log('=== PROCESS SCANNED DOCUMENT END ===');

        } catch (error) {
            console.error('Error processing scanned document:', error);
            console.error('Full input data:', base64Data);

            Swal.fire({
                icon: 'error',
                title: 'Processing Error',
                html: `
                        <div class="text-left">
                          <p><strong>Error:</strong> ${error.message}</p>
                          <p class="text-sm mt-2">Please try scanning again.</p>
                        </div>
                      `,
                confirmButtonText: 'OK'
            });
        }
    };

    const closeDocumentModal = () => {
        showDocumentModal.value = false;
        currentDocument.value = null;
    };

    const closeAllDocumentsModal = () => {
        showAllDocumentsModal.value = false;
    };

    const downloadFile = (fileURL, fileName) => {
        const link = document.createElement('a');
        link.href = fileURL;
        link.download = fileName;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };

    const handleKeydown = (event) => {
        if (event.key === 'Escape') {
            if (showDocumentModal.value) {
                closeDocumentModal();
            }
            if (showAllDocumentsModal.value) {
                closeAllDocumentsModal();
            }
            if (showFilePreviewModal.value) {
                closeFilePreviewModal();
            }
        }
    };
    const handleFingerPrintCaptured = (payload) => {
        props.setFieldValue(`application.biometrics.${payload.type}.image_file`, payload.image);
        props.setFieldValue(`application.biometrics.${payload.type}.feature_set`, payload.featureSet);
    };
</script>


<template>
    <div class="step3-wrapper" id="step3">
        <!-- Photo Upload -->
        <div class="mt-10 flex items-center justify-start gap-8" dir="rtl">
            <label class="block font-bold text-[16px] text-gray-700 font-nastaleeq mb-2 text-right">درخواست دہندہ کی
                تصویر</label>

            <div class="flex flex-row-reverse items-center justify-end gap-12">
                <div class="flex flex-col gap-1 items-center">
                    <img v-if="photoPreview" :src="photoPreview" alt="Photo Preview"
                         class="w-[150px] h-[150px] object-cover rounded-md border cursor-pointer p-2"
                         @click="()=>{
                         selectedDocument = {
                             new_file: photoPreview,
                         }
                         showImagePreview = true;
                     }" />
                    <ErrorMessage
                        name="application.personal_image_file"
                        class="text-red-500 font-nastaleeq"
                    />
                </div>
                <div class="flex gap-2 items-center">
                    <Field name="application.personal_image_file" v-slot="{ handleChange, handleBlur }">
                        <input
                            type="file"
                            accept="image/*"
                            hidden
                            class="form-input font-nastaleeq"
                            ref="inputImageUpload"
                            @change="(e) => {
                          const file = e.target.files?.[0];
                          handlePhotoUpload(file);
                        }"
                            @blur="handleBlur"
                        />
                    </Field>

                    <button
                        class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 text-center gap-2 min-w-60 font-nastaleeq"
                        type="button" @click.stop="openCamera">
                        <i class="fa-solid fa-camera me-3"></i> پروفائل پکچر
                    </button>
                </div>
            </div>
        </div>

        <!-- Thumb Impression Section - SHOWN WHEN STATE OR BOTH IS SELECTED -->
        <div
            v-if="(values.application.certificate_type === 'state' || values.application.certificate_type === 'both') && (values.application.application_type_id===1)"
            class="my-8 p-0">
            <h4 class="font-bold text-[16px] text-gray-700 mb-4 text-right font-nastaleeq ">
                انگلیوں کے نشانات
            </h4>
            <p class="text-sm text-yellow-700 mb-4 text-right font-nastaleeq">
                براہِ کرم درج ذیل انگلیوں کے فنگر پرنٹس اپ لوڈ کریں
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-5 gap-4" :dir="genderName==='Male'? 'rtl' : 'ltr' ">
                <!-- Thumb -->
                <FingerPrint finger-type="thumb" label="انگوٹھا (Thumb)"
                             :src="props.values.application?.biometrics?.thumb?.image_path || props.values.application?.biometrics?.thumb?.image_file || null"
                             :setFieldValue="setFieldValue"
                             @fingerprintCaptured="handleFingerPrintCaptured" />
                <FingerPrint finger-type="index" label="کلمے کی انگلی (Index)"
                             :src="props.values.application?.biometrics?.index?.image_path || props.values.application?.biometrics?.index?.image_file || null"
                             :setFieldValue="setFieldValue"
                             @fingerprintCaptured="handleFingerPrintCaptured" />
                <FingerPrint finger-type="middle" label="درمیانی انگلی (Middle)"
                             :src="props.values.application?.biometrics?.middle?.image_path || props.values.application?.biometrics?.middle?.image_file || null"
                             :setFieldValue="setFieldValue"
                             @fingerprintCaptured="handleFingerPrintCaptured" />
                <FingerPrint finger-type="ring" label="انگوٹھی انگلی (Ring)"
                             :src="props.values.application?.biometrics?.ring?.image_path || props.values.application?.biometrics?.ring?.image_file || null"
                             :setFieldValue="setFieldValue"
                             @fingerprintCaptured="handleFingerPrintCaptured" />
                <FingerPrint finger-type="little" label="چھوٹی انگلی (Little)"
                             :src="props.values.application?.biometrics?.little?.image_path || props.values.application?.biometrics?.little?.image_file || null"
                             :setFieldValue="setFieldValue"
                             @fingerprintCaptured="handleFingerPrintCaptured" />

            </div>

            <!-- Thumb Impression Notes -->
            <div class="mt-4 p-3 bg-yellow-100 border border-yellow-200 rounded" dir="rtl">
                <h3 class="font-bold font-nastaleeq text-yellow-800 mb-2 text-right">
                    ہدایات:</h3>
                <ul class="text-xs text-yellow-700 font-nastaleeq text-right space-y-1">
                    <li>انگوٹھے کا نشان واضح اور صاف ہونا چاہیے</li>
                    <li>تصویر اچھی روشنی میں لیں</li>
                    <li>فائل سائز 1MB سے زیادہ نہ ہو</li>
                    <li>صرف JPG، PNG فارمیٹ قبول ہیں</li>
                </ul>
            </div>
        </div>

        <!-- Application required Documents -->
        <div class="mt-4" id="requiredDocuments">
            <!-- Documents Table -->
            <div>
                <div class="flex items-center mb-0 border bg-blue-100">
                    <h3 class="text-lg font-bold p-3 font-nastaleeq pb-1 w-full text-right relative">
                        ضروری دستاویزات
                    </h3>
                </div>

                <table class="min-w-full border border-gray-300 mt-0">
                    <thead class="bg-blue-100">
                    <tr class="bg-blue-100">
                        <th class="border px-4 py-2 font-nastaleeq text-center font-bold text-[16px]">پیش منظر</th>
                        <th class="border px-4 py-2 font-nastaleeq text-center font-bold text-[16px] max-w-[80px]">
                            دستاویز
                            کا طریقہ کار
                        </th>
                        <th class="border px-4 py-2 font-nastaleeq text-center font-bold text-[16px] max-w-[80px]">
                            دستاویز
                            کی قسم <br /> (اصل، نقل، اسکین شدہ)
                        </th>
                        <th class="border px-4 py-2 font-nastaleeq text-right font-bold text-[16px]">دستاویز کا نام</th>
                        <th class="border px-4 py-2 font-nastaleeq text-center font-bold text-[16px] max-w-[40px]">
                            سیرئیل
                            نمبر
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <FieldArray name="application.documents" v-slot="{ fields }">
                        <tr v-for="(fieldItem, index) in fields" :key="fieldItem.key">
                            <Field :name="`application.documents[${index}].id`" type="hidden" />
                            <Field :name="`application.documents[${index}].application_id`" type="hidden" />
                            <Field :name="`application.documents[${index}].key`" type="hidden" />
                            <td class="border text-center">
                                <div class="relative group w-16 h-16 mx-auto"
                                     v-if="fieldItem.value.file_path || fieldItem.value.new_file"
                                >
                                    <div
                                        class="w-16 h-16 border rounded-md p-2 flex items-center justify-center cursor-pointer bg-gray-50"
                                        @click="() => {
                                        selectedDocument = {
                                            new_file: fieldItem.value.new_file,
                                            file_path: fieldItem.value.file_path,
                                            mime_type: fieldItem.value.mime_type,
                                        }
                                        showImagePreview = true;
                                    }"
                                    >
                                        <!-- IMAGE -->
                                        <img
                                            v-if="fieldItem.value.mime_type?.startsWith('image/')"
                                            :src="fieldItem.value.new_file || fieldItem.value.file_path"
                                            class="w-full h-full object-cover rounded"
                                            alt="image"
                                        />

                                        <!-- PDF ICON -->
                                        <i
                                            v-else-if="fieldItem.value.mime_type === 'application/pdf'"
                                            class="fas fa-file-pdf text-red-600 text-3xl"
                                        ></i>

                                        <!-- OTHER FILE ICON -->
                                        <i
                                            v-else
                                            class="fas fa-file text-gray-500 text-3xl"
                                        ></i>
                                    </div>

                                    <!-- Remove Button -->
                                    <button
                                        type="button"
                                        @click.stop="()=>{
                                            props.setFieldValue(`application.documents[${index}].file_path`, null)
                                            props.setFieldValue(`application.documents[${index}].new_file`, null)
                                            props.setFieldValue(`application.documents[${index}].removed_from_frontend`, true)
                                        }"
                                        class="absolute top-[-10px] right-[-10px] bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs
                                    opacity-0 group-hover:opacity-100 transition"
                                    >
                                        ✕
                                    </button>
                                </div>

                                <Field :name="`application.documents[${index}].new_file`" type="hidden" />
                                <ErrorMessage
                                    :name="`application.documents[${index}].new_file`"
                                    class="text-red-500 font-nastaleeq"
                                />
                            </td>
                            <td class="border text-center max-w-[80px]">
                                <div class="inline-flex bg-gray-200 dark:bg-gray-800 p-1 rounded-xl gap-3">
                                    <!-- Upload File -->
                                    <button
                                        :class="fieldItem.value.upload_method === 'manual'
                                        ? 'bg-white text-primary shadow-md'
                                        : 'text-gray-500 dark:text-gray-300 hover:bg-white/60'"
                                        class="flex flex-col items-center justify-center px-2 py-1 rounded-lg text-xs font-medium transition-all duration-200"
                                        type="button" title="Upload File"
                                        @click="selectFile(fieldItem.value, index)">

                                        <IconFileUpload class="w-6 h-6 mb-1" />
                                    </button>

                                    <!-- Scan Image -->
                                    <button
                                        :class="fieldItem.value.upload_method === 'scan_image'
                                        ? 'bg-white text-primary shadow-md'
                                        : 'text-gray-500 dark:text-gray-300 hover:bg-white/60'"
                                        class="flex flex-col items-center justify-center px-2 py-1 rounded-lg text-xs font-medium transition-all duration-200"
                                        type="button" title="Scan as Image"
                                        @click="scanDocument(fieldItem.value, index, 'image')">

                                        <IconScanImage class="w-6 h-6 mb-1" />
                                    </button>

                                    <!-- Scan PDF -->
                                    <button
                                        :class="fieldItem.value.upload_method === 'scan_pdf'
                                        ? 'bg-white text-primary shadow-md'
                                        : 'text-gray-500 dark:text-gray-300 hover:bg-white/60'"
                                        class="flex flex-col items-center justify-center px-2 py-1  rounded-lg text-xs font-medium transition-all duration-200"
                                        type="button" title="Scan as PDF"
                                        @click="scanDocument(fieldItem.value, index, 'pdf')">

                                        <IconScanPdf class="w-6 h-6 mb-1" />
                                    </button>
                                </div>
                            </td>
                            <td v-if="fieldItem.value.required_copy==='original'"
                                class="border px-4 py-2 text-center font-nastaleeq text-red-900 font-bold text-[16px] max-w-[80px]">
                                {{ 'اصل ضروری ہے' }}
                            </td>
                            <td v-else-if="fieldItem.value.required_copy==='photocopy'"
                                class="border px-2 py-2 text-center font-nastaleeq text-yellow-700 font-bold text-[16px] max-w-[80px]">
                                {{ 'نقل قابل قبول ہے' }}
                            </td>
                            <td v-else
                                class="border px-2 py-2 text-center font-nastaleeq text-yellow-400 font-bold text-[16px] max-w-[80px]">
                                {{ 'اسکین شدہ قابل قبول ہے' }}
                            </td>
                            <td class="border px-4 py-2 text-right font-nastaleeq font-bold text-[16px] max-w-52">
                                <span class="text-red-500">*</span>{{ fieldItem.value.urdu_name || fieldItem.value.name
                                }}
                            </td>
                            <td class="border px-4 py-2 text-center font-nastaleeq max-w-[40px]">
                                {{ index + 1 }}
                            </td>
                        </tr>
                        <tr v-if="fields.length === 0">
                            <td class="border px-4 py-4 text-center text-gray-500" colspan="6">
                                No required documents found.
                            </td>
                        </tr>
                    </FieldArray>
                    </tbody>
                </table>
                <input
                    ref="fileInput"
                    type="file"
                    accept="image/*"
                    hidden
                    @change="handleDocFileChange"
                />
            </div>
        </div>

        <!-- Camera Modal -->
        <CameraDialog v-model="showCameraModal" @captured="savePhoto" />

        <!-- Image Preview Modal -->
        <ImagePreviewDialog
            v-if="selectedDocument"
            v-model="showImagePreview"
            :image-src="selectedDocument.new_file || selectedDocument.file_path"
        />
    </div>
</template>
