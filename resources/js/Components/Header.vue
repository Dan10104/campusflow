<template>
  <header class="app__header">
    <div class="app__header__left">
      <button
        @click="toggleSidebar"
        type="button"
        class="app__icon-button"
        aria-label="Abrir o contraer menu"
        title="Menu"
      >
        <i class="bi bi-list" aria-hidden="true"></i>
      </button>

      <div class="app__header-search">
        <FuncionalidadSearch />
      </div>
    </div>

    <div class="app__header__right">
      <DarkModeSelector />

      <form class="app__logout-form" @submit.prevent="logout">
        <button
          data-bs-toggle="tooltip"
          data-bs-placement="left"
          title="Salir"
          type="submit"
          class="app__icon-button app__icon-button--danger"
          aria-label="Cerrar sesión"
        >
          <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
        </button>
      </form>
    </div>
  </header>
</template>

<script setup>
import { inject } from 'vue';
import { router } from '@inertiajs/vue3';
import FuncionalidadSearch from '@/Components/FuncionalidadSearch.vue';
import DarkModeSelector from '@/Components/DarkModeSelector.vue';

const toggleSidebar = inject('toggleSidebar', () => {});

const logout = () => {
  router.post(route('logout'));
};
</script>
