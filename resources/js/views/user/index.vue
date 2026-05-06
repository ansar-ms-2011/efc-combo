<template>
    <div class="p-6 min-h-screen">
        <!-- Breadcrumb + Button -->
        <div class="flex items-center justify-between mb-6">
            <AppBreadcrumb :items="breadcrumbs" />

            <router-link to="/user/create"
                         class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
                <i class="fa fa-plus"></i> Add New
            </router-link>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-lg shadow">
            <!-- Header -->
            <div class="flex items-center gap-2 px-6 py-4 border-b rounded-t-lg">
                <h2 class="text-lg font-semibold">Users</h2>
                <!-- search -->
                <div class="ltr:ml-auto rtl:mr-auto">
                    <input v-model="search1" type="text" class="form-input" placeholder="Search..." />
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <div v-if="loading" class="flex justify-center items-center py-20">
                    <i class="fa fa-spinner fa-spin fa-2xl color-primary"></i>
                </div>
                <table v-else class="min-w-full border-collapse">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Sr.#</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">User Info</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">CNIC</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Region</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">District</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Tehsil</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Center</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Created On</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Action</th>
                    </tr>
                    </thead>

                    <tbody class="">
                    <tr v-for="(user, index) in users" :key="user.id"
                        class="odd:bg-gray-100 even:bg-white hover:bg-gray-100">
                        <td class="px-4 py-1 font-small">{{ index + 1 }}</td>

                        <!-- User Info -->
                        <td class="px-4 py-1 font-small">
                            <div>{{ user.name }}</div>
                            <div class=" text-gray-500">{{ user.email }}</div>
                        </td>
                        <td class="px-4 py-1 font-small">{{ user?.employee?.cnic || 'N/A' }}</td>

                        <td class="px-4 py-1 font-small">{{ user?.region?.name }}</td>
                        <td class="px-4 py-1 font-small">{{ user?.district?.name }}</td>
                        <td class="px-4 py-1 font-small">{{ user?.tehsil?.name }}</td>
                        <td class="px-4 py-1 font-small">{{ user?.center?.name }}</td>
                        <td class="px-4 py-1">
                            <Badge :variant="user?.is_active ? 'green' : 'red'">
                                {{ user?.is_active ? 'Active' : 'Inactive' }}
                            </Badge>
                        </td>

                        <td class="px-4 py-1 font-small text-gray-600">
                            {{ $formatDMY(user.created_at) }}
                        </td>

                        <!-- Actions -->
                        <td class="px-4 py-1">
                            <div class="flex items-center space-x-2">
                                <router-link :to="`/user/edit/${user.id}`"
                                             class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs">
                                    <i class="fa fa-pencil"></i>
                                </router-link>
                                <button @click="deleteUser(user.id)"
                                        class="bg-red-500 text-white px-2 py-1 rounded text-xs">
                                    <i class="fa fa-trash"></i>
                                </button>
                                <button @click="openTransferModal(user)"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs">
                                    <i class="fa fa-exchange"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <ul class="inline-flex items-center space-x-1 rtl:space-x-reverse m-auto justify-end w-full mt-5 mb-3">
                    <!-- Prev -->
                    <button v-if="lastPage > 1" @click="fetchUsers(currentPage - 1)" :disabled="currentPage === 1"
                            class="px-3.5 py-1 rounded font-semibold bg-gray-200 hover:bg-blue-500 hover:text-white">
                        Prev
                    </button>
                    <!-- Pages -->
                    <li v-for="page in lastPage" :key="page" v-if="lastPage > 1">
                        <button @click="fetchUsers(page)" :class="[
                            'px-3 py-1 rounded font-semibold transition',
                            page === currentPage
                                ? 'bg-blue-600 text-white'
                                : 'bg-gray-200 text-gray-700 hover:bg-blue-500 hover:text-white'
                        ]">
                            {{ page }}
                        </button>
                    </li>

                    <!-- Next -->
                    <button v-if="lastPage > 1" @click="fetchUsers(currentPage + 1)"
                            :disabled="currentPage === lastPage"
                            class="px-3.5 py-1 rounded font-semibold bg-gray-200 hover:bg-blue-500 hover:text-white">
                        Next
                    </button>

                </ul>
            </div>
        </div>
    </div>

    <!-- TRANSFER MODAL - HORIZONTAL LAYOUT -->
    <BaseDialog
        v-model="showTransferModal"
        title="User Transfer"
        :subtitle="`Transferring: ${selectedUser?.name || ''}`"
        title-class="text-gray-800"
        max-width="max-w-4xl"
    >
        <template #default>
            <form @submit.prevent="submitTransfer" class="space-y-6">
                <!-- Horizontal Layout - From and To side by side -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- ================= FROM SECTION ================= -->
                    <div class="bg-gray-50 p-5 rounded-lg border">
                        <h3 class="font-semibold text-gray-700 mb-4 flex items-center gap-2 border-b pb-2">
                            <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                            From Location
                        </h3>

                        <!-- Horizontal form fields within From section -->
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <label class="w-24 text-sm font-medium text-gray-700">Region:</label>
                                <div class="flex-1">
                                    <multiselect v-model="transferForm.from_region" :options="regions" label="name"
                                                 placeholder="Region" :show-labels="false"
                                                 :disabled="true">
                                    </multiselect>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <label class="w-24 text-sm font-medium text-gray-700">District:</label>
                                <div class="flex-1">
                                    <multiselect v-model="transferForm.from_district" :options="districts" label="name"
                                                 placeholder="District" :show-labels="false"
                                                 :disabled="true">
                                    </multiselect>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <label class="w-24 text-sm font-medium text-gray-700">Tehsil:</label>
                                <div class="flex-1">
                                    <multiselect v-model="transferForm.from_tehsil" :options="tehsils" label="name"
                                                 placeholder="Tehsil" :show-labels="false"
                                                 :disabled="true">
                                    </multiselect>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <label class="w-24 text-sm font-medium text-gray-700">Center:</label>
                                <div class="flex-1">
                                    <multiselect v-model="transferForm.from_center" :options="centers" label="name"
                                                 placeholder="Center" :show-labels="false"
                                                 :disabled="true">
                                    </multiselect>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <label class="w-24 text-sm font-medium text-gray-700 pt-2">Services:</label>
                                <div class="flex-1">
                                    <multiselect v-model="transferForm.from_services" :options="filteredFromServices"
                                                 track-by="id" label="name" placeholder="Services"
                                                 :show-labels="false" :disabled="true" :multiple="true">
                                    </multiselect>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ================= TO SECTION ================= -->
                    <div class="bg-gray-50 p-5 rounded-lg border">
                        <h3 class="font-semibold text-gray-700 mb-4 flex items-center gap-2 border-b pb-2">
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                            To Location
                        </h3>

                        <!-- Horizontal form fields within To section -->
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <label class="w-24 text-sm font-medium text-gray-700">Region:</label>
                                <div class="flex-1">
                                    <multiselect v-model="transferForm.to_region" :options="regions" label="name"
                                                 placeholder="Select Region"
                                                 :show-labels="false">
                                    </multiselect>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <label class="w-24 text-sm font-medium text-gray-700">District:</label>
                                <div class="flex-1">
                                    <multiselect v-model="transferForm.to_district" :options="filteredToDistricts"
                                                 label="name" placeholder="Select District"
                                                 :show-labels="false">
                                    </multiselect>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <label class="w-24 text-sm font-medium text-gray-700">Tehsil:</label>
                                <div class="flex-1">
                                    <multiselect v-model="transferForm.to_tehsil" :options="filteredToTehsils" label="name"
                                                 placeholder="Select Tehsil"
                                                 :show-labels="false">
                                    </multiselect>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <label class="w-24 text-sm font-medium text-gray-700">Center:</label>
                                <div class="flex-1">
                                    <multiselect v-model="transferForm.to_center" :options="filteredToCenters" label="name"
                                                 placeholder="Select Center"
                                                 :show-labels="false">
                                    </multiselect>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <label class="w-24 text-sm font-medium text-gray-700 pt-2">Services:</label>
                                <div class="flex-1">
                                    <multiselect v-model="transferForm.to_services" :options="filteredToServices"
                                                 track-by="id" :multiple="true" label="name" placeholder="Select Services"
                                                 :show-labels="false">
                                    </multiselect>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FILE UPLOAD - Horizontal layout -->
                <div class="border-t pt-4">
                    <div class="flex items-center gap-4">
                        <label class="w-32 text-sm font-medium text-gray-700">
                            Posting Letter:
                        </label>
                        <div class="flex-1">
                            <input type="file" @change="handleFile" class="w-full border rounded-lg p-2 bg-white" />
                            <p class="text-xs text-gray-400 mt-1">
                                Allowed formats: PDF, JPG, PNG (Max 2MB)
                            </p>
                        </div>
                    </div>
                </div>
            </form>
        </template>

        <template #footer>
            <div class="flex justify-end gap-3">
                <button type="button" @click="closeTransferModal"
                        class="px-5 py-2 rounded-lg bg-gray-200 hover:bg-gray-300">
                    Cancel
                </button>
                <button type="submit" @click="submitTransfer" :disabled="transferLoading"
                        class="px-6 py-2 rounded-lg text-white shadow"
                        :class="transferLoading
                            ? 'bg-green-400 cursor-not-allowed'
                            : 'bg-green-600 hover:bg-green-700'">
                    <span v-if="transferLoading">
                        <i class="fa fa-spinner fa-spin mr-2"></i> Processing...
                    </span>
                    <span v-else>
                        Confirm Transfer
                    </span>
                </button>
            </div>
        </template>
    </BaseDialog>
</template>

<script setup>
    import { computed, onMounted, ref, watch } from 'vue';
    import axios from 'axios';
    import Swal from 'sweetalert2';
    import AppBreadcrumb from '@/components/layout/AppBreadCrumb.vue';
    import Badge from '@/components/Badge.vue';
    import BaseDialog from '@/components/BaseDialog.vue';
    import { useAppStore } from '@/stores/index.ts';

    const breadcrumbs = [
        { label: 'Dashboard', to: '/admin/dashboard' },
        { label: 'User List' }
    ];
    const loading = ref(false);
    const users = ref([]);
    const search1 = ref('');
    const currentPage = ref(1);
    const lastPage = ref(1);
    const perPage = ref(15);

    const transferLoading = ref(false);
    const selectedUser = ref(null);

    // Fetch users
    const fetchUsers = async () => {
        loading.value = true;

        try {
            const response = await axios.get('/api/users', {
                params: {
                    search: search1.value
                }
            });

            if (response.data.success) {
                users.value = response.data.data;
            }
            console.log(response.data);
            currentPage.value = response.data.current_page || 1;
            lastPage.value = response.data.last_page || 1;
            perPage.value = response.data.per_page || 15;
        } catch (error) {
            console.error('Error fetching users:', error);
        } finally {
            loading.value = false;
        }
    };

    const deleteUser = async (id) => {
        const confirm = await Swal.fire({
            icon: 'warning',
            title: 'Are you sure?',
            showCancelButton: true,
            confirmButtonText: 'Delete'
        });

        if (!confirm.isConfirmed) return;

        try {
            await axios.delete(`/api/users/${id}`);
            await fetchUsers();
            Swal.fire('Deleted!', 'Record deleted successfully.', 'success');
        } catch (error) {
            console.error('Delete failed:', error);
            Swal.fire('Error', 'Failed to delete user', 'error');
        }
    };

    watch(search1, () => {
        fetchUsers();
    });

    onMounted(() => {
        fetchUsers();
    });

    const store = useAppStore();
    const showTransferModal = ref(false);
    const transferRole = ref(null);
    const transferForm = ref({
        user_id: null,

        // FROM
        from_region: null,
        from_district: null,
        from_tehsil: null,
        from_center: null,

        // TO
        to_region: null,
        to_district: null,
        to_tehsil: null,
        to_center: null,

        from_services: [],
        to_services: [],

        file: null
    });

    const regions = computed(() => store.groupedData.regions || []);
    const districts = computed(() =>
        store.groupedData.regions.flatMap(r => r.districts || [])
    );
    const tehsils = computed(() =>
        store.groupedData.regions.flatMap(r =>
            (r.districts || []).flatMap(d => d.tehsils || [])
        )
    );
    const centers = computed(() => store.centers || []);

    const userServiceCenters = computed(() => {
        return store.user?.service_centers || [];
    });

    const filteredToDistricts = computed(() => {
        if (!transferForm.value.to_region?.id) return [];
        return districts.value.filter(d =>
            d.parent_id === transferForm.value.to_region.id
        );
    });

    const filteredToTehsils = computed(() => {
        if (!transferForm.value.to_district?.id) return [];
        return tehsils.value.filter(t =>
            t.parent_id === transferForm.value.to_district.id
        );
    });

    const filteredToCenters = computed(() => {
        if (!transferForm.value.to_tehsil?.id) return [];
        return centers.value.filter(c =>
            c.tehsil_id === transferForm.value.to_tehsil.id
        );
    });

    const filteredFromServices = computed(() => {
        return transferForm.value.from_center?.services || [];
    });

    const filteredToServices = computed(() => {
        return transferForm.value.to_center?.services || [];
    });

    const openTransferModal = (user) => {
        selectedUser.value = user;
        showTransferModal.value = true;
        transferRole.value = user.current_role;

        transferForm.value = {
            user_id: user.id,

            // FROM (REAL USER DATA)
            from_region: user.region,
            from_district: user.district,
            from_tehsil: user.tehsil,
            from_center: user.center,

            // TO EMPTY INITIALLY
            to_region: null,
            to_district: null,
            to_tehsil: null,
            to_center: null,

            from_services: user.service_centers
                ? user.service_centers.map(sc => sc.service).filter(Boolean)
                : [],

            to_services: [],

            file: null
        };
    };

    const closeTransferModal = () => {
        showTransferModal.value = false;
        selectedUser.value = null;
        transferForm.value.file = null;
    };

    const handleFile = (e) => {
        transferForm.value.file = e.target.files[0];
    };

    const submitTransfer = async () => {
        if (transferLoading.value) return;

        if (
            !transferForm.value.to_region ||
            !transferForm.value.to_district
        ) {
            Swal.fire('Error', 'Please fill all required fields', 'warning');
            return;
        }

        transferLoading.value = true;
        try {
            const formData = new FormData();

            formData.append('user_id', transferForm.value.user_id);

            // FROM
            formData.append('from_region_id', transferForm.value.from_region?.id || '');
            formData.append('from_district_id', transferForm.value.from_district?.id || '');
            formData.append('from_tehsil_id', transferForm.value.from_tehsil?.id || '');
            formData.append('from_center_id', transferForm.value.from_center?.id || '');

            // TO
            formData.append('to_region_id', transferForm.value.to_region?.id || '');
            formData.append('to_district_id', transferForm.value.to_district?.id || '');
            formData.append('to_tehsil_id', transferForm.value.to_tehsil?.id || '');
            formData.append('to_center_id', transferForm.value.to_center?.id || '');

            if (transferForm.value.to_services.length > 0) {
                transferForm.value.to_services.forEach(service => {
                    formData.append('service_ids[]', service.id);
                });
            }

            if (transferForm.value.file) {
                formData.append('posting_letter', transferForm.value.file);
            }

            await axios.post('/api/user-transfer', formData);

            Swal.fire('Success', 'Transfer completed', 'success');
            closeTransferModal();
            fetchUsers(); // Refresh the user list
        } catch (err) {
            console.log(err);
            Swal.fire('Error', 'Transfer failed', 'error');
        } finally {
            transferLoading.value = false;
        }
    };

    onMounted(async () => {
        await store.loadDropdowns();
        await fetchUsers();
    });

    watch(() => transferForm.value.to_center, (newVal) => {
        if (newVal) {
            transferForm.value.to_services = newVal.services || [];
        } else {
            transferForm.value.to_services = [];
        }
    });
</script>
