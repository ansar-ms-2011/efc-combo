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

        <span class="cursor-pointer">Template List</span>
      </div>

      <router-link to="/templates/create"
        class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
        <i class="fa fa-plus"></i>
        Add New
      </router-link>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-lg shadow">

      <!-- Header -->
      <div class="flex items-center gap-2 px-6 py-4 border-b">
        <h2 class="text-lg font-semibold">Template List</h2>

        <div class="ml-auto">
          <input v-model="search" type="text" class="form-input" placeholder="Search..." />
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">

        <div v-if="loading" class="flex justify-center items-center py-20">
          <span class="animate-spin border-8 border-gray-200 border-l-blue-500 rounded-full w-14 h-14"></span>
        </div>

        <table v-else class="min-w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 w-[60px]">Sr.#</th>
              <th class="px-4 py-3">Name</th>
              <th class="px-4 py-3">Content</th>
              <th class="px-4 py-3 text-center">Action</th>
            </tr>
          </thead>

          <tbody class="divide-y">
            <tr v-for="(item, index) in templates" :key="item.id">

              <td class="px-4 py-2">
                {{ (currentPage - 1) * perPage + index + 1 }}
              </td>

              <td class="px-4 py-2 font-medium">
                {{ item.name }}
              </td>

              <td class="px-4 py-2 text-gray-600 truncate max-w-[300px]">
                <!-- <div v-html="item.content"></div> -->
             <span dir="rtl" class="block text-right font-nastaleeq">
    {{ truncateContent(item.content, 50) }}
  </span>
              </td>


              <td class="px-4 py-2 text-center space-x-2">

                <router-link
                  :to="`/templates/${item.id}/edit`"
                  class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs">
                  <i class="fa fa-pencil"></i>
                </router-link>

                <button
                  class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs"
                  @click="deleteTemplate(item.id)">
                  <i class="fa fa-trash"></i>
                </button>

              </td>

            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <!-- <div class="flex justify-end p-4 gap-2">
          <button @click="fetchTemplates(currentPage - 1)" :disabled="currentPage === 1"
            class="px-3 py-1 bg-gray-200 rounded">Prev</button>

          <span class="px-3 py-1">{{ currentPage }} / {{ lastPage }}</span>

          <button @click="fetchTemplates(currentPage + 1)" :disabled="currentPage === lastPage"
            class="px-3 py-1 bg-gray-200 rounded">Next</button>
        </div> -->

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

const templates = ref([])
const loading = ref(false)
const search = ref('')

const currentPage = ref(1)
const lastPage = ref(1)
const perPage = ref(10)

const fetchTemplates = async (page = 1) => {
  loading.value = true

  try {
    const res = await axios.get('/api/templates', {
      params: {
        
        search: search.value
      }
    })
    console.log('Template data:', res.data)

    templates.value = res.data.data || []
    // currentPage.value = res.data.current_page || 1
    // lastPage.value = res.data.last_page || 1
    // perPage.value = res.data.per_page || 10

  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const truncateContent = (html, limit = 50) => {
  if (!html) return ''

  // HTML tags remove karo
  const text = html.replace(/<[^>]*>/g, ' ')

  const words = text.split(/\s+/)

  if (words.length <= limit) return text

  return words.slice(0, limit).join(' ') + ' ...'
} 

const deleteTemplate = async (id) => {
  const confirm = await Swal.fire({
    icon: 'warning',
    title: 'Are you sure?',
    showCancelButton: true
  })

  if (!confirm.isConfirmed) return

  try {
    await axios.delete(`/api/templates/${id}`)
    templates.value = templates.value.filter(t => t.id !== id)

    Swal.fire('Deleted!', 'Template deleted.', 'success')
  } catch (e) {
    Swal.fire('Error', 'Delete failed.', 'error')
  }
}

watch(search, () => {
  fetchTemplates(1)
})

onMounted(() => {
  fetchTemplates()
})
</script>
