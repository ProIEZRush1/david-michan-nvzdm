<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center rounded-xl border border-transparent bg-gradient-to-r from-blue-700 to-cyan-600 px-5 py-2.5 text-xs font-semibold uppercase tracking-widest text-white shadow-lg shadow-blue-600/30 transition duration-150 ease-in-out hover:from-blue-600 hover:to-cyan-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 active:from-blue-800 active:to-cyan-700"
    >
        <slot />
    </button>
</template>
