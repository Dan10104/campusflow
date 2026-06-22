<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { EnvelopeOpenIcon } from '@heroicons/vue/24/outline';

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
    <GuestLayout auth-shell>
        <Head title="Verifica tu correo" />

        <div>
            <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#E0F2FE] text-[#2563EB]">
                <EnvelopeOpenIcon class="h-7 w-7" />
            </div>
            <p class="text-sm font-black uppercase tracking-[0.18em] text-[#2563EB]">Correo pendiente</p>
            <h1 class="mt-2 text-3xl font-black tracking-normal text-[var(--cf-heading)] sm:text-4xl">Verifica tu correo</h1>
            <p class="mt-3 text-sm leading-6 text-[var(--cf-text-muted)]">
                Antes de comenzar, confirma tu dirección de correo desde el enlace que enviamos. Si no lo recibiste, puedes solicitar uno nuevo.
            </p>
        </div>

        <div
            v-if="verificationLinkSent"
            class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"
        >
            Se envió un nuevo enlace de verificación al correo registrado.
        </div>

        <form class="mt-7" @submit.prevent="submit">
            <div class="space-y-3">
                <PrimaryButton
                    :class="{ '!opacity-60': form.processing }"
                    :disabled="form.processing"
                    class="!flex !w-full !justify-center !rounded-xl !border-transparent !bg-[#2563EB] !px-5 !py-3.5 !text-sm !font-black !normal-case !tracking-normal !text-white transition hover:!bg-[#1D4ED8] focus:!ring-[#2563EB] disabled:!cursor-not-allowed"
                >
                    {{ form.processing ? 'Reenviando...' : 'Reenviar correo de verificación' }}
                </PrimaryButton>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="inline-flex w-full items-center justify-center rounded-xl border border-[var(--cf-border)] bg-[var(--cf-surface)] px-5 py-3.5 text-sm font-black text-[var(--cf-heading)] transition hover:border-[#2563EB] hover:text-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2 focus:ring-offset-[var(--cf-surface)]"
                >
                    Cerrar sesión
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
