<script setup>
import { useForm } from '@inertiajs/vue3';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { 
    ArrowLeftIcon, 
    CheckIcon, // <--- CAMBIO: Usamos CheckIcon en lugar de SaveIcon
    TagIcon,
    MapPinIcon,
    QrCodeIcon
} from '@heroicons/vue/24/outline';

// ... el resto de tu código (props, form, submit) sigue igual
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
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center mb-4">
                    <Link
                        :href="route('activos.disponibles')"
                        class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700"
                    >
                        <ArrowLeftIcon class="h-5 w-5 mr-1" />
                        Cancelar y volver
                    </Link>
                </div>

                <div class="md:flex md:items-center md:justify-between">
                    <div class="flex-1 min-w-0">
                        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                            Registrar Nuevo Activo
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            Complete la información para dar de alta un equipo en el inventario.
                        </p>
                    </div>
                    <div class="mt-4 flex md:mt-0 md:ml-4">
                        <button
                            @click="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                        >
                            <CheckIcon class="h-5 w-5 mr-2" />
                            Guardar Activo
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
            <form @submit.prevent="submit" class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                        <div class="px-6 py-5 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                                <TagIcon class="h-5 w-5 mr-2 text-gray-400"/>
                                Detalles del Equipo
                            </h3>
                        </div>
                        <div class="px-6 py-5 space-y-6">
                            
                            <div>
                                <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción / Nombre *</label>
                                <div class="mt-1">
                                    <textarea
                                        id="descripcion"
                                        v-model="form.descripcion"
                                        rows="3"
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                        placeholder="Ej: Proyector Epson X41 - Laboratorio 1"
                                    />
                                </div>
                                <p v-if="form.errors.descripcion" class="mt-1 text-sm text-red-600">{{ form.errors.descripcion }}</p>
                            </div>

                            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                                <div>
                                    <label for="tipo_activo" class="block text-sm font-medium text-gray-700">Tipo de Activo *</label>
                                    <select
                                        id="tipo_activo"
                                        v-model="form.tipo_activo_id"
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                                    >
                                        <option value="" disabled>Seleccione un tipo</option>
                                        <option v-for="tipo in tiposActivo" :key="tipo.id" :value="tipo.id">
                                            {{ tipo.nombre }}
                                        </option>
                                    </select>
                                    <p v-if="form.errors.tipo_activo_id" class="mt-1 text-sm text-red-600">{{ form.errors.tipo_activo_id }}</p>
                                </div>

                                <div>
                                    <label for="marca" class="block text-sm font-medium text-gray-700">Marca</label>
                                    <input
                                        type="text"
                                        id="marca"
                                        v-model="form.marca"
                                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    />
                                    <p v-if="form.errors.marca" class="mt-1 text-sm text-red-600">{{ form.errors.marca }}</p>
                                </div>

                                <div>
                                    <label for="modelo" class="block text-sm font-medium text-gray-700">Modelo</label>
                                    <input
                                        type="text"
                                        id="modelo"
                                        v-model="form.modelo"
                                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    />
                                    <p v-if="form.errors.modelo" class="mt-1 text-sm text-red-600">{{ form.errors.modelo }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    
                    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                        <div class="px-6 py-5 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                                <MapPinIcon class="h-5 w-5 mr-2 text-gray-400"/>
                                Estado y Ubicación
                            </h3>
                        </div>
                        <div class="px-6 py-5 space-y-6">
                            <div>
                                <label for="estado" class="block text-sm font-medium text-gray-700">Estado Inicial *</label>
                                <select
                                    id="estado"
                                    v-model="form.estado"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                                >
                                    <option value="disponible">Disponible</option>
                                    <option value="en_deposito">En Depósito</option>
                                    <option value="mantenimiento">Mantenimiento</option>
                                    <option value="baja">Dado de Baja</option>
                                </select>
                                <p v-if="form.errors.estado" class="mt-1 text-sm text-red-600">{{ form.errors.estado }}</p>
                            </div>

                            <div>
                                <label for="ubicacion" class="block text-sm font-medium text-gray-700">Ubicación Actual</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <input
                                        type="text"
                                        id="ubicacion"
                                        v-model="form.ubicacion_actual"
                                        class="focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                        placeholder="Ej: Estante 3, Nivel 2"
                                    />
                                </div>
                                <p v-if="form.errors.ubicacion_actual" class="mt-1 text-sm text-red-600">{{ form.errors.ubicacion_actual }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                        <div class="px-6 py-5 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                                <QrCodeIcon class="h-5 w-5 mr-2 text-gray-400"/>
                                Identificación
                            </h3>
                        </div>
                        <div class="px-6 py-5">
                            <div>
                                <label for="qr_code" class="block text-sm font-medium text-gray-700">Código QR / Serial</label>
                                <div class="mt-1">
                                    <input
                                        type="text"
                                        id="qr_code"
                                        v-model="form.qr_code"
                                        class="focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md font-mono"
                                        placeholder="Escanee o ingrese código"
                                    />
                                </div>
                                <p class="mt-2 text-xs text-gray-500">
                                    Puede dejar este campo vacío para generarlo automáticamente más tarde.
                                </p>
                                <p v-if="form.errors.qr_code" class="mt-1 text-sm text-red-600">{{ form.errors.qr_code }}</p>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>