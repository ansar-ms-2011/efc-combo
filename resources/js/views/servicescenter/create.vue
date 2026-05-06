<template>
  <div class="page-content-wrapper bg-gray-100 min-h-screen">
    <div class="page-content">

      <!-- Breadcrumb -->
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center text-sm text-gray-600 space-x-1">
          <i class="fa fa-dashboard"></i>

          <router-link to="/" class="cursor-pointer">
            Center Service
          </router-link>

          <i class="fa fa-angle-right"></i>
          <i class="fa fa-users"></i>

          <router-link to="/services-center" class="cursor-pointer">
            Center Service List
          </router-link>

          <i class="fa fa-angle-right"></i>

          <span class="font-semibold text-blue-600">
            Add Center Service
          </span>
        </div>
      </div>

      <!-- Card -->
      <div class="max-w-7xl mx-auto bg-white rounded-lg shadow border p-6">
        <!-- Header -->
        <div class="flex items-center gap-2 rounded-t-lg rounded-b-lg">
          <h2 class="text-lg font-semibold text-black">
            Add Center Service
          </h2>
        </div>

        <!-- Form -->
        <div class="p-6">
          <form @submit.prevent="handleSubmit">

  <!-- Center Multiselect -->
  <div class="mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-2">
      Select Center(s)
    </label>

    <multiselect
      v-model="selectedCenters"
      :options="centers"
      track-by="id"
      label="name"
      placeholder="Select Centers"
      :multiple="true"
      :searchable="true"
      :close-on-select="false"
      :show-labels="false"
      class="custom-multiselect"
    />
  </div>

  <!-- Services List -->
  <div class="border rounded-lg overflow-hidden">
    <table class="min-w-full bg-white">

      <thead class="bg-gray-100">
        <tr>
          <th class="text-left p-3 border">Service Name</th>
          <th class="text-left p-3 border">Assign</th>
        </tr>
      </thead>

      <tbody>
        <tr
          v-for="service in services"
          :key="service.id"
          class="hover:bg-gray-50"
        >
          <td class="p-3 border">
            {{ service.name }}
          </td>

          <td class="p-3 border">
            <input
              type="checkbox"
              :value="service.id"
              v-model="selectedServices"
              class="w-5 h-5"
            />
          </td>
        </tr>
      </tbody>

    </table>
  </div>

  <!-- Buttons -->
  <div class="flex justify-end gap-3 mt-6">

    <button
      type="submit"
      class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded shadow"
    >
      Save
    </button>

    <button
      type="button"
      @click="resetForm"
      class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded shadow"
    >
      Reset
    </button>

    <router-link
      to="/services-center"
      class="px-6 py-2 bg-red-100 hover:bg-red-500 text-red-600 hover:text-white rounded shadow"
    >
      Cancel
    </router-link>

  </div>

</form>

         

        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

// Reactive State
const centers = ref([])
const services = ref([])
const selectedCenters = ref([])
const selectedServices = ref([])

// Fetch Centers
const fetchCenters = async () => {
  try {
    const res = await axios.get('/api/centers')
    centers.value = res.data.data?.data || res.data.data || []
  } catch (err) {
    console.error(err)
  }
}

// Fetch Services
const fetchServices = async () => {
  try {
    const res = await axios.get('/api/services')
    services.value = res.data.data?.data || res.data.data || []
  } catch (err) {
    console.error(err)
  }
}

// Submit
const handleSubmit = async () => {

  if (!selectedCenters.value.length) {
    return Swal.fire('Warning', 'Please select at least one center', 'warning')
  }

  if (!selectedServices.value.length) {
    return Swal.fire('Warning', 'Please select at least one service', 'warning')
  }

  const payload = []

  selectedCenters.value.forEach(center => {
  selectedServices.value.forEach(serviceId => {
    payload.push({
      center_id: center.id,
      service_id: serviceId
    })
  })
})

  try {
    await axios.post('/api/service-centers', {
      assignments: payload
    })

    Swal.fire('Success', 'Services assigned successfully', 'success').then(() => window.location.href = '/services-center')
    resetForm()

  } catch (err) {
    Swal.fire('Error', err.response?.data?.message || 'Something went wrong', 'error')
  }
}

// Reset
const resetForm = () => {
  selectedCenters.value = []
  selectedServices.value = []
}

// Mounted
onMounted(() => {
  fetchCenters()
  fetchServices()
})
</script>
