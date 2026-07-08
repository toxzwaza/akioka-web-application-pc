<script setup>
import { computed } from 'vue';

/** テーブルデータセル。 */
const props = defineProps({
    align: { type: String, default: 'left' }, // left / center / right
    nowrap: { type: Boolean, default: false },
});

const alignClass = computed(
    () =>
        ({
            left: 'text-left',
            center: 'text-center',
            right: 'text-right',
        }[props.align])
);
</script>

<template>
    <td
        class="px-4 py-3 text-content align-middle"
        :class="[alignClass, { 'whitespace-nowrap': nowrap }]"
    >
        <slot />
    </td>
</template>
