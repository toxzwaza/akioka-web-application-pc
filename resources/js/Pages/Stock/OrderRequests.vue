<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { onMounted, reactive, ref } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";
import MainTitle from "@/Components/Title/MainTitle.vue";

const props = defineProps({
  order_users: Array,
  user_id: Number,
});

const modal_status = reactive({
  status: false,
  order_request: null,
  approval_path: null,
});
const openModal = (order_request) => {
  modal_status.approval_path = "";
  modal_status.order_request = order_request;
  modal_status.status = true;
  if (order_request.file_path) {
    modal_status.approval_path = `https://akioka.cloud/${order_request.file_path}`;
  }
};
const reNotify = (order_request_id, user_id) => {
  console.log(order_request_id, user_id);
  axios
    .post(route("stock.accept.order_request.re-notify"), {
      order_request_id: order_request_id,
      user_id: user_id,
    })
    .then((res) => {
      console.log(res.data);
      if (res.data.status) {
        alert("再通知が完了しました。");
      }
    })
    .catch((error) => {
      console.log(error);
    });
};
const contain_approvals = reactive({
  list: [],
  file_path: "",
}); //同一稟議書で承認依頼
const toggle_contain_approvals = (order_request) => {
  // 単価・数量・取引先が登録されているか確認
  if (
    !(
      order_request.price &&
      order_request.quantity &&
      order_request.supplier_id
    )
  ) {
    return alert("数量・単価・金額・取引先が入力されていません。");
  }

  const index = contain_approvals.list.indexOf(order_request.id);
  if (index === -1) {
    contain_approvals.list.push(order_request.id);
    if (order_request.file_path) {
      if (!contain_approvals.file_path) {
        contain_approvals.file_path = order_request.file_path;
      } else {
        if (confirm("既に稟議書が設定されています。")) {
          return;
        }
      }
    }
  } else {
    contain_approvals.list.splice(index, 1);
    order_request.select_flg = false;
  }

  console.log(contain_approvals);
};

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
      order_requests.value = res.data.order_requests;
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
        order_request.price &&
        order_request.quantity &&
        order_request.supplier_id
      )
    ) {
      return alert("数量・単価・金額・取引先が入力されていません。");
    }

    if (contain_approvals.list.length > 0 && !contain_approvals.file_path) {
      return alert("選択された物品依頼の中に稟議書が含まれていません。");
    }

    axios
      .post(route("stock.accept.order_request"), {
        order_request_id: order_request_id,
        user_id: order_config.user_id,

        // 複数の承認依頼を送信
        contain_approvals: contain_approvals.list,
        file_path: contain_approvals.file_path,
      })
      .then((res) => {
        console.log(res.data);
        if (res.data.status) {
          const order_request = order_requests.value.find(
            (request) => request.id === order_request_id
          );
          if (res.data.accept_flg == 1) {
            alert("承認依頼を送信しました。");
            if (order_request) {
              order_request.accept_flg = 1;
            }
          } else if (res.data.accept_flg == 2) {
            alert("承認はありませんでした。");
            if (order_request) {
              order_request.accept_flg = 2;
            }
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
          order_requests.value = order_requests.value.filter(
            (request) => request.id !== order_request_id
          );
        }
      })
      .catch((error) => {
        console.log(error);
      });
  }
};

// 発注数量更新
const updateQuantityPriceCalcPricePostage = (flg, order_request) => {
  let master = {
    price: false,
    postage: false,
  };

  switch (flg) {
    case "price":
      if (
        confirm("単価が変更されました。設定した単価をマスタに設定しますか？")
      ) {
        master.price = true;
      }
    case "quantity":
      if (confirm("数量もしくは単価が変更されました。金額を再計算しますか？")) {
        order_request.calc_price = order_request.quantity * order_request.price;
      }
      break;
    case "calc_price":
      break;
    case "postage":
      if (
        confirm("送料が変更されました。設定した送料をマスタに設定しますか？")
      ) {
        master.postage = true;
      }
      break;
  }

  axios
    .put(route("stock.updateOrderRequest"), {
      order_request_id: order_request.id,
      quantity: order_request.quantity,
      price: order_request.price,
      calc_price: order_request.calc_price,
      postage: order_request.postage,
      is_update_price: master.price,
      is_update_postage: master.postage,
      supplier_id: order_request.supplier_id,
    })
    .then((res) => {
      console.log(res.data);
      if (res.data.status) {
        console.log("更新完了しました。");
      }
    })
    .catch((error) => {
      console.log(error);
    });
};
const updateSubDescription = (val) => {
  if (!modal_status.order_request.id) {
    return alert("エラーが発生しました。");
  }

  // 発注担当者コメント更新処理
  axios
    .post(route("stock.updateSubDescription"), {
      order_request_id: modal_status.order_request.id,
      sub_description: val,
    })
    .then((res) => {
      if (res.data.status) {
        alert("発注担当者コメントを更新しました。");
        // window.location.reload()
      }
    })
    .catch((error) => {
      console.log(error);
    });
};

// 完了処理
const completeOrderRequest = (order_request_id) => {
  const order_request = getOrderRequestByOrderRequestId(order_request_id);

  if (
    order_request.price &&
    order_request.quantity &&
    order_request.supplier_id
  ) {
    if (
      confirm(
        `以下の内容で発注登録してよろしいですか？\n発注先:${
          order_request.supplier_name
        }\n単価:${order_request.price}\n数量:${order_request.quantity}\n金額:${
          order_request.price * order_request.quantity
        }`
      )
    ) {
      axios
        .put(route("stock.completeOrderRequest"), {
          order_request_id: order_request_id,
          user_id: order_config.user_id,
          price: order_request.price,
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
  } else {
    order_config.user_id = null;
    order_config.user_name = null;
  }
};

const purchaseOrder = (order_request_id) => {
  const order_request = getOrderRequestByOrderRequestId(order_request_id);
  if (
    !(
      order_request.price &&
      order_request.quantity &&
      order_request.supplier_id
    )
  ) {
    return alert("数量・単価・金額・取引先が入力されていません。");
  }

  router.get(
    route("stock.purchase_order", { order_request_id: order_request_id }),
    {
      user_id: order_config.user_id,
      supplier_id: order_request.supplier_id,
      price: order_request.price,
      quantity: order_request.quantity,
    }
  );
};

const skipAccept = (order_request_id) => {
  const order_request = order_requests.value.find(
    (order_request) => order_request.order_request_id === order_request_id
  );

  if (order_request) {
    if (confirm("承認をスキップして発注データを作成します。よろしいですか？")) {
      axios
        .post(route("stock.accept.order_request.skip"), {
          order_request_id: order_request_id,
          user_id: order_config.user_id,
        })
        .then((res) => {
          if (res.data.status) {
            alert("承認をスキップしました。");
            order_request.accept_flg = 2;
          }
        });
    }
  } else {
    alert("発注依頼データが見つかりませんでした。");
  }
  console.log(order_request_id);
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
      <MainTitle
        :top="'発注依頼一覧'"
        :sub="'在庫管理システムより取得した発注依頼を完了することができます。複数選択後、依頼をクリックすると添付されている稟議書を共有して承認フローを回します。'"
      />

      <section class="text-gray-600 body-font">
        <div class="mx-auto">
          <div
            v-if="!order_config.user_id"
            class="w-full mx-auto mb-8 p-4 bg-gray-100"
          >
            <div
              class="flex min-h-full flex-col justify-center px-6 py-4 lg:px-8"
            >
              <div class="sm:mx-auto sm:w-full sm:max-w-sm">
                <img
                  class="mt-10 mx-auto h-10 w-auto"
                  src="/img/base/AK_logo.png"
                  alt="Your Company"
                />
              </div>

              <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
                <form class="space-y-6" action="#" method="POST">
                  <div>
                    <label
                      for="email"
                      class="block text-sm/6 font-medium text-gray-900"
                      >ログインユーザー</label
                    >
                    <div class="mt-2">
                      <select
                        name=""
                        id=""
                        class="block w-full rounded-md bg-white px-3 py-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
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

                  <div>
                    <button
                      type="submit"
                      class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm/6 font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                    >
                      Sign in
                    </button>
                  </div>
                </form>

                <p class="mt-10 text-center text-sm/6 text-gray-500">
                  Not a member?
                  <a
                    href="#"
                    class="font-semibold text-indigo-600 hover:text-indigo-500"
                    >管理者に確認してください。</a
                  >
                </p>
              </div>
            </div>
          </div>

          <div v-else class="w-full mx-auto overflow-auto">
            <h2 class="mb-4 text-lg font-bold">
              ログイン中：{{ order_config.user_name }}
            </h2>
            <table
              id="table_container"
              class="table-auto w-full text-left whitespace-no-wrap"
            >
              <thead>
                <tr>
                  <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap"
                  >
                    選択
                  </th>
                  <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap"
                  >
                    承認
                  </th>

                  <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap"
                  >
                    コメント
                  </th>
                  <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl"
                  >
                    画像
                  </th>
                  <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    品名
                  </th>
                  <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    品番
                  </th>
                  <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap"
                  >
                    現在個数
                  </th>
                  <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap"
                  >
                    発注点
                  </th>
                  <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap"
                  >
                    発注数量
                  </th>
                  <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap"
                  >
                    発注単位
                  </th>
                  <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    単価
                  </th>
                  <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    金額
                  </th>
                  <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    送料
                  </th>
                  <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    発注先
                  </th>
                  <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    発注依頼日
                  </th>
                  <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    依頼者
                  </th>
                  <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    発注者
                  </th>
                  <!-- <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    注文書
                  </th> -->

                  <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap"
                  ></th>
                  <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  ></th>
                  <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  ></th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="order_request in order_requests"
                  :key="order_request.id"
                  :class="{
                    'transition duration-300': true,
                    'bg-blue-50': order_request.select_flg,
                  }"
                >
                  <td class="text-center">
                    <input
                      v-if="order_request.accept_flg === 0"
                      v-model="order_request.select_flg"
                      type="checkbox"
                      @change="toggle_contain_approvals(order_request)"
                    />
                  </td>
                  <td
                    :class="{
                      'px-4 py-4 text-lg text-gray-900 w-32': true,
                    }"
                  >
                    <div v-if="order_config.user_id" class="flex">
                      <Link
                        v-if="!order_request.stock_id"
                        :href="
                          route('stock.stocks.create', {
                            order_request_id: order_request.id,
                          })
                        "
                        class="text-sm bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded-full"
                      >
                        在庫登録
                      </Link>

                      <button
                        v-else-if="order_request.accept_flg === 0"
                        @click="sendAccept(order_request.id)"
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
                    </div>
                  </td>

                  <td class="px-4 py-4 text-lg text-gray-900">
                    <button class="bg-blue-600 py-1 px-2.5 rounded">
                      <i
                        @click="openModal(order_request)"
                        class="text-white fas fa-comment"
                      ></i>
                    </button>
                  </td>
                  <td class="img_container">
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
                  <td class="px-4 py-4">
                    <a
                      v-if="order_request.stock_id"
                      class="underline text-blue-500"
                      :href="
                        route('stock.show.stocks', {
                          stock_id: order_request.stock_id,
                        })
                      "
                      >{{ order_request.name }}</a
                    >
                    <span class="text-lg text-gray-900" v-else>{{
                      order_request.order_request_name
                    }}</span>
                  </td>
                  <td class="px-4 py-4 text-lg text-gray-900">
                    {{
                      order_request.s_name
                        ? order_request.s_name
                        : order_request.order_request_s_name
                    }}
                  </td>
                  <td class="px-4 py-4 text-lg text-gray-900">
                    {{ order_request.stock_storage_quantity }}
                  </td>
                  <td class="px-4 py-4 text-lg text-gray-900">
                    {{ order_request.reorder_point }}
                  </td>
                  <td class="px-4 py-4 text-lg text-gray-900 w-32">
                    <input
                      type="number"
                      name=""
                      id=""
                      class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-4 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      v-model="order_request.quantity"
                      @change="
                        updateQuantityPriceCalcPricePostage(
                          'quantity',
                          order_request
                        )
                      "
                    />
                  </td>
                  <td class="px-4 py-4 text-lg text-gray-900 w-32">
                    {{ order_request.unit }}
                  </td>
                  <td class="px-4 py-4 text-lg text-gray-900 w-48">
                    <input
                      type="number"
                      name=""
                      id=""
                      class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-4 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      v-model="order_request.price"
                      @change="
                        updateQuantityPriceCalcPricePostage(
                          'price',
                          order_request
                        )
                      "
                    />
                  </td>
                  <td class="px-4 py-4 text-lg text-gray-900 w-48">
                    <input
                      type="number"
                      name=""
                      id=""
                      class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-4 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      v-model="order_request.calc_price"
                      @change="
                        updateQuantityPriceCalcPricePostage(
                          'calc_price',
                          order_request
                        )
                      "
                    />
                  </td>
                  <td class="px-4 py-4 text-lg text-gray-900 w-48">
                    <input
                      type="number"
                      name=""
                      id=""
                      class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-4 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      v-model="order_request.postage"
                      @change="
                        updateQuantityPriceCalcPricePostage(
                          'postage',
                          order_request
                        )
                      "
                    />
                  </td>
                  <td class="px-4 py-4 text-lg text-gray-900">
                    <span v-if="order_request.supplier_id"
                      >{{ `${order_request.supplier_name}` }} ({{
                        order_request.stock_supplier_lead_time
                          ? `${order_request.stock_supplier_lead_time}日`
                          : "未"
                      }})</span
                    >

                    <span v-else class="text-sm text-red-500 underline"
                      ><a
                        v-if="order_request.stock_id"
                        :href="
                          route('stock.show.stocks', {
                            stock_id: order_request.stock_id,
                          })
                        "
                        >取引先を設定してください。</a
                      ></span
                    >
                  </td>

                  <td class="px-4 py-4 text-lg text-gray-900">
                    {{
                      new Date(order_request.created_at).toLocaleDateString(
                        "ja-JP"
                      )
                    }}
                  </td>
                  <td
                    :class="{
                      'px-4 py-4 text-lg text-gray-900': true,
                    }"
                  >
                    {{ order_request.request_user_name }}
                  </td>
                  <td
                    :class="{
                      'px-4 py-4 text-lg text-gray-900': true,
                    }"
                  >
                    {{ order_request.order_user_name }}
                  </td>
                  <td class="w-32">
                    <button
                      @click="openModal(order_request)"
                      class="text-sm bg-gray-500 hover:bg-gray-700 text-white py-2 px-4 rounded whitespace-nowrap"
                    >
                      詳細確認
                    </button>
                  </td>

                  <td class="w-32">
                    <button
                      @click="skipAccept(order_request.id)"
                      class="text-sm bg-green-500 hover:bg-green-700 text-white py-2 px-4 rounded whitespace-nowrap"
                    >
                      承認スキップ
                    </button>
                  </td>
                  <td
                    :class="{
                      'px-4 py-4 text-lg text-gray-900': true,
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

      <!-- モーダルウィンドウ -->
      <div id="modal" :class="{ active: modal_status.status }">
        <div id="close_container">
          <button
            @click="modal_status.status = !modal_status.status"
            class="modal__close bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm"
            aria-label="Close modal"
          >
            <i class="fa fa-times"></i>
          </button>
        </div>

        <div v-if="modal_status.order_request" class="mb-4">
          <label
            for="message"
            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
            >依頼者コメント</label
          >
          <textarea
            id="message"
            rows="4"
            :class="{
              'block p-2.5 w-full text-sm text-gray-900 bg-gray-200 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 pointer-events-none': true,
            }"
            placeholder="コメントがありません。"
            :value="modal_status.order_request.description"
          ></textarea>
        </div>
        <div v-if="modal_status.order_request" class="mb-8">
          <label
            for="message"
            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
            >発注者コメント</label
          >
          <textarea
            id="message"
            rows="4"
            :class="{
              'block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500': true,
            }"
            @change="updateSubDescription($event.target.value)"
            placeholder="コメントがある場合はこちらに記載してください。"
            :value="modal_status.order_request.sub_description"
          ></textarea>
        </div>

        <details class="mb-4" open>
          <summary
            class="text-gray-700 mb-2 font-bold text-lg cursor-pointer hover:text-gray-900"
          >
            承認状況確認
          </summary>
          <div
            v-if="modal_status.order_request"
            class="flex items-center justify-start mt-4"
            id="approval_container"
          >
            <div
              v-for="approval in modal_status.order_request
                .order_request_approvals"
              :key="approval.id"
              class="card rounded overflow-hidden shadow-lg mr-8"
            >
              <img
                class="w-full"
                :src="
                  approval.status === 1
                    ? '/img/stock/order_request/approval_icon.png'
                    : approval.status === 2
                    ? '/img/stock/order_request/not_approval_icon.png'
                    : '/img/stock/order_request/none_approval.png'
                "
                alt="承認状態アイコン"
              />
              <div class="px-6 py-4">
                <div class="text-sm mb-2">
                  {{ new Date(approval.updated_at).getFullYear() }}年{{
                    new Date(approval.updated_at).getMonth() + 1
                  }}月{{ new Date(approval.updated_at).getDate() }}日
                  {{ new Date(approval.updated_at).getHours() }}時{{
                    new Date(approval.updated_at).getMinutes()
                  }}分
                </div>
                <div class="font-bold text-xl mb-2">{{ approval.name }}</div>
                <p class="text-gray-700 text-base">
                  {{
                    approval.comment
                      ? approval.comment
                      : "コメントがありません。"
                  }}
                </p>
                <button
                  @click="
                    reNotify(
                      modal_status.order_request.order_request_id,
                      approval.user_id
                    )
                  "
                  v-if="approval.status == 0"
                  class="notify_button bg-blue-600 py-1 px-2.5 rounded"
                >
                  <i class="text-white fas fa-bell"></i>
                </button>
              </div>
            </div>
          </div>
        </details>

        <details class="mt-8 mb-4" open>
          <summary
            class="text-gray-700 mb-2 font-bold text-lg cursor-pointer hover:text-gray-900"
          >
            稟議書確認
          </summary>

          <div id="pdfviewer">
            <iframe ref="pdfViewer" :src="modal_status.approval_path"></iframe>
          </div>
        </details>
      </div>
    </template>
  </MainLayout>
</template>
<style scoped lang="scss">
table {
  &#table_container {
    width: 130vw;
  }

  td {
    white-space: nowrap;

    &.img_container {
      width: 4vw;
      padding: 0;

      img {
        width: 100%;
        height: auto;
        width: 200px;
        object-fit: contain;
      }
    }
  }
}

// モーダルウインドウ
#modal {
  position: fixed;
  bottom: 0;
  left: 48%;
  // transform: translateX(-50%);

  width: 50vw;

  padding: 1rem;

  background-color: white;
  box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
  border-radius: 10px 10px 0 0;
  height: 0;
  transform: translateY(100%);
  overflow-y: scroll;
  &.active {
    height: auto;
    max-height: 80vh;
    overflow-y: scroll;

    transform: translateY(0);
    transition: all 0.5s;
  }

  & #close_container {
    width: 100%;
    display: flex;
    justify-content: end;
  }
  & #pdfviewer {
    height: 70vh;

    & iframe {
      height: 96%;
      width: 100%;
    }
  }
}

.card {
  position: relative;

  & .notify_button {
    position: absolute;
    right: 2%;
    top: 2%;
  }
}
</style>