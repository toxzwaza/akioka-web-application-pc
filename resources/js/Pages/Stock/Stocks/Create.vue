<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { onMounted, reactive, ref } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";
import MainTitle from "@/Components/Title/MainTitle.vue";

const props = defineProps({
  classifications: Array,
  stock_processes: Array,
  order_request: Object,
});

const form = reactive({
  order_request_id: null,
  name: null,
  s_name: null,
  jan_code: null,
  img_path: null,
  url: null,
  purchase_identification_number: null,
  price: null,
  solo_unit: null,
  org_unit: null,
  quantity_per_org: null,
  classification_id: null,
  deli_location: null,
  stock_process_id: 0,
  del_flg: 0
});

const handleClassification = () => {
  if (form.classification_id == 11) {
    form.stock_process_id = 29;
  }
};
const createStock = () => {
  if (
    !form.name ||
    !form.price ||
    !form.classification_id ||
    !form.stock_process_id
  ) {
    return alert("必須項目が入力されていません。");
  }

  // 在庫追加
  axios
    .post(route("stock.store.stocks"), form)
    .then((res) => {
      console.log(res.data);
      if (res.data.status) {
        if (form.order_request_id) {
          alert('登録が完了しました。発注依頼一覧へ遷移します。')
          window.location.href = route('stock.order_requests')
        } else if (confirm("登録が完了しました。続けて在庫を追加しますか？")) {
          window.location.reload();
        } else {
          window.location.href = route("stock");
        }
      }
    })
    .catch((error) => {
      console.log(error);
    });
};

onMounted(() => {
  console.log(props.order_request);
  if (props.order_request) {
    const order_request = props.order_request;
    form.order_request_id = order_request.id;
    form.name = order_request.name;
    form.s_name = order_request.s_name;
    form.solo_unit = order_request.unit;
  }
});
</script>
<template>
  <MainLayout :title="'在庫追加'">
    <template #content>
      <MainTitle
        :top="'在庫追加'"
        :sub="'在庫を登録を行います。必須項目を入力して、新規登録ボタンを押してください。作成した物品データは在庫一覧より確認できます。'"
      />
      <div class="flex justify-between py-12">
        <div id="right_container" class="w-full">
          <form class="w-1/2 mx-auto">
            <div class="flex flex-wrap -mx-3 mb-6">
              <div class="w-full px-3">
                <label
                  :class="{
                    'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2': true,
                    'text-red-500': !form.name,
                  }"
                  for="name"
                >
                  *品名
                </label>
                <input
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  id="name"
                  type="text"
                  placeholder=""
                  v-model="form.name"
                />
              </div>
            </div>
            <div class="flex flex-wrap -mx-3 mb-6">
              <div class="w-full px-3">
                <label
                  class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                  for="s_name"
                >
                  品番
                </label>
                <input
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  id="s_name"
                  type="text"
                  placeholder=""
                  v-model="form.s_name"
                />
              </div>
            </div>
            <div class="flex flex-wrap -mx-3 mb-6">
              <div class="w-full px-3">
                <label
                  class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                  for="jan_code"
                >
                  JANコード
                </label>
                <input
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  id="jan_code"
                  type="text"
                  placeholder=""
                  v-model="form.jan_code"
                />
              </div>
            </div>
            <div class="flex flex-wrap -mx-3 mb-6">
              <div class="w-full md:w-1/2 px-3">
                <label
                  class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                  for="grid-last-name"
                >
                  画像URL
                  <span class="ml-2 text-red-500 text-xs"
                    >※インターネットの画像を使用する場合コチラから設定</span
                  >
                </label>
                <input
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  id="grid-last-name"
                  type="text"
                  placeholder="https://****"
                  v-model="form.img_path"
                />
              </div>
              <div class="w-full md:w-1/2 px-3">
                <label
                  class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                  for="grid-last-name"
                >
                  購買用URL
                </label>
                <input
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  id="grid-last-name"
                  type="text"
                  placeholder="https://****"
                  v-model="form.url"
                />
              </div>
            </div>
            <div class="flex flex-wrap -mx-3 mb-6">
              <div class="w-full px-3">
                <label
                  class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                  for="grid-password"
                >
                  適確事業者番号
                </label>
                <input
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  id="grid-password"
                  type="text"
                  placeholder=""
                  v-model="form.purchase_identification_number"
                />
              </div>
            </div>
            <div class="flex flex-wrap -mx-3 mb-6">
              <div class="w-full px-3">
                <label
                  :class="{
                    'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2': true,
                    'text-red-500': !form.price,
                  }"
                  for="grid-password"
                >
                  *価格
                </label>
                <input
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  id="grid-password"
                  type="number"
                  placeholder=""
                  v-model="form.price"
                />
              </div>
            </div>

            <div class="flex flex-wrap -mx-3 mb-6">
              <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                <label
                  class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                  for="grid-city"
                >
                  単位1
                </label>
                <input
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  id="grid-city"
                  type="text"
                  placeholder="個"
                  v-model="form.solo_unit"
                />
              </div>
              <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                <label
                  class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                  for="grid-city"
                >
                  単位2
                </label>
                <input
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  id="grid-city"
                  type="text"
                  placeholder="箱"
                  v-model="form.org_unit"
                />
              </div>
              <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                <label
                  class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                  for="grid-city"
                >
                  換算値<span class="ml-2 text-gray-500 text-xs"
                    >※納品時の数量登録</span
                  >
                </label>
                <input
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  id="grid-city"
                  type="number"
                  placeholder=""
                  v-model="form.quantity_per_org"
                />
              </div>
            </div>
            <div class="flex flex-wrap -mx-3 mb-6">
              <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                <label
                  :class="{
                    'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2': true,
                    'text-red-500': !form.classification_id,
                  }"
                  for="grid-city"
                >
                  *備品カテゴリ
                </label>
                <select
                  name=""
                  id=""
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  @change="handleClassification"
                  v-model="form.classification_id"
                >
                  <option value="0">未選択</option>
                  <option
                    v-for="classification in classifications"
                    :key="classification.id"
                    :value="classification.id"
                  >
                    {{ classification.name }}
                  </option>
                </select>
              </div>
              <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                <label
                  class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                  for="grid-city"
                >
                  配送先
                </label>
                <input
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  id="grid-city"
                  type="text"
                  placeholder=""
                  v-model="form.deli_location"
                />
              </div>
              <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                <label
                  :class="{
                    'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2': true,
                    'text-red-500': !form.stock_process_id,
                  }"
                  for="grid-city"
                >
                  工程 (※発注依頼時工程選択のデフォルト値)
                </label>
                <select
                  name=""
                  id=""
                  :class="{
                    'appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500': true,
                  }"
                  v-model="form.stock_process_id"
                >
                  <option value="0">未選択</option>
                  <option
                    v-for="stock_process in props.stock_processes"
                    :key="stock_process.id"
                    :value="stock_process.id"
                  >
                    {{ stock_process.name }}
                  </option>
                </select>
              </div>
            </div>

            <div class="flex items-center justify-between sm:col-span-2 mt-16">
              <button
                @click.prevent="createStock"
                class="inline-block rounded-lg bg-indigo-500 px-8 py-3 text-center text-sm font-semibold text-white outline-none ring-indigo-300 transition duration-100 hover:bg-indigo-600 focus-visible:ring active:bg-indigo-700 md:text-base"
              >
                新規登録
              </button>

              <span class="text-sm text-red-500">*Required</span>
            </div>
          </form>
        </div>
      </div>
    </template>
  </MainLayout>
</template>
<style scoped lang="scss">
#left_container {
  & .img_container {
    height: 20vh;
    width: 20vw;
    & img {
      height: 100%;
      width: 100%;
      object-fit: contain;
    }
  }
}
#right_container {
}
</style>