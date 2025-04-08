<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { onMounted, reactive, ref } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";
const props = defineProps({
  order_users: Array,
  user_id: Number,
});

// 注文者
const order_config = reactive({
  user_id: null,
  user_name: null,
});

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

const getOrderRequestByOrderRequestId = (order_request_id) => {
  let order_request = null;

  order_request = order_requests.value.find(
    (request) => request.id === order_request_id
  );

  return order_request;
};

// 承認依頼
const sendAccept = (order_request_id) => {
  if (order_request_id) {
    const order_request = getOrderRequestByOrderRequestId(order_request_id);
    if (
      !(
        order_request.stock_price &&
        order_request.quantity &&
        order_request.stock_supplier
      )
    ) {
      return alert("数量・単価・金額・取引先が入力されていません。");
    }

    axios
      .post(route("stock.accept.order_request"), {
        order_request_id: order_request_id,
      })
      .then((res) => {
        console.log(res.data);
        if (res.data.status) {
          alert("承認依頼を送信しました。");
          const order_request = order_requests.value.find(
            (request) => request.id === order_request_id
          );
          if (order_request) {
            order_request.accept_flg = 1;
          }
        }
      })
      .catch((error) => {
        console.log(error);
      });
  }
};
// 発注依頼から発注作成
const sendInitialOrder = (order_request_id) => {
  console.log(order_request_id);
  if (confirm("発注登録を行いますか？")) {
    axios
      .post(route("stock.createInitialOrder"), {
        order_request_id: order_request_id,
      })
      .then((res) => {
        console.log(res.data);
        if (res.data.status) {
          alert(
            "発注登録が完了しました。発注一覧から発注情報を確認してください。"
          );
          window.location.reload();
        }
      })
      .catch((error) => {
        console.log(error);
      });
  }
};

// 発注数量更新
const updateQuantity = (order_request_id, quantity) => {};

// 完了処理
const completeOrderRequest = (order_request_id) => {
  const order_request = getOrderRequestByOrderRequestId(order_request_id);

  if (
    order_request.stock_price &&
    order_request.quantity &&
    order_request.stock_supplier
  ) {
    if (
      confirm(
        `以下の内容で発注登録してよろしいですか？\n発注先:${
          order_request.stock_supplier.supplier_name
        }\n単価:${order_request.stock_price}\n数量:${
          order_request.quantity
        }\n金額:${order_request.stock_price * order_request.quantity}`
      )
    ) {
      axios
        .put(route("stock.completeOrderRequest"), {
          order_request_id: order_request_id,
          user_id: order_config.user_id,
          price: order_request.stock_price,
          quantity: order_request.quantity,
        })

        .then((res) => {
          if (res.data) {
            alert("発注完了登録を実行しました");
            getOrderRequests();
          }
        })
        .catch((error) => {});
    }
  } else {
    return alert("数量・単価・金額・取引先が入力されていません。");
  }

  return;
};

const deleteOrderRequest = (order_request_id) => {
  console.log(order_request_id);
  if (confirm("発注依頼を削除してよろしいですか？")) {
    axios
      .delete(route("stock.deleteOrderRequest"), {
        params: {
          order_request_id: order_request_id,
        },
      })
      .then((res) => {
        console.log(res.data);
        if (res.data.status) {
          alert("発注依頼を削除しました。");
          window.location.reload();
        }
      })
      .catch((error) => {
        console.log(error);
      });
  } else {
    alert("発注依頼削除を取り消しました。");
  }
};

const handleUserId = (user_id) => {
  order_config.user_id = user_id;
  const selectedUser = props.order_users.find((user) => user.id == user_id);
  if (selectedUser) {
    order_config.user_name = selectedUser.name;
    console.log(selectedUser.name);
  }
};

const purchaseOrder = (order_request_id) => {
  const order_request = getOrderRequestByOrderRequestId(order_request_id);
  if (
    !(
      order_request.stock_price &&
      order_request.quantity &&
      order_request.stock_supplier
    )
  ) {
    return alert("数量・単価・金額・取引先が入力されていません。");
  }

  router.get(
    route("stock.purchase_order", { order_request_id: order_request_id }),
    {
      user_id: order_config.user_id,
      supplier_id: order_request.stock_supplier.supplier_id,
      price: order_request.stock_price,
      quantity: order_request.quantity,
    }
  );
};
onMounted(() => {
  getOrderRequests();

  if (props.user_id) {
    handleUserId(props.user_id);
  }
});
</script>
<template>
  <MainLayout :title="'発注依頼一覧'">
    <template #content>
      <h1 class="text-center text-xl font-bold text-gray-800">発注依頼一覧</h1>

      <section class="text-gray-600 body-font">
        <div class="py-12 mx-auto">
          <div class="flex flex-col text-center w-full mb-20">
            <p class="lg:w-2/3 mx-auto leading-relaxed text-base">
              在庫管理システムより取得した発注依頼を完了することができます。<br />
              URLをクリックすることで設定済みの注文ページが開きます。<br />
              注文完了後は、完了ボタンを押下してください。
            </p>
          </div>

          <div
            v-if="!order_config.user_id"
            class="w-1/2 mx-auto mb-8 p-4 bg-gray-100"
          >
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
            <h2 class="mb-4 text-lg font-bold">
              注文者：{{ order_config.user_name }}
            </h2>
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
                    現在個数
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    数量
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    単価
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    金額
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    送料
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    発注先(リードタイム)
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    発注依頼日
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    依頼者
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    注文書
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap"
                  >
                    承認
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    完了
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  ></th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="order_request in order_requests"
                  :key="order_request.id"
                  :class="{ 'bg-indigo-50': !order_request.img_path }"
                >
                  <td class="px-4 py-3">
                    <a
                      class="underline text-blue-500"
                      :href="
                        route('stock.show.stocks', {
                          stock_id: order_request.stock_id,
                        })
                      "
                      >{{ order_request.order_request_id }}</a
                    >
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
                  <td class="px-4 py-3 text-lg text-gray-900">{{}}</td>
                  <td class="px-4 py-3 text-lg text-gray-900 w-32">
                    <input
                      type="number"
                      name=""
                      id=""
                      class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      :value="order_request.quantity"
                      @change="
                        updateQuantity(order_request.id, $event.target.value)
                      "
                    />
                  </td>
                  <td class="px-4 py-3 text-lg text-gray-900">
                    <input
                      type="number"
                      name=""
                      id=""
                      class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      :value="order_request.stock_price"
                      @input="order_request.stock_price = $event.target.value"
                    />
                  </td>
                  <td class="px-4 py-3 text-lg text-gray-900">
                    <input
                      type="number"
                      name=""
                      id=""
                      class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      :value="order_request.calc_price"
                    />
                  </td>
                  <td class="px-4 py-3 text-lg text-gray-900">
                    {{ order_request.postage }}
                  </td>
                  <td class="px-4 py-3 text-lg text-gray-900">
                    <span v-if="order_request.stock_supplier"
                      >{{ `${order_request.stock_supplier.supplier_name}` }} ({{
                        order_request.stock_supplier.lead_time
                          ? `${order_request.stock_supplier.lead_time}日`
                          : "未"
                      }})</span
                    >

                    <span v-else class="text-sm text-red-500 underline"
                      ><a
                        :href="
                          route('stock.show.stocks', {
                            stock_id: order_request.stock_id,
                          })
                        "
                        >取引先を設定してください。</a
                      ></span
                    >
                  </td>

                  <td class="px-4 py-3 text-lg text-gray-900">
                    {{
                      new Date(order_request.created_at).toLocaleDateString(
                        "ja-JP"
                      )
                    }}
                  </td>
                  <td
                    :class="{
                      'px-4 py-3 text-lg text-gray-900': true,
                    }"
                  >
                    {{ order_request.request_user_name }}
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
                    <button
                      v-else-if="order_config.user_id"
                      @click="purchaseOrder(order_request.id)"
                      class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm"
                    >
                      発注書
                    </button>
                    <span v-else class="text-sm text-red-500"
                      >注文者を選択してください。</span
                    >
                  </td>
                  <td
                    :class="{
                      'px-4 py-3 text-lg text-gray-900 w-24': true,
                    }"
                  >
                    <button
                      @click="sendAccept(order_request.id)"
                      v-if="order_request.accept_flg === 0"
                      class="text-sm bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded-full"
                    >
                      依頼
                    </button>
                    <span
                      class="text-sm bg-orange-500 text-white py-2 px-4 rounded-full"
                      v-else-if="order_request.accept_flg === 1"
                      >待ち</span
                    >
                    <button
                      @click="sendInitialOrder(order_request.id)"
                      v-if="order_request.accept_flg === 2"
                      class="text-sm bg-green-500 hover:bg-green-700 text-white py-2 px-4 rounded-full"
                    >
                      承認
                    </button>
                    <span
                      class="text-sm bg-red-500 hover:bg-red-700 text-white py-2 px-4 rounded-full"
                      v-else-if="order_request.accept_flg === 3"
                      >却下</span
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
                    <span class="text-sm font-bold" v-else
                      >発注者を選択すると完了できます</span
                    >
                  </td>
                  <td
                    :class="{
                      'px-4 py-3 text-lg text-gray-900': true,
                    }"
                  >
                    <button
                      @click="
                        deleteOrderRequest(order_request.order_request_id)
                      "
                      class="text-sm bg-red-500 hover:bg-red-700 text-white py-2 px-4 rounded whitespace-nowrap"
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