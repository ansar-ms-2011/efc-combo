<template>
    <div class="p-4">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center text-sm text-gray-600 space-x-2">
                <i class="fa fa-dashboard"></i>
                <span class="cursor-pointer">Dashboard</span>
                <i class="fa fa-angle-right"></i>
                <span class="cursor-not-allowed text-gray-400">
                    <span style="text-transform: capitalize;">Drafted Applications</span>
                </span>
            </div>
        </div>

        <div class="max-w-7xl mx-auto bg-white rounded-lg shadow border p-4 min-h-[calc(100vh-250px)] relative">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-xl font-semibold"><span style="text-transform: capitalize;">Drafted Applications</span>
                </h2>
                <router-link
                    v-if="(store.user?.role === 'DEO' || store.user?.role === 'Center In-charge')"
                    to="/app/applications/create"
                    class="group flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg
                       shadow-sm hover:shadow-md
                       transition-all duration-300 ease-out
                       hover:bg-primary-dark">
                    <i class="fa fa-plus transition-transform duration-300 group-hover:rotate-90"></i>
                    <span class="transition-transform duration-300 group-hover:translate-x-1">New Application</span>
                </router-link>
            </div>
            <hr>
            <!-- Filter section -->
            <transition enter-active-class="transition-all duration-300 ease-out"
                        leave-active-class="transition-all duration-200 ease-in"
                        enter-from-class="opacity-0 -translate-y-2"
                        leave-to-class="opacity-0 -translate-y-2">
                <div class="bg-white mb-3 flex justify-start items-center gap-3 mt-4">
                    <input v-model="searchQuery" type="text" placeholder="Search by Applicant Name, Missal No or Identity Number ..."
                           class="border px-3 py-2 rounded md:w-[450px]" @keydown.enter="fetchApplications" />
                    <div class="buttons-wrapper flex justify-between items-center gap-2">
                        <button @click="handleDraftSearching"
                                class="flex items-center gap-2 bg-transparent text-primary hover:bg-primary hover:text-white border border-primary  px-4 py-2 rounded-lg">
                            <i class="fa fa-search"></i>
                            Search
                        </button>
                        <button @click="clearFilters"
                                class="flex items-center gap-2 bg-transparent text-primary hover:bg-primary hover:text-white border border-primary px-4 py-2 rounded-lg transition">
                            <i class="fa fa-rotate-left"></i>
                            Reset
                        </button>
                    </div>
                </div>
            </transition>

            <!-- Table -->
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="min-w-full border text-sm">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-2 text-center">Draft ID</th>
                        <th class="border px-3 py-2 text-center">Missal No</th>
                        <th class="border px-3 py-2 text-center">Service Name</th>
                        <th class="border px-3 py-2 text-center">Type</th>
                        <th class="border px-3 py-2 text-center">Applicant Full Name</th>
                        <th class="border px-3 py-2 text-center">CNIC / Refugee No</th>
                        <th class="border px-3 py-2 text-center">Current Status</th>
                        <th class="border px-3 py-2 text-center">Created On</th>
                        <th class="border px-3 py-2 text-center">Action</th>
                    </tr>
                    </thead>

                    <tbody>
                    <!-- Loading State -->
                    <tr v-if="loading">
                        <td colspan="9" class="border px-3 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <i class="fa fa-spinner fa-spin text-blue-600"></i>
                                <span>Loading drafted applications...</span>
                            </div>
                        </td>
                    </tr>

                    <!-- No Data State -->
                    <tr v-else-if="applications.length === 0">
                        <td colspan="9" class="border px-3 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center gap-2">
                                <i class="fa fa-folder-open text-3xl text-gray-400"></i>
                                <span>No drafted applications found</span>
                            </div>
                        </td>
                    </tr>

                    <!-- Drafted Applications -->
                    <tr v-else v-for="(app, index) in applications" :key="index" class="hover:bg-gray-50">
                        <td class="border px-3 py-2 text-center">{{ getLastPartOfDraftID(app.id) || 'N/A' }}</td>
                        <td class="border px-3 py-2 text-center">{{ app.missalNo || '-' }}</td>
                        <td class="border px-3 py-2 text-center">
                            <Badge v-if="app.certificateType === 'state'" variant="blue" class="px-2 py-1">
                                <span class="text-nowrap">{{ readableService(app.certificateType) }}</span>
                            </Badge>
                            <Badge v-if="app.certificateType === 'domicile'" variant="green" class="px-2 py-1">
                                <span class="text-nowrap">{{ readableService(app.certificateType) }}</span>
                            </Badge>
                            <Badge v-if="app.certificateType === 'both'" variant="yellow" class="px-2 py-1">
                                <span class="text-nowrap">{{ readableService(app.certificateType) }}</span>
                            </Badge>
                        </td>
                        <td class="border px-3 py-2 text-center">
                            <Badge v-if="readableType(app.applicationType) === 'New'" variant="purple-light"
                                   class="px-2 py-1">
                                New
                            </Badge>
                            <Badge v-else-if="readableType(app.applicationType) === 'Duplicate'" variant="red-light"
                                   class="px-2 py-1">
                                Duplicate
                            </Badge>
                            <Badge v-else variant="gray" class="px-2 py-1">
                                {{ readableType(app.applicationType) }}
                            </Badge>
                        </td>
                        <td class="border px-3 py-2 text-center font-nastaleeq">{{ app.applicantName }}</td>
                        <td class="border px-3 py-2 text-center whitespace-nowrap">
                            {{ app.identityType === 'local' ? formatCnic(app.identityNumber.trim()) : app.identityNumber.trim()
                            }}
                        </td>
                        <td class="border px-3 py-2 capitalize text-center">
                            <Badge variant="gray" class="px-2 py-1">
                                Draft
                            </Badge>
                        </td>
                        <td class="border px-2 py-2 text-center">
                            <div class="flex flex-col items-center gap-1 text-xs text-muted-foreground">
                                <span>{{ formatDMY(app.createdAt) }}</span>
                                <span>{{ formatTime(app.createdAt) }}</span>
                            </div>
                        </td>
                        <td class="border px-2 py-2 text-center">
                            <button @click="toggleDropdown(app, $event)"
                                    class="dropdown-btn inline-flex items-center justify-center text-center w-8 h-8 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded transition"
                                    title="More Actions">
                                <i class="fa fa-ellipsis-v"></i>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <!-- Global Dropdown Menu -->
            <Teleport to="body">
                <div v-if="openDropdownId !== null"
                     class="absolute bg-white border border-gray-200 rounded shadow-xl z-[9999] w-48"
                     :style="dropdownPosition" @click.stop>

                    <!-- Edit Link -->
                    <router-link
                        v-if="store.user?.role === 'DEO' || store.user?.role === 'Center In-charge'"
                        :to="{ name: 'applications.edit-draft', params: { draftId: selectedApp?.id } }"
                        class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 border-b transition"
                        @click="openDropdownId = null">
                        <i class="fa fa-edit text-blue-600 w-4"></i>
                        <span>Edit Application</span>
                    </router-link>

                    <!-- Remove Draft Button -->
                    <button
                        v-if="store.user?.role === 'DEO' || store.user?.role === 'Center In-charge'"
                        @click="confirmRemoveDraft"
                        class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-red-50 w-full text-left transition">
                        <i class="fa fa-trash text-red-600 w-4"></i>
                        <span>Remove from Drafts</span>
                    </button>
                </div>
            </Teleport>
        </div>

        <!-- Confirmation Dialog -->
        <BaseDialog
            v-model="showConfirmDialog"
            title="Remove Draft"
            max-width="max-w-md">
            <div class="text-center py-4">
                <p class="text-sm text-red-600">
                    <i class="fa fa-info-circle"></i>
                    Are you sure you want to remove this draft? This action cannot be undone.
                </p>
            </div>
            <template #footer>
                <div class="flex justify-end gap-3">
                    <button
                        @click="showConfirmDialog = false"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button
                        @click="handleRemoveDraft"
                        :disabled="removing"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                        <i v-if="removing" class="fa fa-spinner fa-spin"></i>
                        <i v-else class="fa fa-trash"></i>
                        {{ removing ? 'Removing...' : 'Yes, Remove Draft' }}
                    </button>
                </div>
            </template>
        </BaseDialog>
    </div>
</template>

<script setup>
    import { onMounted, ref, computed, watch } from 'vue';
    import { useApplicationForm } from '@/composables/useApplicationForm.js';
    import { useAppStore } from '@/stores/index';
    import Badge from '@/components/Badge.vue';
    import BaseDialog from '@/components/BaseDialog.vue';
    import { formatCnic, formatDMY, formatTime } from '@/mixin/index.ts';

    const dbManager = useApplicationForm();
    const store = useAppStore();

    const loading = ref(false);
    const allApplications = ref([]); // Store all applications for filtering
    const applications = ref([]); // Filtered applications to display
    const openDropdownId = ref(null);
    const selectedApp = ref(null);
    const dropdownPosition = ref({ top: '0px', left: '0px' });
    const showConfirmDialog = ref(false);
    const removing = ref(false);

    const filterBy = ref('');
    const searchQuery = ref('');

    // Fetch all applications
    const fetchApplications = async () => {
        loading.value = true;
        try {
            const drafts = await dbManager.getAllDrafts();
            // Sort by createdAt in descending order (newest first)
            allApplications.value = drafts.sort((a, b) => {
                return new Date(b.createdAt) - new Date(a.createdAt);
            });
            applications.value = [...allApplications.value];
            console.log('response:', applications.value);
        } catch (error) {
            console.error('Error fetching applications:', error);
        } finally {
            loading.value = false;
        }
    };

    // Search function
    const handleDraftSearching = async () => {
        loading.value = true;

        // Simulate a small delay for better UX (optional)
        await new Promise(resolve => setTimeout(resolve, 300));

        try {
            if (!searchQuery.value || searchQuery.value.trim() === '') {
                // If search query is empty, show all applications
                applications.value = [...allApplications.value];
            } else {
                const query = searchQuery.value.toLowerCase().trim();

                // Filter applications based on search criteria
                const filtered = allApplications.value.filter(app => {
                    // Search by Applicant Name
                    if (app.applicantName && app.applicantName.toLowerCase().includes(query)) {
                        return true;
                    }

                    // Search by Missal No
                    if (app.missalNo && app.missalNo.toString().toLowerCase().includes(query)) {
                        return true;
                    }

                    // Search by Identity Number (CNIC/Refugee No)
                    if (app.identityNumber) {
                        const identityNumber = app.identityNumber.trim();
                        // For local CNIC, search with and without dashes
                        if (app.identityType === 'local') {
                            const originalFormat = identityNumber;
                            const noDashFormat = identityNumber.replace(/-/g, '');
                            if (originalFormat.toLowerCase().includes(query) ||
                                noDashFormat.toLowerCase().includes(query)) {
                                return true;
                            }
                        } else {
                            // For refugee numbers
                            if (identityNumber.toLowerCase().includes(query)) {
                                return true;
                            }
                        }
                    }

                    // Optional: Search by Draft ID
                    const draftIdLastPart = getLastPartOfDraftID(app.id);
                    if (draftIdLastPart.toLowerCase().includes(query)) {
                        return true;
                    }

                    return false;
                });

                applications.value = filtered;

                // Optional: Show a message if no results found
                if (filtered.length === 0) {
                    console.log('No results found for:', query);
                }
            }
        } catch (error) {
            console.error('Error searching applications:', error);
            applications.value = [...allApplications.value];
        } finally {
            loading.value = false;
        }
    };

    const clearFilters = () => {
        filterBy.value = '';
        searchQuery.value = '';
        applications.value = [...allApplications.value];
    };

    const getLastPartOfDraftID = (draftID) => {
        if (!draftID) return 'N/A';
        return draftID.substring(draftID.lastIndexOf('_') + 1);
    };

    const removeDraft = async (draftId) => {
        if (!draftId) return;
        removing.value = true;
        try {
            await dbManager.clearDraft(draftId);
            dbManager.getDraftsCount().then(count => {
                store.updateDraftCount(count);
            });
            // Close the dropdown and dialog
            openDropdownId.value = null;
            selectedApp.value = null;
            showConfirmDialog.value = false;
            // Refresh the list
            await fetchApplications();
        } catch (error) {
            console.error('Error removing draft:', error);
        } finally {
            removing.value = false;
        }
    };

    // Show confirmation dialog
    const confirmRemoveDraft = () => {
        // Close the dropdown first
        openDropdownId.value = null;
        // Show confirmation dialog
        showConfirmDialog.value = true;
    };

    // Handler for the remove draft button in dialog
    const handleRemoveDraft = () => {
        if (selectedApp.value && selectedApp.value.id) {
            removeDraft(selectedApp.value.id);
        }
    };

    // Debounced search for real-time searching (optional)
    let searchTimeout;
    const debouncedSearch = () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            handleDraftSearching();
        }, 500);
    };

    // Optional: Auto-search when typing (uncomment if you want real-time search)
    watch(searchQuery, () => {
        debouncedSearch();
    });

    onMounted(() => {
        if (store.isAuthenticated) {
            fetchApplications();
            store.loadDropdowns();
        }
    });

    const toggleDropdown = (app, event) => {
        if (openDropdownId.value === app.id) {
            openDropdownId.value = null;
            selectedApp.value = null;
            return;
        }

        openDropdownId.value = app.id;
        selectedApp.value = app;

        // Calculate the position of the dropdown
        const button = event.target.closest('button');
        if (button) {
            const rect = button.getBoundingClientRect();
            dropdownPosition.value = {
                top: (rect.bottom + window.scrollY + 8) + 'px',
                left: (rect.right + window.scrollX - 200) + 'px'
            };
        }

        // Close dropdown when clicking outside
        setTimeout(() => {
            const closeHandler = (e) => {
                if (!e.target.closest('.dropdown-btn') && !e.target.closest('.absolute.bg-white')) {
                    openDropdownId.value = null;
                    selectedApp.value = null;
                    document.removeEventListener('click', closeHandler);
                }
            };
            document.addEventListener('click', closeHandler);
        }, 0);
    };

    // Readable fields
    const readableService = (type) => {
        if (type === 'domicile') return 'Domicile Certificate';
        if (type === 'state') return 'State Subject Certificate';
        if (type === 'both') return 'Domicile & State Subject';
        return '-';
    };

    const readableType = (typeId) => {
        if (typeId === 1) return 'New';
        if (typeId === 2) return 'Duplicate';
        if (typeId === 3) return 'Renewal';
        return '-';
    };

    // Optional: Show search results count
    const searchResultsCount = computed(() => {
        if (searchQuery.value && searchQuery.value.trim() !== '') {
            return applications.value.length;
        }
        return null;
    });
</script>

