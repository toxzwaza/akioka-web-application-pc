<script setup>
import PageHeader from "@/Components/UI/PageHeader.vue";

/**
 * 旧ページ見出し（indigoバー付き）の互換ラッパー。
 * 見た目はデザインシステムの UI/PageHeader に委譲する。
 * 新規画面は直接 UI/PageHeader を使うこと（本コンポーネントは既存画面の互換用）。
 */
defineProps({
  top: { type: String, default: "" },
  sub: { type: String, default: "" },
  icon: { type: String, default: "" },
});
</script>
<template>
  <PageHeader :title="top" :subtitle="sub" :icon="icon">
    <template v-if="$slots.actions" #actions>
      <slot name="actions" />
    </template>
  </PageHeader>
</template>
