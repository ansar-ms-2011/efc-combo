<template>
    <button :class="{
        'text-green-700 border-green-300': form.certificateType === 'state',
        'text-blue-700 border-blue-300': form.certificateType === 'domicile',
        'text-purple-700 border-purple-300': form.certificateType === 'both'
    }"
            class="font-nastaleeq w-[75px] px-3 py-1.5 text-sm bg-white border rounded-lg hover:bg-gray-300  transition-colors duration-200"
            type="button" @click="form.showDialog = true">
        تبدیل کریں
    </button>
    <BaseDialog v-model="form.showDialog" title="درخواست کی ابتدائی معلومات" subtitle="Select Certificate Type"
                title-class="font-nastaleeq" :maxWidth="maxWidth">
        <template #header-right>
            <button @click="handleDialogClose"
                    class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 focus:outline-none">
                <span class="text-2xl">&times;</span>
            </button>
        </template>
        <div class="transition-container">
            <Transition :name="transitionName" mode="out-in">
                <div :key="step" class="step-wrapper min-h-[250px]">
                    <div class="step1 step-content space-y-4" v-if="step === 1">
                        <label v-for="(type, index) in certificateTypes" :key="type.id"
                               class="flex items-center p-4 border-2  rounded-lg cursor-pointer transition-colors duration-200"
                               :class="[form.certificateType === type.id ? type.activeClass : type.hoverClass, !isAllowed(type.id) ? 'cursor-not-allowed opacity-50' : '']">
                            <input type="radio" class="mr-3 h-5 w-5" :class="type.radioColor"
                                   v-model="form.certificateType" :value="type.id" :tabindex="index + 1"
                                   :disabled="!isAllowed(type.id)">
                            <div class="flex items-center flex-1">
                                <!-- Icon -->
                                <div :class="['p-2 rounded-lg mr-3', type.iconBg]">
                                    <component :is="type.icon" class="w-6 h-6" :class="type.iconColor" />
                                </div>
                                <!-- Text -->
                                <div class="text-left">
                                    <h3 class="font-bold text-gray-900 font-nastaleeq">
                                        {{ type.urdu }}
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        {{ type.english }}
                                    </p>
                                </div>
                            </div>
                        </label>
                    </div>
                    <div class="step2 step-content space-y-4" v-if="step === 2">
                        <div class="flex gap-4 justify-center mb-4">
                            <label
                                class="flex items-center p-2 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-50 w-full justify-center"
                                :class="{ 'bg-blue-50 border-blue-500': form.identificationType === 'local' }"
                                @click="form.identificationType = 'local'">
                                <input type="radio" v-model="form.identificationType" value="local" class="mr-1">
                                CNIC
                            </label>

                            <label
                                class="flex items-center p-2 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-50 w-full justify-center"
                                :class="{ 'bg-blue-50 border-blue-500': form.identificationType === 'refugee' }"
                                @click="form.identificationType = 'refugee'">
                                <input type="radio" v-model="form.identificationType" value="refugee" class="mr-2">
                                Refugee
                            </label>
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2 text-center">
                                <span>{{ form.identificationType === 'local' ? 'CNIC Number' : 'Refugee Number'
                                    }}</span>
                                <span class="text-red-500">*</span>
                            </label>
                            <input ref="inputCardNumber" v-if="form.identificationType === 'local'"
                                   v-model="form.identificationNumber"
                                   class="form-input text-center text-sm p-2 font-nastaleeq" maxlength="15"
                                   placeholder="XXXXX-XXXXXXX-X" type="text" @input="handleFormating" v-auto-focus
                                   tabindex="4" />

                            <input v-else ref="inputRefugeeNumber" v-model="form.identificationNumber"
                                   class="form-input text-center text-sm p-2 font-nastaleeq" maxlength="50"
                                   placeholder="Enter Refugee Number" type="text" v-auto-focus tabindex="5" />
                            <p v-if="form.identificationType === 'local'"
                               class="text-xs text-gray-500 text-center mt-2">
                                Format: 12345-1234567-1
                            </p>
                        </div>
                    </div>
                    <div class="step3 step-content" v-if="step === 3">
                        <div v-if="applicantDetails" class="border border-gray-300 rounded-lg">
                            <ApplicantDetails :applicant="applicantDetails" />
                            <ApplicantCertificates :certificates="applicantDetails?.certificates" />
                        </div>
                        <div class="app-type-wrapper flex flex-row gap-4 mt-2 justify-between items-center">
                            <label for="application_type_id_1"
                                   :class="{ 'bg-blue-50 border-blue-500': form.applicationType === 1 }"
                                   class="font-nastaleeq font-normal flex items-center justify-center p-2 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-50 md:w-[200px]">
                                <input type="radio" name="application.application_type_id"
                                       v-model="form.applicationType" :value="1" ref="optionNew"
                                       id="application_type_id_1"
                                       class="mr-1" />
                                نئی
                            </label>

                            <label for="application_type_id_2"
                                   :class="{ 'bg-blue-50 border-blue-500': form.applicationType === 2 }"
                                   class="font-nastaleeq font-normal flex items-center justify-center p-2 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-50 md:w-[200px]">
                                <input type="radio" name="application.application_type_id"
                                       v-model="form.applicationType" ref="optionDuplicate" :value="2"
                                       id="application_type_id_2" class="mr-1" />
                                نقل
                            </label>
                            <label
                                class="block text-sm font-medium font-nastaleeq text-gray-700 mb-2 text-right text-nowrap">
                                <span class="text-red-500">*</span>درخواست کی قسم
                            </label>
                        </div>
                        <p v-if="optionMessage" class="font-nastaleeq text-red-400 text-lg text-center">{{ optionMessage
                            }}</p>
                        <div v-if="isLoadingDetails" class="flex justify-center items-center h-full  min-h-[150px]">
                            <div class="flex gap-2 items-center justify-center">
                                <i class="fa fa-spin fa-spinner fa-2x"></i> <span class="font-nastaleeq text-lg">درخواست
                                    دہندہ
                                    کی تفصیلات لوڈ ہو رہی ہیں</span>
                            </div>
                        </div>
                        <div v-if="!applicantDetails && !isLoadingDetails"
                             class="flex justify-center items-center h-full  min-h-[150px]">
                            <p class="text-gray-500 font-nastaleeq text-lg">سسٹم میں درخواست دہندہ کی کوئی تفصیلات موجود
                                نہیں
                                ہیں۔</p>
                        </div>
                    </div>
                </div>
            </Transition>
        </div>
        <!-- Footer -->
        <template #footer>

            <div class="flex justify-between items-center gap-3">
                <button :disabled="step === 1" type="button"
                        class="flex-1 px-4 py-2.5 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed font-nastaleeq max-w-20"
                        @click="proceedToPrevStep" tabindex="0">
                    واپس جائیں
                </button>
                <button v-if="step < 3" type="button" :disabled="readyForNextStep"
                        class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed font-nastaleeq  max-w-20"
                        @click="proceedToNextStep" tabindex="6">
                    جاری رکھیں
                </button>

                <button v-if="step === 3" type="button"
                        :disabled="!form.identificationType || !form.identificationNumber || !form.applicationType"
                        class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed font-nastaleeq  max-w-20"
                        @click="proceedWithSelection">
                    جاری رکھیں
                </button>
            </div>
        </template>
    </BaseDialog>
</template>

<script setup>
    import { computed, nextTick, ref, watch } from 'vue';
    import BaseDialog from '@/components/BaseDialog.vue';
    import ApplicantDetails from '@/components/ApplicantDetails.vue';
    import { formatCnic } from '@/mixin/index.ts';
    import router from '@/router';
    import axios from 'axios';
    import ApplicantCertificates from '@/components/ApplicantCertificates.vue';
    import { useAppStore } from '@/stores/index.ts';

    const props = defineProps({
        modelValue: {
            type: Object
        }
    });

    const emit = defineEmits([
        'update:modelValue',
        'selected'
    ]);
    const step = ref(1);
    const transitionName = ref('slide-left');
    const inputCardNumber = ref(null);
    const inputRefugeeNumber = ref(null);
    const isLoadingDetails = ref(false);
    const applicantDetails = ref(null);
    const optionNew = ref(null);
    const optionDuplicate = ref(null);
    const optionMessage = ref(null);

    const store = useAppStore();

    const normalize = (str) => {
        return (str || '')
            .toLowerCase()
            .trim();
    };

    const serviceMap = computed(() => {
        const services = (store.user?.allowed_services || [])
            .map(normalize);

        return {
            domicile: services.includes('domicile'),
            state: services.includes('state subject certificate'),
            both:
                services.includes('domicile') &&
                services.includes('state subject certificate')
        };
    });


    const maxWidth = computed(() => {
        if (step.value === 1 || step.value === 2) {
            return 'max-w-md';
        } else if (applicantDetails.value !== null) {
            return 'max-w-2xl';
        }
    });
    const enableAvailableType = async () => {
        optionMessage.value = '';
        // Wait for the DOM to update with the new step content
        await nextTick();
        // Additional delay to ensure Vue has completed rendering
        await new Promise(resolve => setTimeout(resolve, 50));

        let conditionApplied = null;
        let selectedCertificateType = form.value.certificateType;
        let certificates = applicantDetails.value?.certificates.filter(cert => !cert.is_revoked);

        // Check if refs are available
        if (!optionNew.value || !optionDuplicate.value) {
            console.warn('Radio button refs not available yet, retrying...');
            // Retry after a short delay
            setTimeout(() => enableAvailableType(), 100);
            return;
        }

        console.log('Radio refs found, applying logic...');
        if (!applicantDetails.value) {
            form.value.applicationType = 1;
            optionNew.value.checked = true;
            optionNew.value.disabled = false;
            optionDuplicate.value.checked = true;
            optionDuplicate.value.disabled = true;
        } else if (certificates?.length === 0 && selectedCertificateType === 'both') {
            form.value.applicationType = 1;
            optionNew.value.checked = true;
            optionNew.value.disabled = false;
            optionDuplicate.value.checked = false;
            optionDuplicate.value.disabled = false;
            conditionApplied = '0, both';
            optionMessage.value = '';
        } else if (certificates?.length > 0 && selectedCertificateType === 'both') {
            form.value.applicationType = null;
            optionNew.value.checked = false;
            optionNew.value.disabled = true;
            optionDuplicate.value.checked = false;
            optionDuplicate.value.disabled = true;
            conditionApplied = '>0, both';
            optionMessage.value = 'سرٹیفکیٹ پہلے سے موجود ہے، منتخب کردہ آپشنز کے ساتھ کارروائی ممکن نہیں۔';
        } else if (certificates?.length > 0 && selectedCertificateType === 'domicile') {
            let existing = certificates?.find(cert => cert.type === selectedCertificateType);
            if (existing) {
                form.value.applicationType = 2;
                optionNew.value.checked = false;
                optionNew.value.disabled = true;
                optionDuplicate.value.checked = true;
                optionDuplicate.value.disabled = false;
                conditionApplied = '>0, exist , domicile';
                optionMessage.value = '';
            } else {
                form.value.applicationType = 1;
                optionNew.value.checked = true;
                optionNew.value.disabled = false;
                optionDuplicate.value.checked = false;
                optionDuplicate.value.disabled = true;
                conditionApplied = '>0, not exist , domicile';
                optionMessage.value = '';
            }
        } else if (certificates?.length > 0 && selectedCertificateType === 'state') {
            let existing = certificates?.find(cert => cert.type === selectedCertificateType);
            if (existing) {
                form.value.applicationType = 2;
                optionNew.value.checked = false;
                optionNew.value.disabled = true;
                optionDuplicate.value.checked = true;
                optionDuplicate.value.disabled = false;
                conditionApplied = '>0, exist , state';
                optionMessage.value = '';
            } else {
                form.value.applicationType = 1;
                optionNew.value.checked = true;
                optionNew.value.disabled = false;
                optionDuplicate.value.checked = false;
                optionDuplicate.value.disabled = true;
                conditionApplied = '>0, not exist , state';
                optionMessage.value = '';
            }
        }
        console.log('conditionApplied', conditionApplied, selectedCertificateType);
    };

    const form = ref({
        ...props.modelValue
    });

    watch(() => props.modelValue, (val) => {
        form.value = { ...val };
        console.log(form.value);
    });

    watch(() => form.value.identificationType, (val) => {
        if (!form.value.identificationNumber) return;

        if (val === 'local') {
            form.value.identificationNumber = formatCnic(form.value.identificationNumber);
        } else {
            form.value.identificationNumber = form.value.identificationNumber.replaceAll('-', '');
        }
    });

    // Add a watcher for step 3 to handle cases when certificate type changes
    watch(() => form.value.certificateType, async (newVal, oldVal) => {
        // If we're on step 3 and have applicant details, re-evaluate the options
        if (step.value === 3 && applicantDetails.value && newVal !== oldVal) {
            await nextTick();
            await enableAvailableType();
        }
    });

    const handleFormating = (e) => {
        const input = e.target;
        if (form.value.identificationType === 'local') {
            input.value = formatCnic(input.value);
            form.value.identificationNumber = input.value;
        } else {
            input.value = input.value.toUpperCase();
        }
    };

    function proceedToPrevStep() {
        transitionName.value = 'slide-right';
        step.value -= 1;
    }

    const readyForNextStep = computed(() => {
        if (step.value === 1) {
            return (
                !form.value.certificateType ||
                !isAllowed(form.value.certificateType)
            );
        } else if (step.value === 2) {
            return (
                !form.value.identificationType ||
                !form.value.identificationNumber ||
                (form.value.identificationType === 'local' &&
                    form.value.identificationNumber.length < 15)
            );
        } else {
            return false;
        }
    });

    const proceedToNextStep = async () => {
        transitionName.value = 'slide-left';
        step.value += 1;
        if (step.value === 3) {
            applicantDetails.value = null;
            await getApplicantDetails();
            await nextTick();
            await enableAvailableType();
        }
    };

    function proceedWithSelection() {
        nextTick(() => {
            emit('selected', form.value);
            form.value.showDialog = false;
            step.value = 1;
        });
    }

    const getApplicantDetails = async () => {
        try {
            isLoadingDetails.value = true;
            const response = await axios.get(`/api/applicant-details?id=${form.value.identificationNumber}&type=${form.value.identificationType}`);
            applicantDetails.value = response.data?.applicant;
            form.value.applicantDetails = response.data?.applicant;
            return response.data?.applicant;
        } catch (error) {
            console.error('Error fetching applicant details:', error);
            return null;
        } finally {
            isLoadingDetails.value = false;
        }
    };

    const vAutoFocus = {
        mounted: (el) => {
            el.focus();
        }
    };

    const handleDialogClose = async () => {
        form.value.showDialog = false;
        step.value = 1;
        if (form.value.mode === 'create') {
            await router.push({ name: 'applications.view', params: { status: 'all' } });
        }
    };

    const isAllowed = (typeId) => {
        return serviceMap.value[typeId];
    };

    const certificateTypes = [

        {
            id: 'domicile',
            urdu: 'ڈومیسائل سرٹیفکیٹ',
            english: 'Domicile Certificate',

            hoverClass: 'hover:bg-blue-50',
            activeClass: 'border-blue-600 bg-blue-50',

            radioColor: 'text-blue-600',

            iconBg: 'bg-blue-100',
            iconColor: 'text-blue-600',

            icon: {
                template: `
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                        `
            }
        },

        {
            id: 'state',
            urdu: 'اسٹیٹ سبجیکٹ سرٹیفکیٹ',
            english: 'State Subject Certificate',

            hoverClass: 'hover:bg-green-50',
            activeClass: 'border-green-600 bg-green-50',

            radioColor: 'text-green-600',

            iconBg: 'bg-green-100',
            iconColor: 'text-green-600',

            icon: {
                template: `
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>`
            }
        },

        {
            id: 'both',
            urdu: 'ڈومیسائل اور اسٹیٹ سبجیکٹ سرٹیفکیٹ',
            english: 'Domicile and State Subject Certificates',

            hoverClass: 'hover:bg-purple-50',
            activeClass: 'border-purple-600 bg-purple-50',

            radioColor: 'text-purple-600',

            iconBg: 'bg-purple-100',
            iconColor: 'text-purple-600',

            icon: {
                template: `
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>`
            }
        }
    ];
</script>
<style scoped>
    .transition-container {
        position: relative;
        min-height: 240px;
        /* Adjust based on your content */
        overflow: hidden;
        width: 100%;
    }

    .step-wrapper {
        position: relative;
        width: 100%;
    }

    .step-content {
        width: 100%;
        box-sizing: border-box;
    }

    /* Slide animations */
    .slide-left-enter-active,
    .slide-left-leave-active,
    .slide-right-enter-active,
    .slide-right-leave-active {
        transition: all 0.35s ease;
    }

    /* Slide left (forward) */
    .slide-left-enter-from {
        opacity: 0;
        transform: translateX(100%);
    }

    .slide-left-enter-to {
        opacity: 1;
        transform: translateX(0);
    }

    .slide-left-leave-from {
        opacity: 1;
        transform: translateX(0);
    }

    .slide-left-leave-to {
        opacity: 0;
        transform: translateX(-100%);
    }

    /* Slide right (backward) */
    .slide-right-enter-from {
        opacity: 0;
        transform: translateX(-100%);
    }

    .slide-right-enter-to {
        opacity: 1;
        transform: translateX(0);
    }

    .slide-right-leave-from {
        opacity: 1;
        transform: translateX(0);
    }

    .slide-right-leave-to {
        opacity: 0;
        transform: translateX(100%);
    }

    /* Form input styling */
    .form-input {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #e2e8f0;
        border-radius: 0.5rem;
        transition: all 0.2s;
    }

    .form-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    /* Debug - remove in production */
    /* .step-content:first-child { background: #f0f9ff; } */
    /* .step-content:last-child { background: #f0fdf4; } */
</style>
