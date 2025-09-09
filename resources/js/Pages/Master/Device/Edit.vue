<template>
  <MainLayout>
    <template #content>
      <MainTitle :top="'デバイス情報編集'" :sub="'デバイス情報を編集できます。変更したい項目を修正してください。'"/>

      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
      <div class="p-6 bg-white border-b border-gray-200">
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-2xl font-bold text-gray-900">デバイス情報編集</h1>
          <Link
            :href="route('master.devices')"
            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
          >
            戻る
          </Link>
        </div>

        <form @submit.prevent="submitForm" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- デバイス名 -->
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                デバイス名 <span class="text-red-500">*</span>
              </label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': errors.name }"
              />
              <p v-if="errors.name" class="mt-1 text-sm text-red-600">
                {{ errors.name[0] }}
              </p>
            </div>

            <!-- デバイス種類 -->
            <div>
              <label for="device_type" class="block text-sm font-medium text-gray-700 mb-2">
                デバイス種類 <span class="text-red-500">*</span>
              </label>
              <select
                id="device_type"
                v-model="form.device_type"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': errors.device_type }"
              >
                <option value="">選択してください</option>
                <option value="PC">PC</option>
                <option value="タブレット">タブレット</option>
                <option value="スマートフォン">スマートフォン</option>
                <option value="プリンター">プリンター</option>
                <option value="ルーター">ルーター</option>
                <option value="サーバー">サーバー</option>
                <option value="その他">その他</option>
              </select>
              <p v-if="errors.device_type" class="mt-1 text-sm text-red-600">
                {{ errors.device_type[0] }}
              </p>
            </div>

            <!-- IPアドレス -->
            <div>
              <label for="ip_address" class="block text-sm font-medium text-gray-700 mb-2">
                IPアドレス
              </label>
              <input
                id="ip_address"
                v-model="form.ip_address"
                type="text"
                placeholder="192.168.1.100"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': errors.ip_address }"
              />
              <p v-if="errors.ip_address" class="mt-1 text-sm text-red-600">
                {{ errors.ip_address[0] }}
              </p>
            </div>

            <!-- MACアドレス -->
            <div>
              <label for="mac_address" class="block text-sm font-medium text-gray-700 mb-2">
                MACアドレス
              </label>
              <input
                id="mac_address"
                v-model="form.mac_address"
                type="text"
                placeholder="00:11:22:33:44:55"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': errors.mac_address }"
              />
              <p v-if="errors.mac_address" class="mt-1 text-sm text-red-600">
                {{ errors.mac_address[0] }}
              </p>
            </div>

            <!-- 設置場所 -->
            <div>
              <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                設置場所
              </label>
              <input
                id="location"
                v-model="form.location"
                type="text"
                placeholder="オフィス1階"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': errors.location }"
              />
              <p v-if="errors.location" class="mt-1 text-sm text-red-600">
                {{ errors.location[0] }}
              </p>
            </div>

            <!-- ステータス -->
            <div>
              <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                ステータス
              </label>
              <select
                id="status"
                v-model="form.status"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': errors.status }"
              >
                <option value="">選択してください</option>
                <option value="稼働中">稼働中</option>
                <option value="停止中">停止中</option>
                <option value="メンテナンス中">メンテナンス中</option>
                <option value="廃止予定">廃止予定</option>
              </select>
              <p v-if="errors.status" class="mt-1 text-sm text-red-600">
                {{ errors.status[0] }}
              </p>
            </div>
          </div>

          <!-- 最終アクセス時間表示 -->
          <div class="bg-gray-50 p-4 rounded-md">
            <h3 class="text-sm font-medium text-gray-700 mb-2">接続情報</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
              <div>
                <span class="text-gray-600">最終アクセス:</span>
                <span class="ml-2 font-medium">
                  {{ (device.last_access_date && 
                      device.last_access_date !== '0000-00-00 00:00:00' && 
                      !device.last_access_date.startsWith('-0001')) ? device.last_access_date_formatted : '未接続' }}
                </span>
              </div>
              <div class="flex items-center">
                <span class="text-gray-600">接続状態:</span>
                <div class="flex items-center ml-2">
                  <div 
                    class="w-3 h-3 rounded-full mr-2"
                    :class="getStatusIconClass(device.connection_status)"
                  ></div>
                  <span class="font-medium">
                    {{ getStatusText(device.connection_status) }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- 説明 -->
          <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
              説明・備考
            </label>
            <textarea
              id="description"
              v-model="form.description"
              rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-500': errors.description }"
              placeholder="デバイスの詳細情報や備考を入力してください"
            ></textarea>
            <p v-if="errors.description" class="mt-1 text-sm text-red-600">
              {{ errors.description[0] }}
            </p>
          </div>

          <!-- 送信ボタン -->
          <div class="flex justify-end space-x-3">
            <Link
              :href="route('master.devices')"
              class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
            >
              キャンセル
            </Link>
            <button
              type="submit"
              :disabled="loading"
              class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="loading">更新中...</span>
              <span v-else>更新</span>
            </button>
          </div>
        </form>
      </div>
    </div>
    </template>
  </MainLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import MainTitle from '@/Components/Title/MainTitle.vue'
import axios from 'axios'

// Props
const props = defineProps({
  device: Object
})

// リアクティブデータ
const form = ref({
  name: '',
  device_type: '',
  ip_address: '',
  mac_address: '',
  location: '',
  status: '',
  description: ''
})

const errors = ref({})
const loading = ref(false)

// 初期化
onMounted(() => {
  if (props.device) {
    form.value = {
      name: props.device.name || '',
      device_type: props.device.device_type || '',
      ip_address: props.device.ip_address || '',
      mac_address: props.device.mac_address || '',
      location: props.device.location || '',
      status: props.device.status || '',
      description: props.device.description || ''
    }
  }
})

// 接続状態のアイコンクラス取得
const getStatusIconClass = (status) => {
  switch (status) {
    case 'online':
      return 'bg-green-500'
    case 'warning':
      return 'bg-orange-500'
    default:
      return 'bg-red-500'
  }
}

// 接続状態のテキスト取得
const getStatusText = (status) => {
  switch (status) {
    case 'online':
      return 'オンライン'
    case 'warning':
      return '警告'
    default:
      return 'オフライン'
  }
}

// フォーム送信
const submitForm = async () => {
  loading.value = true
  errors.value = {}

  try {
    const response = await router.put(route('master.devices.update', props.device.id), form.value, {
      onSuccess: (page) => {
        alert('デバイス情報を更新しました')
        router.visit(route('master.devices'))
      },
      onError: (errors) => {
        console.error('更新エラー:', errors)
        alert('デバイス情報の更新に失敗しました')
      }
    })
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* カスタムスタイル */
</style>
