<script setup>
    import { ErrorMessage, Field } from 'vee-validate';

    const props = defineProps({
        required: { type: Boolean, default: false },
        name: { type: String, required: true },
        label: { type: String, required: true },
        placeholder: { type: String, default: '' },
        options: { type: Array, default: () => [] },
        dir: { type: String, default: 'rtl' },
        formatter: { type: Function, default: null },
        rows: { type: Number, default: 1 },
        wrapperClass: { type: String, default: '' },
        labelClass: { type: String, default: '' },
        inputClass: { type: String, default: '' },
        showPlusButton: { type: Boolean, default: false },
        disabled: { type: Boolean, default: false },
        disablePlusButton: { type: Boolean, default: false },
    });
    const emit = defineEmits(['onAddClick']);
</script>

<template>
    <div :class="wrapperClass" dir="ltr">
        <!-- Label -->
        <label
            class="block font-nastaleeq text-sm font-medium text-gray-700 mb-2 text-right"
            :dir="dir"
        >
            {{ label }} <span v-if="required" class="text-red-500">*</span>
        </label>

        <Field :name="name" v-slot="{ field, errors, handleChange }">
            <div class="flex items-center gap-1">
                <button
                    :disabled="disablePlusButton"
                    v-if="showPlusButton"
                    type="button"
                    @click="emit('onAddClick')"
                    class="flex items-center justify-center w-10 h-10 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <i class="fas fa-plus text-sm"></i>
                </button>
                <select
                    :disabled="disabled"
                    v-bind="field"
                    class="form-input font-nastaleeq text-right"
                    :class="{ 'border-red-500 focus:border-red-500': errors.length }"
                >
                    <option value="">منتخب کریں</option>
                    <option
                        v-for="option in options"
                        :key="option.id"
                        :value="option.id"
                    >
                        {{ option.urdu_name || option.name }}
                    </option>
                </select>
            </div>
        </Field>
        <!-- Error -->
        <ErrorMessage
            :name="name"
            class="text-red-500 font-nastaleeq text-sm ms-9"
        />
    </div>
</template>
