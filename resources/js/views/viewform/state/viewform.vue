<template>
    <div class="page-content-wrapper p-6">
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
                <!-- ================= HEADER ================= -->
                <table>
                    <tbody>
                    <tr class="flex items-center justify-center">
                        <td width="100">
                            <img src="/assets/images/logo.png" width="70" height="70" alt="LOGO">
                        </td>
                        <td>
                            <h3><strong>E-Facilitation Center AJK</strong></h3>
                            <h3><strong>(Digital Service Centre)</strong></h3>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <!-- ================= QR & SERVICE INFO ================= -->
                <table class="mt-4 w-full">
                    <tbody>
                    <tr>
                        <td>
                            <img src="/assets/images/qr.png" width="100" alt="QR">
                        </td>

                        <td class="text-center">
                            <h3><strong>Application Form</strong></h3>
                            <h3><strong class="font-nastaleeq">درخواست فارم</strong></h3>
                            <h5><strong>Domicile</strong></h5>
                            <h5><strong>Muzafarabad</strong></h5>
                        </td>

                        <td>
                            <div class="flex justify-end">
                                <img src="/assets/images/secondoloho.png" width="100" alt="Applicant Image">
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <!-- ================= STATUS ================= -->
                <table class="mt-4">
                    <tbody>
                    <tr>
                        <td><strong>Status :</strong></td>
                        <td>Pending</td>
                        <td><strong>Application type:</strong></td>
                        <td>Normal</td>
                        <td><strong>Service fee:</strong></td>
                        <td>N/A</td>
                    </tr>
                    </tbody>
                </table>

                <!-- ================= APPLICATION APPLIED FOR ================= -->
                <table class="mt-4">
                    <tbody>
                    <tr>
                        <td class="font-[800]">
                            <strong>Application Applied For</strong>
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
                <table width="100%" class="table mt-2">
                    <tbody>
                    <tr>
                        <td><strong>01 -Application ID</strong></td>
                        <td class="text-center">{{ application.id || 'N/A' }}</td>
                        <td class="font-nastaleeq text-right">درخواست نمبر</td>
                    </tr>

                    <tr>
                        <td><strong>02 -Name</strong></td>
                        <td class="text-center">{{ application.first_name || 'N/A' }}</td>
                        <td class="font-nastaleeq text-right">درخواست دہندہ کا نام</td>
                    </tr>

                    <tr>
                        <td><strong>03 -Father/Husband Name</strong></td>
                        <td class="text-center">{{ application.father_name || 'N/A' }}</td>
                        <td class="font-nastaleeq text-right">والد / شوہر کا نام</td>
                    </tr>

                    <tr>
                        <td><strong>04 -CNIC</strong></td>
                        <td class="text-center">{{ formatCNIC(application.cnic) || 'N/A' }}</td>
                        <td class="font-nastaleeq text-right">شناخنتی کارڈ نمبر</td>
                    </tr>

                    <tr>
                        <td><strong>05 -Gender</strong></td>
                        <td class="text-center">{{ getDisplayValue('gender', application.gender) || 'N/A' }}</td>
                        <td class="font-nastaleeq text-right">جنس</td>
                    </tr>

                    <tr>
                        <td><strong>06 -Date of Birth</strong></td>
                        <td class="text-center">{{ formatDate(application.dob) || 'N/A' }}</td>
                        <td class="font-nastaleeq text-right">پیدائش کی تاریخ</td>
                    </tr>

                    <tr>
                        <td><strong>07 -Address</strong></td>
                        <td class="text-center">{{ application.address || 'N/A' }}</td>
                        <td class="font-nastaleeq text-right">درخواست دہندہ کا پتہ</td>
                    </tr>

                    <tr>
                        <td><strong>08 -District</strong></td>
                        <td class="text-center font-nastaleeq">
                            {{ getDisplayValue('district', application.district) }}
                        </td>
                        <td class="font-nastaleeq text-right">ضلع</td>
                    </tr>

                    <tr>
                        <td><strong>09 -City/Tehsil</strong></td>
                        <td class="text-center font-nastaleeq">
                            {{ getDisplayValue('tehsil', application.city) }}
                        </td>
                        <td class="font-nastaleeq text-right">شہر/تحصیل</td>
                    </tr>

                    <tr>
                        <td><strong>10 -Contact No</strong></td>
                        <td class="text-center">{{ application.phone || 'N/A' }}</td>
                        <td class="font-nastaleeq text-right">رابطہ نمبر</td>
                    </tr>

                    <tr>
                        <td><strong>11 -Religion</strong></td>
                        <td class="text-center">{{ getDisplayValue('religion', application.religion) || 'N/A' }}</td>
                        <td class="font-nastaleeq text-right">مذہب</td>
                    </tr>

                    <tr>
                        <td><strong>12 -Occupation</strong></td>
                        <td class="text-center">{{ application.occupation || 'N/A' }}</td>
                        <td class="font-nastaleeq text-right">پیشہ</td>
                    </tr>

                    <tr>
                        <td><strong>13 -Marital Status</strong></td>
                        <td class="text-center">
                            {{ getDisplayValue('marital_status', application.marital_status) || 'N/A' }}
                        </td>
                        <td class="font-nastaleeq text-right">ازدواجی حیثیت</td>
                    </tr>

                    <tr>
                        <td><strong>14 -Remark/Reason</strong></td>
                        <td class="text-center">{{ application.remarks || 'N/A' }}</td>
                        <td class="font-nastaleeq text-right">کیفیت</td>
                    </tr>
                    </tbody>
                </table>

                <!-- ================= APPLICATION INFORMATION ================= -->
                <table class="table borderless mt-8">
                    <tbody>
                    <tr>
                        <td class="font-[800]">
                            <strong>Application Information</strong>
                        </td>
                        <td></td>
                        <td style="text-align:right" dir="rtl">
                            <strong class="font-nastaleeq font-[600]">
                                درخواست دہندہ کی معلومات
                            </strong>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <table width="100%" class="table borderless mt-2">
                    <tbody>
                    <tr>
                        <td><strong>01 -Applicant Name</strong></td>
                        <td class="text-center">{{ application.first_name || 'N/A' }}</td>
                        <td class="font-nastaleeq text-right">درخواست دہندہ کا نام</td>
                    </tr>

                    <tr>
                        <td><strong>02 -Father/Husband Name</strong></td>
                        <td class="text-center">{{ application.father_name || 'N/A' }}</td>
                        <td class="font-nastaleeq text-right">والد / شوہر کا نام</td>
                    </tr>

                    <tr>
                        <td><strong>03 -Applicant CNIC</strong></td>
                        <td class="text-center">{{ formatCNIC(application.cnic) || 'N/A' }}</td>
                        <td class="font-nastaleeq text-right">شناخنتی کارڈ نمبر</td>
                    </tr>

                    <tr>
                        <td><strong>04 -Relation</strong></td>
                        <td class="text-center">{{ getDisplayValue('guardian', application.guardian) || 'N/A' }}</td>
                        <td class="font-nastaleeq text-right">رشتہ</td>
                    </tr>
                    </tbody>
                </table>

                <!-- ================= AC / DC SIGNATURES ================= -->
                <div v-if="application.ac_name || application.dc_name" class="mt-8 border-t pt-6">
                    <div class="grid grid-cols-2 gap-8">
                        <!-- AC Signature Section -->
                        <div v-if="application.ac_name" class="text-center">
                            <div class="mb-2">
                                <img v-if="application.ac_sign" :src="getDocumentURL(application.ac_sign)"
                                     class="max-h-24 mx-auto object-contain" alt="AC Signature">
                                <div v-else class="h-24 flex items-center justify-center text-gray-400 italic">No
                                    Signature
                                </div>
                            </div>
                            <div class="border-t border-gray-400 pt-2">
                                <p class="font-bold text-sm">{{ application.ac_name }}</p>
                                <p class="text-xs text-gray-600">{{ application.ac_designation }}</p>
                                <p v-if="application.ac_sign_date" class="text-[10px] text-gray-400 mt-1">Signed on: {{
                                        formatDate(application.ac_sign_date) }}</p>
                            </div>
                        </div>

                        <!-- DC Signature Section -->
                        <div v-if="application.dc_name" class="text-center">
                            <div class="mb-2">
                                <img v-if="application.dc_sign" :src="getDocumentURL(application.dc_sign)"
                                     class="max-h-24 mx-auto object-contain" alt="DC Signature">
                                <div v-else class="h-24 flex items-center justify-center text-gray-400 italic">No
                                    Signature
                                </div>
                            </div>
                            <div class="border-t border-gray-400 pt-2">
                                <p class="font-bold text-sm">{{ application.dc_name }}</p>
                                <p class="text-xs text-gray-600">{{ application.dc_designation }}</p>
                                <p v-if="application.dc_sign_date" class="text-[10px] text-gray-400 mt-1">Signed on: {{
                                        formatDate(application.dc_sign_date) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Print Button -->
                <div class="mt-8 text-center print:hidden flex justify-center gap-3">
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

<script>
    import axios from 'axios';

    export default {
        name: 'PrintForm',
        data() {
            return {
                loading: true,
                error: null,
                application: null,
                lookupData: {
                    genders: [],
                    religions: [],
                    maritalStatuses: [],
                    guardianTypes: [],
                    districts: [],
                    tehsils: []
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

                    if (!id) {
                        throw new Error('Application ID not found in URL');
                    }

                    // Fetch application - same as edit form
                    const response = await axios.get(`/api/applications/${id}`);

                    if (!response.data.success) {
                        throw new Error(response.data.message || 'Failed to fetch application');
                    }

                    this.application = response.data.data;

                    // Load lookup data using same endpoints as edit form
                    await Promise.all([
                        this.fetchDistricts(),
                        this.fetchLookupType('gender', 'genders'),
                        this.fetchLookupType('religion', 'religions'),
                        this.fetchLookupType('marital_status', 'maritalStatuses'),
                        this.fetchLookupType('guardian_type', 'guardianTypes')
                    ]);

                    // Fetch tehsils if district exists
                    if (this.application.district) {
                        await this.fetchTehsils(this.application.district);
                    }

                } catch (error) {
                    console.error('Error fetching application:', error);
                    this.error = error.response?.data?.message || error.message || 'Failed to load application data';
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
                if (!value) return 'N/A';

                // If value is already an object with name
                if (typeof value === 'object' && value !== null) {
                    return value.urdu_name || value.name || 'N/A';
                }

                // If value is an ID, look it up
                const lookupMap = {
                    'gender': { data: this.lookupData.genders, default: 'N/A' },
                    'religion': { data: this.lookupData.religions, default: 'N/A' },
                    'marital_status': { data: this.lookupData.maritalStatuses, default: 'N/A' },
                    'guardian': { data: this.lookupData.guardianTypes, default: 'N/A' },
                    'district': { data: this.lookupData.districts, default: 'N/A' },
                    'tehsil': { data: this.lookupData.tehsils, default: 'N/A' }
                };

                const lookup = lookupMap[type];
                if (lookup && lookup.data && lookup.data.length > 0) {
                    const item = lookup.data.find(item => item.id === value);
                    return item ? (item.urdu_name || item.name) : lookup.default;
                }

                return lookup?.default || 'N/A';
            },

            formatDate(dateString) {
                if (!dateString) return 'N/A';
                try {
                    const date = new Date(dateString);
                    return date.toLocaleDateString('en-GB'); // DD/MM/YYYY format
                } catch (error) {
                    return dateString;
                }
            },

            formatCNIC(cnic) {
                if (!cnic) return 'N/A';

                // Format CNIC: XXXXX-XXXXXXX-X
                const cleaned = cnic.replace(/\D/g, '');
                if (cleaned.length === 13) {
                    return cleaned.replace(/(\d{5})(\d{7})(\d{1})/, '$1-$2-$3');
                }
                return cnic;
            },
            getDocumentURL(filePath) {
                if (!filePath) return '';
                if (filePath.startsWith('http://') || filePath.startsWith('https://')) return filePath;
                const backendURL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000';
                return `${backendURL}${filePath.startsWith('/') ? '' : '/'}${filePath}`;
            },

            printForm() {
                window.print();
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
</script>

<style>
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

    .btn-primary:hover {
        background-color: #0056b3;
    }
</style>
