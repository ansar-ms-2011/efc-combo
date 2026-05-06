<template>
    <div class="page-content-wrapper  min-h-screen">
        <div class="page-content">

            <!-- Breadcrumb -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center text-sm text-gray-600 space-x-1">
                    <i class="fa fa-dashboard"></i>
                    <router-link to="/services-center" class="cursor-pointer">Center Services</router-link>
                    <i class="fa fa-angle-right"></i>
                    <span class="font-semibold text-blue-600">Edit Center Services</span>
                </div>
            </div>

            <!-- Card -->
            <div v-if="!isFetching" class="max-w-7xl mx-auto bg-white rounded-lg shadow border p-6">
                <h2 class="text-lg font-semibold mb-4">Edit Center Services</h2>
                <form @submit.prevent="handleSubmit">

                    <!-- Single Center -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Center</label>

                        <multiselect
                            v-model="selectedCenter"
                            :options="centers"
                            track-by="id"
                            label="name"
                            placeholder="Select Center"
                            :multiple="false"
                            :searchable="true"
                            :show-labels="false"
                            class="custom-multiselect"
                        />
                    </div>

                    <!-- Services Table -->
                    <div class="border rounded-lg overflow-hidden">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-100">
                            <tr>
                                <th class="text-left p-3 border">Service Name</th>
                                <th class="text-left p-3 border">Assigned</th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr v-for="service in services" :key="service.id" class="hover:bg-gray-50">
                                <td class="p-3 border">{{ service.name }}</td>
                                <td class="p-3 border">
                                    <input
                                        type="checkbox"
                                        :value="service.id"
                                        v-model="assigned"
                                        class="w-5 h-5"
                                    />
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="submit"
                                class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded shadow">
                            <span v-if="!isProcessing">Update</span>
                            <span v-else>
                                <i class="fa fa-spinner fa-spin"></i>
                            </span>
                        </button>

                        <button type="button" @click="resetForm"
                                class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded shadow">
                            Reset
                        </button>

                        <router-link to="/services-center"
                                     class="px-6 py-2 bg-red-100 hover:bg-red-500 text-red-600 hover:text-white rounded shadow">
                            Cancel
                        </router-link>
                    </div>

                </form>
            </div>
            <div v-else >
                <div class="flex justify-center items-center h-screen">
                    <i class="fa fa-spinner fa-spin"></i>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { onMounted, ref } from 'vue';
    import axios from 'axios';
    import Swal from 'sweetalert2';
    import { useRoute, useRouter } from 'vue-router';

    const route = useRoute();
    const router = useRouter();

    // State
    const centers = ref([]);
    const services = ref([]);
    const selectedCenter = ref(null);
    const assigned = ref([]); // array of service IDs
    const isProcessing = ref(false);
    const isFetching = ref(false);
    // Detect if we are editing (based on route param)
    const centerId = ref(route.params.id ? parseInt(route.params.id) : null);

    const fetchData = async () => {
        try {
            isFetching.value = true;
            if (centerId.value) {
                const resAssignments = await axios.get(`/api/service-centers/${centerId.value}`);
                assigned.value = resAssignments.data.assigned_services || [];
                centers.value = resAssignments.data.centers || [];
                services.value = resAssignments.data.services || [];

                selectedCenter.value = centers.value.find(c => c.id === centerId.value);
            }
        } catch (err) {
            console.error(err);
        }finally {
            isFetching.value = false;
        }
    };

    // Handle form submit
    const handleSubmit = async () => {
        if (!selectedCenter.value) {
            return Swal.fire('Warning', 'Please select a center', 'warning');
        }

        if (assigned.value.length === 0) {
            return Swal.fire('Warning', 'Please select at least one service', 'warning');
        }

        const payload = assigned.value.map(service_id => ({
            center_id: selectedCenter.value.id,
            service_id
        }));

        try {
            isProcessing.value = true;
            await axios.put('/api/service-centers', { assignments: payload });
            Swal.fire('Success', 'Center services updated successfully', 'success')
                .then(() => router.push('/services-center'));
        } catch (err) {
            Swal.fire('Error', err.response?.data?.message || 'Something went wrong', 'error');
        }finally {
            isProcessing.value = false;
        }
    };

    // Reset form
    const resetForm = () => {
        selectedCenter.value = null;
        assigned.value = [];
    };

    // Fetch data on page load
    onMounted(() => {
        fetchData();
    });
</script>
