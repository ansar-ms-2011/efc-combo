<script setup lang="ts">
    import { computed, nextTick, onMounted, ref } from 'vue';
    import { storeToRefs } from 'pinia';
    import { useAppStore } from '@/stores';
    import BaseField from '@/components/Form/BaseField.vue';
    import SelectField from '@/components/Form/SelectField.vue';
    import NewRegionDialog from '@/views/applications/Dialogs/newRegionDialog.vue';
    import NewDistrictDialog from '@/views/applications/Dialogs/NewDistrictDialog.vue';
    import NewTehsilDialog from '@/views/applications/Dialogs/NewTehsilDialog.vue';
    import { Region } from '@/types';

    const props = defineProps({
        values: {
            type: Object,
            required: true
        },
        setFieldValue: {
            type: Function,
            required: true
        }
    });
    const store = useAppStore();
    const appStore = useAppStore();
    const { regions, delivery_modes } = storeToRefs(appStore);
    const showRegionDialog = ref(false);
    const showDistrictDialog = ref(false);
    const showTehsilDialog = ref(false);

    onMounted(() => {
        appStore.loadDropdowns();
    });

    const deliveryOptions = computed(() => {
        return delivery_modes.value.map((mode: any) => ({
            id: mode.name,
            name: mode.urdu_name
        }));
    });

    const handleRegionAddClick = (payload: any) => {
        showRegionDialog.value = true;
    };
    const handleDistrictAddClick = (payload: any) => {
        showDistrictDialog.value = true;
    };
    const handleTehsilAddClick = (payload: any) => {
        showTehsilDialog.value = true;
    };

    const handleRegionAdded = (region: any) => {
        appStore.addRegion(region);
        nextTick(() => {
            props.setFieldValue('applicant.region_id', region.id);
        });
    };
    const handleDistrictAdded = (district: any) => {
        appStore.addDistrict(district);
        nextTick(() => {
            props.setFieldValue('applicant.district_id', district.id);
        });
    };

    const handleTehsilAdded = (tehsil: any) => {
        appStore.addTehsil(tehsil);
        nextTick(() => {
            props.setFieldValue('applicant.tehsil_id', tehsil.id);
        });
    };

    const districts = computed(() => {
        return regions.value.find((region: Region) => region.id === props.values.applicant.region_id)?.districts || [];
    });

    const tehsils = computed(() => {
        return districts.value.find((district: {
            id: any;
        }) => district.id === props.values.applicant.district_id)?.tehsils || [];
    });

</script>

<template>
    <div class="setp2-wrapper" id="step2" dir="rtl">
        <!-- Combined District, Tehsil, Location Section -->
        <div class="flex gap-4 mt-10">
            <SelectField
                required
                wrapper-class="w-1/3"
                type="select"
                name="applicant.region_id"
                label="ریجن کا نام"
                :options="regions"
                showPlusButton
                @onAddClick="handleRegionAddClick"
            />
            <SelectField
                required
                wrapper-class="w-1/3"
                type="select"
                name="applicant.district_id"
                label="ضلع کا نام"
                :options="districts"
                showPlusButton
                :disablePlusButton="!values.applicant.region_id"
                @onAddClick="handleDistrictAddClick"
            />
            <SelectField
                required
                wrapper-class="w-1/3"
                type="select"
                name="applicant.tehsil_id"
                label=" تحصیل  کا نام"
                :options="tehsils"
                showPlusButton
                :disablePlusButton="!values.applicant.district_id"
                @onAddClick="handleTehsilAddClick"
            />
            <!-- Location -->
            <div class="w-1/3" dir="ltr">
                <BaseField
                    required
                    name="applicant.location"
                    label="جگہ کا نام"
                    placeholder="جگہ کا نام درج کریں"
                    :enableUrdu= "store.urduInputEnabled"
                />
            </div>
            <NewRegionDialog
                v-model="showRegionDialog"
                @onItemAdded="handleRegionAdded"
                
            />
            <NewDistrictDialog
                :parentId="values.applicant.region_id"
                v-model="showDistrictDialog"
                @onItemAdded="handleDistrictAdded"
                
            />
            <NewTehsilDialog
                :regionId="values.applicant.region_id"
                :districtId="values.applicant.district_id"
                v-model="showTehsilDialog"
                @onItemAdded="handleTehsilAdded"
                
            />
        </div>

        <!-- Address Section -->
        <div class="grid grid-cols-1 gap-4 mt-3"
             :class="{'md:grid-cols-2': (values.application.certificate_type === 'state' || values.application.certificate_type === 'both')}"
        >
            <div>
                <BaseField
                    type="textarea"
                    name="applicant.address"
                    label="مکمل رہائشی پتہ"
                    :rows="values.application.certificate_type === 'domicile' ? 5 : 11"
                    :enableUrdu="store.urduInputEnabled"
                />
            </div>
            <div class="gap-2"
                 v-if="values.application.certificate_type === 'state' || values.application.certificate_type === 'both'">
                <!-- Address 2 -->
                <BaseField
                    type="textarea"
                    name="applicant.address2"
                    placeholder="Additional Address (Optional)"
                    label="(الف)  اگر درخواست دہندہ ریاست جموں و کشمیر میں مہاجر ہو"
                    :rows="2"
                    :enableUrdu="store.urduInputEnabled"
                />

                <!-- Address 3 -->
                <BaseField
                    type="textarea"
                    name="applicant.address3"
                    placeholder="Additional Address (Optional)"
                    label="(ب)  آزاد جموں و کشمیر"
                    :rows="2"
                    :enableUrdu="store.urduInputEnabled"
                />

                <!-- Address 4 -->
                <BaseField
                    type="textarea"
                    name="applicant.address4"
                    placeholder="Additional Address (Optional)"
                    label="(ج)  آزاد جموں و کشمیر سے باہر"
                    :rows="2"
                    :enableUrdu="store.urduInputEnabled"
                />
            </div>
        </div>

        <div class="relative flex items-center justify-center my-6">
            <div class="flex-grow border-t border-gray-300"></div>
            <h2 class="mx-4 text-lg font-semibold  font-nastaleeq text-primary">
                ڈیلیوری کی تفصیلات
            </h2>
            <div class="flex-grow border-t border-gray-300"></div>
        </div>

        <!-- Delivery Details Section -->
        <div class="grid grid-cols-1 gap-4">
            <div class="flex items-end gap-2">
                <SelectField
                    required
                    wrapper-class="w-1/3"
                    type="select"
                    name="application.delivery_details.delivery_mode"
                    label="ڈیلیوری کا طریقہ"
                    :options="deliveryOptions"
                />
            </div>
            <div class="flex gap-2" v-if="values.application.delivery_details.delivery_mode === 'home'">
                <!-- Address -->
                <div class="w-full">
                    <BaseField
                        required
                        type="textarea"
                        name="application.delivery_details.delivery_address"
                        placeholder="ہوم ڈیلیوری کے لیے مکمل پتہ درج کریں"
                        label="ڈیلیوری کے لیے پتہ"
                        :rows="5"
                        :enableUrdu="store.urduInputEnabled"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
