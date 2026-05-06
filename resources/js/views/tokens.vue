<template>
    <div class="p-6 min-h-screen">
        <!-- Breadcrumb + Button -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center text-sm text-gray-600 space-x-2">
                <i class="fa fa-dashboard"></i>
                <router-link to="/admin/dashboard" class="cursor-pointer">
                    Dashboard
                </router-link>
                <i class="fa fa-angle-right"></i>
                <span class="cursor-pointer">API Access Tokens</span>
            </div>

            <button
                @click="openCreateModal"
                class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
                <i class="fa fa-plus"></i>
                Generate Token
            </button>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-lg shadow">
            <!-- Header -->
            <div class="flex items-center gap-2 px-6 py-4 border-b rounded-t-lg">
                <h2 class="text-lg font-semibold">API Access Tokens</h2>

                <!-- search -->
                <div class="ltr:ml-auto rtl:mr-auto">
                    <input v-model="search" type="text" class="form-input" placeholder="Search by token name ..." />
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <div v-if="loading" class="flex justify-center items-center py-20">
                    <i class="fa fa-spinner fa-spin fa-2xl"></i>
                </div>
                <table v-else class="min-w-full border-collapse">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 w-[60px]">Sr.#</th>
                        <th class="px-4 py-3">Application Name</th>
                        <th class="px-4 py-3 w-[180px]">Last Used at</th>
                        <th class="px-4 py-3 w-[180px]">Expires at</th>
                        <th class="px-4 py-3 w-[180px]">Created Date</th>
                        <th class="px-4 py-3 w-[100px] text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y">
                    <tr v-for="(token, index) in tokens" :key="token.id" class="hover:bg-gray-50 border-b">
                        <td class="px-4 py-2 w-[60px]">
                            {{ (currentPage - 1) * perPage + index + 1 }}
                        </td>
                        <td class="px-4 py-2 font-medium">
                            {{ token.name }}
                        </td>
                        <td class="px-4 py-2 text-gray-600">
                            <div class="flex flex-col gap-1 text-xs text-gray-600">
                                <span>{{ formatDMY(token.last_used_at, true) }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-2 text-gray-600">
                            <div class="flex flex-col gap-1 text-xs text-gray-600">
                                <span>{{ formatDMY(token.expires_at, true) }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-2 text-gray-600">
                            <div class="flex flex-col gap-1 text-xs text-gray-600">
                                <span>{{ formatDMY(token.created_at, true) }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-2 w-[100px] text-center space-x-2">
                            <button
                                @click="revokeToken(token.id)"
                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">
                                <div class="flex items-center justify-center gap-1">
                                    <i class="fa fa-trash"></i>
                                    <span>Revoke</span>
                                </div>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <!-- Pagination -->
                <ul class="inline-flex items-center space-x-1 rtl:space-x-reverse m-auto justify-end w-full mt-5 mb-3">
                    <button v-if="lastPage > 1" @click="fetchTokens(currentPage - 1)" :disabled="currentPage === 1"
                            class="px-3.5 py-1 rounded font-semibold bg-gray-200 hover:bg-blue-500 hover:text-white">
                        Prev
                    </button>
                    <li v-for="page in lastPage" :key="page" v-if="lastPage > 1">
                        <button @click="fetchTokens(page)" :class="[
              'px-3 py-1 rounded font-semibold transition',
              page === currentPage
                ? 'bg-blue-600 text-white'
                : 'bg-gray-200 text-gray-700 hover:bg-blue-500 hover:text-white'
            ]">
                            {{ page }}
                        </button>
                    </li>
                    <button v-if="lastPage > 1" @click="fetchTokens(currentPage + 1)"
                            :disabled="currentPage === lastPage"
                            class="px-3.5 py-1 rounded font-semibold bg-gray-200 hover:bg-blue-500 hover:text-white">
                        Next
                    </button>
                </ul>
            </div>
        </div>

        <!-- Create Token Modal -->
        <BaseDialog v-model="showCreateModal" title="Generate New API Token"
                    subtitle="Create a new token for third-party access">
            <form @submit.prevent="generateToken">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Application Name</label>
                    <input
                        v-model="newTokenName"
                        type="text"
                        class="form-input w-full"
                        placeholder="e.g., 'Mobile App', 'External Service'"
                        required
                        autofocus
                    />
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button
                        type="submit"
                        :disabled="generating"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded disabled:opacity-50">
                        <i v-if="generating" class="fa fa-spinner fa-spin mr-1"></i>
                        Generate Token
                    </button>
                    <button
                        type="button"
                        @click="closeModal"
                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded">
                        Cancel
                    </button>
                </div>
            </form>
        </BaseDialog>

        <!-- Display newly generated token with copy option -->
        <BaseDialog v-model="showTokenModal" title="New API Token Generated"
                    subtitle="Save this token now. It will not be shown again.">
            <div class="text-center">
                <div class="mb-4">
                    <div class="relative">
                        <code class="bg-gray-100 p-3 rounded block break-all text-sm pr-12">{{ newlyGeneratedToken }}</code>
                        <button
                            @click="copyToClipboard"
                            class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 transition-colors"
                            title="Copy to clipboard">
                            <i class="fa fa-copy"></i>
                        </button>
                    </div>
                </div>
                <div class="flex justify-center gap-2">
                    <button
                        @click="copyToClipboard"
                        class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded">
                        <i class="fa fa-copy mr-1"></i>
                        Copy
                    </button>
                    <button
                        @click="closeTokenModal"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
                        I've Saved It
                    </button>
                </div>
            </div>
        </BaseDialog>
    </div>
</template>

<script setup>
    import { ref, onMounted, watch } from 'vue';
    import axios from 'axios';
    import Swal from 'sweetalert2';
    import BaseDialog from '@/components/BaseDialog.vue';
    import { formatDMY, formatTime } from '@/mixin/index.ts';
    import apiClient from '@/services/axios.ts'; // Adjust path as needed

    const loading = ref(false);
    const generating = ref(false);
    const search = ref('');
    const tokens = ref([]);
    const currentPage = ref(1);
    const lastPage = ref(1);
    const perPage = ref(15);

    // Modal states
    const showCreateModal = ref(false);
    const showTokenModal = ref(false);
    const newTokenName = ref('');
    const newlyGeneratedToken = ref(null);

    const fetchTokens = async (page = 1) => {
        loading.value = true;

        try {
            const res = await apiClient.get(`/api/tokens`, {
                params: {
                    page,
                    search: search.value
                }
            });
            console.log('Fetched tokens:', res.data);
            tokens.value = res.data.data || [];
            currentPage.value = res.current_page || 1;
            lastPage.value = res.last_page || 1;
            perPage.value = res.per_page || 10;
        } catch (err) {
            console.error('Failed to fetch tokens:', err);
            Swal.fire('Error', 'Failed to fetch tokens', 'error');
        } finally {
            loading.value = false;
        }
    };

    const generateToken = async () => {
        if (!newTokenName.value.trim()) return;

        generating.value = true;

        try {
            const response = await apiClient.post('/api/tokens', {
                token_name: newTokenName.value
            });

            newlyGeneratedToken.value = response.data.token;
            newTokenName.value = '';

            // Close create modal and open token display modal
            showCreateModal.value = false;
            showTokenModal.value = true;

            // Refresh the list
            await fetchTokens();

        } catch (error) {
            console.error(error);
            Swal.fire('Error', 'Failed to generate token', 'error');
        } finally {
            generating.value = false;
        }
    };

    const revokeToken = async (tokenId) => {
        const confirm = await Swal.fire({
            icon: 'warning',
            title: 'Are you sure?',
            text: 'This token will be revoked immediately. Applications using it will lose access.',
            showCancelButton: true,
            confirmButtonText: 'Revoke',
            cancelButtonText: 'Cancel'
        });

        if (!confirm.isConfirmed) return;

        try {
            await apiClient.delete(`/api/tokens/${tokenId}`);
            tokens.value = tokens.value.filter(t => t.id !== tokenId);
            Swal.fire({
                icon: 'success',
                text: 'Revoked!, Token has been revoked successfully.',
                showConfirmButton: false,
                toast: true,
                position: 'top-end',
                timer: 1500,
            });
        } catch (error) {
            console.error(error);
            Swal.fire('Error', 'Failed to revoke token', 'error');
        }
    };

    const copyToClipboard = async () => {
        if (!newlyGeneratedToken.value) return;

        try {
            await navigator.clipboard.writeText(newlyGeneratedToken.value);
            Swal.fire({
                icon: 'success',
                text: 'Copied!, Token has been copied to clipboard',
                timer: 1500,
                toast: true,
                position: 'top-end',
                showConfirmButton: false
            });
        } catch (err) {
            // Fallback for older browsers
            const textarea = document.createElement('textarea');
            textarea.value = newlyGeneratedToken.value;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);

            Swal.fire({
                icon: 'success',
                text: 'Copied!, Token has been copied to clipboard',
                timer: 1500,
                toast: true,
                position: 'top-end',
                showConfirmButton: false
            });
        }
    };

    const openCreateModal = () => {
        newTokenName.value = '';
        showCreateModal.value = true;
    };

    const closeModal = () => {
        showCreateModal.value = false;
        newTokenName.value = '';
    };

    const closeTokenModal = () => {
        showTokenModal.value = false;
        newlyGeneratedToken.value = null;
    };

    // Watch for search input
    watch(search, () => {
        fetchTokens(1);
    });

    onMounted(() => {
        fetchTokens();
    });
</script>
