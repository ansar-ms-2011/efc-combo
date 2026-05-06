<template>
    <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-lg p-6 rounded shadow-lg">
            <h2 class="text-lg font-semibold mb-4">
                {{ form.id ? 'Edit Document' : 'Add Document' }}
            </h2>

            <div class="space-y-4">

                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium mb-1">Document Name</label>
                    <input v-model="form.name" placeholder="Enter name"
                           :class="['form-input w-full', errors.name ? 'border-red-500' : '']" />
                    <p v-if="errors.name" class="text-red-500 text-xs mt-1">{{ errors.name[0] }}</p>
                </div>

                <!-- Urdu Name -->
                <div>
                    <label class="block text-sm font-medium mb-1">Document Name Urdu</label>
                    <input v-model="form.urdu_name" placeholder="اردو نام"
                           :class="['form-input w-full font-nastaleeq', errors.urdu_name ? 'border-red-500' : '']" />
                    <p v-if="errors.urdu_name" class="text-red-500 text-xs mt-1">{{ errors.urdu_name[0] }}</p>
                </div>

                <!-- Service Name -->
                <div>
                    <label class="block text-sm font-medium mb-1">Certificate Name</label>
                    <select v-model="form.service_name"
                            :class="['form-input w-full', errors.service_name ? 'border-red-500' : '']">
                        <option value="">Select Service</option>
                        <option value="domicile">Domicile</option>
                        <option value="state-subject">State Subject Certificate</option>
                        <option value="both">Both</option>
                    </select>
                    <p v-if="errors.service_name" class="text-red-500 text-xs mt-1">{{ errors.service_name[0] }}</p>
                </div>

                <!-- Service Type -->
                <div>
                    <label class="block text-sm font-medium mb-1">Certificate Type</label>
                    <select v-model="form.service_type" :class="['form-input w-full', errors.service_type ? 'border-red-500' : '']">
                        <option value="">Select Type</option>
                        <option value="new">New</option>
                        <option value="duplicate">Duplicate</option>
                        <option value="both">Both</option>
                    </select>
                    <p v-if="errors.service_type" class="text-red-500 text-xs mt-1">{{ errors.service_type[0] }}</p>
                </div>

                <!-- Duplicate Reason -->
                <div v-if="form.service_type === 'duplicate'">
                    <label class="block text-sm font-medium mb-1">Duplicate Reason</label>
                    <select v-model="form.reason_type_id"
                            :class="['form-input w-full', errors.reason_type_id ? 'border-red-500' : '']">
                        <option value="" disabled>Select Reason</option>
                        <option v-for="(reason, index) in duplicate_reasons"
                                :value="reason.id"
                                :key="index">
                            {{ reason.name + ' ' + reason.urdu_name }}
                        </option>
                    </select>
                    <p v-if="errors.reason_type_id" class="text-red-500 text-xs mt-1">{{ errors.reason_type_id[0] }}</p>
                </div>

                <!-- Document Type -->
                <div>
                    <label class="block text-sm font-medium mb-1">Document Type</label>
                    <select v-model="form.required_copy"
                            :class="['form-input w-full', errors.required_copy ? 'border-red-500' : '']">
                        <option value="">Select Type</option>
                        <option value="original">Original</option>
                        <option value="photocopy">Photocopy</option>
                        <option value="scanned">Scanned</option>
                    </select>
                    <p v-if="errors.required_copy" class="text-red-500 text-xs mt-1">{{ errors.required_copy[0] }}</p>
                </div>

                <!-- File Type Dropdown -->
                    <div>
                        <label class="block text-sm font-medium mb-1">File Type</label>
                        <select v-model="form.file_type"
                                :class="['form-input w-full', errors.file_type ? 'border-red-500' : '']">
                            <option value="">Select File Type</option>
                            <option value="image">Image</option>
                            <option value="pdf">PDF</option>
                            <option value="both">Both (Image & PDF)</option>

                        </select>
                        <p v-if="errors.file_type" class="text-red-500 text-xs mt-1">{{ errors.file_type[0] }}</p>
                    </div>



                <!-- Active -->
                <div class="flex items-center gap-2">
                    <input type="checkbox" v-model="form.active" id="active"
                           class="w-4 h-4" />
                    <label for="active" class="text-sm">Active</label>
                </div>

            </div>

            <div class="flex justify-end gap-4 mt-4">
                <button @click="submit"
                        :disabled="loading"
                        class="px-4 py-2 bg-green-600 text-white rounded flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span v-if="loading"
                          class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
                    <span>{{ loading ? 'Saving...' : 'Save' }}</span>
                </button>

                <button @click="close"
                        class="px-4 py-2 bg-gray-400 text-white rounded">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, watch } from 'vue';
    import axios from 'axios';
    import Swal from 'sweetalert2';
    import { useAppStore } from '@/stores/index.ts';
    import { storeToRefs } from 'pinia';

    const appStore = useAppStore();
    const { duplicate_reasons } = storeToRefs(appStore);
    console.log(duplicate_reasons);

    const props = defineProps({ service: Object, show: Boolean });
    const emit = defineEmits(['update:show', 'saved']);

    const form = ref({
        name: '',
        urdu_name: '',
        service_name: '',
        service_type: '',
        reason_type_id:'',
        required_copy: '',
         file_type: '', 
        active: true
    });

    const errors = ref({});

    watch(() => props.service, (val) => {
        if (val) form.value = { ...val };
        else form.value = {
            name: '',
            urdu_name: '',
            service_name: '',
            service_type: '',
            reason_type_id:'',
            required_copy: '',
             file_type: '', 
            active: true
        };
        // Clear errors when switching between edit/add
        errors.value = {};
        console.log(form.value);
    }, { immediate: true });


    const close = () => {
        errors.value = {};
        emit('update:show', false);
    };

    const loading = ref(false);

    const submit = async () => {
        // Clear previous errors
        errors.value = {};
        loading.value = true;

        try {
            if (form.value.id) {
                await axios.put(`/api/required-documents/${form.value.id}`, form.value);
                Swal.fire({
                    icon: 'success',
                    title: 'Updated',
                    text: 'Record updated successfully',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500
                });
                emit('saved');
                close();
            } else {
                await axios.post(`/api/required-documents`, form.value);
                Swal.fire({
                    icon: 'success',
                    title: 'Added',
                    text: 'Record added successfully',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500
                });
                emit('saved');
                close();
            }
        } catch (e) {
            if (e.response && e.response.status === 422) {
                // Laravel validation error
                errors.value = e.response.data.errors;
                Swal.fire({
                    icon: 'warning',
                    title: 'Validation Error',
                    text: 'Please check the form for errors',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000
                });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Something went wrong',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        } finally {
            loading.value = false;
        }
    };
</script>
