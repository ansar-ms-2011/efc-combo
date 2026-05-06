<template>
  <div class="p-6 min-h-screen">

    <!-- Breadcrumb + Add Button -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center text-sm text-gray-600 space-x-2">
        <i class="fa fa-dashboard"></i>

        <router-link to="/" class="cursor-pointer">
          Dashboard
        </router-link>

        <i class="fa fa-angle-right"></i>

        <router-link to="/checkList/index" class="cursor-pointer">
          Check List
        </router-link>
      </div>

      <router-link
        to="/checkList/create"
        class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow"
      >
        <i class="fa fa-plus"></i>
        Add New
      </router-link>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-lg shadow">

      <!-- Header -->
      <div class="flex items-center gap-2 px-6 py-4 border-b rounded-t-lg">
        <h2 class="text-lg font-semibold">Check List</h2>
      </div>

      <!-- Body -->
      <div class="p-6">

        <div class="max-w-md">
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

      </div>
    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue'

const selectedService = ref(null)

const services = ref([
  { id: 1, name: 'Birth Certificate' },
  { id: 2, name: 'Death Certificate' },
  { id: 3, name: 'Arms License' },
  { id: 4, name: 'Domicile' },
])
</script>
