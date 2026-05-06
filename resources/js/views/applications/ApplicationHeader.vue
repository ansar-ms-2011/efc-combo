<script setup lang="ts">
    import ApplicationTypeDialog from '@/views/applications/Dialogs/ApplicationTypeDialog.vue';
    import { ref, watch } from 'vue';
    import { useRoute, useRouter } from 'vue-router';

    const router = useRouter();
    const route = useRoute();
    const queryType = route.query.type;
    const allowedTypes = ['state', 'domicile', 'both'];
    const props = defineProps({
        modelValue: {
            type: Object,
            default: () => ({
                mode : 'create',
                certificateType: null,
                identificationType: 'local',
                identificationNumber: null,
                applicantDetails: null
            })
        },
    });

    const emit = defineEmits([
        'selected'
    ]);
    const form = ref({
        mode: props.modelValue.mode,
        showDialog: props.modelValue.mode === 'create',
        certificateType: allowedTypes.includes(queryType as string)
            ? queryType
            : props.modelValue.certificateType,
        identificationType: props.modelValue.identificationType,
        identificationNumber: props.modelValue.identificationNumber,
        applicationType: props.modelValue.applicationType,
        applicantDetails: props.modelValue.applicantDetails,
    });
    const handleChange = (payload: any) => {
        form.value.certificateType = payload.certificateType
        form.value.identificationType = payload.identificationType
        form.value.identificationNumber = payload.identificationNumber
        form.value.applicationType = payload.applicationType
        form.value.applicantDetails = payload.applicantDetails
        emit('selected', payload)
    }
    watch(
        () => form.value.certificateType,
        (newType) => {
            router.replace({
                query: {
                    ...route.query,
                    type: newType || undefined, // remove if empty
                }
            });
        }
    );
</script>

<template>
    <div class="panel lg:col-span-2 p-0">
        <div :class="{
                'bg-green-50 border border-green-200': form.certificateType === 'state',
                'bg-blue-50 border border-blue-200': form.certificateType === 'domicile',
                'bg-purple-50 border border-purple-200': form.certificateType === 'both'
            }" class="mb-4 p-3 rounded-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div :class="{
                            'bg-green-100': form.certificateType === 'state',
                            'bg-blue-100': form.certificateType === 'domicile',
                            'bg-purple-100': form.certificateType === 'both'
                        }" class="p-2 rounded-lg mr-3">
                        <svg :class="{
                                'text-green-600': form.certificateType === 'state',
                                'text-blue-600': form.certificateType === 'domicile',
                                'text-purple-600': form.certificateType === 'both'
                            }" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path v-if="form.certificateType === 'domicile'"
                                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            <path v-else-if="form.certificateType === 'state'"
                                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            <path v-else
                                  d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                    </div>
                    <div>
                        <h3 :class="{
                                'text-green-800': form.certificateType === 'state',
                                'text-blue-800': form.certificateType === 'domicile',
                                'text-purple-800': form.certificateType === 'both'
                            }" class="font-bold font-nastaleeq">
                            {{
                                form.certificateType === 'state' ? 'اسٹیٹ سبجیکٹ سرٹیفکیٹ' :
                                    form.certificateType === 'domicile' ? 'ڈومیسائل سرٹیفکیٹ' :
                                        'ڈومیسائل اور اسٹیٹ سبجیکٹ سرٹیفکیٹ'
                            }}
                        </h3>
                        <p :class="{
                                'text-green-600': form.certificateType === 'state',
                                'text-blue-600': form.certificateType === 'domicile',
                                'text-purple-600': form.certificateType === 'both'
                            }" class="text-sm">
                            {{
                                form.certificateType === 'state' ? 'State Subject Certificate' :
                                    form.certificateType === 'domicile' ? 'Domicile Certificate' :
                                        'Domicile and State Subject Certificates'
                            }}
                        </p>
                    </div>
                </div>
                <ApplicationTypeDialog
                    v-model="form"
                    @selected="handleChange"
                />
            </div>
        </div>
    </div>
</template>
