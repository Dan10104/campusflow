<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { 
    ArrowLeftIcon,
    CalendarIcon,
    ClockIcon,
    UserIcon,
    MapPinIcon
} from '@heroicons/vue/24/outline';
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'

const props = defineProps({
    aula: Object,
    reservas: Array,
    isAdmin: Boolean,
});

const aprobarReserva = (id) => {
    if (confirm('¿Estás seguro de aprobar esta reserva?')) {
        router.post(route('admin.reservas.aprobar', id), {}, {
            onSuccess: () => {
                // Inertia recargará la página automáticamente
            }
        });
    }
};

const handleEventClick = (info) => {
    const props = info.event.extendedProps;
    if (props.estado === 'pendiente' && isAdmin.value) {
         if (confirm(`Solicitud de: ${props.usuario}\nPropósito: ${props.proposito}\n\n¿Desea APROBAR esta reserva?`)) {
             router.post(route('admin.reservas.aprobar', info.event.id));
         }
    } else {
        // Mostrar detalles simples
        alert(`Reserva de: ${props.usuario}\nEstado: ${getEstadoTexto(props.estado)}\nPropósito: ${props.proposito || 'N/A'}`);
    }
};

const isAdmin = computed(() => props.isAdmin);

// ... (resto de funciones) ...



const volver = () => {
    router.visit(route('aulas.disponibles'));
};

const reservarAula = () => {
    router.visit(route('reservas.crear', { aula: props.aula.codigo }));
};

const formatFecha = (fecha) => {
    return new Date(fecha).toLocaleDateString('es-BO', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const formatHora = (fecha) => {
    return new Date(fecha).toLocaleTimeString('es-BO', {
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getEstadoBadge = (estado) => {
    const badges = {
        'confirmada': 'bg-green-100 text-green-800',
        'aprobada': 'bg-blue-100 text-blue-800',
        'en_uso': 'bg-yellow-100 text-yellow-800',
        'completada': 'bg-gray-100 text-gray-800',
        'cancelada': 'bg-red-100 text-red-800'
    };
    return badges[estado] || 'bg-gray-100 text-gray-800';
};

const getEstadoTexto = (estado) => {
    const textos = {
        'confirmada': 'Confirmada',
        'aprobada': 'Aprobada',
        'en_uso': 'En Uso',
        'completada': 'Completada',
        'cancelada': 'Cancelada'
    };
    return textos[estado] || estado;
};

// Agrupar reservas por fecha
const reservasPorFecha = computed(() => {
    const grupos = {};
    props.reservas.forEach(reserva => {
        const fecha = new Date(reserva.inicio).toDateString();
        if (!grupos[fecha]) {
            grupos[fecha] = [];
        }
        grupos[fecha].push(reserva);
    });
    return grupos;
});

// FullCalendar Setup

const calendarEvents = computed(() => {
    return props.reservas.map(reserva => ({
        id: reserva.id,
        title: `${reserva.usuario.nombre} - ${getEstadoTexto(reserva.estado)}`,
        start: reserva.inicio,
        end: reserva.fin,
        color: getEventColor(reserva.estado),
        textColor: '#fff',
        extendedProps: {
            proposito: reserva.proposito,
            estado: reserva.estado,
            usuario: reserva.usuario.nombre
        }
    }));
});

const getEventColor = (estado) => {
    const colors = {
        'confirmada': '#10B981', // green-500
        'aprobada': '#3B82F6', // blue-500
        'pendiente': '#8B5CF6', // purple-500
        'en_uso': '#F59E0B', // yellow-500
        'completada': '#6B7280', // gray-500
        'cancelada': '#EF4444' // red-500
    };
    return colors[estado] || '#6B7280';
};

const calendarOptions = computed(() => ({
    plugins: [ dayGridPlugin, timeGridPlugin, interactionPlugin ],
    initialView: 'dayGridMonth',
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    locale: 'es',
    events: calendarEvents.value,
    height: 'auto',
    // eventClick: handleEventClick // TODO: Detail modal
}));
</script>

<template>
    <Head :title="`Calendario - Aula ${aula.codigo}`" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center mb-4">
                    <button
                        @click="volver"
                        class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700"
                    >
                        <ArrowLeftIcon class="h-5 w-5 mr-1" />
                        Volver a aulas
                    </button>
                </div>

                <div class="md:flex md:items-center md:justify-between">
                    <div class="flex-1 min-w-0">
                        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                            Calendario - Aula {{ aula.codigo }}
                        </h2>
                        <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                            <div class="mt-2 flex items-center text-sm text-gray-500">
                                <MapPinIcon class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" />
                                {{ aula.modulo.nombre }} - {{ aula.modulo.facultad.nombre }}
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
                        <a 
                            :href="route('aulas.calendario.report', aula.codigo)" 
                            target="_blank"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2 text-red-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            Imprimir
                        </a>
                        <button
                            @click="reservarAula"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Crear reserva
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Columna principal - Calendario -->
                <div class="lg:col-span-2">
                    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                        <div class="px-6 py-5 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                                <CalendarIcon class="h-5 w-5 mr-2 text-gray-400" />
                                Calendario
                            </h3>
                        </div>
                        
                        <div class="p-4">
                            <FullCalendar :options="calendarOptions" />
                        </div>

                        <div class="px-6 py-5 border-t border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">
                                Lista de Reservas (Detallada)
                            </h3>
                        </div>
                        
                        <div class="px-6 py-5">
                            <!-- Sin reservas -->
                            <div v-if="reservas.length === 0" class="text-center py-12">
                                <CalendarIcon class="mx-auto h-12 w-12 text-gray-400" />
                                <h3 class="mt-2 text-sm font-medium text-gray-900">
                                    No hay reservas programadas
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    El aula está disponible para los próximos 30 días
                                </p>
                                <div class="mt-6">
                                    <button
                                        @click="reservarAula"
                                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    >
                                        Crear primera reserva
                                    </button>
                                </div>
                            </div>

                            <!-- Lista de reservas agrupadas por fecha -->
                            <div v-else class="space-y-6">
                                <div
                                    v-for="(reservasDelDia, fecha) in reservasPorFecha"
                                    :key="fecha"
                                    class="border-b border-gray-200 pb-6 last:border-b-0 last:pb-0"
                                >
                                    <h4 class="text-sm font-semibold text-gray-900 mb-3">
                                        {{ formatFecha(reservasDelDia[0].inicio) }}
                                    </h4>
                                    
                                    <div class="space-y-3">
                                        <div
                                            v-for="reserva in reservasDelDia"
                                            :key="reserva.id"
                                            class="flex items-start space-x-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors"
                                        >
                                            <div class="flex-shrink-0">
                                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <ClockIcon class="h-5 w-5 text-blue-600" />
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between">
                                                    <p class="text-sm font-medium text-gray-900">
                                                        {{ formatHora(reserva.inicio) }} - {{ formatHora(reserva.fin) }}
                                                    </p>
                                                    <div class="flex items-center">
                                                        <span
                                                            :class="getEstadoBadge(reserva.estado)"
                                                            class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                                        >
                                                            {{ getEstadoTexto(reserva.estado) }}
                                                        </span>
                                                        
                                                        <button
                                                            v-if="isAdmin && reserva.estado === 'pendiente'"
                                                            @click="aprobarReserva(reserva.id)"
                                                            class="ml-3 inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                                        >
                                                            Aprobar
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="mt-1 flex items-center text-sm text-gray-500">
                                                    <UserIcon class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" />
                                                    {{ reserva.usuario.nombre }}
                                                </div>
                                                <p v-if="reserva.proposito" class="mt-1 text-sm text-gray-600">
                                                    {{ reserva.proposito }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna lateral - Información del aula -->
                <div class="space-y-6">
                    <!-- Detalles del aula -->
                    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                        <div class="px-6 py-5 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">
                                Detalles del Aula
                            </h3>
                        </div>
                        <div class="px-6 py-5">
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Código</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ aula.codigo }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Capacidad</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ aula.capacidad }} personas</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Tipo</dt>
                                    <dd class="mt-1 text-sm text-gray-900 capitalize">{{ aula.tipo.replace('_', ' ') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Módulo</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ aula.modulo.nombre }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Facultad</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ aula.modulo.facultad.sigla }} - {{ aula.modulo.facultad.nombre }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Características -->
                    <div v-if="aula.caracteristicas && aula.caracteristicas.length > 0" class="bg-white shadow-sm rounded-lg border border-gray-200">
                        <div class="px-6 py-5 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">
                                Características
                            </h3>
                        </div>
                        <div class="px-6 py-5">
                            <ul class="space-y-2">
                                <li
                                    v-for="caracteristica in aula.caracteristicas"
                                    :key="caracteristica.id"
                                    class="flex items-center text-sm text-gray-700"
                                >
                                    <svg class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    {{ caracteristica.nombre }}
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Estadísticas -->
                    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                        <div class="px-6 py-5">
                            <h3 class="text-sm font-medium text-gray-900 mb-3">
                                Estadísticas
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Reservas próximas</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ reservas.length }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Estado</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                        Disponible
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>