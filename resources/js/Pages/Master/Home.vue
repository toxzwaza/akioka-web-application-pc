<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import MainTitle from "@/Components/Title/MainTitle.vue";
import { Link, router } from "@inertiajs/vue3";
import { onMounted, ref, watch } from "vue";
import GroupCreateForm from "@/Pages/Master/Components/GroupCreateForm.vue";
import UserCreateForm from "@/Pages/Master/Components/UserCreateForm.vue";
import UserListPanel from "@/Pages/Master/Components/UserListPanel.vue";

const props = defineProps({
  users: Array,
  groups: Array,
  processes: Array,
  positions: Array,
  initialTab: {
    type: String,
    default: "list",
  },
  stats: {
    type: Object,
    default: () => ({ user_count: 0, group_count: 0 }),
  },
});

const tab = ref(props.initialTab);

watch(
  () => props.initialTab,
  (t) => {
    if (t) tab.value = t;
  }
);

onMounted(() => {
  const params = new URLSearchParams(window.location.search);
  const t = params.get("tab");
  if (t && ["group", "user", "list"].includes(t)) {
    tab.value = t;
  }
});

const setTab = (t) => {
  tab.value = t;
  const url = new URL(window.location.href);
  url.searchParams.set("tab", t);
  window.history.replaceState({}, "", url.pathname + url.search);
};

const reloadMasterData = () => {
  router.reload({
    only: ["users", "groups", "processes", "positions", "stats", "initialTab"],
  });
};

const quickLinks = [
  {
    label: "カレンダー",
    desc: "休日・スケジュール設定",
    routeName: "master.calender",
    icon: "calendar_month",
  },
  {
    label: "デバイス情報",
    desc: "端末の登録・編集",
    routeName: "master.devices",
    icon: "devices",
  },
  {
    label: "承認フロー",
    desc: "承認フローの作成・管理",
    routeName: "master.approval-flows.index",
    icon: "assignment",
  },
];

const tabClass = (id) =>
  tab.value === id
    ? "border-indigo-600 text-indigo-600 bg-white"
    : "border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300";
</script>
<template>
  <MainLayout title="基幹マスタ管理">
    <template #content>
      <MainTitle
        top="基幹マスタ管理"
        sub="部署・従業員の登録と一覧は下のタブで行えます。その他のマスタ機能へはカードから移動できます。"
      />

      <section
        class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-10"
        aria-label="概要"
      >
        <div
          class="rounded-lg border border-gray-200 bg-gray-50 p-5 shadow-sm"
        >
          <p class="text-sm text-gray-500">登録従業員数</p>
          <p class="text-2xl font-bold text-gray-900">
            {{ stats.user_count }}
          </p>
        </div>
        <div
          class="rounded-lg border border-gray-200 bg-gray-50 p-5 shadow-sm"
        >
          <p class="text-sm text-gray-500">部署数</p>
          <p class="text-2xl font-bold text-gray-900">
            {{ stats.group_count }}
          </p>
        </div>
      </section>

      <section class="mb-10" aria-label="その他のマスタ機能">
        <h2 class="text-lg font-bold text-gray-800 mb-4">その他の機能</h2>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <Link
            v-for="item in quickLinks"
            :key="item.routeName"
            :href="route(item.routeName)"
            class="flex items-start gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm transition hover:border-indigo-300 hover:shadow-md"
          >
            <span
              class="material-symbols-outlined text-indigo-600 text-2xl shrink-0"
            >
              {{ item.icon }}
            </span>
            <div>
              <p class="font-semibold text-gray-900">{{ item.label }}</p>
              <p class="text-sm text-gray-500 mt-1">{{ item.desc }}</p>
            </div>
          </Link>
        </div>
      </section>

      <section class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div
          class="flex flex-wrap gap-1 border-b border-gray-200 bg-gray-100 px-2 pt-2"
          role="tablist"
        >
          <button
            type="button"
            role="tab"
            :aria-selected="tab === 'group'"
            :class="[
              'px-4 py-3 text-sm font-medium border-b-2 -mb-px transition rounded-t-md',
              tabClass('group'),
            ]"
            @click="setTab('group')"
          >
            部署登録
          </button>
          <button
            type="button"
            role="tab"
            :aria-selected="tab === 'user'"
            :class="[
              'px-4 py-3 text-sm font-medium border-b-2 -mb-px transition rounded-t-md',
              tabClass('user'),
            ]"
            @click="setTab('user')"
          >
            従業員登録
          </button>
          <button
            type="button"
            role="tab"
            :aria-selected="tab === 'list'"
            :class="[
              'px-4 py-3 text-sm font-medium border-b-2 -mb-px transition rounded-t-md',
              tabClass('list'),
            ]"
            @click="setTab('list')"
          >
            全従業員参照
          </button>
        </div>

        <div class="p-6">
          <div v-show="tab === 'group'">
            <h2 class="text-xl font-bold text-gray-800 mb-2">部署登録</h2>
            <p class="text-gray-500 text-sm mb-6">
              部署名と電話番号を登録します。登録後、従業員登録の所属部署に反映されます。
            </p>
            <GroupCreateForm @registered="reloadMasterData" />
          </div>

          <div v-show="tab === 'user'">
            <h2 class="text-xl font-bold text-gray-800 mb-2">従業員登録</h2>
            <p class="text-gray-500 text-sm mb-6">
              新規ユーザーを登録します。登録内容は「全従業員参照」タブで確認できます。
            </p>
            <UserCreateForm
              :groups="groups"
              :processes="processes"
              :positions="positions"
              @registered="reloadMasterData"
            />
          </div>

          <div v-show="tab === 'list'">
            <h2 class="text-xl font-bold text-gray-800 mb-2">全従業員参照</h2>
            <p class="text-gray-500 text-sm mb-6">
              従業員一覧の確認・フィルター・社員カード印刷ができます。
            </p>
            <UserListPanel
              :users="users"
              :groups="groups"
              :processes="processes"
              :positions="positions"
            />
          </div>
        </div>
      </section>
    </template>
  </MainLayout>
</template>
<style scoped lang="scss"></style>
