<template>
    <div class="p-6 min-h-screen">

        <!-- Breadcrumb + Button -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center text-sm text-gray-600 space-x-2">
                <i class="fa fa-dashboard"></i>

                <router-link to="/" class="cursor-pointer">
                    Dashboard
                </router-link>
                <i class="fa fa-angle-right"></i>
                <span class="cursor-pointer">Department List</span>
            </div>
            <router-link to="/department/create"
                         class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
                <i class="fa fa-plus"></i>
                Add New
            </router-link>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-lg shadow">

            <!-- Header -->
            <div class="flex items-center gap-2 px-6 py-4 border-b rounded-t-lg">
                <!-- <i class="fa fa-cogs"></i> -->
                <h2 class="text-lg font-semibold">
                    Departments List
                </h2>
                <!-- search -->
                <div class="ltr:ml-auto rtl:mr-auto">
                    <input v-model="search" type="text" class="form-input" placeholder="Search..." />
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <div v-if="loading" class="flex justify-center items-center py-20">
          <span
              class="animate-spin border-8 border-[#f1f2f3] border-l-primary rounded-full w-14 h-14 inline-block"></span>
                </div>
                <table v-else class="min-w-full border-collapse">

                    <!-- Table Head -->
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">
                            Sr.#
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">
                            Department Name
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-semibold  text-gray-600">
                            Created Date
                        </th>
                        <th class="px-4 py-3 text-center text-sm font-semibold  text-gray-600">
                            Action
                        </th>
                    </tr>
                    </thead>

                    <!-- Table Body -->
                    <tbody class="divide-y">
                    <tr v-for="(dept, index) in departments" :key="dept.id" class="hover:bg-gray-50">
                        <td class="px-4 py-1 font-small">
                            {{ (currentPage - 1) * perPage + index + 1 }}
                        </td>

                        <td class="px-4 py-1 font-small">
                            {{ dept.name }}
                        </td>

                        <td class="px-4 py-1 font-small text-gray-600">
                            {{ dept.created_at }}
                        </td>

                        <td class="px-4 py-1 text-center space-x-2">
                            <router-link :to="`/department/edit/${dept.id}`"
                                         class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs">
                                <i class="fa fa-pencil"></i>
                            </router-link>

                            <button @click="deleteDepartments(dept.id)"
                                    class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <tr v-if="isOffline">
                        <td colspan="4" class="px-4 py-4 text-center text-sm font-semibold text-gray-600">
                            <span>It looks like you are offline</span>
                        </td>
                    </tr>
                    </tbody>

                </table>
                <ul class="inline-flex items-center space-x-1 rtl:space-x-reverse m-auto justify-end w-full mt-5 mb-3">

                    <!-- Prev -->
                    <button v-if="lastPage > 1" @click="fetchDepartments(currentPage - 1)" :disabled="currentPage === 1"
                            class="px-3.5 py-1 rounded font-semibold bg-gray-200 hover:bg-blue-500 hover:text-white">
                        Prev
                    </button>

                    <!-- Pages -->
                    <li v-for="page in lastPage" :key="page" v-if="lastPage > 1">
                        <button @click="fetchDepartments(page)" :class="[
              'px-3 py-1 rounded font-semibold transition',
              page === currentPage
                ? 'bg-blue-600 text-white'
                : 'bg-gray-200 text-gray-700 hover:bg-blue-500 hover:text-white'
            ]">
                            {{ page }}
                        </button>
                    </li>

                    <!-- Next -->
                    <button v-if="lastPage > 1" @click="fetchDepartments(currentPage + 1)"
                            :disabled="currentPage === lastPage"
                            class="px-3.5 py-1 rounded font-semibold bg-gray-200 hover:bg-blue-500 hover:text-white">
                        Next
                    </button>

                </ul>
            </div>

        </div>
    </div>
</template>

<script setup>
    import { onMounted, ref, watch } from 'vue';
    import axios from 'axios';
    import { useRoute } from 'vue-router';
    // Delete Center
    import Swal from 'sweetalert2';

    const loading = ref(false);
    const search = ref('');
    const departments = ref([]);
    const currentPage = ref(1);
    const lastPage = ref(1);
    const perPage = ref(15);
    const route = useRoute();
    const isOffline = ref(false);


    const fetchDepartments = async (page = 1) => {
        loading.value = true;
        isOffline.value = false;
        try {
            const response = await axios.get(`/api/departments?page=${page}&search=${search.value}`);
            departments.value = response.data.data.data;
            currentPage.value = response.data.data.current_page;
            lastPage.value = response.data.data.last_page;
            console.log('departments from api:', response.data.data);
        } catch (error) {
            console.error('Error fetching departments:', error);
            if (!error.response) {
                isOffline.value = true;
            }
        } finally {
            loading.value = false;
        }
    };

    const deleteDepartments = async (id) => {
        const result = await Swal.fire({
            title: 'Are you sure?',
            text: 'Delete this department record?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        });

        if (result.isConfirmed) {
            try {
                await axios.delete(`/api/departments/${id}`);
                departments.value = departments.value.filter(c => c.id !== id);
                Swal.fire('Deleted!', 'Record has been deleted.', 'success');
            } catch (error) {
                Swal.fire('Error!', 'Failed to delete record.', 'error');
            }
        }
    };

    watch(search, () => {
        fetchDepartments(1);
    });

    onMounted(() => {
        fetchDepartments();
    });
</script>
