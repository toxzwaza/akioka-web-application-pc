<script setup>
import { Head, Link } from "@inertiajs/vue3";
import Message from "@/Components/Message.vue";
import { reactive, ref, onMounted, onBeforeUnmount } from "vue";

const props = defineProps({
  title: String,
  p_none: Boolean,
});

/** FAX・サイネージ・動画・お問い合わせなど現状未使用モジュールの導線。true にすると再表示。 */
const showUnusedModuleNav = false;

const sub_nav_close = ref(false);

const sharedLogin = reactive({
  user_id: null,
  user_name: null,
  user_role: null,
});

const syncSharedLogin = () => {
  sharedLogin.user_id = localStorage.getItem("user_id");
  sharedLogin.user_name = localStorage.getItem("user_name");
  sharedLogin.user_role = localStorage.getItem("user_role");
};

const clearSharedLogin = () => {
  localStorage.removeItem("user_id");
  localStorage.removeItem("user_name");
  localStorage.removeItem("user_role");
  window.dispatchEvent(new CustomEvent("shared-login-changed"));
  syncSharedLogin();
};

onMounted(() => {
  syncSharedLogin();
  window.addEventListener("storage", syncSharedLogin);
  window.addEventListener("shared-login-changed", syncSharedLogin);
});

onBeforeUnmount(() => {
  window.removeEventListener("storage", syncSharedLogin);
  window.removeEventListener("shared-login-changed", syncSharedLogin);
});
</script>
<template>
  <Head :title="props.title" />
  <header id="header-layout" class="body-font bg-gray-700 text-white">
    <div
      class="container mx-auto flex flex-wrap p-2 flex-col md:flex-row items-center"
    >
      <a
        :href="route('home')"
        class="flex title-font font-medium items-center text-gray-900 mb-4 md:mb-0"
      >
        <img class="w-16" src="/img/base/logo.jpg" alt="" />
        <span class="ml-3 text-xl text-white gFont">管理画面</span>
      </a>
      <nav
        class="md:mr-auto md:ml-4 md:py-1 md:pl-4 md:border-l md:border-gray-400 flex flex-wrap items-center text-base justify-center"
      >
        <a
          :href="route('master')"
          :class="{
            'flex justify-center mt-4 lg:mt-0 text-gray-100 hover:text-white mr-4 py-2 px-3 rounded': true,
            'bg-blue-600 font-bold': route().current().includes('master'),
          }"
          ><span class="mr-1 material-symbols-outlined"> database </span
          >基幹マスタ管理</a
        >
        <a
          :href="route('stock')"
          :class="{
            'flex justify-center mt-4 lg:mt-0 text-gray-100 hover:text-white mr-4 py-2 px-3 rounded': true,
            'bg-blue-600 font-bold': route().current().includes('stock'),
          }"
          ><span class="mr-1 material-symbols-outlined"> list_alt </span
          >在庫管理</a
        >
        <a
          :href="route('delivery-signage.index')"
          :class="{
            'flex justify-center mt-4 lg:mt-0 text-gray-100 hover:text-white mr-4 py-2 px-3 rounded': true,
            'bg-blue-600 font-bold': route().current().includes('delivery-signage'),
          }"
          ><span class="mr-1 material-symbols-outlined"> tv </span
          >納品サイネージ管理</a
        >
        <!-- <a href="route('order')" class="mr-5 hover:text-gray-900 flex justify-center {{ Route::is('order*') ? 'font-bold text-gray-900' : ''}}"><span class="mr-1 material-symbols-outlined">
            toc
          </span>発注管理</a> -->
        <a
          :href="route('lunch')"
          :class="{
            'flex justify-center mt-4 lg:mt-0 text-gray-100 hover:text-white mr-4 py-2 px-3 rounded': true,
            'bg-blue-600 font-bold': route().current().includes('lunch'),
          }"
          ><span class="mr-1 material-symbols-outlined"> restaurant </span
          >弁当注文</a
        >

        <a
          v-if="showUnusedModuleNav"
          :href="route('movie2')"
          :class="{
            'flex justify-center mt-4 lg:mt-0 text-gray-100 hover:text-white mr-4 py-2 px-3 rounded': true,
            'bg-blue-600 font-bold': route().current().includes('movie2'),
          }"
        >
          <span class="mr-1 material-symbols-outlined"> live_tv </span>
          動画視聴</a
        >

        <!-- FAX設定 -->
        <a
          v-if="showUnusedModuleNav"
          :href="route('fax')"
          :class="{
            'flex justify-center mt-4 lg:mt-0 text-gray-100 hover:text-white mr-4 py-2 px-3 rounded': true,
            'bg-blue-600 font-bold': route().current().includes('fax'),
          }"
        >
          <span class="mr-1 material-symbols-outlined"> description </span>
          FAX振分設定</a
        >

        <!-- サイネージシステム -->
        <a
          v-if="showUnusedModuleNav"
          :href="route('signage.home')"
          :class="{
            'flex justify-center mt-4 lg:mt-0 text-gray-100 hover:text-white mr-4 py-2 px-3 rounded': true,
            'bg-blue-600 font-bold': route().current().includes('signage'),
          }"
        >
          <span class="mr-1 material-symbols-outlined"> tv </span>
          サイネージ
        </a>

        <!-- 問い合わせ管理システム -->
        <a
          v-if="showUnusedModuleNav"
          :href="route('contact.home')"
          :class="{
            'flex justify-center mt-4 lg:mt-0 text-gray-100 hover:text-white mr-4 py-2 px-3 rounded': true,
            'bg-blue-600 font-bold': route().current().includes('contact'),
          }"
        >
          <span class="mr-1 material-symbols-outlined"> description </span>
          問い合わせ
        </a>

        <!-- 通知管理 -->
        <a
          :href="route('notification.home')"
          :class="{
            'flex justify-center mt-4 lg:mt-0 text-gray-100 hover:text-white mr-4 py-2 px-3 rounded': true,
            'bg-blue-600 font-bold': route().current().includes('notification'),
          }"
        >
          <span class="mr-1 material-symbols-outlined"> notifications </span>
          通知
        </a>

        <!-- ログ管理 -->
        <a
          :href="route('log.home')"
          :class="{
            'flex justify-center mt-4 lg:mt-0 text-gray-100 hover:text-white mr-4 py-2 px-3 rounded': true,
            'bg-blue-600 font-bold': route().current().includes('log'),
          }"
        >
          <span class="mr-1 material-symbols-outlined"> list_alt </span>
          ログ
        </a>

        <!-- リモート接続 -->
        <!-- <a
          :href="route('remote')"
          class="mr-5 hover:text-gray-900 flex justify-center"
        >
          <span class="mr-1 material-symbols-outlined"> computer </span>
          リモート接続
        </a> -->
      </nav>
      <div class="flex items-center gap-3 text-sm text-gray-100">
        <template v-if="sharedLogin.user_id">
          <div class="rounded-lg bg-gray-800/70 px-3 py-2">
            <p class="font-semibold">{{ sharedLogin.user_name || "ログイン中" }}</p>
            <p class="text-xs text-gray-300">{{ sharedLogin.user_role || "担当者" }}</p>
          </div>
          <button
            type="button"
            class="rounded bg-red-600 px-3 py-2 font-semibold hover:bg-red-700"
            @click="clearSharedLogin"
          >
            ログアウト
          </button>
        </template>
        <p v-else class="rounded-lg bg-gray-800/70 px-3 py-2 text-xs">未ログイン</p>
      </div>
    </div>
  </header>

  <!-- サブナビゲーション -->

  <div
    class="flex justify-between px-5 py-3 text-gray-700 border border-gray-200 bg-gray-50 dark:bg-gray-800 dark:border-gray-700"
  >
    <nav class="flex w-4/5 justify-start">
      <div class="flex items-center mr-8">
        <i
          v-if="sub_nav_close"
          @click="sub_nav_close = !sub_nav_close"
          class="fas fa-caret-up"
        ></i>
        <i
          v-if="!sub_nav_close"
          @click="sub_nav_close = !sub_nav_close"
          class="fas fa-caret-down ml-2"
        ></i>
      </div>

      <div v-if="!sub_nav_close" class="sub_nav_container">
        <template v-if="route().current().startsWith('master')">
          <Link
            :href="route('master')"
            :class="{
              '  hover:bg-gray-100 border border-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700 me-2': true,
              'bg-white text-gray-900': route().current() != 'master',
              'bg-blue-500 text-white': route().current() == 'master',
            }"
          >
            <span class="text-gray-100 mr-1 material-symbols-outlined">
              groups
            </span>
            従業員・部署</Link
          >
          <Link
            :href="route('master.calender')"
            :class="{
              '  hover:bg-gray-100 border border-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700 me-2': true,
              'bg-white text-gray-900': route().current() != 'master.calender',
              'bg-blue-500 text-white': route().current() == 'master.calender',
            }"
          >
            <span class="text-gray-100 mr-1 material-symbols-outlined">
              list_alt
            </span>
            カレンダー</Link
          >
          <Link
            :href="route('master.devices')"
            :class="{
              '  hover:bg-gray-100 border border-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700 me-2': true,
              'bg-white text-gray-900':
                !route().current().startsWith('master.devices'),
              'bg-blue-500 text-white':
                route().current().startsWith('master.devices'),
            }"
          >
            <span class="text-gray-100 mr-1 material-symbols-outlined">
              devices </span
            >デバイス情報</Link
          >
          <Link
            :href="route('master.approval-flows.index')"
            :class="{
              '  hover:bg-gray-100 border border-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700 me-2': true,
              'bg-white text-gray-900':
                !route().current().startsWith('master.approval-flows'),
              'bg-blue-500 text-white':
                route().current().startsWith('master.approval-flows'),
            }"
          >
            <span class="text-gray-100 mr-1 material-symbols-outlined">
              assignment </span
            >承認フロー作成</Link
          >
        </template>

        <template v-else-if="route().current().startsWith('stock')">
          <a
            :href="route('stock.stocks.create')"
            :class="{
              '  hover:bg-gray-100 border border-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700 me-2': true,
              'bg-white text-gray-900':
                route().current() != 'stock.stocks.create',
              'bg-blue-500 text-white':
                route().current() == 'stock.stocks.create',
            }"
          >
            <span class="text-gray-100 mr-1 material-symbols-outlined">
              edit_square </span
            >在庫追加</a
          >
          <Link
            :href="route('stock.create.initialOrders')"
            :class="{
              '  hover:bg-gray-100 border border-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700 me-2': true,
              'bg-white text-gray-900':
                route().current() != 'stock.create.initialOrders',
              'bg-blue-500 text-white':
                route().current() == 'stock.create.initialOrders',
            }"
          >
            <span class="text-gray-100 mr-1 material-symbols-outlined">
              edit_square
            </span>
            新規品発注</Link
          >
          <a
            :href="route('stock.suppliers')"
            :class="{
              '  hover:bg-gray-100 border border-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700 me-2': true,
              'bg-white text-gray-900':
                route().current() != 'stock.suppliers.create',
              'bg-blue-500 text-white':
                route().current() == 'stock.suppliers.create',
            }"
          >
            <span class="text-gray-100 mr-1 material-symbols-outlined">
              edit_square </span
            >取引先</a
          >
          <a
            :href="route('stock.locations')"
            :class="{
              '  hover:bg-gray-100 border border-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700 me-2': true,
              'bg-white text-gray-900': route().current() != 'stock.locations',
              'bg-blue-500 text-white': route().current() == 'stock.locations',
            }"
          >
            <span class="text-gray-100 mr-1 material-symbols-outlined">
              edit_square </span
            >格納先追加</a
          >

          <a
            :href="route('stock.stocks')"
            :class="{
              '  hover:bg-gray-100 border border-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700 me-2': true,
              'bg-white text-gray-900': route().current() != 'stock.stocks',
              'bg-blue-500 text-white': route().current() == 'stock.stocks',
            }"
          >
            <span class="text-gray-100 mr-1 material-symbols-outlined">
              list_alt
            </span>
            在庫一覧</a
          >
          <Link
            :href="route('stock.order_requests')"
            :class="{
              '  hover:bg-gray-100 border border-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700 me-2': true,
              'bg-white text-gray-900':
                route().current() != 'stock.order_requests',
              'bg-blue-500 text-white':
                route().current() == 'stock.order_requests',
            }"
          >
            <span class="text-gray-100 mr-1 material-symbols-outlined">
              list_alt
            </span>
            発注依頼一覧</Link
          >
          <Link
            :href="route('stock.initialOrders')"
            :class="{
              '  hover:bg-gray-100 border border-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700 me-2': true,
              'bg-white text-gray-900':
                route().current() != 'stock.initialOrders',
              'bg-blue-500 text-white':
                route().current() == 'stock.initialOrders',
            }"
          >
            <span class="text-gray-100 mr-1 material-symbols-outlined">
              list_alt
            </span>
            発注一覧</Link
          >

          <Link
            :href="route('stock.retentions')"
            :class="{
              '  hover:bg-gray-100 border border-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700 me-2': true,
              'bg-white text-gray-900': route().current() != 'stock.retentions',
              'bg-blue-500 text-white': route().current() == 'stock.retentions',
            }"
          >
            <span class="text-gray-100 mr-1 material-symbols-outlined">
              list_alt
            </span>
            滞留品</Link
          >

          <Link
            :href="route('stock.stocks.taking')"
            class="text-gray-900 bg-white hover:bg-gray-100 border border-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700 me-2"
            :class="{
              'text-blue-500 font-bold':
                route().current() == 'stock.storage_addresses',
            }"
          >
            <span class="text-gray-100 mr-1 material-symbols-outlined">
              list_alt
            </span>
            棚卸し</Link
          >
          <!-- <Link
        :href="route('stock.retained.stocks')"
        class="text-gray-900 bg-white hover:bg-gray-100 border border-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700 me-2"
        :class="{
          'text-blue-500 font-bold':
            route().current() == 'stock.retained.stocks',
        }"
      >
        <span class="text-gray-100 mr-1 material-symbols-outlined"> link </span>
        滞留品</Link
      > -->
        </template>

        <template v-else-if="route().current().startsWith('lunch')">
          <Link
            :href="route('lunch.order')"
            :class="{
              '  hover:bg-gray-100 border border-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700 me-2': true,
              'bg-white text-gray-900': route().current() != 'lunch.order',
              'bg-blue-500 text-white': route().current() == 'lunch.order',
            }"
            >当日発注書</Link
          >
          <Link
            :href="route('lunch.reserve')"
            :class="{
              '  hover:bg-gray-100 border border-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700 me-2': true,
              'bg-white text-gray-900': route().current() != 'lunch.reserve',
              'bg-blue-500 text-white': route().current() == 'lunch.reserve',
            }"
            >弁当予約</Link
          >
          <a
            :href="route('lunch.order-archive')"
            :class="{
              '  hover:bg-gray-100 border border-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700 me-2': true,
              'bg-white text-gray-900':
                route().current() != 'lunch.order-archive',
              'bg-blue-500 text-white':
                route().current() == 'lunch.order-archive',
            }"
            >弁当注文履歴</a
          >
          <a
            :href="route('lunch.create_description')"
            :class="{
              '  hover:bg-gray-100 border border-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700 me-2': true,
              'bg-white text-gray-900':
                route().current() != 'lunch.create_description',
              'bg-blue-500 text-white':
                route().current() == 'lunch.create_description',
            }"
            >備考作成</a
          >
          <a
            :href="route('lunch.export_csv')"
            :class="{
              '  hover:bg-gray-100 border border-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700 me-2': true,
              'bg-white text-gray-900': route().current() != 'lunch.export_csv',
              'bg-blue-500 text-white': route().current() == 'lunch.export_csv',
            }"
            >注文状況書き出し</a
          >
        </template>

        <template
          v-else-if="
            showUnusedModuleNav && route().current().startsWith('movie')
          "
        >
          <Link
            :class="{
              '  hover:bg-gray-100 border border-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700 me-2': true,
              'bg-white text-gray-900': route().current() != 'movie2',
              'bg-blue-500 text-white': route().current() == 'movie2',
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
              '  hover:bg-gray-100 border border-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700 me-2': true,
              'bg-white text-gray-900': route().current() != 'movie2.create',
              'bg-blue-500 text-white': route().current() == 'movie2.create',
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
              '  hover:bg-gray-100 border border-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700 me-2': true,
              'bg-white text-gray-900':
                route().current() != 'movie2.categoryAndTag',
              'bg-blue-500 text-white':
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
        <template
          v-else-if="
            showUnusedModuleNav && route().current().startsWith('fax')
          "
        >
          <Link
            :class="{
              '  hover:bg-gray-100 border border-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700 me-2': true,
              'bg-white text-gray-900': route().current() != 'fax.manual',
              'bg-blue-500 text-white': route().current() == 'fax.manual',
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
              '  hover:bg-gray-100 border border-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700 me-2': true,
              'bg-white text-gray-900': route().current() != 'fax',
              'bg-blue-500 text-white': route().current() == 'fax',
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
              '  hover:bg-gray-100 border border-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700 me-2': true,
              'bg-white text-gray-900': route().current() != 'fax.group',
              'bg-blue-500 text-white': route().current() == 'fax.group',
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
              '  hover:bg-gray-100 border border-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700 me-2': true,
              'bg-white text-gray-900': route().current() != 'fax.folder',
              'bg-blue-500 text-white': route().current() == 'fax.folder',
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
        <template v-else-if="route().current().startsWith('message')">
          <Link
            :class="{
              'mr-5 text-gray-900 bg-white hover:bg-gray-100 border border-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700 me-2': true,
              'text-indigo-600 font-bold': route().current() == 'message',
            }"
            :href="route('message')"
            ><svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-1"
              viewBox="0 0 24 24"
              fill="currentColor"
            >
              <path
                d="M6 2a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6H6zm7 7V3.5L18.5 9H13z"
              /></svg
            >通知送信</Link
          >
        </template>
      </div>
    </nav>
  </div>

  <main :class="{ 'py-16 px-24': !p_none }">
    <Message />
    <slot name="content" />
  </main>
</template>
<style lang="scss" scoped>
.gFont {
  font-family: "Noto Sans JP";
  font-optical-sizing: auto;
  font-weight: 600;
  font-style: normal;
}
</style>