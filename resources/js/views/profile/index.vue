<template>
    <div class="p-6 min-h-screen">
        <!-- Breadcrumb -->
        <ul class="flex space-x-2 rtl:space-x-reverse mb-5 text-gray-600 text-sm">
            <li><a href="javascript:;" class="hover:underline">Users</a></li>
            <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2"><span>Profile</span></li>
        </ul>

        <!-- Additional Info Panel -->
        <div v-if="loading" class="flex justify-center items-center py-20">
            <span
                class="animate-spin border-8 border-[#f1f2f3] border-l-primary rounded-full w-14 h-14 inline-block"></span>
        </div>
        <div v-else class="panel lg:col-span-3 bg-white rounded-lg shadow p-5">

            <div class="">
                <div class="flex items-center justify-between mb-5">
                    <h5 class="font-semibold text-lg">Profile</h5>
                    <router-link to="/profile/edit" class="btn btn-primary p-2 rounded-full">
                        <icon-pencil-paper />
                    </router-link>
                </div>

                <div class="flex flex-col items-center">
                    <img :src="profileImage" alt="Profile" class="w-24 h-24 rounded-full object-cover mb-4" />
                    <p class="font-semibold text-xl">{{ employee?.name }}</p>
                </div>
            </div>

            <table class="w-full text-sm text-left border-collapse">
                <tbody>
                <tr>
                    <td class="border px-3 py-2 font-semibold">First Name</td>
                    <td class="border px-3 py-2">{{ employee?.first_name }}</td>
                </tr>
                <tr>
                    <td class="border px-3 py-2 font-semibold">Last Name</td>
                    <td class="border px-3 py-2">{{ employee?.last_name }}</td>
                </tr>
                <tr>
                    <td class="border px-3 py-2 font-semibold">Role</td>
                    <td class="border px-3 py-2">{{ employee?.roles?.[0]?.name || 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="border px-3 py-2 font-semibold">Email</td>
                    <td class="border px-3 py-2">{{ employee?.email || 'N/A' }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</template>

<script setup lang="ts">
    import { computed, onMounted, ref } from 'vue';
    import axios from 'axios';
    import { useAppStore } from '@/stores/index';
    import IconPencilPaper from '@/components/icon/icon-pencil-paper.vue';

    const store = useAppStore();
    const employee = ref<any>(null);
    const loading = ref(true);

    const fetchProfile = async () => {
        loading.value = true;
        try {
            const res = await axios.get('/api/employee/profile');
            console.log('Profile data:', res.data);
            employee.value = res.data;
            console.log('Employee image:', employee.value?.image);
        } catch (error) {
            console.error('Profile fetch error', error);
        } finally {
            loading.value = false;
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

    const formatDate = (dateStr: string) => {
        if (!dateStr) return 'N/A';
        const d = new Date(dateStr);
        return `${d.getDate()}-${d.getMonth() + 1}-${d.getFullYear()}`;
    };
</script>

<style scoped>
    .panel {
        background-color: #fff;
        border-radius: 0.5rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        padding: 1rem;
    }
</style>
