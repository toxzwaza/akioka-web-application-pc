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
  classifications: Array
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

const modal_status = reactive({
  initial_order_id: null,
  type: null,
  status: false,
  img_path: "",
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
const openModal = (img_path, order, flg) => {
  modal_status.img_path = "";

  switch (flg) {
    case "img": //画像表示
      modal_status.type = "img";
      modal_status.img_path = getImgPath(img_path);
      break;
    case "purchase": //発注書表示
      modal_status.type = "purchase";
      print_order.value = [order];
      break;
    case "multi":
      modal_status.type = "purchase";
      print_order.value = purchase_list.value;
      console.log("purchase_list", purchase_list.value);
      console.log("print_order", print_order.value);
      break;
    case "deli_file": //納品書表示
      modal_status.type = "deli_file";
      modal_status.initial_order_id = order.id;
      modal_status.img_path =
        img_path && img_path.includes("storage")
          ? `https://akioka.cloud/${img_path}`
          : img_path.includes("deli_file")
          ? `https://akioka.cloud/storage/${img_path}`
          : img_path;
      break;
    case "approval":
      modal_status.type = "approval";

      if (order.document_id) {
        console.log("稟議書を表示--->", order);
        approval_document.document_id = order.document_id;
        approval_document.user_name = order.order_user;
        approval_document.evalution_date = order.document_evalution_date; //評価日
        approval_document.desire_delivery_date = order.desire_delivery_date; //希望日
        approval_document.supplier_name = order.com_name;
        approval_document.price = order.price;
        approval_document.quantity = order.quantity;
        approval_document.calc_price = order.calc_price;
        approval_document.name = order.name;
        approval_document.s_name = order.s_name;
        approval_document.title = order.document_title;
        approval_document.content = order.document_content;
        approval_document.main_reason = order.document_main_reason;
        approval_document.sub_reason = order.document_sub_reason;
        approval_document.approvals = order.order_request_approvals;
      } else {
        modal_status.img_path = `https://akioka.cloud/${order.file_path}`;
      }

      console.log(modal_status.img_path);
      break;
  }

  modal_status.status = true;
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

// 納入希望日を保存する
const handleDeliveryDateUpdate = (date) => {
  console.log("選択された納入希望日:", date, print_order.value);
  axios
    .post(route("stock.update_desired_delivery_date"), {
      initial_order_id: print_order.value.id,
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

const login = () => {
  if (props.admin_users.some((user) => user.password == pwd.value)) {
    is_login.value = true;
  }
};

const getInitialOrders = (reset) => {
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

    console.log("検索条件リセット");
  }

  router.get(route("stock.initialOrders"), {
    order_by: form.order_by,
    keyword: form.keyword,
    start_order_date: form.start_order_date,
    end_order_date: form.end_order_date,
    supplier_id: form.supplier_id,
    order_user_id: form.order_user_id,
    user_id: form.user_id,
    group_id: form.group_id,
    process_id: form.process_id,
    classification_id: form.classification_id
  });
};

// 発注完了登録
const orderComplete = (order) => {
  let flg;

  if (order.order_complete_flg) {
    flg = 0;
  } else {
    flg = 1;
  }

  axios
    .post(route("stock.updateOrderComplete"), {
      initial_order_id: order.id,
      order_complete_flg: flg,
    })
    .then((res) => {
      console.log(res.data);
      if (res.data.status) {
        order.order_complete_flg = flg;
      }
    })
    .catch((error) => {
      console.log(error);
    });
};

onMounted(() => {
  console.log(props.initial_orders);

  initial_orders.value.data = props.initial_orders.data;
  initial_orders.value.links = props.initial_orders.links;
  base_initial_orders.value = props.initial_orders.data;

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

  console.log(props.totals);
});

// 納品書更新

const fileUpload = async (event) => {
  const file = event.target.files[0];
  if (file) {
    if (!confirm("ファイルが選択されました。納品書を更新しますか？")) {
      alert("納品書更新を中止しました。");
      return;
    }

    const formData = new FormData();
    formData.append("file", file);
    formData.append("initial_order_id", modal_status.initial_order_id);
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
const deleteInitialOrder = order => {
  console.log(order)
  const msg = `
  以下の物品を削除してもよろしいですか？
  依頼者: ${order.order_user}
  発注担当者: ${order.manage_user_name}
  品名: ${order.name}
  品番: ${order.s_name}
  単価: ${order.price}
  数量: ${order.quantity}
  金額: ${order.calc_price}
  `
  if(confirm(msg)){
    axios.delete(route('stock.delete.initialOrders'), {
      params: {
        initial_order_id : order.id
      }
    })
    .then(res => {
      console.log(res.data)
      if(res.data.status){
        alert('削除が完了しました。')
      }
    })
    .catch(error => {
      console.log(error)
    })
  }
}
</script>
<template>
  <MainLayout :title="'発注一覧'">
    <template #content>
      <div class="flex justify-between">
        <MainTitle
          :top="'発注一覧'"
          :sub="'発注情報の確認ができます。ログインすることで、品名・品番の修正が可能です。'"
        />
        <form class="w-full max-w-sm">
          <div class="flex items-center border-b border-blue-500 py-2">
            <input
              class="appearance-none bg-transparent border-none w-full text-gray-700 mr-3 py-1 px-2 leading-tight focus:outline-none"
              type="text"
              placeholder="生年月日"
              v-model="pwd"
            />
            <button
              @click.prevent="login()"
              class="flex-shrink-0 bg-blue-500 hover:bg-blue-700 border-blue-500 hover:border-blue-700 text-sm border-4 text-white py-1 px-2 rounded"
              type="button"
            >
              Sign Up
            </button>
            <button
              class="flex-shrink-0 border-transparent border-4 text-blue-500 hover:text-blue-800 text-sm py-1 px-2 rounded"
              type="button"
            >
              Cancel
            </button>
          </div>
        </form>
      </div>

      <section class="text-gray-600 body-font">
        <div class="py-12 mx-auto">
          <div id="sort_container" class="my-8 flex items-start justify-start">
            <div class="w-1/6">
              <p class="mb-2 font-bold">並び順</p>
              <div class="button_container flex items-center justify-start">
                <!-- <button
                  :class="{
                    'mr-4 text-sm bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded': true,
                  }"
                  @click="updateFilter('reset')"
                >
                  リセット
                </button> -->

                <button
                  :class="{
                    'mr-2 text-sm bg-blue-500  text-white font-bold py-2 px-4 rounded': true,
                    'opacity-60': form.order_by != 'desc',
                  }"
                  @click="form.order_by = 'desc'"
                >
                  新しい順
                </button>
                <button
                  :class="{
                    'mr-2 text-sm bg-blue-500  text-white font-bold py-2 px-4 rounded': true,
                    'opacity-60': form.order_by != 'asc',
                  }"
                  @click="form.order_by = 'asc'"
                >
                  古い順
                </button>
              </div>
            </div>

            <div class="w-5/6">
              <div class="mr-8">
                <p class="mb-2 font-bold">検索</p>
                <div class="button_container flex items-bottom justify-start">
                  <div class="w-62 mr-2">
                    <label
                      class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                      for="grid-last-name"
                    >
                      品名・品番から検索
                    </label>
                    <input
                      class="appearance-none block w-64 bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      type="text"
                      name=""
                      id=""
                      v-model="form.keyword"
                    />
                  </div>
                  <div class="mr-2">
                    <label
                      class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                      for="grid-last-name"
                    >
                      注文日
                    </label>
                    <div class="flex items-center">
                      <input
                        type="date"
                        name=""
                        id=""
                        class="appearance-none block w-64 bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500 mr-2"
                        v-model="form.start_order_date"
                      />
                      ～
                      <input
                        type="date"
                        name=""
                        id=""
                        class="appearance-none block w-64 bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500 ml-2"
                        v-model="form.end_order_date"
                      />
                    </div>
                  </div>

                  <div class="w-32 mr-6">
                    <label
                      class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                      for="grid-last-name"
                    >
                      注文先
                    </label>
                    <select
                      @change="updateFilter('com_name', $event.target.value)"
                      class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      name="order_user"
                      id=""
                      v-model="form.supplier_id"
                    >
                      <option value="0">未選択</option>
                      <option
                        v-for="supplier in props.suppliers"
                        :key="supplier.id"
                        :value="supplier.id"
                      >
                        {{ supplier.name }}
                      </option>
                    </select>
                  </div>
                  <div class="w-32 mr-2">
                    <label
                      class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                      for="grid-last-name"
                    >
                      依頼部門（大区分）
                    </label>
                    <select
                      @change="updateFilter('group', $event.target.value)"
                      class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      name="order_user"
                      id=""
                      v-model="form.group_id"
                    >
                      <option value="0">未選択</option>
                      <option
                        v-for="group in props.groups"
                        :key="group.id"
                        :value="group.id"
                      >
                        {{ group.name }}
                      </option>
                    </select>
                  </div>
                  <div class="w-32 mr-2">
                    <label
                      class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                      for="grid-last-name"
                    >
                      依頼部門（中区分）
                    </label>
                    <select
                      @change="updateFilter('process', $event.target.value)"
                      class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      name="order_user"
                      id=""
                      v-model="form.process_id"
                    >
                      <option value="0">未選択</option>
                      <option
                        v-for="process in props.processes"
                        :key="process.id"
                        :value="process.id"
                      >
                        {{ process.name }}
                      </option>
                    </select>
                  </div>
                  <div class="w-32 mr-2">
                    <label
                      class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                      for="grid-last-name"
                    >
                      依頼者
                    </label>
                    <select
                      @change="updateFilter('order_user', $event.target.value)"
                      class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      name="order_user"
                      id=""
                      v-model="form.order_user_id"
                    >
                      <option value="0">未選択</option>
                      <option
                        v-for="user in props.order_users"
                        :key="user.id"
                        :value="user.id"
                      >
                        {{ user.name }}
                      </option>
                    </select>
                  </div>
                  <div class="w-32 mr-2 ml-4">
                    <label
                      class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                      for="grid-last-name"
                    >
                      担当者
                    </label>
                    <select
                      @change="updateFilter('order_user', $event.target.value)"
                      class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      name="order_user"
                      id=""
                      v-model="form.user_id"
                    >
                      <option value="0">未選択</option>
                      <option
                        v-for="user in props.users"
                        :key="user.id"
                        :value="user.id"
                      >
                        {{ user.name }}
                      </option>
                    </select>
                  </div>
                  <div class="w-32 mr-2 ml-4">
                    <label
                      class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                      for="grid-last-name"
                    >
                      カテゴリー
                    </label>
                    <select
                     
                      class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      name="order_user"
                      id=""
                      v-model="form.classification_id"
                    >
                      <option value="0">未選択</option>
                      <option
                        v-for="classification in props.classifications"
                        :key="classification.id"
                        :value="classification.id"
                      >
                        {{ classification.name }}
                      </option>
                    </select>
                  </div>
                  <!-- <div class="w-32 mr-2">
                    <label
                      class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                      for="grid-last-name"
                    >
                      納品書
                    </label>
                    <select
                      @change="
                        updateFilter('delifile_path', $event.target.value)
                      "
                      class="appearance-none block w-64 bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      name="order_user"
                      id=""
                    >
                      <option value="0">未選択</option>
                      <option value="1">済</option>
                      <option value="2">未</option>
                    </select>
                  </div>
                  <div class="w-32 mr-2">
                    <label
                      class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                      for="grid-last-name"
                    >
                      稟議書
                    </label>
                    <select
                      @change="
                        updateFilter('delifile_path', $event.target.value)
                      "
                      class="appearance-none block w-64 bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      name="order_user"
                      id=""
                    >
                      <option value="0">未選択</option>
                      <option value="1">済</option>
                      <option value="2">未</option>
                    </select>
                  </div>
                  <div class="w-32 mr-2">
                    <label
                      class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                      for="grid-last-name"
                    >
                      ステータス
                    </label>
                    <select
                      @change="
                        updateFilter('delifile_path', $event.target.value)
                      "
                      class="appearance-none block w-64 bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      name="order_user"
                      id=""
                    >
                      <option value="0">未選択</option>
                      <option value="1">済</option>
                      <option value="2">未</option>
                    </select>
                  </div> -->

                  <button
                    @click="getInitialOrders()"
                    class="ml-4 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
                  >
                    検索
                  </button>
                  <button
                    @click="getInitialOrders('reset')"
                    class="ml-4 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                  >
                    リセット
                  </button>
                </div>
              </div>
            </div>
          </div>

          <hr class="my-8" />

          <section id="topContent" class="">
            <div id="cardContent">
              <div class="contactCard bg-gray-500">
                <p class="title">検索合計発注数</p>
                <hr class="my-1" />
                <p class="value">{{ props.totals.total_order_count }}件</p>
              </div>
              <div class="contactCard bg-gray-500">
                <p class="title">検索合計金額</p>
                <hr class="my-1" />
                <p class="value">
                  {{
                    Number(props.totals.total_calc_price_sum).toLocaleString()
                  }}円
                </p>
              </div>
              <div class="contactCard bg-blue-500">
                <p class="title">今月合計発注数</p>
                <hr class="my-1" />
                <p class="value">{{ props.totals.current_month_count }}件</p>
              </div>
              <div class="contactCard bg-blue-500">
                <p class="title">今月合計金額</p>
                <hr class="my-1" />
                <p class="value">
                  {{
                    Number(props.totals.current_month_sum).toLocaleString()
                  }}円
                </p>
              </div>
            </div>
          </section>

          <div class="mb-8 flex justify-end">
            <Pagination :links="initial_orders.links" />
          </div>

          <div
            v-if="purchase_list.length > 1"
            class="mt-12 mb-2 flex justify-end"
          >
            <button
              @click="openModal(null, null, 'multi')"
              class="bg-red-500 hover:bg-red-500 text-white font-bold py-2 px-4 rounded"
            >
              まとめて発注書作成
            </button>
          </div>

          <div class="w-full mx-auto overflow-x-scroll">
            <!-- <p class="mb-2">
              <span class="text-green-500 font-bold">緑色</span
              >の注文Noをクリックすると<span class="font-bold text-red-600"
                >納品書</span
              >を確認できます。
            </p> -->
            <table
              id="table_container"
              class="table-auto w-full text-left whitespace-no-wrap"
            >
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
                    発注済み登録
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
                    'transition duration-30 hover:bg-gray-300': true, 
                    'bg-green-50': order.order_complete_flg 
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
                      @click="openModal(order.img_path, null, 'img')"
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
                        updateNameOrSName(
                          order.id,
                          's_name',
                          $event.target.value
                        )
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
                        ? new Date(
                            order.desire_delivery_date
                          ).toLocaleDateString("ja-JP")
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
                    {{ order.quantity }}
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
                      @click="openModal(null, order, 'purchase')"
                      :class="{
                        ' hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-xs': true,
                        'bg-green-500': order.purchase_path,
                        'bg-gray-500': !order.purchase_path,
                      }"
                    >
                      {{ order.purchase_path ? "発行済" : "未発行" }}
                      <i
                        v-if="order.purchase_path"
                        class="ml-2 fas fa-check"
                      ></i>
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
                      v-if="order.delifile_path"
                      class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-xs"
                      @click="
                        openModal(order.delifile_path, order, 'deli_file')
                      "
                    >
                      納品書
                    </button>
                  </td>
                  <td
                    class="ml-2 px-4 py-3 text-lg text-gray-900 whitespace-nowrap"
                  >
                    <button
                      v-if="order.file_path || order.document_id"
                      class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-xs"
                      @click="openModal(null, order, 'approval')"
                    >
                      稟議書
                    </button>
                  </td>
                  <td
                    class="ml-2 px-4 py-3 text-lg text-gray-900 whitespace-nowrap"
                  >
                    <button
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
                    </button>
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
          <hr class="my-8" />
          <div class="mb-8 flex justify-end">
            <Pagination :links="initial_orders.links" />
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

        <div
          v-if="
            modal_status.type === 'img' || modal_status.type === 'deli_file'
          "
        >
          <div
            v-if="modal_status.type === 'deli_file'"
            class="w-1/2 mx-auto mb-8"
          >
            <div class="flex items-center justify-center w-full">
              <label
                for="dropzone-file"
                class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600"
              >
                <div
                  class="flex flex-col items-center justify-center pt-5 pb-6"
                >
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
                    <span class="font-semibold">Click to upload</span> or drag
                    and drop
                  </p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">
                    稟議書を変更する場合は、こちらから再設定してください。
                  </p>
                </div>
                <input
                  id="dropzone-file"
                  type="file"
                  accept="image/*"
                  class="hidden"
                  @change="fileUpload($event, modal_status.initial_order_id)"
                />
              </label>
            </div>
          </div>

          <div id="img_modal">
            <img :src="modal_status.img_path" alt="" />
          </div>
        </div>

        <Purchase
          v-else-if="modal_status.type === 'purchase'"
          :current_month_holidays="props.current_month_holidays"
          :next_month_holidays="props.next_month_holidays"
          :orders="print_order"
          @update-delivery-date="handleDeliveryDateUpdate"
        />

        <div id="pdfviewer" v-else-if="modal_status.type === 'approval'">
          <iframe
            v-if="modal_status.img_path"
            ref="pdfViewer"
            :src="modal_status.img_path"
          ></iframe>

          <!-- 画像ファイルがない場合稟議書要素を表示 -->
          <div v-else>
            <ApprovalDocument :approval_document="approval_document" />
          </div>
        </div>
      </div>
    </template>
  </MainLayout>
</template>
<style scoped lang="scss">
#table_container {
  width: 130vw;
}

#modal {
  position: fixed;
  bottom: 0;

  width: 90vw;

  padding: 1rem;

  background-color: white;
  box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
  border-radius: 10px 10px 0 0;
  height: 0;
  transform: translateY(100%);
  &.active {
    height: 99vh;
    transform: translateY(0);
    transition: all 0.5s;
  }

  & #close_container {
    width: 100%;
    display: flex;
    justify-content: end;
  }

  & #img_modal {
    height: 92%;
    display: flex;
    justify-content: center;

    & img {
      height: 100%;
      object-fit: contain;
      box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
    }
  }

  & #pdfviewer {
    height: 100%;

    & iframe {
      height: 96%;
      width: 100%;
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