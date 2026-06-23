<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { EnvelopeIcon, EyeIcon, EyeSlashIcon, LockClosedIcon, UserIcon } from '@heroicons/vue/24/outline';
import { ref } from 'vue';

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout auth-shell>
        <Head title="Crear cuenta" />

        <div>
            <p class="text-sm font-black uppercase tracking-[0.18em] text-[#2563EB]">Nueva cuenta</p>
            <h1 class="mt-2 text-3xl font-black tracking-normal text-[var(--cf-heading)] sm:text-4xl">Crear cuenta</h1>
            <p class="mt-3 text-sm leading-6 text-[var(--cf-text-muted)]">
                Registra tus datos para solicitar acceso al entorno académico de CampusFlow.
            </p>
        </div>

        <form class="mt-7 space-y-5" @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Nombre completo" class="!font-bold !text-[var(--cf-heading)]" />
                <div class="relative mt-2">
                    <UserIcon aria-hidden="true" class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-[var(--cf-text-muted)]" />
                    <TextInput
                        id="name"
                        type="text"
                        class="block h-12 w-full !rounded-xl !border-[var(--cf-border)] !bg-[var(--cf-surface)] !pl-11 !pr-4 !text-[var(--cf-text)] shadow-sm placeholder:text-[var(--cf-text-muted)] focus:!border-[#2563EB] focus:!ring-[#2563EB]/20"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Tu nombre"
                    />
                </div>
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

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
                        autocomplete="username"
                        placeholder="usuario@correo.com"
                    />
                </div>
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="Contraseña" class="!font-bold !text-[var(--cf-heading)]" />
                <div class="relative mt-2">
                    <LockClosedIcon aria-hidden="true" class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-[var(--cf-text-muted)]" />
                    <TextInput
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        class="block h-12 w-full !rounded-xl !border-[var(--cf-border)] !bg-[var(--cf-surface)] !pl-11 !pr-12 !text-[var(--cf-text)] shadow-sm placeholder:text-[var(--cf-text-muted)] focus:!border-[#2563EB] focus:!ring-[#2563EB]/20"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                        placeholder="Crea una contraseña"
                    />
                    <button
                        type="button"
                        class="absolute inset-y-0 right-0 flex w-12 items-center justify-center rounded-r-xl text-[var(--cf-text-muted)] transition hover:text-[#2563EB] focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#2563EB]"
                        :aria-label="showPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'"
                        @click="showPassword = !showPassword"
                    >
                        <EyeSlashIcon v-if="showPassword" class="h-5 w-5" />
                        <EyeIcon v-else class="h-5 w-5" />
                    </button>
                </div>
                <p class="mt-2 text-xs font-semibold text-[var(--cf-text-muted)]">Usa una contraseña segura que cumpla las reglas del sistema.</p>
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div>
                <InputLabel for="password_confirmation" value="Confirmar contraseña" class="!font-bold !text-[var(--cf-heading)]" />
                <div class="relative mt-2">
                    <LockClosedIcon aria-hidden="true" class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-[var(--cf-text-muted)]" />
                    <TextInput
                        id="password_confirmation"
                        :type="showPasswordConfirmation ? 'text' : 'password'"
                        class="block h-12 w-full !rounded-xl !border-[var(--cf-border)] !bg-[var(--cf-surface)] !pl-11 !pr-12 !text-[var(--cf-text)] shadow-sm placeholder:text-[var(--cf-text-muted)] focus:!border-[#2563EB] focus:!ring-[#2563EB]/20"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="Repite tu contraseña"
                    />
                    <button
                        type="button"
                        class="absolute inset-y-0 right-0 flex w-12 items-center justify-center rounded-r-xl text-[var(--cf-text-muted)] transition hover:text-[#2563EB] focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#2563EB]"
                        :aria-label="showPasswordConfirmation ? 'Ocultar confirmación' : 'Mostrar confirmación'"
                        @click="showPasswordConfirmation = !showPasswordConfirmation"
                    >
                        <EyeSlashIcon v-if="showPasswordConfirmation" class="h-5 w-5" />
                        <EyeIcon v-else class="h-5 w-5" />
                    </button>
                </div>
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="space-y-3 pt-2">
                <PrimaryButton
                    :class="{ '!opacity-60': form.processing }"
                    :disabled="form.processing"
                    class="!flex !w-full !justify-center !rounded-xl !border-transparent !bg-[#2563EB] !px-5 !py-3.5 !text-sm !font-black !normal-case !tracking-normal !text-white transition hover:!bg-[#1D4ED8] focus:!ring-[#2563EB] disabled:!cursor-not-allowed"
                >
                    {{ form.processing ? 'Creando cuenta...' : 'Crear cuenta' }}
                </PrimaryButton>

                <Link
                    :href="route('login')"
                    class="inline-flex w-full items-center justify-center rounded-xl border border-[var(--cf-border)] bg-[var(--cf-surface)] px-5 py-3.5 text-sm font-black text-[var(--cf-heading)] transition hover:border-[#2563EB] hover:text-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2 focus:ring-offset-[var(--cf-surface)]"
                >
                    Ya tengo una cuenta
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
