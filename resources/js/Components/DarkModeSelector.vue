<template>
  <div class="theme-mode" role="group" aria-label="Modo de color">
    <button
      v-for="option in options"
      :key="option.value"
      type="button"
      class="theme-mode__button"
      :class="{ 'theme-mode__button--active': mode === option.value }"
      :aria-pressed="mode === option.value"
      :title="option.label"
      @click="setMode(option.value)"
    >
      <i :class="option.icon" aria-hidden="true"></i>
      <span>{{ option.short }}</span>
    </button>
  </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';

const storageKey = 'campusflow_color_mode';
const legacyDarkKey = 'dark-theme';
const legacyAutoKey = 'dark-theme-auto';
const validModes = ['light', 'dark', 'system'];
const mode = ref('system');
const prefersDark = ref(false);
let mediaQuery;

const options = [
  { value: 'light', label: 'Modo claro', short: 'Claro', icon: 'bi bi-sun' },
  { value: 'dark', label: 'Modo oscuro', short: 'Oscuro', icon: 'bi bi-moon-stars' },
  { value: 'system', label: 'Modo automatico', short: 'Auto', icon: 'bi bi-circle-half' },
];

const activeTheme = computed(() => {
  if (mode.value === 'system') {
    return prefersDark.value ? 'dark' : 'light';
  }

  return mode.value;
});

const readSavedMode = () => {
  const saved = localStorage.getItem(storageKey);
  if (validModes.includes(saved)) {
    return saved;
  }

  if (localStorage.getItem(legacyAutoKey) === 'true') {
    return 'system';
  }

  const legacyDark = localStorage.getItem(legacyDarkKey);
  if (legacyDark === 'true') return 'dark';
  if (legacyDark === 'false') return 'light';

  return 'system';
};

const applyTheme = (theme) => {
  document.documentElement.dataset.theme = theme;
  document.documentElement.dataset.colorMode = mode.value;
  document.documentElement.style.colorScheme = theme;
};

const setMode = (value) => {
  if (validModes.includes(value)) {
    mode.value = value;
  }
};

const updateSystemPreference = (event) => {
  prefersDark.value = event.matches;
};

onMounted(() => {
  mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
  prefersDark.value = mediaQuery.matches;
  mode.value = readSavedMode();
  applyTheme(activeTheme.value);

  if (typeof mediaQuery.addEventListener === 'function') {
    mediaQuery.addEventListener('change', updateSystemPreference);
  } else {
    mediaQuery.addListener(updateSystemPreference);
  }
});

watch(mode, (value) => {
  localStorage.setItem(storageKey, value);
  localStorage.setItem(legacyAutoKey, String(value === 'system'));
  localStorage.setItem(legacyDarkKey, String(activeTheme.value === 'dark'));
  applyTheme(activeTheme.value);
});

watch(activeTheme, (theme) => {
  localStorage.setItem(legacyDarkKey, String(theme === 'dark'));
  applyTheme(theme);
});

onUnmounted(() => {
  if (!mediaQuery) return;

  if (typeof mediaQuery.removeEventListener === 'function') {
    mediaQuery.removeEventListener('change', updateSystemPreference);
  } else {
    mediaQuery.removeListener(updateSystemPreference);
  }
});
</script>
