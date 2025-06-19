<script setup>
import { ref, onMounted, onUnmounted, watch } from "vue";
import axios from "axios";

const props = defineProps({
  gantt_update: Boolean,
});

const tasks = ref([]);
const timeSlots = ref([]);
const isMounted = ref(true); // 追加

watch(
  () => props.gantt_update,
  (newVal) => {
    if (newVal) {
      console.log("ガントチャートを更新します。");
      generateTimeSlots();
      getGantData();
    }
  }
);

const getGantData = () => {
  axios
    .get(route("task.getGanttData"))
    .then((res) => {
      if (isMounted.value) {
        // アンマウント済みなら何もしない
        console.log("Gantt API Response:", res.data);
        tasks.value = res.data;
      }
    })
    .catch((error) => {
      if (isMounted.value) {
        console.error("Gantt API Error:", error);
      }
    });
};

// スロットが任意の稼働時間帯に含まれているか判定
const isActive = (slotValue, periods) => {
  return periods.some((p) => slotValue >= p.start && slotValue < p.end);
};

// 時間スロットを現在時刻に基づき動的に生成
const generateTimeSlots = () => {
  const startHour = 8;
  const now = new Date();
  const nowHour = now.getHours();
  const nowMin = now.getMinutes();
  // 今の時刻を10分単位で切り上げ
  const rawEnd = nowHour + nowMin / 60;
  const endHour = Math.ceil(rawEnd * 6) / 6;

  const slots = [];
  for (let h = startHour; h <= endHour; h++) {
    for (let m = 0; m < 60; m += 10) {
      const value = h + m / 60;
      if (value > endHour) break; // 余分なスロットを追加しない
      const label = `${String(h).padStart(2, "0")}:${String(m).padStart(
        2,
        "0"
      )}`;
      slots.push({ value, label });
    }
  }
  timeSlots.value = slots;
};

onMounted(() => {
  isMounted.value = true;
  generateTimeSlots();
  getGantData();
});

onUnmounted(() => {
  isMounted.value = false;
});
</script>

<template>
  <div
    style="box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px"
    class="overflow-x-auto"
  >
    <table class="table-auto border-collapse w-full text-xs">
      <thead>
        <tr>
          <th class="bg-gray-50 border px-4 py-2 w-40 whitespace-nowrap">
            ユーザー名
          </th>
          <th class="bg-gray-50 border px-4 py-2 w-40 whitespace-nowrap">
            タスク名
          </th>
          <th
            v-for="slot in timeSlots"
            :key="slot.label"
            class="bg-gray-50 border px-1 py-1 w-10 text-center"
            :class="{ 'bg-red-100': slot.value >= 17 }"
          >
            {{ slot.label }}
          </th>
        </tr>
      </thead>
      <tbody>
        <template v-for="(user, userIndex) in tasks" :key="userIndex">
          <tr v-for="(task, taskIndex) in user.tasks" :key="taskIndex">
            <td
              v-if="taskIndex === 0"
              :rowspan="user.tasks.length"
              class="bg-gray-50 border px-2 text-center align-top whitespace-nowrap"
            >
              {{ user.name }}
            </td>
            <td class="bg-gray-50 border px-2 whitespace-nowrap">
              {{ task.name }}
            </td>
            <td
              v-for="slot in timeSlots"
              :key="slot.label"
              class="border h-6 p-0"
              :class="{ 'bg-red-100': slot.value >= 17 }"
            >
              <div
                :class="[
                  isActive(slot.value, task.periods) ? task.color : '',
                  'h-full w-full',
                ]"
              />
            </td>
          </tr>
        </template>
      </tbody>
    </table>
  </div>
</template>
<style scoped lang="scss">
</style>
