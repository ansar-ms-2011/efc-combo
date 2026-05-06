<script setup lang="ts">
    import ApplicationForm from '@/views/applications/ApplicationForm.vue';
    import { useRoute, useRouter } from 'vue-router';
    import { onMounted, ref } from 'vue';
    import apiClient from '@/services/axios';

    const route = useRoute();
    const router = useRouter();
    const uuid = route.params.uuid;
    const applicationData = ref(null);
    const isLoading = ref(true);

    onMounted(async () => {
        try {
            const response = await apiClient.get(`/api/applications/${uuid}`);
            let data = response.data.data;
            console.log(data);
            data.headerModel = {
                mode: 'edit',
                certificateType: response.data.data.application.certificate_type,
                identificationType: response.data.data.applicant.identity_type,
                identificationNumber: response.data.data.applicant.identity_number,
                applicationType: response.data.data.application.application_type_id,
            };
            data.applicant.biometrics = response.data.data.applicant.biometrics || {};
            applicationData.value = data;
        } catch (error) {
            console.log(error);
        } finally {
            isLoading.value = false;
        }
    });
</script>

<template>
    <div v-if="isLoading" class="flex justify-center items-center min-h-96">
        <div class="text-center">
            <i class="fas fa-spinner fa-spin-pulse text-4xl text-blue-600 mb-4"></i>
            <p class="text-gray-600">Loading application data ...</p>
        </div>
    </div>
    <ApplicationForm v-else :applicationData="applicationData|| {}" />
</template>
