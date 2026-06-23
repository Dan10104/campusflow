<template>

    <AuthenticatedLayout>
    <div class="d-lg-flex d-block justify-content-between align-items-center">
      <span class="fs-4">Roles</span>
      <Link :href="route('roles.create')"
            class="btn btn-primary btn-sm d-flex align-items-center">
        <i class="bi bi-plus-lg me-1"></i> Nuevo
      </Link>
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
              @click="abrirConfirmacion(role)"
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
      title="Eliminar rol"
      message="El rol será eliminado permanentemente. Esta acción solo puede completarse si el rol no está siendo utilizado por usuarios."
      confirm-text="Eliminar rol"
      cancel-text="Volver"
      variant="danger"
      :processing="procesandoEliminacion"
      @confirm="eliminarRol"
      @cancel="cerrarConfirmacion"
      @close="cerrarConfirmacion"
    />
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import { Link, usePage, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue';
import { CheckCircleIcon, ExclamationTriangleIcon, XCircleIcon } from '@heroicons/vue/24/outline';

const page = usePage();
const { roles } = page.props;
const rolSeleccionado = ref(null);
const mostrarConfirmacion = ref(false);
const procesandoEliminacion = ref(false);
const errorLocal = ref('');

const successMessage = computed(() => page.props.flash?.success || null);
const warningMessage = computed(() => page.props.flash?.warning || null);
const errorMessages = computed(() => [
  errorLocal.value,
  page.props.flash?.error,
  page.props.errors?.role,
  page.props.errors?.rol,
  ...Object.values(page.props.errors || {}),
].filter((message, index, messages) => Boolean(message) && messages.indexOf(message) === index));

function abrirConfirmacion(role) {
  if (procesandoEliminacion.value) return;

  errorLocal.value = '';
  rolSeleccionado.value = role;
  mostrarConfirmacion.value = true;
}

function cerrarConfirmacion() {
  if (procesandoEliminacion.value) return;

  mostrarConfirmacion.value = false;
  rolSeleccionado.value = null;
}

function eliminarRol() {
  if (!rolSeleccionado.value || procesandoEliminacion.value) return;

  router.delete(route('roles.destroy', rolSeleccionado.value.codigo), {
    preserveScroll: true,
    onStart: () => {
      procesandoEliminacion.value = true;
      errorLocal.value = '';
    },
    onSuccess: () => {
      mostrarConfirmacion.value = false;
      rolSeleccionado.value = null;
    },
    onError: (errors) => {
      errorLocal.value = errors?.role || errors?.rol || errors?.error || 'No se pudo eliminar el rol.';
    },
    onFinish: () => {
      procesandoEliminacion.value = false;
    },
  });
}
</script>
