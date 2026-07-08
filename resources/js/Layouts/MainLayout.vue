<script setup>
import { Head } from "@inertiajs/vue3";
import Message from "@/Components/Message.vue";
import Icon from "@/Components/UI/Icon.vue";
import SidebarLink from "@/Components/UI/SidebarLink.vue";
import { mainNav, subNav } from "./navigation";
import { reactive, ref, computed, onMounted, onBeforeUnmount } from "vue";

const props = defineProps({
  title: String,
  p_none: Boolean,
});

/** FAX・サイネージ・動画・お問い合わせなど現状未使用モジュールの導線。true にすると再表示。 */
const showUnusedModuleNav = false;

/** モバイル時のサイドバー開閉 */
const sidebarOpen = ref(false);

/** 表示する主ナビ */
const visibleMainNav = computed(() =>
  mainNav.filter((item) => !item.unused || showUnusedModuleNav)
);

/** 指定モジュールの表示サブナビ */
const visibleSubNav = (key) =>
  (subNav[key] || []).filter((s) => !s.unused || showUnusedModuleNav);

const currentRoute = () => {
  try {
    return route().current() || "";
  } catch (e) {
    return "";
  }
};

const linkHref = (routeName) => {
  try {
    return route(routeName);
  } catch (e) {
    return "#";
  }
};

const isMainActive = (item) => currentRoute().includes(item.match);

const isSubActive = (s) => {
  const cur = currentRoute();
  const key = s.matchKey || s.route;
  return s.match === "startsWith" ? cur.startsWith(key) : cur === key;
};

/** ログイン情報（localStorage 共有） */
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

  <div class="min-h-screen bg-surface-muted">
    <!-- モバイル用オーバーレイ -->
    <transition
      enter-active-class="transition-opacity duration-200"
      enter-from-class="opacity-0"
      leave-active-class="transition-opacity duration-200"
      leave-to-class="opacity-0"
    >
      <div
        v-if="sidebarOpen"
        class="fixed inset-0 z-30 bg-primary-950/40 backdrop-blur-sm lg:hidden"
        @click="sidebarOpen = false"
      ></div>
    </transition>

    <!-- サイドバー -->
    <aside
      class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-surface-base border-r border-border transition-transform duration-300 lg:translate-x-0"
      :class="sidebarOpen ? 'translate-x-0 shadow-2xl' : '-translate-x-full'"
    >
      <!-- ロゴ -->
      <div class="flex h-16 shrink-0 items-center gap-2.5 px-5 border-b border-border">
        <a :href="route('home')" class="flex items-center gap-2.5">
          <img
            src="/img/base/logo.jpg"
            class="h-9 w-9 rounded-xl object-cover shadow-sm"
            alt="ロゴ"
          />
          <span class="text-[15px] font-bold tracking-tight text-content">管理画面</span>
        </a>
      </div>

      <!-- ナビ -->
      <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
        <template v-for="item in visibleMainNav" :key="item.key">
          <SidebarLink
            :href="linkHref(item.route)"
            :icon="item.icon"
            :label="item.label"
            :active="isMainActive(item)"
            component="a"
          />
          <!-- 選択中モジュールのサブナビを展開 -->
          <div
            v-if="isMainActive(item) && visibleSubNav(item.key).length"
            class="mt-0.5 mb-1 space-y-0.5"
          >
            <SidebarLink
              v-for="(s, i) in visibleSubNav(item.key)"
              :key="i"
              :href="linkHref(s.route)"
              :label="s.label"
              :active="isSubActive(s)"
              :component="s.component"
              sub
            />
          </div>
        </template>
      </nav>

      <!-- ユーザー / ログアウト -->
      <div class="shrink-0 border-t border-border p-3">
        <template v-if="sharedLogin.user_id">
          <div class="flex items-center gap-3 rounded-xl px-2 py-2">
            <span
              class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-surface-sunken text-content-muted"
            >
              <Icon name="person" size="md" />
            </span>
            <div class="min-w-0">
              <div class="truncate text-sm font-semibold text-content">
                {{ sharedLogin.user_name || "ログイン中" }}
              </div>
              <div class="text-xs text-content-subtle">
                {{ sharedLogin.user_role || "担当者" }}
              </div>
            </div>
          </div>
          <button
            type="button"
            class="mt-1 flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-medium text-content-muted transition-colors hover:bg-error-50 hover:text-error-600"
            @click="clearSharedLogin"
          >
            <Icon name="logout" size="md" />ログアウト
          </button>
        </template>
        <p v-else class="px-3 py-2 text-xs text-content-subtle">未ログイン</p>
      </div>
    </aside>

    <!-- メインエリア -->
    <div class="lg:pl-64">
      <!-- モバイル用メニューボタン（ヘッダーレス） -->
      <button
        v-if="!sidebarOpen"
        type="button"
        class="fixed left-4 top-4 z-30 rounded-lg border border-border bg-surface-base p-2 text-content-muted shadow-card transition-colors hover:text-content lg:hidden"
        aria-label="メニューを開く"
        @click="sidebarOpen = true"
      >
        <Icon name="menu" size="lg" />
      </button>

      <!-- コンテンツ -->
      <main :class="{ 'px-4 pb-8 pt-16 sm:px-6 lg:px-8 lg:pt-8': !p_none }">
        <Message />
        <slot name="content" />
      </main>
    </div>
  </div>
</template>
