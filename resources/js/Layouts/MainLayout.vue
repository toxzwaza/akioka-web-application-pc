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
      <Link
        :href="route('home')"
        class="flex title-font font-medium items-center text-gray-900 mb-4 md:mb-0"
      >
        <img class="w-16" src="/img/base/logo.jpg" alt="" />
        <span class="ml-3 text-xl">{{ props.title }}</span>
      </Link>
      <nav
        class="md:mr-auto md:ml-4 md:py-1 md:pl-4 md:border-l md:border-gray-400 flex flex-wrap items-center text-base justify-center"
      >
        <a
          :href="route('master')"
          class="mr-5 hover:text-gray-900 flex justify-center"
          ><span class="mr-1 material-symbols-outlined"> database </span
          >基幹マスタ管理</a
        >
        <a
          :href="route('stock')"
          class="mr-5 hover:text-gray-900 flex justify-center"
          ><span class="mr-1 material-symbols-outlined"> list_alt </span
          >在庫管理</a
        >
        <!-- <a href="route('order')" class="mr-5 hover:text-gray-900 flex justify-center {{ Route::is('order*') ? 'font-bold text-gray-900' : ''}}"><span class="mr-1 material-symbols-outlined">
            toc
          </span>発注管理</a> -->
        <a
          :href="route('lunch')"
          class="mr-5 hover:text-gray-900 flex justify-center"
          ><span class="mr-1 material-symbols-outlined"> restaurant </span
          >弁当注文</a
        >

        <a
          :href="route('movie2')"
          class="mr-5 hover:text-gray-900 flex justify-center"
        >
          <span class="material-symbols-outlined"> live_tv </span>
          動画視聴</a
        >

        <!-- 図番棚卸 -->
        <a
          :href="route('calc.product')"
          class="mr-5 hover:text-gray-900 flex justify-center"
        >
          <span class="mr-1 material-symbols-outlined"> list_alt </span>
          製品棚卸</a
        >

        <!-- FAX設定 -->
        <a
          :href="route('fax')"
          class="mr-5 hover:text-gray-900 flex justify-center"
        >
          <span class="mr-1 material-symbols-outlined"> description </span>
          FAX振分設定</a
        >

        <!-- サイネージシステム -->
        <a
          :href="route('signage.home')"
          class="mr-5 hover:text-gray-900 flex justify-center"
        >
          <span class="mr-1 material-symbols-outlined"> tv </span>
          サイネージ
        </a>

        <!-- リモート接続 -->
        <a
          :href="route('remote')"
          class="mr-5 hover:text-gray-900 flex justify-center"
        >
          <span class="mr-1 material-symbols-outlined"> computer </span>
          リモート接続
        </a>
      </nav>

    </div>
  </header>

  <!-- サブナビゲーション -->
  <nav
    class="flex justify-flex-start items-center bg-gray-200 p-4 overflow-x-auto"
  >
    <template v-if="route().current().startsWith('master')">
      <Link
        :href="route('master.create.user')"
        class="flex mx-2 px-8 hover:text-gray-900"
      >
        <span class="text-gray-500 mr-1 material-symbols-outlined">
          edit_square </span
        >従業員登録</Link
      >
      <Link
        :href="route('master.users')"
        class="flex mx-2 px-8 hover:text-gray-900"
      >
        <span class="text-gray-500 mr-1 material-symbols-outlined">
          list_alt
        </span>
        全従業員参照</Link
      >
      <Link
        :href="route('master.akioka-users')"
        class="flex mx-2 px-8 hover:text-gray-900"
      >
        <span class="text-gray-500 mr-1 material-symbols-outlined">
          list_alt
        </span>
        アキオカ従業員参照</Link
      >
    </template>

    <template v-else-if="route().current().startsWith('stock')">
      <a
        :href="route('stock.stocks.create')"
        class="flex mx-2 px-8 hover:text-gray-900"
        :class="{
          'text-red-500 font-bold': route().current() == 'stock.stocks.create',
        }"
      >
        <span class="text-gray-500 mr-1 material-symbols-outlined">
          edit_square </span
        >在庫追加</a
      >
      <a
        :href="route('stock.suppliers.create')"
        class="flex mx-2 px-8 hover:text-gray-900"
        :class="{
          'text-red-500 font-bold':
            route().current() == 'stock.suppliers.create',
        }"
      >
        <span class="text-gray-500 mr-1 material-symbols-outlined">
          edit_square </span
        >取引先追加</a
      >
      <a
        :href="route('stock.storage_addresses.create')"
        class="flex mx-2 px-8 hover:text-gray-900"
        :class="{
          'text-red-500 font-bold':
            route().current() == 'stock.storage_addresses.create',
        }"
      >
        <span class="text-gray-500 mr-1 material-symbols-outlined">
          edit_square </span
        >格納先追加</a
      >

      <a
        :href="route('stock.stocks')"
        class="flex mx-2 px-8 hover:text-gray-900"
        :class="{
          'text-blue-500 font-bold': route().current() == 'stock.stocks',
        }"
      >
        <span class="text-gray-500 mr-1 material-symbols-outlined">
          list_alt
        </span>
        在庫一覧</a
      >
      <a
        :href="route('stock.suppliers')"
        class="flex mx-2 px-8 hover:text-gray-900"
        :class="{
          'text-blue-500 font-bold': route().current() == 'stock.suppliers',
        }"
      >
        <span class="text-gray-500 mr-1 material-symbols-outlined">
          list_alt
        </span>
        取引先一覧</a
      >
      <a
        :href="route('stock.storage_addresses')"
        class="flex mx-2 px-8 hover:text-gray-900"
        :class="{
          'text-blue-500 font-bold':
            route().current() == 'stock.storage_addresses',
        }"
      >
        <span class="text-gray-500 mr-1 material-symbols-outlined">
          list_alt
        </span>
        格納先一覧</a
      >
      <a
        :href="route('stock.initial_orders')"
        class="flex mx-2 px-8 hover:text-gray-900"
        :class="{
          'text-blue-500 font-bold':
            route().current() == 'stock.initial_orders',
        }"
      >
        <span class="text-gray-500 mr-1 material-symbols-outlined">
          list_alt
        </span>
        発注一覧</a
      >

      <!-- <Link
        :href="route('stock.stocks.taking')"
        class="flex mx-2 px-8 hover:text-gray-900"
        :class="{
          'text-blue-500 font-bold':
            route().current() == 'stock.storage_addresses',
        }"
      >
        <span class="text-gray-500 mr-1 material-symbols-outlined">
          list_alt
        </span>
        棚卸し</Link
      >
      <Link
        :href="route('stock.retained.stocks')"
        class="flex mx-2 px-8 hover:text-gray-900"
        :class="{
          'text-blue-500 font-bold':
            route().current() == 'stock.retained.stocks',
        }"
      >
        <span class="text-gray-500 mr-1 material-symbols-outlined"> link </span>
        滞留品</Link
      > -->
    </template>

    <template v-else-if="route().current().startsWith('order')">
      <Link
        :href="route('order.consumOrders')"
        class="flex mx-2 px-8 hover:text-gray-900"
      >
        <span class="material-symbols-outlined"> assignment </span
        >消耗品発注依頼リスト</Link
      >

      <Link
        :href="route('order.already_requests')"
        class="opacity-30 flex mx-2 px-8 hover:text-gray-900"
      >
        <span class="material-symbols-outlined"> approval_delegation </span
        >承認依頼</Link
      >

      <Link
        :href="route('order.already_orders')"
        class="opacity-30 flex mx-2 px-8 hover:text-gray-900"
      >
        <span class="material-symbols-outlined"> assignment </span>
        注文書作成待ちリスト</Link
      >
      <Link
        :href="route('home')"
        class="opacity-30 flex mx-2 px-8 hover:text-gray-900"
      >
        <span class="material-symbols-outlined"> grade </span>
        注文待ちリスト</Link
      >
      <Link
        :href="route('home')"
        class="opacity-30 flex mx-2 px-8 hover:text-gray-900"
      >
        <span class="material-symbols-outlined"> orders </span
        >納品待ちリスト</Link
      >
    </template>

    <template v-else-if="route().current().startsWith('lunch')">
      <Link
        :href="route('lunch.order-archive')"
        class="flex mx-2 px-8 hover:text-gray-900"
        >弁当注文履歴</Link
      >
      <Link
        :href="route('lunch.create_description')"
        class="flex mx-2 px-8 hover:text-gray-900"
        >備考作成</Link
      >
    </template>

    <template v-else-if="route().current().startsWith('movie')">
      <Link
        :class="{
          'mr-5 flex mx-2 px-8 hover:text-gray-900': true,
          'text-indigo-600 font-bold': route().current() == 'movie2',
        }"
        :href="route('movie2')"
        ><svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 mr-1"
          viewBox="0 0 24 24"
          fill="currentColor"
        >
          <path
            d="M3 3h18v2H3V3zm0 4h18v2H3V7zm0 4h18v2H3v-2zm0 4h18v2H3v-2zm0 4h18v2H3v-2z"
          /></svg
        >動画一覧</Link
      >
      <Link
        :class="{
          'mr-5 flex mx-2 px-8 hover:text-gray-900': true,
          'text-indigo-600 font-bold': route().current() == 'movie2.create',
        }"
        :href="route('movie2.create')"
        ><svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 mr-1"
          viewBox="0 0 24 24"
          fill="currentColor"
        >
          <path
            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"
          /></svg
        >動画追加</Link
      >
      <Link
        :class="{
          'mr-5 flex mx-2 px-8 hover:text-gray-900': true,
          'text-indigo-600 font-bold':
            route().current() == 'movie2.categoryAndTag',
        }"
        :href="route('movie2.categoryAndTag')"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 mr-1"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            d="M2 4a2 2 0 012-2h4l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V4z"
          />
        </svg>
        動画タグ追加</Link
      >
    </template>
    <template v-else-if="route().current().startsWith('fax')">
      <Link
        :class="{
          'mr-5 flex mx-2 px-8 hover:text-gray-900': true,
          'text-indigo-600 font-bold': route().current() == 'fax.manual',
        }"
        :href="route('fax.manual')"
        ><svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 mr-1"
          viewBox="0 0 24 24"
          fill="currentColor"
        >
          <path
            d="M6 2a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6H6zm7 7V3.5L18.5 9H13z"
          /></svg
        >マニュアル</Link
      >

      <Link
        :class="{
          'mr-5 flex mx-2 px-8 hover:text-gray-900': true,
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
        >振り分け</Link
      >
      <Link
        :class="{
          'mr-5 flex mx-2 px-8 hover:text-gray-900': true,
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
        >グループ</Link
      >
      <Link
        :class="{
          'mr-5 flex mx-2 px-8 hover:text-gray-900': true,
          'text-indigo-600 font-bold': route().current() == 'fax.folder',
        }"
        :href="route('fax.folder')"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 mr-1"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            d="M2 4a2 2 0 012-2h4l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V4z"
          />
        </svg>
        フォルダ割り当て</Link
      >
    </template>
  </nav>

  <nav
    class="flex px-5 py-3 text-gray-700 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700"
    aria-label="Breadcrumb"
  >
    <ol
      class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse"
    >
      <li class="inline-flex items-center">
        <a
          :href="route('home')"
          class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white"
        >
          <svg
            class="w-3 h-3 me-2.5"
            aria-hidden="true"
            xmlns="http://www.w3.org/2000/svg"
            fill="currentColor"
            viewBox="0 0 20 20"
          >
            <path
              d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"
            />
          </svg>
          Home
        </a>
      </li>

      <template v-if="route().current().startsWith('master')">
        <li>
          <div class="flex items-center">
            <svg
              class="rtl:rotate-180 block w-3 h-3 mx-1 text-gray-400"
              aria-hidden="true"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 6 10"
            >
              <path
                stroke="currentColor"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="m1 9 4-4-4-4"
              />
            </svg>
            <a
              :href="route('home')"
              class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white"
            >
              基幹マスタ管理</a
            >
          </div>
        </li>
        <template v-if="route().current() === 'master'">
          <li aria-current="page">
            <div class="flex items-center">
              <svg
                class="rtl:rotate-180 w-3 h-3 mx-1 text-gray-400"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 6 10"
              >
                <path
                  stroke="currentColor"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="m1 9 4-4-4-4"
                />
              </svg>
              <span
                class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400"
                >TOP</span
              >
            </div>
          </li>
        </template>
      </template>

      <template v-else-if="route().current().startsWith('stock')">
        <li>
          <div class="flex items-center">
            <svg
              class="rtl:rotate-180 block w-3 h-3 mx-1 text-gray-400"
              aria-hidden="true"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 6 10"
            >
              <path
                stroke="currentColor"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="m1 9 4-4-4-4"
              />
            </svg>
            <a
              :href="route('home')"
              class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white"
            >
              在庫管理</a
            >
          </div>
        </li>
        <template v-if="route().current() === 'stock'">
          <li aria-current="page">
            <div class="flex items-center">
              <svg
                class="rtl:rotate-180 w-3 h-3 mx-1 text-gray-400"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 6 10"
              >
                <path
                  stroke="currentColor"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="m1 9 4-4-4-4"
                />
              </svg>
              <span
                class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400"
                >TOP</span
              >
            </div>
          </li>
        </template>
        <template v-else-if="route().current() === 'stock.stocks.create'">
          <li aria-current="page">
            <div class="flex items-center">
              <svg
                class="rtl:rotate-180 w-3 h-3 mx-1 text-gray-400"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 6 10"
              >
                <path
                  stroke="currentColor"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="m1 9 4-4-4-4"
                />
              </svg>
              <span
                class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400"
                >在庫追加</span
              >
            </div>
          </li>
        </template>
        <template v-else-if="route().current() === 'stock.suppliers.create'">
          <li aria-current="page">
            <div class="flex items-center">
              <svg
                class="rtl:rotate-180 w-3 h-3 mx-1 text-gray-400"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 6 10"
              >
                <path
                  stroke="currentColor"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="m1 9 4-4-4-4"
                />
              </svg>
              <span
                class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400"
                >取引先追加</span
              >
            </div>
          </li>
        </template>
        <template
          v-else-if="route().current() === 'stock.storage_addresses.create'"
        >
          <li aria-current="page">
            <div class="flex items-center">
              <svg
                class="rtl:rotate-180 w-3 h-3 mx-1 text-gray-400"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 6 10"
              >
                <path
                  stroke="currentColor"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="m1 9 4-4-4-4"
                />
              </svg>
              <span
                class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400"
                >格納先アドレス追加</span
              >
            </div>
          </li>
        </template>
        <template v-else-if="route().current() === 'stock.stocks'">
          <li aria-current="page">
            <div class="flex items-center">
              <svg
                class="rtl:rotate-180 w-3 h-3 mx-1 text-gray-400"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 6 10"
              >
                <path
                  stroke="currentColor"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="m1 9 4-4-4-4"
                />
              </svg>
              <span
                class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400"
                >在庫一覧</span
              >
            </div>
          </li>
        </template>
        <template v-else-if="route().current() === 'stock.suppliers'">
          <li aria-current="page">
            <div class="flex items-center">
              <svg
                class="rtl:rotate-180 w-3 h-3 mx-1 text-gray-400"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 6 10"
              >
                <path
                  stroke="currentColor"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="m1 9 4-4-4-4"
                />
              </svg>
              <span
                class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400"
                >取引先一覧</span
              >
            </div>
          </li>
        </template>
        <template v-else-if="route().current() === 'stock.storage_addresses'">
          <li aria-current="page">
            <div class="flex items-center">
              <svg
                class="rtl:rotate-180 w-3 h-3 mx-1 text-gray-400"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 6 10"
              >
                <path
                  stroke="currentColor"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="m1 9 4-4-4-4"
                />
              </svg>
              <span
                class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400"
                >格納先アドレス一覧</span
              >
            </div>
          </li>
        </template>
        <template v-else-if="route().current() === 'stock.edit.stocks'">
          <li aria-current="page">
            <div class="flex items-center">
              <svg
                class="rtl:rotate-180 w-3 h-3 mx-1 text-gray-400"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 6 10"
              >
                <path
                  stroke="currentColor"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="m1 9 4-4-4-4"
                />
              </svg>
              <span
                class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400"
                >在庫編集</span
              >
            </div>
          </li>
        </template>
      </template>

      <template v-else-if="route().current().startsWith('order')">
        <li>
          <div class="flex items-center">
            <svg
              class="rtl:rotate-180 block w-3 h-3 mx-1 text-gray-400"
              aria-hidden="true"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 6 10"
            >
              <path
                stroke="currentColor"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="m1 9 4-4-4-4"
              />
            </svg>
            <a
              href="#"
              class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white"
            >
              発注管理</a
            >
          </div>
        </li>
        <template v-if="route().current() === 'order'">
          <li aria-current="page">
            <div class="flex items-center">
              <svg
                class="rtl:rotate-180 w-3 h-3 mx-1 text-gray-400"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 6 10"
              >
                <path
                  stroke="currentColor"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="m1 9 4-4-4-4"
                />
              </svg>
              <span
                class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400"
                >TOP</span
              >
            </div>
          </li>
        </template>
      </template>

      <template v-else-if="route().current().startsWith('lunch')">
        <li>
          <div class="flex items-center">
            <svg
              class="rtl:rotate-180 block w-3 h-3 mx-1 text-gray-400"
              aria-hidden="true"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 6 10"
            >
              <path
                stroke="currentColor"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="m1 9 4-4-4-4"
              />
            </svg>
            <a
              href="#"
              class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white"
            >
              弁当注文</a
            >
          </div>
        </li>
        <template v-if="route().current() === 'lunch'">
          <li aria-current="page">
            <div class="flex items-center">
              <svg
                class="rtl:rotate-180 w-3 h-3 mx-1 text-gray-400"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 6 10"
              >
                <path
                  stroke="currentColor"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="m1 9 4-4-4-4"
                />
              </svg>
              <span
                class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400"
                >TOP</span
              >
            </div>
          </li>
        </template>
      </template>

      <template v-else-if="route().current().startsWith('movie2')">
        <li>
          <div class="flex items-center">
            <svg
              class="rtl:rotate-180 block w-3 h-3 mx-1 text-gray-400"
              aria-hidden="true"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 6 10"
            >
              <path
                stroke="currentColor"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="m1 9 4-4-4-4"
              />
            </svg>
            <a
              :href="route('movie2')"
              class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white"
            >
              動画視聴</a
            >
          </div>
        </li>

        <!-- 下階層 -->
        <template v-if="route().current() === 'movie2'">
          <li aria-current="page">
            <div class="flex items-center">
              <svg
                class="rtl:rotate-180 w-3 h-3 mx-1 text-gray-400"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 6 10"
              >
                <path
                  stroke="currentColor"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="m1 9 4-4-4-4"
                />
              </svg>
              <span
                class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400"
                >動画一覧</span
              >
            </div>
          </li>
        </template>
        <template v-if="route().current() === 'movie2.create'">
          <li aria-current="page">
            <div class="flex items-center">
              <svg
                class="rtl:rotate-180 w-3 h-3 mx-1 text-gray-400"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 6 10"
              >
                <path
                  stroke="currentColor"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="m1 9 4-4-4-4"
                />
              </svg>
              <span
                class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400"
                >動画追加</span
              >
            </div>
          </li>
        </template>
        <template v-if="route().current() === 'movie2.categoryAndTag'">
          <li aria-current="page">
            <div class="flex items-center">
              <svg
                class="rtl:rotate-180 w-3 h-3 mx-1 text-gray-400"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 6 10"
              >
                <path
                  stroke="currentColor"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="m1 9 4-4-4-4"
                />
              </svg>
              <span
                class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400"
                >カテゴリー・タグ追加</span
              >
            </div>
          </li>
        </template>
      </template>
      <template v-else-if="route().current().startsWith('fax')">
        <li>
          <div class="flex items-center">
            <svg
              class="rtl:rotate-180 block w-3 h-3 mx-1 text-gray-400"
              aria-hidden="true"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 6 10"
            >
              <path
                stroke="currentColor"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="m1 9 4-4-4-4"
              />
            </svg>
            <a
              :href="route('fax')"
              class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white"
            >
              FAX振分設定</a
            >
          </div>
        </li>

        <!-- 下階層 -->
        <template v-if="route().current() === 'fax.manual'">
          <li aria-current="page">
            <div class="flex items-center">
              <svg
                class="rtl:rotate-180 w-3 h-3 mx-1 text-gray-400"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 6 10"
              >
                <path
                  stroke="currentColor"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="m1 9 4-4-4-4"
                />
              </svg>
              <span
                class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400"
                >マニュアル</span
              >
            </div>
          </li>
        </template>
        <template v-if="route().current() === 'fax'">
          <li aria-current="page">
            <div class="flex items-center">
              <svg
                class="rtl:rotate-180 w-3 h-3 mx-1 text-gray-400"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 6 10"
              >
                <path
                  stroke="currentColor"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="m1 9 4-4-4-4"
                />
              </svg>
              <span
                class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400"
                >振り分け</span
              >
            </div>
          </li>
        </template>
        <template v-if="route().current() === 'fax.group'">
          <li aria-current="page">
            <div class="flex items-center">
              <svg
                class="rtl:rotate-180 w-3 h-3 mx-1 text-gray-400"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 6 10"
              >
                <path
                  stroke="currentColor"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="m1 9 4-4-4-4"
                />
              </svg>
              <span
                class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400"
                >グループ</span
              >
            </div>
          </li>
        </template>
        <template v-if="route().current() === 'fax.folder'">
          <li aria-current="page">
            <div class="flex items-center">
              <svg
                class="rtl:rotate-180 w-3 h-3 mx-1 text-gray-400"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 6 10"
              >
                <path
                  stroke="currentColor"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="m1 9 4-4-4-4"
                />
              </svg>
              <span
                class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400"
                >フォルダ割り当て</span
              >
            </div>
          </li>
        </template>

      </template>

      <template v-else-if="route().current().startsWith('signage')">
        <li>
          <div class="flex items-center">
            <svg
              class="rtl:rotate-180 block w-3 h-3 mx-1 text-gray-400"
              aria-hidden="true"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 6 10"
            >
              <path
                stroke="currentColor"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="m1 9 4-4-4-4"
              />
            </svg>
            <a
              :href="route('fax')"
              class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white"
            >
              サイネージ</a
            >
          </div>
        </li>

        <!-- 下階層 -->
        <template v-if="route().current() === 'fax.manual'">
          <li aria-current="page">
            <div class="flex items-center">
              <svg
                class="rtl:rotate-180 w-3 h-3 mx-1 text-gray-400"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 6 10"
              >
                <path
                  stroke="currentColor"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="m1 9 4-4-4-4"
                />
              </svg>
              <span
                class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400"
                >マニュアル</span
              >
            </div>
          </li>
        </template>
        <template v-if="route().current() === 'fax'">
          <li aria-current="page">
            <div class="flex items-center">
              <svg
                class="rtl:rotate-180 w-3 h-3 mx-1 text-gray-400"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 6 10"
              >
                <path
                  stroke="currentColor"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="m1 9 4-4-4-4"
                />
              </svg>
              <span
                class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400"
                >振り分け</span
              >
            </div>
          </li>
        </template>
        <template v-if="route().current() === 'fax.group'">
          <li aria-current="page">
            <div class="flex items-center">
              <svg
                class="rtl:rotate-180 w-3 h-3 mx-1 text-gray-400"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 6 10"
              >
                <path
                  stroke="currentColor"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="m1 9 4-4-4-4"
                />
              </svg>
              <span
                class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400"
                >グループ</span
              >
            </div>
          </li>
        </template>
        <template v-if="route().current() === 'fax.folder'">
          <li aria-current="page">
            <div class="flex items-center">
              <svg
                class="rtl:rotate-180 w-3 h-3 mx-1 text-gray-400"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 6 10"
              >
                <path
                  stroke="currentColor"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="m1 9 4-4-4-4"
                />
              </svg>
              <span
                class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400"
                >フォルダ割り当て</span
              >
            </div>
          </li>
        </template>
      </template>
      <template v-else-if="route().current().startsWith('remote')">
        <li>
          <div class="flex items-center">
            <svg
              class="rtl:rotate-180 block w-3 h-3 mx-1 text-gray-400"
              aria-hidden="true"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 6 10"
            >
              <path
                stroke="currentColor"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="m1 9 4-4-4-4"
              />
            </svg>
            <a
              :href="route('fax')"
              class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white"
            >
              リモート接続</a
            >
          </div>
        </li>

        <!-- 下階層 -->
        <template v-if="route().current() === 'fax.manual'">
          <li aria-current="page">
            <div class="flex items-center">
              <svg
                class="rtl:rotate-180 w-3 h-3 mx-1 text-gray-400"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 6 10"
              >
                <path
                  stroke="currentColor"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="m1 9 4-4-4-4"
                />
              </svg>
              <span
                class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400"
                >マニュアル</span
              >
            </div>
          </li>
        </template>
        <template v-if="route().current() === 'fax'">
          <li aria-current="page">
            <div class="flex items-center">
              <svg
                class="rtl:rotate-180 w-3 h-3 mx-1 text-gray-400"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 6 10"
              >
                <path
                  stroke="currentColor"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="m1 9 4-4-4-4"
                />
              </svg>
              <span
                class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400"
                >振り分け</span
              >
            </div>
          </li>
        </template>
        <template v-if="route().current() === 'fax.group'">
          <li aria-current="page">
            <div class="flex items-center">
              <svg
                class="rtl:rotate-180 w-3 h-3 mx-1 text-gray-400"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 6 10"
              >
                <path
                  stroke="currentColor"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="m1 9 4-4-4-4"
                />
              </svg>
              <span
                class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400"
                >グループ</span
              >
            </div>
          </li>
        </template>
        <template v-if="route().current() === 'fax.folder'">
          <li aria-current="page">
            <div class="flex items-center">
              <svg
                class="rtl:rotate-180 w-3 h-3 mx-1 text-gray-400"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 6 10"
              >
                <path
                  stroke="currentColor"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="m1 9 4-4-4-4"
                />
              </svg>
              <span
                class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400"
                >フォルダ割り当て</span
              >
            </div>
          </li>
        </template>
      </template>
    </ol>
  </nav>

  <main :class="{ 'py-16 px-24': true }">
    <Message />
    <slot name="content" />
  </main>
</template>
<style>
</style>