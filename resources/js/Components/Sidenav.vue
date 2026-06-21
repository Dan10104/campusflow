<template>
  <div class="nav">
    <div class="nav__header">
      <img class="nav__header__image text-light" :src="logoUrl" alt="mylogo.png" />
      <span>{{ user.nombre }}</span>
    </div>

    <div class="nav__body">
      <div class="nav__body__items">

        <!-- Permisos filtrados -->
        <template v-for="permiso in permisos" :key="permiso.codigo">
          <Link
            v-if="permiso.estado"
            :href="route(permiso.ruta)"
            :class="{ 'nav__body__items__active': isActiveRoute(permiso.ruta) }"
          >
            <i :class="permiso.icono"></i>
            <span>{{ permiso.nombre }}</span>
          </Link>
        </template>
      </div>
    </div>
  </div>
</template>


<script setup>
import { computed } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'

// Datos desde el backend
const page = usePage()
const user = page.props.auth.user
const logoUrl = '/assets/images/logo.jpg'
const { props } = usePage()
// Permisos anidados: role → permisos → funcionalidad
const permisos = props.auth?.permisos || []


console.log('Permisos:', permisos)

const isActiveRoute = (name) => {
  if (!name) return false;
  // Comprobar coincidencia exacta o prefijo de recurso (ej: users.index activa users.create)
  return route().current(name) || route().current(name.split('.')[0] + '.*');
}
</script>
