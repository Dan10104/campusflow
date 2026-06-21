<template>
  <div class="app__body__footer">
    <span class="me-4">
      &copy; {{ new Date().getFullYear() }} {{ appName }} Todos los derechos reservados.
    </span>
  </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3'

const { props } = usePage()
const appName = import.meta.env.VITE_APP_NAME || 'Laravel'
</script>
