<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';
import {
    AcademicCapIcon,
    ArrowPathIcon,
    ArrowRightIcon,
    Bars3Icon,
    BuildingOffice2Icon,
    CalendarDaysIcon,
    ChartBarIcon,
    ChatBubbleLeftRightIcon,
    CheckCircleIcon,
    CpuChipIcon,
    CubeIcon,
    DevicePhoneMobileIcon,
    ExclamationTriangleIcon,
    FingerPrintIcon,
    LockClosedIcon,
    MapPinIcon,
    PaperAirplaneIcon,
    PresentationChartLineIcon,
    QrCodeIcon,
    RectangleStackIcon,
    ShieldCheckIcon,
    SparklesIcon,
    Squares2X2Icon,
    UsersIcon,
    XMarkIcon,
} from '@heroicons/vue/24/outline';

const currentYear = new Date().getFullYear();
const isMobileMenuOpen = ref(false);

const navigationItems = [
    { label: 'Inicio', href: '#inicio' },
    { label: 'Solución', href: '#solucion' },
    { label: 'Beneficios', href: '#beneficios' },
    { label: 'Funcionamiento', href: '#funcionamiento' },
    { label: 'Tecnología', href: '#tecnologia' },
    { label: 'Contacto', href: '#contacto' },
];

const problems = [
    'Reservas dispersas en diferentes canales.',
    'Conflictos de horarios y baja visibilidad de disponibilidad.',
    'Escasa trazabilidad sobre solicitudes, aprobaciones y uso real.',
    'Dificultad para controlar activos académicos compartidos.',
    'Información administrativa fragmentada para tomar decisiones.',
];

const benefits = [
    {
        title: 'Disponibilidad centralizada',
        description: 'Consulta aulas, laboratorios y activos desde una experiencia única y ordenada.',
        icon: CalendarDaysIcon,
    },
    {
        title: 'Reducción de conflictos',
        description: 'Los flujos de reserva ayudan a evitar cruces de horarios y solicitudes duplicadas.',
        icon: ArrowPathIcon,
    },
    {
        title: 'Control de activos',
        description: 'Gestiona préstamos, estados y devoluciones de recursos académicos compartidos.',
        icon: CubeIcon,
    },
    {
        title: 'Trazabilidad operativa',
        description: 'Cada solicitud queda asociada a usuarios, estados y eventos de seguimiento.',
        icon: ShieldCheckIcon,
    },
    {
        title: 'Acceso según roles',
        description: 'La experiencia se adapta a permisos y responsabilidades dentro de la institución.',
        icon: LockClosedIcon,
    },
    {
        title: 'Información para decisiones',
        description: 'Los reportes apoyan la lectura administrativa del uso de espacios y activos.',
        icon: ChartBarIcon,
    },
];

const modules = [
    { title: 'Aulas y laboratorios', description: 'Visualización y administración de espacios académicos.', icon: BuildingOffice2Icon },
    { title: 'Reservas', description: 'Solicitudes, estados y seguimiento de uso.', icon: CalendarDaysIcon },
    { title: 'Activos', description: 'Inventario académico y control de disponibilidad.', icon: CubeIcon },
    { title: 'Préstamos', description: 'Entrega, devolución y gestión de recursos compartidos.', icon: RectangleStackIcon },
    { title: 'Usuarios, roles y permisos', description: 'Acceso diferenciado según responsabilidades.', icon: UsersIcon },
    { title: 'Validación QR', description: 'Apoyo para check-in, check-out y validación en campo.', icon: QrCodeIcon },
    { title: 'Monitoreo IoT', description: 'Integración tecnológica y prototipo funcional para escenarios demostrativos.', icon: CpuChipIcon },
    { title: 'Trazabilidad blockchain', description: 'Módulo demostrativo para explorar registros verificables de eventos.', icon: FingerPrintIcon },
    { title: 'Reportes y analítica', description: 'Capacidad incorporada para organizar datos y apoyar decisiones.', icon: PresentationChartLineIcon },
];

const steps = [
    {
        id: 1,
        title: 'Ingreso institucional',
        description: 'Los usuarios acceden al sistema según sus credenciales y permisos.',
    },
    {
        id: 2,
        title: 'Consulta de espacios o activos',
        description: 'CampusFlow muestra disponibilidad, estados y opciones de solicitud.',
    },
    {
        id: 3,
        title: 'Solicitud y aprobación',
        description: 'Las reservas o préstamos avanzan por un flujo claro de revisión.',
    },
    {
        id: 4,
        title: 'Validación, seguimiento y reportes',
        description: 'El uso se valida con QR y queda disponible para consulta administrativa.',
    },
];

const technologies = [
    'Laravel',
    'Vue 3',
    'Inertia.js',
    'MySQL',
    'IoT',
    'Blockchain',
    'Inteligencia artificial',
    'Aplicación móvil',
];

const team = [
    {
        name: 'Coco Daniel Vega Barriga',
        role: 'Desarrollo e integración del proyecto',
        initials: 'CD',
    },
    {
        name: 'Karen Andrea Montalvan Medina',
        role: 'Análisis, documentación y experiencia del producto',
        initials: 'KM',
    },
];

const dashboardStats = [
    { label: 'Aulas', value: 'Disponibles' },
    { label: 'Activos', value: 'En control' },
    { label: 'Reservas', value: 'En seguimiento' },
];

const previewSchedule = [
    { time: '08:00', room: 'Laboratorio A', status: 'Reserva pendiente' },
    { time: '10:00', room: 'Aula 204', status: 'Disponible' },
    { time: '14:00', room: 'Auditorio', status: 'Aprobación requerida' },
];

const contactForm = useForm({
    name: '',
    email: '',
    message: ''
});
const contactFeedback = ref(null);

const submitContact = () => {
    contactFeedback.value = null;

    contactForm.post(route('contact.send'), {
        preserveScroll: true,
        onSuccess: (page) => {
            const flash = page.props?.flash || {};

            if (flash.error) {
                contactFeedback.value = {
                    type: 'error',
                    message: flash.error,
                };
                return;
            }

            contactFeedback.value = {
                type: 'success',
                message: flash.success || 'Mensaje enviado correctamente. El equipo revisara tu solicitud.',
            };
            contactForm.reset();
        },
        onError: () => {
            contactFeedback.value = {
                type: 'error',
                message: 'Hubo un error al enviar el mensaje. Por favor revise los campos.',
            };
        }
    });
};

const isChatOpen = ref(false);
const chatInput = ref('');
const messages = ref([
    {
        id: 1,
        text: '¡Hola! Soy el asistente de CampusFlow. Puedo orientarte sobre aulas, reservas, activos y acceso al sistema.',
        isBot: true,
    }
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

    messages.value.push({ id: Date.now(), text: chatInput.value, isBot: false });
    const userText = chatInput.value.toLowerCase();
    chatInput.value = '';
    scrollToBottom();

    setTimeout(() => {
        let response = '';

        if (userText.includes('hola') || userText.includes('buenos') || userText.includes('buenas')) {
            response = '¡Hola! Puedo orientarte sobre CampusFlow, sus módulos y cómo solicitar una demostración institucional.';
        } else if (userText.includes('aula') || userText.includes('laboratorio') || userText.includes('espacio')) {
            response = 'CampusFlow permite consultar aulas y laboratorios, revisar disponibilidad y organizar solicitudes desde un flujo centralizado.';
        } else if (userText.includes('reserva') || userText.includes('horario')) {
            response = 'Para gestionar reservas, los usuarios ingresan al sistema, consultan disponibilidad y envían solicitudes para revisión o aprobación.';
        } else if (userText.includes('activo') || userText.includes('prestamo') || userText.includes('préstamo')) {
            response = 'El módulo de activos y préstamos ayuda a controlar recursos académicos, entregas, devoluciones y estados de seguimiento.';
        } else if (userText.includes('qr') || userText.includes('check')) {
            response = 'La validación QR apoya procesos de check-in, check-out y registro de uso dentro de los flujos del proyecto.';
        } else if (userText.includes('registro') || userText.includes('cuenta') || userText.includes('acceso')) {
            response = 'El acceso depende de credenciales institucionales y permisos asignados por la administración del sistema.';
        } else if (userText.includes('soporte') || userText.includes('ayuda')) {
            response = 'Para soporte o consultas específicas, utiliza el formulario de contacto y el equipo responderá tu solicitud.';
        } else if (userText.includes('demo') || userText.includes('demostración') || userText.includes('demostracion')) {
            response = 'Puedes solicitar una demostración institucional desde el formulario de contacto de esta página.';
        } else {
            response = 'Soy un asistente de orientación con respuestas simuladas. Para una consulta específica, envía tu mensaje mediante el formulario de contacto.';
        }

        messages.value.push({ id: Date.now() + 1, text: response, isBot: true });
        scrollToBottom();
    }, 1000);
};

const closeMobileMenu = () => {
    isMobileMenuOpen.value = false;
};
</script>

<template>
    <Head title="CampusFlow | Nexora Tech" />

    <div class="landing-page min-h-screen bg-[#F5F7FB] text-[#0F172A]">
        <header class="fixed inset-x-0 top-0 z-50 border-b border-white/60 bg-white/90 shadow-sm backdrop-blur-xl">
            <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8" aria-label="Navegación principal">
                <a href="#inicio" class="flex items-center gap-3 rounded-lg focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#2563EB]" @click="closeMobileMenu">
                    <span class="brand-mark" aria-hidden="true">
                        <svg viewBox="0 0 48 48" class="h-10 w-10">
                            <rect x="4" y="4" width="40" height="40" rx="10" fill="#2563EB" />
                            <path d="M15 30.5c4.6 4.2 13.4 4.2 18 0M14.5 18.5h19M16 18.5l8-5 8 5M18 20.5v8M24 20.5v8M30 20.5v8" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" />
                        </svg>
                    </span>
                    <span class="leading-tight">
                        <span class="block text-base font-black tracking-tight text-[#0B1F3A]">CampusFlow</span>
                        <span class="block text-xs font-semibold uppercase tracking-[0.18em] text-[#64748B]">por Nexora Tech</span>
                    </span>
                </a>

                <div class="hidden items-center gap-7 lg:flex">
                    <a
                        v-for="item in navigationItems"
                        :key="item.href"
                        :href="item.href"
                        class="rounded-md text-sm font-semibold text-[#334155] transition hover:text-[#2563EB] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#2563EB]"
                    >
                        {{ item.label }}
                    </a>
                </div>

                <div class="hidden items-center gap-3 lg:flex">
                    <Link
                        :href="route('login')"
                        class="rounded-lg bg-[#0B1F3A] px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#12345F] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#2563EB]"
                    >
                        Ingresar al sistema
                    </Link>
                </div>

                <button
                    type="button"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-lg border border-[#E2E8F0] bg-white text-[#0B1F3A] shadow-sm transition hover:border-[#2563EB] hover:text-[#2563EB] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#2563EB] lg:hidden"
                    :aria-expanded="isMobileMenuOpen"
                    aria-controls="mobile-menu"
                    aria-label="Abrir navegación"
                    @click="isMobileMenuOpen = !isMobileMenuOpen"
                >
                    <Bars3Icon v-if="!isMobileMenuOpen" class="h-6 w-6" />
                    <XMarkIcon v-else class="h-6 w-6" />
                </button>
            </nav>

            <div
                v-if="isMobileMenuOpen"
                id="mobile-menu"
                class="border-t border-[#E2E8F0] bg-white px-4 py-4 shadow-lg lg:hidden"
            >
                <div class="mx-auto flex max-w-7xl flex-col gap-2">
                    <a
                        v-for="item in navigationItems"
                        :key="`mobile-${item.href}`"
                        :href="item.href"
                        class="rounded-lg px-3 py-3 text-sm font-bold text-[#0F172A] transition hover:bg-[#F5F7FB] hover:text-[#2563EB] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2563EB]"
                        @click="closeMobileMenu"
                    >
                        {{ item.label }}
                    </a>
                    <Link
                        :href="route('login')"
                        class="mt-2 rounded-lg bg-[#0B1F3A] px-4 py-3 text-center text-sm font-bold text-white transition hover:bg-[#12345F] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2563EB]"
                        @click="closeMobileMenu"
                    >
                        Ingresar al sistema
                    </Link>
                </div>
            </div>
        </header>

        <main>
            <section id="inicio" class="relative isolate overflow-hidden px-4 pb-20 pt-32 sm:px-6 sm:pt-36 lg:px-8 lg:pb-28">
                <div class="tech-grid absolute inset-0 -z-20"></div>
                <div class="absolute left-1/2 top-20 -z-10 h-72 w-72 -translate-x-1/2 rounded-full bg-[#06B6D4]/20 blur-3xl"></div>
                <div class="absolute right-0 top-44 -z-10 h-80 w-80 rounded-full bg-[#2563EB]/15 blur-3xl"></div>

                <div class="mx-auto grid max-w-7xl items-center gap-12 lg:grid-cols-[1.02fr_0.98fr]">
                    <div>
                        <div class="inline-flex items-center gap-2 rounded-full border border-[#E2E8F0] bg-white px-4 py-2 text-sm font-bold text-[#0B1F3A] shadow-sm">
                            <SparklesIcon class="h-4 w-4 text-[#06B6D4]" />
                            Plataforma SaaS universitaria para la UAGRM
                        </div>
                        <h1 class="mt-8 max-w-4xl text-4xl font-black tracking-tight text-[#0B1F3A] sm:text-5xl lg:text-6xl">
                            Gestión universitaria conectada, transparente e inteligente
                        </h1>
                        <p class="mt-6 max-w-2xl text-lg leading-8 text-[#475569]">
                            CampusFlow centraliza la reserva de espacios, el control de activos y la trazabilidad académica en una plataforma diseñada para modernizar la gestión universitaria.
                        </p>
                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            <a
                                href="#solucion"
                                class="inline-flex items-center justify-center rounded-xl bg-[#2563EB] px-5 py-3 text-sm font-black text-white shadow-lg shadow-blue-600/20 transition hover:bg-[#1D4ED8] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#2563EB]"
                            >
                                Explorar CampusFlow
                                <ArrowRightIcon class="ml-2 h-4 w-4" />
                            </a>
                            <Link
                                :href="route('login')"
                                class="inline-flex items-center justify-center rounded-xl border border-[#E2E8F0] bg-white px-5 py-3 text-sm font-black text-[#0B1F3A] shadow-sm transition hover:border-[#2563EB] hover:text-[#2563EB] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#2563EB]"
                            >
                                Ingresar al sistema
                            </Link>
                        </div>
                    </div>

                    <div class="dashboard-preview relative">
                        <div class="absolute -left-4 -top-4 h-24 w-24 rounded-full bg-[#06B6D4]/20 blur-2xl"></div>
                        <div class="rounded-[2rem] border border-white/80 bg-white/90 p-4 shadow-2xl shadow-slate-900/10 backdrop-blur">
                            <div class="rounded-[1.5rem] bg-[#0B1F3A] p-4 text-white">
                                <div class="mb-5 flex items-center justify-between">
                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-cyan-200">CampusFlow</p>
                                        <h2 class="mt-1 text-xl font-black">Panel institucional</h2>
                                    </div>
                                    <div class="flex gap-1.5">
                                        <span class="h-3 w-3 rounded-full bg-[#06B6D4]"></span>
                                        <span class="h-3 w-3 rounded-full bg-[#2563EB]"></span>
                                        <span class="h-3 w-3 rounded-full bg-white/40"></span>
                                    </div>
                                </div>

                                <div class="grid gap-3 sm:grid-cols-3">
                                    <div
                                        v-for="stat in dashboardStats"
                                        :key="stat.label"
                                        class="rounded-2xl border border-white/10 bg-white/10 p-3"
                                    >
                                        <p class="text-xs text-slate-300">{{ stat.label }}</p>
                                        <p class="mt-1 text-sm font-black">{{ stat.value }}</p>
                                    </div>
                                </div>

                                <div class="mt-4 grid gap-4 lg:grid-cols-[1.1fr_0.9fr]">
                                    <div class="rounded-2xl bg-white p-4 text-[#0F172A]">
                                        <div class="mb-4 flex items-center justify-between">
                                            <p class="font-black">Disponibilidad del día</p>
                                            <CalendarDaysIcon class="h-5 w-5 text-[#2563EB]" />
                                        </div>
                                        <div class="space-y-3">
                                            <div
                                                v-for="item in previewSchedule"
                                                :key="`${item.time}-${item.room}`"
                                                class="flex items-center justify-between gap-3 rounded-xl border border-[#E2E8F0] p-3"
                                            >
                                                <div>
                                                    <p class="text-xs font-bold text-[#64748B]">{{ item.time }}</p>
                                                    <p class="text-sm font-black text-[#0B1F3A]">{{ item.room }}</p>
                                                </div>
                                                <span class="rounded-full bg-[#F5F7FB] px-3 py-1 text-xs font-bold text-[#2563EB]">{{ item.status }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="rounded-2xl border border-white/10 bg-white/10 p-4">
                                        <p class="font-black">Flujo de activos</p>
                                        <div class="mt-5 space-y-4">
                                            <div class="flex items-center gap-3">
                                                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#06B6D4]">
                                                    <CubeIcon class="h-5 w-5" />
                                                </span>
                                                <div>
                                                    <p class="text-sm font-bold">Solicitud registrada</p>
                                                    <p class="text-xs text-slate-300">Equipo multimedia</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#2563EB]">
                                                    <QrCodeIcon class="h-5 w-5" />
                                                </span>
                                                <div>
                                                    <p class="text-sm font-bold">Validación QR</p>
                                                    <p class="text-xs text-slate-300">Seguimiento en campo</p>
                                                </div>
                                            </div>
                                            <div class="rounded-2xl bg-[#061324] p-4">
                                                <p class="text-xs text-slate-300">Trazabilidad</p>
                                                <div class="mt-2 h-2 rounded-full bg-white/10">
                                                    <div class="h-2 w-3/4 rounded-full bg-gradient-to-r from-[#06B6D4] to-[#2563EB]"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="solucion" class="bg-white px-4 py-20 sm:px-6 lg:px-8">
                <div class="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[0.95fr_1.05fr]">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-[#2563EB]">Problema y solución</p>
                        <h2 class="mt-4 text-3xl font-black tracking-tight text-[#0B1F3A] sm:text-4xl">
                            Una operación académica ordenada empieza con información conectada
                        </h2>
                        <p class="mt-5 text-base leading-8 text-[#64748B]">
                            CampusFlow concentra procesos que suelen manejarse de forma aislada y los convierte en flujos trazables para aulas, laboratorios, activos y reportes administrativos.
                        </p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl border border-[#E2E8F0] bg-[#F5F7FB] p-6">
                            <h3 class="font-black text-[#0B1F3A]">Antes</h3>
                            <ul class="mt-4 space-y-3 text-sm leading-6 text-[#64748B]">
                                <li v-for="problem in problems" :key="problem" class="flex gap-3">
                                    <XMarkIcon class="mt-1 h-4 w-4 flex-none text-red-500" />
                                    <span>{{ problem }}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="rounded-2xl border border-[#CFFAFE] bg-gradient-to-br from-[#ECFEFF] to-white p-6">
                            <h3 class="font-black text-[#0B1F3A]">Con CampusFlow</h3>
                            <ul class="mt-4 space-y-3 text-sm leading-6 text-[#0F172A]">
                                <li class="flex gap-3">
                                    <CheckCircleIcon class="mt-1 h-4 w-4 flex-none text-[#06B6D4]" />
                                    <span>Disponibilidad, solicitudes y estados en un solo entorno.</span>
                                </li>
                                <li class="flex gap-3">
                                    <CheckCircleIcon class="mt-1 h-4 w-4 flex-none text-[#06B6D4]" />
                                    <span>Flujos de aprobación para reservas y préstamos.</span>
                                </li>
                                <li class="flex gap-3">
                                    <CheckCircleIcon class="mt-1 h-4 w-4 flex-none text-[#06B6D4]" />
                                    <span>Validación QR, seguimiento y reportes administrativos.</span>
                                </li>
                                <li class="flex gap-3">
                                    <CheckCircleIcon class="mt-1 h-4 w-4 flex-none text-[#06B6D4]" />
                                    <span>Acceso diferenciado según roles y permisos institucionales.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <section id="beneficios" class="px-4 py-20 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-7xl">
                    <div class="max-w-3xl">
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-[#2563EB]">Beneficios principales</p>
                        <h2 class="mt-4 text-3xl font-black tracking-tight text-[#0B1F3A] sm:text-4xl">
                            Claridad para usuarios, administración y toma de decisiones
                        </h2>
                    </div>

                    <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <article
                            v-for="benefit in benefits"
                            :key="benefit.title"
                            class="group rounded-2xl border border-[#E2E8F0] bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-xl hover:shadow-slate-900/10"
                        >
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#EFF6FF] text-[#2563EB] transition group-hover:bg-[#2563EB] group-hover:text-white">
                                <component :is="benefit.icon" class="h-6 w-6" />
                            </div>
                            <h3 class="mt-5 text-lg font-black text-[#0B1F3A]">{{ benefit.title }}</h3>
                            <p class="mt-3 text-sm leading-7 text-[#64748B]">{{ benefit.description }}</p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="bg-[#0B1F3A] px-4 py-20 text-white sm:px-6 lg:px-8">
                <div class="mx-auto max-w-7xl">
                    <div class="max-w-3xl">
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-cyan-200">Módulos de CampusFlow</p>
                        <h2 class="mt-4 text-3xl font-black tracking-tight sm:text-4xl">
                            Un ecosistema para espacios, activos y trazabilidad académica
                        </h2>
                    </div>

                    <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <article
                            v-for="module in modules"
                            :key="module.title"
                            class="rounded-2xl border border-white/10 bg-white/[0.08] p-6 backdrop-blur transition hover:bg-white/[0.12]"
                        >
                            <component :is="module.icon" class="h-7 w-7 text-[#06B6D4]" />
                            <h3 class="mt-4 text-lg font-black">{{ module.title }}</h3>
                            <p class="mt-3 text-sm leading-7 text-slate-300">{{ module.description }}</p>
                        </article>
                    </div>
                </div>
            </section>

            <section id="funcionamiento" class="bg-white px-4 py-20 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-7xl">
                    <div class="max-w-3xl">
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-[#2563EB]">Cómo funciona</p>
                        <h2 class="mt-4 text-3xl font-black tracking-tight text-[#0B1F3A] sm:text-4xl">
                            Del acceso institucional al reporte administrativo
                        </h2>
                    </div>

                    <div class="mt-12 grid gap-5 lg:grid-cols-4">
                        <article
                            v-for="step in steps"
                            :key="step.id"
                            class="relative rounded-2xl border border-[#E2E8F0] bg-[#F8FAFC] p-6"
                        >
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#0B1F3A] text-lg font-black text-white">
                                {{ step.id }}
                            </div>
                            <h3 class="mt-5 text-lg font-black text-[#0B1F3A]">{{ step.title }}</h3>
                            <p class="mt-3 text-sm leading-7 text-[#64748B]">{{ step.description }}</p>
                        </article>
                    </div>
                </div>
            </section>

            <section id="tecnologia" class="px-4 py-20 sm:px-6 lg:px-8">
                <div class="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[0.85fr_1.15fr]">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-[#2563EB]">Tecnología</p>
                        <h2 class="mt-4 text-3xl font-black tracking-tight text-[#0B1F3A] sm:text-4xl">
                            Base técnica moderna para una solución académica integrada
                        </h2>
                        <p class="mt-5 text-base leading-8 text-[#64748B]">
                            El proyecto combina tecnologías web, base de datos, capacidades móviles y módulos demostrativos de integración avanzada para explorar escenarios de campus conectado.
                        </p>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div
                            v-for="tech in technologies"
                            :key="tech"
                            class="rounded-2xl border border-[#E2E8F0] bg-white p-5 shadow-sm"
                        >
                            <div class="flex items-center gap-3">
                                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#EFF6FF] text-[#2563EB]">
                                    <DevicePhoneMobileIcon v-if="tech === 'Aplicación móvil'" class="h-5 w-5" />
                                    <CpuChipIcon v-else-if="tech === 'IoT'" class="h-5 w-5" />
                                    <FingerPrintIcon v-else-if="tech === 'Blockchain'" class="h-5 w-5" />
                                    <SparklesIcon v-else-if="tech === 'Inteligencia artificial'" class="h-5 w-5" />
                                    <Squares2X2Icon v-else class="h-5 w-5" />
                                </span>
                                <span class="font-black text-[#0B1F3A]">{{ tech }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="bg-white px-4 py-20 sm:px-6 lg:px-8">
                <div class="mx-auto grid max-w-7xl gap-8 lg:grid-cols-2">
                    <div class="rounded-3xl border border-[#E2E8F0] bg-[#F8FAFC] p-8 sm:p-10">
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-[#2563EB]">Nexora Tech</p>
                        <h2 class="mt-4 text-3xl font-black tracking-tight text-[#0B1F3A]">
                            Soluciones digitales institucionales desde Santa Cruz de la Sierra
                        </h2>
                        <p class="mt-5 text-base leading-8 text-[#64748B]">
                            Nexora Tech es el nombre comercial de Nexora Software S.R.L., orientada al desarrollo de soluciones digitales para procesos institucionales. CampusFlow forma parte de esa visión: construir productos claros, útiles y mantenibles para contextos académicos.
                        </p>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-3xl bg-[#0B1F3A] p-8 text-white">
                            <AcademicCapIcon class="h-8 w-8 text-[#06B6D4]" />
                            <h3 class="mt-5 text-xl font-black">Misión</h3>
                            <p class="mt-3 text-sm leading-7 text-slate-300">
                                Desarrollar herramientas digitales que ayuden a instituciones a organizar procesos, mejorar la trazabilidad y simplificar la experiencia de sus usuarios.
                            </p>
                        </div>
                        <div class="rounded-3xl border border-[#E2E8F0] bg-white p-8">
                            <SparklesIcon class="h-8 w-8 text-[#2563EB]" />
                            <h3 class="mt-5 text-xl font-black text-[#0B1F3A]">Visión</h3>
                            <p class="mt-3 text-sm leading-7 text-[#64748B]">
                                Consolidar productos tecnológicos sostenibles para acompañar la transformación digital de organizaciones educativas e institucionales.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="px-4 py-20 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-7xl">
                    <div class="max-w-3xl">
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-[#2563EB]">Equipo del proyecto</p>
                        <h2 class="mt-4 text-3xl font-black tracking-tight text-[#0B1F3A] sm:text-4xl">
                            Desarrollo, análisis y experiencia del producto
                        </h2>
                    </div>
                    <div class="mt-10 grid gap-5 md:grid-cols-2">
                        <article
                            v-for="member in team"
                            :key="member.name"
                            class="rounded-3xl border border-[#E2E8F0] bg-white p-6 shadow-sm"
                        >
                            <div class="flex items-center gap-5">
                                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-[#2563EB] to-[#06B6D4] text-xl font-black text-white">
                                    {{ member.initials }}
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-[#0B1F3A]">{{ member.name }}</h3>
                                    <p class="mt-1 text-sm font-semibold text-[#64748B]">{{ member.role }}</p>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <section class="px-4 pb-20 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-7xl overflow-hidden rounded-[2rem] bg-[#0B1F3A] p-8 text-white shadow-2xl shadow-slate-900/15 sm:p-12 lg:flex lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-cyan-200">Demostración institucional</p>
                        <h2 class="mt-4 max-w-2xl text-3xl font-black tracking-tight sm:text-4xl">
                            Conoce cómo CampusFlow puede ordenar espacios, activos y trazabilidad académica
                        </h2>
                    </div>
                    <a
                        href="#contacto"
                        class="mt-8 inline-flex items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-black text-[#0B1F3A] transition hover:bg-cyan-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white lg:mt-0"
                    >
                        Solicitar demostración
                        <ArrowRightIcon class="ml-2 h-4 w-4" />
                    </a>
                </div>
            </section>

            <section id="contacto" class="bg-white px-4 py-20 sm:px-6 lg:px-8">
                <div class="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[0.85fr_1.15fr]">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-[#2563EB]">Contacto</p>
                        <h2 class="mt-4 text-3xl font-black tracking-tight text-[#0B1F3A] sm:text-4xl">
                            Solicita información o una demostración de CampusFlow
                        </h2>
                        <div class="mt-8 space-y-4">
                            <div class="flex gap-4 rounded-2xl border border-[#E2E8F0] bg-[#F8FAFC] p-5">
                                <MapPinIcon class="h-6 w-6 flex-none text-[#2563EB]" />
                                <div>
                                    <h3 class="font-black text-[#0B1F3A]">Ubicación general</h3>
                                    <p class="mt-1 text-sm text-[#64748B]">Santa Cruz de la Sierra, Bolivia.</p>
                                </div>
                            </div>
                            <div class="flex gap-4 rounded-2xl border border-[#E2E8F0] bg-[#F8FAFC] p-5">
                                <ChatBubbleLeftRightIcon class="h-6 w-6 flex-none text-[#2563EB]" />
                                <div>
                                    <h3 class="font-black text-[#0B1F3A]">Atención mediante formulario</h3>
                                    <p class="mt-1 text-sm text-[#64748B]">Comparte tu consulta y el equipo revisará tu solicitud.</p>
                                </div>
                            </div>
                            <div class="flex gap-4 rounded-2xl border border-[#E2E8F0] bg-[#F8FAFC] p-5">
                                <PresentationChartLineIcon class="h-6 w-6 flex-none text-[#2563EB]" />
                                <div>
                                    <h3 class="font-black text-[#0B1F3A]">Solicitud de demostración institucional</h3>
                                    <p class="mt-1 text-sm text-[#64748B]">Indica el contexto de tu institución y los módulos de interés.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form @submit.prevent="submitContact" class="rounded-[2rem] border border-[#E2E8F0] bg-[#F8FAFC] p-6 shadow-xl shadow-slate-900/5 sm:p-8" novalidate>
                        <div class="grid gap-6">
                            <div
                                v-if="contactFeedback"
                                :class="[
                                    'flex items-start gap-3 rounded-xl border px-4 py-3 text-sm font-semibold',
                                    contactFeedback.type === 'success'
                                        ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                                        : 'border-amber-200 bg-amber-50 text-amber-700'
                                ]"
                            >
                                <CheckCircleIcon
                                    v-if="contactFeedback.type === 'success'"
                                    class="mt-0.5 h-5 w-5 flex-none"
                                    aria-hidden="true"
                                />
                                <ExclamationTriangleIcon
                                    v-else
                                    class="mt-0.5 h-5 w-5 flex-none"
                                    aria-hidden="true"
                                />
                                <span>{{ contactFeedback.message }}</span>
                            </div>

                            <div>
                                <label for="name" class="block text-sm font-black text-[#0B1F3A]">Nombre</label>
                                <input
                                    v-model="contactForm.name"
                                    type="text"
                                    name="name"
                                    id="name"
                                    required
                                    autocomplete="name"
                                    class="mt-2 block w-full rounded-xl border border-[#E2E8F0] bg-white px-4 py-3 text-[#0F172A] shadow-sm transition placeholder:text-[#94A3B8] focus:border-[#2563EB] focus:outline-none focus:ring-4 focus:ring-blue-600/15"
                                    :class="{ 'border-red-400 focus:border-red-500 focus:ring-red-500/15': contactForm.errors.name }"
                                    placeholder="Tu nombre"
                                >
                                <p v-if="contactForm.errors.name" class="mt-2 text-sm font-semibold text-red-600">{{ contactForm.errors.name }}</p>
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-black text-[#0B1F3A]">Correo electrónico</label>
                                <input
                                    v-model="contactForm.email"
                                    type="email"
                                    name="email"
                                    id="email"
                                    required
                                    autocomplete="email"
                                    class="mt-2 block w-full rounded-xl border border-[#E2E8F0] bg-white px-4 py-3 text-[#0F172A] shadow-sm transition placeholder:text-[#94A3B8] focus:border-[#2563EB] focus:outline-none focus:ring-4 focus:ring-blue-600/15"
                                    :class="{ 'border-red-400 focus:border-red-500 focus:ring-red-500/15': contactForm.errors.email }"
                                    placeholder="tu.correo@institucion.edu"
                                >
                                <p v-if="contactForm.errors.email" class="mt-2 text-sm font-semibold text-red-600">{{ contactForm.errors.email }}</p>
                            </div>
                            <div>
                                <label for="message" class="block text-sm font-black text-[#0B1F3A]">Mensaje</label>
                                <textarea
                                    v-model="contactForm.message"
                                    name="message"
                                    id="message"
                                    rows="5"
                                    required
                                    class="mt-2 block w-full resize-none rounded-xl border border-[#E2E8F0] bg-white px-4 py-3 text-[#0F172A] shadow-sm transition placeholder:text-[#94A3B8] focus:border-[#2563EB] focus:outline-none focus:ring-4 focus:ring-blue-600/15"
                                    :class="{ 'border-red-400 focus:border-red-500 focus:ring-red-500/15': contactForm.errors.message }"
                                    placeholder="Cuéntanos qué módulos te interesan o qué proceso deseas ordenar."
                                ></textarea>
                                <p v-if="contactForm.errors.message" class="mt-2 text-sm font-semibold text-red-600">{{ contactForm.errors.message }}</p>
                            </div>
                            <button
                                type="submit"
                                :disabled="contactForm.processing"
                                class="inline-flex items-center justify-center rounded-xl bg-[#2563EB] px-5 py-3 text-sm font-black text-white shadow-lg shadow-blue-600/20 transition hover:bg-[#1D4ED8] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#2563EB] disabled:cursor-not-allowed disabled:opacity-70"
                            >
                                <span v-if="contactForm.processing">Enviando...</span>
                                <span v-else>Enviar mensaje</span>
                            </button>
                        </div>
                    </form>
                </div>
            </section>
        </main>

        <footer class="border-t border-white/10 bg-[#061324] px-4 py-10 text-white sm:px-6 lg:px-8">
            <div class="mx-auto flex max-w-7xl flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-lg font-black">CampusFlow</p>
                    <p class="mt-1 text-sm text-slate-300">Nexora Tech · Nexora Software S.R.L.</p>
                    <p class="mt-1 text-sm text-slate-400">Santa Cruz de la Sierra, Bolivia · &copy; {{ currentYear }}</p>
                </div>
                <Link
                    :href="route('login')"
                    class="inline-flex items-center justify-center rounded-xl border border-white/15 px-4 py-2.5 text-sm font-black text-white transition hover:bg-white hover:text-[#0B1F3A] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white"
                >
                    Ingresar al sistema
                </Link>
            </div>
        </footer>

        <div class="fixed bottom-5 right-4 z-50 sm:bottom-6 sm:right-6">
            <button
                type="button"
                @click="toggleChat"
                class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#2563EB] text-white shadow-2xl shadow-blue-600/30 transition hover:-translate-y-0.5 hover:bg-[#1D4ED8] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#2563EB]"
                :aria-expanded="isChatOpen"
                aria-label="Abrir asistente de CampusFlow"
            >
                <ChatBubbleLeftRightIcon v-if="!isChatOpen" class="h-7 w-7" />
                <XMarkIcon v-else class="h-7 w-7" />
            </button>

            <div v-if="isChatOpen" class="chat-window absolute bottom-[4.5rem] right-0 flex h-[32rem] max-h-[78vh] w-[calc(100vw-2rem)] max-w-sm flex-col overflow-hidden rounded-[1.5rem] border border-[#E2E8F0] bg-white shadow-2xl shadow-slate-900/20 sm:w-96">
                <div class="bg-[#0B1F3A] p-4 text-white">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/10">
                            <ChatBubbleLeftRightIcon class="h-6 w-6 text-[#06B6D4]" />
                        </div>
                        <div>
                            <h3 class="font-black">Asistente CampusFlow</h3>
                            <p class="text-xs text-slate-300">Orientación simulada del producto</p>
                        </div>
                    </div>
                </div>

                <div ref="chatContainer" class="flex-1 space-y-4 overflow-y-auto bg-[#F8FAFC] p-4">
                    <div
                        v-for="msg in messages"
                        :key="msg.id"
                        class="flex"
                        :class="msg.isBot ? 'justify-start' : 'justify-end'"
                    >
                        <div
                            class="max-w-[82%] rounded-2xl px-4 py-3 text-sm leading-6 shadow-sm"
                            :class="msg.isBot ? 'rounded-tl-sm border border-[#E2E8F0] bg-white text-[#0F172A]' : 'rounded-tr-sm bg-[#2563EB] text-white'"
                        >
                            {{ msg.text }}
                        </div>
                    </div>
                </div>

                <div class="border-t border-[#E2E8F0] bg-white p-3">
                    <form @submit.prevent="sendMessage" class="flex gap-2">
                        <input
                            v-model="chatInput"
                            type="text"
                            placeholder="Escribe tu consulta..."
                            class="min-w-0 flex-1 rounded-full border border-[#E2E8F0] px-4 py-2 text-sm text-[#0F172A] outline-none transition focus:border-[#2563EB] focus:ring-4 focus:ring-blue-600/15"
                            aria-label="Mensaje para el asistente"
                        >
                        <button
                            type="submit"
                            class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-[#2563EB] text-white transition hover:bg-[#1D4ED8] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2563EB] disabled:cursor-not-allowed disabled:opacity-50"
                            :disabled="!chatInput.trim()"
                            aria-label="Enviar mensaje"
                        >
                            <PaperAirplaneIcon class="h-5 w-5" />
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
:global(html) {
    scroll-behavior: smooth;
}

.landing-page {
    font-family: var(--cf-font-sans, Figtree, Arial, sans-serif);
}

.brand-mark,
.dashboard-preview,
.chat-window {
    animation: fadeInUp 0.35s ease-out;
}

.tech-grid {
    background-image:
        linear-gradient(rgba(37, 99, 235, 0.08) 1px, transparent 1px),
        linear-gradient(90deg, rgba(37, 99, 235, 0.08) 1px, transparent 1px);
    background-size: 42px 42px;
    mask-image: linear-gradient(to bottom, black 0%, transparent 82%);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(16px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (prefers-reduced-motion: reduce) {
    :global(html) {
        scroll-behavior: auto;
    }

    .brand-mark,
    .dashboard-preview,
    .chat-window {
        animation: none;
    }

    * {
        transition-duration: 0.01ms !important;
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
    }
}
</style>
