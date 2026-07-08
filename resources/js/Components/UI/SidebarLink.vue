<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
import Icon from "./Icon.vue";

/**
 * サイドバーのナビリンク。主項目（アイコン付き）とサブ項目（インデント）を兼ねる。
 */
const props = defineProps({
  href: { type: String, default: "#" },
  icon: { type: String, default: "" },
  label: { type: String, required: true },
  active: { type: Boolean, default: false },
  component: { type: String, default: "a" }, // 'a' | 'Link'
  sub: { type: Boolean, default: false },
});

const tag = computed(() => (props.component === "Link" ? Link : "a"));
</script>

<template>
  <component
    :is="tag"
    :href="href"
    class="group flex items-center gap-3 rounded-xl px-3 text-sm font-medium transition-colors duration-150"
    :class="[
      sub ? 'py-2 pl-11 text-[13px]' : 'py-2.5',
      active
        ? 'bg-primary-50 text-primary-700'
        : 'text-content-muted hover:bg-surface-sunken hover:text-content',
    ]"
  >
    <Icon
      v-if="icon && !sub"
      :name="icon"
      size="md"
      class="shrink-0 transition-colors"
      :class="active ? 'text-primary-600' : 'text-content-subtle group-hover:text-content-muted'"
    />
    <span
      v-if="sub"
      class="-ml-5 mr-1.5 h-1.5 w-1.5 shrink-0 rounded-full transition-colors"
      :class="active ? 'bg-primary-600' : 'bg-border-strong group-hover:bg-content-subtle'"
    ></span>
    <span class="truncate">{{ label }}</span>
  </component>
</template>
