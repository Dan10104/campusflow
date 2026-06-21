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
        <!-- Header -->
        <div class="py-6">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center mb-4">
                    <button
                        @click="volver"
                        class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700"
                    >
                        <ArrowLeftIcon class="h-5 w-5 mr-1" />
                        Volver a activos
                    </button>
                </div>

                <div>
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        Solicitar Préstamo
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Completa la información para solicitar el préstamo del activo
                    </p>
                </div>
            </div>
        </div>

        <!-- Formulario -->
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Información del activo -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Activo Seleccionado
                    </h3>
                    
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Descripción</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ activo.descripcion }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tipo</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ activo.tipo_activo.nombre }}</dd>
                        </div>
                        <div v-if="activo.marca">
                            <dt class="text-sm font-medium text-gray-500">Marca</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ activo.marca }}</dd>
                        </div>
                        <div v-if="activo.modelo">
                            <dt class="text-sm font-medium text-gray-500">Modelo</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ activo.modelo }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Código</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ activo.codigo }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Estado</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ activo.estado }}
                                </span>
                            </dd>
                        </div>
                    </div>
                </div>

                <!-- Fechas del préstamo -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Período del Préstamo
                    </h3>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Fecha de inicio -->
                        <div>
                            <label for="inicio_previsto" class="block text-sm font-medium text-gray-700">
                                Fecha y hora de retiro <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="inicio_previsto"
                                v-model="form.inicio_previsto"
                                type="datetime-local"
                                :min="fechaInicioMinima"
                                required
                                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                :class="{ 'border-red-300': form.errors.inicio_previsto }"
                            />
                            <p v-if="form.errors.inicio_previsto" class="mt-2 text-sm text-red-600">
                                {{ form.errors.inicio_previsto }}
                            </p>
                        </div>

                        <!-- Fecha de fin -->
                        <div>
                            <label for="fin_previsto" class="block text-sm font-medium text-gray-700">
                                Fecha y hora de devolución <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="fin_previsto"
                                v-model="form.fin_previsto"
                                type="datetime-local"
                                :min="form.inicio_previsto || fechaInicioMinima"
                                required
                                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                :class="{ 'border-red-300': form.errors.fin_previsto }"
                            />
                            <p v-if="form.errors.fin_previsto" class="mt-2 text-sm text-red-600">
                                {{ form.errors.fin_previsto }}
                            </p>
                            <p v-else-if="validarFechas()" class="mt-2 text-sm text-red-600">
                                {{ validarFechas() }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 rounded-md bg-blue-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <ExclamationCircleIcon class="h-5 w-5 text-blue-400" />
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">
                                    Importante
                                </h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>Asegúrate de devolver el activo en la fecha indicada</li>
                                        <li>Cualquier daño será registrado y podrá afectar futuros préstamos</li>
                                        <li>El personal verificará el estado del activo al momento de la entrega y devolución</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Errores generales -->
                <div v-if="form.errors.error" class="rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <ExclamationCircleIcon class="h-5 w-5 text-red-400" />
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                {{ form.errors.error }}
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
                        :disabled="form.processing || !!validarFechas()"
                        class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{ form.processing ? 'Enviando...' : 'Solicitar Préstamo' }}
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>