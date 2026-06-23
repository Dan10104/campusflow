<script setup>
import { computed, ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import {
    ArrowLeftIcon,
    QrCodeIcon,
    ClockIcon,
    MapPinIcon,
    UserIcon,
    CalendarIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
    ShieldCheckIcon,
    XCircleIcon
} from '@heroicons/vue/24/outline';
import QrcodeVue from 'qrcode.vue';

const props = defineProps({
    reserva: Object,
});

const page = usePage();
const mostrarConfirmacion = ref(false);
const procesandoCancelacion = ref(false);

const successMessage = computed(() => page.props.flash?.success || null);
const warningMessage = computed(() => page.props.flash?.warning || null);
const errorMessages = computed(() =>
    Object.values(page.props.errors || {}).filter((message) => Boolean(message)),
);

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
        'confirmada': 'border border-emerald-200 bg-emerald-100 text-emerald-800',
        'aprobada': 'border border-emerald-200 bg-emerald-100 text-emerald-800',
        'pendiente': 'border border-amber-200 bg-amber-100 text-amber-800',
        'en_uso': 'border border-blue-200 bg-blue-100 text-blue-800',
        'completada': 'border border-slate-200 bg-slate-100 text-slate-700',
        'cancelada': 'border border-red-200 bg-red-100 text-red-800'
    };
    return badges[estado] || 'border border-slate-200 bg-slate-100 text-slate-700';
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

const puedeCancelar = () => {
    return ['pendiente', 'confirmada', 'aprobada'].includes(props.reserva.estado);
};

const hacerCheckout = () => {
    router.post(route('reservas.checkout', props.reserva.id), {}, {
        preserveScroll: true
    });
};

const cancelarReserva = () => {
    if (procesandoCancelacion.value) return;

    mostrarConfirmacion.value = true;
};

const cerrarConfirmacion = () => {
    if (procesandoCancelacion.value) return;

    mostrarConfirmacion.value = false;
};

const confirmarCancelacion = () => {
    if (procesandoCancelacion.value) return;

    router.delete(route('reservas.destroy', props.reserva.id), {
        preserveScroll: true,
        onStart: () => {
            procesandoCancelacion.value = true;
        },
        onSuccess: () => {
            mostrarConfirmacion.value = false;
        },
        onFinish: () => {
            procesandoCancelacion.value = false;
        },
    });
};

const getCheckinTexto = (tipo) => {
    return tipo === 'checkin' ? 'Check-in' : 'Check-out';
};
</script>

<template>
    <Head :title="`Reserva - Aula ${reserva.aula.codigo}`" />

    <AuthenticatedLayout>
        <div class="min-h-screen w-full bg-[#F5F7FB] text-[#0F172A]">
            <div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
                        <div class="min-w-0">
                            <button
                                @click="volver"
                                class="inline-flex items-center gap-2 rounded-xl border border-blue-200 bg-white px-3 py-2 text-sm font-semibold text-blue-700 shadow-sm transition hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                            >
                                <ArrowLeftIcon class="h-4 w-4" aria-hidden="true" />
                                Volver a mis reservas
                            </button>

                            <div class="mt-5 flex flex-wrap items-center gap-3">
                                <p class="text-xs font-bold uppercase tracking-widest text-blue-600">
                                    Detalle operativo
                                </p>
                                <span
                                    :class="getEstadoBadge(reserva.estado)"
                                    class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold"
                                >
                                    {{ getEstadoTexto(reserva.estado) }}
                                </span>
                            </div>

                            <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                                Aula {{ reserva.aula.codigo }}
                            </h1>
                            <p class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm font-medium text-slate-600">
                                <span class="inline-flex items-center gap-2">
                                    <CalendarIcon class="h-4 w-4 text-blue-600" aria-hidden="true" />
                                    {{ formatFecha(reserva.inicio) }}
                                </span>
                                <span class="inline-flex items-center gap-2">
                                    <ClockIcon class="h-4 w-4 text-blue-600" aria-hidden="true" />
                                    {{ formatHora(reserva.inicio) }} - {{ formatHora(reserva.fin) }}
                                </span>
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <Link
                                v-if="$page.props.auth?.isAdmin && reserva?.id"
                                :href="route('blockchain.reserva.history', reserva.id)"
                                class="inline-flex items-center justify-center rounded-xl border border-blue-200 bg-white px-4 py-3 text-sm font-bold text-blue-700 shadow-sm transition hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                            >
                                <ShieldCheckIcon class="mr-2 h-5 w-5" aria-hidden="true" />
                                Ver trazabilidad
                            </Link>

                            <div
                                v-if="puedeHacerCheckin() && !tieneCheckin()"
                                class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-blue-900 shadow-sm"
                            >
                                <p class="text-sm font-bold">
                                    Check-in disponible
                                </p>
                                <p class="mt-1 max-w-sm text-sm leading-5">
                                    Presenta el código QR al personal autorizado para registrar el inicio del uso del aula.
                                </p>
                            </div>

                            <button
                                v-if="reserva.estado === 'en_uso'"
                                @click="hacerCheckout"
                                class="inline-flex items-center justify-center rounded-xl border border-transparent bg-blue-600 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                            >
                                Hacer check-out
                            </button>

                            <button
                                v-if="puedeCancelar()"
                                @click="cancelarReserva"
                                :disabled="procesandoCancelacion"
                                class="inline-flex items-center justify-center rounded-xl border border-transparent bg-red-600 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:bg-red-300"
                            >
                                <XCircleIcon class="mr-2 h-5 w-5" aria-hidden="true" />
                                {{ procesandoCancelacion ? 'Cancelando...' : 'Cancelar reserva' }}
                            </button>
                        </div>
                    </div>
                </section>

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

                <div class="grid grid-cols-1 gap-6 xl:grid-cols-12">
                    <div class="space-y-6 xl:col-span-8">
                        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                            <div class="border-b border-slate-200 px-5 py-5 sm:px-6">
                                <p class="border-l-4 border-blue-600 pl-3 text-sm font-bold text-slate-900">
                                    Informacion de la reserva
                                </p>
                                <h2 class="mt-1 text-xl font-bold text-slate-900">
                                    Datos principales
                                </h2>
                            </div>
                            <dl class="grid gap-4 p-5 sm:grid-cols-2 sm:p-6">
                                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Fecha</dt>
                                    <dd class="mt-1 text-sm font-semibold text-slate-900">{{ formatFecha(reserva.inicio) }}</dd>
                                </div>
                                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Estado</dt>
                                    <dd class="mt-2">
                                        <span
                                            :class="getEstadoBadge(reserva.estado)"
                                            class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold"
                                        >
                                            {{ getEstadoTexto(reserva.estado) }}
                                        </span>
                                    </dd>
                                </div>
                                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Hora de inicio</dt>
                                    <dd class="mt-1 text-sm font-semibold text-slate-900">{{ formatHora(reserva.inicio) }}</dd>
                                </div>
                                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Hora de fin</dt>
                                    <dd class="mt-1 text-sm font-semibold text-slate-900">{{ formatHora(reserva.fin) }}</dd>
                                </div>
                                <div v-if="reserva.asistentes_estimados" class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Asistentes estimados</dt>
                                    <dd class="mt-1 text-sm font-semibold text-slate-900">{{ reserva.asistentes_estimados }} personas</dd>
                                </div>
                                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 sm:col-span-2">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Proposito</dt>
                                    <dd class="mt-1 text-sm font-medium leading-6 text-slate-900">{{ reserva.proposito }}</dd>
                                </div>
                            </dl>
                        </section>

                        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                            <div class="border-b border-slate-200 px-5 py-5 sm:px-6">
                                <p class="border-l-4 border-blue-600 pl-3 text-sm font-bold text-slate-900">
                                    Espacio academico
                                </p>
                                <h2 class="mt-1 text-xl font-bold text-slate-900">
                                    Informacion del aula
                                </h2>
                            </div>
                            <dl class="grid gap-4 p-5 sm:grid-cols-2 sm:p-6">
                                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Codigo</dt>
                                    <dd class="mt-1 text-lg font-black text-slate-900">{{ reserva.aula.codigo }}</dd>
                                </div>
                                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Capacidad</dt>
                                    <dd class="mt-1 text-sm font-semibold text-slate-900">{{ reserva.aula.capacidad }} personas</dd>
                                </div>
                                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Tipo</dt>
                                    <dd class="mt-1 text-sm font-semibold capitalize text-slate-900">{{ reserva.aula.tipo.replace('_', ' ') }}</dd>
                                </div>
                                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                    <dt class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        <MapPinIcon class="h-4 w-4 text-blue-600" aria-hidden="true" />
                                        Ubicacion
                                    </dt>
                                    <dd class="mt-1 text-sm font-semibold text-slate-900">{{ reserva.aula.modulo.nombre }}</dd>
                                </div>
                                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 sm:col-span-2">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Facultad</dt>
                                    <dd class="mt-1 text-sm font-semibold text-slate-900">
                                        {{ reserva.aula.modulo.facultad.sigla }} - {{ reserva.aula.modulo.facultad.nombre }}
                                    </dd>
                                </div>
                            </dl>
                        </section>

                        <section v-if="reserva.checkins && reserva.checkins.length > 0" class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                            <div class="border-b border-slate-200 px-5 py-5 sm:px-6">
                                <p class="border-l-4 border-blue-600 pl-3 text-sm font-bold text-slate-900">
                                    Control de uso
                                </p>
                                <h2 class="mt-1 text-xl font-bold text-slate-900">
                                    Historial de check-ins
                                </h2>
                            </div>
                            <div class="p-5 sm:p-6">
                                <ul role="list" class="space-y-4">
                                    <li
                                        v-for="(checkin, idx) in reserva.checkins"
                                        :key="checkin.id"
                                        class="relative flex gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4"
                                    >
                                        <span
                                            v-if="idx !== reserva.checkins.length - 1"
                                            class="absolute left-7 top-12 h-6 w-px bg-slate-200"
                                            aria-hidden="true"
                                        />
                                        <span
                                            :class="checkin.tipo === 'checkin' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-blue-200 bg-blue-50 text-blue-700'"
                                            class="flex h-8 w-8 flex-none items-center justify-center rounded-full border"
                                        >
                                            <ClockIcon class="h-4 w-4" aria-hidden="true" />
                                        </span>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between">
                                                <div>
                                                    <p class="text-sm font-bold text-slate-900">
                                                        {{ getCheckinTexto(checkin.tipo) }}
                                                    </p>
                                                    <p class="mt-1 text-sm text-slate-600">
                                                        {{ formatFechaHora(checkin.registrado_en) }}
                                                    </p>
                                                </div>
                                                <p class="text-sm font-semibold text-slate-600">
                                                    {{ checkin.origen }}
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </section>
                    </div>

                    <aside class="space-y-6 xl:col-span-4">
                        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                            <div class="border-b border-slate-200 px-5 py-5 sm:px-6">
                                <p class="border-l-4 border-blue-600 pl-3 text-sm font-bold text-slate-900">
                                    Codigo QR
                                </p>
                                <h2 class="mt-1 text-xl font-bold text-slate-900">
                                    Validacion de reserva
                                </h2>
                            </div>
                            <div class="p-5 sm:p-6">
                                <div class="flex flex-col items-center rounded-2xl border border-slate-200 bg-white p-5">
                                    <div class="flex w-full items-center justify-center rounded-2xl border border-slate-200 bg-white p-4">
                                        <QrcodeVue
                                            v-if="reserva.qr_code && reserva.qr_code.startsWith('http')"
                                            :value="reserva.qr_code"
                                            :size="224"
                                            level="M"
                                            render-as="svg"
                                            :margin="2"
                                        />
                                        <div v-else class="text-center text-slate-500">
                                            <QrCodeIcon class="mx-auto h-24 w-24" aria-hidden="true" />
                                            <p class="mt-2 text-sm font-semibold text-slate-700">QR no disponible</p>
                                        </div>
                                    </div>
                                    <button
                                        v-if="$page.props.auth?.isAdmin"
                                        @click="router.visit(route('reservas.escanear'))"
                                        class="mt-4 inline-flex items-center justify-center rounded-xl border border-transparent bg-blue-600 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                    >
                                        Escanear QR
                                    </button>
                                    <p class="mt-4 max-w-full break-all text-center font-mono text-xs text-slate-600">
                                        {{ reserva.qr_code }}
                                    </p>
                                    <p class="mt-2 text-center text-xs font-medium text-slate-500">
                                        Muestra este codigo al administrador
                                    </p>
                                </div>
                            </div>
                        </section>

                        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                            <div class="border-b border-slate-200 px-5 py-5 sm:px-6">
                                <p class="border-l-4 border-blue-600 pl-3 text-sm font-bold text-slate-900">
                                    Solicitante
                                </p>
                                <h2 class="mt-1 text-xl font-bold text-slate-900">
                                    Usuario
                                </h2>
                            </div>
                            <div class="p-5 sm:p-6">
                                <div class="flex min-w-0 items-center gap-4">
                                    <div class="flex h-12 w-12 flex-none items-center justify-center rounded-xl border border-blue-200 bg-blue-50 text-blue-600">
                                        <UserIcon class="h-6 w-6" aria-hidden="true" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-bold text-slate-900">
                                            {{ reserva.usuario.nombre }}
                                        </p>
                                        <p class="truncate text-sm text-slate-600">
                                            {{ reserva.usuario.email }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section v-if="$page.props.auth?.isAdmin && reserva?.asiento_blockchain" class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                            <div class="border-b border-slate-200 px-5 py-5 sm:px-6">
                                <p class="border-l-4 border-blue-600 pl-3 text-sm font-bold text-slate-900">
                                    Registro tecnico
                                </p>
                                <h2 class="mt-1 text-xl font-bold text-slate-900">
                                    Trazabilidad Blockchain
                                </h2>
                                <p class="mt-1 text-sm text-slate-600">
                                    Consulta el registro tecnico y la verificacion de integridad asociados a esta reserva.
                                </p>
                            </div>
                            <div class="p-5 sm:p-6">
                                <div class="flex gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                                    <CheckCircleIcon class="mt-0.5 h-5 w-5 flex-none text-emerald-600" aria-hidden="true" />
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-emerald-900">
                                            Esta reserva esta registrada en blockchain
                                        </p>
                                        <p class="mt-2 break-all font-mono text-xs text-emerald-700">
                                            Hash: {{ reserva.asiento_blockchain.hash }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                            <p class="border-l-4 border-blue-600 pl-3 text-sm font-bold text-slate-900">
                                Resumen operativo
                            </p>
                            <h2 class="mt-1 text-xl font-bold text-slate-900">
                                Estado de la reserva
                            </h2>
                            <div class="mt-5 space-y-3">
                                <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4">
                                    <div
                                        :class="[
                                            'h-3 w-3 rounded-full',
                                            reserva.estado === 'cancelada' ? 'bg-red-500' :
                                            reserva.estado === 'completada' ? 'bg-slate-400' :
                                            'bg-emerald-500'
                                        ]"
                                    />
                                    <span class="text-sm font-bold text-slate-900">
                                        {{ getEstadoTexto(reserva.estado) }}
                                    </span>
                                </div>
                                <div v-if="tieneCheckin()" class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-700">
                                    <CheckCircleIcon class="h-5 w-5" aria-hidden="true" />
                                    Check-in realizado
                                </div>
                            </div>
                        </section>
                    </aside>
                </div>
            </div>
        </div>

        <ConfirmationModal
            :show="mostrarConfirmacion"
            title="Cancelar reserva"
            message="La reserva quedara cancelada y el aula volvera a estar disponible. Esta accion no elimina el historial."
            confirm-text="Cancelar reserva"
            cancel-text="Volver"
            variant="danger"
            :processing="procesandoCancelacion"
            @confirm="confirmarCancelacion"
            @cancel="cerrarConfirmacion"
            @close="cerrarConfirmacion"
        />
    </AuthenticatedLayout>
</template>
