<script setup>
    import BaseDialog from '@/components/BaseDialog.vue';
    import { computed, ref, watch } from 'vue';
    import { Field, Form } from 'vee-validate';
    import * as yup from 'yup';
    import apiClient from '@/services/axios';
    import { useAppStore } from '@/stores';
    import { storeToRefs } from 'pinia';
    import SelectField from '@/components/Form/SelectField.vue';
    import { string } from 'yup';

    const props = defineProps({
        modelValue: {
            type: Boolean,
            default: false
        },
        regionId:{
            required: true,
            type: [Number, String]
        },
        districtId: {
            required: true,
            type: [Number, String]
        }
    });

    const appStore = useAppStore();
    const { regions  } = storeToRefs(appStore);
    const districts = computed(() => {
        //@ts-ignore
        let districts = regions.value.find((region) => region.id === props.regionId)?.districts || [];
        return districts;
    });

    const emit = defineEmits(['update:modelValue', 'onItemAdded']);
    const showAddDialog = computed({
        get: () => props.modelValue,
        set: (value) => emit('update:modelValue', value)
    });

    // Yup validation schema
    const validationSchema = yup.object({
        name: yup.string().required('Tehsil Name is required'),
        urdu_name: yup.string().required('تحصیل کا نام ضروری ہے'),
        code: yup.string().nullable(),
        parent_id: yup.string().nullable(),
        type: yup.string().default('TEHSIL')
    });

    const form = ref({
        id: null,
        name: '',
        urdu_name: '',
        code: '',
        parent_id: props.districtId,
        type_original: 'TEHSIL',
        is_ajk_district: true
    });

    watch(()=>props.districtId,()=>{
        form.value.parent_id = props.districtId;
    })

    const handleDialogClose = () => {
        showAddDialog.value = false;
    };

    const handleSubmit = async (values, { resetForm }) => {
        try {
            const response = await apiClient.post('/api/demographies', values);
            resetForm();
            emit('onItemAdded', response.data?.data);
            handleDialogClose();
        } catch (error) {
            console.error('Submission failed:', error);
        }
    };

</script>

<template>
    <BaseDialog v-model="showAddDialog" title="نئی تحصیل شامل کریں" class="font-nastaleeq">
        <template #header-right>
            <button
                @click="handleDialogClose"
                class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 focus:outline-none"
            >
                <span class="text-2xl">&times;</span>
            </button>
        </template>

        <Form
            v-if="districtId && regionId"
            :validation-schema="validationSchema"
            v-slot="{ errors, isSubmitting, submitForm, resetForm }"
            :initial-values="form"
            @submit="handleSubmit"
        >
            <div class="space-y-4 p-4" dir="rtl">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        ریجن کا نام <span class="text-red-500">*</span>
                    </label>
                    <select
                        readonly
                        :value="regionId"
                        class="form-input font-nastaleeq text-right"
                    >
                        <option value="">منتخب کریں</option>
                        <option
                            v-for="option in regions"
                            :key="option.id"
                            :value="option.id"
                        >
                            {{ option.urdu_name || option.name }}
                        </option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        ضلع کا نام <span class="text-red-500">*</span>
                    </label>
                    <select
                        readonly
                        :value="districtId"
                        class="form-input font-nastaleeq text-right"
                    >
                        <option value="">منتخب کریں</option>
                        <option
                            v-for="option in districts"
                            :key="option.id"
                            :value="option.id"
                        >
                            {{ option.urdu_name || option.name }}
                        </option>
                    </select>
                </div>
                <!-- Name Field -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        District Name <span class="text-red-500">*</span>
                    </label>
                    <Field
                        autofocus
                        name="name"
                        type="text"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        :class="{ 'border-red-500': errors.name }"
                        placeholder="Enter name"
                    />
                    <p v-if="errors.name" class="mt-1 text-sm text-red-600">
                        {{ errors.name }}
                    </p>
                </div>

                <!-- Urdu Name Field -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 font-nastaleeq">
                        نئے ضلع کا نام <span class="text-red-500">*</span>
                    </label>
                    <Field
                        name="urdu_name"
                        v-urdu-input = "store.urduInputEnabled"
                        type="text"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 font-nastaleeq"
                        :class="{ 'border-red-500': errors.urdu_name }"
                        placeholder="اردو نام درج کریں"
                    />
                    <p v-if="errors.urdu_name" class="mt-1 text-sm text-red-600">
                        {{ errors.urdu_name }}
                    </p>
                </div>
                <!-- Type Field (Hidden) -->
                <Field name="id" type="hidden" />
                <Field name="parent_id" type="hidden" :value="districtId"/>
                <Field name="type_original" type="hidden" />
                <Field name="is_ajk_district" type="hidden" />
            </div>

            <div class="flex justify-end gap-3 mt-2 pt-2 border-t">
                <button
                    type="submit"
                    :disabled="isSubmitting"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                >
                    <span v-if="isSubmitting"
                          class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                    {{ isSubmitting ? 'Saving...' : 'Save' }}
                </button>
                <button
                    type="button"
                    @click="()=>{ resetForm(); handleDialogClose(); }"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium"
                >
                    Cancel
                </button>
            </div>
        </Form>
    </BaseDialog>
</template>

<style scoped>
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .animate-spin {
        animation: spin 0.6s linear infinite;
    }
</style>
