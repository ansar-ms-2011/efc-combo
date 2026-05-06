<template>
    <div>
        <!-- Signature Preview Section -->
        <div>
            <div class="flex items-center justify-between mb-1">
                <label class="text-sm font-semibold mb-1 text-gray-700">Digital Signature</label>
                <div class="flex gap-2">
                    <button type="button" @click="openSignatureModal"
                            class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded shadow text-xs">
                        {{ signatureImage ? 'Edit Signature' : 'Add Signature' }}
                    </button>
                    <button v-if="signatureImage" type="button" @click="confirmRemove"
                            class="px-2 py-1 bg-red-100 hover:bg-red-500 text-red-600 hover:text-white rounded shadow text-sm">
                        Remove
                    </button>
                </div>
            </div>

            <!-- Signature Preview -->
            <div class="flex justify-center items-center p-4 bg-gray-100 rounded-lg border border-dashed">
                <div v-if="signatureImage" class="relative">
                    <img :src="signatureImage" class="max-h-32 object-contain" alt="Signature" />
                </div>
                <div v-else class="text-gray-400 text-center py-1">
                    <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    <p>No signature added</p>
                    <p class="text-xs mt-1">Click "Add Signature" to draw or upload</p>
                </div>
            </div>
        </div>

        <!-- Signature Modal -->
        <BaseDialog
            v-model="showModal"
            title="Digital Signature"
            subtitle="Draw, upload, or crop your signature"
            max-width="max-w-2xl"
        >
            <!-- Tab Buttons -->
            <div class="flex gap-4 mb-6 border-b">
                <button
                    type="button"
                    @click="switchTab('draw')"
                    :class="[
                        'px-4 py-2 font-medium transition-colors relative',
                        activeTab === 'draw'
                            ? 'text-blue-600 border-b-2 border-blue-600'
                            : 'text-gray-500 hover:text-gray-700'
                    ]"
                >
                    Draw Signature
                </button>
                <button
                    type="button"
                    @click="switchTab('upload')"
                    :class="[
                        'px-4 py-2 font-medium transition-colors relative',
                        activeTab === 'upload'
                            ? 'text-blue-600 border-b-2 border-blue-600'
                            : 'text-gray-500 hover:text-gray-700'
                    ]"
                >
                    Upload Image
                </button>
                <button
                    type="button"
                    @click="switchTab('crop')"
                    :disabled="!hasCanvasContent"
                    :class="[
                        'px-4 py-2 font-medium transition-colors relative',
                        activeTab === 'crop'
                            ? 'text-blue-600 border-b-2 border-blue-600'
                            : 'text-gray-500 hover:text-gray-700',
                        !hasCanvasContent && 'opacity-50 cursor-not-allowed'
                    ]"
                >
                    Crop Image
                </button>
            </div>

            <!-- Tab 1 & 2: Draw/Upload Canvas -->
            <div v-show="activeTab === 'draw' || activeTab === 'upload'"
                 class="border rounded-lg overflow-hidden bg-white">
                <canvas
                    ref="mainCanvas"
                    :width="canvasWidth"
                    :height="canvasHeight"
                    class="border-b touch-none"
                    :class="{ 'cursor-crosshair': activeTab === 'draw' }"
                    @mousedown="startDrawing"
                    @mousemove="draw"
                    @mouseup="stopDrawing"
                    @mouseleave="stopDrawing"
                    @touchstart="handleTouchStart"
                    @touchmove="handleTouchMove"
                    @touchend="stopDrawing"
                    @touchcancel="stopDrawing"
                    style="background: white; touch-action: none; display: block; width: 100%; height: auto;"
                ></canvas>

                <!-- Draw Mode Controls -->
                <div v-if="activeTab === 'draw'"
                     class="flex flex-wrap justify-between items-center p-3 bg-gray-50 gap-2">
                    <div class="flex gap-2 flex-wrap items-center">
                        <label for="stokeWidth" class="mb-0">Pen Thickness</label>
                        <select id="stokeWidth" v-model="brushSize"
                                @change="(event) => setBrushSize(event.target.value)"
                                class="px-2 py-1 rounded text-sm border border-gray-300 cursor-pointer"
                                title="Select brush size"
                        >
                            <option value="2">Small (2px)</option>
                            <option value="4">Medium (4px)</option>
                            <option value="6">Large (6px)</option>
                        </select>
                    </div>
                    <div class="flex gap-2 flex-wrap items-center">
                        <label class="text-sm">Smoothing:</label>
                        <select v-model="smoothingLevel"
                                @change="updateSmoothing"
                                class="px-2 py-1 rounded text-sm border border-gray-300 cursor-pointer">
                            <option value="off">Off</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High (Bézier)</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" @click="clearCanvas" title="Clear canvas"
                                class="px-3 py-1 bg-gray-300 hover:bg-gray-400 rounded text-sm">Clear
                        </button>
                        <button type="button" @click="undoLastDraw" title="Undo last draw"
                                class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded text-sm">Undo
                        </button>
                    </div>
                </div>

                <!-- Upload Mode Controls -->
                <div v-if="activeTab === 'upload'"
                     class="flex flex-wrap justify-between items-center p-3 bg-gray-50 gap-2">
                    <div class="flex gap-2 flex-wrap">
                        <input type="file" accept="image/*" @change="handleFileUpload" ref="fileInput"
                               class="hidden" />
                        <button type="button" @click="triggerFileUpload" title="Upload image"
                                class="px-3 py-1 bg-gray-300 hover:bg-gray-400 rounded ">
                            <i class="fa fa-upload fa-lg"></i>
                        </button>
                        <button type="button" @click="rotateImage(-90)" title="Rotate image left"
                                class="px-3 py-1 bg-gray-300 hover:bg-gray-400 rounded text-sm">
                            <i class="fa-solid fa-rotate-left fa-lg"></i>
                        </button>
                        <button type="button" @click="rotateImage(90)" title="Rotate image right"
                                class="px-3 py-1 bg-gray-300 hover:bg-gray-400 rounded text-sm">
                            <i class="fa-solid fa-rotate-right fa-lg"></i>
                        </button>
                        <button type="button" @click="resetImageTransform" title="Reset image transform"
                                class="px-3 py-1 bg-gray-300 hover:bg-gray-400 rounded text-sm">
                            <i class="fa-solid fa-times fa-lg"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Cropper -->
            <div v-show="activeTab === 'crop'" class="cropper-container">
                <div v-if="isLoadingCropper" class="text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <p class="mt-2 text-gray-500">Loading image...</p>
                </div>
                <cropper
                    v-else-if="canvasImageSrc"
                    ref="cropperRef"
                    class="cropper"
                    :src="canvasImageSrc"
                    :stencil-props="{

                        movable: true,
                        resizable: true
                    }"
                    @change="handleCropChange"
                    @ready="onCropperReady"
                />
                <div v-else class="text-center py-8 text-gray-400">
                    No image to crop. Please draw or upload an image first.
                </div>
            </div>

            <!-- Footer Buttons -->
            <template #footer>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="saveSignatureFromCurrentTab"
                            :disabled="!canSaveSignature"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded shadow text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        Save Signature
                    </button>
                    <button type="button" @click="closeModal"
                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded shadow text-sm">
                        Cancel
                    </button>
                </div>
            </template>
        </BaseDialog>
    </div>
</template>

<script setup>
    import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
    import Swal from 'sweetalert2';
    import BaseDialog from '@/components/BaseDialog.vue';
    import { Cropper } from 'vue-advanced-cropper';
    import 'vue-advanced-cropper/dist/style.css';
    import { drawCatmullRomCurve, getDistance } from '@/mixin/index.ts';

    const props = defineProps({
        modelValue: {
            type: File,
            default: null
        },
        existingSignatureUrl: {
            type: [String, null, undefined],
            default: null
        }
    });

    // Emits
    const emit = defineEmits(['update:modelValue', 'saved', 'removed']);

    // Signature state
    const signatureImage = ref(null);
    const signatureFile = ref(null);
    const showModal = ref(false);
    const brushSize = ref(2);

    const activeTab = ref('draw');

    // Canvas refs
    const mainCanvas = ref(null);
    let canvasCtx = null;

    // Canvas dimensions
    const canvasWidth = ref(600);
    const canvasHeight = ref(200);

    // Drawing state
    let drawing = false;
    let currentPoints = []; // Store points for Bézier smoothing
    let smoothingEnabled = ref(true); // Toggle smoothing
    let smoothingLevel = ref('medium'); // 'low', 'medium', 'high'

    // Add point collection settings
    let minDistance = 5; // Minimum distance between points (pixels)
    const maxPoints = 120; // Maximum points to store per stroke

    const currentBrushSize = ref(3);
    const drawHistory = ref([]);
    const hasDrawing = ref(false);

    // Uploaded image state
    const uploadedImage = ref(null);
    const imageTransform = ref({
        x: 0,
        y: 0,
        width: 0,
        height: 0,
        rotation: 0,
        scaleX: 1,
        scaleY: 1
    });
    const fileInput = ref(null);

    // Cropper state
    const cropperRef = ref(null);
    const canvasImageSrc = ref(null);
    const croppedImageData = ref(null);
    const isLoadingCropper = ref(false);

    // Computed
    const hasCanvasContent = computed(() => {
        if (activeTab.value === 'draw') {
            return hasDrawing.value;
        } else if (activeTab.value === 'upload') {
            return uploadedImage.value !== null;
        }
        return false;
    });

    const canSaveSignature = computed(() => {
        if (activeTab.value === 'draw') {
            return hasDrawing.value;
        } else if (activeTab.value === 'upload') {
            return uploadedImage.value !== null;
        } else if (activeTab.value === 'crop') {
            return croppedImageData.value !== null;
        }
        return false;
    });

    // Helper function to get canvas context
    const getCanvasContext = (canvas, withReadFreq = false) => {
        if (!canvas) return null;
        if (withReadFreq) {
            return canvas.getContext('2d', { willReadFrequently: true });
        }
        return canvas.getContext('2d');
    };

    // Initialize main canvas
    const initMainCanvas = () => {
        if (!mainCanvas.value) {
            console.log('mainCanvas not available yet');
            return;
        }

        console.log('Initializing main canvas');
        mainCanvas.value.width = canvasWidth.value;
        mainCanvas.value.height = canvasHeight.value;
        canvasCtx = getCanvasContext(mainCanvas.value, true);

        if (canvasCtx) {
            canvasCtx.lineCap = 'round';
            canvasCtx.lineJoin = 'round';
            canvasCtx.lineWidth = currentBrushSize.value;
            canvasCtx.strokeStyle = '#000000';
            canvasCtx.fillStyle = '#ffffff';
            canvasCtx.fillRect(0, 0, canvasWidth.value, canvasHeight.value);

            saveToHistory();
            hasDrawing.value = false;
        }
    };

    // Update canvas image source for cropper
    const updateCanvasImageSrc = () => {
        if (mainCanvas.value) {
            // Get the canvas data URL
            const dataUrl = mainCanvas.value.toDataURL('image/png');
            canvasImageSrc.value = dataUrl;
            return dataUrl;
        }
        return null;
    };

    // Force refresh cropper when image source changes
    const refreshCropper = async () => {
        if (activeTab.value === 'crop' && canvasImageSrc.value) {
            isLoadingCropper.value = true;
            await nextTick();
            // Small delay to ensure cropper component re-renders
            setTimeout(() => {
                isLoadingCropper.value = false;
            }, 100);
        }
    };

    // Save to history for undo functionality
    const saveToHistory = () => {
        if (mainCanvas.value && canvasCtx) {
            const imageData = canvasCtx.getImageData(0, 0, canvasWidth.value, canvasHeight.value);
            drawHistory.value.push(imageData);
            if (drawHistory.value.length > 20) drawHistory.value.shift();

            const pixels = imageData.data;
            let hasNonWhite = false;
            for (let i = 0; i < pixels.length; i += 4) {
                if (pixels[i] < 250 || pixels[i + 1] < 250 || pixels[i + 2] < 250) {
                    hasNonWhite = true;
                    break;
                }
            }
            hasDrawing.value = hasNonWhite;
            updateCanvasImageSrc();
        }
    };

    // Undo last drawing
    const undoLastDraw = () => {
        if (drawHistory.value.length > 1) {
            drawHistory.value.pop();
            const lastState = drawHistory.value[drawHistory.value.length - 1];
            if (canvasCtx && mainCanvas.value) {
                canvasCtx.putImageData(lastState, 0, 0);
                updateCanvasImageSrc();
            }
        } else if (drawHistory.value.length === 1 && canvasCtx && mainCanvas.value) {
            canvasCtx.fillStyle = '#ffffff';
            canvasCtx.fillRect(0, 0, canvasWidth.value, canvasHeight.value);
            const whiteState = canvasCtx.getImageData(0, 0, canvasWidth.value, canvasHeight.value);
            drawHistory.value = [whiteState];
            hasDrawing.value = false;
            updateCanvasImageSrc();
        }
    };

    const startDrawing = (e) => {
        if (activeTab.value !== 'draw') return;
        e.preventDefault();

        drawing = true;
        currentPoints = [];

        const rect = mainCanvas.value?.getBoundingClientRect();
        if (rect && canvasCtx) {
            const scaleX = mainCanvas.value.width / rect.width;
            const scaleY = mainCanvas.value.height / rect.height;

            let clientX, clientY;
            if (e.touches) {
                clientX = e.touches[0].clientX;
                clientY = e.touches[0].clientY;
            } else {
                clientX = e.clientX;
                clientY = e.clientY;
            }

            const x = (clientX - rect.left) * scaleX;
            const y = (clientY - rect.top) * scaleY;

            // Add first point
            currentPoints.push({ x, y });

            // Start drawing
            canvasCtx.beginPath();
            canvasCtx.moveTo(x, y);
            canvasCtx.lineTo(x, y);
            canvasCtx.stroke();
        }
    };

    const draw = (e) => {
        if (!drawing || activeTab.value !== 'draw' || !canvasCtx || !mainCanvas.value) return;
        e.preventDefault();

        const rect = mainCanvas.value.getBoundingClientRect();
        const scaleX = mainCanvas.value.width / rect.width;
        const scaleY = mainCanvas.value.height / rect.height;

        let clientX, clientY;
        if (e.touches) {
            clientX = e.touches[0].clientX;
            clientY = e.touches[0].clientY;
        } else {
            clientX = e.clientX;
            clientY = e.clientY;
        }

        const currentX = (clientX - rect.left) * scaleX;
        const currentY = (clientY - rect.top) * scaleY;

        // Add point if far enough from last point
        const lastPoint = currentPoints[currentPoints.length - 1];
        const distance = getDistance(lastPoint, { x: currentX, y: currentY });

        if (distance >= minDistance && currentPoints.length < maxPoints) {
            currentPoints.push({ x: currentX, y: currentY });

            // Redraw the entire stroke with smoothing
            redrawCurrentStroke();
        }
    };

    const redrawCurrentStroke = () => {
        if (!canvasCtx || !mainCanvas.value) return;

        // Get the current canvas state before this stroke
        const currentState = drawHistory.value[drawHistory.value.length - 1];
        if (currentState) {
            canvasCtx.putImageData(currentState, 0, 0);
        }

        // Draw the stroke with Bézier smoothing
        canvasCtx.beginPath();
        canvasCtx.lineCap = 'round';
        canvasCtx.lineJoin = 'round';
        canvasCtx.lineWidth = currentBrushSize.value;
        canvasCtx.strokeStyle = '#000000';

        if (smoothingEnabled.value && currentPoints.length >= 2) {
            // Use Catmull-Rom for best smoothness
            drawCatmullRomCurve(canvasCtx, currentPoints);
        } else if (currentPoints.length >= 2) {
            // Fallback to simple lines
            canvasCtx.moveTo(currentPoints[0].x, currentPoints[0].y);
            for (let i = 1; i < currentPoints.length; i++) {
                canvasCtx.lineTo(currentPoints[i].x, currentPoints[i].y);
            }
            canvasCtx.stroke();
        }
    };

    const stopDrawing = () => {
        if (drawing) {
            drawing = false;

            // Final redraw with full smoothing
            if (currentPoints.length > 0) {
                redrawCurrentStroke();
            }

            saveToHistory();
            currentPoints = []; // Clear points
        }
    };

    const updateSmoothing = () => {
        switch(smoothingLevel.value) {
            case 'off':
                smoothingEnabled.value = false;
                minDistance = 2;
                break;
            case 'low':
                smoothingEnabled.value = true;
                minDistance = 4;
                break;
            case 'medium':
                smoothingEnabled.value = true;
                minDistance = 6;
                break;
            case 'high':
                smoothingEnabled.value = true;
                minDistance = 8;
                break;
        }
    };

    const handleTouchStart = (e) => {
        e.preventDefault();
        startDrawing(e);
    };

    const handleTouchMove = (e) => {
        e.preventDefault();
        draw(e);
    };

    const clearCanvas = () => {
        if (canvasCtx && mainCanvas.value) {
            canvasCtx.fillStyle = '#ffffff';
            canvasCtx.fillRect(0, 0, canvasWidth.value, canvasHeight.value);
            canvasCtx.fillStyle = '#000000';
            saveToHistory();
            hasDrawing.value = false;
            updateCanvasImageSrc();
        }
    };

    const setBrushSize = (size) => {
        currentBrushSize.value = size;
        if (canvasCtx) {
            canvasCtx.lineWidth = currentBrushSize.value;
        }
    };

    // Upload functions
    const triggerFileUpload = () => {
        fileInput.value.click();
    };

    const handleFileUpload = (e) => {
        const target = e.target;
        if (target.files && target.files[0]) {
            const file = target.files[0];
            const reader = new FileReader();

            reader.onload = (event) => {
                const img = new Image();
                img.onload = () => {
                    uploadedImage.value = img;
                    initializeImageOnCanvas();
                };
                img.src = event.target.result;
            };
            reader.readAsDataURL(file);
        }
    };

    const initializeImageOnCanvas = () => {
        if (!uploadedImage.value || !canvasCtx || !mainCanvas.value) return;

        const canvasAspect = canvasWidth.value / canvasHeight.value;
        const imageAspect = uploadedImage.value.width / uploadedImage.value.height;

        let drawWidth, drawHeight;
        if (imageAspect > canvasAspect) {
            drawWidth = canvasWidth.value;
            drawHeight = canvasWidth.value / imageAspect;
        } else {
            drawHeight = canvasHeight.value;
            drawWidth = canvasHeight.value * imageAspect;
        }

        imageTransform.value.width = drawWidth;
        imageTransform.value.height = drawHeight;
        imageTransform.value.x = (canvasWidth.value - drawWidth) / 2;
        imageTransform.value.y = (canvasHeight.value - drawHeight) / 2;
        imageTransform.value.rotation = 0;
        imageTransform.value.scaleX = 1;
        imageTransform.value.scaleY = 1;

        drawUploadedImage();
    };

    const drawUploadedImage = () => {
        if (!uploadedImage.value || !canvasCtx || !mainCanvas.value) return;

        canvasCtx.fillStyle = '#ffffff';
        canvasCtx.fillRect(0, 0, canvasWidth.value, canvasHeight.value);

        canvasCtx.save();
        const centerX = imageTransform.value.x + imageTransform.value.width / 2;
        const centerY = imageTransform.value.y + imageTransform.value.height / 2;

        canvasCtx.translate(centerX, centerY);
        canvasCtx.rotate((imageTransform.value.rotation * Math.PI) / 180);
        canvasCtx.scale(imageTransform.value.scaleX, imageTransform.value.scaleY);
        canvasCtx.translate(-centerX, -centerY);

        canvasCtx.drawImage(
            uploadedImage.value,
            imageTransform.value.x,
            imageTransform.value.y,
            imageTransform.value.width,
            imageTransform.value.height
        );

        canvasCtx.restore();
        updateCanvasImageSrc();
        hasDrawing.value = true;
    };

    const rotateImage = (degrees) => {
        if (!uploadedImage.value) return;
        imageTransform.value.rotation = (imageTransform.value.rotation + degrees) % 360;
        drawUploadedImage();
    };

    const resetImageTransform = () => {
        if (!uploadedImage.value) return;
        initializeImageOnCanvas();
    };

    // Cropper functions
    const onCropperReady = () => {
        console.log('Cropper is ready');
        isLoadingCropper.value = false;
    };

    const handleCropChange = ({ coordinates, canvas }) => {
        if (canvas) {
            // Get the cropped image as data URL
            croppedImageData.value = canvas.toDataURL('image/png');
        }
    };

    const applyCropToCanvas = async () => {
        if (croppedImageData.value && mainCanvas.value && canvasCtx) {
            return new Promise((resolve) => {
                const img = new Image();
                img.onload = () => {
                    // Clear canvas
                    canvasCtx.fillStyle = '#ffffff';
                    canvasCtx.fillRect(0, 0, canvasWidth.value, canvasHeight.value);

                    // Calculate dimensions to fit the canvas
                    const scale = Math.min(
                        canvasWidth.value / img.width,
                        canvasHeight.value / img.height
                    );

                    const width = img.width * scale;
                    const height = img.height * scale;
                    const x = (canvasWidth.value - width) / 2;
                    const y = (canvasHeight.value - height) / 2;

                    // Draw cropped image centered on canvas
                    canvasCtx.drawImage(img, x, y, width, height);
                    updateCanvasImageSrc();
                    hasDrawing.value = true;
                    resolve();
                };
                img.src = croppedImageData.value;
            });
        }
    };

    // Save functions
    const saveSignatureFromCurrentTab = async () => {
        try {
            let blob;

            if (activeTab.value === 'crop' && croppedImageData.value) {
                // Apply crop to the main canvas first
                await applyCropToCanvas();
                // Then get blob from canvas
                blob = await new Promise((resolve) => {
                    mainCanvas.value.toBlob(resolve, 'image/png', 1);
                });
            } else if (mainCanvas.value) {
                blob = await new Promise((resolve) => {
                    mainCanvas.value.toBlob(resolve, 'image/png', 1);
                });
            }

            if (blob) {
                const file = new File([blob], 'signature.png', { type: 'image/png' });
                saveSignature(file);
                closeModal();
            }
        } catch (error) {
            console.error('Error saving signature:', error);
            Swal.fire('Error', 'Failed to save signature', 'error');
        }
    };

    const saveSignature = (file) => {
        signatureFile.value = file;
        if (signatureImage.value) {
            URL.revokeObjectURL(signatureImage.value);
        }
        signatureImage.value = URL.createObjectURL(file);
        emit('update:modelValue', file);
        emit('saved', file);

        Swal.fire({
            icon: 'success',
            title: 'Signature Saved',
            showConfirmButton: false,
            timer: 1500
        });
    };

    const confirmRemove = () => {
        Swal.fire({
            title: 'Remove Signature?',
            text: 'Are you sure you want to remove your signature?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, remove it!'
        }).then((result) => {
            if (result.isConfirmed) {
                if (signatureImage.value) {
                    URL.revokeObjectURL(signatureImage.value);
                }
                signatureImage.value = null;
                signatureFile.value = null;
                emit('update:modelValue', null);
                emit('removed');

                Swal.fire('Removed!', 'Your signature has been removed.', 'success');
            }
        });
    };

    // Tab switching
    const switchTab = async (tab) => {
        if (tab === 'crop' && !hasCanvasContent.value) {
            return;
        }

        if (tab === 'crop') {
            isLoadingCropper.value = true;
            // Update the image source before switching
            updateCanvasImageSrc();
            await nextTick();
            // Small delay to ensure DOM updates
            setTimeout(() => {
                isLoadingCropper.value = false;
            }, 200);
        }

        activeTab.value = tab;
        croppedImageData.value = null;
    };

    const openSignatureModal = () => {
        console.log('Opening signature modal');
        activeTab.value = 'draw';
        showModal.value = true;

        nextTick(() => {
            // Reset states
            drawHistory.value = [];
            hasDrawing.value = false;
            drawing = false;
            uploadedImage.value = null;
            croppedImageData.value = null;

            // Initialize canvas
            initMainCanvas();
        });
    };

    const closeModal = () => {
        showModal.value = false;
        croppedImageData.value = null;
        isLoadingCropper.value = false;
    };

    const cleanup = () => {
        uploadedImage.value = null;
        hasDrawing.value = false;
        drawHistory.value = [];
        croppedImageData.value = null;
        isLoadingCropper.value = false;
    };

    // Watchers
    watch(() => props.existingSignatureUrl, (newVal) => {
        console.log('Existing signature URL changed:', newVal);
        if (newVal) {
            signatureImage.value = newVal;
        }
    });

    watch(() => props.modelValue, (newVal) => {
        if (newVal) {
            signatureFile.value = newVal;
        }
    });

    watch(showModal, (newVal) => {
        if (newVal) {
            nextTick(() => {
                initMainCanvas();
            });
        } else {
            cleanup();
        }
    });

    watch(canvasImageSrc, async (newVal) => {
        if (newVal && activeTab.value === 'crop') {
            await refreshCropper();
        }
    });

    // Lifecycle
    onMounted(() => {
        console.log('Component mounted');
        if (props.existingSignatureUrl) {
            signatureImage.value = props.existingSignatureUrl;
        }
        if (props.modelValue) {
            signatureFile.value = props.modelValue;
        }
    });

    onBeforeUnmount(() => {
        if (signatureImage.value) {
            URL.revokeObjectURL(signatureImage.value);
        }
    });
</script>

<style scoped>
    canvas {
        display: block;
        max-width: 100%;
        height: auto;
        background: white;
    }

    .touch-none {
        touch-action: none;
    }

    .cropper-container {
        height: 400px;
        background: #DDD;
        border-radius: 8px;
        overflow: hidden;
        position: relative;
    }

    .cropper {
        height: 100%;
        width: 100%;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }
</style>
