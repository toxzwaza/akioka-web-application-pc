<script setup>
import { Head,Link } from "@inertiajs/vue3";
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

          <Link :class="{'mr-5 hover:text-gray-900 flex justify-center': true,
          'text-indigo-600 font-bold': route().current() == 'fax'}" :href="route('fax')">振り分け</Link>
          <Link :class="{'mr-5 hover:text-gray-900 flex justify-center': true,
          'text-indigo-600 font-bold': route().current() == 'fax.group'}" :href="route('fax.group')">グループ</Link>
          <Link :class="{'mr-5 hover:text-gray-900 flex justify-center': true,
          'text-indigo-600 font-bold': route().current() == 'fax.folder'}" :href="route('fax.folder')">フォルダ割り当て</Link>


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