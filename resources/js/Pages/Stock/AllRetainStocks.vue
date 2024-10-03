<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { ref, onMounted, reactive } from "vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
  retained_stocks: Array,
});
const param = ref({});
props.retained_stocks.forEach((stock) => {
  param.value[stock.id] = null;
});

const decisionTreat = (stock_id, retain_lists) => {
  for (let list of retain_lists) {
    if (list.treat_name != "廃棄") {
      return "";
    }
  }
  addParam(stock_id, "1");
  return "廃棄";
};

onMounted(() => {});
const checkImg = (imgPath) => {
  modalImg.imgPath = imgPath.includes("https") ? imgPath : "/" + imgPath;
  modalImg.status = true;
};
const changeModal = () => {
  (modalImg.status = null), (modalImg.imgPath = null);
};
const modalImg = reactive({
  status: null,
  imgPath: null,
});

const sendTreat = () => {
  if (checkSelect()) {
    router.post(route("stock.store.last_retained.stocks"), {
      treat_lists: param.value,
    });
  } else {
    alert("未選択項目があります。");
  }
};
const checkSelect = () => {
  let isSuccess = true;
  const selectEls = document.querySelectorAll(".treat_select");
  selectEls.forEach((el) => {
    if (el.value == 0) {
      console.log("未選択項目あり。");

      isSuccess = false;
      return isSuccess;
    }
  });
  return isSuccess;
};
const addParam = (stock_id, el) => {
  if (!el || !el.classList) {
    console.log("Invalid element:", el);
    return;
  }
  param.value[stock_id] = el.value;
  switch (el.value) {
    case "1":
      el.classList.add("border-2", "border-red-500");
      break;
    case "2":
      el.classList.add("border-2", "border-orange-500");
      break;
    case "3":
      el.classList.add("border-2", "border-blue-500");
      break;
    case "4":
      el.classList.add("border-2", "border-purple-500");
      break;
    default:
      el.classList.add("border-2", "border-gray-500");
      break;
  }

  console.log(param.value);
};
</script>

<template>
  <MainLayout :title="'滞留品通達'">
    <template #content>
      <section id="modal" class="text-gray-600 body-font">
        <div class="container px-5 py-24 mx-auto">
          <div class="flex flex-col text-center w-full mb-20">
            <h1
              class="sm:text-4xl text-3xl font-medium title-font mb-2 text-gray-900"
            >
              最終滞留品処遇決定
            </h1>
            <p
              class="lg:w-2/3 mx-auto leading-relaxed text-base text-center mt-4"
            >
              滞留品の処遇を決定してください。<br />

              滞留品の処遇決定は、今後継続的(毎月)に行われますが、<br />本件以降は発注時に登録した管理部署の課長にのみ表示されることとなります。<br />

              <span class="block mt-4 text-sm text-red-400"
                >*今回全課長に送信しているのは、品証二階へ移動させた物品の管理部署が不明な為です。<br />
                備品倉庫に継続的に置くことはできません。
              </span>
            </p>
          </div>

          <div id="button_container" class="text-right mb-4">
            <button
              @click="sendTreat"
              class="bg-transparent hover:bg-green-500 text-green-700 font-semibold hover:text-white py-2 px-4 border border-green-500 hover:border-transparent rounded"
            >
              確定する
            </button>
          </div>

          <div class="w-full mx-auto overflow-auto">
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
                    一課
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    二課
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    品証
                  </th>

                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-red-900 text-sm bg-gray-100"
                  >
                    最終処遇
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="stock in props.retained_stocks" :key="stock.id">
                  <td class="border-t-2 border-gray-200 px-4 py-4">
                    {{ stock.id }}
                  </td>

                  <td class="border-t-2 border-gray-200 py-2">
                    <img
                      @click="checkImg(stock.img_path)"
                      class="w-16"
                      :src="
                        stock.img_path.includes('https')
                          ? stock.img_path
                          : '/' + stock.img_path
                      "
                      alt=""
                    />
                    <!-- 開発用 -->
                    <!-- <img
                      @click="checkImg(stock.img_path)"
                      class="w-16"
                      :src="
                        stock.img_path.includes('https') ? stock.img_path : '/'
                      "
                      alt=""
                    /> -->
                  </td>
                  <td class="border-t-2 border-gray-200 px-4 py-4">
                    {{ stock.name }}
                  </td>

                  <td class="border-t-2 border-gray-200 px-4 py-4">
                    {{
                      stock.retain_lists.find((list) => list.user_id === 37)
                        ?.treat_name
                    }}
                  </td>
                  <td class="border-t-2 border-gray-200 px-4 py-4">
                    {{
                      stock.retain_lists.find((list) => list.user_id === 84)
                        ?.treat_name
                    }}
                  </td>
                  <td class="border-t-2 border-gray-200 px-4 py-4">
                    {{
                      stock.retain_lists.find((list) => list.user_id === 16)
                        ?.treat_name
                    }}
                  </td>
                  <td class="border-t-2 border-gray-200 px-4 py-4">
                    <select
                      @change="addParam(stock.id, $event.target)"
                      v-if="decisionTreat(stock.id, stock.retain_lists)"
                      :class="{ 'treat_select border-2 border-red-500': true }"
                    >
                      <option selected value="1">廃棄</option>
                      <option value="2">一課引き取り</option>
                      <option value="3">二課引き取り</option>
                      <option value="4">品証引き取り</option>
                    </select>

                    <select
                      @change="addParam(stock.id, $event.target)"
                      :class="{ treat_select: true }"
                      v-else
                    >
                      <option value="0">未選択</option>
                      <option value="1">廃棄</option>
                      <option value="2">一課引き取り</option>
                      <option value="3">二課引き取り</option>
                      <option value="4">品証引き取り</option>
                    </select>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </template>
  </MainLayout>

  <div v-if="modalImg.status" id="img_modal">
    <div @click="changeModal" id="img_container" class="">
      <button
        @click="changeModal"
        class="bg-white hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded"
      >
        閉じる
      </button>
      <img :src="modalImg.imgPath" alt="" />
    </div>
  </div>
</template>



<style>
#img_modal {
  position: fixed;
  top: 0;
  height: 100vh;
  width: 100vw;
}
#img_modal #img_container {
  position: relative;
  height: 100%;
  width: 100%;
  background-color: rgba(0, 0, 0, 0.734);
}
#img_modal #img_container button {
  position: absolute;
  top: 4%;
  left: 84%;
}

#img_modal #img_container img {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  height: 80%;
  width: 80%;
  object-fit: contain;
}
</style>