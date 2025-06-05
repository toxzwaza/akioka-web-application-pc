<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { onMounted, reactive, ref } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";
const props = defineProps({
  classifications: Array,
  stock: Object,
  processes: Array,
  stock_storages: Array,
  locations: Array,
  storage_addresses: Array,
  stock_suppliers: Array,
  users: Array,
  admin_users: Array,
  suppliers: Array,
  initial_order: Object,
  stock_processes: Array,
});

const initial_orders = ref([]);
const select_storage_addresses = ref([]);

const form = reactive({
  stock_id: null,
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
  stock_process_id: null,
  del_flg: null,

  // 発注依頼用
  order_user: null,
  user_id: null,
  supplier_id: null,
  lead_time: null,
  quantity: null,
  unit: null,
  order_price: null,
  calc_price: null,
  postage: null,
  order_stock_process_id: 0,

  location_id: 0,
  storage_address_id: 0,

  stock_storage_id: 0,
  stock_storage_quantity: null,

  stock_supplier_supplier_id: null,
  stock_supplier_lead_time: null,

  orderNumber: null,
  alias: null,
});
const updateStockRequest = (flg) => {
  const update_data = {
    stock_id: form.stock_id,
  };

  if (flg === "alias") {
    update_data.alias = form.alias;
  } else if (flg === "orderNumber") {
    update_data.orderNumber = form.orderNumber;
  }

  axios
    .post(route("stock.update.stock_request"), update_data)
    .then((res) => {
      if (res.data.status) {
        alert("依頼情報を更新しました");
        window.location.reload();
      } else {
        alert("更新に失敗しました");
      }
    })
    .catch((error) => {
      console.error(error);
      alert("エラーが発生しました");
    });
};

const toggleStockRequest = () => {
  if (confirm("表示設定を変更してもよろしいですか？")) {
    axios
      .get(route("stock.toggle.stock_request", { stock_id: form.stock_id }))
      .then((res) => {
        console.log(res.data);
        if (res.data.status) {
          alert("現場依頼物品設定を更新しました");
          window.location.reload();
        }
      });
  } else {
    alert("キャンセルしました。");
  }
};

const handleClassification = () => {
  if (form.classification_id == 11) {
    form.stock_process_id = 29;
  }
};

const createInitialOrder = () => {
  if (
    !form.order_user ||
    !form.user_id ||
    !form.supplier_id ||
    !form.lead_time ||
    !form.quantity ||
    !form.calc_price ||
    !form.unit ||
    !form.order_price ||
    !form.order_stock_process_id
  ) {
    return alert("必須項目が入力されていません。");
  }

  // 在庫追加と発注登録
  axios
    .post(route("stock.store.initialOrders"), form)
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
const createStockStorage = () => {};
const createStockSupplier = () => {
  if (
    !form.stock_id ||
    !form.stock_supplier_supplier_id ||
    !form.stock_supplier_lead_time
  ) {
    return alert("必須入力項目が入力されていません。");
  }

  axios
    .post(route("stock.stock_supplier.store"), {
      stock_id: form.stock_id,
      supplier_id: form.stock_supplier_supplier_id,
      lead_time: form.stock_supplier_lead_time,
    })
    .then((res) => {
      if (res.data.status) {
        if (confirm("手配先登録が完了しました。")) {
          window.location.reload();
        }
      }
    })
    .catch((error) => {
      console.log(error);
    });
};

const editStock = () => {
  // 在庫編集
  axios
    .post(route("stock.store.stocks"), form)
    .then((res) => {
      console.log(res.data);
      if (res.data.status) {
        if (confirm("編集が完了しました。続けて在庫を追加しますか？")) {
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

const handleLocation = (location_id) => {
  console.log(props.storage_addresses);

  select_storage_addresses.value = props.storage_addresses.filter(
    (storage_address) => storage_address.location_id == location_id
  );
};

// 発注履歴を取得
const getInitialOrders = () => {
  if (initial_orders.value.length == 0) {
    axios
      .get(route("stock.getInitialOrders"), {
        params: {
          stock_id: form.stock_id,
        },
      })
      .then((res) => {
        console.log(res.data);
        if (res.data) {
          initial_orders.value = res.data;
        }
      })
      .catch((error) => {
        console.log(error);
      });
  }
};

const updateStockSupplier = (flg, stock_supplier) => {
  console.log(flg, stock_supplier);

  switch (flg) {
    case "save":
      axios
        .post(route("stock.stock_supplier.update"), {
          stock_supplier_id: stock_supplier.stock_supplier_id,
          lead_time: stock_supplier.lead_time,
          postage: stock_supplier.postage
        })
        .then((res) => {
          console.log(res.data);
          if(res.data.status){
            alert('更新が完了しました')
            window.location.reload()
          }else{
            alert(res.data.msg)
          }
        })
        .catch((error) => {
          console.log(error);
        });
      break;

    case "delete":
      axios
        .delete(route("stock.stock_supplier.delete"), {
          params: {
            stock_supplier_id: stock_supplier.stock_supplier_id,
          }
        })
        .then((res) => {
          console.log(res.data);
          if(res.data.status){
            alert('削除が完了しました')
            window.location.reload()
          }else{
            alert(res.data.msg)
          }
        })
        .catch((error) => {
          console.log(error);
        })
      break;
  }
};
const updateStockStorage = (flg, stock_storage) => {
  console.log(flg, stock_storage);

  switch (flg) {
    case "save":
      axios
        .post(route("stock.stock_storage.update"), {
          stock_storage_id: stock_storage.stock_storage_id,
          quantity: stock_storage.quantity,
          reorder_point: stock_storage.reorder_point
        })
        .then((res) => {
          console.log(res.data);
          if(res.data.status){
            alert('更新が完了しました')
            window.location.reload()
          }else{
            alert(res.data.msg)
          }
        })
        .catch((error) => {
          console.log(error);
        });
      break;

    case "delete":
      axios
        .delete(route("stock.stock_storage.delete"), {
          params: {
            stock_storage_id: stock_storage.stock_storage_id,
          }
        })
        .then((res) => {
          console.log(res.data);
          if(res.data.status){
            alert('削除が完了しました')
            window.location.reload()
          }else{
            alert(res.data.msg)
          }
        })
        .catch((error) => {
          console.log(error);
        })
      break;
  }
};

onMounted(() => {
  console.log(props.stock);

  if (props.initial_order) {
    form.user_id = props.initial_order.user_id;
    form.order_user = props.initial_order.order_user_id ?? 0;
    form.quantity = props.initial_order.quantity;
    form.calc_price = props.initial_order.calc_price;
    form.postage = props.initial_order.postage;
    form.unit = props.initial_order.order_unit;
    form.order_price = props.initial_order.price;
  }

  form.stock_id = props.stock.id;
  form.name = props.stock.name;
  form.s_name = props.stock.s_name;
  form.jan_code = props.stock.jan_code;
  form.img_path = props.stock.img_path;
  form.url = props.stock.url;
  form.purchase_identification_number =
    props.stock.purchase_identification_number;
  form.price = props.stock.price;
  form.solo_unit = props.stock.solo_unit;
  form.org_unit = props.stock.org_unit;
  form.quantity_per_org = props.stock.quantity_per_org;
  form.classification_id = props.stock.classification_id;
  form.deli_location = props.stock.deli_location;
  form.stock_process_id = props.stock.stock_process_id
    ? props.stock.stock_process_id
    : 0;
  form.del_flg = props.stock.del_flg;

  if (props.stock_suppliers && props.stock_suppliers.length > 0) {
    form.supplier_id = props.stock_suppliers[0].id;
    form.lead_time = props.stock_suppliers[0].lead_time;
  }

  if (props.stock_storages && props.stock_storages.length == 1) {
    form.stock_storage_id = props.stock_storages[0].stock_storage_id;
  }

  form.alias = props.stock.alias;
  form.orderNumber = props.stock.orderNumber;

  form.order_stock_process_id = form.stock_process_id
    ? form.stock_process_id
    : 0;
});
</script>
<template>
  <MainLayout :title="'在庫詳細'">
    <template #content>
      <h1 class="text-center text-xl font-bold text-gray-800">在庫詳細</h1>
      <div class="flex justify-center py-12">
        <div id="left_container" class="w-2/5">
          <!-- 発注登録 -->
          <div class="bg-red-50 p-4">
            <h3 class="text-lg font-bold dark:text-white mb-2">発注依頼登録</h3>
            <div v-if="props.stock_suppliers.length > 0">
              <p
                v-if="props.initial_order != null"
                class="text-gray-700 mb-3 text-sm"
              >
                ※直近の発注データをセットしています。必要に応じて変更してください。
              </p>
              <div class="flex flex-wrap -mx-3 mb-6">
                <div class="w-1/2 px-3">
                  <label
                    :class="{
                      'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2': true,
                      'text-red-500': !form.order_user,
                    }"
                    for="order_user"
                  >
                    *注文依頼者
                  </label>
                  <input
                    class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    list="users"
                    v-model="form.order_user"
                    id="order_user"
                  />
                  <datalist id="users">
                    <option value="0">未選択</option>
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
                  <select
                    class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    v-model="form.user_id"
                  >
                    <option value="0">未選択</option>
                    <option
                      v-for="user in props.admin_users"
                      :key="user.id"
                      :value="user.id"
                    >
                      {{ user.name }}
                    </option>
                  </select>
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
                  <select
                    :class="{
                      'appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500': true,
                    }"
                    id="name"
                    v-model="form.supplier_id"
                  >
                    <option value="">未選択</option>
                    <option
                      v-for="supplier in props.stock_suppliers"
                      :key="supplier.id"
                      :value="supplier.id"
                    >
                      {{
                        supplier.supplier_no != ""
                          ? `${supplier.supplier_no} : ${supplier.name}`
                          : supplier.name
                      }}
                    </option>
                  </select>
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
                    type="number"
                    placeholder=""
                    v-model="form.quantity"
                    @change="form.calc_price = form.order_price * form.quantity"
                  />
                </div>

                <div class="w-1/2 px-3">
                  <label
                    :class="{
                      'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2': true,
                      'text-red-500': !form.unit,
                    }"
                    for="s_name"
                  >
                    *単位
                  </label>

                  <select
                    name=""
                    id=""
                    class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    v-model="form.unit"
                  >
                    <option
                      v-if="props.stock.solo_unit"
                      :value="props.stock.solo_unit"
                    >
                      {{ props.stock.solo_unit }}
                    </option>
                    <option
                      v-if="props.stock.org_unit"
                      :value="props.stock.org_unit"
                    >
                      {{ props.stock.org_unit }}
                    </option>
                  </select>
                </div>
              </div>
              <div class="flex flex-wrap -mx-3 mb-6">
                <div class="w-1/2 px-3">
                  <label
                    :class="{
                      'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2': true,
                      'text-red-500': !form.order_price,
                    }"
                    for="name"
                  >
                    *単価 (変更可)
                  </label>
                  <input
                    class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    id="s_name"
                    type="number"
                    placeholder=""
                    v-model="form.order_price"
                    @change="form.calc_price = form.order_price * form.quantity"
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
              <div class="flex flex-wrap -mx-3 mb-6">
                <div class="w-1/2 px-3">
                  <label
                    :class="{
                      'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2': true,
                      'text-red-500':
                        !form.order_stock_process_id ||
                        form.order_stock_process_id == '0',
                    }"
                    for="name"
                  >
                    工程
                  </label>
                  <select
                    name=""
                    id=""
                    :class="{
                      'appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500': true,
                    }"
                    v-model="form.order_stock_process_id"
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
                <div class="w-1/2 px-3">
                  <label
                    :class="{
                      'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2': true,
                    }"
                    for="s_name"
                  >
                    送料(※その他費用)
                  </label>
                  <input
                    class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    id="s_name"
                    type="number"
                    placeholder=""
                    v-model="form.postage"
                  />
                </div>
              </div>
              <div class="flex flex-wrap -mx-3 mb-6">
                <div class="w-1/2 px-3">
                  <label
                    :class="{
                      'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2': true,
                    }"
                    for="name"
                  >
                    想定格納場所(※発注点更新用)
                  </label>
                  <select
                    :class="{
                      'appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500': true,
                    }"
                    id="name"
                    v-model="form.stock_storage_id"
                  >
                    <option value="0">未選択</option>
                    <option
                      v-for="stock_storage in props.stock_storages"
                      :key="stock_storage.stock_storage_id"
                      :value="stock_storage.stock_storage_id"
                    >
                      {{
                        `${stock_storage.location_name}:${stock_storage.address}`
                      }}
                    </option>
                  </select>
                </div>
              </div>

              <div class="flex items-center justify-center sm:col-span-2">
                <button
                  @click="createInitialOrder"
                  class="inline-block rounded-lg bg-red-500 px-8 py-3 text-center font-semibold text-white outline-none ring-red-300 transition duration-100 hover:bg-red-600 focus-visible:ring active:bg-red-700 text-xs"
                >
                  登録
                </button>
              </div>
            </div>
            <p v-else>
              手配先が設定されていません。先に手配先を登録してください。<br />
              リードタイムは取引に応じて自動で設定されるため、厳格に設定する必要はありません。
            </p>

            <!-- 発注履歴を表示 -->
            <details id="initial_order_details" class="mt-8">
              <summary
                @click="getInitialOrders"
                class="cursor-pointer text-blue-500"
              >
                発注履歴を表示
              </summary>
              <div class="mt-2">
                <table id="initial_order_table" class="min-w-full bg-white">
                  <thead>
                    <tr>
                      <th class="px-4 py-4 text-gray-700">ステータス</th>
                      <th class="px-4 py-4 text-gray-700">数量</th>
                      <th class="px-4 py-4 text-gray-700">価格</th>
                      <th class="px-4 py-4 text-gray-700">金額</th>
                      <th class="px-4 py-4 text-gray-700">発注依頼者</th>
                      <th class="px-4 py-4 text-gray-700">発注日</th>
                      <th class="px-4 py-4 text-gray-700">納品日</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="order in initial_orders" :key="order.id">
                      <td
                        class="px-4 py-4 text-center font-bold"
                        :class="{
                          'text-green-500': order.receive_flg,
                          'text-red-500': !order.receive_flg,
                        }"
                      >
                        {{ order.receive_flg ? "済" : "未" }}
                      </td>
                      <td class="px-4 py-4 text-gray-500">
                        {{ order.quantity }}
                      </td>
                      <td class="px-4 py-4 text-gray-500">
                        {{ order.price.toLocaleString() }}
                      </td>
                      <td class="px-4 py-4 text-gray-500">
                        {{ order.calc_price.toLocaleString() }}
                      </td>
                      <td class="px-4 py-4 text-gray-500">
                        {{ order.order_user }}
                      </td>
                      <td class="px-4 py-4 text-gray-500">
                        {{
                          new Date(order.order_date).toLocaleDateString("ja-JP")
                        }}
                      </td>
                      <td class="px-4 py-4 text-gray-500">
                        {{
                          new Date(order.delivery_date).toLocaleDateString(
                            "ja-JP"
                          )
                        }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </details>
          </div>
          <!-- 手配先設定 -->
          <div class="mt-8 bg-gray-100 p-4">
            <h3 class="text-lg font-bold dark:text-white mb-2">手配先設定</h3>

            <div class="flex flex-wrap -mx-3 mb-6">
              <div class="w-1/2 px-3">
                <label
                  :class="{
                    'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2': true,
                    'text-red-500': !form.stock_supplier_supplier_id,
                  }"
                  for="name"
                >
                  *手配先
                </label>
                <select
                  :class="{
                    'appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500': true,
                  }"
                  id="name"
                  v-model="form.stock_supplier_supplier_id"
                >
                  <option value="">未選択</option>
                  <option
                    v-for="supplier in props.suppliers"
                    :key="supplier.id"
                    :value="supplier.id"
                  >
                    {{
                      supplier.supplier_no != "" || supplier.supplier_no != null
                        ? `${supplier.supplier_no} : ${supplier.name}`
                        : supplier.name
                    }}
                  </option>
                </select>
              </div>
              <div class="w-1/2 px-3">
                <label
                  :class="{
                    'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2': true,
                    'text-red-500': !form.stock_supplier_lead_time,
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
                  v-model="form.stock_supplier_lead_time"
                />
              </div>
            </div>

            <div class="flex items-center justify-center sm:col-span-2">
              <button
                @click.prevent="createStockSupplier"
                class="inline-block rounded-lg bg-gray-500 px-8 py-3 text-center font-semibold text-white outline-none ring-gray-300 transition duration-100 hover:bg-gray-600 focus-visible:ring active:bg-gray-700 text-xs"
              >
                登録
              </button>
            </div>

            <hr class="my-8" />

            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
              <table
                class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
              >
                <thead
                  class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
                >
                  <tr>
                    <th scope="col" class="px-6 py-3">手配先</th>
                    <th scope="col" class="px-6 py-3">リードタイム</th>
                    <th scope="col" class="px-6 py-3">送料</th>
                    <th scope="col" class="px-6 py-3"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200"
                    v-for="stock_supplier in props.stock_suppliers"
                    :key="stock_supplier.id"
                  >
                    <td
                      scope="row"
                      class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                    >
                      {{ stock_supplier.name }}
                    </td>
                    <td class="px-6 py-4 w-48">
                      <input
                        type="number"
                        name=""
                        id=""
                        v-model="stock_supplier.lead_time"
                        class="text-center appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      />
                    </td>
                    <td class="px-6 py-4 w-48">
                      <input
                        type="number"
                        name=""
                        id=""
                        v-model="stock_supplier.postage"
                        class="text-center appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      />
                    </td>
                    <td class="py-4 flex items-center">
                      <button
                        @click="updateStockSupplier('save', stock_supplier)"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded text-sm mr-6 whitespace-nowrap"
                      >
                        保存
                      </button>
                      <i
                        @click="updateStockSupplier('delete', stock_supplier)"
                        class="text-red-500 fas fa-trash-alt"
                      ></i>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- 格納先 -->
          <div class="mt-8 bg-gray-100 p-4">
            <h3 class="text-lg font-bold dark:text-white mb-2">格納先設定</h3>

            <form action="" class="mb-6">
              <div class="flex flex-wrap -mx-3 mb-1">
                <div class="w-1/3 px-3">
                  <label
                    :class="{
                      'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2': true,
                      'text-red-500': !form.location_id,
                    }"
                    for="name"
                  >
                    *倉庫
                  </label>
                  <select
                    class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    v-model="form.location_id"
                    @change="handleLocation($event.target.value)"
                  >
                    <option value="0">未選択</option>
                    <option
                      v-for="location in props.locations"
                      :key="location.id"
                      :value="location.id"
                    >
                      {{ location.name }}
                    </option>
                  </select>
                </div>

                <div class="w-1/3 px-3">
                  <label
                    :class="{
                      'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2': true,
                      'text-red-500': !form.storage_address_id,
                    }"
                  >
                    *アドレス
                  </label>
                  <select
                    class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    v-model="form.storage_address_id"
                  >
                    <option value="0">未選択</option>
                    <option
                      v-for="address in select_storage_addresses"
                      :key="address.id"
                      :value="address.id"
                    >
                      {{ address.address }}
                    </option>
                  </select>
                </div>
                <div class="w-1/3 px-3">
                  <label
                    :class="{
                      'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2': true,
                      'text-red-500': !form.stock_storage_quantity,
                    }"
                  >
                    *数量
                  </label>
                  <input
                    class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    type="number"
                    v-model="form.stock_storage_quantity"
                  />
                </div>
              </div>

              <div class="flex justify-center">
                <button
                  @click="createStockStorage"
                  class="inline-block rounded-lg bg-gray-500 px-8 py-3 text-center font-semibold text-white outline-none ring-gray-300 transition duration-100 hover:bg-gray-600 focus-visible:ring active:bg-gray-700 text-xs"
                >
                  登録
                </button>
              </div>
            </form>

            <hr class="my-8" />

            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
              <table
                class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
              >
                <thead
                  class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
                >
                  <tr>
                    <th scope="col" class="px-6 py-3">倉庫</th>
                    <th scope="col" class="px-6 py-3">アドレス</th>
                    <th scope="col" class="px-6 py-3">個数</th>
                    <th scope="col" class="px-6 py-3">発注点</th>
                    <th scope="col" class="px-6 py-3"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200"
                    v-for="stock_storage in props.stock_storages"
                    :key="stock_storage.stock_storage_id"
                  >
                    <td
                      scope="row"
                      class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                    >
                      {{ stock_storage.location_name }}
                    </td>
                    <td class="px-6 py-4">{{ stock_storage.address }}</td>
                    <td class="px-6 py-4 w-48">
                      <input
                        type="number"
                        name=""
                        id=""
                        v-model="stock_storage.quantity"
                        class="text-center appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      />
                    </td>
                                        <td class="px-6 py-4 w-48">
                      <input
                        type="number"
                        name=""
                        id=""
                        v-model="stock_storage.reorder_point"
                        class="text-center appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      />
                    </td>
                    <td class="py-4 flex items-center">
                      <button
                        @click="updateStockStorage('save', stock_storage)"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded text-sm mr-6 whitespace-nowrap"
                      >
                        保存
                      </button>
                      <i
                        @click="updateStockStorage('delete', stock_storage)"
                        class="text-red-500 fas fa-trash-alt"
                      ></i>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- 弁場依頼物品設定 -->
          <div class="mt-8 bg-gray-100 p-4">
            <h3 class="text-lg font-bold dark:text-white mb-2">
              現場依頼物品設定
            </h3>

            <div v-if="stock.stock_request_id">
              <!-- 番号・名前も表示 -->
              <div class="flex justify-between">
                <div class="w-1/2 pr-3">
                  <label
                    :class="{
                      'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2': true,
                    }"
                  >
                    表示名
                  </label>
                  <input
                    class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    type="text"
                    v-model="form.alias"
                    @change="updateStockRequest('alias')"
                  />
                </div>
                <div class="w-1/2 pl-3">
                  <label
                    :class="{
                      'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2': true,
                    }"
                  >
                    表示順
                  </label>
                  <input
                    class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    type="number"
                    v-model="form.orderNumber"
                    @change="updateStockRequest('orderNumber')"
                  />
                </div>
              </div>

              <button
                @click="toggleStockRequest"
                class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-6 px-4 rounded"
              >
                設定済
              </button>
            </div>

            <button
              v-else
              @click="toggleStockRequest"
              class="w-full bg-gray-500 hover:bg-gray-700 text-white font-bold py-6 px-4 rounded"
            >
              未設定
            </button>
          </div>
        </div>

        <div id="right_container" class="w-2/5 px-4">
          <!-- 画像 -->
          <img
            class="mb-8"
            :src="
              stock.img_path && stock.img_path.includes('https://')
                ? stock.img_path
                : 'https://akioka.cloud/' + stock.img_path
            "
            alt=""
          />

          <form class="w-full mx-auto">
            <div class="flex flex-wrap -mx-3 mb-6">
              <div class="w-full px-3">
                <label
                  :class="{
                    'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2': true,
                    'text-red-500': !form.name,
                  }"
                  for="name"
                >
                  ID
                </label>
                <input
                  class="appearance-none block w-full bg-gray-300 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500 pointer-events-none"
                  id="name"
                  type="number"
                  placeholder=""
                  v-model="form.stock_id"
                />
              </div>
            </div>
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
                  v-model="form.classification_id"
                  @change="handleClassification"
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
                  class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                  for="grid-city"
                >
                  工程 (※発注依頼時デフォルト値)
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
            <div class="flex flex-wrap -mx-3 mb-6">
              <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                <label
                  class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                  for="grid-city"
                >
                  表示フラグ
                </label>
                <select
                  :class="{
                    'appearance-none block w-full bg-gray-200 text-green-500 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500 font-bold': true,
                    'text-red-500': form.del_flg,
                  }"
                  id="grid-city"
                  type="text"
                  placeholder=""
                  v-model="form.del_flg"
                >
                  <option class="text-green-500" value="0">表示</option>
                  <option class="text-red-500" value="1">非表示</option>
                </select>
              </div>
            </div>

            <div class="flex items-center justify-center sm:col-span-2">
              <button
                @click.prevent="editStock"
                class="mt-8 inline-block rounded-lg bg-green-500 px-8 py-3 text-center text-sm font-semibold text-white outline-none ring-green-300 transition duration-100 hover:bg-green-600 focus-visible:ring active:bg-green-700 md:text-base"
              >
                変更
              </button>
            </div>
          </form>
        </div>
      </div>
    </template>
  </MainLayout>
</template>
<style scoped lang="scss">
#left_container {
  padding: 0 2rem;

  & #initial_order_details {
    width: 100%;
    overflow-x: scroll;
    & #initial_order_table {
      & td,
      th {
        white-space: nowrap;
        text-align: center;
      }
    }
  }
}
#right_container {
  // & .img_content {
  //   height: 30%;
  //   width: 100%;

  //   & img {
  //     height: 100%;
  //     width: 100%;
  //     object-fit: contain;
  //   }
  // }
}
</style>