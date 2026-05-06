  <template>
  <div class="p-6 min-h-screen">
    <!-- breadcrumb -->
      <div class="text-sm text-gray-600 mb-4">
        <router-link to="/" class="text-grey-600 hover:underline">
          Dashboard
        </router-link>

<i class="fa fa-angle-right"></i>     
   <router-link to="/center" class="text-gery-600 hover:underline">
          Center List
        </router-link>
      <i class="fa fa-angle-right"></i>
      <router-link to="/center/create" class="font-semibold text-blue-600 hover:underline">
        Add Center
        </router-link>     
       </div>
    <div class="max-w-7xl mx-auto bg-white rounded-lg shadow border p-6">

  
      <!-- heading -->
      <h2 class="text-xl  mb-6 text-black p-2 rounded font-semibold">
        Add Center
      </h2>
      <!-- form -->
      <form class="space-y-6" @submit.prevent="handleSubmit">

        <!-- row 1 -->
<!-- Row 1 -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
  <div>
    <label class="block text-sm font-medium mb-1">Center Name <span class="text-red-500">*</span></label>
    <input
    v-model="form.name"
      type="text"
      placeholder="Center Name"
       class="form-input"
    />
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">No. of Counters</label>
    <input
    v-model="form.number_of_counters"
      type="number"
      placeholder="Counter"
       class="form-input"
    />
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Address</label>
    <input
    v-model="form.address"
      type="text"
      placeholder="Center Address"
       class="form-input"
    />
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
      class="custom-multiselect">
    </multiselect>
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
      class="custom-multiselect">
    </multiselect>
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
      :show-labels="false">        
    </multiselect>
  </div>

    <div>
    <label class="block text-sm font-medium mb-1">Time</label>
    <div class="flex gap-4">
      <input
      v-model="form.working_start"
        type="time"
         class="form-input"
      />
      <!-- <input
        type="time"
        class="flex-1 border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500"
      /> -->
    </div>

  </div>
   <div>
    <label class="block text-sm font-medium mb-1 mt-6"></label>
    <div class="flex gap-4 ">
      <input
      v-model="form.working_end"
        type="time"
         class="form-input"
      />
      <!-- <input
        type="time"
        class="flex-1 border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500"
      /> -->
    </div>
    
  </div>

    <!-- <div>
      <label class="block text-sm font-medium mb-1">Timing</label>
      <div class="flex gap-4 w-full">
        <input
          type="time"
          class=" border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500"
        />
        <input
          type="time"
          class=" border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500"
        />
      </div>
    </div> -->
</div>


<!-- row 3 -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
  <div>
    <label class="block text-sm font-medium mb-1">Lunch Break</label>
    <div class="flex gap-4">
      <input
     v-model="form.lunch_break"
        type="time"
        class="form-input"
      />
      <input
      v-model="form.lunch_end"
        type="time"
         class="form-input"
      />
    </div>
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Jumma Break</label>
    <div class="flex gap-4">
      <input
      v-model="form.jumma_start"
        type="time"
         class="form-input"
      />
      <input
      v-model="form.jumma_end"
        type="time"
         class="form-input"
      />
    </div>
  </div>
</div>
 <!-- row4  -->
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
        <div class="flex justify-end  gap-3 mt-6">
              <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded shadow">
                Save
              </button>
              <button 
               type="button" @click="resetForm"
                class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded shadow">
                Reset
              </button>
              <router-link to="/center"
                class="px-6 py-2 bg-red-100 hover:bg-red-500 text-red-600 hover:text-white rounded shadow">
                Cancel
              </router-link>
            </div>

      </form>
    </div>
  </div>
</template>


<script setup>
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import { useRouter } from 'vue-router' 

const router = useRouter()

// Form data
const form = ref({
  name: '',
  number_of_counters:'',
  address:'',
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

// Districts
const districts = ref([])
const fetchDistricts = async () => {
  try {
    const res = await axios.get('/api/demographies/parents/DISTRICT')
    districts.value = res.data
  } catch (e) {
    console.error('Error fetching districts', e)
  }
}

// Tehsils
// Tehsils
const tehsils = ref([])

const fetchTehsils = async (districtId) => {
  try {
    const res = await axios.get(`/api/get-tehsils?district_id=${districtId}`)
    tehsils.value = res.data
    console.log('Tehsils fetched:', tehsils.value)
  } catch (error) {
    console.error('Error fetching tehsils', error)
  }
}

// Watch for district changes
watch(() => form.value.district_id, (newVal) => {
  form.value.tehsil_id = null
  tehsils.value = []

  if (newVal && newVal.id) {
    fetchTehsils(newVal.id)
  }
})

// Working Days
const workingDays = ref([])
const fetchWorkingDays = async () => {
  try {
    const res = await axios.get('/api/working-days')
    workingDays.value = res.data
  } catch (error) {
    console.error('Error fetching working days:', error)
  }
}

// Fetch districts and working days on mount
onMounted(() => {
  fetchDistricts()
  fetchWorkingDays()
})

// Form submission
const handleSubmit = async () => {
  try {
    const payload = { ...form.value }

    // Convert district & tehsil to IDs
    payload.district_id = payload.district_id?.id || null
    payload.tehsil_id = payload.tehsil_id?.id || null

    // Timing & breaks
    payload.timing = `${payload.working_start} - ${payload.working_end}`
    payload.lunch_break = `${payload.lunch_break} - ${payload.lunch_end}`
    payload.jumma_break = `${payload.jumma_start} - ${payload.jumma_end}`

    const response = await axios.post('/api/centers', payload)
    console.log('Center saved:', response.data)

    Swal.fire({
      icon: 'success',
      title: 'Success!',
      text: 'Center saved successfully!',
      padding: '2em',
    }).then(() => {
      router.push('/center')
    })

    resetForm()
  } catch (error) {
    console.error('Error saving center:', error)
    Swal.fire({
      icon: 'error',
      title: 'Failed!',
      text: error.response?.data?.message || 'Failed to save center data',
      padding: '2em',
    })
  }
}

// Reset form
const resetForm = () => {
  form.value = {
    name: '',
    number_of_counters:'',
    address:'',
    working_days: [],
    working_start: '',
    working_end: '',
    lunch_break: '',
    lunch_end: '',
    jumma_start: '',
    jumma_end: '',
    district_id: null,
    tehsil_id: null,
     contact_number: '', // Reset this
    // geo_location: '',   // Reset this
    latitude: '',       // Reset this
    longitude: '',  
  }
}
</script>

