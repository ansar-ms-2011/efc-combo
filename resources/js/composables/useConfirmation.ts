// composables/useConfirmation.ts
import { ref } from 'vue';

interface ConfirmationOptions {
    title: string;
    message: string;
    confirmText?: string;
    cancelText?: string;
    confirmButtonClass?: string;
    onConfirm: () => void | Promise<void>;
    onCancel?: () => void;
}

export const useConfirmation = () => {
    const showConfirmation = ref(false);
    const options = ref<ConfirmationOptions | null>(null);
    let isProcessing = false;

    const confirm = (confirmOptions: ConfirmationOptions) => {
        options.value = confirmOptions;
        showConfirmation.value = true;
    };

    const handleConfirm = async () => {
        if (isProcessing || !options.value) return;

        isProcessing = true;
        try {
            await options.value.onConfirm();
            showConfirmation.value = false;
        } catch (error) {
            console.error('Confirmation action failed:', error);
        } finally {
            isProcessing = false;
        }
    };

    const handleCancel = () => {
        if (options.value?.onCancel) {
            options.value.onCancel();
        }
        showConfirmation.value = false;
    };

    return {
        confirm,
        showConfirmation,
        options,
        handleConfirm,
        handleCancel
    };
};
