<script setup>
import { Chart, registerables } from "chart.js";
import { ref, onMounted } from "vue";

Chart.register(...registerables);

const chartRef = ref(null);

const props = defineProps({
  title: null,
  pie_data: null
})

onMounted(() => {
  const ctx = chartRef.value.getContext("2d");
  new Chart(ctx, {
    type: "pie",
    data: props.pie_data,
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: "top",
        },
        title: {
          display: true,
          text: props.title,
        },
      },
    },
  });
});
</script>

<template>
  <div>
    <canvas ref="chartRef"></canvas>
  </div>
</template>

