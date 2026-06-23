<script setup>
import { onMounted, provide, ref, watch } from 'vue';
import Sidenav from '@/Components/Sidenav.vue';
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';

const isSidebarCollapsed = ref(false);

onMounted(() => {
  const savedState = localStorage.getItem('sidebar_collapsed');
  if (savedState !== null) {
    isSidebarCollapsed.value = savedState === 'true';
  }
});

watch(isSidebarCollapsed, (newVal) => {
  localStorage.setItem('sidebar_collapsed', String(newVal));
});

const toggleSidebar = () => {
  isSidebarCollapsed.value = !isSidebarCollapsed.value;
};

provide('toggleSidebar', toggleSidebar);
</script>

<template>
  <div class="campus-shell" :class="{ 'campus-shell--sidebar-collapsed': isSidebarCollapsed }">
    <Sidenav :class="{ 'nav--collapsed': isSidebarCollapsed }" />

    <div id="app-2" class="app default" :class="{ 'app--sidebar-collapsed': isSidebarCollapsed }">
      <Header />

      <main class="app__body" id="app-body">
        <div class="app__body__content">
          <slot />
        </div>
        <Footer />
      </main>
    </div>
  </div>
</template>
