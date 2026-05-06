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
      <span class="cursor-pointer text-blue-600">Edit Additional Charge</span>
    </div>

    <div class="max-w-7xl mx-auto bg-white rounded-lg shadow border p-6">
      <!-- Heading -->
      <h2 class="text-xl mb-6 text-black p-2 rounded font-semibold">
        Edit Additional Charge
      </h2>

      <!-- Form -->
      <form class="space-y-6" @submit.prevent="updateAdditionalCharge">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4">

          <!-- User -->
          <div>
            <label class="block text-sm font-medium mb-1">
              User <span class="text-red-500">*</span>
            </label>
            <multiselect
              v-model="form.primary_user_id"
              :options="charges"
              track-by="id"
              label="name"
              placeholder="Select User"
              class="custom-multiselect"
            />
          </div>

          <!-- Charge -->
          <div>
            <label class="block text-sm font-medium mb-1">
              Charge <span class="text-red-500">*</span>
            </label>
            <multiselect
              v-model="form.temporary_user_id"
              :options="charges"
              track-by="id"
              label="name"
              placeholder="Select Charge"
              class="custom-multiselect"
            />
          </div>

          <!-- Start Date -->
          <div>
            <label class="block text-sm font-medium mb-1">
              Start Date <span class="text-red-500">*</span>
            </label>
            <input v-model="form.start_date" type="date" class="form-input" />
          </div>

          <!-- End Date -->
          <div>
            <label class="block text-sm font-medium mb-1">
              End Date <span class="text-red-500">*</span>
            </label>
            <input v-model="form.end_date" type="date" class="form-input" />
          </div>

          <!-- Notice Number -->
          <div>
            <label class="block text-sm font-medium mb-1">
              Notice Number <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.notice_number"
              type="text"
              class="form-input"
              placeholder="Notice Number"
            />
          </div>

          <!-- Notice Image -->
          <div>
            <label class="block text-sm font-medium mb-1">
              Notice Image
            </label>
            <div class="flex items-center gap-4">
              <input
                type="file"
                @change="handlePhotoUpload"
                accept="image/*"
                class="flex-1 px-3 py-2 border border-gray-300 rounded-md"
              />

              <!-- Old or New Preview -->
              <img
                v-if="photoPreview"
                :src="photoPreview"
                class="w-20 h-20 object-cover rounded-md border"
              />
            </div>
          </div>

          <!-- Description -->
          <div class="col-span-2">
            <label class="block text-sm font-medium mb-1">
              Description
            </label>
            <textarea
              v-model="form.description"
              class="form-textarea"
              rows="5"
            ></textarea>
          </div>

        </div>

        <!-- Buttons -->
        <div class="flex justify-end gap-3 mt-6">
          <button
            type="submit"
            class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded shadow"
          >
            Update
          </button>

          <router-link
            to="/additional-charges"
            class="px-6 py-2 bg-red-100 hover:bg-red-500 text-red-600 hover:text-white rounded shadow"
          >
            Cancel
          </router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import Swal from 'sweetalert2'

const route = useRoute()
const id = route.params.id

const charges = ref([])
const photoPreview = ref(null)
const photo = ref(null)

const form = ref({
  primary_user_id: null,
  temporary_user_id: null,
  start_date: null,
  end_date: null,
  notice_number: null,
  description: null,
})

/* Get Users */
const getCharges = async () => {
  const res = await axios.get('/api/get-charges')
  charges.value = res.data.data || []
}

/* Get Existing Record */
const getAdditionalCharge = async () => {
  const res = await axios.get(`/api/additional-charges/${id}`)
  const data = res.data.data

  // console.log('Data:', data)

  form.value.start_date = data.start_date
  form.value.end_date = data.end_date
  form.value.notice_number = data.notice_number
  form.value.description = data.description

  form.value.primary_user_id = charges.value.find(u => u.id === data.primary_user_id) || null
  form.value.temporary_user_id = charges.value.find(u => u.id === data.temporary_user_id) || null

  // Old image preview
  if (data.notice_image) {
    photoPreview.value = null
    photoPreview.value = `http://localhost:8000/storage/${(data.notice_image)}`
  }
}

/* Update */
const updateAdditionalCharge = async () => {
  try {
    const formData = new FormData()
    formData.append('_method', 'PUT')
    formData.append('primary_user_id', form.value.primary_user_id?.id || '')
    formData.append('temporary_user_id', form.value.temporary_user_id?.id || '')
    formData.append('start_date', form.value.start_date || '')
    formData.append('end_date', form.value.end_date || '')
    formData.append('notice_number', form.value.notice_number || '')
    formData.append('description', form.value.description || '')

    if (photo.value) {
      formData.append('notice_image', photo.value)
    }

    await axios.post(`/api/additional-charges/${id}`, formData)

    Swal.fire('Updated!', 'Additional charge updated successfully', 'success')
      .then(() => {
        window.location.href = '/additional-charges'
      })

  } catch (err) {
    Swal.fire('Error', 'Update failed', 'error')
  }
}

const handlePhotoUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    photo.value = file
    photoPreview.value = URL.createObjectURL(file)
  }
}

onMounted(async () => {
  await getCharges()
  await getAdditionalCharge()
})
</script>

