// --------------------------------------------------
// Core
// --------------------------------------------------
import { createApp } from 'vue'
import App from '@/App.vue'
import router from '@/router'
import { createPinia } from 'pinia'
import Vue3Toastify, { toast } from 'vue3-toastify'
import { VueDatePicker } from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
// --------------------------------------------------
// Styles
// --------------------------------------------------
import '@/assets/css/app.css'
import '@suadelabs/vue3-multiselect/dist/vue3-multiselect.css'
import 'easymde/dist/easymde.min.css'
import 'vue3-toastify/dist/index.css'
import urdu from './directives/urdu'

const toastOptions = {
    // mount to document.body so layout switches don’t destroy it
    container: () => document.body,
    autoClose: 3000,
    position: 'top-right',
    theme: "colored"
}

// --------------------------------------------------
// Libraries / Plugins
// --------------------------------------------------

import axios from 'axios'
import { createHead } from '@vueuse/head'
import { PerfectScrollbarPlugin } from 'vue3-perfect-scrollbar'
import { TippyPlugin } from 'tippy.vue'
import { vMaska } from 'maska/vue'
import VueEasymde from 'vue3-easymde'
import vue3JsonExcel from 'vue3-json-excel'
import { registerSW } from 'virtual:pwa-register'

// i18n & settings
import i18n from '@/i18n'
import appSetting from '@/app-setting'

// Components
import Popper from 'vue3-popper'
import Multiselect from '@suadelabs/vue3-multiselect'
import BaseDialog from './components/BaseDialog.vue';
import Pagination from './components/Pagination.vue';

// Utils
import { formatDMY } from '@/mixin'

// --------------------------------------------------
// Axios Config
// --------------------------------------------------
axios.defaults.baseURL = import.meta.env.VITE_API_BASE_URL
axios.defaults.withCredentials = true; // MUST for cookies
axios.defaults.withXSRFToken = true;

// --------------------------------------------------
// Create App
// --------------------------------------------------
const app = createApp(App)

// --------------------------------------------------
// App Plugins
// --------------------------------------------------
app.use(createPinia())
app.use(router)
app.use(createHead())
app.use(i18n)
app.use(PerfectScrollbarPlugin)
app.use(TippyPlugin)
app.use(VueEasymde)
app.component('BaseDialog', BaseDialog)
app.component('Pagination', Pagination)
app.use(vue3JsonExcel)
app.use(Vue3Toastify, toastOptions)

// --------------------------------------------------
// Global Directives
// --------------------------------------------------
app.directive('maska', vMaska)
app.directive('urdu-input', urdu)

// --------------------------------------------------
// Global Components
// --------------------------------------------------
app.component('Popper', Popper)
app.component('Multiselect', Multiselect)
app.component('VueDatePicker', VueDatePicker);
// --------------------------------------------------
// Global Properties
// --------------------------------------------------
app.config.globalProperties.$formatDMY = formatDMY

// --------------------------------------------------
// App Settings Init
// --------------------------------------------------
appSetting.init()

// --------------------------------------------------
// Mount
// --------------------------------------------------
app.mount('#app')

// --------------------------------------------------
// PWA Service Worker
// --------------------------------------------------
registerSW({
    immediate: true,
    onNeedRefresh() {
        console.log('New version available')
    },
    onOfflineReady() {
        console.log('App ready offline')
    }
})
