<script setup>
import { ref, computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import {
    ArrowLeftIcon,
    CalendarIcon,
    ClockIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
    UserIcon,
    MapPinIcon,
    BuildingOfficeIcon,
    UserGroupIcon,
    XCircleIcon
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

const page = usePage();
const reservaParaAprobar = ref(null);
const procesandoAprobacion = ref(false);

const successMessage = computed(() => page.props.flash?.success || null);
const warningMessage = computed(() => page.props.flash?.warning || null);
const errorMessages = computed(() => Object.values(page.props.errors || {}).filter(Boolean));

const aprobarReserva = (reserva) => {
    if (procesandoAprobacion.value) return;

    reservaParaAprobar.value = reserva;
};

const handleEventClick = (info) => {
    const evento = info.event.extendedProps;

    if (evento.estado === 'pendiente' && isAdmin.value) {
        reservaParaAprobar.value = {
            id: info.event.id,
            usuario: { nombre: evento.usuario },
            proposito: evento.proposito,
        };

        return;
    }

    router.visit(route(isAdmin.value ? 'admin.reservas.show' : 'reservas.show', info.event.id));
};

const isAdmin = computed(() => props.isAdmin);

const cerrarConfirmacionAprobacion = () => {
    if (procesandoAprobacion.value) return;

    reservaParaAprobar.value = null;
};

const confirmarAprobacion = () => {
    if (!reservaParaAprobar.value || procesandoAprobacion.value) return;

    router.post(route('admin.reservas.aprobar', reservaParaAprobar.value.id), {}, {
        preserveScroll: true,
        onStart: () => {
            procesandoAprobacion.value = true;
        },
        onSuccess: () => {
            reservaParaAprobar.value = null;
        },
        onFinish: () => {
            procesandoAprobacion.value = false;
        },
    });
};

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
        'confirmada': 'border-emerald-200 bg-emerald-50 text-emerald-700',
        'aprobada': 'border-blue-200 bg-blue-50 text-blue-700',
        'en_uso': 'border-amber-200 bg-amber-50 text-amber-700',
        'completada': 'border-slate-200 bg-slate-100 text-slate-700',
        'cancelada': 'border-red-200 bg-red-50 text-red-700'
    };
    return badges[estado] || 'border-slate-200 bg-slate-100 text-slate-700';
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
    expandRows: true,
    buttonText: {
        today: 'Hoy',
        month: 'Mes',
        week: 'Semana',
        day: 'Día'
    },
    eventDisplay: 'block',
    eventTimeFormat: {
        hour: '2-digit',
        minute: '2-digit',
        meridiem: false
    },
    eventDidMount: (info) => {
        info.el.title = info.event.title;
    },
    // eventClick: handleEventClick // TODO: Detail modal
}));
</script>

<template>
    <Head :title="`Calendario - Aula ${aula.codigo}`" />

    <AuthenticatedLayout>
        <section class="min-w-0 space-y-5 px-4 py-5 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-[100rem] space-y-5">
                <header class="rounded-2xl border border-[var(--cf-border)] bg-[var(--cf-surface)] px-5 py-5 shadow-sm sm:px-6">
                    <div class="mb-4">
                        <button
                            @click="volver"
                            type="button"
                            class="inline-flex items-center gap-2 rounded-xl border border-[var(--cf-border)] bg-[var(--cf-surface-muted)] px-3 py-2 text-sm font-bold text-[var(--cf-text-muted)] transition hover:border-[#2563EB] hover:text-[#2563EB] focus:outline-none focus:ring-4 focus:ring-[#2563EB]/20"
                        >
                            <ArrowLeftIcon aria-hidden="true" class="h-4 w-4" />
                            Volver a aulas
                        </button>
                    </div>

                    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                        <div class="min-w-0">
                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-[#2563EB]">
                                Calendario del espacio
                            </p>
                            <h1 class="mt-1.5 text-2xl font-bold tracking-normal text-[var(--cf-heading)] sm:text-3xl">
                                Aula {{ aula.codigo }}
                            </h1>
                            <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-[var(--cf-text-muted)]">
                                <span class="inline-flex items-center gap-1.5">
                                    <MapPinIcon aria-hidden="true" class="h-4 w-4 text-[#2563EB]" />
                                    {{ aula.modulo.nombre }} - {{ aula.modulo.facultad.nombre }}
                                </span>
                                <span class="inline-flex rounded-full border border-[var(--cf-border)] bg-[var(--cf-surface-muted)] px-2.5 py-1 text-xs font-bold capitalize text-[var(--cf-text)]">
                                    {{ aula.tipo.replace('_', ' ') }}
                                </span>
                                <span class="inline-flex rounded-full border border-[var(--cf-border)] bg-[var(--cf-surface-muted)] px-2.5 py-1 text-xs font-bold text-[var(--cf-text)]">
                                    {{ aula.capacidad }} personas
                                </span>
                            </div>
                        </div>

                        <div class="grid w-full grid-cols-1 gap-2 sm:w-auto sm:grid-cols-2">
                            <a
                                :href="route('aulas.calendario.report', aula.codigo)"
                                target="_blank"
                                class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 px-3.5 py-2 text-sm font-bold text-red-700 shadow-sm transition hover:border-red-300 hover:bg-red-100 focus:outline-none focus:ring-4 focus:ring-red-500/20 focus:ring-offset-2 focus:ring-offset-[var(--cf-surface)]"
                            >
                                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                                Reporte
                            </a>
                            <button
                                @click="reservarAula"
                                type="button"
                                class="inline-flex min-h-10 items-center justify-center rounded-xl border border-[#2563EB] bg-[#2563EB] px-3.5 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-[#1D4ED8] focus:outline-none focus:ring-4 focus:ring-[#2563EB]/25"
                            >
                                Reservar aula
                            </button>
                        </div>
                    </div>
                </header>

                <section
                    v-if="successMessage || warningMessage || errorMessages.length"
                    class="space-y-3"
                >
                    <div
                        v-if="successMessage"
                        class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"
                    >
                        <CheckCircleIcon class="mt-0.5 h-5 w-5 flex-none" aria-hidden="true" />
                        <span>{{ successMessage }}</span>
                    </div>
                    <div
                        v-if="warningMessage"
                        class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700"
                    >
                        <ExclamationTriangleIcon class="mt-0.5 h-5 w-5 flex-none" aria-hidden="true" />
                        <span>{{ warningMessage }}</span>
                    </div>
                    <div
                        v-for="error in errorMessages"
                        :key="error"
                        class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700"
                    >
                        <XCircleIcon class="mt-0.5 h-5 w-5 flex-none" aria-hidden="true" />
                        <span>{{ error }}</span>
                    </div>
                </section>

                <section class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    <article class="rounded-2xl border border-[var(--cf-border)] bg-[var(--cf-surface)] p-4 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-wide text-[var(--cf-text-muted)]">Código</p>
                        <p class="mt-1 text-lg font-bold text-[var(--cf-heading)]">{{ aula.codigo }}</p>
                    </article>
                    <article class="rounded-2xl border border-[var(--cf-border)] bg-[var(--cf-surface)] p-4 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-wide text-[var(--cf-text-muted)]">Módulo</p>
                        <p class="mt-1 text-sm font-bold text-[var(--cf-heading)]">{{ aula.modulo.nombre }}</p>
                    </article>
                    <article class="rounded-2xl border border-[var(--cf-border)] bg-[var(--cf-surface)] p-4 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-wide text-[var(--cf-text-muted)]">Facultad</p>
                        <p class="mt-1 text-sm font-bold text-[var(--cf-heading)]">{{ aula.modulo.facultad.sigla }} - {{ aula.modulo.facultad.nombre }}</p>
                    </article>
                    <article class="rounded-2xl border border-[var(--cf-border)] bg-[var(--cf-surface)] p-4 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-wide text-[var(--cf-text-muted)]">Capacidad y tipo</p>
                        <p class="mt-1 text-sm font-bold capitalize text-[var(--cf-heading)]">{{ aula.capacidad }} personas · {{ aula.tipo.replace('_', ' ') }}</p>
                    </article>
                </section>

                <div class="grid min-w-0 grid-cols-1 gap-5 xl:grid-cols-12">
                    <section class="min-w-0 xl:col-span-8">
                        <article class="overflow-hidden rounded-2xl border border-[var(--cf-border)] bg-[var(--cf-surface)] shadow-sm">
                            <div class="flex flex-col gap-2 border-b border-[var(--cf-border)] px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                                <div>
                                    <h2 class="flex items-center gap-2 text-base font-bold text-[var(--cf-heading)]">
                                        <CalendarIcon aria-hidden="true" class="h-5 w-5 text-[#2563EB]" />
                                        Calendario de reservas
                                    </h2>
                                    <p class="mt-1 text-xs text-[var(--cf-text-muted)]">Vista mensual, semanal y diaria del espacio seleccionado.</p>
                                </div>
                                <span class="w-fit rounded-full border border-[var(--cf-border)] bg-[var(--cf-surface-muted)] px-2.5 py-1 text-xs font-bold text-[var(--cf-text-muted)]">
                                    {{ reservas.length }} reservas
                                </span>
                            </div>

                            <div class="cf-calendar min-w-0 p-3 sm:p-4 lg:p-5">
                                <FullCalendar :options="calendarOptions" />
                            </div>
                        </article>
                    </section>

                    <aside class="min-w-0 space-y-4 xl:col-span-4">
                        <article class="rounded-2xl border border-[var(--cf-border)] bg-[var(--cf-surface)] shadow-sm">
                            <div class="border-b border-[var(--cf-border)] px-4 py-4">
                                <h2 class="text-base font-bold text-[var(--cf-heading)]">Detalles del aula</h2>
                            </div>
                            <dl class="divide-y divide-[var(--cf-border)] px-4">
                                <div class="grid grid-cols-[6.5rem_minmax(0,1fr)] items-start gap-3 py-2.5">
                                    <dt class="text-sm font-semibold text-[var(--cf-text-muted)]">Código</dt>
                                    <dd class="min-w-0 break-words text-right text-sm font-bold text-[var(--cf-heading)]">{{ aula.codigo }}</dd>
                                </div>
                                <div class="grid grid-cols-[6.5rem_minmax(0,1fr)] items-start gap-3 py-2.5">
                                    <dt class="text-sm font-semibold text-[var(--cf-text-muted)]">Capacidad</dt>
                                    <dd class="min-w-0 break-words text-right text-sm font-bold text-[var(--cf-heading)]">{{ aula.capacidad }} personas</dd>
                                </div>
                                <div class="grid grid-cols-[6.5rem_minmax(0,1fr)] items-start gap-3 py-2.5">
                                    <dt class="text-sm font-semibold text-[var(--cf-text-muted)]">Tipo</dt>
                                    <dd class="min-w-0 break-words text-right text-sm font-bold capitalize text-[var(--cf-heading)]">{{ aula.tipo.replace('_', ' ') }}</dd>
                                </div>
                                <div class="grid grid-cols-[6.5rem_minmax(0,1fr)] items-start gap-3 py-2.5">
                                    <dt class="text-sm font-semibold text-[var(--cf-text-muted)]">Módulo</dt>
                                    <dd class="min-w-0 break-words text-right text-sm font-bold text-[var(--cf-heading)]">{{ aula.modulo.nombre }}</dd>
                                </div>
                                <div class="grid grid-cols-[6.5rem_minmax(0,1fr)] items-start gap-3 py-2.5">
                                    <dt class="text-sm font-semibold text-[var(--cf-text-muted)]">Facultad</dt>
                                    <dd class="min-w-0 break-words text-right text-sm font-bold text-[var(--cf-heading)]">
                                        {{ aula.modulo.facultad.sigla }} - {{ aula.modulo.facultad.nombre }}
                                    </dd>
                                </div>
                            </dl>
                        </article>

                        <article v-if="aula.caracteristicas && aula.caracteristicas.length > 0" class="rounded-2xl border border-[var(--cf-border)] bg-[var(--cf-surface)] p-4 shadow-sm">
                            <h2 class="text-base font-bold text-[var(--cf-heading)]">Características</h2>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <span
                                    v-for="caracteristica in aula.caracteristicas"
                                    :key="caracteristica.id"
                                    class="inline-flex items-center gap-1.5 rounded-full border border-[var(--cf-border)] bg-[var(--cf-surface-muted)] px-3 py-1.5 text-xs font-bold text-[var(--cf-text-muted)]"
                                >
                                    <span aria-hidden="true" class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    {{ caracteristica.nombre }}
                                </span>
                            </div>
                        </article>

                        <article class="rounded-2xl border border-[var(--cf-border)] bg-[var(--cf-surface)] p-4 shadow-sm">
                            <h2 class="text-base font-bold text-[var(--cf-heading)]">Resumen de reservas</h2>
                            <div class="mt-3 flex items-center justify-between rounded-xl bg-[var(--cf-surface-muted)] px-3 py-2.5">
                                <span class="text-sm font-semibold text-[var(--cf-text-muted)]">Reservas próximas</span>
                                <span class="text-lg font-bold text-[var(--cf-heading)]">{{ reservas.length }}</span>
                            </div>
                        </article>
                    </aside>
                </div>

                <article class="overflow-hidden rounded-2xl border border-[var(--cf-border)] bg-[var(--cf-surface)] shadow-sm">
                    <div class="flex flex-col gap-2 border-b border-[var(--cf-border)] px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                        <div class="min-w-0">
                            <h2 class="text-base font-bold text-[var(--cf-heading)]">Reservas agrupadas por fecha</h2>
                            <p class="mt-1 text-xs text-[var(--cf-text-muted)]">Detalle operativo de horarios, usuarios, propósito y estado.</p>
                        </div>
                        <span class="w-fit rounded-full border border-[var(--cf-border)] bg-[var(--cf-surface-muted)] px-2.5 py-1 text-xs font-bold text-[var(--cf-text-muted)]">
                            {{ reservas.length }} reservas
                        </span>
                    </div>

                    <div class="p-4 sm:p-5">
                        <div v-if="reservas.length === 0" class="rounded-2xl border border-dashed border-[var(--cf-border-strong)] bg-[var(--cf-surface-muted)] px-4 py-8 text-center">
                            <CalendarIcon aria-hidden="true" class="mx-auto h-10 w-10 text-[var(--cf-text-muted)]" />
                            <h3 class="mt-3 text-sm font-bold text-[var(--cf-heading)]">
                                No hay reservas programadas
                            </h3>
                            <p class="mt-1 text-sm text-[var(--cf-text-muted)]">
                                El aula está disponible para los próximos 30 días.
                            </p>
                            <div class="mt-4">
                                <button
                                    @click="reservarAula"
                                    type="button"
                                    class="inline-flex min-h-10 items-center justify-center rounded-xl border border-[#2563EB] bg-[#2563EB] px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-[#1D4ED8] focus:outline-none focus:ring-4 focus:ring-[#2563EB]/25"
                                >
                                    Crear primera reserva
                                </button>
                            </div>
                        </div>

                        <div v-else class="space-y-4">
                            <section
                                v-for="(reservasDelDia, fecha) in reservasPorFecha"
                                :key="fecha"
                                class="rounded-2xl border border-[var(--cf-border)] bg-[var(--cf-surface-muted)] p-3 sm:p-4"
                            >
                                <h3 class="px-1 text-sm font-bold capitalize text-[var(--cf-heading)]">
                                    {{ formatFecha(reservasDelDia[0].inicio) }}
                                </h3>

                                <div class="mt-3 grid grid-cols-1 gap-3">
                                    <article
                                        v-for="reserva in reservasDelDia"
                                        :key="reserva.id"
                                        class="rounded-xl border border-[var(--cf-border)] bg-[var(--cf-surface)] p-3 transition hover:border-[#2563EB]/50 sm:p-4"
                                    >
                                        <div class="grid min-w-0 grid-cols-1 gap-3 lg:grid-cols-[minmax(10rem,14rem)_minmax(0,1fr)_auto] lg:items-start">
                                            <p class="flex items-center gap-2 text-sm font-bold text-[var(--cf-heading)]">
                                                <ClockIcon aria-hidden="true" class="h-4 w-4 shrink-0 text-[#2563EB]" />
                                                <span>{{ formatHora(reserva.inicio) }} - {{ formatHora(reserva.fin) }}</span>
                                            </p>

                                            <div class="min-w-0">
                                                <p class="flex min-w-0 items-center gap-2 text-sm font-semibold text-[var(--cf-text-muted)]">
                                                    <UserIcon aria-hidden="true" class="h-4 w-4 shrink-0" />
                                                    <span class="min-w-0 break-words">{{ reserva.usuario.nombre }}</span>
                                                </p>
                                                <p v-if="reserva.proposito" class="mt-1.5 min-w-0 break-words text-sm text-[var(--cf-text-muted)]">
                                                    {{ reserva.proposito }}
                                                </p>
                                            </div>

                                            <div class="flex flex-wrap items-center gap-2 lg:justify-end">
                                                <span
                                                    :class="getEstadoBadge(reserva.estado)"
                                                    class="inline-flex rounded-full border px-2.5 py-1 text-xs font-bold"
                                                >
                                                    {{ getEstadoTexto(reserva.estado) }}
                                                </span>

                                                <button
                                                    v-if="isAdmin && reserva.estado === 'pendiente'"
                                                    @click="aprobarReserva(reserva)"
                                                    type="button"
                                                    :disabled="procesandoAprobacion && reservaParaAprobar?.id === reserva.id"
                                                    class="inline-flex min-h-8 items-center rounded-lg border border-emerald-600 bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-500/25 disabled:cursor-not-allowed disabled:bg-emerald-300"
                                                >
                                                    {{ procesandoAprobacion && reservaParaAprobar?.id === reserva.id ? 'Procesando...' : 'Aprobar' }}
                                                </button>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            </section>
                        </div>
                    </div>
                </article>
            </div>
        </section>

        <ConfirmationModal
            :show="Boolean(reservaParaAprobar)"
            title="Aprobar reserva"
            :message="`La solicitud de ${reservaParaAprobar?.usuario?.nombre || 'este usuario'} sera confirmada y el aula quedara reservada en el horario indicado.`"
            confirm-text="Aprobar"
            cancel-text="Volver"
            variant="primary"
            :processing="procesandoAprobacion"
            @confirm="confirmarAprobacion"
            @cancel="cerrarConfirmacionAprobacion"
            @close="cerrarConfirmacionAprobacion"
        />
    </AuthenticatedLayout>
</template>

<style scoped>
.cf-calendar {
    min-width: 0;
    overflow-x: clip;
    width: 100%;
}

.cf-calendar :deep(.fc) {
    color: var(--cf-text);
    font-size: 0.875rem;
    max-width: 100%;
    min-width: 0;
    width: 100%;
}

.cf-calendar :deep(.fc-toolbar) {
    align-items: center;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-bottom: 1.1rem;
}

.cf-calendar :deep(.fc-toolbar-chunk) {
    align-items: center;
    display: flex;
    flex-wrap: wrap;
    gap: 0.35rem;
}

.cf-calendar :deep(.fc-view-harness),
.cf-calendar :deep(.fc-view),
.cf-calendar :deep(.fc-scrollgrid) {
    max-width: 100%;
    min-width: 0;
    width: 100%;
}

.cf-calendar :deep(.fc-toolbar-title) {
    color: var(--cf-heading);
    font-size: clamp(1rem, 1.3vw, 1.25rem);
    font-weight: 800;
    letter-spacing: 0;
    line-height: 1.25;
}

.cf-calendar :deep(.fc-button) {
    border: 1px solid var(--cf-border-strong);
    border-radius: 0.75rem;
    background: var(--cf-surface-muted);
    color: var(--cf-text);
    box-shadow: none;
    font-size: 0.78rem;
    font-weight: 800;
    padding: 0.42rem 0.68rem;
    text-transform: none;
}

.cf-calendar :deep(.fc-button:hover),
.cf-calendar :deep(.fc-button:focus) {
    border-color: #2563EB;
    background: var(--cf-accent-soft);
    color: var(--cf-heading);
    box-shadow: var(--cf-focus-ring);
}

.cf-calendar :deep(.fc-button-primary:not(:disabled).fc-button-active) {
    border-color: #2563EB;
    background: #2563EB;
    color: #ffffff;
}

.cf-calendar :deep(.fc-scrollgrid),
.cf-calendar :deep(.fc-theme-standard td),
.cf-calendar :deep(.fc-theme-standard th) {
    border-color: var(--cf-border);
}

.cf-calendar :deep(.fc-col-header-cell) {
    background: var(--cf-surface-muted);
    padding: 0.25rem 0;
}

.cf-calendar :deep(.fc-col-header-cell-cushion),
.cf-calendar :deep(.fc-daygrid-day-number) {
    color: var(--cf-text-muted);
    font-weight: 800;
    text-decoration: none;
}

.cf-calendar :deep(.fc-daygrid-day-frame) {
    min-height: 6.35rem;
    padding: 0.25rem;
}

.cf-calendar :deep(.fc-daygrid-day-top) {
    padding-right: 0.2rem;
}

.cf-calendar :deep(.fc-event) {
    border: 0;
    border-radius: 0.5rem;
    font-weight: 800;
    max-width: 100%;
    min-width: 0;
    padding: 0.14rem 0.38rem;
}

.cf-calendar :deep(.fc-event-title),
.cf-calendar :deep(.fc-event-time) {
    color: #ffffff;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.cf-calendar :deep(.fc-daygrid-event) {
    margin-top: 0.18rem;
    max-width: 100%;
}

.cf-calendar :deep(.fc-daygrid-more-link) {
    color: var(--cf-accent);
    font-size: 0.78rem;
    font-weight: 800;
}

.cf-calendar :deep(.fc-day-today) {
    background: var(--cf-accent-soft);
}

@media (max-width: 1024px) {
    .cf-calendar :deep(.fc-toolbar) {
        align-items: stretch;
    }

    .cf-calendar :deep(.fc-toolbar-chunk) {
        justify-content: center;
    }
}

@media (max-width: 640px) {
    .cf-calendar :deep(.fc-toolbar) {
        align-items: stretch;
        flex-direction: column;
    }

    .cf-calendar :deep(.fc-toolbar-chunk) {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        width: 100%;
    }

    .cf-calendar :deep(.fc-button) {
        padding: 0.4rem 0.55rem;
    }

    .cf-calendar :deep(.fc-daygrid-day-frame) {
        min-height: 4.8rem;
        padding: 0.15rem;
    }

    .cf-calendar :deep(.fc) {
        font-size: 0.78rem;
    }
}
</style>
