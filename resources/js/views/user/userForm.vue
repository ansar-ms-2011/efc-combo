<template>
    <div class="max-w-7xl mx-auto bg-white rounded-lg shadow border p-6">
        <!-- Heading -->
        <h2 class="text-xl  mb-6 text-black p-2 rounded font-semibold">
            {{ form.id ? 'Edit User' : 'Create User' }}
        </h2>

        <!-- Form -->
        <form class="space-y-6" @submit.prevent="handleSubmit">

            <!-- ROW 1 : 4 Columns -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Pre</label>

                    <multiselect v-model="form.prefix" :options="prefixes"
                                 placeholder="Select Prefix" :multiple="false" :searchable="true"
                                 :clear-on-select="true"
                                 :close-on-select="true" :show-labels="false" >
                    </multiselect>
                    <p v-if="errors.prefix" class="text-red-500 text-xs mt-1">
                        {{ errors.prefix[0] }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">First Name <span
                        class="text-red-500">*</span></label>
                    <input type="text" v-model="form.first_name" placeholder="First Name" class="form-input" />
                    <p v-if="errors.first_name" class="text-red-500 text-xs mt-1">
                        {{ errors.first_name[0] }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Last Name <span
                        class="text-red-500">*</span></label>
                    <input type="text" v-model="form.last_name" placeholder="Last Name" class="form-input" />
                    <p v-if="errors.last_name" class="text-red-500 text-xs mt-1">
                        {{ errors.last_name[0] }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" v-model="form.email" placeholder="Email" class="form-input" />
                    <p v-if="errors.email" class="text-red-500 text-xs mt-1">
                        {{ errors.email[0] }}
                    </p>
                </div>
            </div>

            <!-- ROW 2 : 4 Columns -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">CNIC </label>
                    <input type="text" v-model="form.cnic" class="form-input" placeholder="12345-1234567-1"
                           maxlength="15"
                           @input="handleCnicFormating" />
                    <p v-if="errors.cnic" class="text-red-500 text-xs mt-1">
                        {{ errors.cnic[0] }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Phone No </label>
                    <input type="text" v-model="form.phone_no" class="form-input" placeholder="921234567"
                           maxlength="9" />
                    <p v-if="errors.phone_no" class="text-red-500 text-xs mt-1">
                        {{ errors.phone_no[0] }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Address </label>
                    <input type="text" v-model="form.address" class="form-input" placeholder="Enter Address"
                           maxlength="35" />
                    <p v-if="errors.address" class="text-red-500 text-xs mt-1">
                        {{ errors.address[0] }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1" for="is_active">Is-Active <span
                        class="text-red-500">*</span></label>
                    <input type="checkbox" v-model="form.is_active" class="form-checkbox mt-[7px]" id="is_active" />
                    <p v-if="errors.is_active" class="text-red-500 text-xs mt-">
                        {{ errors.is_active[0] }}
                    </p>
                </div>
            </div>
            <hr>
            <!-- ROW 3 : 1 Column -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Designation / Role <span
                        class="text-red-500">*</span></label>

                    <multiselect v-model="form.role" :options="roles || filteredRoles" track-by="id" label="name"
                                 placeholder="Select Designation / Role" :multiple="false" :searchable="true"
                                 :clear-on-select="true" :disabled="isRoleDisabled"
                                 :close-on-select="true" :show-labels="false" >
                    </multiselect>
                    <p v-if="errors.role_id" class="text-red-500 text-xs mt-1">
                        {{ errors.role_id[0] }}
                    </p>
                </div>
                <div v-if="role_name==='Commissioner'">
                    <label class="block text-sm font-medium mb-1">Region Name <span
                        class="text-red-500">*</span></label>

                    <multiselect v-model="form.region" :options="regions" track-by="id" label="name"
                                 placeholder="Select Region Name" :multiple="false" :searchable="true"
                                 :clear-on-select="true" @update:model-value="handleRegionChanged(form.region)"
                                 :close-on-select="true" :show-labels="false" >
                    </multiselect>
                    <p v-if="errors.region_id" class="text-red-500 text-xs mt-1">
                        {{ errors.region_id[0] }}
                    </p>
                </div>
                <div
                    v-if="role_name==='DC' || role_name==='AC' || role_name==='ACR' || role_name==='DEO' || role_name==='Center In-charge' || role_name==='Patwari' || role_name==='Scanner' || role_name==='Supervisor'">
                    <label class="block text-sm font-medium mb-1">District Name <span
                        class="text-red-500">*</span></label>

                    <multiselect v-model="form.district" :options="districts" :disabled="isCI" track-by="id"
                                 label="name"
                                 placeholder="Select District" :multiple="false" :searchable="true"
                                 :clear-on-select="true" @update:model-value="handleDistrictChanged"
                                 :close-on-select="true" :show-labels="false" >
                    </multiselect>
                    <p v-if="errors.district_id" class="text-red-500 text-xs mt-1">
                        {{ errors.district_id[0] }}
                    </p>
                </div>
                <div
                    v-if="role_name==='AC' || role_name==='ACR' || role_name==='DEO' || role_name==='Center In-charge' || role_name==='Patwari'">
                    <label class="block text-sm font-medium mb-1">Tehsil Name <span
                        class="text-red-500">*</span></label>

                    <multiselect v-model="form.tehsil" :options="districtTehsils" :disabled="isCI || !form.district"
                                 track-by="id" label="name"
                                 placeholder="Select Tehsil" :multiple="false" :searchable="true"
                                 :clear-on-select="true" @update:model-value="handleTehsilChanged"
                                 :close-on-select="true" :show-labels="false" >
                    </multiselect>
                    <p v-if="errors.tehsil_id" class="text-red-500 text-xs mt-1">
                        {{ errors.tehsil_id[0] }}
                    </p>
                </div>
                 <div
                    v-if="role_name==='Patwari'">
                    <label class="block text-sm font-medium mb-1">City Name <span
                        class="text-red-500">*</span></label>

                    <multiselect v-model="form.city" :options="tehsilcity " 
                                 track-by="id" label="name"
                                 placeholder="Select City" :multiple="false" :searchable="true"
                                 :clear-on-select="true" @update:model-value="handleCityChanged"
                                 :close-on-select="true" :show-labels="false" >
                    </multiselect>
                    <p v-if="errors.city_id" class="text-red-500 text-xs mt-1">
                        {{ errors.city_id[0] }}
                    </p>
                </div>
                <div v-if="role_name==='DEO' || role_name==='Center In-charge'">
                    <label class="block text-sm font-medium mb-1">Center <span class="text-red-500">*</span></label>

                    <multiselect v-model="form.center" :options="filteredCenters" :disabled="isCI || !form.tehsil"
                                 track-by="id" label="name"
                                 placeholder="Select Center" :multiple="false" :searchable="true"
                                 :clear-on-select="true" @update:model-value="handleCenterChanged"
                                 :close-on-select="true" :show-labels="false" >
                    </multiselect>
                    <p v-if="errors.center_id" class="text-red-500 text-xs mt-1">
                        {{ errors.center_id[0] }}
                    </p>
                </div>
                <div v-if="role_name==='DEO' || role_name==='Center In-charge'" class="col-span-2">
                    <label class="block text-sm font-medium mb-1">Assigned service(s) available on Center <span class="text-red-500">*</span></label>

                    <multiselect v-model="form.services" :options="filteredServices" :disabled="isCI || !form.center"
                                 track-by="id" label="name"
                                 placeholder="Select Service" :multiple="true" :searchable="true"
                                 :clear-on-select="true"
                                 :close-on-select="true" :show-labels="false" >
                    </multiselect>
                    <p v-if="errors.service_id" class="text-red-500 text-xs mt-1">
                        {{ errors.service_id[0] }}
                    </p>
                </div>
                <template v-if="role_name==='DC' || role_name==='AC' || role_name==='ACR'">
                    <div>
                        <label class="block text-sm font-medium mb-1">Signature specimen <span
                            class="text-red-500">*</span></label>
                        <input type="file"
                               accept="image/*"
                               class="form-input"
                               @change="handleSignFile" />
                        <p v-if="errors.sign_file" class="text-red-500 text-xs mt-1">
                            {{ errors.sign_file[0] }}
                        </p>
                    </div>
                    <div v-if="form.sign_url" class="flex flex-col justify-center align-middle items-center">
                        <img
                            :src="form.sign_url"
                            alt="sign_file" style="max-width:150px"
                            class="border border-gray-300 rounded-lg shadow-sm"
                        />
                        <span class="text-xs text-muted pt-1">Signature Preview</span>
                    </div>
                </template>
            </div>
            <hr>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Password <span
                        class="text-red-500">*</span></label>
                    <input type="password" v-model="form.password" class="form-input" />
                    <p v-if="errors.password" class="text-red-500 text-xs mt-1">
                        {{ errors.password[0] }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Confirm Password <span
                        class="text-red-500">*</span></label>
                    <input type="password" v-model="form.password_confirmation" class="form-input" />
                    <p v-if="errors.confirm_password" class="text-red-500 text-xs mt-1">
                        {{ errors.confirm_password[0] }}
                    </p>
                </div>
            </div>
            <hr>
            <!-- Buttons -->
            <div class="flex justify-end gap-3 mt-6">
                <button :disabled="isSubmitting" type="submit"
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded shadow">
                    Update
                </button>
                <button type="button" @click="resetForm"
                        class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded shadow">
                    Reset
                </button>

                <router-link to="/users"
                             class="px-6 py-2 bg-red-100 hover:bg-red-500 text-red-600 hover:text-white rounded shadow">
                    Cancel
                </router-link>
            </div>
        </form>
    </div>
</template>

<script setup>
    import { computed, onMounted, ref } from 'vue';
    import axios from 'axios';
    import router from '@/router';
    import Swal from 'sweetalert2';
    import { useRoute } from 'vue-router';
    import { formatCnic } from '@/mixin/index.ts';
    import { useAppStore } from '@/stores/index.ts';


    const selectedPrefix = ref(null);

    const regions = ref([]);
    const districts = ref([]);
    const tehsils = ref([]);
    const roles = ref([]);
    const city = ref([]);
    const centers = ref([]);
    const isSubmitting = ref(false);
    const route = useRoute();
    const userId = route.params.id;
    const errors = ref({});

    const form = ref({
        id: null,
        prefix: '',
        first_name: '',
        last_name: '',
        email: '',
        cnic: '',
        phone_no: '',
        address: '',
        password: '',
        password_confirmation: '',
        center_id: null,
        region_id: null,
        district_id: null,
        tehsil_id: null,
        role: null,
        tehsil: null,
        district: null,
        region: null,
        center: null,
        sign_file: null,
        sign_url: null,
        is_active: false,
        services: [],
        city: null,        
    });

    // Prefix options
    const prefixes = ref([
        'Mr',
        'Mrs',
        'Miss'
    ]);

    const role_name = computed(() => {
        return form.value?.role?.name;
    });

    const districtTehsils = computed(() => {
        return tehsils.value.filter((value) => value.parent_id === form.value.district?.id);
    });

    const filteredCenters = computed(() => {
        if (!form.value.tehsil?.id) return [];
        return centers.value.filter(center =>
            Number(center.tehsil_id) === Number(form.value.tehsil.id)
        );
    });

//    const tehsilcity = computed(() => {
//     console.log('role:', role_name.value);
//     console.log('tehsil:', form.value.tehsil);
//     console.log('cities:', form.value.tehsil?.cities);

//     return form.value.tehsil?.cities ?? [];
// });



//     const tehsilcity = computed(() => {
//     if (!form.value.tehsil?.id) return [];
//     console.log('role:', role_name.value);
//     console.log('tehsil:', form.value.tehsil);
//     console.log('cities:', form.value.tehsil?.cities);

//     const selectedTehsil = tehsils.value.find(
//         tehsil => Number(tehsil.id) === Number(form.value.tehsil.id)
//     );

//     return selectedTehsil?.cities || [];
// });

const tehsilcity = computed(() => {
    if (!form.value.tehsil?.id) return [];

    return city.value.filter(c => 
        Number(c.parent_id) === Number(form.value.tehsil.id)
    );
});

     const handleCityChanged = (value) => {
        form.value.city = value;
    };

    const handleTehsilChanged = (value) => {
        form.value.center = null;
        form.value.city = null;

    };

    const handleDistrictChanged = (value) => {
        form.value.tehsil = null;
        form.value.city = null;
    };

    const handleRegionChanged = (value) => {
        form.value.region = value;
    };

    const handleCenterChanged = (value) => {
        form.value.center = value;
        form.value.services = []; // reset services when center changes
    };
    const serviceCenters = ref([]);

    const handleSignFile = (event) => {
        console.log(event.target?.files[0]);
        const file = event.target.files[0];

        if (!file) return;

        // Ensure single file
        if (event.target.files.length > 1) {
            event.target.value = '';
            Swal.fire('Only one image is allowed.');
            return;
        }

        // Validate file type
        if (!file.type.startsWith('image/')) {
            event.target.value = '';
            Swal.fire('Only image files are allowed.');
            return;
        }

        // Optional: Validate size (e.g., max 2MB)
        if (file.size > 3 * 1024 * 1024) {
            event.target.value = '';
            Swal.fire('Image must be less than 3MB.');
            return;
        }

        form.value.sign_file = file;
    };

    // Fetch roles
    const fetchDropdownData = async () => {
        try {
            const response = await axios.get('/api/get-users-dropdown-data');
            regions.value = response.data.regions;
            districts.value = response.data.districts;
            tehsils.value = response.data.tehsils;
            city.value = response.data.cities;
            roles.value = response.data.roles;
            centers.value = response.data.centers;
            serviceCenters.value = response.data.service_centers;
            console.log(response);
        } catch (error) {
            console.error('Error fetching roles:', error);
        }
    };


    const fetchUser = async () => {
        try {
            const response = await axios.get(`/api/users/${userId}`);
            const user = response.data.data;
            console.log('user-rec', user);
            form.value.prefix = user.prefix ? user.prefix : '';
            form.value.first_name = user.first_name;
            form.value.last_name = user.last_name;
            form.value.id = user.id;
            form.value.email = user.email;
            form.value.cnic = user.employee?.cnic ?? '';  // <-- use employee relation
            form.value.phone_no = user.employee?.phone_no ?? '';  // <-- use employee relation
            form.value.address = user.employee?.address ?? '';  // <-- use employee relation
            form.value.center_id = centers.value.find(c => c.id === user.center_id) || null;
            form.value.region = user.region ?? null;
            form.value.district = user.district ?? null;
            form.value.tehsil = user.tehsil ?? null;
            form.value.center = user.center ?? null;
            form.value.role = user.current_role ?? null;
            form.value.region_id = user.region_id ?? null;
            form.value.district_id = user.district_id ?? null;
            form.value.tehsil_id = user.tehsil_id ?? null;
            form.value.center_id = user.center_id ?? null;
            form.value.services = user.services || [];
            form.value.role_id = user.current_role?.id ?? null;
            form.value.sign_url = user.sign_url ?? null;
            form.value.is_active = user.is_active ?? null;
            form.value.city = user.city ?? null;

        } catch (error) {
            console.error('Error fetching user:', error);
        }
    };

    // Handle form submission
    const handleSubmit = async () => {

        const formData = new FormData();

        formData.append('prefix', form.value.prefix);
        formData.append('first_name', form.value.first_name);
        formData.append('last_name', form.value.last_name);
        formData.append('phone_no', form.value.phone_no);
        formData.append('address', form.value.address);
        formData.append('email', form.value.email);
        formData.append('cnic', form.value.cnic);
        formData.append('user_id', form.value.id);
        form.value.services.forEach((service, index) => {
            formData.append(`service_ids[${index}]`, service.id);
        });
        formData.append('center_id', form.value.center?.id || '');
        formData.append('role', form.value.role || '');
        formData.append('role_id', form.value.role?.id || '');
        formData.append('role_name', form.value.role?.name || '');
        formData.append('region_id', form.value.region?.id || '');
        formData.append('district_id', form.value.district?.id || '');
        formData.append('tehsil_id', form.value.tehsil?.id || '');
        formData.append('city_id', form.value.city?.id || '');
        formData.append('is_active', form.value.is_active || '');
        formData.append('sign_url', form.value.sign_url || '');
        if (form.value.password) {
            formData.append('password', form.value.password);
            formData.append('password_confirmation', form.value.password_confirmation);
        }

        // ✅ Append sign file (only if exists)
        if (form.value.sign_file) {
            formData.append('sign_file', form.value.sign_file);
        }

        try {
            isSubmitting.value = true;

            if (form.value.id) {
                // await axios.post('/api/user-service-assign', formData);
                await axios.post(`/api/users/${userId}?_method=PUT`, formData);
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'User updated successfully!',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                });
            } else {
                await axios.post(`/api/users`, formData);
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'User created successfully!',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                });
            }
            router.push('/users');

        } catch (error) {
            console.error('Error saving user:', error);

            if (error.response && error.response.status === 422) {
                errors.value = error.response.data.errors;
            } else {
                Swal.fire('Error', 'Failed to save user', 'error');
            }
        } finally {
            isSubmitting.value = false;
        }
    };

    const handleCnicFormating = () => {
        form.value.cnic = formatCnic(form.value.cnic);
    };

    onMounted(async () => {
        if (userId) {
            await fetchUser();
        }
        await fetchDropdownData();
    });

    const resetForm = () => {
        form.value = {
            first_name: '',
            last_name: '',
            email: '',
            cnic: '',
            password: '',
            confirm_password: '',
            center_id: null,
            region_id: null,
            district_id: null,
            tehsil_id: null,
            prefix: '',
            phone_no: '',
            role_id: null
        };
        selectedPrefix.value = null;
    };

    const store = useAppStore();


    // const fetchAuthUser = async () => {
    //     const res = await axios.get('/api/user');
    //     console.log('auth-user', res.data);
    //     store.user = res.data;
    // };

    const isCI = computed(() => store.user?.role === 'Center In-charge');


    const filteredRoles = computed(() => {
        if (isCI.value) {
            // Center Incharge can only assign DEO
            return roles.value.filter(r => r.name === 'DEO');
        }
        return roles.value; // baaki sab ke liye full list
    });
    const isRoleDisabled = computed(() => isCI.value); // true agar CI

    const setCIData = () => {
        form.value.region = regions.value.find(r => r.id === store.user?.region_id);
        form.value.district = districts.value.find(d => d.id === store.user?.district_id);
        form.value.tehsil = tehsils.value.find(t => t.id === store.user?.tehsil_id);
        form.value.center = centers.value.find(c => c.id === store.user?.center_id);

        if (isCI.value) {
            // Automatically set DEO role
            const deoRole = roles.value.find(r => r.name === 'DEO');
            if (deoRole) form.value.role = deoRole;
        }
    };

    onMounted(async () => {
        await fetchDropdownData();  // 👈 second

        if (isCI.value) {
            setCIData();            // only CI gets auto-fill & lock
        }

        if (userId) {
            await fetchUser();      // edit mode
            if (isCI.value) {
                setCIData();        // re-apply CI restrictions after fetch
            }
        }
    });


    const filteredServices = computed(() => {
        if (!form.value.center?.id) return [];

        return serviceCenters.value
            .filter(sc => sc.center_id === form.value.center.id)
            .map(sc => sc.service);
    });

   

</script>
