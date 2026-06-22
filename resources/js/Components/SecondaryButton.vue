<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-[var(--cf-border-strong)] bg-[var(--cf-surface)] px-4 py-2.5 text-sm font-bold tracking-normal text-[var(--cf-heading)] shadow-sm transition duration-150 ease-in-out hover:border-[#2563EB] hover:bg-[var(--cf-surface-muted)] hover:text-[#2563EB] focus:outline-none focus:ring-4 focus:ring-[#2563EB]/20 focus:ring-offset-2 focus:ring-offset-[var(--cf-surface)] active:bg-[var(--cf-accent-soft)] disabled:cursor-not-allowed disabled:opacity-60"
    >
        <slot />
    </button>
</template>
