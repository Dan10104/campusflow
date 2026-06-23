<script setup>
defineProps({
    message: {
        type: String,
    },
});
</script>

<template>
    <div v-if="message" class="mt-1">
        <p class="text-sm font-semibold text-red-600">
            {{ message }}
        </p>
    </div>
</template>
