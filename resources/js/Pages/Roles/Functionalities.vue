<template>
  <AuthenticatedLayout>
    <h2>Permisos para {{ role.nombre }}</h2>

    <table class="table table-hover table-bordered">
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Ruta</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="p in permisos"
          :key="p.codigo"
          @click="selectPermiso(p)"
          style="cursor: pointer"
        >
          <td>{{ p.nombre }}</td>
          <td>/{{ p.ruta }}</td>
          <td>
            <span :class="p.estado ? 'text-success' : 'text-danger'">
              {{ p.estado ? 'Asignado' : 'No asignado' }}
            </span>
          </td>
        </tr>
      </tbody>
    </table>

    <div class="mt-4">
      <button @click="irAListaRoles" class="btn btn-secondary">
        Guardar y volver a la lista de roles
      </button>
    </div>

    <!-- Modal: NO usar v-if aquí, que elimina el <dialog> -->
    <Modal :show="!!selectedPermiso" @close="selectedPermiso = null">
  <template #default>
    <div class="p-4">
      <h3 class="text-lg font-semibold mb-4">
        Permisos para {{ selectedPermiso?.nombre }}
      </h3>

      <div v-for="p in permisos" :key="p.codigo" class="form-check mb-2">
        <input
          type="checkbox"
          class="form-check-input"
          :value="p.codigo"
          v-model="permisosSeleccionados"
          :id="'check-' + p.codigo"
        />
        <label class="form-check-label ms-1" :for="'check-' + p.codigo">
          {{ p.descripcion }}
        </label>
      </div>

      <div class="mt-4 flex justify-between">
        <button class="btn btn-secondary" @click="selectedPermiso = null">
          Cancelar
        </button>
        <button class="btn btn-primary" @click="guardar">
          Guardar
        </button>
      </div>
    </div>
  </template>
</Modal>


  </AuthenticatedLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { Inertia } from '@inertiajs/inertia'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Modal from '@/Components/Modal.vue'

const { role, permisos } = usePage().props
console.log(permisos);
const selectedPermiso = ref(null)
const permisosSeleccionados = ref([])

function selectPermiso(p) {
  selectedPermiso.value = p
  permisosSeleccionados.value = [...p.permisos_asignados]
  console.log(permisosSeleccionados.value);
}

function irAListaRoles() {
  Inertia.visit(route('roles.index'))
}



function guardar() {
  Inertia.put(
    route('roles.functionalities.update', role.codigo),
    {
      permiso_id: selectedPermiso.value.codigo,
      permisos: permisosSeleccionados.value
    },
    {
      onSuccess: () => {
        selectedPermiso.value = null
      }
    }
  )
}

</script>
