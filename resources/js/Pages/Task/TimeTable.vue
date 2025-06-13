<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
  taskTransactions: {
    type: Array,
    required: true
  }
});

const timeSlots = ref([]);
const users = ref([]);

const generateTimeSlots = () => {
  const slots = [];
  for (let hour = 9; hour <= 18; hour++) {
    slots.push({
      start: `${hour.toString().padStart(2, '0')}:00`,
      end: `${(hour + 1).toString().padStart(2, '0')}:00`
    });
  }
  return slots;
};

const generateUserTimeData = () => {
  const userData = {};
  const timeData = {};

  // ユーザーごとのデータを初期化
  users.value.forEach(user => {
    userData[user.id] = {};
    timeSlots.value.forEach(slot => {
      userData[user.id][slot.start] = 0;
    });
  });

  // トランザクションデータから時間を集計
  props.taskTransactions.forEach(transaction => {
    const date = new Date(transaction.created_at);
    const hour = date.getHours();
    const timeSlot = `${hour.toString().padStart(2, '0')}:00`;

    if (userData[transaction.user_id] && userData[transaction.user_id][timeSlot] !== undefined) {
      userData[transaction.user_id][timeSlot]++;
    }
  });

  return userData;
};

onMounted(() => {
  timeSlots.value = generateTimeSlots();
  users.value = [
    { id: 48, name: '中村仁美' },
    { id: 68, name: '岡堂莉子' },
    { id: 81, name: '三谷優月' },
    { id: 120, name: '風早結衣' },
    { id: 43, name: '中原清志' },
    { id: 91, name: '村上飛羽' }
  ];
});
</script>

<template>
  <div class="overflow-x-auto">
    <table class="min-w-full bg-white border border-gray-300">
      <thead>
        <tr>
          <th class="sticky left-0 bg-gray-100 border-b border-r p-2">ユーザー</th>
          <th v-for="slot in timeSlots" :key="slot.start" class="border-b border-r p-2">
            {{ slot.start }}<br>-<br>{{ slot.end }}
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id">
          <td class="sticky left-0 bg-gray-50 border-b border-r p-2">{{ user.name }}</td>
          <td v-for="slot in timeSlots" :key="slot.start" class="border-b border-r p-2 text-center">
            {{ generateUserTimeData()[user.id]?.[slot.start] || 0 }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<style scoped>
.overflow-x-auto {
  overflow-x: auto;
  white-space: nowrap;
  -webkit-overflow-scrolling: touch;
}

.sticky {
  position: sticky;
  z-index: 1;
}
</style> 