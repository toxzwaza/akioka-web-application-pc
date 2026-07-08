<script setup>
import { computed } from 'vue';

/** テーブルヘッダーセル。直書きの長大クラスを一元化。 */
const props = defineProps({
    align: { type: String, default: 'left' }, // left / center / right
    sticky: { type: Boolean, default: false },
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
    <th
        class="px-4 py-3 bg-surface-sunken text-content text-xs font-semibold tracking-wide whitespace-nowrap border-b border-border"
        :class="[alignClass, { 'sticky top-0 z-10': sticky }]"
    >
        <slot />
    </th>
</template>
