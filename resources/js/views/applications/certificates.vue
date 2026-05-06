<template>
    <div class="certificate-container">
        <div v-if="!invalidUuid" class="certificate-selection flex justify-between items-center mb-4 w-[70%]">
            <span class="text-2xl font-bold">Certificates</span>
            <div v-if="application && application.certificates && application.certificates.length"
                class="flex items-center gap-2">
                <label v-for="certificate in application.certificates" :key="certificate.uuid"
                    class="certificate-label">
                    <input type="radio" name="certificate" :value="certificate.uuid" v-model="selectedCertificate"
                        @change="loadCertificate" class="mr-2" />
                    {{ getCertificateName(certificate.type) }}
                </label>
                <!-- Print Button -->
                <button @click="printOriginalCertificate" :disabled="isPrinting || !selectedCertificate"
                    class="print-button w-[130px]">
                    {{ isPrinting ? 'Fetching...' : 'Print' }}
                </button>

                <button @click="markAsDelivered" :disabled="!selectedCertificate || isMarking"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 disabled:bg-gray-400 w-[130px]">
                    {{ isMarking ? 'Marking...' : 'Mark Delivered' }}
                </button>
            </div>
        </div>

        <div class="pdf-preview-wrapper w-[70%]" v-if="!invalidUuid">
            <div v-if="loadingPreview" class="loading-spinner">
                Loading preview...
            </div>
            <div v-else-if="!pdfDoc" class="loading-spinner">
                Select a certificate to preview
            </div>
            <div v-else class="pdf-canvas-container w-full">
                <canvas v-for="pageNum in totalPages" :key="pageNum" :ref="el => setCanvasRef(el, pageNum)"
                    class="pdf-canvas"></canvas>
            </div>
        </div>
        <div v-else class="flex justify-center items-center min-h-[500px] text-center mt-4">
            <span class="text-red-500 text-lg">
                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                No certificate(s) found for this application ID
            </span>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, markRaw, nextTick, watch } from 'vue';
import * as pdfjsLib from 'pdfjs-dist';
import apiClient from '@/services/axios.ts';
import { useRoute } from 'vue-router';
import Swal from 'sweetalert2';

import workerUrl from 'pdfjs-dist/build/pdf.worker.min.mjs?url';

// Set the worker URL
pdfjsLib.GlobalWorkerOptions.workerSrc = workerUrl;

const route = useRoute();
const application = ref(null);

// Reactive state
const loadingPreview = ref(false);
const isPrinting = ref(false);
const totalPages = ref(0);
const pdfDoc = ref(null);
const canvasRefs = ref({});
const selectedCertificate = ref(null);
const invalidUuid = ref(false);

const getCertificateName = (type) => {
    switch (type) {
        case 'state':
            return 'State Subject Certificate';
        case 'domicile':
            return 'Domicile Certificate';
        default:
            return 'Certificate';
    }
}
// Set canvas refs dynamically
const setCanvasRef = (el, pageNum) => {
    if (el) {
        canvasRefs.value[pageNum] = el;
    }
};

const loadCertificate = async () => {
    if (!selectedCertificate.value) return;
    await nextTick();
    await loadPreview();
};

const isMarking = ref(false);

const markAsDelivered = async () => {
    if (!selectedCertificate.value) return;

    try {
        isMarking.value = true;

        await apiClient.post(`/api/certificate/mark-delivered/${selectedCertificate.value}`);

        application.value.current_status = 'delivered';

        Swal.fire({
            icon: 'success',
            text: 'Certificate marked as delivered successfully',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1500
        });

    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to mark as delivered',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000
        });
    } finally {
        isMarking.value = false;
    }
};

const getApplicationWithCertificates = async () => {
    try {
        invalidUuid.value = false;
        const response = await apiClient.get(`/api/application-certificates/${route.params.uuid}`);
        application.value = response.data;

        // Load first certificate automatically if available
        if (application.value &&
            application.value.certificates &&
            application.value.certificates.length > 0) {
            selectedCertificate.value = application.value.certificates[0].uuid;
            await loadCertificate();
        }
    } catch (error) {
        console.error('Error fetching application:', error);
        invalidUuid.value = true;
    }
};

// Render a single page
const renderPage = async (pageNum) => {
    try {
        const page = await pdfDoc.value.getPage(pageNum);

        const viewport = page.getViewport({ scale: 1.5 });

        const canvas = canvasRefs.value[pageNum];

        if (!canvas) {
            console.warn('Canvas not found for page', pageNum);
            return;
        }

        const context = canvas.getContext('2d');
        if (!context) return;

        canvas.height = viewport.height;
        canvas.width = viewport.width;

        await page.render({
            canvasContext: context,
            viewport: viewport
        }).promise;

    } catch (error) {
        console.error(`Error rendering page ${pageNum}:`, error);
    }
};

// 1. Load and render preview PDF
const loadPreview = async () => {
    if (!selectedCertificate.value) return;

    try {
        loadingPreview.value = true;
        // Clear previous PDF and canvases
        pdfDoc.value = null;
        canvasRefs.value = {};
        totalPages.value = 0;

        const response = await apiClient.get(`/api/certificate/preview/${selectedCertificate.value}`, {
            responseType: 'arraybuffer'
        });

        const loadingTask = pdfjsLib.getDocument({
            data: response.data,
            disableAutoFetch: true,
            disableStream: true
        });

        pdfDoc.value = markRaw(await loadingTask.promise);
        totalPages.value = pdfDoc.value.numPages;

        console.log('Pages:', totalPages.value);

        // Hide loading spinner and wait for canvases
        loadingPreview.value = false;
        await nextTick();

        // Render all pages
        const renderPromises = [];
        for (let i = 1; i <= totalPages.value; i++) {
            renderPromises.push(renderPage(i));
        }
        await Promise.all(renderPromises);

    } catch (error) {
        console.error('Error loading preview:', error);
        alert(error.message || 'Failed to load certificate preview');
        loadingPreview.value = false;
        pdfDoc.value = null;
    }
};

// 2. Fetch original PDF and print
const printOriginalCertificate = async () => {
    if (!selectedCertificate.value) {
        alert('Please select a certificate first');
        return;
    }

    isPrinting.value = true;

    try {
        const response = await apiClient.get(`/api/certificate/original/${selectedCertificate.value}`, {
            responseType: 'blob'
        });

        const url = URL.createObjectURL(response.data);

        const iframe = document.createElement('iframe');
        iframe.style.position = 'absolute';
        iframe.style.width = '0';
        iframe.style.height = '0';
        iframe.style.border = 'none';
        iframe.src = url;

        document.body.appendChild(iframe);

        iframe.onload = () => {
            setTimeout(() => {
                iframe.contentWindow.print();

                const cleanup = () => {
                    URL.revokeObjectURL(url);
                    if (document.body.contains(iframe)) {
                        document.body.removeChild(iframe);
                    }
                };

                iframe.contentWindow.onafterprint = cleanup;
                setTimeout(cleanup, 5000);
            }, 100);
        };

        iframe.onerror = () => {
            URL.revokeObjectURL(url);
            document.body.removeChild(iframe);
            throw new Error('Failed to load PDF for printing');
        };

    } catch (error) {
        console.error('Error fetching original certificate:', error);
        alert(error.message || 'Failed to load original certificate for printing');
    } finally {
        isPrinting.value = false;
    }
};

onMounted(async () => {
    await getApplicationWithCertificates();
});
</script>

<style scoped>
.certificate-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
}

.certificate-label {
    margin-right: 20px;
    margin-bottom: 0px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
}

.pdf-preview-wrapper {
    border: 1px solid #ccc;
    border-radius: 8px;
    overflow: auto;
    background: #f5f5f5;
    padding: 20px;
    min-height: 400px;
}

.pdf-canvas-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
}

.pdf-canvas {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    max-width: 100%;
    height: auto;
}

.loading-spinner {
    text-align: center;
    padding: 40px;
    font-size: 16px;
    color: #666;
}

.print-button {
    background-color: #4CAF50;
    color: white;
    padding: 8px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s;
}

.print-button:hover:not(:disabled) {
    background-color: #45a049;
}

.print-button:disabled {
    background-color: #cccccc;
    cursor: not-allowed;
}
</style>
