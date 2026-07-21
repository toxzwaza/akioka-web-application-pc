<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { onMounted, reactive, ref } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";
import { Chart, registerables } from "chart.js";
import EditAlias from "@/Components/Stock/EditAlias.vue";
import PageHeader from "@/Components/UI/PageHeader.vue";
import SectionCard from "@/Components/UI/SectionCard.vue";
import Button from "@/Components/UI/Button.vue";
import FormField from "@/Components/UI/FormField.vue";
import Table from "@/Components/UI/Table.vue";
import TableHeaderCell from "@/Components/UI/TableHeaderCell.vue";
import TableRow from "@/Components/UI/TableRow.vue";
import TableDataCell from "@/Components/UI/TableDataCell.vue";
import Badge from "@/Components/UI/Badge.vue";
import Icon from "@/Components/UI/Icon.vue";

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
  aliases: Array,
  prev_stock_id: [Number, String],
  next_stock_id: [Number, String],
  nav_filter: Object,
});

// 一覧の絞り込み条件を保持したまま前後の在庫詳細へ遷移
const goToStock = (stock_id) => {
  if (!stock_id) return;
  const params = new URLSearchParams();
  if (props.nav_filter?.keyword) params.append("keyword", props.nav_filter.keyword);
  if (props.nav_filter?.supplier_name)
    params.append("supplier_name", props.nav_filter.supplier_name);
  if (props.nav_filter?.storage_address_id)
    params.append("storage_address_id", props.nav_filter.storage_address_id);
  const query = params.toString();
  window.location.href =
    route("stock.show.stocks", { stock_id: stock_id }) +
    (query ? `?${query}` : "");
};

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
      <div class="container mx-auto">
          <!-- ヘッダーセクション -->
          <PageHeader
            title="在庫詳細"
            subtitle="物品データ閲覧・変更及び手配先や格納先の紐づけを行います。"
          >
            <template #actions>
              <div class="flex items-center gap-2">
                <Button
                  variant="secondary"
                  icon-left="chevron_left"
                  :disabled="!props.prev_stock_id"
                  @click="goToStock(props.prev_stock_id)"
                >
                  前へ
                </Button>
                <Button
                  variant="secondary"
                  icon-right="chevron_right"
                  :disabled="!props.next_stock_id"
                  @click="goToStock(props.next_stock_id)"
                >
                  次へ
                </Button>
                <Link
                  :href="route('stock.stocks.create', { stock_id: props.stock.id })"
                >
                  <Button variant="primary" icon-left="content_copy">
                    複製して在庫追加
                  </Button>
                </Link>
              </div>
            </template>
          </PageHeader>

          <!-- iframeセクション -->
          <div
            v-if="props.stock.url && props.stock.url.includes('askul')"
            class="mb-6 bg-surface-base border border-border rounded-card shadow-card overflow-hidden"
          >
            <iframe
              id="stock_iframe"
              :src="props.stock.url"
              frameborder="0"
              class="w-full h-[60vh]"
            ></iframe>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
            <!-- 左カラム -->
            <div id="left_container" class="lg:col-span-2 space-y-6">
          <!-- 略名登録ブロック -->
          <EditAlias :aliases="props.aliases" :stock_id="props.stock.id" />
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
              <SectionCard title="手配先設定">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                  <FormField label="手配先" required :error="!form.stock_supplier_supplier_id ? '手配先を選択してください' : ''">
                    <select
                      v-model="form.stock_supplier_supplier_id"
                      class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
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
                  </FormField>
                  <FormField label="リードタイム" required :error="!form.stock_supplier_lead_time ? 'リードタイムを入力してください' : ''">
                    <input
                      type="number"
                      v-model="form.stock_supplier_lead_time"
                      class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                      placeholder="日数を入力"
                    />
                  </FormField>
                </div>

                <div class="flex justify-center mb-6">
                  <Button variant="primary" icon-left="add" @click.prevent="createStockSupplier">
                    登録
                  </Button>
                </div>

                <div class="border-t border-border my-6"></div>

                <Table>
                  <thead>
                    <tr>
                      <TableHeaderCell>手配先</TableHeaderCell>
                      <TableHeaderCell>リードタイム</TableHeaderCell>
                      <TableHeaderCell>送料</TableHeaderCell>
                      <TableHeaderCell>操作</TableHeaderCell>
                    </tr>
                  </thead>
                  <tbody>
                    <TableRow
                      v-for="stock_supplier in props.stock_suppliers"
                      :key="stock_supplier.id"
                      :state="stock_supplier.main_flg ? 'success' : 'default'"
                    >
                      <TableDataCell nowrap>
                        <div class="flex items-center gap-2">
                          <button
                            @click="updateStockSupplier('delete', stock_supplier)"
                            class="text-error-600 hover:text-error-700 transition-colors flex-shrink-0"
                          >
                            <Icon name="delete" size="sm" />
                          </button>
                          <div class="min-w-0">
                            <Badge
                              v-if="stock_supplier.main_flg"
                              variant="success"
                              class="mb-1"
                            >
                              <span class="w-1.5 h-1.5 bg-success-600 rounded-full"></span>
                              適用中
                            </Badge>
                            <p class="font-medium text-content text-sm whitespace-nowrap">{{ stock_supplier.name }}</p>
                          </div>
                        </div>
                      </TableDataCell>
                      <TableDataCell>
                        <input
                          type="number"
                          v-model="stock_supplier.lead_time"
                          class="w-20 text-center rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                        />
                      </TableDataCell>
                      <TableDataCell>
                        <input
                          type="number"
                          v-model="stock_supplier.postage"
                          class="w-20 text-center rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                        />
                      </TableDataCell>
                      <TableDataCell>
                        <div class="flex flex-col gap-2">
                          <Button variant="primary" size="sm" icon-left="check" @click="updateStockSupplier('save', stock_supplier)">
                            保存
                          </Button>
                          <Button
                            v-if="!stock_supplier.main_flg"
                            variant="secondary"
                            size="sm"
                            icon-left="task_alt"
                            @click="changeStockSupplierMainFlg(stock_supplier.stock_supplier_id)"
                          >
                            適用変更
                          </Button>
                        </div>
                      </TableDataCell>
                    </TableRow>
                  </tbody>
                </Table>
              </SectionCard>

              <!-- 手配先価格設定 -->
              <SectionCard title="手配先価格設定">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                  <FormField label="手配先" required :error="!form.price_stock_supplier_id ? '手配先を選択してください' : ''">
                    <select
                      v-model="form.price_stock_supplier_id"
                      class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
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
                  </FormField>
                  <FormField label="価格" required :error="!form.price_value ? '価格を入力してください' : ''">
                    <input
                      type="number"
                      v-model="form.price_value"
                      class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                      placeholder="価格を入力"
                      step="0.01"
                    />
                  </FormField>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                  <FormField label="適用開始日" required :error="!form.price_start_date ? '適用開始日を入力してください' : ''">
                    <input
                      type="date"
                      v-model="form.price_start_date"
                      class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                    />
                  </FormField>
                  <div>
                    <label class="block mb-1 text-sm font-medium text-content">
                      適用終了日
                      <span class="ml-1 text-xs font-normal text-content-muted">※未入力の場合は無期限</span>
                    </label>
                    <input
                      type="date"
                      v-model="form.price_end_date"
                      class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                    />
                  </div>
                </div>

                <div class="flex justify-center mb-6">
                  <Button variant="primary" icon-left="add" @click.prevent="createStockSupplierPrice">
                    登録
                  </Button>
                </div>

                <div class="border-t border-border my-6"></div>

                <Table>
                  <thead>
                    <tr>
                      <TableHeaderCell>手配先</TableHeaderCell>
                      <TableHeaderCell>価格</TableHeaderCell>
                      <TableHeaderCell>適用開始日</TableHeaderCell>
                      <TableHeaderCell>適用終了日</TableHeaderCell>
                      <TableHeaderCell>状態</TableHeaderCell>
                      <TableHeaderCell>操作</TableHeaderCell>
                    </tr>
                  </thead>
                  <tbody>
                    <TableRow
                      v-for="price in props.stock_supplier_prices"
                      :key="price.id"
                    >
                      <TableDataCell nowrap>
                        <span class="font-medium">{{ price.stock_supplier?.supplier?.name || '-' }}</span>
                      </TableDataCell>
                      <TableDataCell>
                        <input
                          type="number"
                          v-model="price.price"
                          class="w-24 text-center rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                          step="0.01"
                        />
                      </TableDataCell>
                      <TableDataCell>
                        <input
                          type="date"
                          :value="formatDate(price.start_date)"
                          @input="price.start_date = $event.target.value"
                          class="w-36 text-center rounded-md border-border shadow-sm text-xs focus:border-primary-500 focus:ring-primary-500"
                        />
                      </TableDataCell>
                      <TableDataCell>
                        <input
                          type="date"
                          :value="formatDate(price.end_date)"
                          @input="price.end_date = $event.target.value"
                          class="w-36 text-center rounded-md border-border shadow-sm text-xs focus:border-primary-500 focus:ring-primary-500"
                        />
                      </TableDataCell>
                      <TableDataCell>
                        <button
                          v-if="price.active_flg !== 2"
                          @click="updateStockSupplierPrice('toggle', price)"
                        >
                          <Badge :variant="price.active_flg === 1 ? 'success' : 'neutral'">
                            {{ price.active_flg === 1 ? '適用待ち' : '適用済み' }}
                          </Badge>
                        </button>
                        <Badge v-else variant="info">適用済み</Badge>
                      </TableDataCell>
                      <TableDataCell>
                        <div class="flex flex-col gap-2">
                          <Button variant="primary" size="sm" icon-left="check" @click="updateStockSupplierPrice('save', price)">
                            保存
                          </Button>
                          <button
                            @click="updateStockSupplierPrice('delete', price)"
                            class="text-error-600 hover:text-error-700 transition-colors"
                          >
                            <Icon name="delete" size="sm" />
                          </button>
                        </div>
                      </TableDataCell>
                    </TableRow>
                  </tbody>
                </Table>
              </SectionCard>

              <!-- 格納先設定 -->
              <SectionCard title="格納先設定">
                <form action="" class="mb-6">
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <FormField label="倉庫" required :error="!form.location_id ? '倉庫を選択してください' : ''">
                      <select
                        v-model="form.location_id"
                        @change="handleLocation($event.target.value)"
                        class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
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
                    </FormField>

                    <FormField label="アドレス" required :error="!form.storage_address_id ? 'アドレスを選択してください' : ''">
                      <select
                        v-model="form.storage_address_id"
                        class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
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
                    </FormField>

                    <FormField label="数量" required :error="!form.stock_storage_quantity ? '数量を入力してください' : ''">
                      <input
                        type="number"
                        v-model="form.stock_storage_quantity"
                        class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                        placeholder="数量を入力"
                      />
                    </FormField>
                  </div>

                  <div class="flex justify-center mb-6">
                    <Button variant="primary" icon-left="add" @click="createStockStorage">
                      登録
                    </Button>
                  </div>
                </form>

                <div class="border-t border-border my-6"></div>

                <Table>
                  <thead>
                    <tr>
                      <TableHeaderCell>倉庫</TableHeaderCell>
                      <TableHeaderCell>アドレス</TableHeaderCell>
                      <TableHeaderCell>個数</TableHeaderCell>
                      <TableHeaderCell>発注点</TableHeaderCell>
                      <TableHeaderCell>操作</TableHeaderCell>
                    </tr>
                  </thead>
                  <tbody>
                    <TableRow
                      v-for="stock_storage in props.stock_storages"
                      :key="stock_storage.stock_storage_id"
                    >
                      <TableDataCell nowrap>
                        <span class="font-medium">{{ stock_storage.location_name }}</span>
                      </TableDataCell>
                      <TableDataCell nowrap>
                        <span class="text-content-muted">{{ stock_storage.address }}</span>
                      </TableDataCell>
                      <TableDataCell>
                        <input
                          type="number"
                          v-model="stock_storage.quantity"
                          class="w-20 text-center rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                        />
                      </TableDataCell>
                      <TableDataCell>
                        <input
                          type="number"
                          v-model="stock_storage.reorder_point"
                          class="w-20 text-center rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                        />
                      </TableDataCell>
                      <TableDataCell>
                        <div class="flex flex-col gap-2">
                          <Button variant="primary" size="sm" icon-left="check" @click="updateStockStorage('save', stock_storage)">
                            保存
                          </Button>
                          <button
                            @click="updateStockStorage('delete', stock_storage)"
                            class="text-error-600 hover:text-error-700 transition-colors text-center"
                          >
                            <Icon name="delete" size="sm" />
                          </button>
                        </div>
                      </TableDataCell>
                    </TableRow>
                  </tbody>
                </Table>
              </SectionCard>

              <!-- 現場依頼物品設定 -->
              <SectionCard title="現場依頼物品設定">
                <div v-if="stock.stock_request_id" class="space-y-4">
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <FormField label="表示名">
                      <input
                        type="text"
                        v-model="form.alias"
                        @change="updateStockRequest('alias')"
                        class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                        placeholder="表示名を入力"
                      />
                    </FormField>
                    <FormField label="表示順">
                      <input
                        type="number"
                        v-model="form.orderNumber"
                        @change="updateStockRequest('orderNumber')"
                        class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                        placeholder="順番を入力"
                      />
                    </FormField>
                    <FormField label="単位">
                      <input
                        type="text"
                        v-model="form.orderUnit"
                        @change="updateStockRequest('unit')"
                        class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                        placeholder="単位を入力"
                      />
                    </FormField>
                  </div>

                  <Button variant="primary" icon-left="task_alt" block @click="toggleStockRequest">
                    設定済
                  </Button>
                </div>

                <Button
                  v-else
                  variant="secondary"
                  icon-left="warning"
                  block
                  @click="toggleStockRequest"
                >
                  未設定
                </Button>
              </SectionCard>
            </div>

            <!-- 右カラム -->
            <div id="right_container" class="lg:col-span-3 space-y-6">
              <!-- 画像カード -->
              <div class="bg-surface-base border border-border rounded-card shadow-card overflow-hidden">
                <img
                  class="w-full h-80 object-contain p-6 bg-surface-muted"
                  :src="
                    stock.img_path && stock.img_path.includes('https://')
                      ? stock.img_path
                      : 'https://akioka.cloud/' + stock.img_path
                  "
                  alt="商品画像"
                />
              </div>

              <!-- 在庫情報フォーム -->
              <SectionCard title="在庫基本情報">
                <form class="space-y-6">
                  <!-- ID -->
                  <FormField label="ID">
                    <input
                      type="number"
                      v-model="form.stock_id"
                      class="w-full rounded-md border-border shadow-sm text-sm bg-surface-sunken text-content-subtle cursor-not-allowed"
                      disabled
                    />
                  </FormField>

                  <!-- 品名 -->
                  <FormField label="品名" required :error="!form.name ? '品名を入力してください' : ''">
                    <input
                      type="text"
                      v-model="form.name"
                      class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                      placeholder="品名を入力"
                    />
                  </FormField>

                  <!-- 品番 -->
                  <FormField label="品番">
                    <input
                      type="text"
                      v-model="form.s_name"
                      class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                      placeholder="品番を入力"
                    />
                  </FormField>

                  <!-- JANコード -->
                  <FormField label="JANコード">
                    <input
                      type="text"
                      v-model="form.jan_code"
                      class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                      placeholder="JANコードを入力"
                    />
                  </FormField>

                  <!-- URL -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <FormField label="画像URL">
                      <input
                        type="text"
                        v-model="form.img_path"
                        class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                        placeholder="https://****"
                      />
                    </FormField>
                    <FormField label="購買用URL">
                      <input
                        type="text"
                        v-model="form.url"
                        class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                        placeholder="https://****"
                      />
                    </FormField>
                  </div>

                  <!-- 適確事業者番号 -->
                  <FormField label="適確事業者番号">
                    <input
                      type="text"
                      v-model="form.purchase_identification_number"
                      class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                      placeholder="適確事業者番号を入力"
                    />
                  </FormField>

                  <!-- 価格・税区分 -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <FormField label="価格" required :error="!form.price ? '価格を入力してください' : ''">
                      <input
                        type="number"
                        v-model="form.price"
                        class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                        placeholder="価格を入力"
                      />
                    </FormField>
                    <FormField label="税区分" required>
                      <select
                        v-model="form.tax_included"
                        class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                      >
                        <option value="0">税抜き</option>
                        <option value="1">税込み</option>
                      </select>
                    </FormField>
                  </div>

                  <!-- 価格推移グラフ -->
                  <div
                    v-if="props.stock_price_archive && props.stock_price_archive.length > 0"
                    class="bg-surface-muted p-6 rounded-card border border-border"
                  >
                    <canvas ref="chartRef"></canvas>
                  </div>

                  <!-- 単位 -->
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <FormField label="発注単位">
                      <input
                        type="text"
                        v-model="form.solo_unit"
                        class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                        placeholder="個"
                      />
                    </FormField>
                    <FormField label="在庫単位">
                      <input
                        type="text"
                        v-model="form.org_unit"
                        class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                        placeholder="箱"
                      />
                    </FormField>
                    <div>
                      <label class="block mb-1 text-sm font-medium text-content">
                        換算値
                        <span class="ml-1 text-xs text-content-muted font-normal">※納品時の数量登録</span>
                      </label>
                      <input
                        type="number"
                        v-model="form.quantity_per_org"
                        class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                        placeholder="換算値を入力"
                      />
                    </div>
                  </div>

                  <!-- カテゴリ・配送先・工程 -->
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <FormField label="備品カテゴリ" required :error="!form.classification_id ? '備品カテゴリを選択してください' : ''">
                      <select
                        v-model="form.classification_id"
                        @change="handleClassification"
                        class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
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
                    </FormField>
                    <FormField label="配送先">
                      <input
                        type="text"
                        v-model="form.deli_location"
                        class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                        placeholder="配送先を入力"
                      />
                    </FormField>
                    <div>
                      <label class="block mb-1 text-sm font-medium text-content">
                        工程
                        <span class="ml-1 text-xs text-content-muted font-normal">※発注依頼時デフォルト値</span>
                      </label>
                      <select
                        v-model="form.stock_process_id"
                        class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
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
                    <FormField label="稟議申請時発注先名">
                      <input
                        type="text"
                        v-model="form.approval_supplier_name"
                        class="w-full rounded-md border-border shadow-sm text-sm bg-surface-sunken text-content-subtle cursor-not-allowed"
                        disabled
                      />
                    </FormField>
                    <FormField label="備考">
                      <input
                        type="text"
                        v-model="form.desc_memo"
                        class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                        placeholder="備考を入力"
                      />
                    </FormField>
                    <FormField label="納品書金額表示">
                      <select
                        v-model="form.show_price_on_invoice"
                        :class="{
                          'w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500 font-semibold': true,
                          'text-success-700': !form.show_price_on_invoice,
                          'text-error-700': form.show_price_on_invoice
                        }"
                      >
                        <option class="text-success-700" value="0">表示</option>
                        <option class="text-error-700" value="1">非表示</option>
                      </select>
                    </FormField>
                  </div>
                  <FormField label="ステータス">
                    <select
                      v-model="form.del_flg"
                      :class="{
                        'text-center w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500 font-semibold': true,
                        'text-success-700': !form.del_flg,
                        'text-error-700': form.del_flg
                      }"
                    >
                      <option class="text-success-700" value="0">有効</option>
                      <option class="text-error-700" value="1">無効</option>
                    </select>
                  </FormField>

                  <!-- 変更ボタン -->
                  <div class="flex justify-center pt-4">
                    <Button variant="primary" size="lg" icon-left="check" @click.prevent="editStock">
                      変更を保存
                    </Button>
                  </div>
                </form>
              </SectionCard>
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
#left_container > section,
#right_container > div,
#right_container > section {
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