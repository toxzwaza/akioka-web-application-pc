<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { onMounted, reactive, ref } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";

const props = defineProps({
  initial_orders: Object,
});

const modal_status = reactive({
  status: false,
  img_path: "",
})

const openModal = img_path => {
  modal_status.img_path = ''
  
  if(!img_path){
    return
  }

  modal_status.img_path = img_path && img_path.includes('storage') ? `https://akioka.cloud/${img_path}` : img_path.includes('deli_file') ? `https://akioka.cloud/storage/${img_path}` : img_path

  modal_status.status = true

}

const base_initial_orders = ref([]);
const initial_orders = ref([]);
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
        getInitialOrders();
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

const updateExpectedDeliveryDate = (order_id, expected_delivery_date) => {
  console.log(order_id, expected_delivery_date);
  axios
    .post(route("stock.update_expected_delivery_date"), {
      order_id: order_id,
      expected_delivery_date: expected_delivery_date,
    })
    .then((res) => {
      console.log(res.data);
    })
    .catch((error) => {
      console.log(error);
    });
};
onMounted(() => {
  initial_orders.value = props.initial_orders.data;
  base_initial_orders.value = props.initial_orders.data;
  updateOrderUsers();
  updateComName();
});
</script>
<template>
  <MainLayout :title="'発注一覧'">
    <template #content>
      <h1 class="text-center text-xl font-bold text-gray-800">発注一覧</h1>

      <section class="text-gray-600 body-font">
        <div class="container px-5 py-12 mx-auto">
          <div class="flex flex-col text-center w-full mb-20">
            <p class="lg:w-2/3 mx-auto leading-relaxed text-base">
              発注確認と品名・品番の修正が可能です。<br />
              倉庫格納済みおよび引き渡しが完了しているリストは変更できませんのでご注意ください。
            </p>
          </div>
          <div id="sort_container" class="my-8 flex items-start justify-start">
            <div class="w-1/4">
              <p class="mb-2 font-bold">並び替え</p>
              <div class="button_container flex items-center justify-start">
                <button
                  :class="{
                    'mr-4 text-sm bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded': true,
                  }"
                  @click="updateFilter('reset')"
                >
                  リセット
                </button>

                <button
                  :class="{
                    'mr-2 text-sm bg-blue-500  text-white font-bold py-2 px-4 rounded': true,
                    'opacity-60': sort === 'new_order',
                  }"
                  @click="createSort('new_order')"
                >
                  新しい順
                </button>
                <button
                  :class="{
                    'mr-2 text-sm bg-blue-500  text-white font-bold py-2 px-4 rounded': true,
                    'opacity-60': sort === 'old_order',
                  }"
                  @click="createSort('old_order')"
                >
                  古い順
                </button>
              </div>
            </div>
            <div class="mr-8">
              <p class="mb-2 font-bold">検索</p>
              <div class="button_container flex items-center justify-start">
                <div class="w-62 mr-2">
                  <label
                    class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                    for="grid-last-name"
                  >
                    品名・品番から検索
                  </label>
                  <input
                    class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    type="text"
                    name=""
                    id=""
                    @change="updateFilter('nameOrSname', $event.target.value)"
                  />
                </div>
              </div>
            </div>
            <div>
              <p class="mb-2 font-bold">フィルタ</p>
              <div class="button_container flex items-center justify-start">
                <div class="w-32 mr-2">
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
                  >
                    <option value="0">未選択</option>
                    <option
                      v-for="com_name in com_names"
                      :key="com_name"
                      :value="com_name"
                    >
                      {{ com_name }}
                    </option>
                  </select>
                </div>
                <div class="w-32 mr-2">
                  <label
                    class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                    for="grid-last-name"
                  >
                    注文者
                  </label>
                  <select
                    @change="updateFilter('order_user', $event.target.value)"
                    class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    name="order_user"
                    id=""
                  >
                    <option value="0">未選択</option>
                    <option
                      v-for="user in order_users"
                      :key="user.id"
                      :value="user"
                    >
                      {{ user }}
                    </option>
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
                    @change="updateFilter('delifile_path', $event.target.value)"
                    class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    name="order_user"
                    id=""
                  >
                    <option value="0">未選択</option>
                    <option value="1">済</option>
                    <option value="2">未</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <hr class="my-8" />
          <div class="mb-8 flex justify-end">
            <Pagination :links="props.initial_orders.links" />
          </div>

          <div class="w-full mx-auto overflow-auto">
            <p class="mb-2">
              <span class="text-green-500 font-bold">緑色</span
              >の注文Noをクリックすると<span class="font-bold text-red-600"
                >納品書</span
              >を確認できます。
            </p>
            <table class="table-auto w-full text-left whitespace-no-wrap">
              <thead>
                <tr>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl"
                  >
                    注文No
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl"
                  >
                    画像
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    注文者
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    注文日
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
                    style="border-radius: 0 10px 10px 0"
                  >
                    納入予定日
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap"
                  >
                    数量
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="order in initial_orders"
                  :key="order.id"
                  :class="{ 'bg-indigo-50': !order.img_path }"
                >
                  <td
                    :class="{
                      'px-4 py-3': true,
                      'text-green-500 font-bold cursor-pointer':
                        order.receive_flg,
                    }"
                    @click="openModal(order.delifile_path)"
                  >
                    {{ order.order_no }}
                  </td>
                  <td class="w-24 px-4 py-6">
                    <img
                      :src="
                        order.img_path && order.img_path.includes('https://')
                          ? order.img_path
                          : 'https://akioka.cloud/' + order.img_path
                      "
                      @click="openModal(order.img_path)"
                      alt=""
                    />
                  </td>
                  <td class="px-4 py-3">{{ order.order_user }}</td>
                  <td class="px-4 py-3 text-lg text-gray-900">
                    {{ new Date(order.order_date).toLocaleDateString("ja-JP") }}
                  </td>
                  <td class="px-4 py-3 text-lg text-gray-900">
                    {{ order.com_name }}
                  </td>
                  <td class="px-4 py-3 text-lg text-gray-900">
                    <input
                      @change="
                        updateNameOrSName(order.id, 'name', $event.target.value)
                      "
                      type="text"
                      name="name"
                      v-model="order.name"
                      id=""
                      class="appearance-none block w-full bg-gray-100 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    />
                  </td>
                  <td class="px-4 py-3 text-lg text-gray-900">
                    <input
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
                      class="appearance-none block w-full bg-gray-100 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    />
                  </td>

                  <td class="px-4 py-3 text-lg text-gray-900">
                    <input
                      @change="
                        updateExpectedDeliveryDate(
                          order.id,
                          $event.target.value
                        )
                      "
                      type="date"
                      name=""
                      id=""
                      class="appearance-none block w-full bg-gray-100 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      :value="order.expected_delivery_date"
                    />
                  </td>
                  <td class="ml-2 px-4 py-3 text-lg text-gray-900">
                    {{ order.quantity }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>
      <div id="modal" :class="{'active' : modal_status.status}">
        <div id="close_container">
          <button
            @click="modal_status.status = !modal_status.status"
            class="modal__close bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm"
            aria-label="Close modal"
          >
            <i class="fa fa-times"></i> 
          </button>
        </div>

        <div id="img_modal">
              
          <img
            :src="modal_status.img_path"
            alt=""
          />
        </div>
      </div>
    </template>
  </MainLayout>
</template>
<style scoped lang="scss">
#modal {
  position: fixed;
  bottom: 0;


  width: 90vw;

  padding: 1rem;

  background-color: rgb(227 226 226 / 96%);
box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
  border-radius: 10px 10px 0 0 ;
  height: 0;
  transform: translateY(100%);
  &.active{
    height: 60vh;
    transform: translateY(0);
    transition: all 0.5s;
  }


  & #close_container{
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
}
</style>