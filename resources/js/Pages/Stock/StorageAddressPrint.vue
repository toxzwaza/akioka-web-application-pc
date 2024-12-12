<script setup>
import { onMounted, ref, reactive } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import Message from "@/Components/Message.vue";

import QRCode from "qrcode";

const props = defineProps({
  locations: Array,
  storage_addresses: Array,
});
const storage_addresses = ref([]);
const print_flg = ref(0);
const print_config = reactive({
  size: null,
  height: null,
  width: null,
});

// QRコードを生成する関数
const generateQRCode = async (val) => {
  try {
    // QRコードを生成
    return await QRCode.toDataURL(val);
  } catch (error) {
    console.error("QRコードの生成に失敗しました:", error);
    return "";
  }
};

const changeLocation = (locationId) => {
  storage_addresses.value = props.storage_addresses.filter(
    (address) => address.location_id == locationId
  );
  console.log(storage_addresses.value);
};

// 対象のアドレスを印刷リストから削除
const toggleStorageAddress = (storage_address_id) => {
  storage_addresses.value = storage_addresses.value.filter(
    (address) => address.id != storage_address_id
  );
};

// QRコードを生成してアドレスリストに追加
const createPrint = async () => {
  for (const storage_address of storage_addresses.value) {
    try {
      const data = await generateQRCode(storage_address.id.toString());
      storage_address.qr_code = data;
    } catch (error) {
      console.error("QRコードの生成に失敗しました:", error);
    }
  }
  print_flg.value = 1;
};

// 印刷処理
const doPrint = () => {
  console.log("実行");
  try {
    const print_hidden_list = document.querySelectorAll('.print-hidden');
    print_hidden_list.forEach((el) => {
        console.log(el);
        el.classList.add('hidden');
    });
    const print_config = document.querySelector('#print_config');
    print_config.classList.remove('py-20');
    print_config.classList.remove('mx-auto');

    window.print()
  } catch (e) {
    console.log(e);
  }
};

onMounted(() => {
  print_config.size = 1;
  print_config.height = 10;
  print_config.width = 80;
});
</script>
<template>
  <Head :title="'アドレス印刷'" />
  <header id="header" class="print-hidden text-gray-600 body-font">
    <div
      class="container mx-auto flex flex-wrap p-5 flex-col md:flex-row items-center"
    >
      <a
        :href="route('home')"
        class="flex title-font font-medium items-center text-gray-900 mb-4 md:mb-0"
      >
        <img class="w-16" src="/img/base/logo.jpg" alt="" />
        <span class="ml-3 text-xl">{{ props.title }}</span>
      </a>

      <nav
        class="md:mr-auto md:ml-4 md:py-1 md:pl-4 md:border-l md:border-gray-400 flex flex-wrap items-center text-base justify-center"
      ></nav>
    </div>
  </header>

  <main>
    <Message />
    <section id="print_config" class="text-gray-600 body-font max-w-4xl mx-auto py-20">
      <div class="container mx-auto">
        <div class="print-hidden flex flex-col text-center w-full mb-12">
          <h1
            class="sm:text-4xl text-3xl font-medium title-font mb-2 text-gray-900"
          >
            アドレス印刷
          </h1>
          <p class="lg:w-2/3 mx-auto leading-relaxed text-base">
            印刷したいアドレスを選択してください。
          </p>

          <form class="print_hidden">
            <div class="flex flex-wrap -mx-3 mb-6">
              <div class="w-full px-3">
                <label
                  class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2 text-left"
                  for="grid-password"
                >
                  格納先
                </label>
                <select
                  name=""
                  id=""
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  @change="changeLocation($event.target.value)"
                >
                  <option value="">選択してください。</option>
                  <option
                    v-for="location in locations"
                    :key="location.id"
                    :value="location.id"
                  >
                    {{ location.name }}
                  </option>
                </select>
              </div>
            </div>
          </form>
        </div>

        <div v-if="!print_flg" class="w-full mx-auto overflow-auto">
          <div v-if="storage_addresses.length > 0" class="text-center mb-8">
            <button
              class="text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded"
              @click="createPrint"
            >
              プレビュー
            </button>
          </div>
          <table class="table-auto w-full text-left whitespace-no-wrap">
            <thead>
              <tr>
                <th
                  class="w-10 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tr rounded-br"
                ></th>
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl"
                >
                  ID
                </th>
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                >
                  アドレス
                </th>
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                >
                  作成日
                </th>
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                >
                  更新日
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="storage_address in storage_addresses"
                :key="storage_address.id"
              >
                <td class="w-20 text-center">
                  <input
                    @change="toggleStorageAddress(storage_address.id)"
                    name="plan"
                    type="checkbox"
                    checked
                  />
                </td>
                <td class="px-4 py-3">{{ storage_address.id }}</td>
                <td class="px-4 py-3">{{ storage_address.address }}</td>
                <td class="px-4 py-3">
                  {{
                    new Date(storage_address.created_at).toLocaleDateString(
                      "ja-JP"
                    )
                  }}
                </td>
                <td class="px-4 py-3">
                  {{
                    new Date(storage_address.updated_at).toLocaleDateString(
                      "ja-JP"
                    )
                  }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="" v-else>
          <div v-if="storage_addresses.length > 0" class="print-hidden text-center mb-8">
            <div class="flex items-venter justify-between">
              <!-- 印刷用紙サイズ -->
              <div class="w-1/3 px-3">
                <label
                  class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2 text-left"
                  for="grid-password"
                >
                  用紙サイズ
                </label>
                <select
                  name=""
                  id=""
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  v-model="print_config.size"
                >
                  <option value="1">A4</option>
                  <option value="2">A3</option>
                </select>
              </div>
              <!-- アドレスカードのサイズ -->
              <div class="w-1/3 px-3">
                <label
                  class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2 text-left"
                  for="grid-password"
                >
                  縦(mm)
                </label>
                <input
                  v-model="print_config.height"
                  type="number"
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  name=""
                  id=""
                />
              </div>
              <div class="w-1/3 px-3">
                <label
                  class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2 text-left"
                  for="grid-password"
                >
                  横(mm)
                </label>
                <input
                  v-model="print_config.width"
                  type="number"
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  name=""
                  id=""
                />
              </div>
            </div>
            <div class="flex items-center justify-center my-12">
              <button
                class="text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded mr-2"
                @click="print_flg = 0"
              >
                選択画面
              </button>

              <button
                v-if="
                  print_config.size && print_config.height && print_config.width
                "
                class="text-white bg-red-500 border-0 py-2 px-6 focus:outline-none hover:bg-red-600 rounded ml-2"
                @click="doPrint"
              >
                印刷
              </button>
            </div>
        </div>

          <!-- プリント用紙 -->
          <div
            id="print_canvas"
            class="p-4 flex items-start justify-between flex-wrap"
            :style="
              print_config.size == 1
                ? { height: '297mm', width: '210mm' }
                : { height: '420mm', width: '297mm' }
            "
          >
            <div
              v-for="storage_address in storage_addresses"
              :key="storage_address.id"
              class="address_card flex justify-center items-center"
              :style="{
                height: print_config.height + 'mm',
                width: print_config.width + 'mm',
              }"
            >
              <span class="address">{{ storage_address.address }}</span>
              <img class="qr" :src="storage_address.qr_code" alt="" />
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
</template>


<style scoped>
#print_canvas {
  margin: 0 auto;
  border: 2px dashed rgb(48, 48, 48);
  box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px;
  overflow: hidden;

  /* height: 297mm;
  width: 210mm; */
  /* 
  height: 420mm;
  width: 297mm; */
}

.address_card {
  border: 1px solid black;
  position: relative;
}

.address {
  font-weight: bold;
  font-size: 24px;
  letter-spacing: 0.4rem;
}
.qr {
  height: 100%;
  position: absolute;
  top: 0;
  right: 5%;
}
</style>
