<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import MainLayout from "@/Layouts/MainLayout.vue";
import axios from "axios";
import MainTitle from "@/Components/Title/MainTitle.vue"

// リアクティブデータ
const devices = ref({ data: [], total: 0, current_page: 1, last_page: 1 });
const loading = ref(false);
const searchQuery = ref("");
const statusFilter = ref("");
const showDeleteModal = ref(false);
const deviceToDelete = ref(null);

// 検索のデバウンス用
let searchTimeout = null;

// ページネーション用の計算プロパティ
const paginationPages = computed(() => {
  const pages = [];
  const current = devices.value.current_page;
  const last = devices.value.last_page;

  for (
    let i = Math.max(1, current - 2);
    i <= Math.min(last, current + 2);
    i++
  ) {
    pages.push(i);
  }

  return pages;
});

// デバイス一覧取得
const fetchDevices = async (page = 1) => {
  loading.value = true;
  try {
    const response = await axios.get(route("master.devices.get"), {
      params: {
        page,
        search: searchQuery.value,
        status_filter: statusFilter.value,
      },
    });
    
    console.log('API Response:', response.data); // デバッグ用
    
    devices.value = response.data;
  } catch (error) {
    console.error("デバイス情報の取得に失敗しました:", error);
    console.error("Error details:", error.response?.data); // 詳細なエラー情報
    
    // エラーの場合も空のデータ構造を設定
    devices.value = {
      data: [],
      current_page: 1,
      last_page: 1,
      total: 0,
      from: 0,
      to: 0
    };
    
    alert("デバイス情報の取得に失敗しました: " + (error.response?.data?.message || error.message));
  } finally {
    loading.value = false;
  }
};

// 検索機能（デバウンス付き）
const searchDevices = () => {
  if (searchTimeout) {
    clearTimeout(searchTimeout);
  }
  searchTimeout = setTimeout(() => {
    fetchDevices(1);
  }, 500);
};

// フィルタ機能
const filterDevices = () => {
  fetchDevices(1);
};

// ページ変更
const changePage = (page) => {
  fetchDevices(page);
};

// 接続状態のアイコンクラス取得
const getStatusIconClass = (status) => {
  switch (status) {
    case "online":
      return "bg-green-500";
    case "warning":
      return "bg-orange-500";
    default:
      return "bg-red-500";
  }
};

// 接続状態のテキスト取得
const getStatusText = (status) => {
  switch (status) {
    case "online":
      return "オンライン";
    case "warning":
      return "警告";
    default:
      return "オフライン";
  }
};

// デバイス削除
const deleteDevice = (device) => {
  deviceToDelete.value = device;
  showDeleteModal.value = true;
};

// 削除確認
const confirmDelete = async () => {
  try {
    await router.delete(
      route("master.devices.destroy", deviceToDelete.value.id)
    );
    showDeleteModal.value = false;
    deviceToDelete.value = null;
    fetchDevices(devices.value.current_page);
    alert("デバイス情報を削除しました");
  } catch (error) {
    console.error("デバイス削除に失敗しました:", error);
    alert("デバイス削除に失敗しました");
  }
};

// 初期化
onMounted(() => {
  fetchDevices();
});

// 定期的な更新（30秒ごと）
const intervalId = setInterval(() => {
  fetchDevices(devices.value.current_page);
}, 30000);

// コンポーネント破棄時にインターバルをクリア
onUnmounted(() => {
  if (intervalId) {
    clearInterval(intervalId);
  }
});
</script>
<template>
  <MainLayout>
    <template #content>
      <MainTitle :top="'デバイス情報一覧'" :sub="'デバイス情報を確認できます。新規登録する場合は、デバイス登録ページから行ってください。'"/>

      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
          <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">デバイス情報一覧</h1>
            <Link
              :href="route('master.devices.create')"
              class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
            >
              新規登録
            </Link>
          </div>

          <!-- 検索・フィルタ -->
          <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                検索
              </label>
              <input
                v-model="searchQuery"
                @input="searchDevices"
                type="text"
                placeholder="デバイス名、種類、IPアドレス、場所で検索..."
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                接続状態フィルタ
              </label>
              <select
                v-model="statusFilter"
                @change="filterDevices"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="">すべて</option>
                <option value="online">オンライン（24時間以内）</option>
                <option value="warning">警告（1週間以内）</option>
                <option value="offline">オフライン</option>
              </select>
            </div>
          </div>

          <!-- デバイス一覧テーブル -->
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    状態
                  </th>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    デバイス名
                  </th>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    種類
                  </th>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    IPアドレス
                  </th>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    場所
                  </th>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    最終アクセス
                  </th>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    操作
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr
                  v-for="device in devices.data"
                  :key="device.id"
                  class="hover:bg-gray-50"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div
                        class="w-3 h-3 rounded-full mr-2"
                        :class="getStatusIconClass(device.connection_status)"
                      ></div>
                      <span class="text-xs font-medium">
                        {{ getStatusText(device.connection_status) }}
                      </span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">
                      {{ device.name }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                      {{ device.device_type }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                      {{ device.ip_address || "-" }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                      {{ device.location || "-" }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                      {{ device.last_access_date_formatted }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                      <Link
                        :href="route('master.devices.edit', device.id)"
                        class="text-indigo-600 hover:text-indigo-900"
                      >
                        編集
                      </Link>
                      <button
                        @click="deleteDevice(device)"
                        class="text-red-600 hover:text-red-900"
                      >
                        削除
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- ページネーション -->
          <div v-if="devices.last_page > 1" class="mt-6">
            <nav class="flex justify-between items-center">
              <div class="text-sm text-gray-700">
                {{ devices.from }} - {{ devices.to }} / {{ devices.total }} 件
              </div>
              <div class="flex space-x-1">
                <button
                  v-for="page in paginationPages"
                  :key="page"
                  @click="changePage(page)"
                  :class="[
                    'px-3 py-2 text-sm font-medium rounded-md',
                    page === devices.current_page
                      ? 'bg-blue-500 text-white'
                      : 'bg-white text-gray-500 hover:bg-gray-50 border border-gray-300',
                  ]"
                >
                  {{ page }}
                </button>
              </div>
            </nav>
          </div>

          <!-- 空の状態 -->
          <div
            v-if="devices.data && devices.data.length === 0"
            class="text-center py-12"
          >
            <div class="text-gray-500 text-lg">
              デバイス情報が見つかりませんでした
            </div>
            <div class="mt-2 text-gray-400 text-sm">
              新しいデバイスを登録してください
            </div>
          </div>

          <!-- ローディング -->
          <div v-if="loading" class="text-center py-12">
            <div class="text-gray-500">読み込み中...</div>
          </div>
        </div>
      </div>

      <!-- 削除確認モーダル -->
      <div
        v-if="showDeleteModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
        @click="showDeleteModal = false"
      >
        <div
          class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white"
          @click.stop
        >
          <div class="mt-3 text-center">
            <h3 class="text-lg font-medium text-gray-900">デバイス削除確認</h3>
            <div class="mt-2 px-7 py-3">
              <p class="text-sm text-gray-500">
                「{{ deviceToDelete?.name }}」を削除しますか？<br />
                この操作は取り消せません。
              </p>
            </div>
            <div class="flex justify-center space-x-3 mt-4">
              <button
                @click="showDeleteModal = false"
                class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md hover:bg-gray-400"
              >
                キャンセル
              </button>
              <button
                @click="confirmDelete"
                class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md hover:bg-red-600"
              >
                削除
              </button>
            </div>
          </div>
        </div>
      </div>
    </template>
  </MainLayout>
</template>



<style scoped>
/* カスタムスタイル */
.table-fixed {
  table-layout: fixed;
}
</style>
