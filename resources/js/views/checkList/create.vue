<template>
  <div>
    <!-- Breadcrumb -->
    <div class="text-sm text-gray-600 mb-4 flex items-center">
      <router-link to="/admin/dashboard" class="hover:underline">
        Dashboard
      </router-link>
      <i class="fa fa-angle-right"></i>

      <router-link to="/checkList" class="hover:underline">
        Check List
      </router-link>
      <i class="fa fa-angle-right"></i>

      <router-link to="/checkList/create" class="hover:underline text-blue-600">
        Add Check List
      </router-link>
    </div>

    <!-- Card -->
    <div class="max-w-7xl mx-auto bg-white rounded-lg shadow border p-6">

      <h2 class="text-xl mb-6 font-semibold">
        Add Check List
      </h2>

      <div class="space-y-6">

        <!-- FIRST ROW (Service, Document, Price) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

          <div>
            <label class="text-sm font-medium">Service Name</label>

            <multiselect v-model="selectedService" :options="services" track-by="id" label="name"
              placeholder="All Services" :multiple="false" :searchable="true" :clear-on-select="true"
              :close-on-select="true" :show-labels="false" class="custom-multiselect">
            </multiselect>
          </div>


          <div>
            <label class="text-sm font-medium">Document Name</label>
            <input type="text" placeholder="Document Name"  class="form-input" />
          </div>

          <!-- Price -->
          <div>
            <label class="text-sm font-medium">Price</label>
            <input type="text" placeholder="Rs. Amount"  class="form-input" />
          </div>

        </div>

        <!-- Description -->
        <div>
          <label class="text-sm font-medium">Description</label>
          <textarea rows="4" placeholder="Description" class="form-textarea"></textarea>
        </div>

        <!-- Required / Original -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

          <div class="flex border">
            <div class="w-1/2 px-4 py-3 text-sm">Required</div>
            <div class="w-1/2 bg-gray-100 flex items-center justify-center gap-6">
              <label class="flex items-center gap-1">
                Yes <input type="radio" name="required">
              </label>
              <label class="flex items-center gap-1">
                No <input type="radio" name="required">
              </label>
            </div>
          </div>

          <div class="flex border">
            <div class="w-1/2 px-4 py-3 text-sm">Need Original?</div>
            <div class="w-1/2 bg-gray-100 flex items-center justify-center gap-6">
              <label class="flex items-center gap-1">
                Yes <input type="radio" name="original">
              </label>
              <label class="flex items-center gap-1">
                No <input type="radio" name="original">
              </label>
            </div>
          </div>

        </div>

        <!-- Attestation / Photocopy -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

          <div class="flex border">
            <div class="w-1/2 px-4 py-3 text-sm">Need attestation?</div>
            <div class="w-1/2 bg-gray-100 flex items-center justify-center gap-6">
              <label class="flex items-center gap-1">
                Yes <input type="radio" name="attestation">
              </label>
              <label class="flex items-center gap-1">
                No <input type="radio" name="attestation">
              </label>
            </div>
          </div>

          <div class="flex border">
            <div class="w-1/2 px-4 py-3 text-sm">Need photocopy?</div>
            <div class="w-1/2 bg-gray-100 flex items-center justify-center gap-6">
              <label class="flex items-center gap-1">
                Yes <input type="radio" name="photocopy">
              </label>
              <label class="flex items-center gap-1">
                No <input type="radio" name="photocopy">
              </label>
            </div>
          </div>

        </div>

        <!-- LAST ROW (New, Duplicate, Renewal) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

          <div class="flex border">
            <div class="w-12 bg-gray-100 flex items-center justify-center">
              <input type="checkbox">
            </div>
            <div class="px-4 py-3">New</div>
          </div>

          <div class="flex border">
            <div class="w-12 bg-gray-100 flex items-center justify-center">
              <input type="checkbox">
            </div>
            <div class="px-4 py-3">Duplicate</div>
          </div>

          <div class="flex border">
            <div class="w-12 bg-gray-100 flex items-center justify-center">
              <input type="checkbox">
            </div>
            <div class="px-4 py-3">Renewal</div>
          </div>

        </div>

        <!-- Buttons -->
        <div class="flex justify-end  gap-3 mt-6">
          <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded shadow">
            Save
          </button>
          <button type="button" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded shadow">
            Reset
          </button>
          <router-link to="/city"
            class="px-6 py-2 bg-red-100 hover:bg-red-500 text-red-600 hover:text-white rounded shadow">
            Cancel
          </router-link>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const selectedService = ref(null)

const services = ref([
  { id: 0, name: 'All Services' },
  { id: 1, name: 'Birth Certificate' },
  { id: 2, name: 'Death Certificate' },
  { id: 3, name: 'Arms License' },
  { id: 4, name: 'State Subject Certificate' },
  { id: 5, name: 'Domicile' }
])


</script>