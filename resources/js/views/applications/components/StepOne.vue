<script setup lang="ts">
    import { ErrorMessage, Field, FieldArray, useField } from 'vee-validate';
    import { formatCnic, formatMobile } from '@/mixin';
    import { computed, Ref, watch } from 'vue';
    import { storeToRefs } from 'pinia';
    import { useAppStore } from '@/stores';
    import { Type } from '@/types';

    const store = useAppStore();

    const props = defineProps({
        form: {
            type: Object,
            required: true
        },
        values: {
            type: Object,
            required: true
        },
        setFieldValue: {
            type: Function,
            required: true
        },
        errors: {
            type: Object
        }
    });

    const maxDate = new Date()
    maxDate.setDate(maxDate.getDate() - 1)

    const emit = defineEmits(['next']);
    const {
        appointment_for_list,
        guardian_types,
        genders,
        religions,
        marital_statuses,
        duplicate_reasons,
    } = storeToRefs(useAppStore()) as {
        appointment_for_list: Ref<Type[]>;
        guardian_types: Ref<Type[]>;
        genders: Ref<Type[]>;
        religions: Ref<Type[]>;
        marital_statuses: Ref<Type[]>;
        duplicate_reasons: Ref<Type[]>;
    };

    const class_types = computed(() => {
        return [
            {name: 'first', urdu_name: 'اول'},
            {name: 'second', urdu_name: 'دوم'},
            {name: 'third', urdu_name: 'سوم'},
        ]
    })

    const maritalStatusMap = computed(() =>
        Object.fromEntries(marital_statuses.value.map(s => [s.id, s]))
    );

    const isMarried = computed(() => {
        const status = maritalStatusMap.value[props.values.applicant?.marital_status_id];
        return status?.name?.toLowerCase() === 'married';
    });

    const canHaveChildren = computed(() => {
        const status = maritalStatusMap.value[props.values.applicant?.marital_status_id];
        return status?.name?.toLowerCase() !== 'single';
    });

    const hasRefugeeError = computed(() => {
        return props.errors?.['applicant.identity_number'] || props.errors?.['applicant.refugee_details.refugee_from'] || props.errors?.['applicant.refugee_details.refugee_year'] || false;
    });

    const { value } = useField('applicant.identity_type');

    watch(value, (newVal, oldVal) => {
        let identityNumber = props.values.applicant.identity_number;
        if(newVal==='local'){
            props.setFieldValue('applicant.identity_number', formatCnic(identityNumber));
        }else{
            props.setFieldValue('applicant.identity_number', identityNumber?.replaceAll('-', ''));
        }
    });

</script>

<template>
    <div class="appplication-wrapper step1-wrapper" id="step1">
        <Field
            type="hidden"
            name="application.certificate_type"
        />
        <div class="flex flex-wrap justify-start  gap-3 mt-10" dir="rtl">
            <!-- Appointment For -->
            <div class="form-field basis-full sm:basis-[calc(50%-0.75rem)] lg:basis-[calc(25%-0.75rem)]">
                <label
                    class="block text-sm font-nastaleeq font-medium text-gray-700 mb-2 text-right"
                    dir="rtl">
                    جس کے لیے درخواست دی جا رہی ہے <span class="text-red-500">*</span>
                </label>
                <Field name="application.application_for_id" v-slot="{ field, errors }">
                    <select
                        :value="field.value"
                        v-bind="field"
                        dir="rtl"
                        class="form-input font-nastaleeq text-right border-gray-300"
                        :class="{ 'border-red-500 focus:border-red-500': errors.length }"
                    >
                        <option value="" disabled>منتخب کریں</option>

                        <option v-for="type in appointment_for_list" :key="type.id" :value="type.id">
                            {{ type.urdu_name || type.name }}
                        </option>
                    </select>
                </Field>

                <ErrorMessage name="application.application_for_id" class="text-red-500 font-nastaleeq" />
            </div>

            <!-- Missal No -->
            <div class="form-field basis-full sm:basis-[calc(50%-0.75rem)] lg:basis-[calc(25%-0.75rem)]">
                <label
                    class="block text-sm font-medium text-gray-700 mb-2 font-nastaleeq text-right"
                    dir="rtl">
                    مثل نمبر
                </label>
                <Field name="application.missal_no" v-slot="{ field, errors }">
                    <input
                        :value="field.value"
                        v-bind="field"
                        type="number"
                        dir="rtl"
                        class="form-input font-nastaleeq"
                        :class="{ 'border-red-500 focus:border-red-500': errors.length }"
                        placeholder="مسل نمبر درج کریں"
                    />
                </Field>
                <ErrorMessage name="application.missal_no" class="text-red-500 font-nastaleeq" />
            </div>

            <!-- QMatic Token -->
            <div class="form-field basis-full sm:basis-[calc(50%-0.75rem)] lg:basis-[calc(25%-0.75rem)]">
                <label
                    class="block text-sm font-medium font-nastaleeq text-gray-700 mb-2 text-right">
                    کیو میٹک ٹوکن نمبر
                </label>
                <Field name="application.appointment.qmatic_token"
                       v-slot="{ field, errors }">
                    <input v-bind="field"
                           dir="rtl" :value="field.value"
                           class="form-input text-right font-nastaleeq"
                           :class="{ 'border-red-500 focus:border-red-500': errors.length }"
                           placeholder="کیو میٹک ٹوکن درج کریں"
                    />
                </Field>
                <ErrorMessage name="application.appointment.qmatic_token" class="text-red-500 font-nastaleeq" />
            </div>
        </div>

        <!-- Type (Local/Refugee) -->
        <div class="border-t pt-6 mt-5 flex flex-wrap justify-end gap-3">
            <template v-if="values.applicant.identity_type==='refugee'">
                <div class="form-field basis-full sm:basis-[calc(50%-0.75rem)] lg:basis-[calc(25%-0.75rem)]">
                    <label class="block text-sm font-medium font-nastaleeq text-gray-700 mb-2 text-right">
                        جائے ہجرت<span class="text-red-500">*</span>
                    </label>
                    <Field name="applicant.refugee_details.refugee_from"
                           v-slot="{ field, errors }">
                        <input v-bind="field" type="text"
                               dir="rtl" :value="field.value"
                               class="form-input font-nastaleeq text-right" placeholder="جائے ہجرت"
                               :class="{ 'border-red-500 focus:border-red-500': errors.length }"
                        />
                    </Field>
                    <ErrorMessage name="applicant.refugee_details.refugee_from" class="text-red-500 font-nastaleeq" />
                </div>
                <div class="form-field basis-full sm:basis-[calc(50%-0.75rem)] lg:basis-[calc(25%-0.75rem)]">
                    <label class="block text-sm font-medium font-nastaleeq text-gray-700 mb-2 text-right">
                        سالِ ہجرت<span class="text-red-500">*</span>
                    </label>
                    <Field name="applicant.refugee_details.refugee_year"
                           v-slot="{ field, errors }">
                        <input v-bind="field" :value="field.value"
                               class="form-input font-nastaleeq" dir="rtl"
                               :class="{ 'border-red-500 focus:border-red-500': errors.length }"
                               placeholder="سالِ ہجرت" />
                    </Field>
                    <ErrorMessage name="applicant.refugee_details.refugee_year" class="text-red-500 font-nastaleeq" />
                </div>
            </template>
            <div class="form-field basis-full sm:basis-[calc(50%-0.75rem)] lg:basis-[calc(25%-0.75rem)]">
                <label class="block text-sm font-medium font-nastaleeq text-gray-700 mb-2 text-right">
                    {{ values.applicant.identity_type==='local'? 'شناختی کارڈ نمبر': 'مہاجر کارڈ نمبر' }}
                </label>
                <Field name="applicant.identity_number" v-slot="{ field, errors }"
                >
                    <input v-bind="field" :value="field.value"
                           maxlength="15" dir="rtl" disabled
                           class="form-input font-nastaleeq"
                           :class="{ 'border-red-500 focus:border-red-500': errors.length }"
                           :placeholder="values.applicant.identity_type==='local'? 'شناختی کارڈ نمبر': 'مہاجر کارڈ نمبر'" />
                </Field>
                <ErrorMessage name="applicant.identity_number" class="text-red-500 font-nastaleeq" />
            </div>
            <div class="flex items-end flex-col justify-end basis-full sm:basis-[calc(50%-075rem)] lg:basis-[calc(25%-0.75rem)]"
                 :class="{'mb-5': hasRefugeeError}"
            >
                <label class="block text-sm font-medium font-nastaleeq text-right text-nowrap text-gray-700">
                    درخواست گزار کی قسم
                </label>
                <div class="flex flex-row gap-2">
                    <label for="is_refugee_1"
                           :class="{ 'bg-blue-50 border-blue-500': values.applicant.identity_type==='refugee' }"
                           class="font-nastaleeq font-normal flex items-center justify-center mb-0 p-2 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-50 w-full">
                        <Field
                            type="radio"
                            name="applicant.identity_type"
                            value="refugee"
                            id="is_refugee_1"
                            class="mr-1 md:w-[95px]"
                        />
                        مہاجر
                    </label>
                    <label for="is_refugee_0"
                           :class="{ 'bg-blue-50 border-blue-500': values.applicant.identity_type==='local' }"
                           class="font-nastaleeq font-normal flex items-center justify-center  mb-0  p-2 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-50 w-full">
                        <Field
                            type="radio"
                            name="applicant.identity_type"
                            value="local"
                            id="is_refugee_0"
                            class="mr-1 md:w-[95px]"
                        />
                        مقامی
                    </label>
                </div>
            </div>
        </div>

        <div class="border-t pt-6 mt-5">
            <div class="flex items-center justify-end mb-4">
                <h4 class="text-lg font-semibold text-center font-nastaleeq">درخواست دہندہ کی
                    معلومات</h4>
            </div>

            <!-- ONE GRID – 3 COLUMNS (Designation moved to Documentation tab) -->
            <div class="flex flex-wrap justify-start gap-3" dir="rtl">
                <!-- Applicant Name -->
                <div class="form-field basis-full sm:basis-[calc(50%-0.75rem)] lg:basis-[calc(25%-0.75rem)]">
                    <label
                        class="block text-sm font-medium font-nastaleeq text-gray-700 mb-2 text-right"
                        dir="rtl">
                        درخواست دہندہ کا مکمل نام <span class="text-red-500">*</span>
                    </label>
                    <Field name="applicant.full_name"
                           v-slot="{ field, errors }">
                        <input v-urdu-input = "store.urduInputEnabled"  type="text" required dir="rtl" v-bind="field"
                               class="form-input font-nastaleeq" :value="field.value"
                               :class="{ 'border-red-500 focus:border-red-500': errors.length }"
                               placeholder="درخواست دہندہ کا مکمل نام" />
                    </Field>
                    <ErrorMessage name="applicant.full_name" class="text-red-500 font-nastaleeq" />
                </div>

                <!-- Father / Husband Name -->
                <div class="form-field basis-full sm:basis-[calc(50%-0.75rem)] lg:basis-[calc(25%-0.75rem)]">
                    <label
                        class="block text-sm font-medium font-nastaleeq text-gray-700 mb-2 text-right"
                        dir="rtl">
                        <span v-if="values.application.certificate_type === 'domicile'">والد کا نام</span>
                        <span v-else>والد / شوہر کا نام</span>
                        <span class="text-red-500">*</span>
                    </label>
                    <Field name="applicant.father_name"
                           v-slot="{ field, errors }">
                        <input v-urdu-input = "store.urduInputEnabled" type="text" required dir="rtl" v-bind="field"
                               :class="{ 'border-red-500 focus:border-red-500': errors.length }"
                               class="form-input font-nastaleeq text-right"
                               placeholder="والد / شوہر کا نام" />
                    </Field>
                    <ErrorMessage name="applicant.father_name" class="text-red-500 font-nastaleeq" />
                </div>

                <!-- Date of Birth -->
                <div class="form-field basis-full sm:basis-[calc(50%-0.75rem)] lg:basis-[calc(25%-0.75rem)]">
                    <label
                        class="block text-sm font-nastaleeq font-medium text-gray-700 mb-2 text-right"
                        dir="rtl">
                        پیدائش کی تاریخ <span class="text-red-500">*</span>
                    </label>
                    <Field name="applicant.dob" v-slot="{ field, handleChange, value, errorMessage }">
                        <VueDatePicker
                            placeholder="تاریخ پیدائش درج کریں"
                            class="urdu-datepicker"
                            :class="{ 'dp-invalid': errorMessage }"
                            :model-value="value"
                            @update:model-value="handleChange"
                            auto-apply
                            :invalid="true"
                            model-type="yyyy-MM-dd"
                            :formats="{ input: 'dd-MM-yyyy' }"
                            :time-config="{ enableTimePicker: false }"
                            :max-date="maxDate"
                            text-input
                        />
                    </Field>
                    <ErrorMessage name="applicant.dob" class="text-red-500 font-nastaleeq" />
                </div>

                <!-- Phone -->
                <div class="form-field basis-full sm:basis-[calc(50%-0.75rem)] lg:basis-[calc(25%-0.75rem)]">
                    <label
                        class="block  font-nastaleeq text-sm font-medium text-gray-700 mb-2 text-right"
                        dir="rtl">
                        موبائل نمبر <span class="text-red-500">*</span>
                    </label>
                    <Field name="applicant.phone"
                           v-slot="{ field, errors, handleChange }">
                        <input id="telephoneInput" type="tel" required class="form-input text-center" :value="field.value"
                               @input="(e:any) => handleChange(formatMobile(e.target?.value))"
                               :class="{ 'border-red-500 focus:border-red-500': errors.length }"
                               placeholder="0333-1234567" dir="ltr" />
                    </Field>
                    <ErrorMessage name="applicant.phone" class="text-red-500 font-nastaleeq" />
                </div>

                <!-- Gender -->
                <div class="form-field basis-full sm:basis-[calc(50%-0.75rem)] lg:basis-[calc(25%-0.75rem)]">
                    <label
                        class="block text-sm font-nastaleeq font-medium text-gray-700 mb-2 text-right"
                        dir="rtl">
                        جنس <span class="text-red-500">*</span>
                    </label>
                    <Field name="applicant.gender_id"
                           v-slot="{ field, errors }">
                        <select required dir="rtl" v-bind="field" :value="field.value"
                                class="form-input font-nastaleeq text-right"
                                :class="{ 'border-red-500 focus:border-red-500': errors.length }"
                        >
                            <option value="">منتخب کریں</option>
                            <option v-for="type in genders" :key="type.id" :value="type.id">
                                {{ type.urdu_name || type.name }}
                            </option>
                        </select>
                    </Field>
                    <ErrorMessage name="applicant.gender_id" class="text-red-500 font-nastaleeq" />
                </div>

                <!-- Religion -->
                <div class="form-field basis-full sm:basis-[calc(50%-0.75rem)] lg:basis-[calc(25%-0.75rem)]">
                    <label
                        class="block font-nastaleeq text-sm font-medium text-gray-700 mb-2 text-right"
                        dir="rtl">
                        مذہب <span class="text-red-500">*</span>
                    </label>
                    <Field name="applicant.religion_id"
                           v-slot="{ field, errors }">
                        <select dir="rtl" v-bind="field"
                                class="form-input font-nastaleeq text-right" :value="field.value"
                                :class="{ 'border-red-500 focus:border-red-500': errors.length }"
                        >
                            <option value="" disabled>منتخب کریں</option>
                            <option v-for="type in religions" :key="type.id" :value="type.id">
                                {{ type.urdu_name || type.name }}
                            </option>
                        </select>
                    </Field>
                    <ErrorMessage name="applicant.religion_id" class="text-red-500 font-nastaleeq" />
                </div>

                <!-- Marital status -->
                <div class="form-field basis-full sm:basis-[calc(50%-0.75rem)] lg:basis-[calc(25%-0.75rem)]">
                    <label
                        class="block text-sm font-medium font-nastaleeq text-gray-700 mb-2 text-right"
                        dir="rtl">ازدواجی حیثیت <span class="text-red-500">*</span></label>
                    <Field name="applicant.marital_status_id" v-slot="{ field, errors }">
                        <select v-bind="field" :value="field.value"
                                class="form-input font-nastaleeq text-right"
                                :class="{ 'border-red-500 focus:border-red-500': errors.length }"
                                dir="rtl">
                            <option value="" disabled>منتخب کریں</option>
                            <option v-for="type in marital_statuses" :key="type.id" :value="type.id">
                                {{ type.urdu_name || type.name }}
                            </option>
                        </select>
                    </Field>
                    <ErrorMessage name="applicant.marital_status_id" class="text-red-500 font-nastaleeq" />
                </div>

                <!-- Husband/Wife Name -->
                <div v-if="isMarried" class="form-field basis-full sm:basis-[calc(50%-0.75rem)] lg:basis-[calc(25%-0.75rem)]">
                    <label
                        class="block text-sm font-medium font-nastaleeq text-gray-700 mb-2 text-right"
                        dir="rtl">بیوی/شوہر کا نام<span class="text-red-500">*</span></label>
                    <Field name="applicant.wife_husband_name" v-slot="{ field, errors }">
                        <input v-urdu-input = "store.urduInputEnabled" type="text" v-bind="field" :value="field.value" dir="rtl"
                               class="form-input font-nastaleeq text-right"
                               :class="{ 'border-red-500 focus:border-red-500': errors.length }"
                        >
                    </Field>
                    <ErrorMessage name="applicant.wife_husband_name" class="text-red-500 font-nastaleeq" />
                </div>

                <!-- Guardian -->
                <div class="form-field basis-full sm:basis-[calc(50%-0.75rem)] lg:basis-[calc(25%-0.75rem)]">
                    <label
                        class="block font-nastaleeq text-sm font-medium text-gray-700 mb-2 text-right"
                        dir="rtl">
                        سرپرست
                    </label>
                    <Field name="applicant.guardian_type_id"
                           v-slot="{ field, errors }">
                        <select dir="rtl" v-bind="field" :value="field.value"
                                class="form-input font-nastaleeq text-right"
                                :class="{ 'border-red-500 focus:border-red-500': errors.length }"
                        >
                            <option value="" disabled>منتخب کریں</option>
                            <option v-for="type in guardian_types" :key="type.id" :value="type.id">
                                {{ type.urdu_name || type.name }}
                            </option>
                        </select>
                    </Field>
                    <ErrorMessage name="applicant.guardian_type_id" class="text-red-500 font-nastaleeq" />
                </div>

                <!-- Father CNIC -->
                <div class="form-field basis-full sm:basis-[calc(50%-0.75rem)] lg:basis-[calc(25%-0.75rem)]">
                    <label
                        class="block text-sm font-medium font-nastaleeq text-gray-700 mb-2 text-right"
                        dir="rtl">
                        <span v-if="values.applicant.identity_type==='local'">والد کا شناختی کارڈ نمبر</span>
                        <span v-else>والد کا مہاجر کارڈ نمبر</span>
                    </label>
                    <Field name="applicant.father_identity_number"
                           v-slot="{ field, errors, handleChange}">
                        <input id="fatherIdentityNo" type="text" v-bind="field" :value="field.value"
                               @input="(e:any) => handleChange(formatCnic(e.target?.value))"
                               class="form-input text-center" placeholder="12345-1234567-1"
                               :class="{ 'border-red-500 focus:border-red-500': errors.length }"
                        />
                    </Field>
                    <ErrorMessage name="applicant.father_identity_number" class="text-red-500 font-nastaleeq" />
                </div>

                <!-- Drjah Field (Only for State or Both) -->
                <div class="form-field basis-full sm:basis-[calc(50%-0.75rem)] lg:basis-[calc(25%-0.75rem)]"
                     v-if="values.application.certificate_type === 'state' || values.application.certificate_type === 'both'">
                    <label
                        class="block text-sm font-medium font-nastaleeq text-gray-700 mb-2 text-right"
                        dir="rtl">درجہ<span class="text-red-500">*</span></label>
                    <Field name="applicant.state_subject_class" v-slot="{ field, errors }">
                        <select dir="rtl" v-bind="field" :value="field.value"
                                class="form-input font-nastaleeq text-right"
                                :class="{ 'border-red-500 focus:border-red-500': errors.length }"
                        >
                            <option value="" disabled>درجہ منتخب کریں</option>
                            <option v-for="type in class_types" :key="type.urdu_name" :value="type.urdu_name">
                                {{ type.urdu_name || type.name }}
                            </option>
                        </select>
                    </Field>
                    <ErrorMessage name="applicant.state_subject_class" class="text-red-500 font-nastaleeq" />
                </div>

                <!-- Sakinah -->
                <div class="form-field basis-full sm:basis-[calc(50%-0.75rem)] lg:basis-[calc(25%-0.75rem)]">
                    <label
                        class="block font-nastaleeq text-sm font-medium text-gray-700 mb-2 text-right"
                        dir="rtl">ساکنہ</label>
                    <Field name="applicant.residence_place"
                           v-slot="{ field, errors }">
                        <input v-urdu-input = "store.urduInputEnabled" v-bind="field" :value="field.value"  type="text" dir="rtl"
                               class="form-input font-nastaleeq text-right"
                               :class="{ 'border-red-500 focus:border-red-500': errors.length }"
                               placeholder="ساکنہ">
                    </Field>
                    <ErrorMessage name="applicant.residence_place" class="text-red-500 font-nastaleeq" />
                </div>

                <!--  Identity Mark -->
                <div class="form-field basis-full sm:basis-[calc(50%-0.75rem)] lg:basis-[calc(25%-0.75rem)]">
                    <label
                        class="block text-sm font-nastaleeq font-medium text-gray-700 mb-2 text-right"
                        dir="rtl">شناختی
                        علامت <span class="text-red-500">*</span></label>
                    <Field name="applicant.identity_symbol"
                           v-slot="{ field, errors }">
                        <input v-urdu-input = "store.urduInputEnabled" type="text" required dir="rtl" v-bind="field"
                               class="form-input font-nastaleeq text-right" :value="field.value"
                               :class="{ 'border-red-500 focus:border-red-500': errors.length }"
                               placeholder="شناختی علامت" />
                    </Field>
                    <ErrorMessage name="applicant.identity_symbol" class="text-red-500 font-nastaleeq" />
                </div>

                <!-- Place of Birth -->
                <div class="form-field basis-full sm:basis-[calc(50%-0.75rem)] lg:basis-[calc(25%-0.75rem)]">
                    <label
                        class="block text-sm font-nastaleeq font-medium text-gray-700 mb-2 text-right"
                        dir="rtl">
                        پیدائش کی جگہ
                    </label>
                    <Field name="applicant.pob"
                           v-slot="{ field, errors }">
                        <input v-urdu-input = "store.urduInputEnabled" v-bind="field" type="text" dir="rtl"
                               class="form-input font-nastaleeq text-right"
                               :class="{ 'border-red-500 focus:border-red-500': errors.length }"
                               placeholder="جائے پیدائش" />
                    </Field>
                    <ErrorMessage name="applicant.pob" class="text-red-500 font-nastaleeq" />
                </div>

                <!-- Occupation -->
                <div class="form-field basis-full sm:basis-[calc(50%-0.75rem)] lg:basis-[calc(25%-0.75rem)]">
                    <label
                        class="block text-sm font-medium font-nastaleeq text-gray-700 mb-2 text-right"
                        dir="rtl">پیشہ</label>
                    <Field name="applicant.occupation"
                           v-slot="{ field, errors }">
                        <input v-urdu-input = "store.urduInputEnabled" type="text" dir="rtl" v-bind="field" :value="field.value"
                               class="form-input font-nastaleeq text-right"
                               :class="{ 'border-red-500 focus:border-red-500': errors.length }"
                               placeholder="پیشہ">
                    </Field>
                    <ErrorMessage name="applicant.occupation" class="text-red-500 font-nastaleeq" />
                </div>

                <!-- Email -->
                <div class="form-field basis-full sm:basis-[calc(50%-0.75rem)] lg:basis-[calc(25%-0.75rem)]">
                    <label
                        class="block text-sm font-medium font-nastaleeq text-gray-700 mb-2 text-right"
                        dir="rtl">
                        ای میل
                    </label>
                    <Field name="applicant.email"
                           v-slot="{ field, errors }">
                        <input type="email" required class="form-input text-right font-nastaleeq"
                               placeholder="ای میل" v-bind="field" :value="field.value"
                               :class="{ 'border-red-500 focus:border-red-500': errors.length }"
                        />
                    </Field>
                    <ErrorMessage name="applicant.email" class="text-red-500 font-nastaleeq" />
                </div>
            </div>
        </div>

        <!-- Children Table -->
        <div class="my-8" v-if="canHaveChildren">
            <FieldArray name="applicant.children" v-slot="{ fields, push, remove }">
                <div class="flex justify-start gap-4 items-center mb-4" dir="rtl">
                    <h4
                        class="text-lg font-semibold font-nastaleeq text-gray-700"
                        dir="rtl"
                    >
                        بچوں کی تفصیلات
                    </h4>

                    <button
                        type="button"
                        @click="push({ id: null, applicant_id: null,application_id: null, name: '', age: '' })"
                        class="px-3 py-1 bg-gray-400 text-white rounded hover:bg-blue-700"
                    >
                        <i class="fa fa-plus"></i>
                    </button>

                </div>
                <table class="min-w-full border border-gray-300" v-if="values.applicant.children.length > 0">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-2 text-center font-nastaleeq">کاروائیاں</th>
                        <th class="border px-4 py-2 text-center font-nastaleeq"> بچے کی عمر</th>
                        <th class="border px-4 py-2 text-center font-nastaleeq">بچے کا نام</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(fieldItem, index) in fields" :key="fieldItem.key">
                        <Field name="applicant.children[${index}].id" type="hidden" />
                        <Field name="applicant.children[${index}].applicant_id" type="hidden" />
                        <Field name="applicant.children[${index}].application_id" type="hidden" />
                        <!-- Actions -->
                        <td class="border px-4 py-2 text-center">
                            <button
                                type="button"
                                @click="remove(index)"
                                class="px-2 py-1 bg-red-300 text-white rounded hover:bg-red-700"
                            >
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                        <!-- Child Age -->
                        <td class="border px-4 py-2">
                            <Field
                                :name="`applicant.children[${index}].age`"
                                v-slot="{ field, errors }"
                            >
                                <input
                                    type="number"
                                    v-bind="field"
                                    min="0"
                                    max="100"
                                    dir="rtl"
                                    class="form-input text-center font-nastaleeq"
                                    :class="{ 'border-red-500': errors.length }"
                                    placeholder="بچے کی عمر"
                                />
                            </Field>

                            <ErrorMessage
                                :name="`applicant.children[${index}].age`"
                                class="text-red-500 font-nastaleeq"
                            />
                        </td>

                        <!-- Child Name -->
                        <td class="border px-4 py-2">
                            <Field
                                :name="`applicant.children[${index}].name`"
                                v-slot="{ field, errors }"
                            >
                                <input
                                    v-urdu-input = "store.urduInputEnabled"
                                    type="text"
                                    v-bind="field"
                                    dir="rtl"
                                    class="form-input font-nastaleeq text-right"
                                    :class="{ 'border-red-500': errors.length }"
                                    placeholder="بچے کا نام"
                                />
                            </Field>

                            <ErrorMessage
                                :name="`applicant.children[${index}].name`"
                                class="text-red-500 font-nastaleeq"
                            />
                        </td>
                    </tr>
                    </tbody>
                </table>
            </FieldArray>
        </div>
        <div v-if="values.application.application_type_id===2" class="space-y-4">
            <div class="border-b"></div>
            <div class="mt-10">
                <label
                    class="block text-sm font-nastaleeq font-medium text-gray-700 mb-2 text-right"
                    dir="rtl">
                     وجہ منتخب کریں<span class="text-red-500">*</span>
                </label>
                <Field name="application.duplicate_details.reason_type_id" v-slot="{ field, errors }">
                    <select
                        :value="field.value"
                        v-bind="field"
                        dir="rtl"
                        class="form-input font-nastaleeq text-right border-gray-300"
                        :class="{ 'border-red-500 focus:border-red-500': errors.length }"
                    >
                        <option value="" disabled>منتخب کریں</option>

                        <option v-for="type in duplicate_reasons" :key="type.id" :value="type.id">
                            {{ type.urdu_name || type.name }}
                        </option>
                    </select>
                </Field>

                <ErrorMessage name="application.duplicate_details.reason_type_id" class="text-red-500 font-nastaleeq" />
            </div>
            <div class="form-field basis-full sm:basis-[calc(50%-0.75rem)] lg:basis-[calc(25%-0.75rem)]">
                <label
                    class="block text-sm font-nastaleeq font-medium text-gray-700 mb-2 text-right"
                    dir="rtl">ڈپلیکیٹ درخواست کے لیے ریمارکس</label>
                <Field name="application.duplicate_details.reason" v-slot="{ field, errors }">
                    <textarea v-urdu-input = "store.urduInputEnabled" v-bind="field" :value="field.value" dir="rtl"
                              class="form-textarea font-nastaleeq" rows="3"
                              :class="{ 'border-red-500 focus:border-red-500': errors.length }"
                              placeholder="وجہ یہاں درج کریں" />
                </Field>
                <ErrorMessage name="application.duplicate_details.reason" class="text-red-500 font-nastaleeq" />
            </div>
        </div>
    </div>
</template>

<style scoped>
    .dp-invalid.dp__theme_light {
        --dp-border-color: #ff0000;
        --dp-menu-border-color: #ff0000;
        --dp-danger-color: #e53935;
        --dp-border-color-hover: #e53935;
    }
</style>
<style>
    :root {
        --dp-border-radius: 0.375rem;
    }

    .dp__input {
        font-family: 'Jameel-Noori-Nastaleeq', serif;
        font-size: 14px;
        text-align: center;
        padding-right: 30px;
        color: rgb(14 23 38) !important;
    }
</style>
