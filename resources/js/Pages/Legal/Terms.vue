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
    title: 'Objeto y finalidad',
    body: 'CampusFlow es un proyecto académico orientado a la gestión de espacios, reservas y activos en la FICCT. Su finalidad es apoyar la organización, trazabilidad y consulta de operaciones académicas relacionadas con aulas, laboratorios y recursos institucionales.',
  },
  {
    title: 'Uso permitido',
    body: 'El sistema debe utilizarse únicamente para fines académicos, administrativos o de demostración vinculados al proyecto. Cada usuario debe operar con su propia cuenta y dentro de los permisos asignados.',
  },
  {
    title: 'Responsabilidades del usuario',
    body: 'El usuario es responsable de mantener la confidencialidad de sus credenciales, cerrar sesión cuando corresponda y utilizar la información disponible de forma responsable.',
  },
  {
    title: 'Exactitud de la información',
    body: 'Las solicitudes, reservas, préstamos, observaciones y datos ingresados deben ser claros, verificables y acordes con la operación que se realiza.',
  },
  {
    title: 'Reservas y préstamos',
    body: 'Las reservas de aulas y solicitudes de activos están sujetas a disponibilidad, revisión, aprobación, check-in, check-out y devolución según el flujo definido en CampusFlow.',
  },
  {
    title: 'Uso indebido',
    body: 'No está permitido manipular datos, simular operaciones no autorizadas, registrar información falsa, intentar evadir controles de acceso o interferir con el funcionamiento del sistema.',
  },
  {
    title: 'Disponibilidad y mantenimiento',
    body: 'CampusFlow puede requerir mantenimiento, ajustes técnicos o interrupciones temporales propias de su condición de proyecto académico y de demostración.',
  },
  {
    title: 'Propiedad intelectual',
    body: 'La identidad, interfaz, código y contenidos propios de CampusFlow y Nexora Tech forman parte del proyecto y no deben copiarse o reutilizarse sin autorización.',
  },
  {
    title: 'Limitación de responsabilidad',
    body: 'CampusFlow organiza información operativa del proyecto, pero no reemplaza decisiones institucionales, controles físicos ni procedimientos administrativos externos al sistema.',
  },
  {
    title: 'Modificaciones',
    body: 'Estos términos pueden actualizarse para reflejar cambios funcionales, académicos o legales del proyecto. Una nueva versión podrá requerir aceptación adicional.',
  },
  {
    title: 'Contacto',
    body: 'Las consultas pueden realizarse mediante los canales definidos por el equipo del proyecto o a través del formulario de contacto disponible en el sitio corporativo.',
  },
];
</script>

<template>
  <GuestLayout>
    <Head title="Términos y Condiciones" />

    <section class="bg-[var(--cf-bg)] px-4 py-10 text-[var(--cf-text)] sm:px-6 lg:px-8">
      <div class="mx-auto max-w-5xl">
        <div class="rounded-2xl border border-[var(--cf-border)] bg-[var(--cf-surface)] p-6 shadow-sm sm:p-8">
          <p class="text-sm font-black uppercase tracking-[0.16em] text-[#2563EB]">CampusFlow</p>
          <h1 class="mt-3 text-3xl font-black text-[var(--cf-heading)] sm:text-4xl">
            Términos y Condiciones
          </h1>
          <p class="mt-4 max-w-3xl text-base leading-7 text-[var(--cf-text-muted)]">
            Este documento regula el uso de CampusFlow como proyecto académico de Nexora Tech para la gestión inteligente de espacios y activos en la FICCT.
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
            También puedes revisar cómo se tratan los datos dentro del sistema.
          </p>
          <Link
            :href="route('policies.privacy')"
            class="inline-flex items-center justify-center rounded-xl border border-[#2563EB] px-4 py-2 text-sm font-black text-[#2563EB] transition hover:bg-[#EFF6FF] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2563EB]"
          >
            Ver Política de Privacidad
          </Link>
        </div>
      </div>
    </section>
  </GuestLayout>
</template>
