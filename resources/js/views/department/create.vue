<template>
    <div class="p-6 min-h-screen">
        <!-- Breadcrumb -->
        <div class="text-sm text-gray-600 mb-4">
            <router-link to="/admin/dashboard" class="text-grey-600 hover:underline">
                Department
            </router-link>
            <i class="fa fa-angle-right"></i>

            <router-link to="/departments" class="text-grey-600 hover:underline">
                Department List
            </router-link>
            <i class="fa fa-angle-right"></i>

            <span class="font-semibold text-blue-600">Add Department </span>
        </div>
        <div class="max-w-7xl mx-auto bg-white rounded-lg shadow border p-6">
            <!-- Heading -->
            <h2 class="text-xl  mb-6 text-black p-2 rounded font-semibold">
                Add Department
            </h2>
            <form class="space-y-6" @submit.prevent="handleSubmit">

                <!-- ROW 1 -->
                <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-1 ">

                    <div>
                        <label class="block text-sm font-medium mb-1">Name <span class="text-red-500">*</span></label>
                        <input
                            v-model="form.name"
                            type="text"
                            placeholder=" Department Name"
                            class="form-input" />

                        <label class="block text-sm font-medium mb-2">Description</label>
                        <textarea
                            v-model="form.description"
                            placeholder="Description"
                            class="form-input w-full mb-1"
                            rows="3"
                        ></textarea>
                    </div>
                </div>
                <!-- Buttons -->
                <div class="flex justify-end  gap-3 mt-6">
                    <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded shadow">
                        Save
                    </button>
                    <button type="button"
                            @click="resetForm"
                            class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded shadow">
                        Reset
                    </button>
                    <router-link to="/departments"
                                 class="px-6 py-2 bg-red-100 hover:bg-red-500 text-red-600 hover:text-white rounded shadow">
                        Cancel
                    </router-link>
                </div>


            </form>
        </div>
    </div>
</template>
<script setup>
    import { ref } from 'vue';
    import apiClient from '@/services/axios';
    import { addToQueue } from '@/services/offlineQueue';
    import Swal from 'sweetalert2';
    import { toast } from 'vue3-toastify';
    import { useRouter } from 'vue-router';

    const router = useRouter();

    const form = ref({
        name: '',
        urdu_name: ''
    });

    const handleSubmit = async () => {
        try {
            const response = await apiClient.post('/api/departments', form.value);

            console.log('Saved successfully:', response.data);
            resetForm();

            toast.success('Department saved successfully!');
            await router.push('/departments');

        } catch (error) {
            console.error('Error saving type:', error);

            if (!error.response) {

                await addToQueue({
                    syncRoute: '/api/departments',
                    payload: { ...form.value },
                    resourceLabel: 'Department',
                    resourceKey: 'department'
                });
                toast.error('Looks like you are offline. Application saved locally and will sync when online.');
                router.push('/departments');
                return;
            }

            Swal.fire({
                icon: 'error',
                title: 'Failed!',
                text: error.response?.data?.message || 'Failed to load department',
                padding: '2em',
                
            });
        }
    };


    const resetForm = () => {
        form.value = {
            name: '',
            description: ''
        };
    };
</script>

