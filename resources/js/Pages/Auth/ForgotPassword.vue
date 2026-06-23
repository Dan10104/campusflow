<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { EnvelopeIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout auth-shell>
        <Head title="Recuperar acceso" />

        <div>
            <p class="text-sm font-black uppercase tracking-[0.18em] text-[#2563EB]">Soporte de acceso</p>
            <h1 class="mt-2 text-3xl font-black tracking-normal text-[var(--cf-heading)] sm:text-4xl">Recuperar acceso</h1>
            <p class="mt-3 text-sm leading-6 text-[var(--cf-text-muted)]">
                Escribe el correo registrado y te enviaremos un enlace para restablecer tu contraseña.
            </p>
        </div>

        <div
            v-if="props.status"
            class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"
        >
            {{ props.status }}
        </div>

        <form class="mt-7 space-y-5" @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Correo electrónico" class="!font-bold !text-[var(--cf-heading)]" />
                <div class="relative mt-2">
                    <EnvelopeIcon aria-hidden="true" class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-[var(--cf-text-muted)]" />
                    <TextInput
                        id="email"
                        type="email"
                        class="block h-12 w-full !rounded-xl !border-[var(--cf-border)] !bg-[var(--cf-surface)] !pl-11 !pr-4 !text-[var(--cf-text)] shadow-sm placeholder:text-[var(--cf-text-muted)] focus:!border-[#2563EB] focus:!ring-[#2563EB]/20"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="usuario@correo.com"
                    />
                </div>
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="space-y-3 pt-2">
                <PrimaryButton
                    :class="{ '!opacity-60': form.processing }"
                    :disabled="form.processing"
                    class="!flex !w-full !justify-center !rounded-xl !border-transparent !bg-[#2563EB] !px-5 !py-3.5 !text-sm !font-black !normal-case !tracking-normal !text-white transition hover:!bg-[#1D4ED8] focus:!ring-[#2563EB] disabled:!cursor-not-allowed"
                >
                    {{ form.processing ? 'Enviando enlace...' : 'Enviar enlace de recuperación' }}
                </PrimaryButton>

                <Link
                    :href="route('login')"
                    class="inline-flex w-full items-center justify-center rounded-xl border border-[var(--cf-border)] bg-[var(--cf-surface)] px-5 py-3.5 text-sm font-black text-[var(--cf-heading)] transition hover:border-[#2563EB] hover:text-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2 focus:ring-offset-[var(--cf-surface)]"
                >
                    Volver al inicio de sesión
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
