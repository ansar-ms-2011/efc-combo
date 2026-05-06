<template>
    <div class="min-h-screen bg-gray-50 py-1 relative px-1">
        <Form :validation-schema="validationSchema" v-if="!isLoading"
              v-slot="{ validate, setFieldValue, values, validateField, errors, setErrors }"
              :initial-values="form"
        >
            <ApplicationHeader @selected="payload => handleSelected(payload, setFieldValue)"
                               v-model="form.headerModel" />

            <div class="panel lg:col-span-2">
                <Header v-model="form.application.certificate_type" />
                <StepProgress
                    :currentStep="currentStep"
                    :steps="[
                        'درخواست کی تفصیلات',
                        'پتہ اور ڈیلیوری کی تفصیلات',
                        'مطلوبہ دستاویزات',
                        'مکمل جائزہ'
                    ]"
                />
                <div v-show="currentStep === 1" class="step">
                    <StepOne
                        :setFieldValue="setFieldValue"
                        :form="form"
                        :values="values"
                        :errors="errors"
                    />
                </div>
                <div v-show="currentStep === 2" class="step">
                    <StepTwo
                        :values="values"
                        :setFieldValue="setFieldValue"
                    />
                </div>
                <div v-show="currentStep === 3" class="step">
                    <StepThree :values="values" ref="step3formRef"
                               :setFieldValue="setFieldValue" />
                </div>
                <div v-show="currentStep === 4" class="step">
                    <StepFour :values="values" />
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-between mt-8 pt-6 border-t border-gray-200">
                    <button
                        type="button"
                        @click="prevStep"
                        :disabled="currentStep === 1"
                        class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <i class="fas fa-arrow-left mr-2"></i> Previous
                    </button>

                    <div class="flex space-x-3">
                        <button
                            v-if="currentStep < 4"
                            type="button"
                            @click="nextStep(validate, validateField, values, errors, setFieldValue)"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium flex items-center"
                        >
                            Next <i class="fas fa-arrow-right ml-2"></i>
                        </button>

                        <button
                            v-if="currentStep === 4"
                            type="button"
                            :disabled="isSubmitting"
                            @click="handleSubmit(values, setErrors)"
                            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium flex items-center"
                        >
                            <i v-if="isSubmitting" class="fas fa-spinner fa-spin mr-2"></i>
                            <i v-else class="fas fa-check mr-2"></i>
                            {{ isSubmitting ? 'Submitting...' : 'Submit Application' }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>
<script setup>
    import { Form } from 'vee-validate';
    import router from '@/router';
    import ApplicationHeader from '@/views/applications/ApplicationHeader.vue';
    import Header from '@/views/applications/components/Header.vue';
    import { useMeta } from '@/composables/use-meta';
    import { onMounted, ref } from 'vue';
    import { storeToRefs } from 'pinia';
    import Swal from 'sweetalert2';
    import StepOne from '@/views/applications/components/StepOne.vue';
    import StepTwo from '@/views/applications/components/StepTwo.vue';
    import StepThree from '@/views/applications/components/StepThree.vue';
    import StepFour from '@/views/applications/components/StepFour.vue';
    import StepProgress from '@/views/applications/components/StepProgress.vue';
    import { useApplicationForm } from '@/composables/useApplicationForm';
    import apiClient from '@/services/axios.ts';
    import { useAppStore } from '@/stores';
    import {
        step1FieldNames,
        step2FieldNames,
        step3FieldNames,
        validationSchema
    } from '@/validation/applicationSchemas';

    const props = defineProps({
        applicationData: {
            type: Object,
            default: () => ({})
        }
    });
    useMeta({ title: props.applicationData?.application?.id ? 'Edit Application' : 'New Application' });
    const formManger = useApplicationForm();
    const appStore = useAppStore();
    const { isLoading } = storeToRefs(appStore);
    const currentStep = ref(1);
    const form = props.applicationData;
    const isSubmitting = ref(false);
    const step3formRef = ref(null);
    const manualValidationStep1 = (values) => {
        const children = values.applicant.children || [];
        let valid = true;
        children.forEach((child, index) => {
            if (!child.name || child.name.trim() === '' || !child.age || child.age < 1) {
                valid = false;
            }
        });
        //TODO: validation needed for wife and husband name for married applicants
        return valid;
    };
    const manuallyValidateDocuments = (values) => {
        const docs = values.application.documents || [];
        let valid = true;
        // To tackle documents size validation, because we are using a custom validation rule for file size
        const requiredDocumentsDiv = document.getElementById('requiredDocuments');
        const alerts = requiredDocumentsDiv.querySelectorAll('[role="alert"]');
        if (alerts.length > 0) {
            valid = false;
        }
        return valid;
    };
    const showValidationToast = (stepNumber) => {
        Swal.fire({
            icon: 'error',
            text: 'Validation Error: Please fix the highlighted fields.',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
        window.scrollTo({
            top: 0
        });
    };

    const nextStep = async (validate, validateField, values, errors, setFieldValue) => {
        if (currentStep.value < 4) {
            if (currentStep.value === 1) {
                let childrenLength = values.applicant.children?.length || 0;
                const dynamicFieldsStep1 = [...step1FieldNames];

                if (values.application.application_type_id === 2) {
                    dynamicFieldsStep1.push('application.duplicate_details.reason');
                }

                for (let i = 0; i < childrenLength; i++) {
                    dynamicFieldsStep1.push(`applicant.children.${i}.name`);
                    dynamicFieldsStep1.push(`applicant.children.${i}.age`);
                }

                const results = await Promise.all(
                    dynamicFieldsStep1.map(field => validateField(field))
                );
                if (!manualValidationStep1(values) || !results.every(r => r.valid)) {
                    console.log('error...', errors);
                    showValidationToast(1);
                    return;
                }
                console.log('Step 1 is valid');
                await saveAsDraft(values, setFieldValue, 1);
            }

            if (currentStep.value === 2) {
                const results = await Promise.all(
                    step2FieldNames.map(field => validateField(field))
                );

                if (!results.every(r => r.valid)) {
                    console.log('error...', errors);
                    showValidationToast(1);
                    return;
                }
                console.log('Step 2 is valid');
                if (step3formRef.value && (!values.draft || values.application.documents.length === 0)) {
                    step3formRef.value.filterDocs();
                }
                await saveAsDraft(values, setFieldValue, 2);
            }

            if (currentStep.value === 3) {
                const dynamicFieldsStep3 = [...step3FieldNames];

                const results = await Promise.all(
                    dynamicFieldsStep3.map(field => validateField(field))
                );

                if (!manuallyValidateDocuments(values) || !results.every(r => r.valid)) {
                    console.log('error...', errors);
                    showValidationToast(1);
                    return;
                }
                console.log('Step 3 is valid', values);
                await saveAsDraft(values, setFieldValue, 3);
            }
            currentStep.value++;
        }
    };

    const prevStep = () => {
        if (currentStep.value > 1) {
            currentStep.value--;
        }
    };
    const saveAsDraft = async (values, setFieldValue, stepNumber = 1, isComplete = false) => {
        if (values.draft) {
            setFieldValue('draft.step', stepNumber);
            setFieldValue('draft.isComplete', isComplete);
            setFieldValue('draft.updatedAt', new Date().toISOString());

            await formManger.saveToIndexedDB(values);
            formManger.getDraftsCount().then(count => {
                appStore.updateDraftCount(count);
            });
        }
    };

    const handleSaveAsDraft = async (values) => {
        await formManger.saveToIndexedDB(values);
        Swal.fire({
            icon: 'success',
            text: 'Application saved as draft successfully.',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000
        });
        await router.push({ name: 'drafted-applications.view' });
    };
    const handleSubmit = async (values, setErrors) => {

        const formData = new FormData();

        const applicationData = { ...values.application };
        const applicantData = { ...values.applicant };

        // Append JSON parts
        formData.append('application', JSON.stringify(applicationData));
        formData.append('applicant', JSON.stringify(applicantData));

        try {
            let message = 'Submitted: New application has been submitted successfully.';
            isSubmitting.value = true;
            if (values.application.id) {
                await apiClient.put(`/api/applications/${values.application.id}`, { ...values });
                message = 'Submitted: Application has been updated successfully.';
            } else {
                await apiClient.post('/api/applications', { ...values });
            }
            Swal.fire({
                icon: 'success',
                text: message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
            if (values.draft) {
                await formManger.clearDraft(values.draft.id);
                await formManger.getDraftsCount().then(count => {
                    appStore.updateDraftCount(count);
                });
            }
            await router.push({ name: 'applications.view', params: { status: 'all' } });
        } catch (error) {
            if (error.response?.status === 422) {
                handleBackendErrors(error.response.data.errors, setErrors);
            } else {
                console.log('error', error);
            }
        } finally {
            isSubmitting.value = false;
        }
    };

    const handleBackendErrors = (errors, setErrors) => {
        const backendErrors = errors;
        const formattedErrors = {};

        Object.keys(backendErrors).forEach(key => {
            formattedErrors[key] = backendErrors[key][0]; // take first message
        });

        setErrors(formattedErrors);
        const firstErrorKey = Object.keys(backendErrors)[0];

        if (firstErrorKey.startsWith('applicant')) {
            currentStep.value = 1;
        } else if (firstErrorKey.startsWith('application.address')) {
            currentStep.value = 2;
        } else if (firstErrorKey.startsWith('application.documents')) {
            currentStep.value = 3;
        }

        console.log('Laravel errors:', backendErrors);
        Swal.fire({
            icon: 'error',
            title: 'Validation Error: Please fix the highlighted fields.',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    };


    const handleSelected = (payload, setFieldValue) => {
        form.application.certificate_type = payload.certificateType;
        form.applicant.identity_type = payload.identificationType;
        form.applicant.identity_number = payload.identificationNumber;
        form.application.application_type_id = payload.applicationType;

        if (payload.applicantDetails?.id) {
            setFieldValue('applicant', payload.applicantDetails);
        } else {
            setFieldValue('applicant.identity_number', payload.identificationNumber);
        }

        setFieldValue('application.certificate_type', payload.certificateType);
        setFieldValue('application.application_type_id', payload.applicationType);

        if (payload.identificationType === 'local') {
            setFieldValue('applicant.refugee_details.refugee_year', '');
            setFieldValue('applicant.refugee_details.refugee_from', '');
            setFieldValue('applicant.identity_type', 'local');
        } else {
            setFieldValue('applicant.identity_type', 'refugee');
        }
    };

    onMounted(() => {
        appStore.loadDropdowns();
    });

</script>
