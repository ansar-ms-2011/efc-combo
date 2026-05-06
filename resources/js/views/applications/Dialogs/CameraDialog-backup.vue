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
                @click="emit('update:modelValue', false)"
                class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 focus:outline-none"
            >
                <span class="text-2xl">&times;</span>
            </button>
        </template>

        <div class="">
            <!-- Camera Preview -->
            <video
                v-if="!photoCaptured && !isProcessing"
                ref="videoElement"
                autoplay
                class="w-full rounded-lg mb-4"
            ></video>

            <!-- Processing State -->
            <div v-if="isProcessing" class="processing-container w-full rounded-lg mb-4">
                <div class="processing-overlay">
                    <div class="spinner"></div>
                    <p class="mt-3 text-white">Removing background...</p>
                </div>
            </div>

            <!-- Captured Image (Original) -->
            <img
                v-if="photoCaptured && !isProcessing && !processedImageUrl && !useOriginalAsIs"
                :src="originalImageUrl"
                alt="Captured"
                class="w-full rounded-lg mb-4"
            />

            <!-- Processed Image (with blue background) -->
            <img
                v-if="processedImageUrl && !isProcessing && !useOriginalAsIs"
                :src="processedImageUrl"
                alt="Processed"
                class="w-full rounded-lg mb-4"
            />

            <!-- Original Image (when using as is) -->
            <img
                v-if="useOriginalAsIs && originalImageUrl && !isProcessing"
                :src="originalImageUrl"
                alt="Original"
                class="w-full rounded-lg mb-4"
            />
        </div>

        <!-- Footer -->
        <template #footer>
            <div class="flex  gap-3" :class="{ 'justify-center': !photoCaptured && !isProcessing, 'justify-between': photoCaptured && !isProcessing }">
                <!-- Capture Button -->
                <button
                    v-if="!photoCaptured && !isProcessing"
                    class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center gap-2"
                    type="button"
                    @click="capturePhoto"
                >
                    <i class="fa-solid fa-camera"></i>
                    Capture
                </button>

                <!-- Options after capture -->
                <template v-if="photoCaptured && !isProcessing">
                    <!-- Use As Is Button -->
                    <button
                        v-if="!useOriginalAsIs && !processedImageUrl"
                        class="px-2 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 flex items-center gap-2"
                        type="button"
                        @click="useImageAsIs"
                    >
                        <i class="fa-solid fa-image"></i>
                        Use same
                    </button>

                    <!-- Remove Background Button -->
                    <button
                        v-if="!processedImageUrl && !useOriginalAsIs"
                        class="px-2 py-2 bg-primary text-white rounded hover:bg-blue-800 flex items-center gap-2"
                        type="button"
                        @click="removeBackgroundAndSetBlueBg"
                    >
                        <i class="fa-solid fa-wand-magic"></i>
                        Add Blue BG
                    </button>

                    <!-- Retake Button -->
                    <button
                        v-if="!useOriginalAsIs"
                        class="px-2 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700 flex items-center gap-2"
                        type="button"
                        @click="retakePhoto"
                    >
                        <i class="fa-solid fa-redo"></i>
                        Retake
                    </button>

                    <!-- Use Photo Button (for processed or original as is) -->
                    <button
                        v-if="processedImageUrl || useOriginalAsIs"
                        class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-2"
                        type="button"
                        @click="savePhoto"
                    >
                        <i class="fa-solid fa-check"></i>
                        Use Photo
                    </button>

                    <!-- Back button (when showing processed or as-is option) -->
                    <button
                        v-if="processedImageUrl || useOriginalAsIs"
                        class="px-6 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 flex items-center gap-2"
                        type="button"
                        @click="backToOptions"
                    >
                        <i class="fa-solid fa-arrow-left"></i>
                        Back
                    </button>
                </template>
            </div>
        </template>
    </BaseDialog>
</template>

<script setup>
    import { nextTick, onBeforeUnmount, ref, watch } from 'vue';
    import { removeBackground } from '@imgly/background-removal';
    import BaseDialog from '@/components/BaseDialog.vue';
    import Swal from 'sweetalert2';

    const props = defineProps({
        modelValue: Boolean
    });

    const emit = defineEmits(['update:modelValue', 'captured']);

    const videoElement = ref(null);
    const originalImageUrl = ref(null);
    const originalImageBlob = ref(null);
    const processedImageUrl = ref(null);
    const processedImageBlob = ref(null);
    const photoCaptured = ref(false);
    const videoStream = ref(null);
    const isProcessing = ref(false);
    const useOriginalAsIs = ref(false);

    // Watch modal open/close
    watch(
        () => props.modelValue,
        async (val) => {
            if (val) {
                await nextTick();
                await startCamera();
            } else {
                closeCamera();
                resetState();
            }
        }
    );

    // Reset all state
    const resetState = () => {
        if (originalImageUrl.value) {
            URL.revokeObjectURL(originalImageUrl.value);
        }
        if (processedImageUrl.value) {
            URL.revokeObjectURL(processedImageUrl.value);
        }
        originalImageUrl.value = null;
        originalImageBlob.value = null;
        processedImageUrl.value = null;
        processedImageBlob.value = null;
        photoCaptured.value = false;
        isProcessing.value = false;
        useOriginalAsIs.value = false;
    };

    // Start Camera
    const startCamera = async () => {
        try {
            photoCaptured.value = false;
            isProcessing.value = false;
            useOriginalAsIs.value = false;

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

    // Capture Photo
    const capturePhoto = () => {
        const video = videoElement.value;
        if (!video) return;

        // Create canvas to capture frame
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0);

        // Convert to blob
        canvas.toBlob(async (blob) => {
            originalImageBlob.value = blob;
            originalImageUrl.value = URL.createObjectURL(blob);
            photoCaptured.value = true;

            // Stop camera stream to save resources
            if (videoStream.value) {
                videoStream.value.getTracks().forEach(track => track.stop());
                videoStream.value = null;
            }
        }, 'image/png');
    };

    // Use image as is (without background removal)
    const useImageAsIs = () => {
        useOriginalAsIs.value = true;
        Swal.fire({
            icon: 'success',
            text: 'You can now use the photo with original background.',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000
        });
    };

    // Back to options (from processed or as-is view)
    const backToOptions = () => {
        processedImageUrl.value = null;
        processedImageBlob.value = null;
        useOriginalAsIs.value = false;
    };

    // Add solid background to transparent image
    const addSolidBackground = (foregroundBlob, backgroundColor) => {
        return new Promise((resolve, reject) => {
            const img = new Image();
            const url = URL.createObjectURL(foregroundBlob);

            img.onload = () => {
                const canvas = document.createElement('canvas');
                canvas.width = img.width;
                canvas.height = img.height;
                const ctx = canvas.getContext('2d');

                // Draw solid background
                ctx.fillStyle = backgroundColor;
                ctx.fillRect(0, 0, canvas.width, canvas.height);

                // Draw foreground image (with transparency)
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

    // Remove background and set blue background
    const removeBackgroundAndSetBlueBg = async () => {
        if (!originalImageBlob.value) return;

        isProcessing.value = true;

        try {

            // Step 1: Remove background (returns transparent PNG)
            console.log('Removing background...');
            const transparentBlob = await removeBackground(originalImageBlob.value, {

                publicPath: `${window.location.origin}/bg-removal/`,   //Model and WASM files are in the same project public directory
                debug: true,
                model: 'isnet_quint8', //isnet_fp16, isnet_fp32, isnet_int8, isnet_int4
                device: 'cpu',
                proxyToWorker: true,
                crossIsolate: true,

                output: {
                    format: 'image/png'
                },
                progress: (key, current, total) => {
                    console.log(`Progress: ${key} - ${current}/${total}`);
                    // Update Swal text with progress
                    if (key === 'download') {
                        Swal.update({
                            text: `Downloading model... ${Math.round(current / total * 100)}%`
                        });
                    } else if (key === 'inference') {
                        Swal.update({
                            text: `Processing image... ${Math.round(current / total * 100)}%`
                        });
                    }
                }
            });

            // Step 2: Add solid blue background
            console.log('Adding blue background...');
            const blueBackgroundBlob = await addSolidBackground(transparentBlob, '#3B82F6');

            // Store processed image
            processedImageBlob.value = blueBackgroundBlob;
            processedImageUrl.value = URL.createObjectURL(blueBackgroundBlob);

            console.log('Processing complete!');

            Swal.close();

            // Show success message
            Swal.fire({
                icon: 'success',
                text: 'Background removed successfully!',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
        } catch (error) {
            console.error('Background removal error:', error);
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Failed to remove background. Please try again.',
                confirmButtonText: 'OK'
            });
        } finally {
            isProcessing.value = false;
        }
    };

    // Retake photo
    const retakePhoto = () => {
        // Clean up existing URLs
        if (originalImageUrl.value) {
            URL.revokeObjectURL(originalImageUrl.value);
        }
        if (processedImageUrl.value) {
            URL.revokeObjectURL(processedImageUrl.value);
        }

        originalImageUrl.value = null;
        originalImageBlob.value = null;
        processedImageUrl.value = null;
        processedImageBlob.value = null;
        photoCaptured.value = false;
        isProcessing.value = false;
        useOriginalAsIs.value = false;

        // Restart camera
        startCamera();
    };

    // Save final photo
    const savePhoto = () => {
        let blobToSend = null;

        // Determine which blob to send
        if (useOriginalAsIs.value && originalImageBlob.value) {
            blobToSend = originalImageBlob.value;
        } else if (processedImageBlob.value) {
            blobToSend = processedImageBlob.value;
        } else {
            return;
        }

        // Convert blob to base64 for emitting
        const reader = new FileReader();
        reader.onloadend = () => {
            const base64 = reader.result;
            emit('captured', base64);

            // Close modal after successful save
            emit('update:modelValue', false);

            Swal.fire({
                icon: 'success',
                text: useOriginalAsIs.value ? 'Your original photo has been saved!' : 'Your photo has been processed and saved successfully!',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
        };
        reader.readAsDataURL(blobToSend);
    };

    // Close Camera Properly
    const closeCamera = () => {
        if (videoStream.value) {
            videoStream.value.getTracks().forEach(track => track.stop());
            videoStream.value = null;
        }
        if (videoElement.value) {
            videoElement.value.srcObject = null;
        }
    };

    // Cleanup when component unmounts
    onBeforeUnmount(() => {
        closeCamera();
        resetState();
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
</style>
