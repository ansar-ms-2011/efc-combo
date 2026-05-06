<script setup lang="ts">
    defineProps({
        title: String
    })
</script>
<template>
    <svg width="28" height="28" viewBox="0 0 24 24" fill="none"
         xmlns="http://www.w3.org/2000/svg">

        <!-- add title element if title prop exists -->
        <title v-if="title">{{ title }}</title>

        <!-- Outer wave -->
        <path d="M3 10C8 5 16 5 21 10"
              stroke="#22c55e"
              stroke-width="2"
              stroke-linecap="round"
              opacity="0.2">
            <animate attributeName="opacity"
                     values="0.2;1;0.2"
                     dur="1.6s"
                     repeatCount="indefinite"/>
        </path>

        <!-- Middle wave -->
        <path d="M6 13C9 10 15 10 18 13"
              stroke="#22c55e"
              stroke-width="2"
              stroke-linecap="round"
              opacity="0.4">
            <animate attributeName="opacity"
                     values="0.4;1;0.4"
                     dur="1.6s"
                     begin="0.2s"
                     repeatCount="indefinite"/>
        </path>

        <!-- Inner wave -->
        <path d="M9 16C11 14.5 13 14.5 15 16"
              stroke="#22c55e"
              stroke-width="2"
              stroke-linecap="round"
              opacity="0.7">
            <animate attributeName="opacity"
                     values="0.7;1;0.7"
                     dur="1.6s"
                     begin="0.4s"
                     repeatCount="indefinite"/>
        </path>

        <!-- Dot -->
        <circle cx="12" cy="19" r="2.2" fill="#22c55e">
            <animate attributeName="r"
                     values="2.2;2.8;2.2"
                     dur="1.2s"
                     repeatCount="indefinite"/>
        </circle>

    </svg>
</template>

