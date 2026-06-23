<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Html5Qrcode } from 'html5-qrcode';
import axios from 'axios';

const scanning = ref(false);
const result = ref(null);
const error = ref(null);
const success = ref(null);
const cameras = ref([]);
const selectedCamera = ref(null);
const processingScan = ref(false);
let html5QrCode = null;

const startScanning = async () => {
    if (scanning.value || processingScan.value) {
        return;
    }

    await stopScanning();

    error.value = null;
    success.value = null;
    result.value = null;
    processingScan.value = false;
    
    try {
        const devices = await Html5Qrcode.getCameras();
        if (devices && devices.length) {
            cameras.value = devices;
            if (!selectedCamera.value) {
                selectedCamera.value = devices[0].id;
            }
            
            html5QrCode = new Html5Qrcode("reader");
            
            await html5QrCode.start(
                selectedCamera.value, 
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 }
                },
                onScanSuccess,
                onScanFailure
            );
            
            scanning.value = true;
        } else {
            error.value = "No se encontraron cámaras.";
        }
    } catch (err) {
        await stopScanning();
        error.value = "Error al acceder a la cámara: " + err;
    }
};

const stopScanning = async () => {
    const scanner = html5QrCode;

    if (!scanner) {
        scanning.value = false;
        return;
    }

    try {
        if (scanning.value) {
            await scanner.stop();
        }
    } catch (stopError) {
        console.warn('No se pudo detener normalmente el lector.', stopError);
    }

    try {
        if (typeof scanner.clear === 'function') {
            scanner.clear();
        }
    } catch (clearError) {
        console.warn('No se pudo limpiar el lector.', clearError);
    }

    if (html5QrCode === scanner) {
        html5QrCode = null;
    }

    scanning.value = false;
};

const onScanSuccess = async (decodedText, decodedResult) => {
    if (processingScan.value) {
        return;
    }

    processingScan.value = true;
    result.value = decodedText;
    success.value = null;
    error.value = null;

    await stopScanning();
    
    try {
        // Asumiendo que el QR contiene la URL firmada completa
        const response = await axios.post(decodedText);
        success.value = "Check-in realizado con éxito para la reserva #" + (response.data.reserva?.id || '');
        
        // Opcional: Redirigir o refrescar
    } catch (err) {
        if (err.response) {
            error.value = err.response.data.message || "Error al procesar el check-in.";
        } else {
            error.value = "URL inválida o error de conexión.";
        }
    } finally {
        processingScan.value = false;
    }
};

const onScanFailure = (error) => {
    // console.warn(`Code scan error = ${error}`);
};

onBeforeUnmount(() => {
    stopScanning();
});
</script>

<template>
    <Head title="Escanear QR" />

    <AuthenticatedLayout>
        <div class="min-h-screen w-full bg-[#F5F7FB] text-[#0F172A]">
            <div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div class="min-w-0">
                            <p class="text-xs font-bold uppercase tracking-widest text-blue-600">
                                Validación de reservas
                            </p>
                            <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                                Escanear reserva
                            </h1>
                            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                                Escanea el código presentado por el usuario para registrar su check-in.
                            </p>
                        </div>

                        <button
                            type="button"
                            @click="router.visit(route('reservas.index'))"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="h-5 w-5"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"
                                />
                            </svg>
                            Volver a Mis reservas
                        </button>
                    </div>
                </section>

                <div v-if="$page.props.auth?.isAdmin" class="grid grid-cols-1 gap-6 lg:grid-cols-5">
                    <aside class="order-1 space-y-6 lg:order-2 lg:col-span-2">
                        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                            <div class="flex items-start gap-3">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-blue-200 bg-blue-50 text-blue-600">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="h-6 w-6"
                                        aria-hidden="true"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z"
                                        />
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 13.5h1.5v1.5h-1.5v-1.5Z"
                                        />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-slate-900">
                                        Guía rápida
                                    </h2>
                                    <p class="mt-1 text-sm leading-6 text-slate-600">
                                        Sigue estos pasos para realizar una validación clara y sin interrupciones.
                                    </p>
                                </div>
                            </div>

                            <ol class="mt-5 space-y-3">
                                <li class="flex gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">1</span>
                                    <span class="text-sm font-medium text-slate-700">Permite el acceso a la cámara.</span>
                                </li>
                                <li class="flex gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">2</span>
                                    <span class="text-sm font-medium text-slate-700">Selecciona la cámara adecuada.</span>
                                </li>
                                <li class="flex gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">3</span>
                                    <span class="text-sm font-medium text-slate-700">Coloca el código dentro del marco.</span>
                                </li>
                                <li class="flex gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">4</span>
                                    <span class="text-sm font-medium text-slate-700">Espera la validación automática.</span>
                                </li>
                            </ol>
                        </section>

                        <section
                            v-if="cameras.length > 1 && !scanning"
                            class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6"
                        >
                            <label for="camera-select" class="block text-sm font-bold text-slate-900">
                                Cámara
                            </label>
                            <p class="mt-1 text-sm text-slate-600">
                                Elige el dispositivo que usarás para escanear el código.
                            </p>
                            <select
                                id="camera-select"
                                v-model="selectedCamera"
                                class="mt-3 block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-800 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <option v-for="cam in cameras" :key="cam.id" :value="cam.id">
                                    {{ cam.label }}
                                </option>
                            </select>
                        </section>
                    </aside>

                    <main class="order-2 space-y-6 lg:order-1 lg:col-span-3">
                        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                            <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                                <div>
                                    <p class="text-sm font-bold text-slate-900">
                                        Lector de código QR
                                    </p>
                                    <p class="mt-1 text-sm text-slate-600">
                                        Mantén el código visible dentro del recuadro para procesarlo.
                                    </p>
                                </div>

                                <div
                                    v-if="scanning"
                                    class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700"
                                >
                                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                                    Cámara activa
                                </div>
                                <div
                                    v-else
                                    class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-600"
                                >
                                    <span class="h-2.5 w-2.5 rounded-full bg-slate-400"></span>
                                    Escáner en espera
                                </div>
                            </div>

                            <div class="space-y-5 p-4 sm:p-6">
                                <div class="rounded-2xl border border-blue-100 bg-blue-50/60 p-3 sm:p-5">
                                    <div id="reader" class="mx-auto w-full"></div>
                                </div>

                                <div
                                    v-if="!scanning && !success && !error && !result"
                                    class="rounded-2xl border border-slate-200 bg-white p-5 text-center"
                                >
                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl border border-blue-200 bg-blue-50 text-blue-600">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                            class="h-8 w-8"
                                            aria-hidden="true"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z"
                                            />
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 13.5h1.5v1.5h-1.5v-1.5Z"
                                            />
                                        </svg>
                                    </div>
                                    <h2 class="mt-4 text-xl font-bold text-slate-900">
                                        Escáner listo
                                    </h2>
                                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-600">
                                        Inicia la cámara y acerca el QR de la reserva para validar el acceso.
                                    </p>
                                    <button
                                        type="button"
                                        @click="startScanning"
                                        :disabled="scanning || processingScan"
                                        class="mt-5 inline-flex items-center justify-center rounded-xl border border-transparent bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
                                    >
                                        Iniciar escáner
                                    </button>
                                </div>

                                <div
                                    v-if="scanning"
                                    class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5"
                                >
                                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <p class="inline-flex items-center gap-2 text-sm font-bold text-emerald-800">
                                                <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                                                Cámara activa
                                            </p>
                                            <p class="mt-1 text-sm leading-6 text-emerald-900">
                                                Coloca el código QR dentro del marco. El sistema lo validará cuando pueda leerlo correctamente.
                                            </p>
                                        </div>
                                        <button
                                            type="button"
                                            @click="stopScanning"
                                            :disabled="processingScan"
                                            class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
                                        >
                                            Detener escáner
                                        </button>
                                    </div>
                                </div>

                                <div
                                    v-if="processingScan && result && !success && !error"
                                    class="rounded-2xl border border-blue-200 bg-blue-50 p-5"
                                    role="status"
                                >
                                    <p class="text-sm font-bold text-blue-800">
                                        Código detectado
                                    </p>
                                    <p class="mt-1 text-sm leading-6 text-blue-900">
                                        Validando la reserva...
                                    </p>
                                </div>

                                <div
                                    v-if="success"
                                    class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5"
                                    role="status"
                                >
                                    <div class="flex gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-white">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.5"
                                                stroke="currentColor"
                                                class="h-6 w-6"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="m4.5 12.75 6 6 9-13.5"
                                                />
                                            </svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <h2 class="text-lg font-bold text-emerald-900">
                                                Validación completada
                                            </h2>
                                            <p class="mt-1 text-sm leading-6 text-emerald-900">
                                                {{ success }}
                                            </p>
                                            <button
                                                type="button"
                                                @click="startScanning"
                                                :disabled="scanning || processingScan"
                                                class="mt-4 inline-flex items-center justify-center rounded-xl border border-transparent bg-blue-600 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
                                            >
                                                Escanear otro código
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    v-if="error"
                                    class="rounded-2xl border border-red-200 bg-red-50 p-5"
                                    role="alert"
                                >
                                    <div class="flex gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-600 text-white">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.5"
                                                stroke="currentColor"
                                                class="h-6 w-6"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"
                                                />
                                            </svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <h2 class="text-lg font-bold text-red-900">
                                                No se pudo completar la validación
                                            </h2>
                                            <p class="mt-1 text-sm leading-6 text-red-900">
                                                {{ error }}
                                            </p>
                                            <button
                                                type="button"
                                                @click="startScanning"
                                                :disabled="scanning || processingScan"
                                                class="mt-4 inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
                                            >
                                                Intentar nuevamente
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    v-if="result"
                                    class="rounded-2xl border border-slate-200 bg-slate-50 p-4"
                                >
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                        Resultado escaneado
                                    </p>
                                    <p class="mt-2 break-all text-sm font-medium leading-6 text-slate-700">
                                        {{ result }}
                                    </p>
                                </div>
                            </div>
                        </section>
                    </main>
                </div>

                <section
                    v-else
                    class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm sm:p-10"
                >
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl border border-blue-200 bg-blue-50 text-blue-600">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="h-8 w-8"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"
                            />
                        </svg>
                    </div>
                    <h2 class="mt-5 text-xl font-bold text-slate-900">
                        Acceso restringido
                    </h2>
                    <p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-slate-600">
                        La validación de códigos QR está disponible para Administradores y Encargados de Laboratorio.
                    </p>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
#reader {
    min-height: 320px;
    max-width: 520px;
    overflow: hidden;
    border: 1px solid #dbeafe;
    border-radius: 1rem;
    background: #ffffff;
    color: #0f172a;
}

#reader :deep(video),
#reader :deep(canvas),
#reader :deep(img) {
    max-width: 100% !important;
    border-radius: 0.75rem;
}

#reader :deep(#reader__scan_region) {
    background: #ffffff;
}

#reader :deep(#reader__dashboard_section) {
    padding: 0.75rem;
    color: #334155;
}

#reader :deep(button),
#reader :deep(select) {
    border-radius: 0.75rem;
}
</style>
