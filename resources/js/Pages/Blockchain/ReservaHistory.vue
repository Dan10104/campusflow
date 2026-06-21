<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { CheckCircleIcon, ShieldCheckIcon, ClockIcon, DocumentDuplicateIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    reserva: Object,
    eventos: Array,
    asientos_locales: Array,
    total_eventos: Number,
    error: String,
});

const verificandoTx = ref(null);
const resultadoVerificacion = ref(null);

const estadoBadgeClass = computed(() => {
    const estados = {
        'pendiente': 'bg-yellow-100 text-yellow-800',
        'aprobada': 'bg-blue-100 text-blue-800',
        'confirmada': 'bg-green-100 text-green-800',
        'en_uso': 'bg-purple-100 text-purple-800',
        'completada': 'bg-gray-100 text-gray-800',
        'cancelada': 'bg-red-100 text-red-800',
        'liberada_auto': 'bg-orange-100 text-orange-800',
    };
    return estados[props.reserva.estado] || 'bg-gray-100 text-gray-800';
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
        alert('Copiado al portapapeles');
    } catch (err) {
        console.error('Error al copiar:', err);
    }
};

const verificarIntegridad = async (txId) => {
    verificandoTx.value = txId;
    resultadoVerificacion.value = null;
    
    try {
        const response = await fetch('/api/blockchain/verify', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ tx_id: txId }),
        });
        
        const data = await response.json();
        
        if (data.success) {
            resultadoVerificacion.value = {
                txId,
                valido: data.data.valid,
                mensaje: data.data.message,
                confirmaciones: data.data.blockchain_confirmations,
            };
        } else {
            alert('Error al verificar: ' + data.message);
        }
    } catch (error) {
        alert('Error de conexión al verificar transacción');
    } finally {
        verificandoTx.value = null;
    }
};

const getEventIcon = (tipo) => {
    if (tipo.includes('Creada')) return '✓';
    if (tipo.includes('Modificada')) return '✎';
    if (tipo.includes('Cancelada')) return '✕';
    if (tipo.includes('Check-in')) return '→';
    if (tipo.includes('Check-out')) return '←';
    return '•';
};
</script>

<template>
    <Head title="Historial Blockchain - Reserva" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Header -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">
                                    Historial Blockchain
                                </h2>
                                <p class="text-sm text-gray-600 mt-1">
                                    Trazabilidad inmutable de eventos - Reserva #{{ reserva.id }}
                                </p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <ShieldCheckIcon class="w-8 h-8 text-green-600" />
                                <span class="text-sm font-medium text-green-600">
                                    Auditoría Verificable
                                </span>
                            </div>
                        </div>

                        <!-- Info de la reserva -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6 p-4 bg-gray-50 rounded-lg">
                            <div>
                                <p class="text-xs text-gray-500 uppercase">Espacio</p>
                                <p class="font-semibold text-gray-900">
                                    {{ reserva.aula.nombre }}
                                    <span class="text-xs text-gray-500">({{ reserva.aula.tipo }})</span>
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase">Usuario</p>
                                <p class="font-semibold text-gray-900">{{ reserva.usuario }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase">Estado</p>
                                <span :class="estadoBadgeClass" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                                    {{ reserva.estado }}
                                </span>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-xs text-gray-500 uppercase">Período</p>
                                <p class="text-sm text-gray-900">
                                    {{ formatFecha(reserva.inicio) }} - {{ formatFecha(reserva.fin) }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase">Propósito</p>
                                <p class="text-sm text-gray-900">{{ reserva.proposito }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alerta de error -->
                <div v-if="error" class="bg-red-50 border-l-4 border-red-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">{{ error }}</p>
                        </div>
                    </div>
                </div>

                <!-- Timeline de eventos -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Línea de tiempo blockchain ({{ total_eventos }} eventos)
                        </h3>

                        <div v-if="eventos.length === 0" class="text-center py-12 text-gray-500">
                            <ClockIcon class="w-12 h-12 mx-auto mb-4 text-gray-400" />
                            <p>No hay eventos registrados en blockchain para esta reserva.</p>
                        </div>

                        <div v-else class="space-y-4">
                            <div
                                v-for="(evento, index) in eventos"
                                :key="evento.tx_id"
                                class="relative pl-8 pb-8 border-l-2 border-gray-200 last:border-transparent"
                            >
                                <!-- Icono del evento -->
                                <div class="absolute left-0 -translate-x-1/2 w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold shadow-lg">
                                    {{ getEventIcon(evento.tipo) }}
                                </div>

                                <!-- Contenido del evento -->
                                <div class="bg-gray-50 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-2">
                                                <span class="font-semibold text-gray-900">
                                                    {{ evento.tipo }}
                                                </span>
                                                <span class="text-xs text-gray-500">
                                                    Bloque #{{ evento.bloque }}
                                                </span>
                                            </div>
                                            
                                            <p class="text-sm text-gray-600 mb-3">
                                                {{ formatFecha(evento.timestamp) }}
                                            </p>

                                            <!-- Hash y TX ID -->
                                            <div class="space-y-2 text-xs font-mono">
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-gray-500">TX ID:</span>
                                                    <code class="bg-gray-200 px-2 py-1 rounded flex-1 truncate">
                                                        {{ evento.tx_id }}
                                                    </code>
                                                    <button
                                                        @click="copiarAlPortapapeles(evento.tx_id)"
                                                        class="text-blue-600 hover:text-blue-800"
                                                        title="Copiar"
                                                    >
                                                        <DocumentDuplicateIcon class="w-4 h-4" />
                                                    </button>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-gray-500">Hash:</span>
                                                    <code class="bg-gray-200 px-2 py-1 rounded flex-1 truncate">
                                                        {{ evento.hash }}
                                                    </code>
                                                    <button
                                                        @click="copiarAlPortapapeles(evento.hash)"
                                                        class="text-blue-600 hover:text-blue-800"
                                                        title="Copiar"
                                                    >
                                                        <DocumentDuplicateIcon class="w-4 h-4" />
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Botón de verificación -->
                                            <button
                                                @click="verificarIntegridad(evento.tx_id)"
                                                :disabled="verificandoTx === evento.tx_id"
                                                class="mt-3 inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                                            >
                                                <CheckCircleIcon class="w-4 h-4 mr-1" />
                                                {{ verificandoTx === evento.tx_id ? 'Verificando...' : 'Verificar Integridad' }}
                                            </button>

                                            <!-- Resultado de verificación -->
                                            <div
                                                v-if="resultadoVerificacion && resultadoVerificacion.txId === evento.tx_id"
                                                :class="[
                                                    'mt-3 p-3 rounded-lg border-2',
                                                    resultadoVerificacion.valido
                                                        ? 'bg-green-50 border-green-500'
                                                        : 'bg-red-50 border-red-500'
                                                ]"
                                            >
                                                <div class="flex items-start">
                                                    <CheckCircleIcon
                                                        :class="[
                                                            'w-5 h-5 mr-2',
                                                            resultadoVerificacion.valido ? 'text-green-600' : 'text-red-600'
                                                        ]"
                                                    />
                                                    <div>
                                                        <p :class="resultadoVerificacion.valido ? 'text-green-800' : 'text-red-800'" class="font-semibold text-sm">
                                                            {{ resultadoVerificacion.mensaje }}
                                                        </p>
                                                        <p class="text-xs text-gray-600 mt-1">
                                                            {{ resultadoVerificacion.confirmaciones }} confirmaciones en blockchain
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información adicional -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <ShieldCheckIcon class="h-5 w-5 text-blue-400" />
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">
                                Sobre la auditoría blockchain
                            </h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>
                                    Cada evento está registrado de forma inmutable en una red blockchain permisionada.
                                    Los hashes criptográficos garantizan que ningún dato puede ser alterado sin detección.
                                    Puede verificar la integridad de cualquier transacción en cualquier momento.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botón volver -->
                <div class="flex justify-end">
                    <Link
                        :href="route('reservas.index')"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        ← Volver a Reservas
                    </Link>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>