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
        <div class="flex">


          <Link
            :class="{
              'mr-5 hover:text-gray-900 flex justify-center': true,
              'text-indigo-600 font-bold': route().current() == 'fax',
            }"
            :href="route('fax')"
            ><svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-1"
              viewBox="0 0 24 24"
              fill="currentColor"
            >
              <path
                d="M3 3h18v2H3V3zm0 4h18v2H3V7zm0 4h18v2H3v-2zm0 4h18v2H3v-2zm0 4h18v2H3v-2z"
              /></svg
            >新規ユーザー</Link
          >
          <Link
            :class="{
              'mr-5 hover:text-gray-900 flex justify-center': true,
              'text-indigo-600 font-bold': route().current() == 'fax.group',
            }"
            :href="route('fax.group')"
            ><svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-1"
              viewBox="0 0 24 24"
              fill="currentColor"
            >
              <path
                d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"
              /></svg
            >従業員一覧</Link
          >
        </div>
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