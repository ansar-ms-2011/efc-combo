<template>
  <div class="p-6 min-h-screen">

    <!-- Breadcrumb + Add Button -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center text-sm text-gray-600 space-x-2">
        <i class="fa fa-dashboard"></i>
        <router-link to="/">Dashboard</router-link>
        <i class="fa fa-angle-right"></i>
        <span>{{ currentTypeLabel }} List</span>
      </div>

      <router-link :to="`/demography/${currentType}/create`"
        class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
        <i class="fa fa-plus"></i> Add {{ currentTypeLabel }}
      </router-link>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-lg shadow">

      <!-- Header -->
      <div class="flex items-center gap-2 px-6 py-4 border-b">
        <h2 class="text-lg font-semibold">{{ currentTypeLabel }} List</h2>

        <div class="ltr:ml-auto rtl:mr-auto">
          <input v-model="search" type="text" class="form-input" placeholder="Search..." />
        </div>
      </div>

      <!-- Loader -->
      <div class="overflow-x-auto">
        <div v-if="loading" class="flex justify-center items-center py-20">
          <span
            class="animate-spin border-8 border-[#f1f2f3] border-l-primary rounded-full w-14 h-14 inline-block"></span>
        </div>

        <!-- Table -->

        <table v-else class="min-w-full table-fixed border-collapse">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-sm font-semibold">Sr.#</th>
              <th class="px-4 py-3 text-left text-sm font-semibold">Name</th>
              <th class="px-4 py-3 text-left text-sm font-semibold">Urdu Name</th>
              <th class="px-4 py-3 text-left text-sm font-semibold">Code</th>
              <th class="px-4 py-3 text-left text-sm font-semibold">Type</th>
              <th class="px-4 py-3 text-left text-sm font-semibold">Parent</th>
              <th class="px-4 py-3 text-center text-sm font-semibold">Action</th>
            </tr>
          </thead>

          <tbody class="divide-y">
            <tr v-for="(item, index) in demographies" :key="item.id" class="hover:bg-gray-50">
              <td class="px-4 py-2">
                {{ (currentPage - 1) * perPage + index + 1 }}
              </td>
              <td class="px-4 py-2">{{ item.name }}</td>
              <td class="px-4 py-2 font-nastaleeq">{{ item.urdu_name || '-' }}</td>
              <td class="px-4 py-2">{{ item.code || '-' }}</td>
              <td class="px-4 py-2">{{ item.type }}</td>
              <td class="px-4 py-2">{{ item?.parent?.name || 'N/A' }}</td>

              <td class="px-4 py-2 text-center space-x-2">
                <router-link :to="`/demography/${currentType}/edit/${item.id}`"
                  class="bg-green-500 text-white px-2 py-1 rounded text-xs">
                  <i class="fa fa-pencil"></i>
                </router-link>
                <button @click="deleteDemography(item.id)" class="bg-red-500 text-white px-2 py-1 rounded text-xs">
                  <i class="fa fa-trash"></i>
                </button>
              </td>
            </tr>

            <tr v-if="demographies.length === 0">
              <td colspan="7" class="text-center py-6 text-gray-500">
                No data found
              </td>
            </tr>
          </tbody>
        </table>

        <ul v-if="!loading && lastPage > 1"
          class="inline-flex items-center space-x-1 rtl:space-x-reverse m-auto justify-end w-full mt-5 mb-3">

          <!-- Prev -->
          <button v-if="lastPage > 1" @click="fetchDemographies(currentPage - 1)" :disabled="currentPage === 1"
            class="px-3.5 py-1 rounded font-semibold bg-gray-200 hover:bg-blue-500 hover:text-white">
            Prev
          </button>

          <!-- Pages -->
          <li v-for="page in lastPage" :key="page" v-if="lastPage > 1">
            <button @click="fetchDemographies(page)" :class="[
              'px-3 py-1 rounded font-semibold transition',
              page === currentPage
                ? 'bg-blue-600 text-white'
                : 'bg-gray-200 text-gray-700 hover:bg-blue-500 hover:text-white'
            ]">
              {{ page }}
            </button>
          </li>

          <!-- Next -->
          <button v-if="lastPage > 1" @click="fetchDemographies(currentPage + 1)" :disabled="currentPage === lastPage"
            class="px-3.5 py-1 rounded font-semibold bg-gray-200 hover:bg-blue-500 hover:text-white">
            Next
          </button>

        </ul>
      </div>

    </div>




  </div>

</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { fetchList } from '@/services/listService'
import { useRoute } from 'vue-router'
import axios from 'axios'
import Swal from 'sweetalert2'

const route = useRoute()

const demographies = ref([])
const loading = ref(false)
const search = ref('')

const currentPage = ref(1)
const lastPage = ref(1)
const perPage = ref(15)

/* Current TYPE from route */
const currentType = computed(() =>
  route.params.type?.toUpperCase() || 'COUNTRY'
)

/* Label */
const currentTypeLabel = computed(() =>
  currentType.value.charAt(0) +
  currentType.value.slice(1).toLowerCase()
)

/* Fetch data */
const fetchDemographies = async (page = 1) => {
  loading.value = true
  try {
    const result = await fetchList('demographies', page, currentType.value)
    // const res = await axios.get(
    //   `/api/demographies?type=${currentType.value}&page=${page}`
    // )

    demographies.value = result.items
    currentPage.value = result.currentPage
    lastPage.value = result.lastPage
    perPage.value = result.perPage
  } finally {
    loading.value = false
  }
}

// /* Pagination */
// const goNextPage = () => {
//   if (currentPage.value < lastPage.value) {
//     fetchDemographies(currentPage.value + 1)
//   }
// }

// const goPrevPage = () => {
//   if (currentPage.value > 1) {
//     fetchDemographies(currentPage.value - 1)
//   }
// }

/* Delete */
const deleteDemography = async (id) => {
  const confirm = await Swal.fire({
    icon: 'warning',
    title: 'Are you sure?',
    showCancelButton: true,
    confirmButtonText: 'Delete'
  })

  if (!confirm.isConfirmed) return

  await axios.delete(`/api/demographies/${id}`)
  fetchDemographies(currentPage.value)

  Swal.fire('Deleted!', 'Record deleted successfully.', 'success')
}

// // Compute pages 
// const pagesToShow = computed(() => {
//   const pages = []
//   const total = totalPages.value
//   const current = currentPage.value
//   const maxButtons = 5 // Number of page

//   if (total <= 7) {
//     // Show all pages if total is small
//     for (let i = 1; i <= total; i++) {
//       pages.push({ type: 'page', number: i, key: i })
//     }
//   } else {
//     //  show first page
//     pages.push({ type: 'page', number: 1, key: 1 })

//     let start = Math.max(2, current - 1)
//     let end = Math.min(total - 1, current + 1)

//     if (start > 2) {
//       pages.push({ type: 'ellipsis', key: 'start-ellipsis' })
//     }

//     for (let i = start; i <= end; i++) {
//       pages.push({ type: 'page', number: i, key: i })
//     }

//     if (end < total - 1) {
//       pages.push({ type: 'ellipsis', key: 'end-ellipsis' })
//     }

//     // show last page
//     pages.push({ type: 'page', number: total, key: total })
//   }

//   return pages
// })


// TYPE change */
watch(currentType, () => {
  fetchDemographies(1)
})
 watch(search, () => {
  fetchDemographies(1)
})

onMounted(() => {
  fetchDemographies(1)
})
</script>
