<script setup>
import { computed, reactive } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {
    ClockIcon,
    EyeIcon,
    PlusIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    prestamos: {
        type: [Object, Array],
        required: true,
    },
    isAdmin: {
        type: Boolean,
        default: false,
    },
    modo_administrativo: {
        type: Boolean,
        default: false,
    },
    filtros: {
        type: Object,
        default: () => ({}),
    },
    estados: {
        type: Array,
        default: () => [],
    },
    conteos: {
        type: Object,
        default: () => ({}),
    },
});

const filtrosForm = reactive({
    buscar: props.filtros.buscar || "",
    estado: props.filtros.estado || "",
    fecha_desde: props.filtros.fecha_desde || "",
    fecha_hasta: props.filtros.fecha_hasta || "",
});

const prestamosListado = computed(() =>
    Array.isArray(props.prestamos) ? props.prestamos : props.prestamos?.data || [],
);

const enlacesPaginacion = computed(() =>
    Array.isArray(props.prestamos) ? [] : props.prestamos?.links || [],
);

const totalPrestamos = computed(() =>
    Array.isArray(props.prestamos)
        ? props.prestamos.length
        : props.prestamos?.total || 0,
);

const modoAdmin = computed(() => Boolean(props.modo_administrativo || props.isAdmin));

const estadoLabels = {
    pendiente: "Pendiente",
    aprobado: "Aprobado",
    entregado: "Entregado",
    devuelto: "Devuelto",
    vencido: "Vencido",
    rechazado: "Rechazado",
};

const estadoOptions = computed(() =>
    props.estados.map((estado) => ({
        value: estado,
        label: estadoLabels[estado] || estado,
    })),
);

const resumenes = computed(() => [
    {
        label: "Total",
        value: props.conteos.total || 0,
        tone: "border-slate-200 bg-white text-slate-900",
    },
    {
        label: "Pendientes",
        value: props.conteos.pendientes || 0,
        tone: "border-amber-200 bg-amber-50 text-amber-900",
    },
    {
        label: "Aprobados",
        value: props.conteos.aprobados || 0,
        tone: "border-blue-200 bg-blue-50 text-blue-900",
    },
    {
        label: "Entregados",
        value: props.conteos.entregados || 0,
        tone: "border-emerald-200 bg-emerald-50 text-emerald-900",
    },
    {
        label: "Devueltos",
        value: props.conteos.devueltos || 0,
        tone: "border-slate-200 bg-slate-50 text-slate-800",
    },
    {
        label: "Vencidos",
        value: props.conteos.vencidos || 0,
        tone: "border-red-200 bg-red-50 text-red-900",
    },
    {
        label: "Rechazados",
        value: props.conteos.rechazados || 0,
        tone: "border-red-200 bg-red-50 text-red-700",
    },
]);

const filtrosActivos = computed(() =>
    Object.values(filtrosForm).some((value) => String(value || "").trim() !== ""),
);

const pageTitle = computed(() =>
    modoAdmin.value ? "Gestión de préstamos" : "Mis préstamos",
);

const pageDescription = computed(() =>
    modoAdmin.value
        ? "Consulta y supervisa todas las solicitudes, entregas y devoluciones de activos."
        : "Consulta las solicitudes y préstamos de activos asociados a tu cuenta.",
);

const datosFiltro = () =>
    Object.fromEntries(
        Object.entries(filtrosForm).filter(
            ([, value]) => String(value || "").trim() !== "",
        ),
    );

const aplicarFiltros = () => {
    router.get(route("prestamos.index"), datosFiltro(), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const limpiarFiltros = () => {
    filtrosForm.buscar = "";
    filtrosForm.estado = "";
    filtrosForm.fecha_desde = "";
    filtrosForm.fecha_hasta = "";

    router.get(
        route("prestamos.index"),
        {},
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
};

const visitarPagina = (url) => {
    if (!url) return;

    router.visit(url, {
        preserveState: true,
        preserveScroll: true,
    });
};

const formatFecha = (fecha) => {
    if (!fecha) return "Sin fecha";

    return new Date(fecha).toLocaleDateString("es-BO", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};

const formatHora = (fecha) => {
    if (!fecha) return "--:--";

    return new Date(fecha).toLocaleTimeString("es-BO", {
        hour: "2-digit",
        minute: "2-digit",
    });
};

const formatFechaHora = (fecha) => {
    if (!fecha) return "Sin registro";

    return `${formatFecha(fecha)} - ${formatHora(fecha)}`;
};

const getEstadoBadge = (estado) => {
    const badges = {
        pendiente: "border-amber-200 bg-amber-50 text-amber-800",
        aprobado: "border-blue-200 bg-blue-50 text-blue-800",
        entregado: "border-emerald-200 bg-emerald-50 text-emerald-800",
        devuelto: "border-slate-200 bg-slate-100 text-slate-700",
        vencido: "border-red-200 bg-red-50 text-red-800",
        rechazado: "border-red-200 bg-red-50 text-red-700",
    };

    return badges[estado] || "border-slate-200 bg-slate-100 text-slate-700";
};

const getEstadoLabel = (estado) => estadoLabels[estado] || estado || "Sin estado";

const getControlPrestamo = (prestamo) => {
    const controles = {
        pendiente: "Pendiente de aprobacion",
        aprobado: "Aprobado, pendiente de entrega",
        entregado: "Activo entregado",
        devuelto: "Activo devuelto",
        vencido: "Préstamo vencido",
        rechazado: "Solicitud rechazada",
    };

    return controles[prestamo.estado] || "Sin información de control";
};

const activoDescripcion = (prestamo) =>
    prestamo.activo?.descripcion || "Activo no disponible";

const activoTipo = (prestamo) =>
    prestamo.activo?.tipo_activo?.nombre ||
    prestamo.activo?.tipoActivo?.nombre ||
    "Tipo no registrado";
</script>

<template>
    <Head :title="pageTitle" />

    <AuthenticatedLayout>
        <div class="min-h-screen w-full bg-[#F5F7FB] text-[#0F172A]">
            <div class="mx-auto flex w-full max-w-7xl flex-col gap-5 px-4 py-6 sm:px-6 lg:px-8">
                <section class="rounded-2xl border border-[#E2E8F0] bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                        <div class="min-w-0">
                            <span
                                v-if="modoAdmin"
                                class="inline-flex rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-blue-700"
                            >
                                Gestión institucional
                            </span>
                            <p
                                v-else
                                class="text-xs font-bold uppercase tracking-widest text-[#2563EB]"
                            >
                                Control personal de recursos
                            </p>
                            <h1 class="mt-3 text-2xl font-black text-[#0F172A] sm:text-3xl">
                                {{ pageTitle }}
                            </h1>
                            <p class="mt-2 max-w-3xl text-sm font-medium leading-6 text-[#475569] sm:text-base">
                                {{ pageDescription }}
                            </p>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                            <span class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] px-4 py-2.5 text-sm font-bold text-[#334155]">
                                <ClockIcon class="h-5 w-5 shrink-0 text-[#2563EB]" aria-hidden="true" />
                                {{ totalPrestamos }} préstamos
                            </span>

                            <button
                                type="button"
                                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-[#2563EB] px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#1D4ED8] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2"
                                @click="router.visit(route('activos.disponibles'))"
                            >
                                <PlusIcon class="h-5 w-5" aria-hidden="true" />
                                Nuevo préstamo
                            </button>
                        </div>
                    </div>
                </section>

                <section class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
                    <article
                        v-for="resumen in resumenes"
                        :key="resumen.label"
                        class="rounded-2xl border p-4 shadow-sm"
                        :class="resumen.tone"
                    >
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                            {{ resumen.label }}
                        </p>
                        <p class="mt-2 text-2xl font-black">
                            {{ resumen.value }}
                        </p>
                    </article>
                </section>

                <section class="rounded-2xl border border-[#E2E8F0] bg-white p-4 shadow-sm sm:p-5">
                    <form
                        class="grid gap-4 lg:grid-cols-[minmax(0,1.4fr)_minmax(170px,.8fr)_minmax(140px,.7fr)_minmax(140px,.7fr)_auto] lg:items-end"
                        @submit.prevent="aplicarFiltros"
                    >
                        <div class="min-w-0">
                            <label for="buscar" class="block text-sm font-bold text-[#334155]">
                                Buscar
                            </label>
                            <input
                                id="buscar"
                                v-model="filtrosForm.buscar"
                                type="search"
                                class="mt-2 w-full rounded-xl border border-[#E2E8F0] bg-white px-4 py-2.5 text-sm font-medium text-[#0F172A] outline-none transition focus:border-[#2563EB] focus:ring-2 focus:ring-blue-100"
                                placeholder="ID, solicitante, activo o tipo"
                            />
                        </div>

                        <div>
                            <label for="estado" class="block text-sm font-bold text-[#334155]">
                                Estado
                            </label>
                            <select
                                id="estado"
                                v-model="filtrosForm.estado"
                                class="mt-2 w-full rounded-xl border border-[#E2E8F0] bg-white px-4 py-2.5 text-sm font-medium text-[#0F172A] outline-none transition focus:border-[#2563EB] focus:ring-2 focus:ring-blue-100"
                            >
                                <option value="">Todos los estados</option>
                                <option
                                    v-for="estado in estadoOptions"
                                    :key="estado.value"
                                    :value="estado.value"
                                >
                                    {{ estado.label }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label for="fecha_desde" class="block text-sm font-bold text-[#334155]">
                                Desde
                            </label>
                            <input
                                id="fecha_desde"
                                v-model="filtrosForm.fecha_desde"
                                type="date"
                                class="mt-2 w-full rounded-xl border border-[#E2E8F0] bg-white px-4 py-2.5 text-sm font-medium text-[#0F172A] outline-none transition focus:border-[#2563EB] focus:ring-2 focus:ring-blue-100"
                            />
                        </div>

                        <div>
                            <label for="fecha_hasta" class="block text-sm font-bold text-[#334155]">
                                Hasta
                            </label>
                            <input
                                id="fecha_hasta"
                                v-model="filtrosForm.fecha_hasta"
                                type="date"
                                class="mt-2 w-full rounded-xl border border-[#E2E8F0] bg-white px-4 py-2.5 text-sm font-medium text-[#0F172A] outline-none transition focus:border-[#2563EB] focus:ring-2 focus:ring-blue-100"
                            />
                        </div>

                        <div class="flex flex-col gap-2 sm:flex-row lg:flex-col">
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-xl bg-[#2563EB] px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#1D4ED8] focus:outline-none focus:ring-2 focus:ring-blue-200"
                            >
                                Aplicar filtros
                            </button>
                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-xl border border-[#E2E8F0] bg-white px-4 py-2.5 text-sm font-bold text-[#334155] transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                @click="limpiarFiltros"
                            >
                                Limpiar
                            </button>
                        </div>
                    </form>

                    <p v-if="filtrosActivos" class="mt-4 text-sm font-semibold text-[#475569]">
                        Filtros activos aplicados al listado.
                    </p>
                </section>

                <section class="overflow-hidden rounded-2xl border border-[#E2E8F0] bg-white shadow-sm">
                    <div class="border-b border-[#E2E8F0] px-5 py-4 sm:px-6">
                        <h2 class="text-lg font-black text-[#0F172A]">
                            Solicitudes registradas
                        </h2>
                        <p class="mt-1 text-sm font-medium text-[#475569]">
                            {{ Array.isArray(prestamos) ? prestamosListado.length : (prestamos.from || 0) }}-{{ Array.isArray(prestamos) ? prestamosListado.length : (prestamos.to || 0) }} de {{ totalPrestamos }} registros
                        </p>
                    </div>

                    <div v-if="prestamosListado.length > 0" class="hidden w-full overflow-x-auto lg:block">
                        <table class="w-full min-w-[1060px] table-fixed text-left">
                            <colgroup v-if="modoAdmin">
                                <col class="w-[12%]" />
                                <col class="w-[17%]" />
                                <col class="w-[19%]" />
                                <col class="w-[18%]" />
                                <col class="w-[10%]" />
                                <col class="w-[14%]" />
                                <col class="w-[10%]" />
                            </colgroup>
                            <colgroup v-else>
                                <col class="w-[14%]" />
                                <col class="w-[26%]" />
                                <col class="w-[22%]" />
                                <col class="w-[12%]" />
                                <col class="w-[16%]" />
                                <col class="w-[10%]" />
                            </colgroup>

                            <thead class="bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-4 py-3">Préstamo</th>
                                    <th v-if="modoAdmin" class="px-4 py-3">Solicitante</th>
                                    <th class="px-4 py-3">Activo</th>
                                    <th class="px-4 py-3">Periodo</th>
                                    <th class="px-4 py-3">Estado</th>
                                    <th class="px-4 py-3">Entrega / devolución</th>
                                    <th class="px-4 py-3 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E2E8F0]">
                                <tr
                                    v-for="prestamo in prestamosListado"
                                    :key="prestamo.id"
                                    class="align-top transition hover:bg-slate-50"
                                >
                                    <td class="px-4 py-5">
                                        <p class="text-sm font-black text-[#0F172A]">
                                            #{{ prestamo.id }}
                                        </p>
                                        <p class="mt-1 whitespace-nowrap text-xs font-medium text-[#64748B]">
                                            {{ formatFecha(prestamo.created_at) }}
                                        </p>
                                    </td>

                                    <td v-if="modoAdmin" class="px-4 py-5">
                                        <p class="break-words text-sm font-bold text-[#0F172A]">
                                            {{ prestamo.usuario?.nombre || "Usuario no disponible" }}
                                        </p>
                                        <p class="mt-1 break-words text-sm font-medium text-slate-500">
                                            {{ prestamo.usuario?.email || "Sin correo" }}
                                        </p>
                                    </td>

                                    <td class="px-4 py-5">
                                        <p class="break-words text-sm font-black text-[#0F172A]">
                                            {{ activoDescripcion(prestamo) }}
                                        </p>
                                        <p class="mt-1 break-all font-mono text-xs font-semibold text-[#475569]">
                                            Código {{ prestamo.activo_codigo }}
                                        </p>
                                        <p class="mt-1 break-words text-xs font-medium text-[#64748B]">
                                            {{ activoTipo(prestamo) }}
                                        </p>
                                    </td>

                                    <td class="px-4 py-5">
                                        <p class="text-sm font-bold text-[#0F172A]">
                                            {{ formatFecha(prestamo.inicio_previsto) }}
                                        </p>
                                        <p class="mt-1 whitespace-nowrap text-xs font-semibold text-[#475569]">
                                            {{ formatHora(prestamo.inicio_previsto) }} - {{ formatHora(prestamo.fin_previsto) }}
                                        </p>
                                        <p v-if="prestamo.entregado_en" class="mt-2 text-xs font-medium text-emerald-700">
                                            Entregado: {{ formatFechaHora(prestamo.entregado_en) }}
                                        </p>
                                        <p v-if="prestamo.devuelto_en" class="mt-1 text-xs font-medium text-slate-600">
                                            Devuelto: {{ formatFechaHora(prestamo.devuelto_en) }}
                                        </p>
                                    </td>

                                    <td class="px-4 py-5">
                                        <span
                                            class="inline-flex min-w-fit whitespace-nowrap rounded-full border px-2.5 py-1 text-xs font-black"
                                            :class="getEstadoBadge(prestamo.estado)"
                                        >
                                            {{ getEstadoLabel(prestamo.estado) }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-5">
                                        <p class="break-words text-sm font-semibold leading-5 text-[#475569]">
                                            {{ getControlPrestamo(prestamo) }}
                                        </p>
                                    </td>

                                    <td class="px-4 py-5 text-center">
                                        <Link
                                            :href="route('prestamos.show', prestamo.id)"
                                            class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-blue-700 transition hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                        >
                                            <EyeIcon class="h-4 w-4 shrink-0" aria-hidden="true" />
                                            Ver detalle
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="prestamosListado.length > 0" class="grid gap-3 p-4 lg:hidden">
                        <article
                            v-for="prestamo in prestamosListado"
                            :key="`mobile-${prestamo.id}`"
                            class="rounded-2xl border border-[#E2E8F0] bg-white p-4"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-black text-[#0F172A]">
                                        Préstamo #{{ prestamo.id }}
                                    </p>
                                    <p class="mt-1 break-words text-sm font-semibold text-[#475569]">
                                        {{ activoDescripcion(prestamo) }}
                                    </p>
                                </div>
                                <span
                                    class="shrink-0 rounded-full border px-2.5 py-1 text-xs font-black"
                                    :class="getEstadoBadge(prestamo.estado)"
                                >
                                    {{ getEstadoLabel(prestamo.estado) }}
                                </span>
                            </div>

                            <dl class="mt-4 grid gap-3 text-sm">
                                <div v-if="modoAdmin" class="rounded-xl bg-slate-50 p-3">
                                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                        Solicitante
                                    </dt>
                                    <dd class="mt-1 break-words font-bold text-[#0F172A]">
                                        {{ prestamo.usuario?.nombre || "Usuario no disponible" }}
                                    </dd>
                                    <dd class="break-words text-xs font-medium text-[#64748B]">
                                        {{ prestamo.usuario?.email || "Sin correo" }}
                                    </dd>
                                </div>

                                <div class="grid gap-3 sm:grid-cols-2">
                                    <div class="rounded-xl bg-slate-50 p-3">
                                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                            Activo
                                        </dt>
                                        <dd class="mt-1 break-words font-bold text-[#0F172A]">
                                            Código {{ prestamo.activo_codigo }}
                                        </dd>
                                        <dd class="break-words text-xs font-medium text-[#64748B]">
                                            {{ activoTipo(prestamo) }}
                                        </dd>
                                    </div>

                                    <div class="rounded-xl bg-slate-50 p-3">
                                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                            Periodo
                                        </dt>
                                        <dd class="mt-1 font-bold text-[#0F172A]">
                                            {{ formatFecha(prestamo.inicio_previsto) }}
                                        </dd>
                                        <dd class="whitespace-nowrap text-xs font-medium text-[#64748B]">
                                            {{ formatHora(prestamo.inicio_previsto) }} - {{ formatHora(prestamo.fin_previsto) }}
                                        </dd>
                                    </div>
                                </div>

                                <div class="rounded-xl bg-slate-50 p-3">
                                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                        Entrega / devolución
                                    </dt>
                                    <dd class="mt-1 break-words font-bold text-[#0F172A]">
                                        {{ getControlPrestamo(prestamo) }}
                                    </dd>
                                    <dd v-if="prestamo.entregado_en" class="mt-1 text-xs font-medium text-emerald-700">
                                        Entregado: {{ formatFechaHora(prestamo.entregado_en) }}
                                    </dd>
                                    <dd v-if="prestamo.devuelto_en" class="mt-1 text-xs font-medium text-slate-600">
                                        Devuelto: {{ formatFechaHora(prestamo.devuelto_en) }}
                                    </dd>
                                </div>

                                <Link
                                    :href="route('prestamos.show', prestamo.id)"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-blue-700 transition hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                >
                                    <EyeIcon class="h-4 w-4" aria-hidden="true" />
                                    Ver detalle
                                </Link>
                            </dl>
                        </article>
                    </div>

                    <div v-if="prestamosListado.length === 0" class="flex flex-col items-center justify-center px-5 py-14 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl border border-blue-200 bg-[#EFF6FF] text-[#2563EB]">
                            <ClockIcon class="h-8 w-8" aria-hidden="true" />
                        </div>
                        <h3 class="mt-4 text-lg font-black text-[#0F172A]">
                            No se encontraron préstamos con los filtros seleccionados.
                        </h3>
                        <p class="mx-auto mt-2 max-w-md text-sm font-medium leading-6 text-[#475569]">
                            Ajusta la búsqueda o limpia los filtros para volver al listado disponible.
                        </p>
                        <button
                            v-if="filtrosActivos"
                            type="button"
                            class="mt-5 rounded-xl border border-[#E2E8F0] bg-white px-4 py-2.5 text-sm font-bold text-[#334155] transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-100"
                            @click="limpiarFiltros"
                        >
                            Limpiar filtros
                        </button>
                    </div>

                    <div
                        v-if="enlacesPaginacion.length > 3"
                        class="flex flex-col gap-3 border-t border-[#E2E8F0] px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <p class="text-sm font-medium text-[#475569]">
                            Página {{ prestamos.current_page }} de {{ prestamos.last_page }}
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="link in enlacesPaginacion"
                                :key="`${link.label}-${link.url}`"
                                type="button"
                                :disabled="!link.url"
                                class="min-w-10 rounded-lg border px-3 py-2 text-sm font-bold transition focus:outline-none focus:ring-2 focus:ring-blue-100 disabled:cursor-not-allowed disabled:border-slate-100 disabled:bg-slate-50 disabled:text-slate-300"
                                :class="link.active ? 'border-[#2563EB] bg-[#2563EB] text-white' : 'border-[#E2E8F0] bg-white text-[#334155] hover:bg-slate-50'"
                                @click="visitarPagina(link.url)"
                            >
                                <span v-html="link.label"></span>
                            </button>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
