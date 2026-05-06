<template>
    <div class="p-6 min-h-screen">
        <ul class="flex space-x-2 rtl:space-x-reverse mb-5 text-gray-600 text-sm">
            <li>
                <router-link to="/profile" class="hover:underline">Users</router-link>
            </li>
            <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2">
                <span>Edit Profile</span>
            </li>
        </ul>

        <div v-if="loading" class="flex justify-center items-center py-20">
            <span
                class="animate-spin border-8 border-[#f1f2f3] border-l-primary rounded-full w-14 h-14 inline-block"></span>
        </div>

        <!-- Profile Fields Section -->
        <div v-else class="panel bg-white rounded-lg shadow p-5">
            <form @submit.prevent="updateProfile" class="space-y-4">

                <div class="flex items-center justify-between mb-5">
                    <h5 class="font-semibold text-lg">Profile Information</h5>
                </div>
                <div class="flex flex-col items-center mb-6">
                    <div class="relative group w-28 h-28">
                        <img :src="previewImage || profileImage"
                            class="w-28 h-28 rounded-full object-cover border shadow" />
                        <label for="photoInput" class="absolute inset-0 bg-black/40 rounded-full
             flex items-center justify-center
             opacity-0 group-hover:opacity-100
             transition duration-300 cursor-pointer">
                            <icon-camera class="w-6 h-6 text-white" />
                        </label>
                        <input id="photoInput" type="file" accept="image/*" capture="environment" class="hidden"
                            @change="handleImageChange" />
                    </div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <div>
                            <label class="block text-sm font-semibold mb-1 text-gray-700">First Name</label>
                            <input v-model="form.first_name" type="text"
                                class="w-full rounded-lg py-2 px-3 border border-gray-300 focus:ring-2 " />
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-1 text-gray-700">Last Name</label>
                            <input v-model="form.last_name" type="text"
                                class="w-full rounded-lg py-2 px-3 border border-gray-300 focus:ring-2" />
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-1 text-gray-700">Email</label>
                            <input v-model="form.email" type="email"
                                class="w-full rounded-lg py-2 px-3 border border-gray-300 focus:ring-2" />
                        </div>
                        <div class="mt-4">
                            <label class="flex items-center gap-2">

                                <span class="text-sm font-semibold text-gray-700">
                                    Enable Application Urdu Keyboard
                                </span>
                                <input type="checkbox" v-model="form.keyboard_settings.urduInput" />
                            </label>
                        </div>
                    </div>
                    <!-- Signature Manager Section -->
                    <SignatureManager v-model="signatureFile" :existingSignatureUrl="existingSignatureUrl"
                        @removed="handleSignatureRemoved" @saved="handleSignatureChange" />
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="submit"
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow transition duration-200">
                        Update Profile
                    </button>
                    <router-link to="/profile"
                        class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg shadow transition duration-200">
                        Cancel
                    </router-link>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import Swal from 'sweetalert2';
import IconCamera from '@/components/icon/icon-camera.vue';
import SignatureManager from '@/components/SignatureManager.vue';
import apiClient from '@/services/axios';

const router = useRouter();

const loading = ref(false);
const employee = ref<Employee | null>(null);
const existingSignatureUrl = ref<string | null>(null);
const signatureFile = ref<File | null>(null);

interface Employee {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    center_id: number;
    image?: string;
    sign_file?: string;
}

const form = ref({
    first_name: '',
    last_name: '',
    email: '',
    center_id: '',
    role_id: '',
    photo: null as File | null,
    sign_file: null as File | null,
    keyboard_settings: {
    'urduInput': false
}
});

const fetchProfile = async () => {
    loading.value = true;
    const res = await apiClient.get('/api/employee/profile');
    employee.value = res.data;

    form.value.first_name = res.data.first_name;
    form.value.last_name = res.data.last_name;
    form.value.email = res.data.email;
    form.value.center_id = res.data.center_id;
    form.value.role_id = res.data.roles?.[0]?.id;

    existingSignatureUrl.value = res.data.sign_url;

    form.value.keyboard_settings.urduInput = res.data.keyboard_settings?.urduInput || false;
    loading.value = false;
    console.log('Employee data:', res);
};

// Handle signature change from SignatureManager
const handleSignatureChange = (file: File) => {
    signatureFile.value = file;
    console.log('Signature file updated:', file);
};

const updateProfile = async () => {
    try {
        const formData = new FormData();

        // Add text fields
        formData.append('first_name', form.value.first_name || '');
        formData.append('last_name', form.value.last_name || '');
        formData.append('email', form.value.email || '');
        formData.append('center_id', String(form.value.center_id || ''));
        formData.append('role_id', String(form.value.role_id || ''));
        formData.append('keyboard_settings',JSON.stringify(form.value.keyboard_settings));

        // Add profile photo if changed
        if (form.value.photo) {
            formData.append('photo', form.value.photo);
        }

        // Add signature file if changed
        if (signatureFile.value) {
            formData.append('new_sign_file', signatureFile.value);
            console.log('Sending signature file:', signatureFile.value.name, signatureFile.value.type);
        }

        formData.append('_method', 'PUT');

        const response = await apiClient.post('/api/employee/profile', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        Swal.fire({
            icon: 'success',
            title: 'Profile updated successfully',
            showConfirmButton: false,
            timer: 1500
        }).then(() => {
            router.push('/profile');
        });

    } catch (error: any) {
        console.error('Update error:', error.response?.data);

        Swal.fire({
            icon: 'error',
            title: 'Failed',
            text: error.response?.data?.message || 'Something went wrong'
        });
    }
};

const handleSignatureRemoved = async () => {
    try {
        await axios.delete('/api/employee/signature');
        existingSignatureUrl.value = null;
        signatureFile.value = null; // Clear the signature file
        form.value.sign_file = null;
    } catch (error) {
        console.error('Failed to remove signature from server:', error);
    }
};

const previewImage = ref<string | null>(null);

const handleImageChange = (e: Event) => {
    const target = e.target as HTMLInputElement;

    if (target.files && target.files[0]) {
        form.value.photo = target.files[0];
        previewImage.value = URL.createObjectURL(target.files[0]);
    }
};

const profileImage = computed(() => {
    if (!employee.value?.image) {
        return '/assets/images/user-profile.jpeg';
    }
    return `http://localhost:8000/storage/${employee.value.image}`;
});

onMounted(() => {
    fetchProfile();
});
</script>

<style scoped>
.panel {
    background-color: #fff;
    border-radius: 0.5rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}
</style>
