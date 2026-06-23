<script setup>
import { computed, reactive } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    reservas: {
        type: Object,
        required: true,
    },
    filtros: {
        type: Object,
        default: () => ({}),
    },
    estados: {
        type: Array,
        default: () => [],
    },
    aulas: {
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
    aula: props.filtros.aula || "",
    fecha_desde: props.filtros.fecha_desde || "",
    fecha_hasta: props.filtros.fecha_hasta || "",
});

const reservasListado = computed(() => props.reservas?.data || []);
const enlacesPaginacion = computed(() => props.reservas?.links || []);

const estadosTexto = {
    pendiente: "Pendiente",
    aprobada: "Aprobada",
    confirmada: "Confirmada",
    en_uso: "En uso",
    completada: "Completada",
    cancelada: "Cancelada",
    liberada_auto: "Liberada auto",
};

const estadoOptions = computed(() =>
    props.estados.map((estado) => ({
        value: estado,
        label: estadosTexto[estado] || estado,
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
        label: "Confirmadas",
        value: props.conteos.confirmadas || 0,
        tone: "border-blue-200 bg-blue-50 text-blue-900",
    },
    {
        label: "En uso",
        value: props.conteos.en_uso || 0,
        tone: "border-emerald-200 bg-emerald-50 text-emerald-900",
    },
    {
        label: "Completadas",
        value: props.conteos.completadas || 0,
        tone: "border-slate-200 bg-slate-50 text-slate-800",
    },
]);

const filtrosActivos = computed(() =>
    Object.values(filtrosForm).some((value) => String(value || "").trim() !== ""),
);

const datosFiltro = () =>
    Object.fromEntries(
        Object.entries(filtrosForm).filter(
            ([, value]) => String(value || "").trim() !== "",
        ),
    );

const aplicarFiltros = () => {
    router.get(route("admin.reservas.index"), datosFiltro(), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const limpiarFiltros = () => {
    filtrosForm.buscar = "";
    filtrosForm.estado = "";
    filtrosForm.aula = "";
    filtrosForm.fecha_desde = "";
    filtrosForm.fecha_hasta = "";

    router.get(
        route("admin.reservas.index"),
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

const getEstadoBadge = (estado) => {
    const badges = {
        pendiente: "border-amber-200 bg-amber-50 text-amber-800",
        aprobada: "border-blue-200 bg-blue-50 text-blue-800",
        confirmada: "border-blue-200 bg-blue-50 text-blue-800",
        en_uso: "border-emerald-200 bg-emerald-50 text-emerald-800",
        completada: "border-slate-200 bg-slate-100 text-slate-700",
        cancelada: "border-red-200 bg-red-50 text-red-800",
        liberada_auto: "border-cyan-200 bg-cyan-50 text-cyan-800",
    };

    return badges[estado] || "border-slate-200 bg-slate-100 text-slate-700";
};

const getEstadoTexto = (estado) => estadosTexto[estado] || estado || "Sin estado";

const tieneCheckin = (reserva) =>
    Boolean(reserva.tiene_checkin) ||
    Boolean(reserva.checkins?.some((checkin) => checkin.tipo === "checkin"));

const tieneCheckout = (reserva) =>
    Boolean(reserva.tiene_checkout) ||
    Boolean(reserva.checkins?.some((checkin) => checkin.tipo === "checkout"));

const getControlReserva = (reserva) => {
    const checkin = tieneCheckin(reserva);
    const checkout = tieneCheckout(reserva);

    if (checkin && checkout) {
        return {
            label: "Finalizada",
            className: "border-slate-200 bg-slate-100 text-slate-700",
        };
    }

    if (checkin) {
        return {
            label: "En uso",
            className: "border-emerald-200 bg-emerald-50 text-emerald-800",
        };
    }

    return {
        label: "Sin check-in",
        className: "border-amber-200 bg-amber-50 text-amber-800",
    };
};

const aulaLabel = (aula) => {
    const modulo = aula?.modulo?.nombre ? ` - ${aula.modulo.nombre}` : "";
    const facultad = aula?.modulo?.facultad?.sigla
        ? ` (${aula.modulo.facultad.sigla})`
        : "";

    return `Aula ${aula?.codigo || "N/D"}${modulo}${facultad}`;
};

const motivoResumido = (motivo) => {
    if (!motivo) return "Sin motivo registrado";

    return motivo.length > 92 ? `${motivo.slice(0, 92)}...` : motivo;
};
</script>

<template>
    <Head title="Gestión de reservas" />

    <AuthenticatedLayout>
        <div class="min-h-screen bg-[#F5F7FB] px-4 py-6 text-[#0F172A] sm:px-6 lg:px-8">
            <div class="mx-auto flex w-full max-w-7xl flex-col gap-5">
                <section class="rounded-2xl border border-[#E2E8F0] bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div class="min-w-0">
                            <span class="inline-flex rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-blue-700">
                                Gestión institucional
                            </span>
                            <h1 class="mt-3 text-2xl font-black text-[#0F172A] sm:text-3xl">
                                Gestión de reservas
                            </h1>
                            <p class="mt-2 max-w-3xl text-sm font-medium leading-6 text-[#475569] sm:text-base">
                                Consulta y supervisa todas las reservas de aulas de la institución.
                            </p>
                        </div>

                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                            <span class="block text-xs font-bold uppercase tracking-wide text-slate-500">
                                Registros
                            </span>
                            <span class="text-lg font-black text-slate-900">
                                {{ reservas.total || 0 }}
                            </span>
                        </div>
                    </div>
                </section>

                <section class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
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
                    <form class="grid gap-4 lg:grid-cols-[minmax(0,1.4fr)_minmax(160px,.8fr)_minmax(170px,.9fr)_minmax(140px,.7fr)_minmax(140px,.7fr)_auto] lg:items-end" @submit.prevent="aplicarFiltros">
                        <div class="min-w-0">
                            <label for="buscar" class="block text-sm font-bold text-[#334155]">
                                Buscar
                            </label>
                            <input
                                id="buscar"
                                v-model="filtrosForm.buscar"
                                type="search"
                                class="mt-2 w-full rounded-xl border border-[#E2E8F0] bg-white px-4 py-2.5 text-sm font-medium text-[#0F172A] outline-none transition focus:border-[#2563EB] focus:ring-2 focus:ring-blue-100"
                                placeholder="ID, usuario, correo, aula o motivo"
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
                            <label for="aula" class="block text-sm font-bold text-[#334155]">
                                Aula
                            </label>
                            <select
                                id="aula"
                                v-model="filtrosForm.aula"
                                class="mt-2 w-full rounded-xl border border-[#E2E8F0] bg-white px-4 py-2.5 text-sm font-medium text-[#0F172A] outline-none transition focus:border-[#2563EB] focus:ring-2 focus:ring-blue-100"
                            >
                                <option value="">Todas las aulas</option>
                                <option
                                    v-for="aula in aulas"
                                    :key="aula.codigo"
                                    :value="`${aula.codigo}`"
                                >
                                    {{ aulaLabel(aula) }}
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
                    <div class="flex items-center justify-between gap-3 border-b border-[#E2E8F0] px-5 py-4">
                        <div>
                            <h2 class="text-lg font-black text-[#0F172A]">
                                Reservas registradas
                            </h2>
                            <p class="text-sm font-medium text-[#475569]">
                                {{ reservas.from || 0 }}-{{ reservas.to || 0 }} de {{ reservas.total || 0 }} registros
                            </p>
                        </div>
                    </div>

                    <div v-if="reservasListado.length > 0" class="hidden w-full overflow-x-auto lg:block">
                        <table class="w-full min-w-[1050px] table-fixed text-left">
                            <colgroup>
                                <col class="w-[17%]" />
                                <col class="w-[18%]" />
                                <col class="w-[17%]" />
                                <col class="w-[17%]" />
                                <col class="w-[10%]" />
                                <col class="w-[10%]" />
                                <col class="w-[11%]" />
                            </colgroup>
                            <thead class="bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-4 py-3">Reserva</th>
                                    <th class="px-4 py-3">Usuario</th>
                                    <th class="px-4 py-3">Aula</th>
                                    <th class="px-4 py-3">Fecha y horario</th>
                                    <th class="px-4 py-3">Estado</th>
                                    <th class="px-4 py-3">Control</th>
                                    <th class="px-4 py-3 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E2E8F0]">
                                <tr
                                    v-for="reserva in reservasListado"
                                    :key="reserva.id"
                                    class="align-top transition hover:bg-slate-50"
                                >
                                    <td class="px-4 py-5">
                                        <p class="text-sm font-black text-[#0F172A]">
                                            #{{ reserva.id }}
                                        </p>
                                        <p class="mt-1 break-words text-sm font-medium leading-5 text-[#475569]">
                                            {{ motivoResumido(reserva.proposito) }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-5">
                                        <p class="break-words text-sm font-bold text-[#0F172A]">
                                            {{ reserva.usuario?.nombre || "Usuario no disponible" }}
                                        </p>
                                        <p class="mt-1 break-words text-sm font-medium text-slate-500">
                                            {{ reserva.usuario?.email || "Sin correo" }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-5">
                                        <p class="text-sm font-black text-[#0F172A]">
                                            Aula {{ reserva.aula?.codigo || reserva.aula_codigo }}
                                        </p>
                                        <p class="mt-1 break-words text-xs font-semibold text-[#475569]">
                                            {{ reserva.aula?.modulo?.nombre || "Modulo no registrado" }}
                                        </p>
                                        <p class="mt-1 text-xs font-medium text-[#64748B]">
                                            {{ reserva.aula?.modulo?.facultad?.sigla || "Sin facultad" }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-5">
                                        <p class="text-sm font-bold text-[#0F172A]">
                                            {{ formatFecha(reserva.inicio) }}
                                        </p>
                                        <p class="mt-1 whitespace-nowrap text-xs font-semibold text-[#475569]">
                                            {{ formatHora(reserva.inicio) }} - {{ formatHora(reserva.fin) }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-5">
                                        <span
                                            class="inline-flex min-w-fit whitespace-nowrap rounded-full border px-2.5 py-1 text-xs font-black"
                                            :class="getEstadoBadge(reserva.estado)"
                                        >
                                            {{ getEstadoTexto(reserva.estado) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-5">
                                        <span
                                            class="inline-flex min-w-fit whitespace-nowrap rounded-full border px-2.5 py-1 text-xs font-black"
                                            :class="getControlReserva(reserva).className"
                                        >
                                            {{ getControlReserva(reserva).label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-5 text-center">
                                        <Link
                                            :href="route('admin.reservas.show', reserva.id)"
                                            class="inline-flex items-center justify-center whitespace-nowrap rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-blue-700 transition hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                        >
                                            Ver detalle
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="reservasListado.length > 0" class="grid gap-3 p-4 lg:hidden">
                        <article
                            v-for="reserva in reservasListado"
                            :key="`mobile-${reserva.id}`"
                            class="rounded-2xl border border-[#E2E8F0] bg-white p-4"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-black text-[#0F172A]">
                                        Reserva #{{ reserva.id }}
                                    </p>
                                    <p class="mt-1 break-words text-sm font-medium text-[#475569]">
                                        {{ motivoResumido(reserva.proposito) }}
                                    </p>
                                </div>
                                <span
                                    class="shrink-0 rounded-full border px-2.5 py-1 text-xs font-black"
                                    :class="getEstadoBadge(reserva.estado)"
                                >
                                    {{ getEstadoTexto(reserva.estado) }}
                                </span>
                            </div>

                            <dl class="mt-4 grid gap-3 text-sm">
                                <div class="rounded-xl bg-slate-50 p-3">
                                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                        Usuario
                                    </dt>
                                    <dd class="mt-1 break-words font-bold text-[#0F172A]">
                                        {{ reserva.usuario?.nombre || "Usuario no disponible" }}
                                    </dd>
                                    <dd class="break-words text-xs font-medium text-[#64748B]">
                                        {{ reserva.usuario?.email || "Sin correo" }}
                                    </dd>
                                </div>

                                <div class="grid gap-3 sm:grid-cols-2">
                                    <div class="rounded-xl bg-slate-50 p-3">
                                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                            Aula
                                        </dt>
                                        <dd class="mt-1 font-bold text-[#0F172A]">
                                            Aula {{ reserva.aula?.codigo || reserva.aula_codigo }}
                                        </dd>
                                        <dd class="text-xs font-medium text-[#64748B]">
                                            {{ reserva.aula?.modulo?.nombre || "Modulo no registrado" }}
                                        </dd>
                                    </div>

                                    <div class="rounded-xl bg-slate-50 p-3">
                                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                            Fecha y horario
                                        </dt>
                                        <dd class="mt-1 font-bold text-[#0F172A]">
                                            {{ formatFecha(reserva.inicio) }}
                                        </dd>
                                        <dd class="text-xs font-medium text-[#64748B]">
                                            {{ formatHora(reserva.inicio) }} - {{ formatHora(reserva.fin) }}
                                        </dd>
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center gap-2">
                                    <span
                                        class="inline-flex rounded-full border px-2.5 py-1 text-xs font-black"
                                        :class="getControlReserva(reserva).className"
                                    >
                                        {{ getControlReserva(reserva).label }}
                                    </span>
                                    <span class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-bold text-slate-500">
                                        Detalle administrativo
                                    </span>
                                </div>

                                <Link
                                    :href="route('admin.reservas.show', reserva.id)"
                                    class="inline-flex w-full items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                >
                                    Ver detalle
                                </Link>
                            </dl>
                        </article>
                    </div>

                    <div v-if="reservasListado.length === 0" class="flex flex-col items-center justify-center px-5 py-14 text-center">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl border border-blue-100 bg-blue-50 text-xl font-black text-[#2563EB]">
                            CF
                        </div>
                        <h3 class="mt-4 text-lg font-black text-[#0F172A]">
                            No se encontraron reservas con los filtros seleccionados.
                        </h3>
                        <p class="mt-2 max-w-md text-sm font-medium leading-6 text-[#475569]">
                            Ajusta la búsqueda o limpia los filtros para volver al listado completo.
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
                            Página {{ reservas.current_page }} de {{ reservas.last_page }}
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
