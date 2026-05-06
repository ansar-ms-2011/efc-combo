<template>
  <div class="p-6 min-h-screen">
    <!-- Breadcrumb + Button -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center text-sm text-gray-600 space-x-2">
        <i class="fa fa-dashboard"></i>

        <router-link to="/admin/dashboard" class="cursor-pointer">
          Dashboard
        </router-link>

        <i class="fa fa-angle-right"></i>

        <span class="cursor-pointer">Service List</span>
      </div>

      <router-link to="/services/create"
        class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
        <i class="fa fa-plus"></i>
        Add New
      </router-link>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-lg shadow">
      <!-- Header -->
      <div class="flex items-center gap-2 px-6 py-4 border-b rounded-t-lg">
        <h2 class="text-lg font-semibold">Services List</h2>

        <!-- search -->
        <div class="ltr:ml-auto rtl:mr-auto">
          <input v-model="search1" type="text" class="form-input" placeholder="Search..." />
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
    <th class="px-4 py-3 w-[60px]">Sr.#</th>
    <th class="px-4 py-3 w-[200px]">Service Name</th>
    <th class="px-4 py-3 w-[160px]">Department</th>
    <!-- <th class="px-4 py-3 w-[80px]">Icon</th> -->
    <th class="px-4 py-3 w-[120px]">Price</th>
    <th class="px-4 py-3 w-[120px]">Days</th>
    <!-- <th class="px-4 py-3 w-[180px]">Center</th>
    <th class="px-4 py-3 w-[180px]">User</th> -->
    <th class="px-4 py-3 w-[120px] text-center">Action</th>
  </tr>
</thead>


         <tbody class="divide-y">
  <tr v-for="(service, index) in services" :key="service.id" class="hover:bg-gray-50">

    <td class="px-4 py-2 w-[60px]">
      {{ (currentPage - 1) * perPage + index + 1 }}
    </td>

    <td class="px-4 py-2 w-[200px] font-medium">
      {{ service.name }}
    </td>

    <td class="px-4 py-2 w-[160px] text-gray-600">
      {{ service.department.name }}
    </td>

    <!-- <td class="px-4 py-2 w-[80px] text-center">
      <i :class="`fas ${service.service_icon}`"></i>
    </td> -->

    <td class="px-4 py-2 w-[120px]">
      {{ service.price }}
    </td>

    <td class="px-4 py-2 w-[120px]">
      {{ service.no_of_days }}
    </td>

    <!-- <td class="px-4 py-2 w-[180px]">
  <div class="flex flex-wrap gap-1" v-if="service.service_centers?.length">
    <span
      v-for="sc in service.service_centers"
      :key="sc.id"
       class="badge badge-outline-success">
      {{ sc.center?.name }}
    </span>
  </div>
  <span v-else class="text-gray-400">-</span>
</td>


    <td class="px-4 py-2 w-[180px]">
  <div
    class="flex flex-wrap gap-1"
    v-if="service.service_centers?.length">

    <span
      v-for="u in service.service_centers.flatMap(sc => sc.users)"
      :key="u.id"
       class="badge badge-outline-success">
      {{ u.user?.name }}
    </span>

  </div>
  <span v-else class="text-gray-400">-</span>
</td> -->


    <td class="px-4 py-2 w-[120px] text-center space-x-2">
      <router-link
        :to="`/services/edit/${service.id}`"
        class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs">
        <i class="fa fa-pencil"></i>
      </router-link>

      <button
        class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs"
        @click.prevent="deleteService(service.id)">
        <i class="fa fa-trash"></i>
      </button>
    </td>

  </tr>
</tbody>

        </table>
        <ul class="inline-flex items-center space-x-1 rtl:space-x-reverse m-auto justify-end w-full mt-5 mb-3">

          <!-- Prev -->
          <button v-if="lastPage > 1" @click="fetchServices(currentPage - 1)" :disabled="currentPage === 1"
            class="px-3.5 py-1 rounded font-semibold bg-gray-200 hover:bg-blue-500 hover:text-white">
            Prev
          </button>

          <!-- Pages -->
          <li v-for="page in lastPage" :key="page" v-if="lastPage > 1">
            <button @click="fetchServices(page)" :class="[
              'px-3 py-1 rounded font-semibold transition',
              page === currentPage
                ? 'bg-blue-600 text-white'
                : 'bg-gray-200 text-gray-700 hover:bg-blue-500 hover:text-white'
            ]">
              {{ page }}
            </button>
          </li>

          <!-- Next -->
          <button v-if="lastPage > 1" @click="fetchServices(currentPage + 1)" :disabled="currentPage === lastPage"
            class="px-3.5 py-1 rounded font-semibold bg-gray-200 hover:bg-blue-500 hover:text-white">
            Next
          </button>

        </ul>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

// const search1 = ref('')
const loading = ref(false)

const search1 = ref('')
const services = ref([])
const currentPage = ref(1)
const lastPage = ref(1)
const perPage = ref(15)

const fetchServices = async (page = 1) => {
  loading.value = true

  try {
    const res = await axios.get(`/api/services`, {
      params: { page,
        search: search1.value
       }
    })

    services.value = res.data.data.data || []
    currentPage.value = res.data.data.current_page || 1
    lastPage.value = res.data.data.last_page || 1
    perPage.value = res.data.data.per_page || 15
  } catch (err) {
    console.error('Failed to fetch services:', err)

  } finally {
    loading.value = false
  }

}

// const deleteService = async (id) => {
//   if (!confirm('Are you sure you want to delete this service?')) return

//   try {
//     await axios.delete(`/api/services/${id}`)
//     alert('Service deleted successfully')

//     // list refresh
//     fetchServices()

//   } catch (error) {
//     console.error(error)
//     alert('Delete failed')
//   }
// }

// ====== DELETE TYPE ======
const deleteService = async (id) => {
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
    await axios.delete(`/api/services/${id}`)
    services.value = services.value.filter(s => s.id !== id)
    Swal.fire('Deleted!', 'Record deleted successfully.', 'success')
  } catch (error) {
    console.error(error)
    Swal.fire('Error', 'Delete failed.', 'error')
  }
}

watch(search1, () => {
  fetchServices(1)
})  

onMounted(() => {
  fetchServices()
})
</script>
