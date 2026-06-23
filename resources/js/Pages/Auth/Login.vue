<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { EnvelopeIcon, EyeIcon, EyeSlashIcon, LockClosedIcon } from '@heroicons/vue/24/outline';
import { ref } from 'vue';

const props = defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const showPassword = ref(false);

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout auth-shell>
        <Head title="Iniciar sesión" />

        <div>
            <p class="text-sm font-black uppercase tracking-[0.18em] text-[#2563EB]">Acceso institucional</p>
            <h1 class="mt-2 text-3xl font-black tracking-normal text-[var(--cf-heading)] sm:text-4xl">Iniciar sesión</h1>
            <p class="mt-3 text-sm leading-6 text-[var(--cf-text-muted)]">
                Ingresa con tu cuenta para administrar reservas, espacios y activos académicos en CampusFlow.
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

            <div>
                <div class="flex items-center justify-between gap-4">
                    <InputLabel for="password" value="Contraseña" class="!font-bold !text-[var(--cf-heading)]" />
                    <Link
                        v-if="props.canResetPassword"
                        :href="route('password.request')"
                        class="text-sm font-bold text-[#2563EB] transition hover:text-[#0B1F3A] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2 focus:ring-offset-[var(--cf-surface)]"
                    >
                        ¿Olvidaste tu contraseña?
                    </Link>
                </div>
                <div class="relative mt-2">
                    <LockClosedIcon aria-hidden="true" class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-[var(--cf-text-muted)]" />
                    <TextInput
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        class="block h-12 w-full !rounded-xl !border-[var(--cf-border)] !bg-[var(--cf-surface)] !pl-11 !pr-12 !text-[var(--cf-text)] shadow-sm placeholder:text-[var(--cf-text-muted)] focus:!border-[#2563EB] focus:!ring-[#2563EB]/20"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        placeholder="Ingresa tu contraseña"
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
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <label class="flex items-center gap-3 text-sm font-semibold text-[var(--cf-text-muted)]">
                <Checkbox
                    name="remember"
                    v-model:checked="form.remember"
                    class="!border-[var(--cf-border-strong)] !text-[#2563EB] focus:!ring-[#2563EB]/25"
                />
                <span>Recordar sesión</span>
            </label>

            <div class="space-y-3 pt-2">
                <PrimaryButton
                    :disabled="form.processing"
                    class="!flex !w-full !justify-center !rounded-xl !border-transparent !bg-[#2563EB] !px-5 !py-3.5 !text-sm !font-black !normal-case !tracking-normal !text-white transition hover:!bg-[#1D4ED8] focus:!ring-[#2563EB] disabled:!cursor-not-allowed disabled:!opacity-60"
                >
                    {{ form.processing ? 'Ingresando...' : 'Ingresar' }}
                </PrimaryButton>

                <Link
                    :href="route('register')"
                    class="inline-flex w-full items-center justify-center rounded-xl border border-[var(--cf-border)] bg-[var(--cf-surface)] px-5 py-3.5 text-sm font-black text-[var(--cf-heading)] transition hover:border-[#2563EB] hover:text-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2 focus:ring-offset-[var(--cf-surface)]"
                >
                    Crear cuenta
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
