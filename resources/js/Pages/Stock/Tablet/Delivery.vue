<script setup>
import Tablet from "@/Layouts/Tablet.vue";
import { onMounted, ref, reactive } from "vue";
import axios from "axios";

const props = defineProps({
  order: Object,
});

// 対象格納先在庫数
const storage_quantity = ref(0);

const form = reactive({
  id: props.order.id,
  storage_address_id: null,
  stock_storage_id: null,
  quantity: props.order.quantity - props.order.split_quantity_sum,
});

const updateDelivery = () => {
  if (confirm("納品登録をおこないますか？")) {
    axios
      .get(route("stock.tablet.updateDelivery"), {
        params: form,
      })
      .then((res) => {
        console.log(res.data);
        if (res.data.status == "ok") {
          window.location.href = route("stock.tablet.archive");
        }
      })
      .catch((error) => {});
  }
};

onMounted(() => {
  console.log(props.order);
  if (props.order.stock_storages && props.order.stock_storages.length > 0) {
    form.storage_address_id = props.order.stock_storages[0].address_id;
    form.stock_storage_id = props.order.stock_storages[0].stock_storage_id;
    storage_quantity.value = props.order.stock_storages[0].storage_quantity;
  }
});
</script>
<template>
  <Tablet :title="'納品登録'">
    <template #content>
      <section class="text-gray-600 body-font">
        <div class="container px-5 py-24 mx-auto">
          <div class="flex flex-col text-center w-full mb-8">
            <h1
              class="sm:text-4xl text-3xl font-medium title-font mb-2 text-blue-600"
            >
              納品数量登録
            </h1>
            <p class="lg:w-2/3 mx-auto leading-relaxed text-base">
              以下の画面より納品数量登録を行います。
            </p>
          </div>

          <div class="text-gray-600 body-font relative">
            <div class="container px-5 mx-auto">
              <div class="flex flex-col text-center w-full mb-12">
                <p class="w-full mx-auto leading-relaxed text-base">
                  分納の場合は、以下のテキストボックスに数量を記入して、納品ボタンを押下してください。<br />
                  なお、分納の場合は<span class="font-bold text-red-600 mx-2"
                    >納品登録リスト</span
                  >から削除されません。
                </p>
              </div>
              <div
                class="bg-gray-50 py-8 w-full mx-auto flex justify-center items-start"
              >
                <!-- 物品確認用コンテンツ -->
                <div class="w-1/3">
                  <section class="text-gray-600 body-font">
                    <div
                      class="container mx-auto flex items-center justify-center flex-col"
                    >
                      <div class="w-full">
                        <h1
                          class="title-font text-2xl mb-2 font-medium text-gray-600"
                        >
                          {{ props.order.name }}
                        </h1>
                        <span class="block mb-4 text-xl">{{
                          props.order.s_name
                        }}</span>
                      </div>

                      <img
                        class="w-5/6 object-cover object-center rounded"
                        alt="hero"
                        :src="
                          props.order.img_path &&
                          props.order.img_path.includes('https://')
                            ? props.order.img_path
                            : 'http://monokanri-app.local/' +
                              props.order.img_path
                        "
                      />
                    </div>
                  </section>
                </div>

                <!-- 納入用フォーム -->
                <!-- 格納先アドレスが一つ以上の場合表示 -->
                <div
                  v-if="form.stock_storage_id"
                  class="flex flex-wrap -m-2 justify-center mb-4"
                >
                  <div class="p-2">
                    <div class="relative mb-2">
                      <label
                        for="name"
                        class="font-bold mb-1leading-7 text-xl text-gray-600"
                        >格納先アドレス</label
                      >
                      <select
                        v-model="form.stock_storage_id"
                        id="storage_address_id"
                        name="storage_address_id"
                        class="text-center w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
                      >
                        <option
                          v-for="storage in props.order.stock_storages"
                          :key="storage.address_id"
                          :value="storage.stock_storage_id"
                        >
                          {{ storage.address }}
                        </option>
                      </select>
                    </div>

                    <div class="relative mt-8">
                      <label
                        for="name"
                        class="font-bold mb-1leading-7 text-xl text-gray-600"
                        >納入数</label
                      >
                      <input
                        v-model="form.quantity"
                        type="number"
                        id="quantity"
                        name="quantity"
                        class="text-center w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
                      />
                    </div>

                    <p class="mt-4 w-full flex justify-between">
                      <span>現在個数: {{ storage_quantity }}</span>
                      <span>
                        <i class="fas fa-arrow-right w-6 h-6 inline-block"></i>
                      </span>
                      <span
                      class="font-bold"
                        >納入後個数:
                        {{ form.quantity + storage_quantity }}</span
                      >
                    </p>
                  </div>
                  <div class="p-2 w-full mt-4">
                    <button
                      @click="updateDelivery"
                      class="flex mx-auto text-white bg-red-500 border-0 py-2 px-8 focus:outline-none hover:bg-red-600 rounded text-lg"
                    >
                      納入
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </template>
  </Tablet>
</template>
<style scoped>
</style>