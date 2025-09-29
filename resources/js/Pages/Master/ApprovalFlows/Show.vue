<template>
  <div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
      <!-- ヘッダー -->
      <div class="flex justify-between items-center mb-6">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">承認フロー詳細</h1>
          <p class="text-gray-600 mt-2">承認フローの詳細情報を表示します</p>
        </div>
        <div class="flex space-x-3">
          <Link
            :href="route('master.approval-flows.edit', flow.id)"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors"
          >
            編集
          </Link>
          <Link
            :href="route('master.approval-flows.index')"
            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors"
          >
            一覧に戻る
          </Link>
        </div>
      </div>

      <!-- 承認フロー情報 -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">基本情報</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">フロー名</label>
            <p class="mt-1 text-sm text-gray-900">{{ flow.name }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">説明</label>
            <p class="mt-1 text-sm text-gray-900">{{ flow.description || '説明なし' }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">ステータス</label>
            <span
              :class="flow.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
              class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
            >
              {{ flow.is_active ? 'アクティブ' : '非アクティブ' }}
            </span>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">作成日時</label>
            <p class="mt-1 text-sm text-gray-900">{{ formatDateTime(flow.created_at) }}</p>
          </div>
        </div>
      </div>

      <!-- 条件 -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">適用条件</h2>
        <div v-if="flow.conditions && flow.conditions.length > 0">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    条件タイプ
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    演算子
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    値
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="condition in flow.conditions" :key="condition.id">
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ getConditionTypeLabel(condition.condition_type) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ getOperatorLabel(condition.operator) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ condition.condition_value }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div v-else class="text-gray-500 text-center py-4">
          条件が設定されていません
        </div>
      </div>

      <!-- 承認ステップ -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">承認ステップ</h2>
        <div v-if="flow.steps && flow.steps.length > 0">
          <div class="space-y-4">
            <div
              v-for="(step, index) in sortedSteps"
              :key="step.id"
              class="flex items-center p-4 border border-gray-200 rounded-lg"
            >
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-semibold">
                  {{ step.step_order }}
                </div>
              </div>
              <div class="ml-4 flex-1">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm font-medium text-gray-900">
                      {{ step.approver ? step.approver.name : `ユーザーID: ${step.approver_user_id}` }}
                    </p>
                    <p class="text-sm text-gray-500">
                      {{ step.approver ? step.approver.email : '' }}
                    </p>
                  </div>
                  <div class="flex items-center space-x-2">
                    <span
                      :class="step.is_required ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'"
                      class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                    >
                      {{ step.is_required ? '必須' : '任意' }}
                    </span>
                  </div>
                </div>
              </div>
              <div v-if="index < sortedSteps.length - 1" class="ml-4">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="text-gray-500 text-center py-4">
          承認ステップが設定されていません
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  flow: {
    type: Object,
    required: true
  }
})

// 承認ステップを順序でソート
const sortedSteps = computed(() => {
  if (!props.flow.steps) return []
  return [...props.flow.steps].sort((a, b) => a.step_order - b.step_order)
})

// 条件タイプのラベルを取得
const getConditionTypeLabel = (type) => {
  const labels = {
    'position': '役職',
    'group': '部署',
    'price_min': '最低金額',
    'price_max': '最高金額',
    'is_new_item': '新規品フラグ'
  }
  return labels[type] || type
}

// 演算子のラベルを取得
const getOperatorLabel = (operator) => {
  const labels = {
    '=': '等しい',
    '>': 'より大きい',
    '<': 'より小さい',
    '>=': '以上',
    '<=': '以下',
    'in': '含む',
    'not_in': '含まない'
  }
  return labels[operator] || operator
}

// 日時をフォーマット
const formatDateTime = (dateTime) => {
  if (!dateTime) return ''
  const date = new Date(dateTime)
  return date.toLocaleString('ja-JP', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>
