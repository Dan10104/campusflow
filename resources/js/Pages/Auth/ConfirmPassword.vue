<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
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
    <GuestLayout>
        <Head title="Confirma tu identidad" />

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
                        <p class="text-sm font-semibold text-[#06B6D4]">Zona protegida</p>
                        <h1 class="mt-3 text-4xl font-black leading-tight text-white">Confirma tu acceso antes de continuar.</h1>
                        <p class="mt-5 max-w-lg text-base leading-7 text-white/72">
                            Algunas operaciones requieren verificar nuevamente tu identidad para proteger la información institucional.
                        </p>
                    </div>

                    <div class="mt-10 flex items-start gap-3 border-t border-white/10 pt-8">
                        <LockClosedIcon class="mt-0.5 h-5 w-5 flex-none text-[#06B6D4]" />
                        <p class="text-sm leading-6 text-white/78">Esta confirmación no cambia tu contraseña ni altera los datos enviados al sistema.</p>
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
                        <p class="text-sm font-bold uppercase tracking-[0.16em] text-[#2563EB]">Verificación requerida</p>
                        <h2 class="mt-2 text-3xl font-black tracking-normal text-[var(--cf-heading)]">Confirma tu identidad</h2>
                        <p class="mt-3 text-sm leading-6 text-[var(--cf-text-muted)]">
                            Ingresa tu contraseña para continuar en esta zona protegida de CampusFlow.
                        </p>
                    </div>

                    <form class="mt-7 space-y-5" @submit.prevent="submit">
                        <div>
                            <InputLabel for="password" value="Contraseña" class="!font-bold !text-[var(--cf-heading)]" />
                            <div class="relative mt-2">
                                <TextInput
                                    id="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    class="block h-12 w-full !rounded-lg !border-[var(--cf-border)] !bg-[var(--cf-surface)] px-4 pr-12 !text-[var(--cf-text)] shadow-sm placeholder:text-[var(--cf-text-muted)] focus:!border-[#2563EB] focus:!ring-[#2563EB]/20"
                                    v-model="form.password"
                                    required
                                    autocomplete="current-password"
                                    autofocus
                                    placeholder="Ingresa tu contraseña"
                                />
                                <button
                                    type="button"
                                    class="absolute inset-y-0 right-0 flex w-12 items-center justify-center rounded-r-lg text-[var(--cf-text-muted)] transition hover:text-[#2563EB] focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#2563EB]"
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
                            class="!flex !w-full !justify-center !rounded-lg !border-transparent !bg-[#2563EB] !px-5 !py-3 !text-sm !font-bold !normal-case !tracking-normal !text-white transition hover:!bg-[#1D4ED8] focus:!ring-[#2563EB] disabled:!cursor-not-allowed"
                        >
                            {{ form.processing ? 'Confirmando...' : 'Confirmar contraseña' }}
                        </PrimaryButton>
                    </form>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>
