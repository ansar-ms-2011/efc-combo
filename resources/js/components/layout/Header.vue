<template>
    <header class="z-10" :class="{ dark: store.semidark && store.menu === 'horizontal' }">
        <div class="shadow-sm">
            <div class="relative bg-white flex w-full items-center px-5 py-2.5 dark:bg-[#0e1726]">
                <div class="horizontal-logo flex lg:hidden justify-between items-center ltr:mr-2 rtl:ml-2">
                    <router-link to="/app" class="main-logo flex items-center shrink-0">
                        <img class="w-8 ltr:-ml-1 rtl:-mr-1 inline" :src="appLogo" alt="" />
                        <span
                            class="text-2xl ltr:ml-1.5 rtl:mr-1.5 font-semibold align-middle hidden md:inline dark:text-white-light transition-all duration-300">EFC-AJK</span>
                    </router-link>

                    <a href="javascript:;"
                       class="collapse-icon flex-none dark:text-[#d0d2d6] hover:text-primary dark:hover:text-primary flex lg:hidden ltr:ml-2 rtl:mr-2 p-2 rounded-full bg-white-light/40 dark:bg-dark/40 hover:bg-white-light/90 dark:hover:bg-dark/60"
                       @click="store.toggleSidebar()">
                        <icon-menu class="w-5 h-5" />
                    </a>
                </div>
                <div
                    class="sm:flex-1 ltr:sm:ml-0 ltr:ml-auto sm:rtl:mr-0 rtl:mr-auto flex items-center space-x-1.5 lg:space-x-2 rtl:space-x-reverse dark:text-[#d0d2d6] relative">
                    <div class="sm:ltr:mr-auto sm:rtl:ml-auto">
                        <form
                            class="sm:relative absolute inset-x-0 sm:top-0 top-1/2 sm:translate-y-0 -translate-y-1/2 sm:mx-0 mx-4 z-10 sm:block hidden"
                            :class="{ '!block': search }" @submit.prevent="search = false">
                        </form>

                        <button type="button"
                                class="search_btn sm:hidden p-2 rounded-full bg-white-light/40 dark:bg-dark/40 hover:bg-white-light/90 dark:hover:bg-dark/60"
                                @click="search = !search">
                            <icon-search class="w-4.5 h-4.5 mx-auto dark:text-[#d0d2d6]" />
                        </button>
                    </div>
                    <div class="absolute left-1/2 -translate-x-1/2">
                        <h3 class="font-bold">{{ store.user ? 'Welcome, ' + store.user?.name : '' }}</h3>
                    </div>

                    <div class="flex justify-center items-center">
                        <template v-if="store.monitoring">
                            <IconOnline class="cursor-pointer" v-if="store?.isOnline" @click="toggleNetworkMonitoring"
                                        title="Network is being monitored" />
                            <IconOffline class="cursor-pointer" v-else @click="toggleNetworkMonitoring"
                                         title="Network is disconnected" />
                        </template>
                        <template v-else>
                            <IconMonitoringStopped class="cursor-pointer" @click="toggleNetworkMonitoring"
                                                   title="Monitoring of network/server connectivity is stopped, click to start it." />
                        </template>
                    </div>

                    <div class="dropdown shrink-0">
                        <Popper :placement="store.rtlClass === 'rtl' ? 'bottom-end' : 'bottom-start'" offsetDistance="8"
                                class="align-middle">
                            <button type="button"
                                    class="relative block p-2 rounded-full bg-white-light/40 dark:bg-dark/40 hover:text-primary hover:bg-white-light/90 dark:hover:bg-dark/60">
                                <icon-bell-bing />

                                <span class="flex absolute w-3 h-3 ltr:right-0 rtl:left-0 top-0">
                                    <span
                                        class="animate-ping absolute ltr:-left-[3px] rtl:-right-[3px] -top-[3px] inline-flex h-full w-full rounded-full bg-success/50 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full w-[6px] h-[6px] bg-success"></span>
                                </span>
                            </button>
                            <template #content="{ close }">
                                <ul
                                    class="!py-0 text-dark dark:text-white-dark w-[300px] sm:w-[350px] divide-y dark:divide-white/10">
                                    <li>
                                        <div class="flex items-center px-4 py-2 justify-between font-semibold">
                                            <h4 class="text-lg">Notification</h4>
                                            <template v-if="notifications.length">
                                                <span class="badge bg-primary/80"
                                                      v-text="notifications.length + 'New'"></span>
                                            </template>
                                        </div>
                                    </li>
                                    <template v-for="notification in notifications" :key="notification.id">
                                        <li class="dark:text-white-light/90">
                                            <div class="group flex items-center px-4 py-2">
                                                <div class="grid place-content-center rounded">
                                                    <div class="w-12 h-12 relative">
                                                        <img class="w-12 h-12 rounded-full object-cover"
                                                             :src="`/assets/images/${notification.profile}`" alt="" />
                                                        <span
                                                            class="bg-success w-2 h-2 rounded-full block absolute right-[6px] bottom-0"></span>
                                                    </div>
                                                </div>
                                                <div class="ltr:pl-3 rtl:pr-3 flex flex-auto">
                                                    <div class="ltr:pr-3 rtl:pl-3">
                                                        <h6 v-html="notification.message"></h6>
                                                        <span class="text-xs block font-normal dark:text-gray-500"
                                                              v-text="notification.time"></span>
                                                    </div>
                                                    <button type="button"
                                                            class="ltr:ml-auto rtl:mr-auto text-neutral-300 hover:text-danger opacity-0 group-hover:opacity-100"
                                                            @click="removeNotification(notification.id)">
                                                        <icon-x-circle />
                                                    </button>
                                                </div>
                                            </div>
                                        </li>
                                    </template>
                                    <template v-if="notifications.length">
                                        <li>
                                            <div class="p-4">
                                                <button class="btn btn-primary block w-full btn-small"
                                                        @click="close()">Read All Notifications
                                                </button>
                                            </div>
                                        </li>
                                    </template>
                                    <template v-if="!notifications.length">
                                        <li>
                                            <div
                                                class="!grid place-content-center hover:!bg-transparent text-lg min-h-[200px]">
                                                <div
                                                    class="mx-auto ring-4 ring-primary/30 rounded-full mb-4 text-primary">
                                                    <icon-info-circle :fill="true" class="w-10 h-10" />
                                                </div>
                                                No data available.
                                            </div>
                                        </li>
                                    </template>
                                </ul>
                            </template>
                        </Popper>
                    </div>

                    <div class="dropdown shrink-0">
                        <Popper :placement="store.rtlClass === 'rtl' ? 'bottom-end' : 'bottom-start'" offsetDistance="8"
                                class="!block align-middle">
                            <button type="button" class="relative group block">
                                <img class="w-9 h-9 rounded-full object-cover saturate-50 group-hover:saturate-100"
                                     :src="userProfileImage" alt="User Profile" />
                            </button>
                            <template #content="{ close }">
                                <ul
                                    class="text-dark dark:text-white-dark !py-0 w-[230px] font-semibold dark:text-white-light/90">
                                    <li>
                                        <div class="flex items-center px-4 py-4">
                                            <div class="flex-none">
                                                <img class="rounded-md w-10 h-10 object-cover"
                                                     :src="userProfileImage" alt="User Profile" />
                                            </div>
                                            <div class="ltr:pl-4 rtl:pr-4 truncate">
                                                <h5 class="text-base">
                                                    {{ store.user?.name }}
                                                </h5>
                                                <p>
                                                    <span
                                                        class="text-sm bg-success-light rounded text-success px-1 ltr:ml-2 rtl:ml-2">
                                                        {{ store.user?.roles?.[0]?.name }}
                                                    </span>
                                                </p>

                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <router-link to="/app/profile" class="dark:hover:text-white" @click="close()">
                                            <icon-user class="w-4.5 h-4.5 ltr:mr-2 rtl:ml-2 shrink-0" />
                                            Profile
                                        </router-link>
                                    </li>
                                    <li>
                                        <router-link to="/app/enable-two-factor" class="dark:hover:text-white" @click="close()">
                                            <icon-lock-dots class="w-4.5 h-4.5 ltr:mr-2 rtl:ml-2 shrink-0" />
                                            2F Authentication
                                        </router-link>
                                    </li>
                                    <li>
                                        <router-link to="/app/change-password" class="dark:hover:text-white" @click="close()">
                                            <icon-lock-dots class="w-4.5 h-4.5 ltr:mr-2 rtl:ml-2 shrink-0" />
                                            Change Password
                                        </router-link>
                                    </li>
                                    <li class="border-t border-white-light dark:border-white-light/10">
                                        <button :disabled="isLoggingOut" class="w-full text-left dark:hover:text-white" @click="logout()">
                                            <icon-logout class="w-4.5 h-4.5 ltr:mr-2 rtl:ml-2 rotate-90 shrink-0" />
                                            Log Out
                                        </button>
                                    </li>
                                </ul>
                            </template>
                        </Popper>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>

<script lang="ts" setup>
    import { ref, onMounted, computed, reactive, watch, nextTick } from 'vue';
    import { useI18n } from 'vue-i18n';
    import appSetting from '@/app-setting';
    import apiClient from '@/services/axios';
    import { useRoute } from 'vue-router';
    import { useAppStore } from '@/stores';

    import IconMenu from '@/components/icon/icon-menu.vue';
    import IconSearch from '@/components/icon/icon-search.vue';
    import IconXCircle from '@/components/icon/icon-x-circle.vue';
    import IconInfoCircle from '@/components/icon/icon-info-circle.vue';
    import IconBellBing from '@/components/icon/icon-bell-bing.vue';
    import IconUser from '@/components/icon/icon-user.vue';
    import IconLockDots from '@/components/icon/icon-lock-dots.vue';
    import IconLogout from '@/components/icon/icon-logout.vue';
    import IconMenuDashboard from '@/components/icon/menu/icon-menu-dashboard.vue';
    import IconCaretDown from '@/components/icon/icon-caret-down.vue';
    import IconMenuApps from '@/components/icon/menu/icon-menu-apps.vue';
    import IconMenuComponents from '@/components/icon/menu/icon-menu-components.vue';
    import IconMenuElements from '@/components/icon/menu/icon-menu-elements.vue';
    import IconMenuDatatables from '@/components/icon/menu/icon-menu-datatables.vue';
    import IconMenuForms from '@/components/icon/menu/icon-menu-forms.vue';
    import IconMenuPages from '@/components/icon/menu/icon-menu-pages.vue';
    import IconMenuMore from '@/components/icon/menu/icon-menu-more.vue';
    import IconOnline from '@/components/icon/icon-online.vue';
    import IconOffline from '@/components/icon/icon-offline.vue';
    import IconMonitoringStopped from '@/components/icon/icon-monitoring-stopped.vue';
    import router from '@/router';
    import appLogo from '@/assets/images/logo.png';
    const store = useAppStore();
    const route = useRoute();
    const search = ref(false);
    const i18n = reactive(useI18n());
    const isLoggingOut = ref(false);


    const changeLanguage = (item: any) => {
        i18n.locale = item.code;
        appSetting.toggleLanguage(item);
    };
    const currentFlag = computed(() => {
        return `/assets/images/flags/${i18n.locale.toUpperCase()}.svg`;
    });

   const triggerGlobalSearch = () => {
    if (store.globalSearchQuery && store.globalSearchQuery.trim() !== '') {
        router.push({
            path: '/applications/all',
            query: { search: store.globalSearchQuery }
        });
    }
};

watch(() => store.globalSearchQuery, (val) => {
    if (val && val.trim() !== '') {
        router.push({ path: '/applications/all', query: { search: val } });
    }
});


const clearSearch = () => {
    store.globalSearchQuery = '';

    router.replace({ path: '/', query: {} });
};

const userProfileImage = computed(() => {
    const img = (store.user as any)?.image; // cast to any
    return img ? `http://localhost:8000/storage/${img}` : '/assets/images/user-profile.jpeg';
});


    const notifications = ref([
        {
            id: 1,
            profile: 'user-profile.jpeg',
            message: '<strong class="text-sm mr-1">John Doe</strong>invite you to <strong>Prototyping</strong>',
            time: '45 min ago'
        },
        {
            id: 2,
            profile: 'profile-34.jpeg',
            message: '<strong class="text-sm mr-1">Adam Nolan</strong>mentioned you to <strong>UX Basics</strong>',
            time: '9h Ago'
        },
        {
            id: 3,
            profile: 'profile-16.jpeg',
            message: '<strong class="text-sm mr-1">Anna Morgan</strong>Upload a file',
            time: '9h Ago'
        }
    ]);

    const messages = ref([
        {
            id: 1,
            image: '<span class="grid place-content-center w-9 h-9 rounded-full bg-success-light dark:bg-success text-success dark:text-success-light"><svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg></span>',
            title: 'Congratulations!',
            message: 'Your OS has been updated.',
            time: '1hr'
        },
        {
            id: 2,
            image: '<span class="grid place-content-center w-9 h-9 rounded-full bg-info-light dark:bg-info text-info dark:text-info-light"><svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg></span>',
            title: 'Did you know?',
            message: 'You can switch between artboards.',
            time: '2hr'
        }
    ]);

    onMounted(() => {
        setActiveDropdown();
    });

    watch(route, (to, from) => {
        setActiveDropdown();
    });

    const setActiveDropdown = () => {
        const selector = document.querySelector('ul.horizontal-menu a[href="' + window.location.pathname + '"]');
        if (selector) {
            selector.classList.add('active');
            const all: any = document.querySelectorAll('ul.horizontal-menu .nav-link.active');
            for (let i = 0; i < all.length; i++) {
                all[0]?.classList.remove('active');
            }
            const ul: any = selector.closest('ul.sub-menu');
            if (ul) {
                let ele: any = ul.closest('li.menu').querySelectorAll('.nav-link');
                if (ele) {
                    ele = ele[0];
                    setTimeout(() => {
                        ele?.classList.add('active');
                    });
                }
            }
        }
    };

    const toggleNetworkMonitoring = () => {
        if (store.monitoring) {
            store.stopMonitoring();
        } else {
            store.startMonitoring();
        }
    };

    const removeNotification = (value: number) => {
        notifications.value = notifications.value.filter((d) => d.id !== value);
    };

    const removeMessage = (value: number) => {
        messages.value = messages.value.filter((d) => d.id !== value);
    };

    const logout =  async  () => {
        isLoggingOut.value = true;
        await apiClient.post('/logout');

        isLoggingOut.value = false;
        store.toggleAuthenticated()
        localStorage.removeItem('isAuthenticated');
        router.push('/app/login');
    };
</script>
