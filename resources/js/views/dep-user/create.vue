  <template>
  <div>
    <!-- Breadcrumb -->
      <div class="text-sm text-gray-600 mb-4">
        <router-link to="/admin/dashboard" class="text-grey-600 hover:underline">
        Dashboard
        </router-link>
         <i class="fa fa-angle-right"></i>
         <router-link to="/dep-user" class="text-grey-600 hover:underline">
        Department -User
        </router-link>
       
         <i class="fa fa-angle-right"></i>
         <router-link to="/dep-user" class="text-grey-600 hover:underline">
         Add-User
        </router-link>

      </div>
    <div class="max-w-7xl mx-auto bg-white rounded-lg shadow border p-6">

  
      <!-- Heading -->
<h2 class="text-xl  mb-6 text-black p-2 rounded font-semibold">
  Add User
</h2>
      <!-- Form -->
     <form class="space-y-6">

  <!-- ROW 1 : 4 Columns -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <div>
      <label class="block text-sm font-medium mb-1">Pre</label>
      <select class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
        <option>Mr</option>
        <option>Miss</option>
      </select>
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">First Name</label>
      <input
        type="text"
        placeholder="First Name"
        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500"
      />
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Last Name</label>
      <input
        type="text"
        placeholder="Last Name"
        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500"
      />
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Email</label>
      <input
        type="email"
        placeholder="Email"
        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500"
      />
    </div>
  </div>

  <!-- ROW 2 : 4 Columns -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <div>
      <label class="block text-sm font-medium mb-1">CNIC</label>
      <input
        type="number"
        placeholder="CNIC"
        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500"
      />
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Password</label>
      <input
        type="password"
        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500"
      />
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Confirm Password</label>
      <input
        type="password"
        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500"
      />
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">User Type</label>
      <select class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
        <option>Select User Type</option>
        <option>AC</option>
        <option>DC</option>
        <option>Commissioner</option>
        <option>AC Rural</option>
        <option>Revenue Officer</option>
      </select>
    </div>
  </div>

  <!-- ROW 3 : 1 Column -->
  <div class="grid grid-cols-1 gap-4">
    <div>
      <label class="block text-sm font-medium mb-1">Region</label>
      <select class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
        <option>Select Region</option>
        <option>Mirpur</option>
        <option>Muzaffarabad</option>
        <option>Poonch</option>
      </select>
    </div>
  </div>

  <!-- Buttons -->
 <div class="flex justify-end  gap-3 mt-6">
              <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded shadow">
                Save
              </button>
              <button type="button" 
                class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded shadow">
                Reset
              </button>
              <router-link to="/city"
                class="px-6 py-2 bg-red-100 hover:bg-red-500 text-red-600 hover:text-white rounded shadow">
                Cancel
              </router-link>
            </div>

</form>

    </div>
  </div>
</template>

