<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, router } from "@inertiajs/vue3";
import {
    CalendarIcon,
    UserIcon,
    CubeIcon,
    CheckCircleIcon,
    ArrowLeftIcon,
    HandThumbUpIcon,
    ArrowPathIcon,
    QrCodeIcon,
} from "@heroicons/vue/24/outline";
import QrcodeVue from "qrcode.vue";

const props = defineProps({
    prestamo: Object,
    isAdmin: Boolean,
});

const form = useForm({});

const entregar = () => {
    if (confirm("¿Confirmar entrega física del activo?")) {
        form.post(route("prestamos.entregar", props.prestamo.id), {
            preserveScroll: true,
            onSuccess: () => alert("Activo entregado correctamente."),
        });
    }
};

const devolver = () => {
    const condicion = prompt(
        "Condición de devolución (ej. Buen estado, Rayado):",
        "Buen estado",
    );
    if (condicion) {
        form.transform((data) => ({ ...data, condicion: condicion })).post(
            route("prestamos.devolver", props.prestamo.id),
            {
                preserveScroll: true,
                onSuccess: () => alert("Devolución registrada correctamente."),
            },
        );
    }
};

const getStatusColor = (status) => {
    const colors = {
        aprobado: "bg-teal-100 text-teal-800",
        pendiente: "bg-yellow-100 text-yellow-800",
        entregado: "bg-blue-100 text-blue-800",
        devuelto: "bg-green-100 text-green-800",
        cancelada: "bg-red-100 text-red-800",
    };
    return colors[status] || "bg-gray-100 text-gray-800";
};
</script>

<template>
    <Head title="Detalle de Préstamo" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center">
                <button
                    @click="router.visit(route('prestamos.index'))"
                    class="mr-4 text-gray-500 hover:text-gray-700"
                >
                    <ArrowLeftIcon class="h-5 w-5" />
                </button>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Detalle de Solicitud #{{ prestamo.id }}
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <!-- Header Status -->
                    <div
                        class="px-6 py-5 border-b border-gray-200 flex justify-between items-center bg-gray-50"
                    >
                        <div>
                            <span class="text-sm text-gray-500"
                                >Estado Actual</span
                            >
                            <div class="mt-1">
                                <span
                                    class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full uppercase tracking-wide"
                                    :class="getStatusColor(prestamo.estado)"
                                >
                                    {{ prestamo.estado }}
                                </span>
                            </div>
                        </div>
                        <div class="flex space-x-3">
                            <!-- Botones de Acción (Admin UI) -->
                            <button
                                v-if="isAdmin && (prestamo.estado === 'pendiente' || prestamo.estado === 'aprobado')"
                                @click="entregar"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                <HandThumbUpIcon
                                    class="-ml-1 mr-2 h-5 w-5"
                                    aria-hidden="true"
                                />
                                Entregar Activo
                            </button>

                            <button
                                v-if="isAdmin && prestamo.estado === 'entregado'"
                                @click="devolver"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                            >
                                <ArrowPathIcon
                                    class="-ml-1 mr-2 h-5 w-5"
                                    aria-hidden="true"
                                />
                                Registrar Devolución
                            </button>
                        </div>
                    </div>

                    <div class="px-6 py-6">
                        <dl
                            class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2"
                        >
                            <!-- Activo Info -->
                            <div class="sm:col-span-2">
                                <dt
                                    class="text-sm font-medium text-gray-500 flex items-center"
                                >
                                    <CubeIcon class="h-5 w-5 mr-1" />
                                    Activo Solicitado
                                </dt>
                                <dd
                                    class="mt-1 text-lg font-medium text-gray-900"
                                >
                                    {{ prestamo.activo.descripcion }}
                                </dd>
                                <dd class="mt-1 text-sm text-gray-500">
                                    Código: {{ prestamo.activo_codigo }} | Tipo:
                                    {{
                                        prestamo.activo.tipo_activo?.nombre ||
                                        "General"
                                    }}
                                </dd>
                            </div>

                            <!-- Usuario Info -->
                            <div class="sm:col-span-1">
                                <dt
                                    class="text-sm font-medium text-gray-500 flex items-center"
                                >
                                    <UserIcon class="h-5 w-5 mr-1" />
                                    Solicitante
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ prestamo.usuario.name }}
                                </dd>
                                <dd class="text-xs text-gray-500">
                                    {{ prestamo.usuario_id }}
                                </dd>
                            </div>

                            <!-- QR Code Visual -->
                            <div
                                class="sm:col-span-2 mt-4 bg-gray-50 rounded-lg p-4 border border-gray-100 flex items-center justify-between"
                            >
                                <div>
                                    <h4
                                        class="text-sm font-medium text-gray-900 flex items-center mb-1"
                                    >
                                        <QrCodeIcon class="h-5 w-5 mr-1" />
                                        Código QR del Activo
                                    </h4>
                                    <p class="text-xs text-gray-500">
                                        Muestra o escanea este código para la
                                        entrega/devolución mediante la App
                                        Scanner.
                                    </p>
                                    <p
                                        class="mt-2 text-xs font-mono text-gray-600 break-all"
                                    >
                                        {{ prestamo.activo_codigo }}
                                    </p>
                                </div>
                                <div
                                    class="bg-white p-2 rounded shadow-sm border border-gray-200"
                                >
                                    <QrcodeVue
                                        v-if="prestamo.activo.qr_code"
                                        :value="prestamo.activo.qr_code"
                                        :size="100"
                                        level="M"
                                    />
                                    <div
                                        v-else
                                        class="text-center text-xs text-gray-400 w-24 flex flex-col items-center"
                                    >
                                        <QrCodeIcon class="h-8 w-8 mx-auto" />
                                        Sin QR
                                    </div>
                                </div>
                            </div>

                            <!-- Fechas -->
                            <div class="sm:col-span-1">
                                <dt
                                    class="text-sm font-medium text-gray-500 flex items-center"
                                >
                                    <CalendarIcon class="h-5 w-5 mr-1" />
                                    Horario Previsto
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    Inicio:
                                    {{
                                        new Date(
                                            prestamo.inicio_previsto,
                                        ).toLocaleString()
                                    }}
                                </dd>
                                <dd class="text-sm text-gray-900">
                                    Fin:
                                    {{
                                        new Date(
                                            prestamo.fin_previsto,
                                        ).toLocaleString()
                                    }}
                                </dd>
                            </div>

                            <!-- Fechas Reales -->
                            <div
                                v-if="prestamo.entregado_en"
                                class="sm:col-span-1 bg-blue-50 p-3 rounded-md"
                            >
                                <dt class="text-xs font-medium text-blue-700">
                                    Entregado el
                                </dt>
                                <dd class="mt-1 text-sm text-blue-900">
                                    {{
                                        new Date(
                                            prestamo.entregado_en,
                                        ).toLocaleString()
                                    }}
                                </dd>
                            </div>

                            <div
                                v-if="prestamo.devuelto_en"
                                class="sm:col-span-1 bg-green-50 p-3 rounded-md"
                            >
                                <dt class="text-xs font-medium text-green-700">
                                    Devuelto el
                                </dt>
                                <dd class="mt-1 text-sm text-green-900">
                                    {{
                                        new Date(
                                            prestamo.devuelto_en,
                                        ).toLocaleString()
                                    }}
                                </dd>
                                <dd class="text-xs text-green-700 mt-1">
                                    Condición: {{ prestamo.condicion_entrada }}
                                </dd>
                            </div>
                        </dl>

                        <!-- Blockchain Visualizer Link (Optional) -->
                        <div class="mt-8 border-t border-gray-100 pt-6">
                            <h4 class="text-sm font-medium text-gray-900">
                                Auditoría Blockchain
                            </h4>
                            <div class="mt-2 flex items-center">
                                <a
                                    v-if="isAdmin"
                                    :href="
                                        route(
                                            'blockchain.activo.history',
                                            prestamo.activo_codigo,
                                        )
                                    "
                                    class="text-indigo-600 hover:text-indigo-500 text-sm font-medium"
                                >
                                    Ver Historial Inmutable del Activo &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
