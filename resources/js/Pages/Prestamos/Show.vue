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
        aprobado: "border border-blue-200 bg-blue-50 text-blue-800",
        pendiente: "border border-amber-200 bg-amber-50 text-amber-800",
        entregado: "border border-blue-200 bg-[#EFF6FF] text-blue-800",
        devuelto: "border border-emerald-200 bg-emerald-50 text-emerald-800",
        cancelada: "border border-red-200 bg-red-50 text-red-800",
    };
    return colors[status] || "border border-slate-200 bg-slate-100 text-slate-700";
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
                                    {{ prestamo.estado }}
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
                                v-if="isAdmin && (prestamo.estado === 'pendiente' || prestamo.estado === 'aprobado')"
                                @click="entregar"
                                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-transparent bg-[#2563EB] px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#1D4ED8] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2"
                            >
                                <HandThumbUpIcon class="h-5 w-5 shrink-0" aria-hidden="true" />
                                Entregar activo
                            </button>

                            <button
                                v-if="isAdmin && prestamo.estado === 'entregado'"
                                @click="devolver"
                                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-transparent bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                            >
                                <ArrowPathIcon class="h-5 w-5 shrink-0" aria-hidden="true" />
                                Registrar devolución
                            </button>
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
                                        {{ prestamo.activo.descripcion }}
                                    </dd>
                                    <dd class="mt-2 break-words text-sm font-semibold text-[#475569]">
                                        Código:
                                        <span class="break-all font-mono text-[#334155]">{{ prestamo.activo_codigo }}</span>
                                        | Tipo:
                                        {{
                                            prestamo.activo.tipo_activo?.nombre ||
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
                                        {{ prestamo.usuario.name }}
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
                                        Inicio:
                                        {{
                                            new Date(
                                                prestamo.inicio_previsto,
                                            ).toLocaleString()
                                        }}
                                    </dd>
                                    <dd class="mt-1 text-sm font-semibold text-[#475569]">
                                        Fin:
                                        {{
                                            new Date(
                                                prestamo.fin_previsto,
                                            ).toLocaleString()
                                        }}
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
                                        {{
                                            new Date(
                                                prestamo.entregado_en,
                                            ).toLocaleString()
                                        }}
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
                                        {{
                                            new Date(
                                                prestamo.devuelto_en,
                                            ).toLocaleString()
                                        }}
                                    </dd>
                                    <dd class="mt-1 break-words text-xs font-semibold text-emerald-800">
                                        Condición: {{ prestamo.condicion_entrada }}
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
                                    :href="
                                        route(
                                            'blockchain.activo.history',
                                            prestamo.activo_codigo,
                                        )
                                    "
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
                                            v-if="prestamo.activo.qr_code"
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
                                {{ prestamo.estado }}
                            </span>

                            <div v-if="form.processing" class="mt-4 rounded-xl border border-blue-200 bg-[#EFF6FF] px-4 py-3 text-sm font-bold text-blue-800">
                                Procesando acción...
                            </div>
                        </section>
                    </aside>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
