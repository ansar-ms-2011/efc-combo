<script setup lang="ts">

    const props = defineProps({
        modelValue: {
            type: String,
        },
    });
</script>

<template>
    <div class="relative mb-6">

        <!-- Centered Heading -->
        <div class="text-center">
            <h2 class="text-2xl font-bold font-nastaleeq text-blue-600">
                {{
                    props.modelValue === 'state'
                        ? 'اسٹیٹ سبجیکٹ سرٹیفکیٹ کے لیے درخواست'
                        : props.modelValue === 'domicile'
                            ? 'ڈومیسائل سرٹیفکیٹ کی درخواست'
                            : 'ڈومیسائل اور اسٹیٹ سبجیکٹ سرٹیفکیٹ کے لیے درخواست'
                }}
            </h2>

            <p class="text-gray-600 font-nastaleeq font-bold">
                {{
                    props.modelValue === 'state'
                        ? 'Application for State Subject Certificate'
                        : props.modelValue === 'domicile'
                            ? 'Application for Domicile Certificate'
                            : 'Application for Domicile and State Subject Certificate'
                }}
            </p>
        </div>
    </div>
</template>
