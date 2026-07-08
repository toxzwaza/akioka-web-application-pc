<script setup>
import { computed, onMounted, reactive, ref } from "vue";
import axios from "axios";
import { Link } from "@inertiajs/vue3";
import MainLayout from "@/Layouts/MainLayout.vue";
import PageHeader from "@/Components/UI/PageHeader.vue";
import Calender from "@/Components/Calender.vue";
import SearchLoading from "@/Components/Loading/SearchLoading.vue";

const selectedDate = ref(null);
const loadingDashboard = ref(false);
const loadingRecords = ref(false);

const dashboard = ref({
  kpi: {
    total_stocks: 0,
    low_stock_count: 0,
    pending_order_requests: 0,
    month_initial_order_sum: 0,
    month_initial_order_count: 0,
  },
  trends: {
    daily_operations: [],
    monthly_orders: [],
  },
  alerts: {
    low_stocks: [],
    overdue_requests: [],
  },
  rankings: {
    top_outbound_stocks: [],
    top_suppliers: [],
  },
  updated_at: null,
});

const sortOperation = reactive({
  id: null,
  name: null,
});

const inventoryOperationBaseRecords = ref([]);
const inventoryOperationRecordsByDate = ref([]);

const formatYen = (value) =>
  new Intl.NumberFormat("ja-JP", {
    style: "currency",
    currency: "JPY",
    maximumFractionDigits: 0,
  }).format(value || 0);

const maxTrendValue = computed(() => {
  const values = dashboard.value.trends.daily_operations.flatMap((row) => [
    row.inbound_count || 0,
    row.outbound_count || 0,
  ]);
  return Math.max(1, ...values);
});

const recordSort = (operationId, operationName = null) => {
  sortOperation.id = operationId || null;
  sortOperation.name = operationName;

  if (!operationId) {
    inventoryOperationRecordsByDate.value = [...inventoryOperationBaseRecords.value];
    return;
  }

  inventoryOperationRecordsByDate.value = inventoryOperationBaseRecords.value.filter(
    (record) => record.inventory_operation_id === operationId
  );
};

const fetchDashboardSummary = async () => {
  loadingDashboard.value = true;
  try {
    const response = await axios.get(route("stock.dashboard.summary"));
    dashboard.value = response.data;
  } catch (error) {
    console.error(error);
  } finally {
    loadingDashboard.value = false;
  }
};

const getInventoryOperationRecordByDate = async (targetDate) => {
  loadingRecords.value = true;
  try {
    const response = await axios.get(
      route("stock.stocks.getInventoryOperationRecordsByDate"),
      { params: { target_date: targetDate } }
    );
    inventoryOperationBaseRecords.value = response.data;
    inventoryOperationRecordsByDate.value = [...response.data];
    sortOperation.id = null;
    sortOperation.name = null;
  } catch (error) {
    console.error(error);
  } finally {
    loadingRecords.value = false;
  }
};

const handleDateClick = (dateStr) => {
  selectedDate.value = dateStr;
  getInventoryOperationRecordByDate(dateStr);
};

onMounted(async () => {
  const today = new Date();
  const yyyy = today.getFullYear();
  const mm = String(today.getMonth() + 1).padStart(2, "0");
  const dd = String(today.getDate()).padStart(2, "0");
  const formattedToday = `${yyyy}-${mm}-${dd}`;
  selectedDate.value = formattedToday;

  await Promise.all([fetchDashboardSummary(), getInventoryOperationRecordByDate(formattedToday)]);
});
</script>

<template>
  <MainLayout :title="'在庫管理'">
    <template #content>
      <PageHeader
        title="在庫管理HOME"
        subtitle="在庫逼迫・発注負荷・入出庫動向を一画面で確認できます。"
      />

      <!-- KPI -->
      <div class="grid grid-cols-1 xl:grid-cols-4 gap-4 mb-6">
        <div class="bg-surface-base border border-border rounded-card shadow-card p-4">
          <p class="text-sm text-content-muted">有効在庫品目数</p>
          <p class="text-2xl font-bold text-content">
            {{ dashboard.kpi.total_stocks }}
          </p>
          <Link :href="route('stock.stocks')" class="text-xs text-primary-600 hover:underline">在庫一覧へ</Link>
        </div>
        <div class="bg-surface-base border border-error-100 rounded-card shadow-card p-4">
          <p class="text-sm text-error-600">発注点割れ</p>
          <p class="text-2xl font-bold text-error-700">
            {{ dashboard.kpi.low_stock_count }}
          </p>
          <Link :href="route('stock.stocks')" class="text-xs text-primary-600 hover:underline">優先確認</Link>
        </div>
        <div class="bg-surface-base border border-warning-100 rounded-card shadow-card p-4">
          <p class="text-sm text-warning-600">未処理発注依頼</p>
          <p class="text-2xl font-bold text-warning-700">
            {{ dashboard.kpi.pending_order_requests }}
          </p>
          <Link :href="route('stock.order_requests')" class="text-xs text-primary-600 hover:underline">発注依頼一覧へ</Link>
        </div>
        <div class="bg-surface-base border border-success-100 rounded-card shadow-card p-4">
          <p class="text-sm text-success-600">今月発注金額 / 件数</p>
          <p class="text-xl font-bold text-success-700">
            {{ formatYen(dashboard.kpi.month_initial_order_sum) }}
          </p>
          <p class="text-sm text-content-muted">
            {{ dashboard.kpi.month_initial_order_count }} 件
          </p>
          <Link :href="route('stock.initialOrders')" class="text-xs text-primary-600 hover:underline">発注一覧へ</Link>
        </div>
      </div>

      <!-- トレンド / 月次 -->
      <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
        <div class="xl:col-span-2 bg-surface-base border border-border rounded-card shadow-card p-4">
          <div class="flex items-center justify-between mb-3">
            <h2 class="font-semibold text-content">入出庫トレンド（直近30日）</h2>
            <span class="text-xs text-content-muted">更新: {{ dashboard.updated_at || "-" }}</span>
          </div>
          <div class="space-y-2">
            <div
              v-for="row in dashboard.trends.daily_operations"
              :key="row.date"
              class="grid grid-cols-12 gap-2 items-center"
            >
              <div class="col-span-2 text-xs text-content-muted">{{ row.label }}</div>
              <div class="col-span-5">
                <div
                  class="h-2 rounded bg-primary-400"
                  :style="{ width: `${(row.outbound_count / maxTrendValue) * 100}%` }"
                ></div>
                <p class="text-[10px] text-content-muted mt-1">出庫 {{ row.outbound_count }}</p>
              </div>
              <div class="col-span-5">
                <div
                  class="h-2 rounded bg-success-500"
                  :style="{ width: `${(row.inbound_count / maxTrendValue) * 100}%` }"
                ></div>
                <p class="text-[10px] text-content-muted mt-1">入庫 {{ row.inbound_count }}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-surface-base border border-border rounded-card shadow-card p-4">
          <h2 class="font-semibold text-content mb-3">月次発注サマリ</h2>
          <div class="space-y-2">
            <div
              v-for="month in dashboard.trends.monthly_orders"
              :key="month.month"
              class="flex items-center justify-between text-sm border-b border-border pb-2"
            >
              <span class="text-content-muted">{{ month.month }}</span>
              <div class="text-right">
                <p class="font-semibold text-content">{{ formatYen(month.order_sum) }}</p>
                <p class="text-xs text-content-muted">{{ month.order_count }}件</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- アラート / ランキング -->
      <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
        <div class="bg-surface-base border border-border rounded-card shadow-card p-4">
          <h2 class="font-semibold text-content mb-3">発注点割れ（上位）</h2>
          <div class="space-y-2 text-sm">
            <div
              v-for="stock in dashboard.alerts.low_stocks"
              :key="`${stock.stock_id}-${stock.address}`"
              class="border border-error-100 rounded-md p-2"
            >
              <Link :href="route('stock.show.stocks', { stock_id: stock.stock_id })" class="text-error-700 font-semibold hover:underline">
                {{ stock.stock_name }}
              </Link>
              <p class="text-content-muted">
                在庫 {{ stock.quantity }} / 発注点 {{ stock.reorder_point }}
              </p>
              <p class="text-xs text-content-subtle">{{ stock.location_name }} {{ stock.address }}</p>
            </div>
          </div>
        </div>

        <div class="bg-surface-base border border-border rounded-card shadow-card p-4">
          <h2 class="font-semibold text-content mb-3">納期超過リスク</h2>
          <div class="space-y-2 text-sm">
            <div
              v-for="order in dashboard.alerts.overdue_requests"
              :key="order.id"
              class="border border-warning-100 rounded-md p-2"
            >
              <Link :href="route('stock.show.stocks', { stock_id: order.stock_id })" class="text-warning-700 font-semibold hover:underline">
                {{ order.stock_name || "未紐付け在庫" }}
              </Link>
              <p class="text-xs text-content-subtle">希望納期: {{ order.desire_delivery_date }}</p>
            </div>
          </div>
        </div>

        <div class="bg-surface-base border border-border rounded-card shadow-card p-4">
          <h2 class="font-semibold text-content mb-3">ランキング</h2>
          <div class="mb-3">
            <p class="text-xs text-content-muted mb-1">出庫量 上位</p>
            <div class="space-y-1 text-sm">
              <div
                v-for="row in dashboard.rankings.top_outbound_stocks.slice(0, 5)"
                :key="row.stock_id"
                class="flex justify-between"
              >
                <span class="text-content truncate mr-2">{{ row.stock_name }}</span>
                <span class="font-semibold text-content">{{ row.total_quantity }}</span>
              </div>
            </div>
          </div>
          <div>
            <p class="text-xs text-content-muted mb-1">仕入先（当月発注金額）</p>
            <div class="space-y-1 text-sm">
              <div
                v-for="row in dashboard.rankings.top_suppliers"
                :key="row.supplier_id"
                class="flex justify-between"
              >
                <span class="text-content truncate mr-2">{{ row.supplier_name || '未設定' }}</span>
                <span class="font-semibold text-content">{{ formatYen(row.total_amount) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- カレンダー / 作業履歴 -->
      <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">
        <div class="xl:col-span-2 bg-surface-base border border-border rounded-card shadow-card p-4">
          <h2 class="font-semibold text-content mb-3">日付選択</h2>
          <Calender @date-click="handleDateClick" />
        </div>

        <div class="xl:col-span-3 bg-surface-base border border-border rounded-card shadow-card p-4">
          <div class="flex items-center justify-between mb-3">
            <h2 class="font-semibold text-content">
              作業履歴 {{ selectedDate }}
            </h2>
            <div class="text-xs text-content-muted">{{ inventoryOperationRecordsByDate.length }}件</div>
          </div>

          <div class="flex flex-wrap gap-2 mb-3">
            <button
              @click="recordSort(2, '出庫')"
              class="px-3 py-1 text-xs rounded-md border transition-colors"
              :class="sortOperation.id === 2 ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-content border-border hover:bg-surface-sunken'"
            >
              出庫
            </button>
            <button
              @click="recordSort(8, '入庫')"
              class="px-3 py-1 text-xs rounded-md border transition-colors"
              :class="sortOperation.id === 8 ? 'bg-success-600 text-white border-success-600' : 'bg-white text-content border-border hover:bg-surface-sunken'"
            >
              入庫
            </button>
            <button
              @click="recordSort(9, '数量編集')"
              class="px-3 py-1 text-xs rounded-md border transition-colors"
              :class="sortOperation.id === 9 ? 'bg-content text-white border-content' : 'bg-white text-content border-border hover:bg-surface-sunken'"
            >
              数量編集
            </button>
            <button @click="recordSort(0)" class="px-3 py-1 text-xs rounded-md border bg-white text-content border-border hover:bg-surface-sunken transition-colors">
              リセット
            </button>
          </div>

          <div class="max-h-96 overflow-y-auto space-y-2">
            <div
              v-for="record in inventoryOperationRecordsByDate"
              :key="`${record.created_at}-${record.stock_id}-${record.inventory_operation_id}`"
              class="border border-border rounded-md p-3 text-sm"
            >
              <div class="flex justify-between mb-1">
                <Link :href="route('stock.show.stocks', { stock_id: record.stock_id })" class="font-semibold text-primary-700 hover:underline">
                  {{ record.stock_name || "在庫未設定" }}
                </Link>
                <span class="text-content-muted">{{ new Date(record.created_at).toLocaleTimeString("ja-JP", { hour: "2-digit", minute: "2-digit" }) }}</span>
              </div>
              <p class="text-content">
                {{ record.inventory_operation_name }} / 数量 {{ record.quantity }}
                <span v-if="record.inventory_operation_id === 9">（変更前: {{ record.bef_quantity }}）</span>
              </p>
              <p class="text-xs text-content-subtle">{{ record.location_name }} {{ record.address }}</p>
            </div>
          </div>
        </div>
      </div>

      <SearchLoading
        :isLoading="loadingDashboard || loadingRecords"
        title="在庫ダッシュボードを更新中..."
        message="集計データと作業履歴を取得しています。"
      />
    </template>
  </MainLayout>
</template>
