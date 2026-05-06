<template>
    <div class="p-6 min-h-screen">
        <div class="max-w-7xl mx-auto bg-white rounded-lg shadow border p-6">
            <div class="flex items-center justify-between mb-4 border-b pb-2">
                <h2 class="text-xl font-bold text-gray-800">Archived Documents Report</h2>
            </div>

            <transition
                enter-active-class="transition-all duration-300 ease-out"
                leave-active-class="transition-all duration-200 ease-in"
                enter-from-class="opacity-0 -translate-y-2"
                leave-to-class="opacity-0 -translate-y-2"
            >
                <div v-if="showFilter" class="bg-gray-50 p-5 mb-6 rounded-lg border grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 items-end">
                    <div class="w-full min-w-0">
                        <label class="block text-xs mb-1 text-gray-500">From Date To Date </label>
                        <VueDatePicker
                            v-model="filters.date_range"
                            range
                            placeholder="Select Date  "
                            class="urdu-datepicker"
                            auto-apply
                            model-type="yyyy-MM-dd"
                            :formats="{ input: 'dd-MM-yyyy' }"
                            :enable-time-picker="false"
                        />
                    </div>
                    <div class="treeselect-wrapper w-full min-w-0">
                        <label class="block text-xs mb-1 text-gray-500">Area / District / Tehsil</label>
                        <treeselect v-model="selectedNodes" :options="regionTreeData" :multiple="true" placeholder="Select Area  ..." />
                    </div>

                    <div class="w-full min-w-0">
                        <label class="block text-xs mb-1 text-gray-500 font-bold">Users</label>
                        <select v-model="filters.user_id" class="w-full h-[38px] border rounded px-2 text-sm outline-none">
                            <option value="">All Users</option>
                            <option v-for="user in scannerUsers" :key="user.id" :value="user.id">{{ user.first_name }} {{ user.last_name }}</option>
                        </select>
                    </div>
                    <div class="w-full min-w-0 flex gap-2 items-end">
                        <button
                            @click="generateReport"
                            :disabled="loading"
                            class="flex-1 min-w-0 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors flex items-center justify-center gap-2 h-[38px] px-3 text-sm disabled:opacity-60"
                        >
                            <i v-if="loading" class="fa fa-spinner fa-spin"></i>
                            <i v-else class="fa fa-search text-xs"></i>
                            Search
                        </button>

                        <button
                            @click="resetFilters"
                            class="shrink-0 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors h-[38px] px-4 text-sm"
                        >
                            Reset
                        </button>
                        <button
                            @click="printReport"
                            class="shrink-0 bg-green-600 text-white rounded hover:bg-green-700 transition-colors flex items-center gap-2 h-[38px] px-3 text-sm"
                        >
                            <i class="fa fa-print"></i> Print
                        </button>
                    </div>
                </div>
            </transition>
            <!-- Table Section -->
            <div class="hidden print:block mb-6 border-b-2 border-gray-300 pb-4">
                <h1 class="text-2xl font-bold text-center underline mb-2">Archived Documents Report</h1>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p><strong>Date Range:</strong> {{ filters.date_range ? filters.date_range[0] + ' to ' + filters.date_range[1] : 'All Time' }}</p>
                        <p><strong>User Name:</strong> {{ getScannerName(filters.user_id) }}</p>
                    </div>
                    <div class="text-right">
                        <p><strong>Total Records:</strong> {{ totalRecords }}</p>
                        <p><strong>Print Date:</strong> {{ new Date().toLocaleString() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded border overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-800">
                        <tr>
                            <th class="border px-3 py-2 text-center">ID</th>
                            <th class="border px-3 py-2 text-center">Name</th>
                            <th class="border px-3 py-2 text-center">Father Name</th>
                            <th class="border px-3 py-2 text-center">District / Tehsil</th>
                            <th class="border px-3 py-2 text-center">Misal no</th>
                            <th class="border px-3 py-2 text-center">Identity No</th>
                            <th class="border px-3 py-2 text-center">Data Entered By</th>
                            <th class="border px-3 py-2 text-center">Image Uploaded By</th>
                            <th class="border px-3 py-2 text-center">Scanner</th>
                            <th class="border px-3 py-2 text-center">Date of Scanning</th>
                        </tr>
                    </thead>
                    <tbody v-if="reportData.length > 0">
                        <tr v-for="(row, index) in reportData" :key="row.id" class="border-b hover:bg-blue-50/50 transition">
                            <td class="border px-3 py-2 text-center text-xs">{{ index + 1 }}</td>
                            <td class="border px-3 py-2 text-center text-xs">{{ row.applicant?.full_name }}</td>
                            <td class="border px-3 py-2 text-center text-xs">{{ row.applicant?.father_name || 'N/A' }}</td>
                            <td class="border px-3 py-2 text-center text-xs">
                                <span class="text-xs text-gray-500">{{ row.applicant?.tehsil?.parent?.name || row.applicant?.tehsil?.parent?.name }} / </span>
                                <span class="font-bold text-blue-700">
                                    {{ row.applicant?.tehsil?.name }}
                                </span>
                            </td>
                            <td class="border px-3 py-2 text-center text-xs">{{ row.misal_no }}</td>
                            <td class="border px-3 py-2 text-center text-xs">{{ row.applicant?.identity_number }}</td>
                            <td class="border px-3 py-2 text-center text-xs">
                                <span>{{ row.verification?.data_enterer?.first_name || 'N/A' }} </span>
                            </td>
                            <td class="border px-3 py-2 text-center text-xs">
                                <span> {{ row.verification?.image_uploader?.first_name || 'N/A' }}</span>
                            </td>
                            <td class="border px-3 py-2 text-center text-xs">
                                <span class="px-2 py-1 rounded text-xs">{{ row.uploader?.first_name }} {{ row.uploader?.last_name }}</span>
                            </td>

                            <td class="border px-3 py-2 text-center text-xs">{{ formatDMY(row.created_at) }}</td>
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr>
                            <td colspan="10" class="p-20 text-center text-gray-400 italic">
                                <i class="fa fa-folder-open text-4xl mb-3 block mx-auto opacity-20"></i>
                                No records found. Please apply filters to generate report.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!--  summary Section -->
            <div v-if="reportData.length > 0" class="mt-8 break-inside-avoid print:break-before-page">
                <h3 class="text-lg font-bold mb-3 border-b pb-1">User Performance Summary</h3>
                <div class="bg-gray-50 rounded border overflow-hidden">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-200 text-gray-700">
                            <tr>
                                <th class="border px-4 py-2 text-left">User Name</th>
                                <th class="border px-4 py-2 text-center">Documents Scanned</th>
                                <th class="border px-4 py-2 text-center">Data Entries</th>
                                <th class="border px-4 py-2 text-center">Images Uploaded</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="user in userWiseSummary" :key="user.name" class="bg-white hover:bg-gray-50">
                                <td class="border px-4 py-2 font-medium">{{ user.name }}</td>
                                <td class="border px-4 py-2 text-center">{{ user.scannedCount }}</td>
                                <td class="border px-4 py-2 text-center">{{ user.dataEntryCount }}</td>
                                <td class="border px-4 py-2 text-center">{{ user.imageUploadCount }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div v-if="reportData.length > 0" class="mt-4 p-4 border-2 border-gray-400 flex justify-between items-center print:break-inside-avoid">
                <span class="font-bold text-lg">Grand Total </span>
                <span class="font-black text-2xl text-blue-600">
                    {{ totalRecords }}
                </span>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, computed, onMounted } from 'vue';
    import Treeselect from 'vue3-treeselect';
    import 'vue3-treeselect/dist/vue3-treeselect.css';
    import apiClient from '@/services/axios';
    import { formatDMY } from '@/mixin/index.ts';
    import { useAppStore } from '@/stores/index.ts';
    const store = useAppStore();
    const loading = ref(false);
    const scannerUsers = ref([]);
    const showFilter = ref(true);
    const filters = ref({
        date_range: '',
        user_id: '',
    });
    const selectedNodes = ref([]);
    const reportData = ref([]);
    const totalRecords = ref(0);

    // Populate TreeSelect
    const regionTreeData = computed(() => {
        return store.regions.map((region) => ({
            id: `region_${region.id}`,
            label: region.name,
            children:
                region.districts?.map((district) => ({
                    id: `district_${district.id}`,
                    label: district.name,
                    children:
                        district.tehsils?.map((tehsil) => ({
                            id: `tehsil_${tehsil.id}`,
                            label: tehsil.name,
                        })) || [],
                })) || [],
        }));
    });

    const fetchScanners = async () => {
        try {
            const res = await apiClient.get('/api/scanners-list');
            scannerUsers.value = res.data.data;
        } catch (err) {
            console.error('Error fetching scanners', err);
        }
    };
    const generateReport = async () => {
        loading.value = true;
        try {
            let fromDate = null;
            let toDate = null;
            if (filters.value.date_range && filters.value.date_range.length === 2) {
                fromDate = filters.value.date_range[0];
                toDate = filters.value.date_range[1];
            }

            const rIds = selectedNodes.value.filter((id) => String(id).startsWith('region_')).map((id) => id.replace('region_', ''));
            const dIds = selectedNodes.value.filter((id) => String(id).startsWith('district_')).map((id) => id.replace('district_', ''));
            const tIds = selectedNodes.value.filter((id) => String(id).startsWith('tehsil_')).map((id) => id.replace('tehsil_', ''));

            const res = await apiClient.get('/api/archive-report', {
                params: {
                    from_date: fromDate,
                    to_date: toDate,
                    user_id: filters.value.user_id || null,
                    region_ids: rIds.length ? rIds.join(',') : null,
                    district_ids: dIds.length ? dIds.join(',') : null,
                    tehsil_ids: tIds.length ? tIds.join(',') : null,
                },
            });

            reportData.value = res.data.data;
            totalRecords.value = res.data.total_count;
        } catch (err) {
            console.error('Report Error:', err);
        } finally {
            loading.value = false;
        }
    };
    //userWise Record
    const userWiseSummary = computed(() => {
        const stats = {};
        reportData.value.forEach((row) => {
            const initUser = (user) => {
                if (!user) return null;
                if (!stats[user.id]) {
                    stats[user.id] = {
                        name: `${user.first_name} ${user.last_name || ''}`,
                        scannedCount: 0,
                        dataEntryCount: 0,
                        imageUploadCount: 0,
                    };
                }
                return user.id;
            };
            const scannerId = initUser(row.uploader);
            if (scannerId) stats[scannerId].scannedCount++;
            const entererId = initUser(row.verification?.data_enterer);
            if (entererId) stats[entererId].dataEntryCount++;
            const imgUploaderId = initUser(row.verification?.image_uploader);
            if (imgUploaderId) stats[imgUploaderId].imageUploadCount++;
        });
        return Object.values(stats);
    });

    const resetFilters = () => {
        filters.value = { from_date: '', to_date: '' };
        selectedNodes.value = [];
        reportData.value = [];
        totalRecords.value = 0;
    };

    onMounted(() => {
        store.loadDropdowns();
        fetchScanners();
    });

    const getScannerName = (id) => {
        if (!id) return 'AllScanner ';
        const user = scannerUsers.value.findLast((u) => u.id === id);
        return user ? `${user.first_name} ${user.last_name}` : 'Unknow';
    };

    const printReport = () => {
        window.print();
    };
</script>

<style>
    @media print {
        nav,
        h2,
        aside,
        header,
        footer,
        .sidebar,
        .main-sidebar,
        .main-header,
        .main-footer,
        .navbar,
        .no-print,
        .btn,
        button,
        .VueDatePicker,
        #footer,
        .treeselect-wrapper,
        .bg-gray-50.p-5.mb-6,
        #sidebar-wrapper {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }
        @page {
            size: landscape;
            margin: 5mm;
        }
        .max-w-7xl,
        .bg-white,
        .rounded-lg,
        .shadow,
        .border.p-6 {
            border: none !important;
            box-shadow: none !important;
        }
        th,
        td {
            border: 1px solid #cac6c6 !important;
            padding: 3px !important;
            font-size: 10px !important;
            word-wrap: break-word !important;
            white-space: nowrap;
            vertical-align: middle;
        }

        thead {
            display: table-header-group !important;
        }
    }

    th,
    td {
        white-space: nowrap;
    }
</style>
