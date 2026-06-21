<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, nextTick } from 'vue';
import { 
    CpuChipIcon, 
    ShieldCheckIcon, 
    CalendarDaysIcon, 
    ChartBarIcon, 
    ArrowRightIcon,
    AcademicCapIcon,
    EnvelopeIcon,
    PhoneIcon,
    MapPinIcon,
    LightBulbIcon,
    ChatBubbleLeftRightIcon,
    XMarkIcon,
    PaperAirplaneIcon,
    UserIcon // Added UserIcon explicitly
} from '@heroicons/vue/24/outline';

const features = [
    {
        name: 'Gestión Inteligente de Espacios',
        description: 'Reserve aulas y laboratorios con control de disponibilidad en tiempo real, evitando conflictos y maximizando el uso de recursos.',
        icon: CalendarDaysIcon,
    },
    {
        name: 'Monitoreo IoT en Tiempo Real',
        description: 'Red de sensores integrados para el control automático de energía y seguridad en cada instalación del campus.',
        icon: CpuChipIcon,
    },
    {
        name: 'Auditoría Blockchain',
        description: 'Transparencia total con un registro inmutable de todas las tranacciones, reservas y préstamos de activos.',
        icon: ShieldCheckIcon,
    },
    {
        name: 'Analítica Predictiva',
        description: 'Toma de decisiones basada en datos con modelos de IA que anticipan la demanda de recursos académicos.',
        icon: ChartBarIcon,
    },
];

const contactForm = useForm({
    name: '',
    email: '',
    message: ''
});

const submitContact = () => {
    contactForm.post(route('contact.send'), {
        preserveScroll: true,
        onSuccess: () => {
            alert('¡Gracias por su mensaje! Nos pondremos en contacto pronto.');
            contactForm.reset();
        },
        onError: () => {
            alert('Hubo un error al enviar el mensaje. Por favor revise los campos.');
        }
    });
};

// --- CHATBOT LOGIC ---
const isChatOpen = ref(false);
const chatInput = ref('');
const messages = ref([
    { id: 1, text: '¡Hola! Soy el asistente virtual de SIGEA. ¿En qué puedo ayudarte hoy?', isBot: true }
]);
const chatContainer = ref(null);

const toggleChat = () => {
    isChatOpen.value = !isChatOpen.value;
    if (isChatOpen.value) {
        scrollToBottom();
    }
};

const scrollToBottom = () => {
    nextTick(() => {
        if (chatContainer.value) {
            chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
        }
    });
};

const sendMessage = () => {
    if (!chatInput.value.trim()) return;

    // User message
    messages.value.push({ id: Date.now(), text: chatInput.value, isBot: false });
    const userText = chatInput.value.toLowerCase();
    chatInput.value = '';
    scrollToBottom();

    // Simulated AI response
    setTimeout(() => {
        let response = '';
        
        if (userText.includes('hola') || userText.includes('buenos')) {
            response = '¡Hola! Bienvenido a SIGEA. Pregúntame sobre aulas, horarios o cómo registrarte.';
        } else if (userText.includes('precio') || userText.includes('costo')) {
            response = 'SIGEA es una plataforma institucional gratuita para la comunidad universitaria de la UAGRM.';
        } else if (userText.includes('aulas') || userText.includes('reserva')) {
            response = 'Para reservar un aula, debes iniciar sesión, ir a "Aulas Disponibles" y seleccionar el horario deseado.';
        } else if (userText.includes('registro') || userText.includes('cuenta')) {
            response = 'El registro de cuentas es gestionado por la administración de la facultad. Contacta a soporte para obtener tus credenciales.';
        } else if (userText.includes('misión') || userText.includes('mision')) {
            response = 'Nuestra misión es liderar la transformación digital en la gestión educativa.';
        } else {
            response = 'Entiendo. Para consultas más específicas, por favor utiliza el formulario de contacto al final de la página y un agente humano te responderá.';
        }

        messages.value.push({ id: Date.now()+1, text: response, isBot: true });
        scrollToBottom();
    }, 1000);
};

// --- DATA FOR NEW SECTIONS ---
const steps = [
    { id: 1, title: 'Inicia Sesión', description: 'Accede con tus credenciales institucionales.' },
    { id: 2, title: 'Busca Disponibilidad', description: 'Explora aulas y laboratorios en tiempo real.' },
    { id: 3, title: 'Realiza tu Reserva', description: 'Selecciona el horario y confirma tu solicitud.' },
    { id: 4, title: 'Check-in QR', description: 'Al llegar, escanea el código QR para validar tu uso.' },
];
</script>

<template>
    <Head title="SIGEA - Gestión Corporativa" />

    <div class="bg-white">
        <!-- Header / Navigation -->
        <header class="absolute inset-x-0 top-0 z-50">
            <nav class="flex items-center justify-between p-6 lg:px-8" aria-label="Global">
                <div class="flex lg:flex-1">
                    <a href="#" class="-m-1.5 p-1.5 flex items-center gap-3">
                        <!-- LOGO UPDATE -->
                        <img src="/assets/images/logo.jpg" alt="SIGEA Logo" class="h-10 w-auto rounded-lg shadow-sm" />
                        <span class="text-xl font-bold text-gray-900 tracking-tight">SIGEA CORP</span>
                    </a>
                </div>
                <div class="flex flex-1 justify-end space-x-6">
                    <a href="#mission" class="text-sm font-semibold leading-6 text-gray-900 hover:text-blue-600 hidden md:block">Nosotros</a>
                    <a href="#guide" class="text-sm font-semibold leading-6 text-gray-900 hover:text-blue-600 hidden md:block">Guía</a>
                    <a href="#contact" class="text-sm font-semibold leading-6 text-gray-900 hover:text-blue-600">Contacto</a>
                    <Link :href="route('login')" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-colors">
                        Portal Académico
                    </Link>
                </div>
            </nav>
        </header>

        <main class="isolate">
            <!-- Hero Section -->
            <div class="relative pt-14 pb-16 sm:pb-20 lg:pb-24 overflow-hidden">
                <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80">
                   <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-[#3b82f6] to-[#1e40af] opacity-20 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]"></div>
                </div>
                
                <div class="mx-auto max-w-7xl px-6 lg:px-8 mt-20 sm:mt-24">
                    <div class="mx-auto max-w-2xl text-center">
                        <div class="mb-8 flex justify-center">
                            <div class="relative rounded-full px-3 py-1 text-sm leading-6 text-gray-600 ring-1 ring-gray-900/10 hover:ring-gray-900/20">
                                Innovación para la Educación Superior. <a href="#features" class="font-semibold text-blue-600"><span class="absolute inset-0" aria-hidden="true" />Conoce más <span aria-hidden="true">&rarr;</span></a>
                            </div>
                        </div>
                        <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl text-balance">
                            Transformando la Gestión Administrativa
                        </h1>
                        <p class="mt-6 text-lg leading-8 text-gray-600">
                            Centralizamos operaciones, optimizamos recursos y garantizamos la transparencia institucional mediante tecnología de vanguardia.
                        </p>
                        <div class="mt-10 flex items-center justify-center gap-x-6">
                            <Link :href="route('login')" class="rounded-md bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                Acceso al Sistema
                            </Link>
                            <a href="#contact" class="text-sm font-semibold leading-6 text-gray-900">
                                Solicitar Demo <span aria-hidden="true">→</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MISSION & VISION SECTION (NEW) -->
            <div id="mission" class="bg-white py-16 sm:py-24">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                        <!-- Mission -->
                        <div class="bg-blue-50 rounded-2xl p-8 shadow-sm border border-blue-100 transform hover:-translate-y-1 transition-transform duration-300">
                            <div class="h-12 w-12 bg-blue-600 rounded-lg flex items-center justify-center mb-6 text-white">
                                <AcademicCapIcon class="h-7 w-7"/>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Nuestra Misión</h3>
                            <p class="text-gray-700 leading-relaxed">
                                Proporcionar una plataforma tecnológica integral que revolucione la administración de recursos académicos, garantizando eficiencia, transparencia y accesibilidad para toda la comunidad universitaria, fomentando un entorno educativo moderno y sostenible.
                            </p>
                        </div>
                        <!-- Vision -->
                        <div class="bg-indigo-50 rounded-2xl p-8 shadow-sm border border-indigo-100 transform hover:-translate-y-1 transition-transform duration-300">
                             <div class="h-12 w-12 bg-indigo-600 rounded-lg flex items-center justify-center mb-6 text-white">
                                <LightBulbIcon class="h-7 w-7"/>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Nuestra Visión</h3>
                            <p class="text-gray-700 leading-relaxed">
                                Ser el referente global en sistemas de gestión para instituciones de educación superior, reconocidos por nuestra innovación en IoT e Inteligencia Artificial, impulsando universidades inteligentes (Smart Campus) en toda Latinoamérica.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Section -->
            <div id="features" class="bg-gray-50 py-24 sm:py-32">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="mx-auto max-w-2xl lg:text-center">
                        <h2 class="text-base font-semibold leading-7 text-blue-600">Nuestra Tecnología</h2>
                        <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                            Soluciones integrales para universidades modernas
                        </p>
                    </div>
                    <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-none">
                        <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-4">
                            <div v-for="feature in features" :key="feature.name" class="flex flex-col">
                                <dt class="flex items-center gap-x-3 text-base font-semibold leading-7 text-gray-900">
                                    <component :is="feature.icon" class="h-5 w-5 flex-none text-blue-600" aria-hidden="true" />
                                    {{ feature.name }}
                                </dt>
                                <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600">
                                    <p class="flex-auto">{{ feature.description }}</p>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- USER GUIDE SECTION (NEW) -->
            <div id="guide" class="bg-white py-24 sm:py-32">
                 <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="mx-auto max-w-2xl lg:text-center mb-16">
                        <h2 class="text-base font-semibold leading-7 text-blue-600">Fácil y Rápido</h2>
                        <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                            ¿Cómo funciona SIGEA?
                        </p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8 relative">
                        <!-- Connecting Line (Desktop) -->
                        <div class="hidden md:block absolute top-1/2 left-0 w-full h-0.5 bg-gray-200 -z-10 transform -translate-y-1/2"></div>
                        
                        <div v-for="(step, index) in steps" :key="step.id" class="relative bg-white p-4">
                            <div class="h-12 w-12 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xl mx-auto mb-4 border-4 border-white shadow-md">
                                {{ step.id }}
                            </div>
                            <h3 class="text-lg font-bold text-center mb-2">{{ step.title }}</h3>
                            <p class="text-center text-gray-600 text-sm">{{ step.description }}</p>
                        </div>
                    </div>
                 </div>
            </div>

            <!-- About Section (Gustavo Camargo) -->
            <div id="about" class="relative isolate overflow-hidden bg-white px-6 py-24 sm:py-32 lg:overflow-visible lg:px-0">
                <div class="mx-auto max-w-7xl lg:pr-8 lg:grid lg:grid-cols-2 lg:gap-8">
                    <div class="lg:col-span-1">
                        <div class="lg:max-w-lg">
                            <h2 class="text-base font-semibold leading-7 text-blue-600">Liderazgo</h2>
                            <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Conoce a nuestro Director</p>
                            <p class="mt-6 text-xl leading-8 text-gray-700">
                                "La tecnología no es solo una herramienta, es el cimiento sobre el cual construimos el futuro de la educación."
                            </p>
                            <div class="mt-10 border-l-4 border-blue-600 pl-6 italic text-gray-600">
                                Gustavo Camargo, Estudiante de Ingeniería en Sistemas y visionario detrás de la plataforma SIGEA, lidera la transformación digital con un enfoque en eficiencia y transparencia.
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Image Placeholder -->
                <div class="lg:absolute lg:top-0 lg:bottom-0 lg:right-0 lg:w-1/2 bg-gray-100 flex items-center justify-center">
                    <div class="text-center p-12">
                         <div class="h-64 w-64 rounded-full bg-gradient-to-r from-gray-200 to-gray-300 mx-auto flex items-center justify-center shadow-inner mb-6 overflow-hidden">
                            <!-- Placeholder icon or actual image if available -->
                            <UserIcon class="h-32 w-32 text-gray-400" />
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">Est. Gustavo Camargo</h3>
                        <p class="text-blue-600 font-medium">CEO & Fundador</p>
                    </div>
                </div>
            </div>

            <!-- Contact Section -->
            <div id="contact" class="bg-blue-900 py-24 sm:py-32">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="mx-auto max-w-2xl lg:mx-0">
                        <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">Contáctanos</h2>
                        <p class="mt-6 text-lg leading-8 text-gray-300">
                            Estamos listos para modernizar tu institución. Envíanos un mensaje y coordinemos una reunión.
                        </p>
                    </div>
                    <div class="mx-auto mt-16 grid max-w-2xl grid-cols-1 gap-8 lg:mx-0 lg:max-w-none lg:grid-cols-2">
                        <!-- Contact Info -->
                        <div class="text-gray-300 space-y-4">
                            <div class="flex items-center gap-4">
                                <div class="bg-blue-800 p-3 rounded-lg">
                                    <MapPinIcon class="h-6 w-6 text-white" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-white">Oficina Principal</h3>
                                    <p>Av. Bush, Módulo 224, Santa Cruz, Bolivia</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="bg-blue-800 p-3 rounded-lg">
                                    <EnvelopeIcon class="h-6 w-6 text-white" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-white">Email Corporativo</h3>
                                    <p>gustavo@sigea.com</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="bg-blue-800 p-3 rounded-lg">
                                    <PhoneIcon class="h-6 w-6 text-white" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-white">Teléfono</h3>
                                    <p>+591 63580939</p>
                                </div>
                            </div>
                        </div>

                        <!-- Form -->
                        <form @submit.prevent="submitContact" class="bg-white rounded-2xl p-8 shadow-xl">
                            <div class="grid grid-cols-1 gap-y-6">
                                <div>
                                    <label for="name" class="block text-sm font-semibold leading-6 text-gray-900">Nombre Completo</label>
                                    <input v-model="contactForm.name" type="text" name="name" id="name" class="block w-full rounded-md border-0 px-3.5 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6" placeholder="Juan Pérez">
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-semibold leading-6 text-gray-900">Correo Electrónico</label>
                                    <input v-model="contactForm.email" type="email" name="email" id="email" class="block w-full rounded-md border-0 px-3.5 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6" placeholder="juan@empresa.com">
                                </div>
                                <div>
                                    <label for="message" class="block text-sm font-semibold leading-6 text-gray-900">Mensaje</label>
                                    <textarea v-model="contactForm.message" name="message" id="message" rows="4" class="block w-full rounded-md border-0 px-3.5 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6" placeholder="¿Cómo podemos ayudarte?"></textarea>
                                </div>
                                <button type="submit" class="block w-full rounded-md bg-blue-600 px-3.5 py-2.5 text-center text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-all">
                                    Enviar Mensaje
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
        
        <!-- Footer -->
        <footer class="bg-gray-900 border-t border-gray-800">
            <div class="mx-auto max-w-7xl overflow-hidden px-6 py-12 lg:px-8">
                <div class="flex justify-center space-x-6 mb-8">
                   <!-- Social Icons Placeholders -->
                   <a href="#" class="text-gray-400 hover:text-gray-300">Facebook</a>
                   <a href="#" class="text-gray-400 hover:text-gray-300">LinkedIn</a>
                   <a href="#" class="text-gray-400 hover:text-gray-300">Twitter</a>
                </div>
                <p class="text-center text-xs leading-5 text-gray-400">
                    &copy; 2025 SIGEA Corp. Todos los derechos reservados. Desarrollado por Gustavo Camargo.
                </p>
            </div>
        </footer>

        <!-- CHATBOT WIDGET (NEW) -->
        <div class="fixed bottom-6 right-6 z-50">
            <!-- Chat Button -->
            <button 
                @click="toggleChat"
                class="bg-blue-600 hover:bg-blue-700 text-white rounded-full p-4 shadow-lg transition-transform hover:scale-105 flex items-center justify-center"
            >
                <ChatBubbleLeftRightIcon v-if="!isChatOpen" class="h-8 w-8" />
                <XMarkIcon v-else class="h-8 w-8" />
            </button>

            <!-- Chat Window -->
            <div v-if="isChatOpen" class="absolute bottom-20 right-0 w-80 md:w-96 bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden flex flex-col transition-all animate-fade-in-up" style="height: 500px; max-height: 80vh;">
                <!-- Header -->
                <div class="bg-blue-600 p-4 text-white flex items-center gap-3">
                    <div class="h-10 w-10 bg-white/20 rounded-full flex items-center justify-center">
                        <CpuChipIcon class="h-6 w-6" />
                    </div>
                    <div>
                        <h3 class="font-bold">Asistente SIGEA</h3>
                        <p class="text-xs text-blue-100 flex items-center gap-1">
                            <span class="block h-2 w-2 rounded-full bg-green-400"></span> En línea
                        </p>
                    </div>
                </div>

                <!-- Messages Area -->
                <div ref="chatContainer" class="flex-1 p-4 overflow-y-auto space-y-4 bg-gray-50">
                    <div 
                        v-for="msg in messages" 
                        :key="msg.id" 
                        class="flex"
                        :class="msg.isBot ? 'justify-start' : 'justify-end'"
                    >
                        <div 
                            class="max-w-[80%] p-3 rounded-2xl text-sm shadow-sm"
                            :class="msg.isBot ? 'bg-white text-gray-800 rounded-tl-none border border-gray-100' : 'bg-blue-600 text-white rounded-tr-none'"
                        >
                            {{ msg.text }}
                        </div>
                    </div>
                </div>

                <!-- Input Area -->
                <div class="p-3 border-t border-gray-200 bg-white">
                    <form @submit.prevent="sendMessage" class="flex gap-2">
                        <input 
                            v-model="chatInput" 
                            type="text" 
                            placeholder="Escribe tu consulta..." 
                            class="flex-1 rounded-full border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2 px-4"
                        >
                        <button 
                            type="submit" 
                            class="bg-blue-600 text-white p-2 rounded-full hover:bg-blue-700 transition-colors flex-shrink-0"
                            :disabled="!chatInput"
                        >
                            <PaperAirplaneIcon class="h-5 w-5 -ml-0.5 mt-0.5 transform rotate-0" />
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.3s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
