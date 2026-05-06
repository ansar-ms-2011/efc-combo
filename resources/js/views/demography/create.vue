<template>
  <div class="page-content-wrapper p-6 bg-gray-100 min-h-screen">
    <div class="page-content">

      <!-- Breadcrumb -->
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center text-sm text-gray-600 space-x-1">
          <i class="fa fa-dashboard"></i>
          <router-link to="/" class="cursor-pointer">Dashboard</router-link>
          <i class="fa fa-angle-right"></i>
          <i class="fa fa-users"></i>
          <router-link :to="`/demography/${currentType.toLowerCase()}`" class="cursor-pointer">
            {{ currentTypeLabel }} List
          </router-link>
          <i class="fa fa-angle-right"></i>
          <span class="font-semibold text-blue-600">Add {{ currentTypeLabel }}</span>
        </div>
      </div>

      <!-- Card -->
      <div class="max-w-7xl mx-auto bg-white rounded-lg shadow border p-6">

        <!-- Header -->
        <div class="flex items-center gap-2 rounded-t-lg rounded-b-lg mb-4">
          <i class="fa fa-plus text-white"></i>
          <h2 class="text-lg font-semibold text-black">Add {{ currentTypeLabel }}</h2>
        </div>

        <!-- Form -->
        <div class="p-6">
          <form @submit.prevent="handleSubmit">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

             
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Name (English) <span class="text-red-500">*</span>
                </label>
                <input type="text" v-model="form.name" placeholder="Name" class="form-input" required />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Name (Urdu)
                </label>
                <input type="text" v-model="form.urdu_name" dir="rtl" placeholder="نام"
                  class="form-input font-nastaleeq " />
              </div>

            
              <div>
                <label class="block text-sm font-medium mb-1">Code</label>
                <input v-model="form.code" class="form-input" placeholder="Code" />
              </div>

              <!-- Type dropdown,  -->
            <div v-if="currentType !== 'COUNTRY'">
              <label class="block text-sm font-medium mb-1">{{form.type.charAt(0).toUpperCase() + form.type.slice(1).toLowerCase()}} <span class="text-red-500">*</span></label>
              <select v-model="form.parent_id" class="form-input" required>
                <option value="">Select  {{form.type.charAt(0).toUpperCase() + form.type.slice(1).toLowerCase()}}</option>
                <option v-for="p in parents" :key="p.id" :value="p.id">
                  {{ p.name }}
                </option>
              </select>
        </div>


            </div>

        <div v-if="currentType === 'DISTRICT'" class="flex items-center mt-6">
          <input type="checkbox" v-model="form.is_ajk_district" class="mr-2" />
          <label class="text-sm font-medium">AJK District</label>
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
              <router-link :to="`/demography/${currentType.toLowerCase()}`"
                class="px-6 py-2 bg-red-100 hover:bg-red-500 text-red-600 hover:text-white rounded shadow">
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
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import Swal from 'sweetalert2'

const route = useRoute()
const router = useRouter()

const arr  = {
  'REGION': 'COUNTRY',
  'DISTRICT':'REGION',
  'TEHSIL':'DISTRICT',
  'CITY':'TEHSIL',
  'UNION_COUNCIL':'CITY'
}


const form = ref({
  name: '',
  urdu_name: '',
  code: '',
  type: arr[route.params.type?.toUpperCase()],
  type_original: route.params.type?.toUpperCase(),
  parent_id: '',
  is_ajk_district: false
})


const currentType = computed(() => {
  return route.params.type ? route.params.type.toUpperCase() : form.value.type
})

// Breadcrumb 
const currentTypeLabel = computed(() => {
  return currentType.value
    .toLowerCase()
    .split('_')
    .map(w => w.charAt(0).toUpperCase() + w.slice(1))
    .join(' ')
});




const parents = ref([])

const fetchParents = async () => {
  try {
     
   const res = await axios.get(`/api/demographies/parents/${form.value.type}` )
    parents.value = res.data

// auto select Pakistan 
if(currentType.value === 'REGION'){
  const pakistan = parents.value.find(p => p.name === 'Pakistan')
  if(pakistan){
    form.value.parent_id = pakistan.id
  }
}
      


  } catch (e) {
    console.error('Error loading parents', e)
  }
}


// watch route param to update form type
watch(currentType, () => {
  form.value.parent_id = ''  // reset selected parent
  fetchParents()
})


onMounted(() => {
  fetchParents()
})




const handleSubmit = async () => {
  try {

    console.log("form values", form.value)
    await axios.post('/api/demographies', form.value)
    Swal.fire({
      icon: 'success',
      title: 'Good job!',
      text: `${currentTypeLabel.value} saved successfully!`,
      padding: '2em'
    }).then(() => {
      router.push(`/demography/${currentType.value.toLowerCase()}`)
    })
    resetForm()
  } catch (error) {
    console.error('Error saving type:', error)
    Swal.fire({
      icon: 'error',
      title: 'Failed!',
      text: `Failed to save ${currentTypeLabel.value}`,
      padding: '2em'
    })
  }
}

const resetForm = () => {
  form.value = {
    name: '',
    urdu_name: '',
    code: '',
    type: arr[currentType.value],
    type_original: currentType.value,
    parent_id: '',
    is_ajk_district: false
  }
}
</script>
