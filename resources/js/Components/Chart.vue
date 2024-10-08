<script setup>
import { Chart, registerables } from "chart.js";
import { LineChart } from "vue-chart-3"; // BarChartからLineChartに変更
import { reactive, onMounted, watch } from "vue";
const props = defineProps({
  labels: Array,
  data: Array,
  label: String,
  color: String
});

Chart.register(...registerables);
const lineData = reactive({
  // lineDataに変更
  labels: [],
  datasets: [
    {
      label: "",
      data: [],
      backgroundColor: "",
      tension: 0.1,
    },
  ],
});

watch(
  () => [props.labels, props.data],
  ([newLabels, newData]) => {
    lineData.labels = newLabels;
    lineData.datasets[0].data = newData;
  },
  { immediate: true }
);

onMounted(() => {
  lineData.datasets[0].label = props.label;
  lineData.datasets[0].backgroundColor = props.color;
  
})
</script>
<template>
  <div>
    <LineChart :chartData="lineData" />
    <!-- BarChartからLineChartに変更 -->
  </div>
</template>