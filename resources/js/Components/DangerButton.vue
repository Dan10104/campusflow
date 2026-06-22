<template>
    <button
        class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-red-600 bg-red-600 px-4 py-2.5 text-sm font-bold tracking-normal text-white shadow-sm transition duration-150 ease-in-out hover:border-red-700 hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-500/25 focus:ring-offset-2 focus:ring-offset-[var(--cf-surface)] active:border-red-800 active:bg-red-800 disabled:cursor-not-allowed disabled:opacity-60"
    >
        <slot />
    </button>
</template>
