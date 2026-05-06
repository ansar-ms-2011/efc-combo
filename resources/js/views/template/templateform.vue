<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import Swal from 'sweetalert2'

import { Editor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import TextAlign from '@tiptap/extension-text-align'

const route = useRoute()
const router = useRouter()

const isEdit = computed(() => !!route.params.id)

const form = ref({
  name: ''
})

const btnClass = (active) => {
  return [
    'px-3 py-1 border rounded text-sm',
    active ? 'bg-blue-600 text-white' : 'bg-white'
  ];
};

const editor = new Editor({
  extensions: [
    StarterKit,
    TextAlign.configure({
      types: ['heading', 'paragraph']
    })
  ],
  content: '<p></p>'
})

// ✅ LOAD DATA (edit mode)
const loadData = async () => {
  if (!isEdit.value) return

  const res = await axios.get(`/api/templates/${route.params.id}`)

  form.value.name = res.data.data.name
  editor.commands.setContent(res.data.data.content || '<p></p>')
}

// ✅ SUBMIT (create OR update)
const handleSubmit = async () => {
  const payload = {
    name: form.value.name,
    content: editor.getHTML()
  }

  if (isEdit.value) {
    await axios.put(`/api/templates/${route.params.id}`, payload)

   
     Swal.fire({
                    icon: 'success',
                    title: 'Updated',
                    text: 'Record updated successfully',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500
                });
  } else {
    await axios.post('/api/templates', payload)

     Swal.fire({
                    icon: 'success',
                    title: 'Added',
                    text: 'Record added successfully',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500
                });
  }

  router.push('/templates')
}

onMounted(loadData)

onBeforeUnmount(() => {
  editor.destroy()
})
</script>



<template>
  <div class="p-6 min-h-screen">
    <!-- Breadcrumb -->
    <div class="text-sm text-gray-600 mb-4">
      <router-link to="/admin/dashboard" class="hover:underline">
        Templates
      </router-link>
      <i class="fa fa-angle-right"></i>

      <router-link to="/templates" class="hover:underline">
        Template List
      </router-link>
      <i class="fa fa-angle-right"></i>

      <span class="font-semibold text-blue-600"> {{ isEdit ? 'Edit Template' : 'Add Template' }}</span>
    </div>

    <div class="max-w-7xl mx-auto bg-white rounded-lg shadow border p-6">
      <h2 class="text-xl mb-6 text-black font-semibold">
        Add Template
      </h2>

      <form class="space-y-6" @submit.prevent="handleSubmit">

        <!-- Name -->
        <div>
          <label class="block text-sm font-medium mb-1">
            Name <span class="text-red-500">*</span>
          </label>
          <input
            v-model="form.name"
            type="text"
            placeholder="Template Name"
            class="form-input w-[300px]"
          />
        </div>

        <div class="border rounded p-2 flex flex-wrap gap-2 bg-gray-50">
          <button type="button" @click="editor.chain().focus().toggleBold().run()" :class="btnClass(editor.isActive('bold'))">B</button>
          <button type="button" @click="editor.chain().focus().toggleHeading({ level: 1 }).run()" :class="btnClass(editor.isActive('heading', { level: 1 }))">H1</button>
          <button type="button" @click="editor.chain().focus().toggleHeading({ level: 2 }).run()" :class="btnClass(editor.isActive('heading', { level: 2 }))">H2</button>
          <!-- Alignment -->
<button type="button"
  @click="editor.chain().focus().setTextAlign('left').run()"
  class="px-3 py-1 border rounded hover:bg-gray-200">
  <i class="fa fa-align-left"></i>
</button>

<button type="button"
  @click="editor.chain().focus().setTextAlign('center').run()"
  class="px-3 py-1 border rounded hover:bg-gray-200">
  <i class="fa fa-align-center"></i>
</button>

<button type="button"
  @click="editor.chain().focus().setTextAlign('right').run()"
  class="px-3 py-1 border rounded hover:bg-gray-200">
  <i class="fa fa-align-right"></i>
</button>

<button type="button"
  @click="editor.chain().focus().setTextAlign('justify').run()"
  class="px-3 py-1 border rounded hover:bg-gray-200">
  <i class="fa fa-align-justify"></i>
</button>
          <!-- <button type="button" class="px-6 py-2 bg-red-100 hover:bg-red-500 text-red-600 hover:text-white rounded shadow" @click="editor.chain().focus().undo().run()">Undo</button>
          <button type="button" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded shadow" @click="editor.chain().focus().redo().run()">Redo</button> -->
        </div>

        <!-- Tiptap Editor -->
        <div>
          <label class="block text-sm font-medium mb-2">Content</label>

          <div class="border rounded p-2 ">
            <EditorContent :editor="editor" class=" font-nastaleeq" dir="rtl"/>
          </div>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end gap-3 mt-6">
          <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded shadow">
            {{ isEdit ? 'Update' : 'Save' }}
          </button>
          <button type="button" @click="resetForm" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded shadow">
            Reset
          </button>
          <router-link to="/templates" class="px-6 py-2 bg-red-100 hover:bg-red-500 text-red-600 hover:text-white rounded shadow">
            Cancel
          </router-link>
        </div>

      </form>
    </div>
  </div>
</template>

<style scoped>
:deep(.ProseMirror) {
  min-height: 250px;
  font-size: 18px;
  line-height: 2;
}
</style>