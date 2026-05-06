<template>
    <div
        class="main-section antialiased relative font-nunito text-sm font-normal"
        :class="[store.sidebar ? 'toggle-sidebar' : '', store.menu, store.layout, store.rtlClass]"
    >
        <component v-bind:is="mainLayout"></component>
    </div>
</template>

<script lang="ts" setup>
    import { computed, onMounted } from 'vue';
    import appLayout from '@/layouts/app-layout.vue';
    import authLayout from '@/layouts/auth-layout.vue';
    import { useAppStore } from '@/stores';

    const store = useAppStore();

    const mainLayout = computed(() => {
        // console.log('computing layout..');
        return store.mainLayout === 'auth' ? authLayout : appLayout;
    });
</script>
