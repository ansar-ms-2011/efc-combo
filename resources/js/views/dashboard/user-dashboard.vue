<template>
    <!-- Loading Spinner -->
    <div v-if="isLoadingCounts" class="fixed inset-0 flex items-center justify-center bg-white/50 z-50">
        <i class="fa fa-spinner fa-spin fa-2xl text-primary"></i>
    </div>
    <div class="p-3">
        <div class="flex flex-row justify-between rounded-lg bg-white px-4 py-3 text-sm font-semibold text-primary shadow-md border border-gray-200">
            <h1 class="px-4 py-3 font-semibold">Dashboard</h1>
            <!-- Filters Section -->
            <div
                v-if="store.user?.roles?.[0]?.name === 'Super Admin' || store.user?.roles?.[0]?.name === 'Commissioner'"
                class="flex flex-wrap items-center gap-3"
            >
                <select
                    v-model="filterType"
                    class="h-9 min-w-[160px] rounded-md border border-gray-300 bg-transparent px-3 text-sm text-black focus:outline-none focus:ring-1 focus:ring-primary"
                >
                    <option value="">Select Filter</option>
                    <option value="demography">Demography</option>
                    <option value="center">Center</option>
                </select>

                <!-- Demography -->
                <div v-if="filterType === 'demography'" class="flex gap-4">
                    <select v-model="selectedRegion" class="border rounded px-3 py-2">
                        <option value="">Select Region</option>
                        <option v-for="r in regions" :key="r.id" :value="r.id">{{ r.name }}</option>
                    </select>

                    <select v-model="selectedDistrict" class="border rounded px-3 py-2" :disabled="!districts.length">
                        <option value="">Select District</option>
                        <option v-for="d in districts" :key="d.id" :value="d.id">{{ d.name }}</option>
                    </select>

                    <select v-model="selectedTehsil" class="border rounded px-3 py-2" :disabled="!tehsils.length">
                        <option value="">Select Tehsil</option>
                        <option v-for="t in tehsils" :key="t.id" :value="t.id">{{ t.name }}</option>
                    </select>
                </div>

                <div v-if="filterType === 'center'" class="flex items-center gap-3">
                    <select v-model="selectedCenter" class="border rounded px-3 py-2">
                        <option value="">Select Center</option>
                        <option v-for="center in centers" :key="center.id" :value="center.id">
                            {{ center.name }}
                        </option>
                    </select>
                </div>

                <button class="bg-blue-600 text-white px-4 py-2 rounded" @click="searchData()"><i class="fa fa-search"></i> Search</button>
            </div>
        </div>
    </div>

    <div class="p-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- All Applications -->
            <Card title="All Applications" :value="dashboardCounts.all" color="#3b82f6" icon="fa-folder" link="/applications/all" />

            <Card title="Pending Applications"
             :value="dashboardCounts.pending"
             color="#f59e0b" icon="fa-clock"
              link="/applications/pending" />
            <Card
                title="Waiting Verification"
                :value="dashboardCounts.submitted"
                color="#10b981"
                icon="fa-file-signature"
                link="/applications/submitted"
            />

            <Card title="Waiting Approval"
             :value="dashboardCounts.verified"
              color="#8b5cf6" icon="fa-stamp"
              link="/applications/verified" />

            <!-- Applications for Printing -->
            <Card title="Applications for Printing"
             :value="dashboardCounts.approved"
              color="#ea580c" icon="fa-print"
               link="/applications/approved" />

            <Card
                title="Applications for Delivery"
                :value="dashboardCounts.delivery"
                color="#06b6d4"
                icon="fa-truck-fast"
                link="/applications/ready_for_delivery"
            />

            <!--  Delivered Applications -->
            <Card title="Delivered Applications"
             :value="dashboardCounts.delivered"
              color="#14b8a6" icon="fa-check-double"
              link="/applications/delivered" />

            <!--  Objected Applications -->
            <Card
                title="Objected Applications"
                :value="dashboardCounts.objected"
                :percentage="objectedPercentage"
                color="#ef4444"
                icon="fa-triangle-exclamation"
                link="/applications/objected"
            />

            <!--  Domicile Certificates -->
            <Card title="Domicile Issued"
            :value="dashboardCounts.domicile_certificate"
            color="#6366f1" icon="fa-certificate"
            link="/applications/delivered" />

            <!--  State Subject -->
            <Card
                title="State Subject Issued"
                :value="dashboardCounts.state_subject_certificate"
                color="#f59e0b"
                icon="fa-id-card"
                link="/applications/delivered"
            />

            <!-- Average Processing Time -->
            <Card title="Average Processing Time (Days)"
             :value="dashboardCounts.average_processing_time"
             color="#7c3aed" icon="fa-clock"
              link="#" />
        </div>
    </div>
</template>

<script setup lang="ts">
    import Card from '@/views/dashboard/card.vue';
    import { useAppStore } from '@/stores';
    import { useMeta } from '@/composables/use-meta';
    import { Center, Demography } from '@/types';
    import apiClient from '@/services/axios';
    import { computed, onMounted, ref, watch } from 'vue';

    useMeta({ title: 'E-Facilitation Center AJK' });
    const store = useAppStore();
    const dashboardCounts = ref({
        all: 0,
        pending: 0,
        submitted: 0,
        verified: 0,
        approved: 0,
        delivery: 0,
        delivered: 0,
        objected: 0,
        domicile_certificate: 0,
        state_subject_certificate: 0,
        average_processing_time: 0,
    });
    const filterType = ref('');

    // Dropdown options
    const regions = ref<Demography[]>([]);
    const districts = ref<Demography[]>([]);
    const tehsils = ref<Demography[]>([]);
    const centers = ref<Center[]>([]);

    // Selected values
    const selectedRegion = ref('');
    const selectedDistrict = ref('');
    const selectedTehsil = ref('');
    const selectedCenter = ref('');
    const isLoadingCounts = ref<boolean>(false);

    const searchData = () => {
        sessionStorage.setItem('district_id', selectedDistrict.value);
        sessionStorage.setItem('tehsil_id', selectedTehsil.value);
        sessionStorage.setItem('center_id', selectedCenter.value);
        fetchDashboardCounts();
    };

    onMounted(async () => {
        if (store.user) {
            await fetchDashboardCounts();
            await fetchDemographies();
        }
    });

    const fetchDemographies = async () => {
        try {
            const res = await apiClient.get('/api/demographies', { params: { type: 'REGION', parent_id: null } });
            regions.value = res.data.data.data;
        } catch (err) {
            console.error('Error fetching regions', err);
        }
    };

    // watch for filterType changes to reset selections and options
    watch(filterType, () => {
        selectedRegion.value = '';
        selectedDistrict.value = '';
        selectedTehsil.value = '';
        selectedCenter.value = '';
        districts.value = [];
        tehsils.value = [];
    });

    // Watch for selectedRegion changes to load districts dynamically
    watch(selectedRegion, async (regionId) => {
        selectedDistrict.value = '';
        selectedTehsil.value = '';
        districts.value = [];
        tehsils.value = [];

        if (!regionId) return;

        try {
            const res = await apiClient.get('/api/demographies', { params: { type: 'DISTRICT', parent_id: regionId } });
            districts.value = res.data.data.data;
        } catch (err) {
            console.error('Error fetching districts', err);
        }
    });

    // Watch for selectedDistrict changes to load tehsils dynamically
    watch(selectedDistrict, async (districtId) => {
        selectedTehsil.value = '';
        tehsils.value = [];

        if (!districtId) return;

        try {
            const res = await apiClient.get('/api/demographies', { params: { type: 'TEHSIL', parent_id: districtId } });
            tehsils.value = res.data.data.data;
        } catch (err) {
            console.error('Error fetching tehsils', err);
        }
    });

    onMounted(async () => {
        try {
            const res = await apiClient.get('/api/centers');
            centers.value = res.data.data.data; // backend structure ke hisaab se
        } catch (err) {
            console.error('Error fetching centers', err);
        }
    });

    const fetchDashboardCounts = async () => {
        try {
            isLoadingCounts.value = true;
            const res = await apiClient.get('/api/dashboard-counts', {
                params: {
                    district_id: selectedDistrict.value,
                    tehsil_id: selectedTehsil.value,
                    center_id: selectedCenter.value,
                },
            });
            dashboardCounts.value = res.data;
        } catch (err) {
            console.error('Error fetching dashboard counts', err);
        } finally {
            isLoadingCounts.value = false;
        }
    };

    const objectedPercentage = computed(() => {
        const total = dashboardCounts.value.all;
        const objected = dashboardCounts.value.objected;
        if (total === 0) return 0;
        return parseFloat(((objected / total) * 100).toFixed(2));
    });
</script>
