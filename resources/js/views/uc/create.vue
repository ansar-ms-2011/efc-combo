<template>
  <div class="page-content-wrapper p-6">
    <div class="page-content">

      <!-- Breadcrumb -->
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center text-sm text-gray-600 space-x-1">
          <i class="fa fa-dashboard"></i>

          <router-link to="/" class="cursor-pointer">
            Dashboard
          </router-link>

          <i class="fa fa-angle-right"></i>
          <i class="fa fa-users"></i>

          <router-link to="/unioncouncil" class="cursor-pointer">
            UC List
          </router-link>

          <i class="fa fa-angle-right"></i>
          <span class="font-semibold text-blue-600">
            Add UC
          </span>
        </div>
      </div>

      <!-- Card -->
      <div class="max-w-7xl mx-auto bg-white rounded-lg shadow border p-6">

        <!-- Header -->
        <div class="flex items-center gap-2 rounded-t-lg rounded-b-lg">
          <i class="fa fa-plus text-white"></i>
          <h2 class="text-lg font-semibold text-black">
            Add UC
          </h2>
        </div>

        <!-- Form -->
        <div class="p-6">
          <form @submit.prevent="handleSubmit">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

              <!-- City Name -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  City Name
                </label>
                <multiselect v-model="city_id" :options="cities" track-by="id" label="name" placeholder="Select City"
                  :multiple="false" :searchable="true" :clear-on-select="true" :close-on-select="true" class="custom-multiselect"
                  :show-labels="false" @input="updateUCcode">
                </multiselect>

              </div>

              <!-- UC Code -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  UC Code
                </label>
                <input type="text" v-model="uc_code" placeholder="UC Code"
                   class="form-input" required />
              </div>

              <!-- UC Name -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  UC Name
                </label>
                <input type="text" v-model="name" placeholder="UC Name"
                  class="form-input" required />
              </div>

              <!-- UC Name Urdu -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  UC Name (Urdu)
                </label>
                <input type="text" v-model="name_urdu" placeholder="یونین کونسل کا نام "
                  class="form-input font-nastaleeq" required />
              </div>

            </div>

            <!-- Buttons -->
            <div class="flex justify-end  gap-3 mt-6">

              <!-- Save -->
              <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded shadow">
                Save
              </button>

              <!-- Reset -->
              <button type="button" @click="resetForm"
                class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded shadow">
                Reset
              </button>

              <!-- Cancel -->
              <router-link to="/unioncouncil"
                class="px-6 py-2 bg-red-100 hover:bg-red-500 text-red-600 hover:text-white rounded shadow">
                Cancel
              </router-link>

            </div>
          </form>

          <!-- Frontend Preview -->
          <div v-if="submitted" class="mt-6 p-4 bg-green-50 border border-green-200 rounded">
            <h3 class="font-semibold text-green-700 mb-2">
              UC Added (Frontend only)
            </h3>
            <p><strong>City:</strong> {{ getCityName(city_id) }}</p>
            <p><strong>UC Code:</strong> {{ uc_code }}</p>
            <p><strong>UC Name:</strong> {{ name }}</p>
            <p><strong>UC Name Urdu:</strong> {{ name_urdu }}</p>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<!-- <script setup>
import { ref } from 'vue'

const cities = ref([
  { id: 1, name: 'City A', city_code: 'C001' },
  { id: 2, name: 'City B', city_code: 'C002' },
  { id: 3, name: 'City C', city_code: 'C003' },
])

const city_id = ref('')
const uc_code = ref('')
const name = ref('')
const name_urdu = ref('')
const submitted = ref(false)

const handleSubmit = () => {
  submitted.value = true
  console.log('UC Data:', {
    city_id: city_id.value,
    uc_code: uc_code.value,
    name: name.value,
    name_urdu: name_urdu.value,
  })
}

const resetForm = () => {
  city_id.value = ''
  uc_code.value = ''
  name.value = ''
  name_urdu.value = ''
  submitted.value = false
}


</script> -->


<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

// FORM FIELDS
const city_id = ref('')
const uc_code = ref('')
const name = ref('')
const name_urdu = ref('')
const submitted = ref(false)

// Dummy UC Data (Normally API se aata)
const ucs = [
  {
    id: 1,
    city_id: 1,
    city_name: 'City A',
    uc_code: 'UC001',
    name: 'UC One',
    name_urdu: 'یونین کونسل 1',
  },
  {
    id: 2,
    city_id: 2,
    city_name: 'City B',
    uc_code: 'UC002',
    name: 'UC Two',
    name_urdu: 'یونین کونسل 2',
  },
]

// City dropdown
const cities = ref([
  { id: 1, name: 'City A', city_code: 'C001' },
  { id: 2, name: 'City B', city_code: 'C002' },
  { id: 3, name: 'City C', city_code: 'C003' },
])

// 🔥 EDIT MODE LOAD
onMounted(() => {
  const editId = route.params.id

  if (editId) {
    const uc = ucs.find(u => u.id == editId)

    if (uc) {
      city_id.value = uc.city_id
      uc_code.value = uc.uc_code
      name.value = uc.name
      name_urdu.value = uc.name_urdu
    }
  }
})

// 🔹 FORM SUBMIT
function handleSubmit() {
  submitted.value = true

  console.log('UC DATA:', {
    city_id: city_id.value,
    uc_code: uc_code.value,
    name: name.value,
    name_urdu: name_urdu.value,
  })
}

// 🔹 RESET FORM
function resetForm() {
  city_id.value = ''
  uc_code.value = ''
  name.value = ''
  name_urdu.value = ''
  submitted.value = false
}
</script>
