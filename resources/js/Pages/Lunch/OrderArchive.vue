<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { onMounted, reactive, ref } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";
import MainTitle from "@/Components/Title/MainTitle.vue";

const props = defineProps({
  order_date: String,
  lunch_orders: Array,
});

const order_date = ref(props.order_date);

const changeOrderDate = () => {
  console.log(order_date.value);
  router.get(route("lunch.order-archive"), {
    order_date: order_date.value,
  });
};

const deleteLunchOrder = (lunch_order_id) => {
  const lunch_order = props.lunch_orders.find(
    (lunch_order) => lunch_order.id === lunch_order_id
  );
  console.log(lunch_order);

  if (
    lunch_order &&
    confirm(`${lunch_order.user_name}さんの注文を取消しますか？`)
  ) {
    axios
      .delete(route("lunch.order.delete"), {
        params: {
          lunch_order_id: lunch_order_id,
        },
      })
      .then((res) => {
        console.log(res.data);
        if (res.data.status) {
          alert(`${lunch_order.user_name}さんの注文を削除しました。`)
          window.location.reload()
        }
      })
      .catch((error) => {
        console.log(error);
      });
  }
};
onMounted(() => {
  console.log(props.lunch_orders);
});
</script>
<template>
  <MainLayout :title="'弁当注文履歴'">
    <template #content>
      <MainTitle
        :top="'弁当注文履歴'"
        :sub="'弁当注文履歴の確認を行います。詳細から、注文データの修正を行います。'"
      />

      <section class="text-gray-600 body-font">
        <div class="mx-auto">
          <div class="w-1/2 mx-auto mb-8">
            <label
              class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
              for="grid-last-name"
            >
              注文日
            </label>
            <input
              class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
              id="order-date"
              type="date"
              v-model="order_date"
              @change="changeOrderDate"
            />
          </div>

          <div class="w-full mx-auto overflow-auto">
            <table class="table-auto w-full text-left whitespace-no-wrap">
              <thead>
                <tr>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl"
                  >
                    注文者
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    注文日時
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    受け取り日時
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    受け取り可否
                  </th>
                  <th
                    class="w-10 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tr rounded-br"
                  ></th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="lunch_order in props.lunch_orders"
                  :key="lunch_order.id"
                  :class="{ 'hover:bg-gray-50': true }"
                >
                  <td class="px-4 py-3">{{ lunch_order.user_name }}</td>
                  <td class="px-4 py-3">
                    {{
                      new Date(lunch_order.created_at).toLocaleTimeString([], {
                        hour: "2-digit",
                        minute: "2-digit",
                      })
                    }}
                  </td>
                  <td class="px-4 py-3">
                    {{
                      new Date(lunch_order.updated_at).toLocaleTimeString([], {
                        hour: "2-digit",
                        minute: "2-digit",
                      })
                    }}
                  </td>
                  <td class="px-4 py-3 text-lg text-gray-900">
                    {{ lunch_order.receive_flg ? "済" : "未" }}
                  </td>
                  <td class="w-10 text-center">
                    <button
                      @click="deleteLunchOrder(lunch_order.id)"
                      class="whitespace-nowrap text-sm bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                    >
                      削除
                    </button>
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