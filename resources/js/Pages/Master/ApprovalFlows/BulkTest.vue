<template>
  <div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
      <!-- ヘッダー -->
      <div class="flex justify-between items-center mb-6">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">承認フロー一括テスト</h1>
          <p class="text-gray-600 mt-2">すべての部署・役職・金額区分の承認フローをテストします</p>
        </div>
        <div class="flex space-x-3">
          <Link
            :href="route('master.approval-flows.index')"
            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors"
          >
            一覧に戻る
          </Link>
        </div>
      </div>

      <!-- テスト実行ボタン -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center">
          <div>
            <h2 class="text-xl font-semibold text-gray-900">一括テスト実行</h2>
            <p class="text-gray-600 mt-1">全{{ testCases.length }}件のテストケースを実行します</p>
          </div>
          <button
            @click="runAllTests"
            :disabled="isLoading"
            class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white px-6 py-3 rounded-lg transition-colors"
          >
            {{ isLoading ? 'テスト実行中...' : '一括テスト実行' }}
          </button>
        </div>
        
        <!-- 進捗表示 -->
        <div v-if="isLoading" class="mt-4">
          <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
            <span>進捗: {{ currentTestIndex + 1 }} / {{ testCases.length }}</span>
            <span>{{ Math.round(((currentTestIndex + 1) / testCases.length) * 100) }}%</span>
          </div>
          <div class="w-full bg-gray-200 rounded-full h-2">
            <div
              class="bg-blue-600 h-2 rounded-full transition-all duration-300"
              :style="{ width: `${((currentTestIndex + 1) / testCases.length) * 100}%` }"
            ></div>
          </div>
        </div>
      </div>

      <!-- テスト結果 -->
      <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-xl font-semibold text-gray-900">テスト結果</h2>
        </div>
        
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  部署
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  役職
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  金額区分
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  種類
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  承認者
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  適用フロー
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  ステータス
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="testCase in testCases" :key="testCase.key">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ testCase.groupName }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ testCase.positionName }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ testCase.priceRange }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ testCase.itemType }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <div v-if="testCase.userId">
                    <span v-if="testCase.result && testCase.result.approvers && testCase.result.approvers.length > 0">
                      <span
                        v-for="(approver, index) in testCase.result.approvers"
                        :key="approver.id"
                        class="inline-block"
                      >
                        {{ approver.name || `ユーザーID: ${approver.id}` }}
                        <span v-if="index < testCase.result.approvers.length - 1"> → </span>
                      </span>
                    </span>
                    <span v-else-if="testCase.result && testCase.result.approvers && testCase.result.approvers.length === 0" class="text-green-600">
                      承認不要
                    </span>
                    <span v-else class="text-gray-500">
                      未実行
                    </span>
                  </div>
                  <span v-else class="text-red-600">
                    (ユーザーなし)
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <div v-if="testCase.userId && testCase.result && testCase.result.approvalFlow">
                    <Link
                      :href="route('master.approval-flows.edit', testCase.result.approvalFlow.id)"
                      class="text-blue-600 hover:text-blue-800 hover:underline cursor-pointer"
                    >
                      {{ testCase.result.approvalFlow.name }}
                    </Link>
                  </div>
                  <span v-else-if="testCase.userId && testCase.result && testCase.result.approvers && testCase.result.approvers.length === 0" class="text-green-600">
                    承認不要
                  </span>
                  <span v-else-if="testCase.userId" class="text-gray-500">
                    未実行
                  </span>
                  <span v-else class="text-red-600">
                    -
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    v-if="testCase.userId"
                    :class="{
                      'bg-green-100 text-green-800': testCase.result && testCase.result.success,
                      'bg-red-100 text-red-800': testCase.result && !testCase.result.success,
                      'bg-gray-100 text-gray-800': !testCase.result
                    }"
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                  >
                    {{ testCase.result ? (testCase.result.success ? '成功' : 'エラー') : '未実行' }}
                  </span>
                  <span v-else class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                    エラー
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  users: {
    type: Array,
    required: true
  },
  groups: {
    type: Array,
    required: true
  },
  positions: {
    type: Array,
    required: true
  }
})

const isLoading = ref(false)
const currentTestIndex = ref(0)
const testResults = ref({})

// テストケースを生成
const testCases = computed(() => {
  const cases = []
  
  // 指定された部署（IDと名前のマッピング）
  const groupTests = [
    { groupId: 1, groupName: '技術' },
    { groupId: 2, groupName: '品証' },
    { groupId: 3, groupName: '製造一課' },
    { groupId: 4, groupName: '製造二課' },
    { groupId: 5, groupName: '保全課' },
    { groupId: 6, groupName: '総務部' },
    { groupId: 7, groupName: '業務部' }
  ]
  
  // 指定された金額区分
  const priceRanges = [
    { min: 0, max: 9999, label: '10,000円未満' },
    { min: 10000, max: 149999, label: '10,000円以上150,000円未満' },
    { min: 150000, max: 999999, label: '150,000円以上' }
  ]
  
  // 新規/既存品
  const itemTypes = [
    { value: false, label: '既存品' },
    { value: true, label: '新規品' }
  ]
  
  // 指定された役職（係長・GL・一般は統合）
  const positions = [
    { ids: [7, 8, 9], name: '係長・GL・一般' },
    { ids: [6], name: '課長' }
  ]
  
  // テストケースを生成（すべての組み合わせを網羅）
  groupTests.forEach(group => {
    positions.forEach(position => {
      // 該当するユーザーを探す（複数のposition_idを考慮）
      const matchingUsers = props.users.filter(u => 
        u.group_id === group.groupId && 
        position.ids.includes(u.position_id)
      )
      
      const user = matchingUsers[0] // 最初のユーザーを使用
      
      // 金額と新規/既存の組み合わせでテストケースを生成
      priceRanges.forEach(priceRange => {
        itemTypes.forEach(itemType => {
          if (user) {
            cases.push({
              key: `${group.groupId}-${position.name}-${priceRange.min}-${itemType.value}`,
              userId: user.id,
              groupName: group.groupName,
              positionName: position.name,
              priceRange: priceRange.label,
              itemType: itemType.label,
              price: priceRange.min + Math.floor((priceRange.max - priceRange.min) / 2), // 中間値
              isNewItem: itemType.value,
              result: testResults.value[`${group.groupId}-${position.name}-${priceRange.min}-${itemType.value}`] || null
            })
          } else {
            // ユーザーが見つからない場合でもテストケースを追加（エラー表示用）
            cases.push({
              key: `${group.groupId}-${position.name}-${priceRange.min}-${itemType.value}`,
              userId: null,
              groupName: group.groupName,
              positionName: position.name,
              priceRange: priceRange.label,
              itemType: itemType.label,
              price: priceRange.min + Math.floor((priceRange.max - priceRange.min) / 2),
              isNewItem: itemType.value,
              result: testResults.value[`${group.groupId}-${position.name}-${priceRange.min}-${itemType.value}`] || null
            })
          }
        })
      })
    })
  })
  
  return cases
})

// 全テスト実行
const runAllTests = async () => {
  isLoading.value = true
  currentTestIndex.value = 0
  testResults.value = {}

  for (let i = 0; i < testCases.value.length; i++) {
    const testCase = testCases.value[i]
    currentTestIndex.value = i

    if (!testCase.userId) {
      // ユーザーが見つからない場合はスキップ
      continue
    }

    try {
      const response = await window.axios.post(route('master.approval-flows.run-test-api'), {
        user_id: testCase.userId,
        price: testCase.price,
        is_new_item: testCase.isNewItem
      })

      console.log('API Response for', testCase.key, ':', response.data)
      testResults.value[testCase.key] = {
        success: true,
        approvers: response.data.approvers || [],
        approvalFlow: response.data.approvalFlow || null
      }
    } catch (error) {
      console.error('テスト実行エラー:', error)
      testResults.value[testCase.key] = {
        success: false,
        error: error.response?.data?.message || 'テスト実行中にエラーが発生しました'
      }
    }

    // 少し待機してUIの更新を確認できるようにする
    await new Promise(resolve => setTimeout(resolve, 100))
  }

  isLoading.value = false
}

onMounted(() => {
  // デバッグ用：ユーザーデータをコンソールに出力
  console.log('Users data:', props.users)
  console.log('Groups data:', props.groups)
  console.log('Positions data:', props.positions)
  
  // 係長・GL・一般のユーザーを確認（position_id 7, 8, 9）
  const kachouUsers = props.users.filter(u => [7, 8, 9].includes(u.position_id))
  console.log('係長・GL・一般のユーザー:', kachouUsers)
  
  // 課長のユーザーも確認
  const kachouUsers2 = props.users.filter(u => u.position_id === 6)
  console.log('課長のユーザー:', kachouUsers2)
})
</script>
