<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { ref, reactive, onMounted, computed } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";
import Title from "@/Components/Title/MainTitle.vue";
import { Pie, Bar } from "vue-chartjs";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({});

const form = reactive({
  level: null,
  device_name: null,
  service_name: null,
  message: null,
  start_date: null,
  end_date: null,
});

const logs = ref([]);
const device_names = ref([]);
const service_names = ref([]);

const sortConfig = reactive({
  key: null,
  direction: 'asc'
});

const sortedLogs = computed(() => {
  if (!sortConfig.key) return logs.value;
  
  return [...logs.value].sort((a, b) => {
    let aValue = a[sortConfig.key];
    let bValue = b[sortConfig.key];
    
    if (sortConfig.key === 'created_at') {
      aValue = new Date(aValue).getTime();
      bValue = new Date(bValue).getTime();
    }
    
    if (aValue < bValue) return sortConfig.direction === 'asc' ? -1 : 1;
    if (aValue > bValue) return sortConfig.direction === 'asc' ? 1 : -1;
    return 0;
  });
});

const sortBy = (key) => {
  if (sortConfig.key === key) {
    sortConfig.direction = sortConfig.direction === 'asc' ? 'desc' : 'asc';
  } else {
    sortConfig.key = key;
    sortConfig.direction = 'asc';
  }
};

const getLogs = () => {
  axios
    .get(route("log.getLogs"), {
      params: {
        level: form.level,
        device_name: form.device_name,
        service_name: form.service_name,
        message: form.message,
        start_date: form.start_date,
        end_date: form.end_date,
      },
    })
    .then((res) => {
      console.log(res.data);
      logs.value = res.data.logs;
      device_names.value = res.data.device_names;
      service_names.value = res.data.service_names;
    })
    .catch((error) => {
      console.log(error);
    });
};

onMounted(() => {
  getLogs();
  console.log(logs.value);
});
</script>
<template>
  <MainLayout :p_none="true">
    <template #content>
      <section class="py-16 px-24">
        <Title :top="'ログ一覧'" :sub="'ログを確認できます。'" />

        <section class="text-gray-600 body-font">
          <div id="search_box" class="flex items-center justify-start mb-8">
            <div class="w-64 mr-2">
              <label
                class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                for="grid-last-name"
              >
                レベル
              </label>
              <select
                name=""
                id=""
                class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                v-model="form.level"
                @change="getLogs"
              >
                <option value=""></option>
                <option value="0">Info</option>
                <option value="1">Warn</option>
                <option value="2">Error</option>
              </select>
            </div>
            <div class="w-64 mr-2">
              <label
                class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                for="grid-last-name"
              >
                デバイス名
              </label>
              <select
                name=""
                id=""
                class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                v-model="form.device_name"
                @change="getLogs"
              >
                <option value=""></option>
                <option
                  v-for="device_name in device_names"
                  :key="device_name"
                  :value="device_name"
                >
                  {{ device_name }}
                </option>
              </select>
            </div>
            <div class="w-64 mr-2">
              <label
                class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                for="grid-last-name"
              >
                サービス名
              </label>
              <select
                name=""
                id=""
                class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                v-model="form.service_name"
                @change="getLogs"
              >
                <option value=""></option>
                <option
                  v-for="service_name in service_names"
                  :key="service_name"
                  :value="service_name"
                >
                  {{ service_name }}
                </option>
              </select>
            </div>
            <div class="w-64 mr-2">
              <label
                class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                for="grid-last-name"
              >
                メッセージ
              </label>
              <input
                class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                id="grid-last-name"
                type="text"
                v-model="form.message"
                @change="getLogs"
              />
            </div>

            <div class="w-1/5mr-2">
              <label
                class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                for="grid-last-name"
              >
                日時
              </label>
              <div class="flex items-center ">
                <input
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500 mr-2"
                  id="grid-last-name"
                  type="date"
                  v-model="form.start_date"
                  @change="getLogs"
                />
                ～
                <input
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500 ml-2"
                  id="grid-last-name"
                  type="date"
                  v-model="form.end_date"
                  @change="getLogs"
                />
              </div>
            </div>
          </div>
          <div class="mx-auto">
            <div class="w-full mx-auto overflow-auto">
              <table class="table-auto w-full text-left whitespace-no-wrap">
                <thead>
                  <tr>
                    <th
                      class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap cursor-pointer hover:bg-gray-200"
                      @click="sortBy('created_at')"
                    >
                      日時
                      <span v-if="sortConfig.key === 'created_at'" class="ml-1">
                        {{ sortConfig.direction === 'asc' ? '↑' : '↓' }}
                      </span>
                    </th>
                    <th
                      class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap cursor-pointer hover:bg-gray-200"
                      @click="sortBy('level')"
                    >
                      レベル
                      <span v-if="sortConfig.key === 'level'" class="ml-1">
                        {{ sortConfig.direction === 'asc' ? '↑' : '↓' }}
                      </span>
                    </th>
                    <th
                      class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap cursor-pointer hover:bg-gray-200"
                      @click="sortBy('device_name')"
                    >
                      デバイス名
                      <span v-if="sortConfig.key === 'device_name'" class="ml-1">
                        {{ sortConfig.direction === 'asc' ? '↑' : '↓' }}
                      </span>
                    </th>
                    <th
                      class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap cursor-pointer hover:bg-gray-200"
                      @click="sortBy('service_name')"
                    >
                      サービス名
                      <span v-if="sortConfig.key === 'service_name'" class="ml-1">
                        {{ sortConfig.direction === 'asc' ? '↑' : '↓' }}
                      </span>
                    </th>
                    <th
                      class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap cursor-pointer hover:bg-gray-200"
                      @click="sortBy('message')"
                    >
                      メッセージ
                      <span v-if="sortConfig.key === 'message'" class="ml-1">
                        {{ sortConfig.direction === 'asc' ? '↑' : '↓' }}
                      </span>
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(log, index) in sortedLogs"
                    :key="log.id"
                    :class="{
                      'bg-white': index % 2 === 0,
                      'bg-gray-100': index % 2 !== 0,
                    }"
                  >
                    <td class="whitespace-nowrap px-4 py-3">
                      {{
                        new Date(log.created_at)
                          .toLocaleString("ja-JP", {
                            year: "numeric",
                            month: "2-digit",
                            day: "2-digit",
                            hour: "2-digit",
                            minute: "2-digit",
                            second: "2-digit",
                          })
                          .replace(/\//g, "-")
                      }}
                    </td>
                    <td
                      class="whitespace-nowrap px-4 py-3 text-lg text-gray-900"
                    >
                      <span
                        v-if="log.level === 0"
                        class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-gray-500/10 ring-inset"
                        >Info</span
                      >
                      <span
                        v-else-if="log.level === 1"
                        class="inline-flex items-center rounded-md bg-orange-50 px-2 py-1 text-xs font-medium text-orange-800 ring-1 ring-orange-600/20 ring-inset"
                        >warn</span
                      >
                      <span
                        v-else-if="log.level === 2"
                        class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-red-600/10 ring-inset"
                        >Error</span
                      >
                    </td>
                    <td class="whitespace-nowrap px-4 py-3">
                      {{ log.device_name }}
                    </td>
                    <td class="whitespace-nowrap px-4 py-3">
                      {{ log.service_name }}
                    </td>

                    <td
                      class="whitespace-nowrap px-4 py-3 text-lg text-gray-900"
                    >
                      {{ log.message }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </section>
      </section>
    </template>
  </MainLayout>
</template>
<style scoped lang="scss">
</style>