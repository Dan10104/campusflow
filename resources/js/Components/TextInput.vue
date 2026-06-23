<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="h-11 rounded-xl border border-[var(--cf-border)] bg-[var(--cf-surface)] px-3.5 py-2.5 text-sm text-[var(--cf-text)] shadow-sm transition duration-150 ease-in-out placeholder:text-[var(--cf-text-muted)] focus:border-[#2563EB] focus:outline-none focus:ring-4 focus:ring-[#2563EB]/15 disabled:cursor-not-allowed disabled:bg-[var(--cf-surface-muted)] disabled:text-[var(--cf-text-muted)] disabled:opacity-70 read-only:bg-[var(--cf-surface-muted)] read-only:text-[var(--cf-text-muted)]"
        v-model="model"
        ref="input"
    />
</template>
