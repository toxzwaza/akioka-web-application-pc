<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { onMounted, reactive, ref } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";
import MainTitle from "@/Components/Title/MainTitle.vue";

const props = defineProps({
  location: Object,
  processes: Array,
  location_processes: Array,
  storage_addresses: Array,
});
const form = reactive({
  location: {
    location_id: null,
    location_name: "",
    processes: [],
  },
  address: {
    id: null,
    shelf: '',
    row: '',
    col: '',
    sub_row: ''
  }
});

const checkProcess = () => {
  props.processes.forEach((process) => {
    process.select_flg = props.location_processes.includes(process.id) ? 1 : 0;
  });

  console.log(props.processes);
};
const resetAddressForm = () => {
  form.address.id = null
  form.address.shelf = ''
  form.address.row = ''
  form.address.col = ''
  form.address.sub_row = ''
}

const sendLocation = () => {
  form.location.processes = props.processes
    .filter((process) => process.select_flg)
    .map((process) => process.id);

  if (!form.location.location_name || !form.location.processes.length > 0) {
    alert(
      "格納先名が入力されていないか、管理部署が選択されていない可能性があります。"
    );
  } else {
    console.log(form);
    axios
      .post(route("stock.locations.store"), form.location)
      .then((res) => {
        console.log(res.data);
        alert("登録・編集が完了しました。");
        window.location.reload()
      })
      .catch((error) => {
        console.log(error);
      });
  }
};
const sendStorageAddress = () => {
  axios.post(route('stock.storage_addresses.store'), {
    location_id: form.location.location_id,
    storage_address_id : form.address.id,
    shelf: form.address.shelf,
    row: form.address.row,
    col: form.address.col,
    sub_row: form.address.sub_row
  })
  .then(res => {
    console.log(res.data)
    alert("登録・編集が完了しました。");
    window.location.reload()
  })
  .catch(error => {
    console.log(error)
  })
}

const editStorageAddress = (storage_address) => {
  console.log(storage_address)
  form.address.id = storage_address.id
  form.address.shelf = storage_address.shelf
  form.address.row = storage_address.row
  form.address.col = storage_address.col
  form.address.sub_row = storage_address.sub_row
}

onMounted(() => {
  form.location.location_id = props.location.id;
  form.location.location_name = props.location.name;
  checkProcess();
});
</script>
<template>
  <MainLayout :title="'格納先詳細'">
    <template #content>
      <MainTitle
        :top="'格納先詳細'"
        :sub="'格納先・格納先アドレスの詳細確認と編集を行います。'"
      />

      <section class="text-gray-600 body-font">
        <div class="flex justify-between">
          <form class="w-1/2 px-4">
            <h2 class="font-bold mb-4 text-lg">格納先編集</h2>
            <div class="flex flex-wrap -mx-3 mb-6">
              <div class="w-full px-3">
                <label
                  class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                  for="grid-password"
                >
                  格納先名
                </label>
                <input
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  id="grid-password"
                  type="text"
                  placeholder="格納先名を入力してください"
                  v-model="form.location.location_name"
                />
              </div>
            </div>
            <div class="flex flex-wrap -mx-3 mb-6">
              <div class="w-full px-3">
                <label
                  class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                  for="grid-password"
                >
                  管理部署選択
                </label>
                <span
                  v-for="process in props.processes"
                  :key="process.id"
                  @click="process.select_flg = !process.select_flg"
                  :class="{
                    'inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1 mb-1': true,
                    'opacity-100': process.select_flg,
                    'opacity-60': !process.select_flg,
                  }"
                >
                  {{ process.name }}
                </span>
              </div>
            </div>

            <button
              class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
              @click.prevent="sendLocation"
            >
              編集
            </button>
          </form>

          <form class="w-1/2 px-4">
            <h2 :class="{'font-bold mb-4 text-lg' : true, 'text-green-500': form.address.id }">{{ form.address.id ? 'アドレス編集' : 'アドレス登録' }}</h2>
            <div class="flex flex-wrap -mx-3 mb-6">
              <div class="w-full md:w-1/4 px-3 mb-6 md:mb-0">
                <label
                  class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                  for="grid-city"
                >
                  <span class="text-red-500">*</span>棚
                </label>
                <input
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  type="text"
                  placeholder="A-Z"
                  v-model="form.address.shelf"
                />
              </div>
              <div class="w-full md:w-1/4 px-3 mb-6 md:mb-0">
                <label
                  class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                  for="grid-state"
                >
                  <span class="text-red-500">*</span>段
                </label>
                <div class="relative">
                  <input
                    class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    type="number"
                    placeholder="0~9"
                    v-model="form.address.row"
                  />
                </div>
              </div>
              <div class="w-full md:w-1/4 px-3 mb-6 md:mb-0">
                <label
                  class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                  for="grid-zip"
                >
                  列
                </label>
                <input
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  type="number"
                  placeholder="0~9"
                  v-model="form.address.col"
                />
              </div>
              <div class="w-full md:w-1/4 px-3 mb-6 md:mb-0">
                <label
                  class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                  for="grid-zip"
                >
                  列の列
                </label>
                <input
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  type="number"
                  placeholder="0~9"
                  v-model="form.address.sub_row"
                />
              </div>
            </div>

            <button
              :class="{ 'border bg-white hover:bg-blue-500 hover:text-white text-blue-500 font-bold py-2 px-4 rounded' : true , 'text-green-500': form.address.id, 'bg-green-500': form.address.id }"
              @click.prevent="sendStorageAddress"
            >
              {{ form.address.id ? '編集' : '登録' }}
            </button>
            <button
              :class="{ 'border bg-white hover:bg-blue-500 hover:text-white text-blue-500 font-bold py-2 px-4 rounded  ml-4' : true ,  }"
              v-if="form.address.id"
              @click.prevent="resetAddressForm"
            >
              新規登録へ戻る
            </button>
          </form>
        </div>

        <div class="container px-5 py-24 mx-auto">
          <div class="lg:w-2/3 w-full mx-auto overflow-auto">
            <table class="table-auto w-full text-left whitespace-no-wrap">
              <thead>
                <tr>
                  <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap"
                  >
                    アドレスID
                  </th>
                  <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap"
                  >
                    アドレス
                  </th>
                  <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap"
                  >
                    最終更新日
                  </th>
                  <th
                    class="px-4 py-4 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 whitespace-nowrap"
                  >
                    
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="storage_address in storage_addresses"
                  :key="storage_address.id"
                >
                  <td class="px-4 py-3">{{ storage_address.id }}</td>
                  <td class="px-4 py-3">{{ storage_address.address }}</td>

                  <td class="px-4 py-3 text-lg text-gray-900">
                    {{
                      new Date(storage_address.updated_at).toLocaleDateString(
                        "ja-JP",
                        { year: "numeric", month: "2-digit", day: "2-digit" }
                      )
                    }}
                  </td>
                  <td class="px-4 py-3 text-lg text-gray-900">
                    <button
                      class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm"
                      @click.prevent="editStorageAddress(storage_address)"
                    >
                      編集
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