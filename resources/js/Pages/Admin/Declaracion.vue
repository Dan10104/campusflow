<script setup>
import { Head, usePage, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed } from 'vue';
import { 
    CheckCircleIcon, 
    XCircleIcon, 
    ClockIcon,
    BuildingLibraryIcon,
    CubeIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    reservasPendientes: Array,
    prestamosPendientes: Array
});

const form = useForm({});
const activeTab = ref('reservas'); // 'reservas' | 'activos'

const formatDate = (date) => new Date(date).toLocaleString();

const aprobarReserva = (id) => {
    if(confirm('¿Aprobar esta reserva de aula?')) {
        form.post(route('admin.reservas.aprobar', id), {
            preserveScroll: true,
            onSuccess: () => alert('Reserva Aprobada')
        });
    }
};

const rechazarReserva = (id) => {
    if(confirm('¿Rechazar esta reserva?')) {
        form.post(route('admin.reservas.rechazar', id), {
            preserveScroll: true,
            onSuccess: () => alert('Reserva Rechazada')
        });
    }
};

const aprobarPrestamo = (id) => {
    // EN este flujo, aprobar el préstamo = Entregar activo
    if(confirm('¿Aprobar solicitud y confirmar entrega de activo?')) {
        form.post(route('prestamos.entregar', id), {
            preserveScroll: true,
            onSuccess: () => alert('Solicitud Aprobada y Activo Entregado')
        });
    }
};

const rechazarPrestamo = (id) => {
    // Falta implementar rechazo explícito en controller, por ahora simulamos
    alert('Funcionalidad de rechazo pendiente de implementar en backend');
};

</script>

<template>
    <Head title="Panel de Aprobación Admin" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Centro de Aprobaciones
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Tabs -->
                <div class="mb-6 border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button @click="activeTab = 'reservas'"
                            :class="[activeTab === 'reservas' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center']">
                            <BuildingLibraryIcon class="w-5 h-5 mr-2" />
                            Reservas de Aula <span class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2.5 rounded-full text-xs" v-if="reservasPendientes.length">{{ reservasPendientes.length }}</span>
                        </button>
                        <button @click="activeTab = 'activos'"
                            :class="[activeTab === 'activos' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center']">
                            <CubeIcon class="w-5 h-5 mr-2" />
                            Préstamos de Activos <span class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2.5 rounded-full text-xs" v-if="prestamosPendientes.length">{{ prestamosPendientes.length }}</span>
                        </button>
                    </nav>
                </div>

                <!-- Reservas Aulas -->
                <div v-show="activeTab === 'reservas'" class="bg-white shadow overflow-hidden sm:rounded-md">
                    <ul role="list" class="divide-y divide-gray-200">
                        <li v-for="reserva in reservasPendientes" :key="reserva.id">
                            <div class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div class="text-sm font-medium text-blue-600 truncate">
                                        {{ reserva.aula.nombre }} ({{ reserva.aula.codigo }})
                                    </div>
                                    <div class="ml-2 flex-shrink-0 flex">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Pendiente
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-2 sm:flex sm:justify-between">
                                    <div class="sm:flex">
                                        <p class="flex items-center text-sm text-gray-500">
                                            <ClockIcon class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" />
                                            {{ formatDate(reserva.inicio) }} - {{ formatDate(reserva.fin) }}
                                        </p>
                                        <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                                            Solicitante: {{ reserva.usuario.name }}
                                        </p>
                                    </div>
                                    <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                        <button @click="aprobarReserva(reserva.id)" class="mr-3 text-green-600 hover:text-green-900 font-medium">Aprobar</button>
                                        <button @click="rechazarReserva(reserva.id)" class="text-red-600 hover:text-red-900 font-medium">Rechazar</button>
                                    </div>
                                </div>
                                <p class="mt-2 text-sm text-gray-600 italic border-l-2 pl-2 border-gray-300">
                                    "{{ reserva.proposito }}"
                                </p>
                            </div>
                        </li>
                        <li v-if="reservasPendientes.length === 0" class="px-4 py-10 text-center text-gray-500">
                            No hay reservas de aula pendientes de aprobación.
                        </li>
                    </ul>
                </div>

                <!-- Prestamos Activos -->
                <div v-show="activeTab === 'activos'" class="bg-white shadow overflow-hidden sm:rounded-md">
                    <ul role="list" class="divide-y divide-gray-200">
                        <li v-for="prestamo in prestamosPendientes" :key="prestamo.id">
                            <div class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div class="text-sm font-medium text-purple-600 truncate">
                                        {{ prestamo.activo.descripcion }}
                                    </div>
                                    <div class="ml-2 flex-shrink-0 flex">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Pendiente
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-2 sm:flex sm:justify-between">
                                    <div class="sm:flex">
                                        <p class="flex items-center text-sm text-gray-500">
                                            <ClockIcon class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" />
                                            {{ formatDate(prestamo.inicio_previsto) }}
                                        </p>
                                        <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                                            Solicitante: {{ prestamo.usuario.name }}
                                        </p>
                                    </div>
                                    <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                        <button @click="aprobarPrestamo(prestamo.id)" class="mr-3 text-green-600 hover:text-green-900 font-medium">Entregar/Aprobar</button>
                                        <button @click="rechazarPrestamo(prestamo.id)" class="text-red-600 hover:text-red-900 font-medium">Rechazar</button>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li v-if="prestamosPendientes.length === 0" class="px-4 py-10 text-center text-gray-500">
                            No hay solicitudes de préstamo pendientes.
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
