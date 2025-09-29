<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { onMounted, reactive, ref } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";
import MainTitle from "@/Components/Title/MainTitle.vue"

import Calender from "@/Components/Calender.vue";
import PiChart from "@/Components/PiChart.vue";
import SearchLoading from "@/Components/Loading/SearchLoading.vue";

const pickUpDate = ref(null);
const sortOperation = reactive({
  id: null,
  name: null,
});

// ストリーミング動画を拡大表示
const watchUpStream = ref(false);

const inventoryOperationBaseRecords = ref([]);
const inventoryOperationRecordsByDate = ref([]);
const isDataLoading = ref(false);

const recordSort = (operation_id, operation_name) => {
  sortOperation.id = operation_id;
  sortOperation.name = operation_name;

  if (operation_id) {
    inventoryOperationRecordsByDate.value =
      inventoryOperationBaseRecords.value.filter(
        (record) => record.inventory_operation_id == operation_id
      );
    if (inventoryOperationRecordsByDate.value.length == 0) {
      alert(
        `表示可能なデータはありません。${pickUpDate.value}の全てのデータを表示します。`
      );
      recordSort(0);
    }
  } else {
    // リセット
    inventoryOperationRecordsByDate.value = inventoryOperationBaseRecords.value;
  }
};
// カレンダーの日付が選択された場合の処理
const handleDateClick = (dateStr) => {
  inventoryOperationRecordsByDate.value = [];
  inventoryOperationBaseRecords.value = [];

  console.log(dateStr);
  pickUpDate.value = dateStr;

  getInventoryOperationRecordByDate(dateStr);
};

// クリックされた日付の入出庫データを取得
const getInventoryOperationRecordByDate = (target_date) => {
  // ローディング開始
  isDataLoading.value = true;
  
  axios
    .get(route("stock.stocks.getInventoryOperationRecordsByDate"), {
      params: {
        target_date: target_date,
      },
    })
    .then((res) => {
      console.log(res.data);

      //  二つの配列を初期化
      inventoryOperationRecordsByDate.value = res.data;
      inventoryOperationBaseRecords.value = res.data;

      console.log(res.data);
    })
    .catch((error) => {
      console.log(error);
    })
    .finally(() => {
      // ローディング終了（少し遅延を入れて自然な感じにする）
      setTimeout(() => {
        isDataLoading.value = false;
      }, 500);
    });
};

const pie_data = {
  labels: ["赤", "青", "黄", "緑", "紫", "橙"],
  datasets: [
    {
      data: [12, 19, 3, 5, 2, 3],
      backgroundColor: [
        "rgba(255, 99, 132, 0.2)",
        "rgba(54, 162, 235, 0.2)",
        "rgba(255, 206, 86, 0.2)",
        "rgba(75, 192, 192, 0.2)",
        "rgba(153, 102, 255, 0.2)",
        "rgba(255, 159, 64, 0.2)",
      ],
      borderColor: [
        "rgba(255, 99, 132, 1)",
        "rgba(54, 162, 235, 1)",
        "rgba(255, 206, 86, 1)",
        "rgba(75, 192, 192, 1)",
        "rgba(153, 102, 255, 1)",
        "rgba(255, 159, 64, 1)",
      ],
      borderWidth: 1,
    },
  ],
};

onMounted(() => {
  const today = new Date();
  const yyyy = today.getFullYear();
  const mm = String(today.getMonth() + 1).padStart(2, "0");
  const dd = String(today.getDate()).padStart(2, "0");
  const formattedToday = `${yyyy}-${mm}-${dd}`;

  handleDateClick(formattedToday);
});
</script>
<template>
  <MainLayout :title="'在庫管理'">
    <template #content>
      <MainTitle :top="'在庫管理HOME'" :sub="'入出庫および物品の作業履歴の確認が可能です。'"/>

      <!-- Stats Cards -->
      <div class="stats-grid mb-8">
        <div class="stat-card">
          <div class="stat-icon bg-blue-500">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-5m5 0v-5a2 2 0 00-2-2h-2m0 0V6a2 2 0 00-2-2h-2m0 0V4a2 2 0 00-2-2h-2"></path>
            </svg>
          </div>
          <div class="stat-content">
            <h3 class="stat-title">今日の出庫</h3>
            <p class="stat-value">{{ inventoryOperationRecordsByDate.filter(r => r.inventory_operation_id == 2).length }}</p>
          </div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon bg-green-500">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
          </div>
          <div class="stat-content">
            <h3 class="stat-title">今日の入庫</h3>
            <p class="stat-value">{{ inventoryOperationRecordsByDate.filter(r => r.inventory_operation_id == 8).length }}</p>
          </div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon bg-purple-500">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
          </div>
          <div class="stat-content">
            <h3 class="stat-title">総操作数</h3>
            <p class="stat-value">{{ inventoryOperationRecordsByDate.length }}</p>
          </div>
        </div>
      </div>

      <!-- 監視カメラ映像 -->
      <div class="camera-section mb-8">
        <div class="camera-card">
          <div class="camera-header">
            <Link :href="route('stock.camera')" class="camera-link">
              <h3 class="camera-title">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                備品倉庫監視カメラ
              </h3>
            </Link>
            <div class="rec-status">
              <span class="rec-dot"></span>
              <span class="rec-text">LIVE</span>
            </div>
          </div>
          
          <div class="camera-container" :class="{ 'expanded': watchUpStream }">
            <img
              @click="watchUpStream = !watchUpStream"
              class="camera-stream"
              src="//192.168.210.91:8080?action=stream"
              alt="監視カメラ映像"
            />
            <div class="camera-overlay">
              <button class="expand-btn" @click="watchUpStream = !watchUpStream">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content Grid -->
      <div class="main-grid">
        <!-- Calendar Section -->
        <div class="calendar-section">
          <div class="section-card">
            <div class="section-header">
              <h2 class="section-title">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                カレンダー
              </h2>
            </div>
            <Calender @date-click="handleDateClick" />
          </div>
        </div>

        <!-- Records Section -->
        <div class="records-section">
          <div class="section-card">
            <div v-if="!inventoryOperationRecordsByDate.length > 0" class="loading-state">
              <div class="loading-spinner">
                <div class="spinner"></div>
              </div>
              <h2 class="loading-text">データを読み込み中...</h2>
            </div>

            <div v-else class="records-container">
              <div class="records-header">
                <div class="date-info">
                  <h2 class="section-title">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    作業履歴
                  </h2>
                  <div class="date-badge">
                    <span class="date-text">{{ new Date(pickUpDate).toLocaleDateString("ja-JP") }}</span>
                    <span class="count-badge">{{ inventoryOperationRecordsByDate.length }}件</span>
                  </div>
                </div>
                
                <div v-if="sortOperation.name" class="filter-info">
                  <span class="filter-label">フィルター:</span>
                  <span class="filter-value" :class="{
                    'text-blue-600': sortOperation.id == 2,
                    'text-green-600': sortOperation.id == 8,
                    'text-gray-600': sortOperation.id == 9,
                  }">{{ sortOperation.name }}</span>
                </div>
              </div>
              
              <div class="filter-controls">
                <div class="filter-buttons">
                  <button
                    @click="recordSort(2, '出庫')"
                    class="filter-btn filter-btn-primary"
                    :class="{ 'active': sortOperation.id == 2 }"
                  >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                    </svg>
                    出庫
                  </button>
                  <button
                    @click="recordSort(8, '入庫')"
                    class="filter-btn filter-btn-success"
                    :class="{ 'active': sortOperation.id == 8 }"
                  >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                    入庫
                  </button>
                  <button
                    @click="recordSort(9, '数量編集')"
                    class="filter-btn filter-btn-secondary"
                    :class="{ 'active': sortOperation.id == 9 }"
                  >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    数量編集
                  </button>
                  <button
                    @click="recordSort(0)"
                    class="filter-btn filter-btn-reset"
                  >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    リセット
                  </button>
                </div>
              </div>

              <div class="records-timeline">
                <div
                  v-for="record in inventoryOperationRecordsByDate"
                  :key="record.id"
                  class="record-item"
                >
                  <div class="record-card">
                    <div class="record-header">
                      <div class="record-icon" :class="{
                        'icon-outbound': record.inventory_operation_id == 2,
                        'icon-order': record.inventory_operation_id == 7,
                        'icon-inbound': record.inventory_operation_id == 8,
                        'icon-adjust': record.inventory_operation_id == 9,
                        'icon-system': record.inventory_operation_id == 11 || record.inventory_operation_id == 12,
                        'icon-other': record.inventory_operation_id == 13,
                      }">
                        <svg v-if="record.inventory_operation_id == 2" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                        </svg>
                        <svg v-else-if="record.inventory_operation_id == 7" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <svg v-else-if="record.inventory_operation_id == 8" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                        <svg v-else-if="record.inventory_operation_id == 9" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                      </div>
                      <div class="record-time">{{ new Date(record.created_at).toLocaleTimeString("ja-JP", { hour: "2-digit", minute: "2-digit" }) }}</div>
                    </div>
                    
                    <div class="record-content">
                      <div class="record-image">
                        <img
                          :src="record.stock_img_path && record.stock_img_path.includes('storage') ? 'https://akioka.cloud/' + record.stock_img_path : record.stock_img_path ? record.stock_img_path : '/img/base/camera.png'"
                          class="product-image"
                          :alt="record.stock_name || '商品画像'"
                        />
                      </div>

                      <!-- 出庫の場合 -->
                      <div v-if="record.inventory_operation_id == 2" class="record-details">
                        <div class="record-description">
                          <span class="user-name">{{ record.user_name }}</span>
                          <span class="action-text">さんが</span>
                          <Link
                            class="product-link"
                            :href="route('stock.show.stocks', { stock_id: record.stock_id })"
                          >
                            {{ record.stock_name }}
                          </Link>
                          <span class="action-text">を</span>
                          <span class="quantity-badge outbound">{{ record.quantity }}個</span>
                          <span class="action-text">{{ record.inventory_operation_name }}しました</span>
                        </div>
                        <div class="location-info">
                          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                          </svg>
                          <span class="location-name">{{ record.location_name }}</span>
                          <span class="location-address">{{ record.address }}</span>
                        </div>
                      </div>

                      <!-- 入庫の場合 -->
                      <div v-else-if="record.inventory_operation_id == 8" class="record-details">
                        <div class="record-description">
                          <Link
                            class="product-link"
                            :href="route('stock.show.stocks', { stock_id: record.stock_id })"
                          >
                            {{ record.stock_name }}
                          </Link>
                          <span class="action-text">を</span>
                          <span class="quantity-badge inbound">{{ record.quantity }}個</span>
                          <span class="action-text">{{ record.inventory_operation_name }}しました</span>
                        </div>
                        <div class="location-info">
                          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                          </svg>
                          <span class="location-name">{{ record.location_name }}</span>
                          <span class="location-address">{{ record.address }}</span>
                        </div>
                      </div>

                      <!-- 発注依頼の場合 -->
                      <div v-else-if="record.inventory_operation_id == 7" class="record-details">
                        <div class="record-description">
                          <Link
                            class="product-link"
                            :href="route('stock.show.stocks', { stock_id: record.stock_id })"
                          >
                            {{ record.stock_name }}
                          </Link>
                          <span class="action-text">が発注依頼されました</span>
                        </div>
                        <div class="location-info">
                          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                          </svg>
                          <span class="location-name">{{ record.location_name }}</span>
                          <span class="location-address">{{ record.address }}</span>
                        </div>
                      </div>
                      <!-- 数量調整の場合 -->
                      <div v-else-if="record.inventory_operation_id == 9" class="record-details">
                        <div class="record-description">
                          <Link
                            class="product-link"
                            :href="route('stock.show.stocks', { stock_id: record.stock_id })"
                          >
                            {{ record.stock_name }}
                          </Link>
                          <span class="action-text">を</span>
                          <span class="quantity-change">
                            <span class="quantity-before">{{ record.bef_quantity }}個</span>
                            <svg class="w-4 h-4 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                            <span class="quantity-after">{{ record.quantity }}個</span>
                          </span>
                          <span class="action-text">{{ record.inventory_operation_name }}しました</span>
                        </div>
                        <div class="location-info">
                          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                          </svg>
                          <span class="location-name">{{ record.location_name }}</span>
                          <span class="location-address">{{ record.address }}</span>
                        </div>
                      </div>

                      <!-- 録画開始の場合 -->
                      <div v-else-if="record.inventory_operation_id == 11" class="record-details">
                        <div class="record-description system-action">
                          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                          </svg>
                          <span class="system-text">録画開始</span>
                        </div>
                      </div>
                      
                      <!-- 録画終了の場合 -->
                      <div v-else-if="record.inventory_operation_id == 12" class="record-details">
                        <div class="record-description system-action">
                          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path>
                          </svg>
                          <span class="system-text">録画終了</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Data Loading Component -->
      <SearchLoading 
        :isLoading="isDataLoading"
        title="データ取得中..."
        message="在庫操作履歴を取得しています。しばらくお待ちください。"
      />
    </template>
  </MainLayout>
</template>
<style scoped lang="scss">
// Hero Section
.hero-section {
  @apply bg-gradient-to-br from-blue-50 to-indigo-100 rounded-2xl p-8 mb-8;
  
  .hero-content {
    @apply text-center;
    
    .hero-title {
      @apply text-4xl font-bold text-gray-900 mb-3;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    .hero-subtitle {
      @apply text-lg text-gray-600 font-medium;
    }
  }
}

// Stats Grid
.stats-grid {
  @apply grid grid-cols-1 md:grid-cols-3 gap-6;
  
  .stat-card {
    @apply bg-white rounded-xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1;
    
    .stat-icon {
      @apply w-12 h-12 rounded-lg flex items-center justify-center text-white mb-4;
    }
    
    .stat-content {
      .stat-title {
        @apply text-sm font-medium text-gray-600 mb-1;
      }
      
      .stat-value {
        @apply text-3xl font-bold text-gray-900;
      }
    }
  }
}

// Camera Section
.camera-section {
  .camera-card {
    @apply bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden;
    
    .camera-header {
      @apply flex items-center justify-between p-4 bg-gray-50 border-b border-gray-100;
      
      .camera-link {
        @apply hover:text-blue-600 transition-colors duration-200;
        
        .camera-title {
          @apply text-lg font-semibold text-gray-800 flex items-center;
        }
      }
      
      .rec-status {
        @apply flex items-center space-x-2;
        
        .rec-dot {
          @apply w-3 h-3 bg-red-500 rounded-full animate-pulse;
        }
        
        .rec-text {
          @apply text-sm font-semibold text-red-600;
        }
      }
    }
    
    .camera-container {
      @apply relative transition-all duration-500 ease-in-out;
      
      &:not(.expanded) {
        @apply h-48;
      }
      
      &.expanded {
        @apply h-96;
      }
      
      .camera-stream {
        @apply w-full h-full object-cover cursor-pointer transition-transform duration-300 hover:scale-105;
      }
      
      .camera-overlay {
        @apply absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-20 transition-all duration-300 flex items-center justify-center opacity-0 hover:opacity-100;
        
        .expand-btn {
          @apply bg-white bg-opacity-90 hover:bg-opacity-100 rounded-full p-3 text-gray-700 hover:text-gray-900 transition-all duration-200 transform hover:scale-110;
        }
      }
    }
  }
}

// Main Grid
.main-grid {
  @apply grid grid-cols-1 lg:grid-cols-5 gap-8;
  
  .calendar-section {
    @apply lg:col-span-2;
  }
  
  .records-section {
    @apply lg:col-span-3;
  }
}

// Section Cards
.section-card {
  @apply bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden;
  
  .section-header {
    @apply p-6 bg-gray-50 border-b border-gray-100;
    
    .section-title {
      @apply text-xl font-semibold text-gray-800 flex items-center;
    }
  }
}

// Loading State
.loading-state {
  @apply flex flex-col items-center justify-center p-12;
  
  .loading-spinner {
    @apply mb-6;
    
    .spinner {
      @apply w-12 h-12 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin;
    }
  }
  
  .loading-text {
    @apply text-lg text-gray-600 font-medium;
  }
}

// Records Container
.records-container {
  .records-header {
    @apply p-6 bg-gray-50 border-b border-gray-100;
    
    .date-info {
      @apply flex items-center justify-between mb-4;
      
      .date-badge {
        @apply flex items-center space-x-3;
        
        .date-text {
          @apply text-lg font-semibold text-gray-800;
        }
        
        .count-badge {
          @apply bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold;
        }
      }
    }
    
    .filter-info {
      @apply flex items-center space-x-2 text-sm;
      
      .filter-label {
        @apply text-gray-600;
      }
      
      .filter-value {
        @apply font-semibold px-2 py-1 rounded bg-gray-100;
      }
    }
  }
  
  .filter-controls {
    @apply p-4 bg-white border-b border-gray-100;
    
    .filter-buttons {
      @apply flex flex-wrap gap-2;
      
      .filter-btn {
        @apply flex items-center px-4 py-2 rounded-lg font-medium text-sm transition-all duration-200 border-2 border-transparent;
        
        &.filter-btn-primary {
          @apply bg-blue-50 text-blue-700 hover:bg-blue-100 border-blue-200;
          
          &.active {
            @apply bg-blue-600 text-white border-blue-600 shadow-lg;
          }
        }
        
        &.filter-btn-success {
          @apply bg-green-50 text-green-700 hover:bg-green-100 border-green-200;
          
          &.active {
            @apply bg-green-600 text-white border-green-600 shadow-lg;
          }
        }
        
        &.filter-btn-secondary {
          @apply bg-gray-50 text-gray-700 hover:bg-gray-100 border-gray-200;
          
          &.active {
            @apply bg-gray-600 text-white border-gray-600 shadow-lg;
          }
        }
        
        &.filter-btn-reset {
          @apply bg-red-50 text-red-700 hover:bg-red-100 border-red-200 hover:bg-red-600 hover:text-white;
        }
      }
    }
  }
}

// Records Timeline
.records-timeline {
  @apply max-h-96 overflow-y-auto p-4;
  
  &::-webkit-scrollbar {
    @apply w-2;
  }
  
  &::-webkit-scrollbar-track {
    @apply bg-gray-100 rounded-full;
  }
  
  &::-webkit-scrollbar-thumb {
    @apply bg-gray-300 rounded-full hover:bg-gray-400;
  }
  
  .record-item {
    @apply mb-4 last:mb-0;
    
    .record-card {
      @apply bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden;
      
      .record-header {
        @apply flex items-center justify-between p-3 bg-gray-50 border-b border-gray-100;
        
        .record-icon {
          @apply w-8 h-8 rounded-full flex items-center justify-center text-white;
          
          &.icon-outbound {
            @apply bg-blue-500;
          }
          
          &.icon-inbound {
            @apply bg-green-500;
          }
          
          &.icon-order {
            @apply bg-orange-500;
          }
          
          &.icon-adjust {
            @apply bg-purple-500;
          }
          
          &.icon-system {
            @apply bg-gray-500;
          }
          
          &.icon-other {
            @apply bg-indigo-500;
          }
        }
        
        .record-time {
          @apply text-sm font-medium text-gray-600;
        }
      }
      
      .record-content {
        @apply p-4;
        
        .record-image {
          @apply flex justify-center mb-4;
          
          .product-image {
            @apply w-16 h-16 object-cover rounded-lg border border-gray-200;
          }
        }
        
        .record-details {
          .record-description {
            @apply mb-3 flex flex-wrap items-center gap-1 text-sm leading-relaxed;
            
            .user-name {
              @apply font-semibold text-gray-800 bg-gray-100 px-2 py-1 rounded;
            }
            
            .product-link {
              @apply font-semibold text-blue-600 hover:text-blue-800 hover:underline transition-colors duration-200;
            }
            
            .action-text {
              @apply text-gray-700;
            }
            
            .quantity-badge {
              @apply font-bold px-2 py-1 rounded text-white;
              
              &.outbound {
                @apply bg-blue-500;
              }
              
              &.inbound {
                @apply bg-green-500;
              }
            }
            
            .quantity-change {
              @apply flex items-center bg-gray-100 px-2 py-1 rounded;
              
              .quantity-before {
                @apply text-red-600 font-semibold;
              }
              
              .quantity-after {
                @apply text-green-600 font-semibold;
              }
            }
            
            &.system-action {
              @apply justify-center text-gray-600 font-medium;
              
              .system-text {
                @apply font-semibold;
              }
            }
          }
          
          .location-info {
            @apply flex items-center text-sm text-gray-600 bg-gray-50 px-3 py-2 rounded-lg;
            
            .location-name {
              @apply font-medium text-gray-800 mr-2;
            }
            
            .location-address {
              @apply text-gray-600;
            }
          }
        }
      }
    }
  }
}

// Responsive Design
@media (max-width: 768px) {
  .hero-section {
    @apply p-6;
    
    .hero-content {
      .hero-title {
        @apply text-3xl;
      }
      
      .hero-subtitle {
        @apply text-base;
      }
    }
  }
  
  .stats-grid {
    @apply grid-cols-1;
  }
  
  .main-grid {
    @apply grid-cols-1;
  }
  
  .filter-controls {
    .filter-buttons {
      @apply justify-center;
    }
  }
}

// Animations
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.record-item {
  animation: fadeInUp 0.3s ease-out;
}

.stat-card {
  animation: fadeInUp 0.3s ease-out;
}

.camera-card {
  animation: fadeInUp 0.3s ease-out;
}
</style>