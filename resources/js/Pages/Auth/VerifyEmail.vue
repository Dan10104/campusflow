<script setup>
import { computed } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { EnvelopeOpenIcon, ShieldCheckIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head title="Verifica tu correo" />

        <section class="relative isolate min-h-[calc(100vh-var(--cf-header-height))] overflow-hidden bg-[var(--cf-bg)] px-4 py-8 text-[var(--cf-text)] sm:px-6 lg:px-8">
            <div
                class="pointer-events-none absolute inset-0 opacity-80"
                style="background-image: linear-gradient(rgba(37, 99, 235, 0.08) 1px, transparent 1px), linear-gradient(90deg, rgba(37, 99, 235, 0.08) 1px, transparent 1px), linear-gradient(135deg, var(--cf-bg), var(--cf-surface)); background-size: 28px 28px, 28px 28px, 100% 100%;"
            ></div>

            <div class="relative mx-auto grid max-w-6xl items-center gap-8 py-4 lg:grid-cols-[0.95fr_1.05fr] lg:py-10">
                <aside class="hidden rounded-lg border border-white/10 bg-[#0B1F3A] p-8 text-white shadow-[0_24px_60px_rgba(11,31,58,0.28)] lg:block">
                    <div class="flex items-center gap-4">
                        <ApplicationLogo class="h-12 w-12" />
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#06B6D4]">CampusFlow</p>
                            <p class="text-sm text-white/70">por Nexora Tech</p>
                        </div>
                    </div>

                    <div class="mt-12">
                        <p class="text-sm font-semibold text-[#06B6D4]">Verificación de cuenta</p>
                        <h1 class="mt-3 text-4xl font-black leading-tight text-white">Confirma tu correo para activar el acceso.</h1>
                        <p class="mt-5 max-w-lg text-base leading-7 text-white/72">
                            La verificación ayuda a proteger tu identidad y mantiene el acceso institucional ordenado.
                        </p>
                    </div>

                    <div class="mt-10 flex items-start gap-3 border-t border-white/10 pt-8">
                        <ShieldCheckIcon class="mt-0.5 h-5 w-5 flex-none text-[#06B6D4]" />
                        <p class="text-sm leading-6 text-white/78">Puedes reenviar el correo de verificación si el enlace anterior expiró o no llegó.</p>
                    </div>
                </aside>

                <div class="mx-auto w-full max-w-md rounded-lg border border-[var(--cf-border)] bg-[var(--cf-surface)] p-6 shadow-[var(--cf-shadow-md)] sm:p-8">
                    <div class="flex items-center gap-3">
                        <ApplicationLogo class="h-11 w-11" />
                        <div>
                            <p class="text-lg font-black text-[var(--cf-heading)]">CampusFlow</p>
                            <p class="text-sm font-semibold text-[var(--cf-text-muted)]">por Nexora Tech</p>
                        </div>
                    </div>

                    <div class="mt-8">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-[#E0F2FE] text-[#2563EB]">
                            <EnvelopeOpenIcon class="h-6 w-6" />
                        </div>
                        <p class="mt-6 text-sm font-bold uppercase tracking-[0.16em] text-[#2563EB]">Correo pendiente</p>
                        <h2 class="mt-2 text-3xl font-black tracking-normal text-[var(--cf-heading)]">Verifica tu correo</h2>
                        <p class="mt-3 text-sm leading-6 text-[var(--cf-text-muted)]">
                            Antes de comenzar, confirma tu dirección de correo desde el enlace que enviamos. Si no lo recibiste, puedes solicitar uno nuevo.
                        </p>
                    </div>

                    <div
                        v-if="verificationLinkSent"
                        class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"
                    >
                        Se envió un nuevo enlace de verificación al correo registrado.
                    </div>

                    <form class="mt-7" @submit.prevent="submit">
                        <div class="flex flex-col gap-4">
                            <PrimaryButton
                                :class="{ '!opacity-60': form.processing }"
                                :disabled="form.processing"
                                class="!flex !w-full !justify-center !rounded-lg !border-transparent !bg-[#2563EB] !px-5 !py-3 !text-sm !font-bold !normal-case !tracking-normal !text-white transition hover:!bg-[#1D4ED8] focus:!ring-[#2563EB] disabled:!cursor-not-allowed"
                            >
                                {{ form.processing ? 'Reenviando...' : 'Reenviar correo de verificación' }}
                            </PrimaryButton>

                            <Link
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="inline-flex w-full items-center justify-center rounded-lg border border-[var(--cf-border)] bg-[var(--cf-surface)] px-5 py-3 text-sm font-bold text-[var(--cf-heading)] transition hover:border-[#2563EB] hover:text-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2 focus:ring-offset-[var(--cf-surface)]"
                            >
                                Cerrar sesión
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>
