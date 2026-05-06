<script setup lang="ts">
    import type { PropType } from 'vue';
    import { useAppStore } from '@/stores';
    import { Certificate } from '@/types';
    import { formatDMY } from '@/mixin';

    const props = defineProps({
        certificates: {
            type: Array as PropType<Certificate[]>,
            required: false,
            default: null
        }
    });

    const getTypeText = (type: string) => {
        return type === 'state' ? 'ریاستی باشندہ' : 'ڈومیسائل';
    };
</script>

<template>
    <div class="container font-nastaleeq overflow-y-auto max-h-80 bg-white rounded-b-lg" dir="rtl" v-if="certificates">
        <!-- Heading -->
        <div class="border-b border-gray-300 bg-gray-200">
            <h3 class="font-bold font-nastaleeq text-blue-800  p-2 text-lg">
                پہلے سے جاری شدہ سرٹیفکیٹس
            </h3>
        </div>

        <!-- Table View -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-4 py-1 text-center font-bold text-gray-700 uppercase tracking-wider">
                        سرٹیفکیٹ نمبر
                    </th>
                    <th scope="col" class="px-4 py-1 text-center font-bold text-gray-700 uppercase tracking-wider">
                        قسم
                    </th>
                    <th scope="col" class="px-4 py-1 text-center font-bold text-gray-700 uppercase tracking-wider">
                        جاری کرنے کی تاریخ
                    </th>
                    <th scope="col" class="px-4 py-1 text-center font-bold text-gray-700 uppercase tracking-wider">
                        منسوخ شدہ
                    </th>
                    <th scope="col" class="px-4 py-1 text-center font-bold text-gray-700 uppercase tracking-wider">
                        ماخذ
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="certificate in certificates" :key="certificate.id" class="hover:bg-gray-50 transition-colors">
                    <!-- Certificate Number -->
                    <td class="px-4 py-3 text-center whitespace-nowrap text-sm text-blue-600 font-mono">
                        {{ certificate.certificate_number || '—' }}
                    </td>

                    <!-- Type -->
                    <td class="px-4 py-3 text-center whitespace-nowrap text-sm">
                        <span class="px-4 py-0 rounded-full text-xs font-semibold"
                              :class="certificate.type === 'state' ? 'bg-purple-100 text-purple-800' : 'bg-indigo-100 text-indigo-800'">
                            {{ getTypeText(certificate.type) }}
                        </span>
                    </td>

                    <!-- Issue Date -->
                    <td class="px-4 py-3 text-center whitespace-nowrap text-sm text-gray-700">
                        {{ formatDMY(certificate.issue_date) }}
                    </td>

                    <!-- Revoked -->
                    <td class="px-4 py-3 text-center whitespace-nowrap text-sm">
                        <span :class="certificate.is_revoked ? 'text-red-600' : 'text-green-600'">
                            {{ certificate.is_revoked ? 'ہاں' : 'نہیں' }}
                        </span>
                    </td>

                    <!-- Source -->
                    <td class="px-4 py-3 text-center whitespace-nowrap text-sm text-gray-600">
                        {{ certificate.source || '—' }}
                    </td>
                </tr>

                <!-- Empty State -->
                <tr v-if="certificates.length === 0">
                    <td colspan="7" class="px-4 py-8 text-center text-lg text-gray-500">
                        کوئی سرٹیفکیٹ موجود نہیں ہے
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<style scoped>
    /* Custom scrollbar for the container */
    .container::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .container::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
