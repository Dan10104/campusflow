<template>
  <AuthenticatedLayout>
    <div class="d-lg-flex d-block justify-content-between align-items-center">
      <span class="fs-4">Usuarios</span>
  
      <!-- botón +Nuevo -->
      <Link :href="route('users.create')"
            class="btn btn-primary btn-sm d-flex align-items-center">
        <i class="bi bi-plus-lg me-1"></i> Nuevo
      </Link>
      <div class="d-flex gap-2">
          <a :href="route('users.export')" class="btn btn-secondary btn-sm" target="_blank">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Exportar CSV
          </a>
          <a :href="route('users.report')" class="btn btn-danger btn-sm" target="_blank">
            <i class="bi bi-file-pdf me-1"></i> Reporte PDF
          </a>
      </div>
    </div>

    <div class="card">
      <div class="card-body" style="overflow-x:auto">
        <table class="table table-hover table-bordered" style="white-space:nowrap">
          <thead class="table-primary">
            <tr>
              <th>Nombre</th>
              <th>CI</th>
              <th>Email</th>
              <th>Rol</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="u in users" :key="u.codigo">
              <td>{{ u.nombre }}</td>
              <td>{{ u.ci }}</td>
              <td>{{ u.email }}</td>
              <td>{{ u.roles.join(', ') || '-' }}</td>
              <td class="d-flex gap-2">
                <Link
                  :href="route('users.edit', u.codigo)"
                  class="btn btn-outline-secondary btn-sm"
                >
                  <i class="bi bi-pencil-square"></i>
                </Link>
                <button
                  @click="destroy(u.codigo)"
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
import { router } from '@inertiajs/vue3';
import { Link, usePage } from '@inertiajs/vue3';

const { users } = usePage().props;

function destroy(id) {
  if (confirm('¿Eliminar este usuario?')) {
    router.delete(route('users.destroy', id), {
      onSuccess: () => router.visit(route('users.index')),
      onError: () => alert('Error al eliminar el usuario')
    });
  }
}
</script>
