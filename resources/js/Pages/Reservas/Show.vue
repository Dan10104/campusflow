<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { 
    ArrowLeftIcon,
    QrCodeIcon,
    ClockIcon,
    MapPinIcon,
    UserIcon,
    CalendarIcon,
    CheckCircleIcon,
    XCircleIcon
} from '@heroicons/vue/24/outline';
import QrcodeVue from 'qrcode.vue';

const props = defineProps({
    reserva: Object,
});

const volver = () => {
    router.visit(route('reservas.index'));
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

const formatFechaHora = (fecha) => {
    return new Date(fecha).toLocaleString('es-BO', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getEstadoBadge = (estado) => {
    const badges = {
        'confirmada': 'bg-green-100 text-green-800',
        'aprobada': 'bg-blue-100 text-blue-800',
        'pendiente': 'bg-yellow-100 text-yellow-800',
        'en_uso': 'bg-purple-100 text-purple-800',
        'completada': 'bg-gray-100 text-gray-800',
        'cancelada': 'bg-red-100 text-red-800'
    };
    return badges[estado] || 'bg-gray-100 text-gray-800';
};

const getEstadoTexto = (estado) => {
    const textos = {
        'confirmada': 'Confirmada',
        'aprobada': 'Aprobada',
        'pendiente': 'Pendiente',
        'en_uso': 'En Uso',
        'completada': 'Completada',
        'cancelada': 'Cancelada'
    };
    return textos[estado] || estado;
};

const puedeHacerCheckin = () => {
    if (props.reserva.estado !== 'confirmada' && props.reserva.estado !== 'aprobada') {
        return false;
    }
    
    const ahora = new Date();
    const inicio = new Date(props.reserva.inicio);
    const ventanaInicio = new Date(inicio.getTime() - 15 * 60000); // 15 minutos antes
    const fin = new Date(props.reserva.fin);
    
    return ahora >= ventanaInicio && ahora <= fin;
};

const tieneCheckin = () => {
    return props.reserva.checkins && props.reserva.checkins.some(c => c.tipo === 'checkin');
};

const hacerCheckin = () => {
    router.post(route('reservas.checkin', props.reserva.id), {}, {
        preserveScroll: true
    });
};

const hacerCheckout = () => {
    router.post(route('reservas.checkout', props.reserva.id), {}, {
        preserveScroll: true
    });
};

const cancelarReserva = () => {
    if (confirm('¿Estás seguro de que deseas cancelar esta reserva?')) {
        router.delete(route('reservas.destroy', props.reserva.id));
    }
};

const getCheckinTexto = (tipo) => {
    return tipo === 'checkin' ? 'Check-in' : 'Check-out';
};
</script>

<template>
    <Head :title="`Reserva - Aula ${reserva.aula.codigo}`" />

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
                        Volver a mis reservas
                    </button>
                </div>

                <div class="md:flex md:items-center md:justify-between">
                    <div class="flex-1 min-w-0">
                        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                            Aula {{ reserva.aula.codigo }}
                        </h2>
                        <div class="mt-1 flex items-center">
                            <span
                                :class="getEstadoBadge(reserva.estado)"
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                            >
                                {{ getEstadoTexto(reserva.estado) }}
                            </span>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="mt-4 flex flex-wrap gap-2 md:mt-0 md:ml-4">
                        <button
                            v-if="puedeHacerCheckin() && !tieneCheckin()"
                            @click="hacerCheckin"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                        >
                            <CheckCircleIcon class="h-5 w-5 mr-2" />
                            Hacer check-in
                        </button>

                        <button
                            v-if="reserva.estado === 'en_uso'"
                            @click="hacerCheckout"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Hacer check-out
                        </button>

                        <button
                            v-if="['confirmada', 'aprobada', 'pendiente'].includes(reserva.estado)"
                            @click="cancelarReserva"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                        >
                            <XCircleIcon class="h-5 w-5 mr-2" />
                            Cancelar reserva
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Columna principal -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Detalles de la reserva -->
                    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                        <div class="px-6 py-5 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">
                                Detalles de la Reserva
                            </h3>
                        </div>
                        <div class="px-6 py-5">
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 flex items-center">
                                        <CalendarIcon class="h-5 w-5 mr-2 text-gray-400" />
                                        Fecha
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatFecha(reserva.inicio) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Hora de inicio</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatHora(reserva.inicio) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Hora de fin</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatHora(reserva.fin) }}</dd>
                                </div>
                                <div v-if="reserva.asistentes_estimados" class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Asistentes estimados</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ reserva.asistentes_estimados }} personas</dd>
                                </div>
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Propósito</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ reserva.proposito }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Información del aula -->
                    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                        <div class="px-6 py-5 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">
                                Información del Aula
                            </h3>
                        </div>
                        <div class="px-6 py-5">
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Código</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ reserva.aula.codigo }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Capacidad</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ reserva.aula.capacidad }} personas</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Tipo</dt>
                                    <dd class="mt-1 text-sm text-gray-900 capitalize">{{ reserva.aula.tipo.replace('_', ' ') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 flex items-center">
                                        <MapPinIcon class="h-5 w-5 mr-2 text-gray-400" />
                                        Ubicación
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ reserva.aula.modulo.nombre }}</dd>
                                </div>
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Facultad</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ reserva.aula.modulo.facultad.sigla }} - {{ reserva.aula.modulo.facultad.nombre }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Historial de check-ins -->
                    <div v-if="reserva.checkins && reserva.checkins.length > 0" class="bg-white shadow-sm rounded-lg border border-gray-200">
                        <div class="px-6 py-5 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">
                                Historial de Check-ins
                            </h3>
                        </div>
                        <div class="px-6 py-5">
                            <div class="flow-root">
                                <ul role="list" class="-mb-8">
                                    <li
                                        v-for="(checkin, idx) in reserva.checkins"
                                        :key="checkin.id"
                                        class="relative pb-8"
                                    >
                                        <span
                                            v-if="idx !== reserva.checkins.length - 1"
                                            class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"
                                            aria-hidden="true"
                                        />
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span
                                                    :class="checkin.tipo === 'checkin' ? 'bg-green-500' : 'bg-blue-500'"
                                                    class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white"
                                                >
                                                    <ClockIcon class="h-5 w-5 text-white" />
                                                </span>
                                            </div>
                                            <div class="flex min-w-0 flex-1 justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-900 font-medium">
                                                        {{ getCheckinTexto(checkin.tipo) }}
                                                    </p>
                                                    <p class="mt-0.5 text-sm text-gray-500">
                                                        {{ formatFechaHora(checkin.registrado_en) }}
                                                    </p>
                                                </div>
                                                <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                                    {{ checkin.origen }}
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna lateral -->
                <div class="space-y-6">
                    <!-- Código QR -->
                    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                        <div class="px-6 py-5 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">
                                Código QR
                            </h3>
                        </div>
                        <div class="px-6 py-5">
                            <div class="flex flex-col items-center">
                                <div class="p-4 bg-white rounded-lg border-2 border-gray-100 flex items-center justify-center">
                                    <QrcodeVue v-if="reserva.qr_code && reserva.qr_code.startsWith('http')" :value="reserva.qr_code" :size="200" level="H" />
                                    <div v-else class="text-center text-gray-400">
                                        <QrCodeIcon class="h-32 w-32 mx-auto" />
                                        <p class="text-sm">QR no disponible</p>
                                    </div>
                                </div>
                                <div class="mt-4 flex gap-2">
                                     <button @click="router.visit(route('reservas.escanear'))" class="btn btn-primary text-xs">
                                        Escanear QR
                                     </button>
                                </div>
                                <p class="mt-3 text-xs font-mono text-gray-500 text-center break-all">
                                    {{ reserva.qr_code }}
                                </p>
                                <p class="mt-2 text-xs text-gray-500 text-center">
                                    Muestra este código al administrador
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Usuario -->
                    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                        <div class="px-6 py-5 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">
                                Usuario
                            </h3>
                        </div>
                        <div class="px-6 py-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <UserIcon class="h-6 w-6 text-blue-600" />
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ reserva.usuario.nombre }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ reserva.usuario.email }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Blockchain -->
                    <div v-if="reserva.asiento_blockchain" class="bg-white shadow-sm rounded-lg border border-gray-200">
                        <div class="px-6 py-5 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">
                                Registro Blockchain
                            </h3>
                        </div>
                        <div class="px-6 py-5">
                            <div class="flex items-start">
                                <CheckCircleIcon class="h-5 w-5 text-green-500 mt-0.5" />
                                <div class="ml-3">
                                    <p class="text-sm text-gray-700">
                                        Esta reserva está registrada en blockchain
                                    </p>
                                    <p class="mt-2 text-xs font-mono text-gray-500 break-all">
                                        Hash: {{ reserva.asiento_blockchain.hash }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Estado -->
                    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                        <div class="px-6 py-5">
                            <h3 class="text-sm font-medium text-gray-900 mb-3">
                                Estado de la Reserva
                            </h3>
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <div
                                        :class="[
                                            'h-2 w-2 rounded-full mr-2',
                                            reserva.estado === 'cancelada' ? 'bg-red-500' : 
                                            reserva.estado === 'completada' ? 'bg-gray-400' :
                                            'bg-green-500'
                                        ]"
                                    />
                                    <span class="text-sm text-gray-700">
                                        {{ getEstadoTexto(reserva.estado) }}
                                    </span>
                                </div>
                                <div v-if="tieneCheckin()" class="flex items-center text-sm text-green-700">
                                    <CheckCircleIcon class="h-5 w-5 mr-2 text-green-500" />
                                    Check-in realizado
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>