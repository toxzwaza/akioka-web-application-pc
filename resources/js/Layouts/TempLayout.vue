<script setup>
import { Head, Link } from "@inertiajs/vue3";
import Message from "@/Components/Message.vue";

const props = defineProps({
  title: String,
});
</script>
<template>
  <Head :title="props.title" />
  <header class="text-gray-600 body-font">

  </header>

  <main>
    <slot name="content" />
  </main>
</template>
<style>
</style>