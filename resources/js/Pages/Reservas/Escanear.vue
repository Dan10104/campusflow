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
let html5QrCode = null;

const startScanning = async () => {
    error.value = null;
    success.value = null;
    result.value = null;
    
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
        error.value = "Error al acceder a la cámara: " + err;
    }
};

const stopScanning = async () => {
    if (html5QrCode && scanning.value) {
        await html5QrCode.stop();
        scanning.value = false;
    }
};

const onScanSuccess = async (decodedText, decodedResult) => {
    await stopScanning(); // Detener al encontrar algo
    result.value = decodedText;
    
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
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Escanear Código QR
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        
                        <div class="mb-4 text-center">
                            <p class="text-gray-600 mb-4">Escanea el código QR de una reserva para realizar el check-in.</p>
                            
                            <div v-if="!scanning && !success && !error" class="mb-4">
                                <button @click="startScanning" class="btn btn-primary">
                                    Iniciar Escáner
                                </button>
                            </div>
                            
                            <div v-if="scanning" class="mb-4">
                                <button @click="stopScanning" class="btn btn-secondary">
                                    Detener
                                </button>
                            </div>

                            <div v-if="cameras.length > 1 && !scanning" class="mb-4">
                                <select v-model="selectedCamera" class="form-select">
                                    <option v-for="cam in cameras" :key="cam.id" :value="cam.id">
                                        {{ cam.label }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Area del lector -->
                        <div id="reader" width="600px" class="mx-auto" style="max-width: 500px;"></div>

                        <!-- Resultados -->
                        <div v-if="success" class="alert alert-success mt-4">
                            {{ success }}
                            <div class="mt-2">
                                <button @click="startScanning" class="btn btn-sm btn-outline-success">Escanear otro</button>
                            </div>
                        </div>

                        <div v-if="error" class="alert alert-danger mt-4">
                            {{ error }}
                            <div class="mt-2">
                                <button @click="startScanning" class="btn btn-sm btn-outline-danger">Intentar de nuevo</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
#reader {
    border: 1px solid #ccc;
    border-radius: 8px;
}
</style>
