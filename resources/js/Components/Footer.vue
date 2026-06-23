<template>
  <footer class="app__body__footer">
    <span class="app__footer-product">CampusFlow</span>
    <span class="app__footer-divider" aria-hidden="true"></span>
    <span>Nexora Tech</span>
    <span class="app__footer-year">&copy; {{ year }}</span>
    <a :href="route('policies.terms')" class="app__footer-link">Términos</a>
    <a :href="route('policies.privacy')" class="app__footer-link">Privacidad</a>
  </footer>
</template>

<script setup>
const year = new Date().getFullYear();
</script>
