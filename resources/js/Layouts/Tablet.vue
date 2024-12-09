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

        <span class="ml-3 text-xl">納品管理システム</span>
      </a>
      <nav
        class="md:mr-auto md:ml-4 md:py-1 md:pl-4 md:border-l md:border-gray-400 flex flex-wrap items-center text-base justify-center"
      >
        <Link :href="route('stock.tablet.receive')" :class="{'mr-5 hover:text-gray-900': true , 'font-bold': route().current() == 'stock.tablet.receive'}">納品書登録</Link>
        <Link :href="route('stock.tablet.archive')" :class="{'mr-5 hover:text-gray-900': true , 'font-bold': route().current() == 'stock.tablet.archive'}">納品登録</Link>
        <Link :href="route('stock.tablet.receipt')" :class="{'mr-5 hover:text-gray-900': true , 'font-bold': route().current() == 'stock.tablet.receipt'}">引き渡し登録</Link>


        <Link :href="route('signage.content.stockDeliveryList')" :class="{'mr-5 hover:text-gray-900': true}">サイネージ画面確認</Link>

        
      </nav>
    </div>
  </header>
  <Message />

  <main>
    <slot name="content" />
  </main>
</template>
<style>
</style>