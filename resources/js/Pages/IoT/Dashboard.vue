<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import {
    SignalIcon,
    SignalSlashIcon,
    UserIcon,
    FireIcon
} from '@heroicons/vue/24/solid';

const props = defineProps({
    sensores: Array
});

let intervalId = null;

const refreshData = () => {
    router.reload({ only: ['sensores'], preserveScroll: true });
};

onMounted(() => {
    // Refrescar cada 10 segundos para ver "movimiento"
    intervalId = setInterval(refreshData, 10000);
});

onUnmounted(() => {
    if (intervalId) clearInterval(intervalId);
});
</script>

<template>
    <Head title="IoT Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0">
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Monitoreo IoT
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Supervisa el estado, ocupación y condiciones ambientales de las aulas conectadas.
                    </p>
                </div>

                <div class="flex flex-col gap-3 sm:items-end">
                    <div class="text-sm text-gray-500">
                        <span class="font-semibold text-gray-800">{{ sensores.length }}</span>
                        aulas monitoreadas
                    </div>
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                        <span class="text-xs text-gray-400">
                            Actualización automática cada 10 segundos
                        </span>
                        <button
                            type="button"
                            @click="refreshData"
                            class="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        >
                            Actualizar ahora
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <div class="bg-gray-50 py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div v-if="sensores.length === 0" class="rounded-lg bg-white p-8 text-center shadow-sm">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-50 text-gray-400">
                        <SignalSlashIcon class="h-6 w-6" />
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">
                        No hay aulas monitoreadas
                    </h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Cuando existan sensores asociados a aulas, sus lecturas aparecerán en este panel.
                    </p>
                    <button
                        type="button"
                        @click="refreshData"
                        class="mt-5 inline-flex items-center justify-center rounded-md border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700 transition hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        Actualizar ahora
                    </button>
                </div>

                <div v-else class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="aula in sensores"
                        :key="aula.codigo"
                        class="bg-white p-5 shadow-sm transition hover:shadow-md sm:rounded-lg"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Aula monitoreada
                                </p>

                                <h2 class="mt-2 break-words text-lg font-bold leading-snug text-slate-900">
                                    {{ aula.nombre }}
                                </h2>

                                <p class="mt-1 break-words text-sm text-slate-600">
                                    Código: {{ aula.codigo }}
                                </p>
                            </div>

                            <span
                                v-if="aula.sensor_status === 'online'"
                                class="inline-flex shrink-0 items-center gap-2 rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700 ring-1 ring-inset ring-green-200"
                            >
                                <span class="relative flex h-2.5 w-2.5">
                                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-green-400 opacity-75"></span>
                                    <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-green-500"></span>
                                </span>
                                online
                            </span>
                            <span
                                v-else
                                class="inline-flex shrink-0 items-center gap-2 rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700 ring-1 ring-inset ring-red-200"
                            >
                                <span class="h-2.5 w-2.5 rounded-full bg-red-500"></span>
                                {{ aula.sensor_status || 'offline' }}
                            </span>
                        </div>

                        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div
                                class="flex min-h-[124px] min-w-0 flex-col items-center justify-center rounded-2xl border p-4 text-center"
                                :class="aula.movimiento ? 'border-blue-100 bg-blue-50' : 'border-slate-200 bg-slate-50'"
                            >
                                <p class="mb-3 w-full text-center text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Ocupación
                                </p>

                                <div class="flex w-full min-w-0 flex-col items-center justify-center gap-2">
                                    <span
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white shadow-sm"
                                        :class="aula.movimiento ? 'text-blue-600' : 'text-gray-400'"
                                    >
                                        <UserIcon class="h-5 w-5" />
                                    </span>
                                    <span class="block min-w-[5.75rem] max-w-full whitespace-nowrap px-3 text-center text-base font-bold leading-snug text-slate-900">
                                        {{ aula.movimiento ? 'Detectada' : 'Vacía' }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex min-h-[124px] min-w-0 flex-col items-center justify-center rounded-2xl border border-orange-100 bg-orange-50 p-4 text-center">
                                <p class="mb-3 w-full text-center text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Temperatura
                                </p>

                                <div class="flex w-full min-w-0 flex-col items-center justify-center gap-2">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-orange-500 shadow-sm">
                                        <FireIcon class="h-5 w-5" />
                                    </span>
                                    <span class="block w-full min-w-0 break-words px-2 text-center text-base font-bold leading-snug text-slate-900">
                                        <span v-if="aula.temperatura !== null && aula.temperatura !== undefined">
                                            {{ aula.temperatura }}°C
                                        </span>
                                        <span v-else>Sin dato</span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="aula.humedad !== null && aula.humedad !== undefined"
                            class="mt-4 flex min-h-[116px] min-w-0 flex-col items-center justify-center rounded-2xl border border-cyan-100 bg-cyan-50 p-4 text-center"
                        >
                            <p class="mb-3 w-full text-center text-xs font-bold uppercase tracking-wide text-slate-500">
                                Humedad
                            </p>

                            <div class="flex w-full min-w-0 flex-col items-center justify-center gap-2">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-cyan-600 shadow-sm">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path
                                            d="M12 3.75C9.65 6.8 7.5 9.85 7.5 13.1A4.5 4.5 0 0 0 12 17.6a4.5 4.5 0 0 0 4.5-4.5c0-3.25-2.15-6.3-4.5-9.35Z"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            stroke-linejoin="round"
                                        />
                                        <path
                                            d="M10.35 13.25A1.95 1.95 0 0 0 12.3 15.2"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            stroke-linecap="round"
                                        />
                                    </svg>
                                </span>
                                <span class="block w-full min-w-0 break-words px-2 text-center text-base font-bold leading-snug text-slate-900">
                                    {{ aula.humedad }}%
                                </span>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-col gap-2 border-t border-gray-100 pt-3 text-xs text-gray-500 sm:flex-row sm:items-center sm:justify-between">
                            <span class="inline-flex min-w-0 items-center gap-1.5">
                                <SignalIcon
                                    v-if="aula.sensor_status === 'online'"
                                    class="h-4 w-4 shrink-0 text-green-500"
                                />
                                <SignalSlashIcon
                                    v-else
                                    class="h-4 w-4 shrink-0 text-red-500"
                                />
                                <span class="break-words">
                                    {{ aula.sensor_status || 'sin estado' }}
                                </span>
                            </span>
                            <span class="break-words">
                                Actualizado: {{ aula.ultima_actualizacion }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
