<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    markSize: {
        type: String,
        default: 'h-10 w-10',
    },
    textSize: {
        type: String,
        default: 'text-2xl',
    },
    showText: {
        type: Boolean,
        default: true,
    },
});

const page = usePage();
const name = computed(() => page.props.name || 'David Michan');
const initial = computed(() => name.value.trim().charAt(0).toUpperCase() || 'D');
</script>

<template>
    <div class="flex items-center gap-3">
        <span
            :class="markSize"
            class="relative inline-flex shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-[#1d4ed8] to-[#0891b2] shadow-lg shadow-cyan-500/30"
        >
            <svg
                viewBox="0 0 40 40"
                class="h-3/5 w-3/5 text-white"
                fill="none"
                aria-hidden="true"
            >
                <path
                    d="M9 12c0-1.1.9-2 2-2h3.2c.9 0 1.7.6 1.9 1.5l1 4.1c.2.8-.1 1.7-.8 2.2l-2 1.5a15.5 15.5 0 0 0 7.4 7.4l1.5-2c.5-.7 1.4-1 2.2-.8l4.1 1c.9.2 1.5 1 1.5 1.9V30c0 1.1-.9 2-2 2h-1.5C15.8 32 8 24.2 8 14.5V12z"
                    fill="currentColor"
                />
            </svg>
        </span>

        <span
            v-if="showText"
            :class="textSize"
            class="bg-gradient-to-r from-[#1d4ed8] to-[#0891b2] bg-clip-text font-extrabold tracking-tight text-transparent"
        >
            {{ name }}
        </span>
    </div>
</template>
