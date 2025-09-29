<template>
  <div v-if="isLoading" class="loading-overlay">
    <div class="loading-container">
      <div class="loading-spinner">
        <div class="spinner-ring">
          <div></div>
          <div></div>
          <div></div>
          <div></div>
        </div>
      </div>
      
      <div class="loading-content">
        <h3 class="loading-title">{{ title || '検索中...' }}</h3>
        <p class="loading-message">{{ message || 'データを取得しています。しばらくお待ちください。' }}</p>
        
        <!-- プログレスバー（オプション） -->
        <div v-if="showProgress" class="progress-bar">
          <div class="progress-fill" :style="{ width: progress + '%' }"></div>
        </div>
      </div>
      
      <!-- 検索アイコン -->
      <div class="search-icon">
        <svg class="w-8 h-8 text-blue-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue'

const props = defineProps({
  isLoading: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: '検索中...'
  },
  message: {
    type: String,
    default: 'データを取得しています。しばらくお待ちください。'
  },
  showProgress: {
    type: Boolean,
    default: false
  },
  progress: {
    type: Number,
    default: 0
  }
})
</script>

<style scoped lang="scss">
.loading-overlay {
  @apply fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center;
  backdrop-filter: blur(4px);
  
  .loading-container {
    @apply bg-white rounded-2xl shadow-2xl p-8 max-w-md mx-4 text-center relative;
    animation: fadeInScale 0.3s ease-out;
    
    .loading-spinner {
      @apply mb-6 flex justify-center;
      
      .spinner-ring {
        @apply inline-block relative w-20 h-20;
        
        div {
          @apply absolute border-4 rounded-full;
          width: 64px;
          height: 64px;
          margin: 8px;
          border-color: #3b82f6 transparent transparent transparent;
          animation: spin 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
          
          &:nth-child(1) {
            animation-delay: -0.45s;
          }
          
          &:nth-child(2) {
            animation-delay: -0.3s;
          }
          
          &:nth-child(3) {
            animation-delay: -0.15s;
          }
        }
      }
    }
    
    .loading-content {
      @apply mb-6;
      
      .loading-title {
        @apply text-2xl font-bold text-gray-800 mb-2;
      }
      
      .loading-message {
        @apply text-gray-600 leading-relaxed;
      }
      
      .progress-bar {
        @apply w-full bg-gray-200 rounded-full h-2 mt-4 overflow-hidden;
        
        .progress-fill {
          @apply bg-blue-500 h-full rounded-full transition-all duration-300 ease-out;
          background: linear-gradient(90deg, #3b82f6, #1d4ed8, #3b82f6);
          background-size: 200% 100%;
          animation: shimmer 2s infinite;
        }
      }
    }
    
    .search-icon {
      @apply absolute -top-4 -right-4 bg-white rounded-full p-3 shadow-lg border-4 border-blue-100;
    }
  }
}

// アニメーション
@keyframes fadeInScale {
  from {
    opacity: 0;
    transform: scale(0.9) translateY(20px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

@keyframes shimmer {
  0% {
    background-position: -200% 0;
  }
  100% {
    background-position: 200% 0;
  }
}

// レスポンシブデザイン
@media (max-width: 640px) {
  .loading-container {
    @apply mx-4 p-6;
    
    .loading-title {
      @apply text-xl;
    }
    
    .loading-message {
      @apply text-sm;
    }
    
    .loading-spinner .spinner-ring {
      @apply w-16 h-16;
      
      div {
        width: 48px;
        height: 48px;
      }
    }
  }
}
</style>






