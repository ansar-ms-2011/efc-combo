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

        <!-- Certificate Content -->
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
                <!-- ================= HEADER ================= -->
                <table class="w-full">
                    <tbody>
                    <tr>
                        <td class="font-nastaleeq left-col">
                            فارم-A
                        </td>

                        <td class="center-col">
<!--                            <img src="/assets/images/mirpur-statesubject.png" />-->
                        </td>

                        <td class="right">
                            <!-- Empty for alignment -->
                        </td>
                    </tr>
                    </tbody>
                </table>

                <!-- ================= BODY ================= -->
                <table class="w-full mt-2">
                    <tbody>
                    <tr>
                        <td class="font-nastaleeq" width="120">تصدیق کیا جاتا ہے کہ مسمی</td>
                        <td class="line font-nastaleeq">{{ application.full_name || '' }}
                        </td>
                    </tr>
                    </tbody>
                </table>

                <table class="w-full">
                    <tbody>
                    <tr>
                        <td class="font-nastaleeq" width="50">ساکن</td>
                        <td class="line font-nastaleeq">{{ application.residence_place || '' }}</td>
                        <td class="font-nastaleeq" width="60">تحصیل</td>
                        <td class="line font-nastaleeq">{{ getTehsilName() }}</td>
                        <td class="font-nastaleeq" width="50">ضلع</td>
                        <td class="line font-nastaleeq">{{ getDistrictName() }}</td>
                    </tr>
                    </tbody>
                </table>

                <table class="w-full">
                    <tbody>
                    <tr>
                        <td class="font-nastaleeq" width="140">بروئے تحقیقات برمثل</td>
                        <td class="line font-nastaleeq" width="250">{{ application.missal_no || '' }}</td>
                        <td class="font-nastaleeq" width="150">باشندہ ریاست جموں و کشمیر درجہ</td>
                        <td class="line font-nastaleeq">{{ application.state_subject_class || '' }}</td>
                        <td class="font-nastaleeq">ہے۔</td>
                    </tr>
                    </tbody>
                </table>

                <table class="w-full">
                    <tbody>
                    <tr>
                        <td class="font-nastaleeq" width="140"> لہذا قانون باشندہ ریاست جموں و کشمیر مجریہ ۱۹۸۰ء کے تحت
                            سرٹیفکیٹ
                            جاری کیا جاتا ہے۔
                        </td>
                    </tr>
                    </tbody>
                </table>

                <!-- ================= SIGN ================= -->
                <table class="w-full mt-6">
                    <tbody>
                    <tr>
                        <td></td>
                        <td class="text-center">
                            <div class="font-nastaleeq">ڈسٹرکٹ مجسٹریٹ</div>
                            <div class="font-nastaleeq">ضلع آزاد کشمیر</div>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <!-- ================= DETAILS ================= -->
                <table class="w-full mt-4">
                    <tbody>
                    <tr>
                        <td class="font-nastaleeq font-[600]" width="60">تفصیل درخواست دہندہ:</td>
                    </tr>
                    </tbody>
                </table>

                <table class="w-full mt-4">
                    <tbody>
                    <tr>
                        <td class="font-nastaleeq" width="60">نام</td>
                        <td class="line text-center ">{{ application.full_name || '' }}
                        </td>
                        <td class="font-nastaleeq" width="60">زوجہ</td>
                        <td class="line text-center">{{ application.wife_husband_name || 'N/A' }}</td>
                    </tr>
                    </tbody>
                </table>

                <table class="w-full">
                    <tbody>
                    <tr>
                        <td class="font-nastaleeq" width="60">پتہ</td>
                        <td class="line font-nastaleeq">{{ application.address || '' }}</td>
                    </tr>
                    </tbody>
                </table>

                <table class="w-full">
                    <tbody>
                    <tr>
                        <td class="font-nastaleeq" width="90">ازدواجی حیثیت</td>
                        <td class="line font-nastaleeq">{{ getMaritalStatus(application.marital_status ||
                            application.marital_status_id) }}
                        </td>
                        <td class="font-nastaleeq" width="90">بیوی/شوہر کا نام</td>
                        <td class="line font-nastaleeq">{{ application.wife_husband_name || 'N/A' }}</td>
                        <td class="font-nastaleeq" width="90">شناختی کارڈ نمبر</td>
                        <td class="line">{{ application.identity_number || '' }}</td>
                    </tr>
                    </tbody>
                </table>

                <table class="w-full">
                    <tbody>
                    <tr>
                        <td class="font-nastaleeq" width="90">تاریخ پیدائش</td>
                        <td class="line">{{ formatDMY(application.dob) }}</td>
                        <td class="font-nastaleeq" width="90">پیشہ</td>
                        <td class="line font-nastaleeq">{{ application.occupation || '' }}</td>
                    </tr>
                    </tbody>
                </table>

                <table class="w-full">
                    <tbody>
                    <tr>
                        <td class="font-nastaleeq" width="90">شناختی علامت</td>
                        <td class="line">{{ application.identity_symbol || '' }}</td>
                    </tr>
                    </tbody>
                </table>

                <!-- Children table -->
                <div class="flex mt-10 justify-between">
                    <table class="w-[40%]">
                        <tbody>
                        <tr>
                            <td class="font-nastaleeq font-[600]" width="200px">بچوں کے نام اور اُنکی عمریں:</td>
                        </tr>
                        </tbody>
                    </table>

                    <!-- CHILDREN -->
                    <table class="child-table mx-auto">
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

                <div class="flex">
                    <table class="w-[45%] mt-2">
                        <tbody>
                        <tr>
                            <td class="font-nastaleeq" width="100">دستخط یا نشان انگوٹھا</td>
                            <td class="line font-nastaleeq">{{ application.signature_thumb || '' }}</td>
                        </tr>
                        </tbody>
                    </table>

                    <table class="w-[55%] mt-2">
                        <tbody>
                        <tr>
                            <td class="font-nastaleeq" width="100">بائیں ہاتھ کے انگوٹھا یا انگلیوں کے نشان <br><small>(خواتین
                                کے
                                دائیں ہاتھ کے انگوٹھا/ انگلیوں کے نشان)</small></td>
                        </tr>
                        </tbody>
                    </table>

                    <table class="child-table mt-5">
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

                <div class="grid grid-cols-3 items-center mt-6">
                    <!-- Left -->
                    <div>
<!--                        <img src="/assets/images/secondoloho.png" alt="">-->
                    </div>

                    <!-- Center -->
                    <div class="font-nastaleeq">ڈسٹرکٹ مجسٹریٹ ضلع آزاد کشمیر</div>

                    <!-- Right empty -->
                    <div class="flex flex-col items-center text-center">
                        <table>
                            <tbody>
                            <tr>
                                <td class="font-nastaleeq" width="60px">تاریخ</td>
                                <td class="line"></td>
                            </tr>
                            <tr>
                                <td class="font-nastaleeq">ضلع نمبر</td>
                                <td class="line">{{ application.district_number || '' }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- <script>
    import axios from 'axios';

    export default {
        name: 'CertificateForm',
        data() {
            return {
                loading: true,
                error: null,
                application: null,
                children: [],
                lookupData: {
                    districts: [],
                    tehsils: [],
                    maritalStatuses: []
                }
            };
        },
        async mounted() {
            await this.fetchApplication();
        },
        methods: {
            async fetchApplication() {
                try {
                    this.loading = true;
                    this.error = null;

                    const id = this.$route.params.id;
                    if (!id) throw new Error('Application ID not found');

                    const response = await axios.get(`/api/applications/${id}`);
                    if (!response.data.success) throw new Error(response.data.message);

                    this.application = response.data.data;

                    // Parse children if exists
                    if (this.application.children) {
                        this.children = Array.isArray(this.application.children)
                            ? this.application.children
                            : JSON.parse(this.application.children || '[]');
                    }

                    await Promise.all([
                        this.fetchDistricts(),
                        this.fetchLookupType('marital_status', 'maritalStatuses')
                    ]);

                    if (this.application.district) {
                        await this.fetchTehsils(this.application.district);
                    }

                } catch (error) {
                    this.error = error.message;
                    console.error('Error fetching application:', error);
                } finally {
                    this.loading = false;
                }
            },

            async fetchLookupType(type, property) {
                try {
                    const response = await axios.get(`/api/types/parent/${type}`);
                    this.lookupData[property] = response.data || [];
                } catch (error) {
                    console.error(`Error fetching ${type}:`, error);
                    this.lookupData[property] = [];
                }
            },

            async fetchDistricts() {
                try {
                    const response = await axios.get('/api/districts');
                    this.lookupData.districts = response.data.data?.data || response.data.data || response.data || [];
                } catch (error) {
                    console.error('Error fetching districts:', error);
                    this.lookupData.districts = [];
                }
            },

            async fetchTehsils(districtId) {
                try {
                    const response = await axios.get(`/api/demographies?type=TEHSIL&district_id=${districtId}`);
                    this.lookupData.tehsils = response.data.data?.data || response.data.data || response.data || [];
                } catch (error) {
                    console.error('Error fetching tehsils:', error);
                    this.lookupData.tehsils = [];
                }
            },

            getDisplayValue(type, value) {
                if (!value) return '';

                if (typeof value === 'object' && value !== null) {
                    return value.urdu_name || value.name || '';
                }

                const lookupMap = {
                    'district': this.lookupData.districts,
                    'tehsil': this.lookupData.tehsils,
                    'marital_status': this.lookupData.maritalStatuses
                };

                const items = lookupMap[type] || [];
                const item = items.find(i => i.id == value);
                return item ? (item.urdu_name || item.name) : '';
            },

            getDistrictName() {
                return this.getDisplayValue('district', this.application?.district);
            },

            getTehsilName() {
                return this.getDisplayValue('tehsil', this.application?.city);
            },

            getMaritalStatus(statusId) {
                return this.getDisplayValue('marital_status', statusId);
            },

            formatDate(dateString) {
                if (!dateString) return '';
                try {
                    const date = new Date(dateString);
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const year = date.getFullYear();
                    return `${day}-${month}-${year}`;
                } catch (error) {
                    return '';
                }
            },
            getFullUrl(path) {
                if (!path) return '';
                if (path.startsWith('http')) return path;
                const baseUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000';
                return `${baseUrl}${path.startsWith('/') ? '' : '/'}${path}`;
            },

            goBack() {
                if (window.opener) {
                    window.close();
                } else {
                    this.$router.back();
                }
            }
        }
    };
</script> -->

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
    .certificate-wrapper {
        padding: 10px;
    }

    .certificate {
        border: 1px solid #000;
        padding: 10px;
        max-width: 210mm;
        margin: 0 auto;
    }

    .left-col {
        width: 80px;
        text-align: center;
        vertical-align: top;
        border: none !important;
    }

    .center-col {
        width: 150px;
        text-align: center;
        vertical-align: top;
    }

    .right {
        width: 80px;
        height: 50px;
        text-align: center;
        vertical-align: center;
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

    .print-btn {
        margin-top: 20px;
        background: green;
        color: #fff;
        padding: 8px 15px;
        border-radius: 5px;
    }

    table tbody tr td {
        padding-top: 0.35rem !important;
        padding-bottom: 0.35rem !important;
        padding-left: 1rem;
        padding-right: 1rem;
    }

    @media print {
        .print-btn {
            display: none;
        }

        body,
        html {
            margin: 0;
            padding: 0;
        }

        body * {
            visibility: hidden;
        }

        .certificate-wrapper * {
            visibility: visible;
        }

        .certificate-wrapper {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            border: 1px solid;
        }

        /* Hide loading/error states when printing */
        .text-center {
            display: none !important;
        }
    }
</style>
