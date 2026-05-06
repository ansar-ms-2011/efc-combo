<template>
    <div class="p-6 min-h-screen">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <div class="flex items-center text-sm text-gray-600 space-x-2">
                <i class="fa fa-dashboard"></i>
                <router-link to="/" class="cursor-pointer">Dashboard</router-link>
                <i class="fa fa-angle-right"></i>
                <span class="cursor-pointer">Backups</span>
            </div>

            <div class="flex items-center gap-2" v-if="canCreate">
                <select v-model="scope" class="form-select w-52">
                    <option value="full_site">Full Site Backup</option>
                    <option value="monthly" disabled>Monthly Backup</option>
                    <option value="yearly" disabled>Yearly Backup</option>
                    <option value="custom" disabled>Custom Range</option>
                </select>

                <div v-if="scope === 'custom'" class="w-64">
                    <VueDatePicker
                        v-model="dateRange"
                        range
                        placeholder="Select Date Range"
                        auto-apply
                        model-type="yyyy-MM-dd"
                        :formats="{ input: 'dd-MM-yyyy' }"
                        :enable-time-picker="false"
                    />
                </div>

                <button
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow disabled:opacity-70"
                    :disabled="creatingBackup"
                    @click="triggerBackup"
                >
                    {{ creatingBackup ? 'Processing...' : 'Create Backup' }}
                </button>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b rounded-t-lg">
                <h2 class="text-lg font-semibold">Backup History</h2>
            </div>

            <div class="overflow-x-auto">
                <div v-if="loading" class="flex justify-center items-center py-16">
                    <span
                        class="animate-spin border-8 border-[#f1f2f3] border-l-primary rounded-full w-12 h-12 inline-block">
                    </span>
                </div>

                <table v-else class="min-w-full border-collapse">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">ID</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Type</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Scope</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Progress</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Created At</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Action</th>
                    </tr>
                    </thead>

                    <tbody class="divide-y">
                    <tr v-for="backup in backups" :key="backup.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ backup.id }}</td>
                        <td class="px-4 py-3 capitalize">{{ backup.type.replace('_', ' ') }}</td>
                        <td class="px-4 py-3 capitalize">{{ backup.scope.replace('_', ' ') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 capitalize text-xs rounded font-semibold"
                                  :class="statusClass(backup.status)">
                              {{ backup.status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 w-72">
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div
                                    class="h-2.5 rounded-full transition-all duration-300"
                                    :class="backup.status === 'failed' ? 'bg-red-500' : 'bg-blue-600'"
                                    :style="{ width: `${backup.progress_percentage || 0}%` }"
                                ></div>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">{{ backup.progress_percentage || 0 }}%</div>
                        </td>
                        <td class="px-4 py-3">{{ formatDMY(backup.created_at, true) }}</td>
                        <td class="px-4 py-3 text-center">
                            <button
                                v-if="backup.status === 'completed'"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs disabled:opacity-70 disabled:cursor-not-allowed"
                                @click="handleDownload(backup.id)"
                                :disabled="isDownloading"
                            >
                                Download
                            </button>
                            <span v-else class="text-xs text-gray-400">N/A</span>
                        </td>
                    </tr>

                    <tr v-if="!backups.length">
                        <td colspan="7" class="text-center py-6 text-gray-500">No backups found</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { computed, onMounted, onUnmounted, ref } from 'vue';
    import '@vuepic/vue-datepicker/dist/main.css';
    import Swal from 'sweetalert2';
    import { useAppStore } from '@/stores';
    import { createBackup, downloadBackup, getBackups } from '@/services/backupService';
    import { formatDMY } from '@/mixin/index.ts';

    const store = useAppStore();

    const backups = ref([]);
    const loading = ref(false);
    const creatingBackup = ref(false);
    const scope = ref('full_site');
    const dateRange = ref(null);
    const pollTimer = ref(null);
    const isDownloading = ref(false);

    const canCreate = computed(() => store.user?.permissions?.includes('backups.create'));

    const hasActiveBackup = computed(() => backups.value.some(item => ['pending', 'processing'].includes(item.status)));

    const fetchBackups = async (silently = false) => {
        if (!silently) {
            loading.value = true;
        }

        try {
            const res = await getBackups(1);
            backups.value = res.data?.data || [];
        } catch (error) {
            console.error('Failed to fetch backups', error);
        } finally {
            if (!silently) {
                loading.value = false;
            }
        }
    };

    const triggerBackup = async () => {
        if (scope.value === 'custom' && !dateRange.value) {
            Swal.fire('Error', 'Please select a date range for custom backup.', 'error');
            return;
        }

        creatingBackup.value = true;

        try {
            const params = {};
            if (scope.value === 'custom' && dateRange.value) {
                params.start_date = dateRange.value[0];
                params.end_date = dateRange.value[1];
            }

            await createBackup(scope.value, params);
            await fetchBackups(true);
            startPolling();

            Swal.fire({
                icon: 'success',
                text: 'Success : Backup job has been dispatched successfully.',
                timer: 1800,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } catch (error) {
            console.error(error);
            Swal.fire('Error', error?.response?.data?.message || 'Failed to queue backup.', 'error');
        } finally {
            creatingBackup.value = false;
        }
    };

    const handleDownload = async (backupId) => {
        try {
            isDownloading.value = true;
            await downloadBackup(backupId);
        } catch (error) {
            Swal.fire('Error', error?.response?.data?.message || 'Failed to download backup.', 'error');
        } finally {
            isDownloading.value = false;
        }
    };

    const startPolling = () => {
        stopPolling();
        pollTimer.value = setInterval(async () => {
            await fetchBackups(true);

            if (!hasActiveBackup.value) {
                stopPolling();
            }
        }, 5000);
    };

    const stopPolling = () => {
        if (pollTimer.value) {
            clearInterval(pollTimer.value);
            pollTimer.value = null;
        }
    };

    const statusClass = (status) => {
        if (status === 'completed') return 'bg-green-100 text-green-700';
        if (status === 'failed') return 'bg-red-100 text-red-700';
        if (status === 'processing') return 'bg-blue-100 text-blue-700';
        return 'bg-yellow-100 text-yellow-700';
    };

    const formatDate = (value) => {
        if (!value) return '-';

        return new Date(value).toLocaleString();
    };

    onMounted(async () => {
        await fetchBackups();

        if (hasActiveBackup.value) {
            startPolling();
        }
    });

    onUnmounted(() => {
        stopPolling();
    });
</script>
