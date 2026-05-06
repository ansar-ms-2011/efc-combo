<template>
    <div class="p-6 min-h-screen">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <div class="flex items-center text-sm text-gray-600 space-x-2">
                <i class="fa fa-dashboard"></i>
                <router-link to="/" class="cursor-pointer">Dashboard</router-link>
                <i class="fa fa-angle-right"></i>
                <span class="cursor-pointer">Scheduled-Jobs</span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b rounded-t-lg">
                <h2 class="text-lg font-semibold">Scheduled Jobs</h2>
            </div>

            <div class="overflow-x-auto">
                <div v-if="loading" class="flex justify-center items-center py-16">
                    <i class="fa fa-spinner fa-spin fa-2xl"></i>
                </div>

                <table v-else class="min-w-full border-collapse">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Tracking Token No</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Applicant Full Name</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Identity Number</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Job Status</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Scheduled At</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Duration</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Failed Message</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Action</th>
                    </tr>
                    </thead>

                    <tbody class="divide-y">
                    <tr v-for="job in jobs" :key="job.token" class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ job.tracking_token_no }}</td>
                        <td class="px-4 py-3">{{ job.applicant_name }}</td>
                        <td class="px-4 py-3">{{ job.identity_number }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-bold" :class="{
                              'bg-yellow-100 text-yellow-700': job.status === 'pending',
                              'bg-blue-100 text-blue-700': job.status === 'processing',
                              'bg-green-100 text-green-700': job.status === 'completed',
                              'bg-red-100 text-red-700': job.status === 'failed',
                              'bg-gray-100 text-gray-700': job.status === 're-initiated',
                            }">
                              {{ job.status }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            {{ formatDMY(job.created_at, true) }}
                        </td>
                        <td class="px-4 py-3">{{ job.duration || '-' }}</td>
                        <td class="text-center">
                            <button v-if="job.message" class="px-3 py-1 bg-blue-500 text-white rounded"
                                    @click="openMessageModal(job.message)">
                                View
                            </button>
                        </td>
                        <td class="text-center">
                            <button class="px-3 py-1 bg-red-500 disabled:bg-red-500/50 text-white rounded"
                                    @click="handleReinitiate(job)"
                                    :disabled="job.status === 're-initiated' || job.status === 'completed' || job.status === 'pending'">
                                Re-initiate
                            </button>
                        </td>
                    </tr>

                    <tr v-if="!loading && jobs?.length === 0">
                        <td colspan="6" class="text-center py-6 text-gray-500">
                            No details found
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <Pagination
                :current-page="currentPage"
                :total-pages="lastPage"
                @page-changed="fetchJobs"
            />
        </div>
        <div v-if="showModal"
             class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
            <div class="bg-white rounded-lg w-1/2 p-6 relative">
                <h3 class="text-lg font-semibold mb-4">Message</h3>
                <p>{{ modalMessage }}</p>
                <button class="absolute top-3 right-3 text-gray-500 hover:text-gray-800"
                        @click="showModal = false">
                    &times;
                </button>
            </div>
        </div>
        <BaseDialog
            v-model="showConfirmation"
            :title="options?.title || 'Confirm'"
            max-width="max-w-sm"
        >
            <div class="text-center py-4">
                <p class="text-gray-700">{{ options?.message }}</p>
            </div>

            <template #footer>
                <div class="flex gap-3 justify-end">
                    <button
                        class="px-4 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-70"
                        @click="handleConfirm" :disabled="isProcessing"
                        :class="options?.confirmButtonClass"
                    >
                        <span v-if="!isProcessing">{{ options?.confirmText || 'Confirm' }}</span>
                        <span v-else>
                                    <i class="fa fa-spinner fa-spin fa-lg"></i>
                                </span>
                    </button>
                    <button
                        @click="handleCancel"
                        class="px-4 py-1 border border-gray-300 rounded-md hover:bg-gray-50"
                    >
                        {{ options?.cancelText || 'Cancel' }}
                    </button>
                </div>
            </template>
        </BaseDialog>
    </div>
</template>


<script setup>
    import { useConfirmation } from '@/composables/useConfirmation';
    import { onMounted, ref } from 'vue';
    import { formatDMY } from '@/mixin/index.ts';
    import apiClient from '@/services/axios.ts';

    const centers = ref([]);
    const currentPage = ref(1);
    const lastPage = ref(1);
    const isProcessing = ref(false);
    const {
        confirm,
        showConfirmation,
        options,
        handleConfirm,
        handleCancel
    } = useConfirmation();

    // state
    const jobs = ref([]);
    const loading = ref(false);

    const showModal = ref(false);
    const modalMessage = ref('');

    const openMessageModal = (message) => {
        modalMessage.value = message;
        showModal.value = true;
    };


    // API call
    const fetchJobs = async (page = 1) => {
        try {
            loading.value = true;

            const response = await apiClient.get('/api/certificate-jobs?page=' + page);
            console.log('Fetched jobs:', response.data);
            jobs.value = response.data.data;
            currentPage.value = response.data.current_page;
            lastPage.value = response.data.last_page;
        } catch (error) {
            console.error('Error fetching jobs:', error);
        } finally {
            loading.value = false;
        }
    };

    // lifecycle
    onMounted(() => {
        fetchJobs();
    });

    const handleReinitiate = async (job) => {
        confirm({
            title: 'Confirmation',
            message: 'Do you really want to re-initiate this job?',
            confirmText: 'Initiate',
            cancelText: 'Cancel',
            confirmButtonClass: 'px-4 py-1 bg-green-600 text-white rounded-md hover:bg-green-700',
            onConfirm: async () => {
                isProcessing.value = true;
                const res = await apiClient.post(`/api/certificate-jobs/${job.id}/re-initiate`);
                console.log(res);
                isProcessing.value = false;
                await fetchJobs(1);
            },
            onCancel: () => {
                console.log('Action cancelled by user.');
                isProcessing.value = false;
            }
        });
    };
</script>
