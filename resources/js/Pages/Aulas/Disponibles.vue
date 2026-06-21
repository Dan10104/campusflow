<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { 
    MagnifyingGlassIcon, 
    FunnelIcon, 
    BuildingOfficeIcon,
    UserGroupIcon,
    CalendarIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    aulas: Array,
    facultades: Array,
    tipos: Array,
    filtros: Object,
});

// Estado local
const busqueda = ref(props.filtros?.busqueda || '');
const facultadSeleccionada = ref(props.filtros?.facultad || '');
const tipoSeleccionado = ref(props.filtros?.tipo || '');
const capacidadMinima = ref(props.filtros?.capacidad || '');
const fecha = ref(props.filtros?.fecha || '');
const horaInicio = ref(props.filtros?.hora_inicio || '');
const horaFin = ref(props.filtros?.hora_fin || '');
const mostrarFiltros = ref(false);

// Funciones
const reservarAula = (aula) => {
    router.visit(route('reservas.crear', { 
        aula: aula.codigo,
        fecha: fecha.value,
        hora_inicio: horaInicio.value,
        hora_fin: horaFin.value
    }));
};

const verCalendario = (aula) => {
    router.visit(route('aulas.calendario', aula.codigo));
};

const limpiarFiltros = () => {
    busqueda.value = '';
    facultadSeleccionada.value = '';
    tipoSeleccionado.value = '';
    capacidadMinima.value = '';
    fecha.value = '';
    horaInicio.value = '';
    horaFin.value = '';
    router.get(route('aulas.disponibles'));
};

const aplicarFiltros = () => {
    router.get(route('aulas.disponibles'), {
        busqueda: busqueda.value,
        facultad: facultadSeleccionada.value,
        tipo: tipoSeleccionado.value,
        capacidad: capacidadMinima.value,
        fecha: fecha.value,
        hora_inicio: horaInicio.value,
        hora_fin: horaFin.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

const getTipoBadge = (tipo) => {
    const badges = {
        'aula': 'bg-blue-100 text-blue-800',
        'laboratorio': 'bg-purple-100 text-purple-800',
        'auditorio': 'bg-green-100 text-green-800',
        'sala_reuniones': 'bg-yellow-100 text-yellow-800'
    };
    return badges[tipo] || 'bg-gray-100 text-gray-800';
};

const getTipoTexto = (tipo) => {
    const textos = {
        'aula': 'Aula',
        'laboratorio': 'Laboratorio',
        'auditorio': 'Auditorio',
        'sala_reuniones': 'Sala de Reuniones'
    };
    return textos[tipo] || tipo;
};
</script>

<template>
    <Head title="Aulas Disponibles" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="md:flex md:items-center md:justify-between">
                    <div class="flex-1 min-w-0">
                        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                            Aulas Disponibles
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            Reserva espacios para tus clases y actividades académicas
                        </p>
                    </div>
                    <div class="mt-4 flex md:mt-0 md:ml-4">
                        <a 
                            :href="route('aulas.report')" 
                            target="_blank"
                            class="mr-3 inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2 text-red-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            Reporte
                        </a>
                        <span class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg">
                            <BuildingOfficeIcon class="w-5 h-5 mr-2 text-gray-400" />
                            {{ aulas.length }} aulas
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros y Búsqueda -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <!-- Barra de búsqueda principal -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                            </div>
                            <input
                                v-model="busqueda"
                                @input="aplicarFiltros"
                                type="text"
                                placeholder="Buscar por código, módulo o facultad..."
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            />
                        </div>
                    </div>
                    
                    <button
                        @click="mostrarFiltros = !mostrarFiltros"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        <FunnelIcon class="h-5 w-5 mr-2 text-gray-400" />
                        Filtros
                        <span 
                            v-if="facultadSeleccionada || tipoSeleccionado || capacidadMinima || fecha" 
                            class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800"
                        >
                            {{ [facultadSeleccionada, tipoSeleccionado, capacidadMinima, fecha].filter(Boolean).length }}
                        </span>
                    </button>
                </div>

                <!-- Panel de filtros expandible -->
                <transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0 -translate-y-1"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 -translate-y-1"
                >
                    <div v-show="mostrarFiltros" class="mt-4 pt-4 border-t border-gray-200">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <!-- Facultad -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Facultad
                                </label>
                                <select
                                    v-model="facultadSeleccionada"
                                    @change="aplicarFiltros"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                >
                                    <option value="">Todas las facultades</option>
                                    <option
                                        v-for="facultad in facultades"
                                        :key="facultad.codigo"
                                        :value="facultad.codigo"
                                    >
                                        {{ facultad.sigla }} - {{ facultad.nombre }}
                                    </option>
                                </select>
                            </div>

                            <!-- Tipo -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Tipo
                                </label>
                                <select
                                    v-model="tipoSeleccionado"
                                    @change="aplicarFiltros"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                >
                                    <option value="">Todos los tipos</option>
                                    <option
                                        v-for="tipo in tipos"
                                        :key="tipo"
                                        :value="tipo"
                                    >
                                        {{ getTipoTexto(tipo) }}
                                    </option>
                                </select>
                            </div>

                            <!-- Capacidad mínima -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Capacidad mínima
                                </label>
                                <input
                                    v-model="capacidadMinima"
                                    @change="aplicarFiltros"
                                    type="number"
                                    min="1"
                                    placeholder="Ej: 30"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                />
                            </div>

                            <!-- Fecha -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Fecha
                                </label>
                                <input
                                    v-model="fecha"
                                    @change="aplicarFiltros"
                                    type="date"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                />
                            </div>

                            <!-- Hora inicio -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Hora inicio
                                </label>
                                <input
                                    v-model="horaInicio"
                                    @change="aplicarFiltros"
                                    type="time"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                />
                            </div>

                            <!-- Hora fin -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Hora fin
                                </label>
                                <input
                                    v-model="horaFin"
                                    @change="aplicarFiltros"
                                    type="time"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                />
                            </div>

                            <!-- Botón limpiar -->
                            <div class="flex items-end sm:col-span-2 lg:col-span-2">
                                <button
                                    @click="limpiarFiltros"
                                    type="button"
                                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                >
                                    Limpiar filtros
                                </button>
                            </div>
                        </div>
                    </div>
                </transition>
            </div>
        </div>

        <!-- Grid de Aulas -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
            <!-- Sin resultados -->
            <div v-if="aulas.length === 0" class="text-center py-12">
                <BuildingOfficeIcon class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900">No se encontraron aulas</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Intenta ajustar tus filtros de búsqueda
                </p>
            </div>

            <!-- Grid de tarjetas -->
            <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="aula in aulas"
                    :key="aula.codigo"
                    class="bg-white overflow-hidden rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200"
                >
                    <!-- Header de la tarjeta -->
                    <div class="p-5">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Aula {{ aula.codigo }}
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ aula.modulo.nombre }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ aula.modulo.facultad.sigla }}
                                </p>
                            </div>
                            <span
                                :class="getTipoBadge(aula.tipo)"
                                class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium whitespace-nowrap"
                            >
                                {{ getTipoTexto(aula.tipo) }}
                            </span>
                        </div>

                        <!-- Detalles del aula -->
                        <div class="mt-4 space-y-2">
                            <div class="flex items-center text-sm text-gray-600">
                                <UserGroupIcon class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" />
                                <span>Capacidad: {{ aula.capacidad }} personas</span>
                            </div>

                            <!-- Características -->
                            <div v-if="aula.caracteristicas && aula.caracteristicas.length > 0" class="mt-3">
                                <p class="text-xs font-medium text-gray-500 mb-1">Características:</p>
                                <div class="flex flex-wrap gap-1">
                                    <span
                                        v-for="caracteristica in aula.caracteristicas"
                                        :key="caracteristica.id"
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700"
                                    >
                                        {{ caracteristica.nombre }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer de la tarjeta (botones) -->
                    <div class="bg-gray-50 px-5 py-3 flex justify-between items-center">
                        <button
                            @click="verCalendario(aula)"
                            class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500"
                        >
                            <CalendarIcon class="h-4 w-4 mr-1" />
                            Ver calendario
                        </button>
                        <button
                            @click="reservarAula(aula)"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Reservar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>