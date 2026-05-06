<template>
  <div class="p-6 min-h-screen">
    <!-- Breadcrumb -->
    <div class="text-sm text-gray-600 mb-4">
      <router-link to="/admin/dashboard" class="text-grey-600 hover:underline">
        Dashboard
      </router-link>
      <i class="fa fa-angle-right"></i>
      <router-link to="/services" class="text-grey-600 hover:underline">
        Services
      </router-link>
      <i class="fa fa-angle-right"></i>
      <span class="cursor-pointer text-blue-600">Add Additional Charge</span>
    </div>

    <div class="max-w-7xl mx-auto bg-white rounded-lg shadow border p-6">
      <!-- Heading -->
      <h2 class="text-xl mb-6 text-black p-2 rounded font-semibold">
        Add Additional Charge
      </h2>

      <!-- Form -->
      <form class="space-y-6" @submit.prevent="saveAdditionalCharge">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4">

          <!-- User -->
          <div>
            <label class="block text-sm font-medium mb-1">User <span class="text-red-500">*</span></label>
            <multiselect v-model="form.primary_user_id" :options="charges" track-by="id" label="name"
              placeholder="Select User" class="custom-multiselect" :multiple="false" :searchable="true"
              :clear-on-select="true" :close-on-select="true" :show-labels="false" />
          </div>

          <!-- Charge -->
          <div>
            <label class="block text-sm font-medium mb-1">Charge <span class="text-red-500">*</span></label>
            <multiselect v-model="form.temporary_user_id" :options="charges" track-by="id" label="name"
              placeholder="Select Charge" class="custom-multiselect" :multiple="false" :searchable="true"
              :clear-on-select="true" :close-on-select="true" :show-labels="false" />
          </div>

          <!-- Start Date -->
          <div>
            <label class="block text-sm font-medium mb-1">Start Date <span class="text-red-500">*</span></label>
            <input v-model="form.start_date" type="date" class="form-input" />
          </div>

          <!-- End Date -->
          <div>
            <label class="block text-sm font-medium mb-1">End Date <span class="text-red-500">*</span></label>
            <input v-model="form.end_date" type="date" class="form-input" />
          </div>

          <!-- Notice Number -->
          <div>
            <label class="block text-sm font-medium mb-1">Notice Number <span class="text-red-500">*</span></label>
            <input v-model="form.notice_number" type="text" class="form-input" placeholder="Notice Number" />
          </div>

          <!-- Notice Image -->
          <div>
            <label class="block text-sm font-medium mb-1">Notice Image <span class="text-red-500">*</span></label>
            <div class="flex items-center gap-4">
              <input type="file" @change="handlePhotoUpload" accept="image/*"
                class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
              <img v-if="photoPreview" :src="photoPreview" alt="Photo Preview"
                class="w-20 h-20 object-cover rounded-md border">
            </div>
          </div>

          <!-- Description -->
          <div class="col-span-2">
            <label class="block text-sm font-medium mb-1">Description</label>
            <textarea v-model="form.description" placeholder="Description" class="form-textarea" rows="5"></textarea>
          </div>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end gap-3 mt-6">
          <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded shadow">
            Save
          </button>
          <button type="button" @click="resetForm"
            class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded shadow">
            Reset
          </button>
          <router-link to="/additional-charges"
            class="px-6 py-2 bg-red-100 hover:bg-red-500 text-red-600 hover:text-white rounded shadow">
            Cancel
          </router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
const charges = ref([])
const photoPreview = ref(null)
const photo = ref(null)



const form = ref({
  primary_user_id: null,
  temporary_user_id: null,
  start_date: null,
  end_date: null,
  notice_number: null,
  notice_image: null,
  description: null,
})

const getCharges = async () => {
  try {
    const res = await axios.get('/api/get-charges')
    charges.value = res.data.data || []
    console.log('charges', charges.value)
  } catch (err) {
    console.error('Error fetching charges:', err)
  }
}


const saveAdditionalCharge = async () => {
  try {
    const formData = new FormData()

    formData.append('primary_user_id', form.value.primary_user_id?.id || '')
    formData.append('temporary_user_id', form.value.temporary_user_id?.id || '')
    formData.append('start_date', form.value.start_date || '')
    formData.append('end_date', form.value.end_date || '')
    formData.append('notice_number', form.value.notice_number || '')
    formData.append('description', form.value.description || '')

    if (photo.value) {
      formData.append('notice_image', photo.value)
    }

    await axios.post('/api/additional-charges', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    Swal.fire({
      icon: 'success',
      title: 'Saved!',
      text: 'Additional charge saved successfully',
    }).then(() => {
      window.location.href = '/additional-charges'
    })

  } catch (err) {
    console.error(err.response?.data)
    Swal.fire({
      icon: 'error',
      title: 'Failed!',
      text: err.response?.data?.message || 'Failed to save',
    })
  }
}


const handlePhotoUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = (e) => {
      photoPreview.value = e.target.result;
    };
    photo.value = file;
    form.value.notice_image = file;
  }
};



onMounted(() => {
  getCharges()
})
</script>
