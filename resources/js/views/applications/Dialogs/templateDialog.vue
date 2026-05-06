<script setup>
import { ref, watch } from 'vue'
import axios from 'axios'
import BaseDialog from '@/components/BaseDialog.vue'

// state
const showTemplateModal = ref(false)
const templatesList = ref([])
const selectedTemplateId = ref('')
const generatedContent = ref('')
const selectedAppData = ref(null)
const loading = ref(false)

// open modal
const openTemplateModal = async (app) => {
  selectedAppData.value = app
  showTemplateModal.value = true
  selectedTemplateId.value = ''
  generatedContent.value = ''
  loading.value = true

  try {
    const [appRes, templateRes] = await Promise.all([
      axios.get(`/api/applications/${app.uuid}`),
      axios.get('/api/templates')
    ])

    selectedAppData.value = appRes.data.data
    templatesList.value = templateRes.data.data || []

  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

// 🔥 template engine (with underline fallback)
const fillTemplate = (content, data) => {
  return content.replace(/{{\s*([\w_]+)\s*}}/g, (match, key) => {
    const value = data[key]

    const finalValue =
      value === undefined || value === null || value === ''
        ? ''
        : value

    return `
      <span class="print-field">${finalValue}</span>
    `
  })
}


import { useAppStore } from '@/stores/index'

const appStore = useAppStore()

const getRegionName = (id) => {
  const region = appStore.groupedData.regions.find(r => r.id == id)
  return region?.urdu_name || '__________'
}

const getDistrictName = (regionId, districtId) => {
  const region = appStore.groupedData.regions.find(r => r.id == regionId)
  const district = region?.districts?.find(d => d.id == districtId)
  return district?.urdu_name || '__________'
}

const getTehsilName = (regionId, districtId, tehsilId) => {
  const region = appStore.groupedData.regions.find(r => r.id == regionId)
  const district = region?.districts?.find(d => d.id == districtId)
  const tehsil = district?.tehsils?.find(t => t.id == tehsilId)
  return tehsil?.urdu_name || '__________'
}

// watch template selection
watch(selectedTemplateId, async (newVal) => {
  if (!newVal || !selectedAppData.value?.applicant) return

  const template = templatesList.value.find(t => t.id == newVal)
  if (!template) return

  const applicant = selectedAppData.value.applicant

  const data = {
    person_name: applicant.full_name || '',
    father: applicant.father_name || '',
    grandfather_name: applicant.grandfather_name || '',
    cnic: applicant.identity_number || '',
    dob: applicant.dob || '',
    pob: applicant.pob || '',
    village: applicant.residence_place || '',
    address: applicant.address || '',
    address2: applicant.address2 || '',
    address3: applicant.address3 || '',
    address4: applicant.address4 || '',
    
  region: getRegionName(selectedAppData.value?.applicant?.region_id),
  district: getDistrictName(
    selectedAppData.value?.applicant?.region_id,
    selectedAppData.value?.applicant?.district_id
  ),
  tehsil: getTehsilName(
    selectedAppData.value?.applicant?.region_id,
    selectedAppData.value?.applicant?.district_id,
    selectedAppData.value?.applicant?.tehsil_id
  ),
    date: new Date().toLocaleDateString()
  }

  console.log('Template Data:', data)

  generatedContent.value = fillTemplate(template.content, data)
})

const printTemplate = () => {
  window.print()
}

// expose to parent
defineExpose({
  openTemplateModal
})
</script>

<template>
  <BaseDialog v-model="showTemplateModal" title="Select Template" maxWidth="max-w-3xl">

    <div class="space-y-4 h-[50vh]" >

      <!-- loader -->
      <div v-if="loading" class="flex justify-center py-10">
        <span class="animate-spin border-4 border-blue-500 border-t-transparent rounded-full w-8 h-8"></span>
      </div>

      <!-- content -->
      <template v-else>

        <!-- template dropdown -->
        <select v-model="selectedTemplateId" class="w-full border p-2 rounded">
          <option value="">Select Template</option>
          <option v-for="t in templatesList" :key="t.id" :value="t.id">
            {{ t.name }}
          </option>
        </select>

        <!-- preview -->
        <div v-if="generatedContent"
             class="border rounded p-3 max-h-[75vh] overflow-auto text-sm font-nastaleeq"
             dir="rtl">
             <div  id="printArea">

          <div v-html="generatedContent"></div>
          </div>
        </div>

      </template>

    </div>

    <!-- footer (HIDE DURING LOADING) -->
    <template #footer v-if="!loading">

      <div class="flex justify-end gap-2">

        <button @click="showTemplateModal = false"
                class="px-4 py-2 bg-gray-200 rounded">
          Cancel
        </button>

        <button @click="printTemplate"
                :disabled="!generatedContent"
                class="px-4 py-2 bg-blue-600 text-white rounded">
          Print
        </button>

      </div>

    </template>

  </BaseDialog>
</template>

<style>

@media print {
  body * {
    visibility: hidden;
  }

  #printArea,
  #printArea * {
    visibility: visible;
  }

  #printArea {
    position: absolute;
     font-size: 18px; 
    line-height: 1.8;
    left: 0;
    top: 20px;
    width: 100%;
    padding: 20px;
    direction: rtl;
  }
  @page {
  size: A4;
  margin: 10mm;
}
 html, body {
    height: 100%;
    overflow: hidden;
  }

 .print-field {
    display: inline-block;
    border-bottom: 1px solid #000;
    padding: 0 4px;
    min-width: 80px;
  }

  

  
}
</style>