<script setup>
import { computed } from 'vue';

const emit = defineEmits(['update:checked']);

const props = defineProps({
    checked: {
        type: [Array, Boolean],
        required: true,
    },
    value: {
        default: null,
    },
});

const proxyChecked = computed({
    get() {
        return props.checked;
    },

    set(val) {
        emit('update:checked', val);
    },
});
</script>

<template>
    <input
        type="checkbox"
        :value="value"
        v-model="proxyChecked"
        class="h-4 w-4 rounded-md border-[var(--cf-border-strong)] bg-[var(--cf-surface)] text-[#2563EB] shadow-sm transition duration-150 ease-in-out focus:ring-4 focus:ring-[#2563EB]/20 focus:ring-offset-2 focus:ring-offset-[var(--cf-surface)] disabled:cursor-not-allowed disabled:opacity-60"
    />
</template>
