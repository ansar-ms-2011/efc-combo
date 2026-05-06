<template>
  <div class="p-6 min-h-screen">
    <!-- Breadcrumb -->
    <div class="text-sm text-gray-600 mb-4">
      <router-link to="/admin/dashboard" class="text-grey-600 hover:underline">Dashboard</router-link>
      <i class="fa fa-angle-right"></i>
      <router-link to="/services" class="text-grey-600 hover:underline">Services</router-link>
      <i class="fa fa-angle-right"></i>
      <span class="cursor-pointer text-blue-600">{{ isEdit ? 'Edit Service' : 'Add Service' }}</span>
    </div>

    <div class="max-w-7xl mx-auto bg-white rounded-lg shadow border p-6">
      <h2 class="text-xl mb-6 text-black p-2 rounded font-semibold">{{ isEdit ? 'Edit Service' : 'Add Service' }}</h2>

      <form @submit.prevent="saveService" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

          <!-- Service Name -->
          <div>
            <label class="block text-sm font-medium mb-1">Service <span class="text-red-500">*</span></label>
            <input v-model="serviceName" type="text" placeholder="Service Name" class="form-input"/>
          </div>

          <!-- Department -->
          <div>
            <label class="block text-sm font-medium mb-1">Department <span class="text-red-500">*</span></label>
            <multiselect v-model="selected.department" :options="departments" track-by="id" label="name"
              placeholder="Select Department" :multiple="false" :searchable="true" :clear-on-select="true"
              :close-on-select="true" :show-labels="false" class="custom-multiselect"/>
          </div>

        

          <!-- No. of Days -->
          <div>
            <label class="block text-sm font-medium mb-1">No. of Days</label>
            <input v-model="days" type="number" placeholder="No of Days" class="form-input"/>
          </div>

          <!-- Price -->
          <div>
            <label class="block text-sm font-medium mb-1">Price</label>
            <input v-model="price" type="text" placeholder="Rs:" class="form-input" @input="formatPrice"/>
          </div>

          <!-- File -->
          <div>
            <label class="block text-sm font-medium mb-1">Upload File</label>
            <input type="file" @change="handleFile" class="form-input"/>
          </div>

          <!-- Description -->
          <div class="col-span-4">
            <label class="block text-sm font-medium mb-1">Service Description</label>
            <textarea v-model="description" rows="5" class="form-textarea"></textarea>
          </div>

        </div>

        <!-- Buttons -->
        <div class="flex justify-end gap-3 mt-6">
          <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded shadow">
            {{ isEdit ? 'Update' : 'Save' }}
          </button>
          <button type="button" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded shadow" @click="resetForm">Reset</button>
          <router-link to="/services" class="px-6 py-2 bg-red-100 hover:bg-red-500 text-red-600 hover:text-white rounded shadow">Cancel</router-link>
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
const serviceId = route.params.id || null
const isEdit = ref(!!serviceId)

const departments = ref([])
const selected = ref({ department: null })
const serviceName = ref('')
const days = ref('')
const price = ref('')
const file = ref(null)
const description = ref('')

// Fetch Departments
const fetchDepartments = async () => {
  try {
    const res = await axios.get('/api/departments')
    departments.value = res.data.data?.data || []
  } catch (err) { console.error(err) }
}

// Handle file
const handleFile = (e) => { file.value = e.target.files[0] }

// Reset form
const resetForm = () => {
  serviceName.value = ''
  days.value = ''
  price.value = ''
  file.value = null
  description.value = ''
  selected.department = null
}

// Load service for edit
const loadService = async () => {
  if (!isEdit.value) return
  try {
    const res = await axios.get(`/api/services/${serviceId}`)
    const data = res.data.data

    serviceName.value = data.name
    days.value = data.no_of_days
    price.value = data.price
    description.value = data.service_description
    selected.department = departments.value.find(d => d.id === data.dept_id) || null
  } catch (err) {
    console.error(err)
    Swal.fire({ icon: 'error', title: 'Failed', text: 'Failed to load service data' })
  }
}

// Save service
const saveService = async () => {
  if (!serviceName.value || !selected.department) {
    return Swal.fire({ icon: 'warning', title: 'Missing fields', text: 'Please fill all required fields' })
  }

  const formData = new FormData()
  formData.append('name', serviceName.value)
  formData.append('dept_id', selected.department.id)
  formData.append('no_of_days', days.value || '')
  formData.append('price', price.value || '')
  formData.append('service_description', description.value || '')
  if (file.value) formData.append('file', file.value)

  try {
    if (isEdit.value) {
      formData.append('_method', 'PUT')
      await axios.post(`/api/services/${serviceId}`, formData)
    } else {
      await axios.post('/api/services', formData)
    }

    Swal.fire({ icon: 'success', title: `Service ${isEdit.value ? 'updated' : 'saved'} successfully` })
      .then(() => window.location.href = '/services')
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Failed', text: err.response?.data?.message || 'Failed to save service' })
  }
}

const formatPrice = (event) => {

  let value = event.target.value;

  // allow digits and dot
  value = value.replace(/[^0-9.]/g, '');

  // allow only one decimal point
  const parts = value.split('.');
  if (parts.length > 2) {
    value = parts[0] + '.' + parts[1];
  }

  let [integer = '', decimal = ''] = value.split('.');

  // decimal(8,2) → 6 integer digits
  if (integer.length > 6) {
    integer = integer.slice(0, 6);
  }

  // limit decimal to 2 digits
  if (decimal.length > 2) {
    decimal = decimal.slice(0, 2);
  }

  // preserve typing behavior
  if (value.endsWith('.')) {
    price.value = integer + '.';
  } 
  else if (decimal) {
    price.value = integer + '.' + decimal;
  } 
  else {
    price.value = integer;
  }

};

// Mounted
onMounted(async () => {
  await fetchDepartments()
  await loadService()
})
</script>