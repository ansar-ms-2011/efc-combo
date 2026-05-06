<template>
    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- Loading State -->
            <div v-if="loading" class="text-center py-[25vh]">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4"></div>
                <p class="text-gray-600 font-nastaleeq text-2xl">...ڈیٹا لوڈ ہو رہا ہے</p>
            </div>

            <!-- Error State (only shown if error) -->
            <div v-else-if="error" class="alert alert-danger">
                <h4>Error Loading Application</h4>
                <p>{{ error }}</p>
                <button @click="fetchApplication" class="btn btn-primary mt-2">
                    Try Again
                </button>
            </div>

            <!-- Main Content (shown when data is loaded) -->
            <div v-else-if="application">
                <!-- ================= QR & SERVICE INFO ================= -->
                <table class="mt-2 w-full view-header-table">
                    <tbody>
                    <tr>
                        <td>
                            <img :src="application.qr_code_url" width="100" height="100" alt="QR"
                                 v-if="application.qr_code_url">
                            <div v-else
                                 class="w-[100px] h-[100px] bg-gray-200 flex items-center justify-center text-xs">No QR
                            </div>
                        </td>

                        <td class="text-center">
                            <div class="flex flex-col items-center justify-center space-y-1">
                                <h3 class="text-lg"><strong>Application Form</strong></h3>
                                <h3><strong class="font-nastaleeq">درخواست فارم</strong></h3>
                                <span class="px-3 py-1 text-sm rounded-full bg-gray-200  uppercase font-bold">
                              {{ application.certificate_type === 'both' ? 'Domicile and State Subject Certificates' : application.certificate_type === 'domicile' ? 'Domicile Certificate' : 'State Subject Certificate'
                                    }}
                            </span>
                                <h5><strong>Center : {{ centerName }}</strong></h5>
                            </div>

                        </td>

                        <td>
<!--                            <div class="flex justify-end">-->
<!--                                <img src="/assets/images/secondoloho.png" width="100" alt="Applicant Image">-->
<!--                            </div>-->
                        </td>
                    </tr>
                    </tbody>
                </table>

                <!-- ================= APPLICATION APPLIED FOR ================= -->
                <table class="table borderless mt-2 applicant-table-heading">
                    <tbody>
                    <tr class="border-b border-gray-200 bg-gray-300">
                        <td class="font-[800]">
                            <strong>Applicant Details</strong>
                        </td>
                        <td style="text-align:right" dir="rtl">
                            <strong class="font-nastaleeq font-[600]">
                                جس کے لیے درخواست دی جا رہی ہے
                            </strong>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <!-- ================= APPLICANT DETAILS ================= -->
                <table class="table mt-2 applicant-table">
                    <tbody>
                    <tr>
                        <td><strong>Applicant Name</strong></td>
                        <td class="text-center">{{ applicant.full_name || 'N/A' }}</td>
                        <td class="font-nastaleeq text-right">درخواست دہندہ کا نام</td>
                    </tr>

                    <tr>
                        <td><strong>Father/Husband Name</strong></td>
                        <td class="text-center font-nastaleeq">{{ applicant.father_name || 'N/A' }}</td>
                        <td class="font-nastaleeq text-right">والد / شوہر کا نام</td>
                    </tr>

                    <tr>
                        <td><strong>CNIC</strong></td>
                        <td class="text-center ">{{ applicant.identity_number }}</td>
                        <td class="font-nastaleeq text-right">شناخنتی کارڈ نمبر</td>
                    </tr>

                    <tr>
                        <td><strong>Gender</strong></td>
                        <td class="text-center font-nastaleeq">{{ gender }}</td>
                        <td class="font-nastaleeq text-right">جنس</td>
                    </tr>

                    <tr>
                        <td><strong>Date of Birth</strong></td>
                        <td class="text-center">{{ formatDMY(applicant.dob) || 'N/A' }}</td>
                        <td class="font-nastaleeq text-right">پیدائش کی تاریخ</td>
                    </tr>

                    <tr>
                        <td><strong>Address</strong></td>
                        <td class="text-center font-nastaleeq">{{ applicant.address || 'N/A' }}</td>
                        <td class="font-nastaleeq text-right">درخواست دہندہ کا پتہ</td>
                    </tr>

                    <tr>
                        <td><strong>District</strong></td>
                        <td class="text-center font-nastaleeq">
                            {{ district }}
                        </td>
                        <td class="font-nastaleeq text-right">ضلع</td>
                    </tr>

                    <tr>
                        <td><strong>City/Tehsil</strong></td>
                        <td class="text-center font-nastaleeq">
                            {{ tehsil }}
                        </td>
                        <td class="font-nastaleeq text-right">شہر/تحصیل</td>
                    </tr>

                    <tr>
                        <td><strong>Contact No</strong></td>
                        <td class="text-center">{{ applicant.phone || 'N/A' }}</td>
                        <td class="font-nastaleeq text-right">رابطہ نمبر</td>
                    </tr>

                    <tr>
                        <td><strong>Guardian</strong></td>
                        <td class="text-center font-nastaleeq">{{ guardianType }}</td>
                        <td class="font-nastaleeq text-right">سرپرست</td>
                    </tr>

                    <tr>
                        <td><strong>Religion</strong></td>
                        <td class="text-center font-nastaleeq">{{ religion }}
                        </td>
                        <td class="font-nastaleeq text-right">مذہب</td>
                    </tr>

                    <tr>
                        <td><strong>Occupation</strong></td>
                        <td class="text-center font-nastaleeq">{{ applicant.occupation || 'N/A' }}</td>
                        <td class="font-nastaleeq text-right">پیشہ</td>
                    </tr>

                    <tr>
                        <td><strong>Marital Status</strong></td>
                        <td class="text-center font-nastaleeq">{{ maritalStatus }}</td>
                        <td class="font-nastaleeq text-right">ازدواجی حیثیت</td>
                    </tr>
                    </tbody>
                </table>

                <!-- ================= APPLICATION INFORMATION ================= -->
                <table class="table borderless mt-2 application-table-heading">
                    <tbody>
                    <tr class="border-b border-gray-200 bg-gray-200">
                        <td class="font-[800]">
                            <strong>Application Details</strong>
                        </td>
                        <td></td>
                        <td style="text-align:right" dir="rtl">
                            <strong class="font-nastaleeq font-[600]">
                                درخواست کی معلومات
                            </strong>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <table class="table borderless mt-2 application-table">
                    <tbody>
                    <tr>
                        <td><strong>Tracking ID</strong></td>
                        <td class="text-center font-nastaleeq">{{ application.tracking_token_no || 'N/A' }}</td>
                        <td class="font-nastaleeq text-right">ٹریکنگ ٹوکن نمبر</td>
                    </tr>
                    <tr>
                        <td><strong>Missal No</strong></td>
                        <td class="text-center font-nastaleeq">{{ application.missal_no || 'N/A' }}</td>
                        <td class="font-nastaleeq text-right">مثل نمبر</td>
                    </tr>
                    <tr>
                        <td><strong>Q-Matic Token No</strong></td>
                        <td class="text-center font-nastaleeq">{{ application.appointment.qmatic_token || 'N/A' }}</td>
                        <td class="font-nastaleeq text-right">کیو میٹک ٹوکن نمبر</td>
                    </tr>
                    <tr>
                        <td><strong>Entry Date</strong></td>
                        <td class="text-center ">{{ $formatDMY(application.entry_datetime) || 'N/A' }}</td>
                        <td class="font-nastaleeq text-right">اندراج کی تاریخ</td>
                    </tr>
                    <tr>
                        <td><strong>Application Type</strong></td>
                        <td class="text-center font-nastaleeq">{{ appTypeMap[application.certificate_type] || 'N/A' }}
                        </td>
                        <td class="font-nastaleeq text-right">درخواست کی قسم</td>
                    </tr>
                    <tr>
                        <td><strong>Application Status</strong></td>
                        <td class="text-center font-nastaleeq">
                            {{ (application.application_type_id === 1 ? 'نئی' : 'نقل') || 'N/A' }}
                        </td>
                        <td class="font-nastaleeq text-right">درخواست کی نوعیت</td>
                    </tr>
                    <tr>
                        <td><strong>Remarks</strong></td>
                        <td class="text-center font-nastaleeq">{{ application.remarks || 'N/A' }}</td>
                        <td class="font-nastaleeq text-right">ریمارکس</td>
                    </tr>
                    <tr>
                        <td><strong>Status</strong></td>
                        <td class="text-center">
                            <span
                                class="px-3 my-1 py-0 text-sm rounded-full bg-green-100 text-green-700 uppercase">
                              {{ statusText }}
                            </span>
                        </td>
                        <td class="font-nastaleeq text-right">موجودہ حیثیت</td>
                    </tr>
                    </tbody>
                </table>

                <template v-if="application.approvals.length > 0">
                    <table class="table borderless mt-2 approval-details-heading">
                        <tbody>
                        <tr class="border-b border-gray-200 bg-gray-300">
                            <td class="font-[800]">
                                <strong>Approval Details</strong>
                            </td>
                            <td style="text-align:right" dir="rtl">
                                <strong class="font-nastaleeq font-[600]">
                                    منظوری کی تفصیلات
                                </strong>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <table class="table borderless mt-2 approval-details-table">
                        <tbody>
                        <tr>
                            <td><strong>Assistant Commissioner</strong></td>
                            <td class="text-center">
                                <!-- If sign exists -->
                                <template v-if="application.approvals[0].esign">
                                    <!-- Image -->
                                    <img
                                        :src="application.approvals[0].esign_url"
                                        alt="E-Sign"
                                        style="max-width: 80px; height: auto; margin: auto;"
                                    />
                                    <!-- Date -->
                                    <span>
                                    {{ formatDMY(application.approvals[0].sign_date, true) }}
                                </span>
                                </template>
                            </td>
                            <td class="font-nastaleeq text-right">اسسٹنٹ کمشنر</td>
                        </tr>
                        <tr v-if="application.approvals.length > 1">
                            <td><strong>District Commissioner</strong></td>
                            <td class="text-center">
                                <!-- If sign exists -->
                                <template v-if="application.approvals[1].esign">
                                    <!-- Image -->
                                    <img
                                        :src="application.approvals[1].esign_url"
                                        alt="E-Sign"
                                        style="max-width: 80px; height: auto; margin: auto;"
                                    />
                                    <!-- Date -->
                                    <span>
                                    {{ formatDMY(application.approvals[1].sign_date, true) }}
                                </span>
                                </template>
                            </td>
                            <td class="font-nastaleeq text-right">ڈپٹی کمشنر</td>
                        </tr>
                        </tbody>
                    </table>
                </template>


                <!-- Action Buttons -->
                <div v-if="!hidePrintButton" class="mt-8 text-center print:hidden flex justify-center gap-3">
                    <button @click="goBack" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i> Back
                    </button>
                    <button @click="printForm" class="btn btn-primary">
                        <i class="fas fa-print mr-2"></i> Print Form
                    </button>
                </div>
            </div>

            <!-- No Data State -->
            <div v-else-if="!loading" class="text-center py-8">
                <p>No application data found.</p>
            </div>
        </div>
    </div>
</template>

<script setup>
    import axios from 'axios';
    import { computed, onMounted, ref } from 'vue';
    import { useRoute, useRouter } from 'vue-router';
    import { storeToRefs } from 'pinia';
    import { useAppStore } from '@/stores/index.ts';
    import { formatDMY } from '@/mixin/index.ts';

    // Props
    const props = defineProps({
        hidePrintButton: {
            type: Boolean,
            default: false
        }
    });

    const emit = defineEmits(['loaded']);

    const route = useRoute();
    const router = useRouter();
    const appStore = useAppStore();
    const loading = ref(true);
    const error = ref(null);
    const application = ref(null);
    const applicant = ref(null);

    const {
        centers,
        regions,
        guardian_types,
        genders,
        religions,
        marital_statuses
    } = storeToRefs(appStore);


    const fetchApplication = async () => {
        try {
            loading.value = true;
            error.value = null;

            const id = route.params.id;
            if (!id) throw new Error('Application ID not found in URL');

            const response = await axios.get(`/api/applications/${id}`);
            if (!response.data.success) throw new Error(response.data.message || 'Failed to fetch application');
            console.log('Application fetched:', response.data.data);
            application.value = response.data.data.application;
            applicant.value = response.data.data.applicant;

        } catch (err) {
            console.error('Error fetching application:', err);
            error.value = err.response?.data?.message || err.message || 'Failed to load application data';
        } finally {
            loading.value = false;
        }
    };

    const gender = computed(() => {
        console.log('applicant gender value:', applicant.value, genders.value);
        if (!applicant.value?.gender_id) return 'N/A';
        const item = genders.value.find(g => g.id === applicant.value.gender_id);
        return item ? (item.urdu_name || item.name) : 'N/A';
    });

    const religion = computed(() => {
        if (!applicant.value?.religion_id) return 'N/A';
        const item = religions.value.find(r => r.id === applicant.value.religion_id);
        return item ? (item.urdu_name || item.name) : 'N/A';
    });

    const maritalStatus = computed(() => {
        if (!applicant.value?.marital_status_id) return 'N/A';
        const item = marital_statuses.value?.find(m => m.id === applicant.value.marital_status_id);
        return item ? (item.urdu_name || item.name) : 'N/A';
    });

    const guardianType = computed(() => {
        if (!applicant.value?.guardian_type_id) return 'N/A';
        const item = guardian_types.value?.find(g => g.id === applicant.value.guardian_type_id);
        return item ? (item.urdu_name || item.name) : 'N/A';
    });

    const centerName = computed(() => {
        if (!application.value?.center_id) return 'N/A';
        const item = centers.value?.find(g => g.id === application.value.center_id);
        return item ? item.name : 'N/A';
    });

    const allDistricts = computed(() =>
        regions.value.flatMap(r => r.districts || [])
    );

    const allTehsils = computed(() =>
        allDistricts.value.flatMap(d => d.tehsils || [])
    );

    const district = computed(() => {
        const id = applicant.value?.district_id;
        if (!id) return 'N/A';

        const item = allDistricts.value.find(d => d.id === id);
        return item ? (item.urdu_name || item.name) : 'N/A';
    });

    const tehsil = computed(() => {
        const id = applicant.value?.tehsil_id;
        if (!id) return 'N/A';

        const item = allTehsils.value.find(t => t.id === id);
        return item ? (item.urdu_name || item.name) : 'N/A';
    });

    const statusMap = {
        pending: 'Waiting Verification',
        submitted: 'Forwarded to Assistant Commissioner',
        verified: 'Forwarded to District Commissioner',
        approved: 'Approved',
        objected: 'Objected',
        ready_for_delivery: 'Ready for Delivery',
        delivered: 'Delivered'
    };
    const appTypeMap = {
        state: 'State Subject Certificate',
        domicile: 'Domicile Certificate',
        both: 'Domicile and State Subject Certificate'
    };

    const statusText = computed(() => {
        return statusMap[application.value?.current_status] || 'N/A';
    });

    const printForm = () => window.print();

    const goBack = () => {
        if (window.opener) window.close();
        else router.back();
    };

    // Lifecycle
    onMounted(async () => {
        await appStore.loadDropdowns();
        await fetchApplication();
        emit('loaded');
    });
</script>

<style>
    .view-header-table {
        width: 100%;
        border-collapse: collapse;
    }

    .view-header-table th,
    .view-header-table td {
        padding: unset !important;
        text-align: center;
    }

    .view-header-table th {
        background-color: #f2f2f2;
    }

    .view-header-table tr:nth-child(even) {
    }

    .table td,
    .table th {
        width: 33.33%;
        border-top: none !important;
        padding: 3px 8px;
    }

    .table.borderless td,
    .table.borderless th {
        border: none !important;
    }

    @media print {
        body * {
            visibility: hidden;
        }

        .page-content,
        .page-content * {
            visibility: visible;
            padding: 2px;
        }

        .page-content {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            border: 1px solid;
        }

        .print\:hidden {
            display: none !important;
        }
    }

    .page-content-wrapper {
        page-break-inside: avoid;

    }

    /* Loading and Error Styles */
    .spinner-border {
        display: inline-block;
        width: 2rem;
        height: 2rem;
        border: 0.25em solid currentColor;
        border-right-color: transparent;
        border-radius: 50%;
        animation: spinner-border .75s linear infinite;
    }

    @keyframes spinner-border {
        to {
            transform: rotate(360deg);
        }
    }

    .alert-danger {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
        padding: 1rem;
        border-radius: 4px;
        margin-bottom: 1rem;
    }

    .btn {
        padding: 8px 16px;
        border: 1px solid transparent;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 500;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }
</style>
