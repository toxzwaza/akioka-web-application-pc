<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { ref, reactive, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import Title from "@/Components/Title/MainTitle.vue";

const props = defineProps({
  users: Array,
  groups: Array,
  positions: Array,
  testResult: Object
});

const testForm = reactive({
  user_id: '',
  price: '',
  is_new_item: false
});

const testResult = ref(null);
const isLoading = ref(false);

const runTest = async () => {
  if (!testForm.user_id || !testForm.price) {
    alert('ユーザーと金額を選択してください。');
    return;
  }

  isLoading.value = true;
  
  // Inertia.jsのrouter.postを使用
  router.post(route('master.approval-flows.run-test'), testForm, {
    onSuccess: (page) => {
      testResult.value = page.props.testResult;
    },
    onError: (errors) => {
      console.error('テスト実行エラー:', errors);
      testResult.value = {
        error: 'テスト実行中にエラーが発生しました。',
        approvers: []
      };
    },
    onFinish: () => {
      isLoading.value = false;
    }
  });
};

const getUserInfo = (userId) => {
  return props.users.find(user => user.id == userId);
};

const getGroupName = (groupId) => {
  const group = props.groups.find(g => g.id == groupId);
  return group ? group.name : `グループ${groupId}`;
};

const getPositionName = (positionId) => {
  const position = props.positions.find(p => p.id == positionId);
  return position ? position.name : `役職${positionId}`;
};

onMounted(() => {
  // セッションデータがあれば初期化
  if (props.testResult) {
    testResult.value = props.testResult;
  }
});
</script>

<template>
  <MainLayout :p_none="true">
    <template #content>
      <section class="py-16 px-24">
        <Title :top="'承認フローテスト'" :sub="'条件を指定して承認者リストをテストできます。'" />

        <div class="max-w-4xl mx-auto">
          <!-- テストフォーム -->
          <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h3 class="text-lg font-semibold mb-4">テスト条件</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  ユーザー <span class="text-red-500">*</span>
                </label>
                <select
                  v-model="testForm.user_id"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">選択してください</option>
                  <option v-for="user in props.users" :key="user.id" :value="user.id">
                    {{ user.name }} ({{ getGroupName(user.group_id) }} - {{ getPositionName(user.position_id) }})
                  </option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  金額 <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="testForm.price"
                  type="number"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="金額を入力"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  新規品フラグ
                </label>
                <select
                  v-model="testForm.is_new_item"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option :value="false">既存品</option>
                  <option :value="true">新規品</option>
                </select>
              </div>
            </div>

            <div class="mt-6">
              <button
                @click="runTest"
                :disabled="isLoading"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50"
              >
                {{ isLoading ? 'テスト実行中...' : 'テスト実行' }}
              </button>
            </div>
          </div>

          <!-- テスト結果 -->
          <div v-if="testResult" class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">テスト結果</h3>
            
            <!-- 入力条件 -->
            <div class="mb-6">
              <h4 class="font-medium mb-2">入力条件</h4>
              <div class="bg-gray-50 p-4 rounded">
                <p><strong>ユーザー:</strong> {{ getUserInfo(testForm.user_id)?.name }}</p>
                <p><strong>部署:</strong> {{ getGroupName(getUserInfo(testForm.user_id)?.group_id) }}</p>
                <p><strong>役職:</strong> {{ getPositionName(getUserInfo(testForm.user_id)?.position_id) }}</p>
                <p><strong>金額:</strong> {{ Number(testForm.price).toLocaleString() }}円</p>
                <p><strong>新規品フラグ:</strong> {{ testForm.is_new_item ? '新規品' : '既存品' }}</p>
              </div>
            </div>

            <!-- 承認フロー情報 -->
            <div v-if="testResult.approval_flow" class="mb-6">
              <h4 class="font-medium mb-2">適用された承認フロー</h4>
              <div class="bg-blue-50 p-4 rounded">
                <p><strong>フロー名:</strong> {{ testResult.approval_flow.name }}</p>
                <p><strong>説明:</strong> {{ testResult.approval_flow.description || '説明なし' }}</p>
                <p><strong>ステータス:</strong> {{ testResult.approval_flow.is_active ? '有効' : '無効' }}</p>
              </div>
            </div>

            <!-- 承認者リスト -->
            <div>
              <h4 class="font-medium mb-2">承認者リスト</h4>
              <div v-if="testResult.approvers.length === 0" class="bg-yellow-50 p-4 rounded">
                <p class="text-yellow-800">承認者が設定されていません。</p>
              </div>
              <div v-else class="space-y-2">
                <div 
                  v-for="(approver, index) in testResult.approvers" 
                  :key="index"
                  class="bg-green-50 p-4 rounded"
                >
                  <div class="flex items-center justify-between">
                    <div>
                      <p class="font-medium">{{ approver.name }}</p>
                      <p class="text-sm text-gray-600">ID: {{ approver.id }}</p>
                    </div>
                    <span class="bg-green-100 text-green-800 text-sm font-medium px-2.5 py-0.5 rounded-sm">
                      ステップ {{ index + 1 }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- エラーメッセージ -->
            <div v-if="testResult.error" class="mt-6">
              <h4 class="font-medium mb-2 text-red-600">エラー</h4>
              <div class="bg-red-50 p-4 rounded">
                <p class="text-red-800">{{ testResult.error }}</p>
              </div>
            </div>
          </div>
        </div>
      </section>
    </template>
  </MainLayout>
</template>

<style scoped lang="scss">
</style>
