<script setup>
import TempLayout from "@/Layouts/TempLayout.vue";
import Chart from "@/Components/Chart.vue";
import { onMounted, ref, reactive } from "vue";
import { router } from "@inertiajs/vue3";
import axios from "axios";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";

const props = defineProps({
  data_lists: Array,
});
const form = reactive({
  start_date: new Date().toISOString().slice(0, 10),
  finish_date: new Date().toISOString().slice(0, 10),
});

const data_lists = ref([]);
const changeProcessName = (process_id) => {
  let process_name = "";

  switch (process_id) {
    case 1:
      process_name = "電気炉";
      break;
    case 2:
      process_name = "生型造型";
      break;
    case 3:
      process_name = "フラン";
      break;
    case 4:
      process_name = "中子";
      break;
    case 5:
      process_name = "仕上げ";
      break;
    case 6:
      process_name = "出荷検査";
      break;
    case 7:
      process_name = "外気";
      break;
  }

  return process_name;
};
const reverseData = (d_lists) => {
  d_lists.forEach((data) => {
    data.temperatures.reverse();
    data.humidities.reverse();
    data.labels.reverse();
  });

  return d_lists;
};
const sortByDate = () => {
  if (!(form.start_date && form.finish_date)) {
    alert("絞り込み開始日と終了日を入力してください。");
  }

  updateData();
};

const updateData = () => {
  console.log('実行');

  axios
    .get("/getData", {
      params: {
        start_date: form.start_date,
        finish_date: form.finish_date,
      },
    })
    .then((response) => {
      data_lists.value = reverseData(response.data);
      console.log(response);
    })
    .catch((error) => {
      console.error("データの取得に失敗しました:", error);
    });
};
onMounted(() => {
  // data_lists.value = reverseData(props.data_lists);
  updateData();

  setInterval(() => {
    updateData();
  }, 3600000); // 一時間に一回実行
});
</script>
<template>
  <TempLayout :title="'現場温湿度チェック'">
    <template #content>
      <div class="bg-gray-50 py-16 px-16 flex justify-around items-center">
        <form @submit.prevent class="w-full max-w-lg">
          <div class="flex -mx-3 mb-6">
            <div class="w-1/2 px-3 mb-6 md:mb-0">
              <label
                class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                for="start_date"
              >
                開始
              </label>
              <input
                class="appearance-none block w-full bg-gray-200 text-gray-700 border border-transparent rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                id="start_date"
                type="date"
                name="start_date"
                v-model="form.start_date"
              />
            </div>
            <div class="w-1/2 px-3">
              <label
                class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                for="finish_date"
              >
                終了
              </label>
              <input
                class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                id="finish_date"
                type="date"
                name="finish_date"
                v-model="form.finish_date"
              />
            </div>
          </div>
          <div class="mt-8 flex justify-center">
            <button
              @click="sortByDate"
              class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded whitespace-nowrap"
            >
              絞り込み
            </button>
          </div>
        </form>
        
      </div>


      <div>
        <section v-for="data in data_lists" :key="data.id">
          <h1 class="text-gray-500 text-4xl font-bold pt-16 pb-4 text-center">
            {{ changeProcessName(data.process_id) }}
          </h1>
          <div
            v-if="data.temperatures.length > 0"
            class="flex justify-around items-center my-8"
          >
            <Chart
              class="w-1/4"
              :labels="data_lists[data.process_id - 1].labels"
              :data="data_lists[data.process_id - 1].wbgt"
              :label="'暑さ指数'"
              :color="'rgb(255, 99, 132)'"
            />

            <Chart
              class="w-1/4"
              :labels="data_lists[data.process_id - 1].labels"
              :data="data_lists[data.process_id - 1].temperatures"
              :label="'気温(℃)'"
              :color="'rgb(54, 162, 235)'"
            />
            <Chart
              class="w-1/4"
              :labels="data_lists[data.process_id - 1].labels"
              :data="data_lists[data.process_id - 1].humidities"
              :label="'湿度(%)'"
              :color="'rgb(75, 192, 192)'"
            />
          </div>
          <div v-else class="text-center">
            <p class="py-4 text-gray-400">データがありません。</p>
          </div>
        </section>
      </div>
    </template>
  </TempLayout>
</template>