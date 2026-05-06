<template>
    <BaseDialog v-model="showPreview" :title="title" max-width="max-w-3xl">
        <div class="relative h-[70vh] w-full overflow-auto">
            <!-- Loading state while image loads -->
            <div v-if="isLoading" class="flex justify-center items-center h-96">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin-pulse text-4xl text-blue-600 mb-4"></i>
                    <p class="text-gray-600">Loading image...</p>
                </div>
            </div>

            <!-- Error state if image fails to load -->
            <div v-else-if="error" class="flex justify-center items-center h-96">
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle text-4xl text-red-600 mb-4"></i>
                    <p class="text-red-600">{{ error }}</p>
                    <button
                        @click="retryLoad"
                        class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                    >
                        Try Again
                    </button>
                </div>
            </div>

            <!-- Image preview -->
            <img
                v-if="!error && !isLoading && imageType !== 'pdf'"
                :src="imageSource"
                :alt="title || 'Image preview'"
                class="w-full h-auto rounded-lg"
                @load="handleImageLoad"
                @error="handleImageError"
            />
            <iframe v-else
                    :src="pdfBlobUrl + '#toolbar=0&navpanes=0&scrollbar=0'"
                    class="w-full h-full rounded-xl"
                    @load="handlePdfLoad"
                    @error="handlePdfError"
            ></iframe>
        </div>

        <!-- Image info footer -->
        <template #footer>
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-300">
                    <span v-if="imageType === 'base64'">
                        <i class="fas fa-database mr-1"></i> Base64 Image
                    </span>
                    <span v-else-if="imageType === 'url'">
                        <i class="fas fa-link mr-1"></i> External URL
                    </span>
                </div>
                <div class="flex space-x-3">
                    <button
                        @click="downloadImage"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition"
                    >
                        <i class="fas fa-download mr-2"></i> Download
                    </button>
                    <button
                        @click="closePreview"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
                    >
                        Close
                    </button>
                </div>
            </div>
        </template>
    </BaseDialog>
</template>

<script setup lang="ts">
    import { computed, ref, watch } from 'vue';
    import BaseDialog from '@/components/BaseDialog.vue';

    const props = defineProps({
        // Control dialog visibility
        modelValue: Boolean,
        // The image source (can be base64 or URL)
        imageSrc: {
            type: String,
            default: ''
        },
        // Optional title for the dialog
        title: {
            type: String,
            default: 'File Preview'
        },
        // Optional file name for download
        fileName: {
            type: String,
            default: 'image'
        }
    });

    const emit = defineEmits(['update:modelValue']);

    // Local state
    const showPreview = computed({
        get: () => props.modelValue,
        set: (value) => emit('update:modelValue', value)
    });

    const isLoading = ref(true);
    const error = ref<string | null>(null);

    // Detect if the image is base64 or URL
    const imageType = computed(() => {
        if (props.imageSrc.startsWith('data:application/pdf')) {
            return 'pdf';
        } else if (props.imageSrc.startsWith('data:image')) {
            return 'base64';
        } else if (
            props.imageSrc.startsWith('http') ||
            props.imageSrc.startsWith('/')
        ) {
            return 'url';
        } else {
            return 'unknown';
        }
    });

    // Get the image source
    const imageSource = computed(() => {
        return props.imageSrc || '';
    });

    const pdfBlobUrl = computed(() => {
        if (!props.imageSrc) return '';

        if (props.imageSrc.startsWith('data:application/pdf')) {
            const base64 = props.imageSrc.split(',')[1];
            const byteCharacters = atob(base64);
            const byteNumbers = new Array(byteCharacters.length);

            for (let i = 0; i < byteCharacters.length; i++) {
                byteNumbers[i] = byteCharacters.charCodeAt(i);
            }

            const byteArray = new Uint8Array(byteNumbers);
            const blob = new Blob([byteArray], { type: 'application/pdf' });

            return URL.createObjectURL(blob);
        }

        return props.imageSrc;
    });

    // Get image extension for download
    const getImageExtension = () => {
        if (imageType.value === 'base64') {
            // Extract from the base64 header (e.g., "data:image/png;base64,...")
            const matches = props.imageSrc.match(/^data:image\/([a-zA-Z0-9]+);base64,/);
            return matches ? matches[1] : 'png';
        } else {
            // Extract from URL
            const extension = props.imageSrc.split('.').pop()?.split('?')[0];
            return extension || 'png';
        }
    };

    // Handle successful image load
    const handleImageLoad = () => {
        isLoading.value = false;
        error.value = null;
    };

    // Handle image loading error
    const handleImageError = () => {
        isLoading.value = false;
        error.value = 'Failed to load image. Please check if the file exists or try again.';
    };

    const handlePdfLoad = () => {
        isLoading.value = false;
        error.value = null;
    };

    const handlePdfError = () => {
        isLoading.value = false;
        error.value = 'Failed to load PDF file.';
    };

    // Retry loading image
    const retryLoad = () => {
        isLoading.value = true;
        error.value = null;

        // Force image reload by toggling src
        const img = new Image();
        img.src = imageSource.value;
        img.onload = handleImageLoad;
        img.onerror = handleImageError;
    };

    // Download image
    const downloadImage = async () => {
        try {
            const isPdfBase64 =
                imageType.value === 'base64' &&
                imageSource.value.startsWith('data:application/pdf');

            if (imageType.value === 'base64') {

                // ✅ Handle PDF Base64
                if (isPdfBase64) {
                    const base64Data = imageSource.value.split(',')[1];
                    const byteCharacters = atob(base64Data);
                    const byteNumbers = new Array(byteCharacters.length)
                        .fill(0)
                        .map((_, i) => byteCharacters.charCodeAt(i));

                    const byteArray = new Uint8Array(byteNumbers);
                    const blob = new Blob([byteArray], { type: 'application/pdf' });
                    const url = window.URL.createObjectURL(blob);

                    const link = document.createElement('a');
                    link.href = url;
                    link.download = `${props.fileName}.pdf`;

                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    window.URL.revokeObjectURL(url);
                }

                // ✅ Handle Image Base64 (existing logic)
                else {
                    const link = document.createElement('a');
                    link.href = imageSource.value;
                    link.download = `${props.fileName}.${getImageExtension()}`;

                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }

            } else {
                // ✅ Handle URL (image or pdf)
                const response = await fetch(imageSource.value);
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);

                // detect type from blob
                const isPdf = blob.type === 'application/pdf';
                const extension = isPdf ? 'pdf' : getImageExtension();

                const link = document.createElement('a');
                link.href = url;
                link.download = `${props.fileName}.${extension}`;

                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                window.URL.revokeObjectURL(url);
            }

        } catch (err) {
            console.error('Download failed:', err);
            error.value = 'Failed to download file. Please try again.';
        }
    };

    // Close preview
    const closePreview = () => {
        showPreview.value = false;
    };

    // Reset loading state when an image source changes
    watch(() => props.imageSrc, () => {
        if (props.imageSrc) {
            isLoading.value = true;
            error.value = null;
        }
    });

    // Reset loading state when the dialog opens
    watch(() => props.modelValue, (newVal) => {
        if (newVal && props.imageSrc) {
            isLoading.value = true;
            error.value = null;
        }
    });
</script>
