<template>
    <div v-if="modelValue"
         class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/50"
         @keydown="handleTab"
         @keydown.escape="close">

        <div ref="dialogRef"
             :class="['bg-white rounded-lg shadow-2xl w-full mx-4', maxWidth]"
             role="dialog"
             aria-modal="true"
             :aria-labelledby="title ? 'dialog-title' : undefined"
             tabindex="-1">

            <!-- Header -->
            <div v-if="title" class="relative px-6 pt-6 pb-2 text-center border-b border-gray-200">
                <h2 :id="title ? 'dialog-title' : undefined"
                    :class="['text-2xl font-bold text-blue-600', titleClass]">
                    {{ title }}
                </h2>
                <slot name="header-right" />
                <p v-if="subtitle" class="text-gray-600 mt-1">{{ subtitle }}</p>
            </div>

            <!-- Body -->
            <div class="p-6 pt-2">
                <slot />
            </div>

            <!-- Footer -->
            <div v-if="$slots.footer" class="px-6 pb-6">
                <slot name="footer" />
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
    import { ref, watch, onMounted, onUnmounted, nextTick } from 'vue';

    const props = defineProps({
        modelValue: Boolean,
        title: String,
        subtitle: String,
        titleClass: String,
        maxWidth: {
            type: String,
            default: "max-w-md"
        }
    });

    const emit = defineEmits(['update:modelValue']);

    const dialogRef = ref<HTMLElement | null>(null);
    let focusableElements: HTMLElement[] = [];
    let firstFocusableElement: HTMLElement | null = null;
    let lastFocusableElement: HTMLElement | null = null;
    let previousActiveElement: HTMLElement | null = null;

    // Function to handle dialog open
    const handleDialogOpen = () => {
        // Store the current active element
        previousActiveElement = document.activeElement as HTMLElement;

        // Hide scrollbar
        document.body.style.overflow = 'hidden';
        document.body.style.paddingRight = '15px'; // Prevent layout shift

        // Focus trap setup
        nextTick(() => {
            if (dialogRef.value) {
                setFocusableElements();
                // Focus the first focusable element or the dialog itself
                if (firstFocusableElement) {
                    firstFocusableElement.focus();
                } else {
                    dialogRef.value.focus();
                }
            }
        });
    };

    // Function to handle dialog close
    const handleDialogClose = () => {
        // Restore scrollbar
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';

        // Restore focus to previous element
        if (previousActiveElement) {
            previousActiveElement.focus();
        }
    };

    // Watch for modelValue changes
    watch(() => props.modelValue, (isOpen) => {
        if (isOpen) {
            handleDialogOpen();
        } else {
            handleDialogClose();
        }
    });

    // Check initial state on mount
    onMounted(() => {
        if (props.modelValue) {
            handleDialogOpen();
        }
    });

    // Collect all focusable elements within the dialog
    const setFocusableElements = () => {
        if (!dialogRef.value) return;

        // Find all focusable elements
        focusableElements = Array.from(
            dialogRef.value.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            )
        ) as HTMLElement[];

        // Filter out disabled elements
        focusableElements = focusableElements.filter(el => !el.hasAttribute('disabled'));

        if (focusableElements.length > 0) {
            firstFocusableElement = focusableElements[0];
            lastFocusableElement = focusableElements[focusableElements.length - 1];
        } else {
            firstFocusableElement = null;
            lastFocusableElement = null;
        }
    };

    // Handle tab key for focus trapping
    const handleTab = (e: KeyboardEvent) => {
        if (!focusableElements.length) return;

        if (e.key === 'Tab') {
            if (e.shiftKey) {
                // Shift + Tab - if on first element, go to last
                if (document.activeElement === firstFocusableElement) {
                    e.preventDefault();
                    lastFocusableElement?.focus();
                }
            } else {
                // Tab - if on the last element, go to the first
                if (document.activeElement === lastFocusableElement) {
                    e.preventDefault();
                    firstFocusableElement?.focus();
                }
            }
        }
    };

    // Close dialog function
    function close() {
        emit('update:modelValue', false);
    }

    // Update focusable elements when content changes
    const updateFocusableElements = () => {
        if (props.modelValue && dialogRef.value) {
            nextTick(() => {
                setFocusableElements();
            });
        }
    };

    // Watch for slot content changes
    watch(() => props.modelValue, (newVal) => {
        if (newVal) {
            updateFocusableElements();
        }
    });

    // Cleanup on unmount
    onUnmounted(() => {
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    });
</script>
