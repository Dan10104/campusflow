<template>

    <AuthenticatedLayout>
    <div class="d-lg-flex d-block justify-content-between align-items-center">
      <span class="fs-4">Roles</span>
      <Link :href="route('roles.create')"
            class="btn btn-primary btn-sm d-flex align-items-center">
        <i class="bi bi-plus-lg me-1"></i> Nuevo
      </Link>
    </div>
    <div class="card" wire:ignore.self>
    <div class="card-body" style="overflow-x: auto">
    <table class="table table-hover table-bordered" style="white-space: nowrap" wire:ignore.self>
      <thead class="table-primary">
        <tr>
            <th scope="col" class="text-success">Nombre</th>
            <th scope="col" class="text-success">Permisos</th>
            <th scope="col" class="text-success"></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="role in roles" :key="role.codigo">
          <td>{{ role.nombre }}</td>
          <td>{{ role.permisos.map(p => p.nombre).join(', ') || '-' }}</td>
          <td class="d-flex gap-2">
            <Link :href="route('roles.edit', role.codigo)" class="btn btn-outline-secondary btn-sm">
              <i class="bi bi-pencil-square"></i>
            </Link>
            <button
              @click="destroy(role.codigo)"
              class="btn btn-outline-danger btn-sm"
            >
              <i class="bi bi-trash"></i>
            </button>
          </td>
        </tr>
      </tbody>
    </table>
    </div>
    </div>
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, usePage, router } from '@inertiajs/vue3'

const { roles } = usePage().props

function destroy(id) {
  if (confirm('¿Eliminar este rol?')) {
    router.delete(route('roles.destroy', id), {
  onSuccess: () => router.visit(route('roles.index')),
  onError: () => alert('Error al eliminar el rol'),
});
  }
}
</script>
