<template>
  <header class="app__header">
    <div class="app__header__left">
    </div>
    <div class="app__header__right">
      <ThemeSelector />
      <DarkModeSelector />

      <a
      v-if="$page.props.auth.user"
      :href="route('dashboard')"
      class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
    >
      Dashboard
    </a>

    <template v-else>
      <a
        :href="route('login')"
        class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
      >
        Log in
      </a>
      </template>
    </div>
  </header>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3';
import FuncionalidadSearch from '@/Components/FuncionalidadSearch.vue'
import ThemeSelector from '@/Components/ThemeSelector.vue'
import DarkModeSelector from '@/Components/DarkModeSelector.vue'

const { props } = usePage()
const metaTag = document.querySelector('meta[name="csrf-token"]')
const csrf = props.csrf || (metaTag ? metaTag.getAttribute('content') : '')


import { router } from '@inertiajs/vue3'

const logout = () => {
  router.post(route('logout'))
}

</script>
