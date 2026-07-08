<script setup>
/** 画面上部の見出し + アクション領域。 */
defineProps({
    title: { type: String, default: '' },
    subtitle: { type: String, default: '' },
});
</script>

<template>
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-content tracking-tight">
                {{ title }}
            </h1>
            <p v-if="subtitle" class="mt-1 text-sm text-content-muted">
                {{ subtitle }}
            </p>
        </div>
        <div v-if="$slots.actions" class="flex items-center gap-2">
            <slot name="actions" />
        </div>
    </div>
</template>
