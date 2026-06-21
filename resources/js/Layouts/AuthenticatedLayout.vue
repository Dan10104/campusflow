<script setup>
import { ref } from 'vue';
import Sidenav from '@/Components/Sidenav.vue';
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';


import { provide, onMounted, watch } from 'vue';

const showingNavigationDropdown = ref(false);

const isSidebarCollapsed = ref(false);

// Recuperar estado al montar
onMounted(() => {
  const savedState = localStorage.getItem('sidebar_collapsed');
  if (savedState) {
    isSidebarCollapsed.value = savedState === 'true';
  }
});

// Guardar cambios
watch(isSidebarCollapsed, (newVal) => {
  localStorage.setItem('sidebar_collapsed', newVal);
});

const toggleSidebar = () => {
  isSidebarCollapsed.value = !isSidebarCollapsed.value;
};

// Proveer la función a hijos (Header)
provide('toggleSidebar', toggleSidebar);
</script>

<template>
    
  <Sidenav :class="{ 'nav--collapsed': isSidebarCollapsed }" />
  <div id="app-2" class="app default" :class="{ 'app--sidebar-collapsed': isSidebarCollapsed }">
    <Header/>

    <main class="app__body" id="app-body">
        <slot />
      <Footer />
    </main>
  </div>
</template>
