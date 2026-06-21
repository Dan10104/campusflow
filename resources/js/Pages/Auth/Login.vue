<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    console.log(form.data());
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

</script>

<template>
  <GuestLayout>
    <Head title="Log in" />

    <div class="login-container mx-auto mt-5" style="max-width: 400px;">
      <h3 class="mb-4">Iniciar Sesión</h3>

      <form @submit.prevent="submit">
        <div class="mb-3">
          <InputLabel for="email" value="Email" />
          <TextInput
            id="email"
            type="email"
            class="form-control"
            v-model="form.email"
            required autofocus autocomplete="username"
          />
          <InputError :message="form.errors.email" />
        </div>

        <div class="mb-3">
          <InputLabel for="password" value="Password" />
          <TextInput
            id="password"
            type="password"
            class="form-control"
            v-model="form.password"
            required autocomplete="current-password"
          />
          <InputError :message="form.errors.password" />
        </div>

        <div class="form-check mb-3">
          <Checkbox name="remember" v-model:checked="form.remember" />
          <label class="form-check-label ms-2">Remember me</label>
        </div>

        <div class="d-flex justify-content-between align-items-center">
          <Link
            v-if="canResetPassword"
            :href="route('password.request')"
            class="text-decoration-underline"
          >
            Forgot your password?
          </Link>
          <PrimaryButton :disabled="form.processing" class="btn btn-primary">
            Log in
          </PrimaryButton>
        </div>
      </form>
      </div>
    </GuestLayout>
</template>
