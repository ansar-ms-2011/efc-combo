<script setup>
    import { ErrorMessage, Field } from 'vee-validate';

    const props = defineProps({
        required: { type: Boolean, default: false },
        name: { type: String, required: true },
        label: { type: String, required: true },
        type: { type: String, default: 'text' }, // text, textarea, select, radio
        placeholder: { type: String, default: '' },
        options: { type: Array, default: () => [] },
        dir: { type: String, default: 'rtl' },
        formatter: { type: Function, default: null },
        rows: { type: Number, default: 1 },
        wrapperClass: { type: String, default: '' },
        labelClass: { type: String, default: '' },
        inputClass: { type: String, default: '' },
        showPlusButton: { type: Boolean, default: false },
        enableUrdu: { type: Boolean, default: false } // add this
    });
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

            <!-- TEXT INPUT -->
            <input
                v-if="type === 'text'"
                :value="field.value"
                :dir="dir"
                v-urdu-input="enableUrdu"
                type="text"
                class="form-input font-nastaleeq text-right"
                :placeholder="placeholder"
                :class="{ 'border-red-500 focus:border-red-500': errors.length }"
                @input="e => formatter ? handleChange(formatter(e.target.value)) : handleChange(e.target.value)"
            />

            <!-- TEXTAREA -->
            <textarea
                v-else-if="type === 'textarea'"
                v-bind="field"
                v-urdu-input="enableUrdu"
                :dir="dir"
                :rows="rows"
                class="form-input font-nastaleeq text-right"
                :placeholder="placeholder"
                :class="{ 'border-red-500 focus:border-red-500': errors.length }"
            />

            <!-- SELECT -->
            <div class="flex items-center gap-1" v-else-if="type === 'select'">
                <!-- + BUTTON -->
                <button

                    type="button"
                    class="px-1 py-2 bg-gray-300 text-white rounded hover:bg-gray-500"
                >
                    <i class="fas fa-plus"></i>
                </button>
                <select
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

            <!-- RADIO -->
            <div v-else-if="type === 'radio'" class="flex gap-4 justify-end">
                <label
                    v-for="option in options"
                    :key="option.value"
                    class="flex items-center gap-1 font-nastaleeq"
                >
                    <input
                        type="radio"
                        :value="option.value"
                        v-bind="field"
                    />
                    {{ option.label }}
                </label>
            </div>

        </Field>

        <!-- Error -->
        <ErrorMessage
            :name="name"
            class="text-red-500 font-nastaleeq text-sm"
        />
    </div>
</template>
