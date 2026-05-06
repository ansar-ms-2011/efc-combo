<template>
  <div class="p-6 min-h-screen">

    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center text-sm text-gray-600 space-x-2">
        <i class="fa fa-dashboard"></i>
        <span class="cursor-pointer">Dashboard</span>
        <i class="fa fa-angle-right"></i>
        <span class="cursor-pointer">Applications</span>
      </div>

      <router-link to="applications/create"
        class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
        <i class="fa fa-plus"></i> Add New
      </router-link>
    </div>

    <div class="max-w-7xl mx-auto bg-white rounded-lg shadow border p-6">

      <!-- Filter Button -->
      <div class="flex items-center justify-between mb-4 border-b">
        <h2 class="text-xl font-semibold mb-3">Applications for Approval</h2>

        <button @click="showFilter = !showFilter"
          class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 mb-3 rounded transition">
          <i :class="showFilter ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
          {{ showFilter ? 'Hide Filters' : 'Show Filters' }}
        </button>
      </div>

      <!-- Filter section -->
      <transition enter-active-class="transition-all duration-300 ease-out"
        leave-active-class="transition-all duration-200 ease-in" enter-from-class="opacity-0 -translate-y-2"
        leave-to-class="opacity-0 -translate-y-2">
        <div v-if="showFilter" class="bg-white p-4 mb-4 rounded shadow flex gap-4">

          <select class="border px-3 py-2 rounded w-40">
            <option>Token</option>
            <option>CNIC</option>
            <option>Name</option>
            <option>Missal No</option>

          </select>

          <input type="text" placeholder="Search for..." class="border px-3 py-2 rounded w-64" />

          <select class="border px-3 py-2 rounded w-56">
            <option>All Administrative Area</option>
            <option>Region Muzaffarabad</option>
            <option>Region Mirpur</option>
            <option> Region Poonch</option>

          </select>

          <select class="border px-3 py-2 rounded w-40">
            <option>All Services</option>
            <option>Birth Certificate</option>
            <option>Death Certificate</option>
            <option>Arms License</option>
            <option>State Subject Certificate</option>
            <option>Domicile</option>

          </select>

          <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded flex items-center gap-1">
            <i class="fa fa-search"></i>
            Search
          </button>
        </div>
      </transition>

      <!-- Table -->
      <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full border text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="border px-3 py-2">Token</th>
              <th class="border px-3 py-2">Missal</th>
              <th class="border px-3 py-2">Service</th>
              <th class="border px-3 py-2">Type</th>
              <th class="border px-3 py-2">Name</th>
              <th class="border px-3 py-2">CNIC</th>
              <th class="border px-3 py-2">Date & Time</th>
              <th class="border px-3 py-2 text-center">Action</th>
            </tr>
          </thead>

          <tbody>
            <!-- Loading State -->
            <tr v-if="loading">
              <td colspan="8" class="border px-3 py-4 text-center">
                <div class="flex items-center justify-center gap-2">
                  <i class="fa fa-spinner fa-spin text-blue-600"></i>
                  <span>Loading applications...</span>
                </div>
              </td>
            </tr>

            <!-- No Data State -->
            <tr v-else-if="applications.length === 0">
              <td colspan="8" class="border px-3 py-8 text-center text-gray-500">
                <div class="flex flex-col items-center gap-2">
                  <i class="fa fa-folder-open text-3xl text-gray-400"></i>
                  <span>No applications found</span>
                </div>
              </td>
            </tr>

            <!-- Data Rows - Dynamic -->
            <tr v-for="app in applications" :key="app.id" class="hover:bg-gray-50">
              <td class="border px-3 py-2">{{ app.qmatic_token || 'N/A' }}</td>
              <td class="border px-3 py-2">{{ app.missalno || '-' }}</td>
              <td class="border px-3 py-2">
                <span v-if="app.certificate_type === 'domicile'">Domicile</span>
                <span v-if="app.certificate_type === 'state'">State Subject</span>
                <span v-if="app.certificate_type === 'both'">Domicile/State Subject</span>
              </td>
              <td class="border px-3 py-2">
                <span v-if="app.application_type_id === 1">New</span>
                <span v-if="app.application_type_id === 2">Duplicate</span>
                <span v-if="app.application_type_id === 3">Renewal</span>
              </td>
              <td class="border px-3 py-2">{{ app.first_name }}</td>
              <td class="border px-3 py-2">{{ formatCNIC(app.cnic) }}</td>
              <td class="border px-3 py-2">{{ formatDateTime(app.created_at) }}</td>

              <!--  BUTTONS -->
              <td class="border px-2 py-2">
                <div class="flex justify-center gap-2">

                  <button @click="approveApplication(app)" class="action-btn bg-emerald-600 hover:bg-emerald-700">
                    <span>Approvel</span>
                  </button>

                  <router-link :to="{ name: 'applications.edit', params: { id: app.id } }"
                    class="action-btn bg-blue-600 hover:bg-blue-700" title="Edit Application">
                    <i class="fa fa-edit"></i>
                    <span>Edit</span>
                  </router-link>

                  <button @click="printApplication(app)" class="action-btn bg-blue-500 hover:bg-blue-600">
                    <i class="fa fa-print"></i>
                  </button>

                  <button @click="viewApplication(app)" class="action-btn bg-green-500 hover:bg-green-600">
                    <i class="fa fa-eye"></i>
                  </button>

                  <button @click="printFinal(app)" class="action-btn bg-emerald-600 hover:bg-emerald-700">
                    <i class="fa fa-print"></i>
                    <span>Final</span>
                  </button>

                  <button @click="assignDiary(app)" class="action-btn bg-red-500 hover:bg-red-600">
                    <span>Diary No.</span>
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
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const showFilter = ref(true)
const loading = ref(false)
const applications = ref([])
const router = useRouter()

// Fetch applications on component mount
onMounted(async () => {
  loading.value = true
  try {
    const response = await axios.get('/api/applications')
    applications.value = response.data.data || []
  } catch (error) {
    console.error('Error fetching applications:', error)
    // You can show an error message here if needed
  } finally {
    loading.value = false
  }
})

// Helper function to format CNIC
const formatCNIC = (cnic) => {
  if (!cnic) return '-'
  // Format: XXXXX-XXXXXXX-X
  const cleaned = cnic.replace(/\D/g, '')
  if (cleaned.length === 13) {
    return `${cleaned.substring(0, 5)}-${cleaned.substring(5, 12)}-${cleaned.substring(12)}`
  }
  return cnic
}

// Helper function to format date and time
const formatDateTime = (dateString) => {
  if (!dateString) return '-'
  const date = new Date(dateString)

  // Format: DD-MM-YYYY HH:MM:SS
  const day = String(date.getDate()).padStart(2, '0')
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const year = date.getFullYear()
  const hours = String(date.getHours()).padStart(2, '0')
  const minutes = String(date.getMinutes()).padStart(2, '0')
  const seconds = String(date.getSeconds()).padStart(2, '0')

  return `${day}-${month}-${year} ${hours}:${minutes}:${seconds}`
}

// Action methods (you can implement these as needed)
const approveApplication = (app) => {
  console.log('Approve application:', app)
  alert(`Approve application ${app.token}`)
}

const printApplication = (app) => {
  console.log('Print application:', app)
  // Navigate to admin form with the application ID
  router.push(`/form/${app.id}`)
}

const viewApplication = (app) => {
  console.log('View application:', app)
  // You can navigate to view page
  // router.push(`applications/${app.id}`)
  alert(`View application ${app.token}`)
}

const printFinal = (app) => {
  console.log('Print final:', app)
  window.open(`/print/final/${app.id}`, '_blank')
}

const assignDiary = (app) => {
  console.log('Assign diary number:', app)
  const diaryNo = prompt('Enter diary number for application ' + app.token)
  if (diaryNo) {
    alert(`Diary number ${diaryNo} assigned to application ${app.token}`)
  }
}
</script>

<style>
.action-btn {
  width: 60px;
  height: 29px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 4px;
  color: white;
  font-size: 11px;
  border-radius: 4px;
  transition: 0.2s;
}
</style>