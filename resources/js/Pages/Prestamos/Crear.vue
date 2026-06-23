<script setup>
import { ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ArrowLeftIcon, ExclamationCircleIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    activo: Object,
});

const form = useForm({
    activo_codigo: props.activo.codigo,
    inicio_previsto: '',
    fin_previsto: '',
});

const volver = () => {
    router.visit(route('activos.disponibles'));
};

const submit = () => {
    form.post(route('prestamos.store'), {
        preserveScroll: true,
        onSuccess: () => {
            // Redirige al index de préstamos después de crear
        },
    });
};

// Validación de fechas
const fechaInicioMinima = ref(new Date().toISOString().slice(0, 16));

const validarFechas = () => {
    if (form.inicio_previsto && form.fin_previsto) {
        const inicio = new Date(form.inicio_previsto);
        const fin = new Date(form.fin_previsto);
        
        if (fin <= inicio) {
            return 'La fecha de devolución debe ser posterior a la fecha de retiro';
        }
    }
    return null;
};
</script>

<template>
    <Head :title="`Solicitar Préstamo - ${activo.descripcion}`" />

    <AuthenticatedLayout>
        <div class="min-h-screen w-full bg-[#F5F7FB] text-[#0F172A]">
            <div class="mx-auto max-w-5xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
                <section class="rounded-2xl border border-[#E2E8F0] bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                        <div class="min-w-0">
                            <button
                                type="button"
                                @click="volver"
                                class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-[#E2E8F0] bg-white px-3 py-2 text-sm font-bold text-[#334155] shadow-sm transition hover:bg-[#F8FAFC] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2"
                            >
                                <ArrowLeftIcon class="h-5 w-5 shrink-0 text-[#2563EB]" aria-hidden="true" />
                                Volver a activos
                            </button>

                            <p class="mt-5 text-xs font-bold uppercase tracking-widest text-[#2563EB]">
                                Solicitud de préstamo
                            </p>
                            <h1 class="mt-2 text-3xl font-bold tracking-tight text-[#0F172A]">
                                Solicitar préstamo
                            </h1>
                            <p class="mt-2 max-w-3xl text-sm leading-6 text-[#475569]">
                                Completa la información para solicitar el préstamo del activo seleccionado.
                            </p>
                        </div>

                        <div v-if="form.processing" class="rounded-xl border border-blue-200 bg-[#EFF6FF] px-4 py-3 text-sm font-bold text-blue-800">
                            Enviando solicitud...
                        </div>
                    </div>
                </section>

                <form @submit.prevent="submit" class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <main class="space-y-6 lg:col-span-2">
                        <section class="overflow-hidden rounded-2xl border border-[#E2E8F0] bg-white shadow-sm">
                            <div class="border-b border-[#E2E8F0] px-5 py-5 sm:px-6">
                                <h2 class="text-lg font-bold text-[#0F172A]">
                                    Activo seleccionado
                                </h2>
                                <p class="mt-1 text-sm text-[#475569]">
                                    Verifica los datos antes de enviar la solicitud.
                                </p>
                            </div>

                            <dl class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-2 sm:p-6">
                                <div class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                                    <dt class="text-xs font-bold uppercase tracking-wide text-[#64748B]">Descripción</dt>
                                    <dd class="mt-1 break-words text-sm font-semibold text-[#0F172A]">{{ activo.descripcion }}</dd>
                                </div>
                                <div class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                                    <dt class="text-xs font-bold uppercase tracking-wide text-[#64748B]">Tipo</dt>
                                    <dd class="mt-1 break-words text-sm font-semibold text-[#0F172A]">{{ activo.tipo_activo.nombre }}</dd>
                                </div>
                                <div v-if="activo.marca" class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                                    <dt class="text-xs font-bold uppercase tracking-wide text-[#64748B]">Marca</dt>
                                    <dd class="mt-1 break-words text-sm font-semibold text-[#0F172A]">{{ activo.marca }}</dd>
                                </div>
                                <div v-if="activo.modelo" class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                                    <dt class="text-xs font-bold uppercase tracking-wide text-[#64748B]">Modelo</dt>
                                    <dd class="mt-1 break-words text-sm font-semibold text-[#0F172A]">{{ activo.modelo }}</dd>
                                </div>
                                <div class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4 text-center">
                                    <dt class="text-xs font-bold uppercase tracking-wide text-[#64748B]">Código</dt>
                                    <dd class="mt-2 flex min-h-10 items-center justify-center break-all font-mono text-sm font-bold leading-tight text-[#0F172A]">{{ activo.codigo }}</dd>
                                </div>
                                <div class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                                    <dt class="text-xs font-bold uppercase tracking-wide text-[#64748B]">Estado</dt>
                                    <dd class="mt-2">
                                        <span class="inline-flex items-center justify-center rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-center text-xs font-semibold leading-none text-emerald-800">
                                            {{ activo.estado }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </section>

                        <section class="overflow-hidden rounded-2xl border border-[#E2E8F0] bg-white shadow-sm">
                            <div class="border-b border-[#E2E8F0] px-5 py-5 sm:px-6">
                                <h2 class="text-lg font-bold text-[#0F172A]">
                                    Período del préstamo
                                </h2>
                                <p class="mt-1 text-sm text-[#475569]">
                                    Define la fecha de retiro y la fecha prevista de devolución.
                                </p>
                            </div>

                            <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 sm:p-6">
                                <div>
                                    <label for="inicio_previsto" class="block text-sm font-bold text-[#334155]">
                                        Fecha y hora de retiro <span class="text-red-600">*</span>
                                    </label>
                                    <input
                                        id="inicio_previsto"
                                        v-model="form.inicio_previsto"
                                        type="datetime-local"
                                        :min="fechaInicioMinima"
                                        required
                                        class="mt-2 block min-h-11 w-full rounded-xl border border-[#E2E8F0] bg-white px-4 py-2.5 text-sm font-medium text-[#0F172A] shadow-sm focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]"
                                        :class="{ 'border-red-300': form.errors.inicio_previsto }"
                                    />
                                    <p v-if="form.errors.inicio_previsto" class="mt-2 text-sm font-semibold text-red-600">
                                        {{ form.errors.inicio_previsto }}
                                    </p>
                                </div>

                                <div>
                                    <label for="fin_previsto" class="block text-sm font-bold text-[#334155]">
                                        Fecha y hora de devolución <span class="text-red-600">*</span>
                                    </label>
                                    <input
                                        id="fin_previsto"
                                        v-model="form.fin_previsto"
                                        type="datetime-local"
                                        :min="form.inicio_previsto || fechaInicioMinima"
                                        required
                                        class="mt-2 block min-h-11 w-full rounded-xl border border-[#E2E8F0] bg-white px-4 py-2.5 text-sm font-medium text-[#0F172A] shadow-sm focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]"
                                        :class="{ 'border-red-300': form.errors.fin_previsto }"
                                    />
                                    <p v-if="form.errors.fin_previsto" class="mt-2 text-sm font-semibold text-red-600">
                                        {{ form.errors.fin_previsto }}
                                    </p>
                                    <p v-else-if="validarFechas()" class="mt-2 text-sm font-semibold text-red-600">
                                        {{ validarFechas() }}
                                    </p>
                                </div>
                            </div>
                        </section>
                    </main>

                    <aside class="space-y-6">
                        <section class="rounded-2xl border border-blue-200 bg-[#EFF6FF] p-5">
                            <div class="flex gap-3">
                                <ExclamationCircleIcon class="mt-0.5 h-5 w-5 shrink-0 text-[#2563EB]" aria-hidden="true" />
                                <div class="min-w-0">
                                    <h2 class="text-sm font-bold text-blue-900">
                                        Importante
                                    </h2>
                                    <ul class="mt-2 space-y-2 text-sm leading-6 text-[#475569]">
                                        <li>Asegúrate de devolver el activo en la fecha indicada</li>
                                        <li>Cualquier daño será registrado y podrá afectar futuros préstamos</li>
                                        <li>El personal verificará el estado del activo al momento de la entrega y devolución</li>
                                    </ul>
                                </div>
                            </div>
                        </section>

                        <section v-if="form.errors.error" class="rounded-2xl border border-red-200 bg-red-50 p-5">
                            <div class="flex gap-3">
                                <ExclamationCircleIcon class="mt-0.5 h-5 w-5 shrink-0 text-red-600" aria-hidden="true" />
                                <div class="min-w-0">
                                    <h2 class="text-sm font-bold text-red-900">
                                        {{ form.errors.error }}
                                    </h2>
                                </div>
                            </div>
                        </section>

                        <div class="flex flex-col gap-3 sm:flex-row lg:flex-col">
                            <button
                                type="button"
                                @click="volver"
                                class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#E2E8F0] bg-white px-4 py-2.5 text-sm font-bold text-[#334155] shadow-sm transition hover:bg-[#F8FAFC] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                :disabled="form.processing || !!validarFechas()"
                                class="inline-flex min-h-11 items-center justify-center rounded-xl border border-transparent bg-[#2563EB] px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#1D4ED8] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2 disabled:cursor-not-allowed disabled:bg-slate-300 disabled:text-slate-600"
                            >
                                {{ form.processing ? 'Enviando...' : 'Solicitar Préstamo' }}
                            </button>
                        </div>
                    </aside>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
