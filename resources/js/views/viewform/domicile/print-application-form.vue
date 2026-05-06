<template>
    <div class="certificate-wrapper">
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-[25vh]">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4"></div>
            <p class="text-gray-600 font-nastaleeq text-2xl">...ڈیٹا لوڈ ہو رہا ہے</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="text-center py-8 text-red-600">
            <p class="text-lg mb-4 font-nastaleeq">غلطی: {{ error }}</p>
            <button @click="fetchApplication" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                دوبارہ کوشش کریں
            </button>
        </div>

        <div v-else-if="application">
            <div class="print:hidden flex justify-between gap-3 mb-4 max-w-[210mm] mx-auto">
                <button @click="goBack" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </button>
                <button @click="handlePrint" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
            </div>
            <div class="certificate" dir="rtl">
                <!-- HEADER -->
                <table class="w-full">
                    <tbody>
                    <tr>
                        <td class="font-nastaleeq left-col">
                            فارم نمبر : <u>A-1</u>
                        </td>

                        <td class="text-center">
                            <div class="font-nastaleeq mb-4">(فارم B-1)</div>
                            <div class="font-nastaleeq title">
                                آزاد جموں و کشمیر
                            </div>
                            <div class="font-nastaleeq rule-box">
                                (قواعد باشندہ ریاست آزاد جموں و کشمیر مجریہ۱۹۸۰ کا قاعدہ نمبر ۷ ملاحظہ ہو)
                            </div>
                            <div class="font-nastaleeq sub-title">
                                درخواست برائے ڈومیسائل سرٹیفکیٹ آزاد جموں و کشمیر
                            </div>
                        </td>

                        <!-- IMAGE IN RIGHT COLUMN -->
                        <td class="right-col">
                            <div class="relative group inline-block">
                                <!-- Applicant Image with preview functionality -->
                                <img v-if="application.personal_image"
                                     :src="getFullImageUrl(application.personal_image)" alt="Personal Photo"
                                     class="w-24 h-24 object-cover rounded-md border-2 border-gray-300 cursor-pointer "
                                     @error="handleImageError" />

                                <!-- Default image when no applicant image exists -->
<!--                                <img v-else src="/assets/images/boy.png" alt="Default"-->
<!--                                     class="w-24 h-24 object-contain rounded-md border-2 border-gray-300" />-->

                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <table class="w-[30%] mt-2">
                    <tbody>
                    <tr>
                        <td class="font-nastaleeq line" width="200px">جناب مجسٹریٹ درجہ اول</td>
                    </tr>
                    </tbody>
                </table>

                <!-- ADDRESSING -->
                <table class="w-[50%] mt-2">
                    <tbody>
                    <tr>
                        <td class="font-nastaleeq" width="120px">مقام</td>
                        <td class="line font-nastaleeq">{{ application.location || '' }}</td>
                    </tr>
                    <tr>
                        <td class="font-nastaleeq">جناب عالی</td>
                    </tr>
                    </tbody>
                </table>

                <!-- PERSONAL INFO -->
                <table class="w-full mt-2">
                    <tbody>
                    <tr>
                        <td class="font-nastaleeq" width="120px">منکہ</td>
                        <td class="line font-nastaleeq">{{ application.full_name || '' }}</td>
                        <td class="font-nastaleeq" width="120px">زوجہ</td>
                        <td class="line font-nastaleeq">{{ application.wife_husband_name || '' }}</td>
                    </tr>
                    <tr>
                        <td class="font-nastaleeq">عمر</td>
                        <td class="line font-nastaleeq">{{ calculateAge(application?.dob) || '' }}</td>
                        <td class="font-nastaleeq">ساکن</td>
                        <td class="line font-nastaleeq">{{ application?.residence_place || '' }}</td>
                    </tr>
                    </tbody>
                </table>

                <table>
                    <tbody>
                    <tr>
                        <td class="font-nastaleeq" width="200px">اقرار کرتا/کرتی ہوں میں(سابق سکونت) سے یہاں</td>
                        <td class="line font-nastaleeq">{{ application?.address || '&nbsp;' }}</td>
                        <td class="font-nastaleeq" width="40px">تحصیل</td>
                        <td class="line font-nastaleeq">{{ getTehsilName() || '&nbsp;' }}</td>
                        <td class="font-nastaleeq" width="40px">ضلع</td>
                        <td class="line font-nastaleeq">{{ getDistrictName() || '&nbsp;' }}</td>
                    </tr>
                    </tbody>
                </table>

                <!-- RESIDENCE & DOMICILE -->
                <table class="w-full mt-2">
                    <tbody>
                    <tr>
                        <td class="font-nastaleeq" width="180px">آزاد جموں و کشمیر مورخہ</td>
                        <td class="line font-nastaleeq w-32">{{ formatDMY(application?.entry_date) || '' }}</td>
                        <td class="font-nastaleeq" width="250px">کو وارد ہوا/ہوئی۔ میں آزاد کشمیر میں مسلسل عرصہ
                        </td>
                        <td class="line font-nastaleeq">{{ application?.entry_time || '' }}</td>
                        <td class="font-nastaleeq" width="40px">سال</td>
                        <td class="line font-nastaleeq">{{ application?.entry_month || '' }}</td>
                        <td class="font-nastaleeq" width="90px">ماہ سے</td>
                        <td class="line font-nastaleeq w-32 text-[12px]">{{ application?.location || '' }}</td>
                    </tr>
                    </tbody>
                </table>

                <!-- DECLARATION -->
                <table class="w-full mt-2">
                    <tbody>
                    <tr>
                        <td class="font-nastaleeq w-full">
                            رہائش پذیرہوں اور آزاد جموں وکشمیر کا ڈومیسائل حاصل کرنے کے سلسلے میں بیان دیتا/دیتی ہوں
                            اور
                            اقرار کرتا/کرتی ہوں کہ اپنی بقیہ زندگی میں آزاد جموں و کشمیر میں مستقل رہائش رکھوں گا/گی
                        </td>
                    </tr>
                    </tbody>
                </table>

                <!-- OTHER INFO -->
                <table class="w-full mt-2">
                    <tbody>
                    <tr>
                        <td class="font-nastaleeq">دوسرے کوائف درجہ ذیل ہیں-</td>
                    </tr>
                    </tbody>
                </table>

                <!-- MARITAL INFO -->
                <table class="w-full mt-2">
                    <tbody>
                    <tr>
                        <td class="font-nastaleeq" width="220px">شادی شدہ/غیرشادی شدہ/رنڈہ/بیوہ</td>
                        <td class="line font-nastaleeq">{{ getMaritalStatus(application?.marital_status ||
                            application?.marital_status_id) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="font-nastaleeq" width="80px">بیوی/شوہر کا نام</td>
                        <td class="line font-nastaleeq">{{ application?.wife_husband_name || 'Nill' }}</td>
                    </tr>
                    </tbody>
                </table>

                <div class="flex mt-10 justify-between">
                    <table class="w-[40%]">
                        <tbody>
                        <tr>
                            <td class="font-nastaleeq" width="200px">بچوں کے نام اور اُنکی عمریں</td>
                        </tr>
                        </tbody>
                    </table>
                    <!-- CHILDREN - You might need to add a children field to your form -->
                    <table class="child-table mt-4 mx-auto">
                        <thead>
                        <tr>
                            <th class="font-nastaleeq"> نام</th>
                            <th class="font-nastaleeq">عمر</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(child, index) in children" :key="'child-' + index">
                            <td>{{ child.name || '&nbsp;' }}</td>
                            <td>{{ child.age || '&nbsp;' }}</td>
                        </tr>
                        <!-- Fill empty rows if fewer than 2 children for aesthetics -->
                        <tr v-for="i in Math.max(0, 2 - children.length)" :key="'empty-' + i">
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- OCCUPATION & IDENTITY -->
                <table class="w-full mt-2">
                    <tbody>
                    <tr>
                        <td class="font-nastaleeq" width="12px">پیشہ</td>
                        <td class="line font-nastaleeq">{{ application?.occupation || '' }}</td>
                    </tr>
                    <tr>
                        <td class="font-nastaleeq" width="180px">شناختی علامت</td>
                        <td class="line font-nastaleeq">{{ application?.identity_symbol || '' }}</td>
                    </tr>
                    </tbody>
                </table>

                <table>
                    <tbody>
                    <tr>
                        <td class="font-nastaleeq">میں حلیفہ اقرار کرتا/کرتی ہوں کہ مذکورہ بیان میرے علم و یقین کے
                            مطابق
                            درست ہے۔
                        </td>
                    </tr>
                    </tbody>
                </table>

                <!-- SIGNATURE -->
                <table class="w-full mt-4">
                    <tbody>
                    <tr>
                        <td class="font-nastaleeq" width="80">دستخط</td>
                        <td class="line font-nastaleeq"></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="font-nastaleeq" width="80">جگہ</td>
                        <td class="line font-nastaleeq">{{ application?.location || '' }}</td>
                        <td class="font-nastaleeq" width="50">تاریخ</td>
                        <td class="line font-nastaleeq">{{ formatDMY(application?.created_at) || '' }}</td>
                    </tr>
                    </tbody>
                </table>

                <!-- VERIFIER -->
                <table class="w-full mt-4">
                    <tbody>
                    <tr>
                        <td width="20%"></td>
                        <td>
                            <table class="w-full">
                                <tbody>
                                <tr>
                                    <td class="font-nastaleeq" width="160px">تصدیق کنندہ کے دستخط</td>
                                    <td class="line font-nastaleeq"></td>
                                </tr>
                                <tr>
                                    <td class="font-nastaleeq" width="80px">تصدیق کنندہ کا نام</td>
                                    <td class="line font-nastaleeq">{{ application?.authority_name || '' }}</td>
                                </tr>
                                <tr>
                                    <td class="font-nastaleeq" width="80px">عہدہ</td>
                                    <td class="line font-nastaleeq">{{ application?.authority_designation || ''
                                        }}
                                    </td>
                                    <td class="font-nastaleeq" width="80px">جگہ</td>
                                    <td class="line font-nastaleeq">{{ application?.location || '' }}</td>
                                </tr>
                                <tr>
                                    <td class="font-nastaleeq" width="200px">تاریخ</td>
                                    <td class="line font-nastaleeq">{{ formatDMY(application?.entry_date) || ''
                                        }}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { computed, onMounted, ref } from 'vue';
    import axios from 'axios';
    import Swal from 'sweetalert2';
    import { useRoute } from 'vue-router';
    import { formatDMY } from '@/mixin/index';

    const loading = ref(true);
    const error = ref(null);
    const application = ref(null);
    const children = ref([]);
    const route = useRoute();
    const groupedData = ref({
        regions: [],
        maritalStatuses: []
    });


    const handlePrint = () => {
        window.print();
    };

    // ------------------ LOCALSTORAGE ------------------
    function loadDropdownData() {
        const stored = localStorage.getItem('groupedData');
        if (stored) {
            try {
                groupedData.value = JSON.parse(stored);
            } catch (e) {
                console.error('Failed to parse groupedData from localStorage', e);
            }
        }
    }

    // ------------------ API ------------------
    async function fetchApplication() {
        try {
            loading.value = true;
            error.value = null;

            const id = route.params.id;
            if (!id) throw new Error('Application ID not found');

            const res = await axios.get(`/api/applications/${id}`);
            if (!res.data.success) throw new Error(res.data.message || 'Failed to load');

            // application.value = res.data.data.applicant;

            application.value = {
                ...res.data.data.applicant,
                ...res.data.data.application
            };

            // Children parsing
            if (application.value?.children) {
                try {
                    children.value = Array.isArray(application.value.children)
                        ? application.value.children
                        : JSON.parse(application.value.children || '[]');
                } catch (e) {
                    children.value = [];
                }
            }

            loadDropdownData();
        } catch (err) {
            error.value = err.message;
            Swal.fire({ icon: 'error', title: 'Error', text: err.message, confirmButtonText: 'OK' });
        } finally {
            loading.value = false;
        }
    }

    // ------------------ HELPER FUNCTIONS ------------------
    // function getRegionName() {
    //   if (!application.value) return '';
    //   const region = groupedData.value.regions?.find(r => r.id == application.value.region_id);
    //   return region ? (region.urdu_name || region.name) : '';
    // }

    const filteredDistricts = computed(() => {
        if (!application.value) return [];
        const region = groupedData.value.regions?.find(r => r.id == application.value.region_id);
        return region?.districts || [];
    });

    function getDistrictName() {
        if (!application.value) return '';
        const district = filteredDistricts.value.find(d => d.id === application.value.district_id);
        return district ? (district.urdu_name || district.name) : '';
    }

    // const filteredTehsils = computed(() => {
    //   if (!application.value) return [];
    //   const district = filteredDistricts.value.find(d => d.id == application.value.district_id);
    //   return district?.tehsils || [];
    // });

    function getTehsilName() {
        if (!application.value) return '';
        const district = filteredDistricts.value.find(d => d.id === application.value.district_id);
        const tehsil = district?.tehsils.find(t => t.id === application.value.tehsil_id);
        return tehsil ? (tehsil.urdu_name || tehsil.name) : '';
    }

    function getMaritalStatus(id) {
        if (!id) return '';

        const list = groupedData.value.marital_status || [];
        const item = list.find(i => i.id === id);

        return item ? (item.urdu_name || item.name) : '';
    }

    function calculateAge(dob) {
        if (!dob) return '';
        const birth = new Date(dob);
        const today = new Date();
        let age = today.getFullYear() - birth.getFullYear();
        if (today.getMonth() < birth.getMonth() || (today.getMonth() === birth.getMonth() && today.getDate() < birth.getDate())) {
            age--;
        }
        return `${age} سال`;
    }

    function getFullImageUrl(path) {
        if (!path) return '';
        if (path.startsWith('http') || path.startsWith('data:') || path.startsWith('blob:')) return path;
        return `/storage/${path}`;
    }

    function handleImageError(event) {
        event.target.src = '/assets/images/boy.png';
    }

    function goBack() {
        window.history.back();
    }

    // ------------------ MOUNT ------------------
    onMounted(() => {
        fetchApplication();
    });
</script>


<style scoped>
    /* Keep all your existing CSS styles here */
    .certificate-wrapper {
        padding: 5px;
    }

    .certificate {
        border: 1px solid #000;
        padding: 5px;
        font-size: 14px;
        max-width: 210mm;
        margin: 0 auto;
    }

    .left-col {
        width: 150px;
        text-align: center;
        vertical-align: top;
    }

    .right-col {
        width: 150px;
        text-align: center;
    }

    .title {
        font-size: 20px;
    }

    .sub-title {
        font-size: 18px;
        font-weight: bold;
    }

    .rule-box {
        border: 1px solid #000;
        padding: 8px;
        margin: 10px auto;
        display: inline-block;
    }

    .line {
        border-bottom: 1px solid black;
        min-height: 24px;
        height: auto;
        min-width: 40px;
        padding: 2px 5px;
        vertical-align: bottom;
        word-break: break-all;
    }

    .child-table {
        width: 50%;
        border-collapse: collapse;
        margin-left: 15px;
    }

    .child-table th,
    .child-table td {
        border: 1px solid #000;
        text-align: center;
        height: 25px;
        padding-bottom: 0px !important;
        padding-top: 0px !important;
    }

    tbody tr td {
        padding-top: 0.15rem !important;
        padding-bottom: 0.50rem !important;
        padding-left: 1rem;
        padding-right: 1rem;
    }

    /* Image styles */
    .object-cover {
        object-fit: cover;
    }

    .object-contain {
        object-fit: contain;
    }

    .rounded-md {
        border-radius: 0.375rem;
    }

    .cursor-pointer {
        cursor: pointer;
    }

    /* Transition effects */
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 200ms;
    }

    .hover\:scale-105:hover {
        transform: scale(1.05);
    }

    .hover\:border-blue-500:hover {
        border-color: #3b82f6;
    }

    /* Modal styles */
    .fixed {
        position: fixed;
    }

    .inset-0 {
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
    }

    .z-\[100\] {
        z-index: 100;
    }

    .bg-black\/95 {
        background-color: rgba(0, 0, 0, 0.95);
    }

    /* Tooltip */
    .relative {
        position: relative;
    }

    .group:hover .group-hover\:opacity-100 {
        opacity: 1;
    }

    .absolute {
        position: absolute;
    }

    .bottom-full {
        bottom: 100%;
    }

    .left-1\/2 {
        left: 50%;
    }

    .transform {
        transform: translateX(-50%);
    }

    .-translate-x-1\/2 {
        transform: translateX(-50%);
    }

    .mb-2 {
        margin-bottom: 0.5rem;
    }

    .px-2 {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }

    .py-1 {
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
    }

    .bg-gray-900 {
        background-color: #111827;
    }

    .text-white {
        color: white;
    }

    .text-xs {
        font-size: 0.75rem;
    }

    .rounded {
        border-radius: 0.25rem;
    }

    .opacity-0 {
        opacity: 0;
    }

    .pointer-events-none {
        pointer-events: none;
    }

    .z-50 {
        z-index: 50;
    }

    /* Keep original table structure */
    table {
        border-collapse: collapse;
    }

    td {
        vertical-align: middle;
    }

    .w-24 {
        width: 6rem;
    }

    .h-24 {
        height: 6rem;
    }

    .border-2 {
        border-width: 2px;
    }

    .border-gray-300 {
        border-color: #d1d5db;
    }

    .inline-block {
        display: inline-block;
    }

    @page {
        size: A4;
        margin: 0;
    }

    /* ================= PRINT STYLES ================= */
    @media print {
        body * {
            visibility: hidden !important;
        }

        .certificate-wrapper,
        .certificate-wrapper * {
            visibility: visible !important;
        }

        .certificate-wrapper {
            position: absolute !important;
            top: 20px;
            left: 0;
            width: 210mm !important;
            height: 297mm !important;
            overflow: hidden !important;
        }

        /* Hide modal in print */
        .fixed {
            display: none !important;
        }

        /* Hide loading/error states when printing */
        .text-center.py-\[25vh\],
        .text-center.py-8 {
            display: none !important;
        }

        /* Ensure image prints properly */
        img {
            max-width: 100% !important;
            page-break-inside: avoid !important;
        }
    }
</style>
