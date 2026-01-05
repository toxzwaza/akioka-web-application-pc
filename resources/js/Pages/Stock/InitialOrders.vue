<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { onMounted, reactive, ref } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";
import Purchase from "@/Components/Purchase.vue";
import MainTitle from "@/Components/Title/MainTitle.vue";
import { getImgPath } from "@/Helper/Method";
import ApprovalDocument from "@/Components/Accept/ApprovalDocument.vue";
import SearchLoading from "@/Components/Loading/SearchLoading.vue";
import UserLogin from "@/Components/Auth/UserLogin.vue";

const props = defineProps({
  initial_orders: Object,
  current_month_holidays: Array,
  next_month_holidays: Array,
  admin_users: Array,
  users: Array,
  order_users: Array,
  suppliers: Array,
  totals: Object,
  groups: Array,
  processes: Array,
  classifications: Array,
});

const form = reactive({
  order_by: "desc",
  keyword: null,
  start_order_date: null,
  end_order_date: null,
  supplier_id: null,
  order_user_id: null,
  user_id: null,
  group_id: null,
  process_id: null,
  delivery_status: null,
  start_delivery_date: null,
  end_delivery_date: null,
});

// 稟議書OBJ
const approval_document = reactive({
  document_id: null,
  user_name: null,
  evalution_date: null, //評価日
  desire_delivery_date: null, //希望日
  supplier_name: null,
  price: null,
  quantity: null,
  calc_price: null,
  name: null,
  s_name: null,
  document_id: null,
  title: null,
  content: null,
  main_reason: null,
  sub_reason: null,
  approvals: [],
});

const is_login = ref(false);
const pwd = ref("");
const isSearchLoading = ref(false);
const current_admin_user = ref(null);

// モーダル状態管理（シンプル化）
const modal = reactive({
  isOpen: false,
  type: null, // 'image', 'purchase', 'delivery', 'approval'
  data: null, // モーダルに表示するデータ
  currentIndex: 0, // 複数アイテム表示時の現在のインデックス
});

const purchase_list = ref([]);
const print_order = ref([]);

const changeMessage = (order) => {
  console.log(order);

  if (order.description) {
    let device_notify_flg = false;
    if (confirm("備考が変更されました。依頼者に通知しますか？")) {
      device_notify_flg = true;
    }

    axios
      .post(route("stock.initialOrder.sendDeviceMessage"), {
        device_notify_flg: device_notify_flg,
        initial_order_id: order.id,
        message: order.description,
      })
      .then((res) => {
        console.log(res.data);
        if (res.data.status) {
          alert("成功しました。");
        }
      })
      .catch((error) => {
        console.log(error);
      });
  }
};
// モーダルを開く（シンプル化）
const openModal = (type, data) => {
  modal.type = type;
  modal.data = data;
  modal.currentIndex = 0;
  modal.isOpen = true;
};

// モーダルを閉じる
const closeModal = () => {
  modal.isOpen = false;
  modal.type = null;
  modal.data = null;
  modal.currentIndex = 0;
};

// 画像パスを取得
const getImagePath = (path) => {
  if (!path) return '';
  if (path.includes('https://')) return path;
  if (path.includes('storage')) return `https://akioka.cloud/${path}`;
  if (path.includes('deli_file')) return `https://akioka.cloud/storage/${path}`;
  return `https://akioka.cloud/${path}`;
};

// 納品書パスを配列で取得（複数対応）
const getDeliveryFiles = (order) => {
  if (!order) return [];
  
  const files = [];
  
  // deliveriesリレーションから納品書を取得（優先）
  if (order.deliveries && Array.isArray(order.deliveries) && order.deliveries.length > 0) {
    order.deliveries.forEach((delivery, index) => {
      if (delivery.document_image) {
        files.push({
          path: getImagePath(delivery.document_image),
          id: delivery.id || `${order.id}_${index}`,
          delivery_id: delivery.id,
          delivery_date: order.delivery_date, // 納品日（initial_orderのdelivery_dateを使用）
        });
      }
    });
  }
  
  // 後方互換性: deliveriesがない場合は既存のdelifile_pathを使用
  if (files.length === 0) {
    // 単一の納品書パスがある場合
    if (order.delifile_path) {
      files.push({
        path: getImagePath(order.delifile_path),
        id: order.id,
        delivery_date: order.delivery_date, // 納品日（initial_orderのdelivery_dateを使用）
      });
    }
    
    // 複数の納品書パスがある場合（配列またはカンマ区切り）
    if (order.delifile_paths && Array.isArray(order.delifile_paths)) {
      order.delifile_paths.forEach((path, index) => {
        files.push({
          path: getImagePath(path),
          id: `${order.id}_${index}`,
          delivery_date: order.delivery_date, // 納品日（initial_orderのdelivery_dateを使用）
        });
      });
    } else if (order.delifile_paths && typeof order.delifile_paths === 'string') {
      // カンマ区切りの場合
      order.delifile_paths.split(',').forEach((path, index) => {
        const trimmedPath = path.trim();
        if (trimmedPath) {
          files.push({
            path: getImagePath(trimmedPath),
            id: `${order.id}_${index}`,
            delivery_date: order.delivery_date, // 納品日（initial_orderのdelivery_dateを使用）
          });
        }
      });
    }
  }
  
  return files;
};

// 前の納品書に移動
const prevDeliveryFile = () => {
  if (modal.currentIndex > 0) {
    modal.currentIndex--;
  }
};

// 次の納品書に移動
const nextDeliveryFile = () => {
  if (modal.data && modal.data.files && modal.currentIndex < modal.data.files.length - 1) {
    modal.currentIndex++;
  }
};

const base_initial_orders = ref([]);
const initial_orders = ref({
  data: [],
  links: [],
});

const order_users = ref([]);
const com_names = ref([]);

const sort = ref("new_order");
const filter = ref("");

const createSort = (sort_field) => {
  sort.value = sort_field;
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

const updateOrderUsers = () => {
  const unique_order_users = [
    ...new Set(initial_orders.value.map((order) => order.order_user)),
  ];
  order_users.value = unique_order_users;
  console.log(unique_order_users);
};
const updateComName = () => {
  const unique_com_names = [
    ...new Set(initial_orders.value.map((order) => order.com_name)),
  ];
  com_names.value = unique_com_names;
  console.log(unique_com_names);
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
        window.location.reload();
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
    case "com_name":
      initial_orders.value = initial_orders.value.filter(
        (order) => order.com_name === value
      );
      break;
    case "nameOrSname":
      if (value) {
        initial_orders.value = initial_orders.value.filter(
          (order) =>
            (order.name && order.name.includes(value)) ||
            (order.s_name && order.s_name.includes(value))
        );
      } else {
        initial_orders.value = [];
      }

      break;
    case "reset":
      initial_orders.value = base_initial_orders.value;
      break;
    default:
      break;
  }
  if (initial_orders.value.length === 0) {
    alert(
      "フィルター条件に合う結果が見つかりませんでした。リセットを行います。"
    );
    updateFilter("reset");
  }
};

const openFaxParameter = (faxParameterId) => {
  const url = `http://monokanri-manage.local:5000/${faxParameterId}`;
  window.open(url, '_blank');
};

const updateDate = (flg, order_id, date) => {
  console.log(flg, order_id, date);
  // return;
  if (flg) {
    axios
      .post(route("stock.update_date"), {
        flg: flg,
        initial_order_id: order_id,
        date: date,
      })
      .then((res) => {
        console.log(res.data);
        alert("日付を更新しました。");
      })
      .catch((error) => {
        console.log(error);
      });
  }
};

//  単価改定
const handlePrice = (order, price) => {
  // 値上がりした場合
  if (order.price < price) {
    if (
      confirm(
        "単価が変更されました。\n値上がりしている為、再承認が必要です。\n発注依頼をおこないますか？"
      )
    ) {
      axios
        .post(route("stock.update_price"), {
          initial_order_id: order.id,
          price: price,
        })
        .then((res) => {
          console.log(res.data);
          if (res.data.status) {
            alert("再度、発注依頼を作成しました。");
            initial_orders.value = initial_orders.value.filter(
              (o) => o.id !== order.id
            );
          }
        })
        .catch((error) => {
          console.log(error);
        });
    }
  } else {
    // 値下げの場合単価をそのまま変更
    axios
      .post(route("stock.update_data"), {
        initial_order_id: order.id,
        flg: "price",
        val: price,
      })
      .then((res) => {
        console.log(res.data);
        if (res.data.status) {
          alert("単価を値下げしました。");
        }
      })
      .catch((error) => {
        console.log(error);
      });
  }
};

const handlePostage = (order, postage) => {
  axios
    .post(route("stock.update_data"), {
      initial_order_id: order.id,
      flg: "postage",
      val: postage,
    })
    .then((res) => {
      console.log(res.data);
      if (res.data.status) {
        alert("送料を更新しました。");
      }
    })
    .catch((error) => {
      console.log(error);
    });
};

const handleQuantity = (order, quantity) => {
  const newQuantity = parseInt(quantity);
  if (newQuantity <= 0) {
    alert("数量は1以上で入力してください。");
    return;
  }

  axios
    .post(route("stock.update_data"), {
      initial_order_id: order.id,
      flg: "quantity",
      val: newQuantity,
    })
    .then((res) => {
      console.log(res.data);
      if (res.data.status) {
        alert("数量を更新しました。");
        // 金額の再計算
        order.quantity = newQuantity;
        order.calc_price = order.price * newQuantity;
      }
    })
    .catch((error) => {
      console.log(error);
    });
};

// 納入希望日を保存する
const handleDeliveryDateUpdate = (date) => {
  if (!modal.data || !modal.data.orders || modal.data.orders.length === 0) {
    console.error("発注データが見つかりません");
    return;
  }
  
  const orderId = modal.data.orders[0].id;
  console.log("選択された納入希望日:", date, orderId);
  
  axios
    .post(route("stock.update_desired_delivery_date"), {
      initial_order_id: orderId,
      desired_delivery_date: date,
    })
    .then((res) => {
      if (res.data.status) {
        alert("納入希望日を設定しました。");
      }
    })
    .catch((error) => {
      console.log(error);
    });
};

const handleSelect = (order) => {
  console.log(order);
  const index = purchase_list.value.findIndex((item) => item.id === order.id);

  if (index !== -1) {
    purchase_list.value.splice(index, 1);
    console.log(purchase_list.value);
    return;
  }

  if (purchase_list.value.length > 3) {
    alert("同時に一つの注文書に含めることができるのは4件以内です。");
    order.select_flg = false;
    console.log(purchase_list.value);
    return;
  }

  if (
    purchase_list.value.length > 0 &&
    purchase_list.value[0].supplier_id !== order.supplier_id
  ) {
    alert("複数の注文先の発注情報を含めることはできません。");
    order.select_flg = false;
    console.log(purchase_list.value);
    return;
  }

  purchase_list.value.push(order);
  console.log(purchase_list.value);
};

// 新しいログインコンポーネント用のハンドラー
const handleAdminLoginSuccess = (loginData) => {
  console.log("管理者ログイン成功:", loginData);
  is_login.value = true;
  current_admin_user.value = loginData.user;
  // 選択されたユーザー情報をログに出力
  console.log("選択された管理者:", loginData.user.name);
};

const handleAdminLogout = () => {
  console.log("管理者ログアウト");
  is_login.value = false;
  pwd.value = "";
  current_admin_user.value = null;
};

// 従来のメソッド（後方互換性のため保持）
const login = () => {
  if (props.admin_users.some((user) => user.password == pwd.value)) {
    is_login.value = true;
  }
};

const getInitialOrders = (reset) => {
  // ローディング開始
  isSearchLoading.value = true;

  if (reset === "reset") {
    form.order_by = null;
    form.keyword = null;
    form.start_order_date = null;
    form.end_order_date = null;
    form.supplier_id = null;
    form.order_user_id = null;
    form.user_id = null;
    form.group_id = null;
    form.process_id = null;
    form.classification_id = null;
    form.delivery_status = null;
    form.start_delivery_date = null;
    form.end_delivery_date = null;

    console.log("検索条件リセット");
  }

  // Inertia.jsのイベントリスナーを追加してローディングを制御
  router.get(
    route("stock.initialOrders"),
    {
      order_by: form.order_by,
      keyword: form.keyword,
      start_order_date: form.start_order_date,
      end_order_date: form.end_order_date,
      supplier_id: form.supplier_id,
      order_user_id: form.order_user_id,
      user_id: form.user_id,
      group_id: form.group_id,
      process_id: form.process_id,
      classification_id: form.classification_id,
      delivery_status: form.delivery_status,
      start_delivery_date: form.start_delivery_date,
      end_delivery_date: form.end_delivery_date,
    },
    {
      onFinish: () => {
        // ページ遷移完了後にローディングを停止
        setTimeout(() => {
          isSearchLoading.value = false;
        }, 500); // 少し遅延を入れて自然な感じにする
      },
      onError: () => {
        // エラー時もローディングを停止
        isSearchLoading.value = false;
      },
    }
  );
};

// 発注完了登録
const orderComplete = (order, value) => {
  let flg;

  if (!value) {
    return
  }

  const numericValue = Number(value);

  axios
    .post(route("stock.updateOrderComplete"), {
      initial_order_id: order.id,
      order_complete_flg: numericValue,
    })
    .then((res) => {
      console.log(res.data);
      if (res.data.status) {
        order.order_complete_flg = numericValue;
        // 強制的に再レンダリングさせるため、リアクティブな更新を確認
        console.log('order_complete_flg updated:', order.order_complete_flg);
      }
    })
    .catch((error) => {
      console.log(error);
    });
};

onMounted(() => {
  console.log(props.initial_orders);

  // initial_ordersのデータをマッピングし、order_complete_flgを数値に変換
  initial_orders.value.data = props.initial_orders.data.map(order => ({
    ...order,
    order_complete_flg: order.order_complete_flg ? Number(order.order_complete_flg) : null
  }));
  initial_orders.value.links = props.initial_orders.links;
  base_initial_orders.value = props.initial_orders.data.map(order => ({
    ...order,
    order_complete_flg: order.order_complete_flg ? Number(order.order_complete_flg) : null
  }));

  // updateOrderUsers();
  // updateComName();

  const params = new URLSearchParams(window.location.search);
  form.keyword = params.get("keyword") || "";
  form.order_by = params.get("order_by") || "desc";
  form.start_order_date = params.get("start_order_date");
  form.end_order_date = params.get("end_order_date");
  form.supplier_id = params.get("supplier_id");
  form.order_user_id = params.get("order_user_id");
  form.user_id = params.get("user_id");
  form.group_id = params.get("group_id");
  form.process_id = params.get("process_id");
  form.classification_id = params.get("classification_id");
  form.delivery_status = params.get("delivery_status");
  form.start_delivery_date = params.get("start_delivery_date");
  form.end_delivery_date = params.get("end_delivery_date");

  console.log(props.totals);

  // Inertiaのページ遷移イベントをリッスン（ページネーション対応）
  router.on("start", () => {
    isSearchLoading.value = true;
  });

  router.on("finish", () => {
    setTimeout(() => {
      isSearchLoading.value = false;
    }, 300);
  });
});

// 納品書更新

const fileUpload = async (event, initial_order_id) => {
  const file = event.target.files[0];
  if (file) {
    if (!confirm("ファイルが選択されました。納品書を更新しますか？")) {
      alert("納品書更新を中止しました。");
      return;
    }

    const formData = new FormData();
    formData.append("file", file);
    formData.append("initial_order_id", initial_order_id);
    console.log(formData);

    axios
      .post(route("stock.update_deli_file"), formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      })
      .then((res) => {
        console.log(res.data);

        if (res.data.status) {
          alert("納品書の更新が完了しました。");
          window.location.reload();
        } else {
          alert("納品書更新中に何らかのエラーが発生しました。");
        }
      })
      .catch((error) => {
        alert(`エラーが発生しました。${error}`);
      });
  }
};

// 削除
const deleteInitialOrder = (order) => {
  console.log(order);
  const msg = `
  以下の物品を削除してもよろしいですか？
  依頼者: ${order.order_user}
  発注担当者: ${order.manage_user_name}
  品名: ${order.name}
  品番: ${order.s_name}
  単価: ${order.price}
  数量: ${order.quantity}
  金額: ${order.calc_price}
  `;
  if (confirm(msg)) {
    axios
      .delete(route("stock.delete.initialOrders"), {
        params: {
          initial_order_id: order.id,
        },
      })
      .then((res) => {
        console.log(res.data);
        if (res.data.status) {
          alert("削除が完了しました。");
        }
      })
      .catch((error) => {
        console.log(error);
      });
  }
};
</script>
<template>
  <MainLayout :title="'発注一覧'">
    <template #content>
      <!-- Header Section -->
      <div class="header-section mb-8">
        <div class="header-content">
          <MainTitle
            :top="'発注一覧'"
            :sub="'発注情報の確認・管理ができます。'"
          />

          <!-- Admin Login Component -->
          <UserLogin
            :users="admin_users"
            title="管理者ログイン"
            role="管理者"
            helpText="管理者権限で品名・品番・数量の編集が可能です"
            storageKey="user_id"
            @login="handleAdminLoginSuccess"
            @logout="handleAdminLogout"
          />
        </div>
      </div>

      <!-- Search and Filter Section -->
      <div class="search-section mb-8">
        <div class="search-card">
          <div class="search-header">
            <h2 class="search-title">
              <svg
                class="w-5 h-5 mr-2"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                ></path>
              </svg>
              検索・フィルター
            </h2>
          </div>

          <div class="search-content">
            <!-- Sort Controls -->
            <div class="sort-section">
              <label class="sort-label">並び順</label>
              <div class="sort-buttons">
                <button
                  class="sort-btn"
                  :class="{ active: form.order_by === 'desc' }"
                  @click="form.order_by = 'desc'"
                >
                  <svg
                    class="w-4 h-4 mr-1"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M19 14l-7 7m0 0l-7-7m7 7V3"
                    ></path>
                  </svg>
                  新しい順
                </button>
                <button
                  class="sort-btn"
                  :class="{ active: form.order_by === 'asc' }"
                  @click="form.order_by = 'asc'"
                >
                  <svg
                    class="w-4 h-4 mr-1"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 10l7-7m0 0l7 7m-7-7v18"
                    ></path>
                  </svg>
                  古い順
                </button>
              </div>
            </div>

            <!-- Filter Grid -->
            <div class="filter-grid">
              <div class="filter-item">
                <label class="filter-label">品名・品番</label>
                <div class="input-with-icon">
                  <svg
                    class="input-icon"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                    ></path>
                  </svg>
                  <input
                    class="filter-input"
                    type="text"
                    placeholder="品名または品番で検索"
                    v-model="form.keyword"
                  />
                </div>
              </div>
              <div class="filter-item date-range">
                <label class="filter-label">注文日</label>
                <div class="date-range-inputs">
                  <input
                    type="date"
                    class="filter-input date-input"
                    v-model="form.start_order_date"
                  />
                  <span class="date-separator">～</span>
                  <input
                    type="date"
                    class="filter-input date-input"
                    v-model="form.end_order_date"
                  />
                </div>
              </div>

              <div class="filter-item">
                <label class="filter-label">注文先</label>
                <select class="filter-select" v-model="form.supplier_id">
                  <option value="0">すべての注文先</option>
                  <option
                    v-for="supplier in props.suppliers"
                    :key="supplier.id"
                    :value="supplier.id"
                  >
                    {{ supplier.name }}
                  </option>
                </select>
              </div>
              <div class="filter-item">
                <label class="filter-label">部門（大区分）</label>
                <select class="filter-select" v-model="form.group_id">
                  <option value="0">すべての部門</option>
                  <option
                    v-for="group in props.groups"
                    :key="group.id"
                    :value="group.id"
                  >
                    {{ group.name }}
                  </option>
                </select>
              </div>
              <div class="filter-item">
                <label class="filter-label">部門（中区分）</label>
                <select class="filter-select" v-model="form.process_id">
                  <option value="0">すべての部門</option>
                  <option
                    v-for="process in props.processes"
                    :key="process.id"
                    :value="process.id"
                  >
                    {{ process.name }}
                  </option>
                </select>
              </div>
              <div class="filter-item">
                <label class="filter-label">依頼者</label>
                <select class="filter-select" v-model="form.order_user_id">
                  <option value="0">すべての依頼者</option>
                  <option
                    v-for="user in props.order_users"
                    :key="user.id"
                    :value="user.id"
                  >
                    {{ user.name }}
                  </option>
                </select>
              </div>
              <div class="filter-item">
                <label class="filter-label">担当者</label>
                <select class="filter-select" v-model="form.user_id">
                  <option value="0">すべての担当者</option>
                  <option
                    v-for="user in props.users"
                    :key="user.id"
                    :value="user.id"
                  >
                    {{ user.name }}
                  </option>
                </select>
              </div>
              <div class="filter-item">
                <label class="filter-label">カテゴリー</label>
                <select class="filter-select" v-model="form.classification_id">
                  <option value="0">すべてのカテゴリー</option>
                  <option
                    v-for="classification in props.classifications"
                    :key="classification.id"
                    :value="classification.id"
                  >
                    {{ classification.name }}
                  </option>
                </select>
              </div>
              <div class="filter-item">
                <label class="filter-label">納入状況</label>
                <div class="flex flex-col gap-2">
                  <label class="flex items-center cursor-pointer">
                    <input
                      type="checkbox"
                      :checked="form.delivery_status === 'delivered'"
                      @change="
                        if ($event.target.checked) {
                          form.delivery_status = 'delivered';
                        } else {
                          form.delivery_status = null;
                          form.start_delivery_date = null;
                          form.end_delivery_date = null;
                        }
                      "
                      class="mr-2"
                    />
                    <span class="text-sm">納入済み</span>
                  </label>
                  <label class="flex items-center cursor-pointer">
                    <input
                      type="checkbox"
                      :checked="form.delivery_status === 'undelivered'"
                      @change="
                        if ($event.target.checked) {
                          form.delivery_status = 'undelivered';
                        } else {
                          form.delivery_status = null;
                          form.start_delivery_date = null;
                          form.end_delivery_date = null;
                        }
                      "
                      class="mr-2"
                    />
                    <span class="text-sm">未納品</span>
                  </label>
                </div>
              </div>
              <div class="filter-item date-range" v-if="form.delivery_status === 'delivered'">
                <label class="filter-label">納入日（期間指定は任意）</label>
                <div class="date-range-inputs">
                  <input
                    type="date"
                    class="filter-input date-input"
                    v-model="form.start_delivery_date"
                    placeholder="開始日"
                  />
                  <span class="date-separator">～</span>
                  <input
                    type="date"
                    class="filter-input date-input"
                    v-model="form.end_delivery_date"
                    placeholder="終了日"
                  />
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="search-actions">
              <button @click="getInitialOrders()" class="action-btn primary">
                <svg
                  class="w-4 h-4 mr-1"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                  ></path>
                </svg>
                検索
              </button>
              <button
                @click="getInitialOrders('reset')"
                class="action-btn secondary"
              >
                <svg
                  class="w-4 h-4 mr-1"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                  ></path>
                </svg>
                リセット
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Statistics Section -->
      <div class="stats-section mb-8">
        <div class="stats-header mb-6">
          <h2 class="stats-title">
            <svg
              class="w-5 h-5 mr-2"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
              ></path>
            </svg>
            発注統計
          </h2>
        </div>

        <div class="stats-grid">
          <div class="stat-card search-stats">
            <div class="stat-header">
              <div class="stat-icon">
                <svg
                  class="w-6 h-6"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                  ></path>
                </svg>
              </div>
              <div class="stat-content">
                <h3 class="stat-title">検索結果</h3>
                <p class="stat-subtitle">現在の検索条件</p>
              </div>
            </div>
            <div class="stat-values">
              <div class="stat-value-item">
                <span class="stat-value">{{
                  props.totals.total_order_count
                }}</span>
                <span class="stat-unit">件</span>
              </div>
              <div class="stat-amount">
                {{
                  Number(props.totals.total_calc_price_sum).toLocaleString()
                }}円
              </div>
            </div>
          </div>

          <div class="stat-card monthly-stats">
            <div class="stat-header">
              <div class="stat-icon">
                <svg
                  class="w-6 h-6"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                  ></path>
                </svg>
              </div>
              <div class="stat-content">
                <h3 class="stat-title">今月の発注</h3>
                <p class="stat-subtitle">
                  {{
                    new Date().toLocaleDateString("ja-JP", {
                      year: "numeric",
                      month: "long",
                    })
                  }}
                </p>
              </div>
            </div>
            <div class="stat-values">
              <div class="stat-value-item">
                <span class="stat-value">{{
                  props.totals.current_month_count
                }}</span>
                <span class="stat-unit">件</span>
              </div>
              <div class="stat-amount">
                {{ Number(props.totals.current_month_sum).toLocaleString() }}円
              </div>
            </div>
          </div>

          <div class="stat-card performance-stats">
            <div class="stat-header">
              <div class="stat-icon">
                <svg
                  class="w-6 h-6"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"
                  ></path>
                </svg>
              </div>
              <div class="stat-content">
                <h3 class="stat-title">平均単価</h3>
                <p class="stat-subtitle">検索結果から算出</p>
              </div>
            </div>
            <div class="stat-values">
              <div class="stat-amount large">
                {{
                  props.totals.total_order_count > 0
                    ? Math.round(
                        props.totals.total_calc_price_sum /
                          props.totals.total_order_count
                      ).toLocaleString()
                    : 0
                }}円
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Table Controls -->
      <div class="table-controls mb-6">
        <div class="controls-left">
          <div class="pagination-wrapper">
            <Pagination :links="initial_orders.links" />
          </div>
        </div>

        <div class="controls-right">
          <button
            v-if="purchase_list.length > 1"
            @click="openModal('purchase', { orders: purchase_list })"
            class="batch-order-btn"
          >
            <svg
              class="w-4 h-4 mr-2"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
              ></path>
            </svg>
            まとめて発注書作成 ({{ purchase_list.length }}件)
          </button>
        </div>
      </div>

      <!-- Modern Table Container -->
      <div class="modern-table-container">
        <div class="table-wrapper">
          <table class="modern-table">
            <thead>
              <tr>
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl whitespace-nowrap"
                >
                  選択
                </th>
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl"
                >
                  注文No
                </th>
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl whitespace-nowrap"
                >
                  画像
                </th>
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                >
                  工程
                </th>
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                >
                  注文依頼者
                </th>
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                >
                  担当者
                </th>
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                >
                  注文日
                </th>
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                >
                  納入場所
                </th>
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                >
                  注文先
                </th>
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  style="
                    border-radius: 10px 0 0 10px;
                    background-color: #ffabab;
                  "
                >
                  品名
                </th>
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  style="background-color: rgb(255 188 188)"
                >
                  品番
                </th>

                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-300"
                >
                  納入希望日
                </th>
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-400"
                >
                  納入予定日
                </th>
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-500"
                  style="border-radius: 0 10px 10px 0"
                >
                  納入日
                </th>
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap"
                >
                  LT
                </th>
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap"
                >
                  単価
                </th>
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap"
                >
                  送料
                </th>
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap"
                >
                  数量
                </th>
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap"
                >
                  単位
                </th>
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap"
                >
                  金額
                </th>
                <th
                  class="w-1/5 px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap"
                >
                  備考
                </th>

                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap"
                >
                  発注書
                </th>
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap"
                >
                  納品書
                </th>
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap"
                >
                  稟議書
                </th>
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap"
                >
                  完了登録
                </th>
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap"
                >
                  FAX送信
                </th>
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap"
                  v-if="is_login"
                >
                  削除
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="order in initial_orders.data"
                :key="order.id"
                :class="{
                  'transition duration-300 hover:scale-[1.01] hover:z-10 hover:shadow-lg hover:bg-yellow-100 hover:font-bold hover:border-2 hover:border-yellow-300': true,
                  'bg-green-100': order.order_complete_flg === 1,
                  'bg-blue-100': order.order_complete_flg === 2,
                  'bg-white': !order.order_complete_flg || order.order_complete_flg === 0,
                }"
              >
                <td class="text-center">
                  <input
                    type="checkbox"
                    name=""
                    id=""
                    @change="handleSelect(order)"
                    v-model="order.select_flg"
                  />
                </td>
                <td
                  :class="{
                    'px-4 py-3': true,
                    'text-green-500 font-bold cursor-pointer':
                      order.receive_flg,
                  }"
                >
                  {{ order.order_no }}
                </td>
                <td class="w-28">
                  <img
                    :src="
                      order.img_path && order.img_path.includes('https://')
                        ? order.img_path
                        : 'https://akioka.cloud/' + order.img_path
                    "
                    @click="openModal('image', { path: order.img_path && order.img_path.includes('https://') ? order.img_path : 'https://akioka.cloud/' + order.img_path })"
                    alt=""
                  />
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-center">
                  {{
                    order.stock_processes_order_request_code
                      ? `${order.stock_processes_order_request_code}:${order.stock_processes_order_request_name}`
                      : order.stock_processes_base_code
                      ? `${order.stock_processes_base_code}:${order.stock_processes_base_name}`
                      : "-"
                  }}
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                  {{ order.order_user }}
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                  {{ order.manage_user_name }}
                </td>
                <td class="px-4 py-3 text-lg text-gray-900">
                  {{ new Date(order.order_date).toLocaleDateString("ja-JP") }}
                </td>
                <td class="px-4 py-3 text-lg text-gray-900 whitespace-nowrap">
                  <input
                    v-if="is_login"
                    @change="
                      updateNameOrSName(
                        order.id,
                        'deli_location',
                        $event.target.value
                      )
                    "
                    type="text"
                    name="name"
                    v-model="order.deli_location"
                    id=""
                    class="appearance-none block w-64 bg-gray-100 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  />

                  <span v-else>{{ order.deli_location }}</span>
                </td>
                <td class="px-4 py-3 text-lg text-gray-900">
                  {{ order.com_name }}
                </td>
                <td class="px-4 py-3 text-lg text-gray-900">
                  <input
                    v-if="is_login"
                    @change="
                      updateNameOrSName(order.id, 'name', $event.target.value)
                    "
                    type="text"
                    name="name"
                    v-model="order.name"
                    id=""
                    class="appearance-none block w-64 bg-gray-100 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  />

                  <span v-else>{{ order.name }}</span>
                </td>
                <td class="px-4 py-3 text-lg text-gray-900">
                  <input
                    v-if="is_login"
                    @change="
                      updateNameOrSName(order.id, 's_name', $event.target.value)
                    "
                    type="text"
                    name="s_name"
                    v-model="order.s_name"
                    id=""
                    class="appearance-none block w-64 bg-gray-100 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  />

                  <span v-else>{{ order.s_name }}</span>
                </td>

                <td class="px-4 py-3 text-lg text-gray-900">
                  <input
                    v-if="is_login"
                    type="date"
                    name=""
                    id=""
                    class="appearance-none block w-64 bg-gray-100 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    :value="order.desire_delivery_date"
                    @change="
                      updateDate('desired', order.id, $event.target.value)
                    "
                  />
                  <span v-else>{{
                    order.desire_delivery_date
                      ? new Date(order.desire_delivery_date).toLocaleDateString(
                          "ja-JP"
                        )
                      : "-"
                  }}</span>
                </td>
                <td class="px-4 py-3 text-lg text-gray-900">
                  <input
                    v-if="is_login"
                    @change="
                      updateDate('expected', order.id, $event.target.value)
                    "
                    type="date"
                    name=""
                    id=""
                    class="appearance-none block w-64 bg-gray-100 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    :value="order.expected_delivery_date"
                  />
                  <span v-else>{{
                    order.expected_delivery_date
                      ? new Date(
                          order.expected_delivery_date
                        ).toLocaleDateString("ja-JP")
                      : "-"
                  }}</span>
                </td>
                <td class="px-4 py-3 text-lg text-gray-900">
                  <input
                    v-if="is_login"
                    @change="
                      updateDate('delivery', order.id, $event.target.value)
                    "
                    type="date"
                    name=""
                    id=""
                    class="appearance-none block w-64 bg-gray-100 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    :value="order.delivery_date"
                  />
                  <span v-else>{{
                    order.delivery_date
                      ? new Date(order.delivery_date).toLocaleDateString(
                          "ja-JP"
                        )
                      : "-"
                  }}</span>
                </td>

                <td
                  :class="{
                    'ml-2 px-4 py-3 text-lg whitespace-nowrap': true,
                    'text-gray-400': order.base_lead_time != null,
                    'text-gray-700': order.lead_time != null,
                  }"
                >
                  {{
                    order.lead_time
                      ? `${order.lead_time}日`
                      : order.base_lead_time
                      ? `≒ ${order.base_lead_time}日`
                      : "-"
                  }}
                </td>

                <td
                  class="ml-2 px-4 py-3 text-lg text-gray-900 whitespace-nowrap"
                >
                  <input
                    v-if="is_login"
                    type="number"
                    class="appearance-none block w-64 bg-gray-100 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    :value="order.price"
                    @change="handlePrice(order, $event.target.value)"
                  />
                  <span v-else
                    >{{ order.price.toLocaleString()
                    }}{{
                      order.stock_tax_included ? " (税込)" : " (税抜)"
                    }}</span
                  >
                </td>
                <td class="ml-2 px-4 py-3 text-lg text-gray-900">
                  <input
                    v-if="is_login"
                    type="number"
                    class="appearance-none block w-64 bg-gray-100 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    :value="order.postage"
                    @change="handlePostage(order, $event.target.value)"
                  />
                  <span v-else>{{
                    order.postage ? order.postage.toLocaleString() : "-"
                  }}</span>
                </td>
                <td class="ml-2 px-4 py-3 text-lg text-gray-900">
                  <input
                    v-if="is_login"
                    type="number"
                    min="1"
                    class="appearance-none block w-20 bg-gray-100 text-gray-700 border border-gray-200 rounded py-2 px-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    :value="order.quantity"
                    @change="handleQuantity(order, $event.target.value)"
                  />
                  <span v-else>{{ order.quantity }}</span>
                </td>
                <td class="ml-2 px-4 py-3 text-lg text-gray-900">
                  {{ order.order_unit }}
                </td>
                <td
                  class="ml-2 px-4 py-3 text-lg text-gray-900 whitespace-nowrap"
                >
                  {{ order.calc_price.toLocaleString() }}
                  <span class="text-xs text-gray-600">円</span>
                </td>
                <td class="min-w-md ml-2 px-4 py-3 text-lg text-gray-900">
                  <input
                    v-if="is_login"
                    type="text"
                    class="appearance-none block w-64 bg-gray-100 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    v-model="order.description"
                    @change="changeMessage(order)"
                  />
                  <span v-else>{{ order.description }}</span>
                </td>

                <td
                  class="ml-2 px-4 py-3 text-lg text-gray-900 whitespace-nowrap"
                >
                  <button
                    v-if="!order.url"
                    @click="openModal('purchase', { orders: [order] })"
                    :class="{
                      ' hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-xs': true,
                      'bg-green-500': order.purchase_path,
                      'bg-gray-500': !order.purchase_path,
                    }"
                  >
                    {{ order.purchase_path ? "発行済" : "未発行" }}
                    <i v-if="order.purchase_path" class="ml-2 fas fa-check"></i>
                  </button>

                  <a
                    v-else
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-xs"
                    :href="order.url"
                    target="blank"
                    >URL</a
                  >
                </td>
                <td
                  class="ml-2 px-4 py-3 text-lg text-gray-900 whitespace-nowrap"
                >
                  <button
                    v-if="(order.deliveries && order.deliveries.length > 0) || order.delifile_path || (order.delifile_paths && order.delifile_paths.length > 0)"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-xs"
                    @click="openModal('delivery', { order: order, files: getDeliveryFiles(order) })"
                  >
                    納品書{{ getDeliveryFiles(order).length > 1 ? ` (${getDeliveryFiles(order).length}件)` : '' }}
                  </button>
                </td>
                <td
                  class="ml-2 px-4 py-3 text-lg text-gray-900 whitespace-nowrap"
                >
                  <button
                    v-if="order.file_path || order.document_id"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-xs"
                    @click="openModal('approval', { order: order, document: order.document_id ? { document_id: order.document_id, user_name: order.order_user, evalution_date: order.document_evalution_date, desire_delivery_date: order.desire_delivery_date, supplier_name: order.com_name, price: order.price, quantity: order.quantity, calc_price: order.calc_price, name: order.name, s_name: order.s_name, title: order.document_title, content: order.document_content, main_reason: order.document_main_reason, sub_reason: order.document_sub_reason, approvals: order.order_request_approvals } : null, file_path: order.file_path ? `https://akioka.cloud/${order.file_path}` : null })"
                  >
                    稟議書
                  </button>
                </td>
                <td
                  class="ml-2 px-4 py-3 text-lg text-gray-900 whitespace-nowrap"
                >
                  <!-- <button
                    @click="orderComplete(order)"
                    :class="{
                      ' text-white font-bold py-2 px-4 rounded text-xs': true,
                      'bg-green-500 hover:bg-green-700':
                        order.order_complete_flg,
                      'bg-gray-500 hover:bg-gray-700':
                        !order.order_complete_flg,
                    }"
                  >
                    <span v-if="order.order_complete_flg"
                      >完了済<i class="ml-2 fas fa-check"></i
                    ></span>
                    <span v-else>未完了</span>
                  </button> -->

                  <select
                    name=""
                    v-model="order.order_complete_flg"
                    @change="orderComplete(order, $event.target.value)"
                    :class="{
                      ' font-bold py-2 px-4 rounded text-xs': true,
                      'text-white bg-green-500':
                        order.order_complete_flg === 1,
                      'text-white bg-blue-500':
                        order.order_complete_flg === 2,
                      'bg-gray-200 text-gray-700':
                        !order.order_complete_flg || order.order_complete_flg === 0,
                    }"
                  >
                    <option class="" :value="0">未完了</option>
                    <option class="" :value="1">発注済み</option>
                    <option class="" :value="2">返信済み</option>
                  </select>
                </td>
                <td
                  class="ml-2 px-4 py-3 text-lg text-gray-900 whitespace-nowrap"
                >
                  <span
                    @click="order.fax_parameter_id && order.fax_parameter_status === 1 ? openFaxParameter(order.fax_parameter_id) : null"
                    :class="{
                      'inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold transition-all': true,
                      'bg-green-100 text-green-800 cursor-pointer hover:bg-green-200 hover:shadow-md': order.fax_parameter_id && order.fax_parameter_status === 1,
                      'bg-yellow-100 text-yellow-800': order.fax_parameter_id && order.fax_parameter_status === 0,
                      'bg-gray-100 text-gray-600': !order.fax_parameter_id,
                    }"
                  >
                    <!-- 完了アイコン -->
                    <svg
                      v-if="order.fax_parameter_id && order.fax_parameter_status === 1"
                      class="w-3 h-3 mr-1"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M5 13l4 4L19 7"
                      />
                    </svg>
                    <!-- 待機中アイコン -->
                    <svg
                      v-else-if="order.fax_parameter_id && order.fax_parameter_status === 0"
                      class="w-3 h-3 mr-1"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                      />
                    </svg>
                    <!-- 未送信アイコン -->
                    <svg
                      v-else
                      class="w-3 h-3 mr-1"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
                      />
                    </svg>
                    {{
                      order.fax_parameter_id && order.fax_parameter_status === 1
                        ? '完了'
                        : order.fax_parameter_id && order.fax_parameter_status === 0
                        ? '待機中'
                        : '未送信'
                    }}
                    <!-- 外部リンクアイコン（完了時のみ表示） -->
                    <svg
                      v-if="order.fax_parameter_id && order.fax_parameter_status === 1"
                      class="w-3 h-3 ml-1"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"
                      />
                    </svg>
                  </span>
                </td>
                <td
                  v-if="is_login"
                  class="ml-2 px-4 py-3 text-lg text-gray-900 whitespace-nowrap"
                >
                  <button
                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-xs"
                    @click="deleteInitialOrder(order)"
                  >
                    削除
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Bottom Pagination -->
      </div>
      <div class="mt-6">
        <Pagination :links="initial_orders.links" />
      </div>

      <!-- モーダルウィンドウ（モダンなデザイン） -->
      <Transition name="modal">
        <div
          v-if="modal.isOpen"
          class="modal-overlay"
          @click.self="closeModal"
        >
          <div class="modal-container" @click.stop>
            <!-- モーダルヘッダー -->
            <div class="modal-header">
              <h3 class="modal-title">
                <span v-if="modal.type === 'image'">画像</span>
                <span v-else-if="modal.type === 'purchase'">発注書</span>
                <span v-else-if="modal.type === 'delivery'">納品書</span>
                <span v-else-if="modal.type === 'approval'">稟議書</span>
              </h3>
              <button
                @click="closeModal"
                class="modal-close-btn"
                aria-label="閉じる"
              >
                <svg
                  class="w-6 h-6"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                  />
                </svg>
              </button>
            </div>

            <!-- モーダルコンテンツ -->
            <div class="modal-content">
              <!-- 画像表示 -->
              <div v-if="modal.type === 'image' && modal.data" class="modal-image-viewer">
                <img :src="modal.data.path" alt="画像" />
              </div>

              <!-- 発注書表示 -->
              <div v-else-if="modal.type === 'purchase' && modal.data" class="modal-purchase-viewer">
                <Purchase
                  :current_month_holidays="props.current_month_holidays"
                  :next_month_holidays="props.next_month_holidays"
                  :orders="modal.data.orders"
                  :admin_user="current_admin_user"
                  @update-delivery-date="handleDeliveryDateUpdate"
                />
              </div>

              <!-- 納品書表示（複数対応） -->
              <div
                v-else-if="modal.type === 'delivery' && modal.data && modal.data.files && modal.data.files.length > 0"
                class="modal-delivery-viewer"
              >
                <!-- ファイルアップロードエリア -->
                <!-- <div class="delivery-upload-section">
                  <label
                    for="dropzone-file"
                    class="delivery-upload-area"
                  >
                    <div class="upload-content">
                      <svg
                        class="upload-icon"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                        />
                      </svg>
                      <p class="upload-text">
                        <span class="font-semibold">クリックしてアップロード</span> または ドラッグ&ドロップ
                      </p>
                      <p class="upload-hint">
                        納品書を変更する場合は、こちらから再設定してください
                      </p>
                    </div>
                    <input
                      id="dropzone-file"
                      type="file"
                      accept="image/*"
                      class="hidden"
                      @change="fileUpload($event, modal.data.order.id)"
                    />
                  </label>
                </div> -->

                <!-- 複数納品書のナビゲーション -->
                <div
                  v-if="modal.data.files.length > 1"
                  class="delivery-navigation"
                >
                  <button
                    @click="prevDeliveryFile"
                    :disabled="modal.currentIndex === 0"
                    class="nav-btn"
                    :class="{ disabled: modal.currentIndex === 0 }"
                  >
                    <svg
                      class="w-5 h-5"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 19l-7-7 7-7"
                      />
                    </svg>
                    前へ
                  </button>
                  <span class="nav-counter">
                    {{ modal.currentIndex + 1 }} / {{ modal.data.files.length }}
                  </span>
                  <button
                    @click="nextDeliveryFile"
                    :disabled="modal.currentIndex === modal.data.files.length - 1"
                    class="nav-btn"
                    :class="{ disabled: modal.currentIndex === modal.data.files.length - 1 }"
                  >
                    次へ
                    <svg
                      class="w-5 h-5"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 5l7 7-7 7"
                      />
                    </svg>
                  </button>
                </div>

                <!-- 納品書画像表示 -->
                <div class="delivery-image-container">
                  <!-- 納品日表示（1枚でも複数枚でも表示） -->
                  <div 
                    v-if="modal.data && modal.data.files && modal.data.files[modal.currentIndex] && modal.data.files[modal.currentIndex].delivery_date" 
                    class="delivery-date-info"
                  >
                    <span class="delivery-date-label">納品日:</span>
                    <span class="delivery-date-value">
                      {{
                        new Date(modal.data.files[modal.currentIndex].delivery_date).toLocaleDateString("ja-JP", {
                          year: "numeric",
                          month: "2-digit",
                          day: "2-digit",
                        })
                      }}
                    </span>
                  </div>
                  <img
                    :src="modal.data.files[modal.currentIndex].path"
                    alt="納品書"
                    class="delivery-image"
                  />
                </div>
              </div>

              <!-- 稟議書表示 -->
              <div v-else-if="modal.type === 'approval' && modal.data" class="modal-approval-viewer">
                <iframe
                  v-if="modal.data.file_path"
                  ref="pdfViewer"
                  :src="modal.data.file_path"
                  class="approval-iframe"
                ></iframe>
                <div v-else-if="modal.data.document" class="approval-document-container">
                  <ApprovalDocument :approval_document="modal.data.document" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </Transition>

      <!-- Search Loading Component -->
      <SearchLoading
        :isLoading="isSearchLoading"
        title="検索中..."
        message="発注データを取得しています。しばらくお待ちください。"
      />
    </template>
  </MainLayout>
</template>
<style scoped lang="scss">
// Header Section
.header-section {
  .header-content {
    @apply flex items-start justify-between;
  }
}

// Search Section
.search-section {
  .search-card {
    @apply bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden;

    .search-header {
      @apply p-6 bg-gray-50 border-b border-gray-100;

      .search-title {
        @apply text-xl font-semibold text-gray-800 flex items-center;
      }
    }

    .search-content {
      @apply p-6;

      .sort-section {
        @apply mb-6;

        .sort-label {
          @apply block text-sm font-medium text-gray-700 mb-3;
        }

        .sort-buttons {
          @apply flex gap-2;

          .sort-btn {
            @apply flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors duration-200;

            &.active {
              @apply bg-blue-600 text-white border-blue-600;
            }
          }
        }
      }

      .filter-grid {
        @apply grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-6;

        .filter-item {
          .filter-label {
            @apply block text-sm font-medium text-gray-700 mb-2;
          }

          .filter-input {
            @apply w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent;
          }

          .filter-select {
            @apply w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white;
          }

          .input-with-icon {
            @apply relative;

            .input-icon {
              @apply absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400;
            }

            .filter-input {
              @apply pl-10;
            }
          }

          &.date-range {
            .date-range-inputs {
              @apply flex items-center gap-2;

              .date-input {
                @apply flex-1;
              }

              .date-separator {
                @apply text-gray-500 font-medium;
              }
            }
          }
        }
      }

      .search-actions {
        @apply flex gap-3 justify-end;

        .action-btn {
          @apply flex items-center px-6 py-2 rounded-lg font-medium transition-colors duration-200;

          &.primary {
            @apply bg-blue-600 hover:bg-blue-700 text-white;
          }

          &.secondary {
            @apply bg-gray-100 hover:bg-gray-200 text-gray-700;
          }
        }
      }
    }
  }
}

// Statistics Section
.stats-section {
  .stats-header {
    .stats-title {
      @apply text-2xl font-bold text-gray-800 flex items-center;
    }
  }

  .stats-grid {
    @apply grid grid-cols-1 md:grid-cols-3 gap-6;

    .stat-card {
      @apply bg-white rounded-xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300;

      .stat-header {
        @apply flex items-start gap-4 mb-4;

        .stat-icon {
          @apply w-12 h-12 rounded-lg flex items-center justify-center;
        }

        .stat-content {
          .stat-title {
            @apply text-lg font-semibold text-gray-800;
          }

          .stat-subtitle {
            @apply text-sm text-gray-600;
          }
        }
      }

      .stat-values {
        .stat-value-item {
          @apply flex items-baseline gap-1 mb-2;

          .stat-value {
            @apply text-3xl font-bold text-gray-900;
          }

          .stat-unit {
            @apply text-lg text-gray-600;
          }
        }

        .stat-amount {
          @apply text-lg font-semibold text-gray-700;

          &.large {
            @apply text-2xl font-bold text-gray-900;
          }
        }
      }

      &.search-stats {
        .stat-icon {
          @apply bg-blue-100 text-blue-600;
        }
      }

      &.monthly-stats {
        .stat-icon {
          @apply bg-green-100 text-green-600;
        }
      }

      &.performance-stats {
        .stat-icon {
          @apply bg-purple-100 text-purple-600;
        }
      }
    }
  }
}

// Table Section
.table-controls {
  @apply flex items-center justify-between;

  .controls-left {
    // Pagination styles will be inherited from Pagination component
  }

  .controls-right {
    .batch-order-btn {
      @apply bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center;
    }
  }
}

.modern-table-container {
  @apply bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden;

  .table-wrapper {
    @apply overflow-x-auto;

    .modern-table {
      @apply w-full min-w-max;

      thead {
        @apply bg-gray-50;

        th {
          @apply px-6 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider border-b border-gray-200;

          &:first-child {
            @apply rounded-tl-lg;
          }

          &:last-child {
            @apply rounded-tr-lg;
          }
        }
      }

      tbody {
        @apply bg-white divide-y divide-gray-200;

        tr {
          &.bg-green-50 {
            background-color: #f0fdf4;

          }

          td {
            @apply px-6 py-4 whitespace-nowrap text-sm text-gray-900;

            input,
            select,
            textarea {
              @apply w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent;
            }

            button {
              @apply px-3 py-1 rounded text-xs font-medium transition-colors duration-200;
            }

            img {
              @apply w-16 h-16 object-cover rounded-lg shadow-sm cursor-pointer hover:shadow-md transition-shadow duration-200;
            }
          }
        }
      }
    }
  }

  .table-footer {
    @apply p-4 bg-gray-50 border-t border-gray-200 flex justify-center mt-8;
  }
}

// Responsive Design
@media (max-width: 1024px) {
  .header-content {
    @apply flex-col gap-6;
  }

  .filter-grid {
    @apply grid-cols-1 md:grid-cols-2;
  }

  .stats-grid {
    @apply grid-cols-1 md:grid-cols-2;
  }

  .table-controls {
    @apply flex-col gap-4 items-stretch;
  }
}

@media (max-width: 768px) {
  .search-actions {
    @apply flex-col;
  }

  .stats-grid {
    @apply grid-cols-1;
  }

  .filter-grid {
    @apply grid-cols-1;
  }
}

// モーダルオーバーレイ
.modal-overlay {
  position: fixed;
  inset: 0;
  background-color: rgba(0, 0, 0, 0.75);
  backdrop-filter: blur(4px);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
}

// モーダルコンテナ
.modal-container {
  background: white;
  border-radius: 16px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  width: 90vw;
  max-width: 1200px;
  height: 90vh;
  max-height: 900px;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

// モーダルヘッダー
.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem 2rem;
  border-bottom: 1px solid #e5e7eb;
  background: linear-gradient(to right, #f9fafb, #ffffff);

  .modal-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #111827;
    margin: 0;
  }

  .modal-close-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.5rem;
    background-color: #f3f4f6;
    color: #6b7280;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;

    &:hover {
      background-color: #e5e7eb;
      color: #374151;
      transform: rotate(90deg);
    }

    &:active {
      transform: rotate(90deg) scale(0.95);
    }
  }
}

// モーダルコンテンツ
.modal-content {
  flex: 1;
  overflow-y: auto;
  overflow-x: hidden;
  padding: 2rem;
  min-height: 0; // flexboxでスクロールを有効にするため
}

// 画像ビューアー
.modal-image-viewer {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 400px;
  width: 100%;
  max-width: 100%;
  overflow: hidden;

  img {
    max-width: 100%;
    max-height: calc(90vh - 250px);
    width: auto;
    height: auto;
    object-fit: contain;
    border-radius: 8px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  }
}

// 発注書ビューアー
.modal-purchase-viewer {
  display: flex;
  flex-direction: column;
  min-height: 400px;
  width: 100%;
  max-width: 100%;
  overflow-x: hidden;
  overflow-y: auto;

  // Purchaseコンポーネント内のテーブルが横にはみ出さないように
  > * {
    max-width: 100%;
    overflow-x: auto;
  }

  // テーブルが横にはみ出さないように
  table {
    width: 100%;
    max-width: 100%;
    table-layout: fixed;
    word-wrap: break-word;
  }
}

// 納品書ビューアー
.modal-delivery-viewer {
  width: 100%;
  max-width: 100%;
  overflow-x: hidden;
  display: flex;
  flex-direction: column;
  min-height: 0;

  .delivery-upload-section {
    margin-bottom: 2rem;
    width: 100%;
    max-width: 100%;

    .delivery-upload-area {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 3rem;
      border: 2px dashed #d1d5db;
      border-radius: 12px;
      background: linear-gradient(to bottom, #f9fafb, #ffffff);
      cursor: pointer;
      transition: all 0.3s ease;
      width: 100%;
      box-sizing: border-box;

      &:hover {
        border-color: #3b82f6;
        background: linear-gradient(to bottom, #eff6ff, #f0f9ff);
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.1);
      }

      .upload-content {
        text-align: center;
        width: 100%;

        .upload-icon {
          width: 3rem;
          height: 3rem;
          color: #6b7280;
          margin-bottom: 1rem;
        }

        .upload-text {
          font-size: 0.875rem;
          color: #374151;
          margin-bottom: 0.5rem;
        }

        .upload-hint {
          font-size: 0.75rem;
          color: #9ca3af;
        }
      }
    }
  }

  .delivery-navigation {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1.5rem;
    padding: 1rem;
    background: linear-gradient(to right, #f9fafb, #ffffff);
    border-radius: 12px;
    margin-bottom: 2rem;
    border: 1px solid #e5e7eb;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
    flex-wrap: wrap;

    .nav-btn {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.75rem 1.5rem;
      background: linear-gradient(to right, #3b82f6, #2563eb);
      color: white;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s ease;
      box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
      white-space: nowrap;

      &:hover:not(.disabled) {
        background: linear-gradient(to right, #2563eb, #1d4ed8);
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);
      }

      &:active:not(.disabled) {
        transform: translateY(0);
      }

      &.disabled {
        background: #e5e7eb;
        color: #9ca3af;
        cursor: not-allowed;
        box-shadow: none;
      }
    }

    .nav-counter {
      font-size: 1.125rem;
      font-weight: 700;
      color: #374151;
      min-width: 4rem;
      text-align: center;
      white-space: nowrap;
    }
  }

  .delivery-image-container {
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
    flex: 1;
    min-height: 400px;
    max-height: calc(90vh - 350px);
    background: #f9fafb;
    border-radius: 12px;
    padding: 1rem;
    width: 100%;
    max-width: 100%;
    overflow-y: auto;
    overflow-x: hidden;
    box-sizing: border-box;

    .delivery-date-info {
      width: 100%;
      margin-bottom: 1rem;
      padding: 0.75rem 1rem;
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      display: flex;
      align-items: center;
      gap: 0.5rem;

      .delivery-date-label {
        font-weight: 600;
        color: #374151;
        font-size: 0.875rem;
      }

      .delivery-date-value {
        color: #1f2937;
        font-size: 0.875rem;
      }
    }

    .delivery-image {
      max-width: 100%;
      width: auto;
      height: auto;
      min-height: 0;
      object-fit: contain;
      border-radius: 8px;
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
      display: block;
    }
  }
}

// 稟議書ビューアー
.modal-approval-viewer {
  min-height: 400px;
  max-height: calc(90vh - 250px);
  width: 100%;
  max-width: 100%;
  overflow: hidden;

  .approval-iframe {
    width: 100%;
    height: 100%;
    min-height: 600px;
    max-height: calc(90vh - 250px);
    border: none;
    border-radius: 8px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  }

  .approval-document-container {
    padding: 1rem;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
    overflow-x: auto;
  }
}

// モーダルアニメーション（フェードイン）
.modal-enter-active {
  transition: opacity 0.2s ease;

  .modal-container {
    transition: transform 0.2s ease, opacity 0.2s ease;
  }
}

.modal-leave-active {
  transition: opacity 0.15s ease;

  .modal-container {
    transition: transform 0.15s ease, opacity 0.15s ease;
  }
}

.modal-enter-from {
  opacity: 0;

  .modal-container {
    transform: scale(0.95);
    opacity: 0;
  }
}

.modal-leave-to {
  opacity: 0;

  .modal-container {
    transform: scale(0.95);
    opacity: 0;
  }
}

// レスポンシブ対応
@media (max-width: 768px) {
  .modal-overlay {
    padding: 0;
  }

  .modal-container {
    width: 100vw;
    height: 100vh;
    max-width: 100vw;
    max-height: 100vh;
    border-radius: 0;
  }

  .modal-header {
    padding: 1rem 1.5rem;

    .modal-title {
      font-size: 1.25rem;
    }
  }

  .modal-content {
    padding: 1rem;
  }

  .modal-image-viewer {
    img {
      max-height: calc(100vh - 200px);
    }
  }

  .modal-purchase-viewer {
    min-height: auto;
  }

  .modal-delivery-viewer {
    .delivery-upload-section {
      .delivery-upload-area {
        padding: 2rem 1rem;
      }
    }

    .delivery-navigation {
      flex-wrap: wrap;
      gap: 1rem;

      .nav-btn {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
      }
    }

    .delivery-image-container {
      max-height: calc(100vh - 400px);
    }
  }

  .modal-approval-viewer {
    max-height: calc(100vh - 200px);

    .approval-iframe {
      max-height: calc(100vh - 200px);
    }
  }
}

#purchase_container {
  width: 297mm;
  height: 210mm;
  box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
  border: 1px solid rgba(221, 221, 221, 0.705);
  & #top_content {
    & > div {
      width: 30%;
    }

    & .center_container {
      border: 1px solid rgba(82, 82, 82, 0.555);
      border-radius: 3px;
    }
  }

  & #bottom_content {
    height: 16%;
    & textarea {
      height: 100%;
      border: 1px solid rgba(82, 82, 82, 0.555);
      border-radius: 3px;
    }
  }
}

#topContent {
  height: 30vh;
  display: flex;
  margin-bottom: 4vh;
  justify-content: space-between;

  & #cardContent {
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    height: 100%;

    & .contactCard {
      width: 24%;
      display: flex;
      flex-direction: column;
      text-align: center;
      justify-content: center;
      font-weight: bold;
      color: #fff;
      border-radius: 8px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      &:hover {
        transform: scale(1.02);
        transition: transform 0.2s ease;
      }

      & p {
        padding: 10px 20px;

        &.title {
          font-size: 1.2rem;
        }

        &.value {
          font-size: 2rem;
        }
      }
    }
  }

  & #graphContent {
    width: 56%;
    height: 100%;
    display: flex;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    background-color: white;
    padding: 1rem;

    & > div {
      width: 50%;
      height: 100%;
      display: flex;
      flex-direction: column;
      padding: 0.5rem;

      & h3 {
        font-size: 1rem;
        margin-bottom: 0.5rem;
      }

      & > div {
        flex: 1;
        position: relative;
        min-height: 0;
      }
    }
  }
}
</style>