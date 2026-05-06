<template>
  <div class="p-6 min-h-screen">

    <!-- Breadcrumb + Button -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center text-sm text-gray-600 space-x-2">
        <i class="fa fa-dashboard"></i>
        <router-link to="/">Dashboard</router-link>
        <i class="fa fa-angle-right"></i>
        <span>Center Services</span>
      </div>

      <router-link
        to="/services-center/create"
        class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
        <i class="fa fa-plus"></i>
        Add New
      </router-link>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-lg shadow">

      <!-- Header -->
      <div class="flex items-center gap-2 px-6 py-4 border-b">
        <h2 class="text-lg font-semibold">Center Service List</h2>

        <div class="ml-auto">
          <input
            v-model="search"
            type="text"
            placeholder="Search Center..."
            class="form-input"
          />
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">

        <div v-if="loading" class="flex justify-center py-20">
          <span class="animate-spin border-8 border-gray-200 border-l-blue-500 rounded-full w-12 h-12"></span>
        </div>

        <table v-else class="min-w-full border-collapse">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 w-[60px]">Sr.#</th>
              <th class="px-4 py-3">Center Name</th>
              <th class="px-4 py-3">Assigned Services</th>
              <th class="px-4 py-3 text-center w-[120px]">Action</th>
            </tr>
          </thead>

          <tbody class="divide-y">
            <tr
              v-for="(item, index) in groupedData"
              :key="item.center.id"
              class="hover:bg-gray-50"
            >
              <td class="px-4 py-2">
                {{ (currentPage - 1) * perPage + index + 1 }}
              </td>

              <td class="px-4 py-2 font-medium">
                {{ item.center.name }}
              </td>

              <td class="px-4 py-2">
                <div class="flex flex-wrap gap-1">
                  <span
                    v-for="service in item.services"
                    :key="service.id"
                    class="badge badge-outline-success"
                  >
                    {{ service.name }}
                  </span>
                </div>
              </td>

              <td class="px-4 py-2 text-center space-x-2">
                <router-link
                  :to="`/services-center/edit/${item.center.id}`"
                  class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs"
                >
                  <i class="fa fa-pencil"></i>
                </router-link>

                <button
                  @click="deleteCenter(item.center.id)"
                  class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs"
                >
                  <i class="fa fa-trash"></i>
                </button>
              </td>

            </tr>
          </tbody>
        </table>
        <ul class="inline-flex items-center space-x-1 rtl:space-x-reverse m-auto justify-end w-full mt-5 mb-3">

          <!-- Prev -->
          <button v-if="lastPage > 1" @click="fetchData(currentPage - 1)" :disabled="currentPage === 1"
            class="px-3.5 py-1 rounded font-semibold bg-gray-200 hover:bg-blue-500 hover:text-white">
            Prev
          </button>

          <!-- Pages -->
          <li v-for="page in lastPage" :key="page" v-if="lastPage > 1">
            <button @click="fetchData(page)" :class="[
              'px-3 py-1 rounded font-semibold transition',
              page === currentPage
                ? 'bg-blue-600 text-white'
                : 'bg-gray-200 text-gray-700 hover:bg-blue-500 hover:text-white'
            ]">
              {{ page }}
            </button>
          </li>

          <!-- Next -->
          <button v-if="lastPage > 1" @click="fetchData(currentPage + 1)" :disabled="currentPage === lastPage"
            class="px-3.5 py-1 rounded font-semibold bg-gray-200 hover:bg-blue-500 hover:text-white">
            Next
          </button>

        </ul>
         

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

const loading = ref(false)
const search = ref('')
const data = ref([])

const currentPage = ref(1)
const lastPage = ref(1)
const perPage = ref(15)

// Fetch Assignments
const fetchData = async (page = 1) => {
  loading.value = true
  try {
    const res = await axios.get('/api/service-centers', {   // <-- add 'await'
      params: {
        page,
        search: search.value
      }
    })
    data.value = res.data.data.data || []   // now this will have actual data
    currentPage.value = res.data.data.current_page || 1
    lastPage.value = res.data.data.last_page || 1
    perPage.value = res.data.data.per_page || 15  // if you want to track total pages
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}

// Group by center
const groupedData = computed(() => {

  const grouped = {}

  data.value.forEach(row => {
    if (!grouped[row.center.id]) {
      grouped[row.center.id] = {
        center: row.center,
        services: []
      }
    }

    grouped[row.center.id].services.push(row.service)
  })

  return Object.values(grouped)
})

// Delete All Services for Center
const deleteCenter = async (centerId) => {

  const confirm = await Swal.fire({
    icon: 'warning',
    title: 'Are you sure?',
    text: 'This will remove all services from this center.',
    showCancelButton: true,
  })

  if (!confirm.isConfirmed) return

  try {
    await axios.delete(`/api/service-centers/${centerId}`)
    Swal.fire('Deleted!', 'Services removed successfully.', 'success')
    fetchData()
  } catch (err) {
    Swal.fire('Error', 'Delete failed', 'error')
  }
}

watch( search, () => {
  fetchData(1)
})

onMounted(() => {
  fetchData()
})
</script>