<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { onMounted, reactive, ref } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";

const base_initial_orders = ref([]);
const initial_orders = ref([]);

const getInitialOrders = () => {
  axios
    .get(route("stock.tablet.getInitialOrders"))
    .then((res) => {
      initial_orders.value = res.data;
      base_initial_orders.value = res.data;
      console.log(initial_orders.value);
    })
    .catch((error) => {
      console.log(error);
    });
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
              発注確認と品名・品番の修正が可能です。
            </p>
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
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="order in initial_orders"
                  :key="order.id"
                  :class="{ 'bg-indigo-50': order.not_found_flg }"
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
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </template>
  </MainLayout>
</template>