<script setup>
import axios from "axios";
import { ref, onMounted } from "vue";
import { Link } from "@inertiajs/vue3";

const initial_orders = ref([]);

const getReceiptOrders = () => {
  axios
    .get(route("stock.tablet.getDeliveryOrders"))
    .then((res) => {
      initial_orders.value = res.data;
      console.log(initial_orders.value);
    })
    .catch((error) => {
      console.log(error);
    });
};

onMounted(() => {
    getReceiptOrders();
})
</script>
<template>
  <section id="base-container" class="text-gray-600 body-font">
    <div class="container py-24 mx-auto">
      <div class="flex flex-col text-center w-full mb-8">
        <h1
          class="sm:text-4xl text-3xl font-medium title-font mb-2 text-blue-600"
        >
          発注情報
        </h1>
        <p class="lg:w-2/3 mx-auto leading-relaxed text-base">
          以下に発注情報を記載しています。<br>
          ステータスが<span>納品済</span>となっている場合、事務所に保管されておりますので、近日中に取りに来てください。
        </p>
      </div>
      <div class="w-1/2 mx-auto mb-8">

      </div>
      <div class="w-full mx-auto overflow-auto mt-12">
        <table class="table-auto w-full text-left whitespace-no-wrap">
          <thead>
            <tr>

              <th
                class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-indigo-100 rounded-tl rounded-bl"
              >
                画像
              </th>
              <th
                class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-indigo-100"
              >
                注文者
              </th>
              <th
                class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-indigo-100"
              >
                注文日
              </th>
              <th
                class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-indigo-100"
              >
                注文先
              </th>
              <th
                class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-indigo-100"
              >
                品名:品番
              </th>
              <th
                class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-indigo-100 whitespace-nowrap"
              >
                数量
              </th>
              <th
                class="w-10 title-font tracking-wider font-medium text-gray-900 text-sm bg-indigo-100 rounded-tr rounded-br"
              >状態</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in initial_orders" :key="order.id" :class="{'bg-gray-100' : order.receive_flg === 0, 'bg-red-100': order.receive_flg === 1}">
              <td class="w-24 px-4 py-6">
                <img
                  @click="modalImage($event.target)"
                  :src="
                    order.img_path && order.img_path.includes('https://')
                      ? order.img_path
                      : 'http://monokanri-app.local/' + order.img_path
                  "
                  alt=""
                />
              </td>
              <td class="px-4 py-6">{{ order.order_user }}</td>
              <td class="px-4 py-6">
                {{ new Date(order.order_date).toLocaleDateString("ja-JP") }}
              </td>
              <td class="px-4 py-6">{{ order.com_name }}</td>
              <td class="px-4 py-6">
                {{ order.name + " : " + order.s_name }}
              </td>
              <td class="px-4 py-6 w-24">
                {{ order.quantity + order.order_unit }}
              </td>
              <td class="w-16 text-center pr-4">
                <button
                :class='{"font-semibold hover:text-white py-2 px-4 border  rounded text-sm whitespace-nowrap" : true , "bg-transparent text-gray-700 border-gray-500 hover:border-transparent" : order.receive_flg === 0, "bg-red-600 text-white border-red-500 hover:border-transparent": order.receive_flg === 1}'
                >
                  {{ order.receive_flg === 0 ? '注文中' : '納品済' }}
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</template>
<style scoped>


#base-container{
  font-size: 18px;
  height: 100vh;
  width: 100vw;
}
</style>