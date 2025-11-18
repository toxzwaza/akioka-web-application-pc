<script setup>
import { Line } from "vue-chartjs";
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
} from "chart.js";
import { reactive, watch } from "vue";

const props = defineProps({
  labels: Array,
  data: Array,
  label: String,
  color: String,
});

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend
);

const lineData = reactive({
  labels: [],
  datasets: [
    {
      label: "",
      data: [],
      backgroundColor: "",
      borderColor: "",
      tension: 0.1,
    },
  ],
});

const chartOptions = {
  responsive: true,
  maintainAspectRatio: true,
};

watch(
  () => [props.labels, props.data, props.label, props.color],
  ([newLabels, newData, newLabel, newColor]) => {
    lineData.labels = newLabels;
    lineData.datasets[0].data = newData;
    lineData.datasets[0].label = newLabel;
    lineData.datasets[0].backgroundColor = newColor;
    lineData.datasets[0].borderColor = newColor;
  },
  { immediate: true }
);
</script>
<template>
  <div>
    <Line :data="lineData" :options="chartOptions" />
  </div>
</template>