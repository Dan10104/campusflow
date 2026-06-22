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
        disponible: "border border-emerald-200 bg-emerald-50 text-emerald-800",
        en_deposito: "border border-blue-200 bg-blue-50 text-blue-800",
        prestado: "border border-blue-200 bg-blue-50 text-blue-800",
        mantenimiento: "border border-amber-200 bg-amber-50 text-amber-800",
        baja: "border border-slate-200 bg-slate-100 text-slate-700",
    };
    return badges[estado] || "border border-slate-200 bg-slate-100 text-slate-700";
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
        <div class="min-h-screen w-full bg-[#F5F7FB] text-[#0F172A]">
            <div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
                <section class="rounded-2xl border border-[#E2E8F0] bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
                        <div class="min-w-0">
                            <button
                                @click="volver"
                                class="inline-flex items-center gap-2 rounded-xl border border-[#E2E8F0] bg-white px-3 py-2 text-sm font-bold text-[#334155] shadow-sm transition hover:bg-[#F8FAFC] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2"
                            >
                                <ArrowLeftIcon class="h-5 w-5 text-[#2563EB]" aria-hidden="true" />
                                Volver a activos
                            </button>

                            <div class="mt-5 flex flex-wrap items-center gap-3">
                                <p class="text-xs font-bold uppercase tracking-widest text-[#2563EB]">
                                    Detalle institucional
                                </p>
                                <span
                                    :class="getEstadoBadge(activo.estado)"
                                    class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold"
                                >
                                    {{ getEstadoTexto(activo.estado) }}
                                </span>
                            </div>

                            <h1 class="mt-2 text-3xl font-bold tracking-tight text-[#0F172A]">
                                {{ activo.descripcion }}
                            </h1>
                            <p class="mt-2 flex flex-wrap items-center gap-2 text-sm font-semibold text-[#475569]">
                                <span class="font-mono text-[#334155]">{{ activo.codigo }}</span>
                                <span aria-hidden="true">•</span>
                                <span>{{ activo.tipo_activo.nombre }}</span>
                            </p>
                        </div>

                        <button
                            @click="solicitarPrestamo"
                            :disabled="
                                activo.estado !== 'disponible' &&
                                activo.estado !== 'en_deposito'
                            "
                            :class="[
                                'inline-flex items-center justify-center rounded-xl border border-transparent px-4 py-3 text-sm font-bold shadow-sm transition focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2',
                                activo.estado === 'disponible' ||
                                activo.estado === 'en_deposito'
                                    ? 'bg-[#2563EB] text-white hover:bg-[#1D4ED8]'
                                    : 'cursor-not-allowed bg-slate-200 text-slate-500',
                            ]"
                        >
                            Solicitar préstamo
                        </button>
                    </div>
                </section>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <main class="space-y-6 lg:col-span-2">
                        <section class="overflow-hidden rounded-2xl border border-[#E2E8F0] bg-white shadow-sm">
                            <div class="border-b border-[#E2E8F0] px-5 py-5 sm:px-6">
                                <h2 class="text-lg font-bold text-[#0F172A]">
                                    Información general
                                </h2>
                                <p class="mt-1 text-sm text-[#475569]">
                                    Datos registrados para identificar y ubicar el activo.
                                </p>
                            </div>

                            <dl class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-2 sm:p-6">
                                <div class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                                    <dt class="text-xs font-bold uppercase tracking-wide text-[#64748B]">
                                        Código
                                    </dt>
                                    <dd class="mt-1 break-all font-mono text-sm font-semibold text-[#0F172A]">
                                        {{ activo.codigo }}
                                    </dd>
                                </div>

                                <div class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                                    <dt class="text-xs font-bold uppercase tracking-wide text-[#64748B]">
                                        Estado
                                    </dt>
                                    <dd class="mt-2">
                                        <span
                                            :class="getEstadoBadge(activo.estado)"
                                            class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold"
                                        >
                                            {{ getEstadoTexto(activo.estado) }}
                                        </span>
                                    </dd>
                                </div>

                                <div v-if="activo.marca" class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                                    <dt class="text-xs font-bold uppercase tracking-wide text-[#64748B]">
                                        Marca
                                    </dt>
                                    <dd class="mt-1 text-sm font-semibold text-[#0F172A]">
                                        {{ activo.marca }}
                                    </dd>
                                </div>

                                <div v-if="activo.modelo" class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                                    <dt class="text-xs font-bold uppercase tracking-wide text-[#64748B]">
                                        Modelo
                                    </dt>
                                    <dd class="mt-1 text-sm font-semibold text-[#0F172A]">
                                        {{ activo.modelo }}
                                    </dd>
                                </div>

                                <div v-if="activo.ubicacion_actual" class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                                    <dt class="text-xs font-bold uppercase tracking-wide text-[#64748B]">
                                        Ubicación actual
                                    </dt>
                                    <dd class="mt-1 text-sm font-semibold text-[#0F172A]">
                                        {{ activo.ubicacion_actual }}
                                    </dd>
                                </div>

                                <div class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                                    <dt class="text-xs font-bold uppercase tracking-wide text-[#64748B]">
                                        Tipo de activo
                                    </dt>
                                    <dd class="mt-1 text-sm font-semibold text-[#0F172A]">
                                        {{ activo.tipo_activo.nombre }}
                                    </dd>
                                </div>
                            </dl>
                        </section>

                        <section class="overflow-hidden rounded-2xl border border-[#E2E8F0] bg-white shadow-sm">
                            <div class="border-b border-[#E2E8F0] px-5 py-5 sm:px-6">
                                <h2 class="text-lg font-bold text-[#0F172A]">
                                    Historial de préstamos recientes
                                </h2>
                                <p class="mt-1 text-sm text-[#475569]">
                                    Movimientos registrados para este activo.
                                </p>
                            </div>

                            <div class="p-5 sm:p-6">
                                <div
                                    v-if="
                                        activo.reservas_activos &&
                                        activo.reservas_activos.length > 0
                                    "
                                    class="flow-root"
                                >
                                    <ul role="list" class="space-y-4">
                                        <li
                                            v-for="(
                                                prestamo, idx
                                            ) in activo.reservas_activos"
                                            :key="prestamo.id"
                                            class="relative rounded-2xl border border-[#E2E8F0] bg-[#F8FAFC] p-4"
                                        >
                                            <span
                                                v-if="
                                                    idx !==
                                                    activo.reservas_activos.length -
                                                        1
                                                "
                                                class="absolute left-8 top-12 h-full w-px bg-[#E2E8F0]"
                                                aria-hidden="true"
                                            />
                                            <div class="relative flex gap-3">
                                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-blue-200 bg-[#EFF6FF] text-[#2563EB]">
                                                    <UserIcon class="h-5 w-5" aria-hidden="true" />
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-sm font-bold text-[#0F172A]">
                                                        {{
                                                            prestamo.usuario
                                                                .nombre
                                                        }}
                                                        <span class="font-semibold text-[#475569]">
                                                            -
                                                            {{
                                                                prestamo.estado
                                                            }}
                                                        </span>
                                                    </p>
                                                    <p class="mt-1 text-sm leading-6 text-[#475569]">
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
                                        </li>
                                    </ul>
                                </div>
                                <div v-else class="rounded-2xl border border-dashed border-[#CBD5E1] bg-[#F8FAFC] p-8 text-center">
                                    <ClockIcon class="mx-auto h-12 w-12 text-[#64748B]" aria-hidden="true" />
                                    <p class="mt-3 text-sm font-semibold text-[#475569]">
                                        No hay préstamos registrados
                                    </p>
                                </div>
                            </div>
                        </section>
                    </main>

                    <aside class="space-y-6">
                        <section class="overflow-hidden rounded-2xl border border-[#E2E8F0] bg-white shadow-sm">
                            <div class="border-b border-[#E2E8F0] px-5 py-5">
                                <h2 class="text-lg font-bold text-[#0F172A]">
                                    Código QR
                                </h2>
                                <p class="mt-1 text-sm text-[#475569]">
                                    Identificador asociado al activo.
                                </p>
                            </div>
                            <div class="p-5">
                                <div class="flex flex-col items-center">
                                    <div class="flex min-h-60 w-full items-center justify-center rounded-2xl border border-[#E2E8F0] bg-white p-4">
                                        <QrcodeVue
                                            v-if="activo.qr_code"
                                            :value="activo.qr_code"
                                            :size="200"
                                            level="H"
                                        />
                                        <div
                                            v-else
                                            class="text-center text-[#64748B]"
                                        >
                                            <QrCodeIcon class="mx-auto h-24 w-24" aria-hidden="true" />
                                            <p class="mt-2 text-sm font-semibold">QR no disponible</p>
                                        </div>
                                    </div>
                                    <p class="mt-3 break-all text-center font-mono text-xs font-semibold text-[#475569]">
                                        {{ activo.qr_code }}
                                    </p>
                                </div>
                            </div>
                        </section>

                        <section class="rounded-2xl border border-[#E2E8F0] bg-white p-5 shadow-sm">
                            <h2 class="text-sm font-bold uppercase tracking-wide text-[#334155]">
                                Disponibilidad
                            </h2>

                            <div
                                v-if="activo.estado === 'disponible'"
                                class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-4"
                            >
                                <div class="flex items-start">
                                    <CheckCircleIcon class="mt-0.5 h-6 w-6 text-emerald-600" aria-hidden="true" />
                                    <div class="ml-3">
                                        <p class="text-sm font-bold text-emerald-800">
                                            Disponible para préstamo
                                        </p>
                                        <p class="mt-1 text-sm leading-6 text-[#475569]">
                                            Este activo está listo para ser solicitado.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div
                                v-else-if="activo.estado === 'en_deposito'"
                                class="mt-4 rounded-2xl border border-blue-200 bg-[#EFF6FF] p-4"
                            >
                                <div class="flex items-start">
                                    <CheckCircleIcon class="mt-0.5 h-6 w-6 text-[#2563EB]" aria-hidden="true" />
                                    <div class="ml-3">
                                        <p class="text-sm font-bold text-blue-800">
                                            En depósito
                                        </p>
                                        <p class="mt-1 text-sm leading-6 text-[#475569]">
                                            Solicita la entrega del activo.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="mt-4 rounded-2xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                                <div class="flex items-start">
                                    <div class="mt-1 h-4 w-4 rounded-full bg-[#CBD5E1]" />
                                    <div class="ml-3">
                                        <p class="text-sm font-bold text-[#334155]">
                                            No disponible
                                        </p>
                                        <p class="mt-1 text-sm leading-6 text-[#475569]">
                                            Estado:
                                            {{ getEstadoTexto(activo.estado) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </aside>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
