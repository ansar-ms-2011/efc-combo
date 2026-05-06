<template>
    <nav class="text-sm text-gray-600 mb-4">
        <ol class="flex items-center space-x-2">

            <li v-for="(item, index) in items" :key="index" class="flex items-center">

                <!-- If NOT last item → router-link -->
                <router-link
                    v-if="index !== items.length - 1"
                    :to="item.to"
                    class="text-gray-600 hover:underline"
                >
                    {{ item.label }}
                </router-link>

                <!-- If last item → active text -->
                <span
                    v-else
                    class="font-semibold text-blue-600"
                >
                    {{ item.label }}
                </span>

                <!-- Separator -->
                <span
                    v-if="index !== items.length - 1"
                    class="mx-2 text-gray-400"
                >
                    <i class="fa fa-angle-right"></i>
                </span>

            </li>

        </ol>
    </nav>
</template>

<script setup>
    defineProps({
        items: {
            type: Array,
            required: true
        }
    });
</script>
