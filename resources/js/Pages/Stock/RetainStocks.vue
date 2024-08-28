<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { ref, onMounted, reactive } from "vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
  user_id: Number,
  user_name: String,
  stocks: Array,
  retained_stocks: Array,
});
// const stocks = reactive({});
const checkImg = (imgPath) => {
  console.log(imgPath);
};

const changeSelect = (stock_id, value) => {
  router.post(route("stock.store.retained.stocks"), {
    stock_id: stock_id,
    treat_id: value,
    user_id: props.user_id,
  });
};
onMounted(() => {});
</script>

<template>
  <MainLayout>
    <template #content>
      <section id="modal" class="text-gray-600 body-font">
        <div class="container px-5 py-24 mx-auto">
          <div class="flex flex-col text-center w-full mb-20">
            <h1
              class="sm:text-4xl text-3xl font-medium title-font mb-2 text-gray-900"
            >
              滞留品処遇決定
            </h1>
            <p
              class="lg:w-2/3 mx-auto leading-relaxed text-base text-center mt-4"
            >
              滞留品の処遇を決定してください。<br />
              以下の表のセレクトボックスより、<span
                class="text-ls font-bold text-red-600"
                >「廃棄」</span
              >又は<span class="text-ls font-bold text-green-600"
                >「現場引き取り」</span
              >を選択してください。<br />

              滞留品の処遇決定は、今後継続的(毎月)に行われますが、<br />本件以降は発注時に登録した管理部署の課長にのみ表示されることとなります。<br />

              <span class="block mt-4 text-sm text-red-400"
                >*今回全課長に送信しているのは、品証二階へ移動させた物品の管理部署が不明な為です。<br />
                備品倉庫に継続的に置くことはできません。
              </span>
            </p>
          </div>
          <div class="w-full mx-auto overflow-auto">
            <h2 class="mb-4 font-bold text-gray-400 font-serif">
              <span class="text-xl text-red-500 pr-2">{{ props.user_name }} </span> さんがログイン中。
            </h2>
            <table class="table-auto w-full text-left whitespace-no-wrap">
              <thead>
                <tr>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl"
                  >
                    id
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl"
                  >
                    画像
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    品名
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    価格
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    個数
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    金額
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    処遇
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="stock in props.stocks" :key="stock.id">
                  <td class="border-t-2 border-gray-200 px-4 py-8">
                    {{ stock.id }}
                  </td>

                  <td class="border-t-2 border-gray-200 px-4 py-8">
                    <img
                      @click="checkImg(stock.img_path)"
                      class="w-16"
                      v-if="stock.img_path.includes('https')"
                      :src="stock.img_path"
                      alt=""
                    />
                  </td>
                  <td class="border-t-2 border-gray-200 px-4 py-8">
                    {{ stock.name }}
                  </td>
                  <td class="border-t-2 border-gray-200 px-4 py-8">
                    @ {{ stock.price }}
                  </td>
                  <td class="border-t-2 border-gray-200 px-4 py-8">
                    {{ stock.quantity }}
                  </td>
                  <td class="border-t-2 border-gray-200 px-4 py-8">
                    = {{ (stock.price * stock.quantity).toLocaleString() }}円
                  </td>
                  <td class="border-t-2 border-gray-200 px-4 py-8">
                    <select
                      name=""
                      id="treatSelect"
                      @change="changeSelect(stock.id, $event.target.value)"
                    >
                      <option value="0" class="text-gray-600">
                        選択してください。
                      </option>
                      <option value="1" class="font-bold text-red-400">
                        廃棄
                      </option>
                      <option value="2" class="font-bold text-green-400">
                        現場引き取り
                      </option>
                    </select>
                  </td>
                </tr>

                

              </tbody>
            </table>

            <h2 class="mt-16 mb-4 font-semibold text-xl">滞留品 所在決定済み</h2>
            <table class="table-auto w-full text-left whitespace-no-wrap">
              <thead>
                <tr>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl"
                  >
                    id
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl"
                  >
                    画像
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    品名
                  </th>
                 
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    処遇
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    決定者
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="retained_stock in props.retained_stocks" :key="retained_stock.id">
                  <td class="border-t-2 border-gray-200 px-4 py-8">
                    {{ retained_stock.id }}
                  </td>

                  <td class="border-t-2 border-gray-200 px-4 py-8">
                    <img
                      @click="checkImg(retained_stock.img_path)"
                      class="w-16"
                      :src="retained_stock.img_path"
                      alt=""
                    />
                  </td>
                  <td class="border-t-2 border-gray-200 px-4 py-8">
                    {{ retained_stock.name }}
                  </td>
                 
                  <td class="border-t-2 border-gray-200 px-4 py-8">
                    {{ retained_stock.treat_id == 1 ? '廃棄' : retained_stock == 2 ? '現場引き取り' : ''}}
                  </td>
                  <td class="border-t-2 border-gray-200 px-4 py-8">
                    {{ retained_stock.user_name }}
                  </td>
                </tr>

                

              </tbody>
            </table>
          </div>
        </div>
      </section>
    </template>
  </MainLayout>

  <!-- <div id="img_modal">
    <div id="img_container" class="w-4/5 bg-white">
        <img src="" alt="">
    </div>
  </div> -->
</template>



<style>
/* #img_modal {
  position: fixed;
  top:0;
  height: 100vh;
  width: 100vw;
  background-color: rgba(0, 0, 0, 0.527);
} */
</style>