<script setup>
/** 横スクロール対応のテーブルラッパー。<thead>/<tbody> を slot で差し込む。 */
</script>

<template>
    <div class="overflow-x-auto border border-border rounded-card">
        <table class="min-w-full border-collapse text-sm">
            <slot />
        </table>
    </div>
</template>
