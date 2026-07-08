<script setup>
import { Head } from "@inertiajs/vue3";
import Message from "@/Components/Message.vue";
import Icon from "@/Components/UI/Icon.vue";
import NavItem from "@/Components/UI/NavItem.vue";
import SubNavItem from "@/Components/UI/SubNavItem.vue";
import { mainNav, subNav, unusedSubNavGroups } from "./navigation";
import { reactive, ref, computed, onMounted, onBeforeUnmount } from "vue";

const props = defineProps({
  title: String,
  p_none: Boolean,
});

/** FAX・サイネージ・動画・お問い合わせなど現状未使用モジュールの導線。true にすると再表示。 */
const showUnusedModuleNav = false;

const sub_nav_close = ref(false);

/** 表示する主ナビ（未使用モジュールは showUnusedModuleNav で制御） */
const visibleMainNav = computed(() =>
  mainNav.filter((item) => !item.unused || showUnusedModuleNav)
);

/** 現在のルートに対応するサブナビ項目 */
const currentSubNav = computed(() => {
  let cur = "";
  try {
    cur = route().current() || "";
  } catch (e) {
    return [];
  }
  const group = Object.keys(subNav).find((key) => cur.startsWith(key));
  if (!group) return [];
  if (unusedSubNavGroups.includes(group) && !showUnusedModuleNav) return [];
  return subNav[group];
});

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

  <!-- ヘッダー -->
  <header id="header-layout" class="bg-primary-900 text-white">
    <div
      class="container mx-auto flex flex-wrap p-2 flex-col md:flex-row items-center"
    >
      <a
        :href="route('home')"
        class="flex items-center font-medium mb-4 md:mb-0"
      >
        <img class="w-16" src="/img/base/logo.jpg" alt="ロゴ" />
        <span class="ml-3 text-xl font-semibold text-white">管理画面</span>
      </a>

      <nav
        class="md:mr-auto md:ml-4 md:py-1 md:pl-4 md:border-l md:border-primary-700 flex flex-wrap items-center justify-center"
      >
        <NavItem v-for="item in visibleMainNav" :key="item.key" :item="item" />
      </nav>

      <div class="flex items-center gap-3 text-sm text-primary-100">
        <template v-if="sharedLogin.user_id">
          <div class="rounded-lg bg-primary-800 px-3 py-2">
            <p class="font-semibold text-white">
              {{ sharedLogin.user_name || "ログイン中" }}
            </p>
            <p class="text-xs text-primary-200">
              {{ sharedLogin.user_role || "担当者" }}
            </p>
          </div>
          <button
            type="button"
            class="inline-flex items-center gap-1 rounded-md bg-error-600 px-3 py-2 font-semibold text-white hover:bg-error-700 transition-colors"
            @click="clearSharedLogin"
          >
            <Icon name="logout" size="sm" />ログアウト
          </button>
        </template>
        <p v-else class="rounded-lg bg-primary-800 px-3 py-2 text-xs">
          未ログイン
        </p>
      </div>
    </div>
  </header>

  <!-- サブナビゲーション -->
  <div
    v-if="currentSubNav.length"
    class="flex items-center px-5 py-3 text-content border-b border-border bg-surface-muted"
  >
    <button
      type="button"
      class="flex items-center mr-6 text-content-muted hover:text-content transition-colors"
      :aria-label="sub_nav_close ? 'サブメニューを開く' : 'サブメニューを閉じる'"
      @click="sub_nav_close = !sub_nav_close"
    >
      <Icon :name="sub_nav_close ? 'expand_more' : 'expand_less'" size="md" />
    </button>

    <nav v-if="!sub_nav_close" class="flex flex-wrap items-center gap-y-2">
      <SubNavItem
        v-for="(item, idx) in currentSubNav"
        :key="idx"
        :item="item"
      />
    </nav>
  </div>

  <main :class="{ 'py-16 px-24': !p_none }">
    <Message />
    <slot name="content" />
  </main>
</template>
