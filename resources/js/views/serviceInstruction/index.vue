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

        <span class="cursor-pointer">Service Instruction List</span>
      </div>

      <router-link to="/service-instruction/create"
        class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
        <i class="fa fa-plus"></i>
        Add New
      </router-link>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-lg shadow">
      <!-- Header -->
      <div class="flex items-center gap-2 px-6 py-4 border-b rounded-t-lg">
        <h2 class="text-lg font-semibold">Service Instruction List</h2>

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
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Service Name</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Instruction Title</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Instruction</th>
              <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Action</th>
            </tr>
          </thead>

          <tbody class="divide-y">
            <tr v-for="(instruction, index) in filteredInstructions" :key="instruction.id" class="hover:bg-gray-50">
              <td class="px-4 py-1 font-small w-[5%]"> {{ (currentPage - 1) * perPage + index + 1 }}</td>
              <td class="px-4 py-1 font-small w-[20%]">{{ instruction.service.name }}</td>
              <td class="px-4 py-1 font-small w-[20%]">{{ instruction.instruction_title }}</td>
              <td class="px-4 py-1 font-small w-[30%]">{{ instruction.instruction_description }}</td>
              <td class="px-4 py-1 font-small text-center space-x-2">
                <router-link :to="`/service-instruction/edit/${instruction.id}`"
                  class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs">
                  <i class="fa fa-pencil"></i>
                </router-link>

                <button class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs"
                  @click.prevent="deleteInstruction(instruction.id)">
                  <i class="fa fa-trash"></i>
                </button>
              </td>
            </tr>
            <tr v-if="filteredInstructions.length === 0">
              <td colspan="5" class="text-center py-4 text-gray-500">No records found</td>
            </tr>
          </tbody>
        </table>
        <ul class="inline-flex items-center space-x-1 rtl:space-x-reverse m-auto justify-end w-full mt-5 mb-3">

          <!-- Prev -->
          <button v-if="lastPage > 1" @click="fetchInstructions(currentPage - 1)" :disabled="currentPage === 1"
            class="px-3.5 py-1 rounded font-semibold bg-gray-200 hover:bg-blue-500 hover:text-white">
            Prev
          </button>

          <!-- Pages -->
          <li v-for="page in lastPage" :key="page" v-if="lastPage > 1">
            <button @click="fetchInstructions(page)" :class="[
              'px-3 py-1 rounded font-semibold transition',
              page === currentPage
                ? 'bg-blue-600 text-white'
                : 'bg-gray-200 text-gray-700 hover:bg-blue-500 hover:text-white'
            ]">
              {{ page }}
            </button>
          </li>

          <!-- Next -->
          <button v-if="lastPage > 1" @click="fetchInstructions(currentPage + 1)" :disabled="currentPage === lastPage"
            class="px-3.5 py-1 rounded font-semibold bg-gray-200 hover:bg-blue-500 hover:text-white">
            Next
          </button>

        </ul>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

const loading = ref(false)
const instructions = ref([])
const search = ref('')
const currentPage = ref(1)
const lastPage = ref(1)
const perPage = ref(15)

// Fetch all service instructions
// const fetchInstructions = async () => {
//   try {
//     const res = await axios.get('/api/serviceinstructions')
//     instructions.value = res.data.data || []
//   } catch (err) {
//     console.error('Failed to fetch instructions:', err)
//   }
// }

const fetchInstructions = async (page = 1) => {
  loading.value = true
  try {
    const res = await axios.get('/api/serviceinstructions', {
      params: { page, 
        search: search.value
       }
    })

    instructions.value = res.data.data.data // 👈 important
    currentPage.value = res.data.data.current_page
    lastPage.value = res.data.data.last_page
    perPage.value = res.data.data.per_page

  } catch (err) {
    console.error('Failed to fetch instructions:', err)
  } finally {
    loading.value = false
  }
}


// Delete instruction
const deleteInstruction = async (id) => {
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
    await axios.delete(`/api/serviceinstructions/${id}`)
    instructions.value = instructions.value.filter(i => i.id !== id)
    Swal.fire('Deleted!', 'Instruction deleted successfully.', 'success')
  } catch (err) {
    console.error(err)
    Swal.fire('Error', 'Delete failed.', 'error')
  }
}

// Computed for search filter
const filteredInstructions = computed(() => {
  if (!search.value) return instructions.value
  return instructions.value.filter(i =>
    i.instruction_title.toLowerCase().includes(search.value.toLowerCase()) ||
    i.instruction_description.toLowerCase().includes(search.value.toLowerCase()) ||
    i.service.name.toLowerCase().includes(search.value.toLowerCase())
  )
})

watch(search, () => {
  fetchInstructions(1)
})

onMounted(() => {
  fetchInstructions()
})
</script>
