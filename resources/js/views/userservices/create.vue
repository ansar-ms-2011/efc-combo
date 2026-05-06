<template>
  <div class="page-content-wrapper p-6 bg-gray-100 min-h-screen">
    <div class="page-content">

      <!-- Breadcrumb -->
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center text-sm text-gray-600 space-x-1">
          <i class="fa fa-dashboard"></i>
          <router-link to="/" class="cursor-pointer ">Dashboard</router-link>
          <i class="fa fa-angle-right"></i>
          <router-link to="/userservice" class="cursor-pointer ">User Service List</router-link>
          <i class="fa fa-angle-right"></i>
          <span class="font-semibold text-blue-600">Add User Service</span>
        </div>
      </div>

      <!-- Card -->
      <div class="max-w-7xl mx-auto bg-white rounded-lg shadow border p-6">
        <!-- Header -->
        <div class="flex items-center gap-2 rounded-t-lg rounded-b-lg">
          <i class="fa fa-plus text-white"></i>
          <h2 class="text-lg font-semibold text-black">Add User Service</h2>
        </div>

        <!-- Form -->
        <div class="p-6">
          <form @submit.prevent="handleSubmit">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

              <!-- Service Name -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Service Name</label>

                <multiselect v-model="selectedService" :options="services" track-by="id" label="name"
                  placeholder="Select Service" :multiple="false" :searchable="true" :clear-on-select="true"
                  :close-on-select="true" :show-labels="false" class="custom-multiselect">
                </multiselect>
              </div>


              <!-- User Name -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">User Name</label>

                <multiselect v-model="selectedUser" :options="users" track-by="id"
                  :custom-label="user => `${user.first_name} ${user.last_name} (${user.email})`"
                  placeholder="Select User" :multiple="false" :searchable="true" :clear-on-select="true"
                  :close-on-select="true" :show-labels="false" class="custom-multiselect">
                </multiselect>
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
              <router-link to="/userservice"
                class="px-6 py-2 bg-red-100 hover:bg-red-500 text-red-600 hover:text-white rounded shadow">
                Cancel
              </router-link>
            </div>

          </form>

          <!-- Frontend Result -->
          <div v-if="submitted" class="mt-6 p-4 bg-green-50 border border-blue-200 rounded">
            <h3 class="font-semibold text-green-700 mb-2">User Service Added (Frontend only)</h3>
            <p><strong>Service:</strong> {{ selectedServiceName }}</p>
            <p><strong>User:</strong> {{ selectedUserName }}</p>
          </div>

        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const services = ref([
  { id: 1, name: "Service A" },
  { id: 2, name: "Service B" },
  { id: 3, name: "Service C" },
])

const users = ref([
  { id: 1, first_name: "John", last_name: "Doe", email: "john@example.com" },
  { id: 2, first_name: "Jane", last_name: "Smith", email: "jane@example.com" },
  { id: 3, first_name: "Ali", last_name: "Khan", email: "ali@example.com" },
])

const selectedService = ref('')
const selectedUser = ref('')
const submitted = ref(false)

/* computed */
const selectedServiceName = computed(() => {
  const service = services.value.find(
    s => s.id === parseInt(selectedService.value)
  )
  return service ? service.name : ''
})

const selectedUserName = computed(() => {
  const user = users.value.find(
    u => u.id === parseInt(selectedUser.value)
  )
  return user
    ? `${user.first_name} ${user.last_name} (${user.email})`
    : ''
})

/* methods */
const handleSubmit = () => {
  submitted.value = true
  console.log("User Service Data:", {
    service: selectedServiceName.value,
    user: selectedUserName.value,
  })
}

const resetForm = () => {
  selectedService.value = ''
  selectedUser.value = ''
  submitted.value = false
}
</script>
