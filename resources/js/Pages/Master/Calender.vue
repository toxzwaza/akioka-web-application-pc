<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { ref, onMounted, computed, nextTick } from "vue";
import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import interactionPlugin from "@fullcalendar/interaction";
import jaLocale from "@fullcalendar/core/locales/ja";
import axios from "axios";
import MainTitle from "@/Components/Title/MainTitle.vue"

const holidays = ref([]);
const events = ref([]);
const savedHolidays = ref([]); // 保存済みの休日を追跡
const calendarRef = ref(null); // FullCalendarのref
const isSaving = ref(false); // 保存中フラグ
const lastSavedTime = ref(null); // 最終保存時刻

// パフォーマンス向上のため、Setに変換
const savedHolidaysSet = computed(() => new Set(savedHolidays.value));
const holidaysSet = computed(() => new Set(holidays.value));

// ローカル日付文字列を取得するヘルパー関数
const getLocalDateString = (date) => {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
};

// バックグラウンドで休日を保存する関数
const saveHolidays = async () => {
  if (isSaving.value) return; // 保存中は実行しない
  
  isSaving.value = true;
  try {
    const res = await axios.post(route("master.store.holiday"), { 
      holidays: holidays.value 
    });
    
    if (res.data.status) {
      // 保存成功時に保存済み休日を更新
      savedHolidays.value = [...holidays.value];
      lastSavedTime.value = new Date();
      console.log('休日情報を自動保存しました');
      
      // カレンダーを再レンダリング
      nextTick(() => {
        if (calendarRef.value) {
          calendarRef.value.getApi().render();
        }
      });
    }
  } catch (error) {
    console.error('保存に失敗しました:', error);
  } finally {
    isSaving.value = false;
  }
};

// 年月別に休日をグループ化
const holidaysByYearMonth = computed(() => {
  const grouped = {};
  
  holidays.value.forEach((holiday) => {
    const date = new Date(holiday);
    const year = date.getFullYear();
    const month = date.getMonth() + 1;
    const key = `${year}-${month}`;
    
    if (!grouped[key]) {
      grouped[key] = {
        year,
        month,
        holidays: []
      };
    }
    grouped[key].holidays.push(holiday);
  });
  
  // 年月順にソート
  return Object.values(grouped).sort((a, b) => {
    if (a.year !== b.year) return a.year - b.year;
    return a.month - b.month;
  });
});

// 選択中の休日総数
const totalHolidays = computed(() => holidays.value.length);

// 日付クリックで休日をトグル
const handleDateClick = async (info) => {
  const date = info.dateStr;
  const index = holidays.value.findIndex((h) => h === date);
  
  if (index === -1) {
    holidays.value.push(date);
    console.log('休日を追加:', date);
  } else {
    holidays.value.splice(index, 1);
    console.log('休日を解除:', date);
  }
  
  // カレンダーを即座に再レンダリング
  nextTick(() => {
    if (calendarRef.value) {
      calendarRef.value.getApi().render();
    }
  });
  
  // バックグラウンドで自動保存
  await saveHolidays();
};

// 休日データを取得する関数
const fetchHolidays = async () => {
  try {
    const response = await axios.get(route("master.get.holidays"));
    console.log(response.data);
    holidays.value = response.data;
    savedHolidays.value = [...response.data]; // 保存済み休日も更新
    // データ取得後にカレンダーを再レンダリング
    nextTick(() => {
      if (calendarRef.value) {
        calendarRef.value.getApi().render();
      }
    });
  } catch (error) {
    console.error("休日データの取得に失敗しました:", error);
  }
};

// 既に登録されている休日情報を取得
const handleDatesSet = async (info) => {
  // 月の変更時にも呼ばれるが、初期データは onMounted で取得済み
  if (holidays.value.length === 0) {
    await fetchHolidays();
  }
};

// コンポーネントマウント時に休日データを取得
onMounted(async () => {
  await fetchHolidays();
});

// 土日判定関数
const isWeekend = (date) => {
  const day = new Date(date).getDay();
  return day === 0 || day === 6; // 0が日曜、6が土曜
};

// イベントの生成関数を追加
const getEvents = () => {
  // 休日のイベントを作成
  const holidayEvents = holidays.value.map((date) => ({
    start: date,
    display: "background",
    backgroundColor: "#e5e7eb", // グレー
  }));

  // 土日のイベントを作成
  const weekendEvents = events.value
    .filter((event) => isWeekend(event.start))
    .map((date) => ({
      start: date,
      display: "background",
      backgroundColor: "#e5e7eb", // グレー
    }));

  return [...events.value, ...holidayEvents, ...weekendEvents];
};

const calendarOptions = {
  plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
  initialView: "dayGridMonth",
  editable: true,
  selectable: true,
  events: getEvents, // 関数を指定
  dateClick: handleDateClick,
  datesSet: handleDatesSet,
  locale: jaLocale,
  // 土日の文字色を設定
  businessHours: {
    daysOfWeek: [1, 2, 3, 4, 5], // 月-金を営業日として指定
  },
  // 休日の背景色を設定
  dayCellClassNames: (arg) => {
    const dateStr = getLocalDateString(arg.date);
    // 登録済みの休日は赤色（土日より優先）- Setを使用して高速化
    if (savedHolidaysSet.value.has(dateStr)) {
      return ["saved-holiday-day"];
    }
    // 土日はグレー色
    if (isWeekend(arg.date)) {
      return ["weekend-day"];
    }
    return [];
  },
  // 日付セルの内容をカスタマイズして「登録済み」を表示
  dayCellContent: (arg) => {
    const dateStr = getLocalDateString(arg.date);
    const isSaved = savedHolidaysSet.value.has(dateStr);
    const isSelected = holidaysSet.value.has(dateStr);
    
    return {
      html: `
        <div class="fc-daygrid-day-top">
          <a class="fc-daygrid-day-number">${arg.dayNumberText}</a>
          ${isSaved && isSelected ? '<span class="saved-badge">登録済み</span>' : ''}
        </div>
      `
    };
  },
};
</script>

<template>
  <MainLayout title="在庫管理">
    <template #content>
      <MainTitle :top="'カレンダー設定'" :sub="'社内カレンダーの設定画面です。設定した休日情報は発注書にて使用されます。'"/>
      <div class="flex gap-6">
        <div class="flex-1 p-6">
          <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 p-6 text-white">
              <div class="flex items-start justify-between">
                <div>
                  <h2 class="text-2xl font-bold mb-2">社内休日カレンダー設定</h2>
                  <p class="text-sm opacity-90">日付をクリックして休日を選択・解除できます</p>
                </div>
                <div class="flex items-center gap-2 bg-white/20 backdrop-blur-sm rounded-lg px-3 py-1.5">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                  </svg>
                  <span class="text-xs font-semibold">自動保存</span>
                </div>
              </div>
            </div>
            <div class="p-6">
              <FullCalendar ref="calendarRef" :options="calendarOptions" />
            </div>
          </div>
        </div>
        <div class="w-96 p-6">
          <div class="sticky top-6">
            <!-- ヘッダー -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-t-2xl p-6 text-white shadow-lg">
              <h2 class="text-2xl font-bold mb-2">選択中の休日</h2>
              <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-sm font-medium opacity-90">
                  合計 <span class="text-xl font-bold">{{ totalHolidays }}</span> 日
                </span>
              </div>
            </div>

            <!-- 保存ステータス -->
            <div class="w-full bg-gradient-to-r from-gray-50 to-gray-100 border border-gray-200 py-4 px-6 flex items-center justify-center gap-2">
              <div v-if="isSaving" class="flex items-center gap-2 text-blue-600">
                <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm font-semibold">保存中...</span>
              </div>
              <div v-else class="flex items-center gap-2 text-green-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-sm font-semibold">自動保存済み</span>
              </div>
            </div>

            <!-- 休日リスト -->
            <div class="bg-white rounded-b-2xl shadow-lg overflow-hidden">
              <div class="max-h-[calc(100vh-400px)] overflow-y-auto custom-scrollbar">
                <div v-if="totalHolidays === 0" class="p-12 text-center text-gray-400">
                  <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <p class="text-sm font-medium">休日が選択されていません</p>
                  <p class="text-xs mt-1">カレンダーの日付をクリックして選択してください</p>
                </div>
                
                <div v-for="monthData in holidaysByYearMonth" :key="`${monthData.year}-${monthData.month}`" class="border-b border-gray-100 last:border-b-0">
                  <div class="p-4 bg-gradient-to-r from-gray-50 to-white">
                    <div class="flex items-center gap-2 mb-3">
                      <div class="w-1 h-6 bg-blue-500 rounded-full"></div>
                      <div class="flex items-baseline gap-2">
                        <h3 class="text-lg font-bold text-gray-800">{{ monthData.year }}年</h3>
                        <span class="text-base font-semibold text-gray-600">{{ monthData.month }}月</span>
                      </div>
                      <span class="ml-auto text-xs font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                        {{ monthData.holidays.length }}日
                      </span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                      <span
                        v-for="holiday in monthData.holidays"
                        :key="holiday"
                        class="inline-flex items-center px-3 py-1.5 bg-gradient-to-br from-red-50 to-red-100 text-red-700 text-sm font-semibold rounded-lg border border-red-200 shadow-sm hover:shadow-md transition-shadow duration-200"
                      >
                        {{ new Date(holiday).getDate() }}日
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </MainLayout>
</template>

<style scoped>
/* FullCalendarのスタイル調整 */
:deep(.fc) {
  max-width: 100%;
  margin: 0 auto;
  font-family: inherit;
}

/* ツールバーのスタイル */
:deep(.fc-toolbar) {
  margin-bottom: 1.5rem;
  gap: 0.5rem;
}

:deep(.fc-toolbar-title) {
  font-size: 1.5rem !important;
  font-weight: 700 !important;
  color: #1f2937;
}

:deep(.fc-button) {
  background: linear-gradient(to bottom right, #6366f1, #4f46e5) !important;
  border: none !important;
  border-radius: 0.5rem !important;
  padding: 0.5rem 1rem !important;
  font-weight: 600 !important;
  text-transform: none !important;
  box-shadow: 0 2px 4px rgba(99, 102, 241, 0.2) !important;
  transition: all 0.2s !important;
}

:deep(.fc-button:hover) {
  background: linear-gradient(to bottom right, #4f46e5, #4338ca) !important;
  box-shadow: 0 4px 8px rgba(99, 102, 241, 0.3) !important;
  transform: translateY(-1px);
}

:deep(.fc-button:active) {
  transform: translateY(0);
}

:deep(.fc-button:disabled) {
  opacity: 0.5 !important;
  cursor: not-allowed !important;
}

:deep(.fc-button-primary:not(:disabled):active),
:deep(.fc-button-primary:not(:disabled).fc-button-active) {
  background: linear-gradient(to bottom right, #4338ca, #3730a3) !important;
  box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1) !important;
}

/* ヘッダーのスタイル */
:deep(.fc-col-header-cell) {
  background: linear-gradient(to bottom, #f8fafc, #f1f5f9) !important;
  border-color: #e2e8f0 !important;
  padding: 1rem 0.5rem !important;
  font-weight: 700 !important;
  color: #475569 !important;
  text-transform: uppercase !important;
  font-size: 0.75rem !important;
  letter-spacing: 0.05em;
}

/* セルのスタイル */
:deep(.fc-daygrid-day) {
  border-color: #e2e8f0 !important;
  transition: all 0.2s;
  cursor: pointer;
}

:deep(.fc-daygrid-day:hover) {
  background-color: #f8fafc !important;
  box-shadow: inset 0 0 0 2px #c7d2fe;
}

:deep(.fc-daygrid-day-number) {
  padding: 0.5rem !important;
  font-weight: 600 !important;
  color: #1f2937 !important;
  font-size: 0.875rem !important;
}

/* 土日の背景色 */
:deep(.weekend-day) {
  background-color: #f1f5f9 !important;
}

/* 登録済み休日の背景色（赤） */
:deep(.saved-holiday-day) {
  background: linear-gradient(to bottom right, #fecaca, #fca5a5) !important;
  border-color: #f87171 !important;
}

:deep(.saved-holiday-day .fc-daygrid-day-number) {
  color: #991b1b !important;
  font-weight: 700 !important;
}

/* 今日の日付の背景色 */
:deep(.fc-day-today) {
  background: linear-gradient(to bottom right, #dbeafe, #bfdbfe) !important;
  border-color: #60a5fa !important;
}

:deep(.fc-day-today .fc-daygrid-day-number) {
  color: #1e40af !important;
  font-weight: 700 !important;
}

/* 土曜日の文字色 */
:deep(.fc-day-sat .fc-daygrid-day-number) {
  color: #2563eb !important;
}

/* 日曜日の文字色 */
:deep(.fc-day-sun .fc-daygrid-day-number) {
  color: #dc2626 !important;
}

/* テーブル全体 */
:deep(.fc-scrollgrid) {
  border-radius: 0.75rem !important;
  overflow: hidden !important;
  border-color: #e2e8f0 !important;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
}

/* 登録済みバッジのスタイル */
:deep(.saved-badge) {
  display: inline-block;
  background: linear-gradient(to right, #10b981, #059669);
  color: white;
  font-size: 0.65rem;
  padding: 3px 8px;
  border-radius: 9999px;
  margin-left: 6px;
  font-weight: 600;
  box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);
  letter-spacing: 0.025em;
}

:deep(.fc-daygrid-day-top) {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 2px 4px;
}

/* カスタムスクロールバー */
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
  transition: background 0.2s;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>