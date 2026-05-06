<template>
    <div class="certificate-wrapper">
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-[25vh]">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4"></div>
            <p class="text-gray-600 font-nastaleeq text-2xl">...ڈیٹا لوڈ ہو رہا ہے</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="alert alert-danger">
            <h4>Error Loading Application</h4>
            <p>{{ error }}</p>
            <button @click="fetchApplication" class="btn btn-primary mt-2">Try Again</button>
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
            <div id="certificate" class="certificate" dir="rtl">

                <!-- ================= HEADER ================= -->
                <table class="w-full">
                    <tbody>
                        <tr>
                            <td class="font-nastaleeq left-col">
                                فارم نمبر : <u>A-1</u>
<!--                                <img src="/assets/images/qr.png" alt="">-->
                            </td>

                            <td class="text-center">
                                <div class="font-nastaleeq mb-4">(فارم P-1)</div>
                                <div class="font-nastaleeq title">
                                    آزاد جموں و کشمیر کونسل
                                </div>
                                <div class="font-nastaleeq rule-box">
                                    (قواعد باشندہ ریاست آزاد جموں و کشمیر مجریہ۱۹۸۰ کا قاعدہ نمبر ۷ ملاحظہ ہو)
                                </div>
                                <div class="font-nastaleeq sub-title">
                                    ڈومیسائل سرٹیفکیٹ
                                </div>
                            </td>

                            <td class="right-col">
<!--                                <img src="/assets/images/secondoloho.png" />-->
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- ================= BODY ================= -->
                <table class="w-full mt-2">
                    <tbody>
                        <tr>
                            <td class="font-nastaleeq"> مسمی</td>
                            <td class="line" width="15%">{{ application.full_name || '&nbsp;' }}</td>
                            <td class="font-nastaleeq"> زوجہ</td>
                            <td class="line" width="15%">{{ application.wife_husband_name || '&nbsp;' }}</td>
                            <td class="font-nastaleeq "> نے ازروئے قانون باشندہ ریاست آزاد جموں و کشمیر مجریہ
                                1980ء برائے حصول ڈومیسائل سرٹیفکیٹ درخواست دی ہے۔</td>
                        </tr>
                    </tbody>
                </table>

                <table class="w-full mt-2">
                    <tbody>
                        <tr>
                            <td class="font-nastaleeq"> مندرجہ ذیل کوائف جو کہ درخواست دہندہ سے متعلق ہیں۔ زیر
                                دستخطی ان سے مطمئن ہے۔ اور وہ تمام شرائط جو کہ مذکورہ قانون کی دفعہ 5 کی رو سے بروئے مثل
                                نمبر</td>

                            <td class="line " width="10%">{{ application.missal_no || '&nbsp;' }}</td>
                            <td class="font-nastaleeq"> مسمی</td>
                            <td class="line" width="10%">{{ application.full_name || '&nbsp;' }}</td>
                        </tr>
                    </tbody>
                </table>

                <table class="w-[90%] mt-2">
                    <tbody>
                        <tr>
                            <td class="font-nastaleeq">پر برائے حصول ڈومیسائل سرٹیفکیٹ عائد ہوتیہیں کو پورا کرتا/
                                کرتی ہے ۔ مذکورہ قانون اور اس کے بنائے گئے قواعد کے مطابق زیردستخطی مسمی </td>
                            <td class="line" width="10%">{{ application.full_name || '&nbsp;' }}</td>
                            <td class="font-nastaleeq">کو ڈومیسائل جاری کرتا ہے۔ </td>
                        </tr>
                    </tbody>
                </table>

                <!-- ================= SIGN ================= -->
                <table class="w-full mt-6">
                    <tbody>
                        <tr>
                            <td></td>
                            <td class="text-end">
                                <div class="font-nastaleeq">ڈسٹرکٹ مجسٹریٹ</div>
                                <div class="font-nastaleeq">ضلع آزاد کشمیر</div>
                            </td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <div class="font-nastaleeq text-center mt-5 sub-title"> درخوست دہندہ سے متعلقہ کوائف </div>

                <!-- ================= DETAILS ================= -->
                <table class="w-full mt-4">
                    <tbody>
                        <tr>
                            <td class="font-nastaleeq" width="60">مکمل نام</td>
                            <td class="line text-center font-nastaleeq">{{ application.full_name || '&nbsp;' }}</td>
                            <td class="font-nastaleeq" width="60">زوجہ</td>
                            <td class="line text-center font-nastaleeq">{{ application.wife_husband_name || '&nbsp;' }}
                            </td>
                        </tr>
                    </tbody>
                </table>

                <table class="w-full">
                    <tbody>
                        <tr>
                            <td class="font-nastaleeq" width="60">پتہ</td>
                            <td class="line font-nastaleeq">{{ application.address || '&nbsp;' }}</td>
                        </tr>
                    </tbody>
                </table>
                <table class="w-full">
                    <tbody>
                        <tr>
                            <td class="font-nastaleeq" width="90">ڈومیسائل کی جگہ</td>
                            <td class="line font-nastaleeq">{{ application.location || '&nbsp;' }}</td>
                            <td class="font-nastaleeq" width="90">تحصیل</td>
                            <td class="line font-nastaleeq">{{ getTehsilName() || '&nbsp;'
                                }}
                            </td>
                            <td class="font-nastaleeq" width="90">ضلع</td>
                            <td class="line font-nastaleeq">{{ getDistrictName() ||
                                '&nbsp;'
                                }}</td>
                        </tr>
                    </tbody>
                </table>

                <table class="w-full">
                    <tbody>
                        <tr>
                            <td class="font-nastaleeq" width="90">ازدواجی حیثیت</td>
                            <td class="line font-nastaleeq">{{ getMaritalStatus(application?.marital_status ||
                                application?.marital_status_id) }}</td>
                            <td class="font-nastaleeq" width="90">بیوی/شوہر کا نام</td>
                            <td class="line font-nastaleeq">{{ application.wife_husband_name || '&nbsp;' }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="flex mt-10 justify-between">
                    <table class="w-[40%] mt-0">
                        <tbody>
                            <tr>
                                <td class="font-nastaleeq" width="60">دستخط</td>
                                <td class="line font-nastaleeq"></td>
                            </tr>
                            <tr>
                                <td class="font-nastaleeq" width="100">پیشہ</td>
                                <td class="line font-nastaleeq">{{ application.occupation || '&nbsp;' }}</td>
                            </tr>
                            <tr>
                                <td class="font-nastaleeq" width="100">شناختی علامت</td>
                                <td class="line font-nastaleeq">{{ application.identity_symbol || '&nbsp;' }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <table class="child-table">
                        <tbody>
                            <tr>
                                <th class="font-nastaleeq">بچوں کے نام</th>
                                <th class="font-nastaleeq">عمر</th>
                            </tr>
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

                <div class="flex mt-7 justify-between">
                    <table class="w-[40%] mt-0">
                        <tbody>
                            <tr>
                                <td class="font-nastaleeq" width="60">دستخط</td>
                                <td class="line font-nastaleeq"></td>
                            </tr>
                            <tr>
                                <td class="font-nastaleeq" width="60">نام</td>
                                <td class="line font-nastaleeq">{{ application.authority_name || '&nbsp;' }}</td>
                            </tr>
                            <tr>
                                <td class="font-nastaleeq" width="100">عہدہ</td>
                                <td class="line font-nastaleeq">{{ application.authority_designation || '&nbsp;' }}</td>
                            </tr>
                            <tr>
                                <td class="font-nastaleeq" width="100">تاریخ</td>
                                <td class="line font-nastaleeq">&nbsp;</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- IMAGE DISPLAY SECTION - FIXED -->
                    <table class="w-[20%] mt-4">
                        <tbody>
                            <tr>
                                <td class="flex justify-center mt-0 left-col">
                                    <div class="relative group">
                                        <!-- Image with preview functionality -->
                                        <img v-if="application.personal_image"
                                            :src="getFullImageUrl(application.personal_image)" alt="Personal Photo"
                                            class="w-[100%] h-[100%] object-cover rounded-md border border-gray-300 cursor-pointer"
                                            @click="openPhotoModal(getFullImageUrl(application.personal_image))"
                                            @error="handleImageError" />

                                        <!-- Placeholder when no image exists -->
                                        <div v-else
                                            class="w-20 h-20 bg-gray-100 rounded-md border border-gray-300 flex items-center justify-center">
                                            <i class="fa-solid fa-user text-gray-400 text-2xl"></i>
                                        </div>

                                        <!-- Tooltip -->
                                        <div
                                            class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap pointer-events-none z-50">
                                            Click to preview
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Photo Preview Modal -->
            <div v-if="showPhotoModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/95 p-4"
                @click.self="closePhotoModal">
                <button @click="closePhotoModal"
                    class="absolute top-6 right-6 text-white hover:text-gray-300 text-4xl font-light z-10">
                    &times;
                </button>
                <div class="max-w-[90vw] max-h-[90vh] flex items-center justify-center">
                    <img :src="photoPreview" alt="Photo Preview"
                        class="min-w-[100%] min-h-[100%] object-contain rounded-lg mx-auto" @click.stop>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed,onUnmounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import Swal from 'sweetalert2';
import html2pdf from 'html2pdf.js';
import { formatDMY } from '@/mixin/index';

// -------------------- REFS --------------------
const loading = ref(true);
const error = ref(null);
const application = ref(null);
const children = ref([]);
const showPhotoModal = ref(false);
const photoPreview = ref(null);
const route = useRoute();
const groupedData = ref({
  regions: [],
  maritalStatuses: []
});



// -------------------- FETCH LOCALSTORAGE --------------------
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

// -------------------- API FETCH --------------------
async function fetchApplication() {
  try {
    loading.value = true;
    error.value = null;

    const id = route.params.id;
    if (!id) throw new Error('Application ID not found');

    const res = await axios.get(`/api/applications/${id}`);
    console.log('API Response:', res.data);
    if (!res.data.success) throw new Error(res.data.message || 'Failed to load');

    application.value = {
      ...res.data.data.applicant,
      ...res.data.data.application
    };

    // Parse children
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

// -------------------- COMPUTED / FILTERED --------------------
const filteredDistricts = computed(() => {
  if (!application.value) return [];
  // Assuming region_id exists if you are using groupedData
  const region = groupedData.value.regions?.find(r => r.id === application.value.region_id);
  return region?.districts || [];
});

// -------------------- HELPERS --------------------
function getDistrictName() {
  if (!application.value) return '';
  const district = filteredDistricts.value.find(d => d.id == application.value.district_id);
  return district ? (district.urdu_name || district.name) : '';
}

function getTehsilName() {
  if (!application.value) return '';
  const district = filteredDistricts.value.find(d => d.id == application.value.district_id);
  const tehsil = district?.tehsils.find(t => t.id == application.value.tehsil_id);
  return tehsil ? (tehsil.urdu_name || tehsil.name) : '';
}
function getMaritalStatus(id) {
  if (!id) return '';

  const list = groupedData.value.marital_status || [];
  const item = list.find(i => i.id == id);

  return item ? (item.urdu_name || item.name) : '';
}

// function calculateAge(dob) {
//   if (!dob) return '';
//   const birth = new Date(dob);
//   const today = new Date();
//   let age = today.getFullYear() - birth.getFullYear();
//   if (today.getMonth() < birth.getMonth() || (today.getMonth() === birth.getMonth() && today.getDate() < birth.getDate())) {
//     age--;
//   }
//   return `${age} سال`;
// }



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

// -------------------- MODAL --------------------
function openPhotoModal(url) {
  photoPreview.value = url;
  showPhotoModal.value = true;
  document.body.style.overflow = 'hidden';
}

function closePhotoModal() {
  showPhotoModal.value = false;
  photoPreview.value = null;
  document.body.style.overflow = 'auto';
}

function handleKeydown(event) {
  if (event.key === 'Escape' && showPhotoModal.value) {
    closePhotoModal();
  }
}

// -------------------- PRINT / PDF --------------------
function handlePrint() {
  window.print();
}

function downloadPDF() {
  const element = document.getElementById('certificate');
  if (!element) return;

  const opt = {
    margin: [10, 10, 10, 10],
    filename: 'domicile.pdf',
    image: { type: 'jpeg', quality: 0.98 },
    html2canvas: { scale: 2, logging: true, useCORS: true },
    jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
  };

  html2pdf().set(opt).from(element).save();
}

// -------------------- LIFECYCLE --------------------
onMounted(() => {
  fetchApplication();
  window.addEventListener('keydown', handleKeydown);
});

// onUnmounted(() => {
//   window.removeEventListener('keydown', handleKeydown);
// });
</script>

<style>
/* ================= NORMAL VIEW ================= */
.certificate-wrapper {
    padding: 20px;
}

.certificate {
    border: 1px solid #000;
    padding: 15px;
    background: white;
    max-width: 210mm;
    margin: 0 auto;
}

.left-col,
.right-col {
    width: 150px;
    text-align: center;
    vertical-align: top;
    border: 1px solid #000;
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
    border-bottom: 1px solid #000;
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
}

.child-table th,
.child-table td {
    border: 1px solid #000;
    height: 25px;
    text-align: center;
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
    padding-top: 0.15rem;
    padding-bottom: 0.15rem;
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

/* ================= PAGE SETUP ================= */
@page {
    size: A4;
    margin: 0;
}

/* ================= PRINT FINAL LOCK ================= */
@media print {

    /* Hide everything first */
    body * {
        visibility: hidden !important;
    }


    .certificate-wrapper * {
        visibility: visible !important;
    }

    /* Hide modal in print */
    /* .fixed {
        display: none !important;
    } */

    th, table tbody tr td {
    padding-top: 0.25rem;
    padding-bottom: 0.45rem;
    padding-left: 0.4rem;
    padding-right: 0.4rem;
}




}
</style>
