<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { onMounted, reactive, ref } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";

const props = defineProps({
  users: Array,
  groups: Array,
  lunch_orders: Array,
});

const base_users = ref([]);
const filter_users = ref([]);

const form = reactive({
  user_id: 0,
  date: new Date().toISOString().split("T")[0],
});

const createLunchOrder = () => {
  if (!form.user_id || !form.date) {
    alert("必須項目が入力されていません。");
  }

  // 注文処理
  axios
    .post(route("lunch.reserve.store"), form)
    .then((res) => {
      console.log(res.data);
      if (res.data.status && confirm("注文登録が完了しました。")) {
        window.location.reload();
      }
    })
    .catch((error) => {
      console.log(error);
    });
};

const deleteLunchOrder = (lunch_order_id) => {
  axios
    .delete(route("lunch.reserve.delete"), {
      params: {
        lunch_order_id: lunch_order_id,
      },
    })
    .then((res) => {
      console.log(res.data);
      if (res.data.status && confirm("注文削除が完了しました。")) {
        window.location.reload();
      }
    })
    .catch((error) => {
      console.log(error);
    });
};

const handleGroups = (group_id) => {
  console.log(group_id);
  if (group_id) {
      filter_users.value = base_users.value.filter(
        (user) => user.group_id == group_id
      );
  }else{
    filter_users.value = base_users.value
  }
};

onMounted(() => {
  base_users.value = props.users;
  filter_users.value = props.users;

  console.log(base_users.value);
});
</script>
<template>
  <MainLayout :title="'弁当予約'">
    <template #content>
      <h1 class="text-center text-xl font-bold text-gray-800 mb-12">
        弁当予約
      </h1>

      <section class="text-gray-600 body-font">
        <div class="px-5 mx-auto">
          <form class="w-1/2 mb-20 mx-auto bg-gray-50 p-4">
            <div class="flex justify-center flex-wrap -mx-3 mb-6">
              <div class="w-1/3 px-3 mb-6 md:mb-0">
                <label
                  :class="{
                    'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2': true,
                  }"
                  for="grid-first-name"
                >
                  部署
                </label>
                <select
                  name=""
                  id=""
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-transparent rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                  @change="handleGroups($event.target.value)"
                >
                  <option value="0">未選択</option>
                  <option
                    v-for="group in groups"
                    :key="group.id"
                    :value="group.id"
                  >
                    {{ group.name }}
                  </option>
                </select>
              </div>

              <div class="w-2/3 px-3 mb-6 md:mb-0">
                <label
                  :class="{
                    'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2': true,
                    'text-red-500': !form.user_id,
                  }"
                  for="grid-first-name"
                >
                  注文者
                </label>
                <select
                  name=""
                  id=""
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-transparent rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                  v-model="form.user_id"
                >
                  <option value="0">未選択</option>
                  <option
                    v-for="user in filter_users"
                    :key="user.id"
                    :value="user.id"
                  >
                    {{ user.name }}
                  </option>
                </select>
              </div>
            </div>
            <div class="flex justify-center flex-wrap -mx-3 mb-6">
              <div class="w-full px-3 mb-6 md:mb-0">
                <label
                  :class="{
                    'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2': true,
                    'text-red-500': !form.date,
                  }"
                  for="grid-first-name"
                >
                  日時
                </label>
                <input
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-transparent rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                  id="grid-first-name"
                  type="date"
                  v-model="form.date"
                />
              </div>
            </div>

            <div class="w-full flex justify-center">
              <button
                @click.prevent="createLunchOrder"
                class="flex text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded"
              >
                注文登録
              </button>
            </div>
          </form>

          <div class="w-1/2 mx-auto overflow-auto">
            <h2 class="my-4 font-bold text-xl">
              {{
                new Date(Date.now() + 86400000).toLocaleDateString("ja-JP", {
                  year: "numeric",
                  month: "2-digit",
                  day: "2-digit",
                })
              }}
              以降の弁当予約注文状況
            </h2>
            <table class="table-auto w-full text-left whitespace-no-wrap">
              <thead>
                <tr>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl"
                  >
                    注文者
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    日時
                  </th>

                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    作成日
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  ></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="lunch_order in lunch_orders" :key="lunch_order.id">
                  <td class="px-4 py-3">{{ lunch_order.user_name }}</td>
                  <td class="px-4 py-3">
                    {{
                      new Date(lunch_order.date)
                        .toLocaleDateString("ja-JP", {
                          year: "numeric",
                          month: "2-digit",
                          day: "2-digit",
                        })
                        .replace(/\//g, "/")
                    }}
                  </td>
                  <td class="px-4 py-3">
                    {{
                      new Date(lunch_order.created_at)
                        .toLocaleString("ja-JP", {
                          year: "numeric",
                          month: "2-digit",
                          day: "2-digit",
                          hour: "2-digit",
                          minute: "2-digit",
                        })
                        .replace(/\//g, "/")
                    }}
                  </td>
                  <td class="px-4 py-3 flex justify-end">
                    <button
                      @click.prevent="deleteLunchOrder(lunch_order.id)"
                      class="text-white bg-red-500 border-0 py-2 px-6 focus:outline-none hover:bg-red-600 rounded"
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
<style scoped lang="scss">
</style>