<script setup>
/** 検索・絞り込みフィルターのレイアウト枠（カード見た目 + グリッド）。中身は slot。 */
</script>

<template>
    <div class="bg-surface-base border border-border rounded-card shadow-card p-4 mb-6">
        <div
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4"
        >
            <slot />
        </div>
        <div
            v-if="$slots.actions"
            class="flex flex-wrap items-center justify-end gap-2 mt-4"
        >
            <slot name="actions" />
        </div>
    </div>
</template>
