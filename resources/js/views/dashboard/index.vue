<template>
    <SuperAdminDashboard v-if="store.user?.role === 'Super Admin'" />
    <SupervisorDashboard v-else-if="store.user?.role === 'Supervisor' || store.user?.role === 'Scanner'" />
    <UserDashboard v-else />
</template>
<script lang="ts" setup>
    import { useAppStore } from '@/stores';
    import { useMeta } from '@/composables/use-meta';
    import SuperAdminDashboard from '@/views/dashboard/super-admin-dashboard.vue';
    import UserDashboard from '@/views/dashboard/user-dashboard.vue';
    import SupervisorDashboard from '@/views/dashboard/archivedDashboard.vue';

    import { onMounted } from 'vue';

    const store = useAppStore();

    onMounted(() => {
        const title =
            store.user?.role === 'Super Admin'
                ? 'EFC : Super Admin Dashboard'
                : store.user?.role === 'Supervisor'
                  ? 'EFC : Supervisor Dashboard'
                  : store.user?.role === 'Scanner'
                    ? 'EFC : Scanner Dashboard'
                    : 'EFC : User Dashboard';
        useMeta({ title });
    });
</script>
