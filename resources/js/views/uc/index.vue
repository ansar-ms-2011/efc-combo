<template>
  <div class="p-6  min-h-screen">

    <!-- Breadcrumb + Add Button -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center text-sm text-gray-600 space-x-2">
        <i class="fa fa-dashboard"></i>
       
        <router-link to="/" class="cursor-pointer">
          Dashboard
        </router-link>

        <i class="fa fa-angle-right"></i>

        <span class="cursor-pointer">
          UC List
        </span>
      </div>

      <router-link to="/unioncouncil/create"
        class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
        <i class="fa fa-plus"></i>
        Add New
      </router-link>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-lg shadow">

      <!-- Header -->
      <div class="flex items-center  gap-2 px-6 py-4 border-b rounded-t-lg ">
        <!-- <i class="fa fa-cogs "></i> -->
        <h2 class="text-lg font-semibold ">
          UC List
        </h2>

        <!-- search -->
        <div class="ltr:ml-auto rtl:mr-auto">
          <input v-model="search1" type="text" class="form-input" placeholder="Search..." />
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="min-w-full border-collapse">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">
                Sr.#
              </th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">
                City Name
              </th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">
                UC Code
              </th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">
                UC Name
              </th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">
                Created Date
              </th>
              <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">
                Action
              </th>
            </tr>
          </thead>

          <tbody class="divide-y">
            <tr v-for="(uc, index) in ucs" :key="uc.id" class="hover:bg-gray-50">
              <!-- Sr -->
              <td class="px-4 py-1 font-small">
                {{ index + 1 }}
              </td>

              <!-- City -->
              <td class="px-4 py-1 font-small">
                <div class="font-medium">
                  {{ uc.city_name }}
                </div>
                <!-- <div class="text-sm font-nastaleeq text-gray-500">
                  {{ uc.city_name_urdu }}
                </div>
                <div class="text-xs text-gray-400">
                  {{ uc.city_code }}
                </div> -->
              </td>

              <!-- UC Code -->
              <td class="px-4 py-1 font-small">
                {{ uc.uc_code }}
              </td>

              <!-- UC Name -->
              <td class="px-4 py-1 font-small">
                <div class="font-medium">
                  {{ uc.name }}
                </div>
                <!-- <div class="text-sm font-nastaleeq text-gray-500">
                  {{ uc.name_urdu }}
                </div> -->
              </td>

              <!-- Date -->
              <td class="px-4 py-1 font-small text-gray-600">
                {{ uc.created_at }}
              </td>

              <!-- Action -->
              <td class="px-4 py-1 font-small text-center space-x-2">
                <router-link :to="`/unioncouncil/edit/${uc.id}`"
                  class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs">
                  <i class="fa fa-pencil"></i>
                </router-link>

                <button class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs"
                  @click="deleteUC(uc.id)">
                  <i class="fa fa-trash"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

// Dummy frontend data
const ucs = ref([
  {
    id: 1,
    city_name: 'City A',
    city_name_urdu: 'شہر اے',
    city_code: 'C001',
    uc_code: 'UC001',
    name: 'UC One',
    name_urdu: 'یونین کونسل 1',
    created_at: '31-12-2025 15:00:00',
  },
  {
    id: 2,
    city_name: 'City B',
    city_name_urdu: 'شہر بی',
    city_code: 'C002',
    uc_code: 'UC002',
    name: 'UC Two',
    name_urdu: 'یونین کونسل 2',
    created_at: '30-12-2025 12:30:00',
  },
])
</script>
