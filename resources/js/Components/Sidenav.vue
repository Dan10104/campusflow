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
            v-if="permiso.estado && shouldShowPermission(permiso)"
            :href="route(permiso.ruta)"
            :class="{ 'nav__body__items__active': isMenuItemActive(permiso.ruta) }"
            :title="permiso.nombre"
          >
            <i :class="permiso.icono" aria-hidden="true"></i>
            <span>{{ permiso.nombre }}</span>
          </Link>

          <Link
            v-if="isAdmin && permiso.estado && permiso.ruta === 'admin.aprobaciones'"
            :href="route('admin.reservas.index')"
            :class="{ 'nav__body__items__active': route().current('admin.reservas.index') }"
            title="Gestión de reservas"
          >
            <i class="bi bi-calendar2-week" aria-hidden="true"></i>
            <span>Gestión de reservas</span>
          </Link>

          <Link
            v-if="isAdmin && permiso.estado && permiso.ruta === 'admin.aprobaciones'"
            :href="route('prestamos.index')"
            :class="{ 'nav__body__items__active': route().current('prestamos.*') }"
            title="Gestión de préstamos"
          >
            <i class="bi bi-box-seam" aria-hidden="true"></i>
            <span>Gestión de préstamos</span>
          </Link>
        </template>

        <Link
          v-if="isAdmin && !hasApprovalCenter"
          :href="route('admin.reservas.index')"
          :class="{ 'nav__body__items__active': route().current('admin.reservas.index') }"
          title="Gestión de reservas"
        >
          <i class="bi bi-calendar2-week" aria-hidden="true"></i>
          <span>Gestión de reservas</span>
        </Link>

        <Link
          v-if="isAdmin && !hasApprovalCenter"
          :href="route('prestamos.index')"
          :class="{ 'nav__body__items__active': route().current('prestamos.*') }"
          title="Gestión de préstamos"
        >
          <i class="bi bi-box-seam" aria-hidden="true"></i>
          <span>Gestión de préstamos</span>
        </Link>
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
const isAdmin = computed(() => Boolean(props.auth?.isAdmin));
const hasApprovalCenter = computed(() =>
  permisos.some((permiso) => permiso.estado && permiso.ruta === 'admin.aprobaciones'),
);

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

const isMenuItemActive = (name) => {
  if (name === 'admin.aprobaciones') {
    return route().current('admin.aprobaciones');
  }

  return isActiveRoute(name);
};

const shouldShowPermission = (permiso) => {
  return !(isAdmin.value && permiso?.ruta === 'prestamos.index');
};
</script>
