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

        <!-- Document -->
        <path d="M8 6h6l2 2v10H8z" />
        <path d="M14 6v2h2" />

        <!-- PDF text -->
        <text x="9" y="15" font-size="3" fill="currentColor" stroke="none">PDF</text>

        <!-- Scan line -->
        <line x1="3" y1="12" x2="21" y2="12" />

    </svg>
</template>
