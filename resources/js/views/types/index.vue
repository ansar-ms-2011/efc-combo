<template>
  <div class="p-6 min-h-screen">
    <!-- Breadcrumb + Button -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center text-sm text-gray-600 space-x-2">
        <i class="fa fa-dashboard"></i>
        <router-link to="/" class="cursor-pointer">
          Dashboard
        </router-link>
        <i class="fa fa-angle-right"></i>
        <span class="cursor-pointer">Type List</span>
      </div>

      <router-link :to="`/type/${route.params.type}/create`"
        class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
        <i class="fa fa-plus"></i> Add New
      </router-link>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-lg shadow">
      <!-- Header -->
      <div class="flex items-center gap-2 px-6 py-4 border-b rounded-t-lg">
        <h2 class="text-lg font-semibold">Types</h2>

        <!-- search -->
        <div class="ltr:ml-auto rtl:mr-auto">
          <input v-model="search" type="text" class="form-input" placeholder="Search..." />
        </div>
      </div>


      <!-- Table -->
      <div class="overflow-x-auto">
        <div v-if="loading" class="flex justify-center items-center py-20">
          <span
            class="animate-spin border-8 border-[#f1f2f3] border-l-primary rounded-full w-14 h-14 inline-block"></span>
        </div>
        <table v-else class="min-w-full border-collapse">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Sr.#</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Name</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Urdu Name</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Created Date</th>
              <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Action</th>
            </tr>
          </thead>

          <tbody class="divide-y">
            <tr v-for="(type, index) in types" :key="type.id" class="hover:bg-gray-50">
              <td class="px-4 py-1 font-small">{{ (currentPage - 1) * 10 + index + 1 }}</td>
              <td class="px-4 py-1 font-small">{{ type.name || 'Null' }}</td>
              <td class="px-4 py-1 font-small font-nastaleeq">{{ type.urdu_name || 'Null' }}</td>
              <td class="px-4 py-1 font-small">{{ $formatDMY(type.created_at) }}</td>
              <td class="px-4 py-1 font-small text-center space-x-2">
                <router-link :to="{ name: 'types.edit', params: { type: route.params.type, id: type.id } }"
                  class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs">
                  <i class="fa fa-pencil"></i>
                </router-link>
                <router-link :to="`/type/:type`" @click.prevent="deleteType(type.id)"
                  class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs">
                  <i class="fa fa-trash"></i>
                </router-link>

              </td>
            </tr>
          </tbody>
        </table>

        <ul   v-if="!loading && lastPage > 1" class="inline-flex items-center space-x-1 rtl:space-x-reverse m-auto justify-end w-full mt-5 mb-3">

          <!-- Prev button -->
          <button v-if="lastPage > 1" @click="fetchTypes(currentPage - 1)" :disabled="currentPage === 1"
            class="flex justify-center font-semibold px-3.5 py-1 rounded transition bg-white-light text-dark hover:text-white hover:bg-primary dark:text-white-light dark:bg-[#191e3a] dark:hover:bg-primary">
            Prev
          </button>

          <!-- Page numbers -->
          <li v-for="page in lastPage" :key="page" v-if="lastPage > 1">
            <button type="button" @click="fetchTypes(page)" :class="[
              'px-3 py-1 rounded font-semibold transition',
              page === currentPage
                ? 'bg-blue-600 text-white'
                : 'bg-gray-200 text-gray-700 hover:bg-blue-500 hover:text-white'
            ]">
              {{ page }}
            </button>
          </li>

          <!-- Next button -->
          <button v-if="lastPage > 1" @click="fetchTypes(currentPage + 1)" :disabled="currentPage === lastPage"
            class="flex justify-center font-semibold px-3.5 py-1 rounded transition bg-white-light text-dark hover:text-white hover:bg-primary dark:text-white-light dark:bg-[#191e3a] dark:hover:bg-primary">
            Next
          </button>
        </ul>


      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import Swal from 'sweetalert2'

// ====== STATE ======
const search = ref('')
const types = ref([])
const currentPage = ref(1)
const lastPage = ref(1)

const route = useRoute()

// ====== FETCH TYPES ======
const loading = ref(false) // loader state

const fetchTypes = async (page = 1) => {
  loading.value = true // start loader
  try {
    const response = await axios.get(`/api/types`, {
      params: {
        page,
        type: route.params.type,
        search: search.value
      }
    })

    types.value = response.data?.message?.data || []
    currentPage.value = parseInt(response.data?.message?.current_page || 1)
    lastPage.value = parseInt(response.data?.message?.last_page || 1)
  } catch (error) {
    console.error('Error fetching types:', error)
    types.value = []
    currentPage.value = 1
    lastPage.value = 1
  } finally {
    loading.value = false // stop loader
  }
}


// ====== DELETE TYPE ======
const deleteType = async (id) => {
  const confirm = await Swal.fire({
    icon: 'warning',
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    showCancelButton: true,
    confirmButtonText: 'Delete',
    cancelButtonText: 'Cancel'
  })

  if (!confirm.isConfirmed) return

  try {
    await axios.delete(`/api/types/${id}`)
    types.value = types.value.filter(t => t.id !== id)
    Swal.fire('Deleted!', 'Record deleted successfully.', 'success')
  } catch (error) {
    console.error(error)
    Swal.fire('Error', 'Delete failed.', 'error')
  }
}

// ====== WATCH ROUTE PARAM ======
watch(
  () => route.params.type,
  () => {
    currentPage.value = 1
    fetchTypes(1)
  }
)

// // ====== WATCH SEARCH WITH DEBOUNCE ======
// let searchTimeout = null
// watch(search, () => {
//   clearTimeout(searchTimeout)
//   searchTimeout = setTimeout(() => {
//     currentPage.value = 1
//     fetchTypes(1)
//   }, 500)
// })

watch(search, () => {
  fetchTypes(1)
})

// ====== INITIAL FETCH ======
onMounted(() => {
  fetchTypes()
})
</script>
