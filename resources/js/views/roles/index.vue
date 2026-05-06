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
        <span class="cursor-pointer">Role List</span>
      </div>

     <!-- <router-link to="/role/create" -->
   <!-- class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">--> 
    <!-- <i class="fa fa-plus"></i> Add New -->
     <!-- </router-link> -->
    </div>

    <!-- Card -->
    <div class="bg-white rounded-lg shadow">
      <!-- Header -->
      <div class="flex items-center gap-2 px-6 py-4 border-b rounded-t-lg">
        <!-- <i class="fa fa-cogs"></i> -->
        <h2 class="text-lg font-semibold">Role List</h2>

        <!-- search -->
        <div class="ltr:ml-auto rtl:mr-auto">
          <input v-model="search1" type="text" class="form-input" placeholder="Search..." />
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">
        <div v-if="loading" class="flex justify-center items-center py-20">
          <i class="fa fa-spinner fa-spin fa-2xl"></i>
        </div>
        <table v-else class="min-w-full border-collapse">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Sr.#</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Role Name</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Permissions</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Created Date</th>
              <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Action</th>
            </tr>
          </thead>

          <tbody class="divide-y">
            <tr v-for="(role, index) in filteredRoles" :key="role.id" class="hover:bg-gray-50">
              <td class="px-4 py-1 font-small">{{ index + 1 }}</td>

              <td class="px-4 py-1 font-small">
                {{ role.name }}
              </td>

              <td class="px-4 py-1 font-small">{{ getPermissionCount(role.permissions) }} permissions enabled</td>

              <td class="px-4 py-1 font-small text-gray-600">
                {{ $formatDMY(role.created_at) }}
              </td>

              <td class="px-4 py-1 font-small text-center">
                <div class="flex items-center justify-center gap-2 min-w-full" v-if="role.name !== 'Super Admin'">
                    <router-link :to="`/role/edit/${role.id}`"
                                 class="bg-green-500 hover:bg-green-600  text-white px-2 py-1 rounded text-xs">
                        <i class="fa fa-pencil"></i>
                    </router-link>

                    <button @click="deleteRole(role.id)"
                            class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

// State
const loading = ref(false)
const roles = ref([])
const search1 = ref("")

// Fetch Roles from API
const fetchRoles = async () => {
  loading.value = true
  try {
    const response = await axios.get('/api/roles', {
      params: {
        search: search1.value
      }
    })

    console.log("roles from api:")
    roles.value = response.data
  } catch (error) {
    console.error('Error fetching roles:', error)
  } finally {
    loading.value = false
  }
}

// Delete Role
const deleteRole = async (roleId) => {
  const result = await Swal.fire({
    title: 'Are you sure?',
    text: 'You won\'t be able to revert this!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, delete it!'
  })

  if (result.isConfirmed) {
    try {
      await axios.delete(`/api/roles/${roleId}`)
      Swal.fire('Deleted!', 'The role has been deleted.', 'success')
      // Refresh the roles list
      fetchRoles()
    } catch (error) {
      console.error('Error deleting role:', error)
      Swal.fire('Error!', 'There was an error deleting the role.', 'error')
    }
  }
}

// Simple Search Filter
const filteredRoles = computed(() => {
  return roles.value.filter(role =>
    role.name.toLowerCase().includes(search1.value.toLowerCase())
  )
})

// Get permission count
const getPermissionCount = (permissions) => {
  if (!permissions) return 0
  return Object.values(permissions).filter(Boolean).length
}

// Date Formatter (Optional)
const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}

watch(search1, () => {
  fetchRoles()
})

// Initial Load
onMounted(() => {
  fetchRoles()
})
</script>
