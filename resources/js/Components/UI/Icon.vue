<script setup>
import { computed } from 'vue';

/**
 * Material Symbols アイコンの統一ラッパー。
 * 新規アイコンは必ず本コンポーネント経由で使う（doc/design-system.md）。
 */
const props = defineProps({
    name: { type: String, required: true },
    size: { type: String, default: 'md' }, // xs / sm / md / lg / xl
    filled: { type: Boolean, default: false },
});

const sizeClass = computed(
    () =>
        ({
            xs: 'text-base', // 16px
            sm: 'text-lg', // 18px
            md: 'text-xl', // 20px
            lg: 'text-2xl', // 24px
            xl: 'text-3xl', // 30px
        }[props.size] || 'text-xl')
);

const iconStyle = computed(() =>
    props.filled ? { fontVariationSettings: "'FILL' 1" } : undefined
);
</script>

<template>
    <span
        class="material-symbols-outlined leading-none align-middle select-none"
        :class="sizeClass"
        :style="iconStyle"
        aria-hidden="true"
        >{{ name }}</span
    >
</template>
