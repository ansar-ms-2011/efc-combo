<template>
    <div v-if="totalPages > 1" class="inline-flex items-center space-x-1 rtl:space-x-reverse m-auto justify-center w-full mt-5 mb-3">
        <!-- Previous Button -->
        <button
            @click="goToPage(currentPage - 1)"
            :disabled="currentPage === 1"
            class="px-3.5 py-1 rounded font-semibold bg-gray-200 hover:bg-blue-500 hover:text-white disabled:opacity-50 disabled:cursor-not-allowed"
        >
            Prev
        </button>

        <!-- First Page -->
        <button
            v-if="showFirstEllipsis"
            @click="goToPage(1)"
            class="px-3 py-1 rounded font-semibold bg-gray-200 hover:bg-blue-500 hover:text-white"
        >
            1
        </button>

        <span v-if="showFirstEllipsis" class="px-2">...</span>

        <!-- Page Numbers -->
        <button
            v-for="page in visiblePages"
            :key="page"
            @click="goToPage(page)"
            :class="[
                'px-3 py-1 rounded font-semibold transition',
                page === currentPage
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-blue-500 hover:text-white'
            ]"
        >
            {{ page }}
        </button>

        <!-- Last Page Ellipsis -->
        <span v-if="showLastEllipsis" class="px-2">...</span>

        <button
            v-if="showLastEllipsis"
            @click="goToPage(totalPages)"
            class="px-3 py-1 rounded font-semibold bg-gray-200 hover:bg-blue-500 hover:text-white"
        >
            {{ totalPages }}
        </button>

        <!-- Next Button -->
        <button
            @click="goToPage(currentPage + 1)"
            :disabled="currentPage === totalPages"
            class="px-3.5 py-1 rounded font-semibold bg-gray-200 hover:bg-blue-500 hover:text-white disabled:opacity-50 disabled:cursor-not-allowed"
        >
            Next
        </button>
    </div>
</template>

<script setup>
    import { computed } from 'vue'

    const props = defineProps({
        currentPage: {
            type: Number,
            required: true,
            default: 1
        },
        totalPages: {
            type: Number,
            required: true,
            default: 1
        },
        maxVisibleButtons: {
            type: Number,
            default: 5
        }
    })

    const emit = defineEmits(['page-changed'])

    // Navigate to specific page
    const goToPage = (page) => {
        if (page >= 1 && page <= props.totalPages && page !== props.currentPage) {
            emit('page-changed', page)
        }
    }

    // Calculate visible page numbers with ellipsis logic
    const visiblePages = computed(() => {
        const { currentPage, totalPages, maxVisibleButtons } = props
        const halfVisible = Math.floor(maxVisibleButtons / 2)

        let start = Math.max(currentPage - halfVisible, 1)
        let end = Math.min(start + maxVisibleButtons - 1, totalPages)

        // Adjust start if we're near the end
        if (end - start + 1 < maxVisibleButtons) {
            start = Math.max(end - maxVisibleButtons + 1, 1)
        }

        // Return array of page numbers without first and last if they're handled by ellipsis
        const pages = []
        for (let i = start; i <= end; i++) {
            if (
                (i !== 1 && i !== totalPages) || // Not first or last page
                totalPages <= maxVisibleButtons + 2 // Or total pages is small
            ) {
                pages.push(i)
            }
        }

        return pages
    })

    // Show first page with ellipsis
    const showFirstEllipsis = computed(() => {
        return props.totalPages > props.maxVisibleButtons + 2 && props.currentPage > Math.floor(props.maxVisibleButtons / 2) + 1
    })

    // Show last page with ellipsis
    const showLastEllipsis = computed(() => {
        return props.totalPages > props.maxVisibleButtons + 2 && props.currentPage < props.totalPages - Math.floor(props.maxVisibleButtons / 2)
    })
</script>
