<script setup>
import { computed } from 'vue';
import Icon from './Icon.vue';

/**
 * 統一ボタン。日本語UIのため uppercase / tracking-widest は付けない。
 * variant: primary / secondary / danger / ghost / outline
 */
const props = defineProps({
    variant: { type: String, default: 'primary' },
    size: { type: String, default: 'md' }, // sm / md / lg
    type: { type: String, default: 'button' },
    disabled: { type: Boolean, default: false },
    loading: { type: Boolean, default: false },
    iconLeft: { type: String, default: '' },
    iconRight: { type: String, default: '' },
    block: { type: Boolean, default: false },
});

const base =
    'inline-flex items-center justify-center gap-1.5 font-semibold rounded-md transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-1 disabled:opacity-50 disabled:cursor-not-allowed';

const variantClass = computed(
    () =>
        ({
            primary: 'bg-primary-600 text-white hover:bg-primary-700 active:bg-primary-800 focus:ring-primary-500',
            secondary: 'bg-white text-content border border-border-strong hover:bg-surface-sunken focus:ring-primary-500',
            danger: 'bg-error-600 text-white hover:bg-error-700 active:bg-error-700 focus:ring-error-500',
            ghost: 'bg-transparent text-content hover:bg-surface-sunken focus:ring-primary-500',
            outline: 'bg-transparent text-primary-700 border border-primary-600 hover:bg-primary-50 focus:ring-primary-500',
        }[props.variant])
);

const sizeClass = computed(
    () =>
        ({
            sm: 'text-xs px-3 py-1.5',
            md: 'text-sm px-4 py-2',
            lg: 'text-base px-5 py-2.5',
        }[props.size])
);

const iconSize = computed(() => (props.size === 'lg' ? 'md' : 'sm'));
</script>

<template>
    <button
        :type="type"
        :disabled="disabled || loading"
        :class="[base, variantClass, sizeClass, { 'w-full': block }]"
    >
        <Icon
            v-if="loading"
            name="progress_activity"
            :size="iconSize"
            class="animate-spin"
        />
        <Icon v-else-if="iconLeft" :name="iconLeft" :size="iconSize" />
        <slot />
        <Icon v-if="iconRight && !loading" :name="iconRight" :size="iconSize" />
    </button>
</template>
