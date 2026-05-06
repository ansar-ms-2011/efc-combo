<template>
    <div class="mb-12">
        <div class="flex items-center justify-between">

            <template v-for="(step, index) in steps" :key="index">

                <!-- Step -->
                <div class="flex flex-col items-center relative z-10 ">
                    <div
                        class="w-7 h-7 rounded-full flex items-center justify-center font-semibold"
                        :class="stepClasses(index + 1)"
                    >
                        <span v-if="currentStep > index + 1">
                            <i class="fas fa-check text-sm"></i>
                        </span>
                        <span v-else>
                            {{ index + 1 }}
                        </span>
                    </div>

                    <span
                        class="text-lg font-medium mt-2 text-center font-nastaleeq"
                        :class="stepTextClasses(index + 1)"
                    >
                        {{ step }}
                    </span>
                </div>

                <!-- Line (except after last step) -->
                <div
                    v-if="index < steps.length - 1"
                    class="flex-1 h-1 relative -top-4"
                    :class="lineClasses(index + 1)"
                ></div>

            </template>

        </div>
    </div>
</template>

<script setup>
    import { computed } from 'vue';

    const props = defineProps({
        currentStep: {
            type: Number,
            required: true
        },
        steps: {
            type: Array,
            required: true,
            default: () => []
        }
    });

    /* Step Circle Styles */
    const stepClasses = (step) => {
        if (props.currentStep > step) {
            return 'bg-blue-400 text-white';
        }
        if (props.currentStep === step) {
            return 'bg-blue-600 text-white';
        }
        return 'bg-gray-300 text-gray-600';
    };

    /* Step Text Styles */
    const stepTextClasses = (step) => {
        if (props.currentStep >= step) {
            return 'text-blue-600';
        }
        return 'text-gray-400';
    };

    /* Line Styles */
    const lineClasses = (step) => {
        if (props.currentStep > step) {
            return 'bg-blue-500';
        }
        return 'bg-gray-300';
    };
</script>
