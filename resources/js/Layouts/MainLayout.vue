<script setup>
import { Head } from '@inertiajs/vue3';

</script>
<template>
  <Head title="滞留品管理"/>
  <header class="text-gray-600 body-font">
    <div
      class="container mx-auto flex flex-wrap p-5 flex-col md:flex-row items-center"
    >
      <a
        :href="route('home')"
        class="flex title-font font-medium items-center text-gray-900 mb-4 md:mb-0"
      >
        <img class="w-16" src="/img/base/logo.jpg" alt="" />
        <span class="ml-3 text-xl">滞留品通達ページ</span>
      </a>

      <nav
        class="md:mr-auto md:ml-4 md:py-1 md:pl-4 md:border-l md:border-gray-400 flex flex-wrap items-center text-base justify-center"
      >
        <!-- <a
          href="{{ route('master') }}"
          class="mr-5 hover:text-gray-900 flex justify-center {{ Route::is('master*') ? 'font-bold text-gray-900' : ''}}"
          >基幹マスタ管理</a> -->
      </nav>
    </div>
  </header>

  <main>
    <slot name="content" />
  </main>
</template>
<style>
</style>