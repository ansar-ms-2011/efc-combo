<template>
  <div class="p-6 min-h-screen">

    <!-- Breadcrumb + Add Button -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center text-sm text-gray-600 space-x-2">
        <i class="fa fa-dashboard"></i>
        <router-link to="/" class="cursor-pointer">Dashboard</router-link>
        <i class="fa fa-angle-right"></i>
        <span>User Service List</span>
      </div>

      <router-link
        to="/userservice/create"
        class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
        <i class="fa fa-plus"></i>
        Add New
      </router-link>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-lg shadow">

      <!-- Header -->
      <div class="flex items-center gap-2 px-6 py-4 border-b rounded-t-lg">
        <!-- <i class="fa fa-cogs"></i> -->
        <h2 class="text-lg font-semibold">
          User Service List
        </h2>
      </div>

      <!-- Body -->
      <div class="p-6">

        <!-- Service Multiselect -->
        <div class="max-w-md mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Service Name
          </label>

          <multiselect
            v-model="selectedService"
            :options="services"
            track-by="id"
            label="name"
            placeholder="Select Service"
            :multiple="false"
            :searchable="true"
            :clear-on-select="true"
            :close-on-select="true"
            :show-labels="false"
            class="custom-multiselect"
          />
        </div>

        <!-- Table (only when service selected) -->
        <div v-if="selectedService" class="overflow-x-auto">
          <table class="min-w-full border border-collapse">
            <thead class="bg-gray-50">
              <tr>
                <th class="border px-4 py-2">Sr.#</th>
                <th class="border px-4 py-2">Service Name</th>
                <th class="border px-4 py-2">User Name</th>
                <th class="border px-4 py-2">Created Date</th>
                <th class="border px-4 py-2 text-center">Action</th>
              </tr>
            </thead>

            <tbody>
              <tr
                v-for="(row, index) in filteredUserServices"
                :key="row.id"
                class="hover:bg-gray-50"
              >
                <td class="border px-4 py-2">{{ index + 1 }}</td>
                <td class="border px-4 py-2">{{ row.name }}</td>
                <td class="border px-4 py-2">
                  {{ row.user.first_name }} {{ row.user.last_name }}
                </td>
                <td class="border px-4 py-2">{{ row.created_at }}</td>
                <td class="border px-4 py-2 text-center"></td>
              </tr>

              <tr v-if="filteredUserServices.length === 0">
                <td colspan="5" class="text-center py-4 text-gray-500">
                  No data found
                </td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
const selectedService = ref(null)

const services = ref([
  { id: 1, name: 'Service A' },
  { id: 2, name: 'Service B' },
  { id: 3, name: 'Service C' },
])

const userServices = ref([
  {
    id: 1,
    service_id: 1,
    name: 'Service A',
    created_at: '01-01-2026 10:00',
    user: { first_name: 'John', last_name: 'Doe' },
  },
  {
    id: 2,
    service_id: 1,
    name: 'Service A',
    created_at: '02-01-2026 11:30',
    user: { first_name: 'Jane', last_name: 'Smith' },
  },
  {
    id: 3,
    service_id: 2,
    name: 'Service B',
    created_at: '03-01-2026 15:30',
    user: { first_name: 'Ali', last_name: 'Khan' },
  },
])

// same logic as your first component
const filteredUserServices = computed(() => {
  if (!selectedService.value) return []
  return userServices.value.filter(
    item => item.service_id === selectedService.value.id
  )
})
</script>

