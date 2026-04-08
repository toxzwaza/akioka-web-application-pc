<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { Link } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
  dashboard: {
    type: Object,
    required: true,
  },
});

const stats = computed(() => props.dashboard.stats ?? {});
const announcements = computed(() => props.dashboard.announcements ?? []);
const recentNotifications = computed(
  () => props.dashboard.recent_notifications ?? []
);
const recentLogs = computed(() => props.dashboard.recent_logs ?? []);
const todayLabel = computed(() => props.dashboard.today_label ?? "");

function announcementTypeClass(type) {
  switch (type) {
    case "warning":
      return "border-amber-200 bg-amber-50 text-amber-900";
    case "error":
      return "border-red-200 bg-red-50 text-red-900";
    default:
      return "border-slate-200 bg-slate-50 text-slate-800";
  }
}

function announcementBadgeClass(type) {
  switch (type) {
    case "warning":
      return "bg-amber-100 text-amber-800";
    case "error":
      return "bg-red-100 text-red-800";
    default:
      return "bg-slate-200 text-slate-700";
  }
}

function logLevelClass(level) {
  const n = Number(level);
  if (n === 2) return "bg-red-100 text-red-800";
  if (n === 1) return "bg-amber-100 text-amber-800";
  return "bg-slate-100 text-slate-700";
}

function logLevelLabel(level) {
  const n = Number(level);
  if (n === 2) return "error";
  if (n === 1) return "warn";
  return "info";
}

/** ISO / Carbon 文字列を YYYY/MM/DD 表示用に短縮 */
function formatDate(val) {
  if (!val) return "";
  const s = typeof val === "string" ? val : String(val);
  return s.slice(0, 10).replace(/-/g, "/");
}

function formatDateTime(val) {
  if (!val) return "";
  const s = typeof val === "string" ? val : String(val);
  return s.replace("T", " ").slice(0, 19);
}

const kpiCards = computed(() => [
  {
    key: "stocks",
    label: "有効在庫品目",
    value: stats.value.stocks_count ?? 0,
    hint: "マスタ登録済み品目数",
    href: route("stock.stocks"),
    icon: "inventory_2",
  },
  {
    key: "pending",
    label: "発注依頼・承認待ち",
    value: stats.value.order_requests_pending ?? 0,
    hint: "要対応の依頼件数",
    href: route("stock.order_requests"),
    icon: "pending_actions",
  },
  {
    key: "low",
    label: "発注点以下（保管行）",
    value: stats.value.low_stock_locations ?? 0,
    hint: "quantity ≦ 発注点",
    href: route("stock.stocks"),
    icon: "warning",
  },
  {
    key: "lunch",
    label: "本日の弁当注文",
    value: stats.value.lunch_today_count ?? 0,
    hint: "注文確定分",
    href: route("lunch.order"),
    icon: "restaurant",
  },
]);

const modules = computed(() => [
  {
    title: "在庫管理",
    desc: "品目・発注依頼・初期発注",
    href: route("stock"),
    icon: "list_alt",
  },
  {
    title: "発注管理",
    desc: "消耗品発注・稟議",
    href: route("order"),
    icon: "shopping_cart",
  },
  {
    title: "弁当注文",
    desc: "注文・予約・履歴",
    href: route("lunch.order"),
    icon: "restaurant",
  },
  {
    title: "動画視聴",
    desc: "録画・メモ・タグ",
    href: route("movie2"),
    icon: "live_tv",
  },
  {
    title: "シール作成",
    desc: "ラベル印刷",
    href: route("label.home"),
    icon: "label",
  },
  {
    title: "基幹マスタ",
    desc: "ユーザー・マスタ",
    href: route("master"),
    icon: "database",
  },
  {
    title: "通知",
    desc: "通知キュー一覧",
    href: route("notification.home"),
    icon: "notifications",
  },
  {
    title: "ログ",
    desc: "システムログ",
    href: route("log.home"),
    icon: "history",
  },
]);
</script>
<template>
  <MainLayout :title="'HOME'">
    <template #content>
      <div class="min-h-[calc(100vh-8rem)] bg-gradient-to-b from-slate-50 to-white text-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <!-- ヘッダ -->
          <div
            class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8"
          >
            <div>
              <p class="text-sm font-medium text-slate-500 tracking-wide">
                {{ todayLabel }}
              </p>
              <h1
                class="mt-1 text-2xl sm:text-3xl font-semibold text-slate-900 tracking-tight"
              >
                ダッシュボード
              </h1>
              <p class="mt-2 text-slate-600 text-sm max-w-xl">
                社内システムの主要指標とお知らせです。各カードから詳細画面へ移動できます。
              </p>
            </div>
          </div>

          <!-- KPI -->
          <div
            class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-10"
          >
            <Link
              v-for="card in kpiCards"
              :key="card.key"
              :href="card.href"
              class="group rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm hover:shadow-md hover:border-slate-300 transition-all duration-200"
            >
              <div class="flex items-start justify-between gap-3">
                <div>
                  <p class="text-xs font-medium uppercase tracking-wider text-slate-500">
                    {{ card.label }}
                  </p>
                  <p
                    class="mt-2 text-3xl font-semibold tabular-nums text-slate-900"
                  >
                    {{ card.value }}
                  </p>
                  <p class="mt-1 text-xs text-slate-500">
                    {{ card.hint }}
                  </p>
                </div>
                <span
                  class="material-symbols-outlined text-3xl text-slate-400 group-hover:text-blue-600 transition-colors"
                  aria-hidden="true"
                >
                  {{ card.icon }}
                </span>
              </div>
            </Link>
          </div>

          <!-- お知らせ + フィード -->
          <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-10">
            <section class="lg:col-span-3 rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
              <div
                class="flex items-center justify-between px-5 py-4 border-b border-slate-100 bg-slate-50/80"
              >
                <h2 class="text-sm font-semibold text-slate-800">
                  お知らせ
                </h2>
              </div>
              <ul v-if="announcements.length" class="divide-y divide-slate-100">
                <li
                  v-for="a in announcements"
                  :key="a.id"
                  class="px-5 py-4"
                  :class="announcementTypeClass(a.type)"
                >
                  <div class="flex items-center gap-2 flex-wrap mb-1">
                    <span
                      class="text-xs font-medium px-2 py-0.5 rounded-full"
                      :class="announcementBadgeClass(a.type)"
                    >
                      {{ a.type || "info" }}
                    </span>
                    <span class="text-xs text-slate-500">
                      {{ formatDate(a.start_date) }} ～
                      {{ formatDate(a.end_date) }}
                    </span>
                  </div>
                  <h3 class="font-medium text-slate-900">
                    {{ a.title }}
                  </h3>
                  <p
                    class="mt-2 text-sm text-slate-600 whitespace-pre-wrap line-clamp-4"
                  >
                    {{ a.content }}
                  </p>
                </li>
              </ul>
              <p v-else class="px-5 py-10 text-center text-sm text-slate-500">
                掲載中のお知らせはありません。
              </p>
            </section>

            <div class="lg:col-span-2 flex flex-col gap-6">
              <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden flex flex-col min-h-0">
                <div
                  class="flex items-center justify-between px-4 py-3 border-b border-slate-100 bg-slate-50/80"
                >
                  <h2 class="text-sm font-semibold text-slate-800">
                    最近の通知
                  </h2>
                  <Link
                    :href="route('notification.home')"
                    class="text-xs font-medium text-blue-600 hover:text-blue-800"
                  >
                    一覧へ
                  </Link>
                </div>
                <ul
                  v-if="recentNotifications.length"
                  class="divide-y divide-slate-100 max-h-72 overflow-y-auto"
                >
                  <li
                    v-for="n in recentNotifications"
                    :key="n.id"
                    class="px-4 py-3 hover:bg-slate-50/80"
                  >
                    <a
                      v-if="n.url"
                      :href="n.url"
                      class="block text-left w-full"
                    >
                      <p class="text-sm font-medium text-slate-900 line-clamp-2">
                        {{ n.title }}
                      </p>
                      <p class="text-xs text-slate-500 mt-1">
                        {{ formatDateTime(n.created_at) }}
                      </p>
                    </a>
                    <div v-else>
                      <p class="text-sm font-medium text-slate-900 line-clamp-2">
                        {{ n.title }}
                      </p>
                      <p class="text-xs text-slate-500 mt-1">
                        {{ formatDateTime(n.created_at) }}
                      </p>
                    </div>
                  </li>
                </ul>
                <p v-else class="px-4 py-8 text-center text-sm text-slate-500">
                  通知はまだありません。
                </p>
              </section>

              <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden flex flex-col min-h-0">
                <div
                  class="flex items-center justify-between px-4 py-3 border-b border-slate-100 bg-slate-50/80"
                >
                  <h2 class="text-sm font-semibold text-slate-800">
                    最近のログ
                  </h2>
                  <Link
                    :href="route('log.home')"
                    class="text-xs font-medium text-blue-600 hover:text-blue-800"
                  >
                    一覧へ
                  </Link>
                </div>
                <ul
                  v-if="recentLogs.length"
                  class="divide-y divide-slate-100 max-h-72 overflow-y-auto"
                >
                  <li
                    v-for="log in recentLogs"
                    :key="log.id"
                    class="px-4 py-3"
                  >
                    <div class="flex items-center gap-2 flex-wrap">
                      <span
                        class="text-[10px] font-mono uppercase px-1.5 py-0.5 rounded"
                        :class="logLevelClass(log.level)"
                      >
                        {{ logLevelLabel(log.level) }}
                      </span>
                      <span class="text-xs text-slate-500">
                        {{ formatDateTime(log.created_at) }}
                      </span>
                    </div>
                    <p class="text-xs text-slate-600 mt-1">
                      {{ log.device_name }} / {{ log.service_name }}
                    </p>
                    <p class="text-sm text-slate-800 mt-1 line-clamp-2">
                      {{ log.message }}
                    </p>
                  </li>
                </ul>
                <p v-else class="px-4 py-8 text-center text-sm text-slate-500">
                  ログはまだありません。
                </p>
              </section>
            </div>
          </div>

          <!-- モジュール -->
          <section class="mb-10">
            <h2 class="text-sm font-semibold text-slate-800 mb-4">
              機能へ移動
            </h2>
            <div
              class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3"
            >
              <Link
                v-for="m in modules"
                :key="m.href"
                :href="m.href"
                class="flex items-start gap-3 rounded-xl border border-slate-200 bg-white p-4 hover:border-blue-300 hover:shadow-sm transition-all"
              >
                <span
                  class="material-symbols-outlined text-2xl text-slate-500 shrink-0"
                  aria-hidden="true"
                >
                  {{ m.icon }}
                </span>
                <div class="min-w-0">
                  <p class="text-sm font-medium text-slate-900">
                    {{ m.title }}
                  </p>
                  <p class="text-xs text-slate-500 mt-0.5 line-clamp-2">
                    {{ m.desc }}
                  </p>
                </div>
              </Link>
            </div>
          </section>

          <!-- 機能追加LOG（折りたたみ） -->
          <details
            class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden group"
          >
            <summary
              class="cursor-pointer list-none px-5 py-4 flex items-center justify-between gap-4 bg-slate-50/80 hover:bg-slate-100/80 transition-colors"
            >
              <span class="text-sm font-semibold text-slate-800">
                機能追加ログ（履歴）
              </span>
              <span
                class="material-symbols-outlined text-slate-500 group-open:rotate-180 transition-transform"
              >
                expand_more
              </span>
            </summary>
            <div class="px-5 py-6 border-t border-slate-100 space-y-8 text-slate-600">
              <article class="flex flex-wrap md:flex-nowrap gap-4">
                <div class="md:w-48 shrink-0">
                  <p class="font-semibold text-slate-800 text-sm">
                    基幹マスタ管理
                  </p>
                  <p class="text-xs text-slate-500 mt-1">2024/12/07</p>
                </div>
                <div class="min-w-0 flex-grow">
                  <h3 class="text-lg font-medium text-slate-900">
                    従業員登録を追加しました。
                  </h3>
                  <p class="mt-2 text-sm leading-relaxed">
                    弊社に在籍しているすべての従業員が対象となります。「氏名・パスワード・所属部署・役職・製造工程」が必須項目となります。<br />
                    編集したい場合、従業員一覧から、対象ユーザーのIDをクリックすることで編集画面へ遷移することが可能です。
                  </p>
                  <p class="mt-3 text-sm">
                    参考：
                    <Link
                      :href="route('stock.tablet.receive')"
                      class="text-blue-600 hover:underline"
                    >タブレット納品登録</Link>
                  </p>
                </div>
              </article>
              <article class="flex flex-wrap md:flex-nowrap gap-4 border-t border-slate-100 pt-8">
                <div class="md:w-48 shrink-0">
                  <p class="font-semibold text-slate-800 text-sm">
                    タブレット納品登録
                  </p>
                  <p class="text-xs text-slate-500 mt-1">2024/12/06</p>
                </div>
                <div class="min-w-0 flex-grow">
                  <h3 class="text-lg font-medium text-slate-900">
                    タブレット用納品書登録を追加しました。
                  </h3>
                  <p class="mt-2 text-sm leading-relaxed">
                    <Link
                      :href="route('stock.tablet.receive')"
                      class="text-blue-600 hover:underline"
                    >納品登録ページ</Link>
                    より、納品書を登録できます。納品数量の登録完了後、サイネージ表示の更新につながります。
                  </p>
                </div>
              </article>
              <article class="flex flex-wrap md:flex-nowrap gap-4 border-t border-slate-100 pt-8">
                <div class="md:w-48 shrink-0">
                  <p class="font-semibold text-slate-800 text-sm">サイネージ</p>
                  <p class="text-xs text-slate-500 mt-1">2024/12/03</p>
                </div>
                <div class="min-w-0 flex-grow">
                  <h3 class="text-lg font-medium text-slate-900">
                    サイネージ機能追加
                  </h3>
                  <p class="mt-2 text-sm leading-relaxed">
                    <Link
                      :href="route('signage.home')"
                      class="text-blue-600 hover:underline"
                    >サイネージ管理画面</Link>
                    より、PDFのアップロード・公開情報の変更・ファイル削除が可能です。
                  </p>
                </div>
              </article>
            </div>
          </details>
        </div>
      </div>
    </template>
  </MainLayout>
</template>
<style scoped lang="scss">
details summary::-webkit-details-marker {
  display: none;
}
</style>
