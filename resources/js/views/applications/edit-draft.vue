<script setup>
    import ApplicationForm from '@/views/applications/ApplicationForm.vue';
    import { useApplicationForm } from '@/composables/useApplicationForm';
    import { useRoute } from 'vue-router';
    import { onMounted, ref } from 'vue';

    const formManger = useApplicationForm();
    const route = useRoute();
    const draftId = route.params.draftId;
    const applicationData = ref(null);
    const isLoading = ref(true);

    onMounted(async () => {
        try {
            formManger.loadFromIndexedDB(draftId).then((data) => {
                data.headerModel.mode = 'edit';
                data.headerModel.certificateType = data.application.certificate_type;
                data.headerModel.identificationType = data.applicant.identity_type;
                data.headerModel.identificationNumber = data.applicant.identity_number;
                data.headerModel.applicationType = data.application.application_type_id;
                console.log(data);
                applicationData.value = data;
                isLoading.value = false;
            });
        } catch (error) {
            console.log(error);
        }
    });
</script>

<template>
    <div v-if="isLoading" class="flex justify-center items-center min-h-96">
        <div class="text-center">
            <i class="fas fa-spinner fa-spin-pulse text-4xl text-blue-600 mb-4"></i>
            <p class="text-gray-600">Loading draft application data ...</p>
        </div>
    </div>
    <ApplicationForm v-else :applicationData="applicationData || {}" />
</template>
