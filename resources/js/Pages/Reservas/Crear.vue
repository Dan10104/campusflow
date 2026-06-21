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
            // Redirige al index de reservas después de crear
        },
    });
};

// Validación de fechas
const fechaInicioMinima = ref(new Date().toISOString().slice(0, 16));

const validarFechas = () => {
    if (form.inicio && form.fin) {
        const inicio = new Date(form.inicio);
        const fin = new Date(form.fin);
        
        if (fin <= inicio) {
            return 'La hora de fin debe ser posterior a la hora de inicio';
        }
        
        // Validar que no sea más de 4 horas
        const horas = (fin - inicio) / (1000 * 60 * 60);
        if (horas > 4) {
            return 'La reserva no puede exceder 4 horas';
        }
    }
    return null;
};

const validarAsistentes = computed(() => {
    if (form.asistentes_estimados > props.aula.capacidad) {
        return `El número de asistentes no puede exceder la capacidad del aula (${props.aula.capacidad})`;
    }
    return null;
});
</script>

<template>
    <Head :title="`Reservar - Aula ${aula.codigo}`" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="py-6">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center mb-4">
                    <button
                        @click="volver"
                        class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700"
                    >
                        <ArrowLeftIcon class="h-5 w-5 mr-1" />
                        Volver a aulas
                    </button>
                </div>

                <div>
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        Nueva Reserva
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Completa la información para reservar el aula
                    </p>
                </div>
            </div>
        </div>

        <!-- Formulario -->
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Información del aula -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Aula Seleccionada
                    </h3>
                    
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Código</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ aula.codigo }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tipo</dt>
                            <dd class="mt-1 text-sm text-gray-900 capitalize">{{ aula.tipo.replace('_', ' ') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Capacidad</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ aula.capacidad }} personas</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Ubicación</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ aula.modulo.nombre }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Facultad</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ aula.modulo.facultad.sigla }} - {{ aula.modulo.facultad.nombre }}
                            </dd>
                        </div>
                    </div>

                    <!-- Características -->
                    <div v-if="aula.caracteristicas && aula.caracteristicas.length > 0" class="mt-4 pt-4 border-t border-gray-200">
                        <dt class="text-sm font-medium text-gray-500 mb-2">Características</dt>
                        <div class="flex flex-wrap gap-2">
                            <span
                                v-for="caracteristica in aula.caracteristicas"
                                :key="caracteristica.id"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                            >
                                {{ caracteristica.nombre }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Detalles de la reserva -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Detalles de la Reserva
                    </h3>

                    <div class="space-y-6">
                        <!-- Fechas y horarios -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Fecha y hora de inicio -->
                            <div>
                                <label for="inicio" class="block text-sm font-medium text-gray-700">
                                    Fecha y hora de inicio <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="inicio"
                                    v-model="form.inicio"
                                    type="datetime-local"
                                    :min="fechaInicioMinima"
                                    required
                                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    :class="{ 'border-red-300': form.errors.inicio }"
                                />
                                <p v-if="form.errors.inicio" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.inicio }}
                                </p>
                            </div>

                            <!-- Fecha y hora de fin -->
                            <div>
                                <label for="fin" class="block text-sm font-medium text-gray-700">
                                    Fecha y hora de fin <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="fin"
                                    v-model="form.fin"
                                    type="datetime-local"
                                    :min="form.inicio || fechaInicioMinima"
                                    required
                                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    :class="{ 'border-red-300': form.errors.fin }"
                                />
                                <p v-if="form.errors.fin" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.fin }}
                                </p>
                                <p v-else-if="validarFechas()" class="mt-2 text-sm text-red-600">
                                    {{ validarFechas() }}
                                </p>
                            </div>
                        </div>

                        <!-- Propósito -->
                        <div>
                            <label for="proposito" class="block text-sm font-medium text-gray-700">
                                Propósito de la reserva <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                id="proposito"
                                v-model="form.proposito"
                                rows="3"
                                required
                                maxlength="500"
                                placeholder="Describe brevemente el propósito de tu reserva..."
                                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                :class="{ 'border-red-300': form.errors.proposito }"
                            />
                            <p class="mt-1 text-sm text-gray-500">
                                {{ form.proposito.length }}/500 caracteres
                            </p>
                            <p v-if="form.errors.proposito" class="mt-2 text-sm text-red-600">
                                {{ form.errors.proposito }}
                            </p>
                        </div>

                        <!-- Asistentes estimados -->
                        <div>
                            <label for="asistentes_estimados" class="block text-sm font-medium text-gray-700">
                                Número de asistentes (opcional)
                            </label>
                            <input
                                id="asistentes_estimados"
                                v-model="form.asistentes_estimados"
                                type="number"
                                min="1"
                                :max="aula.capacidad"
                                placeholder="Ej: 30"
                                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                :class="{ 'border-red-300': validarAsistentes }"
                            />
                            <p v-if="validarAsistentes" class="mt-2 text-sm text-red-600">
                                {{ validarAsistentes }}
                            </p>
                            <p v-else class="mt-1 text-sm text-gray-500">
                                Capacidad máxima del aula: {{ aula.capacidad }} personas
                            </p>
                        </div>
                    </div>

                    <!-- Información importante -->
                    <div class="mt-6 rounded-md bg-blue-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <ExclamationCircleIcon class="h-5 w-5 text-blue-400" />
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">
                                    Información importante
                                </h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>Debes realizar check-in al inicio de tu reserva</li>
                                        <li>Puedes hacer check-in hasta 15 minutos antes del inicio</li>
                                        <li>Si no realizas check-in, tu reserva será cancelada</li>
                                        <li>Recuerda dejar el aula limpia y ordenada</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Errores -->
                <div v-if="form.errors.conflicto || form.errors.error" class="rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <ExclamationCircleIcon class="h-5 w-5 text-red-400" />
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                {{ form.errors.conflicto || form.errors.error }}
                            </h3>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="flex justify-end space-x-3">
                    <button
                        type="button"
                        @click="volver"
                        class="px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        :disabled="form.processing || !!validarFechas() || !!validarAsistentes"
                        class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{ form.processing ? 'Creando...' : 'Crear Reserva' }}
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>