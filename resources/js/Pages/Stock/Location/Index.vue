<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { onMounted, reactive, ref } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";
import MainTitle from "@/Components/Title/MainTitle.vue";

const props = defineProps({
  locations: Array,
  processes: Array,
});
const form = reactive({
  location_name: "",
  processes: [],
});

const sendLocation = () => {
  form.processes = props.processes
    .filter((process) => process.select_flg)
    .map((process) => process.id);

  if (!form.location_name || !form.processes.length > 0) {
    alert(
      "格納先名が入力されていないか、管理部署が選択されていない可能性があります。"
    );
  } else {
    console.log(form);
    axios
      .post(route("stock.locations.store"), form)
      .then((res) => {
        console.log(res.data);
      })
      .catch((error) => {
        console.log(error);
      });
  }
};

onMounted(() => {
  console.log(props.locations, props.processes);
});
</script>
<template>
  <MainLayout :title="'格納先追加'">
    <template #content>
      <MainTitle
        :top="'格納先追加'"
        :sub="'格納先・格納先アドレスの確認と編集を行います。'"
      />

      <section class="text-gray-600 body-font">
        <form class="w-full max-w-lg">
          <div class="flex flex-wrap -mx-3 mb-6">
            <div class="w-full px-3">
              <label
                class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                for="grid-password"
              >
                格納先名
              </label>
              <input
                class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                id="grid-password"
                type="text"
                placeholder="格納先名を入力してください"
                v-model="form.location_name"
              />
            </div>
          </div>
          <div class="flex flex-wrap -mx-3 mb-6">
            <div class="w-full px-3">
              <label
                class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                for="grid-password"
              >
                管理部署選択
              </label>
              <span
                v-for="process in props.processes"
                :key="process.id"
                @click="process.select_flg = !process.select_flg"
                :class="{
                  'inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1 mb-1': true,
                  'opacity-100': process.select_flg,
                  'opacity-60': !process.select_flg,
                }"
              >
                {{ process.name }}
              </span>
            </div>
          </div>

          <button
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
            @click.prevent="sendLocation"
          >
            登録
          </button>
        </form>

        <div class="container px-5 py-24 mx-auto">
          <Link
            class="underline text-blue-500 inline-block mb-4"
            :href="route('stock.storage_addresses.print')"
            >アドレス用紙印刷はこちら</Link
          >

          <div class="w-full mx-auto overflow-auto">
            <table class="table-auto w-full text-left whitespace-no-wrap">
              <thead>
                <tr>
                  <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap"
                  >
                    格納先ID
                  </th>
                  <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap"
                  >
                    格納先名
                  </th>
                  <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap"
                  >
                    部署
                  </th>
                  <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap"
                  >
                    最終更新日
                  </th>
                  <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap"
                  >
                    アドレス登録
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="location in locations" :key="location.id">
                  <td class="px-4 py-3">{{ location.id }}</td>
                  <td class="px-4 py-3">{{ location.name }}</td>
                  <td class="px-4 py-3 text-lg text-gray-900">
                    <span
                      v-for="process in location.processes"
                      :key="process.id"
                      class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1 mb-1"
                    >
                      {{ process.name }}
                    </span>
                  </td>
                  <td class="px-4 py-3 text-lg text-gray-900">
                    {{
                      new Date(location.updated_at).toLocaleDateString(
                        "ja-JP",
                        { year: "numeric", month: "2-digit", day: "2-digit" }
                      )
                    }}
                  </td>
                  <td class="px-4 py-3 text-lg text-gray-900">
                    <Link
                      :href="
                        route('stock.locations.show', {
                          location_id: location.id,
                        })
                      "
                      class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm"
                    >
                      詳細
                    </Link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </template>
  </MainLayout>
</template>
<style scoped lang="scss">
</style>