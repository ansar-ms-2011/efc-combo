<template>
    <div class="p-6 min-h-screen">
        <!-- Breadcrumb + Button -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center text-sm text-gray-600 space-x-2">
                <i class="fa fa-dashboard"></i>
                <router-link to="/" class="cursor-pointer">Dashboard</router-link>
                <i class="fa fa-angle-right"></i>
                <span class="cursor-pointer">Document List</span>
            </div>

            <button @click="openModal"
                    class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
                <i class="fa fa-plus"></i>
                Add New
            </button>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-lg shadow">
            <div class="flex items-center gap-2 px-6 py-4 border-b rounded-t-lg">
                <h2 class="text-lg font-semibold">Document List</h2>
                <div class="ltr:ml-auto rtl:mr-auto">
                    <input v-model="search1" type="text" class="form-input" placeholder="Search..." />
                </div>
            </div>

            <div class="overflow-x-auto">
                <div v-if="loading" class="flex justify-center items-center py-20">
          <span
              class="animate-spin border-8 border-[#f1f2f3] border-l-primary rounded-full w-14 h-14 inline-block"></span>
                </div>

                <table v-else class="min-w-full border-collapse">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 w-[60px]">Sr.#</th>
                        <th class="px-4 py-3 w-[120px]">Name</th>
                        <th class="px-4 py-3 w-[120px]">Urdu name</th>
                        <th class="px-4 py-3 w-[120px]">Certificate Name</th>
                        <th class="px-4 py-3 w-[120px]">Category</th>
                        <th class="px-4 py-3 w-[120px]">Duplicate Type</th>
                         <th class="px-4 py-3  w-[120px]">File Type</th>
                        <th class="px-4 py-3 w-[120px]">Required Copy</th>
                        <th class="px-4 py-3 w-[100px]">Status</th>
                        <th class="px-4 py-3 w-[120px] text-center">Action</th>
                    </tr>
                    </thead>

                    <tbody class="divide-y">
                    <tr v-for="(service, index) in services" :key="service.id" class="hover:bg-gray-50">
                        <td class="px-4 py-2 w-[60px]">{{ (currentPage - 1) * perPage + index + 1 }}</td>
                        <td class="px-4 py-2 w-[120px] font-medium">{{ service.name }}</td>
                        <td class="px-4 py-2 w-[120px] font-medium font-nastaleeq">{{ service.urdu_name }}</td>
                        <td class="px-4 py-2 w-[120px] text-gray-600">{{ service.service_name }}</td>
                        <td class="px-4 py-2 w-[120px] text-gray-600">{{ service.service_type }}</td>
                        <td class="px-4 py-2 w-[120px] text-gray-600">{{ service.reason_type?.name }}</td>
                         <td class="px-4 py-2 w-[120px] text-gray-600">{{ service.file_type }}</td> 

                        <td class="px-4 py-2 w-[120px] text-gray-600">{{ service.required_copy }}</td>
                        <td class="px-4 py-2 w-[100px]">
                <span :class="service.active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                      class="px-2 py-1 rounded text-xs font-semibold">
                  {{ service.active ? 'Active' : 'Inactive' }}
                </span>
                        </td>
                        <td class="px-4 py-2 w-[120px] text-center space-x-2">
                            <button @click="editService(service)"
                                    class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs">
                                <i class="fa fa-pencil"></i>
                            </button>
                            <button @click="deleteService(service.id)"
                                    class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <!-- Pagination -->
                <ul class="inline-flex items-center space-x-1 rtl:space-x-reverse m-auto justify-end w-[95%] mt-5 mb-3">
                    <button v-if="lastPage > 1" @click="fetchServices(currentPage - 1)" :disabled="currentPage === 1"
                            class="px-3.5 py-1 rounded font-semibold bg-gray-200 hover:bg-blue-500 hover:text-white">
                        Prev
                    </button>

                    <li v-for="page in lastPage" :key="page" v-if="lastPage > 1">
                        <button @click="fetchServices(page)" :class="[
              'px-3 py-1 rounded font-semibold transition',
              page === currentPage ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-blue-500 hover:text-white'
            ]">{{ page }}
                        </button>
                    </li>

                    <button v-if="lastPage > 1" @click="fetchServices(currentPage + 1)"
                            :disabled="currentPage === lastPage"
                            class="px-3.5 py-1 rounded font-semibold bg-gray-200 hover:bg-blue-500 hover:text-white">
                        Next
                    </button>
                </ul>
            </div>
        </div>

        <!-- Modal Component -->
        <RequiredDocumentModal v-model:show="showModal" :service="selectedService" @saved="refreshList" />
    </div>
</template>

<script setup>
    import { onMounted, ref, watch } from 'vue';
    import axios from 'axios';
    import Swal from 'sweetalert2';
    import RequiredDocumentModal from '@/views/requiredDocuments/RequiredDocumentModal.vue';

    const loading = ref(false);
    const search1 = ref('');
    const services = ref([]);
    const currentPage = ref(1);
    const lastPage = ref(1);
    const perPage = ref(15);

    const fetchServices = async (page = 1) => {
        loading.value = true;
        try {
            const res = await axios.get(`/api/required-documents`, {
                params: { page, search: search1.value }
            });
            services.value = res.data.data.data || [];
            currentPage.value = res.data.data.current_page || 1;
            lastPage.value = res.data.data.last_page || 1;
            perPage.value = res.data.data.per_page || 15;
        } catch (err) {
            console.error('Failed to fetch services:', err);
        } finally {
            loading.value = false;
        }
    };

    watch(search1, () => fetchServices(1));
    onMounted(() => fetchServices());

    const showModal = ref(false);
    const selectedService = ref(null);

    const openModal = () => {
        selectedService.value = null;
        showModal.value = true;
    };
    const editService = (service) => {
        selectedService.value = service;
        showModal.value = true;
    };
    const refreshList = () => fetchServices();

    const deleteService = async (id) => {
        const confirm = await Swal.fire({
            icon: 'warning',
            title: 'Are you sure?',
            text: 'You won\'t be able to revert this!',
            showCancelButton: true,
            confirmButtonText: 'Delete'
        });
        if (!confirm.isConfirmed) return;
        try {
            await axios.delete(`/api/required-documents/${id}`);
            services.value = services.value.filter(s => s.id !== id);
            Swal.fire('Deleted!', 'Record deleted successfully.', 'success');
        } catch (error) {
            Swal.fire('Error', 'Delete failed.', 'error');
        }
    };
</script>
