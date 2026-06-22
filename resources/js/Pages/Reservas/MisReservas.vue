<script setup>
import { computed } from "vue";
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

const reservasPorEstado = computed(() => {
    const conteos = {};

    props.reservas.forEach((reserva) => {
        conteos[reserva.estado] = (conteos[reserva.estado] || 0) + 1;
    });

    return Object.entries(conteos).map(([estado, total]) => ({
        estado,
        total,
    }));
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
        confirmada: "border border-emerald-200 bg-emerald-100 text-emerald-800",
        aprobada: "border border-emerald-200 bg-emerald-100 text-emerald-800",
        pendiente: "border border-amber-200 bg-amber-100 text-amber-800",
        en_uso: "border border-blue-200 bg-blue-100 text-blue-800",
        completada: "border border-slate-200 bg-slate-100 text-slate-700",
        cancelada: "border border-red-200 bg-red-100 text-red-800",
    };
    return badges[estado] || "border border-slate-200 bg-slate-100 text-slate-700";
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
                // La pagina se recargara con los datos actualizados
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
                // La pagina se recargara con los datos actualizados
            },
        },
    );
};

const cancelarReserva = (reservaId) => {
    if (confirm("Estas seguro de que deseas cancelar esta reserva?")) {
        router.delete(route("reservas.destroy", reservaId), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Mis Reservas" />

    <AuthenticatedLayout>
        <div class="min-h-screen w-full bg-[#F5F7FB] text-[#0F172A]">
            <div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                        <div class="min-w-0">
                            <p class="text-xs font-bold uppercase tracking-widest text-blue-600">
                                Gestion personal
                            </p>
                            <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                                Mis reservas
                            </h1>
                            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">
                                Consulta tus solicitudes, revisa codigos QR y ejecuta las acciones disponibles segun el estado de cada reserva.
                            </p>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                            <div class="rounded-xl border border-blue-200 bg-blue-50 px-5 py-3 text-center">
                                <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Total</p>
                                <p class="text-2xl font-black text-slate-900">{{ reservas.length }}</p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <button
                                    @click="router.visit(route('reservas.escanear'))"
                                    class="inline-flex items-center justify-center rounded-xl border border-transparent bg-blue-600 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="mr-2 h-5 w-5"
                                        aria-hidden="true"
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
                                    </svg>
                                    Escanear
                                </button>
                                <button
                                    @click="router.visit(route('aulas.disponibles'))"
                                    class="inline-flex items-center justify-center rounded-xl border border-transparent bg-blue-600 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                >
                                    Nueva reserva
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                <div v-if="reservas.length > 0" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <div
                        v-for="item in reservasPorEstado"
                        :key="item.estado"
                        :class="{
                            'border-l-emerald-500': ['confirmada', 'aprobada'].includes(item.estado),
                            'border-l-amber-500': item.estado === 'pendiente',
                            'border-l-blue-500': item.estado === 'en_uso',
                            'border-l-slate-400': item.estado === 'completada',
                            'border-l-red-500': item.estado === 'cancelada',
                        }"
                        class="rounded-2xl border border-l-4 border-slate-200 bg-white p-4 shadow-sm"
                    >
                        <span
                            :class="getEstadoBadge(item.estado)"
                            class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold"
                        >
                            {{ getEstadoTexto(item.estado) }}
                        </span>
                        <p class="mt-3 text-3xl font-black text-slate-900">{{ item.total }}</p>
                    </div>
                </div>

                <section
                    v-if="reservas.length === 0"
                    class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center shadow-sm sm:p-12"
                >
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl border border-blue-200 bg-blue-50 text-blue-600">
                        <CalendarIcon class="h-8 w-8" aria-hidden="true" />
                    </div>
                    <h3 class="mt-5 text-lg font-bold text-slate-900">
                        No tienes reservas
                    </h3>
                    <p class="mx-auto mt-2 max-w-md text-sm text-slate-600">
                        Comienza buscando un aula disponible para tus actividades academicas.
                    </p>
                    <button
                        @click="router.visit(route('aulas.disponibles'))"
                        class="mt-6 inline-flex items-center justify-center rounded-xl border border-transparent bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        Buscar aulas
                    </button>
                </section>

                <div v-else class="grid gap-4">
                    <article
                        v-for="reserva in reservas"
                        :key="reserva.id"
                        class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                    >
                        <div class="grid gap-0 lg:grid-cols-[1fr_auto]">
                            <div class="p-5 sm:p-6">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-3">
                                            <h2 class="text-xl font-black text-slate-900">
                                                Aula {{ reserva.aula.codigo }}
                                            </h2>
                                            <span
                                                :class="getEstadoBadge(reserva.estado)"
                                                class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold"
                                            >
                                                {{ getEstadoTexto(reserva.estado) }}
                                            </span>
                                        </div>
                                        <div class="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-sm font-medium text-slate-700">
                                            <span class="inline-flex items-center gap-2">
                                                <MapPinIcon class="h-4 w-4 text-blue-600" aria-hidden="true" />
                                                {{ reserva.aula.modulo.nombre }} - {{ reserva.aula.modulo.facultad.sigla }}
                                            </span>
                                            <span class="inline-flex items-center gap-2">
                                                <CalendarIcon class="h-4 w-4 text-blue-600" aria-hidden="true" />
                                                {{ formatFecha(reserva.inicio) }}
                                            </span>
                                            <span class="inline-flex items-center gap-2">
                                                <ClockIcon class="h-4 w-4 text-blue-600" aria-hidden="true" />
                                                {{ formatHora(reserva.inicio) }} - {{ formatHora(reserva.fin) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div
                                        v-if="reserva.qr_code"
                                        class="flex w-full items-center gap-3 rounded-xl border border-slate-200 bg-white p-3 shadow-sm sm:w-auto"
                                    >
                                        <QrcodeVue
                                            :value="reserva.qr_code"
                                            :size="72"
                                            level="M"
                                        />
                                        <div class="min-w-0 text-left sm:hidden">
                                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">QR activo</p>
                                            <p class="text-sm font-semibold text-slate-900">Disponible para control</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Proposito</p>
                                        <p class="mt-1 text-sm font-semibold text-slate-900">
                                            {{ reserva.proposito || 'Sin proposito registrado' }}
                                        </p>
                                    </div>
                                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Asistentes</p>
                                        <p class="mt-1 text-sm font-semibold text-slate-900">
                                            {{ reserva.asistentes_estimados || 'No registrado' }}
                                        </p>
                                    </div>
                                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Control</p>
                                        <p
                                            v-if="tieneCheckin(reserva)"
                                            class="mt-1 inline-flex items-center gap-2 text-sm font-bold text-emerald-700"
                                        >
                                            <CheckCircleIcon class="h-4 w-4" aria-hidden="true" />
                                            Check-in realizado
                                        </p>
                                        <p v-else class="mt-1 text-sm font-semibold text-slate-900">
                                            Pendiente segun horario
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col gap-2 border-t border-slate-200 p-5 lg:w-64 lg:border-l lg:border-t-0">
                                <button
                                    @click="verDetalles(reserva)"
                                    class="inline-flex items-center justify-center rounded-xl border border-blue-200 bg-white px-4 py-3 text-sm font-bold text-blue-700 shadow-sm transition hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                >
                                    Ver detalles
                                </button>

                                <button
                                    v-if="
                                        puedeHacerCheckin(reserva) &&
                                        !tieneCheckin(reserva)
                                    "
                                    @click="hacerCheckin(reserva.id)"
                                    class="inline-flex items-center justify-center rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700 shadow-sm transition hover:bg-emerald-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                                >
                                    <CheckCircleIcon class="mr-2 h-4 w-4" aria-hidden="true" />
                                    Hacer check-in
                                </button>

                                <button
                                    v-if="reserva.estado === 'en_uso'"
                                    @click="hacerCheckout(reserva.id)"
                                    class="inline-flex items-center justify-center rounded-xl border border-transparent bg-blue-600 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                >
                                    Hacer check-out
                                </button>

                                <button
                                    v-if="
                                        [
                                            'confirmada',
                                            'aprobada',
                                            'pendiente',
                                        ].includes(reserva.estado)
                                    "
                                    @click="cancelarReserva(reserva.id)"
                                    class="inline-flex items-center justify-center rounded-xl border border-transparent bg-red-600 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                >
                                    <XCircleIcon class="mr-2 h-4 w-4" aria-hidden="true" />
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
