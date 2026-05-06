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
                Add Role
            </router-link>
        </div>
        <div class="max-w-7xl mx-auto bg-white rounded-lg shadow border p-6">


            <!-- heading -->
            <h2 class="text-xl  mb-6 text-black p-2 rounded font-semibold">
                Add Role
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
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" :checked="isAllPermissionsEnabled()" @change="toggleAllPermissions"
                                   class="sr-only peer" />
                            <div
                                class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                            </div>
                            <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Enable All</span>
                        </label>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse border border-gray-300">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="border px-4 py-3 text-left">Module</th>
                                <th class="border px-4 py-3 text-center">View</th>
                                <th class="border px-4 py-3 text-center">Create</th>
                                <th class="border px-4 py-3 text-center">Edit</th>
                                <th class="border px-4 py-3 text-center">Delete</th>
                                <th class="border px-4 py-3 text-center">All</th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr v-for="module in modules" :key="module.name">
                                <td class="border px-4 py-3 font-medium">
                                    {{ module.name }}
                                </td>

                                <!-- VIEW -->
                                <td class="border px-4 py-3 text-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox"
                                               :checked="hasPermission(module.name.toLowerCase(), 'view')"
                                               @change="togglePermission(`${module.name.toLowerCase()}.view`)"
                                               class="sr-only peer" />
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                        </div>
                                    </label>
                                </td>

                                <!-- CREATE -->
                                <td class="border px-4 py-3 text-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox"
                                               :checked="hasPermission(module.name.toLowerCase(), 'create')"
                                               @change="togglePermission(`${module.name.toLowerCase()}.create`)"
                                               class="sr-only peer" />
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                        </div>
                                    </label>
                                </td>

                                <!-- EDIT -->
                                <td class="border px-4 py-3 text-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox"
                                               :checked="hasPermission(module.name.toLowerCase(), 'edit')"
                                               @change="togglePermission(`${module.name.toLowerCase()}.edit`)"
                                               class="sr-only peer" />
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                        </div>
                                    </label>
                                </td>

                                <!-- DELETE -->
                                <td class="border px-4 py-3 text-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox"
                                               :checked="hasPermission(module.name.toLowerCase(), 'delete')"
                                               @change="togglePermission(`${module.name.toLowerCase()}.delete`)"
                                               class="sr-only peer" />
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                        </div>
                                    </label>
                                </td>

                                <!-- ALL -->
                                <td class="border px-4 py-3 text-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox"
                                               :checked="isModuleFullyEnabled(module.name.toLowerCase())"
                                               @change="toggleModulePermissions(module.name.toLowerCase())"
                                               class="sr-only peer" />
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                        </div>
                                    </label>
                                </td>
                            </tr>
                            <!--   Additional Permissions for controlling side menu-->
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

                <div class="flex justify-end  gap-3 mt-6">
                    <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded shadow">
                        Save
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
    import { onMounted, ref } from 'vue';
    import axios from 'axios';
    import Swal from 'sweetalert2';
    import { useRouter } from 'vue-router'; // Better for navigation than window.location

    const router = useRouter();
    const modules = ref([]);
    const additionalPermissions = ref([]);
    
    const fetchModules = async () => {
        try {
            const response = await axios.get('/api/modules');
            modules.value = response.data;
            modules.value = response.data?.modules;
        if (response.data?.additionalPermissions) {
            additionalPermissions.value = response.data.additionalPermissions.filter(perm => {
                return !perm.name.startsWith('archived-');
            });
        }  
        
    

            console.log('Modules fetched:', modules.value);
        } catch (error) {
            console.error('Error fetching modules:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to load modules',
                padding: '2em',
                customClass: 'sweet-alerts'
            });
        }
    };


    // 1. Define all fields for the Role
    const form = ref({
        name: '',
        permissions: []
    });

    const hasPermission = (module, action) => {
        return form.value.permissions.includes(`${module}.${action}`);
    };

    const togglePermission = (permission) => {
        const index = form.value.permissions.indexOf(permission);
        if (index === -1) {
            form.value.permissions.push(permission);
        } else {
            form.value.permissions.splice(index, 1);
        }
    };

    const isModuleFullyEnabled = (module) => {
        return ['view', 'create', 'edit', 'delete'].every(action =>
            hasPermission(module, action)
        );
    };

    const isAllPermissionsEnabled = () => {
        return modules.value.every(module =>
            isModuleFullyEnabled(module.name.toLowerCase())
        );
    };

    const toggleModulePermissions = (module) => {
        const allEnabled = isModuleFullyEnabled(module)

        ;['view', 'create', 'edit', 'delete'].forEach(action => {
            const permission = `${module}.${action}`;
            const index = form.value.permissions.indexOf(permission);

            if (allEnabled && index !== -1) {
                form.value.permissions.splice(index, 1);
            }

            if (!allEnabled && index === -1) {
                form.value.permissions.push(permission);
            }
        });
    };

    const toggleAllPermissions = () => {
        const allModules = modules.value.map(m => m.name.toLowerCase());
        const allEnabled = allModules.every(module => isModuleFullyEnabled(module));

        allModules.forEach(module => {
            ['view', 'create', 'edit', 'delete'].forEach(action => {
                const permission = `${module}.${action}`;
                const index = form.value.permissions.indexOf(permission);

                if (allEnabled && index !== -1) {
                    form.value.permissions.splice(index, 1);
                }

                if (!allEnabled && index === -1) {
                    form.value.permissions.push(permission);
                }
            });
        });
    };


    const handleSubmit = async () => {
        try {
            console.log('Submitting form', form.value);

            const response = await axios.post('/api/roles', form.value);
            console.log('form data sent:', form.value);

            console.log('Role saved successfully:', response.data);

            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Role saved successfully!',
                padding: '2em',
                customClass: 'sweet-alerts'
            }).then(() => router.push('/admin/roles'));

            resetForm();
        } catch (error) {
            console.error('Error saving role:', error);
            const errorMsg = error.response?.data?.message || 'Failed to save role data';
            Swal.fire({ icon: 'error', title: 'Failed!', text: errorMsg, padding: '2em', customClass: 'sweet-alerts' });
        }
    };

    const resetForm = () => {
        form.value = {
            name: '',
            permissions: []
        };
    };

    onMounted(() => {
        fetchModules();
    });
</script>
