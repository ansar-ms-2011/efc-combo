<template>
    <div class="p-6 min-h-screen">
        <!-- breadcrumb -->
        <div class="text-sm text-gray-600 mb-4">
            <router-link to="/admin/dashboard" class="text-grey-600 hover:underline">
                Dashboard
            </router-link>

            <i class="fa fa-angle-right"></i>
            <router-link to="/admin/roles" class="text-gery-600 hover:underline">
                Role List
            </router-link>
            <i class="fa fa-angle-right"></i>
            <router-link to="/role/create" class="font-semibold text-blue-600 hover:underline">
                Edit Role
            </router-link>
        </div>
        <div class="max-w-7xl mx-auto bg-white rounded-lg shadow border p-6">


            <!-- heading -->
            <h2 class="text-xl  mb-6 text-black p-2 rounded font-semibold">
                Edit Role
            </h2>
            <!-- form -->
            <form class="space-y-6" @submit.prevent="handleSubmit">

                <!-- Role Name -->
                <div>
                    <label class="block text-sm font-medium mb-1">Role Name</label>
                    <input v-model="form.name" type="text" placeholder="Role Name" class="form-input" />
                </div>

                <!-- Permissions -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <label class="block text-sm font-medium">Permissions</label>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Enable All</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" :checked="isAllPermissionsEnabled()"
                                       @change="toggleAllPermissions"
                                       class="sr-only peer" />
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse border border-gray-300">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="border border-gray-300 px-4 py-3 text-left text-sm font-semibold text-gray-600">
                                    Module
                                </th>
                                <th class="border border-gray-300 px-4 py-3 text-center text-sm font-semibold text-gray-600">
                                    View
                                </th>
                                <th class="border border-gray-300 px-4 py-3 text-center text-sm font-semibold text-gray-600">
                                    Create
                                </th>
                                <th class="border border-gray-300 px-4 py-3 text-center text-sm font-semibold text-gray-600">
                                    Edit
                                </th>
                                <th class="border border-gray-300 px-4 py-3 text-center text-sm font-semibold text-gray-600">
                                    Delete
                                </th>
                                <th class="border border-gray-300 px-4 py-3 text-center text-sm font-semibold text-gray-600">
                                    All
                                </th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-300">
                            <tr v-for="module in modules" :key="module.name" class="hover:bg-gray-50">
                                <td class="border border-gray-300 px-4 py-3 font-medium text-gray-900 capitalize">
                                    {{ module.name }}
                                </td>
                                <td class="border border-gray-300 px-4 py-3 text-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" :checked="hasPermission(`${module.name}.view`)"
                                               @change="togglePermission(`${module.name}.view`)" class="sr-only peer" />
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                        </div>
                                    </label>
                                </td>
                                <td class="border border-gray-300 px-4 py-3 text-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" :checked="hasPermission(`${module.name}.create`)"
                                               @change="togglePermission(`${module.name}.create`)"
                                               class="sr-only peer" />
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                        </div>
                                    </label>
                                </td>
                                <td class="border border-gray-300 px-4 py-3 text-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" :checked="hasPermission(`${module.name}.edit`)"
                                               @change="togglePermission(`${module.name}.edit`)" class="sr-only peer" />
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                        </div>
                                    </label>
                                </td>
                                <td class="border border-gray-300 px-4 py-3 text-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" :checked="hasPermission(`${module.name}.delete`)"
                                               @change="togglePermission(`${module.name}.delete`)"
                                               class="sr-only peer" />
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                        </div>
                                    </label>
                                </td>
                                <td class="border border-gray-300 px-4 py-3 text-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" :checked="isModuleFullyEnabled(module.name)"
                                               @change="toggleModulePermissions(module.name)" class="sr-only peer" />
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                        </div>
                                    </label>
                                </td>
                            </tr>
                            <tr v-for="(perm, index) in additionalPermissions" :key="index">
                                <td class="border border-gray-300 px-4 py-3 font-medium text-gray-900 capitalize">
                                    {{ perm.name?.replace(".view", "")?.replaceAll("-", " ") }}
                                </td>
                                <td class="border border-gray-300 px-4 py-3 text-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" :checked="hasPermission(perm.name, 'view')"
                                               @change="togglePermission(perm.name)" class="sr-only peer" />
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                        </div>
                                    </label>
                                </td>
                                <td colspan="4" class="border border-gray-300 px-4 py-3 font-medium text-gray-900 capitalize">
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- buttons -->
                <div class="flex justify-end gap-3 mt-6">
                    <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded shadow">
                        Update
                    </button>
                    <button type="button" @click="resetForm"
                            class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded shadow">
                        Reset
                    </button>
                    <router-link to="/admin/roles"
                                 class="px-6 py-2 bg-red-100 hover:bg-red-500 text-red-600 hover:text-white rounded shadow">
                        Cancel
                    </router-link>
                </div>
            </form>
        </div>
    </div>
</template>


<script setup>
    import { ref, onMounted } from 'vue';
    import axios from 'axios';
    import { useRoute } from 'vue-router';
    import Swal from 'sweetalert2';
    import { useRouter } from 'vue-router';


    const form = ref({
        name: '',
        permissions: []
    });

    const modules = ref([]);
    const additionalPermissions = ref([]);
    const route = useRoute();
    const router = useRouter();


    const fetchModules = async () => {
        try {
            const response = await axios.get('/api/modules');
            modules.value = response.data?.modules || [];
            if (response.data?.additionalPermissions) {
            additionalPermissions.value = response.data.additionalPermissions.filter(perm => {
                return !perm.name.startsWith('archived-');
            });
        }            // add custom modules
           modules.value.push({
            name: 'archived-dashboard'
        });
             modules.value.push({
            name: 'archived-scanner'
        });

        modules.value.push({
            name: 'archived-verification'
        });

        } catch (error) {
            console.error('Error fetching modules:', error);
        }
    };

    const fetchRole = async () => {
        try {
            const response = await axios.get(`/api/roles/${route.params.id}`);

            const role = response.data.role;

            // Spatie returns permissions as objects
            const assignedPermissions = role.permissions.map(p => p.name);

            form.value.name = role.name;
            form.value.permissions = assignedPermissions;

        } catch (error) {
            console.error('Error fetching role:', error);
        }
    };


    const hasPermission = (permission) => {
        return form.value.permissions.includes(permission);
    };

    const togglePermission = (permission) => {
        const index = form.value.permissions.indexOf(permission);
        if (index > -1) {
            form.value.permissions.splice(index, 1);
        } else {
            form.value.permissions.push(permission);
        }
    };

    const isModuleFullyEnabled = (moduleName) => {
        const perms = ['view', 'create', 'edit', 'delete'];
        return perms.every(perm => form.value.permissions.includes(`${moduleName.toLowerCase()}.${perm}`));
    };

    const toggleModulePermissions = (moduleName) => {
        const allEnabled = isModuleFullyEnabled(moduleName);
        const perms = ['view', 'create', 'edit', 'delete'];
        if (allEnabled) {
            // Remove all permissions for this module
            perms.forEach(perm => {
                const permString = `${moduleName.toLowerCase()}.${perm}`;
                const index = form.value.permissions.indexOf(permString);
                if (index > -1) {
                    form.value.permissions.splice(index, 1);
                }
            });
        } else {
            // Add all permissions for this module
            perms.forEach(perm => {
                const permString = `${moduleName.toLowerCase()}.${perm}`;
                if (!form.value.permissions.includes(permString)) {
                    form.value.permissions.push(permString);
                }
            });
        }
    };

    const toggleAllPermissions = () => {
        const allEnabled = isAllPermissionsEnabled();
        if (allEnabled) {
            form.value.permissions = [];
        } else {
            const allPerms = [];
            modules.value.forEach(module => {
                const moduleName = module.name.toLowerCase();
                allPerms.push(`${moduleName}.view`, `${moduleName}.create`, `${moduleName}.edit`, `${moduleName}.delete`);
            });
            form.value.permissions = allPerms;
        }
    };

    const isAllPermissionsEnabled = () => {
        return modules.value.every(module =>
            isModuleFullyEnabled(module.name.toLowerCase())
        );
    };

    const handleSubmit = async () => {
        try {
            const response = await axios.put(`/api/roles/${route.params.id}`, form.value);
            console.log('Role updated successfully:', response.data);
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Role saved successfully!',
                padding: '2em',
                customClass: 'sweet-alerts'
            }).then(() => router.push('/admin/roles'));
        } catch (error) {
            console.error('Error updating role:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Failed to update role.',
                padding: '2em',
                customClass: 'sweet-alerts'
            });
        }
    };

    onMounted(() => {
        fetchModules();
        fetchRole();
    });
</script>
