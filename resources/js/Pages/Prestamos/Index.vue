<script setup>
import { Head, useForm, router, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref } from "vue";
import {
    CheckCircleIcon,
    ClockIcon,
    XCircleIcon,
    HandThumbUpIcon,
    ArrowPathIcon,
    EyeIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    prestamos: Array,
    isAdmin: Boolean,
});

const form = useForm({});

const entregar = (id) => {
    if (confirm("¿Confirmar entrega del activo al usuario?")) {
        form.post(route("prestamos.entregar", id), {
            preserveScroll: true,
            onSuccess: () => alert("Activo entregado correctamente."),
        });
    }
};

const devolver = (id) => {
    const estado = prompt(
        "Condición del activo (ej. Buen estado, Dañado):",
        "Buen estado"
    );
    if (estado) {
        form.transform((data) => ({ ...data, condition: estado })).post(
            route("prestamos.devolver", id),
            {
                preserveScroll: true,
                onSuccess: () => alert("Activo devuelto y liberado."),
            }
        );
    }
};

const getStatusColor = (status) => {
    switch (status) {
        case "pendiente":
            return "bg-yellow-100 text-yellow-800";
        case "aprobado":
            return "bg-teal-100 text-teal-800";
        case "entregado":
            return "bg-blue-100 text-blue-800";
        case "devuelto":
            return "bg-green-100 text-green-800";
        case "cancelada":
            return "bg-red-100 text-red-800";
        default:
            return "bg-gray-100 text-gray-800";
    }
};

const getStatusLabel = (status) => {
    switch (status) {
        case "pendiente":
            return "Pendiente";
        case "aprobado":
            return "Aprobado";
        case "entregado":
            return "En Préstamo";
        case "devuelto":
            return "Devuelto";
        case "cancelada":
            return "Cancelado";
        default:
            return status;
    }
};
</script>

<template>
    <Head title="Gestión de Préstamos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gestión de Préstamos de Activos
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Action Bar -->
                <div class="mb-6 flex justify-end">
                    <button
                        @click="router.visit(route('activos.disponibles'))"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium shadow-sm transition-colors"
                    >
                        + Nuevo Préstamo
                    </button>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Activo
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Usuario
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Fechas
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Estado
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr
                                    v-for="prestamo in prestamos"
                                    :key="prestamo.id"
                                    class="hover:bg-gray-50 transition-colors"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div>
                                                <div
                                                    class="text-sm font-medium text-gray-900"
                                                >
                                                    {{
                                                        prestamo.activo
                                                            ? prestamo.activo
                                                                  .descripcion
                                                            : "Activo Eliminado"
                                                    }}
                                                </div>
                                                <div
                                                    class="text-sm text-gray-500"
                                                >
                                                    {{ prestamo.activo_codigo }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{
                                                prestamo.usuario
                                                    ? prestamo.usuario.name
                                                    : "N/A"
                                            }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ prestamo.usuario_id }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <span class="font-semibold"
                                                >Inicio:</span
                                            >
                                            {{
                                                new Date(
                                                    prestamo.inicio_previsto
                                                ).toLocaleString()
                                            }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <span class="font-semibold"
                                                >Fin:</span
                                            >
                                            {{
                                                new Date(
                                                    prestamo.fin_previsto
                                                ).toLocaleString()
                                            }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="[
                                                'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                                getStatusColor(prestamo.estado),
                                            ]"
                                        >
                                            {{
                                                getStatusLabel(prestamo.estado)
                                            }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                                    >
                                        <!-- Acciones Admin -->
                                        <div class="flex justify-end space-x-2">
                                            <!-- VER DETALLES (NUEVO) -->
                                            <Link
                                                :href="route('prestamos.show', prestamo.id)"
                                                class="text-gray-600 hover:text-gray-900 flex items-center bg-gray-50 border border-gray-200 px-2 py-1 rounded shadow-sm"
                                                title="Ver Detalles y QR"
                                            >
                                                <EyeIcon class="h-4 w-4 mr-1" />
                                                Ver
                                            </Link>

                                            <!-- ACCIONES PROCESO -->
                                            <button
                                                v-if="
                                                    isAdmin &&
                                                    (prestamo.estado === 'pendiente' || prestamo.estado === 'aprobado')
                                                "
                                                @click="entregar(prestamo.id)"
                                                class="text-blue-600 hover:text-blue-900 flex items-center"
                                                title="Entregar Activo"
                                            >
                                                <HandThumbUpIcon
                                                    class="h-5 w-5 mr-1"
                                                />
                                                Entregar
                                            </button>

                                            <button
                                                v-if="
                                                    isAdmin && prestamo.estado === 'entregado'
                                                "
                                                @click="devolver(prestamo.id)"
                                                class="text-green-600 hover:text-green-900 flex items-center"
                                                title="Registrar Devolución"
                                            >
                                                <ArrowPathIcon
                                                    class="h-5 w-5 mr-1"
                                                />
                                                Devolver
                                            </button>

                                            <span
                                                v-if="
                                                    [
                                                        'devuelto',
                                                        'cancelada',
                                                    ].includes(prestamo.estado)
                                                "
                                                class="text-gray-400"
                                            >
                                                -
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="prestamos.length === 0">
                                    <td
                                        colspan="5"
                                        class="px-6 py-10 text-center text-gray-500"
                                    >
                                        No hay solicitudes de préstamo
                                        registradas.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
