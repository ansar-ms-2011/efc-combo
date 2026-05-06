<template>
    <BaseDialog v-model="form.showDiaryModal" max-width="max-w-2xl" title="کارروائیوں کی ڈائری" subtitle="Diary of Actions" title-class="font-nastaleeq">
        <template #header-right>
            <button @click="handleDialogClose" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors focus:outline-none">
                <span class="text-2xl">&times;</span>
            </button>
        </template>

        <div class="p-4 sm:p-10 max-h-[70vh] overflow-y-auto custom-scrollbar bg-white">
            <!-- Loading State -->
            <div v-if="isLoading" class="flex flex-col justify-center items-center h-48">
                <i class="fa fa-spinner fa-spin fa-2x text-blue-500"></i>
                <p class="mt-2 text-gray-500 font-medium font-sans">Loading history...</p>
            </div>

            <!-- No Record State -->
            <div v-else-if="!workflowHistory.length" class="text-center py-10">
                <i class="fa fa-history text-gray-200 text-5xl mb-3"></i>
                <p class="text-gray-400 font-sans font-medium">No record found</p>
            </div>

            <!-- Timeline View -->
            <div v-else class="w-full">
                <div v-for="(logs, date) in groupedHistory" :key="date" class="mb-8">
                    <div class="ps-2 my-4 first:mt-0">
                        <h3 class="text-[11px] font-bold uppercase text-gray-500 tracking-widest border-b border-gray-300 pb-2">
                            {{ date }}
                        </h3>
                    </div>

                    <div v-for="(item, index) in logs" :key="item.id" class="flex gap-x-3 relative group">
                        <div class="w-16 text-end pt-1">
                            <span class="text-[11px] text-gray-600 font-medium whitespace-nowrap">
                                {{ formatTimeOnly(item.created_at) }}
                            </span>
                        </div>

                        <!-- Dot and Line -->
                        <div
                            class="relative last:after:hidden after:absolute after:top-7 after:bottom-0 after:start-3 after:-translate-x-[0.5px] after:border-s after:border-gray-300"
                        >
                            <div class="relative z-10 size-6 flex justify-center items-center">
                                <div class="size-2 rounded-full bg-gray-400 group-hover:bg-blue-500 transition-colors shadow-sm ring-4 ring-white"></div>
                            </div>
                        </div>

                        <div class="grow pt-0.5 pb-8">
                            <!-- Status -->
                            <h3 class="flex gap-x-1.5 font-bold text-gray-700 text-xs leading-tight uppercase tracking-tight">
                                <span v-if="item.from_status" class="text-gray-600">{{ item.from_status }}</span>
                                <i v-if="item.from_status" class="fa fa-arrow-right text-[10px] mt-1 text-gray-600"></i>
                                <span class="text-blue-600">{{ item.to_status || 'Initiated' }}</span>
                            </h3>

                            <!-- Remarks -->
                            <p class="mt-1.5 text-sm text-gray-500 leading-relaxed max-w-lg">
                                {{ item.remarks || 'No additional remarks provided.' }}
                            </p>

                            <!-- User Info  -->
                            <div class="mt-1 flex items-center gap-2">
                                <i class="fa fa-user-circle text-gray-600 text-lg"></i>
                                <span class="text-xs font-semibold text-gray-500 hover:text-blue-600 cursor-default transition-colors">
                                    {{ item.created_by.name }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <template #footer>
            <div class="flex justify-end p-4 border-t bg-gray-50/50">
                <button
                    class="px-8 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-bold text-xs shadow-sm active:scale-95"
                    @click="handleDialogClose"
                >
                    CLOSE
                </button>
            </div>
        </template>
    </BaseDialog>
</template>

<script setup>
    import { ref, watch, computed } from 'vue';
    import BaseDialog from '@/components/BaseDialog.vue';
    import apiClient from '@/services/axios.ts';
    import { formatDMY } from '@/mixin/index.ts';

    const props = defineProps({
        modelValue: Boolean,
        application: Object,
    });

    const emit = defineEmits(['update:modelValue']);
    const form = ref({ showDiaryModal: false });
    const workflowHistory = ref([]);
    const isLoading = ref(false);

    const groupedHistory = computed(() => {
        const groups = {};
        workflowHistory.value.forEach((item) => {
            const dateKey = formatDMY(item.created_at, false);
            if (!groups[dateKey]) groups[dateKey] = [];
            groups[dateKey].push(item);
        });
        return groups;
    });

    const formatTimeOnly = (dateString) => {
        if (!dateString) return '';
        return new Date(dateString).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true });
    };

    watch(
        () => props.modelValue,
        (val) => {
            form.value.showDiaryModal = val;
            if (val) fetchWorkflowHistory();
        },
    );

    const fetchWorkflowHistory = async () => {
        try {
            isLoading.value = true;
            const response = await apiClient.get(`/api/applications/${props.application?.id}/workflow-history`);
            workflowHistory.value = response.data.workflowHistory;
        } catch (error) {
            console.error('Error:', error);
        } finally {
            isLoading.value = false;
        }
    };

    const handleDialogClose = () => {
        form.value.showDiaryModal = false;
        emit('update:modelValue', false);
    };
</script>

<style scoped>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .step-content {
        width: 100%;
        box-sizing: border-box;
    }
    .font-nastaleeq {
        font-family: 'Noto Nastaliq Urdu', serif;
        line-height: 2.2;
    }
</style>
