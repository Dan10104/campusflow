<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import { 
    SignalIcon, 
    SignalSlashIcon, 
    UserIcon, 
    FireIcon 
} from '@heroicons/vue/24/solid';

const props = defineProps({
    sensores: Array
});

// Auto-refresh simple para prototipo
let intervalId = null;

const refreshData = () => {
    router.reload({ only: ['sensores'], preserveScroll: true });
};

onMounted(() => {
    // Refrescar cada 10 segundos para ver "movimiento"
    intervalId = setInterval(refreshData, 10000);
});

onUnmounted(() => {
    if (intervalId) clearInterval(intervalId);
});
</script>

<template>
    <Head title="IoT Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Monitoreo IoT en Tiempo Real (Prototipo)
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="aula in sensores" :key="aula.codigo" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 relative">
                        <!-- Status Indicator -->
                        <div class="absolute top-4 right-4">
                            <span v-if="aula.sensor_status === 'online'" class="flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                            </span>
                            <span v-else class="h-3 w-3 rounded-full bg-red-500 block"></span>
                        </div>

                        <h3 class="text-lg font-bold text-gray-900 mb-2">{{ aula.nombre }}</h3>
                        <p class="text-sm text-gray-500 mb-4">Código: {{ aula.codigo }}</p>

                        <div class="grid grid-cols-2 gap-4">
                            <!-- Movimiento -->
                            <div class="flex items-center space-x-2 p-2 rounded-md" :class="aula.movimiento ? 'bg-blue-50' : 'bg-gray-50'">
                                <UserIcon class="h-6 w-6" :class="aula.movimiento ? 'text-blue-600' : 'text-gray-400'" />
                                <div>
                                    <p class="text-xs text-gray-500">Ocupación</p>
                                    <p class="font-semibold text-sm">{{ aula.movimiento ? 'Detectada' : 'Vacío' }}</p>
                                </div>
                            </div>

                            <!-- Temperatura -->
                            <div class="flex items-center space-x-2 p-2 rounded-md bg-orange-50">
                                <FireIcon class="h-6 w-6 text-orange-500" />
                                <div>
                                    <p class="text-xs text-gray-500">Temp</p>
                                    <p class="font-semibold text-sm">{{ aula.temperatura }}°C</p>
                                </div>
                            </div>

                            <!-- Conectividad -->
                            <div class="col-span-2 flex items-center justify-between text-xs text-gray-400 mt-2 border-t pt-2">
                                <span class="flex items-center">
                                    <SignalIcon v-if="aula.sensor_status === 'online'" class="h-4 w-4 mr-1 text-green-500" />
                                    <SignalSlashIcon v-else class="h-4 w-4 mr-1 text-red-500" />
                                    {{ aula.sensor_status.toUpperCase() }}
                                </span>
                                <span>Actualizado: {{ aula.ultima_actualizacion }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
