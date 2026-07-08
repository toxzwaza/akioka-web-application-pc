<script setup>
import { computed } from 'vue';

/**
 * テーブル行。行状態を state トークンで表現し、inline style の色指定を撲滅する。
 * state: default / selected / success / warning / error / info
 */
const props = defineProps({
    state: { type: String, default: 'default' },
    hoverable: { type: Boolean, default: true },
});

const stateClass = computed(
    () =>
        ({
            default: 'bg-surface-base',
            selected: 'bg-primary-50',
            success: 'bg-success-50',
            warning: 'bg-warning-50',
            error: 'bg-error-50',
            info: 'bg-info-50',
        }[props.state])
);
</script>

<template>
    <tr
        class="border-b border-border transition-colors"
        :class="[stateClass, { 'hover:bg-surface-muted': hoverable }]"
    >
        <slot />
    </tr>
</template>
