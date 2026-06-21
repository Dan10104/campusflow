<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { CheckCircleIcon, ShieldCheckIcon, ClockIcon, DocumentDuplicateIcon, CubeIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    activo: Object,
    eventos: Array,
    total_eventos: Number,
    error: String,
});

// Reutilizar funciones del componente anterior (formatFecha, copiarAlPortapapeles, etc.)
</script>

<template>
    <!-- Estructura similar a ReservaHistory pero con información del activo -->
    <Head title="Historial Blockchain - Activo" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">
                                    Historial Blockchain - Activo
                                </h2>
                                <p class="text-sm text-gray-600 mt-1">
                                    Trazabilidad de préstamos - {{ activo.descripcion }}
                                </p>
                            </div>
                            <CubeIcon class="w-8 h-8 text-blue-600" />
                        </div>

                        <!-- Info del activo -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6 p-4 bg-gray-50 rounded-lg">
                            <div>
                                <p class="text-xs text-gray-500 uppercase">Código</p>
                                <p class="font-semibold text-gray-900">#{{ activo.codigo }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase">Tipo</p>
                                <p class="text-sm text-gray-900">{{ activo.tipo }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase">Estado</p>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ activo.estado }}
                                </span>
                            </div>
                            <div v-if="activo.marca">
                                <p class="text-xs text-gray-500 uppercase">Marca/Modelo</p>
                                <p class="text-sm text-gray-900">{{ activo.marca }} {{ activo.modelo }}</p>
                            </div>
                            <div v-if="activo.ubicacion_actual">
                                <p class="text-xs text-gray-500 uppercase">Ubicación Actual</p>
                                <p class="text-sm text-gray-900">{{ activo.ubicacion_actual }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Timeline similar al de reservas -->
                <!-- ... -->

            </div>
        </div>
    </AuthenticatedLayout>
</template>