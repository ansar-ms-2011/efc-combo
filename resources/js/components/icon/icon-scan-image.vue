
<template>
    <svg xmlns="http://www.w3.org/2000/svg"
         width="64" height="64"
         viewBox="0 0 24 24"
         fill="none"
         stroke="currentColor"
         stroke-width="2"
         stroke-linecap="round"
         stroke-linejoin="round">

        <!-- Scanner frame -->
        <path d="M4 7V4h3" />
        <path d="M20 7V4h-3" />
        <path d="M4 17v3h3" />
        <path d="M20 17v3h-3" />

        <!-- Image (mountain + sun) -->
        <rect x="6" y="8" width="12" height="8" rx="1" />
        <circle cx="9" cy="10" r="1" />
        <path d="M6 14l3-3 2 2 3-3 4 4" />

        <!-- Scan line -->
        <line x1="3" y1="12" x2="21" y2="12" />

    </svg>
</template>
