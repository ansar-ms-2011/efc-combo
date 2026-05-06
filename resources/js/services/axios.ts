import axios, { AxiosError } from 'axios';
import router from '@/router';

// Create axios instance
const apiClient = axios.create({
    baseURL: import.meta.env.VITE_APP_URL || '/',
    withCredentials: true,
    withXSRFToken: true,

    xsrfCookieName: 'XSRF-TOKEN',
    xsrfHeaderName: 'X-XSRF-TOKEN',

    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
    }
});

/**
 * ================================
 * Request Interceptor
 * ================================
 */
apiClient.interceptors.request.use(
    (config) => {
        // You can add a global loader here if needed
        return config;
    },
    (error) => Promise.reject(error)
);

/**
 * ================================
 * Response Interceptor
 * ================================
 */
apiClient.interceptors.response.use(
    (response) => response,
    async (error: AxiosError) => {
        const status = error.response?.status;

        //🔴 If the session expired or unauthenticated
        if (status === 401) {
            // Redirect only if not already on login page
            if (router.currentRoute.value.name !== 'login') {
                localStorage.removeItem('isAuthenticated')
                await router.push({ name: 'login' })
            }
        }
        // 🔴 Handle 419 (CSRF token mismatch)
        if (status === 419) {
            console.warn('CSRF token expired. Refreshing...')
            await apiClient.get('/sanctum/csrf-cookie')
            window.location.reload()
        }

        return Promise.reject(error);
    }
);

export default apiClient;
