<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { onMounted, reactive, ref } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";
import { getCategories, getCategoryTags } from "@/Helper/getByAxios";

const props = defineProps({
  movie_categories: Array,
});
const form = reactive({
  created_at: null,
  title: null,
  file_path: null,
  file: null,
  tag_id: null,
  description: null,
});

// 登録中フラグ
const upload_flg = ref(false);
const uploadingText = "アップロード中...";
const sendMovie = () => {
  if (!(form.title && form.tag_id)) {
    alert("すべての必須フィールドを入力してください。");
    return;
  }
  upload_flg.value = true

  // データを送信
  const formData = new FormData();
  formData.append("title", form.title);
  formData.append("created_at", form.created_at);
  formData.append("file_path", form.file_path);
  formData.append("file", form.file);
  formData.append("tag_id", form.tag_id);
  formData.append("description", form.description);

  axios
    .post(route("movie2.store"), formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    })
    .then((res) => {
      if (res.data.status === "ok" && res.data.url) {
        // RPAを起動
        axios
          .get(res.data.url)
          .then((res) => {
            upload_flg = false
            window.location.href = route("movie2");
          })
          .catch((error) => {
            console.log(error);
          });
      } else {
        alert(res.data.msg);
      }
    })
    .catch((error) => {
      console.log(error);
    });
};
const handleFileChange = (event) => {
  const file = event.target.files[0];
  const maxSizeInMB = 500;
  const maxSizeInBytes = maxSizeInMB * 1024 * 1024; 
  // 500MBをバイトに変換

  if (file) {
    if (file.size > maxSizeInBytes) {
      alert(
        `ファイルサイズが${maxSizeInMB}MBを超えています。別のファイルを選択してください。`
      );
      return;
    }
    form.file = file;
  }
};

const categories = ref([]);
const tags = ref([]);

const get_category_tags = async (category_id) => {
  tags.value = await getCategoryTags(category_id);
};

onMounted(async () => {
  categories.value = await getCategories();
});
</script>
<template>
  <MainLayout :title="'動画視聴'">
    <template #content>
      <div class="bg-white py-6 sm:py-8 lg:py-12">
        <div v-if="!upload_flg" class="mx-auto max-w-screen-2xl px-4 md:px-8">
          <!-- text - start -->
          <div class="mb-10 md:mb-16">
            <h2
              class="mb-4 text-center text-2xl font-bold text-gray-800 md:mb-6 lg:text-3xl"
            >
              動画追加
            </h2>

            <p
              class="mx-auto max-w-screen-md text-center text-gray-500 md:text-lg"
            >
              動画をアップロードすることができます。
            </p>
          </div>

          <form
            @submit.prevent
            class="mx-auto grid max-w-screen-md gap-4 sm:grid-cols-2"
            method="post"
          >
            <div class="sm:col-span-2">
              <label
                for="file"
                class="font-semibold mb-2 inline-block text-sm text-gray-800 sm:text-base"
                >投稿日</label
              >
              <input
                id="date"
                name="date"
                type="date"
                class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring"
                v-model="form.created_at"
              />
            </div>

            <div class="sm:col-span-2">
              <label
                for="title"
                class="font-semibold mb-2 inline-block text-sm text-gray-800 sm:text-base"
                >タイトル</label
              >
              <input
                v-model="form.title"
                id="title"
                name="title"
                class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring"
                placeholder="サンプルタイトル"
              />
            </div>

            <!-- ファイル -->
            <hr class="sm:col-span-2 my-4" />
            <p class="sm:col-span-2 text-gray-400 text-sm">
              ファイルを選択してください。<br />
              ファイルを選択する場合、共有フォルダ内に配置した動画のファイルパスを記入してください。<br />
            </p>
            <div class="sm:col-span-2">
              <label
                for="file_path"
                class="font-semibold mb-2 inline-block text-sm text-gray-800 sm:text-base"
                >ファイルパス</label
              >
              <input
                v-model="form.file_path"
                id="file_path"
                name="file_path"
                class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring"
                placeholder="C://sample-directory/movie/test_movie.mp4"
              />
            </div>
            <!-- <div class="sm:col-span-2">
              <label
                for="file"
                class="font-semibold mb-2 inline-block text-sm text-gray-800 sm:text-base"
                >ファイル</label
              >
              <input
                @change="handleFileChange"
                id="file"
                name="file"
                type="file"
                accept="video/*"
                class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring"
                placeholder="サンプルファイル１"
              />
            </div> -->
            <!-- <p class="sm:col-span-2 text-gray-400 text-sm">
              <span class="text-red-400"
                >どちらも入力されている場合は、ファイルパスが優先されます。</span
              >
            </p> -->

            <hr class="sm:col-span-2 my-4" />

            <!-- カテゴリー -->
            <div class="sm:col-span-2">
              <label
                for="category_id"
                class="font-semibold mb-2 inline-block text-sm text-gray-800 sm:text-base"
                >動画カテゴリ</label
              >
              <select
                @change="get_category_tags($event.target.value)"
                id="category_id"
                :class="{
                  'w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring': true,
                }"
                name="category_id"
              >
                <option value="0">選択してください。</option>

                <option
                  v-for="category in categories"
                  :key="category.id"
                  :value="category.id"
                >
                  {{ category.name }}
                </option>
              </select>
            </div>
            <!-- タグ -->
            <div class="sm:col-span-2">
              <label
                for="tag_id"
                class="font-semibold mb-2 inline-block text-sm text-gray-800 sm:text-base"
                >動画タグ</label
              >
              <select
                v-model="form.tag_id"
                id="tag_id"
                class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring"
                name="tag_id"
              >
                <option value="0">選択してください。</option>

                <option v-for="tag in tags" :key="tag.id" :value="tag.id">
                  {{ tag.name }}
                </option>
              </select>
            </div>

            <hr class="sm:col-span-2 my-4" />

            <!-- 説明 -->
            <div class="sm:col-span-2">
              <label
                for="description"
                class="font-semibold mb-2 inline-block text-sm text-gray-800 sm:text-base"
                >説明</label
              >
              <textarea
                v-model="form.description"
                id="description"
                class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring"
                name="description"
                cols="30"
                rows="10"
              ></textarea>
            </div>

            <div class="flex items-center justify-between sm:col-span-2">
              <button
                @click="sendMovie"
                class="inline-block rounded-lg bg-indigo-500 px-8 py-3 text-center text-sm font-semibold text-white outline-none ring-indigo-300 transition duration-100 hover:bg-indigo-600 focus-visible:ring active:bg-indigo-700 md:text-base"
              >
                作成
              </button>

              <span class="text-sm text-gray-500">*Required</span>
            </div>
          </form>
        </div>
        <div v-else class="text-center">
          <h2 class="uploading-text text-4xl text-gray-500 mb-12 font-bold">
            <span
              v-for="(char, index) in uploadingText"
              :key="index"
              :style="{ animationDelay: `${index * 0.1}s` }"
            >
              {{ char }}
            </span>
          </h2>
          <p class="mb-16 text-lg mx-auto text-red-400">この画面は閉じていただいても構いません。通常、1~2分程度でアップロードが完了します。</p>
          <div class="flex justify-center">
            <img class="w-1/3" src="/uploading.gif" alt="Uploading GIF" />
          </div>
        </div>
      </div>
    </template>
  </MainLayout>
</template>
<style scoped>
.uploading-text span {
  font-family: monospace;
  opacity: 0;
  display: inline-block;
  animation: fadeIn 2s infinite;
}

@keyframes fadeIn {
  to {
    opacity: 1;
  }
}
</style>