<template>
  <div class="container-fluid">
    <div style="position: absolute; top: 5px">
      <input
        class="form-control me-2 mt-auto mb-auto w-auto"
        type="search"
        v-model="search"
        @input="fetchFuncionalidades"
        placeholder="Home..."
        aria-label="Search"
        style="height: 30px;"
      />
      <ul class="list-group">
        <li
          v-for="f in funcionalidades"
          :key="f.id"
          class="list-group-item fw-bold"
          style="cursor: pointer"
          @click="selectFuncionalidad(f)"
        >
          {{ f.nombre }}
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const search = ref('')
const funcionalidades = ref([])

const fetchFuncionalidades = async () => {
  if (search.value.length < 2) {
    funcionalidades.value = []
    return
  }

  const { data } = await axios.get(route('funcionalidades.search'), {
    params: { q: search.value },
  })

  funcionalidades.value = data
}

const selectFuncionalidad = (funcionalidad) => {
  console.log('Seleccionada:', funcionalidad)
  // Acá podrías emitir un evento o redirigir
  window.location.href = route(funcionalidad.ruta)
}
</script>
