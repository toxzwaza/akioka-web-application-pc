<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { ref, onMounted } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";
const props = defineProps({
  display : Number,
  signages : Array,
});

// サイネージ番号
const signage_data = ref([]);
const display = ref(null);

const file = ref(null);
const name = ref("");

const changeFile = (e) => {
  file.value = e.target.files[0];
};

// ファイルを送信する関数を追加
const uploadFile = () => {
  if (file.value) {
    const formData = new FormData();
    formData.append("file", file.value);
    formData.append("name", name.value);

    router.post("/signage/store", formData, {
      onSuccess: () => {
        if (confirm("ファイルが正常にアップロードされました。更新しますか？")) {
          getSignageData();
        }
      },
      onError: () => {
        alert("ファイルのアップロードに失敗しました");
      },
    });
  } else {
    alert("ファイルを選択してください");
  }
};
const deleteAsset = (asset_id) => {
  axios
    .get(route("signage.deleteData", { asset_id: asset_id }))
    .then((res) => {
      console.log(res.data);
      getSignageData();
    })
    .catch((error) => {});
};
const changeActive = (active_flg, asset_id) => {
  console.log(active_flg, asset_id);

  axios
    .get(route("signage.updateData"), {
      params: {
        asset_id: asset_id,
        setData: {
          is_active: active_flg,
          is_enabled: active_flg,
        },
      },
    })
    .then((res) => {
      console.log(res.data);
      getSignageData(display.value);
    })
    .catch((error) => {
      console.error(error);
    });
};

const updateAsset = (asset_id) => {};

const getSignageData = (address) => {
  axios
    .get(route("signage.getData"),{
      params: {
        address: address
      }
    })
    .then((res) => {
      console.log(res.data);
      signage_data.value = res.data;
    })
    .catch((error) => {
      console.log(error);
    });
};

onMounted(() => {
  display.value = props.display.address;
  getSignageData(display.value);
});
</script>
<template>
  <MainLayout>
    <template #content>
      <div class="py-20">
        <section class="text-gray-600 body-font relative">
          <div class="container px-5 mx-auto">
            <div class="flex flex-col text-center w-full mb-12">
              <h1
                class="sm:text-3xl text-2xl font-medium title-font mb-4 text-gray-900"
              >
                サイネージファイルアップロード
              </h1>
              <p class="lg:w-2/3 mx-auto leading-relaxed text-base">
                サイネージ用のファイルをアップロードできます。
                PDFファイルのみ対応しています。
              </p>
            </div>
            <div class="lg:w-1/2 md:w-2/3 mx-auto">
              <div class="p-2 w-full">
                <div class="relative">
                  <label for="file" class="leading-7 text-sm text-gray-600"
                    >サイネージ名</label
                  >
                  <input
                    type="name"
                    id="name"
                    name="name"
                    accept=".pdf"
                    class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
                    v-model="name"
                  />
                </div>
              </div>
              <div class="p-2 w-full">
                <div class="relative">
                  <label for="file" class="leading-7 text-sm text-gray-600"
                    >ファイルを選択</label
                  >
                  <input
                    type="file"
                    id="file"
                    name="file"
                    accept=".pdf"
                    class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
                    @change="changeFile"
                  />
                </div>
              </div>
              <div class="p-2 w-full mt-4">
                <button
                  type="button"
                  class="flex mx-auto text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg"
                  @click="uploadFile"
                >
                  アップロード
                </button>
              </div>
            </div>
          </div>
        </section>

        <hr class="my-12" />

        <!-- サイネージディスプレイ -->
        <section class="text-gray-600 body-font">
          <div class="container px-5 p mx-auto">
            <div class="flex flex-col text-center w-full mb-20">
              <h1
                class="sm:text-4xl text-3xl font-medium title-font mb-2 text-gray-900"
              >
                サイネージデータ
              </h1>
            </div>
            <a
              :href="'http://' + props.display.address"
              class="w-40 text-center block bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mb-4"
            >
              詳細な編集はこちら
            </a>
            <div class="w-full mx-auto overflow-auto">
              <table class="table-auto w-full text-left whitespace-no-wrap">
                <thead>
                  <tr>
                    <th
                      class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl"
                    >
                      名前
                    </th>
                    <th
                      class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                    >
                      開始日
                    </th>
                    <th
                      class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                    >
                      終了日
                    </th>
                    <th
                      class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                    >
                      表示時間
                    </th>
                    <th
                      class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                    >
                      公開状況
                    </th>
                    <th
                      class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                    ></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="signage in signage_data" :key="signage.asset_id">
                    <td class="px-4 py-3">
                      <Link
                        v-if="signage.id"
                        :class="{ 'text-indigo-500 underline': signage.id }"
                        :href="route('signage.show', { id: signage.id })"
                        >{{ signage.name }}</Link
                      >

                      <span v-else>{{ signage.name }}</span>
                    </td>

                    <td class="px-4 py-3">
                      {{
                        new Date(signage.start_date).toLocaleDateString("ja-JP")
                      }}
                    </td>
                    <td class="px-4 py-3">
                      {{
                        new Date(signage.end_date).toLocaleDateString("ja-JP")
                      }}
                    </td>
                    <td class="px-4 py-3">{{ signage.duration }}秒</td>
                    <td class="px-4 py-3">
                      <button
                        @click="changeActive(0, signage.asset_id)"
                        v-if="signage.is_active"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm whitespace-nowrap"
                      >
                        公開中
                      </button>
                      <button
                        @click="changeActive(1, signage.asset_id)"
                        v-else
                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm whitespace-nowrap"
                      >
                        非公開
                      </button>
                    </td>
                    <td class="px-4 py-3">
                      <button
                        @click="deleteAsset(signage.asset_id)"
                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm whitespace-nowrap"
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
      </div>
    </template>
  </MainLayout>
</template>