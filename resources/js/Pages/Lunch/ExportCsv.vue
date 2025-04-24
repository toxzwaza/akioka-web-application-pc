<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { onMounted, ref, reactive } from "vue";
import { router } from "@inertiajs/vue3";

import axios from "axios";
import MainTitle from "@/Components/Title/MainTitle.vue";

const form = reactive({
  start_date: new Date(new Date().getFullYear(), new Date().getMonth() - 1, 22)
    .toISOString()
    .split("T")[0],
  finish_date: new Date(new Date().getFullYear(), new Date().getMonth(), 21)
    .toISOString()
    .split("T")[0],
});

const exportData = () => {
  if (!form.start_date || !form.finish_date) {
    alert("開始日と終了日を入力してください。");
    return;
  }

  const startDate = new Date(form.start_date);
  const finishDate = new Date(form.finish_date);

  if (startDate >= finishDate) {
    alert("開始日は終了日より前の日付を選択してください。");
    return;
  }

  axios
    .get(route("lunch.export"), {
      params: {
        start_date: form.start_date,
        finish_date: form.finish_date,
      },
      responseType: "blob",
    })
    .then((response) => {
      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement("a");
      link.href = url;
      link.setAttribute(
        "download",
        `弁当注文集計_${form.start_date}_${form.finish_date}.csv`
      );
      document.body.appendChild(link);
      link.click();
      link.remove();
    })
    .catch((error) => {
      console.error("ダウンロードに失敗しました:", error);
      alert("ダウンロードに失敗しました。");
    });
};
</script>
<template>
  <MainLayout>
    <template #content>
      <MainTitle
        :top="'注文状況書き出し'"
        :sub="'期間を選択して、弁当注文データをエクスポートできます。'"
      />
      <form class="w-2/3 mx-auto">
        <p class="text-gray-600 mb-4 text-sm">
          先月20日～今月21日をデフォルト値に設定しています。適宜変更して、エクスポートしてください。
        </p>
        <div class="flex flex-wrap items-center justify-between -mx-3 mb-6">
          <div class="w-2/5 px-3 mb-6 md:mb-0">
            <label
              class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
              for="grid-first-name"
            >
              開始
            </label>
            <input
              class="appearance-none block w-full bg-gray-200 text-gray-700 border border-transparent rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
              id="grid-first-name"
              type="date"
              v-model="form.start_date"
            />
          </div>
          <p>～</p>
          <div class="w-2/5 px-3">
            <label
              class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
              for="grid-last-name"
            >
              終了
            </label>
            <input
              class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
              id="grid-last-name"
              type="date"
              v-model="form.finish_date"
            />
          </div>
        </div>

        <div class="flex justify-center mt-8">
          <button
            @click.prevent="exportData"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
          >
            エクスポート
          </button>
        </div>
      </form>
    </template>
  </MainLayout>
</template>
<style scoped lang="scss">
</style>