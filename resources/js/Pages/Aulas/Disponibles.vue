<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    MagnifyingGlassIcon,
    FunnelIcon,
    BuildingOfficeIcon,
    UserGroupIcon,
    CalendarIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    aulas: Array,
    facultades: Array,
    tipos: Array,
    filtros: Object,
});

// Estado local
const busqueda = ref(props.filtros?.busqueda || '');
const facultadSeleccionada = ref(props.filtros?.facultad || '');
const tipoSeleccionado = ref(props.filtros?.tipo || '');
const capacidadMinima = ref(props.filtros?.capacidad || '');
const fecha = ref(props.filtros?.fecha || '');
const horaInicio = ref(props.filtros?.hora_inicio || '');
const horaFin = ref(props.filtros?.hora_fin || '');
const mostrarFiltros = ref(false);

// Funciones
const reservarAula = (aula) => {
    router.visit(route('reservas.crear', {
        aula: aula.codigo,
        fecha: fecha.value,
        hora_inicio: horaInicio.value,
        hora_fin: horaFin.value
    }));
};

const verCalendario = (aula) => {
    router.visit(route('aulas.calendario', aula.codigo));
};

const limpiarFiltros = () => {
    busqueda.value = '';
    facultadSeleccionada.value = '';
    tipoSeleccionado.value = '';
    capacidadMinima.value = '';
    fecha.value = '';
    horaInicio.value = '';
    horaFin.value = '';
    router.get(route('aulas.disponibles'));
};

const aplicarFiltros = () => {
    router.get(route('aulas.disponibles'), {
        busqueda: busqueda.value,
        facultad: facultadSeleccionada.value,
        tipo: tipoSeleccionado.value,
        capacidad: capacidadMinima.value,
        fecha: fecha.value,
        hora_inicio: horaInicio.value,
        hora_fin: horaFin.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

const getTipoBadge = (tipo) => {
    const badges = {
        'aula': 'border-blue-200 bg-blue-50 text-blue-700',
        'laboratorio': 'border-cyan-200 bg-cyan-50 text-cyan-700',
        'auditorio': 'border-emerald-200 bg-emerald-50 text-emerald-700',
        'sala_reuniones': 'border-amber-200 bg-amber-50 text-amber-700'
    };
    return badges[tipo] || 'border-slate-200 bg-slate-100 text-slate-700';
};

const getTipoTexto = (tipo) => {
    const textos = {
        'aula': 'Aula',
        'laboratorio': 'Laboratorio',
        'auditorio': 'Auditorio',
        'sala_reuniones': 'Sala de Reuniones'
    };
    return textos[tipo] || tipo;
};

const facultadActiva = computed(() => props.facultades.find(
    facultad => facultad.codigo === facultadSeleccionada.value
));

const activeFilterCount = computed(() => [
    facultadSeleccionada.value,
    tipoSeleccionado.value,
    capacidadMinima.value,
    fecha.value,
    horaInicio.value,
    horaFin.value
].filter(Boolean).length);

const activeFilters = computed(() => {
    const filters = [];

    if (facultadSeleccionada.value && facultadActiva.value) {
        filters.push(`Facultad: ${facultadActiva.value.sigla}`);
    }

    if (tipoSeleccionado.value) {
        filters.push(`Tipo: ${getTipoTexto(tipoSeleccionado.value)}`);
    }

    if (capacidadMinima.value) {
        filters.push(`Capacidad: ${capacidadMinima.value}+`);
    }

    if (fecha.value) {
        filters.push(`Fecha: ${fecha.value}`);
    }

    if (horaInicio.value) {
        filters.push(`Inicio: ${horaInicio.value}`);
    }

    if (horaFin.value) {
        filters.push(`Fin: ${horaFin.value}`);
    }

    return filters;
});
</script>

<template>
    <Head title="Aulas Disponibles" />

    <AuthenticatedLayout>
        <section class="min-w-0 space-y-5 px-4 py-5 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl space-y-5">
                <header class="rounded-2xl border border-[var(--cf-border)] bg-[var(--cf-surface)] px-5 py-5 shadow-sm sm:px-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div class="min-w-0">
                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-[#2563EB]">
                                Gestión de espacios
                            </p>
                            <h1 class="mt-1.5 text-2xl font-bold tracking-normal text-[var(--cf-heading)] sm:text-3xl">
                                Aulas disponibles
                            </h1>
                            <p class="mt-2 max-w-2xl text-sm leading-6 text-[var(--cf-text-muted)]">
                                Consulta, filtra y reserva espacios académicos según tus necesidades.
                            </p>
                        </div>

                        <div class="grid w-full grid-cols-1 gap-2 sm:w-auto sm:grid-cols-2">
                            <a
                                :href="route('aulas.report')"
                                target="_blank"
                                class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 px-3.5 py-2 text-sm font-bold text-red-700 shadow-sm transition hover:border-red-300 hover:bg-red-100 focus:outline-none focus:ring-4 focus:ring-red-500/20 focus:ring-offset-2 focus:ring-offset-[var(--cf-surface)]"
                            >
                                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                                Reporte
                            </a>
                            <span class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-[var(--cf-border)] bg-[var(--cf-surface-muted)] px-3.5 py-2 text-sm font-bold text-[var(--cf-heading)]">
                                <BuildingOfficeIcon aria-hidden="true" class="h-4 w-4 text-[#2563EB]" />
                                {{ aulas.length }} aulas
                            </span>
                        </div>
                    </div>
                </header>

                <section class="rounded-2xl border border-[var(--cf-border)] bg-[var(--cf-surface)] p-4 shadow-sm sm:p-5">
                    <div class="flex flex-col gap-3 lg:flex-row">
                        <div class="min-w-0 flex-1">
                            <label for="buscar-aula" class="sr-only">Buscar aulas</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                                    <MagnifyingGlassIcon aria-hidden="true" class="h-5 w-5 text-[var(--cf-text-muted)]" />
                                </div>
                                <input
                                    id="buscar-aula"
                                    v-model="busqueda"
                                    @input="aplicarFiltros"
                                    type="text"
                                    placeholder="Buscar por código, módulo o facultad..."
                                    class="block min-h-11 w-full rounded-xl border border-[var(--cf-border)] bg-[var(--cf-surface-raised)] py-2.5 pl-11 pr-3 text-sm text-[var(--cf-text)] placeholder-[var(--cf-text-muted)] shadow-sm transition focus:border-[#2563EB] focus:outline-none focus:ring-4 focus:ring-[#2563EB]/20"
                                />
                            </div>
                        </div>

                        <button
                            @click="mostrarFiltros = !mostrarFiltros"
                            type="button"
                            class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-[var(--cf-border-strong)] bg-[var(--cf-surface)] px-4 py-2.5 text-sm font-bold text-[var(--cf-heading)] shadow-sm transition hover:border-[#2563EB] hover:bg-[var(--cf-surface-muted)] focus:outline-none focus:ring-4 focus:ring-[#2563EB]/20 focus:ring-offset-2 focus:ring-offset-[var(--cf-surface)]"
                        >
                            <FunnelIcon aria-hidden="true" class="h-5 w-5 text-[#2563EB]" />
                            Filtros
                            <span
                                v-if="activeFilterCount > 0"
                                class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-[#2563EB] px-1.5 text-xs font-bold text-white"
                            >
                                {{ activeFilterCount }}
                            </span>
                        </button>
                    </div>

                    <transition
                        enter-active-class="transition ease-out duration-200"
                        enter-from-class="opacity-0 -translate-y-1"
                        enter-to-class="opacity-100 translate-y-0"
                        leave-active-class="transition ease-in duration-150"
                        leave-from-class="opacity-100 translate-y-0"
                        leave-to-class="opacity-0 -translate-y-1"
                    >
                        <div v-show="mostrarFiltros" class="mt-4 border-t border-[var(--cf-border)] pt-4">
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-6">
                                <div class="lg:col-span-2">
                                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-[var(--cf-text-muted)]">
                                        Facultad
                                    </label>
                                    <select
                                        v-model="facultadSeleccionada"
                                        @change="aplicarFiltros"
                                        class="block min-h-10 w-full rounded-xl border border-[var(--cf-border)] bg-[var(--cf-surface-raised)] px-3 py-2 text-sm text-[var(--cf-text)] shadow-sm focus:border-[#2563EB] focus:outline-none focus:ring-4 focus:ring-[#2563EB]/20"
                                    >
                                        <option value="">Todas las facultades</option>
                                        <option
                                            v-for="facultad in facultades"
                                            :key="facultad.codigo"
                                            :value="facultad.codigo"
                                        >
                                            {{ facultad.sigla }} - {{ facultad.nombre }}
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-[var(--cf-text-muted)]">
                                        Tipo
                                    </label>
                                    <select
                                        v-model="tipoSeleccionado"
                                        @change="aplicarFiltros"
                                        class="block min-h-10 w-full rounded-xl border border-[var(--cf-border)] bg-[var(--cf-surface-raised)] px-3 py-2 text-sm text-[var(--cf-text)] shadow-sm focus:border-[#2563EB] focus:outline-none focus:ring-4 focus:ring-[#2563EB]/20"
                                    >
                                        <option value="">Todos los tipos</option>
                                        <option
                                            v-for="tipo in tipos"
                                            :key="tipo"
                                            :value="tipo"
                                        >
                                            {{ getTipoTexto(tipo) }}
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-[var(--cf-text-muted)]">
                                        Capacidad
                                    </label>
                                    <input
                                        v-model="capacidadMinima"
                                        @change="aplicarFiltros"
                                        type="number"
                                        min="1"
                                        placeholder="Ej: 30"
                                        class="block min-h-10 w-full rounded-xl border border-[var(--cf-border)] bg-[var(--cf-surface-raised)] px-3 py-2 text-sm text-[var(--cf-text)] placeholder-[var(--cf-text-muted)] shadow-sm focus:border-[#2563EB] focus:outline-none focus:ring-4 focus:ring-[#2563EB]/20"
                                    />
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-[var(--cf-text-muted)]">
                                        Fecha
                                    </label>
                                    <input
                                        v-model="fecha"
                                        @change="aplicarFiltros"
                                        type="date"
                                        class="block min-h-10 w-full rounded-xl border border-[var(--cf-border)] bg-[var(--cf-surface-raised)] px-3 py-2 text-sm text-[var(--cf-text)] shadow-sm focus:border-[#2563EB] focus:outline-none focus:ring-4 focus:ring-[#2563EB]/20"
                                    />
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-[var(--cf-text-muted)]">
                                        Hora inicio
                                    </label>
                                    <input
                                        v-model="horaInicio"
                                        @change="aplicarFiltros"
                                        type="time"
                                        class="block min-h-10 w-full rounded-xl border border-[var(--cf-border)] bg-[var(--cf-surface-raised)] px-3 py-2 text-sm text-[var(--cf-text)] shadow-sm focus:border-[#2563EB] focus:outline-none focus:ring-4 focus:ring-[#2563EB]/20"
                                    />
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-[var(--cf-text-muted)]">
                                        Hora fin
                                    </label>
                                    <input
                                        v-model="horaFin"
                                        @change="aplicarFiltros"
                                        type="time"
                                        class="block min-h-10 w-full rounded-xl border border-[var(--cf-border)] bg-[var(--cf-surface-raised)] px-3 py-2 text-sm text-[var(--cf-text)] shadow-sm focus:border-[#2563EB] focus:outline-none focus:ring-4 focus:ring-[#2563EB]/20"
                                    />
                                </div>

                                <div class="flex flex-col gap-2 sm:flex-row lg:col-span-5 lg:justify-end">
                                    <button
                                        @click="aplicarFiltros"
                                        type="button"
                                        class="inline-flex min-h-10 items-center justify-center rounded-xl border border-[#2563EB] bg-[#2563EB] px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-[#1D4ED8] focus:outline-none focus:ring-4 focus:ring-[#2563EB]/25"
                                    >
                                        Aplicar filtros
                                    </button>
                                    <button
                                        @click="limpiarFiltros"
                                        type="button"
                                        class="inline-flex min-h-10 items-center justify-center rounded-xl border border-[var(--cf-border-strong)] bg-[var(--cf-surface)] px-4 py-2 text-sm font-bold text-[var(--cf-heading)] shadow-sm transition hover:border-[#2563EB] hover:bg-[var(--cf-surface-muted)] focus:outline-none focus:ring-4 focus:ring-[#2563EB]/20"
                                    >
                                        Limpiar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </transition>

                    <div v-if="activeFilters.length > 0" class="mt-4 flex flex-wrap gap-2">
                        <span
                            v-for="filter in activeFilters"
                            :key="filter"
                            class="inline-flex rounded-full border border-[var(--cf-border)] bg-[var(--cf-surface-muted)] px-3 py-1 text-xs font-bold text-[var(--cf-text-muted)]"
                        >
                            {{ filter }}
                        </span>
                    </div>
                </section>

                <section>
                    <div v-if="aulas.length === 0" class="rounded-2xl border border-dashed border-[var(--cf-border-strong)] bg-[var(--cf-surface)] px-5 py-10 text-center shadow-sm">
                        <BuildingOfficeIcon aria-hidden="true" class="mx-auto h-10 w-10 text-[var(--cf-text-muted)]" />
                        <h2 class="mt-3 text-base font-bold text-[var(--cf-heading)]">No se encontraron aulas</h2>
                        <p class="mt-1 text-sm text-[var(--cf-text-muted)]">
                            Intenta ajustar tus filtros de búsqueda.
                        </p>
                    </div>

                    <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                        <article
                            v-for="aula in aulas"
                            :key="aula.codigo"
                            class="flex min-w-0 flex-col overflow-hidden rounded-2xl border border-[var(--cf-border)] bg-[var(--cf-surface)] shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                        >
                            <div class="flex flex-1 flex-col p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold uppercase tracking-[0.14em] text-[var(--cf-text-muted)]">
                                            Código
                                        </p>
                                        <h2 class="mt-1 truncate text-xl font-bold text-[var(--cf-heading)]">
                                            Aula {{ aula.codigo }}
                                        </h2>
                                    </div>
                                    <span
                                        :class="getTipoBadge(aula.tipo)"
                                        class="inline-flex max-w-[10rem] shrink-0 items-center rounded-full border px-2.5 py-1 text-xs font-bold"
                                    >
                                        {{ getTipoTexto(aula.tipo) }}
                                    </span>
                                </div>

                                <div class="mt-4 space-y-2.5 text-sm">
                                    <div class="flex items-start gap-2 rounded-xl bg-[var(--cf-surface-muted)] px-3 py-2">
                                        <BuildingOfficeIcon aria-hidden="true" class="mt-0.5 h-4 w-4 shrink-0 text-[#2563EB]" />
                                        <div class="min-w-0">
                                            <p class="font-semibold text-[var(--cf-heading)]">{{ aula.modulo.nombre }}</p>
                                            <p class="mt-0.5 text-xs text-[var(--cf-text-muted)]">{{ aula.modulo.facultad.sigla }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 rounded-xl bg-[var(--cf-surface-muted)] px-3 py-2 text-[var(--cf-text)]">
                                        <UserGroupIcon aria-hidden="true" class="h-4 w-4 shrink-0 text-[#06B6D4]" />
                                        <span class="font-semibold">Capacidad: {{ aula.capacidad }} personas</span>
                                    </div>
                                </div>

                                <div v-if="aula.caracteristicas && aula.caracteristicas.length > 0" class="mt-4">
                                    <p class="text-xs font-bold uppercase tracking-wide text-[var(--cf-text-muted)]">Características</p>
                                    <div class="mt-2 flex flex-wrap gap-1.5">
                                        <span
                                            v-for="caracteristica in aula.caracteristicas"
                                            :key="caracteristica.id"
                                            class="inline-flex rounded-full border border-[var(--cf-border)] bg-[var(--cf-surface-muted)] px-2.5 py-1 text-xs font-semibold text-[var(--cf-text-muted)]"
                                        >
                                            {{ caracteristica.nombre }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-2 border-t border-[var(--cf-border)] bg-[var(--cf-surface-muted)] p-3 sm:grid-cols-2">
                                <button
                                    @click="verCalendario(aula)"
                                    type="button"
                                    class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-[var(--cf-border-strong)] bg-[var(--cf-surface)] px-3 py-2 text-sm font-bold text-[var(--cf-heading)] transition hover:border-[#2563EB] hover:text-[#2563EB] focus:outline-none focus:ring-4 focus:ring-[#2563EB]/20"
                                >
                                    <CalendarIcon aria-hidden="true" class="h-4 w-4" />
                                    Ver calendario
                                </button>
                                <button
                                    @click="reservarAula(aula)"
                                    type="button"
                                    class="inline-flex min-h-10 items-center justify-center rounded-xl border border-[#2563EB] bg-[#2563EB] px-3 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-[#1D4ED8] focus:outline-none focus:ring-4 focus:ring-[#2563EB]/25"
                                >
                                    Reservar
                                </button>
                            </div>
                        </article>
                    </div>
                </section>
            </div>
        </section>
    </AuthenticatedLayout>
</template>
