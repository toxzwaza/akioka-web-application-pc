<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { onMounted, reactive, ref } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";

const base_initial_orders = ref([]);
const initial_orders = ref([]);
const order_users = ref([]);

const sort = ref("new_order");
const filter = ref("");

const createSort = (sort_field) => {
  sort.value = sort_field
  switch (sort_field) {
    case "new_order":
      initial_orders.value = [...initial_orders.value].sort(
        (a, b) => new Date(b.order_date) - new Date(a.order_date)
      );
      confirm("新しい順に並び替えました。");
      break;
    case "old_order":
      initial_orders.value = [...initial_orders.value].sort(
        (a, b) => new Date(a.order_date) - new Date(b.order_date)
      );
      confirm("古い順に並び替えました。");
      break;
  }
};
const getInitialOrders = () => {
  axios
    .get(route("stock.getAllInitialOrders"))
    .then((res) => {
      initial_orders.value = res.data;
      base_initial_orders.value = res.data;
      console.log(initial_orders.value);
      updateOrderUsers();
    })
    .catch((error) => {
      console.log(error);
    });
};

const updateOrderUsers = () => {
  const unique_order_users = [
    ...new Set(initial_orders.value.map((order) => order.order_user)),
  ];
  order_users.value = unique_order_users;
  console.log(unique_order_users);
};

const updateNameOrSName = (id, field, value) => {
  console.log(id, field, value);
  if (!(id && field)) {
    alert("エラーが発生しました。");
  }
  axios
    .post(route("stock.update_initial_order"), {
      order_id: id,
      field: field,
      value: value,
    })
    .then((res) => {
      if (res.data.status) {
        confirm("更新が完了しました。");
        getInitialOrders();
      } else {
        if (
          confirm(
            "更新中にエラーが発生しました。再読み込みしますか？" + res.data.msg
          )
        ) {
          location.reload();
        }
      }
    })
    .catch((error) => {
      console.log(error);
    });
};

const updateFilter = (filter, value) => {
  switch (filter) {
    case "order_user":
      initial_orders.value = initial_orders.value.filter(
        (order) => order.order_user === value
      );
      break;
    case "delifile_path":
      if (value === "1") {
        initial_orders.value = initial_orders.value.filter(
          (order) => order.delifile_path
        );
      } else if (value === "2") {
        initial_orders.value = initial_orders.value.filter(
          (order) => !order.delifile_path
        );
      }

      break;
    default:
      initial_orders.value = base_initial_orders.value;
      break;
  }
};

onMounted(() => {
  getInitialOrders();
});
</script>
<template>
  <MainLayout :title="'発注一覧'">
    <template #content>
      <h1 class="text-center text-xl font-bold text-gray-800">発注一覧</h1>

      <section class="text-gray-600 body-font">
        <div class="container px-5 py-12 mx-auto">
          <div class="flex flex-col text-center w-full mb-20">
            <p class="lg:w-2/3 mx-auto leading-relaxed text-base">
              発注確認と品名・品番の修正が可能です。<br />
              倉庫格納済みおよび引き渡しが完了しているリストは変更できませんのでご注意ください。
            </p>
          </div>
          <div id="sort_container" class="my-8">
            <p class="mb-2 font-bold">並び替え</p>
            <div class="button_container flex items-center justify-start">
              <button
                :class="{
                  'mr-2 text-sm bg-blue-500  text-white font-bold py-2 px-4 rounded': true,
                  'opacity-60': sort === 'new_order',
                }"
                @click="createSort('new_order')"
              >
                新しい順
              </button>
              <button
                :class="{
                  'mr-2 text-sm bg-blue-500  text-white font-bold py-2 px-4 rounded': true,
                  'opacity-60': sort === 'old_order',
                }"
                @click="createSort('old_order')"
              >
                古い順
              </button>
            </div>

            <p class="mt-4 mb-2 font-bold">フィルタ</p>
            <div class="button_container flex items-center justify-start">
              <div class="mr-4">
                <button
                  :class="{
                    'text-sm bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded': true,
                  }"
                  @click="updateFilter('reset')"
                >
                  リセット
                </button>
              </div>
              <div class="w-32 mr-2">
                <label
                  class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                  for="grid-last-name"
                >
                  注文者
                </label>
                <select
                  @change="updateFilter('order_user', $event.target.value)"
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  name="order_user"
                  id=""
                >
                  <option value="0">未選択</option>
                  <option
                    v-for="user in order_users"
                    :key="user.id"
                    :value="user"
                  >
                    {{ user }}
                  </option>
                </select>
              </div>
              <div class="w-32 mr-2">
                <label
                  class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                  for="grid-last-name"
                >
                  納品書
                </label>
                <select
                  @change="updateFilter('delifile_path', $event.target.value)"
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  name="order_user"
                  id=""
                >
                  <option value="0">未選択</option>
                  <option value="1">済</option>
                  <option value="2">未</option>
                </select>
              </div>
            </div>
          </div>

          <div class="w-full mx-auto overflow-auto">
            <table class="table-auto w-full text-left whitespace-no-wrap">
              <thead>
                <tr>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl"
                  >
                    注文No
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl"
                  >
                    画像
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    注文者
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    注文日
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    注文先
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    品名
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    品番
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    数量
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    納品書
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="order in initial_orders"
                  :key="order.id"
                  :class="{ 'bg-indigo-50': !order.img_path }"
                >
                  <td class="px-4 py-3">{{ order.order_no }}</td>
                  <td class="w-24 px-4 py-6">
                    <img
                      :src="
                        order.img_path && order.img_path.includes('https://')
                          ? order.img_path
                          : 'http://monokanri-app.local/' + order.img_path
                      "
                      alt=""
                    />
                  </td>
                  <td class="px-4 py-3">{{ order.order_user }}</td>
                  <td class="px-4 py-3 text-lg text-gray-900">
                    {{ new Date(order.order_date).toLocaleDateString("ja-JP") }}
                  </td>
                  <td class="px-4 py-3 text-lg text-gray-900">
                    {{ order.com_name }}
                  </td>
                  <td class="px-4 py-3 text-lg text-gray-900">
                    <input
                      @change="
                        updateNameOrSName(order.id, 'name', $event.target.value)
                      "
                      type="text"
                      name="name"
                      v-model="order.name"
                      id=""
                    />
                  </td>
                  <td class="px-4 py-3 text-lg text-gray-900">
                    <input
                      @change="
                        updateNameOrSName(
                          order.id,
                          's_name',
                          $event.target.value
                        )
                      "
                      type="text"
                      name="s_name"
                      v-model="order.s_name"
                      id=""
                    />
                  </td>
                  <td class="px-4 py-3 text-lg text-gray-900">
                    {{ order.quantity }}
                  </td>
                  <td
                    :class="{
                      'px-4 py-3 text-lg text-gray-900': true,
                      'font-bold text-red-400': order.delifile_path,
                    }"
                  >
                    {{ order.delifile_path ? "済" : "未" }}
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