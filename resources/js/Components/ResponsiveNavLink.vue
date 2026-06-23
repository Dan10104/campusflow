<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'block min-h-11 w-full border-l-4 border-[#2563EB] bg-[var(--cf-accent-soft)] px-4 py-3 text-start text-base font-bold text-[#2563EB] transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#2563EB]/30'
        : 'block min-h-11 w-full border-l-4 border-transparent px-4 py-3 text-start text-base font-semibold text-[var(--cf-text-muted)] transition duration-150 ease-in-out hover:border-[var(--cf-border-strong)] hover:bg-[var(--cf-surface-muted)] hover:text-[var(--cf-heading)] focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#2563EB]/30',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
