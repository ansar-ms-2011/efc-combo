import {createRouter, createWebHistory, RouteRecordRaw} from 'vue-router';
import {useAppStore} from '@/stores';
import appSetting from '@/app-setting';

const routes: RouteRecordRaw[] = [
    {
        path: '/app/login',
        name: 'login',
        component: () => import('@/views/login.vue'),
        meta: {layout: 'auth', requiresAuth: false, isAuthPage: true}
    },
    {
        path: '/app/enable-two-factor',
        name: 'enable-two-factir',
        component: () => import('@/views/two-factor.vue'),
        meta: {requiresAuth: true, skipPermissionCheck: true}
    },
    {
        path: '/app/quick-links',
        name: 'Quick Links',
        component: () => import('@/views/dashboard/quick-links.vue'),
        meta: {requiresAuth: true, skipPermissionCheck: true}
    },
    {
        path: '/app/',
        name: 'dashboard.view',
        component: () => import('@/views/dashboard/index.vue'),
        meta: {requiresAuth: true, skipPermissionCheck: true}
    },
    {
        path: '/app/online-applications',
        name: 'online-applications.view',
        component: () => import('@/views/applications/index.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/app/applications/:status?',
        name: 'applications.view',
        component: () => import('@/views/applications/index.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/app/drafted-applications',
        name: 'drafted-applications.view',
        component: () => import('@/views/applications/draftedApplications.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/app/applications/create',
        name: 'applications.create',
        component: () => import('@/views/applications/create.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/app/applications/edit/:uuid',
        name: 'applications.edit',
        component: () => import('@/views/applications/edit.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/app/applications/edit-draft/:draftId',
        name: 'applications.edit-draft',
        component: () => import('@/views/applications/edit-draft.vue'),
        meta: {requiresAuth: true, skipPermissionCheck: true}
    },
    {
        path: '/app/applications/forward/:id',
        name: 'applications.forward',
        component: () => import('@/views/applications/forward.vue'),
        meta: {requiresAuth: true, skipPermissionCheck: true}
    },
    {
        path: '/app/archived/scanning-form/all',
        name: 'archived.scanning-form.all',
        component: () => import('@/views/archived/index.vue'),
        meta: {requiresAuth: true, skipPermissionCheck: true}
    },
    {
        path: '/app/archived/verification-form/all',
        name: 'archived.verification-form.all',
        component: () => import('@/views/archived/indexVerfication.vue'),
        meta: {requiresAuth: true, skipPermissionCheck: true}
    },
    {
        path: '/app/archivedReport',
        name: 'archived.report',
        component: () => import('@/views/archived/archivedReport.vue'),
        meta: {
            requiresAuth: true,
            skipPermissionCheck: true,
            title: 'Archived Documents Report'
        }
    },
    {
        path: '/app/center',
        name: 'centers.view',
        component: () => import('@/views/center/index.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/app/center/create',
        name: 'centers.create',
        component: () => import('@/views/center/create.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/app/center/edit/:id',
        name: 'centers.edit',
        component: () => import('@/views/center/edit.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/app/center/delete/:id',
        name: 'centers.delete',
        component: () => import('@/views/center/index.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/app/admin/roles',
        name: 'roles.view',
        component: () => import('@/views/roles/index.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/app/role/create',
        name: 'roles.create',
        component: () => import('@/views/roles/create.vue'),
        meta: {requiresAuth: true}
    },

    {
        path: '/app/role/edit/:id',
        name: 'roles.edit',
        component: () => import('@/views/roles/edit.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/app/services-center',
        name: 'services-centers.view',
        component: () => import('@/views/servicescenter/index.vue'),
        meta: {requiresAuth: true, title: 'Services Center'}
    },
    {
        path: '/app/services-center/create',
        name: 'services-centers.create',
        component: () => import('@/views/servicescenter/create.vue'),
        meta: {requiresAuth: true, title: 'Create Services Center'}
    },
    {
        path: '/app/services-center/edit/:id',
        name: 'services-centers.edit',
        component: () => import('@/views/servicescenter/edit.vue'),
        meta: {requiresAuth: true, title: 'Edit Services Center'}
    },

    {
        path: '/app/services',
        name: 'services.view',
        component: () => import('@/views/services/index.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/app/services/create',
        name: 'services.create',
        component: () => import('@/views/services/create.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/app/services/edit/:id',
        name: 'services.edit',
        component: () => import('@/views/services/edit.vue')
    },
    {
        path: '/app/users',
        name: 'users.view',
        component: () => import('@/views/user/index.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/app/user/create',
        name: 'users.create',
        component: () => import('@/views/user/create.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/app/user/edit/:id',
        name: 'users.edit',
        component: () => import('@/views/user/edit.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/app/departments',
        name: 'departments.view',
        component: () => import('@/views/department/index.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/app/department/create',
        name: 'departments.create',
        component: () => import('@/views/department/create.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/app/department/edit/:id',
        name: 'departments.edit',
        component: () => import('@/views/department/edit.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/app/department/delete/:id',
        name: 'departments.delete',
        component: () => import('@/views/department/index.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/app/service-instruction',
        name: 'service-instructions.view',
        component: () => import('@/views/serviceInstruction/index.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/app/service-instruction/create',
        name: 'service-instructions.create',
        component: () => import('@/views/serviceInstruction/create.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/app/service-instruction/edit/:id',
        name: 'service-instructions.edit',
        component: () => import('@/views/serviceInstruction/edit.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/app/templates',
        name: 'templates.view',
        component: () => import('@/views/template/index.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/app/templates/create',
        name: 'templates.create',
        component: () => import('@/views/template/templateform.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/app/templates/:id/edit',
        name: 'templates.edit',
        component: () => import('@/views/template/templateform.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/app/demography/:type',
        name: 'demographies.view',
        component: () => import('@/views/demography/index.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/app/demography/:type/create',
        name: 'demographies.create',
        component: () => import('@/views/demography/create.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/app/demography/:type/edit/:id',
        name: 'demographies.edit',
        component: () => import('@/views/demography/edit.vue'),
        props: true,
        meta: {requiresAuth: true}
    },
    {
        path: '/app/type/:type',
        name: 'types.view',
        component: () => import('@/views/types/index.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/app/type/:type/create',
        name: 'types.create',
        component: () => import('@/views/types/create.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/app/type/:type/edit/:id',
        name: 'types.edit',
        component: () => import('@/views/types/edit.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/app/required-documents',
        name: 'required-documents.view',
        component: () => import('@/views/requiredDocuments/index.vue'),
        meta: {requiresAuth: true}
    },

    {
        path: '/app/form/:id',
        name: 'admin-form',
        component: () => import('@/views/viewform/adminform.vue'),
        meta: {requiresAuth: true, skipPermissionCheck: true}
    },

    {
        path: '/app/certificates/:uuid',
        name: 'certificates-pdf',
        component: () => import('@/views/applications/certificates.vue'),
        meta: {requiresAuth: true, skipPermissionCheck: true}
    },

    {
        path: '/app/application-form/:id',
        name: 'view-form-domicile',
        component: () => import('@/views/viewform/domicile/viewform.vue'),
        meta: {requiresAuth: true, skipPermissionCheck: true}
    },
    {
        path: '/app/final-form-domicile/:id',
        name: 'final-form-domicile',
        component: () => import('@/views/viewform/domicile/finalform.vue'),
        meta: {requiresAuth: true, skipPermissionCheck: true}
    },
    {
        path: '/app/final-form-state/:id',
        name: 'final-form-state',
        component: () => import('@/views/viewform/state/final-form.vue'),
        meta: {requiresAuth: true, skipPermissionCheck: true}
    },
    {
        path: '/app/print-form-domicile/:id',
        name: 'print-form-domicile',
        component: () => import('@/views/viewform/domicile/print-application-form.vue'),
        meta: {requiresAuth: true, skipPermissionCheck: true}
    },
    {
        path: '/app/print-form-state/:id',
        name: 'print-form-state',
        component: () => import('@/views/viewform/state/print-application-form.vue'),
        meta: {requiresAuth: true, skipPermissionCheck: true}
    },
    {
        path: '/app/domicileviewformtesting',
        name: 'view-form-testing',
        component: () => import('@/views/viewform/testing-forms/domicile/viewform.vue'),
        meta: {requiresAuth: true, skipPermissionCheck: true}
    },
    {
        path: '/app/domicilefinalformtesting',
        name: 'final-form-testing',
        component: () => import('@/views/viewform/testing-forms/domicile/finalform.vue'),
        meta: {requiresAuth: true, skipPermissionCheck: true}
    },
    {
        path: '/app/domicileprintformtesting',
        name: 'print-form',
        component: () => import('@/views/viewform/testing-forms/domicile/printform.vue'),
        meta: {requiresAuth: true, skipPermissionCheck: true}
    },
    {
        path: '/app/statefinalformtesting',
        name: 'state-final-form-testing',
        component: () => import('@/views/viewform/testing-forms/state/final-form.vue'),
        meta: {requiresAuth: true, skipPermissionCheck: true}
    },
    {
        path: '/app/stateprintformtesting',
        name: 'state-print-form-testing',
        component: () => import('@/views/viewform/testing-forms/state/print-form.vue'),
        meta: {requiresAuth: true, skipPermissionCheck: true}
    },
    {
        path: '/app/additional-charges',
        name: 'additional-charges.view',
        component: () => import('@/views/additional-charges/index.vue'),
        meta: {requiresAuth: true, skipPermissionCheck: true}
    },
    {
        path: '/app/additional-charges/create',
        name: 'additional-charges.create',
        component: () => import('@/views/additional-charges/create.vue'),
        meta: {requiresAuth: true, skipPermissionCheck: true}
    },
    {
        path: '/app/additional-charges/edit/:id',
        name: 'additional-charges.edit',
        component: () => import('@/views/additional-charges/edit.vue'),
        meta: {requiresAuth: true, skipPermissionCheck: true}
    },
    {
        path: '/app/profile',
        name: 'profile.user',
        component: () => import('@/views/profile/index.vue'),
        meta: {requiresAuth: true, skipPermissionCheck: true}
    },
    {
        path: '/app/profile/edit',
        name: 'profile.user.edit',
        component: () => import('@/views/profile/edit.vue'),
        meta: {requiresAuth: true, skipPermissionCheck: true}
    },
    {
        path: '/app/change-password',
        name: 'change.password',
        component: () => import('@/views/profile/changepassword.vue'),
        meta: {requiresAuth: true, skipPermissionCheck: true}
    },
    {
        path: '/app/backups',
        name: 'backups.view',
        component: () => import('@/views/backup/index.vue'),
        meta: {requiresAuth: true, title: 'Backups'}
    },
    {
        path: '/app/schedule-jobs',
        name: 'schedule-jobs.view',
        component: () => import('@/views/schedulejobs/index.vue'),
        meta: {requiresAuth: true, title: 'Schedule jobs'}
    },
    {
        path: '/app/api-tokens',
        name: 'api-tokens.view',
        component: () => import('@/views/tokens.vue'),
        meta: {requiresAuth: true, title: 'API Tokens'}
    },
    {
        path: '/app/unauthorized-access',
        name: 'Unauthorized Access',
        component: () => import('@/views/pages/error403.vue'),
        meta: {requiresAuth: true, skipPermissionCheck: true}
    },
    {
        path: '/app/:pathMatch(.*)*',
        name: 'not-found',
        component: () => import('@/views/pages/error404.vue'),
        meta: {requiresAuth: true, skipPermissionCheck: true}
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
            return {left: 0, top: 0};
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
        next('/app/login');
        return;
    }

    if (to.meta.requiresAuth && store.isAuthenticated) {
        if (to.meta.skipPermissionCheck) {
            next();
            return;
        }
        const routeName = to.name as string;
        const hasPermission = permissions?.includes(routeName) || false;
        if (!hasPermission && to.name !== 'drafted-applications.view') {
            next('/app/unauthorized-access');
            return;
        }
    }

    // If user is logged in and trying to access auth pages, redirect to dashboard
    if (to.meta.isAuthPage && store.isAuthenticated) {
        next('/app/');
        return;
    }

    next();
});
router.afterEach((to, from, next) => {
    appSetting.changeAnimation();
});
export default router;
