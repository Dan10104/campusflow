<script setup>
import { ref, onMounted } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Chart from 'chart.js/auto';
import {
    ArrowDownTrayIcon,
    CalendarIcon,
    WrenchIcon,
    ComputerDesktopIcon
} from '@heroicons/vue/24/outline';

const { kpis, charts, listas } = usePage().props;

const chartEstados = ref(null);
const chartAulas = ref(null);

const getCssVar = (name, fallback) => {
    if (typeof window === 'undefined') {
        return fallback;
    }

    return getComputedStyle(document.documentElement).getPropertyValue(name).trim() || fallback;
};

const normalizeText = (value) => String(value || '')
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '');

const statusColorMap = {
    confirmada: '#10B981',
    pendiente: '#F59E0B',
    cancelada: '#EF4444',
    en_uso: '#2563EB',
    completada: '#6B7280'
};

const getReservationColor = (estado) => statusColorMap[normalizeText(estado)] || '#06B6D4';

const formatStatusLabel = (estado) => {
    const value = String(estado || '').replace(/_/g, ' ').trim();

    return value ? value.charAt(0).toUpperCase() + value.slice(1) : 'Estado';
};

const chartEstadosLegend = charts.estados.map(e => ({
    estado: e.estado,
    total: e.total,
    label: formatStatusLabel(e.estado),
    color: getReservationColor(e.estado)
}));

const reservationStatusClass = (estado) => {
    const classes = {
        confirmada: 'border-emerald-200 bg-emerald-50 text-emerald-700',
        pendiente: 'border-amber-200 bg-amber-50 text-amber-700',
        cancelada: 'border-red-200 bg-red-50 text-red-700',
        en_uso: 'border-blue-200 bg-blue-50 text-blue-700',
        completada: 'border-slate-200 bg-slate-100 text-slate-700',
    };

    return classes[normalizeText(estado)] || 'border-cyan-200 bg-cyan-50 text-cyan-700';
};

const demandLevelClass = (nivel) => {
    const normalized = normalizeText(nivel);

    if (normalized === 'critico') {
        return 'border-red-200 bg-red-50 text-red-700';
    }

    if (normalized === 'alto' || normalized === 'alta') {
        return 'border-amber-200 bg-amber-50 text-amber-700';
    }

    return 'border-blue-200 bg-blue-50 text-blue-700';
};

onMounted(() => {
    const textColor = getCssVar('--cf-text-muted', '#64748B');
    const surfaceColor = getCssVar('--cf-surface', '#FFFFFF');
    const borderColor = getCssVar('--cf-border', '#E2E8F0');
    const accentColor = getCssVar('--cf-accent', '#2563EB');

    const estadosLabels = charts.estados.map(e => formatStatusLabel(e.estado));
    const estadosData = charts.estados.map(e => e.total);
    const backgroundColors = charts.estados.map(e => getReservationColor(e.estado));

    new Chart(chartEstados.value, {
        type: 'doughnut',
        data: {
            labels: estadosLabels,
            datasets: [{
                data: estadosData,
                backgroundColor: backgroundColors,
                borderColor: surfaceColor,
                borderWidth: 2,
                hoverOffset: 5,
                spacing: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '72%',
            layout: {
                padding: 2
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#0B1F3A',
                    titleColor: '#FFFFFF',
                    bodyColor: '#FFFFFF',
                    borderColor: accentColor,
                    borderWidth: 1,
                    padding: 12,
                    displayColors: true,
                    callbacks: {
                        label: (context) => `${context.label}: ${context.parsed}`
                    }
                }
            }
        }
    });

    new Chart(chartAulas.value, {
        type: 'bar',
        data: {
            labels: charts.aulas_top.map(a => a.nombre),
            datasets: [{
                label: 'Cantidad de Reservas',
                data: charts.aulas_top.map(a => a.total),
                backgroundColor: accentColor,
                hoverBackgroundColor: '#06B6D4',
                borderColor: accentColor,
                borderWidth: 1,
                borderRadius: 8,
                borderSkipped: false,
                maxBarThickness: 28,
                categoryPercentage: 0.72,
                barPercentage: 0.78
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0B1F3A',
                    titleColor: '#FFFFFF',
                    bodyColor: '#FFFFFF',
                    borderColor: accentColor,
                    borderWidth: 1,
                    padding: 12,
                    callbacks: {
                        label: (context) => `Reservas: ${context.parsed.x}`
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        color: textColor,
                        precision: 0,
                        font: {
                            size: 11,
                            weight: '600'
                        }
                    },
                    grid: {
                        color: borderColor,
                        drawTicks: false,
                        lineWidth: 0.5
                    },
                    border: {
                        color: borderColor
                    }
                },
                y: {
                    ticks: {
                        color: textColor,
                        autoSkip: false,
                        font: {
                            size: 11,
                            weight: '600'
                        }
                    },
                    grid: {
                        display: false
                    },
                    border: {
                        color: borderColor
                    }
                }
            }
        }
    });
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString('es-BO', {
        month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'
    });
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <section class="min-w-0 space-y-4 px-4 py-4 sm:space-y-5 sm:px-6 lg:px-6">
            <div class="mx-auto max-w-7xl space-y-4 sm:space-y-5">
                <header class="overflow-hidden rounded-2xl border border-[var(--cf-border)] bg-[var(--cf-surface)] shadow-sm">
                    <div class="flex flex-col gap-4 px-5 py-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between">
                        <div class="min-w-0">
                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-[#2563EB]">
                                Panel institucional
                            </p>
                            <h1 class="mt-1.5 text-2xl font-bold tracking-normal text-[var(--cf-heading)] sm:text-3xl">
                                Dashboard
                            </h1>
                            <p class="mt-2 max-w-2xl text-sm leading-6 text-[var(--cf-text-muted)]">
                                Resumen operativo de espacios, reservas y activos académicos.
                            </p>
                        </div>

                        <div class="grid w-full grid-cols-1 gap-2 sm:w-auto sm:grid-cols-2">
                            <a
                                :href="route('reporte.exportar', 'pdf')"
                                target="_blank"
                                class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 px-3.5 py-2 text-sm font-bold text-red-700 shadow-sm transition hover:border-red-300 hover:bg-red-100 focus:outline-none focus:ring-4 focus:ring-red-500/20 focus:ring-offset-2 focus:ring-offset-[var(--cf-surface)]"
                            >
                                <ArrowDownTrayIcon aria-hidden="true" class="h-4 w-4" />
                                Exportar PDF
                            </a>
                            <a
                                :href="route('reporte.exportar', 'excel')"
                                target="_blank"
                                class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-3.5 py-2 text-sm font-bold text-emerald-700 shadow-sm transition hover:border-emerald-300 hover:bg-emerald-100 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:ring-offset-2 focus:ring-offset-[var(--cf-surface)]"
                            >
                                <ArrowDownTrayIcon aria-hidden="true" class="h-4 w-4" />
                                Exportar Excel
                            </a>
                        </div>
                    </div>
                </header>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-3 lg:gap-4">
                    <article class="min-h-[7.5rem] rounded-2xl border border-t-4 border-[var(--cf-border)] border-t-[#2563EB] bg-[var(--cf-surface)] p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                        <div class="flex items-center gap-3">
                            <div class="rounded-xl bg-blue-50 p-2.5 text-[#2563EB]">
                                <CalendarIcon aria-hidden="true" class="h-5 w-5" />
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-[var(--cf-text-muted)]">Reservas este mes</p>
                                <p class="mt-1 text-3xl font-bold tracking-normal text-[var(--cf-heading)]">{{ kpis.reservas_mes }}</p>
                            </div>
                        </div>
                    </article>

                    <article class="min-h-[7.5rem] rounded-2xl border border-t-4 border-[var(--cf-border)] border-t-[#06B6D4] bg-[var(--cf-surface)] p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                        <div class="flex items-center gap-3">
                            <div class="rounded-xl bg-cyan-50 p-2.5 text-[#0891B2]">
                                <ComputerDesktopIcon aria-hidden="true" class="h-5 w-5" />
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-[var(--cf-text-muted)]">Activos prestados</p>
                                <p class="mt-1 text-3xl font-bold tracking-normal text-[var(--cf-heading)]">{{ kpis.activos_prestados }}</p>
                            </div>
                        </div>
                    </article>

                    <article class="min-h-[7.5rem] rounded-2xl border border-t-4 border-[var(--cf-border)] border-t-amber-500 bg-[var(--cf-surface)] p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                        <div class="flex items-center gap-3">
                            <div class="rounded-xl bg-amber-50 p-2.5 text-amber-600">
                                <WrenchIcon aria-hidden="true" class="h-5 w-5" />
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-[var(--cf-text-muted)]">Aulas en mantenimiento</p>
                                <p class="mt-1 text-3xl font-bold tracking-normal text-[var(--cf-heading)]">{{ kpis.aulas_mantenimiento }}</p>
                            </div>
                        </div>
                    </article>
                </div>

                <div class="grid grid-cols-1 gap-5 lg:grid-cols-5">
                    <article class="rounded-2xl border border-[var(--cf-border)] bg-[var(--cf-surface)] p-4 shadow-sm transition hover:shadow-md sm:p-5 lg:col-span-2">
                        <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <h2 class="text-base font-bold text-[var(--cf-heading)]">Estado de reservas</h2>
                                <p class="mt-1 text-xs leading-5 text-[var(--cf-text-muted)]">
                                    Distribución actual por estado operativo.
                                </p>
                            </div>
                            <span class="w-fit rounded-full border border-[var(--cf-border)] bg-[var(--cf-surface-muted)] px-2.5 py-1 text-xs font-bold text-[var(--cf-text-muted)]">
                                Distribución actual
                            </span>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-[minmax(0,0.95fr)_minmax(8rem,0.8fr)] sm:items-center">
                            <div class="relative mx-auto h-56 w-full max-w-[14rem] sm:h-60 sm:max-w-[15rem]">
                                <canvas ref="chartEstados"></canvas>
                            </div>
                            <ul class="space-y-2">
                                <li
                                    v-for="item in chartEstadosLegend"
                                    :key="item.estado"
                                    class="flex items-center justify-between gap-3 rounded-xl border border-[var(--cf-border)] bg-[var(--cf-surface-muted)] px-3 py-2 text-xs"
                                >
                                    <span class="inline-flex min-w-0 items-center gap-2 font-semibold text-[var(--cf-text)]">
                                        <span class="h-2.5 w-2.5 flex-none rounded-full" :style="{ backgroundColor: item.color }"></span>
                                        <span class="truncate">{{ item.label }}</span>
                                    </span>
                                    <span class="font-bold text-[var(--cf-heading)]">{{ item.total }}</span>
                                </li>
                            </ul>
                        </div>
                    </article>

                    <article class="rounded-2xl border border-[var(--cf-border)] bg-[var(--cf-surface)] p-4 shadow-sm transition hover:shadow-md sm:p-5 lg:col-span-3">
                        <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <h2 class="text-base font-bold text-[var(--cf-heading)]">Aulas más solicitadas</h2>
                                <p class="mt-1 text-xs leading-5 text-[var(--cf-text-muted)]">
                                    Espacios académicos con mayor cantidad de reservas.
                                </p>
                            </div>
                            <span class="w-fit rounded-full border border-[var(--cf-border)] bg-[var(--cf-surface-muted)] px-2.5 py-1 text-xs font-bold text-[var(--cf-text-muted)]">
                                Mayor demanda
                            </span>
                        </div>
                        <div class="relative h-64 sm:h-[18rem]">
                            <canvas ref="chartAulas"></canvas>
                        </div>
                    </article>
                </div>

                <div class="grid grid-cols-1 gap-5 xl:grid-cols-2">
                    <article class="overflow-hidden rounded-2xl border border-[var(--cf-border)] bg-[var(--cf-surface)] shadow-sm">
                        <div class="border-b border-[var(--cf-border)] px-4 py-3.5 sm:px-5">
                            <h2 class="text-base font-bold text-[var(--cf-heading)]">Próximas reservas</h2>
                            <p class="mt-1 text-xs text-[var(--cf-text-muted)]">Agenda inmediata de espacios académicos.</p>
                        </div>
                        <ul role="list" class="divide-y divide-[var(--cf-border)]">
                            <li v-for="reserva in listas.proximas_reservas" :key="reserva.id" class="px-4 py-3 sm:px-5">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="min-w-0">
                                        <p class="break-words text-sm font-bold text-[var(--cf-heading)]">
                                            {{ reserva.aula.modulo.nombre }} - Aula {{ reserva.aula_codigo }}
                                        </p>
                                        <div class="mt-1.5 flex flex-wrap items-center gap-x-3 gap-y-1.5 text-xs text-[var(--cf-text-muted)]">
                                            <span class="inline-flex items-center gap-1.5">
                                                <CalendarIcon aria-hidden="true" class="h-4 w-4" />
                                                {{ formatDate(reserva.inicio) }}
                                            </span>
                                            <span>{{ reserva.usuario?.nombre || 'Usuario' }}</span>
                                        </div>
                                    </div>
                                    <span
                                        class="inline-flex w-fit items-center rounded-full border px-2.5 py-0.5 text-xs font-bold"
                                        :class="reservationStatusClass(reserva.estado)"
                                    >
                                        {{ reserva.estado }}
                                    </span>
                                </div>
                            </li>
                            <li v-if="listas.proximas_reservas.length === 0" class="px-4 py-6 text-center text-sm text-[var(--cf-text-muted)] sm:px-5">
                                No hay reservas próximas.
                            </li>
                        </ul>
                    </article>

                    <article class="overflow-hidden rounded-2xl border border-[var(--cf-border)] bg-[var(--cf-surface)] shadow-sm">
                        <div class="border-b border-[var(--cf-border)] px-4 py-3.5 sm:px-5">
                            <h2 class="text-base font-bold text-[var(--cf-heading)]">Activos en mantenimiento</h2>
                            <p class="mt-1 text-xs text-[var(--cf-text-muted)]">Equipamiento registrado con atención pendiente.</p>
                        </div>
                        <ul role="list" class="divide-y divide-[var(--cf-border)]">
                            <li v-for="activo in listas.activos_mantenimiento" :key="activo.codigo" class="px-4 py-3 sm:px-5">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="min-w-0">
                                        <p class="break-words text-sm font-bold text-[var(--cf-heading)]">
                                            {{ activo.descripcion }}
                                        </p>
                                        <p class="mt-1.5 text-xs text-[var(--cf-text-muted)]">
                                            Tipo: {{ activo.tipo_activo?.nombre }}
                                        </p>
                                    </div>
                                    <p class="w-fit rounded-full border border-[var(--cf-border)] bg-[var(--cf-surface-muted)] px-2.5 py-0.5 font-mono text-xs font-bold text-[var(--cf-text)]">
                                        {{ activo.codigo }}
                                    </p>
                                </div>
                            </li>
                            <li v-if="listas.activos_mantenimiento.length === 0" class="px-4 py-6 text-center text-sm text-[var(--cf-text-muted)] sm:px-5">
                                Todo operativo. Ningún activo en mantenimiento.
                            </li>
                        </ul>
                    </article>
                </div>

                <section
                    v-if="listas.predicciones && listas.predicciones.length > 0"
                    class="rounded-2xl border border-[var(--cf-border)] bg-[var(--cf-surface)] p-4 shadow-sm sm:p-5"
                >
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                        <div class="max-w-3xl">
                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-[#06B6D4]">
                                Módulo demostrativo
                            </p>
                            <h2 class="mt-1.5 text-lg font-bold text-[var(--cf-heading)]">Predicción de demanda</h2>
                            <p class="mt-1 text-sm leading-6 text-[var(--cf-text-muted)]">
                                Estimación orientativa de espacios con posible alta demanda para la siguiente jornada.
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-3">
                        <article
                            v-for="(pred, index) in listas.predicciones"
                            :key="index"
                            class="rounded-2xl border border-[var(--cf-border)] bg-[var(--cf-surface-muted)] p-3.5"
                        >
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                <h3 class="break-words text-base font-bold text-[var(--cf-heading)]">{{ pred.aula }}</h3>
                                <span
                                    class="inline-flex w-fit items-center rounded-full border px-2.5 py-0.5 text-xs font-bold"
                                    :class="demandLevelClass(pred.nivel_demanda)"
                                >
                                    {{ pred.nivel_demanda }}
                                </span>
                            </div>
                            <dl class="mt-3 grid grid-cols-1 gap-2 text-xs sm:grid-cols-2">
                                <div>
                                    <dt class="font-semibold text-[var(--cf-text-muted)]">Probabilidad</dt>
                                    <dd class="mt-1 font-bold text-[var(--cf-heading)]">{{ pred.probabilidad }}%</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold text-[var(--cf-text-muted)]">Hora pico</dt>
                                    <dd class="mt-1 font-bold text-[var(--cf-heading)]">{{ pred.hora_pico }}</dd>
                                </div>
                            </dl>
                        </article>
                    </div>
                </section>
            </div>
        </section>
    </AuthenticatedLayout>
</template>
