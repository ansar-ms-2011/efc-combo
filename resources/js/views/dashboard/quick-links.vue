<template>
    <div class="px-6 mt-10">

        <div class="flex flex-col items-center justify-center gap-4 p-6 text-center">
            <p class="text-gray-700 max-w-xl text-xl text-justify">
                You are logged in to the Domicile and State Subject Digitization Portal, a centralized platform designed
                to streamline the application submission, verification, and issuance process for domicile and state subject
                certificates.
            </p>
        </div>

        <div class="pt-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6 text-white">
                <!-- Domicile -->
                <div
    :class="[
        'flex justify-center items-center panel p-8 transition-opacity',
        isAllowed('domicile')
            ? 'bg-gradient-to-r from-cyan-700 to-cyan-600 cursor-pointer hover:opacity-70'
            : 'bg-gray-300 cursor-not-allowed opacity-50'
    ]"
    @click="isAllowed('domicile') && navigateToCreate('domicile')"
>
    <div class="text-xl font-semibold">Domicile</div>
</div>

                <!-- State Subject Certificate -->
                <div
    :class="[
        'flex justify-center items-center panel p-8 transition-opacity',
        isAllowed('state')
            ? 'bg-gradient-to-r from-cyan-600 to-cyan-600 cursor-pointer hover:opacity-90'
            : 'bg-gray-300 cursor-not-allowed opacity-50'
    ]"
    @click="isAllowed('state') && navigateToCreate('state')"
>
    <div class="text-xl font-semibold">State Subject Certificate</div>
</div>

                <!-- State Subject & Domicile -->
               <div
    :class="[
        'flex justify-center items-center panel p-8 transition-opacity',
        isAllowed('both')
            ? 'bg-gradient-to-r from-cyan-600 to-cyan-700 cursor-pointer hover:opacity-90'
            : 'bg-gray-300 cursor-not-allowed opacity-50'
    ]"
    @click="isAllowed('both') && navigateToCreate('both')"
>
    <div class="text-xl font-semibold">State Subject & Domicile</div>
</div>
            </div>

        </div>
    </div>
</template>
<script lang="ts" setup>
    import { ref, computed, onMounted } from 'vue';
    import { useAppStore } from '@/stores';
    import { useMeta } from '@/composables/use-meta';
    import { useRouter } from 'vue-router';

    useMeta({ title: 'E-Facilitation Center AJK' });
    const store = useAppStore();
    const router = useRouter();

    const navigateToCreate = (type: string) => {
        router.push({ path: '/applications/create', query: { type } });
    };


const normalize = (str) => {
    return (str || '')
        .toLowerCase()
        .trim();
};

const serviceMap = computed(() => {
    const services = (store.user?.allowed_services || [])
        .map(normalize);

    return {
        domicile: services.includes('domicile'),
        state: services.includes('state subject certificate'),
        both:
            services.includes('domicile') &&
            services.includes('state subject certificate')
    };
});

const isAllowed = (typeId) => {
    return serviceMap.value[typeId];
};



    // bitcoin
    const bitcoin = computed(() => {
        return {
            chart: {
                height: 45,
                type: 'line',
                sparkline: {
                    enabled: true
                }
            },
            stroke: {
                width: 2
            },
            markers: {
                size: 0
            },
            colors: ['#00ab55'],
            grid: {
                padding: {
                    top: 0,
                    bottom: 0,
                    left: 0
                }
            },
            tooltip: {
                x: {
                    show: false
                },
                y: {
                    title: {
                        formatter: (val: any) => {
                            return '';
                        }
                    }
                }
            },
            responsive: [
                {
                    breakPoint: 576,
                    options: {
                        chart: {
                            height: 95
                        },
                        grid: {
                            padding: {
                                top: 45,
                                bottom: 0,
                                left: 0
                            }
                        }
                    }
                }
            ]
        };
    });

    const bitcoinSeries = ref([
        {
            data: [21, 9, 36, 12, 44, 25, 59, 41, 25, 66]
        }
    ]);

    // ethereum
    const ethereum = computed(() => {
        return {
            chart: {
                height: 45,
                type: 'line',
                sparkline: {
                    enabled: true
                }
            },
            stroke: {
                width: 2
            },
            markers: {
                size: 0
            },
            colors: ['#e7515a'],
            grid: {
                padding: {
                    top: 0,
                    bottom: 0,
                    left: 0
                }
            },
            tooltip: {
                x: {
                    show: false
                },
                y: {
                    title: {
                        formatter: (val: any) => {
                            return '';
                        }
                    }
                }
            },
            responsive: [
                {
                    breakPoint: 576,
                    options: {
                        chart: {
                            height: 95
                        },
                        grid: {
                            padding: {
                                top: 45,
                                bottom: 0,
                                left: 0
                            }
                        }
                    }
                }
            ]
        };
    });

    const ethereumSeries = ref([
        {
            data: [44, 25, 59, 41, 66, 25, 21, 9, 36, 12]
        }
    ]);

    // litecoin
    const litecoin = computed(() => {
        return {
            chart: {
                height: 45,
                type: 'line',
                sparkline: {
                    enabled: true
                }
            },
            stroke: {
                width: 2
            },
            markers: {
                size: 0
            },
            colors: ['#00ab55'],
            grid: {
                padding: {
                    top: 0,
                    bottom: 0,
                    left: 0
                }
            },
            tooltip: {
                x: {
                    show: false
                },
                y: {
                    title: {
                        formatter: (val: any) => {
                            return '';
                        }
                    }
                }
            },
            responsive: [
                {
                    breakPoint: 576,
                    options: {
                        chart: {
                            height: 95
                        },
                        grid: {
                            padding: {
                                top: 45,
                                bottom: 0,
                                left: 0
                            }
                        }
                    }
                }
            ]
        };
    });

    const litecoinSeries = ref([
        {
            data: [9, 21, 36, 12, 66, 25, 44, 25, 41, 59]
        }
    ]);

    // binance
    const binance = computed(() => {
        return {
            chart: {
                height: 45,
                type: 'line',
                sparkline: {
                    enabled: true
                }
            },
            stroke: {
                width: 2
            },
            markers: {
                size: 0
            },
            colors: ['#e7515a'],
            grid: {
                padding: {
                    top: 0,
                    bottom: 0,
                    left: 0
                }
            },
            tooltip: {
                x: {
                    show: false
                },
                y: {
                    title: {
                        formatter: (val: any) => {
                            return '';
                        }
                    }
                }
            },
            responsive: [
                {
                    breakPoint: 576,
                    options: {
                        chart: {
                            height: 95
                        },
                        grid: {
                            padding: {
                                top: 45,
                                bottom: 0,
                                left: 0
                            }
                        }
                    }
                }
            ]
        };
    });

    const binanceSeries = ref([
        {
            data: [25, 44, 25, 59, 41, 21, 36, 12, 19, 9]
        }
    ]);

    // tether
    const tether = computed(() => {
        return {
            chart: {
                height: 45,
                type: 'line',
                sparkline: {
                    enabled: true
                }
            },
            stroke: {
                width: 2
            },
            markers: {
                size: 0
            },
            colors: ['#00ab55'],
            grid: {
                padding: {
                    top: 0,
                    bottom: 0,
                    left: 0
                }
            },
            tooltip: {
                x: {
                    show: false
                },
                y: {
                    title: {
                        formatter: (val: any) => {
                            return '';
                        }
                    }
                }
            },
            responsive: [
                {
                    breakPoint: 576,
                    options: {
                        chart: {
                            height: 95
                        },
                        grid: {
                            padding: {
                                top: 45,
                                bottom: 0,
                                left: 0
                            }
                        }
                    }
                }
            ]
        };
    });

    const tetherSeries = ref([
        {
            data: [21, 59, 41, 44, 25, 66, 9, 36, 25, 12]
        }
    ]);

    // solana
    const solana = computed(() => {
        return {
            chart: {
                height: 45,
                type: 'line',
                sparkline: {
                    enabled: true
                }
            },
            stroke: {
                width: 2
            },
            markers: {
                size: 0
            },
            colors: ['#e7515a'],
            grid: {
                padding: {
                    top: 0,
                    bottom: 0,
                    left: 0
                }
            },
            tooltip: {
                x: {
                    show: false
                },
                y: {
                    title: {
                        formatter: (val: any) => {
                            return '';
                        }
                    }
                }
            },
            responsive: [
                {
                    breakPoint: 576,
                    options: {
                        chart: {
                            height: 95
                        },
                        grid: {
                            padding: {
                                top: 45,
                                bottom: 0,
                                left: 0
                            }
                        }
                    }
                }
            ]
        };
    });

    const solanaSeries = ref([
        {
            data: [21, -9, 36, -12, 44, 25, 59, -41, 66, -25]
        }
    ]);

    const filterType = ref('');

</script>
