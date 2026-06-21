<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import HeaderGuest from '@/Components/HeaderGuest.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
  
    <div id="app-2" class="app app-2 default">
        <HeaderGuest/>

    <main class="app__body" id="app-body">
    <slot />
    </main>
    </div>
</template>
