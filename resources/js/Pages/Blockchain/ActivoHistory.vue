<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ClockIcon, CubeIcon, ShieldCheckIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    activo: Object,
    eventos: Array,
    total_eventos: Number,
    error: String,
});
</script>

<template>
    <Head title="Historial Blockchain - Activo" />

    <AuthenticatedLayout>
        <div class="min-h-screen w-full bg-[#F5F7FB] text-[#0F172A]">
            <div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
                <section class="rounded-2xl border border-[#E2E8F0] bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-3">
                                <p class="text-xs font-bold uppercase tracking-widest text-[#2563EB]">
                                    Trazabilidad del activo
                                </p>
                                <span
                                    v-if="activo?.estado"
                                    :class="[
                                        activo.estado === 'disponible' ? 'border border-emerald-200 bg-emerald-100 text-emerald-800' :
                                        activo.estado === 'prestado' ? 'border border-blue-200 bg-blue-100 text-blue-800' :
                                        activo.estado === 'mantenimiento' ? 'border border-amber-200 bg-amber-100 text-amber-800' :
                                        activo.estado === 'baja' || activo.estado === 'inactivo' ? 'border border-red-200 bg-red-100 text-red-800' :
                                        'border border-slate-200 bg-slate-100 text-slate-700'
                                    ]"
                                    class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold"
                                >
                                    {{ activo.estado }}
                                </span>
                            </div>

                            <h1 class="mt-2 break-words text-3xl font-bold tracking-tight text-[#0F172A]">
                                Historial Blockchain del activo
                            </h1>
                            <p class="mt-2 max-w-3xl break-words text-sm leading-6 text-[#475569]">
                                {{ activo?.descripcion || 'Sin descripción' }}
                            </p>
                            <p v-if="activo?.codigo" class="mt-2 break-all font-mono text-sm font-bold text-[#334155]">
                                {{ activo.codigo }}
                            </p>
                        </div>

                        <div class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] px-4 py-3">
                            <p class="text-xs font-bold uppercase tracking-wide text-[#64748B]">
                                Total de eventos
                            </p>
                            <p class="mt-1 text-2xl font-bold text-[#0F172A]">
                                {{ total_eventos ?? 0 }}
                            </p>
                        </div>
                    </div>
                </section>

                <div v-if="error" role="alert" class="rounded-2xl border border-red-200 bg-red-50 p-4 shadow-sm">
                    <div class="flex items-start gap-3">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-sm font-semibold text-red-900">
                            {{ error }}
                        </p>
                    </div>
                </div>

                <section class="overflow-hidden rounded-2xl border border-[#E2E8F0] bg-white shadow-sm">
                    <div class="border-b border-[#E2E8F0] px-5 py-5 sm:px-6">
                        <h2 class="flex items-center gap-2 text-lg font-bold text-[#0F172A]">
                            <CubeIcon class="h-5 w-5 shrink-0 text-[#2563EB]" aria-hidden="true" />
                            Información del activo
                        </h2>
                        <p class="mt-1 text-sm text-[#475569]">
                            Datos disponibles para identificar el activo registrado.
                        </p>
                    </div>

                    <dl class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-2 lg:grid-cols-3 sm:p-6">
                        <div v-if="activo?.descripcion" class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4 sm:col-span-2 lg:col-span-3">
                            <dt class="text-xs font-bold uppercase tracking-wide text-[#64748B]">Descripción</dt>
                            <dd class="mt-1 break-words text-sm font-semibold text-[#0F172A]">{{ activo.descripcion }}</dd>
                        </div>
                        <div v-if="activo?.codigo" class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                            <dt class="text-xs font-bold uppercase tracking-wide text-[#64748B]">Código</dt>
                            <dd class="mt-1 break-all font-mono text-sm font-semibold text-[#0F172A]">{{ activo.codigo }}</dd>
                        </div>
                        <div v-if="activo?.tipo" class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                            <dt class="text-xs font-bold uppercase tracking-wide text-[#64748B]">Tipo</dt>
                            <dd class="mt-1 break-words text-sm font-semibold text-[#0F172A]">{{ activo.tipo }}</dd>
                        </div>
                        <div v-if="activo?.estado" class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                            <dt class="text-xs font-bold uppercase tracking-wide text-[#64748B]">Estado</dt>
                            <dd class="mt-2">
                                <span
                                    :class="[
                                        activo.estado === 'disponible' ? 'border border-emerald-200 bg-emerald-100 text-emerald-800' :
                                        activo.estado === 'prestado' ? 'border border-blue-200 bg-blue-100 text-blue-800' :
                                        activo.estado === 'mantenimiento' ? 'border border-amber-200 bg-amber-100 text-amber-800' :
                                        activo.estado === 'baja' || activo.estado === 'inactivo' ? 'border border-red-200 bg-red-100 text-red-800' :
                                        'border border-slate-200 bg-slate-100 text-slate-700'
                                    ]"
                                    class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold"
                                >
                                    {{ activo.estado }}
                                </span>
                            </dd>
                        </div>
                        <div v-if="activo?.marca" class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                            <dt class="text-xs font-bold uppercase tracking-wide text-[#64748B]">Marca</dt>
                            <dd class="mt-1 break-words text-sm font-semibold text-[#0F172A]">{{ activo.marca }}</dd>
                        </div>
                        <div v-if="activo?.modelo" class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                            <dt class="text-xs font-bold uppercase tracking-wide text-[#64748B]">Modelo</dt>
                            <dd class="mt-1 break-words text-sm font-semibold text-[#0F172A]">{{ activo.modelo }}</dd>
                        </div>
                        <div v-if="activo?.ubicacion_actual" class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                            <dt class="text-xs font-bold uppercase tracking-wide text-[#64748B]">Ubicación actual</dt>
                            <dd class="mt-1 break-words text-sm font-semibold text-[#0F172A]">{{ activo.ubicacion_actual }}</dd>
                        </div>
                    </dl>
                </section>

                <section class="overflow-hidden rounded-2xl border border-[#E2E8F0] bg-white shadow-sm">
                    <div class="border-b border-[#E2E8F0] px-5 py-5 sm:px-6">
                        <h2 class="flex items-center gap-2 text-lg font-bold text-[#0F172A]">
                            <ShieldCheckIcon class="h-5 w-5 shrink-0 text-[#2563EB]" aria-hidden="true" />
                            Eventos del activo
                        </h2>
                        <p class="mt-1 text-sm text-[#475569]">
                            {{ total_eventos ?? 0 }} eventos registrados.
                        </p>
                    </div>

                    <div class="p-5 sm:p-6">
                        <div v-if="(!eventos || eventos.length === 0) && !error" class="rounded-2xl border border-[#E2E8F0] bg-[#F8FAFC] p-8 text-center">
                            <ClockIcon class="mx-auto h-10 w-10 text-[#64748B]" />
                            <h3 class="mt-3 text-base font-bold text-[#0F172A]">
                                No hay eventos registrados
                            </h3>
                            <p class="mt-1 text-sm text-[#475569]">
                                Cuando existan eventos asociados al activo, aparecerán en este historial.
                            </p>
                        </div>

                        <div v-else-if="eventos && eventos.length > 0" class="space-y-4">
                            <article
                                v-for="(evento, index) in eventos"
                                :key="evento.tx_id || evento.hash || index"
                                class="rounded-2xl border border-[#E2E8F0] bg-[#F8FAFC] p-4"
                            >
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="min-w-0">
                                        <p v-if="evento.tipo" class="break-words text-base font-bold text-[#0F172A]">
                                            {{ evento.tipo }}
                                        </p>
                                        <p v-if="evento.descripcion" class="mt-1 break-words text-sm font-semibold text-[#475569]">
                                            {{ evento.descripcion }}
                                        </p>
                                        <p v-if="evento.timestamp || evento.fecha" class="mt-2 text-sm font-semibold text-[#475569]">
                                            {{ evento.timestamp || evento.fecha }}
                                        </p>
                                    </div>
                                    <span
                                        v-if="evento.bloque"
                                        class="inline-flex shrink-0 items-center rounded-full border border-[#E2E8F0] bg-white px-3 py-1 text-xs font-bold text-[#334155]"
                                    >
                                        Bloque #{{ evento.bloque }}
                                    </span>
                                </div>

                                <div class="mt-4 grid grid-cols-1 gap-3">
                                    <div v-if="evento.tx_id || evento.transaccion" class="rounded-xl border border-[#E2E8F0] bg-white p-3">
                                        <p class="text-xs font-bold uppercase tracking-wide text-[#64748B]">Transacción</p>
                                        <code class="mt-2 block break-all rounded-lg border border-slate-200 bg-slate-50 p-2 font-mono text-xs text-slate-700">
                                            {{ evento.tx_id || evento.transaccion }}
                                        </code>
                                    </div>

                                    <div v-if="evento.hash" class="rounded-xl border border-[#E2E8F0] bg-white p-3">
                                        <p class="text-xs font-bold uppercase tracking-wide text-[#64748B]">Hash</p>
                                        <code class="mt-2 block break-all rounded-lg border border-slate-200 bg-slate-50 p-2 font-mono text-xs text-slate-700">
                                            {{ evento.hash }}
                                        </code>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
