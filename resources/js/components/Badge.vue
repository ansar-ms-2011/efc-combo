<script setup>
    import { computed } from 'vue'

    const props = defineProps({
        variant: {
            type: String,
            default: 'gray' // gray, green, red, blue, yellow, purple
        },
        size: {
            type: String,
            default: 'sm' // sm, md
        },
        rounded: {
            type: Boolean,
            default: true
        }
    })

    const variantClasses = computed(() => {
        const variants = {
            gray: 'bg-gray-100 text-gray-700 border border-gray-200',
            green: 'bg-green-100 text-green-700 border border-green-200',
            red: 'bg-red-100 text-red-700 border border-red-200',
            'red-light': 'bg-red-50 text-red-700 border border-red-200',
            blue: 'bg-blue-100 text-blue-700 border border-blue-200',
            yellow: 'bg-yellow-100 text-yellow-700 border border-yellow-200',
            purple: 'bg-purple-100 text-purple-700 border border-purple-200',
          'purple-light': 'bg-purple-50 text-purple-700 border border-purple-200',
        }

        return variants[props.variant] || variants.gray
    })

    const sizeClasses = computed(() => {
        return props.size === 'md'
            ? 'px-3 py-1 text-sm'
            : 'px-2.5 py-0.5 text-xs'
    })

    const roundedClass = computed(() =>
        props.rounded ? 'rounded-full' : 'rounded'
    )
</script>

<template>
    <span
        :class="[variantClasses, sizeClasses, roundedClass]"
        class="inline-flex items-center font-medium"
    >
        <slot />
    </span>
</template>
