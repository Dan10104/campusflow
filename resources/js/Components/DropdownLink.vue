<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Link
        :href="href"
        class="block w-full px-4 py-2.5 text-start text-sm font-semibold leading-5 text-[var(--cf-text)] transition duration-150 ease-in-out hover:bg-[var(--cf-accent-soft)] hover:text-[#2563EB] focus:bg-[var(--cf-accent-soft)] focus:text-[#2563EB] focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#2563EB]/30"
    >
        <slot />
    </Link>
</template>
