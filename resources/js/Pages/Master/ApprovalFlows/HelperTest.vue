<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { ref, computed, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import Title from "@/Components/Title/MainTitle.vue";

const props = defineProps({
  users: Array,
  groups: Array,
  positions: Array,
  testPrices: Array,
  testResults: Array
});

const testResults = ref([]);
const isLoading = ref(false);
const selectedItemType = ref('existing'); // 'new' or 'existing'

// フィルター・検索用のリアクティブ変数
const searchFilters = ref({
  name: '', // 氏名検索
  groupId: '', // 部署フィルター
  positionId: '' // 役職フィルター
});

// テスト実行
const runTest = async () => {
  isLoading.value = true;
  
  router.post(route('master.approval-flows.run-helper-test'), {}, {
    onSuccess: (page) => {
      testResults.value = page.props.testResults || [];
      isLoading.value = false;
    },
    onError: (errors) => {
      console.error('テスト実行エラー:', errors);
      alert('テスト実行中にエラーが発生しました。');
      isLoading.value = false;
    },
    onFinish: () => {
      isLoading.value = false;
    }
  });
};

// グループ名を取得
const getGroupName = (groupId) => {
  const group = props.groups.find(g => g.id == groupId);
  return group ? group.name : `グループ${groupId}`;
};

// 役職名を取得
const getPositionName = (positionId) => {
  const position = props.positions.find(p => p.id == positionId);
  return position ? position.name : `役職${positionId}`;
};

// 指定された金額でのテスト結果を取得
const getPriceResult = (userResult, price) => {
  const priceResult = userResult.prices.find(p => p.price === price);
  if (!priceResult) return null;
  
  return selectedItemType.value === 'new' 
    ? priceResult.new_item 
    : priceResult.existing_item;
};

// 金額をフォーマット
const formatPrice = (price) => {
  return Number(price).toLocaleString() + '円';
};

// 承認者リストを表示用の文字列に変換
const formatApprovers = (approvers) => {
  if (!approvers || approvers.length === 0) {
    return '承認不要';
  }
  return approvers.map(a => a.name).join(', ');
};

// フィルタリング用のcomputed
const filteredTestResults = computed(() => {
  if (!testResults.value || testResults.value.length === 0) {
    return [];
  }

  let filtered = [...testResults.value];

  // 氏名検索
  if (searchFilters.value.name) {
    const searchName = searchFilters.value.name.toLowerCase();
    filtered = filtered.filter(userResult => 
      userResult.user_name.toLowerCase().includes(searchName)
    );
  }

  // 部署フィルター
  if (searchFilters.value.groupId) {
    filtered = filtered.filter(userResult => 
      userResult.group_id == searchFilters.value.groupId
    );
  }

  // 役職フィルター
  if (searchFilters.value.positionId) {
    filtered = filtered.filter(userResult => 
      userResult.position_id == searchFilters.value.positionId
    );
  }

  return filtered;
});

// フィルターをリセット
const resetFilters = () => {
  searchFilters.value = {
    name: '',
    groupId: '',
    positionId: ''
  };
};

onMounted(() => {
  // セッションデータがあれば初期化
  if (props.testResults && props.testResults.length > 0) {
    testResults.value = props.testResults;
  }
});
</script>

<template>
  <MainLayout :p_none="true">
    <template #content>
      <section class="py-16 px-24">
        <Title 
          :top="'承認フロー一括テスト (Helper::createApprovalFlow)'" 
          :sub="'全ユーザーに対して指定金額で承認フローをテストします。'" 
        />

        <div class="max-w-full mx-auto">
          <!-- テスト実行ボタン -->
          <div class="bg-white p-6 rounded-lg shadow mb-6">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-lg font-semibold mb-2">テスト実行</h3>
                <p class="text-sm text-gray-600">
                  全{{ props.users?.length || 0 }}ユーザー × {{ props.testPrices?.length || 0 }}金額 × 2（新規品/既存品）の組み合わせでテストを実行します。
                </p>
              </div>
              <button
                @click="runTest"
                :disabled="isLoading"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{ isLoading ? 'テスト実行中...' : 'テスト実行' }}
              </button>
            </div>
          </div>

          <!-- 結果表示切り替え・フィルター -->
          <div v-if="testResults.length > 0" class="bg-white p-6 rounded-lg shadow mb-6">
            <!-- 表示切替 -->
            <div class="mb-4">
              <div class="flex items-center gap-4">
                <label class="text-sm font-medium text-gray-700">表示切替:</label>
                <div class="flex gap-2">
                  <button
                    @click="selectedItemType = 'existing'"
                    :class="[
                      'px-4 py-2 rounded',
                      selectedItemType === 'existing'
                        ? 'bg-blue-500 text-white'
                        : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                    ]"
                  >
                    既存品
                  </button>
                  <button
                    @click="selectedItemType = 'new'"
                    :class="[
                      'px-4 py-2 rounded',
                      selectedItemType === 'new'
                        ? 'bg-blue-500 text-white'
                        : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                    ]"
                  >
                    新規品
                  </button>
                </div>
              </div>
            </div>

            <!-- フィルター・検索 -->
            <div class="border-t pt-4">
              <h4 class="text-sm font-medium text-gray-700 mb-3">絞り込み・検索</h4>
              <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- 氏名検索 -->
                <div>
                  <label class="block text-xs font-medium text-gray-600 mb-1">
                    氏名検索
                  </label>
                  <input
                    v-model="searchFilters.name"
                    type="text"
                    placeholder="氏名で検索"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  />
                </div>

                <!-- 部署フィルター -->
                <div>
                  <label class="block text-xs font-medium text-gray-600 mb-1">
                    部署
                  </label>
                  <select
                    v-model="searchFilters.groupId"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                    <option value="">すべて</option>
                    <option
                      v-for="group in props.groups"
                      :key="group.id"
                      :value="group.id"
                    >
                      {{ group.name }}
                    </option>
                  </select>
                </div>

                <!-- 役職フィルター -->
                <div>
                  <label class="block text-xs font-medium text-gray-600 mb-1">
                    役職
                  </label>
                  <select
                    v-model="searchFilters.positionId"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                    <option value="">すべて</option>
                    <option
                      v-for="position in props.positions"
                      :key="position.id"
                      :value="position.id"
                    >
                      {{ position.name }}
                    </option>
                  </select>
                </div>

                <!-- リセットボタン -->
                <div class="flex items-end">
                  <button
                    @click="resetFilters"
                    class="w-full px-4 py-2 text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md transition-colors"
                  >
                    リセット
                  </button>
                </div>
              </div>

              <!-- 検索結果件数表示 -->
              <div v-if="filteredTestResults.length !== testResults.length" class="mt-3 pt-3 border-t">
                <p class="text-xs text-gray-600">
                  検索結果: <span class="font-semibold text-gray-900">{{ filteredTestResults.length }}</span>件
                  <span class="text-gray-500">
                    (全{{ testResults.length }}件中)
                  </span>
                </p>
              </div>
            </div>
          </div>

          <!-- テスト結果テーブル -->
          <div v-if="testResults.length > 0" class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 sticky top-0">
                  <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">
                      ユーザー情報
                    </th>
                    <th
                      v-for="price in props.testPrices"
                      :key="price"
                      class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-r"
                    >
                      {{ formatPrice(price) }}
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr
                    v-for="userResult in filteredTestResults"
                    :key="userResult.user_id"
                    class="hover:bg-gray-50"
                  >
                    <!-- ユーザー情報列 -->
                    <td class="px-4 py-3 whitespace-nowrap border-r bg-gray-50">
                      <div class="text-sm font-medium text-gray-900">
                        {{ userResult.user_name }}
                      </div>
                      <div class="text-xs text-gray-500">
                        {{ getGroupName(userResult.group_id) }}
                      </div>
                      <div class="text-xs text-gray-500">
                        {{ getPositionName(userResult.position_id) }}
                      </div>
                      <div class="text-xs text-gray-400 mt-1">
                        ID: {{ userResult.user_id }}
                      </div>
                    </td>

                    <!-- 各金額の結果列 -->
                    <td
                      v-for="price in props.testPrices"
                      :key="price"
                      class="px-4 py-3 text-sm text-gray-900 border-r"
                    >
                      <div
                        v-if="getPriceResult(userResult, price)"
                        class="max-w-xs"
                      >
                        <div
                          v-if="getPriceResult(userResult, price).length === 0"
                          class="text-xs text-yellow-600 bg-yellow-50 p-2 rounded"
                        >
                          承認不要
                        </div>
                        <div
                          v-else
                          class="space-y-1"
                        >
                          <div
                            v-for="(approver, index) in getPriceResult(userResult, price)"
                            :key="index"
                            class="text-xs bg-blue-50 text-blue-800 p-1 rounded mb-1"
                          >
                            {{ approver.name }}
                            <span class="text-blue-500 ml-1">
                              (ID: {{ approver.id }})
                            </span>
                          </div>
                        </div>
                      </div>
                      <div
                        v-else
                        class="text-xs text-gray-400"
                      >
                        -
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- テスト結果がない場合のメッセージ -->
          <div
            v-else-if="!isLoading"
            class="bg-white p-12 rounded-lg shadow text-center"
          >
            <p class="text-gray-500 text-lg">
              テスト結果がありません。「テスト実行」ボタンをクリックしてテストを実行してください。
            </p>
          </div>

          <!-- ローディング表示 -->
          <div
            v-if="isLoading"
            class="bg-white p-12 rounded-lg shadow text-center"
          >
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mb-4"></div>
            <p class="text-gray-600 text-lg">
              テストを実行中です。しばらくお待ちください...
            </p>
            <p class="text-gray-500 text-sm mt-2">
              全{{ props.users?.length || 0 }}ユーザー × {{ props.testPrices?.length || 0 }}金額 × 2（新規品/既存品）の組み合わせをテストしています。
            </p>
          </div>
        </div>
      </section>
    </template>
  </MainLayout>
</template>

<style scoped lang="scss">
table {
  border-collapse: separate;
  border-spacing: 0;
}

th:first-child,
td:first-child {
  position: sticky;
  left: 0;
  z-index: 10;
  background-color: white;
}

thead th:first-child {
  background-color: #f9fafb;
  z-index: 20;
}

tbody tr td:first-child {
  background-color: white;
}

tbody tr:hover td:first-child {
  background-color: #f9fafb;
}
</style>