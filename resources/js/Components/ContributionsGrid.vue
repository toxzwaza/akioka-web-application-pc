
<script>
export default {
  name: "OvertimeGrid",
  data() {
    const currentYear = new Date().getFullYear();
    return {
      selectedYear: currentYear,
      years: [2025, 2024, 2023],
      grid: [],
      totalHours: 0,
      monthGridLabels: [],
    };
  },
  mounted() {
    this.fetchOvertimeData();
  },
  methods: {
    selectYear(year) {
      this.selectedYear = year;
      this.fetchOvertimeData();
    },
    async fetchOvertimeData() {
      const year = this.selectedYear;
      const startDate = new Date(year, 0, 1);
      const endDate = new Date(year, 11, 31);

      // ✳️ API取得部分（仮でランダムデータ生成）
      const dates = [];
      let current = new Date(startDate);
      while (current <= endDate) {
        dates.push({
          date: current.toISOString().slice(0, 10),
          hours: parseFloat((Math.random() * 4).toFixed(1)), // 0〜4時間
        });
        current.setDate(current.getDate() + 1);
      }

      // グリッド作成
      const weeks = [];
      let week = new Array(7).fill(null);

      const firstDay = new Date(startDate).getDay();
      let dateIndex = 0;
      let dow = firstDay;

      week = new Array(7).fill(null);
      while (dow < 7 && dateIndex < dates.length) {
        const day = new Date(dates[dateIndex].date).getDay();
        week[day] = dates[dateIndex];
        dateIndex++;
        dow++;
      }
      weeks.push(week);

      week = new Array(7).fill(null);
      while (dateIndex < dates.length) {
        const day = new Date(dates[dateIndex].date).getDay();
        week[day] = dates[dateIndex];
        if (day === 6 || dateIndex === dates.length - 1) {
          weeks.push(week);
          week = new Array(7).fill(null);
        }
        dateIndex++;
      }

      // 月ラベル
      const monthLabels = [];
      let lastMonth = -1;
      for (let i = 0; i < weeks.length; i++) {
        const firstDay = weeks[i].find((d) => d !== null);
        if (firstDay) {
          const m = new Date(firstDay.date).getMonth();
          if (m !== lastMonth) {
            monthLabels.push(m + 1);
            lastMonth = m;
          } else {
            monthLabels.push("");
          }
        } else {
          monthLabels.push("");
        }
      }

      this.grid = weeks;
      this.monthGridLabels = monthLabels;
      this.totalHours = dates.reduce((sum, d) => sum + d.hours, 0).toFixed(1);
    },
    getColor(hours) {
      if (hours === 0) return "bg-gray-200";
      if (hours <= 1) return "bg-green-100";
      if (hours <= 2) return "bg-green-300";
      if (hours <= 3) return "bg-yellow-400";
      return "bg-red-500";
    },
    formatTooltip(dateStr, hours) {
      const date = new Date(dateStr);
      const formatted = `${date.getMonth() + 1}月${date.getDate()}日`;
      return `${formatted} 残業時間: ${hours}時間`;
    },
  },
};
</script>

<template>
  <div class="p-4 flex">
    <div>
      <h2 class="text-lg font-semibold mb-2">
        名前 --- {{ totalHours }} 時間の残業（{{ selectedYear }}年）
      </h2>

      <!-- 月ラベル -->
      <div class="flex text-xs text-gray-600 ml-6 mb-1">
        <div class="w-4 h-4"></div>
        <div
          v-for="(label, index) in monthGridLabels"
          :key="index"
          class="w-3 h-3 mr-1 text-center"
        >
          {{ label }}
        </div>
      </div>

      <div class="flex">
        <!-- 曜日ラベル -->
        <div
          class="flex flex-col justify-between mr-1 text-xs text-gray-500 h-[104px]"
        >
          <span>月</span>
          <span>水</span>
          <span>金</span>
        </div>

        <!-- グリッド -->
        <div
          v-for="(week, weekIndex) in grid"
          :key="weekIndex"
          class="flex flex-col mr-1"
        >
          <div
            v-for="(day, dayIndex) in week"
            :key="dayIndex"
            class="w-3 h-3 rounded mb-1"
            :class="getColor(day?.hours ?? 0)"
            :title="day ? formatTooltip(day.date, day.hours) : ''"
          />
        </div>
      </div>

      <!-- 凡例 -->
      <div class="text-xs mt-2 text-gray-600 flex justify-between">
        <span>少</span>
        <div class="flex space-x-1">
          <div class="w-3 h-3 rounded-sm bg-gray-200"></div>
          <div class="w-3 h-3 rounded-sm bg-green-100"></div>
          <div class="w-3 h-3 rounded-sm bg-green-300"></div>
          <div class="w-3 h-3 rounded-sm bg-yellow-400"></div>
          <div class="w-3 h-3 rounded-sm bg-red-500"></div>
        </div>
        <span>多</span>
      </div>
    </div>

    <!-- 年選択 -->
    <div class="ml-6">
      <div v-for="y in years" :key="y" class="mb-2">
        <button
          class="px-4 py-1 rounded text-white"
          :class="y === selectedYear ? 'bg-blue-600' : 'bg-gray-300 text-black'"
          @click="selectYear(y)"
        >
          {{ y }}
        </button>
      </div>
    </div>
  </div>
</template>
