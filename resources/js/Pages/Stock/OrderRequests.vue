<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { onMounted, reactive, ref } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";
const props = defineProps({
  order_users: Array,
  user_id: Number
});

// 注文者
const order_config = reactive({
  user_id: null,
  user_name: null,
  quantity: null
})

const order_requests = ref([]);

const getOrderRequests = () => {
  axios
    .get(route("stock.getOrderRequests"))
    .then((res) => {
      console.log(res.data);
      order_requests.value = res.data;
    })
    .catch((error) => {
      console.log(error);
    });
};

const completeOrderRequest = (order_request_id) => {
  const quantity = prompt("数量を入力してください:");
  if (quantity !== null) {
    order_config.quantity = quantity;
  }
  if(!(order_config.user_id && order_config.quantity)){
    alert('注文者もしくは数量が選択されていない可能性があります。')
    return
  }

  axios
    .put(route("stock.completeOrderRequest"), {
      order_request_id: order_request_id,
      user_id: order_config.user_id,
      quantity: order_config.quantity
    })

    .then((res) => {
      if (res.data) {
        alert("発注完了登録を実行しました");
        getOrderRequests();
      }
    })
    .catch((error) => {});
};

const handleUserId = (user_id) => {
  order_config.user_id = user_id
  const selectedUser = props.order_users.find(user => user.id == user_id);
  if (selectedUser) {
    order_config.user_name = selectedUser.name;
    console.log(selectedUser.name)
  }
}
onMounted(() => {
  getOrderRequests();

  if(props.user_id){
    handleUserId(props.user_id)
  }
});
</script>
<template>
  <MainLayout :title="'発注一覧'">
    <template #content>
      <h1 class="text-center text-xl font-bold text-gray-800">発注依頼一覧</h1>

      <section class="text-gray-600 body-font">
        <div class="container px-5 py-12 mx-auto">
          <div class="flex flex-col text-center w-full mb-20">
            <p class="lg:w-2/3 mx-auto leading-relaxed text-base">
              在庫管理システムより取得した発注依頼を完了することができます。<br />
              URLをクリックすることで設定済みの注文ページが開きます。<br />
              注文完了後は、完了ボタンを押下してください。
            </p>
          </div>

          <div v-if="!order_config.user_id" class="w-1/2 mx-auto mb-8 p-4 bg-gray-100">
            <h2 class="text-xl text-red-500 font-bold mb-4">
              発注者を選択してください。
            </h2>
            <div class="">
              <div class="">
                <label
                  class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                  for="grid-password"
                >
                  発注者
                </label>
                <select
                  name=""
                  id=""
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  @change="handleUserId($event.target.value)"
                >
                  <option value="0">選択してください</option>
                  <option
                    v-for="order_user in order_users"
                    :key="order_user.id"
                    :value="order_user.id"
                  >
                    {{ order_user.name }}
                  </option>
                </select>
              </div>
            </div>
          </div>

          <div class="w-full mx-auto overflow-auto">
            <h2 class="mb-4 text-lg font-bold">注文者：{{ order_config.user_name }}</h2>
            <table class="table-auto w-full text-left whitespace-no-wrap">
              <thead>
                <tr>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl"
                  >
                    発注依頼ID
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl"
                  >
                    画像
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
                    個数
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    発注依頼日
                  </th>
                  <th></th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="order_request in order_requests"
                  :key="order_request.id"
                  :class="{ 'bg-indigo-50': !order_request.img_path }"
                >
                  <td class="px-4 py-3">
                    {{ order_request.order_request_id }}
                  </td>
                  <td class="w-24 px-4 py-6">
                    <img
                      :src="
                        order_request.img_path &&
                        order_request.img_path.includes('storage')
                          ? 'https://akioka.cloud/' + order_request.img_path
                          : order_request.img_path
                      "
                      alt=""
                    />
                  </td>
                  <td class="px-4 py-3">{{ order_request.name }}</td>
                  <td class="px-4 py-3 text-lg text-gray-900">
                    {{ order_request.s_name }}
                  </td>
                  <td class="px-4 py-3 text-lg text-gray-900">
                    {{ order_request.quantity }}
                  </td>
                  <td class="px-4 py-3 text-lg text-gray-900">
                    {{
                      new Date(order_request.created_at).toLocaleDateString(
                        "ja-JP"
                      )
                    }}
                  </td>
                  <td class="px-4 py-3 text-lg text-gray-900">
                    {{ order_request.quantity }}
                  </td>
                  <td
                    :class="{
                      'px-4 py-3 text-lg text-gray-900': true,
                    }"
                  >
                    <a
                      v-if="order_request.url"
                      class="text-sm bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded-full"
                      :href="order_request.url"
                      target="blank"
                    >
                      URL
                    </a>
                    <span v-else class="text-sm text-red-500 underline"
                      >購入URL設定</span
                    >
                  </td>
                  <td
                    :class="{
                      'px-4 py-3 text-lg text-gray-900': true,
                    }"
                  >
                    <button
                      v-if="order_config.user_id"
                      @click="
                        completeOrderRequest(order_request.order_request_id)
                      "
                      class="text-sm bg-green-500 hover:bg-green-700 text-white py-2 px-4 rounded-full"
                    >
                      完了
                    </button>
                    <span class="text-sm font-bold" v-else>発注者を選択すると完了できます</span>
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