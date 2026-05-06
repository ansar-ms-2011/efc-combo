<template>
    <div>
        <div class="absolute inset-0">
            <img :src="loginBg" alt="image" class="h-full w-full object-cover" />
        </div>

        <div class="relative flex min-h-screen items-center justify-center px-6 py-10 sm:px-16">
            <div
                class="relative w-full max-w-[600px] rounded-md bg-[linear-gradient(45deg,#fff9f9_0%,rgba(255,255,255,0)_25%,rgba(255,255,255,0)_75%,_#fff9f9_100%)] p-2 dark:bg-[linear-gradient(52.22deg,#0E1726_0%,rgba(14,23,38,0)_18.66%,rgba(14,23,38,0)_51.04%,rgba(14,23,38,0)_80.07%,#0E1726_100%)]">
                <div
                    class="relative flex flex-col justify-center rounded-md bg-white/60 backdrop-blur-lg dark:bg-black/50 px-6 lg:min-h-[500px] py-12">
                    <div class="mx-auto w-full max-w-[440px]">
                        <div class="text-center mb-10">
                            <img :src="appLogo" alt="Logo"
                                 class="mx-auto w-24 h-24 mb-4 object-contain" />
                            <h2 class="text-lg font-bold text-gray-800 dark:text-white-dark leading-tight mb-8">
                                Digitalization of State Subject & Domicile in AJ&K
                            </h2>
                            <template v-if="step === 'login'">
                                <h1 class="text-3xl font-extrabold uppercase !leading-snug text-primary md:text-4xl">
                                    Sign in
                                </h1>
                                <p class="text-base font-bold leading-normal text-white-dark">Enter your email and
                                    password
                                    to login
                                </p>
                            </template>
                        </div>
                        <form v-if="step === 'login'" class="space-y-5 dark:text-white"
                              @submit.prevent="handleSubmit()">
                            <div>
                                <label for="Email">Email</label>
                                <div class="relative text-white-dark">
                                    <input id="Email" type="email" placeholder="Enter Email" v-model="form.email"
                                           required class="form-input ps-10 placeholder:text-white-dark" :class="[
                                            'form-input ps-10 placeholder:text-white-dark',
                                            errors.email ? 'border-red-500 focus:border-red-500' : ''
                                        ]" />
                                    <span class="absolute start-4 top-1/2 -translate-y-1/2">
                                        <icon-mail :fill="true" />
                                    </span>
                                </div>
                                <p v-if="errors.email" class="mt-1 text-sm text-red-600">
                                    {{ errors.email[0] }}
                                </p>
                            </div>
                            <div>
                                <label for="Password">Password</label>
                                <div class="relative text-white-dark">
                                    <input :type="showPassword ? 'text' : 'password'" id="Password"
                                           v-model="form.password" placeholder="Enter Password" required
                                           class="form-input ps-10 pe-10 placeholder:text-white-dark" :class="[
                                            'form-input ps-10 placeholder:text-white-dark',
                                            errors.email ? 'border-red-500 focus:border-red-500' : ''
                                        ]" />
                                    <span class="absolute start-4 top-1/2 -translate-y-1/2">
                                        <icon-lock-dots :fill="true" />
                                    </span>
                                    <span
                                        class="absolute end-4 top-1/2 -translate-y-1/2 cursor-pointer transition-all duration-300 hover:scale-110"
                                        @click="showPassword = !showPassword">
                                        <icon-eye v-if="!showPassword" :fill="true"
                                                  class="transition-all duration-300" />
                                        <icon-eye-off v-else :fill="true" class="transition-all duration-300" />
                                    </span>
                                </div>
                                <p v-if="errors.password" class="mt-1 text-sm text-red-600">
                                    {{ errors.password[0] }}
                                </p>
                            </div>
                            <button type="submit" :disabled="isSubmitting"
                                    class="btn btn-primary !mt-6 w-full border-0 uppercase shadow-[0_10px_20px_-10px_rgba(67,97,238,0.44)]">
                                <span v-if="isSubmitting"
                                      class="animate-spin border-4 border-white border-l-transparent rounded-full w-6 h-6 inline-block align-middle m-auto"></span>
                                <span v-else>Sign in</span>
                            </button>
                        </form>
                        <div v-else class="space-y-5 dark:text-white">
                            <div class="text-center">
                                <h2 class="text-xl font-bold text-primary">Two-Factor Authentication</h2>
                                <p class="text-sm text-gray-500 mt-1">
                                    Enter the 6-digit code from your authenticator app
                                </p>
                            </div>
                            <div>
                                <div>
                                    <label>Authentication Code</label>
                                    <input
                                        v-model="code"
                                        maxlength="6"
                                        placeholder="Enter 6-digit code"
                                        class="form-input ps-10 placeholder:text-white-dark" :class="[
                                            'form-input ps-10 placeholder:text-white-dark text-center font-bold',
                                            errors.email ? 'border-red-500 focus:border-red-500' : ''
                                        ]"
                                    />
                                </div>
                                <p v-if="errors.code" class="mt-1 text-sm text-red-600">
                                    {{ errors.code[0] }}
                                </p>
                                <p v-if="errors.user_id" class="mt-1 text-sm text-red-600">
                                    {{ errors.user_id[0] }}
                                </p>
                            </div>

                            <button
                                type="button"
                                @click="verify2FA"
                                :disabled="isSubmitting"
                                class="btn btn-primary w-full"
                            >
                                <span v-if="isSubmitting">Verifying...</span>
                                <span v-else>Verify</span>
                            </button>

                            <button
                                type="button"
                                class="text-sm text-gray-500 w-full"
                                @click="step = 'login'"
                            >
                                Back to login
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
    import { nextTick, ref, watch } from 'vue';
    import { useAppStore } from '@/stores';
    import { useRouter } from 'vue-router';
    import { useMeta } from '@/composables/use-meta';
    import loginBg from '@/assets/images/login-bg.jpeg';
    import appLogo from '@/assets/images/logo.png';
    import IconMail from '@/components/icon/icon-mail.vue';
    import IconLockDots from '@/components/icon/icon-lock-dots.vue';
    import IconEye from '@/components/icon/icon-eye.vue';
    import IconEyeOff from '@/components/icon/icon-eye-off.vue';
    import apiClient from '@/services/axios';
    import Swal from 'sweetalert2';

    useMeta({ title: 'E-Facilitation Center AJK' });
    const router = useRouter();

    const store = useAppStore();
    const showPassword = ref(false);
    const isSubmitting = ref(false);
    const errors = ref<Record<string, string[]>>({});
    const step = ref<'login' | '2fa'>('login');
    const userId = ref<number | null>(null);
    const code = ref('');

    const form = ref({
        email: '',
        password: ''
    });

    watch(step, async (val) => {
        if (val === '2fa') {
            await nextTick();
            document.querySelector('input')?.focus();
        }
    });

    const handleSubmit = async () => {
        isSubmitting.value = true;
        try {
            errors.value = {};
            // Try to initialize CSRF cookie
            try {
                console.log('Fetching CSRF cookie...');
                await apiClient.get('/sanctum/csrf-cookie', { withCredentials: true });
                console.log('CSRF cookie fetched successfully');
            } catch (csrfError) {
                console.error('CSRF initialization failed:', csrfError);
            }
            console.log('Sending login request...', form.value);
            const response = await apiClient.post('/login', form.value);

            if (response.data.two_factor) {
                // 🔐 2FA required
                step.value = '2fa';
                userId.value = response.data.user_id;
                return;
            }
            handleLoginResponse(response);

        } catch (error: any) {
            console.error('Login failed:', error);

            if (error.response?.status === 422) {
                // Validation errors
                errors.value = error.response.data.errors;
            } else if (error.response?.status === 401) {
                errors.value = {
                    email: ['Invalid email or password.']
                };
            } else {
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred. Please try again later.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        } finally {
            isSubmitting.value = false;
        }
    };
    const verify2FA = async () => {
        isSubmitting.value = true;

        try {
            const response = await apiClient.post('/two-factor-challenge', {
                code: code.value,
                user_id: userId.value
            });
            console.log('2FA response:', response.data);
            handleLoginResponse(response);

        } catch (error: any) {
            if (error.response?.status === 422) {
                errors.value = error.response.data.errors;
            } else if (error.response?.status === 401) {
                errors.value = {
                    email: ['Invalid email or password.']
                };
            } else {
                Swal.fire({
                    title: 'Invalid Code',
                    text: 'Please enter correct authentication code',
                    icon: 'error'
                });
            }
        } finally {
            isSubmitting.value = false;
        }
    };
    const handleLoginResponse = (response: any) => {
        if (response.data.success) {
            store.isAuthenticated = true;
            store.setUser(response.data.data.user);
            localStorage.setItem('isAuthenticated', 'true');
            localStorage.setItem('user', JSON.stringify(response.data.data.user));

            if (store.user?.roles?.[0]?.name === 'DEO' || store.user?.role === 'Center In-charge') {
                router.push('/quick-links');
            } else if (store.user?.role === 'AC') {
                router.push({ name: 'applications.view', params: { status: 'submitted' } });
            } else if (store.user?.role === 'DC') {
                router.push({ name: 'applications.view', params: { status: 'verified' } });
            } else {
                router.push('/');
            }
        } else {
            Swal.fire({
                title: 'Login Failed',
                text: response.data.message || 'Login failed. Please try again.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    };


</script>
