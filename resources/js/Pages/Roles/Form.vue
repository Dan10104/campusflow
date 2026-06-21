

<template>
    <AuthenticatedLayout>
  <h2>{{ mode === 'create' ? 'Nuevo rol' : 'Editar rol' }}</h2>
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
        <label class="form-label">Permisos *</label>
        <select
          v-model="form.permisos"
          multiple
          class="form-select"
          :class="{ 'is-invalid': errors.permisos }"
        >
          <option value="">— Seleccionar permisos —</option>
          <option v-for="p in permisos" :key="p.codigo" :value="p.codigo">
            {{ p.nombre }}
          </option>
        </select>
        <div class="invalid-feedback">{{ errors.permisos }}</div>
      </div>

      <button class="btn btn-primary">
        Guardar
      </button>
    </form>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { reactive, toRefs } from 'vue'
import { router } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'

const props = usePage().props
const mode = props.mode            // 'create' o 'edit'
const role = props.role || {}      // si es edit
const permisos = props.permisos || []

const initialPermisoIds = Array.isArray(role.permisos)
  ? role.permisos.map(p => (typeof p === 'object' ? p.codigo : p)).filter(v => v !== undefined && v !== null)
  : [];
  console.log(initialPermisoIds);
const form = reactive({
  nombre: role.nombre || '',
  permisos: initialPermisoIds,
})

const errors = props.errors || {}

function submit() {
  const url   = mode === 'create'
    ? route('roles.store')
    : route('roles.update', role.codigo)
  const method = mode === 'create' ? 'post' : 'put'

  router[method](url, form, {
    onError: () => window.scrollTo(0,0),
    onSuccess: () => router.visit(route('roles.index'))
  })
}
</script>
