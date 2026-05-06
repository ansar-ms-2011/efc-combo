<script setup lang="ts">
    defineProps({
        title: String
    });
</script>
<template>
    <svg
        width="28" height="28"
        viewBox="0 0 24 24" fill="none"
        xmlns="http://www.w3.org/2000/svg"
    >

        <!-- add title element if title prop exists -->
        <title v-if="title">{{ title }}</title>

        <!-- Signal waves -->
        <path d="M4 12C8 8 16 8 20 12" stroke="#ef4444" stroke-width="2" stroke-linecap="round" />
        <path d="M7 15C10 12 14 12 17 15" stroke="#ef4444" stroke-width="2" stroke-linecap="round" />
        <!-- Dot -->
        <circle cx="12" cy="18" r="2.5" fill="#ef4444" />
        <!-- Slash -->
        <line x1="3" y1="3" x2="21" y2="21" stroke="#ef4444" stroke-width="2" stroke-linecap="round" />
    </svg>
</template>
