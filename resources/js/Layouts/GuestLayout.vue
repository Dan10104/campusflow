<script setup>
import HeaderGuest from '@/Components/HeaderGuest.vue';
</script>

<template>
  <div id="app-2" class="app app-2 app--guest default">
    <HeaderGuest />

    <main class="app__body app__body--guest" id="app-body">
      <slot />
    </main>
  </div>
</template>
