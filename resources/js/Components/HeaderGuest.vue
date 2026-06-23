<template>
  <header class="app__header app__header--guest">
    <a :href="route('welcome')" class="app__guest-brand" aria-label="CampusFlow">
      <ApplicationLogo class="app__guest-logo" />
      <span class="app__guest-brand-text">
        <span>CampusFlow</span>
        <small>Nexora Tech</small>
      </span>
    </a>

    <div class="app__header__right">
      <a
        :href="route('policies.terms')"
        class="app__guest-link"
      >
        Términos
      </a>

      <a
        :href="route('policies.privacy')"
        class="app__guest-link"
      >
        Privacidad
      </a>

      <DarkModeSelector />

      <a
        v-if="props.auth?.user"
        :href="route('dashboard')"
        class="app__guest-link"
      >
        Dashboard
      </a>

      <template v-else>
        <a
          :href="route('login')"
          class="app__guest-link"
        >
          Log in
        </a>
      </template>
    </div>
  </header>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import DarkModeSelector from '@/Components/DarkModeSelector.vue';

const { props } = usePage();
</script>
