<template>
  <!-- Unified User Selection Login -->
  <div class="login-card">
    <div class="login-header">
      <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
      </svg>
      <span class="login-title">{{ title || 'ユーザーログイン' }}</span>
      <div class="login-status" :class="{ 'logged-in': isLoggedIn }">
        <span v-if="isLoggedIn" class="status-text">ログイン済み</span>
        <span v-else class="status-text">未ログイン</span>
      </div>
    </div>
    
    <div v-if="!isLoggedIn" class="login-form">
      <div class="select-group">
        <label class="select-label">
          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
          </svg>
          ユーザー選択
        </label>
        <select
          class="user-select"
          @change="handleUserLogin($event.target.value)"
        >
          <option value="0">ユーザーを選択してください</option>
          <option
            v-for="user in users"
            :key="user.id"
            :value="user.id"
          >
            {{ user.name }}
          </option>
        </select>
      </div>
      <p class="login-help">{{ helpText || 'アカウントがない場合は管理者にお問い合わせください' }}</p>
    </div>
    
    <div v-else class="logged-in-info">
      <div class="user-info">
        <div class="user-avatar">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
          </svg>
        </div>
        <div class="user-details">
          <span class="user-name">{{ currentUser?.name }}</span>
          <span class="user-role">{{ role || 'ユーザー' }}</span>
        </div>
      </div>
      <button @click="handleLogout" class="logout-btn">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
        </svg>
        ログアウト
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
  // ユーザーリスト
  users: {
    type: Array,
    default: () => []
  },
  // タイトル
  title: {
    type: String,
    default: null
  },
  // ヘルプテキスト
  helpText: {
    type: String,
    default: null
  },
  // ロール名
  role: {
    type: String,
    default: null
  },
  // LocalStorageキー（userタイプ用）
  storageKey: {
    type: String,
    default: 'user_id'
  }
})

const emit = defineEmits(['login', 'logout', 'user-change'])

// リアクティブデータ
const isLoggedIn = ref(false)
const currentUser = ref(null)

// ユーザー選択ログイン処理
const handleUserLogin = (userId) => {
  if (userId !== '0') {
    const user = props.users.find(u => u.id == userId)
    if (user) {
      localStorage.setItem(props.storageKey, userId)
      isLoggedIn.value = true
      currentUser.value = user
      emit('login', { type: 'user', user: user, userId: userId })
    }
  }
}

// ログアウト処理
const handleLogout = () => {
  localStorage.removeItem(props.storageKey)
  isLoggedIn.value = false
  currentUser.value = null
  emit('logout')
}

// 初期化処理
const initializeLogin = () => {
  const userId = localStorage.getItem(props.storageKey)
  if (userId && userId !== 'null') {
    const user = props.users.find(u => u.id == userId)
    if (user) {
      isLoggedIn.value = true
      currentUser.value = user
      emit('login', { type: 'user', user: user, userId: userId })
    }
  }
}

// マウント時の初期化
onMounted(() => {
  initializeLogin()
})

// 外部から状態をリセットできるメソッドを公開
defineExpose({
  logout: handleLogout,
  isLoggedIn: computed(() => isLoggedIn.value),
  currentUser: computed(() => currentUser.value)
})
</script>

<style scoped lang="scss">
.login-card {
  @apply bg-white rounded-xl shadow-lg border border-gray-100 p-6 max-w-sm;
  
  .login-header {
    @apply flex items-center justify-between mb-4;
    
    .login-title {
      @apply text-lg font-semibold text-gray-800 flex items-center;
    }
    
    .login-status {
      @apply px-3 py-1 rounded-full text-sm font-medium;
      
      &.logged-in {
        @apply bg-green-100 text-green-800;
      }
      
      &:not(.logged-in) {
        @apply bg-gray-100 text-gray-600;
      }
    }
  }
  
  .login-form {
    .input-group {
      @apply flex gap-2 mb-3;
      
      .login-input {
        @apply flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent;
      }
      
      .login-btn {
        @apply bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center;
      }
    }
    
    .select-group {
      @apply mb-3;
      
      .select-label {
        @apply flex items-center text-sm font-medium text-gray-700 mb-2;
      }
      
      .user-select {
        @apply w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white;
      }
    }
    
    .login-help {
      @apply text-xs text-gray-500;
    }
  }
  
  .logged-in-info {
    .user-info {
      @apply flex items-center gap-3 mb-3;
      
      .user-avatar {
        @apply w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center;
      }
      
      .user-details {
        @apply flex-1;
        
        .user-name {
          @apply block font-semibold text-gray-800;
        }
        
        .user-role {
          @apply block text-sm text-gray-600;
        }
      }
    }
    
    .logout-btn {
      @apply w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center;
    }
  }
}

// Responsive Design
@media (max-width: 768px) {
  .login-card {
    @apply max-w-none;
    
    .login-header {
      @apply flex-col gap-2 items-start;
    }
    
    .login-form {
      .input-group {
        @apply flex-col gap-2;
      }
    }
  }
}
</style>
