<template>
  <aside class="nav" aria-label="Navegacion principal">
    <div class="nav__header">
      <ApplicationLogo class="nav__brand-mark" />
      <div class="nav__brand-copy">
        <span class="nav__brand-name">CampusFlow</span>
        <span class="nav__brand-maker">Nexora Tech</span>
      </div>
      <button
        type="button"
        class="nav__collapse-button"
        aria-label="Contraer menu"
        title="Contraer menu"
        @click="toggleSidebar"
      >
        <i class="bi bi-layout-sidebar-inset" aria-hidden="true"></i>
      </button>
    </div>

    <div class="nav__user">
      <div class="nav__user-avatar" aria-hidden="true">
        {{ userInitials }}
      </div>
      <div class="nav__user-copy">
        <span class="nav__user-name">{{ user?.nombre || user?.name || 'Usuario' }}</span>
        <span class="nav__user-role">Panel administrativo</span>
      </div>
    </div>

    <nav class="nav__body">
      <div class="nav__body__items">
        <template v-for="permiso in permisos" :key="permiso.codigo">
          <Link
            v-if="permiso.estado"
            :href="route(permiso.ruta)"
            :class="{ 'nav__body__items__active': isActiveRoute(permiso.ruta) }"
            :title="permiso.nombre"
          >
            <i :class="permiso.icono" aria-hidden="true"></i>
            <span>{{ permiso.nombre }}</span>
          </Link>
        </template>
      </div>
    </nav>
  </aside>
</template>

<script setup>
import { computed, inject } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

const { props } = usePage();
const user = props.auth?.user;
const permisos = props.auth?.permisos || [];
const toggleSidebar = inject('toggleSidebar', () => {});

const userInitials = computed(() => {
  const displayName = user?.nombre || user?.name || user?.email || 'Usuario';
  return displayName
    .split(' ')
    .filter(Boolean)
    .slice(0, 2)
    .map((part) => part.charAt(0).toUpperCase())
    .join('');
});

const isActiveRoute = (name) => {
  if (!name) return false;
  return route().current(name) || route().current(`${name.split('.')[0]}.*`);
};
</script>
