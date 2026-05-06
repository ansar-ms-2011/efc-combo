<template>
    <div class="p-4 h-auto">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center text-sm text-gray-600 space-x-2">
                <i class="fa fa-dashboard"></i>
                <span class="cursor-pointer">Dashboard</span>
                <i class="fa fa-angle-right"></i>
                <span class="cursor-not-allowed text-gray-400">
                    <span style="text-transform: capitalize;">{{ route.params?.status.replaceAll('_', ' ') + ' '
                        }}</span>Applications
                </span>
            </div>
        </div>

        <div class="max-w-7xl mx-auto bg-white rounded-lg shadow border p-4 min-h-[calc(100vh-250px)] relative">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-xl font-semibold"><span style="text-transform: capitalize;">{{
                        route.params?.status.replaceAll('_', ' ') + ' '
                    }}</span> Applications</h2>
                <router-link
                    v-if="(store.user?.role === 'DEO' || store.user?.role === 'Center In-charge') && route.path === '/applications/all'"
                    to="/applications/create"
                    class="group flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg
                       shadow-sm hover:shadow-md
                       transition-all duration-300 ease-out
                       hover:bg-primary-dark">
                    <i class="fa fa-plus transition-transform duration-300 group-hover:rotate-90"></i>
                    <span class="transition-transform duration-300 group-hover:translate-x-1">New Application</span>
                </router-link>
            </div>
            <hr>
            <!-- Filter section -->
            <transition enter-active-class="transition-all duration-300 ease-out"
                        leave-active-class="transition-all duration-200 ease-in"
                        enter-from-class="opacity-0 -translate-y-2"
                        leave-to-class="opacity-0 -translate-y-2">
                <div class="bg-white mb-3 flex justify-between items-center gap-3 mt-4">
                    <select v-model="filterBy" class="border px-1 py-2 rounded w-32">
                        <option value="" disabled>Search By</option>
                        <option value="tracking_no">Tracking No</option>
                        <option value="applicant_name">Applicant Name</option>
                        <option value="identity_number">Identity Number</option>
                        <option value="token">Q-Matic Token</option>
                        <option value="missal">Missal No</option>
                    </select>

                    <input v-model="searchQuery" type="text" placeholder="Search for..."
                           class="border px-3 py-2 rounded md:w-[350px]" @keydown.enter="fetchApplications" />

                    <div class="treeselect-wrapper" style="width: 200px; min-width: 200px; max-width: 200px;">
                        <treeselect
                            v-model="selectedRegions"
                            :options="regionTreeData"
                            placeholder="All Administrative Areas"
                            :clearable="true"
                            :searchable="false"
                            :close-on-select="false"
                            :open-on-click="true"
                            :multiple="true"
                            :limit="0"
                            :limit-text="count => `${count} selected`"
                            :flat="false"
                        />
                    </div>

                    <select v-model="selectedService" class="border px-3 py-2 rounded w-48">
                        <option value="">All Services</option>
                        <!--  TODO: we need to add slug for service name-->
                        <option v-for="service in services" :key="service.id"
                                :value="service.name==='State Subject Certificate'? 'state': service.slug || service.name">
                            {{ service.name }}
                        </option>
                    </select>
                    <div class="buttons-wrapper flex justify-between items-center gap-2">
                        <button @click="fetchApplications"
                                class="flex items-center gap-2 bg-transparent text-primary hover:bg-primary hover:text-white border border-primary  px-4 py-2 rounded-lg">
                            <i class="fa fa-search"></i>
                            Search
                        </button>
                        <button @click="clearFilters"
                                class="flex items-center gap-2 bg-transparent text-primary hover:bg-primary hover:text-white border border-primary px-4 py-2 rounded-lg transition">
                            <i class="fa fa-rotate-left"></i>
                            Reset
                        </button>
                    </div>
                </div>
            </transition>

            <!-- Table -->
            <div class="bg-white rounded shadow">
                <table class="min-w-full border text-sm">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-2 text-center">Tracking No</th>
                        <th class="border px-3 py-2 text-center">Missal No</th>
                        <th class="border px-3 py-2 text-center">Service Name</th>
                        <th class="border px-3 py-2 text-center">Type</th>
                        <th class="border px-3 py-2 text-center">Applicant Full Name</th>
                        <th class="border px-3 py-2 text-center">CNIC / Refugee No</th>
                        <th class="border px-3 py-2 text-center">Current Status</th>
                        <th class="border px-3 py-2 text-center">Created On</th>
                        <th class="border px-3 py-2 text-center">Action</th>
                    </tr>
                    </thead>

                    <tbody>
                    <!-- Loading State -->
                    <tr v-if="loading">
                        <td colspan="9" class="border px-3 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <i class="fa fa-spinner fa-spin text-blue-600"></i>
                                <span>Loading applications...</span>
                            </div>
                        </td>
                    </tr>

                    <!-- No Data State -->
                    <tr v-else-if="applications.length === 0">
                        <td colspan="9" class="border px-3 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center gap-2">
                                <i class="fa fa-folder-open text-3xl text-gray-400"></i>
                                <span>No applications found</span>
                            </div>
                        </td>
                    </tr>

                    <!-- Data Rows -->
                    <tr v-else v-for="(app, index) in applications" :key="index" class="hover:bg-gray-50">
                        <td class="border px-3 py-2 text-center">{{ app.tracking_token_no || 'N/A' }}</td>
                        <td class="border px-3 py-2 text-center">{{ app.missal_no || '-' }}</td>
                        <td class="border px-3 py-2 text-center">
                            <Badge v-if="app.certificate_type === 'state'" variant="blue" class="px-2 py-1">
                                {{ readableService(app.certificate_type) }}
                            </Badge>
                            <Badge v-if="app.certificate_type === 'domicile'" variant="green" class="px-2 py-1">
                                {{ readableService(app.certificate_type) }}
                            </Badge>
                            <Badge v-if="app.certificate_type === 'both'" variant="yellow" class="px-2 py-1">
                                {{ readableService(app.certificate_type) }}
                            </Badge>
                        </td>
                        <td class="border px-3 py-2 text-center">
                            <Badge v-if="readableType(app.application_type_id) === 'New'" variant="purple-light"
                                   class="px-2 py-1">
                                New
                            </Badge>

                            <Badge v-else-if="readableType(app.application_type_id) === 'Duplicate'" variant="red-light"
                                   class="px-2 py-1">
                                Duplicate
                            </Badge>

                            <Badge v-else variant="gray" class="px-2 py-1">
                                {{ readableType(app.application_type_id) }}
                            </Badge>
                        </td>
                        <td class="border px-3 py-2 text-center font-nastaleeq">{{ app.applicant?.full_name }}</td>
                        <td class="border px-3 py-2 text-center whitespace-nowrap">
                            {{ app.applicant?.identity_type === 'local' ?
                            formatCNIC(app.applicant?.identity_number.trim()) :
                            app.applicant?.identity_number.trim() }}
                        </td>
                        <td class="border px-3 py-2 capitalize text-center">
                            <Badge v-if="app.current_status === 'pending'" variant="gray" class="px-2 py-1">
                                {{ app.current_status }}
                            </Badge>
                            <Badge v-else-if="app.current_status === 'submitted'" variant="green" class="px-2 py-1">
                                {{ app.current_status }}
                            </Badge>
                            <Badge v-else-if="app.current_status === 'approved'" variant="green" class="px-2 py-1">
                                {{ app.current_status }}
                            </Badge>
                            <Badge v-else-if="app.current_status === 'delivered'" variant="blue" class="px-2 py-1">
                                {{ app.current_status }}
                            </Badge>
                            <Badge v-else variant="gray" class="px-2 py-1 uppercase">
                                {{ app.current_status.replaceAll('_', ' ').replaceAll('-', '') }}
                            </Badge>
                        </td>
                        <td class="border px-2 py-2 text-center">
                           <div class="flex flex-col items-center gap-1 text-xs text-muted-foreground">
                               <span>{{ formatDMY(app.created_at) }}</span>
                               <span>{{ formatTime(app.created_at) }}</span>
                           </div>
                        </td>
                        <td class="border px-2 py-2 text-center">
                            <button @click="toggleDropdown(app, $event)"
                                    class="dropdown-btn inline-flex items-center justify-center text-center w-8 h-8 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded transition"
                                    title="More Actions">
                                <i class="fa fa-ellipsis-v"></i>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <!-- Global Dropdown Menu -->
            <Teleport to="body">
                <div v-if="openDropdownId !== null"
                     class="absolute bg-white border border-gray-200 rounded shadow-xl z-[9999] w-48"
                     :style="dropdownPosition" @click.stop>

                    <!-- Forward -->
                    <router-link v-if="selectedApp && canForward(selectedApp)"
                                 :to="{ name: 'applications.forward', params: { id: selectedApp.uuid } }"
                                 class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 border-b transition"
                                 @click="openDropdownId = null">
                        <i class="fa fa-arrow-right text-blue-600 w-4"></i>
                        <span>{{ forwardText }}</span>
                    </router-link>

                    <!-- Edit -->
                    <router-link
                        v-if="selectedApp && selectedApp.current_status === 'pending' && store.user?.role === 'DEO' && route.path === '/applications/all'"
                        :to="{ name: 'applications.edit', params: { uuid: selectedApp.uuid } }"
                        class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 border-b transition"
                        @click="openDropdownId = null">
                        <i class="fa fa-edit text-blue-600 w-4"></i>
                        <span>Edit</span>
                    </router-link>

                    <!-- Statement -->
                    <button v-if="selectedApp && selectedApp.current_status === 'pending' && store.user?.role === 'DEO'"
                        @click="openStatement(selectedApp)"
                        class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 border-b transition w-full">

                        <i class="fa fa-file-text text-blue-600 w-4"></i>
                        <span>Statement</span>
                    </button>

                    <!-- Print -->
                    <button v-if="selectedApp" @click="() => { printApplication(selectedApp); openDropdownId = null; }"
                        class="w-full flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 border-b transition">
                        <i class="fa fa-print text-blue-600 w-4"></i>
                        <span>Print</span>
                    </button>

                    <!-- View -->
                    <button v-if="selectedApp" @click="() => { viewApplication(selectedApp); openDropdownId = null; }"
                        class="w-full flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-green-50 border-b transition">
                        <i class="fa fa-eye text-green-600 w-4"></i>
                        <span>Application Receipt</span>
                    </button>

                    <!-- Final -->
                    <button v-if="selectedApp && selectedApp.current_status === 'delivered'"
                            @click="() => { printFinal(selectedApp); openDropdownId = null; }"
                            class="w-full flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 border-b transition">
                        <i class="fa fa-file-pdf text-emerald-600 w-4"></i>
                        <span>Print Final</span>
                    </button>

                    <router-link
                        v-if="selectedApp && (selectedApp.current_status === 'approved' || selectedApp.current_status === 'delivered' ) && (store.user?.role === 'DEO' || store.user?.role === 'Center In-charge') && selectedApp?.certificates?.length > 0"
                        :to="{ name: 'certificates-pdf', params: { uuid: selectedApp.uuid } }"
                        class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 border-b transition"
                        @click="openDropdownId = null">
                        <i class="fa-solid fa-file-pdf text-red-600 w-4"></i>
                        <span>Print Certificate(s)</span>
                    </router-link>

                    <!-- Diary No. -->
                    <button v-if="selectedApp" @click="() => { openDiaryModal(selectedApp); openDropdownId = null; }"
                        class="w-full flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-red-50 border-b transition">
                        <i class="fa fa-book text-red-600 w-4"></i>
                        <span>Diary of Actions</span>
                    </button>
                </div>
            </Teleport>
            <ul class="inline-flex items-center space-x-1 rtl:space-x-reverse m-auto justify-end w-full mt-5 mb-3">

                <!-- Prev -->
                <button v-if="lastPage > 1" @click="fetchApplications(currentPage - 1)" :disabled="currentPage === 1"
                    class="px-3.5 py-1 rounded font-semibold bg-gray-200 hover:bg-blue-500 hover:text-white">
                    Prev
                </button>

                <!-- Pages -->
                <li v-for="(page, index) in visiblePages" :key="index" v-if="lastPage > 1">
                    <button v-if="page !== '...'" @click="fetchApplications(page)" :class="[
                        'px-3 py-1 rounded font-semibold transition',
                        page === currentPage
                            ? 'bg-blue-600 text-white'
                            : 'bg-gray-200 text-gray-700 hover:bg-blue-500 hover:text-white'
                    ]">
                        {{ page }}
                    </button>

                    <span v-else class="px-3 py-1 text-gray-500 font-bold">
                        ...
                    </span>
                </li>

                <!-- Next -->
                <button v-if="lastPage > 1" @click="fetchApplications(currentPage + 1)"
                        :disabled="currentPage === lastPage"
                        class="px-3.5 py-1 rounded font-semibold bg-gray-200 hover:bg-blue-500 hover:text-white">
                    Next
                </button>
            </ul>
        </div>
        <WorkflowHistoryDialog v-model="showDiaryModal" :application="selectedApp" @close="showDiaryModal = false" />
        <templateDialog ref="templateModalRef" />
    </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import axios from 'axios';
import { useRoute } from 'vue-router';
import { useAppStore } from '@/stores/index';
import router from '@/router';
import WorkflowHistoryDialog from '@/views/applications/Dialogs/WorkflowHistoryDialog.vue';
import Badge from '@/components/Badge.vue';
import { storeToRefs } from 'pinia';
import { formatDMY, formatTime } from '@/mixin/index.ts';
import Treeselect from 'vue3-treeselect';
import 'vue3-treeselect/dist/vue3-treeselect.css';
import templateDialog from '@/views/applications/Dialogs/templateDialog.vue';


const templateModalRef = ref(null)

const openStatement = (app) => {
  templateModalRef.value.openTemplateModal(app)
  openDropdownId.value = null
}

const store = useAppStore();

// State
const showDiaryModal = ref(false);
const loading = ref(false);
const applications = ref([]);
const status = ref('all');
const route = useRoute();
const openDropdownId = ref(null);
const selectedApp = ref(null);
const dropdownPosition = ref({ top: '0px', left: '0px' });
const currentPage = ref(1);
const lastPage = ref(1);
const perPage = ref(15);

const filterBy = ref('');
const searchQuery = ref('');
const selectedRegions = ref([]); // Array for multiple selections
const selectedService = ref('');
const { regions } = storeToRefs(store);
const services = ref([]);

// Track selected region IDs for API filtering
const selectedRegionIds = ref([]);

const forwardText = computed(() => {
    if (selectedApp.value?.current_status === 'pending') {
        return 'Forward to AC';
    }
    if (selectedApp.value?.current_status === 'submitted') {
        return 'Verify Application';
    }
    if (selectedApp.value?.current_status === 'verified') {
        return 'Approve Application';
    }
    // if (selectedApp.value?.current_status === 'approved') {
    //     return 'Send to Delivery';
    // }
    if (selectedApp.value?.current_status === 'ready_for_delivery') {
        return 'Mark Delivered';
    }
    if (selectedApp.value?.current_status === 'objected') {
        return 'Restore';
    }
    return 'Forward';
});

// Transform regions data into tree structure for Treeselect
const regionTreeData = computed(() => {
    if (!regions.value?.length) return [];

    const role = store.user?.role;

    if (role === 'Commissioner') {
        const userRegionId = store.user?.region_id;
        const filteredRegions = regions.value.filter(r => r.id === userRegionId);

        return filteredRegions.map(region => ({
            id: `region_${region.id}`,
            label: region.name,
            children: region.districts?.map(district => ({
                id: `district_${district.id}`,
                label: district.name,
                children: district.tehsils?.map(tehsil => ({
                    id: `tehsil_${tehsil.id}`,
                    label: tehsil.name
                })) || []
            })) || []
        }));
    } else if (role === 'DC') {
        const userDistrictId = store.user?.district_id;
        let userDistrict = null;
        regions.value.forEach(r => {
            const found = r.districts?.find(d => d.id === userDistrictId);
            if (found) userDistrict = found;
        });

        if (!userDistrict) return [];

        return [{
            id: `district_${userDistrict.id}`,
            label: userDistrict.name,
            children: userDistrict.tehsils?.map(tehsil => ({
                id: `tehsil_${tehsil.id}`,
                label: tehsil.name
            })) || []
        }];
    } else if (['AC', 'ACR', 'DEO', 'Center In-Charge'].includes(role)) {
        const userTehsilId = store.user?.tehsil_id;
        let userTehsil = null;
        regions.value.forEach(r => {
            r.districts?.forEach(d => {
                const found = d.tehsils?.find(t => t.id === userTehsilId);
                if (found) userTehsil = found;
            });
        });

        if (!userTehsil) return [];

        return [{
            id: `tehsil_${userTehsil.id}`,
            label: userTehsil.name
        }];
    }

    // Default (Super Admin or others)
    return regions.value.map(region => ({
        id: `region_${region.id}`,
        label: region.name,
        children: region.districts?.map(district => ({
            id: `district_${district.id}`,
            label: district.name,
            children: district.tehsils?.map(tehsil => ({
                id: `tehsil_${tehsil.id}`,
                label: tehsil.name
            })) || []
        })) || []
    }));
});

const visiblePages = computed(() => {
    const pages = [];
    const total = lastPage.value;
    const current = currentPage.value;

    if (total <= 4) {
        for (let i = 1; i <= total; i++) {
            pages.push(i);
        }
    } else {

        let start = Math.max(1, current - 1);
        let end = start + 2;

        if (end >= total) {
            end = total;
            start = total - 2;
        }

        for (let i = start; i <= end; i++) {
            pages.push(i);
        }

        if (end < total) {
            pages.push('...');
            pages.push(total);
        }
    }

    return pages;
});

// Ensure selectedRegionIds is perfectly synchronized with the treeselect v-model
watch(() => selectedRegions.value, (newVal) => {
    if (!newVal || (Array.isArray(newVal) && newVal.length === 0)) {
        selectedRegionIds.value = [];
        return;
    }
    const valArray = Array.isArray(newVal) ? newVal : [newVal];
    selectedRegionIds.value = valArray.map(item => (item && typeof item === 'object' && item.id) ? item.id : item);
}, { deep: true });

const openPDFLink = (pdf, type) => {
    window.open(pdf.url + `?type=${type}`, '_blank');
};

const fetchApplications = async (page = 1) => {
    loading.value = true;
    try {
        status.value = route.params?.status || 'all';

        const params = {
            page: page,
            filterBy: filterBy.value || null,
            search: searchQuery.value || null,
            service: selectedService.value?.toLowerCase() || null,
            district_id: sessionStorage.getItem('district_id') || null,
            tehsil_id: sessionStorage.getItem('tehsil_id') || null,
            center_id: sessionStorage.getItem('center_id') || null
        };

        // Handle multiple region, district, and tehsil IDs
        if (selectedRegionIds.value.length > 0) {
            const rIds = selectedRegionIds.value.filter(id => typeof id === 'string' && id.startsWith('region_')).map(id => id.replace('region_', ''));
            const dIds = selectedRegionIds.value.filter(id => typeof id === 'string' && id.startsWith('district_')).map(id => id.replace('district_', ''));
            const tIds = selectedRegionIds.value.filter(id => typeof id === 'string' && id.startsWith('tehsil_')).map(id => id.replace('tehsil_', ''));

            if (rIds.length > 0) params.region_ids = rIds.join(',');
            if (dIds.length > 0) params.district_ids = dIds.join(',');
            if (tIds.length > 0) params.tehsil_ids = tIds.join(',');
        }

        // Remove null/undefined values
        Object.keys(params).forEach(key => {
            if (params[key] === null || params[key] === undefined || params[key] === '') {
                delete params[key];
            }
        });

        const response = await axios.get(`/api/applications/list/${status.value}`, { params });

        applications.value = response.data.data.data || [];
        currentPage.value = response.data.data.current_page;
        lastPage.value = response.data.data.last_page;
        perPage.value = response.data.data.per_page;

        sessionStorage.removeItem('district_id');
        sessionStorage.removeItem('tehsil_id');
        sessionStorage.removeItem('center_id');

    } catch (error) {
        console.error('Error fetching applications:', error);
    } finally {
        loading.value = false;
    }
};

const clearFilters = () => {
    filterBy.value = '';
    searchQuery.value = '';
    selectedRegions.value = [];
    selectedRegionIds.value = [];
    selectedService.value = '';
    fetchApplications();
};


const fetchServices = async () => {
    try {
        const res = await axios.get('/api/services');
        services.value = res.data.data.data;
    } catch (error) {
        console.error('Error fetching services:', error);
    }
};

onMounted(() => {
    if (store.isAuthenticated) {
        fetchApplications();
        fetchServices();
        store.loadDropdowns();
    }
});

// Toggle dropdown menu
const toggleDropdown = (app, event) => {
    if (openDropdownId.value === app.id) {
        openDropdownId.value = null;
        selectedApp.value = null;
        return;
    }

    openDropdownId.value = app.id;
    selectedApp.value = app;

    // Calculate the position of the dropdown
    const button = event.target.closest('button');
    if (button) {
        const rect = button.getBoundingClientRect();
        dropdownPosition.value = {
            top: (rect.bottom + window.scrollY + 8) + 'px',
            left: (rect.right + window.scrollX - 200) + 'px'
        };
    }

    // Close dropdown when clicking outside
    setTimeout(() => {
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.dropdown-btn')) {
                openDropdownId.value = null;
                selectedApp.value = null;
            }
        }, { once: true });
    }, 0);
};

// Watch route changes for status and re-fetch
watch(() => route.params?.status, fetchApplications);

watch(
    () => route.query.search,
    (newSearch) => {
        if (newSearch) {
            searchQuery.value = newSearch;
            fetchApplications();
        }
    },
    { immediate: true }
);

const canForward = (app) => {
    if ((status.value === 'all')) {
        return false;
    }

    const role = store?.user?.role;
    const appStatus = app.current_status;

    return (['DEO', 'Center In-charge'].includes(role) && ['pending', 'approved', 'ready_for_delivery', 'objected'].includes(appStatus)) ||
        (role === 'AC' && appStatus === 'submitted') ||
        (role === 'ACR' && appStatus === 'submitted') ||
        (role === 'DC' && appStatus === 'verified');
};

const canEdit = (app) => {
    console.log('canEdit check:', app.current_status, app);
    return app?.current_status === 'pending';
};

const formatCNIC = (cnic) => {
    if (!cnic) return '-';
    const cleaned = cnic.replace(/\D/g, '');
    if (cleaned.length === 13) return `${cleaned.substring(0, 5)}-${cleaned.substring(5, 12)}-${cleaned.substring(12)}`;
    return cnic;
};

// Readable fields
const readableService = (type) => {
    if (type === 'domicile') return 'Domicile Certificate';
    if (type === 'state') return 'State Subject Certificate';
    if (type === 'both') return 'Domicile & State Subject';
    return '-';
};

const readableType = (typeId) => {
    if (typeId === 1) return 'New';
    if (typeId === 2) return 'Duplicate';
    if (typeId === 3) return 'Renewal';
    return '-';
};

// Actions
const printApplication = (app) => {
    if (app.certificate_type === 'domicile') {
        router.push(`/print-form-domicile/${app.uuid}`);
    }
    if (app.certificate_type === 'state') {
        router.push(`/print-form-state/${app.uuid}`);
    }
};
const viewApplication = (app) => router.push(`/application-form/${app.uuid}`);
const viewDocuments = (app) => router.push(`/applications/forward/${app.id}`);
const printFinal = (app) => {
    if (app.certificate_type === 'domicile') {
        router.push(`/final-form-domicile/${app.id}`);
    } else if (app.certificate_type === 'state') {
        router.push(`/final-form-state/${app.id}`);
    } else {
        console.log('form type both not supported yet');
        alert('form type both not supported yet');
    }
};
const openDiaryModal = (app) => {
    selectedApp.value = app;
    showDiaryModal.value = true;
};

</script>

<style scoped>
:deep(.vue-treeselect) {
    font-size: 14px;
    width: 100%;
}

:deep(.vue-treeselect__control) {
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    min-height: 38px;
    background-color: white;
    transition: all 0.2s ease;
}

:deep(.vue-treeselect__control:hover) {
    border-color: #9ca3af;
}

:deep(.vue-treeselect--focused .vue-treeselect__control) {
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
}

:deep(.vue-treeselect__placeholder) {
    color: #9ca3af;
    line-height: 38px;
}

:deep(.vue-treeselect__multi-value) {
    border-radius: 0.25rem;
    padding: 2px 6px;
    margin: 2px;
}

:deep(.vue-treeselect__multi-value-label) {
    color: #1e40af;
}

:deep(.vue-treeselect__multi-value-icon) {
    color: #1e40af;
}

:deep(.vue-treeselect__multi-value-icon:hover) {
    background-color: #bfdbfe;
    color: #1e3a8a;
}

:deep(.vue-treeselect__option--highlight) {
    background: #eff6ff;
}

:deep(.vue-treeselect__option--selected) {
    background: #dbeafe;
    color: #1e40af;
    font-weight: 500;
}

:deep(.vue-treeselect__menu) {
    max-height: 300px;
    overflow-y: auto;
    border-radius: 0.375rem;
    margin-top: 4px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

/* Optional: Style for disabled state */
:deep(.vue-treeselect--disabled .vue-treeselect__control) {
    background-color: #f3f4f6;
    cursor: not-allowed;
}

/* Style for the value container to handle multiple items */
:deep(.vue-treeselect__value-container) {
    padding: 2px 8px;
    flex-wrap: wrap;
}
</style>
