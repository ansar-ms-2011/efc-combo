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

        <span class="cursor-pointer">Additional Charges List</span>
      </div>

      <router-link to="/additional-charges/create"
        class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
        <i class="fa fa-plus"></i>
        Add New
      </router-link>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-lg shadow">
      <!-- Header -->
      <div class="flex items-center gap-2 px-6 py-4 border-b rounded-t-lg">
        <h2 class="text-lg font-semibold">Additional Charges List</h2>

        <!-- search -->
        <div class="ltr:ml-auto rtl:mr-auto">
          <input v-model="search" type="text" class="form-input" placeholder="Search..." />
        </div>
      </div>

      <!-- Image Modal -->
      <div v-if="showImageModal"
        class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 transition-opacity">
        <!-- Modal Container -->
        <div class="relative max-w-lg w-full mx-4">

          <!-- Close Button (outside image) -->
          <button @click="showImageModal = false"
            class="absolute -top-6 right-0 bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded font-bold"
            title="Close">
            X
          </button>

          <!-- Image Box -->
          <div class="rounded-xl shadow-lg overflow-hidden  p-4">
            <img :src="modalImageUrl" class="w-full max-h-[70vh] object-contain rounded" alt="Notice Image" />
          </div>

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
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">User</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Additional Charge</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Start Date</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">End Date</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Image</th>
              <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Action</th>
            </tr>
          </thead>

          <tbody class="divide-y">
            <tr v-for="(additionalCharge, index) in filteredAdditionalCharges" :key="additionalCharge.id"
              class="hover:bg-gray-50">
              <td class="px-4 py-1 font-small w-[5%]"> {{ (currentPage - 1) * perPage + index + 1 }}</td>
              <td class="px-4 py-1 font-small w-[15%]">{{ additionalCharge.primary_user?.name ?? '-' }}</td>
              <td class="px-4 py-1 font-small w-[15%]">{{ additionalCharge.temporary_user?.name || '-' }}</td>
              <td class="px-4 py-1 font-small w-[15%]">{{ additionalCharge.start_date }}</td>
              <td class="px-4 py-1 font-small w-[15%]">{{ additionalCharge.end_date }}</td>
              <td class="px-4 py-1 font-small w-[15%]">{{ additionalCharge.status }}</td>
              <td class="px-4 py-1 font-small w-[5%] text-center">
                <button @click="openImageModal(additionalCharge.notice_image)"
                  class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs"
                  v-if="additionalCharge.notice_image">
                  View
                </button>
                <span v-else>-</span>
              </td>
              <td class="px-4 py-1 font-small text-center w-[20%] space-x-2">
                <router-link :to="`/additional-charges/edit/${additionalCharge.id}`"
                  class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs">
                  <i class="fa fa-pencil"></i>
                </router-link>

                <button class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs"
                  @click.prevent="deleteAdditionalCharge(additionalCharge.id)">
                  <i class="fa fa-trash"></i>
                </button>
              </td>
            </tr>
            <tr v-if="filteredAdditionalCharges.length === 0">
              <td colspan="5" class="text-center py-4 text-gray-500">No records found</td>
            </tr>
          </tbody>
        </table>
        <ul class="inline-flex items-center space-x-1 rtl:space-x-reverse m-auto justify-end w-full mt-5 mb-3">

          <!-- Prev -->
          <button v-if="lastPage > 1" @click="fetchAdditionalCharges(currentPage - 1)" :disabled="currentPage === 1"
            class="px-3.5 py-1 rounded font-semibold bg-gray-200 hover:bg-blue-500 hover:text-white">
            Prev
          </button>

          <!-- Pages -->
          <li v-for="page in lastPage" :key="page" v-if="lastPage > 1">
            <button @click="fetchAdditionalCharges(page)" :class="[
              'px-3 py-1 rounded font-semibold transition',
              page === currentPage
                ? 'bg-blue-600 text-white'
                : 'bg-gray-200 text-gray-700 hover:bg-blue-500 hover:text-white'
            ]">
              {{ page }}
            </button>
          </li>

          <!-- Next -->
          <button v-if="lastPage > 1" @click="fetchAdditionalCharges(currentPage + 1)"
            :disabled="currentPage === lastPage"
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
const additionalCharges = ref([])
const search = ref('')
const currentPage = ref(1)
const lastPage = ref(1)
const perPage = ref(15)

const fetchAdditionalCharges = async (page = 1) => {
  loading.value = true
  try {
    const res = await axios.get('/api/additional-charges', {
      params: {
        page,
        search: search.value
      }
    })

    additionalCharges.value = res.data.data.data // 👈 important
    currentPage.value = res.data.data.current_page
    lastPage.value = res.data.data.last_page
    perPage.value = res.data.data.per_page

  } catch (err) {
    console.error('Failed to fetch additional charges:', err)
  } finally {
    loading.value = false
  }
}

// Delete instruction
const deleteAdditionalCharge = async (id) => {
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
    await axios.delete(`/api/additional-charges/${id}`)
    additionalCharges.value = additionalCharges.value.filter(i => i.id !== id)
    Swal.fire('Deleted!', 'Additional charge deleted successfully.', 'success')
  } catch (err) {
    console.error(err)
    Swal.fire('Error', 'Delete failed.', 'error')
  }
}

// Computed for search filter
const filteredAdditionalCharges = computed(() => {
  if (!search.value) return additionalCharges.value
  return additionalCharges.value.filter(i =>
    i.additional_charge.toLowerCase().includes(search.value.toLowerCase()) ||
    i.service.name.toLowerCase().includes(search.value.toLowerCase())
  )
})

const showImageModal = ref(false)
const modalImageUrl = ref('')

const openImageModal = (imagePath) => {
  modalImageUrl.value = `http://localhost:8000/storage/${encodeURIComponent(imagePath)}`
  showImageModal.value = true
}

watch(search, () => {
  fetchAdditionalCharges(1)
})

onMounted(() => {
  fetchAdditionalCharges()
})
</script>
