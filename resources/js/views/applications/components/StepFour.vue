<script setup lang="ts">
    import { computed, ref } from 'vue';
    import ImagePreviewDialog from '@/views/applications/Dialogs/ImagePreviewDialog.vue';
    import { useAppStore } from '@/stores';
    import { District, Region, Tehsil, Type } from '@/types';
    import { formatDMY } from '@/mixin';

    const appStore = useAppStore();
    const parsedData = appStore.groupedData;

    const selectedDocument = ref({
        new_file: null,
        file_path: null,
        mime_type: null
    });
    const showImagePreview = ref(false);

    const props = defineProps({
        values: {
            type: Object,
            required: true
        }
    });

    const photoPreview = computed(() => props.values.application.personal_image_file || props.values.application.personal_image);


    const guardian = computed(() => {
        if (!parsedData) return '';
        const guardianType: any = parsedData.guardian_type.find((type: Type) => type.id === props.values.applicant.guardian_type_id);
        return guardianType?.urdu_name ?? '';
    });

    const gender = computed(() => {
        if (!parsedData) return '';
        const genderType: any = parsedData.gender.find((type: Type) => type.id === props.values.applicant.gender_id);
        return genderType ? genderType.urdu_name : '';
    });

    const religion = computed(() => {
        if (!parsedData) return '';
        const religionType: any = parsedData.religion.find((type: Type) => type.id === props.values.applicant.religion_id);
        return religionType ? religionType.urdu_name : '';
    });

    const marital_status = computed(() => {
        if (!parsedData) return '';
        const maritalStatusType: any = parsedData.marital_status.find((type: Type) => type.id === props.values.applicant.marital_status_id);
        return maritalStatusType ? maritalStatusType.urdu_name : '';
    });

    const application_for = computed(() => {
        if (!parsedData) return '';
        const applicationForType: any = parsedData.application_for.find((type: Type) => type.id === props.values.application.application_for_id);
        return applicationForType ? applicationForType.urdu_name : '';
    });

    const region = computed(() => {
        if (!parsedData) return '';
        const regionType: any = parsedData.regions.find((type: Region) => type.id === props.values.applicant.region_id);
        return regionType ? regionType.urdu_name : '';
    });

    const district = computed(() => {
        if (!parsedData) return '';
        const regionType: any = parsedData.regions.find((type: District) => type.id === props.values.applicant.region_id);
        if (!regionType || !regionType.districts) return '';
        const districtType = regionType.districts.find((type: District) => type.id === props.values.applicant.district_id);
        return districtType ? districtType.urdu_name : '';
    });

    const tehsil = computed(() => {
        if (!parsedData) return '';
        const regionType: any = parsedData.regions.find((type: Region) => type.id === props.values.applicant.region_id);
        if (!regionType || !regionType.districts) return '';
        const districtType = regionType.districts.find((type: District) => type.id === props.values.applicant.district_id);
        if (!districtType || !districtType.tehsils) return '';
        const tehsilType = districtType.tehsils.find((type: Tehsil) => type.id === props.values.applicant.tehsil_id);
        return tehsilType ? tehsilType.urdu_name : '';
    });

    const openDocument = (file: string) => {
        if (!file) return;

        // Already a full URL (file_path case)
        if (file.startsWith('http') || file.startsWith('/')) {
            window.open(file, '_blank');
            return;
        }

        // Base64 — detect if PDF or image
        const isPdf = file.startsWith('JVBERi'); // base64 of "%PDF"
        const mimeType = isPdf ? 'application/pdf' : 'image/jpeg';

        const dataUrl = file.startsWith('data:')
            ? file  // already has prefix
            : `data:${mimeType};base64,${file}`;

        const win = window.open('', '_blank');
        if (!win) return;

        if (isPdf) {
            win.document.write(`
            <iframe src="${dataUrl}" width="100%" height="100%" style="border:none;margin:0;padding:0;"></iframe>
        `);
        } else {
            win.document.write(`
            <img src="${dataUrl}" style="max-width:100%;height:auto;" alt="Image"/>
        `);
        }
    };

    const getAppName = (type: string) => {
        if (type === 'domicile') {
            return 'ڈومیسائل سرٹیفکیٹ';
        }
        if (type === 'state') {
            return 'اسٹیٹ سبجیکٹ سرٹیفکیٹ';
        }
        if (type === 'both') {
            return 'ڈومیسائل اور اسٹیٹ سبجیکٹ سرٹیفکیٹ';
        }
    };

    interface DuplicateReason {
        id: number;
        name: string;
        urdu_name: string;
    }

</script>

<template>
    <div class="step4-wrapper font-nastaleeq" dir="rtl" id="step4">
        <div class="flex justify-center px-4">
            <div class="text-2xl font-semibold uppercase">درخواست کا مکمل جائزہ</div>
        </div>
        <!-- application information -->
        <div class="flex justify-between lg:flex-row flex-col gap-2 flex-wrap">
            <h2 class="text-xl font-semibold">درخواست کی معلومات</h2>
            <div class="flex justify-between sm:flex-row flex-col gap-6 lg:w-full border rounded-lg p-4">
                <div class="xl:1/3 lg:w-2/5 sm:w-1/2 space-y-2">
                    <div class="grid grid-cols-2 gap-2">
                        <div class="text-white-dark">درخواست کی قسم :</div>
                        <div>{{ getAppName(props.values.application.certificate_type) }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="text-white-dark">اندراج کی تاریخ :</div>
                        <div>{{ formatDMY(props.values.application.entry_datetime) }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="text-white-dark">درخواست کی نوعیت :</div>
                        <div>{{ props.values.application.application_type_id == 1 ? 'نئی' : 'نقل' }}</div>
                    </div>
                </div>

                <div class="xl:1/3 lg:w-2/5 sm:w-1/2 space-y-2">
                    <div class="grid grid-cols-2 gap-2">
                        <div class="text-white-dark">مثل نمبر :</div>
                        <div class="whitespace-nowrap">{{ props.values.application.missal_no }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="text-white-dark">ٹوکن نمبر :</div>
                        <div>{{ props.values.application.appointment.qmatic_token }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="text-white-dark">جس کے لیے درخواست دی جا رہی ہے :</div>
                        <div class="whitespace-nowrap">{{ application_for }}</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- applicant information -->
        <div class="flex justify-between lg:flex-row flex-col gap-2 flex-wrap mt-3">
            <h2 class="text-xl font-semibold">درخواست دہندہ کی معلومات</h2>
            <div class="flex justify-between sm:flex-row flex-col gap-6 lg:w-full border rounded-lg p-4">
                <div class="xl:1/2 lg:w-2/5 sm:w-1/2 space-y-2">
                    <div class="grid grid-cols-2 gap-2">
                        <div class="text-white-dark">درخواست دہندہ کا مکمل نام :</div>
                        <div>{{ props.values.applicant.full_name }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="text-white-dark">شناختی قسم :</div>
                        <div>{{ props.values.applicant.identity_type }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="text-white-dark">
                            {{ props.values.applicant.identity_type === 'local' ? 'شناختی کارڈ نمبر' : 'مہاجر کارڈ نمبر'
                            }} :
                        </div>
                        <div>{{ props.values.applicant.identity_number }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="text-white-dark">پیدائش کی تاریخ :</div>
                        <div>{{ formatDMY(props.values.applicant.dob) }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="text-white-dark">پیدائش کی جگہ :</div>
                        <div>{{ props.values.applicant.pob }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="text-white-dark">جنس :</div>
                        <div>{{ gender }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="text-white-dark">موبائل نمبر :</div>
                        <div>{{ props.values.applicant.phone }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="text-white-dark">ای میل :</div>
                        <div>{{ props.values.applicant.email }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="text-white-dark">شناختی علامت :</div>
                        <div>{{ props.values.applicant.identity_symbol }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="text-white-dark">مذہب :</div>
                        <div>{{ religion }}</div>
                    </div>
                    <div v-if="props.values.application.certificate_type === 'state'"
                         class="grid grid-cols-2 gap-2">
                        <div class="text-white-dark">درجہ :</div>
                        <div>{{ props.values.applicant.state_subject_class }}</div>
                    </div>
                </div>
                <div class="xl:1/2 lg:w-2/5 sm:w-1/2 space-y-2">
                    <div class="grid grid-cols-2 gap-2">
                        <div class="text-white-dark">والد کا نام :</div>
                        <div class="whitespace-nowrap">{{ props.values.applicant.father_name }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="text-white-dark">والد کا شناختی کارڈ نمبر :</div>
                        <div>{{ props.values.applicant.father_identity_number }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="text-white-dark">ریجن :</div>
                        <div>{{ region }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="text-white-dark">ضلع :</div>
                        <div>{{ district }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="text-white-dark">تحصیل :</div>
                        <div>{{ tehsil }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="text-white-dark">سرپرست :</div>
                        <div>{{ guardian }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="text-white-dark">ساکنہ :</div>
                        <div>{{ props.values.applicant.residence_place }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="text-white-dark">پیشہ :</div>
                        <div>{{ props.values.applicant.occupation }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="text-white-dark">ازدواجی حیثیت :</div>
                        <div>{{ marital_status }}</div>
                    </div>
                    <div v-if="props.values.applicant.wife_husband_name" class="grid grid-cols-2 gap-2">
                        <div class="text-white-dark">بیوی/شوہر کا نام :</div>
                        <div>{{ props.values.applicant.wife_husband_name }}</div>
                    </div>
                </div>
                <div class="xl:1/2lg:w-2/5 sm:w-1/2 space-y-2">
                    <div v-if="props.values.applicant.identity_type==='refugee'"
                         class="grid grid-cols-2 gap-2">
                        <div class="text-white-dark">جائے ہجرت :</div>
                        <div>{{ props.values.applicant.refugee_details.refugee_from }}</div>
                    </div>
                    <div v-if="props.values.applicant.identity_type==='refugee'"
                         class="grid grid-cols-2 gap-2">
                        <div class="text-white-dark">سالِ ہجرت :</div>
                        <div>{{ props.values.applicant.refugee_details.refugee_year }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Children Details -->
        <h2 v-if="props.values.applicant.children && props.values.applicant.children.length > 0"
            class="text-xl font-semibold mt-3">بچوں کی تفصیلات</h2>
        <div v-if="props.values.applicant.children && props.values.applicant.children.length > 0"
             class="table-responsive mt-2 border rounded-lg p-4">
            <table class="table-striped">
                <thead>
                <tr>
                    <th>سیریل نمبر</th>
                    <th>بچے کا نام</th>
                    <th>بچے کی عمر</th>
                </tr>
                </thead>
                <tbody>
                <template v-for="(child, index) in props.values.applicant.children" :key="index">
                    <tr>
                        <td>{{ index + 1 }}</td>
                        <td>{{ child.name }}</td>
                        <td>{{ child.age }}</td>
                    </tr>
                </template>
                </tbody>
            </table>
        </div>

        <!-- Personal Image -->
        <div v-if="photoPreview" class="mt-3">
            <h2 class="text-xl font-semibold">درخواست دہندہ کی تصویر</h2>
            <img v-if="photoPreview" :src="photoPreview" alt="Photo Preview"
                 class="w-[150px] h-[150px] object-cover rounded-md border cursor-pointer p-2"
                 @click="()=>{
                         selectedDocument = {
                             new_file: photoPreview,
                             file_path: null,
                             mime_type: null
                         }
                         showImagePreview = true;
                     }" />
        </div>

        <!-- Biometrics -->
        <div
            v-if="props.values.application.certificate_type !== 'domicile' && props.values.application.application_type_id ===1"
            class="mt-6">
            <h2 class="text-xl font-semibold">انگلیوں کے نشانات</h2>
            <div
                class="bg-white border rounded-lg p-4">
                <div class="flex flex-row flex-wrap justify-center gap-4">

                    <!-- Thumb -->
                    <div class="flex flex-col items-center">
                        <label class="block text-[14px] font-bold font-nastaleeq text-yellow-700 mb-2 text-center"
                               dir="rtl">انگوٹھا (Thumb)</label>
                        <div
                            class="relative w-32 h-32 mb-2 border-2 border-dashed border-yellow-400 rounded-lg flex items-center justify-center bg-yellow-50">
                            <img
                                v-if="props.values.application?.biometrics?.thumb?.image_file || props.values.application?.biometrics?.thumb?.image_path"
                                :src="props.values.application?.biometrics?.thumb?.image_file || props.values.application?.biometrics?.thumb?.image_path"
                                alt="thumb-image" class="w-full h-full object-contain p-1">
                            <div v-else class="text-center">
                                <i class="fa-solid fa-fingerprint text-yellow-400 fa-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Index -->
                    <div class="flex flex-col items-center">
                        <label class="block text-[14px] font-bold font-nastaleeq text-yellow-700 mb-2 text-center"
                               dir="rtl">کلمے کی انگلی (Index)</label>
                        <div
                            class="relative w-32 h-32 mb-2 border-2 border-dashed border-yellow-400 rounded-lg flex items-center justify-center bg-yellow-50">
                            <img
                                v-if="props.values.application?.biometrics?.index?.image_file || props.values.application?.biometrics?.index?.image_path"
                                :src="props.values.application?.biometrics?.index?.image_file || props.values.application?.biometrics?.index?.image_path"
                                alt="index-image" class="w-full h-full object-contain p-1">
                            <div v-else class="text-center">
                                <i class="fa-solid fa-fingerprint text-yellow-400 fa-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Middle -->
                    <div class="flex flex-col items-center">
                        <label class="block text-[14px] font-bold font-nastaleeq text-yellow-700 mb-2 text-center"
                               dir="rtl">درمیانی انگلی (Middle)</label>
                        <div
                            class="relative w-32 h-32 mb-2 border-2 border-dashed border-yellow-400 rounded-lg flex items-center justify-center bg-yellow-50">
                            <img
                                v-if="props.values.application?.biometrics?.middle?.image_file || props.values.application?.biometrics?.middle?.image_path"
                                :src="props.values.application?.biometrics?.middle?.image_file || props.values.application?.biometrics?.middle?.image_path"
                                alt="middle-image" class="w-full h-full object-contain p-1">
                            <div v-else class="text-center">
                                <i class="fa-solid fa-fingerprint text-yellow-400 fa-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Ring -->
                    <div class="flex flex-col items-center">
                        <label class="block text-[14px] font-bold font-nastaleeq text-yellow-700 mb-2 text-center"
                               dir="rtl">انگوٹھی انگلی (Ring)</label>
                        <div
                            class="relative w-32 h-32 mb-2 border-2 border-dashed border-yellow-400 rounded-lg flex items-center justify-center bg-yellow-50">
                            <img
                                v-if="props.values.application?.biometrics?.ring?.image_file || props.values.application?.biometrics?.ring?.image_path"
                                :src="props.values.application?.biometrics?.ring?.image_file || props.values.application?.biometrics?.ring?.image_path"
                                alt="ring-image" class="w-full h-full object-contain p-1">
                            <div v-else class="text-center">
                                <i class="fa-solid fa-fingerprint text-yellow-400 fa-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Little -->
                    <div class="flex flex-col items-center">
                        <label class="block text-[14px] font-bold font-nastaleeq text-yellow-700 mb-2 text-center"
                               dir="rtl">چھوٹی انگلی (Little)</label>
                        <div
                            class="relative w-32 h-32 mb-2 border-2 border-dashed border-yellow-400 rounded-lg flex items-center justify-center bg-yellow-50">
                            <img
                                v-if="props.values.application?.biometrics?.little?.image_file || props.values.application?.biometrics?.little?.image_path"
                                :src="props.values.application?.biometrics?.little?.image_file || props.values.application?.biometrics?.little?.image_path"
                                alt="little-image" class="w-full h-full object-contain p-1">
                            <div v-else class="text-center">
                                <i class="fa-solid fa-fingerprint text-yellow-400 fa-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Documents -->
        <div v-if="props.values.application?.documents" class="mt-6">
            <h2 class="text-xl font-semibold">ضروری دستاویزات</h2>
            <div v-if="props.values.application?.documents.length > 0"
                 class="table-responsive mt-2 border rounded-lg p-4">
                <table class="table-striped">
                    <thead>
                    <tr>
                        <th>سیریل نمبر</th>
                        <th>دستاویز کا نام</th>
                        <th> دستاویز</th>
                    </tr>
                    </thead>
                    <tbody>
                    <template v-for="(document, index) in props.values.application?.documents" :key="index">
                        <tr>
                            <td>{{ index + 1 }}</td>
                            <td>{{ document.urdu_name }}</td>
                            <td>
                                <a
                                    v-if="document.new_file || document.file_path"
                                    href="#"
                                    @click.prevent="openDocument(document.new_file || document.file_path)"
                                    class="text-blue-600 hover:underline cursor-pointer"
                                >
                                    <i class="fa-solid fa-eye fa-lg"></i>
                                </a>
                            </td>
                        </tr>
                    </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Normal Remarks -->
        <div v-if="props.values.application.remarks" class="mt-6">
            <h2 class="text-xl font-semibold">ریمارکس</h2>
            <div class=" border rounded-lg p-4">
                <p class="mt-2">{{ props.values.application.remarks }}</p>
            </div>
        </div>

        <!-- Duplication Remarks -->
        <div v-if="props.values.application.application_type_id===2" class="mt-6">
            <h2 class="text-xl font-semibold">ڈپلیکیشن کی تفصیلات</h2>
            <div class="flex flex-col gap-2 border rounded-lg p-4">
                <span class="font-semibold"> وجہ</span>
                <span class="mt-2">
                    {{
                        (parsedData.duplicate_reasons as DuplicateReason[]).find(
                            (r: DuplicateReason) => r.id === props.values.application.duplicate_details.reason_type_id
                        )?.urdu_name
                    }}
                </span>
                <span class="font-semibold">ریمارکس</span>
                <span class="mt-2">{{ props.values.application.duplicate_details.reason }}</span>
            </div>
        </div>
    </div>

    <!-- Image Preview Modal -->
    <ImagePreviewDialog
        v-if="selectedDocument && selectedDocument.new_file"
        v-model="showImagePreview"
        :image-src="selectedDocument.new_file"
    />
</template>
