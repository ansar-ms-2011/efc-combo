<template>
    <div class="p-6 min-h-screen">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center text-sm text-gray-600 space-x-2">
                <i class="fa fa-dashboard"></i>
                <router-link to="/">Dashboard</router-link>
                <i class="fa fa-angle-right"></i>
                <span class="cursor-pointer">Archived Applications</span>
            </div>
        </div>

        <AddApplicantModal
            :show="showModal"
            :applicant="selectedApplicantForEdit"
            @close="closeModal"
            @saved="handleSave"
            :all-records="applications"
            :api-errors="backendErrors"
            :tehsils="tehsils"
        />

        <div v-if="hasPermission('archived-scanner.view')" class="max-w-7xl  bg-white rounded-lg shadow border p-6">
            <!-- Buttons Section -->
            <div class="flex flex-row-reverse items-center gap-2 mb-3">
                <button
                    v-if="hasPermission('archived-scanner.create')"
                    @click="openModal"
                    class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow"
                >
                    <i class="fa fa-plus"></i> Add New
                </button>
                <button
                    @click="$refs.zipFileInput.click()"
                     :disabled="zipLoading"
                    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow"
                >
                    <i v-if="zipLoading" class="fa fa-spinner fa-spin"></i>
                    <i v-else class="fa fa-file-archive-o"></i>
                    Zip Upload
                </button>
                <input type="file" ref="zipFileInput" class="hidden" accept=".zip" @change="handleZipUpload" />
            </div>

            <div class="bg-white rounded shadow overflow-x-auto"> 
                <table class="min-w-max border text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-3 py-2 text-center">ID</th>
                            <th class="border px-3 py-2 text-center">Name</th>
                            <th class="border px-3 py-2 text-center">Father Name</th>
                            <th class="border px-3 py-2 text-center">Type</th>
                            <th class="border px-3 py-2 text-center">Identity No</th>
                            <th class="border px-3 py-2 text-center">Tehsil</th>
                            <th class="border px-3 py-2 text-center">Misal No</th>
                            <th class="border px-3 py-2 text-center">Created By</th>
                            <th class="border px-3 py-2 text-center">Data Entered By</th>
                            <th class="border px-3 py-2 text-center">Image Uploaded By</th>
                            <th class="border px-3 py-2 text-center">Created At</th>
                            <th class="border px-3 py-2 text-center">Status</th>
                            <th class="border px-3 py-2 text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-if="loading">
                            <td colspan="10" class="border px-3 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <i class="fa fa-spinner fa-spin text-blue-600"></i>
                                    <span>LOADING...</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-else-if="applications.length === 0">
                            <td colspan="10" class="border px-3 py-8 text-center text-gray-500 font-nastaleeq">No application Founded</td>
                        </tr>
                        <!--  Data Rows -->
                        <tr v-else v-for="app in applications" :key="app.id" class="hover:bg-gray-50 transition">
                            <td class="border px-3 py-2 text-center font-bold text-xs">{{ app.id }}</td>
                            <!--draft -->
                            <template v-if="app.applicant?.identity_number === 'DRAFT-00000'">
                                <td class="border px-3 py-2 text-center text-xs">
                                    {{ app.applicant.full_name }}
                                </td>
                                <td class="border px-3 py-2"></td>
                                <td class="border px-3 py-2"></td>
                                <td class="border px-3 py-2"></td>
                                <td class="border px-3 py-2"></td>
                                <td class="border px-3 py-2"></td>

                                <td class="border px-3 py-2 text-center text-xs">{{ app.uploader?.first_name }} {{ app.uploader?.last_name || '' }}</td>
                                <td class="border px-3 py-2 text-center text-xs ">
                                    {{ app.verification?.data_enterer?.first_name || 'N/A' }}
                                </td>
                                <td class="border px-3 py-2 text-center text-xs ">
                                    {{ app.verification?.image_uploader?.first_name || 'N/A' }}
                                </td>

                                <td class="border px-3 py-2 text-center text-xs font-sans" dir="ltr">
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

                            <!-- normal data -->
                            <template v-else>
                                <td class="border px-3 py-2 text-center text-xs">
                                    {{ app.applicant.full_name }}
                                </td>
                                <td class="border px-3 py-2 text-center text-xs">
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
                                <td class="border px-3 py-2 text-center text-xs">
                                    <span v-if="app.applicant?.tehsil?.name">
                                        {{ app.applicant.tehsil.name }}
                                    </span>
                                    <span v-else class="text-gray-400">N/A</span>
                                </td>
                                <td class="border px-3 py-2 text-center text-xs">
                                    {{ app.misal_no || 'N/A' }}
                                </td>
                                <td class="border px-3 py-2 text-center text-xs">{{ app.uploader?.first_name }} {{ app.uploader?.last_name || '' }}</td>
                                <td class="border px-3 py-2 text-center text-xs">
                                    {{ app.verification?.data_enterer?.first_name || 'N/A' }}
                                </td>
                                <td class="border px-3 py-2 text-center text-xs ">
                                    {{ app.verification?.image_uploader?.first_name || 'N/A' }}
                                </td>

                                <td class="border px-3 py-2 text-center text-xs font-sans">
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
                                    <button @click="viewApplicant(app)" class="text-blue-600 hover:text-blue-800 mx-1" title="see">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                    <button
                                        v-if="hasPermission('archived-scanner.create')"
                                        @click="editApplicant(app)"
                                        class="text-green-600 hover:text-green-800 mx-1"
                                        title=" fill form"
                                    >
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button @click="deleteApplicant(app)" class="text-red-600 hover:text-red-800 mx-1">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination  -->
            <ul v-if="lastPage > 1" class="inline-flex items-center space-x-1 rtl:space-x-reverse justify-end w-full mt-5 mb-3">
                <li>
                    <button
                        @click="fetchApplications(currentPage - 1)"
                        :disabled="currentPage === 1"
                        class="px-3.5 py-1 rounded font-semibold bg-gray-200 disabled:opacity-50"
                    >
                        Prev
                    </button>
                </li>
                <li v-for="(page, index) in visiblePages" :key="index">
                    <button
                        v-if="page !== '...'"
                        @click="fetchApplications(page)"
                        :class="[page === currentPage ? 'bg-blue-600 text-white' : 'bg-gray-200']"
                        class="px-3 py-1 rounded font-semibold"
                    >
                        {{ page }}
                    </button>
                    <span v-else class="px-3 py-1">...</span>
                </li>
                <li>
                    <button
                        @click="fetchApplications(currentPage + 1)"
                        :disabled="currentPage === lastPage"
                        class="px-3.5 py-1 rounded font-semibold bg-gray-200 disabled:opacity-50"
                    >
                        Next
                    </button>
                </li>
            </ul>
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
                            <p class="text-lg font-semibold">{{ selectedApplicant?.applicant?.father_name || 'N/A' }}</p>
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
                       
                    </div>

                    <!-- Heading  -->
                    <h3 class="text-xl font-semibold mb-3">Scanned Document Attachment</h3>

                    <div class="border rounded-lg p-4 bg-gray-50 flex flex-col items-center">
                        <!-- File Check -->
                        <div v-if="selectedApplicant?.pdf_path && selectedApplicant.pdf_path !== 'pending'" class="w-full h-[500px]">
                            <!--  PDF -->
                            <iframe
                                v-if="selectedApplicant.pdf_path.toLowerCase().endsWith('.pdf')"
                                :src="'http://localhost:8000/storage/' + selectedApplicant.pdf_path + '#toolbar=0&navpanes=0'"
                                class="w-full h-full border rounded"
                            >
                            </iframe>

                            <!-- Image -->
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
    import { computed, onMounted, ref, onBeforeUnmount, watch } from 'vue';
    import axios from 'axios';
    import Swal from 'sweetalert2';
    import AddApplicantModal from './AddApplicantModal.vue';
    import { useAppStore } from '@/stores/index';
    const store = useAppStore();

    import { useRoute } from 'vue-router';
    const route = useRoute();

    // State
    const loading = ref(false);
    const applications = ref([]);
    const currentPage = ref(1);
    const lastPage = ref(1);
    const perPage = ref(10);
    const total = ref(0);
    const backendErrors = ref({});
    const searchQuery = ref('');
    const showModal = ref(false);
    const showViewModal = ref(false);
    const selectedApplicant = ref(null);
    const saving = ref(false);
    const selectedApplicantForEdit = ref(null);
    const zipLoading = ref(false);

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
                user_id: route.query.user_id || '',
                per_page: perPage.value,
                search: searchQuery.value,
                filter: route.query.filter || '',
                // district_id: store.user?.district_id || null,
                // tehsil_id: store.user?.tehsil_id || null,
                verification_status: route.query.verification_status || '',
                data_entry: route.query.data_entry || '',
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

    const handleSave = async ({ payload, isEditMode, id }) => {
        saving.value = true;
        backendErrors.value = {};
        try {
            const data = {
                name: payload.name,
                father_name: payload.father_name,
                identity_type: payload.identity_type,
                is_refugee: !!payload.is_refugee,
                cnic: payload.cnic,
                refugee_number: payload.refugee_number,
                from: payload.from,
                to: payload.to,
                tehsil_id: payload.tehsil_id,
                misal_no: payload.misal_no,
                documents: payload.documents,
                _method: isEditMode ? 'PUT' : 'POST',
            };

            const url = isEditMode ? `/api/archive-applicants/${id}` : '/api/archive-applicants';

            const response = await axios.post(url, data);

            if (response.data.success) {
                showModal.value = false;
                backendErrors.value = {};
                await Swal.fire({
                    icon: 'success',
                    title: isEditMode ? 'Updated!' : 'Saved!',
                    text: response.data.message || (isEditMode ? 'Application updated successfully' : 'Application saved successfully'),
                    timer: 2000,
                    showConfirmButton: false,
                    position: 'center',
                });
                await fetchApplications();
            }
        } catch (error) {
            if (error.response && error.response.status === 422) {
                backendErrors.value = error.response.data.errors;
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: error.response?.data?.message || 'Something went wrong.',
                });
            }
        } finally {
            saving.value = false;
        }
    };
    const viewApplicant = (applicant) => {
        selectedApplicant.value = applicant;
        showViewModal.value = true;
    };
    const editApplicant = (applicant) => {
        if (document.activeElement instanceof HTMLElement) {
            document.activeElement.blur();
        }
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
    const openModal = () => {
        if (document.activeElement instanceof HTMLElement) {
            document.activeElement.blur();
        }
        selectedApplicantForEdit.value = null;
        showModal.value = true;
        backendErrors.value = {};
    };

    const closeModal = () => {
        showModal.value = false;
        selectedApplicantForEdit.value = null;
        backendErrors.value = {};
    };

    const closeViewModal = () => {
        showViewModal.value = false;
        selectedApplicant.value = null;
    };
    // Fetch tehsils
    const tehsils = ref([]);

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

    // Clean up on unmount
    onBeforeUnmount(() => {
        selectedApplicant.value = null;
        selectedApplicantForEdit.value = null;
    });

    // Permission check
    const hasPermission = (permissionName) => {
        //  Super Admin bypass
        if (store.user?.role_name === 'Super Admin') {
            return true;
        }

        //Regular check
        const userPermissions = store.user?.permissions || [];
        return userPermissions.some((p) => {
            const pName = typeof p === 'string' ? p : p.name;
            return pName.toLowerCase() === permissionName.toLowerCase();
        });
    };
    watch(
        () => route.query.filter,
        () => {
            fetchApplications(1);
        },
    );

    const handleZipUpload = async (event) => {
        const file = event.target.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('zip_file', file);

        loading.value = true;
        try {
            const res = await axios.post('/api/archive-applicants', formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });

            if (res.data.success) {
                Swal.fire('Success!', res.data.message, 'success');
                await fetchApplications(); // Table refresh
            }
        } catch (error) {
            console.error('Upload Error:', error);
            Swal.fire('Error', error.response?.data?.message || 'Zip upload failed', 'error');
        } finally {
            loading.value = false; // Loader OFF
            event.target.value = ''; // Reset input
        }
    };
</script>

<style scoped>
    @import '@/assets/css/urdu-font.css';

    .fa-spinner {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    .transition {
        transition: all 0.2s ease;
    }
    td {
    white-space: nowrap;    
    vertical-align: middle;
}

</style>
