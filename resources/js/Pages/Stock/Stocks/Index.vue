<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { onMounted, reactive, ref } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";
import MainTitle from "@/Components/Title/MainTitle.vue";

const props = defineProps({
  stocks: Object,
  suppliers: Array,
  supplier_name: String,
  keyword: String,
});

const form = reactive({
  keyword: null,
  supplier_name: null,
});

const base_stocks = ref([]);
const filter_stocks = ref([]);

const getStocks = () => {
  console.log(form);

  router.get(route("stock.stocks"), form);
};
const redirectStock = (stock_id) => {
  window.location.href = route("stock.show.stocks", { stock_id: stock_id });
};

onMounted(() => {
  console.log(props.stocks);

  base_stocks.value = props.stocks.data;
  filter_stocks.value = props.stocks.data;

  form.keyword = props.keyword;
  form.supplier_name = props.supplier_name;
});
</script>
<template>
  <MainLayout :title="'在庫一覧'">
    <template #content>
      <MainTitle
        :top="'在庫一覧'"
        :sub="'登録済みの在庫データの確認を行います。'"
      />
      <section class="text-gray-600 body-font">
        <div class="py-12 mx-auto">
          <div class="flex flex-col text-center w-full mb-20"></div>
          <div
            id="sort_container"
            class="my-8 flex items-start justify-between"
          >
            <div class="w-1/4">
              <p class="mb-2 font-bold">並び替え</p>
              <div class="button_container flex items-center justify-start">
                <button
                  :class="{
                    'mr-4 text-sm bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded': true,
                  }"
                >
                  リセット
                </button>

                <button
                  :class="{
                    'mr-2 text-sm bg-blue-500  text-white font-bold py-2 px-4 rounded': true,
                    'opacity-60': sort === 'new_order',
                  }"
                >
                  新しい順
                </button>
                <button
                  :class="{
                    'mr-2 text-sm bg-blue-500  text-white font-bold py-2 px-4 rounded': true,
                    'opacity-60': sort === 'old_order',
                  }"
                >
                  古い順
                </button>
              </div>
            </div>
            <div class="mr-8">
              <p class="mb-2 font-bold">検索</p>
              <div class="button_container flex items-bottom justify-start">
                <div class="w-62 mr-2">
                  <label
                    class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                    for="grid-last-name"
                  >
                    品名・品番から検索
                  </label>
                  <input
                    class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    type="text"
                    name=""
                    id=""
                    v-model="form.keyword"
                  />
                </div>

                <div class="button_container flex items-center justify-start">
                  <div class="w-32 mr-2">
                    <label
                      class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                      for="grid-last-name"
                    >
                      手配先
                    </label>
                    <select
                      class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      name="order_user"
                      id=""
                      v-model="form.supplier_name"
                    >
                      <option value="0">未選択</option>
                      <option
                        v-for="supplier in props.suppliers"
                        :key="supplier.id"
                        :value="supplier.name"
                      >
                        {{ supplier.name }}
                      </option>
                    </select>
                  </div>
                </div>
                <button
                  @click="getStocks"
                  class="ml-4 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
                >
                  検索
                </button>
              </div>
            </div>
          </div>

          <hr class="my-8" />
          <div class="mb-8 flex justify-end">
            <Pagination :links="props.stocks.links" />
          </div>

          <div class="w-full mx-auto overflow-auto">
            <table class="table-auto w-full text-left whitespace-no-wrap">
              <thead>
                <tr>
                  <th
                    class="whitespace-nowrap text-sm w-8 px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100 rounded-tl rounded-bl"
                  >
                    画像
                  </th>

                  <th
                    class="text-sm w-8 px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100 rounded-tl rounded-bl"
                  >
                    ID
                  </th>
                  <th
                    class="text-sm px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100"
                  >
                    JANコード
                  </th>
                  <th
                    class="text-sm px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100"
                  >
                    品名
                  </th>
                  <th
                    class="text-sm px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100"
                  >
                    品番
                  </th>

                  <th
                    class="text-sm px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100"
                  >
                    納品先
                  </th>
                  <!-- <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100">備品カテゴリ</th> -->
                  <th
                    class="whitespace-nowrap w-8 px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100"
                  >
                    価格
                  </th>
                  <th
                    class="whitespace-nowrap w-8 px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100"
                  >
                    カテゴリー
                  </th>
                  <th
                    class="whitespace-nowrap w-8 px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100"
                  >
                    手配先No
                  </th>
                  <th
                    class="whitespace-nowrap w-8 px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100"
                  >
                    手配先
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="stock in filter_stocks"
                  :key="stock.id"
                  :class="{
                    'hover:bg-indigo-50 transition-all duration-100': true,
                    'bg-gray-200': stock.del_flg,
                  }"
                  @click="redirectStock(stock.id)"
                >
                  <td class="w-24 px-4 py-6">
                    <img
                      :src="
                        stock.img_path && stock.img_path.includes('https://')
                          ? stock.img_path
                          : 'https://akioka.cloud/' + stock.img_path
                      "
                      alt=""
                    />
                  </td>
                  <td class="px-4 py-3 w-32">{{ stock.id }}</td>
                  <td class="px-4 py-3 text-gray-900 w-32">
                    {{ stock.jan_code }}
                  </td>
                  <td class="px-4 py-3 text-gray-900">
                    {{ stock.name }}
                  </td>
                  <td class="px-4 py-3 text-gray-900 w-32">
                    {{ stock.s_name }}
                  </td>
                  <td class="px-4 py-3 text-gray-900 w-32">
                    {{ stock.deli_location }}
                  </td>
                  <td class="px-4 py-3 text-gray-900 w-32">
                    {{ stock.price ? stock.price.toLocaleString() : "-" }}
                    <span class="text-xs">円</span>
                  </td>
                  <td class="px-4 py-3 text-gray-900 w-32">
                    {{ stock.classification_name }}
                  </td>
                  <td class="px-4 py-3 text-gray-900 w-32">
                    {{ stock.supplier_no }}
                  </td>
                  <td class="px-4 py-3 text-gray-900 w-32">
                    {{ stock.supplier_name }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <hr class="my-8" />
          <div class="mb-8 flex justify-end">
            <Pagination :links="props.stocks.links" />
          </div>
        </div>
      </section>
    </template>
  </MainLayout>
</template>
<style scoped lang="scss">
</style>