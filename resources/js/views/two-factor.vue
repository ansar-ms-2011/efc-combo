<script setup>
    import { computed, ref } from 'vue';
    import Swal from 'sweetalert2';
    import apiClient from '@/services/axios.ts'
    import QrcodeVue from 'qrcode.vue'
    import { useAppStore } from '@/stores/index.ts';
    const qr = ref('')
    const code = ref('')
    const recoveryCodes = ref([])
    const errors = ref({});
    const loading = ref(false)
    const step = ref(1) // 1 = enable, 2 = confirm
    const appStore = useAppStore();
    const two_fa_enabled = ref(false);

    const enable2FA = async () => {
        loading.value = true
        try {
            const res = await apiClient.post('/api/2fa/enable')
            qr.value = res.data.qr_url
            step.value = 2
        } finally {
            loading.value = false
        }
    }

    const confirm2FA = async () => {
        loading.value = true
        try {
            const res = await apiClient.post('/api/2fa/confirm', {
                code: code.value
            })
            recoveryCodes.value = res.data.recovery_codes
            two_fa_enabled.value = true
            step.value = 3
        }catch (error){
            if (error.response?.status === 422) {
                errors.value = error.response.data.errors;
            }else{
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to confirm 2FA',
                    icon: 'error'
                })
            }
        } finally {
            loading.value = false
        }
    }

    const disable2FA = async () => {
        try {
            const res = await apiClient.post('/api/2fa/disable')
            two_fa_enabled.value = false
            qr.value = ''
            recoveryCodes.value = []
            window.location.reload()
        } catch (e) {
            Swal.fire({
                title: 'Error',
                text: 'Failed to disable 2FA',
                icon: 'error'
            })
        }
    }
</script>

<template>
    <div class="max-w-lg mx-auto mt-10 bg-white shadow-lg rounded-2xl p-6 space-y-6">
        <!-- HEADER -->
        <div class="text-center space-y-6">
            <h2 class="text-2xl font-bold text-gray-800">Two-Factor Authentication</h2>
            <p v-if="!appStore.user.two_fa_enabled" class="text-gray-500 text-sm mt-2">
                Secure your account with 2 factor authentication.
            </p>
            <p v-else class="text-gray-500 text-sm mt-2">
                Your account is secure with 2FA. You can disable it at any time.
            </p>
        </div>

        <!-- STEP 1 -->
        <div v-if="step === 1" class="text-center space-y-4">
            <button
                v-if="!appStore.user?.two_fa_enabled || two_fa_enabled"
                @click="enable2FA"
                class="w-full bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition"
                :disabled="loading"
            >
                Enable 2FA
            </button>

            <button
                v-else
                @click="disable2FA"
                class="btn btn-danger w-full mt-4"
            >
                Disable 2FA
            </button>
        </div>

        <!-- STEP 2: QR + CODE -->
        <div v-if="step === 2" class="space-y-5 text-center">

            <!-- QR -->
            <div v-if="qr" class="flex justify-center items-center">
                <qrcode-vue :value="qr" size="200" level="H" class="rounded-lg"/>
            </div>

            <!-- Instructions -->
            <p class="text-sm text-gray-600">
                Scan this QR code using Google Authenticator or similar app.
            </p>

            <!-- OTP INPUT -->
            <div class="space-y-2">
                <label class="text-sm font-medium text-gray-700">
                    Enter 6-digit code
                </label>

                <input
                    v-model="code"
                    maxlength="6"
                    placeholder="••••••"
                    class="w-full text-center tracking-widest text-lg border rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 outline-none"
                />
                <p v-if="errors.code" class="mt-1 text-sm text-red-600">
                    {{ errors.code[0] }}
                </p>
            </div>

            <button
                @click="confirm2FA"
                class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition"
                :disabled="loading || code.length < 6"
            >
                Confirm 2FA
            </button>
        </div>

        <!-- STEP 3: RECOVERY CODES -->
        <div v-if="step === 3" class="space-y-4">

            <div class="text-center">
                <h3 class="text-lg font-semibold text-green-600">
                    2FA Enabled Successfully 🎉
                </h3>
                <p class="text-sm text-gray-500">
                    Save these recovery codes safely
                </p>
            </div>

            <div class="bg-gray-100 p-4 rounded-lg space-y-2">
                <div
                    v-for="(c, i) in recoveryCodes"
                    :key="i"
                    class="font-mono text-sm bg-white p-2 rounded border"
                >
                    {{ c }}
                </div>
            </div>

            <p class="text-xs text-red-500 text-center">
                ⚠️ These codes will not be shown again
            </p>
        </div>

    </div>
</template>
