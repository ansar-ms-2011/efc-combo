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
                <span class="cursor-pointer">Center List</span>
            </div>

            <router-link to="/center/create"
                         class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
                <i class="fa fa-plus"></i> Add New
            </router-link>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-lg shadow">
            <!-- Header -->
            <div class="flex items-center gap-2 px-6 py-4 border-b rounded-t-lg">
                <!-- <i class="fa fa-cogs"></i> -->
                <h2 class="text-lg font-semibold">Center List</h2>

                <!-- search -->
                <div class="ltr:ml-auto rtl:mr-auto">
                    <input v-model="search1" type="text" class="form-input" placeholder="Search..." />
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <div v-if="loading" class="flex justify-center items-center py-20">
          <span
              class="animate-spin border-8 border-[#f1f2f3] border-l-primary rounded-full w-14 h-14 inline-block"></span>
                </div>
                <table v-else class="min-w-full border-collapse  table-auto">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Sr.#</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Center Name</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Working Hour</th>
                        <!-- <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Working Days</th> -->
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">District</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Tehsil</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Break Timing</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Juma Break</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Contact</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Coordinates (Lat/Long)</th>
                        <!-- <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Created Date</th> -->
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Action</th>
                    </tr>
                    </thead>

                    <tbody class="divide-y">
                    <tr v-for="(center, index) in centers" :key="center.id" class="hover:bg-gray-50">
                        <td class="px-4 py-1 font-small">{{ (currentPage - 1) * perPage + index + 1 }}</td>

                        <td class="px-4 py-1 font-small">
                            {{ center.name }}
                        </td>

                        <td class="px-4 py-1 font-small">{{ center.timing }}</td>

                        <!-- <td class="px-4 py-1 font-small">{{center.working_days.map(day => day.type?.name).join(',')}} </td> -->
                        <td class="px-4 py-1 font-small">{{ center.district?.name || 'N/A' }}</td>
                        <td class="px-4 py-1 font-small">{{ center.tehsil?.name || 'N/A' }}</td>
                        <td class="px-4 py-1 font-small">{{ center.lunch_break }}</td>
                        <td class="px-4 py-1 font-small">{{ center.jumma_break }}</td>
                        <td class="px-4 py-1 font-small">{{ center.contact_number || 'N/A' }}</td>
                        <td class="px-4 py-1 font-small">
                            <span v-if="center.latitude" class="text-xs block">Lat: {{ center.latitude }}</span>
                            <span v-if="center.longitude" class="text-xs block">Long: {{ center.longitude }}</span>
                            <span v-if="!center.latitude && !center.longitude">N/A</span>
                        </td>

                        <!-- <td class="px-4 py-1 font-small text-gray-600">
                          {{ $formatDMY(center.created_at) }}
                        </td> -->

                        <td class="px-4 py-1 font-small text-center space-x-2 flex">
                            <router-link :to="`/center/edit/${center.id}`"
                                         class="bg-green-500 hover:bg-green-600  text-white px-2 py-1 rounded text-xs">
                                <i class="fa fa-pencil"></i>
                            </router-link>
                            <!-- <router-link :to="`/centers`" @click.prevent="deleteType(type.id)"
                              class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs">
                              <i class="fa fa-trash"></i>
                            </router-link> -->

                            <button @click="deleteCenter(center.id)"
                                    class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <ul class="inline-flex items-center space-x-1 rtl:space-x-reverse m-auto justify-end w-full mt-5 mb-3">

                    <!-- Prev -->
                    <button v-if="lastPage > 1" @click="fetchCenters(currentPage - 1)" :disabled="currentPage === 1"
                            class="px-3.5 py-1 rounded font-semibold bg-gray-200 hover:bg-blue-500 hover:text-white">
                        Prev
                    </button>

                    <!-- Pages -->
                    <li v-for="page in lastPage" :key="page" v-if="lastPage > 1">
                        <button @click="fetchCenters(page)" :class="[
              'px-3 py-1 rounded font-semibold transition',
              page === currentPage
                ? 'bg-blue-600 text-white'
                : 'bg-gray-200 text-gray-700 hover:bg-blue-500 hover:text-white'
            ]">
                            {{ page }}
                        </button>
                    </li>

                    <!-- Next -->
                    <button v-if="lastPage > 1" @click="fetchCenters(currentPage + 1)"
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
    import { onMounted, ref } from 'vue';
    import { fetchList } from '@/services/listService';
    import { confirmAndDelete } from '@/services/deleteService';

    // State
    const loading = ref(false);
    const centers = ref([]);
    const search1 = ref('');
    const currentPage = ref(1);
    const lastPage = ref(1);
    const perPage = ref(15);

    // Fetch Centers
    const fetchCenters = async (page = 1) => {
        loading.value = true;
        try {
            const result = await fetchList('centers', page);
            centers.value = result.items;
            currentPage.value = result.currentPage;
            lastPage.value = result.lastPage;
            perPage.value = result.perPage;
        } catch (error) {
            console.error('Error fetching centers:', error);
        } finally {
            loading.value = false;
        }
    };

    // Delete Center
    const deleteCenter = async (id) => {
        const deleted = await confirmAndDelete('centers', id);
        if (deleted) {
            centers.value = centers.value.filter(c => c.id !== id);
        }
    };


    onMounted(() => {
        fetchCenters();
    });
</script>
