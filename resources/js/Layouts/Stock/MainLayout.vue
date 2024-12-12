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
    <div
      class="container mx-auto flex flex-wrap p-5 flex-col md:flex-row items-center"
    >
      <a
        :href="route('home')"
        class="flex title-font font-medium items-center text-gray-900 mb-4 md:mb-0"
      >
        <img class="w-16" src="/img/base/logo.jpg" alt="" />
        <span class="ml-3 text-xl">{{ props.title }}</span>
      </a>

      <nav
        class="md:mr-auto md:ml-4 md:py-1 md:pl-4 md:border-l md:border-gray-400 flex flex-wrap items-center text-base justify-center"
      >

      </nav>
    </div>
  </header>

  <main>
    <Message />
    <slot name="content" />
  </main>
</template>
<style>
</style>