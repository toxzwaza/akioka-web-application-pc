<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { onMounted, reactive, ref } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";
import { Chart, registerables } from "chart.js";
import MainTitle from "@/Components/Title/MainTitle.vue";

Chart.register(...registerables);

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
  stock_price_archive: Array,
  stock_supplier_prices: Array,
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
  tax_included: null,
  approval_supplier_name: null,
  desc_memo: null, //備考
  show_price_on_invoice: null,

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
  orderUnit: null,

  // 手配先価格用
  price_stock_supplier_id: null,
  price_value: null,
  price_start_date: null,
  price_end_date: null,
});
const updateStockRequest = (flg) => {
  const update_data = {
    stock_id: form.stock_id,
  };

  if (flg === "alias") {
    update_data.alias = form.alias;
  } else if (flg === "orderNumber") {
    update_data.orderNumber = form.orderNumber;
  } else if (flg === "unit") {
    update_data.orderUnit = form.orderUnit;
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


const changeStockSupplierMainFlg = (stock_supplier_id) => {
  console.log(stock_supplier_id)

  axios.post(route('stock.stock_supplier.change.main_flg'), {
    stock_supplier_id: stock_supplier_id
  })
  .then(res => {
    console.log(res.data)
    if(res.data.status){
      alert('メイン発注先を変更しました。')
      window.location.reload()
    }
  })
  .catch(error => {
    console.log(error)
  })
}
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
          postage: stock_supplier.postage,
        })
        .then((res) => {
          console.log(res.data);
          if (res.data.status) {
            alert("更新が完了しました");
            window.location.reload();
          } else {
            alert(res.data.msg);
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
          },
        })
        .then((res) => {
          console.log(res.data);
          if (res.data.status) {
            alert("削除が完了しました");
            window.location.reload();
          } else {
            alert(res.data.msg);
          }
        })
        .catch((error) => {
          console.log(error);
        });
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
          reorder_point: stock_storage.reorder_point,
        })
        .then((res) => {
          console.log(res.data);
          if (res.data.status) {
            alert("更新が完了しました");
            window.location.reload();
          } else {
            alert(res.data.msg);
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
          },
        })
        .then((res) => {
          console.log(res.data);
          if (res.data.status) {
            alert("削除が完了しました");
            window.location.reload();
          } else {
            alert(res.data.msg);
          }
        })
        .catch((error) => {
          console.log(error);
        });
      break;
  }
};

// 日付フォーマット用のヘルパー関数（タイムゾーンの影響を受けないように文字列処理）
const formatDate = (date) => {
  if (!date) return '';
  
  // 既に"YYYY-MM-DD"形式の場合はそのまま返す
  if (typeof date === 'string' && /^\d{4}-\d{2}-\d{2}$/.test(date)) {
    return date;
  }
  
  // オブジェクトの場合（Laravelからの日付オブジェクト）
  if (typeof date === 'object' && date.date) {
    return date.date.split(' ')[0]; // "2025-11-04 00:00:00.000000" -> "2025-11-04"
  }
  
  // その他の形式の場合は文字列に変換して最初の10文字を取得
  const dateStr = String(date);
  if (dateStr.length >= 10) {
    return dateStr.substring(0, 10);
  }
  
  return '';
};

// 手配先価格関連
const createStockSupplierPrice = () => {
  if (
    !form.price_stock_supplier_id ||
    !form.price_value ||
    !form.price_start_date
  ) {
    return alert("必須項目が入力されていません。");
  }

  // 日付を確実に文字列として送信
  const startDate = String(form.price_start_date);
  const endDate = form.price_end_date ? String(form.price_end_date) : null;

  axios
    .post(route("stock.stock_supplier_price.store"), {
      stock_id: form.stock_id,
      stock_supplier_id: form.price_stock_supplier_id,
      price: form.price_value,
      start_date: startDate,
      end_date: endDate,
    })
    .then((res) => {
      if (res.data.status) {
        alert("価格を登録しました。");
        window.location.reload();
      } else {
        alert(res.data.message || "登録に失敗しました。");
      }
    })
    .catch((error) => {
      console.log(error);
      alert("エラーが発生しました。");
    });
};

const updateStockSupplierPrice = (flg, price) => {
  console.log(flg, price);

  switch (flg) {
    case "save":
      // 日付を確実に文字列として送信
      const startDate = String(price.start_date);
      const endDate = price.end_date ? String(price.end_date) : null;

      axios
        .post(route("stock.stock_supplier_price.update"), {
          id: price.id,
          price: price.price,
          start_date: startDate,
          end_date: endDate,
          active_flg: price.active_flg,
        })
        .then((res) => {
          if (res.data.status) {
            alert("更新が完了しました");
            window.location.reload();
          } else {
            alert(res.data.message);
          }
        })
        .catch((error) => {
          console.log(error);
          alert("エラーが発生しました。");
        });
      break;

    case "delete":
      if (!confirm("本当に削除しますか？")) return;
      axios
        .delete(route("stock.stock_supplier_price.delete"), {
          params: {
            id: price.id,
          },
        })
        .then((res) => {
          if (res.data.status) {
            alert("削除が完了しました");
            window.location.reload();
          } else {
            alert(res.data.message);
          }
        })
        .catch((error) => {
          console.log(error);
          alert("エラーが発生しました。");
        });
      break;

    case "toggle":
      axios
        .post(route("stock.stock_supplier_price.toggle_active"), {
          id: price.id,
        })
        .then((res) => {
          if (res.data.status) {
            alert("有効フラグを更新しました");
            window.location.reload();
          } else {
            alert(res.data.message);
          }
        })
        .catch((error) => {
          console.log(error);
          alert("エラーが発生しました。");
        });
      break;
  }
};

const chartRef = ref(null);
let priceChart = null;

const initPriceChart = () => {
  if (priceChart) {
    priceChart.destroy();
  }

  const ctx = chartRef.value.getContext("2d");
  const labels = props.stock_price_archive.map((item) => {
    const date = new Date(item.created_at);
    return date.toLocaleDateString("ja-JP");
  });
  const prices = props.stock_price_archive.map((item) => item.price);

  priceChart = new Chart(ctx, {
    type: "line",
    data: {
      labels: labels,
      datasets: [
        {
          label: "価格推移",
          data: prices,
          borderColor: "#3498db",
          backgroundColor: "rgba(52, 152, 219, 0.2)",
          tension: 0.4,
          fill: true,
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        title: {
          display: true,
          text: "価格推移グラフ",
          font: {
            size: 16,
          },
        },
        tooltip: {
          callbacks: {
            label: function (context) {
              return `価格: ${context.raw.toLocaleString()}円`;
            },
          },
        },
      },
      scales: {
        y: {
          beginAtZero: false,
          ticks: {
            callback: function (value) {
              return value.toLocaleString() + "円";
            },
          },
        },
      },
    },
  });
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
  form.tax_included = props.stock.tax_included;
  form.approval_supplier_name = props.stock.approval_supplier_name;
  form.desc_memo  = props.stock.desc_memo
  form.show_price_on_invoice = props.stock.show_price_on_invoice

  if (props.stock_suppliers && props.stock_suppliers.length > 0) {
    form.supplier_id = props.stock_suppliers[0].id;
    form.lead_time = props.stock_suppliers[0].lead_time;
  }

  if (props.stock_storages && props.stock_storages.length == 1) {
    form.stock_storage_id = props.stock_storages[0].stock_storage_id;
  }

  form.alias = props.stock.alias;
  form.orderNumber = props.stock.orderNumber;
  form.orderUnit = props.stock.orderUnit;

  form.order_stock_process_id = form.stock_process_id
    ? form.stock_process_id
    : 0;

  if (props.stock_price_archive && props.stock_price_archive.length > 0) {
    initPriceChart();
  }
});
</script>
<template>
  <MainLayout :title="'在庫詳細'">
    <template #content>
      <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
          <!-- ヘッダーセクション -->
          <div class="mb-8 space-y-4">
            <MainTitle
              :top="'在庫詳細'"
              :sub="'物品データ閲覧・変更及び手配先や格納先の紐づけを行います。'"
            />
            
            <!-- アクションボタン -->
            <div class="flex items-center gap-4">
              <Link
                :href="route('stock.stocks.create', { stock_id: props.stock.id })"
                class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg shadow-blue-500/30 transition-all duration-300 hover:scale-105 hover:shadow-xl"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                複製して在庫追加
              </Link>
            </div>
          </div>

          <!-- iframeセクション -->
          <div
            v-if="props.stock.url && props.stock.url.includes('askul')"
            class="mb-8 bg-white rounded-2xl shadow-xl overflow-hidden"
          >
            <iframe
              id="stock_iframe"
              :src="props.stock.url"
              frameborder="0"
              class="w-full h-[60vh]"
            ></iframe>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
            <!-- 左カラム -->
            <div id="left_container" class="lg:col-span-2 space-y-6">
          <!-- 発注登録 -->
          <!-- <div class="bg-red-50 p-4"> -->
            <!-- <h3 class="text-lg font-bold dark:text-white mb-2">発注依頼登録</h3> -->
            <!-- <div v-if="props.stock_suppliers.length > 0">
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
            </div> -->


            <!-- 発注履歴を表示 -->
            <!-- <details id="initial_order_details" class="mt-8">
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
            </details> -->
          <!-- </div> -->

              <!-- 手配先設定 -->
              <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 p-6">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                  <div class="p-2 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                  </div>
                  <h3 class="text-xl font-bold text-gray-800">手配先設定</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                  <div>
                    <label
                      :class="{
                        'block text-sm font-semibold mb-2 transition-colors': true,
                        'text-red-500': !form.stock_supplier_supplier_id,
                        'text-gray-700': form.stock_supplier_supplier_id
                      }"
                    >
                      *手配先
                    </label>
                    <select
                      v-model="form.stock_supplier_supplier_id"
                      class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200 outline-none hover:border-gray-300"
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
                  <div>
                    <label
                      :class="{
                        'block text-sm font-semibold mb-2 transition-colors': true,
                        'text-red-500': !form.stock_supplier_lead_time,
                        'text-gray-700': form.stock_supplier_lead_time
                      }"
                    >
                      *リードタイム
                    </label>
                    <input
                      type="number"
                      v-model="form.stock_supplier_lead_time"
                      class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200 outline-none hover:border-gray-300"
                      placeholder="日数を入力"
                    />
                  </div>
                </div>

                <div class="flex justify-center mb-6">
                  <button
                    @click.prevent="createStockSupplier"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-semibold py-3 px-8 rounded-xl shadow-lg shadow-purple-500/30 transition-all duration-300 hover:scale-105"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    登録
                  </button>
                </div>

                <div class="h-px bg-gradient-to-r from-transparent via-gray-300 to-transparent my-6"></div>

                <div class="overflow-x-auto">
                  <table class="w-full min-w-max">
                    <thead>
                      <tr class="border-b-2 border-gray-200">
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">手配先</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">リードタイム</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">送料</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">操作</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                      <tr
                        v-for="stock_supplier in props.stock_suppliers"
                        :key="stock_supplier.id"
                        class="hover:bg-purple-50 transition-colors duration-200"
                      >
                        <td class="px-3 py-3">
                          <div class="flex items-center gap-2">
                            <button
                              @click="updateStockSupplier('delete', stock_supplier)"
                              class="text-red-500 hover:text-red-700 hover:scale-110 transition-all duration-200 flex-shrink-0"
                            >
                              <i class="fas fa-trash-alt"></i>
                            </button>
                            <div class="min-w-0">
                              <span
                                v-if="stock_supplier.main_flg"
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 mb-1 whitespace-nowrap"
                              >
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1"></span>
                                適用中
                              </span>
                              <p class="font-medium text-gray-900 text-sm whitespace-nowrap">{{ stock_supplier.name }}</p>
                            </div>
                          </div>
                        </td>
                        <td class="px-3 py-3">
                          <input
                            type="number"
                            v-model="stock_supplier.lead_time"
                            class="w-20 px-2 py-2 text-center bg-gray-50 border-2 border-gray-200 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-100 transition-all duration-200 outline-none text-sm"
                          />
                        </td>
                        <td class="px-3 py-3">
                          <input
                            type="number"
                            v-model="stock_supplier.postage"
                            class="w-20 px-2 py-2 text-center bg-gray-50 border-2 border-gray-200 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-100 transition-all duration-200 outline-none text-sm"
                          />
                        </td>
                        <td class="px-3 py-3">
                          <div class="flex flex-col gap-2">
                            <button
                              @click="updateStockSupplier('save', stock_supplier)"
                              class="inline-flex items-center justify-center gap-1 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-3 rounded-lg text-xs transition-all duration-200 hover:scale-105 whitespace-nowrap"
                            >
                              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                              </svg>
                              保存
                            </button>
                            <button
                              v-if="!stock_supplier.main_flg"
                              @click="changeStockSupplierMainFlg(stock_supplier.stock_supplier_id)"
                              class="inline-flex items-center justify-center gap-1 bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-3 rounded-lg text-xs transition-all duration-200 hover:scale-105 whitespace-nowrap"
                            >
                              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                              </svg>
                              適用変更
                            </button>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- 手配先価格設定 -->
              <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 p-6">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                  <div class="p-2 bg-gradient-to-br from-rose-500 to-pink-500 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </div>
                  <h3 class="text-xl font-bold text-gray-800">手配先価格設定</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                  <div>
                    <label
                      :class="{
                        'block text-sm font-semibold mb-2 transition-colors': true,
                        'text-red-500': !form.price_stock_supplier_id,
                        'text-gray-700': form.price_stock_supplier_id
                      }"
                    >
                      *手配先
                    </label>
                    <select
                      v-model="form.price_stock_supplier_id"
                      class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-rose-500 focus:ring-4 focus:ring-rose-100 transition-all duration-200 outline-none hover:border-gray-300"
                    >
                      <option value="">未選択</option>
                      <option
                        v-for="stock_supplier in props.stock_suppliers"
                        :key="stock_supplier.stock_supplier_id"
                        :value="stock_supplier.stock_supplier_id"
                      >
                        {{ stock_supplier.name }}
                      </option>
                    </select>
                  </div>
                  <div>
                    <label
                      :class="{
                        'block text-sm font-semibold mb-2 transition-colors': true,
                        'text-red-500': !form.price_value,
                        'text-gray-700': form.price_value
                      }"
                    >
                      *価格
                    </label>
                    <input
                      type="number"
                      v-model="form.price_value"
                      class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-rose-500 focus:ring-4 focus:ring-rose-100 transition-all duration-200 outline-none hover:border-gray-300"
                      placeholder="価格を入力"
                      step="0.01"
                    />
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                  <div>
                    <label
                      :class="{
                        'block text-sm font-semibold mb-2 transition-colors': true,
                        'text-red-500': !form.price_start_date,
                        'text-gray-700': form.price_start_date
                      }"
                    >
                      *適用開始日
                    </label>
                    <input
                      type="date"
                      v-model="form.price_start_date"
                      class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-rose-500 focus:ring-4 focus:ring-rose-100 transition-all duration-200 outline-none hover:border-gray-300"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                      適用終了日
                      <span class="ml-1 text-xs font-normal text-gray-500">※未入力の場合は無期限</span>
                    </label>
                    <input
                      type="date"
                      v-model="form.price_end_date"
                      class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-rose-500 focus:ring-4 focus:ring-rose-100 transition-all duration-200 outline-none hover:border-gray-300"
                    />
                  </div>
                </div>

                <div class="flex justify-center mb-6">
                  <button
                    @click.prevent="createStockSupplierPrice"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-rose-500 to-pink-500 hover:from-rose-600 hover:to-pink-600 text-white font-semibold py-3 px-8 rounded-xl shadow-lg shadow-rose-500/30 transition-all duration-300 hover:scale-105"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    登録
                  </button>
                </div>

                <div class="h-px bg-gradient-to-r from-transparent via-gray-300 to-transparent my-6"></div>

                <div class="overflow-x-auto">
                  <table class="w-full min-w-max">
                    <thead>
                      <tr class="border-b-2 border-gray-200">
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">手配先</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">価格</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">適用開始日</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">適用終了日</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">状態</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">操作</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                      <tr
                        v-for="price in props.stock_supplier_prices"
                        :key="price.id"
                        class="hover:bg-rose-50 transition-colors duration-200"
                      >
                        <td class="px-3 py-3 font-medium text-gray-900 text-sm whitespace-nowrap">
                          {{ price.stock_supplier?.supplier?.name || '-' }}
                        </td>
                        <td class="px-3 py-3">
                          <input
                            type="number"
                            v-model="price.price"
                            class="w-24 px-2 py-2 text-center bg-gray-50 border-2 border-gray-200 rounded-lg focus:border-rose-500 focus:ring-2 focus:ring-rose-100 transition-all duration-200 outline-none text-sm"
                            step="0.01"
                          />
                        </td>
                        <td class="px-3 py-3">
                          <input
                            type="date"
                            :value="formatDate(price.start_date)"
                            @input="price.start_date = $event.target.value"
                            class="w-36 px-2 py-2 text-center bg-gray-50 border-2 border-gray-200 rounded-lg focus:border-rose-500 focus:ring-2 focus:ring-rose-100 transition-all duration-200 outline-none text-xs"
                          />
                        </td>
                        <td class="px-3 py-3">
                          <input
                            type="date"
                            :value="formatDate(price.end_date)"
                            @input="price.end_date = $event.target.value"
                            class="w-36 px-2 py-2 text-center bg-gray-50 border-2 border-gray-200 rounded-lg focus:border-rose-500 focus:ring-2 focus:ring-rose-100 transition-all duration-200 outline-none text-xs"
                          />
                        </td>
                        <td class="px-3 py-3">
                          <button
                            v-if="price.active_flg !== 2"
                            @click="updateStockSupplierPrice('toggle', price)"
                            :class="{
                              'px-3 py-1 rounded-full text-xs font-semibold transition-all duration-200 whitespace-nowrap': true,
                              'bg-green-100 text-green-800 hover:bg-green-200': price.active_flg === 1,
                              'bg-gray-100 text-gray-800 hover:bg-gray-200': price.active_flg === 0
                            }"
                          >
                            {{ price.active_flg === 1 ? '適用待ち' : '適用済み' }}
                          </button>
                          <span
                            v-else
                            class="px-3 py-1 rounded-full text-xs font-semibold transition-all duration-200 whitespace-nowrap bg-blue-100 text-blue-800"
                          >
                            適用済み
                          </span>
                        </td>
                        <td class="px-3 py-3">
                          <div class="flex flex-col gap-2">
                            <button
                              @click="updateStockSupplierPrice('save', price)"
                              class="inline-flex items-center justify-center gap-1 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-3 rounded-lg text-xs transition-all duration-200 hover:scale-105 whitespace-nowrap"
                            >
                              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                              </svg>
                              保存
                            </button>
                            <button
                              @click="updateStockSupplierPrice('delete', price)"
                              class="text-red-500 hover:text-red-700 hover:scale-110 transition-all duration-200"
                            >
                              <i class="fas fa-trash-alt"></i>
                            </button>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- 格納先設定 -->
              <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 p-6">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                  <div class="p-2 bg-gradient-to-br from-indigo-500 to-blue-500 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                  </div>
                  <h3 class="text-xl font-bold text-gray-800">格納先設定</h3>
                </div>

                <form action="" class="mb-6">
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                      <label
                        :class="{
                          'block text-sm font-semibold mb-2 transition-colors': true,
                          'text-red-500': !form.location_id,
                          'text-gray-700': form.location_id
                        }"
                      >
                        *倉庫
                      </label>
                      <select
                        v-model="form.location_id"
                        @change="handleLocation($event.target.value)"
                        class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 outline-none hover:border-gray-300"
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

                    <div>
                      <label
                        :class="{
                          'block text-sm font-semibold mb-2 transition-colors': true,
                          'text-red-500': !form.storage_address_id,
                          'text-gray-700': form.storage_address_id
                        }"
                      >
                        *アドレス
                      </label>
                      <select
                        v-model="form.storage_address_id"
                        class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 outline-none hover:border-gray-300"
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
                    
                    <div>
                      <label
                        :class="{
                          'block text-sm font-semibold mb-2 transition-colors': true,
                          'text-red-500': !form.stock_storage_quantity,
                          'text-gray-700': form.stock_storage_quantity
                        }"
                      >
                        *数量
                      </label>
                      <input
                        type="number"
                        v-model="form.stock_storage_quantity"
                        class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 outline-none hover:border-gray-300"
                        placeholder="数量を入力"
                      />
                    </div>
                  </div>

                  <div class="flex justify-center mb-6">
                    <button
                      @click="createStockStorage"
                      class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-500 to-blue-500 hover:from-indigo-600 hover:to-blue-600 text-white font-semibold py-3 px-8 rounded-xl shadow-lg shadow-indigo-500/30 transition-all duration-300 hover:scale-105"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                      </svg>
                      登録
                    </button>
                  </div>
                </form>

                <div class="h-px bg-gradient-to-r from-transparent via-gray-300 to-transparent my-6"></div>

                <div class="overflow-x-auto">
                  <table class="w-full min-w-max">
                    <thead>
                      <tr class="border-b-2 border-gray-200">
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">倉庫</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">アドレス</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">個数</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">発注点</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">操作</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                      <tr
                        v-for="stock_storage in props.stock_storages"
                        :key="stock_storage.stock_storage_id"
                        class="hover:bg-indigo-50 transition-colors duration-200"
                      >
                        <td class="px-3 py-3 font-medium text-gray-900 text-sm whitespace-nowrap">
                          {{ stock_storage.location_name }}
                        </td>
                        <td class="px-3 py-3 text-gray-700 text-sm whitespace-nowrap">{{ stock_storage.address }}</td>
                        <td class="px-3 py-3">
                          <input
                            type="number"
                            v-model="stock_storage.quantity"
                            class="w-20 px-2 py-2 text-center bg-gray-50 border-2 border-gray-200 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all duration-200 outline-none text-sm"
                          />
                        </td>
                        <td class="px-3 py-3">
                          <input
                            type="number"
                            v-model="stock_storage.reorder_point"
                            class="w-20 px-2 py-2 text-center bg-gray-50 border-2 border-gray-200 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all duration-200 outline-none text-sm"
                          />
                        </td>
                        <td class="px-3 py-3">
                          <div class="flex flex-col gap-2">
                            <button
                              @click="updateStockStorage('save', stock_storage)"
                              class="inline-flex items-center justify-center gap-1 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-3 rounded-lg text-xs transition-all duration-200 hover:scale-105 whitespace-nowrap"
                            >
                              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                              </svg>
                              保存
                            </button>
                            <button
                              @click="updateStockStorage('delete', stock_storage)"
                              class="text-red-500 hover:text-red-700 hover:scale-110 transition-all duration-200 text-center"
                            >
                              <i class="fas fa-trash-alt"></i>
                            </button>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- 現場依頼物品設定 -->
              <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 p-6">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                  <div class="p-2 bg-gradient-to-br from-amber-500 to-orange-500 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                  </div>
                  <h3 class="text-xl font-bold text-gray-800">現場依頼物品設定</h3>
                </div>

                <div v-if="stock.stock_request_id" class="space-y-4">
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                      <label class="block text-sm font-semibold text-gray-700 mb-2">
                        表示名
                      </label>
                      <input
                        type="text"
                        v-model="form.alias"
                        @change="updateStockRequest('alias')"
                        class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-amber-500 focus:ring-4 focus:ring-amber-100 transition-all duration-200 outline-none hover:border-gray-300"
                        placeholder="表示名を入力"
                      />
                    </div>
                    <div>
                      <label class="block text-sm font-semibold text-gray-700 mb-2">
                        表示順
                      </label>
                      <input
                        type="number"
                        v-model="form.orderNumber"
                        @change="updateStockRequest('orderNumber')"
                        class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-amber-500 focus:ring-4 focus:ring-amber-100 transition-all duration-200 outline-none hover:border-gray-300"
                        placeholder="順番を入力"
                      />
                    </div>
                    <div>
                      <label class="block text-sm font-semibold text-gray-700 mb-2">
                        単位
                      </label>
                      <input
                        type="text"
                        v-model="form.orderUnit"
                        @change="updateStockRequest('unit')"
                        class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-amber-500 focus:ring-4 focus:ring-amber-100 transition-all duration-200 outline-none hover:border-gray-300"
                        placeholder="単位を入力"
                      />
                    </div>
                  </div>

                  <button
                    @click="toggleStockRequest"
                    class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-4 px-4 rounded-xl transition-all duration-300 hover:scale-102 shadow-lg shadow-blue-500/30"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    設定済
                  </button>
                </div>

                <button
                  v-else
                  @click="toggleStockRequest"
                  class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-gray-400 to-gray-500 hover:from-gray-500 hover:to-gray-600 text-white font-bold py-4 px-4 rounded-xl transition-all duration-300 hover:scale-102"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                  未設定
                </button>
              </div>
            </div>

            <!-- 右カラム -->
            <div id="right_container" class="lg:col-span-3 space-y-6">
              <!-- 画像カード -->
              <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <img
                  class="w-full h-80 object-contain p-6 bg-gradient-to-br from-gray-50 to-gray-100"
                  :src="
                    stock.img_path && stock.img_path.includes('https://')
                      ? stock.img_path
                      : 'https://akioka.cloud/' + stock.img_path
                  "
                  alt="商品画像"
                />
              </div>

              <!-- 在庫情報フォーム -->
              <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                  <div class="p-2 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                  </div>
                  <h3 class="text-xl font-bold text-gray-800">在庫基本情報</h3>
                </div>

                <form class="space-y-6">
                  <!-- ID -->
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                      ID
                    </label>
                    <input
                      type="number"
                      v-model="form.stock_id"
                      class="w-full px-4 py-3 bg-gray-100 border-2 border-gray-200 rounded-xl text-gray-500 cursor-not-allowed outline-none"
                      disabled
                    />
                  </div>

                  <!-- 品名 -->
                  <div>
                    <label
                      :class="{
                        'block text-sm font-semibold mb-2 transition-colors': true,
                        'text-red-500': !form.name,
                        'text-gray-700': form.name
                      }"
                    >
                      *品名
                    </label>
                    <input
                      type="text"
                      v-model="form.name"
                      class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-200 outline-none hover:border-gray-300"
                      placeholder="品名を入力"
                    />
                  </div>

                  <!-- 品番 -->
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                      品番
                    </label>
                    <input
                      type="text"
                      v-model="form.s_name"
                      class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-200 outline-none hover:border-gray-300"
                      placeholder="品番を入力"
                    />
                  </div>

                  <!-- JANコード -->
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                      JANコード
                    </label>
                    <input
                      type="text"
                      v-model="form.jan_code"
                      class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-200 outline-none hover:border-gray-300"
                      placeholder="JANコードを入力"
                    />
                  </div>

                  <!-- URL -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm font-semibold text-gray-700 mb-2">
                        画像URL
                      </label>
                      <input
                        type="text"
                        v-model="form.img_path"
                        class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-200 outline-none hover:border-gray-300"
                        placeholder="https://****"
                      />
                    </div>
                    <div>
                      <label class="block text-sm font-semibold text-gray-700 mb-2">
                        購買用URL
                      </label>
                      <input
                        type="text"
                        v-model="form.url"
                        class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-200 outline-none hover:border-gray-300"
                        placeholder="https://****"
                      />
                    </div>
                  </div>

                  <!-- 適確事業者番号 -->
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                      適確事業者番号
                    </label>
                    <input
                      type="text"
                      v-model="form.purchase_identification_number"
                      class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-200 outline-none hover:border-gray-300"
                      placeholder="適確事業者番号を入力"
                    />
                  </div>

                  <!-- 価格・税区分 -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label
                        :class="{
                          'block text-sm font-semibold mb-2 transition-colors': true,
                          'text-red-500': !form.price,
                          'text-gray-700': form.price
                        }"
                      >
                        *価格
                      </label>
                      <input
                        type="number"
                        v-model="form.price"
                        class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-200 outline-none hover:border-gray-300"
                        placeholder="価格を入力"
                      />
                    </div>
                    <div>
                      <label class="block text-sm font-semibold text-gray-700 mb-2">
                        *税区分
                      </label>
                      <select
                        v-model="form.tax_included"
                        class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-200 outline-none hover:border-gray-300"
                      >
                        <option value="0">税抜き</option>
                        <option value="1">税込み</option>
                      </select>
                    </div>
                  </div>

                  <!-- 価格推移グラフ -->
                  <div
                    v-if="props.stock_price_archive && props.stock_price_archive.length > 0"
                    class="bg-gradient-to-br from-blue-50 to-indigo-50 p-6 rounded-xl border-2 border-blue-100"
                  >
                    <canvas ref="chartRef"></canvas>
                  </div>

                  <!-- 単位 -->
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                      <label class="block text-sm font-semibold text-gray-700 mb-2">
                        単位1
                      </label>
                      <input
                        type="text"
                        v-model="form.solo_unit"
                        class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-200 outline-none hover:border-gray-300"
                        placeholder="個"
                      />
                    </div>
                    <div>
                      <label class="block text-sm font-semibold text-gray-700 mb-2">
                        単位2
                      </label>
                      <input
                        type="text"
                        v-model="form.org_unit"
                        class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-200 outline-none hover:border-gray-300"
                        placeholder="箱"
                      />
                    </div>
                    <div>
                      <label class="block text-sm font-semibold text-gray-700 mb-2">
                        換算値
                        <span class="ml-1 text-xs text-gray-500 font-normal">※納品時の数量登録</span>
                      </label>
                      <input
                        type="number"
                        v-model="form.quantity_per_org"
                        class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-200 outline-none hover:border-gray-300"
                        placeholder="換算値を入力"
                      />
                    </div>
                  </div>

                  <!-- カテゴリ・配送先・工程 -->
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                      <label
                        :class="{
                          'block text-sm font-semibold mb-2 transition-colors': true,
                          'text-red-500': !form.classification_id,
                          'text-gray-700': form.classification_id
                        }"
                      >
                        *備品カテゴリ
                      </label>
                      <select
                        v-model="form.classification_id"
                        @change="handleClassification"
                        class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-200 outline-none hover:border-gray-300"
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
                    <div>
                      <label class="block text-sm font-semibold text-gray-700 mb-2">
                        配送先
                      </label>
                      <input
                        type="text"
                        v-model="form.deli_location"
                        class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-200 outline-none hover:border-gray-300"
                        placeholder="配送先を入力"
                      />
                    </div>
                    <div>
                      <label class="block text-sm font-semibold text-gray-700 mb-2">
                        工程
                        <span class="ml-1 text-xs text-gray-500 font-normal">※発注依頼時デフォルト値</span>
                      </label>
                      <select
                        v-model="form.stock_process_id"
                        class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-200 outline-none hover:border-gray-300"
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

                  <!-- 稟議申請・備考・表示フラグ -->
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                      <label class="block text-sm font-semibold text-gray-700 mb-2">
                        稟議申請時発注先名
                      </label>
                      <input
                        type="text"
                        v-model="form.approval_supplier_name"
                        class="w-full px-4 py-3 bg-gray-100 border-2 border-gray-200 rounded-xl text-gray-500 cursor-not-allowed outline-none"
                        disabled
                      />
                    </div>
                    <div>
                      <label class="block text-sm font-semibold text-gray-700 mb-2">
                        備考
                      </label>
                      <input
                        type="text"
                        v-model="form.desc_memo"
                        class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-200 outline-none hover:border-gray-300"
                        placeholder="備考を入力"
                      />
                    </div>
                    <div>
                      <label class="block text-sm font-semibold text-gray-700 mb-2">
                        納品書金額表示
                      </label>
                      <select
                        v-model="form.show_price_on_invoice"
                        :class="{
                          'w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-4 transition-all duration-200 outline-none hover:border-gray-300 font-semibold': true,
                          'text-green-600 focus:border-green-500 focus:ring-green-100': !form.show_price_on_invoice,
                          'text-red-600 focus:border-red-500 focus:ring-red-100': form.show_price_on_invoice
                        }"
                      >
                        <option class="text-green-600" value="0">表示</option>
                        <option class="text-red-600" value="1">非表示</option>
                      </select>
                    </div>
                  </div>
                  <div class="w-full">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                      ステータス
                    </label>
                    <select
                      v-model="form.del_flg"
                      :class="{
                        'text-center w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-4 transition-all duration-200 outline-none hover:border-gray-300 font-semibold': true,
                        'text-green-600 focus:border-green-500 focus:ring-green-100': !form.del_flg,
                        'text-red-600 focus:border-red-500 focus:ring-red-100': form.del_flg
                      }"
                    >
                      <option class="text-green-600" value="0">有効</option>
                      <option class="text-red-600" value="1">無効</option>
                    </select>
                  </div>

                  <!-- 変更ボタン -->
                  <div class="flex justify-center pt-4">
                    <button
                      @click.prevent="editStock"
                      class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-bold py-4 px-12 rounded-xl shadow-lg shadow-emerald-500/30 transition-all duration-300 hover:scale-105 text-lg"
                    >
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                      変更を保存
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </MainLayout>
</template>
<style scoped>
/* カスタムスクロールバー */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: #cbd5e0;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: #a0aec0;
}

/* ホバーアニメーション */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* フェードインアニメーション */
#left_container > div,
#right_container > div {
  animation: fadeIn 0.5s ease-out;
}

/* スムーズなトランジション */
* {
  transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* フォーカス時のリング効果を強化 */
input:focus,
select:focus,
textarea:focus {
  outline: none;
}
</style>