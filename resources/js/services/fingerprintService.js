import Swal from 'sweetalert2';

class FingerprintService {
    constructor() {
        this.sdk = null;
        this.isInitialized = false;
        this.acquisitionStarted = false;
        this.currentFormat = null;
        this.capturePromise = null;
    }

    /**
     * Check if SDK is available globally and initialize it
     * @returns {Promise<boolean>}
     */
    async loadSDK() {
        if (typeof window.Fingerprint !== 'undefined' && typeof window.WebSdk !== 'undefined') {
            return true;
        }

        return new Promise((resolve) => {
            let attempts = 0;
            const check = setInterval(() => {
                attempts++;
                if (typeof window.Fingerprint !== 'undefined' && typeof window.WebSdk !== 'undefined') {
                    clearInterval(check);
                    resolve(true);
                } else if (attempts > 50) {
                    clearInterval(check);
                    console.error('DigitalPersona SDK failed to load globally.');
                    resolve(false);
                }
            }, 100);
        });
    }

    /**
     * Initialize the fingerprint reader
     * @returns {Promise<boolean>}
     */
    async initialize() {
        const sdkLoaded = await this.loadSDK();
        if (!sdkLoaded) {
            this.showError('SDK Not Loaded', 'DigitalPersona SDK scripts are missing. Please check index.html.');
            return false;
        }

        try {
            if (!this.sdk) {
                this.sdk = new window.Fingerprint.WebApi();

                // Set up event handlers matching the sample app
                this.sdk.onDeviceConnected = (e) => console.log("Scanner Connected");
                this.sdk.onDeviceDisconnected = (e) => console.log("Scanner Disconnected");
                this.sdk.onCommunicationFailed = (e) => {
                    console.error("Communication Failed", e);
                    if (this.capturePromise) this.capturePromise.reject(new Error("Communication Failed"));
                };

                this.sdk.onSamplesAcquired = (s) => {
                    this.handleSampleAcquired(s);
                };
            }
            this.isInitialized = true;
            return true;
        } catch (error) {
            console.error('Initialization error:', error);
            return false;
        }
    }

    /**
     * Handle the sample acquired event
     */
    handleSampleAcquired(s) {
        if (!this.capturePromise) return;

        try {
            const samples = JSON.parse(s.samples);
            const result = {
                image: null,
                featureSet: null
            };

            if (samples && samples.length > 0) {
                const sample = samples[0];

                // Get the image
                const base64Sample = window.Fingerprint.b64UrlTo64(sample);

                result.image = "data:image/png;base64," + base64Sample;
                // console.log("base64Sample", result.image);
                // For feature set, we need to extract from the raw sample
                // The feature set is the base64 encoded sample itself
                result.featureSet = base64Sample;  //TODO: This required to be changed to the feature set, it is not the image itself.
            }

            this.capturePromise.resolve(result);
        } catch (error) {
            console.error("Error parsing sample:", error);
            this.capturePromise.reject(error);
        } finally {
            this.stopAcquisition();
        }
    }

    /**
     * Capture a fingerprint (both image and feature set in one touch)
     * @returns {Promise<Object>}
     */
    async captureFingerprint() {
        if (!this.isInitialized) {
            const ok = await this.initialize();
            if (!ok) return null;
        }

        return new Promise((resolve, reject) => {
            this.capturePromise = { resolve, reject };
            this.currentFormat = window.Fingerprint.SampleFormat.PngImage;

            this.sdk.startAcquisition(this.currentFormat).then(
                () => {
                    this.acquisitionStarted = true;
                },
                (error) => {
                    this.showError('Capture Error', error.message);
                    reject(error);
                }
            );
        });
    }

    /**
     * Stop acquisition
     */
    async stopAcquisition() {
        if (this.sdk && this.acquisitionStarted) {
            try {
                await this.sdk.stopAcquisition();
                this.acquisitionStarted = false;
            } catch (error) {
                console.error("Error stopping acquisition:", error);
            }
        }
        this.capturePromise = null;
    }

    showError(title, message) {
        Swal.fire({ title, text: message, icon: 'error' });
    }
}

const fingerprintService = new FingerprintService();
export default fingerprintService;
