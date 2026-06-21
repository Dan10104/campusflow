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

      <form :action="route('logout')" method="POST" class="app__logout-form">
        <input type="hidden" name="_token" :value="csrf" />
        <button
          data-bs-toggle="tooltip"
          data-bs-placement="left"
          title="Salir"
          type="submit"
          class="app__icon-button app__icon-button--danger"
          aria-label="Cerrar sesion"
        >
          <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
        </button>
      </form>
    </div>
  </header>
</template>

<script setup>
import { inject } from 'vue';
import { usePage } from '@inertiajs/vue3';
import FuncionalidadSearch from '@/Components/FuncionalidadSearch.vue';
import DarkModeSelector from '@/Components/DarkModeSelector.vue';

const { props } = usePage();
const metaTag = document.querySelector('meta[name="csrf-token"]');
const csrf = props.csrf || (metaTag ? metaTag.getAttribute('content') : '');

const toggleSidebar = inject('toggleSidebar', () => {});
</script>
