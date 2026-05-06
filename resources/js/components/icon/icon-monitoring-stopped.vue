<script setup lang="ts">
    defineProps({
        title: String
    })
</script>
<template>
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
        <!-- add title element if title prop exists -->
        <title v-if="title">{{ title }}</title>

        <path d="M4 12C8 8 16 8 20 12" stroke="#9ca3af" stroke-width="2"/>
        <path d="M7 15C10 12 14 12 17 15" stroke="#9ca3af" stroke-width="2"/>
        <circle cx="12" cy="18" r="2.5" fill="#9ca3af"/>
    </svg>
</template>
