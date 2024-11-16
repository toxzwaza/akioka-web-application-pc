<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { ref, onMounted } from "vue";
import { router, Link } from "@inertiajs/vue3";
const props = defineProps({
    signages: Array
});

const file = ref(null);

const changeFile = (e) => {
  file.value = e.target.files[0];
};

// ファイルを送信する関数を追加
const uploadFile = () => {
  if (file.value) {
    const formData = new FormData();
    formData.append("file", file.value);

    router.post("/signage/store", formData, {
      onSuccess: () => {
        alert("ファイルが正常にアップロードされました");
      },
      onError: () => {
        alert("ファイルのアップロードに失敗しました");
      },
    });
  } else {
    alert("ファイルを選択してください");
  }
};

onMounted(() => {
    
})
</script>
<template>
  <MainLayout>
    <template #content>
      <section class="text-gray-600 body-font relative">
        <div class="container px-5 py-24 mx-auto">
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
            <div class="p-2 w-full">
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

      <!-- 登録済みのPDF -->
      <section class="text-gray-600 body-font">
        <div class="container px-5 py-24 mx-auto">
          <div class="flex flex-col text-center w-full mb-20">
            <h1
              class="sm:text-4xl text-3xl font-medium title-font mb-2 text-gray-900"
            >
              アップロード済
            </h1>

          </div>
          <div class="lg:w-2/3 w-full mx-auto overflow-auto">
            <table class="table-auto w-full text-left whitespace-no-wrap">
              <thead>
                <tr>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl"
                  >
                    ID
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    ファイル名
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    作成日
                  </th>

                </tr>
              </thead>
              <tbody>
                <tr v-for="signage in props.signages" :key="signage.id">
                  <td class="px-4 py-3">{{ signage.id }}</td>
                  <td class="px-4 py-3">
                    <Link :class="{'text-blue-500 underline': true }" :href="route('signage.show', signage.id)">{{ signage.file_name }}</Link>
                  </td>
                  <td class="px-4 py-3">{{ new Date(signage.created_at).toLocaleDateString('ja-JP') }}</td>
                </tr>

              </tbody>
            </table>
          </div>

        </div>
      </section>
    </template>
  </MainLayout>
</template>