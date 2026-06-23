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

    <div v-if="successMessage || warningMessage || errorMessages.length" class="my-3 d-grid gap-2">
      <div
        v-if="successMessage"
        class="flex items-start gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"
      >
        <CheckCircleIcon class="mt-0.5 h-4 w-4 flex-none" aria-hidden="true" />
        <span>{{ successMessage }}</span>
      </div>
      <div
        v-if="warningMessage"
        class="flex items-start gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700"
      >
        <ExclamationTriangleIcon class="mt-0.5 h-4 w-4 flex-none" aria-hidden="true" />
        <span>{{ warningMessage }}</span>
      </div>
      <div
        v-for="message in errorMessages"
        :key="message"
        class="flex items-start gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700"
      >
        <XCircleIcon class="mt-0.5 h-4 w-4 flex-none" aria-hidden="true" />
        <span>{{ message }}</span>
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
                  @click="abrirConfirmacion(u)"
                  :disabled="procesandoEliminacion"
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
    <ConfirmationModal
      :show="mostrarConfirmacion"
      title="Eliminar usuario"
      message="El usuario perdera el acceso al sistema. Confirma que deseas continuar con esta accion."
      confirm-text="Eliminar usuario"
      cancel-text="Volver"
      variant="danger"
      :processing="procesandoEliminacion"
      @confirm="eliminarUsuario"
      @cancel="cerrarConfirmacion"
      @close="cerrarConfirmacion"
    />
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import { router } from '@inertiajs/vue3';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { CheckCircleIcon, ExclamationTriangleIcon, XCircleIcon } from '@heroicons/vue/24/outline';

const page = usePage();
const { users } = page.props;
const usuarioSeleccionado = ref(null);
const mostrarConfirmacion = ref(false);
const procesandoEliminacion = ref(false);
const errorLocal = ref('');

const successMessage = computed(() => page.props.flash?.success || null);
const warningMessage = computed(() => page.props.flash?.warning || null);
const errorMessages = computed(() => [
  errorLocal.value,
  page.props.flash?.error,
  page.props.errors?.user,
  page.props.errors?.usuario,
  ...Object.values(page.props.errors || {}),
].filter((message, index, messages) => Boolean(message) && messages.indexOf(message) === index));

function abrirConfirmacion(user) {
  if (procesandoEliminacion.value) return;

  errorLocal.value = '';
  usuarioSeleccionado.value = user;
  mostrarConfirmacion.value = true;
}

function cerrarConfirmacion() {
  if (procesandoEliminacion.value) return;

  mostrarConfirmacion.value = false;
  usuarioSeleccionado.value = null;
}

function eliminarUsuario() {
  if (!usuarioSeleccionado.value || procesandoEliminacion.value) return;

  router.delete(route('users.destroy', usuarioSeleccionado.value.codigo), {
    preserveScroll: true,
    onStart: () => {
      procesandoEliminacion.value = true;
      errorLocal.value = '';
    },
    onSuccess: () => {
      mostrarConfirmacion.value = false;
      usuarioSeleccionado.value = null;
    },
    onError: (errors) => {
      errorLocal.value = errors?.user || errors?.usuario || errors?.error || 'No se pudo eliminar el usuario.';
    },
    onFinish: () => {
      procesandoEliminacion.value = false;
    },
  });
}
</script>
