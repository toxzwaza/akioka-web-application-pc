<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { onMounted, reactive, ref } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";

import Calender from "@/Components/Calender.vue";
import PiChart from "@/Components/PiChart.vue";
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

onMounted(() => {});
</script>
<template>
  <MainLayout :title="'在庫管理'">
    <template #content>
      <h1 class="text-center text-xl font-bold text-gray-800 mb-12">
        在庫管理
      </h1>
      <div class="flex items-center justify-around">
        <div class="w-1/2">
          <Calender />
        </div>
        <div class="w-1/3">
          <PiChart :title="'サンプル円グラフ'" :pie_data="pie_data"></PiChart>
        </div>
      </div>
    </template>
  </MainLayout>
</template>