<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { onMounted, reactive, ref } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";
import MainTitle from "@/Components/Title/MainTitle.vue";
import ApprovalDocument from "@/Components/Accept/ApprovalDocument.vue";
import SearchLoading from "@/Components/Loading/SearchLoading.vue";

const props = defineProps({
  order_users: Array,
  user_id: Number,
  devices: Array
});
const form = reactive({
  upload_file: null,
});
const modal_status = reactive({
  status: false,
  order_request: null,
  approval_path: null,
});

const sendDeviceMessage = () => {
  if (!modal_status.order_request.message) {
    alert("メッセージを入力してください。");
    return;
  }
  console.log(modal_status.order_request);
  // return
  axios
    .post(route("stock.sendDeviceMessage"), {
      order_request_id: modal_status.order_request.id,
      message: modal_status.order_request.message,
      user_id: order_config.user_id,
    })
    .then((res) => {
      console.log(res.data);
      if (res.data.status) {
        alert("メッセージを送信しました。");
      }
    })
    .catch((error) => {
      console.log(error);
    });
};

const editSupplier = (order_request) => {
  if (confirm("発注先を再読み込みしますか？")) {
    axios
      .post(route("stock.reloadSupplier"), {
        order_request_id: order_request.order_request_id,
      })
      .then((res) => {
        if (res.data.status) {
          alert("発注先を再ロードしました。");
          window.location.reload();
        }
      });
  }
  console.log(order_request);
};

const sendRejectInitialOrder = (order_request_id) => {
  axios
    .post(route("stock.accept.order_request.reject"), {
      order_request_id: order_request_id,
    })
    .then((res) => {
      console.log(res.data);
      if (res.data.status) {
        alert("拒否通知が完了しました。");
        const order_request = order_requests.value.find(
          (order_request) => order_request.id === order_request_id
        );
        if (order_request) {
          order_request.accept_flg = 4;
        }
      }
    });
};

const uploadFile = (event) => {
  const file = event.target.files[0];
  form.upload_file = file;

  const formData = new FormData();

  // upload_fileを追加
  if (form.upload_file instanceof File) {
    formData.append("upload_file", form.upload_file);
  }

  // modal_status.order_request.idを追加
  if (modal_status.order_request && modal_status.order_request.id) {
    formData.append("order_request_id", modal_status.order_request.id);
  }

  // formオブジェクトをFormDataに変換
  Object.keys(form).forEach((key) => {
    // nullでない値のみを追加（upload_fileは既に追加済みなので除外）
    if (form[key] !== null && key !== "upload_file") {
      formData.append(key, form[key]);
    }
  });

  axios
    .post(route("stock.store.approval_document"), formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    })
    .then((res) => {
      console.log(res.data);
      if (res.data.status) {
        if (confirm("稟議書を登録しました。")) {
          window.location.reload();
        } else {
          // window.location.href = route("stock");
        }
      }
    })
    .catch((error) => {
      console.log(error);
    });

  console.log(form.upload_file);
};

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
const isDataLoading = ref(false);

const getOrderRequests = (user_id) => {
  // ローディング開始
  isDataLoading.value = true;
  
  axios
    .get(route("stock.getOrderRequests"), {
      params: {
        user_id: user_id,
      },
    })
    .then((res) => {
      console.log(res.data);
      order_requests.value = res.data.order_requests;
    })
    .catch((error) => {
      console.log(error);
    })
    .finally(() => {
      // ローディング終了
      setTimeout(() => {
        isDataLoading.value = false;
      }, 500);
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

// 見積もり確認中変更
const changeEstimate = (order_request_id) => {
  if (order_request_id) {
    axios
      .post(route("stock.accept.order_request.change-estimate"), {
        order_request_id: order_request_id,
        user_id: order_config.user_id,
      })
      .then((res) => {
        console.log(res.data);
        alert("確認中状態へ変更しました。");
        if (res.data.status) {
          const order_request = order_requests.value.find(
            (request) => request.id === order_request_id
          );

          if (order_request) {
            order_request.accept_flg = 5;
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

// 希望納期更新
const updateDesireDeliveryDate = (order_request) => {
  if (confirm("希望納期を変更しますか？")) {
    axios
      .put(route("stock.updateOrderRequest"), {
        order_request_id: order_request.id,
        desire_delivery_date: order_request.desire_delivery_date,
      })
      .then((res) => {
        console.log("Response:", res.data);
        if (res.data.status) {
          alert("希望納期を更新しました。");
        } else {
          alert("希望納期の更新に失敗しました。詳細はコンソールを確認してください。");
          console.error("Update failed:", res.data);
        }
      })
      .catch((error) => {
        console.error("Request error:", error);
        if (error.response) {
          console.error("Error response:", error.response.data);
          console.error("Error status:", error.response.status);
        }
        alert("希望納期の更新に失敗しました。詳細はコンソールを確認してください。");
      });
  }
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
const handleUserId = (user_id) => {
  localStorage.setItem("user_id", user_id);
  loginCheck();
};
const ClearLocalStorage = () => {
  localStorage.removeItem("user_id");
  loginCheck();
};

const loginCheck = () => {
  const userId = localStorage.getItem("user_id");
  if (userId && userId != "null") {
    console.log(userId);
    getOrderRequests(userId);

    const selectedUser = props.order_users.find((user) => user.id == userId);
    if (selectedUser) {
      order_config.user_name = selectedUser.name;
      order_config.user_id = userId;
      console.log(selectedUser.name);
    }
  } else {
    order_config.user_id = null;
    order_config.user_name = null;
  }
};

const changeStockId = (order_request_id, stock_id) => {
  console.log(order_request_id, stock_id);
  if (confirm(`在庫ID: ${stock_id} で登録を行います。よろしいですか？`)) {
    axios
      .post(route("stock.updateStockId"), {
        order_request_id: order_request_id,
        stock_id: stock_id,
      })
      .then((res) => {
        console.log(res.data);
        if (res.data.status) {
          windows.location.reload();
        }
      })
      .catch((error) => {
        console.log(error);
      });
  }
};

const setDeviceId = async (device_id, order_request_id) => {
  try {
    const response = await axios.post(route('stock.setDeviceId'), {
      device_id: device_id,
      order_request_id: order_request_id
    })
    
    if (response.data.status) {
      alert(response.data.msg)
      // データを再読み込み
      window.location.reload()
    } else {
      alert('エラー: ' + response.data.msg)
    }
  } catch (error) {
    console.error('デバイスID設定エラー:', error)
    alert('デバイスIDの設定に失敗しました')
  }
}

onMounted(() => {
  // デバイスID取得
  loginCheck();
});
</script>
<template>
  <MainLayout :title="'発注依頼一覧'">
    <template #content>
      <!-- Header Section -->
      <div class="header-section mb-8">
      <MainTitle
        :top="'発注依頼一覧'"
          :sub="'発注依頼の承認・完了処理ができます。'"
        />
      </div>

      <!-- Login Section -->
      <div v-if="!order_config.user_id" class="login-section mb-8">
        <div class="login-container">
          <div class="login-card">
            <div class="login-header">
              <div class="logo-container">
                <img
                  class="company-logo"
                  src="/img/base/AK_logo.png"
                  alt="会社ロゴ"
                />
              </div>
              <h2 class="login-title">発注担当者ログイン</h2>
              <p class="login-subtitle">発注依頼の管理・承認を行うためにログインしてください</p>
              </div>

            <div class="login-form">
              <div class="form-group">
                <label class="form-label">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                  ユーザー選択
                </label>
                      <select
                  class="form-select"
                        @change="handleUserId($event.target.value)"
                      >
                  <option value="0">ユーザーを選択してください</option>
                        <option
                          v-for="order_user in order_users"
                          :key="order_user.id"
                          :value="order_user.id"
                        >
                          {{ order_user.name }}
                        </option>
                      </select>
                  </div>

              <div class="login-help">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                アカウントがない場合は管理者にお問い合わせください
                  </div>
            </div>
              </div>
            </div>
          </div>

      <!-- User Dashboard -->
      <div v-else class="dashboard-section mb-8">
        <!-- User Info Card -->
        <div class="user-info-card mb-6">
          <div class="user-info-content">
            <div class="user-avatar">
              <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
            </div>
            <div class="user-details">
              <h2 class="user-name">{{ order_config.user_name }}</h2>
              <p class="user-role">発注担当者</p>
            </div>
          </div>
              <button
                @click="ClearLocalStorage"
            class="logout-btn"
              >
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
            ログアウト
              </button>
        </div>
        
        <!-- Orders Table Section -->
        <div class="orders-table-section">
          <div class="table-container">
            <div class="table-header mb-4">
              <h3 class="table-title">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                発注依頼一覧 ({{ order_requests.length }}件)
              </h3>
              <div v-if="contain_approvals.list.length > 0" class="batch-actions">
                <span class="selected-count">{{ contain_approvals.list.length }}件選択中</span>
              </div>
            </div>
            
            <div class="table-wrapper">
              <table class="modern-table">
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
                    在庫ID
                  </th>
                  <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap"
                  >
                    承認
                  </th>
                  <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap"
                  >
                    依頼品
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
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    希望納期
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
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    単価
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
                    発注依頼日時
                  </th>
                  <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    消化予定日
                  </th>

                  <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    依頼者
                  </th>
                  <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    依頼元端末
                  </th>
                  <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap"
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
                    'transition duration-300 border hover:bg-gray-300': true,
                    'bg-blue-50': order_request.select_flg,
                    'bg-gray-100 ':
                      order_request.order_user_id != order_config.user_id,
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
                  <td class="text-center">
                    <input
                      v-if="
                        order_request.accept_flg === 0 &&
                        !order_request.stock_id
                      "
                      type="number"
                      :value="order_request.stock_id"
                      class="w-24 appearance-none block bg-gray-200 text-gray-700 border border-gray-200 rounded py-4 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500 text-center"
                      @change="
                        changeStockId(order_request.id, $event.target.value)
                      "
                    />
                    <span v-else>{{ order_request.stock_id }}</span>
                  </td>
                  <td
                    :class="{
                      'px-4 py-4 text-lg text-gray-900': true,
                    }"
                  >
                    <div
                      v-if="order_config.user_id"
                      class="flex items-center justify-center"
                    >
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
                        @click="sendRejectInitialOrder(order_request.id)"
                        class="text-sm bg-red-500 hover:bg-red-700 text-white py-2 px-4 rounded-full"
                        v-else-if="order_request.accept_flg === 3"
                        >却下</span
                      >
                      <span
                        class="text-sm bg-gray-500 hover:bg-gray-700 text-white py-2 px-4 rounded-full"
                        v-else-if="order_request.accept_flg === 4"
                        >却下再依頼待ち</span
                      >
                      <button
                        v-else-if="order_request.accept_flg === 5"
                        @click="sendAccept(order_request.id)"
                        class="text-sm bg-purple-500 hover:bg-purple-700 text-white py-2 px-4 rounded-full"
                      >
                        確認中
                      </button>
                      <button
                        v-else-if="order_request.accept_flg === 6"
                        class="text-sm bg-yellow-500 hover:bg-yellow-700 text-white py-2 px-4 rounded-full"
                      >
                        差戻対応
                      </button>
                    </div>
                  </td>

                  <td class="px-4 py-4 text-lg text-gray-900">
                    <span
                      v-if="order_request.new_stock_flg"
                      class="bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-blue-900 dark:text-blue-300"
                      >新規品</span
                    >
                    <span
                      v-else
                      class="bg-orange-100 text-orange-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-orange-900 dark:text-orange-300"
                      >既存品</span
                    >
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
                  <td class="name px-4 py-4">
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
                  <td class="s_name px-4 py-4 text-lg text-gray-900">
                    {{
                      order_request.s_name
                        ? order_request.s_name
                        : order_request.order_request_s_name
                    }}
                  </td>
                  <td class="px-4 py-4 text-lg text-gray-900">
                    <input
                      v-if="order_config.user_id"
                      type="date"
                      class="appearance-none block w-48 bg-gray-200 text-gray-700 border border-gray-200 rounded py-4 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      :value="order_request.desire_delivery_date ? new Date(order_request.desire_delivery_date).toISOString().split('T')[0] : ''"
                      @change="
                        order_request.desire_delivery_date = $event.target.value;
                        updateDesireDeliveryDate(order_request)
                      "
                    />
                    <span v-else>
                      {{
                        new Date(
                          order_request.desire_delivery_date
                        ).toLocaleDateString("ja-JP")
                      }}
                    </span>
                  </td>
                  <td class="px-4 py-4 text-lg text-gray-900">
                    {{ order_request.now_quantity }}
                  </td>
                  <td class="px-4 py-4 text-lg text-gray-900">
                    {{ order_request.reorder_point }}
                  </td>
                  <td class="px-4 py-4 text-lg text-gray-900">
                    <input
                      type="number"
                      name=""
                      id=""
                      class="font-bold appearance-none block w-64 bg-gray-200 text-gray-700 border border-gray-200 rounded py-4 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      v-model="order_request.price"
                      @change="
                        updateQuantityPriceCalcPricePostage(
                          'price',
                          order_request
                        )
                      "
                    />
                  </td>
                  <td class="px-4 py-4 text-lg text-gray-900">
                    <input
                      type="number"
                      name=""
                      id=""
                      class="appearance-none block w-32 bg-gray-200 text-gray-700 border border-gray-200 rounded py-4 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
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

                  <td class="px-4 py-4 text-lg text-gray-900">
                    <input
                      type="number"
                      name=""
                      id=""
                      class="font-bold appearance-none block w-64 bg-gray-200 text-gray-700 border border-gray-200 rounded py-4 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      v-model="order_request.calc_price"
                      @change="
                        updateQuantityPriceCalcPricePostage(
                          'calc_price',
                          order_request
                        )
                      "
                    />
                  </td>
                  <td class="px-4 py-4 text-lg text-gray-900">
                    <input
                      type="number"
                      name=""
                      id=""
                      class="appearance-none block w-32 bg-gray-200 text-gray-700 border border-gray-200 rounded py-4 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
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
                    <span v-if="order_request.supplier_id">
                      <i
                        class="fas fa-sync-alt cursor-pointer"
                        @click="editSupplier(order_request)"
                      ></i>

                      {{ `${order_request.supplier_name}` }} ({{
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
                      new Date(order_request.created_at).toLocaleString(
                        "ja-JP",
                        {
                          year: "numeric",
                          month: "2-digit",
                          day: "2-digit",
                          hour: "2-digit",
                          minute: "2-digit",
                          hour12: false,
                        }
                      )
                    }}
                  </td>
                  <td class="px-4 py-4 text-lg text-gray-900">
                    {{
                      new Date(order_request.digest_date).toLocaleDateString(
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
                    <span v-if="order_request.device_id">{{
                      `${order_request.device_id ?? ""} - ${
                        order_request.device_name ?? ""
                      }`
                    }}</span>
                    <select v-else class="font-bold appearance-none block w-64 bg-gray-200 text-gray-700 border border-gray-200 rounded py-4 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" @change="setDeviceId($event.target.value, order_request.id)">
                      <option value="">未選択</option>
                      <option v-for="device in props.devices" :key="device.id" :value="device.id">{{ device.name }}</option>
                    </select>
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

                  <td
                    :class="{
                      'px-4 py-4 text-lg text-gray-900': true,
                    }"
                  >
                    <button
                      v-if="
                        order_request.stock_id && order_request.accept_flg == 0
                      "
                      @click="changeEstimate(order_request.order_request_id)"
                      class="text-sm bg-purple-500 hover:bg-purple-700 text-white py-2 px-4 rounded whitespace-nowrap"
                    >
                      確認中
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        </div>
      </div>

      <!-- モーダルウィンドウ -->
      <div id="modal" :class="{ active: modal_status.status }" @click.self="modal_status.status = false">
        <div class="modal__panel">
          <div id="close_container">
            <button
              @click="modal_status.status = !modal_status.status"
              class="modal__close"
              aria-label="Close modal"
            >
              <i class="fa fa-times"></i>
            </button>
          </div>

          <div class="modal__body">
            <div v-if="modal_status.order_request" class="mb-4">
          <p class="font-bold w-full text-sm text-gray-700">
            品名: {{ modal_status.order_request.name }}
          </p>
          <p class="font-bold w-full text-sm text-gray-700">
            品番: {{ modal_status.order_request.s_name }}
          </p>
          <p class="font-bold w-full text-sm text-gray-700">
            依頼者: {{ modal_status.order_request.request_user_name }}
          </p>
          <p class="font-bold w-full text-sm text-gray-700">
            依頼元デバイス: {{ modal_status.order_request.device_name }}
          </p>
          <hr class="my-4" />
          <h3
            class="block mb-2 text-medium font-medium text-gray-700 dark:text-white"
          >
            依頼者へ確認メッセージ
            <span class="text-sm text-red-500"
              >(※依頼元端末のTOP画面に送信されます)</span
            >
          </h3>
          <textarea
            id="message"
            rows="4"
            :class="{
              'block p-2.5 w-full text-sm text-gray-900 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500': true,
            }"
            placeholder="メッセージを入力してください。"
            v-model="modal_status.order_request.message"
          ></textarea>
          <div class="flex justify-end">
            <button
              @click.prevent="sendDeviceMessage"
              class="mt-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
            >
              <i class="fas fa-paper-plane"></i> 送信
            </button>
          </div>
          <div class="pl-4">
            <label
              for="message"
              class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
            >
              <i class="fas fa-user"></i> 回答メッセージ</label
            >
            <textarea
              id="message"
              rows="4"
              :class="{
                'block p-2.5 w-full text-sm text-gray-900 bg-gray-200 rounded-lg border border-gray-300 h-auto': true,
              }"
              placeholder="コメントがありません。"
              :value="modal_status.order_request.answer"
            ></textarea>
            <label
              v-if="modal_status.order_request.read_flg"
              for="visibility"
              class="text-right block mb-2 text-sm font-medium text-gray-900 dark:text-white"
            >
              <i class="fas fa-eye"></i> 確認済
            </label>
            <label
              v-else
              for="visibility"
              class="text-right block mb-2 text-sm font-medium text-gray-500 dark:text-white"
            >
              未確認
            </label>
          </div>

          <hr class="my-4" />
          <h3
            class="block mb-2 text-medium font-medium text-gray-700 dark:text-white"
          >
            承認用コメント
          </h3>
          <label
            for="message"
            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
            >依頼者コメント</label
          >
          <textarea
            id="message"
            rows="4"
            :class="{
              'block p-2.5 w-full text-sm text-gray-900 bg-gray-200 rounded-lg border border-gray-300 h-auto': true,
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
            class="mt-4"
            id="approval_container"
          >
            <div
              v-for="approval in modal_status.order_request
                .order_request_approvals"
              :key="approval.id"
              class="card"
            >
              <div
                class="card-header"
                :class="{
                  'is-approved': approval.status === 1,
                  'is-rejected': approval.status === 2,
                  'is-pending': approval.status === 0
                }"
              >
                <span class="status-badge">
                  {{ approval.status === 1 ? '承認' : approval.status === 2 ? '却下' : '未処理' }}
                </span>
                <i
                  :class="[
                    'status-icon fas',
                    approval.status === 1
                      ? 'fa-check-circle text-green-600'
                      : approval.status === 2
                      ? 'fa-times-circle text-red-600'
                      : 'fa-clock text-gray-500'
                  ]"
                  aria-hidden="true"
                ></i>

                <div
                  v-if="approval.status == 0"
                  class="notify_button flex space-x-2"
                >
                  <a
                    class="inline-block bg-blue-600 py-1 px-2.5 rounded"
                    :href="`https://akioka.cloud/accept/order-request?user_id=${approval.user_id}`"
                    target="_blank"
                  >
                    <i class="text-white fas fa-external-link-alt"></i>
                  </a>

                  <button
                    class="bg-blue-600 py-1 px-2.5 rounded"
                    @click.prevent="
                      reNotify(
                        modal_status.order_request.order_request_id,
                        approval.user_id
                      )
                    "
                  >
                    <i class="text-white fas fa-bell"></i>
                  </button>
                </div>
              </div>

              <div class="card-body">
                <div class="date-chip">
                  {{ new Date(approval.updated_at).getFullYear() }}年{{
                    new Date(approval.updated_at).getMonth() + 1
                  }}月{{ new Date(approval.updated_at).getDate() }}日
                  {{ new Date(approval.updated_at).getHours() }}時{{
                    new Date(approval.updated_at).getMinutes()
                  }}分
                </div>
                <div class="approver-name">{{ approval.name }}</div>
                <p class="comment">
                  {{
                    approval.comment
                      ? approval.comment
                      : 'コメントがありません。'
                  }}
                </p>
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
            <div v-if="modal_status.order_request?.document_data">
              <ApprovalDocument
                :approval_document="modal_status.order_request.document_data"
              />
            </div>
            <div
              v-if="modal_status.approval_path"
              class="mt-12 mb-8 bg-gray-100 p-4"
            >
              <!-- <h2 class="text-lg text-gray-700 font-bold mb-2">添付資料</h2> -->
              <iframe
                ref="pdfViewer"
                :src="modal_status.approval_path"
                style="height: 60vh; width: 96%; margin: 0 auto"
              ></iframe>
            </div>
          </div>
          <div v-if="!modal_status.approval_path" class="w-full mb-8">
            <label
              for="dropzone-file"
              :class="{
                'flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-green-600 dark:hover:border-green-500 dark:hover:bg-gray-600': true,
              }"
            >
              <div class="flex flex-col items-center justify-center pt-5 pb-6">
                <svg
                  class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400"
                  aria-hidden="true"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 20 16"
                >
                  <path
                    stroke="currentColor"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"
                  />
                </svg>
                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                  <span class="font-semibold text-lg">稟議書</span
                  >をアップロードしてください。
                </p>
                <p
                  class="text-xs text-green-500 dark:text-green-400 text-center"
                >
                  {{
                    form.upload_file
                      ? `${form.upload_file.name} が選択されています。`
                      : ""
                  }}
                  <br />
                </p>
              </div>
              <input
                id="dropzone-file"
                type="file"
                class="hidden"
                @change="uploadFile"
                accept="application/pdf"
              />
            </label>
          </div>
        </details>
          </div>
        </div>
      </div>
      
      <!-- Search Loading Component -->
      <SearchLoading 
        :isLoading="isDataLoading"
        title="データ取得中..."
        message="発注依頼データを取得しています。しばらくお待ちください。"
      />
    </template>
  </MainLayout>
</template>
<style scoped lang="scss">
// Header Section
.header-section {
  @apply mb-8;
}

// Login Section
.login-section {
  @apply min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 -mx-8 -mt-8 px-8 py-12;
  
  .login-container {
    @apply w-full max-w-md;
    
    .login-card {
      @apply bg-white rounded-2xl shadow-xl border border-gray-100 p-8;
      
      .login-header {
        @apply text-center mb-8;
        
        .logo-container {
          @apply mb-6;
          
          .company-logo {
            @apply mx-auto h-16 w-auto;
          }
        }
        
        .login-title {
          @apply text-2xl font-bold text-gray-900 mb-2;
        }
        
        .login-subtitle {
          @apply text-gray-600 leading-relaxed;
        }
      }
      
      .login-form {
        .form-group {
          @apply mb-6;
          
          .form-label {
            @apply flex items-center text-sm font-medium text-gray-700 mb-3;
          }
          
          .form-select {
            @apply w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white text-gray-900;
          }
        }
        
        .login-help {
          @apply flex items-start text-sm text-gray-500 bg-blue-50 p-3 rounded-lg;
        }
      }
    }
  }
}

// Dashboard Section
.dashboard-section {
  .user-info-card {
    @apply bg-white rounded-xl shadow-lg border border-gray-100 p-6 flex items-center justify-between;
    
    .user-info-content {
      @apply flex items-center gap-4;
      
      .user-avatar {
        @apply w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center;
      }
      
      .user-details {
        .user-name {
          @apply text-xl font-semibold text-gray-900;
        }
        
        .user-role {
          @apply text-sm text-gray-600;
        }
      }
    }
    
    .logout-btn {
      @apply bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center;
    }
  }
  
  .orders-table-section {
    @apply bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden;
    
    .table-container {
      .table-header {
        @apply p-6 bg-gray-50 border-b border-gray-100 flex items-center justify-between;
        
        .table-title {
          @apply text-xl font-semibold text-gray-800 flex items-center;
        }
        
        .batch-actions {
          @apply flex items-center gap-3;
          
          .selected-count {
            @apply bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold;
          }
        }
      }
      
      .table-wrapper {
        @apply overflow-x-auto;
        
        .modern-table {
          @apply w-full min-w-max;
          
          thead {
            @apply bg-gray-50;
            
            th {
              @apply px-6 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider border-b border-gray-200;
            }
          }
          
          tbody {
            @apply bg-white divide-y divide-gray-200;
            
            tr {
              @apply hover:bg-gray-50 transition-colors duration-150;
              
              &.bg-blue-50 {
                background-color: #eff6ff;
                
                &:hover {
                  background-color: #dbeafe;
                }
              }
              
              &.bg-gray-100 {
                background-color: #f3f4f6;
                
                &:hover {
                  background-color: #e5e7eb;
                }
              }
              
              td {
                @apply px-6 py-4 whitespace-nowrap text-sm text-gray-900;
                
                input, select, textarea {
                  @apply px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent;
                }
                
                button {
                  @apply px-3 py-2 rounded-lg text-xs font-medium transition-colors duration-200;
                }
                
                img {
                  @apply w-16 h-16 object-cover rounded-lg shadow-sm;
                }

    &.img_container {
                  @apply p-2;
                  
                  img {
                    @apply w-20 h-20 object-contain;
                  }
                }
                
                &.name, &.s_name {
                  @apply max-w-xs;
                  
                  a {
                    @apply text-blue-600 hover:text-blue-800 hover:underline transition-colors duration-200;
                  }
                }
              }
            }
          }
        }
      }
    }
  }
}

// Modern Modal
#modal {
  @apply fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center opacity-0 pointer-events-none transition-opacity;

  &.active {
    @apply opacity-100 pointer-events-auto;
  }

  .modal__panel {
    @apply w-11/12 max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden transform scale-95 transition-transform;
    max-height: 80vh;
  }

  &.active .modal__panel {
    @apply scale-100;
  }

  #close_container {
    @apply p-4 flex justify-end border-b border-gray-100;
    
    .modal__close {
      @apply bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-800 px-3 py-2 rounded-lg transition-colors duration-200 flex items-center;
    }
  }
  
  .modal__body {
    @apply p-6 overflow-y-auto;
    max-height: calc(80vh - 64px);
  }
  
  #pdfviewer {
    @apply mb-4;
    
    iframe {
      @apply w-full rounded-lg border border-gray-200;
      height: 60vh;
    }
  }
  
  details {
    @apply border border-gray-200 rounded-lg overflow-hidden mb-4;
    
    summary {
      @apply bg-gray-50 p-4 font-semibold text-gray-800 cursor-pointer hover:bg-gray-100 transition-colors duration-200;
    }
    
    &[open] summary {
      @apply border-b border-gray-200;
    }
  }
  
  #approval_container {
    @apply p-4 flex flex-col gap-4;

.card {
      @apply bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-200 relative;
      
      .card-header {
        @apply relative h-28 flex items-center justify-between px-4 py-3;
        
        &.is-approved { @apply bg-green-50; }
        &.is-rejected { @apply bg-red-50; }
        &.is-pending { @apply bg-gray-50; }

        .status-badge {
          @apply text-xs font-bold px-2 py-1 rounded-full shadow-sm;
        }
        &.is-approved .status-badge { @apply bg-green-100 text-green-700; }
        &.is-rejected .status-badge { @apply bg-red-100 text-red-700; }
        &.is-pending .status-badge { @apply bg-gray-200 text-gray-700; }

        .status-icon { @apply text-2xl; }
      }
      
      .card-body {
        @apply px-4 py-3;
        
        .date-chip {
          @apply inline-block text-[11px] bg-gray-100 text-gray-700 px-2 py-1 rounded-full mb-2;
        }
        .approver-name { @apply font-bold text-base text-gray-900 mb-1; }
        .comment { @apply text-sm text-gray-600 leading-relaxed; }
      }
      .notify_button {
        @apply absolute top-2 right-2 flex gap-1 z-10;
        
        a, button {
          @apply w-8 h-8 rounded-full flex items-center justify-center shadow-sm hover:shadow-md transition-shadow duration-200;
        }
      }
    }
  }
  
  textarea {
    @apply w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none;
  }
  
  label {
    @apply block text-sm font-medium text-gray-700 mb-2;
  }
}

// Responsive Design
@media (max-width: 1024px) {
  .dashboard-section {
    .user-info-card {
      @apply flex-col gap-4 items-stretch;
    }
  }
  
  #modal {
    @apply w-full left-0 transform-none;
    
    &.active {
      transform: translateY(0);
    }
  }
}

@media (max-width: 768px) {
  .login-section {
    @apply px-4;
    
    .login-card {
      @apply p-6;
    }
  }
  
  #approval_container {
    @apply grid-cols-1;
  }
}

</style>