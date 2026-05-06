<script setup lang="ts">
    import type { PropType } from 'vue';
    import { storeToRefs } from 'pinia';
    import { useAppStore } from '@/stores';
    import { Applicant, Region } from '@/types';
    import { formatDMY } from '@/mixin';

    const appStore = useAppStore();
    const {
        guardian_types,
        genders,
        religions,
        marital_statuses,
        regions
    } = storeToRefs(appStore);

    const props = defineProps({
        applicant: {
            type: Object as PropType<Applicant>,
            required: false,
            default: null
        }
    });

    // Helper functions to get names from hierarchy
    const getRegionName = (regionId?: number) => {
        if (!regionId || !regions.value) return regionId || '—';
        const region: any = regions.value.find((r: any) => r.id === regionId);
        console.log('Region:', region, 'ID:', regionId);
        return region?.urdu_name || regionId;
    };

    const getDistrictName = (districtId?: number) => {
        if (!districtId || !regions.value) return districtId || '—';

        // Search through all regions to find the district
        for (const region of regions.value as any as Region[]) {
            if (region.districts) {
                const district = region.districts.find((d: any) => d.id === districtId);
                if (district) return district.urdu_name;
            }
        }
        return districtId;
    };

    const getTehsilName = (tehsilId?: number) => {
        if (!tehsilId || !regions.value) return tehsilId || '—';

        // Search through all regions and districts to find the tehsil
        for (const region of regions.value as any as Region[]) {
            if (region.districts) {
                for (const district of region.districts) {
                    if (district.tehsils) {
                        const tehsil = district.tehsils.find((t: any) => t.id === tehsilId);
                        if (tehsil) return tehsil.urdu_name;
                    }
                }
            }
        }
        return tehsilId;
    };

    const getGenderName = (genderId?: number) => {
        if (!genderId || !genders.value) return genderId || '—';
        const gender: any = genders.value.find((g: any) => g.id === genderId);
        return gender?.urdu_name || genderId;
    };

    const getReligionName = (religionId?: number) => {
        if (!religionId || !religions.value) return religionId || '—';
        const religion: any = religions.value.find((r: any) => r.id === religionId);
        return religion?.urdu_name || religionId;
    };

    const getMaritalStatusName = (maritalStatusId?: number) => {
        if (!maritalStatusId || !marital_statuses.value) return maritalStatusId || '—';
        const status: any = marital_statuses.value.find((s: any) => s.id === maritalStatusId);
        return status?.urdu_name || maritalStatusId;
    };

    const getGuardianTypeName = (guardianTypeId?: number) => {
        if (!guardianTypeId || !guardian_types.value) return guardianTypeId || '—';
        const type: any = guardian_types.value.find((t: any) => t.id === guardianTypeId);
        return type?.urdu_name || guardianTypeId;
    };
</script>

<template>
    <div class="container bg-white font-nastaleeq rounded-lg" dir="rtl" v-if="applicant">
        <div class="col-span-1 md:col-span-2 bg-gray-200 rounded-t-lg">
            <h3 class="font-bold  text-blue-800 border-b border-gray-300 p-2 text-lg">درخواست گزار کی تفصیلات</h3>
        </div>
        <perfect-scrollbar :options="{
                    swipeEasing: true,
                    wheelPropagation: false,
                }" class="max-h-52 relative">
            <div class="grid grid-cols-1 md:grid-cols-2 overflow-y-auto p-2">
                <!-- ==================== ذاتی معلومات ==================== -->

                <!-- مکمل نام -->
                <div class="flex items-center text-sm ">
                    <label class="font-bold text-gray-700  w-28">مکمل نام :</label>
                    <p class="text-gray-900 flex-1">{{ applicant.full_name }}</p>
                </div>

                <!-- والد کا نام -->
                <div class="flex items-center text-sm ">
                    <label class="font-bold text-gray-700  w-28">والد کا نام :</label>
                    <p class="text-gray-900 flex-1">{{ applicant.father_name }}</p>
                </div>

                <!-- شوہر/بیوی کا نام -->
                <div class="flex items-center text-sm ">
                    <label class="font-bold text-gray-700  w-28">شوہر/بیوی کا نام :</label>
                    <p class="text-gray-900 flex-1">{{ applicant.wife_husband_name || '—' }}</p>
                </div>

                <!-- تاریخ پیدائش -->
                <div class="flex items-center text-sm ">
                    <label class="font-bold text-gray-700  w-28">تاریخ پیدائش :</label>
                    <p class="text-gray-900 flex-1">{{ formatDMY(applicant.dob) }}</p>
                </div>

                <!-- جائے پیدائش -->
                <div class="flex items-center text-sm ">
                    <label class="font-bold text-gray-700  w-28">جائے پیدائش :</label>
                    <p class="text-gray-900 flex-1">{{ applicant.pob }}</p>
                </div>

                <!-- صنف -->
                <div class="flex items-center text-sm ">
                    <label class="font-bold text-gray-700  w-28">صنف :</label>
                    <p class="text-gray-900 flex-1">{{ getGenderName(applicant.gender_id) }}</p>
                </div>

                <!-- ازدواجی حیثیت -->
                <div class="flex items-center text-sm ">
                    <label class="font-bold text-gray-700  w-28">ازدواجی حیثیت :</label>
                    <p class="text-gray-900 flex-1">{{ getMaritalStatusName(applicant.marital_status_id) }}</p>
                </div>

                <!-- مذہب -->
                <div class="flex items-center text-sm ">
                    <label class="font-bold text-gray-700  w-28">مذہب :</label>
                    <p class="text-gray-900 flex-1">{{ getReligionName(applicant.religion_id) }}</p>
                </div>

                <!-- سرپرست کی قسم -->
                <div class="flex items-center text-sm ">
                    <label class="font-bold text-gray-700  w-28">سرپرست کی قسم :</label>
                    <p class="text-gray-900 flex-1">{{ getGuardianTypeName(applicant.guardian_type_id) }}</p>
                </div>

                <!-- پیشہ -->
                <div class="flex items-center text-sm ">
                    <label class="font-bold text-gray-700  w-28">پیشہ :</label>
                    <p class="text-gray-900 flex-1">{{ applicant.occupation }}</p>
                </div>

                <!-- ریاستی مضامین کلاس -->
                <div class="flex items-center text-sm ">
                    <label class="font-bold text-gray-700  w-28">ریاستی سبجیکٹ کلاس :</label>
                    <p class="text-gray-900 flex-1">{{ applicant.state_subject_class }}</p>
                </div>

                <!-- ==================== شناختی معلومات ==================== -->
                <div class="col-span-1 md:col-span-2 mt-1">
                    <h3 class="font-bold  text-blue-800 border-b border-gray-200 pb-1 mb-1 text-lg">شناختی معلومات</h3>
                </div>

                <!-- شناختی قسم -->
                <div class="flex items-center text-sm ">
                    <label class="font-bold text-gray-700  w-28">شناختی قسم :</label>
                    <p class="text-gray-900 flex-1">{{ applicant.identity_type }}</p>
                </div>

                <!-- شناختی نمبر -->
                <div class="flex items-center text-sm ">
                    <label class="font-bold text-gray-700  w-28">شناختی نمبر :</label>
                    <p class="text-gray-900 flex-1">{{ applicant.identity_number }}</p>
                </div>

                <!-- والد کا شناختی نمبر -->
                <div class="flex items-center text-sm ">
                    <label class="font-bold text-gray-700  w-28">والد کا شناختی نمبر :</label>
                    <p class="text-gray-900 flex-1">{{ applicant.father_identity_number }}</p>
                </div>

                <!-- شناختی علامت -->
                <div class="flex items-center text-sm ">
                    <label class="font-bold text-gray-700  w-28">شناختی علامت :</label>
                    <p class="text-gray-900 flex-1">{{ applicant.identity_symbol }}</p>
                </div>

                <!-- ==================== مقام کی معلومات ==================== -->
                <div class="col-span-1 md:col-span-2 mt-1">
                    <h3 class="font-bold  text-blue-800 border-b border-gray-200 pb-1 mb-1 text-lg">مقام کی معلومات</h3>
                </div>

                <!-- پتہ -->
                <div class="flex items-center text-sm ">
                    <label class="font-bold text-gray-700  w-28">پتہ :</label>
                    <p class="text-gray-900 flex-1">{{ applicant.address }}</p>
                </div>

                <!-- رہائشی مقام -->
                <div class="flex items-center text-sm ">
                    <label class="font-bold text-gray-700  w-28">رہائشی مقام :</label>
                    <p class="text-gray-900 flex-1">{{ applicant.residence_place }}</p>
                </div>

                <!-- مقام -->
                <div class="flex items-center text-sm ">
                    <label class="font-bold text-gray-700  w-28">مقام :</label>
                    <p class="text-gray-900 flex-1">{{ applicant.location }}</p>
                </div>

                <!-- علاقہ (Region) -->
                <div class="flex items-center text-sm ">
                    <label class="font-bold text-gray-700  w-28">علاقہ :</label>
                    <p class="text-gray-900 flex-1">{{ getRegionName(applicant.region_id) }}</p>
                </div>

                <!-- ضلع -->
                <div class="flex items-center text-sm ">
                    <label class="font-bold text-gray-700  w-28">ضلع :</label>
                    <p class="text-gray-900 flex-1">{{ getDistrictName(applicant.district_id) }}</p>
                </div>

                <!-- تحصیل -->
                <div class="flex items-center text-sm ">
                    <label class="font-bold text-gray-700  w-28">تحصیل :</label>
                    <p class="text-gray-900 flex-1">{{ getTehsilName(applicant.tehsil_id) }}</p>
                </div>

                <!-- ==================== رابطہ کی معلومات ==================== -->
                <div class="col-span-1 md:col-span-2 mt-1">
                    <h3 class="font-bold  text-blue-800 border-b border-gray-200 pb-1 mb-1 text-lg">رابطہ کی
                        معلومات</h3>
                </div>

                <!-- فون -->
                <div class="flex items-center text-sm ">
                    <label class="font-bold text-gray-700  w-28">فون :</label>
                    <p class="text-gray-900 flex-1">{{ applicant.phone }}</p>
                </div>

                <!-- ای میل -->
                <div class="flex items-center text-sm ">
                    <label class="font-bold text-gray-700  w-28">ای میل :</label>
                    <p class="text-gray-900 flex-1">{{ applicant.email }}</p>
                </div>

            </div>
        </perfect-scrollbar>
    </div>
</template>
