<template>
    <div class="p-6 min-h-screen">
        <!-- Breadcrumb -->
        <!-- Breadcrumb -->
        <AppBreadcrumb :items="breadcrumbs" />
        <UserForm/>
    </div>
</template>

<script setup>
    import AppBreadcrumb from '@/components/layout/AppBreadCrumb.vue';
    import UserForm from '@/views/user/userForm.vue';

    const breadcrumbs = [
        { label: 'Dashboard', to: '/admin/dashboard' },
        { label: 'User List', to: '/users' },
        { label: 'Create User' } // last item no "to"
    ];
</script>

