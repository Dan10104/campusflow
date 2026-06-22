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
            return "border border-amber-200 bg-amber-50 text-amber-800";
        case "aprobado":
            return "border border-blue-200 bg-blue-50 text-blue-800";
        case "entregado":
            return "border border-blue-200 bg-[#EFF6FF] text-blue-800";
        case "devuelto":
            return "border border-emerald-200 bg-emerald-50 text-emerald-800";
        case "cancelada":
            return "border border-red-200 bg-red-50 text-red-800";
        default:
            return "border border-slate-200 bg-slate-100 text-slate-700";
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
        <div class="min-h-screen w-full bg-[#F5F7FB] text-[#0F172A]">
            <div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
                <section class="rounded-2xl border border-[#E2E8F0] bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                        <div class="min-w-0">
                            <p class="text-xs font-bold uppercase tracking-widest text-[#2563EB]">
                                Control de recursos
                            </p>
                            <h1 class="mt-2 text-3xl font-bold tracking-tight text-[#0F172A]">
                                Préstamos de activos
                            </h1>
                            <p class="mt-2 max-w-3xl text-sm leading-6 text-[#475569]">
                                Revisa solicitudes, entregas y devoluciones de activos institucionales.
                            </p>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                            <span class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] px-4 py-2.5 text-sm font-bold text-[#334155]">
                                <ClockIcon class="h-5 w-5 shrink-0 text-[#2563EB]" aria-hidden="true" />
                                {{ prestamos.length }} préstamos
                            </span>

                            <button
                                type="button"
                                @click="router.visit(route('activos.disponibles'))"
                                class="inline-flex min-h-11 items-center justify-center rounded-xl border border-transparent bg-[#2563EB] px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#1D4ED8] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2"
                            >
                                Nuevo préstamo
                            </button>
                        </div>
                    </div>
                </section>

                <section class="overflow-hidden rounded-2xl border border-[#E2E8F0] bg-white shadow-sm">
                    <div class="border-b border-[#E2E8F0] px-5 py-4 sm:px-6">
                        <h2 class="text-lg font-bold text-[#0F172A]">
                            Solicitudes registradas
                        </h2>
                        <p class="mt-1 text-sm text-[#475569]">
                            Las acciones disponibles dependen del estado del préstamo y del rol del usuario.
                        </p>
                    </div>

                    <div v-if="prestamos.length === 0" class="p-8 text-center sm:p-10">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl border border-blue-200 bg-[#EFF6FF] text-[#2563EB]">
                            <ClockIcon class="h-8 w-8" aria-hidden="true" />
                        </div>
                        <h3 class="mt-4 text-lg font-bold text-[#0F172A]">
                            No hay solicitudes de préstamo
                        </h3>
                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-[#475569]">
                            Cuando se registren solicitudes de préstamo aparecerán en esta vista.
                        </p>
                    </div>

                    <div v-else class="divide-y divide-[#E2E8F0]">
                        <article
                            v-for="prestamo in prestamos"
                            :key="prestamo.id"
                            class="grid gap-4 p-5 transition hover:bg-[#F8FAFC] lg:grid-cols-12 lg:items-center lg:px-6"
                        >
                            <div class="min-w-0 lg:col-span-3">
                                <p class="text-xs font-bold uppercase tracking-wide text-[#64748B]">
                                    Activo
                                </p>
                                <p class="mt-1 break-words text-sm font-bold text-[#0F172A]">
                                    {{
                                        prestamo.activo
                                            ? prestamo.activo.descripcion
                                            : "Activo Eliminado"
                                    }}
                                </p>
                                <p class="mt-1 break-all font-mono text-xs font-semibold text-[#475569]">
                                    {{ prestamo.activo_codigo }}
                                </p>
                            </div>

                            <div class="min-w-0 lg:col-span-2">
                                <p class="text-xs font-bold uppercase tracking-wide text-[#64748B]">
                                    Usuario
                                </p>
                                <p class="mt-1 break-words text-sm font-semibold text-[#0F172A]">
                                    {{
                                        prestamo.usuario
                                            ? prestamo.usuario.name
                                            : "N/A"
                                    }}
                                </p>
                                <p class="mt-1 text-xs font-semibold text-[#475569]">
                                    {{ prestamo.usuario_id }}
                                </p>
                            </div>

                            <div class="min-w-0 lg:col-span-3">
                                <p class="text-xs font-bold uppercase tracking-wide text-[#64748B]">
                                    Fechas
                                </p>
                                <p class="mt-1 text-sm font-semibold text-[#0F172A]">
                                    Inicio:
                                    {{
                                        new Date(
                                            prestamo.inicio_previsto
                                        ).toLocaleString()
                                    }}
                                </p>
                                <p class="mt-1 text-sm font-semibold text-[#475569]">
                                    Fin:
                                    {{
                                        new Date(
                                            prestamo.fin_previsto
                                        ).toLocaleString()
                                    }}
                                </p>
                            </div>

                            <div class="lg:col-span-1">
                                <span
                                    :class="getStatusColor(prestamo.estado)"
                                    class="inline-flex min-h-7 items-center justify-center rounded-full px-3 py-1 text-center text-xs font-semibold leading-none"
                                >
                                    {{ getStatusLabel(prestamo.estado) }}
                                </span>
                            </div>

                            <div class="flex flex-wrap gap-2 lg:col-span-3 lg:justify-end">
                                <Link
                                    :href="route('prestamos.show', prestamo.id)"
                                    class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-[#E2E8F0] bg-white px-3 py-2 text-sm font-bold text-[#334155] shadow-sm transition hover:bg-[#EFF6FF] hover:text-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2"
                                    title="Ver Detalles y QR"
                                >
                                    <EyeIcon class="h-4 w-4 shrink-0" aria-hidden="true" />
                                    Ver
                                </Link>

                                <button
                                    v-if="
                                        isAdmin &&
                                        (prestamo.estado === 'pendiente' || prestamo.estado === 'aprobado')
                                    "
                                    @click="entregar(prestamo.id)"
                                    class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-transparent bg-[#2563EB] px-3 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-[#1D4ED8] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2"
                                    title="Entregar Activo"
                                >
                                    <HandThumbUpIcon class="h-4 w-4 shrink-0" aria-hidden="true" />
                                    Entregar
                                </button>

                                <button
                                    v-if="
                                        isAdmin && prestamo.estado === 'entregado'
                                    "
                                    @click="devolver(prestamo.id)"
                                    class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-bold text-emerald-800 shadow-sm transition hover:bg-emerald-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                                    title="Registrar Devolución"
                                >
                                    <ArrowPathIcon class="h-4 w-4 shrink-0" aria-hidden="true" />
                                    Devolver
                                </button>

                                <span
                                    v-if="
                                        [
                                            'devuelto',
                                            'cancelada',
                                        ].includes(prestamo.estado)
                                    "
                                    class="inline-flex min-h-10 items-center justify-center rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] px-3 py-2 text-sm font-bold text-[#64748B]"
                                >
                                    Sin acciones
                                </span>
                            </div>
                        </article>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
