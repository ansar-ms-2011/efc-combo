<template>
    <div class="p-6 min-h-screen">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center text-sm text-gray-600 space-x-2">
                <i class="fa fa-dashboard"></i>
                <router-link to="/">Dashboard</router-link>
                <i class="fa fa-angle-right"></i>
                <router-link to="/archived/scanning-form/all">Scanning Form</router-link>
                <i class="fa fa-angle-right"></i> <span class="cursor-pointer"> Applications Verification</span>
            </div>
        </div>

        <!-- Modal Component -->
        <VerApplicantModel :show="showModal" :applicant="selectedApplicantForEdit" @close="closeModal" @verified="fetchApplications" :tehsils="tehsils" />
        <div v-if="hasPermission('archived-verification.view')" class="max-w-7xl mx-auto bg-white rounded-lg shadow border p-6">
            <div class="flex items-center justify-between mb-4 border-b">
                <h2 class="text-xl font-semibold mb-3">Applications Verification</h2>
            </div>

            <!-- Filter section -->
            <transition
                enter-active-class="transition-all duration-300 ease-out"
    
                leave-active-class="transition-all duration-200 ease-in"
                enter-from-class="opacity-0 -translate-y-2"
                leave-to-class="opacity-0 -translate-y-2"
            >
                <div v-if="showFilter" class="bg-white p-4 mb-4 rounded shadow flex gap-4">
                    <div class="flex gap-2">
                        <input
                            type="text"
                            v-model="searchQuery"
                            placeholder="Search by Name, CNIC,Refugee No "
                            class="border px-3 py-2 rounded w-64"
                            @keyup.enter="fetchApplications()"
                        />
                    </div>

                 
                    <button @click="fetchApplications" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded flex items-center gap-1">
                        <i class="fa fa-search"></i> Search
                    </button>
                </div>
            </transition>

            <!-- Table -->
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="min-w-full border text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-3 py-2 text-center">ID</th>
                            <th class="border px-3 py-2 text-center">Name</th>
                            <th class="border px-3 py-2 text-center"> Father Name </th>
                            <th class="border px-3 py-2 text-center">Type</th>
                            <th class="border px-3 py-2 text-center">Identity No </th>
                            <th class="border px-3 py-2 text-center">Tehsil</th>
                            <th class="border px-3 py-2 text-center">Misal No </th>
                            <th class="border px-3 py-2 text-center">Created By </th>
                            <th class="border px-3 py-2 text-center">Data Entered By</th>
                            <th class="border px-3 py-2 text-center">Image Uploaded By</th>
                            <th class="border px-3 py-2 text-center">Created At </th>
                            <th class="border px-3 py-2 text-center">Status</th>
                            <th class="border px-3 py-2 text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-if="loading">
                            <td colspan="10" class="border px-3 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <i class="fa fa-spinner fa-spin text-blue-600"></i>
                                    <span>Loading applications...</span>
                                </div>
                            </td>
                        </tr>

                        <tr v-else-if="applications.length === 0">
                            <td colspan="10" class="border px-3 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center gap-2">
                                    <i class="fa fa-folder-open text-3xl text-gray-400"></i>
                                    <span>No applications found</span>
                                </div>
                            </td>
                        </tr>

                        <!-- Data Rows -->
                        <tr v-else v-for="app in applications" :key="app.id" class="hover:bg-gray-50 transition">
                            <td class="border px-3 py-2 text-center text-xs">{{ app.id }}</td>
                            <!--draft -->
                            <template v-if="app.applicant?.identity_number === 'DRAFT-00000'">
                                <td class="border px-3 py-2  text-center text-xs">
                                    {{ app.applicant.full_name }}
                                </td>
                                <td class="border px-3 py-2"></td>
                                <td class="border px-3 py-2"></td>
                                <td class="border px-3 py-2"></td>
                                <td class="border px-3 py-2"></td>
                                <td class="border px-3 py-2"></td>

                                <td class="border px-3 py-2 text-center text-xs">{{ app.uploader?.first_name }} {{ app.uploader?.last_name || '' }}</td>
                                 <td class="border px-3 py-2 text-center text-xs">
                                    {{ app.verification?.data_enterer?.first_name || 'N/A' }}
                                </td>
                                <td class="border px-3 py-2 text-center text-xs ">
                                    {{ app.verification?.image_uploader?.first_name || 'N/A' }}
                                </td>
                                <td class="border px-3 py-2 text-center text-xs">
                                    {{ formatDate(app.created_at) }}
                                </td>
                                
                                 <td class="border px-3 py-2 text-center">
                                    <span
                                        v-if="app.verification?.status === 'verified'"
                                        class="inline-flex items-center gap-1 bg-green-100 text-green-700 px-2 py-1 rounded text-[10px] font-bold border border-green-200"
                                    >
                                        Verified
                                    </span>
                                    <span
                                        v-else-if="app.verification?.status === 'rejected'"
                                        class="inline-flex items-center gap-1 bg-red-100 text-red-700 px-2 py-1 rounded text-[10px] font-bold border border-red-200"
                                        :title="'Reason: ' + app.verification?.remarks"
                                    >
                                        Rejected
                                    </span>
                                    <span
                                        v-else
                                        class="inline-flex items-center gap-1 bg-orange-100 text-gray-600 px-2 py-1 rounded text-[10px] font-bold border border-orange-200"
                                    >
                                        Pending
                                    </span>
                                </td>
                            </template>
                            <template v-else>
                                <td class="border px-3 py-2 text-center text-xs">
                                    {{ app.applicant.full_name }}
                                </td>
                                <td class="border px-3 py-2   text-center text-xs">
                                    {{ app.applicant.father_name || 'N/A' }}
                                </td>
                                <td class="border px-3 py-2 text-center">
                                    <span
                                        :class="app.is_refugee ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'"
                                        class="px-2 py-1 rounded-full text-xs"
                                    >
                                        {{ app.applicant.identity_type }}
                                    </span>
                                </td>
                                <td class="border px-3 py-2 text-center text-xs">
                                    {{ app.applicant.identity_number || 'N/A' }}
                                </td>
                                <td class="border px-3 py-2  text-center">
                                    <span v-if="app.applicant?.tehsil?.name">
                                        {{  app.applicant.tehsil.name }}
                                    </span>
                                </td>
                                <td class="border px-3 py-2 text-center text-xs">
                                    {{ app.misal_no || 'N/A' }}
                                </td>
                                <td class="border px-3 py-2 text-center text-xs">{{ app.uploader?.first_name }} {{ app.uploader?.last_name || '' }}</td>
                                 <td class="border px-3 py-2 text-center text-xs ">
                                    {{ app.verification?.data_enterer?.first_name || 'N/A' }}
                                </td>
                                <td class="border px-3 py-2 text-center text-xs ">
                                    {{ app.verification?.image_uploader?.first_name || 'N/A' }}
                                </td>
                                <td class="border px-3 py-2 text-center text-xs">
                                    {{ formatDate(app.created_at) }}
                                </td>
                                <td class="border px-3 py-2 text-center">
                                    <span
                                        v-if="app.verification?.status === 'verified'"
                                        class="inline-flex items-center gap-1 bg-green-100 text-green-700 px-2 py-1 rounded text-[10px] font-bold border border-green-200"
                                    >
                                        Verified
                                    </span>
                                    <span
                                        v-else-if="app.verification?.status === 'rejected'"
                                        class="inline-flex items-center gap-1 bg-red-100 text-red-700 px-2 py-1 rounded text-[10px] font-bold border border-red-200"
                                        :title="'Reason: ' + app.verification?.remarks"
                                    >
                                        Rejected
                                    </span>
                                    <span
                                        v-else
                                        class="inline-flex items-center gap-1 bg-orange-100 text-gray-600 px-2 py-1 rounded text-[10px] font-bold border border-orange-200"
                                    >
                                        Pending
                                    </span>
                                </td>
                            </template>
                            <td class="border px-2 py-2 text-center">
                                <div class="flex justify-center items-center">
                                    <button @click="viewApplicant(app)" class="text-blue-600 hover:text-blue-800 mx-1" title="View Details">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                    <button
                                        v-if="hasPermission('archived-verification.edit')"
                                        @click="editApplicant(app)"
                                        class="text-green-600 hover:text-green-800 mx-1"
                                        title="Verify"
                                    >
                                        <i class="fa fa-check-circle text-lg"></i>
                                    </button>
                                    <button @click="deleteApplicant(app)" class="text-red-600 hover:text-red-800 mx-1" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="lastPage > 1" class="flex justify-end mt-6 mb-2" >
                <ul class="inline-flex items-center space-x-1 rtl:space-x-reverse">
                    <!-- Previous Button -->
                    <li>
                        <button
                            @click="fetchApplications(currentPage - 1)"
                            :disabled="currentPage === 1"
                            class="px-3 py-1 rounded border bg-gray-50 text-gray-600 hover:bg-blue-600 hover:text-white disabled:opacity-50 disabled:cursor-not-allowed transition-all font-medium text-sm"
                        >
                            <i class="fa fa-angle-left mr-1"></i> Prev
                        </button>
                    </li>

                    <!-- Page Numbers -->
                    <li v-for="(page, index) in visiblePages" :key="index">
                        <button
                            v-if="page !== '...'"
                            @click="fetchApplications(page)"
                            :class="[
                                'px-3 py-1 rounded border font-semibold text-sm transition-all',
                                page === currentPage
                                    ? 'bg-blue-600 text-white border-blue-600'
                                    : 'bg-white text-gray-700 hover:bg-blue-500 hover:text-white border-gray-200',
                            ]"
                        >
                            {{ page }}
                        </button>
                        <span v-else class="px-2 text-gray-400">...</span>
                    </li>

                    <!-- Next Button -->
                    <li>
                        <button
                            @click="fetchApplications(currentPage + 1)"
                            :disabled="currentPage === lastPage"
                            class="px-3 py-1 rounded border bg-gray-50 text-gray-600 hover:bg-blue-600 hover:text-white disabled:opacity-50 disabled:cursor-not-allowed transition-all font-medium text-sm"
                        >
                            Next <i class="fa fa-angle-right ml-1"></i>
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <!-- View Modal -->
        <div v-if="showViewModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50" style="z-index: 9999">
            <div class="bg-white w-[90%] max-w-4xl max-h-[90vh] relative shadow-lg rounded-lg overflow-hidden">
                <button class="absolute top-4 right-4 text-gray-500 hover:text-red-500 z-10" @click="closeViewModal">
                    <i class="fa fa-times text-xl"></i>
                </button>

                <div class="p-6 overflow-y-auto max-h-[90vh]">
                    <h2 class="text-2xl font-semibold mb-4">Applicant Details</h2>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Name</label>
                            <p class="text-lg font-semibold">{{ selectedApplicant?.applicant?.full_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Father's Name</label>
                            <p class="text-lg font-semibold">{{ selectedApplicant?.applicant?.father_name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600">Type</label>
                            <p class="text-lg capitalize">{{ selectedApplicant?.type }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">ID Number</label>
                            <p class="font-mono">{{ selectedApplicant?.applicant?.identity_number }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Tehsil</label>
                            <p>{{ selectedApplicant?.applicant?.tehsil?.name || 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Misal No</label>
                            <p>{{ selectedApplicant?.misal_no || 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Created By</label>
                            <p>{{ selectedApplicant?.uploader?.first_name }} {{ selectedApplicant?.uploader?.last_name || '' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Created At</label>
                            <p>{{ formatDate(selectedApplicant?.created_at) }}</p>
                        </div>
                        <div v-if="selectedApplicant?.is_refugee && selectedApplicant?.refugee">
                            <label class="block text-sm font-medium text-gray-600">From/To</label>
                            <p>
                                {{ selectedApplicant?.applicant?.refugee_detail?.refugee_from }} -
                                {{ selectedApplicant?.applicant?.refugee_detail?.refugee_year }}
                            </p>
                        </div>
                    </div>

                    <h3 class="text-xl font-semibold mb-3">Scanned Document Attachment</h3>

                    <div class="border rounded-lg p-4 bg-gray-50 flex flex-col items-center">
                        <!-- File Check -->
                        <div v-if="selectedApplicant?.pdf_path && selectedApplicant.pdf_path !== 'pending'" class="w-full h-[500px]">
                            <iframe
                                v-if="selectedApplicant.pdf_path.toLowerCase().endsWith('.pdf')"
                                :src="'http://localhost:8000/storage/' + selectedApplicant.pdf_path + '#toolbar=0&navpanes=0'"
                                class="w-full h-full border rounded"
                            ></iframe>

                            <img
                                v-else
                                :src="'http://localhost:8000/storage/' + selectedApplicant.pdf_path"
                                class="max-w-full h-auto border shadow-lg mx-auto block"
                                style="max-height: 480px"
                            />
                        </div>

                        <div v-else class="text-gray-400 py-20 flex flex-col items-center">
                            <i class="fa fa-file-o text-5xl mb-3"></i>
                            <p class="italic">No document scanned or file is pending</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { computed, onMounted, ref, onBeforeUnmount } from 'vue';
    import axios from 'axios';
    import Swal from 'sweetalert2';
    import VerApplicantModel from './VerApplicantModel.vue';
    import { useAppStore } from '@/stores/index';
    const store = useAppStore();

    // State
    const showFilter = ref(true);
    const loading = ref(false);
    const applications = ref([]);
    const currentPage = ref(1);
    const lastPage = ref(1);
    const perPage = ref(10);
    const total = ref(0);

    const filterBy = ref('');
    const searchQuery = ref('');
    const showModal = ref(false);
    const showViewModal = ref(false);
    const selectedApplicant = ref(null);
    const selectedApplicantForEdit = ref(null);

    // Computed
    const visiblePages = computed(() => {
        const pages = [];
        const total = lastPage.value;
        const current = currentPage.value;

        if (total <= 5) {
            for (let i = 1; i <= total; i++) pages.push(i);
        } else {
            let start = Math.max(1, current - 2);

            let end = start + 4;
            if (end >= total) {
                end = total;
                start = total - 4;
            }
            for (let i = start; i <= end; i++) pages.push(i);
            if (end < total - 1) pages.push('...');
            if (end < total) pages.push(total);
        }
        return pages;
    });

    // Methods
    const formatDate = (dateString) => {
        if (!dateString) return '-';
        const date = new Date(dateString);
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();

        return `${day}-${month}-${year}`;
    };

    // API calls
    const fetchApplications = async (page = 1) => {
        loading.value = true;
        try {
            const params = {
                page: page,
                per_page: perPage.value,
                search: searchQuery.value,
            };

            const response = await axios.get('/api/archive-applicants', { params });
            console.log('API Response:', response.data.data.data);
            applications.value = response.data.data.data || [];
            currentPage.value = response.data.data.current_page;
            lastPage.value = response.data.data.last_page;
            total.value = response.data.data.total;
        } catch (error) {
            console.error('Error fetching applications:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to fetch applications.',
            });
        } finally {
            loading.value = false;
        }
    };

    const viewApplicant = (applicant) => {
        selectedApplicant.value = applicant;
        showViewModal.value = true;
    };

    const editApplicant = (applicant) => {
        selectedApplicantForEdit.value = applicant;
        showModal.value = true;
    };

    const deleteApplicant = async (applicant) => {
        const result = await Swal.fire({
            title: 'Delete Applicant?',
            text: `Are you sure you want to delete ${applicant.name}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel',
        });

        if (result.isConfirmed) {
            try {
                await axios.delete(`/api/archive-applicants/${applicant.id}`);
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: 'Applicant has been deleted.',
                    timer: 1500,
                    showConfirmButton: false,
                });
                fetchApplications();
            } catch (error) {
                console.error('Error deleting applicant:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to delete applicant.',
                });
            }
        }
    };

    const closeModal = () => {
        showModal.value = false;
        selectedApplicantForEdit.value = null;
    };

    const closeViewModal = () => {
        showViewModal.value = false;
        selectedApplicant.value = null;
    };
    const tehsils = ref([]);

    //Tehsil fetch
    const fetchTehsils = async () => {
        try {
            const response = await axios.get('/api/tehsils');
            tehsils.value = response.data.data;
        } catch (error) {
            console.error('Error fetching tehsils:', error);
        }
    };

    // Lifecycle
    onMounted(() => {
        fetchApplications();
        fetchTehsils();
    });

    onBeforeUnmount(() => {
        selectedApplicant.value = null;
        selectedApplicantForEdit.value = null;
    });

    const hasPermission = (permissionName) => {
        //Super Admin bypass
        if (store.user?.role_name === 'Super Admin') {
            return true;
        }

        // Regular Check
        const userPermissions = store.user?.permissions || [];
        return userPermissions.includes(permissionName.toLowerCase());
    };
</script>

<style scoped>
    .fa-spinner {
        animation: spin 1s linear infinite;
    }


    .transition {
        transition: all 0.2s ease;
    }  td {
    white-space: nowrap;    
    vertical-align: middle;
}

</style>
