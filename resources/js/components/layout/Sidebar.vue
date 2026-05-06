<template>
    <div>
        <nav
            class="sidebar fixed min-h-screen top-0 bottom-0 w-[260px] shadow-[5px_0_25px_0_rgba(94,92,154,0.1)] z-50 trion-all duration-300">
            <div class="bg-white dark:bg-[#0e1726] h-full">
                <div class="flex justify-between items-center px-4 py-3">
                    <router-link to="/app" class="main-logo flex items-center shrink-0">
                        <img class="w-8 ml-[5px] flex-none" :src="appLogo" alt=""/>
                        <span
                            class="text-2xl ltr:ml-1.5 rtl:mr-1.5 font-semibold align-middle lg:inline dark:text-white-light">EFC-AJK</span>
                    </router-link>
                    <a
                        href="javascript:;"
                        class="collapse-icon w-8 h-8 rounded-full flex items-center hover:bg-gray-500/10 dark:hover:bg-dark-light/10 dark:text-white-light transition duration-300 rtl:rotate-180 hover:text-primary"
                        @click="store.toggleSidebar()"
                    >
                        <icon-carets-down class="m-auto rotate-90"/>
                    </a>
                </div>

                <perfect-scrollbar
                    :options="{
                        swipeEasing: true,
                        wheelPropagation: false,
                    }"
                    class="h-[calc(100vh-100px)] relative"
                >
                    <ul class="relative font-semibold space-y-0.5 p-4 py-0">
                        <!-- Dashboard -->
                        <li class="menu nav-item" v-if="store.user?.permissions?.includes('dashboard.view')">
                            <router-link :to="{ name: 'dashboard.view' }" class="group" @click="toggleMobileMenu">
                                <div class="flex items-center">
                                    <i class="fa-solid fa-house-chimney-window fa-lg"></i>
                                    <span
                                        class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">
                                        {{ $t('dashboard') }}
                                    </span>
                                </div>
                            </router-link>
                        </li>

                        <li class="menu nav-item"
                            v-if="store.user?.permissions?.includes('applications.create') || store.user?.role === 'Center In-charge'">
                            <router-link :to="{ name: 'Quick Links' }" class="group" @click="toggleMobileMenu">
                                <div class="flex items-center">
                                    <i class="fa-solid fa-link fa-lg"></i>
                                    <span
                                        class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">
                                        {{ $t('quick-links') }}
                                    </span>
                                </div>
                            </router-link>
                        </li>

                        <!-- Online Applications -->
                        <li class="menu nav-item" v-if="store.user?.permissions?.includes('applications.view')">
                            <router-link :to="{ name: 'applications.view', params: { status: 'online' } }"
                                         class="group" @click="toggleMobileMenu">
                                <div class="flex items-center">
                                    <i class="fa-solid fa-globe-asia fa-lg"/>
                                    <span
                                        class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">{{
                                            $t('Online Applications')
                                        }}</span>
                                </div>
                            </router-link>
                        </li>

                        <li class="menu nav-item" v-if="store.user?.permissions?.includes('applications.create')">
                            <router-link :to="{ name: 'drafted-applications.view' }" class="group"
                                         @click="toggleMobileMenu">
                                <div class="flex items-center justify-between w-full">
                                    <div class="flex items-center">
                                        <i class="fa-solid fa-drafting-compass fa-lg"/>
                                        <span
                                            class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">
                                            {{ $t('drafted-applications') }}
                                        </span>
                                    </div>

                                    <span v-if="store.draftCount>0" class="ml-auto px-2 py-0.5 text-xs font-medium rounded-full
                                             bg-gray-200 text-gray-700
                                             dark:bg-gray-700 dark:text-gray-300
                                             group-[.active]:bg-white/20 group-[.active]:text-white
                                             dark:group-[.active]:bg-white/20 dark:group-[.active]:text-white
                                             group-hover:bg-gray-300 dark:group-hover:bg-gray-600">
                                    {{ store.draftCount }}
                                </span>
                                </div>
                            </router-link>
                        </li>

                        <!-- All Applications -->
                        <li class="menu nav-item" v-if="store.user?.permissions?.includes('applications-all.view')">
                            <router-link :to="{ name: 'applications.view', params: { status: 'all' } }" class="group"
                                         @click="toggleMobileMenu">
                                <div class="flex items-center">
                                    <i class="fa-solid fa-folder-open fa-lg"></i>
                                    <span
                                        class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">{{
                                            $t('Applications')
                                        }}</span>
                                </div>
                            </router-link>
                        </li>

                        <!-- Pending Applications -->
                        <li class="menu nav-item" v-if="store.user?.permissions?.includes('applications-pending.view')">
                            <router-link :to="{ name: 'applications.view', params: { status: 'pending' } }"
                                         class="group" @click="toggleMobileMenu">
                                <div class="flex items-center">
                                    <i class="fa-solid fa-clock fa-lg"></i>
                                    <span
                                        class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">{{
                                            $t('Pending Applications')
                                        }}</span>
                                </div>
                            </router-link>
                        </li>

                        <!-- Applications for Verification -->
                        <li class="menu nav-item"
                            v-if="store.user?.permissions?.includes('applications-for-verification.view')">
                            <router-link :to="{ name: 'applications.view', params: { status: 'submitted' } }"
                                         class="group" @click="toggleMobileMenu">
                                <div class="flex items-center">
                                    <i class="fa-solid fa-circle-check fa-lg"></i>
                                    <span
                                        class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">{{
                                            $t('Applications for Verification')
                                        }}</span>
                                </div>
                            </router-link>
                        </li>

                        <!-- Applications for Approval -->
                        <li class="menu nav-item"
                            v-if="store.user?.permissions?.includes('applications-for-approval.view')">
                            <router-link :to="{ name: 'applications.view', params: { status: 'verified' } }"
                                         class="group" @click="toggleMobileMenu">
                                <div class="flex items-center">
                                    <i class="fa-solid fa-square-check fa-lg"></i>
                                    <span
                                        class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">{{
                                            $t('Applications for Approval')
                                        }}</span>
                                </div>
                            </router-link>
                        </li>

                        <!-- Ready For Printing -->
                        <li class="menu nav-item"
                            v-if="store.user?.permissions?.includes('applications-for-printing.view')">
                            <router-link :to="{ name: 'applications.view', params: { status: 'approved' } }"
                                         class="group" @click="toggleMobileMenu">
                                <div class="flex items-center">
                                    <i class="fa-solid fa-print fa-lg"></i>
                                    <span
                                        class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">{{
                                            $t('Ready for Printing')
                                        }}</span>
                                </div>
                            </router-link>
                        </li>

                        <!-- Ready to Deliver -->
                        <li class="menu nav-item"
                            v-if="store.user?.permissions?.includes('applications-for-delivery.view')">
                            <router-link :to="{ name: 'applications.view', params: { status: 'ready_for_delivery' } }"
                                         class="group" @click="toggleMobileMenu">
                                <div class="flex items-center">
                                    <i class="fa-solid fa-truck-fast fa-lg"></i>
                                    <span
                                        class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">{{
                                            $t('Applications for Delivery')
                                        }}</span>
                                </div>
                            </router-link>
                        </li>

                        <!-- Delivered -->
                        <li class="menu nav-item"
                            v-if="store.user?.permissions?.includes('applications-delivered.view')">
                            <router-link :to="{ name: 'applications.view', params: { status: 'delivered' } }"
                                         class="nav-link group" @click="toggleMobileMenu">
                                <div class="flex items-center">
                                    <i class="fa-solid fa-box-archive fa-lg"></i>
                                    <span
                                        class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">{{
                                            $t('Delivered Applications')
                                        }}</span>
                                </div>
                            </router-link>
                        </li>

                        <!-- Objected -->
                        <li class="menu nav-item"
                            v-if="store.user?.permissions?.includes('applications-objected.view')">
                            <router-link :to="{ name: 'applications.view', params: { status: 'objected' } }"
                                         class="nav-link group" @click="toggleMobileMenu">
                                <div class="flex items-center">
                                    <i class="fa-solid fa-repeat fa-lg"></i>
                                    <span
                                        class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark"
                                    >{{ $t('Objected Applications') }}
                                    </span>
                                    <span v-if="dashboardCounts?.objected > 0" class="ml-3 relative flex h-2 w-2">
                                        <span
                                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-600 opacity-75"/>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-red-400"/>
                                    </span>
                                </div>
                            </router-link>
                        </li>

                        <!-- Setting -->
                        <li class="menu nav-item" v-if="hasAnySettingsPermission()">
                            <button
                                type="button"
                                class="nav-link group w-full"
                                :class="{ active: activeDropdown === 'settings' }"
                                @click="activeDropdown === 'settings' ? (activeDropdown = null) : (activeDropdown = 'settings')"
                            >
                                <span class="flex items-center">
                                    <i class="fa-solid fa-gear fa-lg"></i>
                                    <span
                                        class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">
                                        {{ $t('settings') }}
                                    </span>
                                </span>
                                <span :class="{ 'rtl:rotate-90 -rotate-90': activeDropdown !== 'settings' }">
                                    <icon-caret-down/>
                                </span>
                            </button>
                            <vue-collapsible :isOpen="activeDropdown === 'settings'">
                                <ul class="sub-menu text-gray-500">
                                    <!-- Employee Registration -->
                                    <li v-if="store.user?.permissions?.includes('users.view')">
                                        <router-link :to="{ name: 'users.view' }" class="group"
                                                     @click="toggleMobileMenu">
                                            {{ $t('user-management') }}
                                        </router-link>
                                    </li>

                                    <li v-if="store.user?.permissions?.includes('roles.view')">
                                        <router-link :to="{ name: 'roles.view' }" @click="toggleMobileMenu">
                                            {{ $t('roles-permissions') }}
                                        </router-link>
                                    </li>
                                    <li v-if="store.user?.permissions?.includes('departments.view')">
                                        <router-link :to="{ name: 'departments.view' }" @click="toggleMobileMenu">
                                            {{ $t('Departments') }}
                                        </router-link>
                                    </li>
                                    <li v-if="store.user?.permissions?.includes('centers.view')">
                                        <router-link :to="{ name: 'centers.view' }" @click="toggleMobileMenu">
                                            {{ $t('Centers') }}
                                        </router-link>
                                    </li>
                                    <li v-if="store.user?.permissions?.includes('services.view')">
                                        <router-link :to="{ name: 'services.view' }" @click="toggleMobileMenu">
                                            {{ $t('Services') }}
                                        </router-link>
                                    </li>
                                    <li v-if="store.user?.permissions?.includes('services-centers.view')">
                                        <router-link :to="{ name: 'services-centers.view' }" @click="toggleMobileMenu">
                                            {{ $t('assign-services') }}
                                        </router-link>
                                    </li>
                                    <li v-if="store.user?.permissions?.includes('service-instructions.view')">
                                        <router-link :to="{ name: 'service-instructions.view' }"
                                                     @click="toggleMobileMenu"
                                        >{{ $t('Service Instruction') }}
                                        </router-link>
                                    </li>
                                    <li v-if="store.user?.permissions?.includes('templates.view')">
                                        <router-link :to="{ name: 'templates.view' }"
                                                     @click="toggleMobileMenu"
                                        >{{ $t('Templates') }}
                                        </router-link>
                                    </li>
                                    <li v-if="store.user?.permissions?.includes('demographies.view')">
                                        <a href="javascript:;" @click="demographyMenuOpen = !demographyMenuOpen">
                                            <span class="ltr:pr-3 rtl:pl-3">{{ $t('Demography') }}</span>
                                            <i :class="demographyMenuOpen ? 'fa fa-chevron-down' : 'fa fa-chevron-right'"
                                               class="text-gray-500 text-xs"></i>
                                        </a>

                                        <ul v-show="demographyMenuOpen" class="pl-8 mt-1 space-y-1">
                                            <li>
                                                <router-link
                                                    :to="{ name: 'demographies.view', params: { type: 'COUNTRY' } }">
                                                    {{ $t('Country') }}
                                                </router-link>
                                            </li>
                                            <li>
                                                <router-link
                                                    :to="{ name: 'demographies.view', params: { type: 'REGION' } }">
                                                    {{ $t('Region') }}
                                                </router-link>
                                            </li>
                                            <li>
                                                <router-link
                                                    :to="{ name: 'demographies.view', params: { type: 'DISTRICT' } }">
                                                    {{ $t('District') }}
                                                </router-link>
                                            </li>
                                            <li>
                                                <router-link
                                                    :to="{ name: 'demographies.view', params: { type: 'TEHSIL' } }">
                                                    {{ $t('Tehsil') }}
                                                </router-link>
                                            </li>
                                            <li>
                                                <router-link
                                                    :to="{ name: 'demographies.view', params: { type: 'CITY' } }">
                                                    {{ $t('City') }}
                                                </router-link>
                                            </li>
                                            <li>
                                                <router-link
                                                    :to="{ name: 'demographies.view', params: { type: 'UNION_COUNCIL' } }">
                                                    {{ $t('UC') }}
                                                </router-link>
                                            </li>
                                        </ul>
                                    </li>
                                    <li v-if="store.user?.permissions?.includes('types.view')">
                                        <router-link :to="{ name: 'types.view', params: { type: 'group' } }"
                                                     @click="toggleMobileMenu"
                                        >{{ $t('Dropdown Groups') }}
                                        </router-link>
                                    </li>
                                    <li v-if="store.user?.permissions?.includes('types.view')">
                                        <router-link :to="{ name: 'types.view', params: { type: 'item' } }"
                                                     @click="toggleMobileMenu"
                                        >{{ $t('Dropdown Items') }}
                                        </router-link>
                                    </li>
                                    <li v-if="store.user?.permissions?.includes('required-documents.view')">
                                        <router-link
                                            :to="{ name: 'required-documents.view', params: { type: 'group' } }"
                                            @click="toggleMobileMenu">
                                            {{ $t('required-documents') }}
                                        </router-link>
                                    </li>

                                    <li
                                        v-if="
                                            store.user?.permissions?.includes('backups.view') && store.user?.roles?.some((role) => role.name === 'Super Admin')"
                                    >
                                        <router-link :to="{ name: 'backups.view', params: { type: 'group' } }"
                                                     @click="toggleMobileMenu">{{ $t('backups') }}
                                        </router-link>
                                    </li>
                                    <li
                                        v-if="
                                            store.user?.permissions?.includes('schedule-jobs.view') &&
                                            store.user?.roles?.some((role) => role.name === 'Super Admin')
                                        "
                                    >
                                        <router-link :to="{ name: 'schedule-jobs.view' }" @click="toggleMobileMenu">
                                            {{ $t('Schedule-Jobs') }}
                                        </router-link>
                                    </li>
                                    <li
                                        v-if="
                                            store.user?.permissions?.includes('api-tokens.view') &&
                                            store.user?.roles?.some((role) => role.name === 'Super Admin')
                                        "
                                    >
                                        <router-link :to="{ name: 'api-tokens.view' }" @click="toggleMobileMenu">
                                            {{ $t('API-Tokens') }}
                                        </router-link>
                                    </li>
                                </ul>
                            </vue-collapsible>
                        </li>

                        <!-- Archived -->
                        <!-- Dashboard Link -->
                        <li class="menu nav-item"
                            v-if="store.user?.role === 'Supervisor' || store.user?.role === 'Scanner'">
                            <router-link :to="{ name: 'dashboard.view' }" class="group" @click="toggleMobileMenu">
                                <div class="flex items-center">
                                    <!-- Styling same as other dashboard links -->
                                    <i class="fa-solid fa-house-chimney-window fa-lg text-primary"></i>
                                    <span
                                        class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">
                                        {{ $t('dashboard') }}
                                    </span>
                                </div>
                            </router-link>
                        </li>
                        <li
                            class="menu nav-item"
                            v-if="store.user?.role === 'Super Admin' || store.user?.role === 'Supervisor' || store.user?.role === 'Scanner'"
                        >
                            <button
                                type="button"
                                class="nav-link group w-full"
                                :class="{ active: activeDropdown === 'scanning' }"
                                @click="activeDropdown === 'scanning' ? (activeDropdown = null) : (activeDropdown = 'scanning')"
                            >
                                <span class="flex items-center">
                                    <i class="fa-solid fa-toolbox fa-lg"></i>
                                    <span
                                        class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">
                                        {{ $t('Scanning') }}
                                    </span>
                                </span>
                                <span :class="{ 'rtl:rotate-90 -rotate-90': activeDropdown !== 'scanning' }">
                                    <icon-caret-down/>
                                </span>
                            </button>

                            <vue-collapsible :isOpen="activeDropdown === 'scanning'">
                                <ul class="sub-menu text-gray-500">
                                    <!-- Archived Scanning Form -->
                                    <li v-if="hasPermission('archived-scanner.view')">
                                        <router-link to="/app/archived/scanning-form/all" @click="toggleMobileMenu"
                                                     class="flex items-center">
                                            <span>{{ $t('Scanning-Form') }}</span>
                                            <span v-if="dashboardCounts?.objected > 0" class="flex h-2 w-2 relative">
                                                <span
                                                    class="animate-ping absolute h-full w-full rounded-full bg-red-600 opacity-75"></span>
                                                <span
                                                    class="relative inline-flex rounded-full h-2 w-2 bg-red-400"></span>
                                            </span>
                                        </router-link>
                                    </li>

                                    <!-- Archived Verification Form -->
                                    <li v-if="hasPermission('archived-verification.view')">
                                        <router-link to="/app/archived/verification-form/all" @click="toggleMobileMenu"
                                                     class="flex items-center">
                                            <span>{{ $t('Verification-Form') }}</span>
                                            <span v-if="dashboardCounts?.objected > 0" class="flex h-2 w-2 relative">
                                                <span
                                                    class="animate-ping absolute h-full w-full rounded-full bg-red-600 opacity-75"></span>
                                                <span
                                                    class="relative inline-flex rounded-full h-2 w-2 bg-red-400"></span>
                                            </span>
                                        </router-link>
                                    </li>
                                    <li v-if="store.user?.role !== 'Scanner'">
                                        <router-link to="/app/archivedReport" @click="toggleMobileMenu"
                                                     class="flex items-center">
                                            <span>{{ $t('Archived-Report') }}</span>
                                            <!-- Red notification dot -->
                                            <span v-if="dashboardCounts?.archivedReport > 0"
                                                  class="flex h-2 w-2 relative">
                                                <span
                                                    class="animate-ping absolute h-full w-full rounded-full bg-red-600 opacity-75"></span>
                                                <span
                                                    class="relative inline-flex rounded-full h-2 w-2 bg-red-400"></span>
                                            </span>
                                        </router-link>
                                    </li>
                                </ul>
                            </vue-collapsible>
                        </li>
                    </ul>
                </perfect-scrollbar>
                <div class="w-full px-4 flex justify-start items-center">
                    <button
                        class="w-full flex items-center gap-2 bg-gray-300 hover:bg-gray-500 hover:text-white border px-4 py-2 rounded-lg transition"
                        @click="getFreshDropdownData"
                        :disabled="isLoading"
                    >
                        <span class="flex items-center justify-center gap-2">
                            <template v-if="!isLoading">
                                <i class="fa-solid fa-refresh"/> <span>Refresh local data</span>
                            </template>
                            <template v-else>
                                <p class=""><i class="fa fa-refresh fa-spin"/> Loading data...</p>
                            </template>
                        </span>
                    </button>
                </div>
            </div>
        </nav>
    </div>
</template>

<script setup>
import {onMounted, ref, watch} from 'vue';
import {useRoute, useRouter} from 'vue-router';
import axios from 'axios';

import {useAppStore} from '@/stores';
import VueCollapsible from 'vue-height-collapsible/vue3';
import {useApplicationForm} from '@/composables/useApplicationForm.js';
import IconCaretsDown from '@/components/icon/icon-carets-down.vue';
import IconCaretDown from '@/components/icon/icon-caret-down.vue';
import {storeToRefs} from 'pinia';
import appLogo from '@/assets/images/logo.png';


const store = useAppStore();
const router = useRouter();
const route = useRoute();
const {isLoading} = storeToRefs(store);
const activeDropdown = ref(null);
const dashboardCounts = ref(null);
const demographyMenuOpen = ref(false);
const dbManager = useApplicationForm();
//Update the application form count in the store
dbManager.getDraftsCount().then(count => {
    store.updateDraftCount(count);
});
const hasAnySettingsPermission = () => {
    const settingsPermissions = [
        'users.view',
        'roles.view',
        'departments.view',
        'centers.view',
        'services.view',
        'services-centers.view',
        'service-instructions.view',
        'demographies.view',
        'templates.view',
        'types.view',
        'required-documents.view',
        'backups.view',
    ];

    return settingsPermissions.some((permission) => store.user?.permissions?.includes(permission));
};

const fetchDashboardCounts = async () => {
    const res = await axios.get('/api/dashboard-counts');
    dashboardCounts.value = res.data;
};

const getFreshDropdownData = async () => {
    await store.loadDropdowns(true); // force refresh
};

// Function to check if current route belongs to settings section
const isSettingsRoute = () => {
    const settingsRoutes = [
        'users.view',
        'roles.view',
        'departments.view',
        'centers.view',
        'services.view',
        'services-centers.view',
        'service-instructions.view',
        'demographies.view',
        'templates.view',
        'types.view',
        'required-documents.view',
        'backups.view',
        'schedule-jobs.view',
        'api-tokens.view',
    ];
    return settingsRoutes.includes(route.name);
};

// Function to check if current route belongs to scanning section
const isScanningRoute = () => {
    const scanningRoutes = ['archived-scanner.view', 'archived-verification.view'];
    // Check if route path includes '/archived/'
    return scanningRoutes.includes(route.name) || route.path?.includes('/archived/');
};

const isDemographyRoute = () => {
    return route.name === 'demographies.view';
};

// Watch for route changes to close dropdowns
watch(
    () => route.path,
    (newPath, oldPath) => {
        // Close all dropdowns when route changes
        if (!isSettingsRoute()) {
            activeDropdown.value = null;
        }

        // Optionally, you can keep the dropdown open if still in the same section
        if (isSettingsRoute()) {
            activeDropdown.value = 'settings';
        } else if (isScanningRoute()) {
            activeDropdown.value = 'scanning';
        } else {
            activeDropdown.value = null;
        }

        // keep demography menu open if user is on demography page
        if (isDemographyRoute()) {
            demographyMenuOpen.value = true;
        } else {
            demographyMenuOpen.value = false;
        }

    });

// Also watch for route name changes
watch(
    () => route.name,
    () => {
        if (!isSettingsRoute()) {
            activeDropdown.value = null;
        }

        if (isSettingsRoute()) {
            activeDropdown.value = 'settings';
        } else if (isScanningRoute()) {
            activeDropdown.value = 'scanning';
        } else {
            activeDropdown.value = null;
        }

        if (isDemographyRoute()) {
            demographyMenuOpen.value = true;
        }
    });

onMounted(() => {
    fetchDashboardCounts();

    // Set initial active dropdown based on current route
    if (isSettingsRoute()) {
        activeDropdown.value = 'settings';
    } else if (isScanningRoute()) {
        activeDropdown.value = 'scanning';
    }
    if (isDemographyRoute()) {
        demographyMenuOpen.value = true;
    }

    const selector = document.querySelector('.sidebar ul a[href="' + window.location.pathname + '"]');
    if (selector) {
        selector.classList.add('active');
        const ul = selector.closest('ul.sub-menu');
        if (ul) {
            let ele = ul.closest('li.menu').querySelectorAll('.nav-link') || [];
            if (ele.length) {
                ele = ele[0];
                setTimeout(() => {
                    ele.click();
                });
            }
        }
    }
});

const toggleMobileMenu = () => {
    if (window.innerWidth < 1024) {
        store.toggleSidebar();
    }
};

const hasPermission = (permissionName) => {
    if (store.user?.role_name === 'Super Admin') return true;
    const userPermissions = store.user?.permissions || [];
    return userPermissions.includes(permissionName.toLowerCase());
};
</script>
