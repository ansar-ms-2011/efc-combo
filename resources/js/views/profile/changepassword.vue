<template>
  <div class="p-6 min-h-screen">
    <div class="panel  bg-white rounded-lg shadow p-5">

      <h5 class="font-semibold text-lg mb-5 flex items-center">
        <icon-lock class="w-5 h-5 ltr:mr-2 shrink-0" /> Change Password
      </h5>

      <form @submit.prevent="changePassword">

        <!-- Old Password -->
        <div class="mb-4 relative">
          <label class="block font-semibold mb-1">Old Password <span class="text-red-500">*</span></label>
          <input :type="showOld ? 'text' : 'password'" v-model="form.old_password"
                 placeholder="Enter old password"
                 class="w-full rounded py-2 border px-2 pr-10" />
          <span @click="showOld = !showOld"
                class="absolute right-2 top-9 cursor-pointer text-gray-500">
            <i :class="showOld ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
          </span>
        </div>

        <!-- New Password -->
        <div class="mb-4 relative">
          <label class="block font-semibold mb-1">New Password <span class="text-red-500">*</span></label>
          <input :type="showNew ? 'text' : 'password'" v-model="form.new_password"
                 placeholder="Enter new password"
                 class="w-full rounded py-2 border px-2 pr-10" />
          <span @click="showNew = !showNew"
                class="absolute right-2 top-9 cursor-pointer text-gray-500">
            <i :class="showNew ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
          </span>
        </div>

        <!-- Confirm Password -->
        <div class="mb-4 relative">
          <label class="block font-semibold mb-1">Confirm Password <span class="text-red-500">*</span></label>
          <input :type="showConfirm ? 'text' : 'password'" v-model="form.confirm_password"
                 placeholder="Confirm new password"
                 class="w-full rounded py-2 border px-2 pr-10" />
          <span @click="showConfirm = !showConfirm"
                class="absolute right-2 top-9 cursor-pointer text-gray-500">
            <i :class="showConfirm ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
          </span>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end gap-3 mt-6">
          <button type="submit"
                  class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded shadow">
            Update
          </button>

          <button type="button"
                  class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded shadow"
                  @click="resetForm">
            Reset
          </button>

          <router-link to="/"
                       class="px-6 py-2 bg-red-100 hover:bg-red-500 text-red-600 hover:text-white rounded shadow">
            Cancel
          </router-link>
        </div>

      </form>

    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import { useRouter } from 'vue-router'
import IconLock from '@/components/icon/icon-lock.vue';

const router = useRouter()

const form = ref({
  old_password: '',
  new_password: '',
  confirm_password: ''
})

// Show/Hide toggles
const showOld = ref(false)
const showNew = ref(false)
const showConfirm = ref(false)

// Reset form
const resetForm = () => {
  form.value.old_password = ''
  form.value.new_password = ''
  form.value.confirm_password = ''
  showOld.value = false
  showNew.value = false
  showConfirm.value = false
}

// Change password
const changePassword = async () => {
  if (!form.value.old_password || !form.value.new_password || !form.value.confirm_password) {
    Swal.fire({ icon: 'warning', title: 'Missing fields', text: 'Please fill all fields' })
    return
  }

  if (form.value.new_password !== form.value.confirm_password) {
    Swal.fire({ icon: 'error', title: 'Mismatch', text: 'New password and confirm password do not match' })
    return
  }

  try {
    await axios.put('/api/employee/change-password', {
      old_password: form.value.old_password,
      new_password: form.value.new_password,
      new_password_confirmation: form.value.confirm_password
    })

    Swal.fire({ icon: 'success', title: 'Password updated successfully', timer: 1500, showConfirmButton: false })
      .then(() => router.push('/'))

  } catch (err: any) {
    Swal.fire({ icon: 'error', title: 'Failed', text: err.response?.data?.message || 'Something went wrong' })
  }
}
</script>

<style scoped>
.panel {
  background-color: #fff;
  border-radius: 0.5rem;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}
</style>
