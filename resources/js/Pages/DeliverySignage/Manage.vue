<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import axios from "axios";
import { computed, onMounted, ref } from "vue";

const items = ref([]);
/** 初回は即スケルトン表示（データ取得完了まで空画面を避ける） */
const isLoading = ref(true);
const isSavingOrder = ref(false);
const errorMessage = ref("");
const dragIndex = ref(null);

const manualForm = ref({
  order_user: "",
  name: "",
  s_name: "",
});

const rowKey = (item) => `${item.item_type}-${item.item_id}`;
const isManual = (item) => item.item_type === "manual";

const loadItems = async () => {
  isLoading.value = true;
  errorMessage.value = "";
  try {
    const res = await axios.get(route("delivery-signage.data"));
    items.value = res.data ?? [];
  } catch (error) {
    errorMessage.value = "一覧の取得に失敗しました。";
    console.error(error);
  } finally {
    isLoading.value = false;
  }
};

const hasItems = computed(() => items.value.length > 0);

const resetManualForm = () => {
  manualForm.value.order_user = "";
  manualForm.value.name = "";
  manualForm.value.s_name = "";
};

const createManualItem = async () => {
  errorMessage.value = "";
  try {
    await axios.post(route("delivery-signage.manual.store"), manualForm.value);
    resetManualForm();
    await loadItems();
  } catch (error) {
    errorMessage.value = "手動登録に失敗しました。";
    console.error(error);
  }
};

const deleteManualItem = async (item) => {
  if (!isManual(item)) return;
  if (!window.confirm(`「${item.name || "この項目"}」を削除します。よろしいですか？`)) return;
  errorMessage.value = "";
  try {
    await axios.delete(route("delivery-signage.manual.delete", item.item_id));
    await loadItems();
  } catch (error) {
    errorMessage.value = "削除に失敗しました。";
    console.error(error);
  }
};

const onDragStart = (index) => {
  dragIndex.value = index;
};

const onDrop = (dropIndex) => {
  if (dragIndex.value === null || dragIndex.value === dropIndex) {
    dragIndex.value = null;
    return;
  }

  const moved = items.value.splice(dragIndex.value, 1)[0];
  items.value.splice(dropIndex, 0, moved);
  dragIndex.value = null;
};

const saveOrder = async () => {
  isSavingOrder.value = true;
  errorMessage.value = "";
  try {
    await axios.post(route("delivery-signage.reorder"), {
      items: items.value.map((item) => ({
        item_type: item.item_type,
        item_id: item.item_id,
      })),
    });
    await loadItems();
  } catch (error) {
    errorMessage.value = "並び順の保存に失敗しました。";
    console.error(error);
  } finally {
    isSavingOrder.value = false;
  }
};

onMounted(async () => {
  await loadItems();
});
</script>

<template>
  <MainLayout :title="'納品サイネージ管理'">
    <template #content>
      <section class="px-2 pb-8">
        <div class="mb-6 rounded-2xl border border-blue-100 bg-white p-6 shadow-sm">
          <h1 class="text-2xl font-bold text-gray-800">納品サイネージ管理</h1>
          <p class="mt-1 text-sm text-gray-600">
            現在表示中データ（納品済・未引渡 + 手動登録）の確認と表示順変更ができます。
          </p>
        </div>

        <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
          <h2 class="mb-4 text-lg font-semibold text-gray-800">手動登録</h2>
          <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
            <input v-model="manualForm.order_user" type="text" class="rounded border border-gray-300 px-3 py-2 text-sm" placeholder="注文者" />
            <input v-model="manualForm.name" type="text" class="rounded border border-gray-300 px-3 py-2 text-sm" placeholder="品名" />
            <input v-model="manualForm.s_name" type="text" class="rounded border border-gray-300 px-3 py-2 text-sm" placeholder="品番" />
            <button type="button" class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700" @click="createManualItem">
              追加
            </button>
          </div>
        </div>

        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
          <p class="text-sm text-gray-700">
            <span v-if="isLoading">読み込み中…</span>
            <span v-else>表示件数: {{ items.length }}件</span>
          </p>
          <div class="flex flex-wrap gap-2">
            <button
              type="button"
              class="rounded border border-blue-500 px-4 py-2 text-sm font-semibold text-blue-600 hover:bg-blue-50 disabled:opacity-60"
              :disabled="isLoading"
              @click="loadItems"
            >
              再読込
            </button>
            <a :href="route('signage.content.delivery')" target="_blank" class="rounded border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
              本番画面を開く
            </a>
            <button
              type="button"
              class="rounded bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 disabled:opacity-60"
              :disabled="isSavingOrder || isLoading"
              @click="saveOrder"
            >
              並び順を保存
            </button>
          </div>
        </div>

        <p v-if="errorMessage" class="mb-4 rounded-lg bg-red-100 p-3 text-sm text-red-700">
          {{ errorMessage }}
        </p>

        <div class="rounded-2xl border border-gray-200 bg-white p-2 shadow-sm">
          <div class="w-full overflow-auto">
            <table class="w-full table-auto text-left whitespace-nowrap">
              <thead>
                <tr>
                  <th class="w-20 bg-gray-100 px-4 py-3 text-sm font-medium text-gray-900">移動</th>
                  <th class="bg-gray-100 px-4 py-3 text-sm font-medium text-gray-900">種別</th>
                  <th class="bg-gray-100 px-4 py-3 text-sm font-medium text-gray-900">注文日</th>
                  <th class="bg-gray-100 px-4 py-3 text-sm font-medium text-gray-900">納品日</th>
                  <th class="bg-gray-100 px-4 py-3 text-sm font-medium text-gray-900">注文者</th>
                  <th class="bg-gray-100 px-4 py-3 text-sm font-medium text-gray-900">品名</th>
                  <th class="bg-gray-100 px-4 py-3 text-sm font-medium text-gray-900">品番</th>
                  <th class="w-24 bg-gray-100 px-4 py-3 text-sm font-medium text-gray-900">操作</th>
                </tr>
              </thead>
              <tbody v-if="isLoading">
                <tr v-for="n in 8" :key="'sk-' + n" class="border-t animate-pulse">
                  <td class="px-4 py-4"><div class="mx-auto h-4 w-8 rounded bg-gray-200" /></td>
                  <td class="px-4 py-4"><div class="h-4 w-16 rounded bg-gray-200" /></td>
                  <td class="px-4 py-4"><div class="h-4 w-24 rounded bg-gray-200" /></td>
                  <td class="px-4 py-4"><div class="h-4 w-24 rounded bg-gray-200" /></td>
                  <td class="px-4 py-4"><div class="h-4 w-28 rounded bg-gray-200" /></td>
                  <td class="px-4 py-4"><div class="h-4 w-40 rounded bg-gray-200" /></td>
                  <td class="px-4 py-4"><div class="h-4 w-24 rounded bg-gray-200" /></td>
                  <td class="px-4 py-4"><div class="h-4 w-16 rounded bg-gray-200" /></td>
                </tr>
              </tbody>
              <tbody v-else>
                <tr
                  v-for="(item, idx) in items"
                  :key="rowKey(item)"
                  class="border-t"
                  draggable="true"
                  @dragstart="onDragStart(idx)"
                  @dragover.prevent
                  @drop="onDrop(idx)"
                >
                  <td class="px-4 py-3 text-center text-gray-500">::</td>
                  <td class="px-4 py-3">{{ isManual(item) ? "手動" : "納品物" }}</td>
                  <td class="px-4 py-3">{{ item.order_date ? new Date(item.order_date).toLocaleDateString("ja-JP") : "-" }}</td>
                  <td class="px-4 py-3">{{ item.delivery_date ? new Date(item.delivery_date).toLocaleDateString("ja-JP") : "-" }}</td>
                  <td class="px-4 py-3">{{ item.order_user }}</td>
                  <td class="px-4 py-3">{{ item.name }}</td>
                  <td class="px-4 py-3">{{ item.s_name }}</td>
                  <td class="px-4 py-3 text-center">
                    <button
                      v-if="isManual(item)"
                      type="button"
                      class="rounded bg-red-500 px-3 py-1 text-xs font-semibold text-white hover:bg-red-600"
                      @click="deleteManualItem(item)"
                    >
                      削除
                    </button>
                  </td>
                </tr>
                <tr v-if="!hasItems">
                  <td colspan="8" class="px-4 py-8 text-center text-gray-500">表示対象データはありません。</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </template>
  </MainLayout>
</template>
