<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { EyeIcon, EyeSlashIcon, LockClosedIcon } from '@heroicons/vue/24/outline';
import { ref } from 'vue';

const showPassword = ref(false);

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout auth-shell>
        <Head title="Confirma tu identidad" />

        <div>
            <p class="text-sm font-black uppercase tracking-[0.18em] text-[#2563EB]">Zona protegida</p>
            <h1 class="mt-2 text-3xl font-black tracking-normal text-[var(--cf-heading)] sm:text-4xl">Confirma tu identidad</h1>
            <p class="mt-3 text-sm leading-6 text-[var(--cf-text-muted)]">
                Ingresa tu contraseña para continuar en esta zona protegida de CampusFlow.
            </p>
        </div>

        <form class="mt-7 space-y-5" @submit.prevent="submit">
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
                        autocomplete="current-password"
                        autofocus
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

            <PrimaryButton
                :class="{ '!opacity-60': form.processing }"
                :disabled="form.processing"
                class="!flex !w-full !justify-center !rounded-xl !border-transparent !bg-[#2563EB] !px-5 !py-3.5 !text-sm !font-black !normal-case !tracking-normal !text-white transition hover:!bg-[#1D4ED8] focus:!ring-[#2563EB] disabled:!cursor-not-allowed"
            >
                {{ form.processing ? 'Confirmando...' : 'Confirmar contraseña' }}
            </PrimaryButton>
        </form>
    </GuestLayout>
</template>
