<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { ref } from "vue";
import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import interactionPlugin from "@fullcalendar/interaction";
import jaLocale from "@fullcalendar/core/locales/ja";
import axios from "axios";

const holidays = ref([]);
const events = ref([]);

const updateCalender = () => {
  axios
    .post(route("master.store.holiday"), { holidays: holidays.value })
    .then((res) => {
      console.log(res.data);
    })
    .catch((error) => {
      console.log(error);
    });
};

// 指定された月の休日を取得する関数を追加
const getHolidaysForMonth = (month) => {
  return holidays.value
    .filter((holiday) => new Date(holiday).getMonth() + 1 === month)
    .sort((a, b) => new Date(a) - new Date(b)); // 日付順にソート
};

// 日付クリックで休日をトグル
const handleDateClick = (info) => {
  const date = info.dateStr;
  const index = holidays.value.findIndex((h) => h === date);
  if (index === -1) {
    holidays.value.push(date);

    console.log(holidays.value);
  } else {
    holidays.value.splice(index, 1);
  }
};

// 既に登録されている休日情報を取得
const handleDatesSet = async (info) => {
  try {
    const start = info.start.toISOString().split("T")[0];
    const end = info.end.toISOString().split("T")[0];

    const response = await axios.get(
      route("master.get.holidays")
    ).then(res => {
        console.log(res.data)
        holidays.value = res.data
    });
  } catch (error) {
    console.error("イベントデータの取得に失敗しました:", error);
    events.value = [];
  }
};

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
    if (holidays.value.includes(arg.date.toISOString().split("T")[0])) {
      return ["holiday-day"];
    }
    if (isWeekend(arg.date)) {
      return ["weekend-day"];
    }
    return [];
  },
};
</script>

<template>
  <MainLayout title="在庫管理">
    <template #content>
      <h1 class="text-center text-xl font-bold text-gray-800 mb-12">
        カレンダー
      </h1>
      <div class="flex justify-between">
        <div class="p-6 w-2/3">
          <h2 class="text-xl font-bold mb-4">社内休日カレンダー設定</h2>
          <FullCalendar :options="calendarOptions" />
        </div>
        <div class="p-6 w-1/3">
          <h2 class="text-xl font-bold mb-4">選択中の休日情報</h2>
          <button
            @click="updateCalender"
            class="mb-4 text-sm bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
          >
            確定
          </button>
          <div v-for="month in 12" :key="month" class="mb-4">
            <h3 class="text-lg font-bold">{{ month }}月</h3>
            <ul>
              <li class="mb-2 text-gray-600 flex flex-wrap">
                <span
                  v-for="holiday in getHolidaysForMonth(month)"
                  :key="holiday"
                  class="block mr-2"
                  >{{ new Date(holiday).getDate() }}日</span
                >
              </li>
            </ul>
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
}

:deep(.weekend-day) {
  background-color: #e5e7eb !important;
}

:deep(.holiday-day) {
  background-color: #e5e7eb !important; /* 休日の日付の背景色 */
}

:deep(.fc-day-today) {
  background-color: #dbeafe !important; /* 今日の日付の背景色 */
}

:deep(.fc-day-sat) {
  color: #2563eb; /* 土曜日の文字色 */
}

:deep(.fc-day-sun) {
  color: #dc2626; /* 日曜日の文字色 */
}
</style>