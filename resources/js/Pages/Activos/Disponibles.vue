<!-- resources/js/Pages/Activos/Disponibles.vue -->
<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { MagnifyingGlassIcon, QrCodeIcon, FunnelIcon, CubeIcon, PlusIcon } from '@heroicons/vue/24/outline';
import { CheckCircleIcon, ExclamationCircleIcon } from '@heroicons/vue/24/solid';
import { Link } from '@inertiajs/vue3';
const props = defineProps({
    activos: Array,
    tiposActivo: Array,
    filtros: Object,
});

// Estado local
const busqueda = ref(props.filtros?.busqueda || '');
const tipoSeleccionado = ref(props.filtros?.tipo || '');
const mostrarFiltros = ref(false);

// Activos filtrados
const activosFiltrados = computed(() => {
    let resultado = props.activos;
    
    if (busqueda.value) {
        const termino = busqueda.value.toLowerCase();
        resultado = resultado.filter(activo => 
            activo.descripcion.toLowerCase().includes(termino) ||
            activo.tipo_activo.nombre.toLowerCase().includes(termino) ||
            activo.marca?.toLowerCase().includes(termino) ||
            activo.modelo?.toLowerCase().includes(termino)
        );
    }
    
    if (tipoSeleccionado.value) {
        resultado = resultado.filter(activo => 
            activo.tipo_activo_id === parseInt(tipoSeleccionado.value)
        );
    }
    
    return resultado;
});

// Funciones
const solicitarPrestamo = (activo) => {
    router.visit(route('prestamos.crear', { activo: activo.codigo }));
};

const verDetalles = (activo) => {
    router.visit(route('activos.show', activo.codigo));
};

const limpiarFiltros = () => {
    busqueda.value = '';
    tipoSeleccionado.value = '';
    router.get(route('activos.disponibles'));
};

const aplicarFiltros = () => {
    router.get(route('activos.disponibles'), {
        busqueda: busqueda.value,
        tipo: tipoSeleccionado.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

// Badges de estado
const getEstadoBadge = (estado) => {
    const badges = {
        'disponible': 'border border-emerald-200 bg-emerald-50 text-emerald-800',
        'prestado': 'border border-blue-200 bg-blue-50 text-blue-800',
        'mantenimiento': 'border border-amber-200 bg-amber-50 text-amber-800',
        'baja': 'border border-slate-200 bg-slate-100 text-slate-700'
    };
    return badges[estado] || 'border border-slate-200 bg-slate-100 text-slate-700';
};

const getEstadoTexto = (estado) => {
    const textos = {
        'disponible': 'Disponible',
        'prestado': 'Prestado',
        'mantenimiento': 'Mantenimiento',
        'baja': 'Dado de Baja'
    };
    return textos[estado] || estado;
};
</script>

<template>
    <Head title="Activos Disponibles" />

    <AuthenticatedLayout>
        <div class="min-h-screen w-full bg-[#F5F7FB] text-[#0F172A]">
            <div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
                <section class="rounded-2xl border border-[#E2E8F0] bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
                        <div class="min-w-0">
                            <p class="text-xs font-bold uppercase tracking-widest text-[#2563EB]">
                                Gestión de recursos
                            </p>
                            <h1 class="mt-2 text-3xl font-bold tracking-tight text-[#0F172A]">
                                Activos institucionales
                            </h1>
                            <p class="mt-2 max-w-3xl text-sm leading-6 text-[#475569]">
                                Consulta, filtra y solicita equipos disponibles para actividades académicas.
                            </p>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                            <span class="inline-flex items-center justify-center rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] px-4 py-3 text-sm font-bold text-[#334155]">
                                <CubeIcon class="mr-2 h-5 w-5 text-[#2563EB]" aria-hidden="true" />
                                {{ activosFiltrados?.length || 0 }} activos
                            </span>

                            <a
                                :href="route('activos.report')"
                                target="_blank"
                                class="inline-flex items-center justify-center rounded-xl border border-[#E2E8F0] bg-white px-4 py-3 text-sm font-bold text-[#334155] shadow-sm transition hover:bg-[#F8FAFC] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-2 h-5 w-5 text-[#2563EB]" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                                Reporte
                            </a>

                            <Link
                                :href="route('activos.create')"
                                class="inline-flex items-center justify-center rounded-xl border border-transparent bg-[#2563EB] px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#1D4ED8] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2"
                            >
                                <PlusIcon class="mr-2 h-5 w-5" aria-hidden="true" />
                                Nuevo activo
                            </Link>
                        </div>
                    </div>
                </section>

                <section class="rounded-2xl border border-[#E2E8F0] bg-white p-4 shadow-sm sm:p-5">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center">
                        <div class="min-w-0 flex-1">
                            <label for="busqueda-activo" class="sr-only">Buscar activos</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <MagnifyingGlassIcon class="h-5 w-5 text-[#64748B]" aria-hidden="true" />
                                </div>
                                <input
                                    id="busqueda-activo"
                                    v-model="busqueda"
                                    @input="aplicarFiltros"
                                    type="text"
                                    placeholder="Buscar por descripción, tipo, marca o modelo..."
                                    class="block w-full rounded-xl border border-[#E2E8F0] bg-white py-3 pl-10 pr-3 text-sm font-medium text-[#0F172A] placeholder:text-[#64748B] focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]"
                                />
                            </div>
                        </div>

                        <button
                            type="button"
                            @click="mostrarFiltros = !mostrarFiltros"
                            class="inline-flex items-center justify-center rounded-xl border border-[#E2E8F0] bg-white px-4 py-3 text-sm font-bold text-[#334155] shadow-sm transition hover:bg-[#F8FAFC] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2"
                        >
                            <FunnelIcon class="mr-2 h-5 w-5 text-[#2563EB]" aria-hidden="true" />
                            Filtros
                            <span v-if="tipoSeleccionado" class="ml-2 inline-flex items-center rounded-full border border-blue-200 bg-[#EFF6FF] px-2 py-0.5 text-xs font-bold text-blue-800">
                                1
                            </span>
                        </button>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2 text-xs font-bold text-[#334155]">
                        <span v-if="busqueda" class="rounded-full border border-[#E2E8F0] bg-[#F8FAFC] px-3 py-1">
                            Búsqueda: {{ busqueda }}
                        </span>
                        <span v-if="tipoSeleccionado" class="rounded-full border border-blue-200 bg-[#EFF6FF] px-3 py-1 text-blue-800">
                            Tipo seleccionado
                        </span>
                    </div>

                    <transition
                        enter-active-class="transition ease-out duration-200"
                        enter-from-class="opacity-0 -translate-y-1"
                        enter-to-class="opacity-100 translate-y-0"
                        leave-active-class="transition ease-in duration-150"
                        leave-from-class="opacity-100 translate-y-0"
                        leave-to-class="opacity-0 -translate-y-1"
                    >
                        <div v-show="mostrarFiltros" class="mt-5 border-t border-[#E2E8F0] pt-5">
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label class="mb-2 block text-sm font-bold text-[#334155]">
                                        Tipo de activo
                                    </label>
                                    <select
                                        v-model="tipoSeleccionado"
                                        @change="aplicarFiltros"
                                        class="block w-full rounded-xl border border-[#E2E8F0] bg-white px-3 py-3 text-sm font-semibold text-[#0F172A] shadow-sm focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]"
                                    >
                                        <option value="">Todos los tipos</option>
                                        <option
                                            v-for="tipo in tiposActivo"
                                            :key="tipo.id"
                                            :value="tipo.id"
                                        >
                                            {{ tipo.nombre }}
                                        </option>
                                    </select>
                                </div>

                                <div class="flex items-end">
                                    <button
                                        @click="limpiarFiltros"
                                        type="button"
                                        class="inline-flex w-full items-center justify-center rounded-xl border border-[#E2E8F0] bg-white px-4 py-3 text-sm font-bold text-[#334155] shadow-sm transition hover:bg-[#F8FAFC] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2"
                                    >
                                        Limpiar filtros
                                    </button>
                                </div>
                            </div>
                        </div>
                    </transition>
                </section>

                <section>
                    <div v-if="activosFiltrados.length === 0" class="rounded-2xl border border-dashed border-[#CBD5E1] bg-white p-10 text-center shadow-sm">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl border border-blue-200 bg-[#EFF6FF] text-[#2563EB]">
                            <CubeIcon class="h-8 w-8" aria-hidden="true" />
                        </div>
                        <h2 class="mt-4 text-lg font-bold text-[#0F172A]">
                            No hay activos disponibles para préstamo en este momento.
                        </h2>
                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-[#475569]">
                            Intenta ajustar la búsqueda o limpiar los filtros aplicados.
                        </p>
                    </div>

                    <div v-else class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                        <article
                            v-for="activo in activosFiltrados"
                            :key="activo.codigo"
                            class="flex min-h-full flex-col overflow-hidden rounded-2xl border border-[#E2E8F0] bg-white shadow-sm transition hover:border-blue-200 hover:shadow-md"
                        >
                            <div class="flex-1 p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <h2 class="text-base font-bold leading-6 text-[#0F172A]">
                                            {{ activo.descripcion }}
                                        </h2>
                                        <p class="mt-1 text-sm font-semibold text-[#475569]">
                                            {{ activo.tipo_activo.nombre }}
                                        </p>
                                    </div>
                                    <span
                                        :class="getEstadoBadge(activo.estado)"
                                        class="inline-flex shrink-0 items-center rounded-full px-3 py-1 text-xs font-bold"
                                    >
                                        {{ getEstadoTexto(activo.estado) }}
                                    </span>
                                </div>

                                <div class="mt-4 grid gap-3">
                                    <div class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-3">
                                        <div class="flex items-center gap-2">
                                            <QrCodeIcon class="h-4 w-4 text-[#2563EB]" aria-hidden="true" />
                                            <p class="text-xs font-bold uppercase tracking-wide text-[#64748B]">
                                                Código
                                            </p>
                                        </div>
                                        <p class="mt-1 break-all font-mono text-sm font-semibold text-[#0F172A]">
                                            {{ activo.codigo }}
                                        </p>
                                    </div>

                                    <div v-if="activo.marca || activo.modelo" class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-3">
                                        <p class="text-xs font-bold uppercase tracking-wide text-[#64748B]">
                                            Marca / modelo
                                        </p>
                                        <p class="mt-1 text-sm font-semibold text-[#0F172A]">
                                            {{ activo.marca }} {{ activo.modelo }}
                                        </p>
                                    </div>

                                    <div v-if="activo.ubicacion_actual" class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-3">
                                        <p class="text-xs font-bold uppercase tracking-wide text-[#64748B]">
                                            Ubicación
                                        </p>
                                        <p class="mt-1 text-sm font-semibold text-[#0F172A]">
                                            {{ activo.ubicacion_actual }}
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-4 border-t border-[#E2E8F0] pt-4">
                                    <div v-if="activo.estado === 'disponible'" class="flex items-center text-sm font-bold text-emerald-800">
                                        <CheckCircleIcon class="mr-2 h-5 w-5 text-emerald-600" aria-hidden="true" />
                                        Disponible para préstamo
                                    </div>
                                    <div v-else class="flex items-center text-sm font-bold text-[#475569]">
                                        <ExclamationCircleIcon class="mr-2 h-5 w-5 text-[#64748B]" aria-hidden="true" />
                                        No disponible actualmente
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col gap-3 border-t border-[#E2E8F0] bg-[#F8FAFC] p-4 sm:flex-row sm:items-center sm:justify-between">
                                <button
                                    @click="verDetalles(activo)"
                                    class="inline-flex items-center justify-center rounded-xl border border-[#E2E8F0] bg-white px-4 py-2.5 text-sm font-bold text-[#2563EB] shadow-sm transition hover:bg-[#EFF6FF] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2"
                                >
                                    Ver detalles
                                </button>
                                <button
                                    @click="solicitarPrestamo(activo)"
                                    :disabled="activo.estado !== 'disponible'"
                                    :class="[
                                        'inline-flex items-center justify-center rounded-xl border border-transparent px-4 py-2.5 text-sm font-bold shadow-sm transition focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2',
                                        activo.estado === 'disponible'
                                            ? 'bg-[#2563EB] text-white hover:bg-[#1D4ED8]'
                                            : 'cursor-not-allowed bg-slate-200 text-slate-500'
                                    ]"
                                >
                                    Solicitar préstamo
                                </button>
                            </div>
                        </article>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
