<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import InputError from '@/Components/InputError.vue';

defineProps({
  versions: {
    type: Object,
    required: true,
  },
});

const form = useForm({
  accepted_terms: false,
  accepted_privacy: false,
});

const submit = () => {
  form.post(route('policies.accept.store'), {
    preserveScroll: true,
  });
};
</script>

<template>
  <GuestLayout>
    <Head title="Aceptación de políticas" />

    <section class="min-h-[calc(100vh-4.75rem)] bg-[var(--cf-bg)] px-4 py-10 sm:px-6 lg:px-8">
      <div class="mx-auto flex max-w-5xl flex-col gap-6 lg:grid lg:grid-cols-[0.92fr_1.08fr] lg:items-center">
        <aside class="rounded-3xl border border-[#BFDBFE] bg-[#EFF6FF] p-6 text-[#0F172A] shadow-sm sm:p-8">
          <ApplicationLogo class="h-14 w-14" />
          <p class="mt-5 text-sm font-black uppercase tracking-[0.16em] text-[#2563EB]">CampusFlow</p>
          <h1 class="mt-3 text-3xl font-black leading-tight sm:text-4xl">
            Antes de continuar
          </h1>
          <p class="mt-4 text-sm leading-7 text-[#475569]">
            Para proteger la trazabilidad del sistema y dejar evidencia de uso responsable, cada nueva sesión requiere aceptar los documentos legales vigentes.
          </p>
          <div class="mt-6 grid gap-3 text-sm font-bold text-[#334155]">
            <div class="rounded-2xl border border-[#DBEAFE] bg-white p-4">
              Términos y Condiciones v{{ versions.terms }}
            </div>
            <div class="rounded-2xl border border-[#DBEAFE] bg-white p-4">
              Política de Privacidad v{{ versions.privacy }}
            </div>
          </div>
        </aside>

        <form
          class="rounded-3xl border border-[var(--cf-border)] bg-[var(--cf-surface)] p-6 shadow-sm sm:p-8"
          @submit.prevent="submit"
        >
          <div>
            <p class="text-sm font-black uppercase tracking-[0.16em] text-[#2563EB]">Aceptación requerida</p>
            <h2 class="mt-3 text-2xl font-black text-[var(--cf-heading)]">
              Revisa y confirma ambos documentos
            </h2>
            <p class="mt-3 text-sm leading-7 text-[var(--cf-text-muted)]">
              Puedes abrir los documentos en otra pestaña antes de aceptar. Esta aceptación se conservará solo para la sesión actual.
            </p>
          </div>

          <div class="mt-7 grid gap-4">
            <label class="flex gap-3 rounded-2xl border border-[var(--cf-border)] bg-[#F8FAFC] p-4 text-sm font-bold text-[#334155] transition focus-within:border-[#2563EB]">
              <input
                v-model="form.accepted_terms"
                type="checkbox"
                class="mt-1 h-5 w-5 rounded border-[#CBD5E1] text-[#2563EB] focus:ring-[#2563EB]"
              >
              <span>
                He leído y acepto los
                <a
                  :href="route('policies.terms')"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="font-black text-[#2563EB] underline-offset-4 hover:underline"
                >
                  Términos y Condiciones
                </a>.
              </span>
            </label>
            <InputError :message="form.errors.accepted_terms" />

            <label class="flex gap-3 rounded-2xl border border-[var(--cf-border)] bg-[#F8FAFC] p-4 text-sm font-bold text-[#334155] transition focus-within:border-[#2563EB]">
              <input
                v-model="form.accepted_privacy"
                type="checkbox"
                class="mt-1 h-5 w-5 rounded border-[#CBD5E1] text-[#2563EB] focus:ring-[#2563EB]"
              >
              <span>
                He leído y acepto la
                <a
                  :href="route('policies.privacy')"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="font-black text-[#2563EB] underline-offset-4 hover:underline"
                >
                  Política de Privacidad
                </a>.
              </span>
            </label>
            <InputError :message="form.errors.accepted_privacy" />
          </div>

          <div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <Link
              :href="route('logout')"
              method="post"
              as="button"
              class="inline-flex items-center justify-center rounded-xl border border-[var(--cf-border)] px-5 py-3 text-sm font-black text-[var(--cf-text)] transition hover:border-[#2563EB] hover:text-[#2563EB] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2563EB]"
            >
              Cerrar sesión
            </Link>
            <button
              type="submit"
              :disabled="form.processing || !form.accepted_terms || !form.accepted_privacy"
              class="inline-flex items-center justify-center rounded-xl bg-[#2563EB] px-5 py-3 text-sm font-black text-white shadow-sm transition hover:bg-[#1D4ED8] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2563EB] disabled:cursor-not-allowed disabled:bg-[#94A3B8]"
            >
              {{ form.processing ? 'Registrando aceptación...' : 'Aceptar y continuar' }}
            </button>
          </div>
        </form>
      </div>
    </section>
  </GuestLayout>
</template>
