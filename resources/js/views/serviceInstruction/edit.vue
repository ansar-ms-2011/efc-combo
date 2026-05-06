<template>
  <div class="p-6 min-h-screen">
    <!-- Breadcrumb -->
    <div class="text-sm text-gray-600 mb-4">
      <router-link to="/admin/dashboard" class="text-grey-600 hover:underline">
        Dashboard
      </router-link>
      <i class="fa fa-angle-right"></i>
      <router-link to="/service-instruction" class="text-grey-600 hover:underline">
        Service Instruction List
      </router-link>
      <i class="fa fa-angle-right"></i>
      <span class="cursor-pointer text-blue-600">Edit Service Instruction</span>
    </div>

    <div class="max-w-7xl mx-auto bg-white rounded-lg shadow border p-6">
      <!-- Heading -->  
      <h2 class="text-xl mb-6 text-black p-2 rounded font-semibold">
        Edit Service Instruction
      </h2>

      <!-- Form -->
      <form class="space-y-6" @submit.prevent="updateServiceInstruction">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4">
          <!-- Service Dropdown -->
          <div>
            <label class="block text-sm font-medium mb-1">Service <span class="text-red-500">*</span></label>
            <multiselect 
              v-model="selected.service" 
              :options="services" 
              track-by="id" 
              label="name"
              placeholder="Select Service" 
              class="custom-multiselect" 
              :multiple="false" 
              :searchable="true"
              :clear-on-select="true" 
              :close-on-select="true" 
              :show-labels="false" 
            />
          </div>

          <!-- Service Title -->
          <div>
            <label class="block text-sm font-medium mb-1">Service Title <span class="text-red-500">*</span></label>
            <input v-model="serviceTitle" placeholder="Service Title" type="text" class="form-input" />
          </div>

          <!-- Service Instruction -->
           <div class="col-span-2">
            <label class="block text-sm font-medium mb-1">Service Instruction</label>
            <!-- <input v-model="serviceInstruction" type="text" placeholder="Service Instruction" class="form-input"  /> -->
            <textarea v-model="serviceInstruction" placeholder="Service Instruction" class="form-textarea" rows="5"></textarea>
          </div>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end gap-3 mt-6">
          <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded shadow">
            Update
          </button>
          <button type="button" @click="resetForm" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded shadow">
            Reset
          </button>
          <router-link to="/service-instruction"
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
import { useRoute } from 'vue-router'

const route = useRoute()
const instructionId = route.params.id

// Dropdown data
const services = ref([])
const selected = ref({ service: null })

// Form fields
const serviceTitle = ref('')
const serviceInstruction = ref('')

// Fetch all services
const fetchServices = async () => {
  try {
    const res = await axios.get('/api/services')
    services.value = res.data.data.data || []
  } catch (err) {
    console.error('Error fetching services:', err)
  }
}

// Fetch instruction details
const fetchInstruction = async () => {
  try {
    const res = await axios.get(`/api/serviceinstructions/${instructionId}`)
    if (res.data.success) {
      const data = res.data.data
      selected.value.service = services.value.find(s => s.id === data.service_id) || { id: data.service_id, name: data.service?.name }
      serviceTitle.value = data.instruction_title
      serviceInstruction.value = data.instruction_description
    }
  } catch (err) {
    console.error(err)
    Swal.fire('Error', 'Failed to load instruction data', 'error')
  }
}

// Reset form
const resetForm = () => {
  selected.service = null
  serviceTitle.value = ''
  serviceInstruction.value = ''
}

// Update instruction
const updateServiceInstruction = async () => {
  if (!selected.value.service || !serviceTitle.value || !serviceInstruction.value) {
    Swal.fire('Warning', 'Please fill all required fields', 'warning')
    return
  }

  try {
    const payload = {
      service_id: selected.value.service.id,
      service_title: serviceTitle.value,
      service_instruction: serviceInstruction.value,
    }

    await axios.put(`/api/serviceinstructions/${instructionId}`, payload)

    Swal.fire('Success', 'Instruction updated successfully', 'success').then(() => {
      window.location.href = '/service-instruction'
    })
  } catch (err) {
    console.error(err.response?.data)
    Swal.fire('Error', err.response?.data?.message || 'Update failed', 'error')
  }
}

onMounted(async () => {
  await fetchServices()
  fetchInstruction()
})
</script>
