<!-- resources/js/Pages/Activos/Disponibles.vue -->
<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { MagnifyingGlassIcon, QrCodeIcon, FunnelIcon, CubeIcon, PlusIcon } from '@heroicons/vue/24/outline';
import { CheckCircleIcon, ExclamationCircleIcon } from '@heroicons/vue/24/solid';
import { Link } from '@inertiajs/vue3';
const props = defineProps({
    activos: Array,
    tiposActivo: Array,
    filtros: Object,
});

// Estado local
const busqueda = ref(props.filtros?.busqueda || '');
const tipoSeleccionado = ref(props.filtros?.tipo || '');
const estadoSeleccionado = ref(props.filtros?.estado || 'disponible');
const mostrarFiltros = ref(false);

// Activos filtrados
const activosFiltrados = computed(() => {
    let resultado = props.activos;
    
    if (busqueda.value) {
        const termino = busqueda.value.toLowerCase();
        resultado = resultado.filter(activo => 
            activo.descripcion.toLowerCase().includes(termino) ||
            activo.tipo_activo.nombre.toLowerCase().includes(termino) ||
            activo.marca?.toLowerCase().includes(termino) ||
            activo.modelo?.toLowerCase().includes(termino)
        );
    }
    
    if (tipoSeleccionado.value) {
        resultado = resultado.filter(activo => 
            activo.tipo_activo_id === parseInt(tipoSeleccionado.value)
        );
    }
    
    return resultado;
});

// Funciones
const solicitarPrestamo = (activo) => {
    router.visit(route('prestamos.crear', { activo: activo.codigo }));
};

const verDetalles = (activo) => {
    router.visit(route('activos.show', activo.codigo));
};

const limpiarFiltros = () => {
    busqueda.value = '';
    tipoSeleccionado.value = '';
    router.get(route('activos.disponibles'));
};

const aplicarFiltros = () => {
    router.get(route('activos.disponibles'), {
        busqueda: busqueda.value,
        tipo: tipoSeleccionado.value,
        estado: estadoSeleccionado.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

// Badges de estado
const getEstadoBadge = (estado) => {
    const badges = {
        'disponible': 'bg-green-100 text-green-800',
        'en_deposito': 'bg-blue-100 text-blue-800',
        'prestado': 'bg-yellow-100 text-yellow-800',
        'mantenimiento': 'bg-red-100 text-red-800',
        'baja': 'bg-gray-100 text-gray-800'
    };
    return badges[estado] || 'bg-gray-100 text-gray-800';
};

const getEstadoTexto = (estado) => {
    const textos = {
        'disponible': 'Disponible',
        'en_deposito': 'En Depósito',
        'prestado': 'Prestado',
        'mantenimiento': 'Mantenimiento',
        'baja': 'Dado de Baja'
    };
    return textos[estado] || estado;
};
</script>

<template>
    <Head title="Activos Disponibles" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
               <div class="md:flex md:items-center md:justify-between">
                    <div class="flex-1 min-w-0">
                        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                            Activos Tecnológicos Disponibles
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            Solicita préstamo de equipos para tus actividades académicas
                        </p>
                    </div>
                    
                    <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
                        
                        <span class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm">
                            <CubeIcon class="w-5 h-5 mr-2 text-gray-400" />
                            {{ activosFiltrados?.length || 0 }} activos
                        </span>
                        
                        <a 
                            :href="route('activos.report')" 
                            target="_blank"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2 text-red-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            Reporte
                        </a>

                        <Link
                            :href="route('activos.create')"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            <PlusIcon class="h-5 w-5 mr-2" />
                            Nuevo Activo
                        </Link>
                        
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros y Búsqueda -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <!-- Barra de búsqueda principal -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <!-- Input de búsqueda -->
                    <div class="flex-1">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                            </div>
                            <input
                                v-model="busqueda"
                                @input="aplicarFiltros"
                                type="text"
                                placeholder="Buscar por descripción, tipo, marca o modelo..."
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            />
                        </div>
                    </div>
                    
                    <!-- Botón de filtros -->
                    <button
                        @click="mostrarFiltros = !mostrarFiltros"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        <FunnelIcon class="h-5 w-5 mr-2 text-gray-400" />
                        Filtros
                        <span v-if="tipoSeleccionado" class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                            1
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
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <!-- Tipo de activo -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Tipo de Activo
                                </label>
                                <select
                                    v-model="tipoSeleccionado"
                                    @change="aplicarFiltros"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                >
                                    <option value="">Todos los tipos</option>
                                    <option
                                        v-for="tipo in tiposActivo"
                                        :key="tipo.id"
                                        :value="tipo.id"
                                    >
                                        {{ tipo.nombre }}
                                    </option>
                                </select>
                            </div>

                            <!-- Estado -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Estado
                                </label>
                                <select
                                    v-model="estadoSeleccionado"
                                    @change="aplicarFiltros"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                >
                                    <option value="disponible">Disponible</option>
                                    <option value="en_deposito">En Depósito</option>
                                    <option value="">Todos</option>
                                </select>
                            </div>

                            <!-- Botón limpiar -->
                            <div class="flex items-end">
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

        <!-- Grid de Activos -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
            <!-- Sin resultados -->
            <div v-if="activosFiltrados.length === 0" class="text-center py-12">
                <CubeIcon class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900">No se encontraron activos</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Intenta ajustar tus filtros de búsqueda
                </p>
            </div>

            <!-- Grid de tarjetas -->
            <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="activo in activosFiltrados"
                    :key="activo.codigo"
                    class="bg-white overflow-hidden rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200"
                >
                    <!-- Header de la tarjeta -->
                    <div class="p-5">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-semibold text-gray-900 truncate">
                                    {{ activo.descripcion }}
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ activo.tipo_activo.nombre }}
                                </p>
                            </div>
                            <span
                                :class="getEstadoBadge(activo.estado)"
                                class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium whitespace-nowrap"
                            >
                                {{ getEstadoTexto(activo.estado) }}
                            </span>
                        </div>

                        <!-- Detalles del activo -->
                        <div class="mt-4 space-y-2">
                            <div v-if="activo.marca || activo.modelo" class="flex items-center text-sm text-gray-600">
                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                <span>{{ activo.marca }} {{ activo.modelo }}</span>
                            </div>

                            <div v-if="activo.ubicacion_actual" class="flex items-center text-sm text-gray-600">
                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>{{ activo.ubicacion_actual }}</span>
                            </div>

                            <div class="flex items-center text-sm text-gray-600">
                                <QrCodeIcon class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" />
                                <span class="font-mono text-xs">{{ activo.codigo }}</span>
                            </div>
                        </div>

                        <!-- Estado de disponibilidad -->
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div v-if="activo.estado === 'disponible'" class="flex items-center text-sm text-green-700">
                                <CheckCircleIcon class="h-5 w-5 mr-2 text-green-500" />
                                <span class="font-medium">Disponible para préstamo</span>
                            </div>
                            <div v-else-if="activo.estado === 'en_deposito'" class="flex items-center text-sm text-blue-700">
                                <CheckCircleIcon class="h-5 w-5 mr-2 text-blue-500" />
                                <span class="font-medium">En depósito - Solicitar entrega</span>
                            </div>
                            <div v-else class="flex items-center text-sm text-gray-600">
                                <ExclamationCircleIcon class="h-5 w-5 mr-2 text-gray-400" />
                                <span>No disponible actualmente</span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer de la tarjeta (botones) -->
                    <div class="bg-gray-50 px-5 py-3 flex justify-between items-center">
                        <button
                            @click="verDetalles(activo)"
                            class="text-sm font-medium text-blue-600 hover:text-blue-500"
                        >
                            Ver detalles
                        </button>
                        <button
                            @click="solicitarPrestamo(activo)"
                            :disabled="activo.estado !== 'disponible' && activo.estado !== 'en_deposito'"
                            :class="[
                                'inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white',
                                activo.estado === 'disponible' || activo.estado === 'en_deposito'
                                    ? 'bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500'
                                    : 'bg-gray-300 cursor-not-allowed'
                            ]"
                        >
                            Solicitar préstamo
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>