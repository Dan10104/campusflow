<script setup>
import { ref, computed, onUnmounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { CheckCircleIcon, ClockIcon, DocumentDuplicateIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    reserva: Object,
    eventos: Array,
    asientos_locales: Array,
    total_eventos: Number,
    error: String,
});

const verificandoTx = ref(null);
const resultadoVerificacion = ref(null);
const mensajeCopia = ref('');
const tipoMensajeCopia = ref('success');
let temporizadorCopia = null;

const estadoBadgeClass = computed(() => {
    const estados = {
        'pendiente': 'border border-amber-200 bg-amber-100 text-amber-800',
        'aprobada': 'border border-blue-200 bg-blue-100 text-blue-800',
        'confirmada': 'border border-emerald-200 bg-emerald-100 text-emerald-800',
        'en_uso': 'border border-blue-200 bg-blue-100 text-blue-800',
        'completada': 'border border-slate-200 bg-slate-100 text-slate-700',
        'cancelada': 'border border-red-200 bg-red-100 text-red-800',
        'liberada_auto': 'border border-orange-200 bg-orange-100 text-orange-800',
    };
    return estados[props.reserva.estado] || 'border border-slate-200 bg-slate-100 text-slate-700';
});

const formatFecha = (fecha) => {
    return new Date(fecha).toLocaleString('es-BO', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const copiarAlPortapapeles = async (texto) => {
    try {
        await navigator.clipboard.writeText(texto);
        mostrarMensajeCopia('Informacion copiada al portapapeles.', 'success');
    } catch (err) {
        console.error('Error al copiar:', err);
        mostrarMensajeCopia('No se pudo copiar la informacion.', 'error');
    }
};

const mostrarMensajeCopia = (mensaje, tipo = 'success') => {
    mensajeCopia.value = mensaje;
    tipoMensajeCopia.value = tipo;

    if (temporizadorCopia) {
        clearTimeout(temporizadorCopia);
    }

    temporizadorCopia = setTimeout(() => {
        mensajeCopia.value = '';
        temporizadorCopia = null;
    }, 2800);
};

onUnmounted(() => {
    if (temporizadorCopia) {
        clearTimeout(temporizadorCopia);
    }
});

const verificarIntegridad = async (txId) => {
    if (txId === null || txId === undefined || (typeof txId === 'string' && txId.trim() === '')) {
        resultadoVerificacion.value = {
            txId,
            valido: false,
            mensaje: 'No existe un identificador de transacción para verificar.',
            confirmaciones: null,
        };
        return;
    }

    verificandoTx.value = txId;
    resultadoVerificacion.value = null;

    try {
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta?.getAttribute('content');

        if (!csrfToken) {
            throw new Error('No se encontró el token CSRF de la sesión.');
        }

        const response = await fetch('/api/blockchain/verify', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ tx_id: txId }),
        });

        const responseText = await response.text();
        let data = null;

        try {
            data = responseText ? JSON.parse(responseText) : null;
        } catch {
            data = null;
        }

        if (!response.ok) {
            const fallbackMessages = {
                401: 'Tu sesión no está autenticada. Inicia sesión nuevamente.',
                403: 'No tienes autorización para verificar esta transacción.',
                419: 'La sesión expiró o el token de seguridad no es válido. Recarga la página.',
                422: 'El identificador de transacción no es válido.',
                500: 'El servidor no pudo completar la verificación.',
            };

            resultadoVerificacion.value = {
                txId,
                valido: false,
                mensaje: data?.message
                    || data?.error
                    || data?.errors?.tx_id?.[0]
                    || fallbackMessages[response.status]
                    || `No se pudo verificar la transacción. Código HTTP: ${response.status}.`,
                confirmaciones: null,
            };
            return;
        }

        if (!data) {
            resultadoVerificacion.value = {
                txId,
                valido: false,
                mensaje: 'El servidor respondió en un formato inesperado.',
                confirmaciones: null,
            };
            return;
        }

        if (data.success) {
            resultadoVerificacion.value = {
                txId,
                valido: Boolean(data.data?.valid),
                mensaje: data.data?.message || 'La verificación se completó sin mensaje del servidor.',
                confirmaciones: data.data?.blockchain_confirmations ?? null,
            };
        } else {
            resultadoVerificacion.value = {
                txId,
                valido: false,
                mensaje: data.message || data.error || 'No se pudo verificar la transacción.',
                confirmaciones: null,
            };
        }
    } catch (error) {
        resultadoVerificacion.value = {
            txId,
            valido: false,
            mensaje: error instanceof Error
                ? error.message
                : 'No fue posible conectar con el servidor.',
            confirmaciones: null,
        };
    } finally {
        verificandoTx.value = null;
    }
};

const getEventIcon = (tipo) => {
    if (tipo.includes('Creada')) return '✓';
    if (tipo.includes('Modificada')) return '✎';
    if (tipo.includes('Cancelada')) return '×';
    if (tipo.includes('Check-in')) return '→';
    if (tipo.includes('Check-out')) return '←';
    return '•';
};
</script>

<template>
    <Head title="Historial Blockchain - Reserva" />

    <AuthenticatedLayout>
        <div class="min-h-screen w-full bg-[#F5F7FB] text-[#0F172A]">
            <div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
                <section class="rounded-2xl border border-[#E2E8F0] bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
                        <div class="min-w-0">
                            <Link
                                :href="route('reservas.index')"
                                class="inline-flex items-center justify-center rounded-xl border border-[#E2E8F0] bg-white px-3 py-2 text-sm font-bold text-[#334155] shadow-sm transition hover:bg-[#F8FAFC] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2"
                            >
                                Volver a Reservas
                            </Link>

                            <div class="mt-5 flex flex-wrap items-center gap-3">
                                <p class="text-xs font-bold uppercase tracking-widest text-[#2563EB]">
                                    Trazabilidad Blockchain
                                </p>
                                <span
                                    v-if="reserva?.estado"
                                    :class="estadoBadgeClass"
                                    class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold"
                                >
                                    {{ reserva.estado }}
                                </span>
                            </div>

                            <h1 class="mt-2 text-3xl font-bold tracking-tight text-[#0F172A]">
                                Historial de la reserva
                            </h1>
                            <p class="mt-2 max-w-3xl text-sm leading-6 text-[#475569]">
                                Reserva
                                <span class="font-mono font-bold text-[#334155]">#{{ reserva?.id || 'No disponible' }}</span>
                            </p>
                        </div>

                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] px-4 py-3">
                                <p class="text-xs font-bold uppercase tracking-wide text-[#64748B]">
                                    Total de eventos
                                </p>
                                <p class="mt-1 text-2xl font-bold text-[#0F172A]">
                                    {{ total_eventos ?? 0 }}
                                </p>
                            </div>
                            <div class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] px-4 py-3">
                                <p class="text-xs font-bold uppercase tracking-wide text-[#64748B]">
                                    Eventos cargados
                                </p>
                                <p class="mt-1 text-2xl font-bold text-[#0F172A]">
                                    {{ eventos?.length || 0 }}
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <div v-if="error" role="alert" class="rounded-2xl border border-red-200 bg-red-50 p-4 shadow-sm">
                    <div class="flex items-start gap-3">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-sm font-semibold text-red-900">
                            {{ error }}
                        </p>
                    </div>
                </div>

                <div
                    v-if="mensajeCopia"
                    role="status"
                    :class="[
                        'rounded-xl border px-4 py-3 text-sm font-semibold shadow-sm',
                        tipoMensajeCopia === 'success'
                            ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                            : 'border-red-200 bg-red-50 text-red-700'
                    ]"
                >
                    <div class="flex items-start gap-2">
                        <CheckCircleIcon
                            v-if="tipoMensajeCopia === 'success'"
                            class="mt-0.5 h-4 w-4 flex-none"
                            aria-hidden="true"
                        />
                        <ExclamationTriangleIcon
                            v-else
                            class="mt-0.5 h-4 w-4 flex-none"
                            aria-hidden="true"
                        />
                        <span>{{ mensajeCopia }}</span>
                    </div>
                </div>

                <section class="overflow-hidden rounded-2xl border border-[#E2E8F0] bg-white shadow-sm">
                    <div class="border-b border-[#E2E8F0] px-5 py-5 sm:px-6">
                        <h2 class="text-lg font-bold text-[#0F172A]">
                            Información de la reserva
                        </h2>
                        <p class="mt-1 text-sm text-[#475569]">
                            Datos principales asociados al registro.
                        </p>
                    </div>

                    <dl class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-2 lg:grid-cols-3 sm:p-6">
                        <div v-if="reserva?.aula?.nombre" class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                            <dt class="text-xs font-bold uppercase tracking-wide text-[#64748B]">Aula</dt>
                            <dd class="mt-1 break-words text-sm font-semibold text-[#0F172A]">{{ reserva.aula.nombre }}</dd>
                        </div>
                        <div v-if="reserva?.aula?.tipo" class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                            <dt class="text-xs font-bold uppercase tracking-wide text-[#64748B]">Tipo de aula</dt>
                            <dd class="mt-1 break-words text-sm font-semibold text-[#0F172A]">{{ reserva.aula.tipo }}</dd>
                        </div>
                        <div v-if="reserva?.usuario" class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                            <dt class="text-xs font-bold uppercase tracking-wide text-[#64748B]">Usuario</dt>
                            <dd class="mt-1 break-words text-sm font-semibold text-[#0F172A]">{{ reserva.usuario }}</dd>
                        </div>
                        <div v-if="reserva?.inicio" class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                            <dt class="text-xs font-bold uppercase tracking-wide text-[#64748B]">Inicio</dt>
                            <dd class="mt-1 text-sm font-semibold text-[#0F172A]">{{ formatFecha(reserva.inicio) }}</dd>
                        </div>
                        <div v-if="reserva?.fin" class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                            <dt class="text-xs font-bold uppercase tracking-wide text-[#64748B]">Fin</dt>
                            <dd class="mt-1 text-sm font-semibold text-[#0F172A]">{{ formatFecha(reserva.fin) }}</dd>
                        </div>
                        <div v-if="reserva?.estado" class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                            <dt class="text-xs font-bold uppercase tracking-wide text-[#64748B]">Estado</dt>
                            <dd class="mt-2">
                                <span :class="estadoBadgeClass" class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold">
                                    {{ reserva.estado }}
                                </span>
                            </dd>
                        </div>
                        <div v-if="reserva?.proposito" class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4 sm:col-span-2 lg:col-span-3">
                            <dt class="text-xs font-bold uppercase tracking-wide text-[#64748B]">Propósito</dt>
                            <dd class="mt-1 break-words text-sm font-semibold text-[#0F172A]">{{ reserva.proposito }}</dd>
                        </div>
                    </dl>
                </section>

                <section class="rounded-2xl border border-[#E2E8F0] bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-[#0F172A]">
                                Resumen Blockchain
                            </h2>
                            <p class="mt-1 text-sm text-[#475569]">
                                Conteo de registros disponibles para esta reserva.
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-[#64748B]">Total</p>
                            <p class="mt-1 text-xl font-bold text-[#0F172A]">{{ total_eventos ?? 0 }}</p>
                        </div>
                        <div class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-[#64748B]">Eventos</p>
                            <p class="mt-1 text-xl font-bold text-[#0F172A]">{{ eventos?.length || 0 }}</p>
                        </div>
                        <div
                            v-if="Array.isArray(asientos_locales) && asientos_locales.length > 0"
                            class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4"
                        >
                            <p class="text-xs font-bold uppercase tracking-wide text-[#64748B]">Asientos locales</p>
                            <p class="mt-1 text-xl font-bold text-[#0F172A]">{{ asientos_locales.length }}</p>
                        </div>
                    </div>
                </section>

                <section class="overflow-hidden rounded-2xl border border-[#E2E8F0] bg-white shadow-sm">
                    <div class="border-b border-[#E2E8F0] px-5 py-5 sm:px-6">
                        <h2 class="text-lg font-bold text-[#0F172A]">
                            Línea de tiempo Blockchain
                        </h2>
                        <p class="mt-1 text-sm text-[#475569]">
                            {{ total_eventos ?? 0 }} eventos registrados.
                        </p>
                    </div>

                    <div class="p-5 sm:p-6">
                        <div v-if="!eventos || eventos.length === 0" class="rounded-2xl border border-[#E2E8F0] bg-[#F8FAFC] p-8 text-center">
                            <ClockIcon class="mx-auto h-10 w-10 text-[#64748B]" />
                            <p class="mt-3 text-sm font-semibold text-[#0F172A]">
                                No hay eventos registrados para esta reserva.
                            </p>
                        </div>

                        <div v-else class="space-y-5">
                            <article
                                v-for="(evento, index) in eventos"
                                :key="evento.tx_id"
                                class="relative border-l-2 border-[#E2E8F0] pl-8 last:border-transparent"
                            >
                                <div class="absolute left-0 flex h-9 w-9 -translate-x-1/2 items-center justify-center rounded-full border-4 border-white bg-[#2563EB] text-sm font-bold text-white shadow-sm">
                                    {{ getEventIcon(evento.tipo || '') }}
                                </div>

                                <div class="rounded-2xl border border-[#E2E8F0] bg-[#F8FAFC] p-4 shadow-sm">
                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                        <div class="min-w-0">
                                            <p class="break-words text-base font-bold text-[#0F172A]">
                                                {{ evento.tipo || 'Evento sin tipo' }}
                                            </p>
                                            <p v-if="evento.timestamp" class="mt-1 text-sm font-semibold text-[#475569]">
                                                {{ formatFecha(evento.timestamp) }}
                                            </p>
                                        </div>

                                        <span
                                            v-if="evento.bloque"
                                            class="inline-flex shrink-0 items-center rounded-full border border-[#E2E8F0] bg-white px-3 py-1 text-xs font-bold text-[#334155]"
                                        >
                                            Bloque #{{ evento.bloque }}
                                        </span>
                                    </div>

                                    <div class="mt-4 space-y-3">
                                        <div v-if="evento.tx_id" class="rounded-xl border border-[#E2E8F0] bg-white p-3">
                                            <div class="mb-2 flex items-center justify-between gap-3">
                                                <p class="text-xs font-bold uppercase tracking-wide text-[#64748B]">Transacción</p>
                                                <button
                                                    type="button"
                                                    @click="copiarAlPortapapeles(evento.tx_id)"
                                                    class="inline-flex shrink-0 items-center gap-1 rounded-lg border border-blue-200 bg-blue-50 px-2.5 py-1.5 text-xs font-bold text-[#2563EB] transition hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2"
                                                    aria-label="Copiar transacción"
                                                >
                                                    <DocumentDuplicateIcon class="h-4 w-4" />
                                                    Copiar
                                                </button>
                                            </div>
                                            <code class="block break-all rounded-lg border border-slate-200 bg-slate-50 p-2 font-mono text-xs text-slate-700">
                                                {{ evento.tx_id }}
                                            </code>
                                        </div>

                                        <div v-if="evento.hash" class="rounded-xl border border-[#E2E8F0] bg-white p-3">
                                            <div class="mb-2 flex items-center justify-between gap-3">
                                                <p class="text-xs font-bold uppercase tracking-wide text-[#64748B]">Hash</p>
                                                <button
                                                    type="button"
                                                    @click="copiarAlPortapapeles(evento.hash)"
                                                    class="inline-flex shrink-0 items-center gap-1 rounded-lg border border-blue-200 bg-blue-50 px-2.5 py-1.5 text-xs font-bold text-[#2563EB] transition hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2"
                                                    aria-label="Copiar hash"
                                                >
                                                    <DocumentDuplicateIcon class="h-4 w-4" />
                                                    Copiar
                                                </button>
                                            </div>
                                            <code class="block break-all rounded-lg border border-slate-200 bg-slate-50 p-2 font-mono text-xs text-slate-700">
                                                {{ evento.hash }}
                                            </code>
                                        </div>

                                        <div v-if="evento.red" class="rounded-xl border border-[#E2E8F0] bg-white p-3">
                                            <p class="text-xs font-bold uppercase tracking-wide text-[#64748B]">Red</p>
                                            <p class="mt-1 break-words text-sm font-semibold text-[#0F172A]">{{ evento.red }}</p>
                                        </div>
                                    </div>

                                    <button
                                        v-if="evento.tx_id"
                                        type="button"
                                        @click="verificarIntegridad(evento.tx_id)"
                                        :disabled="verificandoTx === evento.tx_id"
                                        class="mt-4 inline-flex items-center justify-center rounded-xl border border-transparent bg-[#2563EB] px-3 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-[#1D4ED8] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2 disabled:cursor-not-allowed disabled:bg-slate-300"
                                    >
                                        <CheckCircleIcon class="mr-1.5 h-4 w-4" />
                                        {{ verificandoTx === evento.tx_id ? 'Verificando...' : 'Verificar Integridad' }}
                                    </button>

                                    <div
                                        v-if="resultadoVerificacion && resultadoVerificacion.txId === evento.tx_id"
                                        :class="[
                                            'mt-4 rounded-xl border p-4',
                                            resultadoVerificacion.valido
                                                ? 'border-emerald-200 bg-emerald-50 text-emerald-900'
                                                : 'border-red-200 bg-red-50 text-red-900'
                                        ]"
                                    >
                                        <div class="flex items-start gap-3">
                                            <CheckCircleIcon class="mt-0.5 h-5 w-5 shrink-0" />
                                            <div class="min-w-0">
                                                <p class="break-words text-sm font-bold">
                                                    {{ resultadoVerificacion.mensaje }}
                                                </p>
                                                <p
                                                    v-if="resultadoVerificacion.confirmaciones !== null && resultadoVerificacion.confirmaciones !== undefined"
                                                    class="mt-1 text-xs font-semibold text-[#475569]"
                                                >
                                                    {{ resultadoVerificacion.confirmaciones }} confirmaciones en blockchain
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
