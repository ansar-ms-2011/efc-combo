<template>
    <BaseDialog
        :modelValue="modelValue"
        @update:modelValue="emit('update:modelValue', $event)"
        title="درخواست گزار کی تصویر لیں"
        subtitle="Application Personal Image"
        title-class="font-nastaleeq"
    >
        <template #header-right>
            <button
                type="button"
                @click="emit('update:modelValue', false)"
                class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 focus:outline-none"
            >
                <span class="text-2xl">&times;</span>
            </button>
        </template>

        <div class="">
            <!-- Tabs -->
            <div class="flex border-b border-gray-200 mb-4">
                <button
                    type="button"
                    @click="activeTab = 'image'"
                    :class="[
                        'px-4 py-2 text-sm font-medium transition-colors duration-200',
                        activeTab === 'image'
                            ? 'border-b-2 border-blue-500 text-blue-600'
                            : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'
                    ]"
                >
                    <i class="fa-solid fa-image ml-2"></i>
                    Image
                </button>
                <button
                    type="button"
                    @click="activeTab = 'background'"
                    :disabled="!currentImageBlob"
                    :class="[
                        'px-4 py-2 text-sm font-medium transition-colors duration-200',
                        activeTab === 'background'
                            ? 'border-b-2 border-blue-500 text-blue-600'
                            : !currentImageBlob
                                ? 'text-gray-300 cursor-not-allowed'
                                : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'
                    ]"
                >
                    <i class="fa-solid fa-fill-drip ml-2"></i>
                    Background
                </button>
                <button
                    type="button"
                    @click="activeTab = 'crop'"
                    :disabled="!currentImageBlob"
                    :class="[
                        'px-4 py-2 text-sm font-medium transition-colors duration-200',
                        activeTab === 'crop'
                            ? 'border-b-2 border-blue-500 text-blue-600'
                            : !currentImageBlob
                                ? 'text-gray-300 cursor-not-allowed'
                                : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'
                    ]"
                >
                    <i class="fa-solid fa-crop-alt ml-2"></i>
                    Crop
                </button>
            </div>

            <!-- Image Tab Content -->
            <div v-if="activeTab === 'image'" class="space-y-4">
                <div class="flex gap-3 justify-center">
                    <button
                        type="button"
                        @click="triggerFileUpload"
                        class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-2"
                    >
                        <i class="fa-solid fa-upload"></i>
                        Upload Image
                    </button>
                    <button
                        type="button"
                        @click="openCamera"
                        class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center gap-2"
                    >
                        <i class="fa-solid fa-camera"></i>
                        Use Camera
                    </button>
                </div>

                <!-- Hidden file input -->
                <input
                    ref="fileInput"
                    type="file"
                    accept="image/jpeg,image/png,image/jpg"
                    class="hidden"
                    @change="handleFileUpload"
                />

                <!-- Camera View -->
                <div v-if="showCamera" class="space-y-3">
                    <video
                        v-if="!photoCaptured && !isProcessing"
                        ref="videoElement"
                        autoplay
                        class="w-full rounded-lg mb-4"
                    ></video>

                    <div class="flex gap-3 justify-center">
                        <button
                            v-if="!photoCaptured"
                            class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center gap-2"
                            type="button"
                            @click="capturePhoto"
                        >
                            <i class="fa-solid fa-camera"></i>
                            Capture
                        </button>
                        <button
                            v-if="photoCaptured"
                            class="px-6 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700 flex items-center gap-2"
                            type="button"
                            @click="retakePhoto"
                        >
                            <i class="fa-solid fa-redo"></i>
                            Retake
                        </button>
                    </div>
                </div>

                <!-- Current Image Display -->
                <div v-if="currentImageUrl && !showCamera && !isProcessing" class="mt-4">
                    <img :src="currentImageUrl" alt="Current" class="w-full rounded-lg" />
                </div>
            </div>

            <!-- Background Tab Content -->
            <div v-if="activeTab === 'background'" class="space-y-4">
                <div v-if="currentImageUrl && !isProcessing" class="mb-4">
                    <img :src="currentImageUrl" alt="Original" class="w-full rounded-lg mb-4" />
                </div>

                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Background Color
                        </label>
                        <div class="flex gap-3 items-center">
                            <input
                                type="color"
                                v-model="backgroundColor"
                                class="w-12 h-10 rounded border border-gray-300"
                            />
                            <input
                                type="text"
                                v-model="backgroundColor"
                                class="flex-1 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="#3B82F6"
                            />
                            <button
                                type="button"
                                @click="removeBackgroundAndSetBg"
                                :disabled="isProcessing || !currentImageBlob"
                                class="flex-1 px-4 py-2 bg-primary text-white rounded hover:bg-blue-800 disabled:bg-gray-400 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                            >
                                <i class="fa-solid fa-wand-magic"></i>
                                {{ isProcessing ? 'Processing...' : 'Process' }}
                            </button>
                        </div>
                    </div>

                    <!-- Processing State -->
                    <div v-if="isProcessing" class="processing-container w-full rounded-lg">
                        <div class="processing-overlay">
                            <div class="spinner"></div>
                            <p class="mt-3 text-white">Removing background...</p>
                        </div>
                    </div>

                    <!-- Processed Image Preview -->
                    <div v-if="processedImageUrl && !isProcessing" class="mt-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Processed Image:</h4>
                        <img :src="processedImageUrl" alt="Processed" class="w-full rounded-lg mb-3" />
                        <button
                            type="button"
                            @click="applyProcessedImage"
                            class="w-full px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center justify-center gap-2"
                        >
                            <i class="fa-solid fa-check"></i>
                            Apply Changes
                        </button>
                    </div>
                </div>
            </div>

            <!-- Crop Tab Content -->
            <div v-if="activeTab === 'crop'" class="space-y-4">
                <div v-if="currentImageUrl" class="cropper-container">
                    <Cropper
                        ref="cropperRef"
                        :src="currentImageUrl"
                        :stencil-props="{
                            aspectRatio: 1,
                            movable: true,
                            resizable: true,
                            rotatable: true,
                            scalable: true,
                            minWidth: 100,
                            minHeight: 100,
                            maxWidth: 512,
                            maxHeight: 512
                        }"
                        :default-boundaries="{
                            width: 300,
                            height: 300
                        }"
                        class="cropper"
                        @change="onCropChange"
                    />
                    <div class="flex justify-center gap-2" v-if="croppedSize">
                        <span class="text-xs">Width: {{ croppedSize.width}}</span>
                        <span class="text-xs">Height: {{ croppedSize.height}}</span>
                    </div>
                </div>

                <div class="flex justify-around gap-3">
                    <button
                        type="button"
                        @click="applyCrop"
                        class="flex-1 px-2 py-2 bg-green-500 text-white rounded hover:bg-green-600 flex items-center justify-center gap-2"
                    >
                        <i class="fa-solid fa-check"></i>
                        Apply Crop
                    </button>
                    <button
                        type="button"
                        @click="resetCrop"
                        class="flex-1 px-2 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 flex items-center justify-center gap-2"
                    >
                        <i class="fa-solid fa-undo"></i>
                        Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <template #footer>
            <div class="flex justify-between gap-3">
                <button
                    type="button"
                    v-if="currentImageBlob"
                    @click="savePhoto"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-2"
                >
                    <i class="fa-solid fa-check"></i>
                    Save Photo
                </button>
                <button
                    type="button"
                    @click="emit('update:modelValue', false)"
                    class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600"
                >
                    Cancel
                </button>
            </div>
        </template>
    </BaseDialog>
</template>

<script setup>
    import { nextTick, onBeforeUnmount, ref, watch } from 'vue';
    import { removeBackground } from '@imgly/background-removal';
    import { Cropper } from 'vue-advanced-cropper';
    import 'vue-advanced-cropper/dist/style.css';
    import BaseDialog from '@/components/BaseDialog.vue';
    import Swal from 'sweetalert2';

    const props = defineProps({
        modelValue: Boolean
    });

    const emit = defineEmits(['update:modelValue', 'captured']);

    // Refs
    const videoElement = ref(null);
    const fileInput = ref(null);
    const cropperRef = ref(null);

    // State
    const activeTab = ref('image');
    const showCamera = ref(false);
    const showUploadOptions = ref(false);
    const currentImageUrl = ref(null);
    const currentImageBlob = ref(null);
    const originalImageBlob = ref(null);
    const processedImageUrl = ref(null);
    const processedImageBlob = ref(null);
    const photoCaptured = ref(false);
    const videoStream = ref(null);
    const isProcessing = ref(false);
    const backgroundColor = ref('#3B82F6');
    const cropCoordinates = ref(null);
    const croppedSize  =ref(null);
    // Watch modal open/close
    watch(
        () => props.modelValue,
        async (val) => {
            if (val) {
                resetAllState();
                activeTab.value = 'image';
            } else {
                closeCamera();
                resetAllState();
            }
        }
    );

    const resetAllState = () => {
        if (currentImageUrl.value) URL.revokeObjectURL(currentImageUrl.value);
        if (processedImageUrl.value) URL.revokeObjectURL(processedImageUrl.value);

        currentImageUrl.value = null;
        currentImageBlob.value = null;
        originalImageBlob.value = null;
        processedImageUrl.value = null;
        processedImageBlob.value = null;
        photoCaptured.value = false;
        isProcessing.value = false;
        showCamera.value = false;
        cropCoordinates.value = null;

        if (cropperRef.value) {
            cropperRef.value.reset();
        }
    };

    // File upload handling
    const triggerFileUpload = () => {
        fileInput.value.click();
        showUploadOptions.value = false;
    };

    const handleFileUpload = (event) => {
        const file = event.target.files[0];
        if (file && (file.type === 'image/jpeg' || file.type === 'image/png' || file.type === 'image/jpg')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                setNewImage(e.target.result, file);
            };
            reader.readAsDataURL(file);
        } else {
            Swal.fire({
                icon: 'error',
                text: 'Please upload a valid image file (JPEG, PNG)',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        }
        showCamera.value = false;
        event.target.value = '';
    };

    // Camera handling
    const openCamera = () => {
        showUploadOptions.value = false;
        showCamera.value = true;
        startCamera();
    };

    const captureFromCamera = () => {
        showUploadOptions.value = false;
        showCamera.value = true;
        startCamera();
    };

    const startCamera = async () => {
        try {
            photoCaptured.value = false;
            videoStream.value = await navigator.mediaDevices.getUserMedia({
                video: {
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                }
            });

            if (videoElement.value) {
                videoElement.value.srcObject = videoStream.value;
                await videoElement.value.play();
            }
        } catch (error) {
            console.error('Camera access error:', error);
            Swal.fire({
                icon: 'error',
                text: 'Error: Unable to access camera. Please check permissions.',
                confirmButtonText: 'OK'
            });
        }
    };

    const capturePhoto = () => {
        const video = videoElement.value;
        if (!video) return;

        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0);

        canvas.toBlob((blob) => {
            const url = URL.createObjectURL(blob);
            setNewImage(url, blob);
            photoCaptured.value = true;
            closeCamera();
            showCamera.value = false;
        }, 'image/png');
    };

    const retakePhoto = () => {
        photoCaptured.value = false;
        startCamera();
    };

    const closeCamera = () => {
        if (videoStream.value) {
            videoStream.value.getTracks().forEach(track => track.stop());
            videoStream.value = null;
        }
        if (videoElement.value) {
            videoElement.value.srcObject = null;
        }
    };

    // Set new image (common method for both upload and camera)
    const setNewImage = (url, blob) => {
        if (currentImageUrl.value) URL.revokeObjectURL(currentImageUrl.value);

        currentImageUrl.value = url;
        currentImageBlob.value = blob;
        originalImageBlob.value = blob;

        // Clear processed image when new image is set
        if (processedImageUrl.value) {
            URL.revokeObjectURL(processedImageUrl.value);
            processedImageUrl.value = null;
            processedImageBlob.value = null;
        }

        Swal.fire({
            icon: 'success',
            text: 'Image loaded successfully!',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1500
        });
    };

    // Background removal
    const addSolidBackground = (foregroundBlob, backgroundColor) => {
        return new Promise((resolve, reject) => {
            const img = new Image();
            const url = URL.createObjectURL(foregroundBlob);

            img.onload = () => {
                const canvas = document.createElement('canvas');
                canvas.width = img.width;
                canvas.height = img.height;
                const ctx = canvas.getContext('2d');

                ctx.fillStyle = backgroundColor;
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(img, 0, 0);

                canvas.toBlob((blob) => {
                    URL.revokeObjectURL(url);
                    resolve(blob);
                }, 'image/png');
            };

            img.onerror = (error) => {
                URL.revokeObjectURL(url);
                reject(error);
            };

            img.src = url;
        });
    };

    const removeBackgroundAndSetBg = async () => {
        if (!originalImageBlob.value) return;

        isProcessing.value = true;

        try {
            const transparentBlob = await removeBackground(originalImageBlob.value, {
                publicPath: `${window.location.origin}/bg-removal/`,
                debug: true,
                model: 'isnet_quint8',
                device: 'cpu',
                proxyToWorker: true,
                crossIsolate: true,
                output: {
                    format: 'image/png'
                },
                progress: (key, current, total) => {
                    // if (key === 'inference') {
                    //     loadingSwal.update({
                    //         text: `Processing image... ${Math.round(current / total * 100)}%`
                    //     });
                    // }
                }
            });

            const coloredBackgroundBlob = await addSolidBackground(transparentBlob, backgroundColor.value);

            if (processedImageUrl.value) URL.revokeObjectURL(processedImageUrl.value);

            processedImageBlob.value = coloredBackgroundBlob;
            processedImageUrl.value = URL.createObjectURL(coloredBackgroundBlob);
            applyProcessedImage()
        } catch (error) {
            console.error('Background removal error:', error);
            loadingSwal.close();
            Swal.fire({
                icon: 'error',
                title: 'Failed to remove background. Please try again.',
                confirmButtonText: 'OK'
            });
        } finally {
            isProcessing.value = false;
        }
    };

    const applyProcessedImage = () => {
        if (processedImageBlob.value) {
            const url = URL.createObjectURL(processedImageBlob.value);
            setNewImage(url, processedImageBlob.value);
            activeTab.value = 'image';

            Swal.fire({
                icon: 'success',
                text: 'Background changes applied!',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500
            });
        }
    };

    // Crop handling
    const onCropChange = (event) => {
        if (event && event.coordinates) {
            cropCoordinates.value = event.coordinates;
            const width = Math.round(event.coordinates.width);
            const height = Math.round(event.coordinates.height);
            console.log(`Crop size: ${width} x ${height}`);
            croppedSize.value = { width, height };
        }
    };

    const applyCrop = async () => {
        if (!cropperRef.value || !currentImageBlob.value) return;

        try {
            const { canvas } = cropperRef.value.getResult();
            if (canvas) {
                canvas.toBlob((blob) => {
                    const url = URL.createObjectURL(blob);
                    setNewImage(url, blob);
                    activeTab.value = 'image';

                    Swal.fire({
                        icon: 'success',
                        text: 'Image cropped successfully!',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }, 'image/png');
            }
        } catch (error) {
            console.error('Crop error:', error);
            Swal.fire({
                icon: 'error',
                text: 'Failed to crop image',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
        }
    };

    const resetCrop = () => {
        if (cropperRef.value) {
            cropperRef.value.reset();
        }
    };

    // Save final photo
    const savePhoto = () => {
        if (!currentImageBlob.value) return;

        const reader = new FileReader();
        reader.onloadend = () => {
            const base64 = reader.result;
            emit('captured', base64);
            emit('update:modelValue', false);

            Swal.fire({
                icon: 'success',
                text: 'Your photo has been saved successfully!',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
        };
        reader.readAsDataURL(currentImageBlob.value);
    };

    // Cleanup
    onBeforeUnmount(() => {
        closeCamera();
        if (currentImageUrl.value) URL.revokeObjectURL(currentImageUrl.value);
        if (processedImageUrl.value) URL.revokeObjectURL(processedImageUrl.value);
    });
</script>

<style scoped>
    .processing-container {
        position: relative;
        background: #000;
        min-height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .processing-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }

    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid rgba(255, 255, 255, 0.3);
        border-top-color: white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .cropper-container {
        width: 100%;
        background: #f5f5f5;
        border-radius: 8px;
        overflow: hidden;
    }

    .cropper {
        background: #f5f5f5;
    }
</style>
