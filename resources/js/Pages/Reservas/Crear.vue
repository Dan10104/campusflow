<script setup>
import { ref, computed } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ArrowLeftIcon, ExclamationCircleIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    aula: Object,
    fecha: String,
    hora_inicio: String,
    hora_fin: String,
});

const form = useForm({
    aula_codigo: props.aula.codigo,
    inicio: props.fecha && props.hora_inicio ? `${props.fecha}T${props.hora_inicio}` : '',
    fin: props.fecha && props.hora_fin ? `${props.fecha}T${props.hora_fin}` : '',
    proposito: '',
    asistentes_estimados: props.aula.capacidad <= 30 ? props.aula.capacidad : 30,
});

const volver = () => {
    router.visit(route('aulas.disponibles'));
};

const submit = () => {
    form.post(route('reservas.store'), {
        preserveScroll: true,
        onSuccess: () => {
            // Redirige al index de reservas despues de crear
        },
    });
};

// Validacion de fechas
const fechaInicioMinima = ref(new Date().toISOString().slice(0, 16));

const validarFechas = () => {
    if (form.inicio && form.fin) {
        const inicio = new Date(form.inicio);
        const fin = new Date(form.fin);

        if (fin <= inicio) {
            return 'La hora de fin debe ser posterior a la hora de inicio';
        }

        // Validar que no sea mas de 4 horas
        const horas = (fin - inicio) / (1000 * 60 * 60);
        if (horas > 4) {
            return 'La reserva no puede exceder 4 horas';
        }
    }
    return null;
};

const validarAsistentes = computed(() => {
    if (form.asistentes_estimados > props.aula.capacidad) {
        return `El numero de asistentes no puede exceder la capacidad del aula (${props.aula.capacidad})`;
    }
    return null;
});
</script>

<template>
    <Head :title="`Reservar - Aula ${aula.codigo}`" />

    <AuthenticatedLayout>
        <div class="min-h-screen w-full bg-[#F5F7FB] text-[#0F172A]">
            <div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                        <div class="min-w-0">
                            <button
                                @click="volver"
                                class="inline-flex items-center gap-2 rounded-xl border border-blue-200 bg-white px-3 py-2 text-sm font-semibold text-blue-700 shadow-sm transition hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                            >
                                <ArrowLeftIcon class="h-4 w-4" aria-hidden="true" />
                                Volver a aulas
                            </button>

                            <p class="mt-5 text-xs font-bold uppercase tracking-widest text-blue-600">
                                Solicitud de espacio
                            </p>
                            <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                                Crear reserva
                            </h1>
                            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">
                                Revisa el aula seleccionada y completa los datos necesarios para solicitar tu reserva academica.
                            </p>
                        </div>

                        <div class="rounded-2xl border border-blue-200 bg-blue-50 px-5 py-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">
                                Aula seleccionada
                            </p>
                            <div class="mt-2 flex items-end gap-3">
                                <span class="text-3xl font-black text-slate-900">{{ aula.codigo }}</span>
                                <span class="mb-1 rounded-full border border-blue-200 bg-white px-3 py-1 text-xs font-bold uppercase text-blue-700">
                                    {{ aula.tipo.replace('_', ' ') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </section>

                <form @submit.prevent="submit" class="grid gap-6 lg:grid-cols-12">
                    <div class="space-y-6 lg:col-span-8">
                        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                            <div class="border-b border-slate-200 pb-5">
                                <p class="border-l-4 border-blue-600 pl-3 text-sm font-bold text-slate-900">
                                    Datos de la reserva
                                </p>
                                <h2 class="mt-2 text-xl font-bold text-slate-900">
                                    Horario y proposito
                                </h2>
                                <p class="mt-1 text-sm text-slate-600">
                                    La duracion maxima permitida se mantiene en 4 horas.
                                </p>
                            </div>

                            <div class="mt-6 space-y-6">
                                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                    <div>
                                        <label for="inicio" class="block text-sm font-semibold text-slate-700">
                                            Fecha y hora de inicio <span class="text-red-600">*</span>
                                        </label>
                                        <input
                                            id="inicio"
                                            v-model="form.inicio"
                                            type="datetime-local"
                                            :min="fechaInicioMinima"
                                            required
                                            class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm transition placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            :class="{ 'border-red-400 focus:border-red-500 focus:ring-red-500': form.errors.inicio }"
                                        />
                                        <p v-if="form.errors.inicio" class="mt-2 text-sm font-medium text-red-700">
                                            {{ form.errors.inicio }}
                                        </p>
                                    </div>

                                    <div>
                                        <label for="fin" class="block text-sm font-semibold text-slate-700">
                                            Fecha y hora de fin <span class="text-red-600">*</span>
                                        </label>
                                        <input
                                            id="fin"
                                            v-model="form.fin"
                                            type="datetime-local"
                                            :min="form.inicio || fechaInicioMinima"
                                            required
                                            class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm transition placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            :class="{ 'border-red-400 focus:border-red-500 focus:ring-red-500': form.errors.fin || validarFechas() }"
                                        />
                                        <p v-if="form.errors.fin" class="mt-2 text-sm font-medium text-red-700">
                                            {{ form.errors.fin }}
                                        </p>
                                        <p v-else-if="validarFechas()" class="mt-2 text-sm font-medium text-red-700">
                                            {{ validarFechas() }}
                                        </p>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex items-center justify-between gap-3">
                                        <label for="proposito" class="block text-sm font-semibold text-slate-700">
                                            Proposito de la reserva <span class="text-red-600">*</span>
                                        </label>
                                        <span class="text-xs font-semibold text-slate-500">
                                            {{ form.proposito.length }}/500
                                        </span>
                                    </div>
                                    <textarea
                                        id="proposito"
                                        v-model="form.proposito"
                                        rows="4"
                                        required
                                        maxlength="500"
                                        placeholder="Describe brevemente el proposito de tu reserva..."
                                        class="mt-2 block w-full resize-none rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm transition placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :class="{ 'border-red-400 focus:border-red-500 focus:ring-red-500': form.errors.proposito }"
                                    />
                                    <p v-if="form.errors.proposito" class="mt-2 text-sm font-medium text-red-700">
                                        {{ form.errors.proposito }}
                                    </p>
                                </div>

                                <div>
                                    <label for="asistentes_estimados" class="block text-sm font-semibold text-slate-700">
                                        Numero de asistentes (opcional)
                                    </label>
                                    <input
                                        id="asistentes_estimados"
                                        v-model="form.asistentes_estimados"
                                        type="number"
                                        min="1"
                                        :max="aula.capacidad"
                                        placeholder="Ej: 30"
                                        class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm transition placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :class="{ 'border-red-400 focus:border-red-500 focus:ring-red-500': validarAsistentes }"
                                    />
                                    <p v-if="validarAsistentes" class="mt-2 text-sm font-medium text-red-700">
                                        {{ validarAsistentes }}
                                    </p>
                                    <p v-else class="mt-2 text-sm text-slate-600">
                                        Capacidad maxima del aula: {{ aula.capacidad }} personas
                                    </p>
                                </div>
                            </div>
                        </section>

                        <section class="rounded-2xl border border-blue-200 bg-blue-50 p-5 shadow-sm sm:p-6">
                            <div class="flex gap-4">
                                <div class="flex h-11 w-11 flex-none items-center justify-center rounded-xl border border-blue-200 bg-white text-blue-600">
                                    <ExclamationCircleIcon class="h-6 w-6" aria-hidden="true" />
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-blue-900">
                                        Informacion importante
                                    </h3>
                                    <ul class="mt-3 grid gap-2 text-sm leading-6 text-slate-700 sm:grid-cols-2">
                                        <li>Debes realizar check-in al inicio de tu reserva.</li>
                                        <li>Puedes hacer check-in hasta 15 minutos antes del inicio.</li>
                                        <li>Si no realizas check-in, tu reserva será cancelada.</li>
                                        <li>Recuerda dejar el aula limpia y ordenada.</li>
                                    </ul>
                                </div>
                            </div>
                        </section>

                        <section v-if="form.errors.conflicto || form.errors.error" class="rounded-2xl border border-red-200 bg-red-50 p-5">
                            <div class="flex gap-3">
                                <ExclamationCircleIcon class="h-5 w-5 flex-none text-red-600" aria-hidden="true" />
                                <h3 class="text-sm font-semibold text-red-800">
                                    {{ form.errors.conflicto || form.errors.error }}
                                </h3>
                            </div>
                        </section>

                        <section class="flex flex-col-reverse gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:flex-row sm:justify-end">
                            <button
                                type="button"
                                @click="volver"
                                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                :disabled="form.processing || !!validarFechas() || !!validarAsistentes"
                                class="inline-flex items-center justify-center rounded-xl border border-transparent bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:border-slate-200 disabled:bg-slate-100 disabled:text-slate-500"
                            >
                                {{ form.processing ? 'Enviando solicitud...' : 'Solicitar reserva' }}
                            </button>
                        </section>
                    </div>

                    <aside class="space-y-6 lg:col-span-4">
                        <section class="sticky top-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                            <div class="border-b border-slate-200 bg-blue-50 p-6">
                                <p class="text-xs font-bold uppercase tracking-wide text-blue-700">
                                    Resumen del aula
                                </p>
                                <h2 class="mt-3 text-3xl font-black text-slate-900">{{ aula.codigo }}</h2>
                                <p class="mt-2 text-sm font-medium text-slate-600">
                                    {{ aula.modulo.nombre }} - {{ aula.modulo.facultad.sigla }}
                                </p>
                            </div>

                            <dl class="grid grid-cols-2 gap-4 p-5 sm:p-6">
                                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Capacidad</dt>
                                    <dd class="mt-1 text-xl font-black text-slate-900">{{ aula.capacidad }}</dd>
                                </div>
                                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Tipo</dt>
                                    <dd class="mt-1 text-sm font-bold capitalize text-slate-900">{{ aula.tipo.replace('_', ' ') }}</dd>
                                </div>
                                <div class="col-span-2 rounded-xl border border-slate-200 bg-slate-50 p-4">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Facultad</dt>
                                    <dd class="mt-1 text-sm font-semibold text-slate-900">
                                        {{ aula.modulo.facultad.sigla }} - {{ aula.modulo.facultad.nombre }}
                                    </dd>
                                </div>
                            </dl>

                            <div v-if="aula.caracteristicas && aula.caracteristicas.length > 0" class="border-t border-slate-200 p-5 sm:p-6">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Caracteristicas
                                </p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span
                                        v-for="caracteristica in aula.caracteristicas"
                                        :key="caracteristica.id"
                                        class="inline-flex items-center rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700"
                                    >
                                        {{ caracteristica.nombre }}
                                    </span>
                                </div>
                            </div>
                        </section>
                    </aside>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
