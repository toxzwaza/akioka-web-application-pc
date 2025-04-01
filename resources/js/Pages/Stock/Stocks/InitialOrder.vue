<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { onMounted, reactive, ref } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";

const props = defineProps({
  classifications: Array,
  users: Array,
  suppliers: Array,
});

const form = reactive({
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

  order_user: null,
  user_id: null,
  supplier_id: null,
  lead_time: null,
  quantity: null,
  calc_price: null,
});

const createStockAndInitialOrder = () => {
  if (
    !form.name ||
    !form.price ||
    !form.classification_id ||
    !form.deli_location ||
    !form.solo_unit ||
    !form.order_user ||
    !form.user_id ||
    !form.lead_time ||
    !form.quantity ||
    !form.calc_price
  ) {
    return alert("必須項目が入力されていません。");
  }

  // 在庫追加と発注登録
  axios
    .post(route('stock.store.initialOrders'), form)
    .then((res) => {
      console.log(res.data);
      if (res.data.status) {
        if (confirm("発注登録が完了しました。続けて発注登録を行いますか？")) {
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

onMounted(() => {});
</script>
<template>
  <MainLayout :title="'新規品発注登録'">
    <template #content>
      <h1 class="text-center text-xl font-bold text-gray-800">
        新規品発注登録
      </h1>
      <div class="flex justify-between py-12">
        <div id="right_container" class="w-full">
          <form class="w-1/2 mx-auto">
            <h2 class="mb-2">在庫登録</h2>
            <div class="flex flex-wrap -mx-3 mb-6">
              <div class="w-1/2 px-3">
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

              <div class="w-1/2 px-3">
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
              <div class="w-1/2 px-3">
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

              <div class="w-1/2 px-3 mb-6 md:mb-0">
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
            </div>

            <div class="flex flex-wrap -mx-3 mb-6">
              <div class="w-1/2 px-3 mb-6 md:mb-0">
                <label
                  :class="{
                    'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2': true,
                    'text-red-500': !form.deli_location,
                  }"
                  for="grid-city"
                >
                  *配送先
                </label>
                <input
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  id="grid-city"
                  type="text"
                  placeholder=""
                  v-model="form.deli_location"
                />
              </div>
            </div>

            <div class="flex flex-wrap -mx-3 mb-6">
              <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                <label
                  :class="{
                    'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2': true,
                    'text-red-500': !form.solo_unit,
                  }"
                  for="grid-city"
                >
                  *単位1
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

            <!-- 詳細を登録する場合 -->
            <details class="mt-12">
              <summary class="text-gray-700 bg-gray-200 font-bold py-2 pl-4">
                詳細登録(任意)
              </summary>
              <div class="bg-gray-100 p-4">
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
              </div>
            </details>

            <hr class="my-8" />

            <h2 class="mt-8 mb-2">発注登録</h2>
            <div class="flex flex-wrap -mx-3 mb-6">
              <div class="w-1/2 px-3">
                <label
                  :class="{
                    'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2': true,
                    'text-red-500': !form.order_user,
                  }"
                  for="name"
                >
                  *注文依頼者
                </label>
                <input
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  type="text"
                  list="users"
                  v-model="form.order_user"
                />
                <datalist id="users">
                  <option value="未選択"></option>
                  <option
                    v-for="user in props.users"
                    :key="user.id"
                    :value="user.id"
                  >
                    {{ user.name }}
                  </option>
                </datalist>
              </div>

              <div class="w-1/2 px-3">
                <label
                  :class="{
                    'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2': true,
                    'text-red-500': !form.user_id,
                  }"
                >
                  *発注者
                </label>
                <input
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  type="text"
                  list="users"
                  v-model="form.user_id"
                />
              </div>
            </div>
            <div class="flex flex-wrap -mx-3 mb-6">
              <div class="w-1/2 px-3">
                <label
                  :class="{
                    'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2': true,
                    'text-red-500': !form.supplier_id,
                  }"
                  for="name"
                >
                  *手配先
                </label>
                <input
                  :class="{
                    'appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500': true,
                  }"
                  id="name"
                  type="text"
                  placeholder=""
                  list="suppliers"
                  v-model="form.supplier_id"
                />
                <datalist id="suppliers">
                  <option value="未選択"></option>
                  <option
                    v-for="supplier in props.suppliers"
                    :key="supplier.id"
                    :value="supplier.id"
                  >
                    {{
                      supplier.supplier_no != ''
                        ? `${supplier.supplier_no} : ${supplier.name}`
                        : supplier.name
                    }}
                  </option>
                </datalist>
              </div>
              <div class="w-1/2 px-3">
                <label
                  :class="{
                    'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2': true,
                    'text-red-500': !form.lead_time,
                  }"
                  for="name"
                >
                  *リードタイム
                </label>
                <input
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  id="name"
                  type="number"
                  placeholder=""
                  v-model="form.lead_time"
                />
              </div>
            </div>
            <div class="flex flex-wrap -mx-3 mb-6">
              <div class="w-1/2 px-3">
                <label
                  :class="{
                    'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2': true,
                    'text-red-500': !form.quantity,
                  }"
                  for="name"
                >
                  *数量
                </label>
                <input
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  id="name"
                  type="text"
                  placeholder=""
                  v-model="form.quantity"
                  @change="form.calc_price = form.price * form.quantity"
                />
              </div>

              <div class="w-1/2 px-3">
                <label
                  :class="{
                    'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2': true,
                    'text-red-500': !form.calc_price,
                  }"
                  for="s_name"
                >
                  *金額
                </label>
                <input
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  id="s_name"
                  type="number"
                  placeholder=""
                  v-model="form.calc_price"
                />
              </div>
            </div>

            <div class="flex items-center justify-between sm:col-span-2 mt-16">
              <button
                @click.prevent="createStockAndInitialOrder"
                class="inline-block rounded-lg bg-indigo-500 px-8 py-3 text-center text-sm font-semibold text-white outline-none ring-indigo-300 transition duration-100 hover:bg-indigo-600 focus-visible:ring active:bg-indigo-700 md:text-base"
              >
                発注登録
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