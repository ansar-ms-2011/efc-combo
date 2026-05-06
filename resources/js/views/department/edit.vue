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
                <div class="flex justify-end gap-3 mt-6">
                    <button type="submit"
                            class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded shadow">
                        Update
                    </button>
                    <button type="button" @click="resetForm"
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

    import { onMounted, ref } from 'vue';
    import { useRoute, useRouter } from 'vue-router';
    import axios from 'axios';
    import Swal from 'sweetalert2';

    const route = useRoute();
    const router = useRouter();
    const id = route.params.id;

    // form state
    const form = ref({
        name: '',
        description: ''
    });

    // original form for reset
    const originalForm = ref({ ...form.value });

    // fetch existing department
    const getDepartment = async () => {
        try {
            const res = await axios.get(`/api/departments/${id}`);
            // make sure your API returns { data: { name, description } }
            form.value = {
                name: res.data.data.name,
                description: res.data.data.description
            };
            originalForm.value = { ...form.value };
        } catch (error) {
            console.error('Fetch error:', error.response || error);
            Swal.fire({
                icon: 'error',
                title: 'Failed!',
                text: error.response?.data?.message || 'Failed to load department',
                padding: '2em'
            });
        }
    };

    // submit update
    const handleSubmit = async () => {
        try {
            // Laravel expects PUT method for update
            const res = await axios.put(`/api/departments/${id}`, form.value);

            Swal.fire({
                icon: 'success',
                title: 'Updated!',
                text: 'Department updated successfully!',
                padding: '2em'
            }).then(() => router.push('/departments'));
        } catch (error) {
            console.error('Update error:', error.response || error);
            Swal.fire({
                icon: 'error',
                title: 'Failed!',
                text: error.response?.data?.message || 'Failed to update department',
                padding: '2em'
            });
        }
    };

    // reset form to original values
    const resetForm = () => {
        form.value = {
            name: '',
            description: ''

        };
    };

    // load department on mount
    onMounted(() => getDepartment());

</script>

