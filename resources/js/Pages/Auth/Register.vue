<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { EyeIcon, EyeSlashIcon, UserPlusIcon } from '@heroicons/vue/24/outline';
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
    <GuestLayout>
        <Head title="Crear cuenta" />

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
                        <p class="text-sm font-semibold text-[#06B6D4]">Registro institucional</p>
                        <h1 class="mt-3 text-4xl font-black leading-tight text-white">Crea tu acceso para participar en la gestión académica.</h1>
                        <p class="mt-5 max-w-lg text-base leading-7 text-white/72">
                            CampusFlow conecta usuarios, espacios y activos en una interfaz pensada para operaciones universitarias claras.
                        </p>
                    </div>

                    <div class="mt-10 flex items-start gap-3 border-t border-white/10 pt-8">
                        <UserPlusIcon class="mt-0.5 h-5 w-5 flex-none text-[#06B6D4]" />
                        <p class="text-sm leading-6 text-white/78">Usa datos reales y una contraseña segura. Las reglas finales las valida el sistema.</p>
                    </div>
                </aside>

                <div class="mx-auto w-full max-w-lg rounded-lg border border-[var(--cf-border)] bg-[var(--cf-surface)] p-6 shadow-[var(--cf-shadow-md)] sm:p-8">
                    <div class="flex items-center gap-3">
                        <ApplicationLogo class="h-11 w-11" />
                        <div>
                            <p class="text-lg font-black text-[var(--cf-heading)]">CampusFlow</p>
                            <p class="text-sm font-semibold text-[var(--cf-text-muted)]">por Nexora Tech</p>
                        </div>
                    </div>

                    <div class="mt-8">
                        <p class="text-sm font-bold uppercase tracking-[0.16em] text-[#2563EB]">Nueva cuenta</p>
                        <h2 class="mt-2 text-3xl font-black tracking-normal text-[var(--cf-heading)]">Crear cuenta</h2>
                        <p class="mt-3 text-sm leading-6 text-[var(--cf-text-muted)]">
                            Registra tus datos para solicitar acceso al entorno académico de CampusFlow.
                        </p>
                    </div>

                    <form class="mt-7 space-y-5" @submit.prevent="submit">
                        <div>
                            <InputLabel for="name" value="Nombre completo" class="!font-bold !text-[var(--cf-heading)]" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-2 block h-12 w-full !rounded-lg !border-[var(--cf-border)] !bg-[var(--cf-surface)] px-4 !text-[var(--cf-text)] shadow-sm placeholder:text-[var(--cf-text-muted)] focus:!border-[#2563EB] focus:!ring-[#2563EB]/20"
                                v-model="form.name"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder="Tu nombre"
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div>
                            <InputLabel for="email" value="Correo electronico" class="!font-bold !text-[var(--cf-heading)]" />
                            <TextInput
                                id="email"
                                type="email"
                                class="mt-2 block h-12 w-full !rounded-lg !border-[var(--cf-border)] !bg-[var(--cf-surface)] px-4 !text-[var(--cf-text)] shadow-sm placeholder:text-[var(--cf-text-muted)] focus:!border-[#2563EB] focus:!ring-[#2563EB]/20"
                                v-model="form.email"
                                required
                                autocomplete="username"
                                placeholder="usuario@correo.com"
                            />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <div>
                            <InputLabel for="password" value="Contraseña" class="!font-bold !text-[var(--cf-heading)]" />
                            <div class="relative mt-2">
                                <TextInput
                                    id="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    class="block h-12 w-full !rounded-lg !border-[var(--cf-border)] !bg-[var(--cf-surface)] px-4 pr-12 !text-[var(--cf-text)] shadow-sm placeholder:text-[var(--cf-text-muted)] focus:!border-[#2563EB] focus:!ring-[#2563EB]/20"
                                    v-model="form.password"
                                    required
                                    autocomplete="new-password"
                                    placeholder="Crea una contraseña"
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
                            <p class="mt-2 text-xs font-semibold text-[var(--cf-text-muted)]">Usa una contraseña que puedas recordar y que cumpla las reglas del sistema.</p>
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <div>
                            <InputLabel for="password_confirmation" value="Confirmar contraseña" class="!font-bold !text-[var(--cf-heading)]" />
                            <div class="relative mt-2">
                                <TextInput
                                    id="password_confirmation"
                                    :type="showPasswordConfirmation ? 'text' : 'password'"
                                    class="block h-12 w-full !rounded-lg !border-[var(--cf-border)] !bg-[var(--cf-surface)] px-4 pr-12 !text-[var(--cf-text)] shadow-sm placeholder:text-[var(--cf-text-muted)] focus:!border-[#2563EB] focus:!ring-[#2563EB]/20"
                                    v-model="form.password_confirmation"
                                    required
                                    autocomplete="new-password"
                                    placeholder="Repite tu contraseña"
                                />
                                <button
                                    type="button"
                                    class="absolute inset-y-0 right-0 flex w-12 items-center justify-center rounded-r-lg text-[var(--cf-text-muted)] transition hover:text-[#2563EB] focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#2563EB]"
                                    :aria-label="showPasswordConfirmation ? 'Ocultar confirmacion' : 'Mostrar confirmacion'"
                                    @click="showPasswordConfirmation = !showPasswordConfirmation"
                                >
                                    <EyeSlashIcon v-if="showPasswordConfirmation" class="h-5 w-5" />
                                    <EyeIcon v-else class="h-5 w-5" />
                                </button>
                            </div>
                            <InputError class="mt-2" :message="form.errors.password_confirmation" />
                        </div>

                        <div class="flex flex-col gap-4 pt-2 sm:flex-row sm:items-center sm:justify-between">
                            <Link
                                :href="route('login')"
                                class="text-sm font-bold text-[#2563EB] transition hover:text-[#0B1F3A] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2 focus:ring-offset-[var(--cf-surface)]"
                            >
                                Volver al inicio de sesión
                            </Link>

                            <PrimaryButton
                                :class="{ '!opacity-60': form.processing }"
                                :disabled="form.processing"
                                class="!flex !justify-center !rounded-lg !border-transparent !bg-[#2563EB] !px-5 !py-3 !text-sm !font-bold !normal-case !tracking-normal !text-white transition hover:!bg-[#1D4ED8] focus:!ring-[#2563EB] disabled:!cursor-not-allowed"
                            >
                                {{ form.processing ? 'Creando cuenta...' : 'Crear cuenta' }}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>
