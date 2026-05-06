<template>
    <div class="px-3">
        <div class="flex flex-row justify-between rounded-lg bg-white px-4 py-3 text-sm font-semibold text-primary shadow-md border border-gray-200">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Archive Documents Analytics</h1>
                <p class="text-sm text-gray-500">Legacy Data Migration Tracking</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <!-- From Date -->
                <div>
                    <label class="font-sm mb-1 text-gray-500"> From Date To Date </label>
                    <VueDatePicker
                        v-model="filters.date_range"
                        range
                        placeholder="Select Date"
                        class="urdu-datepicker"
                        auto-apply
                        model-type="yyyy-MM-dd"
                        :formats="{ input: 'dd-MM-yyyy' }"
                        :enable-time-picker="false"
                        @update:model-value="fetchDashboardData"
                    />
                </div>

                <div class="flex flex-col">
                    <label class="text-[10px] font-bold text-gray-400 uppercase ml-1">Filter by Scanner</label>
                    <select
                        v-model="selectedScanner"
                        @change="fetchDashboardData"
                        class="h-10 min-w-[220px] rounded-md border border-gray-300 shadow-sm focus:ring-primary focus:border-primary text-sm"
                    >
                        <option value="">All Scanners</option>
                        <option v-for="user in scannerUsers" :key="user.id" :value="user.id">{{ user.first_name }} {{ user.last_name || '' }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="pt-5 p-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            <Card title="Total Documents" :value="dashboardCounts.total" color="#3b82f6" icon="fa-copy" :link="getLink('/archived/scanning-form/all')" />
            <Card
                title="Scanned Today"
                :value="dashboardCounts.today"
                color="#10b981"
                icon="fa-calendar-day"
                :link="getLink('/archived/scanning-form/all?filter=today')"
            />
            <Card
                title="Scanned This Week"
                :value="dashboardCounts.week"
                color="#6366f1"
                icon="fa-calendar-week"
                :link="getLink('/archived/scanning-form/all?filter=week')"
            />
            <Card
                title="Total Images Scanned"
                :value="dashboardCounts.total_scanned"
                color="#d946ef"
                icon="fa-images"
                :link="getLink('/archived/scanning-form/all?data_entry=all_scanned')"
            />
            <Card
                title="Total Data Entries"
                :value="dashboardCounts.total_data_entered"
                color="#06b6d4"
                icon="fa-database"
                :link="getLink('/archived/scanning-form/all?data_entry=completed')"
            />
            <Card
                title="Documents Verified"
                :value="dashboardCounts.total_verified"
                color="#22c55e"
                icon="fa-circle-check"
                :link="getLink('/archived/scanning-form/all?verification_status=verified')"
            />
        </div>

        <div class="grid grid-cols-1 gap-6">
            <div class="panel h-full bg-white p-5 rounded shadow border border-gray-100">
                <div class="mb-5 flex justify-between items-center">
                    <h5 class="text-lg font-bold text-gray-700">Scanner Performance Ranking</h5>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 border-b">
                                <th class="p-4 text-left font-bold uppercase tracking-wider">Rank</th>
                                <th class="p-4 text-left font-bold uppercase tracking-wider">Scanner Name</th>
                                <th class="p-4 text-center font-bold uppercase tracking-wider">Scanned Today</th>
                                <th class="p-4 text-center font-bold uppercase tracking-wider">Total Scanned</th>
                                <th class="p-4 text-center font-bold uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody v-if="!isLoading">
                            <tr v-for="(operator, index) in topOperators" :key="operator.id" class="border-b last:border-0 hover:bg-blue-50/30 transition">
                                <td class="p-4">
                                    <span
                                        :class="index === 0 ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600'"
                                        class="w-8 h-8 rounded-full inline-flex items-center justify-center font-bold"
                                    >
                                        {{ index + 1 }}
                                    </span>
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <div class="font-semibold text-gray-800">{{ operator.name }}</div>
                                    <div class="text-[10px] text-gray-400">ID: #{{ operator.id }}</div>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="font-mono font-bold text-pink-600 text-lg">{{ operator.today_count }}</span>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="font-mono font-bold text-blue-600 text-lg">{{ operator.total_count }}</span>
                                </td>
                                <td class="p-4 text-center">
                                    <span
                                        :class="index === 0 ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700'"
                                        class="px-3 py-1 rounded-full text-[10px] font-black uppercase"
                                    >
                                        {{ index === 0 ? 'Top Performer' : 'Active Scanner' }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="topOperators.length === 0">
                                <td colspan="5" class="text-center py-10 text-gray-400 italic">No scanner activity found for this selection.</td>
                            </tr>
                        </tbody>
                    </table>

                    <div v-if="isLoading" class="py-10 flex justify-center">
                        <span class="animate-spin border-4 border-primary !border-l-transparent rounded-full w-10 h-10"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
    import { ref, onMounted } from 'vue';
    import Card from '@/views/dashboard/card.vue';
    import apiClient from '@/services/axios';

    const isLoading = ref<boolean>(false);
    const selectedScanner = ref<string>('');
    const scannerUsers = ref<any[]>([]);
    const topOperators = ref<any[]>([]);
    const filters = ref({
        date_range: '',
    });
    const dashboardCounts = ref({
        total: 0,
        today: 0,
        week: 0,
        total_scanned: 0,
        total_data_entered: 0,
        total_verified: 0,
    });

    const getLink = (basePath: string) => {
        if (!selectedScanner.value) return basePath;
        const separator = basePath.includes('?') ? '&' : '?';
        return `${basePath}${separator}user_id=${selectedScanner.value}`;
    };

    const fetchDashboardData = async () => {
        try {
            isLoading.value = true;
            const res = await apiClient.get('/api/archive-dashboard', {
                params: {
                    user_id: selectedScanner.value,
                    from_date: filters.value.date_range && filters.value.date_range[0] ? filters.value.date_range[0] : null,
                    to_date: filters.value.date_range && filters.value.date_range[1] ? filters.value.date_range[1] : null,
                },
            });
            console.log('Dashboard Data:', res.data);

            dashboardCounts.value = res.data.counts;
            topOperators.value = res.data.top_operators;
        } catch (err) {
            console.error('Error fetching dashboard data', err);
        } finally {
            isLoading.value = false;
        }
    };

    const fetchScanners = async () => {
        try {
            const res = await apiClient.get('/api/scanners-list');
            scannerUsers.value = res.data.data;
        } catch (err) {
            console.error('Error fetching scanners', err);
        }
    };

    onMounted(() => {
        fetchScanners();
        fetchDashboardData();
    });
</script>
<style scoped>
    @import '@/assets/css/urdu-font.css';

    .transition-all {
        transition: all 0.3s ease;
    }
</style>
