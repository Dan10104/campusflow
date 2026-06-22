<script setup>
import { useForm } from '@inertiajs/vue3';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    ArrowLeftIcon,
    CheckIcon,
    TagIcon,
    MapPinIcon,
    QrCodeIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    tiposActivo: Array,
});

const form = useForm({
    descripcion: '',
    tipo_activo_id: '',
    marca: '',
    modelo: '',
    ubicacion_actual: '',
    qr_code: '',
    estado: 'disponible',
});

const submit = () => {
    form.post(route('activos.store'));
};
</script>

<template>
    <Head title="Nuevo Activo" />

    <AuthenticatedLayout>
        <div class="min-h-screen w-full bg-[#F5F7FB] text-[#0F172A]">
            <div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
                <section class="rounded-2xl border border-[#E2E8F0] bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                        <div class="min-w-0">
                            <Link
                                :href="route('activos.disponibles')"
                                class="inline-flex items-center gap-2 rounded-xl border border-[#E2E8F0] bg-white px-3 py-2 text-sm font-bold text-[#334155] shadow-sm transition hover:bg-[#F8FAFC] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2"
                            >
                                <ArrowLeftIcon class="h-5 w-5 text-[#2563EB]" aria-hidden="true" />
                                Volver a activos
                            </Link>

                            <p class="mt-5 text-xs font-bold uppercase tracking-widest text-[#2563EB]">
                                Nuevo recurso
                            </p>
                            <h1 class="mt-2 text-3xl font-bold tracking-tight text-[#0F172A]">
                                Registrar activo
                            </h1>
                            <p class="mt-2 max-w-3xl text-sm leading-6 text-[#475569]">
                                Complete la información para dar de alta un equipo en el inventario institucional.
                            </p>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                            <Link
                                :href="route('activos.disponibles')"
                                class="inline-flex items-center justify-center rounded-xl border border-[#E2E8F0] bg-white px-4 py-3 text-sm font-bold text-[#334155] shadow-sm transition hover:bg-[#F8FAFC] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2"
                            >
                                Cancelar
                            </Link>
                            <button
                                type="button"
                                @click="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center justify-center rounded-xl border border-transparent bg-[#2563EB] px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#1D4ED8] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2 disabled:cursor-not-allowed disabled:bg-slate-300 disabled:text-slate-600"
                            >
                                <CheckIcon class="mr-2 h-5 w-5" aria-hidden="true" />
                                {{ form.processing ? 'Guardando...' : 'Guardar activo' }}
                            </button>
                        </div>
                    </div>
                </section>

                <form @submit.prevent="submit" class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <div class="space-y-6 lg:col-span-2">
                        <section class="overflow-hidden rounded-2xl border border-[#E2E8F0] bg-white shadow-sm">
                            <div class="border-b border-[#E2E8F0] px-5 py-5 sm:px-6">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl border border-blue-200 bg-[#EFF6FF] text-[#2563EB]">
                                        <TagIcon class="h-5 w-5" aria-hidden="true" />
                                    </div>
                                    <div>
                                        <h2 class="text-lg font-bold text-[#0F172A]">
                                            Detalles del equipo
                                        </h2>
                                        <p class="text-sm text-[#475569]">
                                            Datos descriptivos del activo.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-6 p-5 sm:p-6">
                                <div>
                                    <label for="descripcion" class="block text-sm font-bold text-[#334155]">
                                        Descripción / Nombre *
                                    </label>
                                    <textarea
                                        id="descripcion"
                                        v-model="form.descripcion"
                                        rows="3"
                                        class="mt-2 block w-full rounded-xl border border-[#E2E8F0] bg-white px-4 py-3 text-sm font-medium text-[#0F172A] placeholder:text-[#64748B] shadow-sm focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]"
                                        placeholder="Ej: Proyector Epson X41 - Laboratorio 1"
                                    />
                                    <p v-if="form.errors.descripcion" class="mt-2 text-sm font-semibold text-red-600">{{ form.errors.descripcion }}</p>
                                </div>

                                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                    <div>
                                        <label for="tipo_activo" class="block text-sm font-bold text-[#334155]">
                                            Tipo de activo *
                                        </label>
                                        <select
                                            id="tipo_activo"
                                            v-model="form.tipo_activo_id"
                                            class="mt-2 block w-full rounded-xl border border-[#E2E8F0] bg-white px-4 py-3 text-sm font-semibold text-[#0F172A] shadow-sm focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]"
                                        >
                                            <option value="" disabled>Seleccione un tipo</option>
                                            <option v-for="tipo in tiposActivo" :key="tipo.id" :value="tipo.id">
                                                {{ tipo.nombre }}
                                            </option>
                                        </select>
                                        <p v-if="form.errors.tipo_activo_id" class="mt-2 text-sm font-semibold text-red-600">{{ form.errors.tipo_activo_id }}</p>
                                    </div>

                                    <div>
                                        <label for="marca" class="block text-sm font-bold text-[#334155]">
                                            Marca
                                        </label>
                                        <input
                                            type="text"
                                            id="marca"
                                            v-model="form.marca"
                                            class="mt-2 block w-full rounded-xl border border-[#E2E8F0] bg-white px-4 py-3 text-sm font-medium text-[#0F172A] shadow-sm focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]"
                                        />
                                        <p v-if="form.errors.marca" class="mt-2 text-sm font-semibold text-red-600">{{ form.errors.marca }}</p>
                                    </div>

                                    <div>
                                        <label for="modelo" class="block text-sm font-bold text-[#334155]">
                                            Modelo
                                        </label>
                                        <input
                                            type="text"
                                            id="modelo"
                                            v-model="form.modelo"
                                            class="mt-2 block w-full rounded-xl border border-[#E2E8F0] bg-white px-4 py-3 text-sm font-medium text-[#0F172A] shadow-sm focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]"
                                        />
                                        <p v-if="form.errors.modelo" class="mt-2 text-sm font-semibold text-red-600">{{ form.errors.modelo }}</p>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>

                    <aside class="space-y-6">
                        <section class="overflow-hidden rounded-2xl border border-[#E2E8F0] bg-white shadow-sm">
                            <div class="border-b border-[#E2E8F0] px-5 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl border border-blue-200 bg-[#EFF6FF] text-[#2563EB]">
                                        <MapPinIcon class="h-5 w-5" aria-hidden="true" />
                                    </div>
                                    <div>
                                        <h2 class="text-lg font-bold text-[#0F172A]">
                                            Estado y ubicación
                                        </h2>
                                        <p class="text-sm text-[#475569]">
                                            Condición inicial del recurso.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-5 p-5">
                                <div>
                                    <label for="estado" class="block text-sm font-bold text-[#334155]">
                                        Estado inicial *
                                    </label>
                                    <select
                                        id="estado"
                                        v-model="form.estado"
                                        class="mt-2 block w-full rounded-xl border border-[#E2E8F0] bg-white px-4 py-3 text-sm font-semibold text-[#0F172A] shadow-sm focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]"
                                    >
                                        <option value="disponible">Disponible</option>
                                        <option value="en_deposito">En Depósito</option>
                                        <option value="mantenimiento">Mantenimiento</option>
                                        <option value="baja">Dado de Baja</option>
                                    </select>
                                    <p v-if="form.errors.estado" class="mt-2 text-sm font-semibold text-red-600">{{ form.errors.estado }}</p>
                                </div>

                                <div>
                                    <label for="ubicacion" class="block text-sm font-bold text-[#334155]">
                                        Ubicación actual
                                    </label>
                                    <input
                                        type="text"
                                        id="ubicacion"
                                        v-model="form.ubicacion_actual"
                                        class="mt-2 block w-full rounded-xl border border-[#E2E8F0] bg-white px-4 py-3 text-sm font-medium text-[#0F172A] placeholder:text-[#64748B] shadow-sm focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]"
                                        placeholder="Ej: Estante 3, Nivel 2"
                                    />
                                    <p v-if="form.errors.ubicacion_actual" class="mt-2 text-sm font-semibold text-red-600">{{ form.errors.ubicacion_actual }}</p>
                                </div>
                            </div>
                        </section>

                        <section class="overflow-hidden rounded-2xl border border-[#E2E8F0] bg-white shadow-sm">
                            <div class="border-b border-[#E2E8F0] px-5 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl border border-blue-200 bg-[#EFF6FF] text-[#2563EB]">
                                        <QrCodeIcon class="h-5 w-5" aria-hidden="true" />
                                    </div>
                                    <div>
                                        <h2 class="text-lg font-bold text-[#0F172A]">
                                            Identificación
                                        </h2>
                                        <p class="text-sm text-[#475569]">
                                            Código QR o serial del activo.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-3 p-5">
                                <label for="qr_code" class="block text-sm font-bold text-[#334155]">
                                    Código QR / Serial
                                </label>
                                <input
                                    type="text"
                                    id="qr_code"
                                    v-model="form.qr_code"
                                    class="block w-full rounded-xl border border-[#E2E8F0] bg-white px-4 py-3 font-mono text-sm font-medium text-[#0F172A] placeholder:text-[#64748B] shadow-sm focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]"
                                    placeholder="Escanee o ingrese código"
                                />
                                <p class="text-xs font-medium leading-5 text-[#475569]">
                                    Puede dejar este campo vacío para generarlo automáticamente más tarde.
                                </p>
                                <p v-if="form.errors.qr_code" class="text-sm font-semibold text-red-600">{{ form.errors.qr_code }}</p>
                            </div>
                        </section>

                        <section class="rounded-2xl border border-[#E2E8F0] bg-[#F8FAFC] p-5">
                            <p class="text-sm font-bold text-[#0F172A]">
                                Revisión antes de guardar
                            </p>
                            <p class="mt-2 text-sm leading-6 text-[#475569]">
                                Verifica que la descripción, el tipo, el estado y la ubicación correspondan al activo físico registrado.
                            </p>
                            <div v-if="form.processing" class="mt-4 rounded-xl border border-blue-200 bg-[#EFF6FF] px-4 py-3 text-sm font-bold text-blue-800">
                                Guardando activo...
                            </div>
                        </section>

                        <div class="flex flex-col gap-3 sm:flex-row lg:flex-col">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center justify-center rounded-xl border border-transparent bg-[#2563EB] px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#1D4ED8] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2 disabled:cursor-not-allowed disabled:bg-slate-300 disabled:text-slate-600"
                            >
                                <CheckIcon class="mr-2 h-5 w-5" aria-hidden="true" />
                                {{ form.processing ? 'Guardando...' : 'Guardar activo' }}
                            </button>
                            <Link
                                :href="route('activos.disponibles')"
                                class="inline-flex items-center justify-center rounded-xl border border-[#E2E8F0] bg-white px-4 py-3 text-sm font-bold text-[#334155] shadow-sm transition hover:bg-[#F8FAFC] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2"
                            >
                                Cancelar
                            </Link>
                        </div>
                    </aside>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
