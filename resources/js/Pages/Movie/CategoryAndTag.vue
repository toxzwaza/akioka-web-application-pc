<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { onMounted, reactive, ref } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";

const props = defineProps({});
const movie_categories = ref([]);
const category_tags = ref([]);
const getCategories = () => {
  axios
    .get(route("movie2.getCategories"))
    .then((res) => {
      console.log(res.data);
      movie_categories.value = res.data;
    })
    .catch((error) => {
      console.log(error);
    });
};
const getCategoryTags = (category_id) => {
  axios
    .get(route("movie2.getTags", { category_id: category_id }))
    .then((res) => {
      console.log(res.data);
      category_tags.value = res.data;
    });
};
const category_form = reactive({
  name: null,
  color: null,
});
const tag_form = reactive({
  category_id: null,
  name: null,
  color: null,
});

const addCategory = () => {
  console.log(category_form);
  axios.post(route("movie2.create.category"), category_form).then((res) => {
    console.log(res.data);
    getCategories();
  });
};
const addTag = () => {
  console.log(tag_form);
  axios.post(route("movie2.create.tag"), tag_form).then((res) => {
    console.log(res.data);
    getCategoryTags(tag_form.category_id);

  });
};
onMounted(() => {
  getCategories();
});
</script>
<template>
  <MainLayout :title="'動画視聴'">
    <template #content>
      <div class="flex justify-between">
        <div class="w-1/2 bg-white py-6 sm:py-8 lg:py-12">
          <div class="mx-auto max-w-screen-2xl px-4 md:px-8">
            <!-- text - start -->
            <div class="mb-10 md:mb-16">
              <h2
                class="mb-4 text-center text-2xl font-bold text-gray-800 md:mb-6 lg:text-3xl"
              >
                カテゴリー追加
              </h2>

              <p
                class="mx-auto max-w-screen-md text-center text-gray-500 md:text-lg"
              >
                動画の分類カテゴリーを作成します。
              </p>
            </div>
            <!-- text - end -->

            <!-- form - start -->
            <form
              @submit.prevent
              class="mx-auto grid max-w-screen-md gap-4 sm:grid-cols-2"
            >
              <div class="sm:col-span-2">
                <label
                  for="subject"
                  class="mb-2 inline-block text-sm text-gray-600 sm:text-base"
                  >カテゴリー名*</label
                >
                <input
                  v-model="category_form.name"
                  name="subject"
                  class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring"
                />
              </div>
              <div class="sm:col-span-2">
                <label
                  for="subject"
                  class="mb-2 inline-block text-sm sm:text-base text-gray-600"
                  >アクセントカラー*</label
                >
                <select
                  v-model="category_form.color"
                  name="accent_color"
                  id="accent_color"
                  class="w-full rounded border bg-gray-50 px-3 py-2 outline-none transition duration-100 focus:ring"
                >
                  <option value="">未選択</option>
                  <option value="yellow">黄色</option>
                  <option value="red">赤</option>
                  <option value="green">緑</option>
                  <option value="blue">青</option>
                  <option value="indigo">水色</option>
                  <option value="purple">紫</option>
                  <option value="pink">ピンク</option>
                </select>
              </div>

              <div class="w-full sm:col-span-2 mb-12">
                <p class="text-gray-400 text-xs mb-2">作成済みカテゴリ↓</p>

                <span
                  v-for="category in movie_categories"
                  :key="category.id"
                  :class="`bg-${category.accent_color}-100 text-${category.accent_color}-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-${category.accent_color}-400 border border-${category.accent_color}-400`"
                  >{{ category.name }}
                </span>
              </div>

              <div class="flex items-center justify-between sm:col-span-2">
                <button
                  @click="addCategory"
                  class="inline-block rounded-lg bg-indigo-500 px-8 py-3 text-center text-sm font-semibold text-white outline-none ring-indigo-300 transition duration-100 hover:bg-indigo-600 focus-visible:ring active:bg-indigo-700 md:text-base"
                >
                  追加
                </button>

                <span class="text-sm text-gray-500">*Required</span>
              </div>
            </form>
            <!-- form - end -->
          </div>
        </div>

        <div class="w-1/2 bg-white py-6 sm:py-8 lg:py-12">
          <div class="mx-auto max-w-screen-2xl px-4 md:px-8">
            <!-- text - start -->
            <div class="mb-10 md:mb-16">
              <h2
                class="mb-4 text-center text-2xl font-bold text-gray-600 md:mb-6 lg:text-3xl"
              >
                タグ追加
              </h2>

              <p
                class="mx-auto max-w-screen-md text-center text-gray-500 md:text-lg"
              >
                タグを設定することで、カテゴリーからさらに詳しく分類分けすることができます。
              </p>
            </div>
            <!-- text - end -->

            <!-- form - start -->
            <form
              @submit.prevent
              class="mx-auto grid max-w-screen-md gap-4 sm:grid-cols-2"
            >
              <div class="sm:col-span-2">
                <label
                  for="company"
                  class="mb-2 inline-block text-sm text-gray-600 sm:text-base"
                  >カテゴリー*</label
                >
                <select
                  v-model="tag_form.category_id"
                  @change="getCategoryTags($event.target.value)"
                  name=""
                  id="category_select"
                  class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-600 outline-none ring-indigo-300 transition duration-100 focus:ring"
                >
                  <option
                    v-for="category in movie_categories"
                    :key="category.id"
                    :value="category.id"
                  >
                    {{ category.name }}
                  </option>
                </select>
              </div>

              <div class="sm:col-span-2">
                <label
                  for="email"
                  class="mb-2 inline-block text-sm text-gray-600 sm:text-base"
                  >タグ名*</label
                >
                <input
                  v-model="tag_form.name"
                  name="email"
                  class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-600 outline-none ring-indigo-300 transition duration-100 focus:ring"
                />
              </div>
              <div class="w-full sm:col-span-2 mb-12">
                <p class="text-gray-400 text-xs mb-2">作成済みタグ↓</p>
                <p id="tag_container">
                  <span
                    v-for="tag in category_tags"
                    :key="tag.id"
                    :class="`bg-${tag.accent_color}-100 text-${tag.accent_color}-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-${tag.accent_color}-400 border border-${tag.accent_color}-400`"
                  >
                    {{ tag.name }}
                  </span>
                </p>
              </div>

              <div class="sm:col-span-2">
                <label
                  for="subject"
                  class="mb-2 inline-block text-sm sm:text-base text-gray-600"
                  >アクセントカラー*</label
                >
                <select
                  v-model="tag_form.color"
                  name="accent_color"
                  id="tag_accent_color"
                  class="w-full rounded border bg-gray-50 px-3 py-2 outline-none transition duration-100 focus:ring"
                >
                  <option value="">未選択</option>
                  <option value="yellow">黄色</option>
                  <option value="red">赤</option>
                  <option value="green">緑</option>
                  <option value="blue">青</option>
                  <option value="indigo">水色</option>
                  <option value="purple">紫</option>
                  <option value="pink">ピンク</option>
                </select>
              </div>

              <div class="flex items-center justify-between sm:col-span-2 mt-8">
                <button
                  @click="addTag"
                  class="inline-block rounded-lg bg-white px-8 py-2 text-center text-sm font-semibold text-indigo-400 outline ring-indigo-300 transition duration-100 hover:bg-indigo-600 hover:text-white focus-visible:ring active:bg-indigo-700 md:text-base"
                >
                  追加
                </button>

                <span class="text-sm text-gray-500">*Required</span>
              </div>
            </form>
            <!-- form - end -->
          </div>
        </div>
      </div>
    </template>
  </MainLayout>
</template>