<script setup>
    import { formatNumber } from '@/mixin';
    import { ref, watch, onMounted } from 'vue';

    const props = defineProps({
        title: String,
        value: [Number, String],
        link: String,
        icon: String,
        color: String,
        percentage: [Number, String],
    });

    const displayValue = ref(0);
    const animateCounter = (target) => {
        let startTimestamp = null;
        const duration = 500;
        const startValue = displayValue.value;
        const endValue = parseInt(target) || 0;

        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);

            // Smooth counting calculation
            displayValue.value = Math.floor(progress * (endValue - startValue) + startValue);

            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    };
    watch(
        () => props.value,
        (newVal) => {
            animateCounter(newVal);
        },
        { immediate: true },
    );

    onMounted(() => {
        animateCounter(props.value);
    });
</script>

<template>
    <div
        class="group bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex justify-between items-center transition-all duration-500 hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.15)] hover:-translate-y-2 hover:shadow-lg cursor-pointer relative overflow-hidden"
        :style="{ borderLeft: `4px solid ${color}` }"
    >
        <!-- Animated Background Gradient -->
        <div
            class="absolute inset-0 opacity-0 group-hover:opacity-5 transition-opacity duration-700"
            :style="{ background: `linear-gradient(135deg, ${color}20 0%, transparent 100%)` }"
        ></div>
        <div
            class="absolute inset-0 -translate-x-full group-hover:translate-x-full transition-transform duration-1000 ease-in-out pointer-events-none"
            :style="{ background: `linear-gradient(90deg, transparent, ${color}30, transparent)` }"
        ></div>

        <!-- Left Content -->
        <div class="flex flex-col h-full justify-between relative z-10">
            <div class="mb-2">
                <span
                    class="text-gray-400 font-bold text-[11px] uppercase tracking-wider block mb-2 transition-all duration-300 "
                >
                    {{ title }}
                </span>
                <div class="flex items-center gap-2">
                    <h3
                        class="text-3xl font-extrabold text-slate-800 leading-none transition-all duration-500 group-hover:scale-105 group-hover:text-slate-900"
                    >
                        {{ formatNumber(displayValue, 0) }}
                    </h3>

                    <!-- Percentage Badge -->
                    <span
                        v-if="percentage !== undefined"
                        class="text-rose-500 text-[10px] font-bold px-1.5 py-0.5 rounded bg-rose-50 border border-rose-100 transition-all duration-300 group-hover:bg-rose-100 group-hover:scale-105"
                    >
                        {{ percentage }}%
                    </span>
                </div>
            </div>

            <!-- View Link -->
            <router-link
                v-if="link && link !== '#'"
                :to="link"
                class="relative text-[11px] font-bold uppercase flex items-center gap-1 w-fit group/link"
                :style="{ color: color }"
            >
                <span class="relative">
                    View Details
                    <span
                        class="absolute -bottom-0.5 left-0 w-0 h-[2px] transition-all duration-300 group-hover/link:w-full"
                        :style="{ backgroundColor: color }"
                    ></span>
                </span>
                <span class="transition-all duration-300 group-hover/link:translate-x-1 group-hover/link:-translate-y-[1px]">→</span>
            </router-link>
        </div>

        <!-- Icon -->
        <div class="relative flex items-center justify-center w-14 h-14">
            <div
                class="absolute inset-0 rounded-full scale-0 opacity-0 transition-all duration-500 group-hover:scale-110 group-hover:opacity-15"
                :style="{ backgroundColor: color }"
            ></div>
            <div class="relative z-10 transition-all duration-500 group-hover:scale-110 group-hover:rotate-3" :style="{ color: color }">
                <i :class="['fa-solid text-2xl', icon]"></i>
            </div>
        </div>
        <!-- Bottom Glow  -->
        <div
            class="absolute bottom-0 left-0 right-0 h-[2px] opacity-0 group-hover:opacity-100 transition-all duration-500"
            :style="{ background: `linear-gradient(90deg, transparent, ${color}, transparent)` }"
        ></div>
    </div>
</template>

<style scoped>
    /* Smooth animations */
    .group {
        animation: fadeInUp 0.6s ease-out backwards;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
