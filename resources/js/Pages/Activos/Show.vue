<script setup>
import { ref } from "vue";
import { Head, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {
    ArrowLeftIcon,
    QrCodeIcon,
    ClockIcon,
    UserIcon,
    CheckCircleIcon,
} from "@heroicons/vue/24/outline";
import QrcodeVue from "qrcode.vue";

const props = defineProps({
    activo: Object,
});

const solicitarPrestamo = () => {
    router.visit(route("prestamos.crear", { activo: props.activo.codigo }));
};

const volver = () => {
    router.visit(route("activos.disponibles"));
};

const getEstadoBadge = (estado) => {
    const badges = {
        disponible: "bg-green-100 text-green-800",
        en_deposito: "bg-blue-100 text-blue-800",
        prestado: "bg-yellow-100 text-yellow-800",
        mantenimiento: "bg-red-100 text-red-800",
        baja: "bg-gray-100 text-gray-800",
    };
    return badges[estado] || "bg-gray-100 text-gray-800";
};

const getEstadoTexto = (estado) => {
    const textos = {
        disponible: "Disponible",
        en_deposito: "En Depósito",
        prestado: "Prestado",
        mantenimiento: "Mantenimiento",
        baja: "Dado de Baja",
    };
    return textos[estado] || estado;
};

const formatFecha = (fecha) => {
    if (!fecha) return "-";
    return new Date(fecha).toLocaleDateString("es-BO", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};
</script>

<template>
    <Head :title="`Activo: ${activo.descripcion}`" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center mb-4">
                    <button
                        @click="volver"
                        class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700"
                    >
                        <ArrowLeftIcon class="h-5 w-5 mr-1" />
                        Volver a activos
                    </button>
                </div>

                <div class="md:flex md:items-center md:justify-between">
                    <div class="flex-1 min-w-0">
                        <h2
                            class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate"
                        >
                            {{ activo.descripcion }}
                        </h2>
                        <div
                            class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6"
                        >
                            <div
                                class="mt-2 flex items-center text-sm text-gray-500"
                            >
                                {{ activo.tipo_activo.nombre }}
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 flex md:mt-0 md:ml-4">
                        <button
                            @click="solicitarPrestamo"
                            :disabled="
                                activo.estado !== 'disponible' &&
                                activo.estado !== 'en_deposito'
                            "
                            :class="[
                                'inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white',
                                activo.estado === 'disponible' ||
                                activo.estado === 'en_deposito'
                                    ? 'bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500'
                                    : 'bg-gray-300 cursor-not-allowed',
                            ]"
                        >
                            Solicitar préstamo
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Columna principal -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Información del activo -->
                    <div
                        class="bg-white shadow-sm rounded-lg border border-gray-200"
                    >
                        <div class="px-6 py-5 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">
                                Información del Activo
                            </h3>
                        </div>
                        <div class="px-6 py-5">
                            <dl
                                class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2"
                            >
                                <div>
                                    <dt
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Código
                                    </dt>
                                    <dd
                                        class="mt-1 text-sm text-gray-900 font-mono"
                                    >
                                        {{ activo.codigo }}
                                    </dd>
                                </div>
                                <div>
                                    <dt
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Estado
                                    </dt>
                                    <dd class="mt-1">
                                        <span
                                            :class="
                                                getEstadoBadge(activo.estado)
                                            "
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        >
                                            {{ getEstadoTexto(activo.estado) }}
                                        </span>
                                    </dd>
                                </div>
                                <div v-if="activo.marca">
                                    <dt
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Marca
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ activo.marca }}
                                    </dd>
                                </div>
                                <div v-if="activo.modelo">
                                    <dt
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Modelo
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ activo.modelo }}
                                    </dd>
                                </div>
                                <div v-if="activo.ubicacion_actual">
                                    <dt
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Ubicación Actual
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ activo.ubicacion_actual }}
                                    </dd>
                                </div>
                                <div>
                                    <dt
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Tipo de Activo
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ activo.tipo_activo.nombre }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Historial de préstamos recientes -->
                    <div
                        class="bg-white shadow-sm rounded-lg border border-gray-200"
                    >
                        <div class="px-6 py-5 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">
                                Historial de Préstamos Recientes
                            </h3>
                        </div>
                        <div class="px-6 py-5">
                            <div
                                v-if="
                                    activo.reservas_activos &&
                                    activo.reservas_activos.length > 0
                                "
                                class="flow-root"
                            >
                                <ul role="list" class="-mb-8">
                                    <li
                                        v-for="(
                                            prestamo, idx
                                        ) in activo.reservas_activos"
                                        :key="prestamo.id"
                                        class="relative pb-8"
                                    >
                                        <span
                                            v-if="
                                                idx !==
                                                activo.reservas_activos.length -
                                                    1
                                            "
                                            class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"
                                            aria-hidden="true"
                                        />
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span
                                                    class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white"
                                                >
                                                    <UserIcon
                                                        class="h-5 w-5 text-white"
                                                    />
                                                </span>
                                            </div>
                                            <div
                                                class="flex min-w-0 flex-1 justify-between space-x-4"
                                            >
                                                <div>
                                                    <p
                                                        class="text-sm text-gray-900"
                                                    >
                                                        {{
                                                            prestamo.usuario
                                                                .nombre
                                                        }}
                                                        <span
                                                            class="font-medium text-gray-500"
                                                        >
                                                            -
                                                            {{
                                                                prestamo.estado
                                                            }}
                                                        </span>
                                                    </p>
                                                    <p
                                                        class="mt-0.5 text-sm text-gray-500"
                                                    >
                                                        Del
                                                        {{
                                                            formatFecha(
                                                                prestamo.inicio_previsto,
                                                            )
                                                        }}
                                                        al
                                                        {{
                                                            formatFecha(
                                                                prestamo.fin_previsto,
                                                            )
                                                        }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div v-else class="text-center py-6">
                                <ClockIcon
                                    class="mx-auto h-12 w-12 text-gray-400"
                                />
                                <p class="mt-2 text-sm text-gray-500">
                                    No hay préstamos registrados
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna lateral -->
                <div class="space-y-6">
                    <!-- Código QR -->
                    <div
                        class="bg-white shadow-sm rounded-lg border border-gray-200"
                    >
                        <div class="px-6 py-5 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">
                                Código QR
                            </h3>
                        </div>
                        <div class="px-6 py-5">
                            <div class="flex flex-col items-center">
                                <div
                                    class="p-4 bg-white rounded-lg border-2 border-gray-100 flex items-center justify-center"
                                >
                                    <QrcodeVue
                                        v-if="activo.qr_code"
                                        :value="activo.qr_code"
                                        :size="200"
                                        level="H"
                                    />
                                    <div
                                        v-else
                                        class="text-center text-gray-400"
                                    >
                                        <QrCodeIcon class="h-32 w-32 mx-auto" />
                                        <p class="text-sm">QR no disponible</p>
                                    </div>
                                </div>
                                <p
                                    class="mt-3 text-xs font-mono text-gray-500 text-center break-all"
                                >
                                    {{ activo.qr_code }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Estado de disponibilidad -->
                    <div
                        class="bg-white shadow-sm rounded-lg border border-gray-200"
                    >
                        <div class="px-6 py-5">
                            <h3 class="text-sm font-medium text-gray-900 mb-4">
                                Disponibilidad
                            </h3>
                            <div
                                v-if="activo.estado === 'disponible'"
                                class="flex items-start"
                            >
                                <CheckCircleIcon
                                    class="h-6 w-6 text-green-500 mt-0.5"
                                />
                                <div class="ml-3">
                                    <p
                                        class="text-sm font-medium text-green-800"
                                    >
                                        Disponible para préstamo
                                    </p>
                                    <p class="mt-1 text-sm text-gray-600">
                                        Este activo está listo para ser
                                        solicitado
                                    </p>
                                </div>
                            </div>
                            <div
                                v-else-if="activo.estado === 'en_deposito'"
                                class="flex items-start"
                            >
                                <CheckCircleIcon
                                    class="h-6 w-6 text-blue-500 mt-0.5"
                                />
                                <div class="ml-3">
                                    <p
                                        class="text-sm font-medium text-blue-800"
                                    >
                                        En depósito
                                    </p>
                                    <p class="mt-1 text-sm text-gray-600">
                                        Solicita la entrega del activo
                                    </p>
                                </div>
                            </div>
                            <div v-else class="flex items-start">
                                <div
                                    class="h-6 w-6 rounded-full bg-gray-300 mt-0.5"
                                />
                                <div class="ml-3">
                                    <p
                                        class="text-sm font-medium text-gray-800"
                                    >
                                        No disponible
                                    </p>
                                    <p class="mt-1 text-sm text-gray-600">
                                        Estado:
                                        {{ getEstadoTexto(activo.estado) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
