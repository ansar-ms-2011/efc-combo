import { defineStore } from 'pinia';
import i18n from '@/i18n';
import appSetting from '@/app-setting';
import axios from 'axios';
import { Region, User } from '@/types';

export const useAppStore = defineStore('app', {
    state: () => ({
        user: JSON.parse(localStorage.getItem('user') || 'null') as User | null,
        groupedData: {
            centers: [],
            application_for: [],
            guardian_type: [],
            gender: [],
            religion: [],
            marital_status: [],
            regions: [],
            required_documents: [],
            duplicate_reasons: [],
            delivery_modes: []
        },
        draftCount: 0,
        isLoading: false,
        isDarkMode: false,
        mainLayout: 'auth',
        theme: 'light',
        menu: 'vertical',
        layout: 'full',
        rtlClass: 'ltr',
        animation: '',
        isAuthenticated: JSON.parse(localStorage.getItem('isAuthenticated') || 'false'),
        isOnline: true,
        lastCheck: 0,
        monitoring: JSON.parse(localStorage.getItem('networkMonitoring') || 'false'),
        intervalId: null as null | number,
        checking: false,
        navbar: 'navbar-sticky',
        locale: 'en',
        sidebar: false,
        globalSearchQuery: '',

        languageList: [
            { code: 'en', name: 'English' },
            { code: 'ud', name: 'Urdu' }
        ],
        isShowMainLoader: true,
        semidark: false
    }),

    actions: {
        updateDraftCount(count: number) {
            this.draftCount = count;
        },
        setUser(payload: any = null) {
            this.user = payload;
            sessionStorage.setItem('user', JSON.stringify(payload));
        },
        setGlobalSearch(query: string) {
            this.globalSearchQuery = query;
        },
        clearUser() {
            this.user = null;
            sessionStorage.removeItem('user');
        },
        setMainLayout(payload: any = null) {
            this.mainLayout = payload; //app , auth
        },
        toggleAuthenticated() {
            this.isAuthenticated = !this.isAuthenticated;
        },
        toggleTheme(payload: any = null) {
            payload = payload || this.theme; // light|dark|system
            localStorage.setItem('theme', payload);
            this.theme = payload;
            if (payload == 'light') {
                this.isDarkMode = false;
            } else if (payload == 'dark') {
                this.isDarkMode = true;
            } else if (payload == 'system') {
                if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    this.isDarkMode = true;
                } else {
                    this.isDarkMode = false;
                }
            }

            if (this.isDarkMode) {
                document.querySelector('body')?.classList.add('dark');
            } else {
                document.querySelector('body')?.classList.remove('dark');
            }
        },
        toggleMenu(payload: any = null) {
            payload = payload || this.menu; // vertical, collapsible-vertical, horizontal
            this.sidebar = false; // reset sidebar state
            localStorage.setItem('menu', payload);
            this.menu = payload;
        },
        toggleLayout(payload: any = null) {
            payload = payload || this.layout; // full, boxed-layout
            localStorage.setItem('layout', payload);
            this.layout = payload;
        },
        toggleRTL(payload: any = null) {
            payload = payload || this.rtlClass; // rtl, ltr
            localStorage.setItem('rtlClass', payload);
            this.rtlClass = payload;
            document.querySelector('html')?.setAttribute('dir', this.rtlClass || 'ltr');
        },
        toggleAnimation(payload: any = null) {
            payload = payload || this.animation; // animate__fadeIn, animate__fadeInDown, animate__fadeInUp, animate__fadeInLeft, animate__fadeInRight, animate__slideInDown, animate__slideInLeft, animate__slideInRight, animate__zoomIn
            payload = payload?.trim();
            localStorage.setItem('animation', payload);
            this.animation = payload;
            appSetting.changeAnimation();
        },
        toggleNavbar(payload: any = null) {
            payload = payload || this.navbar; // navbar-sticky, navbar-floating, navbar-static
            localStorage.setItem('navbar', payload);
            this.navbar = payload;
        },
        toggleSemidark(payload: any = null) {
            payload = payload || false;
            localStorage.setItem('semidark', payload);
            this.semidark = payload;
        },
        toggleLocale(payload: any = null) {
            payload = payload || this.locale;
            i18n.global.locale.value = payload;
            localStorage.setItem('i18n_locale', payload);
            this.locale = payload;
            if (this.locale?.toLowerCase() === 'ae') {
                this.toggleRTL('rtl');
            } else {
                this.toggleRTL('ltr');
            }
        },
        toggleSidebar(state: boolean = false) {
            this.sidebar = !this.sidebar;
        },
        toggleMainLoader(state: boolean = false) {
            this.isShowMainLoader = true;
            setTimeout(() => {
                this.isShowMainLoader = false;
            }, 500);
        },
        async pingServer() {
            try {
                await axios.get('/api/ping', { timeout: 2000 });
                this.isOnline = true;
            } catch {
                this.isOnline = false;
            }
            this.lastCheck = Date.now();
        },

        async checkConnection(force = false) {
            const now = Date.now();

            if (!force && now - this.lastCheck < 5000) {
                return this.isOnline;
            }

            await this.pingServer();
            return this.isOnline;
        },

        startMonitoring() {
            // 1️⃣ run immediately
            this.checkConnection(true);
            this.monitoring = true;
            localStorage.setItem('networkMonitoring', JSON.stringify(this.monitoring));
            console.log('Network monitoring started');

            this.intervalId = window.setInterval(() => {
                this.pingServer();
            }, 10000);

            // 3️⃣ still listen to events (extra)
            window.addEventListener('online', () => {
                this.checkConnection(true);
                console.log('online event fired');
            });
            window.addEventListener('offline', () => {
                this.isOnline = false;
                console.log('offline event fired');
            });
        },

        stopMonitoring() {
            if (this.intervalId) clearInterval(this.intervalId);
            this.monitoring = false;
            localStorage.setItem('networkMonitoring', JSON.stringify(this.monitoring));
            console.log('Network monitoring stopped');
        },
        async loadDropdowns(forceRefresh = false) {
            // ✅ 1. Try localStorage first
            const stored = localStorage.getItem('groupedData');

            if (stored && !forceRefresh) {
                this.groupedData = JSON.parse(stored);
                return;
            }

            // ✅ 2. Fetch from API
            this.isLoading = true;
            try {
                const res = await axios.get('/api/get-grouped-types?clear_cache=true');

                this.groupedData = res.data;

                // ✅ 3. Save to localStorage
                localStorage.setItem(
                    'groupedData',
                    JSON.stringify(res.data)
                );
            } catch (err) {
                console.error('Dropdown fetch error:', err);
            } finally {
                this.isLoading = false;
            }
        },
        async addRegion(region: any) {
            try {
                // @ts-ignore
                this.groupedData.regions.push({
                    id: region.id,
                    name: region.name,
                    urdu_name: region.urdu_name,
                    parent_id: region.parent_id
                });
                localStorage.setItem(
                    'groupedData',
                    JSON.stringify(this.groupedData)
                );
            } catch (err) {
                console.error('Region add error:', err);
            }
        },
        async addDistrict(district: any) {
            try {
                // @ts-ignore
                let region: any = this.groupedData.regions.find(region => region.id === district.parent_id);

                if (!region) {
                    console.log('region not found');
                } else {
                    region.districts = region.districts || [];
                    region.districts.push({
                        id: district.id,
                        name: district.name,
                        urdu_name: district.urdu_name,
                        parent_id: district.parent_id
                    });
                    localStorage.setItem(
                        'groupedData',
                        JSON.stringify(this.groupedData)
                    );
                }
            } catch (err) {
                console.error('District add error:', err);
            }
        },

        async addTehsil(tehsil: any) {
            try {
                let region: any = this.groupedData
                    .regions
                    .find((region: { id: any }) => region.id === tehsil.parent.parent_id);
                if (!region) {
                    console.log('region not found');
                }

                let district: any = region.districts.find(district => district.id === tehsil.parent_id);

                if (!district) {
                    console.log('district not found');
                } else {

                    district.tehsils = district.tehsils || [];
                    district.tehsils.push({
                        id: tehsil.id,
                        name: tehsil.name,
                        urdu_name: tehsil.urdu_name,
                        parent_id: tehsil.parent_id
                    });
                    localStorage.setItem(
                        'groupedData',
                        JSON.stringify(this.groupedData)
                    );
                }
            } catch (err) {
                console.error('Tehsil add error:', err);
            }
        },

        // Optional: clear cache manually
        clearCache() {
            localStorage.removeItem('groupedData');
        }
    },
    getters: {
        appointment_for_list: (state) => state.groupedData.application_for || [],
        guardian_types: (state) => state.groupedData.guardian_type || [],
        genders: (state) => state.groupedData.gender || [],
        religions: (state) => state.groupedData.religion || [],
        marital_statuses: (state) => state.groupedData.marital_status || [],
        regions: (state): Region[] => state.groupedData.regions || [],
        requiredDocuments: (state) => state.groupedData.required_documents || [],
        centers: (state) => state.groupedData.centers || [],
        duplicate_reasons: (state) => state.groupedData.duplicate_reasons || [],
        delivery_modes: (state) => state.groupedData.delivery_modes || [],
        urduInputEnabled: (state) => state.user?.keyboard_settings?.urduInput === true
    }
});
