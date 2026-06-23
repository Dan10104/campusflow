<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import { Head, useForm, usePage, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import {
    CalendarIcon,
    UserIcon,
    CubeIcon,
    ArrowLeftIcon,
    HandThumbUpIcon,
    ArrowPathIcon,
    QrCodeIcon,
    ClockIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
} from "@heroicons/vue/24/outline";
import QrcodeVue from "qrcode.vue";

const props = defineProps({
    prestamo: Object,
    isAdmin: Boolean,
    puede_aprobar: Boolean,
    puede_rechazar: Boolean,
    puede_entregar: Boolean,
    esperando_entrega: Boolean,
    entrega_fuera_de_plazo: Boolean,
    puede_devolver: Boolean,
    entrega_disponible_desde: String,
});

const page = usePage();
const form = useForm({});
const procesando = ref("");
const accionSeleccionada = ref(null);
const mostrarConfirmacion = ref(false);
const procesandoAccion = ref(false);

const isProcessing = computed(() => form.processing || Boolean(procesando.value));
const successMessage = computed(() => page.props.flash?.success || null);
const warningMessage = computed(() => page.props.flash?.warning || null);
const errorMessages = computed(() =>
    [
        page.props.flash?.error,
        page.props.errors?.prestamo,
        ...Object.values(page.props.errors || {}),
    ].filter((message, index, messages) => Boolean(message) && messages.indexOf(message) === index),
);

const formatDate = (value) => {
    if (!value) return "No registrado";

    return new Date(value).toLocaleString();
};

const abrirConfirmacion = (config) => {
    if (isProcessing.value) return;

    accionSeleccionada.value = {
        cancelText: "Volver",
        ...config,
    };
    mostrarConfirmacion.value = true;
};

const cerrarConfirmacion = () => {
    if (procesandoAccion.value) return;

    mostrarConfirmacion.value = false;
    accionSeleccionada.value = null;
};

const confirmarAccion = () => {
    if (!accionSeleccionada.value || procesandoAccion.value) return;

    procesandoAccion.value = true;
    procesando.value = accionSeleccionada.value.tipo;

    form.post(route(accionSeleccionada.value.routeName, props.prestamo.id), {
        preserveScroll: true,
        onSuccess: () => {
            mostrarConfirmacion.value = false;
            accionSeleccionada.value = null;
        },
        onFinish: () => {
            procesandoAccion.value = false;
            procesando.value = "";
        },
    });
};

const aprobarSolicitud = () => {
    abrirConfirmacion({
        tipo: "aprobar",
        title: "Aprobar solicitud",
        message: "La solicitud será aprobada. El activo continuará bloqueado para otras solicitudes, pero todavía no se registrará como entregado.",
        confirmText: "Aprobar solicitud",
        variant: "primary",
        routeName: "prestamos.entregar",
    });
};

const rechazarSolicitud = () => {
    abrirConfirmacion({
        tipo: "rechazar",
        title: "Rechazar solicitud",
        message: "La solicitud será rechazada y el activo podrá ser solicitado nuevamente cuando no exista otro préstamo activo. El historial se conservará.",
        confirmText: "Rechazar solicitud",
        variant: "danger",
        routeName: "prestamos.rechazar",
    });
};

const registrarEntrega = () => {
    abrirConfirmacion({
        tipo: "entregar",
        title: "Registrar entrega",
        message: "Confirma que el activo fue entregado físicamente al solicitante. El préstamo pasará a Entregado y el activo quedará marcado como Prestado.",
        confirmText: "Registrar entrega",
        variant: "warning",
        routeName: "prestamos.entregar",
    });
};

const registrarDevolucion = () => {
    abrirConfirmacion({
        tipo: "devolver",
        title: "Registrar devolución",
        message: "Confirma que el activo fue recibido físicamente. El préstamo finalizará y el activo volverá a estar disponible cuando no exista otro préstamo físico activo.",
        confirmText: "Registrar devolución",
        variant: "primary",
        routeName: "prestamos.devolver",
    });
};

const getStatusColor = (status) => {
    const colors = {
        pendiente: "border border-amber-200 bg-amber-50 text-amber-800",
        aprobado: "border border-blue-200 bg-blue-50 text-blue-800",
        entregado: "border border-indigo-200 bg-indigo-50 text-indigo-800",
        devuelto: "border border-emerald-200 bg-emerald-50 text-emerald-800",
        vencido: "border border-red-200 bg-red-50 text-red-800",
        rechazado: "border border-red-200 bg-red-50 text-red-700",
    };

    return colors[status] || "border border-slate-200 bg-slate-100 text-slate-700";
};

const getStatusLabel = (status) => {
    const labels = {
        pendiente: "Pendiente",
        aprobado: "Aprobado",
        entregado: "Entregado",
        devuelto: "Devuelto",
        vencido: "Vencido",
        rechazado: "Rechazado",
    };

    return labels[status] || status;
};
</script>

<template>
    <Head title="Detalle de Préstamo" />

    <AuthenticatedLayout>
        <div class="min-h-screen w-full bg-[#F5F7FB] text-[#0F172A]">
            <div class="mx-auto max-w-6xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
                <section class="rounded-2xl border border-[#E2E8F0] bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
                        <div class="min-w-0">
                            <button
                                type="button"
                                @click="router.visit(route('prestamos.index'))"
                                class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-[#E2E8F0] bg-white px-3 py-2 text-sm font-bold text-[#334155] shadow-sm transition hover:bg-[#F8FAFC] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2"
                            >
                                <ArrowLeftIcon class="h-5 w-5 shrink-0 text-[#2563EB]" aria-hidden="true" />
                                Volver a préstamos
                            </button>

                            <div class="mt-5 flex flex-wrap items-center gap-3">
                                <p class="text-xs font-bold uppercase tracking-widest text-[#2563EB]">
                                    Detalle de solicitud
                                </p>
                                <span
                                    class="inline-flex items-center justify-center rounded-full px-3 py-1 text-center text-xs font-semibold uppercase leading-none"
                                    :class="getStatusColor(prestamo.estado)"
                                >
                                    {{ getStatusLabel(prestamo.estado) }}
                                </span>
                            </div>

                            <h1 class="mt-2 text-3xl font-bold tracking-tight text-[#0F172A]">
                                Solicitud #{{ prestamo.id }}
                            </h1>
                            <p class="mt-2 max-w-3xl text-sm leading-6 text-[#475569]">
                                Consulta los datos del préstamo, el activo asociado y las acciones disponibles.
                            </p>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                            <button
                                v-if="isAdmin && puede_aprobar"
                                type="button"
                                :disabled="isProcessing"
                                @click="aprobarSolicitud"
                                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-transparent bg-[#2563EB] px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#1D4ED8] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                <HandThumbUpIcon class="h-5 w-5 shrink-0" aria-hidden="true" />
                                {{ procesando === "aprobar" ? "Procesando..." : "Aprobar solicitud" }}
                            </button>

                            <button
                                v-if="isAdmin && puede_rechazar"
                                type="button"
                                :disabled="isProcessing"
                                @click="rechazarSolicitud"
                                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-bold text-red-700 shadow-sm transition hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-300 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                <ExclamationTriangleIcon class="h-5 w-5 shrink-0" aria-hidden="true" />
                                {{ procesando === "rechazar" ? "Procesando..." : "Rechazar solicitud" }}
                            </button>

                            <button
                                v-if="isAdmin && puede_entregar"
                                type="button"
                                :disabled="isProcessing"
                                @click="registrarEntrega"
                                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-transparent bg-[#2563EB] px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#1D4ED8] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                <CheckCircleIcon class="h-5 w-5 shrink-0" aria-hidden="true" />
                                {{ procesando === "entregar" ? "Procesando..." : "Registrar entrega" }}
                            </button>

                            <button
                                v-if="isAdmin && puede_devolver"
                                type="button"
                                :disabled="isProcessing"
                                @click="registrarDevolucion"
                                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-transparent bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                <ArrowPathIcon class="h-5 w-5 shrink-0" aria-hidden="true" />
                                {{ procesando === "devolver" ? "Procesando..." : "Registrar devolución" }}
                            </button>
                        </div>
                    </div>
                </section>

                <section v-if="successMessage || warningMessage || errorMessages.length" class="space-y-3">
                    <div
                        v-if="successMessage"
                        class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700"
                    >
                        {{ successMessage }}
                    </div>

                    <div
                        v-if="warningMessage"
                        class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm font-semibold text-amber-700"
                    >
                        {{ warningMessage }}
                    </div>

                    <div
                        v-for="error in errorMessages"
                        :key="error"
                        class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-semibold text-red-700"
                    >
                        {{ error }}
                    </div>
                </section>

                <section
                    v-if="esperando_entrega"
                    class="rounded-2xl border border-blue-200 bg-blue-50 p-5 text-blue-900 shadow-sm sm:p-6"
                >
                    <div class="flex gap-3">
                        <ClockIcon class="mt-0.5 h-6 w-6 shrink-0 text-[#2563EB]" aria-hidden="true" />
                        <div>
                            <h2 class="text-base font-bold">Solicitud aprobada</h2>
                            <p class="mt-1 text-sm leading-6">
                                La entrega podrá registrarse desde 15 minutos antes del inicio previsto.
                            </p>
                            <p v-if="entrega_disponible_desde" class="mt-2 text-sm font-semibold">
                                Disponible desde: {{ formatDate(entrega_disponible_desde) }}
                            </p>
                        </div>
                    </div>
                </section>

                <section
                    v-else-if="entrega_fuera_de_plazo"
                    class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-amber-900 shadow-sm sm:p-6"
                >
                    <div class="flex gap-3">
                        <ExclamationTriangleIcon class="mt-0.5 h-6 w-6 shrink-0 text-amber-600" aria-hidden="true" />
                        <div>
                            <h2 class="text-base font-bold">Periodo de entrega finalizado</h2>
                            <p class="mt-1 text-sm leading-6">
                                La entrega ya no puede registrarse con este préstamo.
                            </p>
                        </div>
                    </div>
                </section>

                <section
                    v-else-if="prestamo.estado === 'entregado'"
                    class="rounded-2xl border border-indigo-200 bg-indigo-50 p-5 text-indigo-900 shadow-sm sm:p-6"
                >
                    <div class="flex gap-3">
                        <CheckCircleIcon class="mt-0.5 h-6 w-6 shrink-0 text-indigo-600" aria-hidden="true" />
                        <div>
                            <h2 class="text-base font-bold">Activo entregado</h2>
                            <p class="mt-1 text-sm leading-6">
                                El equipo se encuentra actualmente en préstamo.
                            </p>
                        </div>
                    </div>
                </section>

                <section
                    v-else-if="prestamo.estado === 'devuelto'"
                    class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-emerald-900 shadow-sm sm:p-6"
                >
                    <div class="flex gap-3">
                        <CheckCircleIcon class="mt-0.5 h-6 w-6 shrink-0 text-emerald-600" aria-hidden="true" />
                        <div>
                            <h2 class="text-base font-bold">Préstamo finalizado</h2>
                            <p class="mt-1 text-sm leading-6">
                                El activo fue devuelto y está disponible nuevamente.
                            </p>
                        </div>
                    </div>
                </section>

                <section
                    v-else-if="prestamo.estado === 'rechazado'"
                    class="rounded-2xl border border-red-200 bg-red-50 p-5 text-red-900 shadow-sm sm:p-6"
                >
                    <div class="flex gap-3">
                        <ExclamationTriangleIcon class="mt-0.5 h-6 w-6 shrink-0 text-red-600" aria-hidden="true" />
                        <div>
                            <h2 class="text-base font-bold">Solicitud rechazada</h2>
                            <p class="mt-1 text-sm leading-6">
                                Esta solicitud no fue autorizada y el activo puede ser solicitado nuevamente.
                            </p>
                        </div>
                    </div>
                </section>

                <section
                    v-else-if="prestamo.estado === 'vencido'"
                    class="rounded-2xl border border-red-200 bg-red-50 p-5 text-red-900 shadow-sm sm:p-6"
                >
                    <div class="flex gap-3">
                        <ExclamationTriangleIcon class="mt-0.5 h-6 w-6 shrink-0 text-red-600" aria-hidden="true" />
                        <div>
                            <h2 class="text-base font-bold">Préstamo vencido</h2>
                            <p class="mt-1 text-sm leading-6">
                                El activo continúa pendiente de devolución.
                            </p>
                        </div>
                    </div>
                </section>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <main class="space-y-6 lg:col-span-2">
                        <section class="overflow-hidden rounded-2xl border border-[#E2E8F0] bg-white shadow-sm">
                            <div class="border-b border-[#E2E8F0] px-5 py-5 sm:px-6">
                                <h2 class="text-lg font-bold text-[#0F172A]">
                                    Información del préstamo
                                </h2>
                                <p class="mt-1 text-sm text-[#475569]">
                                    Datos principales de la solicitud registrada.
                                </p>
                            </div>

                            <dl class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-2 sm:p-6">
                                <div class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4 sm:col-span-2">
                                    <dt class="flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-[#64748B]">
                                        <CubeIcon class="h-5 w-5 shrink-0 text-[#2563EB]" aria-hidden="true" />
                                        Activo solicitado
                                    </dt>
                                    <dd class="mt-2 break-words text-lg font-bold text-[#0F172A]">
                                        {{ prestamo.activo?.descripcion || "Activo no disponible" }}
                                    </dd>
                                    <dd class="mt-2 break-words text-sm font-semibold text-[#475569]">
                                        Código:
                                        <span class="break-all font-mono text-[#334155]">{{ prestamo.activo_codigo }}</span>
                                        | Tipo:
                                        {{
                                            prestamo.activo?.tipo_activo?.nombre ||
                                            prestamo.activo?.tipoActivo?.nombre ||
                                            "General"
                                        }}
                                    </dd>
                                </div>

                                <div class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                                    <dt class="flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-[#64748B]">
                                        <UserIcon class="h-5 w-5 shrink-0 text-[#2563EB]" aria-hidden="true" />
                                        Solicitante
                                    </dt>
                                    <dd class="mt-2 break-words text-sm font-bold text-[#0F172A]">
                                        {{ prestamo.usuario?.nombre || "Usuario no disponible" }}
                                    </dd>
                                    <dd class="mt-1 text-xs font-semibold text-[#475569]">
                                        {{ prestamo.usuario_id }}
                                    </dd>
                                </div>

                                <div class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                                    <dt class="flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-[#64748B]">
                                        <CalendarIcon class="h-5 w-5 shrink-0 text-[#2563EB]" aria-hidden="true" />
                                        Horario previsto
                                    </dt>
                                    <dd class="mt-2 text-sm font-semibold text-[#0F172A]">
                                        Inicio: {{ formatDate(prestamo.inicio_previsto) }}
                                    </dd>
                                    <dd class="mt-1 text-sm font-semibold text-[#475569]">
                                        Fin: {{ formatDate(prestamo.fin_previsto) }}
                                    </dd>
                                </div>

                                <div
                                    v-if="prestamo.condicion_salida"
                                    class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4"
                                >
                                    <dt class="text-xs font-bold uppercase tracking-wide text-[#64748B]">
                                        Condición de salida
                                    </dt>
                                    <dd class="mt-2 break-words text-sm font-semibold text-[#0F172A]">
                                        {{ prestamo.condicion_salida }}
                                    </dd>
                                </div>

                                <div
                                    v-if="prestamo.condicion_retorno"
                                    class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4"
                                >
                                    <dt class="text-xs font-bold uppercase tracking-wide text-[#64748B]">
                                        Condición de retorno
                                    </dt>
                                    <dd class="mt-2 break-words text-sm font-semibold text-[#0F172A]">
                                        {{ prestamo.condicion_retorno }}
                                    </dd>
                                </div>

                                <div
                                    v-if="prestamo.entregado_en"
                                    class="rounded-xl border border-blue-200 bg-[#EFF6FF] p-4"
                                >
                                    <dt class="text-xs font-bold uppercase tracking-wide text-blue-800">
                                        Entregado el
                                    </dt>
                                    <dd class="mt-2 text-sm font-bold text-[#0F172A]">
                                        {{ formatDate(prestamo.entregado_en) }}
                                    </dd>
                                </div>

                                <div
                                    v-if="prestamo.devuelto_en"
                                    class="rounded-xl border border-emerald-200 bg-emerald-50 p-4"
                                >
                                    <dt class="text-xs font-bold uppercase tracking-wide text-emerald-800">
                                        Devuelto el
                                    </dt>
                                    <dd class="mt-2 text-sm font-bold text-[#0F172A]">
                                        {{ formatDate(prestamo.devuelto_en) }}
                                    </dd>
                                </div>
                            </dl>
                        </section>

                        <section class="rounded-2xl border border-[#E2E8F0] bg-white p-5 shadow-sm sm:p-6">
                            <h2 class="text-lg font-bold text-[#0F172A]">
                                Auditoría Blockchain
                            </h2>
                            <p class="mt-1 text-sm leading-6 text-[#475569]">
                                Acceso a la trazabilidad del activo cuando el perfil tiene permisos administrativos.
                            </p>
                            <div class="mt-4">
                                <a
                                    v-if="isAdmin"
                                    :href="route('blockchain.activo.history', prestamo.activo_codigo)"
                                    class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#E2E8F0] bg-white px-4 py-2.5 text-sm font-bold text-[#2563EB] shadow-sm transition hover:bg-[#EFF6FF] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2"
                                >
                                    Ver historial inmutable del activo
                                </a>
                            </div>
                        </section>
                    </main>

                    <aside class="space-y-6">
                        <section class="overflow-hidden rounded-2xl border border-[#E2E8F0] bg-white shadow-sm">
                            <div class="border-b border-[#E2E8F0] px-5 py-5">
                                <h2 class="flex items-center gap-2 text-lg font-bold text-[#0F172A]">
                                    <QrCodeIcon class="h-5 w-5 shrink-0 text-[#2563EB]" aria-hidden="true" />
                                    Código QR del activo
                                </h2>
                                <p class="mt-1 text-sm leading-6 text-[#475569]">
                                    Muestra o escanea este código para la entrega/devolución mediante la App Scanner.
                                </p>
                            </div>

                            <div class="p-5 sm:p-6">
                                <div class="mx-auto flex w-full max-w-xs flex-col items-center justify-center">
                                    <div class="flex w-full items-center justify-center rounded-2xl border border-[#E2E8F0] bg-white p-4">
                                        <QrcodeVue
                                            v-if="prestamo.activo?.qr_code"
                                            :value="prestamo.activo.qr_code"
                                            :size="180"
                                            level="M"
                                        />
                                        <div
                                            v-else
                                            class="flex min-h-32 flex-col items-center justify-center text-center text-[#64748B]"
                                        >
                                            <QrCodeIcon class="h-12 w-12" aria-hidden="true" />
                                            <p class="mt-2 text-sm font-semibold">Sin QR</p>
                                        </div>
                                    </div>

                                    <div class="mt-4 flex min-h-14 w-full items-center justify-center rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] px-4 py-3 text-center">
                                        <span class="break-all font-mono text-xs font-semibold leading-5 text-[#475569]">
                                            {{ prestamo.activo_codigo }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="rounded-2xl border border-[#E2E8F0] bg-white p-5 shadow-sm">
                            <h2 class="text-sm font-bold uppercase tracking-wide text-[#334155]">
                                Estado actual
                            </h2>
                            <span
                                class="mt-4 inline-flex items-center justify-center rounded-full px-3 py-1 text-center text-xs font-semibold uppercase leading-none"
                                :class="getStatusColor(prestamo.estado)"
                            >
                                {{ getStatusLabel(prestamo.estado) }}
                            </span>

                            <div v-if="isProcessing" class="mt-4 rounded-xl border border-blue-200 bg-[#EFF6FF] px-4 py-3 text-sm font-bold text-blue-800">
                                Procesando acción...
                            </div>
                        </section>
                    </aside>
                </div>
            </div>
        </div>

        <ConfirmationModal
            :show="mostrarConfirmacion"
            :title="accionSeleccionada?.title || ''"
            :message="accionSeleccionada?.message || ''"
            :confirm-text="accionSeleccionada?.confirmText || 'Confirmar'"
            :cancel-text="accionSeleccionada?.cancelText || 'Volver'"
            :variant="accionSeleccionada?.variant || 'primary'"
            :processing="procesandoAccion"
            @confirm="confirmarAccion"
            @cancel="cerrarConfirmacion"
            @close="cerrarConfirmacion"
        />
    </AuthenticatedLayout>
</template>
