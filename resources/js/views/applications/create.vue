<script setup lang="ts">
    import { useApplicationForm } from '@/composables/useApplicationForm';
    import ApplicationForm from '@/views/applications/ApplicationForm.vue';
    import { onMounted } from 'vue';

    const { form, generateDraftId } = useApplicationForm();
    form.draft.id = generateDraftId();
    form.draft.step = 1;
    form.draft.isComplete = false;

    onMounted(() => {
        //-----------Do nothing here-----------//
    })
</script>

<template>
    <ApplicationForm v-if="form" :applicationData="form" />
</template>
