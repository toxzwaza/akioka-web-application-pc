<template>
  <div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
      <!-- ヘッダー -->
      <div class="flex justify-between items-center mb-6">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">承認フロー編集</h1>
          <p class="text-gray-600 mt-2">承認フローを編集します</p>
        </div>
        <div class="flex space-x-3">
          <Link
            :href="route('master.approval-flows.show', flow.id)"
            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors"
          >
            詳細
          </Link>
          <Link
            :href="route('master.approval-flows.index')"
            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors"
          >
            一覧に戻る
          </Link>
        </div>
      </div>

      <!-- フォーム -->
      <form @submit.prevent="submit" class="bg-white rounded-lg shadow-md p-6">
        <!-- 基本情報 -->
        <div class="mb-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">基本情報</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                フロー名 <span class="text-red-500">*</span>
              </label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': form.errors.name }"
                required
              />
              <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">
                {{ form.errors.name }}
              </div>
            </div>
            <div>
              <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                説明
              </label>
              <textarea
                id="description"
                v-model="form.description"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': form.errors.description }"
              ></textarea>
              <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">
                {{ form.errors.description }}
              </div>
            </div>
            <div>
              <label class="flex items-center">
                <input
                  v-model="form.is_active"
                  type="checkbox"
                  class="mr-2"
                />
                <span class="text-sm font-medium text-gray-700">アクティブ</span>
              </label>
            </div>
          </div>
        </div>

        <!-- 条件 -->
        <div class="mb-6">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-900">適用条件</h2>
            <button
              type="button"
              @click="addCondition"
              class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm"
            >
              条件を追加
            </button>
          </div>
          <div v-if="form.conditions.length === 0" class="text-gray-500 text-center py-4">
            条件が設定されていません
          </div>
          <div v-else class="space-y-4">
            <div
              v-for="(condition, index) in form.conditions"
              :key="index"
              class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg"
            >
              <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">条件タイプ</label>
                  <select
                    v-model="condition.condition_type"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                    <option value="">選択してください</option>
                    <option
                      v-for="(label, value) in conditionTypes"
                      :key="value"
                      :value="value"
                    >
                      {{ label }}
                    </option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">演算子</label>
                  <select
                    v-model="condition.operator"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                    <option value="">選択してください</option>
                    <option
                      v-for="(label, value) in operators"
                      :key="value"
                      :value="value"
                    >
                      {{ label }}
                    </option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">値</label>
                  <input
                    v-model="condition.condition_value"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="条件値"
                  />
                </div>
              </div>
              <button
                type="button"
                @click="removeCondition(index)"
                class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded"
              >
                削除
              </button>
            </div>
          </div>
        </div>

        <!-- 承認ステップ -->
        <div class="mb-6">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-900">承認ステップ</h2>
            <button
              type="button"
              @click="addStep"
              class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm"
            >
              ステップを追加
            </button>
          </div>
          <div v-if="form.steps.length === 0" class="text-gray-500 text-center py-4">
            承認ステップが設定されていません
          </div>
          <div v-else class="space-y-4">
            <div
              v-for="(step, index) in form.steps"
              :key="index"
              class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg"
            >
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-semibold">
                  {{ step.step_order }}
                </div>
              </div>
              <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">承認者</label>
                  <select
                    v-model="step.approver_user_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                    <option value="">選択してください</option>
                    <option
                      v-for="user in users"
                      :key="user.id"
                      :value="user.id"
                    >
                      {{ user.name }} ({{ user.group?.name }} - {{ user.position?.name }})
                    </option>
                  </select>
                </div>
                <div class="flex items-center">
                  <label class="flex items-center">
                    <input
                      v-model="step.is_required"
                      type="checkbox"
                      class="mr-2"
                    />
                    <span class="text-sm font-medium text-gray-700">必須ステップ</span>
                  </label>
                </div>
              </div>
              <div class="flex space-x-2">
                <button
                  type="button"
                  @click="moveStepUp(index)"
                  :disabled="index === 0"
                  class="bg-gray-600 hover:bg-gray-700 disabled:bg-gray-300 text-white px-3 py-2 rounded text-sm"
                >
                  上へ
                </button>
                <button
                  type="button"
                  @click="moveStepDown(index)"
                  :disabled="index === form.steps.length - 1"
                  class="bg-gray-600 hover:bg-gray-700 disabled:bg-gray-300 text-white px-3 py-2 rounded text-sm"
                >
                  下へ
                </button>
                <button
                  type="button"
                  @click="removeStep(index)"
                  class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded"
                >
                  削除
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- 送信ボタン -->
        <div class="flex justify-end space-x-4">
          <Link
            :href="route('master.approval-flows.show', flow.id)"
            class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition-colors"
          >
            キャンセル
          </Link>
          <button
            type="submit"
            :disabled="form.processing"
            class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white px-6 py-2 rounded-lg transition-colors"
          >
            {{ form.processing ? '更新中...' : '更新' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  flow: {
    type: Object,
    required: true
  },
  users: {
    type: Array,
    required: true
  },
  conditionTypes: {
    type: Object,
    required: true
  },
  operators: {
    type: Object,
    required: true
  }
})

const form = useForm({
  name: props.flow.name,
  description: props.flow.description,
  is_active: props.flow.is_active,
  conditions: props.flow.conditions?.map(condition => ({
    condition_type: condition.condition_type,
    operator: condition.operator,
    condition_value: condition.condition_value
  })) || [],
  steps: props.flow.steps?.map(step => ({
    step_order: step.step_order,
    approver_user_id: step.approver_user_id,
    is_required: step.is_required
  })) || []
})

// 条件を追加
const addCondition = () => {
  form.conditions.push({
    condition_type: '',
    operator: '',
    condition_value: ''
  })
}

// 条件を削除
const removeCondition = (index) => {
  form.conditions.splice(index, 1)
}

// ステップを追加
const addStep = () => {
  form.steps.push({
    step_order: form.steps.length + 1,
    approver_user_id: '',
    is_required: true
  })
}

// ステップを削除
const removeStep = (index) => {
  form.steps.splice(index, 1)
  // ステップ順序を再設定
  form.steps.forEach((step, idx) => {
    step.step_order = idx + 1
  })
}

// ステップを上に移動
const moveStepUp = (index) => {
  if (index > 0) {
    const steps = [...form.steps]
    const temp = steps[index]
    steps[index] = steps[index - 1]
    steps[index - 1] = temp
    
    // ステップ順序を再設定
    steps.forEach((step, idx) => {
      step.step_order = idx + 1
    })
    
    form.steps = steps
  }
}

// ステップを下に移動
const moveStepDown = (index) => {
  if (index < form.steps.length - 1) {
    const steps = [...form.steps]
    const temp = steps[index]
    steps[index] = steps[index + 1]
    steps[index + 1] = temp
    
    // ステップ順序を再設定
    steps.forEach((step, idx) => {
      step.step_order = idx + 1
    })
    
    form.steps = steps
  }
}

// フォーム送信
const submit = () => {
  form.put(route('master.approval-flows.update', props.flow.id))
}
</script>
