<script setup>
import { onMounted, ref } from "vue";
import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import interactionPlugin from "@fullcalendar/interaction";
import axios from "axios";

// イベントデータをrefで管理
const events = ref([]);

// onMounted を使用してカレンダーを初期化
onMounted(() => {
  const calendarEl = document.getElementById("calendar");

  const calendar = new Calendar(calendarEl, {
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
    initialView: "dayGridMonth",
    editable: true,
    selectable: true,
    events: events.value,
    dateClick(info) {
      alert(`選択された日付: ${info.dateStr}`);
    },
    // 月が変更されたときに呼び出されるコールバック
    datesSet(info) {
      // ここで新しい月のデータを取得し、eventsを更新
      fetchEventsForMonth(info.start.toISOString().split('T')[0], info.end.toISOString().split('T')[0]).then(newEvents => {
        calendar.removeAllEvents(); // 既存のイベントを削除
        events.value = newEvents; // eventsを新しいイベントで更新
        calendar.addEventSource(events.value); // 新しいイベントを追加
      });

    },
  });

  calendar.render();
});

// 新しい月のイベントデータを取得する関数
function fetchEventsForMonth(start, end) {
  // ここでAPI呼び出しを実装
  return axios.get(route('stock.stocks.getInventoryOperationRecords'), {
    params: {
      start_date: start,
      end_date: end
    }
  })
  .then(res => {
    console.log(res.data)
    // APIから取得したデータを返す
    return res.data;
  })
  .catch(error => {
    console.error("イベントデータの取得に失敗しました:", error);
    return [];
  });
}
</script>
<style scoped>

</style>
<template>
  <div id="calendar"></div>
</template>
