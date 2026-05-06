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
                                <div class="font-nastaleeq mb-4">(فارم A-1)</div>
                                <div class="font-nastaleeq title">
                                    تحصیل میرپور
                                </div>
                                <div class="font-nastaleeq rule-box">
                                    (قواعد باشندہ ریاست آزاد جموں و کشمیر مجریہ۱۹۸۰ کا قاعدہ نمبر ۷ ملاحظہ ہو)
                                </div>
                                <div class="font-nastaleeq sub-title">
                                    درخواست برائے حصول باشندہ ریاست آزاد جموں و کشمیرمجریہ ۱۹۸۰ء
                                </div>
                            </td>

                            <td class="right-col">
<!--                                <img src="/assets/images/secondoloho.png" />-->
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- PERSONAL INFO -->
                <table class="mt-7">
                    <tbody>
                        <tr>
                            <td class="font-nastaleeq" width="200px">درخوست دہندہ کا پورا نام</td>
                            <td class="line font-nastaleeq">{{ application.full_name || '' }} </td>
                        </tr>
                        <tr>
                            <td class="font-nastaleeq" width="200px">زوجہ</td>
                            <td class="line font-nastaleeq">{{ application.wife_husband_name || 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="font-nastaleeq" width="200px">پتہ</td>
                            <td class="line font-nastaleeq">{{ application.address || '' }}</td>
                        </tr>
                        <tr>
                            <td class="font-nastaleeq" width="200px"> ریاست جموں و کشمیر میں اگر درخواست دہندہ مہاجر ہو
                            </td>
                            <td class="line font-nastaleeq">{{ application.address2 || '' }}</td>
                        </tr>
                        <tr>
                            <td class="font-nastaleeq" width="200px">آزاد جموں و</td>
                            <td class="line font-nastaleeq">{{ application.address3 || '' }}</td>
                        </tr>
                        <tr>
                            <td class="font-nastaleeq" width="200px">آزاد جموں و کشمیر سے باہر</td>
                            <td class="line font-nastaleeq">{{ application.address4 || '' }}</td>
                        </tr>
                    </tbody>
                </table>

                <table class="flex flex-items-center justify-center mt-3">
                    <tbody>
                        <tr>
                            <td class="font-nastaleeq font-[600]"> (جو کالم نا قابل اطلاق ہو اسے حذف کر دیں)</td>
                        </tr>
                    </tbody>
                </table>

                <table class="w-full">
                    <tbody>
                        <tr>
                            <td class="font-nastaleeq" width="50">پتہ</td>
                            <td class="line font-nastaleeq">{{ application.address || '' }}</td>
                            <td class="font-nastaleeq" width="60">تحصیل</td>
                            <td class="line font-nastaleeq">{{ getTehsilName() }}</td>
                            <td class="font-nastaleeq" width="50">ضلع</td>
                            <td class="line font-nastaleeq">{{ getDistrictName() }}</td>
                        </tr>
                    </tbody>
                </table>

                <table class="mt-2">
                    <tbody>
                        <tr>
                            <td class="font-nastaleeq" width="200px">پیدائیش کی جگہ و تاریخ</td>
                            <td class="line font-nastaleeq"> {{ formatDMY(application.dob) }} - {{ application.pob ||
                                '' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="font-nastaleeq" width="200px"> شادی شدہ/غیرشادی شدہ/رنڈہ/بیوہ</td>
                            <td class="line font-nastaleeq">{{ getMaritalStatus(application.marital_status_id ||
                                application.marital_status_id) }}</td>
                        </tr>
                        <tr>
                            <td class="font-nastaleeq" width="200px"> بیوی/شوہر کا نام</td>
                            <td class="line font-nastaleeq">{{ application.wife_husband_name || 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="flex mt-10 justify-between">
                    <table class="w-[40%]">
                        <tbody>
                            <tr>
                                <td class="font-nastaleeq font-[600]" width="200px">بچوں کے نام اور اُنکی عمریں:</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- CHILDREN -->
                    <table class="child-table mt-4 mx-auto">
                        <thead>
                            <tr>
                                <th class="font-nastaleeq"> نام</th>
                                <th class="font-nastaleeq">عمر</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(child, index) in children" :key="index">
                                <td>{{ child.name || '&nbsp;' }}</td>
                                <td>{{ child.age || '&nbsp;' }}</td>
                            </tr>
                            <!-- Fill empty rows if needed -->
                            <tr v-for="i in Math.max(0, 2 - children.length)" :key="'empty-' + i">
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <table class="mt-4">
                    <tbody>
                        <tr>
                            <td class="font-nastaleeq" width="400px"> آیا درخواست دہندہ نے کسی وقت اس پیشترسرٹیفکیٹ
                                باشندہ
                                آزاد جموں و کشمیر کے لیے درخواست دی تھی۔</td>
                            <td class="line font-nastaleeq">{{ application.previous_application ? 'جی ہاں' : 'نہیں' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="font-nastaleeq">وجوہات جن کی بناء سرٹیفکیٹ باشندہ ریاست جموں و کشمیرکے لیے
                                درخواست دی
                                تھی۔</td>
                            <td class="line font-nastaleeq">{{ application.application_reason || 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="font-nastaleeq font-[600]">میں اقرار کرتا/ کرتی ہوں کہ مندرجہ بالا دی گئی اطلاعات
                                میرے علم و یقین کے مطابق درست ہیں-</td>
                        </tr>
                    </tbody>
                </table>

                <table class="w-[45%] mt-5">
                    <tbody>
                        <tr>
                            <td class="font-nastaleeq" width="150px"> درخواست دہندہ کا نام معہ دستخط</td>
                            <td class="line">{{ application.first_name || '' }} {{ application.last_name || '' }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- VERIFIER & FINGERPRINTS -->
                <div class="flex mt-6 gap-6">
                    <!-- Signature section -->
                    <div class="flex-1">
                        <table class="w-[90%]">
                            <tbody>
                                <tr>
                                    <td class="font-nastaleeq" width="160px">تصدیق کنندہ کے دستخط</td>
                                    <td class="line"></td>
                                </tr>
                                <tr>
                                    <td class="font-nastaleeq" width="120px">تصدیق کنندہ کا نام</td>
                                    <td class="line">{{ application.authority_name || '' }}</td>
                                </tr>
                                <tr>
                                    <td class="font-nastaleeq" width="80px">عہدہ</td>
                                    <td class="line">{{ application.authority_designation || '' }}</td>
                                </tr>
                                <tr>
                                    <td class="font-nastaleeq" width="120px">تاریخ</td>
                                    <td class="line">{{ formatDMY(application.entry_date || application.created_at) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-nastaleeq" width="50px">جگہ</td>
                                    <td class="line"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Fingerprint Section -->
                    <div class="flex-1 mr-10" >
                        <div class="text-right font-nastaleeq text-xs mb-1">نشان انگوٹھا / فنگر پرنٹس:</div>
                        <table class="child-table">
                            <tbody>
                                <tr>
                                    <td>
                                        <img v-if="application?.biometrics?.thumb"
                                            :src="application.biometrics.thumb.image_path"
                                            class="w-20 h-20 mx-auto object-contain">
                                        <div v-else class="h-20"></div>
                                    </td>
                                    <td>
                                        <img v-if="application?.biometrics?.index"
                                            :src="application.biometrics.index.image_path"
                                            class="w-20 h-20 mx-auto object-contain">
                                        <div v-else class="h-20"></div>
                                    </td>
                                    <td>
                                        <img v-if="application?.biometrics?.middle"
                                            :src="application.biometrics.middle.image_path"
                                            class="w-20 h-20 mx-auto object-contain">
                                        <div v-else class="h-20"></div>
                                    </td>
                                     <td>
                                        <img v-if="application?.biometrics?.ring"
                                            :src="application.biometrics.ring.image_path"
                                            class="w-20 h-20 mx-auto object-contain">
                                        <div v-else class="h-20"></div>
                                    </td>
                                    <td>
                                        <img v-if="application?.biometrics?.little"
                                            :src="application.biometrics.little.image_path"
                                            class="w-20 h-20 mx-auto object-contain">
                                        <div v-else class="h-20"></div>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { useRoute } from 'vue-router';
import { formatDMY } from '@/mixin/index';


// ------------------ STATE ------------------
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
}
// ------------------ LOAD LOCALSTORAGE ------------------
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

// ------------------ FETCH APPLICATION ------------------
async function fetchApplication() {
  try {
    loading.value = true;
    error.value = null;

    const id = route.params.id;
    if (!id) throw new Error('Application ID not found');

    const res = await axios.get(`/api/applications/${id}`);
    console.log('Application response:', res.data);
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

    // Load groupedData from localStorage
    loadDropdownData();
  } catch (err) {
    error.value = err.message;
    Swal.fire({ icon: 'error', title: 'Error', text: err.message, confirmButtonText: 'OK' });
  } finally {
    loading.value = false;
  }
}

// ------------------ COMPUTED FILTERS ------------------

// Regions
// const filteredRegions = computed(() => groupedData.value.regions || []);

// Districts filtered by application region
const filteredDistricts = computed(() => {
  if (!application.value) return [];
  const region = groupedData.value.regions?.find(r => r.id == application.value.region_id);
  return region?.districts || [];
});

// Tehsils filtered by application district
const filteredTehsils = computed(() => {
  if (!application.value) return [];
  const district = filteredDistricts.value.find(d => d.id == application.value.district_id);
  return district?.tehsils || [];
});

// ------------------ HELPER FUNCTIONS ------------------
function getDisplayValue(type, id) {
  if (!id) return '';
  let list = [];
  if (type === 'region') list = groupedData.value.regions || [];
  else if (type === 'district') list = filteredDistricts.value;
  else if (type === 'tehsil') list = filteredTehsils.value;
  else if (type === 'marital_status') list = groupedData.value.maritalStatuses || [];

  const item = list.find(i => i.id == id);
  return item ? (item.urdu_name || item.name) : '';
}



// function getRegionName() { return getDisplayValue('region', application.value?.region_id); }
function getDistrictName() { return getDisplayValue('district', application.value?.district_id); }
function getTehsilName() { return getDisplayValue('tehsil', application.value?.tehsil_id); }
function getMaritalStatus(id) {
  if (!id) return '';

  const list = groupedData.value.marital_status || [];
  const item = list.find(i => i.id == id);

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

function goBack() {
  window.history.back();
}


// ------------------ MOUNT ------------------
onMounted(() => {
  fetchApplication();
});
</script>
<style>
.certificate-wrapper {
    padding: 10px;
}

.certificate {
    border: 1px solid #000;
    padding: 10px;
    font-size: 14px;
    max-width: 210mm;
    margin: 0 auto;
}

.left-col {
    width: 150px;
    text-align: center;
    vertical-align: top;
    border: none !important;
}

.right-col {
    width: 150px;
    text-align: center;
    border: none !important;
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
    min-height: 25px;
    min-width: 60px;
    padding: 2px !important;
}

tbody tr td {
    padding-top: 0.35rem !important;
    padding-bottom: 0.35rem !important;
    padding-left: 1rem;
    padding-right: 1rem;
}

@page {
    size: A4;
    margin: 0;
}

/* ================= PRINT FINAL LOCK ================= */
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

    /* Hide loading/error states when printing */
    .text-center {
        display: none !important;
    }
}
</style>
