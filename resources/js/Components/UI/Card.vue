<script setup>
import { computed } from 'vue';

/** Bento UI の基本ブロック（白背景・薄影・角丸）。 */
const props = defineProps({
    padding: { type: String, default: 'md' }, // none / sm / md / lg
});

const paddingClass = computed(
    () =>
        ({
            none: '',
            sm: 'p-4',
            md: 'p-6',
            lg: 'p-8',
        }[props.padding])
);
</script>

<template>
    <div
        class="bg-surface-base border border-border rounded-card shadow-card"
        :class="paddingClass"
    >
        <slot />
    </div>
</template>
