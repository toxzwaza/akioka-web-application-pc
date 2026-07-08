<script setup>
import { computed } from 'vue';

/** ステータス表示バッジ。行状態の bg-*-100 直書きを置き換える受け皿。 */
const props = defineProps({
    variant: { type: String, default: 'neutral' }, // neutral/primary/success/warning/error/info
    size: { type: String, default: 'md' }, // sm / md
});

const variantClass = computed(
    () =>
        ({
            neutral: 'bg-surface-sunken text-content-muted',
            primary: 'bg-primary-100 text-primary-800',
            success: 'bg-success-100 text-success-700',
            warning: 'bg-warning-100 text-warning-700',
            error: 'bg-error-100 text-error-700',
            info: 'bg-info-100 text-info-700',
        }[props.variant])
);

const sizeClass = computed(
    () =>
        ({
            sm: 'text-xs px-2 py-0.5',
            md: 'text-xs px-2.5 py-1',
        }[props.size])
);
</script>

<template>
    <span
        class="inline-flex items-center gap-1 font-medium rounded-badge whitespace-nowrap"
        :class="[variantClass, sizeClass]"
    >
        <slot />
    </span>
</template>
