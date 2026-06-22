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
        ? 'inline-flex min-h-10 items-center rounded-lg border-b-2 border-[#2563EB] bg-[var(--cf-accent-soft)] px-3 pt-1 text-sm font-bold leading-5 text-[#2563EB] transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-[#2563EB]/30 focus:ring-offset-2 focus:ring-offset-[var(--cf-surface)]'
        : 'inline-flex min-h-10 items-center rounded-lg border-b-2 border-transparent px-3 pt-1 text-sm font-semibold leading-5 text-[var(--cf-text-muted)] transition duration-150 ease-in-out hover:border-[var(--cf-border-strong)] hover:bg-[var(--cf-surface-muted)] hover:text-[var(--cf-heading)] focus:outline-none focus:ring-2 focus:ring-[#2563EB]/30 focus:ring-offset-2 focus:ring-offset-[var(--cf-surface)]',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
