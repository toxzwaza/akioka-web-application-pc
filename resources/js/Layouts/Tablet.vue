<script setup>
import { Head, Link } from "@inertiajs/vue3";
import Message from "@/Components/Message.vue";

const props = defineProps({
  title: String,
});
</script>
<template>
  <Head :title="props.title" />
  <header class="text-gray-600 body-font bg-gray-100 border-b ">
    <div
      class="container mx-auto flex flex-wrap p-5 flex-col md:flex-row items-center"
    >
      <a
        class="flex title-font font-medium items-center text-gray-900 mb-4 md:mb-0"
      >
        <!-- <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          stroke="currentColor"
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          class="w-10 h-10 text-white p-2 bg-indigo-500 rounded-full"
          viewBox="0 0 24 24"
        >
          <path
            d="M3 3h18v2H3V3zm0 4h18v2H3V7zm0 4h18v2H3v-2zm0 4h18v2H3v-2zm0 4h18v2H3v-2z"
          ></path>
        </svg> -->
        <span class="ml-3 text-xl">納品管理システム</span>
      </a>
      <nav
        class="md:mr-auto md:ml-4 md:py-1 md:pl-4 md:border-l md:border-gray-400 flex flex-wrap items-center text-base justify-center"
      >
        <Link :href="route('stock.tablet.receive')" :class="{'mr-5 hover:text-gray-900': true , 'font-bold': route().current() == 'stock.tablet.receive'}">納品書登録</Link>
        <Link :href="route('stock.tablet.archive')" :class="{'mr-5 hover:text-gray-900': true , 'font-bold': route().current() == 'stock.tablet.archive'}">納品登録</Link>
        <Link :href="route('stock.tablet.receipt')" :class="{'mr-5 hover:text-gray-900': true , 'font-bold': route().current() == 'stock.tablet.receipt'}">引き渡し登録</Link>
      </nav>
      <!-- <button
        class="inline-flex items-center bg-gray-100 border-0 py-1 px-3 focus:outline-none hover:bg-gray-200 rounded text-base mt-4 md:mt-0"
      >
        Button
        <svg
          fill="none"
          stroke="currentColor"
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          class="w-4 h-4 ml-1"
          viewBox="0 0 24 24"
        >
          <path d="M5 12h14M12 5l7 7-7 7"></path>
        </svg>
      </button> -->
    </div>
  </header>
  <Message />

  <main>
    <slot name="content" />
  </main>
</template>
<style>
</style>