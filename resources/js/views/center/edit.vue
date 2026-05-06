<template>
  <div class="p-6 min-h-screen">
    <!-- breadcrumb -->
    <div class="text-sm text-gray-600 mb-4">
      <router-link to="/" class="text-grey-600 hover:underline">Dashboard</router-link>
      <i class="fa fa-angle-right"></i>     
      <router-link to="/center" class="text-gery-600 hover:underline">Center List</router-link>
      <i class="fa fa-angle-right"></i>
      <span class="font-semibold text-blue-600">Edit Center</span>     
    </div>

    <div class="max-w-7xl mx-auto bg-white rounded-lg shadow border p-6">
      <h2 class="text-xl mb-6 text-black p-2 rounded font-semibold">Edit Center</h2>

      <form class="space-y-6" @submit.prevent="handleSubmit">
        <!-- Row 1 -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1">Center Name <span class="text-red-500">*</span></label>
            <input v-model="form.name" type="text" placeholder="Center Name" class="form-input"/>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">No. of Counters</label>
            <input v-model="form.number_of_counters" type="number" placeholder="Counter" class="form-input"/>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Address</label>
            <input v-model="form.address" type="text" placeholder="Center Address" class="form-input"/>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">District <span class="text-red-500">*</span></label>
            <multiselect
              v-model="form.district_id"
              :options="districts"
              track-by="id"
              label="name"
              placeholder="Select District"
              :multiple="false"
              :searchable="true"
              :clear-on-select="true"
              :close-on-select="true"
              :show-labels="false"
              class="custom-multiselect"
            />
          </div>
        </div>

        <!-- Row 2 -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
          <div>
            <label class="block text-sm font-medium mb-1">Tehsil <span class="text-red-500">*</span></label>
            <multiselect
              v-model="form.tehsil_id"
              :options="tehsils"
              track-by="id"
              label="name"
              placeholder="Select Tehsil"
              :multiple="false"
              :searchable="true"
              :clear-on-select="true"
              :close-on-select="true"
              :show-labels="false"
              class="custom-multiselect"
            />
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Working Days <span class="text-red-500">*</span></label>
            <multiselect
              v-model="form.working_days"
              :options="workingDays"
              track-by="id"
              label="name"
              placeholder="Select Day"
              :multiple="true"
              :searchable="true"
              :clear-on-select="true"
              :close-on-select="false"
              :show-labels="false"
              class="Days-multiselect"
            />
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Time</label>
            <div class="flex gap-4">
              <input v-model="form.working_start" type="time" class="form-input"/>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1 mt-6"></label>
            <div class="flex gap-4">
              <input v-model="form.working_end" type="time" class="form-input"/>
            </div>
          </div>
        </div>

        <!-- Row 3 -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
          <div>
            <label class="block text-sm font-medium mb-1">Lunch Break</label>
            <div class="flex gap-4">
              <input v-model="form.lunch_break" type="time" class="form-input"/>
              <input v-model="form.lunch_end" type="time" class="form-input"/>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Jumma Break</label>
            <div class="flex gap-4">
              <input v-model="form.jumma_start" type="time" class="form-input"/>
              <input v-model="form.jumma_end" type="time" class="form-input"/>
            </div>
          </div>  
        </div>
         <!-- New Row for Contact and Geo Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
          <div>
            <label class="block text-sm font-medium mb-1">Contact Number</label>
            <input v-model="form.contact_number" type="text" placeholder="03123456789" class="form-input" />
          </div>

          <!-- <div>
            <label class="block text-sm font-medium mb-1">Geo Location</label>
            <input v-model="form.geo_location" type="text" placeholder="General Area" class="form-input" />
          </div> -->

          <div>
            <label class="block text-sm font-medium mb-1">Latitude</label>
            <input v-model="form.latitude" type="text" placeholder="34.35..." class="form-input" />
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Longitude</label>
            <input v-model="form.longitude" type="text" placeholder="73.47..." class="form-input" />
          </div>
        </div>

        <!-- buttons -->
        <div class="flex justify-end gap-3 mt-6">
          <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded shadow">Update</button>
          <router-link to="/center" class="px-6 py-2 bg-red-100 hover:bg-red-500 text-red-600 hover:text-white rounded shadow">Cancel</router-link>
        </div>

      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()
const centerId = route.params.id

// Form data
const form = ref({
  name: '',
  number_of_counters: '',
  address: '',
  working_days: [],
  working_start: '',
  working_end: '',
  lunch_break: '',
  lunch_end: '',
  jumma_start: '',
  jumma_end: '',
  district_id: null,
  tehsil_id: null,
   contact_number: '', // Add this
  // geo_location: '',   // Add this
  latitude: '',       // Add this
  longitude: '', 
})

// Districts & Tehsils
const districts = ref([])
const tehsils = ref([])
const workingDays = ref([])

const fetchDistricts = async () => {
  try { districts.value = (await axios.get('/api/demographies/parents/DISTRICT')).data } 
  catch(e) { console.error(e) }
}

const fetchWorkingDays = async () => {
  try { workingDays.value = (await axios.get('/api/working-days')).data } 
  catch(e) { console.error(e) }
}

const fetchTehsils = async (districtId) => {
  if(!districtId) return
  try {
    tehsils.value = (await axios.get(`/api/get-tehsils?district_id=${districtId}`)).data
  } catch(e) { console.error('Error fetching tehsils', e) }
}

// Load center data
const fetchCenter = async () => {
  try {
    const res = await axios.get(`/api/centers/${centerId}`)
    const data = res.data
     form.value.id = data.id  
    form.value.name = data.name
    form.value.number_of_counters = data.number_of_counters
    form.value.address = data.address
    form.value.working_start = data.working_start
    form.value.working_end = data.working_end
    form.value.lunch_break = data.lunch_break_start
    form.value.lunch_end = data.lunch_break_end
    form.value.jumma_start = data.jumma_break_start
    form.value.jumma_end = data.jumma_break_end
    form.value.contact_number = data.contact_number || ''
    // form.value.geo_location = data.geo_location || '' 
    form.value.latitude = data.latitude || ''
    form.value.longitude = data.longitude || ''

    // District & Tehsil objects
    form.value.district_id = { id: data.district_id, name: data.district_name }
    await fetchTehsils(data.district_id)
    form.value.tehsil_id = { id: data.tehsil_id, name: data.tehsil_name }

    // Working days array (objects)
    form.value.working_days = data.working_days
  } catch(e) {
    console.error('Error fetching center', e)
  }
}

// Watch for district change to reload tehsils
watch(() => form.value.district_id, (newVal) => {
  form.value.tehsil_id = null
  tehsils.value = []
  if(newVal && newVal.id) fetchTehsils(newVal.id)
})

const handleSubmit = async () => {
  try {
    // Prepare payload
    const payload = { ...form.value }

    // Only send IDs for district, tehsil, working days
    payload.district_id = form.value.district_id?.id || form.value.district_id
    payload.tehsil_id = form.value.tehsil_id?.id || form.value.tehsil_id

    // Working days: send only IDs
    payload.working_days = form.value.working_days.map(day => day.id || day)

    // Timing & breaks
    payload.timing = `${payload.working_start} - ${payload.working_end}`
    payload.lunch_break = `${payload.lunch_break} - ${payload.lunch_end}`
    payload.jumma_break = `${payload.jumma_start} - ${payload.jumma_end}`

    // Send PUT request
    const response = await axios.put(`/api/centers/${form.value.id}`, payload)
    console.log('Center updated:', response.data)

    Swal.fire({
      icon: 'success',
      title: 'Success!',
      text: 'Center updated successfully!',
      padding: '2em',
    }).then(() => {
      router.push('/center')
    })

  } catch (error) {
    console.error('Error updating center:', error)
    Swal.fire({
      icon: 'error',
      title: 'Failed!',
      text: error.response?.data?.message || 'Failed to update center',
      padding: '2em',
    })
  }
}

onMounted(() => {
  fetchDistricts()
  fetchWorkingDays()
  fetchCenter()
})
</script>