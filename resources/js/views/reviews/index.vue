<template>
  <div class="p-6 min-h-screen">
    <!-- Breadcrumb + Button -->
    <div class="flex items-center justify-between mb-5 text-sm text-gray-600">
      <div class="flex items-center text-sm text-gray-600 space-x-2">
        <i class="fa fa-dashboard"></i>
        <router-link to="/" class="cursor-pointer">Dashboard</router-link>
        <i class="fa fa-angle-right"></i>
        <span class="cursor-pointer">Review List</span>
      </div>

      <router-link to="/reviews/create"
        class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
        <i class="fa fa-plus"></i> Add New
      </router-link>
    </div>

    <!-- Card -->
    <div class="max-w-7xl mx-auto bg-white rounded-lg shadow border">
      <!-- Header -->
      <div class="flex items-center gap-2 px-6 py-4 border-b rounded-t-lg">
        <h2 class="text-lg font-semibold">Review List</h2>
        <div class="ltr:ml-auto rtl:mr-auto">
          <input v-model="search" type="text" class="form-input" placeholder="Search..." />
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="w-full border border-gray-300 text-sm">
          <thead class="bg-blue-600 text-black">
            <tr>
              <th class="border px-3 py-2 text-left">Sr.#</th>
              <th class="border px-3 py-2 text-left">Name</th>
              <th class="border px-3 py-2 text-left">Description</th>
              <th class="border px-3 py-2 text-left">Created Date</th>
              <th class="border px-3 py-2 text-center">Action</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(review, index) in filteredReviews" :key="review.id" class="hover:bg-gray-50">
              <td class="border px-3 py-1 font-small">{{ index + 1 }}</td>

              <!-- Name full show -->
              <td class="border px-3 py-1 font-small">
                <div class="font-medium">{{ review.name }}</div>
                <div class="text-xs text-gray-400">{{ review.postion }}</div>


              </td>

              <!-- Description truncated -->
              <td class="border px-3 py-1 font-small max-w-xs truncate" title="Click to see full">
                {{ review.description }}
              </td>

              <td class="border px-3 py-1 font-small">{{ review.created_at }}</td>

              <td class="px-3 py-1 font-small  text-center space-x-2  ">
                <router-link :to="`/reviews/edit/${review.id}`"
                  class="text-white bg-green-600 px-2 py-1 rounded hover:bg-green-700 transition">
                  <i class="fa-solid fa-pencil"></i>
                </router-link>
                <button @click="deleteReview(review.id)"
                  class="bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700 transition">
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
import { ref, computed } from 'vue'

const reviews = ref([
  {
    id: 1,
    name: "Dr. Sohail Habib Tajik ",
    postion: "Ex-Inspector General of Police AJ&K",
    description:
      "I personally visited this E-Facilitation center established by AJK IT Board. After visiting and knowing about the purpose of this project I am really impressed by this initiative taken by the AJK IT Board. I can foresee that it will help the people of the state in the long run and I ensure my support to expand this project from one center to many all over the state. So that we can meet and compete with the modern digital world.",
    created_at: "01-01-2026 10:30",
  },
  {
    id: 2,
    name: "Dr. Khalid Rafique ",
    postion: "Ex-DG Information Technology Board AJ&K",

    description:
      "As the world is transforming quickly into the digital one to manage time, utilize resources effectively and for multitasking. Here at AJK IT Board we are well aware of the needs to run with the modern world. We are giving our best to give all modern and digital facilities to the people of AJK so that a friendly environment can be created between the public and the Govt. Establishment of E-Facilitation center is also part of the initiative to transform Govt's existing paper-based work into reliable digital one. This E-Facilitation center is established with a great purpose and vision and we are ensured with the full support from various Govt departments. We are very much confident about the success of this project.",
    created_at: "02-01-2026 09:15",
  },
])

const search = ref("")

const filteredReviews = computed(() => {
  if (!search.value) return reviews.value
  return reviews.value.filter(
    r =>
      r.name.toLowerCase().includes(search.value.toLowerCase()) ||
      r.description.toLowerCase().includes(search.value.toLowerCase())
  )
})

const deleteReview = (id) => {
  const confirmed = confirm("Are you sure you want to delete this review?")
  if (confirmed) {
    reviews.value = reviews.value.filter(r => r.id !== id)
  }
}
</script>

<style>
/* truncate class for long description */
.truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.max-w-xs {
  max-width: 300px;
  /* adjust width as needed */
}
</style>
