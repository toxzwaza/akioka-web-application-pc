<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import axios from "axios";
import debounce from "lodash/debounce";
import { computed, onBeforeUnmount, onMounted, ref, watch } from "vue";

const initialOrders = ref([]);
const isLoading = ref(true);
const processingId = ref(null);
const errorMessage = ref("");
const isFilterCollapsed = ref(false);

let refreshTimer = null;

const filters = ref({
  keyword: "",
  orderUser: "",
  supplier: "",
  status: "",
  orderDateFrom: "",
  orderDateTo: "",
});

/** テキスト入力のたびに全件フィルタすると重いため、遅延反映 */
const effectiveFilters = ref({ ...filters.value });

const syncFiltersDebounced = debounce(() => {
  effectiveFilters.value = { ...filters.value };
}, 220);

watch(
  () => [filters.value.keyword, filters.value.orderUser, filters.value.supplier],
  () => syncFiltersDebounced(),
  { flush: "post" }
);

watch(
  () => [filters.value.status, filters.value.orderDateFrom, filters.value.orderDateTo],
  () => {
    effectiveFilters.value = { ...filters.value };
  }
);

const loadOrders = async () => {
  isLoading.value = true;
  errorMessage.value = "";

  try {
    const res = await axios.get(route("delivery-signage.data"));
    initialOrders.value = res.data;
  } catch (error) {
    errorMessage.value = "一覧の取得に失敗しました。";
    console.error(error);
  } finally {
    isLoading.value = false;
  }
};

const markReceiptDone = async (orderId) => {
  processingId.value = orderId;
  errorMessage.value = "";

  try {
    await axios.post(route("delivery-signage.receipt", { id: orderId }));
    await loadOrders();
  } catch (error) {
    errorMessage.value = "引渡済更新に失敗しました。";
    console.error(error);
  } finally {
    processingId.value = null;
  }
};

const toggleReceive = async (orderId) => {
  processingId.value = orderId;
  errorMessage.value = "";

  try {
    await axios.post(route("delivery-signage.toggle-receive", { id: orderId }));
    const target = initialOrders.value.find((order) => order.id === orderId);
    if (target) {
      target.receive_flg = target.receive_flg === 1 ? 0 : 1;
    }
  } catch (error) {
    errorMessage.value = "納品状態更新に失敗しました。";
    console.error(error);
  } finally {
    processingId.value = null;
  }
};

const getStatusLabel = (order) => {
  if (order.none_storage_flg === 1 || order.receive_flg === 1) {
    return "納品済";
  }
  return "注文中";
};

const rowClass = (order) => {
  if (order.none_storage_flg === 1 || order.receive_flg === 1) {
    return "bg-red-50 hover:bg-red-100";
  }
  return "bg-white hover:bg-gray-50";
};

const normalizeDate = (dateValue) => {
  if (!dateValue) {
    return "";
  }
  const date = new Date(dateValue);
  if (Number.isNaN(date.getTime())) {
    return "";
  }
  return date.toISOString().slice(0, 10);
};

const filteredOrders = computed(() => {
  const keyword = effectiveFilters.value.keyword.trim().toLowerCase();
  const orderUser = effectiveFilters.value.orderUser.trim().toLowerCase();
  const supplier = effectiveFilters.value.supplier.trim().toLowerCase();
  const status = effectiveFilters.value.status;
  const orderDateFrom = effectiveFilters.value.orderDateFrom;
  const orderDateTo = effectiveFilters.value.orderDateTo;

  return initialOrders.value.filter((order) => {
    const statusLabel = getStatusLabel(order);
    const orderDate = normalizeDate(order.order_date);
    const mergedText = `${order.name ?? ""} ${order.s_name ?? ""} ${order.order_no ?? ""}`.toLowerCase();
    const orderUserText = `${order.order_user ?? ""}`.toLowerCase();
    const supplierText = `${order.com_name ?? ""}`.toLowerCase();

    if (keyword && !mergedText.includes(keyword)) {
      return false;
    }
    if (orderUser && !orderUserText.includes(orderUser)) {
      return false;
    }
    if (supplier && !supplierText.includes(supplier)) {
      return false;
    }
    if (status && statusLabel !== status) {
      return false;
    }
    if (orderDateFrom && orderDate && orderDate < orderDateFrom) {
      return false;
    }
    if (orderDateTo && orderDate && orderDate > orderDateTo) {
      return false;
    }
    return true;
  });
});

const hasOrders = computed(() => filteredOrders.value.length > 0);

const resetFilters = () => {
  syncFiltersDebounced.cancel();
  filters.value.keyword = "";
  filters.value.orderUser = "";
  filters.value.supplier = "";
  filters.value.status = "";
  filters.value.orderDateFrom = "";
  filters.value.orderDateTo = "";
  effectiveFilters.value = { ...filters.value };
};

onMounted(async () => {
  await loadOrders();
  refreshTimer = setInterval(loadOrders, 300000);
});

onBeforeUnmount(() => {
  if (refreshTimer) {
    clearInterval(refreshTimer);
  }
});
</script>

<template>
  <MainLayout :title="'納品サイネージ管理'">
    <template #content>
      <section class="px-2 pb-8">
        <div class="mb-6 rounded-2xl border border-blue-100 bg-white p-6 shadow-sm">
          <h1 class="text-2xl font-bold text-gray-800">納品サイネージ管理</h1>
          <p class="mt-1 text-sm text-gray-600">
            現在サイネージ表示中の物品を確認し、表示状態を管理します。
          </p>
        </div>

        <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
          <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800">検索・フィルター</h2>
            <button
              type="button"
              class="rounded-lg border border-gray-300 px-3 py-1 text-sm text-gray-700 hover:bg-gray-50"
              :title="isFilterCollapsed ? 'フィルターを展開' : 'フィルターを折りたたむ'"
              @click="isFilterCollapsed = !isFilterCollapsed"
            >
              {{ isFilterCollapsed ? "展開" : "折りたたみ" }}
            </button>
          </div>

          <div v-if="!isFilterCollapsed" class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
            <div>
              <label class="mb-1 block text-xs font-semibold text-gray-600">品名・品番・注文No</label>
              <input
                v-model="filters.keyword"
                type="text"
                class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm focus:border-blue-500 focus:bg-white focus:outline-none"
                placeholder="キーワードで検索"
              />
            </div>

            <div>
              <label class="mb-1 block text-xs font-semibold text-gray-600">注文者</label>
              <input
                v-model="filters.orderUser"
                type="text"
                class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm focus:border-blue-500 focus:bg-white focus:outline-none"
                placeholder="注文者で検索"
              />
            </div>

            <div>
              <label class="mb-1 block text-xs font-semibold text-gray-600">注文先</label>
              <input
                v-model="filters.supplier"
                type="text"
                class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm focus:border-blue-500 focus:bg-white focus:outline-none"
                placeholder="注文先で検索"
              />
            </div>

            <div>
              <label class="mb-1 block text-xs font-semibold text-gray-600">状態</label>
              <select
                v-model="filters.status"
                class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm focus:border-blue-500 focus:bg-white focus:outline-none"
              >
                <option value="">すべて</option>
                <option value="注文中">注文中</option>
                <option value="納品済">納品済</option>
              </select>
            </div>

            <div>
              <label class="mb-1 block text-xs font-semibold text-gray-600">注文日（開始）</label>
              <input
                v-model="filters.orderDateFrom"
                type="date"
                class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm focus:border-blue-500 focus:bg-white focus:outline-none"
              />
            </div>

            <div>
              <label class="mb-1 block text-xs font-semibold text-gray-600">注文日（終了）</label>
              <input
                v-model="filters.orderDateTo"
                type="date"
                class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm focus:border-blue-500 focus:bg-white focus:outline-none"
              />
            </div>

            <div class="flex items-end gap-2">
              <button
                type="button"
                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
                :disabled="isLoading"
                @click="loadOrders"
              >
                再読込
              </button>
              <button
                type="button"
                class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                @click="resetFilters"
              >
                リセット
              </button>
            </div>
          </div>
        </div>

        <div class="mb-4 flex items-center justify-between">
          <p class="text-sm text-gray-700">
            表示件数: {{ filteredOrders.length }}件 / 全{{ initialOrders.length }}件
          </p>
        </div>

        <p v-if="errorMessage" class="mb-4 rounded-lg bg-red-100 p-3 text-sm text-red-700">
          {{ errorMessage }}
        </p>

        <div class="rounded-2xl border border-gray-200 bg-white p-2 shadow-sm">
          <div class="w-full overflow-auto">
            <table class="table-auto w-full text-left whitespace-nowrap">
              <thead>
                <tr>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl"
                  >
                    画像
                  </th>
                  <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">注文者</th>
                  <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">注文日</th>
                  <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">注文先</th>
                  <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">品名:品番</th>
                  <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">数量</th>
                  <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">状態</th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tr rounded-br"
                  >
                    操作
                  </th>
                </tr>
              </thead>
              <tbody v-if="isLoading">
                <tr v-for="n in 8" :key="'sk-' + n" class="border-t animate-pulse">
                  <td class="px-4 py-4 border-t"><div class="h-14 w-14 rounded bg-gray-200" /></td>
                  <td class="px-4 py-4 border-t"><div class="h-4 w-24 rounded bg-gray-200" /></td>
                  <td class="px-4 py-4 border-t"><div class="h-4 w-28 rounded bg-gray-200" /></td>
                  <td class="px-4 py-4 border-t"><div class="h-4 w-32 rounded bg-gray-200" /></td>
                  <td class="px-4 py-4 border-t"><div class="h-4 w-40 rounded bg-gray-200" /></td>
                  <td class="px-4 py-4 border-t"><div class="h-4 w-16 rounded bg-gray-200" /></td>
                  <td class="px-4 py-4 border-t"><div class="h-6 w-16 rounded bg-gray-200" /></td>
                  <td class="px-4 py-4 border-t"><div class="h-9 w-24 rounded bg-gray-200" /></td>
                </tr>
              </tbody>
              <tbody v-else>
                <tr
                  v-for="order in filteredOrders"
                  :key="order.item_type ? `${order.item_type}-${order.item_id}` : order.id"
                  :class="rowClass(order)"
                  class="transition-colors duration-150"
                >
                  <td class="px-4 py-3 w-24 border-t">
                    <img
                      v-if="order.img_path"
                      :src="
                        order.img_path.includes('https://')
                          ? order.img_path
                          : 'https://akioka.cloud/' + order.img_path
                      "
                      alt="物品画像"
                      class="h-14 w-14 object-cover rounded border border-gray-200"
                      loading="lazy"
                      decoding="async"
                    />
                    <div v-else class="h-14 w-14 rounded border border-gray-200 bg-gray-100" />
                  </td>
                  <td class="px-4 py-3 border-t">{{ order.order_user }}</td>
                  <td class="px-4 py-3 border-t">
                    {{
                      order.order_date
                        ? new Date(order.order_date).toLocaleDateString("ja-JP")
                        : "-"
                    }}
                  </td>
                  <td class="px-4 py-3 border-t">{{ order.com_name ?? "-" }}</td>
                  <td class="px-4 py-3 border-t">{{ order.name }} : {{ order.s_name }}</td>
                  <td class="px-4 py-3 border-t">
                    {{ order.quantity != null ? order.quantity : "" }}{{ order.order_unit ?? "" }}
                  </td>
                  <td class="px-4 py-3 border-t font-semibold">
                    <span
                      :class="{
                        'inline-flex rounded-full px-2.5 py-1 text-xs': true,
                        'bg-blue-100 text-blue-700': getStatusLabel(order) === '注文中',
                        'bg-red-100 text-red-700': getStatusLabel(order) === '納品済',
                      }"
                    >
                      {{ getStatusLabel(order) }}
                    </span>
                  </td>
                  <td class="px-4 py-3 border-t">
                    <div class="flex gap-2">
                      <button
                        type="button"
                        class="rounded bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700 disabled:opacity-60"
                        :disabled="processingId === order.id || order.item_type === 'manual'"
                        @click="markReceiptDone(order.id)"
                      >
                        引渡済
                      </button>
                      <button
                        type="button"
                        class="rounded border border-blue-500 px-3 py-2 text-xs font-semibold text-blue-600 hover:bg-blue-50 disabled:opacity-60"
                        :disabled="processingId === order.id || order.item_type === 'manual'"
                        @click="toggleReceive(order.id)"
                      >
                        納品状態切替
                      </button>
                    </div>
                  </td>
                </tr>
                <tr v-if="!hasOrders">
                  <td colspan="8" class="px-4 py-8 text-center text-gray-500 border-t">
                    フィルター条件に一致する表示対象データはありません。
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </template>
  </MainLayout>
</template>
