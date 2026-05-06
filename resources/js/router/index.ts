import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';
import { useAppStore } from '@/stores';
import appSetting from '@/app-setting';

const routes: RouteRecordRaw[] = [
    {
        path: '/login',
        name: 'login',
        component: () => import('@/views/login.vue'),
        meta: { layout: 'auth', requiresAuth: false, isAuthPage: true }
    },
    {
        path: '/enable-two-factor',
        name: 'enable-two-factir',
        component: () => import('@/views/two-factor.vue'),
        meta: { requiresAuth: true, skipPermissionCheck: true }
    },
    {
        path: '/quick-links',
        name: 'Quick Links',
        component: () => import('@/views/dashboard/quick-links.vue'),
        meta: { requiresAuth: true, skipPermissionCheck: true }
    },
    {
        path: '/',
        name: 'dashboard.view',
        component: () => import('@/views/dashboard/index.vue'),
        meta: { requiresAuth: true ,skipPermissionCheck: true}
    },
    {
        path: '/online-applications',
        name: 'online-applications.view',
        component: () => import('@/views/applications/index.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/applications/:status?',
        name: 'applications.view',
        component: () => import('@/views/applications/index.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/drafted-applications',
        name: 'drafted-applications.view',
        component: () => import('@/views/applications/draftedApplications.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/applications/create',
        name: 'applications.create',
        component: () => import('@/views/applications/create.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/applications/edit/:uuid',
        name: 'applications.edit',
        component: () => import('@/views/applications/edit.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/applications/edit-draft/:draftId',
        name: 'applications.edit-draft',
        component: () => import('@/views/applications/edit-draft.vue'),
        meta: { requiresAuth: true, skipPermissionCheck: true }
    },
    {
        path: '/applications/forward/:id',
        name: 'applications.forward',
        component: () => import('@/views/applications/forward.vue'),
        meta: { requiresAuth: true, skipPermissionCheck: true }
    },
    {
        path: '/archived/scanning-form/all',
        name: 'archived.scanning-form.all',
        component: () => import('@/views/archived/index.vue'),
        meta: { requiresAuth: true, skipPermissionCheck: true }
    },
    {
        path: '/archived/verification-form/all',
        name: 'archived.verification-form.all',
        component: () => import('@/views/archived/indexVerfication.vue'),
        meta: { requiresAuth: true, skipPermissionCheck: true }
    },
    {
        path: '/archivedReport',
        name: 'archived.report',
        component: () => import('@/views/archived/archivedReport.vue'),
        meta: {
            requiresAuth: true,
            skipPermissionCheck: true,
            title: 'Archived Documents Report'
        }
    },
    {
        path: '/center',
        name: 'centers.view',
        component: () => import('@/views/center/index.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/center/create',
        name: 'centers.create',
        component: () => import('@/views/center/create.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/center/edit/:id',
        name: 'centers.edit',
        component: () => import('@/views/center/edit.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/center/delete/:id',
        name: 'centers.delete',
        component: () => import('@/views/center/index.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/admin/roles',
        name: 'roles.view',
        component: () => import('@/views/roles/index.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/role/create',
        name: 'roles.create',
        component: () => import('@/views/roles/create.vue'),
        meta: { requiresAuth: true }
    },

    {
        path: '/role/edit/:id',
        name: 'roles.edit',
        component: () => import('@/views/roles/edit.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/services-center',
        name: 'services-centers.view',
        component: () => import('@/views/servicescenter/index.vue'),
        meta: { requiresAuth: true, title: 'Services Center' }
    },
    {
        path: '/services-center/create',
        name: 'services-centers.create',
        component: () => import('@/views/servicescenter/create.vue'),
        meta: { requiresAuth: true, title: 'Create Services Center' }
    },
    {
        path: '/services-center/edit/:id',
        name: 'services-centers.edit',
        component: () => import('@/views/servicescenter/edit.vue'),
        meta: { requiresAuth: true, title: 'Edit Services Center' }
    },

    {
        path: '/services',
        name: 'services.view',
        component: () => import('@/views/services/index.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/services/create',
        name: 'services.create',
        component: () => import('@/views/services/create.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/services/edit/:id',
        name: 'services.edit',
        component: () => import('@/views/services/edit.vue')
    },
    {
        path: '/users',
        name: 'users.view',
        component: () => import('@/views/user/index.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/user/create',
        name: 'users.create',
        component: () => import('@/views/user/create.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/user/edit/:id',
        name: 'users.edit',
        component: () => import('@/views/user/edit.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/departments',
        name: 'departments.view',
        component: () => import('@/views/department/index.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/department/create',
        name: 'departments.create',
        component: () => import('@/views/department/create.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/department/edit/:id',
        name: 'departments.edit',
        component: () => import('@/views/department/edit.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/department/delete/:id',
        name: 'departments.delete',
        component: () => import('@/views/department/index.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/service-instruction',
        name: 'service-instructions.view',
        component: () => import('@/views/serviceInstruction/index.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/service-instruction/create',
        name: 'service-instructions.create',
        component: () => import('@/views/serviceInstruction/create.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/service-instruction/edit/:id',
        name: 'service-instructions.edit',
        component: () => import('@/views/serviceInstruction/edit.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/templates',
        name: 'templates.view',
        component: () => import('@/views/template/index.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/templates/create',
        name: 'templates.create',
        component: () => import('@/views/template/templateform.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/templates/:id/edit',
        name: 'templates.edit',
        component: () => import('@/views/template/templateform.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/demography/:type',
        name: 'demographies.view',
        component: () => import('@/views/demography/index.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/demography/:type/create',
        name: 'demographies.create',
        component: () => import('@/views/demography/create.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/demography/:type/edit/:id',
        name: 'demographies.edit',
        component: () => import('@/views/demography/edit.vue'),
        props: true,
        meta: { requiresAuth: true }
    },
    {
        path: '/type/:type',
        name: 'types.view',
        component: () => import('@/views/types/index.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/type/:type/create',
        name: 'types.create',
        component: () => import('@/views/types/create.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/type/:type/edit/:id',
        name: 'types.edit',
        component: () => import('@/views/types/edit.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/required-documents',
        name: 'required-documents.view',
        component: () => import('@/views/requiredDocuments/index.vue'),
        meta: { requiresAuth: true }
    },

    {
        path: '/form/:id',
        name: 'admin-form',
        component: () => import('@/views/viewform/adminform.vue'),
        meta: { requiresAuth: true, skipPermissionCheck: true }
    },

    {
        path: '/certificates/:uuid',
        name: 'certificates-pdf',
        component: () => import('@/views/applications/certificates.vue'),
        meta: { requiresAuth: true, skipPermissionCheck: true }
    },

    {
        path: '/application-form/:id',
        name: 'view-form-domicile',
        component: () => import('@/views/viewform/domicile/viewform.vue'),
        meta: { requiresAuth: true, skipPermissionCheck: true }
    },
    {
        path: '/final-form-domicile/:id',
        name: 'final-form-domicile',
        component: () => import('@/views/viewform/domicile/finalform.vue'),
        meta: { requiresAuth: true, skipPermissionCheck: true }
    },
    {
        path: '/final-form-state/:id',
        name: 'final-form-state',
        component: () => import('@/views/viewform/state/final-form.vue'),
        meta: { requiresAuth: true, skipPermissionCheck: true }
    },
    {
        path: '/print-form-domicile/:id',
        name: 'print-form-domicile',
        component: () => import('@/views/viewform/domicile/print-application-form.vue'),
        meta: { requiresAuth: true, skipPermissionCheck: true }
    },
    {
        path: '/print-form-state/:id',
        name: 'print-form-state',
        component: () => import('@/views/viewform/state/print-application-form.vue'),
        meta: { requiresAuth: true, skipPermissionCheck: true }
    },
    {
        path: '/domicileviewformtesting',
        name: 'view-form-testing',
        component: () => import('@/views/viewform/testing-forms/domicile/viewform.vue'),
        meta: { requiresAuth: true, skipPermissionCheck: true }
    },
    {
        path: '/domicilefinalformtesting',
        name: 'final-form-testing',
        component: () => import('@/views/viewform/testing-forms/domicile/finalform.vue'),
        meta: { requiresAuth: true, skipPermissionCheck: true }
    },
    {
        path: '/domicileprintformtesting',
        name: 'print-form',
        component: () => import('@/views/viewform/testing-forms/domicile/printform.vue'),
        meta: { requiresAuth: true, skipPermissionCheck: true }
    },
    {
        path: '/statefinalformtesting',
        name: 'state-final-form-testing',
        component: () => import('@/views/viewform/testing-forms/state/final-form.vue'),
        meta: { requiresAuth: true, skipPermissionCheck: true }
    },
    {
        path: '/stateprintformtesting',
        name: 'state-print-form-testing',
        component: () => import('@/views/viewform/testing-forms/state/print-form.vue'),
        meta: { requiresAuth: true, skipPermissionCheck: true }
    },
    {
        path: '/additional-charges',
        name: 'additional-charges.view',
        component: () => import('@/views/additional-charges/index.vue'),
        meta: { requiresAuth: true, skipPermissionCheck: true }
    },
    {
        path: '/additional-charges/create',
        name: 'additional-charges.create',
        component: () => import('@/views/additional-charges/create.vue'),
        meta: { requiresAuth: true, skipPermissionCheck: true }
    },
    {
        path: '/additional-charges/edit/:id',
        name: 'additional-charges.edit',
        component: () => import('@/views/additional-charges/edit.vue'),
        meta: { requiresAuth: true, skipPermissionCheck: true }
    },
    {
        path: '/profile',
        name: 'profile.user',
        component: () => import('@/views/profile/index.vue'),
        meta: { requiresAuth: true, skipPermissionCheck: true }
    },
    {
        path: '/profile/edit',
        name: 'profile.user.edit',
        component: () => import('@/views/profile/edit.vue'),
        meta: { requiresAuth: true, skipPermissionCheck: true }
    },
    {
        path: '/change-password',
        name: 'change.password',
        component: () => import('@/views/profile/changepassword.vue'),
        meta: { requiresAuth: true, skipPermissionCheck: true }
    },
    {
        path: '/backups',
        name: 'backups.view',
        component: () => import('@/views/backup/index.vue'),
        meta: { requiresAuth: true, title: 'Backups' }
    },
    {
        path: '/schedule-jobs',
        name: 'schedule-jobs.view',
        component: () => import('@/views/schedulejobs/index.vue'),
        meta: { requiresAuth: true, title: 'Schedule jobs' }
    },
    {
        path: '/api-tokens',
        name: 'api-tokens.view',
        component: () => import('@/views/tokens.vue'),
        meta: { requiresAuth: true, title: 'API Tokens' }
    },
    {
        path: '/unauthorized-access',
        name: 'Unauthorized Access',
        component: () => import('@/views/pages/error403.vue'),
        meta: { requiresAuth: true, skipPermissionCheck: true }
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'not-found',
        component: () => import('@/views/pages/error404.vue'),
        meta: { requiresAuth: true, skipPermissionCheck: true }
    }
];
const router = createRouter({
    history: createWebHistory(),
    linkExactActiveClass: 'active',
    routes,
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition;
        } else {
            return { left: 0, top: 0 };
        }
    }
});

router.beforeEach((to, from, next) => {
    const store = useAppStore();
    const permissions = store?.user?.permissions;

    if (to?.meta?.layout == 'auth') {
        store.setMainLayout('auth');
    } else {
        store.setMainLayout('app');
    }

    // Check if route requires authentication
    if (to.meta.requiresAuth && !store.isAuthenticated) {
        next('/login');
        return;
    }

    if (to.meta.requiresAuth && store.isAuthenticated) {
        if (to.meta.skipPermissionCheck) {
            next();
            return;
        }
        const routeName = to.name as string;
        const hasPermission = permissions?.includes(routeName) || false;
        if (!hasPermission && to.name!=='drafted-applications.view') {
            next('/unauthorized-access');
            return;
        }
    }

    // If user is logged in and trying to access auth pages, redirect to dashboard
    if (to.meta.isAuthPage && store.isAuthenticated) {
        next('/');
        return;
    }

    next();
});
router.afterEach((to, from, next) => {
    appSetting.changeAnimation();
});
export default router;
