<template>
  <div>
    <!-- Breadcrumb -->
    <div class="text-sm text-gray-600 mb-4">
      <router-link to="/admin/dashboard" class="text-gray-600 hover:underline">
       Dashboard
      </router-link>
             <i class="fa fa-angle-right"></i>

      <router-link to="/reviews" class="text-gray-600 hover:underline">
        Reviews
      </router-link>
     
            <i class="fa fa-angle-right"></i>

      <span class="font-semibold text-blue-600">Add Reviews</span>
    </div>
    <div class="max-w-7xl mx-auto bg-white rounded-lg shadow border p-6">

   
    <h2 class="text-xl mb-6 text-black p-2 rounded font-semibold">
      Add Reviews
    </h2>

    <!-- body -->
    <div class="space-y-6">

      <!-- service name -->
      <div>
        <label class="block text-sm font-medium mb-1"> Name</label>
            <input
              type="text"
              placeholder=" Name"
               class="form-input"
            />
      </div>

      <!-- description -->
      <div>
        <label class="text-sm font-medium">Description</label>
        <textarea
          rows="4"
          placeholder="Description"
          class="form-textarea"        ></textarea>
      </div>

      <!-- image  -->
      <div>
        <label class="text-sm font-medium">Upload Image</label>
        <input
          type="file"
          accept="image/"
          class="form-input"        />
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


    </div>
  </div>
</template>
