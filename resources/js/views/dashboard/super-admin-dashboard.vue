<!-- resources/js/components/SuperUser/KpiDashboard.vue -->
<template>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Active Users Card -->
            <div
                class="group  bg-white rounded-xl p-6 shadow-sm border border-gray-100 transition-all duration-500 hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.15)] hover:-translate-y-2 cursor-pointer relative overflow-hidden"
                :style="{ borderLeft: `4px solid ${colors.emerald}` }"
            >
                <div
                    class="absolute inset-0 opacity-0 group-hover:opacity-5 transition-opacity duration-700"
                    :style="{ background: `linear-gradient(135deg, ${colors.emerald}20 0%, transparent 100%)` }"
                ></div>

                <!-- Shimmer Effect -->
                <div
                    class="absolute inset-0 -translate-x-full group-hover:translate-x-full transition-transform duration-1000 ease-in-out pointer-events-none"
                    :style="{ background: `linear-gradient(90deg, transparent, ${colors.emerald}30, transparent)` }"
                ></div>

                <div class="flex justify-between items-start relative z-10">
                    <div class="flex-1">
                        <span
                            class="text-gray-400 font-bold text-[11px] uppercase tracking-wider block mb-2 leading-tight transition-all duration-300 group-hover:text-gray-500 group-hover:tracking-widest"
                        >
                            Active Users
                        </span>

                        <div v-if="loading.activeUsers" class="h-10 flex items-center">
                            <i class="fa fa-spinner fa-spin" :style="{ color: colors.emerald }"></i>
                        </div>
                        <div v-else>
                            <h3
                                class="text-3xl font-extrabold text-slate-800 leading-none mb-1 transition-all duration-500 group-hover:scale-105 group-hover:text-slate-900"
                            >
                                {{ formatNumber(displayActiveUsers) || 1 }}
                            </h3>
                            <p class="text-[11px] text-gray-500 font-medium">Out of {{ activeUsers.data?.total_users || 0 }} total users</p>
                            <div
                                class="mt-2 text-[11px] font-bold transition-all duration-300"
                                :class="
                                    activeUsers.data?.percentage_active > 20
                                        ? 'text-emerald-600 group-hover:text-emerald-700'
                                        : 'text-amber-600 group-hover:text-amber-700'
                                "
                            >
                                <i class="fas fa-chart-line mr-1"></i> {{ activeUsers.data?.percentage_active || 0 }}% active rate
                            </div>
                        </div>

                        <button
                            @click="refreshKPI('activeUsers')"
                            class="mt-4 text-[11px] font-bold uppercase tracking-tighter hover:underline flex items-center gap-1 transition-all duration-300 hover:gap-2"
                            :style="{ color: colors.emerald }"
                        >
                            <i class="fas fa-sync-alt text-[9px] transition-transform duration-300 group-hover:rotate-180"></i> Refresh
                        </button>
                    </div>

                    <div class="relative">
                        <!-- Icon Background Pulse -->
                        <div
                            class="absolute inset-0 rounded-xl scale-0 opacity-0 transition-all duration-500 group-hover:scale-110 group-hover:opacity-15"
                            :style="{ backgroundColor: colors.emerald }"
                        ></div>
                        <div
                            class="relative z-10 w-12 h-12 rounded-xl flex items-center justify-center text-xl transition-all duration-500 group-hover:scale-110"
                            :style="{ color: colors.emerald, backgroundColor: `${colors.emerald}10` }"
                        >
                            <i class="fas fa-users"></i>
                        </div>
                        <!--  -->
                    </div>
                </div>
                <div
                    class="absolute bottom-0 left-0 right-0 h-[2px] opacity-0 group-hover:opacity-100 transition-all duration-500"
                    :style="{ background: `linear-gradient(90deg, transparent, ${colors.emerald}, transparent)` }"
                ></div>
            </div>

            <!-- Applications Volume Card -->
            <div
                class="group bg-white rounded-xl p-6 shadow-sm border border-gray-100 transition-all duration-500 hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.15)] hover:-translate-y-2 hover:shadow-lg cursor-pointer relative overflow-hidden"
                :style="{ borderLeft: `4px solid ${colors.blue}` }"
            >
                <div
                    class="absolute inset-0 opacity-0 group-hover:opacity-5 transition-opacity duration-700"
                    :style="{ background: `linear-gradient(135deg, ${colors.blue}20 0%, transparent 100%)` }"
                ></div>
                <div
                    class="absolute inset-0 -translate-x-full group-hover:translate-x-full transition-transform duration-1000 ease-in-out pointer-events-none"
                    :style="{ background: `linear-gradient(90deg, transparent, ${colors.blue}30, transparent)` }"
                ></div>

                <div class="flex justify-between items-start relative z-10">
                    <div class="flex-1">
                        <span
                            class="text-gray-400 font-bold text-[11px] uppercase tracking-wider block mb-2 leading-tight transition-all duration-300 group-hover:text-gray-500 group-hover:tracking-widest"
                        >
                            Apps Volume (Today)
                        </span>

                        <div v-if="loading.transactionVolume" class="h-10 flex items-center">
                            <i class="fa fa-spinner fa-spin" :style="{ color: colors.blue }"></i>
                        </div>
                        <div v-else>
                            <h3
                                class="text-3xl font-extrabold text-slate-800 leading-none mb-1 transition-all duration-500 group-hover:scale-105 group-hover:text-slate-900"
                            >
                                {{ formatNumber(displayVolume) }}
                            </h3>
                            <p class="text-[11px] text-gray-500 font-medium">Today's Transactions</p>
                            <div class="mt-2 flex gap-3 text-[10px]">
                                <span class="text-gray-400 transition-all duration-300 group-hover:text-gray-500"
                                    >Week: <b class="text-slate-700 font-bold">{{ formatNumber(transactionVolume.data?.this_week?.count || 0) }}</b></span
                                >
                                <span class="text-gray-400 transition-all duration-300 group-hover:text-gray-500"
                                    >Month: <b class="text-slate-700 font-bold">{{ formatNumber(transactionVolume.data?.this_month?.count || 0) }}</b></span
                                >
                            </div>
                        </div>

                        <button
                            @click="refreshKPI('transactionVolume')"
                            class="mt-4 text-[11px] font-bold uppercase tracking-tighter hover:underline flex items-center gap-1 transition-all duration-300 hover:gap-2"
                            :style="{ color: colors.blue }"
                        >
                            <i class="fas fa-sync-alt text-[9px] transition-transform duration-300 group-hover:rotate-180"></i> Refresh
                        </button>
                    </div>

                    <div class="relative">
                        <div
                            class="absolute inset-0 rounded-xl scale-0 opacity-0 transition-all duration-500 group-hover:scale-110 group-hover:opacity-15"
                            :style="{ backgroundColor: colors.blue }"
                        ></div>
                        <div
                            class="relative z-10 w-12 h-12 rounded-xl flex items-center justify-center text-xl transition-all duration-500 group-hover:scale-110 group-hover:rotate-3"
                            :style="{ color: colors.blue, backgroundColor: `${colors.blue}10` }"
                        >
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>

                <div
                    class="absolute bottom-0 left-0 right-0 h-[2px] opacity-0 group-hover:opacity-100 transition-all duration-500"
                    :style="{ background: `linear-gradient(90deg, transparent, ${colors.blue}, transparent)` }"
                ></div>
            </div>

            <!-- API Calls Card -->
            <div
                class="group bg-white rounded-xl p-6 shadow-sm border border-gray-100 transition-all duration-500 hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.15)] hover:-translate-y-2 hover:shadow-lg cursor-pointer relative overflow-hidden"
                :style="{ borderLeft: `4px solid ${colors.fuchsia}` }"
            >
                <div
                    class="absolute inset-0 opacity-0 group-hover:opacity-5 transition-opacity duration-700"
                    :style="{ background: `linear-gradient(135deg, ${colors.fuchsia}20 0%, transparent 100%)` }"
                ></div>
                <div
                    class="absolute inset-0 -translate-x-full group-hover:translate-x-full transition-transform duration-1000 ease-in-out pointer-events-none"
                    :style="{ background: `linear-gradient(90deg, transparent, ${colors.fuchsia}30, transparent)` }"
                ></div>

                <div class="flex justify-between items-start relative z-10">
                    <div class="flex-1">
                        <span
                            class="text-gray-400 font-bold text-[11px] uppercase tracking-wider block mb-2 leading-tight transition-all duration-300 group-hover:text-gray-500 group-hover:tracking-widest"
                        >
                            API Calls (Today)
                        </span>

                        <div v-if="loading.apiCalls" class="h-10 flex items-center">
                            <i class="fa fa-spinner fa-spin" :style="{ color: colors.fuchsia }"></i>
                        </div>
                        <div v-else>
                            <h3
                                class="text-3xl font-extrabold text-slate-800 leading-none mb-1 transition-all duration-500 group-hover:scale-105 group-hover:text-slate-900"
                            >
                                {{ formatNumber(displayApiCalls) }}
                            </h3>
                            <p class="text-[11px] text-gray-500 font-medium">Avg Res: {{ apiCalls.data?.avg_response_time_ms || 0 }}ms</p>
                            <div class="mt-2 flex gap-2 text-[10px] font-bold">
                                <span
                                    v-if="getStatusCount(200)"
                                    class="text-emerald-600 transition-all duration-300 group-hover:text-emerald-700 group-hover:scale-105 inline-block"
                                >
                                    <i class="fas fa-check-circle mr-1"></i>{{ getStatusCount(200) }}
                                </span>
                                <span
                                    v-if="getStatusCount(500)"
                                    class="text-rose-600 transition-all duration-300 group-hover:text-rose-700 group-hover:scale-105 inline-block"
                                >
                                    <i class="fas fa-times-circle mr-1"></i>{{ getStatusCount(500) }}
                                </span>
                            </div>
                        </div>

                        <button
                            @click="refreshKPI('apiCalls')"
                            class="mt-4 text-[11px] font-bold uppercase tracking-tighter hover:underline flex items-center gap-1 transition-all duration-300 hover:gap-2"
                            :style="{ color: colors.fuchsia }"
                        >
                            <i class="fas fa-sync-alt text-[9px] transition-transform duration-300 group-hover:rotate-180"></i> Refresh
                        </button>
                    </div>

                    <div class="relative">
                        <div
                            class="absolute inset-0 rounded-xl scale-0 opacity-0 transition-all duration-500 group-hover:scale-110 group-hover:opacity-15"
                            :style="{ backgroundColor: colors.fuchsia }"
                        ></div>
                        <div
                            class="relative z-10 w-12 h-12 rounded-xl flex items-center justify-center text-xl transition-all duration-500 group-hover:scale-110 group-hover:rotate-3"
                            :style="{ color: colors.fuchsia, backgroundColor: `${colors.fuchsia}10` }"
                        >
                            <i class="fas fa-server"></i>
                        </div>
                    </div>
                </div>

                <div
                    class="absolute bottom-0 left-0 right-0 h-[2px] opacity-0 group-hover:opacity-100 transition-all duration-500"
                    :style="{ background: `linear-gradient(90deg, transparent, ${colors.fuchsia}, transparent)` }"
                ></div>
            </div>

            <!-- Failed Logins Card -->
            <div
                class="group bg-white rounded-xl p-6 shadow-sm border border-gray-100 transition-all duration-500 hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.15)] hover:-translate-y-2 hover:shadow-lg cursor-pointer relative overflow-hidden"
                :style="{ borderLeft: `4px solid ${colors.rose}` }"
            >
                <div
                    class="absolute inset-0 opacity-0 group-hover:opacity-5 transition-opacity duration-700"
                    :style="{ background: `linear-gradient(135deg, ${colors.rose}20 0%, transparent 100%)` }"
                ></div>
                <div
                    class="absolute inset-0 -translate-x-full group-hover:translate-x-full transition-transform duration-1000 ease-in-out pointer-events-none"
                    :style="{ background: `linear-gradient(90deg, transparent, ${colors.rose}30, transparent)` }"
                ></div>

                <div class="flex justify-between items-start relative z-10">
                    <div class="flex-1">
                        <span
                            class="text-gray-400 font-bold text-[11px] uppercase tracking-wider block mb-2 leading-tight transition-all duration-300 group-hover:text-gray-500 group-hover:tracking-widest"
                        >
                            Failed Logins
                        </span>

                        <div v-if="loading.failedLogins" class="h-10 flex items-center">
                            <i class="fa fa-spinner fa-spin" :style="{ color: colors.rose }"></i>
                        </div>
                        <div v-else>
                            <h3
                                class="text-3xl font-extrabold text-slate-800 leading-none mb-1 transition-all duration-500 group-hover:scale-105 group-hover:text-slate-900"
                            >
                                {{ formatNumber(displayFailedLogins) }}
                            </h3>
                            <div
                                class="text-[11px] font-bold transition-all duration-300"
                                :class="
                                    failedLogins.data?.change_from_last_week > 0
                                        ? 'text-rose-600 group-hover:text-rose-700'
                                        : 'text-emerald-600 group-hover:text-emerald-700'
                                "
                            >
                                <i :class="failedLogins.data?.change_from_last_week > 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'"></i>
                                {{ Math.abs(failedLogins.data?.change_from_last_week || 0) }}% from last week
                            </div>
                            <div class="mt-2 space-y-0.5 border-t border-gray-50 pt-1">
                                <div
                                    v-for="ip in (failedLogins.data?.top_offending_ips || []).slice(0, 1)"
                                    :key="ip.ip"
                                    class="text-[9px] flex justify-between text-gray-400 transition-all duration-300 group-hover:text-gray-500"
                                >
                                    <span class="font-mono">Top IP: {{ ip.ip }}</span>
                                    <span class="font-bold transition-all duration-300" :style="{ color: colors.rose }">({{ ip.attempt_count }})</span>
                                </div>
                            </div>
                        </div>

                        <button
                            @click="refreshKPI('failedLogins')"
                            class="mt-4 text-[11px] font-bold uppercase tracking-tighter hover:underline flex items-center gap-1 transition-all duration-300 hover:gap-2"
                            :style="{ color: colors.rose }"
                        >
                            <i class="fas fa-sync-alt text-[9px] transition-transform duration-300 group-hover:rotate-180"></i> Refresh
                        </button>
                    </div>

                    <div class="relative">
                        <div
                            class="absolute inset-0 rounded-xl scale-0 opacity-0 transition-all duration-500 group-hover:scale-110 group-hover:opacity-15"
                            :style="{ backgroundColor: colors.rose }"
                        ></div>
                        <div
                            class="relative z-10 w-12 h-12 rounded-xl flex items-center justify-center text-xl transition-all duration-500 group-hover:scale-110 group-hover:rotate-3"
                            :style="{ color: colors.rose, backgroundColor: `${colors.rose}10` }"
                        >
                            <i class="fas fa-shield-alt"></i>
                        </div>

                    </div>
                </div>

                <div
                    class="absolute bottom-0 left-0 right-0 h-[2px] opacity-0 group-hover:opacity-100 transition-all duration-500"
                    :style="{ background: `linear-gradient(90deg, transparent, ${colors.rose}, transparent)` }"
                ></div>
            </div>
        </div>

        <!-- Charts Section with ApexCharts -->
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Applications Trend Chart -->
            <div class="bg-white rounded-lg shadow" v-if="transactionVolume.data?.last_7_days_trend">
                <div class="bg-green-100 px-4 py-2 rounded-t-lg">
                    <h3 class="text-lg font-semibold text-gray-700 mb-0">
                        <i class="fas fa-chart-line mr-2 text-green-500"></i>
                        Applications Trend (Last 7 Days)
                    </h3>
                </div>
                <div class="p-4 h-80">
                    <apexchart :options="transactionChartOptions" :series="transactionChartSeries" type="area" height="100%" />
                </div>
            </div>

            <!-- Failed Logins Trend Chart -->
            <div class="bg-white rounded-lg shadow" v-if="failedLogins.data?.last_7_days_trend">
                <div class="bg-green-100 px-4 py-2 rounded-t-lg">
                    <h3 class="text-lg font-semibold text-gray-700 mb-0">
                        <i class="fas fa-exclamation-triangle mr-2 text-red-500"></i>
                        Failed Login Trend (Last 7 Days)
                    </h3>
                </div>
                <div class="h-80 p-4">
                    <apexchart :options="failedLoginsChartOptions" :series="failedLoginsChartSeries" type="bar" height="100%" />
                </div>
            </div>

            <!-- Top API Calls -->
            <div class="bg-white rounded-lg shadow" v-if="transactionVolume.data?.last_7_days_trend">
                <div class="bg-green-100 px-4 py-2 rounded-t-lg">
                    <h3 class="text-lg font-semibold text-gray-700 mb-0">
                        <i class="fas fa-chart-line mr-2 text-green-500"></i>
                        Top Endpoints
                    </h3>
                </div>
                <div class="h-80">
                    <table class="table w-full text-sm text-left text-gray-500">
                        <thead>
                            <tr>
                                <th class="text-center">S.No</th>
                                <th class="text-start">Endpoint</th>
                                <th class="text-center">Calls</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(endpoint, index) in (apiCalls.data?.top_endpoints || []).slice(0, 5)" class="even:bg-gray-50 odd:bg-white border-b">
                                <td class="text-center">{{ index + 1 }}</td>
                                <td class="text-start text-wrap">{{ endpoint.endpoint }}</td>
                                <td class="text-center">{{ formatNumber(endpoint.call_count) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- API Calls Hourly Distribution Chart -->
        <div class="mt-8 bg-white rounded-lg shadow" v-if="apiCalls.data?.hourly_breakdown">
            <div class="bg-green-100 px-4 py-2 rounded-t-lg">
                <h3 class="text-lg font-semibold text-gray-700 mb-0">
                    <i class="fas fa-chart-bar mr-2 text-purple-500"></i>
                    API Calls - Last 24 Hours
                </h3>
            </div>
            <div class="h-80 p-4">
                <apexchart :options="apiCallsChartOptions" :series="apiCallsChartSeries" type="line" height="100%" />
            </div>
        </div>

        <!-- Auto-refresh controls -->
        <div class="mt-6 flex justify-center items-center gap-4 text-sm text-gray-500">
            <div class="flex items-center gap-2">
                <i :class="autoRefresh ? 'fas fa-sync-alt fa-spin' : 'fas fa-pause'" class="text-blue-500"></i>
                <span>Auto-refreshing every 30 seconds</span>
            </div>
            <button
                @click="toggleAutoRefresh"
                class="px-3 py-1 rounded-md transition-colors"
                :class="autoRefresh ? 'bg-red-100 text-red-600 hover:bg-red-200' : 'bg-green-100 text-green-600 hover:bg-green-200'"
            >
                <i :class="autoRefresh ? 'fas fa-stop' : 'fas fa-play'" class="mr-1"></i>
                {{ autoRefresh ? 'Disable' : 'Enable' }}
            </button>
            <button @click="refreshAllKPIs" class="px-3 py-1 bg-blue-100 text-blue-600 rounded-md hover:bg-blue-200">
                <i class="fas fa-sync-alt mr-1"></i>
                Refresh Now
            </button>
        </div>

        <!-- Loading overlay for full refresh -->
        <div v-if="loading.global" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 flex items-center gap-3">
                <i class="fas fa-spinner fa-spin text-blue-500 text-2xl"></i>
                <span>Refreshing dashboard...</span>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, onMounted, onUnmounted, computed, watch } from 'vue';
    import apiClient from '@/services/axios.ts';
    import VueApexCharts from 'vue3-apexcharts';

    const colors = {
        emerald: '#10b981',
        blue: '#3b82f6',
        fuchsia: '#d946ef',
        rose: '#f43f5e',
    };
    // Register ApexCharts component
    const apexchart = VueApexCharts;

    // State
    const activeUsers = ref({ data: null, error: null });
    const transactionVolume = ref({ data: null, error: null });
    const apiCalls = ref({ data: null, error: null });
    const failedLogins = ref({ data: null, error: null });
    const loading = ref({
        activeUsers: false,
        transactionVolume: false,
        apiCalls: false,
        failedLogins: false,
        global: false,
    });

    const autoRefresh = ref(true);
    let refreshInterval = null;

    // Helper to get status code counts
    const getStatusCount = (statusCode) => {
        if (!apiCalls.value.data?.status_codes) return 0;
        const statusGroup = apiCalls.value.data.status_codes.find((s) => s.status_code === statusCode);
        return statusGroup ? statusGroup.count : 0;
    };

    // Computed chart data
    const transactionChartSeries = computed(() => {
        console.log(transactionVolume.value);
        if (!transactionVolume.value.data?.last_7_days_trend) return [];
        return [
            {
                name: 'Applications Volume',
                data: transactionVolume.value.data.last_7_days_trend.map((d) => d.count || 0),
            },
        ];
    });

    const transactionChartOptions = computed(() => ({
        chart: {
            type: 'area',
            toolbar: {
                show: true,
                tools: {
                    zoom: true,
                    zoomin: true,
                    zoomout: true,
                    pan: true,
                    reset: true,
                },
            },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
            },
        },
        dataLabels: {
            enabled: false,
        },
        stroke: {
            curve: 'smooth',
            width: 2,
            colors: ['#10B981'],
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3,
                stops: [0, 90, 100],
            },
        },
        xaxis: {
            categories: transactionVolume.value.data?.last_7_days_trend?.map((d) => d.date) || [],
            title: {
                text: 'Date',
            },
        },
        yaxis: {
            title: {
                text: 'Count',
            },
            labels: {
                formatter: (value) => {
                    return value.toLocaleString();
                },
            },
        },
        tooltip: {
            y: {
                formatter: (value) => {
                    return value.toLocaleString();
                },
            },
        },
        colors: ['#10B981'],
        grid: {
            borderColor: '#e7e7e7',
            row: {
                colors: ['#f3f3f3', 'transparent'],
                opacity: 0.5,
            },
        },
    }));

    const failedLoginsChartSeries = computed(() => {
        if (!failedLogins.value.data?.last_7_days_trend) return [];
        return [
            {
                name: 'Failed Login Attempts',
                data: failedLogins.value.data.last_7_days_trend.map((d) => d.count),
            },
        ];
    });

    const failedLoginsChartOptions = computed(() => ({
        chart: {
            type: 'bar',
            toolbar: {
                show: true,
            },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
            },
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: false,
                columnWidth: '55%',
            },
        },
        dataLabels: {
            enabled: false,
        },
        xaxis: {
            categories: failedLogins.value.data?.last_7_days_trend?.map((d) => d.date) || [],
            title: {
                text: 'Date',
            },
        },
        yaxis: {
            title: {
                text: 'Number of Attempts',
            },
        },
        colors: [
            '#EF4701',
            '#F55B1A',
            '#F76F33',
            '#F9834C',
            '#FB9765',
            '#FDAB7E',
            '#FEBF97',
            '#FED3B0',
            '#FFE7C9',
            '#FFF5E8',
            '#1B5E20',
            '#2E7D32',
            '#388E3C',
            '#43A047',
            '#4CAF50',
            '#66BB6A',
            '#81C784',
            '#A5D6A7',
            '#C8E6C9',
            '#E8F5E9',
        ],
        tooltip: {
            y: {
                formatter: (value) => {
                    return value + ' attempts';
                },
            },
        },
        grid: {
            borderColor: '#e7e7e7',
        },
    }));

    const apiCallsChartSeries = computed(() => {
        if (!apiCalls.value.data?.hourly_breakdown) return [];
        return [
            {
                name: 'API Calls',
                data: apiCalls.value.data.hourly_breakdown.map((d) => d.count),
            },
        ];
    });

    const apiCallsChartOptions = computed(() => ({
        chart: {
            type: 'line',
            toolbar: {
                show: true,
            },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
            },
        },
        stroke: {
            curve: 'smooth',
            width: 3,
        },
        dataLabels: {
            enabled: false,
        },
        xaxis: {
            categories: apiCalls.value.data?.hourly_breakdown?.map((d) => `${d.hour}`) || [],
            title: {
                text: 'Hour of Day',
            },
        },
        yaxis: {
            title: {
                text: 'Number of Calls',
            },
        },
        colors: ['#8B5CF6'],
        markers: {
            size: 4,
            colors: ['#8B5CF6'],
            strokeColors: '#fff',
            strokeWidth: 2,
            hover: {
                size: 7,
            },
        },
        tooltip: {
            y: {
                formatter: (value) => {
                    return value.toLocaleString() + ' calls';
                },
            },
        },
        grid: {
            borderColor: '#e7e7e7',
        },
    }));

    // Methods
    const fetchKPI = async (kpiName, url, state) => {
        loading.value[kpiName] = true;
        state.error = null;

        try {
            const response = await apiClient.get(url);
            state.value.data = response.data;
            // console.log(`Fetched ${kpiName}:`, state.value.data);
        } catch (error) {
            console.error(`Error fetching ${kpiName}:`, error);
            state.error = error.message;
        } finally {
            loading.value[kpiName] = false;
        }
    };

    const refreshAllKPIs = async () => {
        loading.value.global = true;
        await Promise.all([
            fetchKPI('activeUsers', '/api/kpi/active-users', activeUsers),
            fetchKPI('transactionVolume', '/api/kpi/transaction-volume', transactionVolume),
            fetchKPI('apiCalls', '/api/kpi/api-calls', apiCalls),
            fetchKPI('failedLogins', '/api/kpi/failed-logins', failedLogins),
        ]);
        loading.value.global = false;
    };

    const refreshKPI = (kpiName) => {
        switch (kpiName) {
            case 'activeUsers':
                fetchKPI('activeUsers', '/api/kpi/active-users', activeUsers);
                break;
            case 'transactionVolume':
                fetchKPI('transactionVolume', '/api/kpi/transaction-volume', transactionVolume);
                break;
            case 'apiCalls':
                fetchKPI('apiCalls', '/api/kpi/api-calls', apiCalls);
                break;
            case 'failedLogins':
                fetchKPI('failedLogins', '/api/kpi/failed-logins', failedLogins);
                break;
        }
    };

    const toggleAutoRefresh = () => {
        autoRefresh.value = !autoRefresh.value;
        if (autoRefresh.value) {
            startAutoRefresh();
        } else {
            stopAutoRefresh();
        }
    };

    const startAutoRefresh = () => {
        if (refreshInterval) clearInterval(refreshInterval);
        refreshInterval = setInterval(() => {
            if (autoRefresh.value) {
                refreshAllKPIs();
            }
        }, 30000); // 30 seconds
    };

    const stopAutoRefresh = () => {
        if (refreshInterval) {
            clearInterval(refreshInterval);
            refreshInterval = null;
        }
    };

    const formatNumber = (value) => {
        return new Intl.NumberFormat('en-US').format(value);
    };

    // Lifecycle
    onMounted(() => {
        refreshAllKPIs();
        startAutoRefresh();
    });

    onUnmounted(() => {
        stopAutoRefresh();
    });

    const displayActiveUsers = ref(0);
    const displayVolume = ref(0);
    const displayApiCalls = ref(0);
    const displayFailedLogins = ref(0);

    const animateCounter = (target, refVar) => {
        let startTimestamp = null;
        const duration = 500;
        const startValue = refVar.value;
        const endValue = parseInt(target) || 0;

        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            refVar.value = Math.floor(progress * (endValue - startValue) + startValue);
            if (progress < 1) window.requestAnimationFrame(step);
            else refVar.value = endValue;
        };
        window.requestAnimationFrame(step);
    };
    watch(
        () => activeUsers.value.data?.active_users,
        (val) => animateCounter(val, displayActiveUsers),
    );
    watch(
        () => transactionVolume.value.data?.today?.count,
        (val) => animateCounter(val, displayVolume),
    );
    watch(
        () => apiCalls.value.data?.today,
        (val) => animateCounter(val, displayApiCalls),
    );
    watch(
        () => failedLogins.value.data?.today,
        (val) => animateCounter(val, displayFailedLogins),
    );
</script>

<style scoped>
    /*.kpi-dashboard {
    //    @apply p-6 bg-gray-50 min-h-screen;
    //}*/

    .bg-gradient-to-r::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.15);
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }

    @keyframes pulse {
        0%,
        100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }

    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    /* ApexCharts custom styling */
    :deep(.apexcharts-tooltip) {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 8px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
    }

    :deep(.apexcharts-tooltip-title) {
        font-weight: 600;
        padding: 8px 12px;
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }
</style>
