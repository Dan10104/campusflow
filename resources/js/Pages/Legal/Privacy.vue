<script setup>
import { Head, Link } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

defineProps({
  version: {
    type: String,
    required: true,
  },
  effectiveDate: {
    type: String,
    required: true,
  },
});

const sections = [
  {
    title: 'Datos de identificación y cuenta',
    body: 'CampusFlow almacena datos necesarios para identificar al usuario dentro del sistema, como nombre, CI, correo, roles asignados y datos técnicos asociados a la cuenta.',
  },
  {
    title: 'Reservas y préstamos',
    body: 'El sistema registra solicitudes, estados, fechas, aulas, activos, observaciones y relaciones necesarias para gestionar reservas de espacios y préstamos de activos.',
  },
  {
    title: 'Check-in, checkout y actividad',
    body: 'CampusFlow puede registrar check-in, checkout, devoluciones, acciones administrativas, bitácora y eventos operativos vinculados al uso de aulas y activos.',
  },
  {
    title: 'Dirección IP y datos técnicos',
    body: 'Para fines de seguridad, auditoría y trazabilidad, se pueden registrar dirección IP, agente de usuario, identificadores de sesión y datos técnicos de la operación.',
  },
  {
    title: 'Notificaciones',
    body: 'Cuando la funcionalidad esté habilitada, CampusFlow puede utilizar tokens de Firebase y registros de notificación para comunicar cambios relevantes al usuario.',
  },
  {
    title: 'Finalidad del tratamiento',
    body: 'Los datos se utilizan para autenticar usuarios, aplicar permisos, gestionar reservas y préstamos, auditar operaciones, mejorar la trazabilidad y demostrar el funcionamiento del proyecto.',
  },
  {
    title: 'Control de acceso',
    body: 'El acceso a la información se limita según roles, permisos y controles del sistema. Las acciones administrativas se reservan a perfiles autorizados.',
  },
  {
    title: 'Conservación de datos',
    body: 'Los registros se conservan mientras sean necesarios para trazabilidad, demostración académica, auditoría del flujo o mantenimiento del proyecto.',
  },
  {
    title: 'Seguridad',
    body: 'CampusFlow aplica autenticación, control de permisos y medidas de registro para reducir accesos indebidos. Ningún sistema debe considerarse libre de riesgos.',
  },
  {
    title: 'Derechos del usuario',
    body: 'El usuario puede solicitar revisión, corrección o aclaración de sus datos a través de los canales definidos por el equipo del proyecto.',
  },
  {
    title: 'Contacto',
    body: 'Las consultas sobre privacidad pueden realizarse mediante los canales definidos por el equipo del proyecto o el formulario de contacto del sitio corporativo.',
  },
];
</script>

<template>
  <GuestLayout>
    <Head title="Política de Privacidad" />

    <section class="bg-[var(--cf-bg)] px-4 py-10 text-[var(--cf-text)] sm:px-6 lg:px-8">
      <div class="mx-auto max-w-5xl">
        <div class="rounded-2xl border border-[var(--cf-border)] bg-[var(--cf-surface)] p-6 shadow-sm sm:p-8">
          <p class="text-sm font-black uppercase tracking-[0.16em] text-[#2563EB]">CampusFlow</p>
          <h1 class="mt-3 text-3xl font-black text-[var(--cf-heading)] sm:text-4xl">
            Política de Privacidad
          </h1>
          <p class="mt-4 max-w-3xl text-base leading-7 text-[var(--cf-text-muted)]">
            Esta política explica qué datos utiliza CampusFlow, con qué finalidad y cómo se protegen dentro del alcance del proyecto académico.
          </p>

          <div class="mt-6 flex flex-wrap gap-3 text-sm font-bold text-[var(--cf-text)]">
            <span class="rounded-full border border-[var(--cf-border)] bg-[#F8FAFC] px-4 py-2">
              Versión {{ version }}
            </span>
            <span class="rounded-full border border-[var(--cf-border)] bg-[#F8FAFC] px-4 py-2">
              Vigente desde {{ effectiveDate }}
            </span>
          </div>
        </div>

        <div class="mt-6 grid gap-4">
          <article
            v-for="section in sections"
            :key="section.title"
            class="rounded-2xl border border-[var(--cf-border)] bg-[var(--cf-surface)] p-5 shadow-sm"
          >
            <h2 class="text-lg font-black text-[var(--cf-heading)]">{{ section.title }}</h2>
            <p class="mt-2 text-sm leading-7 text-[var(--cf-text-muted)]">{{ section.body }}</p>
          </article>
        </div>

        <div class="mt-6 flex flex-col gap-3 rounded-2xl border border-[var(--cf-border)] bg-[var(--cf-surface)] p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
          <p class="text-sm font-bold text-[var(--cf-text-muted)]">
            Revisa también las reglas de uso del sistema.
          </p>
          <Link
            :href="route('policies.terms')"
            class="inline-flex items-center justify-center rounded-xl border border-[#2563EB] px-4 py-2 text-sm font-black text-[#2563EB] transition hover:bg-[#EFF6FF] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2563EB]"
          >
            Ver Términos y Condiciones
          </Link>
        </div>
      </div>
    </section>
  </GuestLayout>
</template>
