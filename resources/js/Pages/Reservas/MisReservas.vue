<script setup>
import { ref } from "vue";
import { Head, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {
    CalendarIcon,
    ClockIcon,
    MapPinIcon,
    CheckCircleIcon,
    XCircleIcon,
} from "@heroicons/vue/24/outline";
import QrcodeVue from "qrcode.vue";

const props = defineProps({
    reservas: Array,
    usuario: Object,
});

const verDetalles = (reserva) => {
    router.visit(route("reservas.show", reserva.id));
};

const formatFecha = (fecha) => {
    return new Date(fecha).toLocaleDateString("es-BO", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

const formatHora = (fecha) => {
    return new Date(fecha).toLocaleTimeString("es-BO", {
        hour: "2-digit",
        minute: "2-digit",
    });
};

const getEstadoBadge = (estado) => {
    const badges = {
        confirmada: "bg-green-100 text-green-800",
        aprobada: "bg-blue-100 text-blue-800",
        pendiente: "bg-yellow-100 text-yellow-800",
        en_uso: "bg-purple-100 text-purple-800",
        completada: "bg-gray-100 text-gray-800",
        cancelada: "bg-red-100 text-red-800",
    };
    return badges[estado] || "bg-gray-100 text-gray-800";
};

const getEstadoTexto = (estado) => {
    const textos = {
        confirmada: "Confirmada",
        aprobada: "Aprobada",
        pendiente: "Pendiente",
        en_uso: "En Uso",
        completada: "Completada",
        cancelada: "Cancelada",
    };
    return textos[estado] || estado;
};

const puedeHacerCheckin = (reserva) => {
    if (reserva.estado !== "confirmada" && reserva.estado !== "aprobada") {
        return false;
    }

    const ahora = new Date();
    const inicio = new Date(reserva.inicio);
    const ventanaInicio = new Date(inicio.getTime() - 15 * 60000); // 15 minutos antes
    const fin = new Date(reserva.fin);

    return ahora >= ventanaInicio && ahora <= fin;
};

const tieneCheckin = (reserva) => {
    return (
        reserva.checkins && reserva.checkins.some((c) => c.tipo === "checkin")
    );
};

const hacerCheckin = (reservaId) => {
    router.post(
        route("reservas.checkin", reservaId),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                // La página se recargará con los datos actualizados
            },
        },
    );
};

const hacerCheckout = (reservaId) => {
    router.post(
        route("reservas.checkout", reservaId),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                // La página se recargará con los datos actualizados
            },
        },
    );
};

const cancelarReserva = (reservaId) => {
    if (confirm("¿Estás seguro de que deseas cancelar esta reserva?")) {
        router.delete(route("reservas.destroy", reservaId), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Mis Reservas" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="md:flex md:items-center md:justify-between">
                    <div class="flex-1 min-w-0">
                        <h2
                            class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate"
                        >
                            Mis Reservas
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            Gestiona tus reservas de aulas y espacios
                        </p>
                    </div>
                    <div class="mt-4 flex md:mt-0 md:ml-4 gap-2">
                        <button
                            @click="router.visit(route('reservas.escanear'))"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="w-5 h-5 mr-2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 13.5h1.5v1.5h-1.5v-1.5z"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918"
                                />
                            </svg>
                            Escanear
                        </button>
                        <button
                            @click="router.visit(route('aulas.disponibles'))"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Nueva reserva
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
            <!-- Sin reservas -->
            <div v-if="reservas.length === 0" class="text-center py-12">
                <CalendarIcon class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900">
                    No tienes reservas
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    Comienza reservando un aula para tus actividades
                </p>
                <div class="mt-6">
                    <button
                        @click="router.visit(route('aulas.disponibles'))"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Buscar aulas
                    </button>
                </div>
            </div>

            <!-- Lista de reservas -->
            <div v-else class="space-y-4">
                <div
                    v-for="reserva in reservas"
                    :key="reserva.id"
                    class="bg-white shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition-shadow duration-200"
                >
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <!-- Información principal -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center">
                                    <h3
                                        class="text-lg font-semibold text-gray-900"
                                    >
                                        Aula {{ reserva.aula.codigo }}
                                    </h3>
                                    <span
                                        :class="getEstadoBadge(reserva.estado)"
                                        class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    >
                                        {{ getEstadoTexto(reserva.estado) }}
                                    </span>
                                </div>

                                <div class="mt-2 space-y-2">
                                    <div
                                        class="flex items-center text-sm text-gray-600"
                                    >
                                        <MapPinIcon
                                            class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400"
                                        />
                                        {{ reserva.aula.modulo.nombre }} -
                                        {{ reserva.aula.modulo.facultad.sigla }}
                                    </div>

                                    <div
                                        class="flex items-center text-sm text-gray-600"
                                    >
                                        <CalendarIcon
                                            class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400"
                                        />
                                        {{ formatFecha(reserva.inicio) }}
                                    </div>

                                    <div
                                        class="flex items-center text-sm text-gray-600"
                                    >
                                        <ClockIcon
                                            class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400"
                                        />
                                        {{ formatHora(reserva.inicio) }} -
                                        {{ formatHora(reserva.fin) }}
                                    </div>
                                </div>

                                <p
                                    v-if="reserva.proposito"
                                    class="mt-3 text-sm text-gray-700"
                                >
                                    <span class="font-medium">Propósito:</span>
                                    {{ reserva.proposito }}
                                </p>

                                <!-- Check-ins -->
                                <div
                                    v-if="tieneCheckin(reserva)"
                                    class="mt-3 flex items-center text-sm text-green-700"
                                >
                                    <CheckCircleIcon
                                        class="h-5 w-5 mr-1.5 text-green-500"
                                    />
                                    Check-in realizado
                                </div>
                            </div>
                            <!-- QR Code Visual para Escaneo Rápido -->
                            <div
                                class="ml-4 flex-shrink-0 flex items-center justify-center p-2 bg-white rounded-lg border border-gray-200"
                            >
                                <QrcodeVue
                                    v-if="reserva.qr_code"
                                    :value="reserva.qr_code"
                                    :size="80"
                                    level="M"
                                />
                                <div
                                    v-else
                                    class="text-center text-xs text-gray-400 w-20 flex flex-col items-center"
                                >
                                    <svg
                                        class="h-8 w-8 mx-auto"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 4v16m8-8H4"
                                        />
                                    </svg>
                                    Sin QR
                                </div>
                            </div>
                        </div>

                        <!-- Acciones -->
                        <div
                            class="mt-4 pt-4 border-t border-gray-200 flex flex-wrap gap-2"
                        >
                            <button
                                @click="verDetalles(reserva)"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                Ver detalles
                            </button>

                            <!-- Check-in -->
                            <button
                                v-if="
                                    puedeHacerCheckin(reserva) &&
                                    !tieneCheckin(reserva)
                                "
                                @click="hacerCheckin(reserva.id)"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                            >
                                <CheckCircleIcon class="h-4 w-4 mr-1.5" />
                                Hacer check-in
                            </button>

                            <!-- Check-out -->
                            <button
                                v-if="reserva.estado === 'en_uso'"
                                @click="hacerCheckout(reserva.id)"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                Hacer check-out
                            </button>

                            <!-- Cancelar -->
                            <button
                                v-if="
                                    [
                                        'confirmada',
                                        'aprobada',
                                        'pendiente',
                                    ].includes(reserva.estado)
                                "
                                @click="cancelarReserva(reserva.id)"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                            >
                                <XCircleIcon class="h-4 w-4 mr-1.5" />
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
