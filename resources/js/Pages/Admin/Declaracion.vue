<script setup>
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import { computed, ref } from 'vue';
import {
    BuildingLibraryIcon,
    ClockIcon,
    CubeIcon,
} from '@heroicons/vue/24/outline';

defineProps({
    reservasPendientes: Array,
    prestamosPendientes: Array,
});

const form = useForm({});
const page = usePage();
const activeTab = ref('reservas');
const procesandoId = ref(null);
const procesandoAccion = ref('');
const accionReservaPendiente = ref(null);
const accionPrestamoPendiente = ref(null);

const successMessage = computed(() => page.props.flash?.success || null);
const warningMessage = computed(() => page.props.flash?.warning || null);
const errorMessages = computed(() => Object.values(page.props.errors || {}).filter(Boolean));
const modalReserva = computed(() => {
    if (!accionReservaPendiente.value) {
        return {
            title: '',
            message: '',
            confirmText: 'Confirmar',
            variant: 'primary',
        };
    }

    if (accionReservaPendiente.value.accion === 'aprobar') {
        return {
            title: 'Aprobar reserva',
            message: 'La solicitud será confirmada y el solicitante podrá realizar el check-in dentro del horario permitido.',
            confirmText: 'Aprobar',
            variant: 'primary',
        };
    }

    return {
        title: 'Rechazar reserva',
        message: 'La solicitud será rechazada y dejará de bloquear el aula. El registro permanecerá en el historial.',
        confirmText: 'Rechazar reserva',
        variant: 'danger',
    };
});
const modalPrestamo = computed(() => {
    if (!accionPrestamoPendiente.value) {
        return {
            title: '',
            message: '',
            confirmText: 'Confirmar',
            variant: 'primary',
        };
    }

    if (accionPrestamoPendiente.value.accion === 'aprobar') {
        return {
            title: 'Aprobar solicitud de préstamo',
            message: 'La solicitud será aprobada. Esto no registra todavía la entrega física del activo.',
            confirmText: 'Aprobar solicitud',
            variant: 'primary',
        };
    }

    return {
        title: 'Rechazar solicitud de préstamo',
        message: 'La solicitud será rechazada, dejará de bloquear el activo cuando corresponda y permanecerá en el historial.',
        confirmText: 'Rechazar solicitud',
        variant: 'danger',
    };
});

const formatDate = (date) => new Date(date).toLocaleString();

const aprobarReserva = (id) => {
    if (procesandoId.value) return;

    accionPrestamoPendiente.value = null;
    accionReservaPendiente.value = { accion: 'aprobar', id };
};

const rechazarReserva = (id) => {
    if (procesandoId.value) return;

    accionPrestamoPendiente.value = null;
    accionReservaPendiente.value = { accion: 'rechazar', id };
};

const cerrarConfirmacionReserva = () => {
    if (procesandoId.value && accionReservaPendiente.value) return;

    accionReservaPendiente.value = null;
};

const confirmarAccionReserva = () => {
    if (!accionReservaPendiente.value || procesandoId.value) return;

    const { accion, id } = accionReservaPendiente.value;
    const ruta = accion === 'aprobar'
        ? route('admin.reservas.aprobar', id)
        : route('admin.reservas.rechazar', id);

    procesandoId.value = id;
    procesandoAccion.value = accion;

    form.post(ruta, {
        preserveScroll: true,
        onSuccess: () => {
            accionReservaPendiente.value = null;
        },
        onFinish: () => {
            procesandoId.value = null;
            procesandoAccion.value = '';
        },
    });
};

const aprobarPrestamo = (id) => {
    if (procesandoId.value) return;

    accionReservaPendiente.value = null;
    accionPrestamoPendiente.value = { accion: 'aprobar', id };
};
const rechazarPrestamo = (id) => {
    if (procesandoId.value) return;

    accionReservaPendiente.value = null;
    accionPrestamoPendiente.value = { accion: 'rechazar', id };
};

const cerrarConfirmacionPrestamo = () => {
    if (procesandoId.value && accionPrestamoPendiente.value) return;

    accionPrestamoPendiente.value = null;
};

const confirmarAccionPrestamo = () => {
    if (!accionPrestamoPendiente.value || procesandoId.value) return;

    const { accion, id } = accionPrestamoPendiente.value;

    procesandoId.value = id;
    procesandoAccion.value = accion;

    const opciones = {
        preserveScroll: true,
        onSuccess: () => {
            accionPrestamoPendiente.value = null;
        },
        onFinish: () => {
            procesandoId.value = null;
            procesandoAccion.value = '';
        },
    };

    if (accion === 'aprobar') {
        form.post(route('prestamos.entregar', id), opciones);
        return;
    }

    router.post(route('prestamos.rechazar', id), {}, opciones);
};
</script>

<template>
    <Head title="Panel de Aprobación Admin" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Centro de Aprobaciones
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="successMessage || warningMessage || errorMessages.length" class="mb-6 space-y-3">
                    <div
                        v-if="successMessage"
                        class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"
                    >
                        {{ successMessage }}
                    </div>
                    <div
                        v-if="warningMessage"
                        class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700"
                    >
                        {{ warningMessage }}
                    </div>
                    <div
                        v-for="error in errorMessages"
                        :key="error"
                        class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700"
                    >
                        {{ error }}
                    </div>
                </div>

                <div class="mb-6 border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button
                            @click="activeTab = 'reservas'"
                            :class="[
                                activeTab === 'reservas'
                                    ? 'border-blue-500 text-blue-600'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center',
                            ]"
                        >
                            <BuildingLibraryIcon class="w-5 h-5 mr-2" />
                            Reservas de Aula
                            <span
                                v-if="reservasPendientes.length"
                                class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2.5 rounded-full text-xs"
                            >
                                {{ reservasPendientes.length }}
                            </span>
                        </button>
                        <button
                            @click="activeTab = 'activos'"
                            :class="[
                                activeTab === 'activos'
                                    ? 'border-blue-500 text-blue-600'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center',
                            ]"
                        >
                            <CubeIcon class="w-5 h-5 mr-2" />
                            Préstamos de Activos
                            <span
                                v-if="prestamosPendientes.length"
                                class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2.5 rounded-full text-xs"
                            >
                                {{ prestamosPendientes.length }}
                            </span>
                        </button>
                    </nav>
                </div>

                <div v-show="activeTab === 'reservas'" class="bg-white shadow overflow-hidden sm:rounded-md">
                    <ul role="list" class="divide-y divide-gray-200">
                        <li v-for="reserva in reservasPendientes" :key="reserva.id">
                            <div class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div class="text-sm font-medium text-blue-600 truncate">
                                        {{ reserva.aula.nombre }} ({{ reserva.aula.codigo }})
                                    </div>
                                    <div class="ml-2 flex-shrink-0 flex">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Pendiente
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-2 sm:flex sm:justify-between">
                                    <div class="sm:flex">
                                        <p class="flex items-center text-sm text-gray-500">
                                            <ClockIcon class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" />
                                            {{ formatDate(reserva.inicio) }} - {{ formatDate(reserva.fin) }}
                                        </p>
                                        <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                                            Solicitante: {{ reserva.usuario.name }}
                                        </p>
                                    </div>
                                    <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                        <button
                                            @click="aprobarReserva(reserva.id)"
                                            :disabled="Boolean(procesandoId)"
                                            class="mr-3 text-green-600 hover:text-green-900 font-medium disabled:cursor-not-allowed disabled:opacity-50"
                                        >
                                            {{ procesandoId === reserva.id && procesandoAccion === 'aprobar' ? 'Procesando...' : 'Aprobar' }}
                                        </button>
                                        <button
                                            @click="rechazarReserva(reserva.id)"
                                            :disabled="Boolean(procesandoId)"
                                            class="text-red-600 hover:text-red-900 font-medium disabled:cursor-not-allowed disabled:opacity-50"
                                        >
                                            {{ procesandoId === reserva.id && procesandoAccion === 'rechazar' ? 'Procesando...' : 'Rechazar' }}
                                        </button>
                                    </div>
                                </div>
                                <p class="mt-2 text-sm text-gray-600 italic border-l-2 pl-2 border-gray-300">
                                    "{{ reserva.proposito }}"
                                </p>
                            </div>
                        </li>
                        <li v-if="reservasPendientes.length === 0" class="px-4 py-10 text-center text-gray-500">
                            No hay reservas de aula pendientes de aprobación.
                        </li>
                    </ul>
                </div>

                <div v-show="activeTab === 'activos'" class="bg-white shadow overflow-hidden sm:rounded-md">
                    <ul role="list" class="divide-y divide-gray-200">
                        <li v-for="prestamo in prestamosPendientes" :key="prestamo.id">
                            <div class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div class="text-sm font-medium text-purple-600 truncate">
                                        {{ prestamo.activo.descripcion }}
                                    </div>
                                    <div class="ml-2 flex-shrink-0 flex">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Pendiente
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-2 sm:flex sm:justify-between">
                                    <div class="sm:flex">
                                        <p class="flex items-center text-sm text-gray-500">
                                            <ClockIcon class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" />
                                            {{ formatDate(prestamo.inicio_previsto) }}
                                        </p>
                                        <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                                            Solicitante: {{ prestamo.usuario.nombre }}
                                        </p>
                                    </div>
                                    <div class="mt-2 flex flex-wrap items-center gap-3 text-sm text-gray-500 sm:mt-0">
                                        <button
                                            @click="aprobarPrestamo(prestamo.id)"
                                            :disabled="Boolean(procesandoId)"
                                            class="text-green-600 hover:text-green-900 font-medium disabled:cursor-not-allowed disabled:opacity-50"
                                        >
                                            {{ procesandoId === prestamo.id && procesandoAccion === 'aprobar' ? 'Procesando...' : 'Aprobar solicitud' }}
                                        </button>
                                        <button
                                            @click="rechazarPrestamo(prestamo.id)"
                                            :disabled="Boolean(procesandoId)"
                                            class="text-red-600 hover:text-red-900 font-medium disabled:cursor-not-allowed disabled:opacity-50"
                                        >
                                            {{ procesandoId === prestamo.id && procesandoAccion === 'rechazar' ? 'Procesando...' : 'Rechazar solicitud' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li v-if="prestamosPendientes.length === 0" class="px-4 py-10 text-center text-gray-500">
                            No hay solicitudes de préstamo pendientes.
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <ConfirmationModal
            :show="Boolean(accionReservaPendiente)"
            :title="modalReserva.title"
            :message="modalReserva.message"
            :confirm-text="modalReserva.confirmText"
            cancel-text="Volver"
            :variant="modalReserva.variant"
            :processing="Boolean(procesandoId) && Boolean(accionReservaPendiente)"
            @confirm="confirmarAccionReserva"
            @cancel="cerrarConfirmacionReserva"
            @close="cerrarConfirmacionReserva"
        />

        <ConfirmationModal
            :show="Boolean(accionPrestamoPendiente)"
            :title="modalPrestamo.title"
            :message="modalPrestamo.message"
            :confirm-text="modalPrestamo.confirmText"
            cancel-text="Volver"
            :variant="modalPrestamo.variant"
            :processing="Boolean(procesandoId) && Boolean(accionPrestamoPendiente)"
            @confirm="confirmarAccionPrestamo"
            @cancel="cerrarConfirmacionPrestamo"
            @close="cerrarConfirmacionPrestamo"
        />
    </AuthenticatedLayout>
</template>
