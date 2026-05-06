<template>
  <div>
    <div class="flex items-center justify-between mb-5 text-sm text-gray-600">
      <div class="flex gap-2">
        <router-link to="/admin/dashboard" class="hover:underline text-gray-600"> Dashboard </router-link>
        <i class="fa fa-angle-right"></i>

        <router-link to="/admin/dep-user" class="hover:underline text-gray-600"> Department User </router-link>
      </div>

      <router-link to="/dep-user/create" class="px-2 py-2 bg-green-600 text-white rounded hover:bg-green-700 shadow transition-all"> + Add User </router-link>
    </div>

    <div class="max-w-7xl mx-auto bg-white rounded-lg shadow border p-0">
      <!-- Header -->
      <div class="flex justify-between items-center mb-0 bg-blue-500 rounded-t-lg rounded-b-lg">
        <h2 class="text-xl text-white p-2">User List</h2>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full border border-gray-300 text-sm">
          <thead class="bg-blue-600 text-black">
            <tr>
              <th class="border px-3 py-2 text-left">Sr.#</th>
              <th class="border px-3 py-2 text-left">Email</th>
              <th class="border px-3 py-2 text-left">Name</th>
              <th class="border px-3 py-2 text-left">NIC</th>
              <th class="border px-3 py-2 text-left">Created</th>
              <th class="border px-3 py-2 text-left">Status</th>
              <th class="border px-3 py-2 text-left">User Type</th>
              <th class="border px-3 py-2 text-center">Action</th>
            </tr>
          </thead>

          <tbody>
            <tr class="hover:bg-gray-50">
              <td class="border px-3 py-2">1</td>
              <td class="border px-3 py-2">dc.mzd@gmail.com</td>
              <td class="border px-3 py-2">DC Office Muzaffarabad</td>
              <td class="border px-3 py-2">00000-0000000-0</td>
              <td class="border px-3 py-2">14-06-2019 05:52:20</td>
              <td class="border px-3 py-2">Active</td>
              <td class="border px-3 py-2">DC</td>
              <td class="border px-3 py-2">
                <router-link class="text-white bg-green-600">
                  <i class="fa-solid fa-pencil"></i>
                </router-link>

                <button type="button" class="text-danger bg-red">
                  <i class="fa-utility fa-semibold fa-trash"></i>
                </button>
              </td>
            </tr>

            <!-- row2 -->
            <tr class="hover:bg-gray-50">
              <td class="border px-3 py-2">2</td>
              <td class="border px-3 py-2">dc.poonch@gmail.com</td>
              <td class="border px-3 py-2">DC Poonch</td>
              <td class="border px-3 py-2">55555-5555555-5</td>
              <td class="border px-3 py-2">14-11-2019 06:27:20</td>
              <td class="border px-3 py-2">Active</td>
              <td class="border px-3 py-2">Dc</td>
              <td class="border px-3 py-2 flex gap-2">
                <router-link class="text-white bg-green-600 px-1 py-1 rounded">
                  <i class="fa-solid fa-pencil"></i>
                </router-link>
                <button type="button" class="bg-red-600 text-white px-1 py-1 rounded hover:bg-red-700 transition">
                  <i class="fa-solid fa-trash"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
<script setup>
</script>


   
