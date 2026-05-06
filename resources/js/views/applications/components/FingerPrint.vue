<script setup lang="ts">
    import { ErrorMessage, Field, FieldArray} from 'vee-validate';
    import { computed, ref } from 'vue';
    import fingerprintService from '@/services/fingerprintService';
    import Swal from 'sweetalert2';

    const props = defineProps({
        fingerType: {
            type: String,
            default: 'thumb'
        },
        label: {
            type: String,
            default: 'انگوٹھا (THUMB)'
        },
        src: {
            type: String,
            default: null
        },
        setFieldValue: {
            type: Function,
            required: false
        }
    });
    const emit = defineEmits(['fingerprintCaptured']);
    const capturingFingerprint = ref(false);
    const inputRef = ref(null);
    const capturedImage = ref(null);
    const preview = computed(() => capturedImage.value || props.src);
    // console.log(props.src, preview.value)
    const captureFingerprint = async () => {
        try {
            // Enable capture mode
            capturingFingerprint.value = true;
            const result = await fingerprintService.captureFingerprint();
            if (result) {
                capturedImage.value = result.image;

                //update field value of veeValidate Form
                emit('fingerprintCaptured', {
                    type: props.fingerType,
                    image: result.image,
                    featureSet: result.featureSet
                });
            }
        } catch (error) {
            console.error('Fingerprint capture error:', error);
        } finally {
            capturingFingerprint.value = false;
        }
    };

    const handleThumbUpload = (event) => {
        const file = event.target.files[0];
        if (!file) return;

        if (!file.type.startsWith('image/')) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid File',
                text: 'Please upload an image file (JPG, PNG)',
                timer: 3000
            });
            return;
        }

        if (file.size > 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'File Too Large',
                text: 'Thumb impression image must be less than 1MB',
                timer: 3000
            });
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            emit('fingerprintCaptured', {
                type: props.fingerType,
                image: e.target?.result,
                featureSet: null
            });

            const labels = {
                thumb: 'Thumb',
                index: 'Index',
                middle: 'Middle',
                ring: 'Ring',
                little: 'Little'
            };

            Swal.fire({
                icon: 'success',
                title: 'Upload Successful',
                text: `${labels[props.fingerType]} thumb impression uploaded`,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
        };
        reader.readAsDataURL(file);
    };
</script>

<template>
    <div class="flex flex-col gap-2">
        <div class="p-2 bg-white rounded-lg transition-all duration-200 relative border border-yellow-100 hover:border-yellow-500 hover:shadow-md">
            <label class="block text-[16px] font-bold font-nastaleeq text-yellow-700 mb-2 text-center" dir="rtl">{{ label }}</label>
            <div class="flex flex-col items-center">
                <div
                    class="relative w-32 h-32 mb-2 border-2 border-dashed border-yellow-400 rounded-lg flex items-center justify-center bg-yellow-50">
                    <img v-if="preview" :src="preview"
                         :alt="`${props.fingerType}-image`" class="w-full h-full object-contain p-1">
                    <div v-else class="text-center">
                        <i class="fa-solid fa-fingerprint text-yellow-400 fa-2xl"></i>
                    </div>
                </div>

                <input ref="inputRef" accept="image/*" class="hidden" type="file"
                       @change="handleThumbUpload">

                <div class="flex gap-1 mt-1">
                    <button
                        class="px-4 py-2 text-white text-[18px] rounded transition-colors duration-200 flex items-center gap-1 font-nastaleeq bg-green-500 hover:bg-green-600"
                        :class="capturingFingerprint ? 'bg-red-500 cursor-not-allowed' : 'bg-green-500 hover:bg-green-600'"
                        type="button" @click.stop="captureFingerprint">
                        {{ capturedImage ? 'فنگر پرنٹ دوبارہ لیں' : 'فنگر پرنٹ اسکین کریں' }}
                    </button>
                </div>

                <div v-if="capturingFingerprint"
                     class="mt-2 text-[8px] font-bold text-red-500 animate-pulse">
                    ⚡ Ready...
                </div>
            </div>
        </div>
        <div class="flex flex-col gap-2 items-center">
            <Field :name="`application.biometrics.${fingerType}`" type="hidden" />
            <ErrorMessage
                :name="`application.biometrics.${fingerType}`"
                class="text-red-500 font-nastaleeq"
            />
        </div>
    </div>
</template>
