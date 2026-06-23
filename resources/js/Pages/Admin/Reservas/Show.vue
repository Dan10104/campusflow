<script setup>
import { computed, ref } from "vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import {
    ArrowLeftIcon,
    CalendarDaysIcon,
    CheckCircleIcon,
    ClockIcon,
    MapPinIcon,
    QrCodeIcon,
    ShieldCheckIcon,
    UserCircleIcon,
    XCircleIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    reserva: {
        type: Object,
        required: true,
    },
    tiene_checkin: {
        type: Boolean,
        default: false,
    },
    tiene_checkout: {
        type: Boolean,
        default: false,
    },
    puede_aprobar: {
        type: Boolean,
        default: false,
    },
    puede_rechazar: {
        type: Boolean,
        default: false,
    },
    puede_checkout: {
        type: Boolean,
        default: false,
    },
});

const page = usePage();
const procesando = ref("");
const accionPendiente = ref(null);

const reserva = computed(() => props.reserva || {});

const estadosTexto = {
    pendiente: "Pendiente",
    aprobada: "Aprobada",
    confirmada: "Confirmada",
    en_uso: "En uso",
    completada: "Completada",
    cancelada: "Cancelada",
    liberada_auto: "Liberada auto",
};

const successMessage = computed(
    () => page.props.flash?.success || page.props.success || null,
);

const warningMessage = computed(() => page.props.flash?.warning || null);

const errorMessages = computed(() =>
    Object.values(page.props.errors || {}).filter((message) => Boolean(message)),
);

const puedeVerBlockchain = computed(
    () =>
        Boolean(page.props.auth?.isAdmin) &&
        Boolean(route().has?.("blockchain.reserva.history")),
);

const esConfirmada = computed(() =>
    ["confirmada", "aprobada"].includes(reserva.value.estado),
);

const esCompletada = computed(() => reserva.value.estado === "completada");

const esCancelada = computed(() =>
    ["cancelada", "liberada_auto", "rechazada"].includes(reserva.value.estado),
);

const historialCompleto = computed(
    () => props.tiene_checkin && props.tiene_checkout,
);

const controlReserva = computed(() => {
    if (historialCompleto.value) {
        return {
            label: "Finalizada",
            className: "border-slate-200 bg-slate-100 text-slate-700",
        };
    }

    if (props.tiene_checkin) {
        return {
            label: "En uso",
            className: "border-emerald-200 bg-emerald-50 text-emerald-800",
        };
    }

    return {
        label: "Sin check-in",
        className: "border-amber-200 bg-amber-50 text-amber-800",
    };
});

const resumenSuperior = computed(() => [
    {
        label: "Estado",
        value: getEstadoTexto(reserva.value.estado),
        detail: "Situacion actual",
        icon: ShieldCheckIcon,
    },
    {
        label: "Aula",
        value: `Aula ${reserva.value.aula?.codigo || reserva.value.aula_codigo || "N/D"}`,
        detail: reserva.value.aula?.modulo?.nombre || "Modulo no registrado",
        icon: MapPinIcon,
    },
    {
        label: "Usuario",
        value: reserva.value.usuario?.nombre || "Usuario no disponible",
        detail: reserva.value.usuario?.email || "Sin correo",
        icon: UserCircleIcon,
    },
    {
        label: "Fecha",
        value: formatFecha(reserva.value.inicio),
        detail: "Reserva de aula",
        icon: CalendarDaysIcon,
    },
    {
        label: "Horario",
        value: `${formatHora(reserva.value.inicio)} - ${formatHora(reserva.value.fin)}`,
        detail: duracionReserva.value,
        icon: ClockIcon,
    },
    {
        label: "Control",
        value: controlReserva.value.label,
        detail: props.tiene_checkout ? "Check-out registrado" : "Seguimiento de uso",
        icon: CheckCircleIcon,
    },
]);

const duracionReserva = computed(() => {
    if (!reserva.value.inicio || !reserva.value.fin) {
        return "Duracion no disponible";
    }

    const inicio = new Date(reserva.value.inicio);
    const fin = new Date(reserva.value.fin);
    const minutos = Math.max(0, Math.round((fin.getTime() - inicio.getTime()) / 60000));
    const horas = Math.floor(minutos / 60);
    const resto = minutos % 60;

    if (horas > 0 && resto > 0) {
        return `${horas} h ${resto} min`;
    }

    if (horas > 0) {
        return `${horas} h`;
    }

    return `${resto} min`;
});

function ejecutarPost(accion, url) {
    if (procesando.value) return;

    procesando.value = accion;

    router.post(
        url,
        {},
        {
            preserveScroll: true,
            onFinish: () => {
                procesando.value = "";
            },
            onSuccess: () => {
                accionPendiente.value = null;
            },
        },
    );
}

const aprobarReserva = () => {
    if (procesando.value) return;

    accionPendiente.value = {
        accion: "aprobar",
        url: route("admin.reservas.aprobar", reserva.value.id),
        title: "Aprobar reserva",
        message: "La solicitud será confirmada y el solicitante podrá realizar el check-in dentro del horario permitido.",
        confirmText: "Aprobar",
        variant: "primary",
    };
};

const rechazarReserva = () => {
    if (procesando.value) return;

    accionPendiente.value = {
        accion: "rechazar",
        url: route("admin.reservas.rechazar", reserva.value.id),
        title: "Rechazar reserva",
        message: "La solicitud será rechazada y dejará de bloquear el aula. El registro permanecerá en el historial.",
        confirmText: "Rechazar reserva",
        variant: "danger",
    };
};

const registrarCheckout = () => {
    if (procesando.value) return;

    accionPendiente.value = {
        accion: "checkout",
        url: route("reservas.checkout", reserva.value.id),
        title: "Registrar check-out",
        message: "Se registrará el check-out y la reserva quedará finalizada. El historial se conservará.",
        confirmText: "Registrar check-out",
        variant: "warning",
    };
};

const cerrarConfirmacion = () => {
    if (procesando.value) return;

    accionPendiente.value = null;
};

const confirmarAccion = () => {
    if (!accionPendiente.value || procesando.value) return;

    ejecutarPost(accionPendiente.value.accion, accionPendiente.value.url);
};

function formatFecha(fecha) {
    if (!fecha) return "Sin fecha";

    return new Date(fecha).toLocaleDateString("es-BO", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
}

function formatHora(fecha) {
    if (!fecha) return "--:--";

    return new Date(fecha).toLocaleTimeString("es-BO", {
        hour: "2-digit",
        minute: "2-digit",
    });
}

function formatFechaHora(fecha) {
    if (!fecha) return "Sin registro";

    return `${formatFecha(fecha)} - ${formatHora(fecha)}`;
}

function getEstadoTexto(estado) {
    return estadosTexto[estado] || estado || "Sin estado";
}

function getEstadoBadge(estado) {
    const badges = {
        pendiente: "border-amber-200 bg-amber-50 text-amber-800",
        aprobada: "border-blue-200 bg-blue-50 text-blue-800",
        confirmada: "border-blue-200 bg-blue-50 text-blue-800",
        en_uso: "border-emerald-200 bg-emerald-50 text-emerald-800",
        completada: "border-slate-200 bg-slate-100 text-slate-700",
        cancelada: "border-red-200 bg-red-50 text-red-800",
        liberada_auto: "border-cyan-200 bg-cyan-50 text-cyan-800",
    };

    return badges[estado] || "border-slate-200 bg-slate-100 text-slate-700";
}

function getTipoControlTexto(tipo) {
    return tipo === "checkout" ? "Check-out" : "Check-in";
}

function getTipoControlBadge(tipo) {
    return tipo === "checkout"
        ? "border-green-200 bg-green-50 text-green-800"
        : "border-blue-200 bg-blue-50 text-blue-800";
}

function getOrigenTexto(origen) {
    const origenes = {
        qr: "QR",
        manual: "Manual",
        nfc: "NFC",
    };

    return origenes[origen] || origen || "Sin origen";
}
</script>

<template>
    <Head :title="`Reserva #${reserva.id}`" />

    <AuthenticatedLayout>
        <div class="min-h-screen bg-[#F5F7FB] px-4 py-6 text-[#0F172A] sm:px-6 lg:px-8">
            <div class="mx-auto flex w-full max-w-7xl flex-col gap-5">
                <section class="rounded-2xl border border-[#E2E8F0] bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div class="min-w-0">
                            <Link
                                :href="route('admin.reservas.index')"
                                class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-100"
                            >
                                <ArrowLeftIcon class="h-4 w-4" aria-hidden="true" />
                                Volver a gestion de reservas
                            </Link>

                            <div class="mt-5 flex flex-wrap items-center gap-3">
                                <h1 class="text-2xl font-black text-[#0F172A] sm:text-3xl">
                                    Reserva #{{ reserva.id }}
                                </h1>
                                <span
                                    class="inline-flex rounded-full border px-3 py-1 text-xs font-black"
                                    :class="getEstadoBadge(reserva.estado)"
                                >
                                    {{ getEstadoTexto(reserva.estado) }}
                                </span>
                            </div>
                            <p class="mt-2 max-w-3xl text-sm font-medium leading-6 text-[#475569] sm:text-base">
                                Detalle administrativo de la reserva de aula.
                            </p>
                        </div>

                        <Link
                            v-if="puedeVerBlockchain"
                            :href="route('blockchain.reserva.history', reserva.id)"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 py-2.5 text-sm font-bold text-blue-700 transition hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        >
                            <ShieldCheckIcon class="h-4 w-4" aria-hidden="true" />
                            Ver trazabilidad Blockchain
                        </Link>
                    </div>
                </section>

                <section v-if="successMessage" class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-700">
                    {{ successMessage }}
                </section>

                <section v-if="warningMessage" class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm font-bold text-amber-700">
                    {{ warningMessage }}
                </section>

                <section v-if="errorMessages.length > 0" class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-bold text-red-700">
                    <p v-for="message in errorMessages" :key="message">
                        {{ message }}
                    </p>
                </section>

                <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-6">
                    <article
                        v-for="item in resumenSuperior"
                        :key="item.label"
                        class="rounded-2xl border border-[#E2E8F0] bg-white p-4 shadow-sm"
                    >
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-blue-100 bg-blue-50 text-blue-700">
                                <component :is="item.icon" class="h-5 w-5" aria-hidden="true" />
                            </span>
                            <div class="min-w-0">
                                <p class="text-xs font-black uppercase tracking-wide text-slate-500">
                                    {{ item.label }}
                                </p>
                                <p class="mt-1 truncate text-sm font-black text-[#0F172A]">
                                    {{ item.value }}
                                </p>
                                <p class="truncate text-xs font-medium text-[#64748B]">
                                    {{ item.detail }}
                                </p>
                            </div>
                        </div>
                    </article>
                </section>

                <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_360px]">
                    <main class="flex flex-col gap-5">
                        <section class="rounded-2xl border border-[#E2E8F0] bg-white p-5 shadow-sm">
                            <div class="flex items-center justify-between gap-3 border-b border-[#E2E8F0] pb-4">
                                <div>
                                    <h2 class="text-lg font-black text-[#0F172A]">
                                        Informacion de la reserva
                                    </h2>
                                    <p class="text-sm font-medium text-[#475569]">
                                        Datos generales registrados para esta solicitud.
                                    </p>
                                </div>
                            </div>

                            <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                                <div class="sm:col-span-2 rounded-xl bg-slate-50 p-4">
                                    <dt class="text-xs font-black uppercase tracking-wide text-slate-500">
                                        Motivo
                                    </dt>
                                    <dd class="mt-2 break-words text-sm font-semibold leading-6 text-[#0F172A]">
                                        {{ reserva.proposito || "Sin motivo registrado" }}
                                    </dd>
                                </div>

                                <div class="rounded-xl bg-slate-50 p-4">
                                    <dt class="text-xs font-black uppercase tracking-wide text-slate-500">
                                        Creacion
                                    </dt>
                                    <dd class="mt-2 text-sm font-bold text-[#0F172A]">
                                        {{ formatFechaHora(reserva.created_at) }}
                                    </dd>
                                </div>

                                <div class="rounded-xl bg-slate-50 p-4">
                                    <dt class="text-xs font-black uppercase tracking-wide text-slate-500">
                                        Ultima actualizacion
                                    </dt>
                                    <dd class="mt-2 text-sm font-bold text-[#0F172A]">
                                        {{ formatFechaHora(reserva.updated_at) }}
                                    </dd>
                                </div>

                                <div class="rounded-xl bg-slate-50 p-4">
                                    <dt class="text-xs font-black uppercase tracking-wide text-slate-500">
                                        Horario completo
                                    </dt>
                                    <dd class="mt-2 text-sm font-bold text-[#0F172A]">
                                        {{ formatFechaHora(reserva.inicio) }} a {{ formatHora(reserva.fin) }}
                                    </dd>
                                    <dd class="mt-1 text-xs font-medium text-[#64748B]">
                                        {{ duracionReserva }}
                                    </dd>
                                </div>

                                <div class="rounded-xl bg-slate-50 p-4">
                                    <dt class="text-xs font-black uppercase tracking-wide text-slate-500">
                                        Asistentes estimados
                                    </dt>
                                    <dd class="mt-2 text-sm font-bold text-[#0F172A]">
                                        {{ reserva.asistentes_estimados || "No registrado" }}
                                    </dd>
                                </div>
                            </dl>
                        </section>

                        <section class="grid gap-5 xl:grid-cols-2">
                            <article class="rounded-2xl border border-[#E2E8F0] bg-white p-5 shadow-sm">
                                <h2 class="text-lg font-black text-[#0F172A]">
                                    Solicitante
                                </h2>
                                <dl class="mt-5 grid gap-3">
                                    <div class="rounded-xl bg-slate-50 p-4">
                                        <dt class="text-xs font-black uppercase tracking-wide text-slate-500">
                                            Nombre
                                        </dt>
                                        <dd class="mt-1 break-words text-sm font-bold text-[#0F172A]">
                                            {{ reserva.usuario?.nombre || "Usuario no disponible" }}
                                        </dd>
                                    </div>
                                    <div class="rounded-xl bg-slate-50 p-4">
                                        <dt class="text-xs font-black uppercase tracking-wide text-slate-500">
                                            Correo
                                        </dt>
                                        <dd class="mt-1 break-words text-sm font-bold text-[#0F172A]">
                                            {{ reserva.usuario?.email || "Sin correo registrado" }}
                                        </dd>
                                    </div>
                                    <div class="rounded-xl bg-slate-50 p-4">
                                        <dt class="text-xs font-black uppercase tracking-wide text-slate-500">
                                            Código
                                        </dt>
                                        <dd class="mt-1 text-sm font-bold text-[#0F172A]">
                                            {{ reserva.usuario?.codigo || reserva.usuario_id || "No disponible" }}
                                        </dd>
                                    </div>
                                </dl>
                            </article>

                            <article class="rounded-2xl border border-[#E2E8F0] bg-white p-5 shadow-sm">
                                <h2 class="text-lg font-black text-[#0F172A]">
                                    Aula
                                </h2>
                                <dl class="mt-5 grid gap-3">
                                    <div class="rounded-xl bg-slate-50 p-4">
                                        <dt class="text-xs font-black uppercase tracking-wide text-slate-500">
                                            Identificador
                                        </dt>
                                        <dd class="mt-1 text-sm font-bold text-[#0F172A]">
                                            Aula {{ reserva.aula?.codigo || reserva.aula_codigo }}
                                        </dd>
                                    </div>
                                    <div class="grid gap-3 sm:grid-cols-2">
                                        <div class="rounded-xl bg-slate-50 p-4">
                                            <dt class="text-xs font-black uppercase tracking-wide text-slate-500">
                                                Capacidad
                                            </dt>
                                            <dd class="mt-1 text-sm font-bold text-[#0F172A]">
                                                {{ reserva.aula?.capacidad || "No registrada" }}
                                            </dd>
                                        </div>
                                        <div class="rounded-xl bg-slate-50 p-4">
                                            <dt class="text-xs font-black uppercase tracking-wide text-slate-500">
                                                Tipo
                                            </dt>
                                            <dd class="mt-1 text-sm font-bold capitalize text-[#0F172A]">
                                                {{ reserva.aula?.tipo?.replace("_", " ") || "No registrado" }}
                                            </dd>
                                        </div>
                                    </div>
                                    <div class="rounded-xl bg-slate-50 p-4">
                                        <dt class="text-xs font-black uppercase tracking-wide text-slate-500">
                                            Modulo
                                        </dt>
                                        <dd class="mt-1 break-words text-sm font-bold text-[#0F172A]">
                                            {{ reserva.aula?.modulo?.nombre || "Modulo no registrado" }}
                                        </dd>
                                        <dd class="mt-1 text-xs font-medium text-[#64748B]">
                                            {{ reserva.aula?.modulo?.ubicacion || "Sin ubicacion registrada" }}
                                        </dd>
                                    </div>
                                    <div class="rounded-xl bg-slate-50 p-4">
                                        <dt class="text-xs font-black uppercase tracking-wide text-slate-500">
                                            Facultad
                                        </dt>
                                        <dd class="mt-1 break-words text-sm font-bold text-[#0F172A]">
                                            {{ reserva.aula?.modulo?.facultad?.sigla || "N/D" }}
                                            <span v-if="reserva.aula?.modulo?.facultad?.nombre">
                                                - {{ reserva.aula.modulo.facultad.nombre }}
                                            </span>
                                        </dd>
                                    </div>
                                </dl>
                            </article>
                        </section>

                        <section class="rounded-2xl border border-[#E2E8F0] bg-white p-5 shadow-sm">
                            <div class="flex items-center justify-between gap-3 border-b border-[#E2E8F0] pb-4">
                                <div>
                                    <h2 class="text-lg font-black text-[#0F172A]">
                                        Historial de uso
                                    </h2>
                                    <p class="text-sm font-medium text-[#475569]">
                                        Registros cronologicos de check-in y check-out.
                                    </p>
                                </div>
                            </div>

                            <div v-if="reserva.checkins?.length" class="mt-5 space-y-3">
                                <article
                                    v-for="checkin in reserva.checkins"
                                    :key="`${checkin.tipo}-${checkin.registrado_en}`"
                                    class="rounded-2xl border border-[#E2E8F0] bg-white p-4"
                                >
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                        <div class="flex min-w-0 items-center gap-3">
                                            <span
                                                class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border"
                                                :class="getTipoControlBadge(checkin.tipo)"
                                            >
                                                <CheckCircleIcon v-if="checkin.tipo === 'checkin'" class="h-5 w-5" aria-hidden="true" />
                                                <XCircleIcon v-else class="h-5 w-5" aria-hidden="true" />
                                            </span>
                                            <div class="min-w-0">
                                                <p class="font-black text-[#0F172A]">
                                                    {{ getTipoControlTexto(checkin.tipo) }}
                                                </p>
                                                <p class="text-sm font-medium text-[#475569]">
                                                    {{ formatFechaHora(checkin.registrado_en) }}
                                                </p>
                                            </div>
                                        </div>
                                        <span
                                            class="inline-flex w-fit rounded-full border px-3 py-1 text-xs font-black"
                                            :class="getTipoControlBadge(checkin.tipo)"
                                        >
                                            {{ getOrigenTexto(checkin.origen) }}
                                        </span>
                                    </div>
                                </article>
                            </div>

                            <div v-else class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-5 text-sm font-semibold text-[#475569]">
                                Todavia no se registro actividad de uso para esta reserva.
                            </div>
                        </section>
                    </main>

                    <aside class="flex flex-col gap-5">
                        <section class="rounded-2xl border border-[#E2E8F0] bg-white p-5 shadow-sm">
                            <h2 class="text-lg font-black text-[#0F172A]">
                                Acciones administrativas
                            </h2>
                            <p class="mt-1 text-sm font-medium text-[#475569]">
                                Acciones disponibles segun el estado actual.
                            </p>

                            <div class="mt-5 space-y-3">
                                <template v-if="props.puede_aprobar || props.puede_rechazar">
                                    <button
                                        v-if="props.puede_aprobar"
                                        type="button"
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#2563EB] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#1D4ED8] focus:outline-none focus:ring-2 focus:ring-blue-200 disabled:cursor-not-allowed disabled:bg-blue-300"
                                        :disabled="Boolean(procesando)"
                                        @click="aprobarReserva"
                                    >
                                        <CheckCircleIcon class="h-5 w-5" aria-hidden="true" />
                                        {{ procesando === "aprobar" ? "Procesando..." : "Aprobar reserva" }}
                                    </button>

                                    <button
                                        v-if="props.puede_rechazar"
                                        type="button"
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700 transition hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-100 disabled:cursor-not-allowed disabled:opacity-70"
                                        :disabled="Boolean(procesando)"
                                        @click="rechazarReserva"
                                    >
                                        <XCircleIcon class="h-5 w-5" aria-hidden="true" />
                                        {{ procesando === "rechazar" ? "Procesando..." : "Rechazar reserva" }}
                                    </button>
                                </template>

                                <div v-else-if="esConfirmada" class="rounded-2xl border border-blue-200 bg-blue-50 p-4">
                                    <h3 class="font-black text-blue-900">
                                        Esperando check-in
                                    </h3>
                                    <p class="mt-2 text-sm font-medium leading-6 text-blue-800">
                                        El usuario debe presentar el codigo QR para que el personal autorizado registre el inicio de uso.
                                    </p>
                                    <Link
                                        :href="route('reservas.escanear')"
                                        class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl border border-blue-200 bg-white px-4 py-2.5 text-sm font-bold text-blue-700 transition hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                    >
                                        <QrCodeIcon class="h-5 w-5" aria-hidden="true" />
                                        Abrir escaner QR
                                    </Link>
                                </div>

                                <button
                                    v-else-if="props.puede_checkout"
                                    type="button"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-100 disabled:cursor-not-allowed disabled:bg-emerald-300"
                                    :disabled="Boolean(procesando)"
                                    @click="registrarCheckout"
                                >
                                    <CheckCircleIcon class="h-5 w-5" aria-hidden="true" />
                                    {{ procesando === "checkout" ? "Procesando..." : "Registrar check-out" }}
                                </button>

                                <div v-else-if="reserva.estado === 'en_uso'" class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                                    <h3 class="font-black text-amber-900">
                                        Control incompleto
                                    </h3>
                                    <p class="mt-2 text-sm font-medium leading-6 text-amber-800">
                                        No se puede registrar check-out desde esta vista porque el historial no cumple las condiciones requeridas.
                                    </p>
                                </div>

                                <div v-else-if="esCompletada" class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                    <h3 class="font-black text-slate-900">
                                        Reserva finalizada
                                    </h3>
                                    <p class="mt-2 text-sm font-medium leading-6 text-slate-700">
                                        <span v-if="historialCompleto">
                                            La reserva cuenta con check-in y check-out registrados.
                                        </span>
                                        <span v-else>
                                            La reserva esta completada, pero su historial de control esta incompleto.
                                        </span>
                                    </p>
                                </div>

                                <div v-else-if="esCancelada" class="rounded-2xl border border-red-200 bg-red-50 p-4">
                                    <h3 class="font-black text-red-900">
                                        Reserva de solo lectura
                                    </h3>
                                    <p class="mt-2 text-sm font-medium leading-6 text-red-800">
                                        Esta reserva no admite acciones administrativas adicionales.
                                    </p>
                                </div>

                                <div v-else class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                    <h3 class="font-black text-slate-900">
                                        Sin acciones disponibles
                                    </h3>
                                    <p class="mt-2 text-sm font-medium leading-6 text-slate-700">
                                        El estado actual no habilita acciones desde esta pantalla.
                                    </p>
                                </div>
                            </div>
                        </section>

                        <section class="rounded-2xl border border-[#E2E8F0] bg-white p-5 shadow-sm">
                            <h2 class="text-lg font-black text-[#0F172A]">
                                Estado de control
                            </h2>
                            <div class="mt-4 space-y-3">
                                <div class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 p-3">
                                    <span class="text-sm font-bold text-[#475569]">Check-in</span>
                                    <span
                                        class="rounded-full border px-2.5 py-1 text-xs font-black"
                                        :class="props.tiene_checkin ? 'border-blue-200 bg-blue-50 text-blue-800' : 'border-slate-200 bg-slate-100 text-slate-600'"
                                    >
                                        {{ props.tiene_checkin ? "Registrado" : "Pendiente" }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 p-3">
                                    <span class="text-sm font-bold text-[#475569]">Check-out</span>
                                    <span
                                        class="rounded-full border px-2.5 py-1 text-xs font-black"
                                        :class="props.tiene_checkout ? 'border-green-200 bg-green-50 text-green-800' : 'border-slate-200 bg-slate-100 text-slate-600'"
                                    >
                                        {{ props.tiene_checkout ? "Registrado" : "Pendiente" }}
                                    </span>
                                </div>
                                <div
                                    class="flex items-center justify-between gap-3 rounded-xl p-3"
                                    :class="controlReserva.className"
                                >
                                    <span class="text-sm font-bold">Control</span>
                                    <span class="text-sm font-black">{{ controlReserva.label }}</span>
                                </div>
                            </div>
                        </section>
                    </aside>
                </div>
            </div>
        </div>

        <ConfirmationModal
            :show="Boolean(accionPendiente)"
            :title="accionPendiente?.title || ''"
            :message="accionPendiente?.message || ''"
            :confirm-text="accionPendiente?.confirmText || 'Confirmar'"
            cancel-text="Volver"
            :variant="accionPendiente?.variant || 'primary'"
            :processing="Boolean(procesando)"
            @confirm="confirmarAccion"
            @cancel="cerrarConfirmacion"
            @close="cerrarConfirmacion"
        />
    </AuthenticatedLayout>
</template>
