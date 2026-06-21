<script setup>
import { ref, onMounted } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Chart from 'chart.js/auto';
import { 
    ArrowDownTrayIcon, 
    CalendarIcon, 
    WrenchIcon, 
    ComputerDesktopIcon 
} from '@heroicons/vue/24/outline';

const { kpis, charts, listas } = usePage().props;

const chartEstados = ref(null);
const chartAulas = ref(null);

onMounted(() => {
    // 1. Gráfico de Pastel: Estado de Reservas
    const estadosLabels = charts.estados.map(e => e.estado.toUpperCase());
    const estadosData = charts.estados.map(e => e.total);
    
    // Colores semánticos para estados
    const colorMap = {
        'confirmada': '#10B981', // Verde
        'pendiente': '#F59E0B',  // Naranja
        'cancelada': '#EF4444',  // Rojo
        'en_uso': '#3B82F6',     // Azul
        'completada': '#6B7280'  // Gris
    };
    const backgroundColors = charts.estados.map(e => colorMap[e.estado] || '#6366F1');

    new Chart(chartEstados.value, {
        type: 'doughnut',
        data: {
            labels: estadosLabels,
            datasets: [{
                data: estadosData,
                backgroundColor: backgroundColors,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right' }
            }
        }
    });

    // 2. Gráfico de Barras: Aulas Top
    new Chart(chartAulas.value, {
        type: 'bar',
        data: {
            labels: charts.aulas_top.map(a => a.nombre),
            datasets: [{
                label: 'Cantidad de Reservas',
                data: charts.aulas_top.map(a => a.total),
                backgroundColor: '#3B82F6',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString('es-BO', { 
        month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' 
    });
};
</script>

<template>
    <Head title="Panel de Control" />

    <AuthenticatedLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="md:flex md:items-center md:justify-between mb-8">
                    <div class="flex-1 min-w-0">
                        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                            Dashboard Institucional
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            Resumen operativo de espacios y activos.
                        </p>
                    </div>
                    <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
                        <a :href="route('reporte.exportar', 'pdf')" target="_blank" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <ArrowDownTrayIcon class="h-5 w-5 mr-2 text-red-500" />
                            PDF
                        </a>
                        <a :href="route('reporte.exportar', 'csv')" target="_blank" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <ArrowDownTrayIcon class="h-5 w-5 mr-2 text-green-500" />
                            CSV
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-3 mb-8">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5 flex items-center">
                            <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                                <CalendarIcon class="h-6 w-6 text-blue-600" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Reservas este mes</dt>
                                    <dd class="text-3xl font-semibold text-gray-900">{{ kpis.reservas_mes }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5 flex items-center">
                            <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                                <ComputerDesktopIcon class="h-6 w-6 text-yellow-600" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Activos Prestados</dt>
                                    <dd class="text-3xl font-semibold text-gray-900">{{ kpis.activos_prestados }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5 flex items-center">
                            <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                                <WrenchIcon class="h-6 w-6 text-red-600" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Aulas en Mantenimiento</dt>
                                    <dd class="text-3xl font-semibold text-gray-900">{{ kpis.aulas_mantenimiento }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-8 lg:grid-cols-2 mb-8">
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Estado de Reservas</h3>
                        <div class="relative h-64">
                            <canvas ref="chartEstados"></canvas>
                        </div>
                    </div>

                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Aulas Más Solicitadas</h3>
                        <div class="relative h-64">
                            <canvas ref="chartAulas"></canvas>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                    
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Próximas Reservas</h3>
                        </div>
                        <ul role="list" class="divide-y divide-gray-200">
                            <li v-for="reserva in listas.proximas_reservas" :key="reserva.id" class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-blue-600 truncate">
                                        {{ reserva.aula.modulo.nombre }} - Aula {{ reserva.aula_codigo }}
                                    </p>
                                    <div class="ml-2 flex-shrink-0 flex">
                                        <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ reserva.estado }}
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-2 sm:flex sm:justify-between">
                                    <div class="sm:flex">
                                        <p class="flex items-center text-sm text-gray-500">
                                            <CalendarIcon class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" />
                                            {{ formatDate(reserva.inicio) }}
                                        </p>
                                    </div>
                                    <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                        <p>{{ reserva.usuario?.nombre || 'Usuario' }}</p>
                                    </div>
                                </div>
                            </li>
                            <li v-if="listas.proximas_reservas.length === 0" class="px-4 py-4 text-sm text-gray-500 text-center">
                                No hay reservas próximas.
                            </li>
                        </ul>
                    </div>

                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Activos en Mantenimiento</h3>
                        </div>
                        <ul role="list" class="divide-y divide-gray-200">
                            <li v-for="activo in listas.activos_mantenimiento" :key="activo.codigo" class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ activo.descripcion }}
                                    </p>
                                    <p class="text-sm text-gray-500">{{ activo.tipo_activo?.nombre }}</p>
                                </div>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Código: <span class="font-mono">{{ activo.codigo }}</span>
                                    </p>
                                </div>
                            </li>
                            <li v-if="listas.activos_mantenimiento.length === 0" class="px-4 py-4 text-sm text-gray-500 text-center">
                                Todo operativo. Ningún activo en mantenimiento.
                            </li>
                        </ul>
                    </div></div>

                <!-- [NUEVO] Widget de Predicciones AI (CU-14) -->
                <div v-if="listas.predicciones && listas.predicciones.length > 0" class="mt-8">
                    <div class="bg-gradient-to-r from-purple-500 to-indigo-600 rounded-lg shadow-lg p-6 text-white">
                        <h3 class="text-lg font-bold mb-2 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Predicción de Demanda Inteligente (Mañana)
                        </h3>
                        <p class="text-indigo-100 text-sm mb-4">Nuestro modelo predice alta saturación en los siguientes espacios. Se recomienda bloquear reservas de mantenimiento.</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div v-for="(pred, index) in listas.predicciones" :key="index" class="bg-white rounded p-3 shadow text-gray-800">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-bold text-xl">{{ pred.aula }}</span>
                                    <span class="px-2 py-1 rounded text-xs font-bold text-white shadow-sm" 
                                          :class="pred.nivel_demanda === 'Critico' ? 'bg-red-500' : 'bg-yellow-500'">
                                        {{ pred.nivel_demanda }}
                                    </span>
                                </div>
                                <div class="text-sm">
                                    <p class="font-medium">Probabilidad: <span class="font-bold">{{ pred.probabilidad }}%</span></p>
                                    <p class="text-gray-600">Hora Pico: {{ pred.hora_pico }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>