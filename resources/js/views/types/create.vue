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

          <router-link to="/type/group" class="cursor-pointer">Type List</router-link>

          <i class="fa fa-angle-right"></i>

          <span class="font-semibold text-blue-600">
            Add {{ type === 'item' ? 'Item' : 'Group' }}
          </span>
        </div>
      </div>

      <!-- Card -->
      <div class="max-w-7xl mx-auto bg-white rounded-lg shadow border p-6">

        <!-- Header -->
        <div class="flex items-center gap-2 rounded-t-lg rounded-b-lg">
          <i class="fa fa-plus text-white"></i>
          <h2 class="text-lg font-semibold text-black">
            Add {{ type === 'item' ? 'Item' : 'Group' }}
          </h2>
        </div>

        <!-- Form -->
        <div class="p-6">
          <form @submit.prevent="handleSubmit">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

              <!-- Name English -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Name (English) <span class="text-red-500">*</span>
                </label>
                <input type="text" v-model="form.name" placeholder="Name" class="form-input" required />
              </div>

              <!-- Name Urdu -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Name (Urdu)
                </label>
                <input type="text" v-model="form.urdu_name" dir="rtl" placeholder="نام"
                  class="form-input font-nastaleeq" />
              </div>

              <!-- 👇 Only for ITEM -->
              <div v-if="type === 'item'">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Select Group <span class="text-red-500">*</span>
                </label>

                <select v-model="form.parent_id"  class="form-input" required>
                  <option value="">Select Group</option>

                  <option v-for="g in groups" :key="g.id" :value="Number(g.id)">
                    {{ g.name }}
                  </option>
                </select>
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

              <router-link to="/type/group"
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
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import Swal from 'sweetalert2'

const route = useRoute()
const type = route.params.type   // "group" | "item"

const form = ref({
  name: '',
  urdu_name: '',
  parent_id: '',
})

const groups = ref([])

// load groups only if item
const loadGroups = async () => {
  if (type !== 'item') return

  const res = await axios.get('/api/types?type=group')
  groups.value = res.data.data.data
}

// save
const handleSubmit = async () => {
  console.log(form.value)
  try {
    await axios.post('/api/types', form.value)

    Swal.fire({
      icon: 'success',
      title: 'Good job!',
      text: 'Type saved successfully!',
    }).then(() => {
      window.location.href = '/type/' + type
    })
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Failed!',
      text: 'Failed to save type',
    })
  }
}

const resetForm = () => {
  form.value = {
    name: '',
    urdu_name: '',
    parent_id: '',
  }
}

onMounted(() => {
  loadGroups()
})
</script>
