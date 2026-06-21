<template>
  <AuthenticatedLayout>
    <h2>
      {{ mode==='create'? 'Nuevo usuario' : 'Editar usuario' }}
    </h2>

    <form @submit.prevent="submit">
      <div class="mb-3">
        <label class="form-label">Nombre *</label>
        <input
          v-model="form.nombre"
          type="text"
          class="form-control"
          :class="{ 'is-invalid': errors.nombre }"
        />
        <div class="invalid-feedback">{{ errors.nombre }}</div>
      </div>

      <div class="mb-3">
        <label class="form-label">CI *</label>
        <input
          v-model="form.ci"
          type="text"
          class="form-control"
          :class="{ 'is-invalid': errors.ci }"
        />
        <div class="invalid-feedback">{{ errors.ci }}</div>
      </div>

      <div class="mb-3">
        <label class="form-label">Email *</label>
        <input
          v-model="form.email"
          type="email"
          class="form-control"
          :class="{ 'is-invalid': errors.email }"
        />
        <div class="invalid-feedback">{{ errors.email }}</div>
      </div>

      <div class="mb-3">
        <label class="form-label">Roles *</label>
        <select
          v-model="form.roles"
          multiple
          class="form-select"
          :class="{ 'is-invalid': errors.roles }"
        >
          <option value="">— Seleccionar roles —</option>
          <option v-for="r in roles" :key="r.codigo" :value="r.codigo">
            {{ r.nombre }}
          </option>
        </select>
        <div class="invalid-feedback">{{ errors.roles }}</div>
      </div>

      <div class="mb-3" v-if="mode==='create'">
        <label class="form-label">Contraseña *</label>
        <input
          v-model="form.password"
          type="password"
          class="form-control"
          :class="{ 'is-invalid': errors.password }"
        />
        <div class="invalid-feedback">{{ errors.password }}</div>
      </div>

      <div class="mb-3" v-if="mode==='create'">
        <label class="form-label">Confirmar contraseña *</label>
        <input
          v-model="form.password_confirmation"
          type="password"
          class="form-control"
          :class="{ 'is-invalid': errors.password_confirmation }"
        />
      </div>

      <button class="btn btn-primary">
        Guardar
      </button>
    </form>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { reactive } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';

const props = usePage().props;
const mode = props.mode;
const user = props.user || {};

const roles = props.roles || [];

// Obtener IDs/códigos de roles ya asociados al usuario (en edición)
const initialRoleIds = Array.isArray(user.roles)
  ? user.roles.map(r => (typeof r === 'object' ? r.codigo : r)).filter(v => v !== undefined && v !== null)
  : [];
  console.log(initialRoleIds);

const form = reactive({
  nombre: user.nombre || '',
  ci:     user.ci || '',
  email:  user.email || '',
  roles: initialRoleIds,
  password: '',
  password_confirmation: '',
});

const errors = props.errors || {};

function submit() {
  const url    = mode==='create'
    ? route('users.store')
    : route('users.update', user.codigo);
  const method = mode==='create' ? 'post' : 'put';

  router[method](url, form, {
    onError: () => window.scrollTo(0,0),
    onSuccess: () => router.visit(route('users.index'))
  });
}
</script>
