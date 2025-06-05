<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { onMounted, reactive, ref } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";

const props = defineProps({
  movies: Array,
  base_movies: Array,
  search_text: String,
  tags: Array,
  categories: Array,
});

const base_movies = ref([]);
const select_movies = ref([]);

// ページネーション用
const movies = ref([]);

const search_text = ref('');

const categories = ref([]);
const base_tags = ref([]);
const tags = ref([]);

const searchMovie = () => {
  console.log(search_text.value);

  router.get(route("movie2"), {
    search_text: search_text.value,
  });
};

const changeCategory = (category_id) => {
  if (!category_id) {
    return;
  }

  tags.value = base_tags.value.filter(tag => tag.movie_tag_category_id == category_id);
  console.log(tags.value);
};

const changeTag = (tag_id) => {
  if(!tag_id){
    return;
  }
  
  select_movies.value = base_movies.value.filter(movie => movie.movie_tag_id == tag_id);
  if(select_movies.value.length == 0){
    alert('選択されたタグに一致する動画は見つかりませんでした。デフォルトに戻します。');
    select_movies.value = movies.value.data
  }

};

const redirectShow = (movie) => {
  window.location.href = route('movie2.show', { movie_id: movie.id })
}
onMounted(() => {
  movies.value = props.movies;
  base_movies.value = props.base_movies;
  select_movies.value = movies.value.data;

  search_text.value = props.search_text;

  categories.value = props.categories;
  base_tags.value = props.tags;
  tags.value = base_tags.value;
});
</script>
<template>
  <MainLayout :title="'動画視聴'">
    <template #content>
      <h1 class="text-center text-xl font-bold text-gray-800">動画一覧</h1>

      <section class="text-gray-600 body-font">
        <div class=" py-24 mx-auto">
          <div class="flex flex-col text-center w-full mb-8">
            <p class="lg:w-2/3 mx-auto leading-relaxed text-base">
              動画一覧を表示します。<br />
              こちらから視聴・編集ページへ遷移することが可能です。
            </p>

            <div class="mt-16 text-gray-600 flex justify-between items-center">
              <form @submit.prevent class="w-1/3 flex items-center relative">
                <input
                  v-model="search_text"
                  @change="searchMovie"
                  name="text"
                  class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring"
                  placeholder="キーワード検索"
                />
                <span class="absolute right-2 ml-2 material-symbols-outlined">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M12.9 14.32a8 8 0 111.414-1.414l4.387 4.387a1 1 0 01-1.414 1.414l-4.387-4.387zM8 14a6 6 0 100-12 6 6 0 000 12z"
                    />
                  </svg>
                </span>
              </form>

              <div id="sort_container" class="w-1/2">
                <div class="flex justify-end">
                  <div class="text-left pl-4">
                    <label for="email" class="leading-7 text-sm text-gray-600"
                      >カテゴリー</label
                    >
                    <select
                      name=""
                      id=""
                      class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
                      @change="changeCategory($event.target.value)"
                    >
                      <option value="">未選択</option>
                      <option
                        v-for="category in categories"
                        :key="category.id"
                        :value="category.id"
                      >
                        {{ category.name }}
                      </option>
                    </select>
                  </div>
                  <div class="text-left pl-4">
                    <label for="email" class="leading-7 text-sm text-gray-600"
                      >タグ</label
                    >
                    <select
                      name=""
                      id=""
                      class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
                      @change="changeTag($event.target.value)"
                    >
                      <option value="">未選択</option>
                      <option v-for="tag in tags" :key="tag.id" :value="tag.id">
                        {{ tag.name }}
                      </option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div v-if="movies.links" class="mb-8 flex justify-end">
            <Pagination :links="movies.links"></Pagination>
          </div>

          <div id="table_container" class="lg:w-full w-full mx-auto overflow-auto">
            <table class="table-auto w-full text-left whitespace-no-wrap">
              <thead>
                <tr>
                  <th
                    class="w-8 whitespace-nowrap px-4 py-4 title-font tracking-wider font-medium text-gray-900 bg-gray-100 rounded-tl rounded-bl"
                  >
                    日付
                  </th>
                  <th
                    class="w-8 whitespace-nowrap px-4 py-4 title-font tracking-wider font-medium text-gray-900 bg-gray-100 rounded-tl rounded-bl"
                  >
                    カテゴリ
                  </th>
                  <th
                    class="whitespace-nowrap px-4 py-4 title-font tracking-wider font-medium text-gray-900 bg-gray-100"
                  >
                    タグ
                  </th>
                  <th
                    class="whitespace-nowrap px-4 py-4 title-font tracking-wider font-medium text-gray-900 bg-gray-100"
                  >
                    ファイル名
                  </th>
                  <th
                    class="whitespace-nowrap px-4 py-4 title-font tracking-wider font-medium text-gray-900 bg-gray-100"
                  >
                    ファイルパス
                  </th>
                  <th
                    class="whitespace-nowrap px-4 py-4 title-font tracking-wider font-medium text-gray-900 bg-gray-100"
                  >
                    YoutubeID
                  </th>
                  <th
                    class="whitespace-nowrap px-4 py-4 title-font tracking-wider font-medium text-gray-900 bg-gray-100"
                  >
                    備考
                  </th>
                  <th
                    class="whitespace-nowrap px-4 py-4 title-font tracking-wider font-medium text-gray-900 bg-gray-100"
                  >
                    コメント数
                  </th>

                </tr>
              </thead>

              <tbody>
                <tr
                  @click="redirectShow(movie)"
                  v-for="movie in select_movies"
                  :key="movie.id"
                  :class="{'border-b border-gray-200 my-4 hover:bg-slate-200 transition': true,
                  'bg-green-50': movie.transcription_flg === 2,
                  'bg-orange-50': movie.transcription_flg === 1
                  }"
                >
                  <td class="w-1/6 whitespace-nowrap px-4 py-3 text-lg text-gray-900">
                    {{
                      new Date(movie.created_at).toLocaleDateString("ja-JP", {
                        year: "numeric",
                        month: "2-digit",
                        day: "2-digit",
                      })
                    }}
                  </td>

                  <td class="w-40 whitespace-nowrap px-4 py-3 text-lg text-gray-900">
                    <Link>
                      <span
                        :class="`bg-${movie.category_color}-100 text-${movie.category_color}-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-white dark:text-${movie.category_color}-400 border border-${movie.category_color}-400`"
                      >
                        {{ movie.movie_tag_category_name }}</span
                      ></Link
                    >
                  </td>

                  <td class="w-40 whitespace-nowrap px-4 py-3 text-lg">
                    <Link>
                      <span
                        :class="`bg-${movie.tag_color}-100 text-${movie.tag_color}-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-white dark:text-${movie.tag_color}-400 border border-${movie.tag_color}-400`"
                      >
                        {{ movie.movie_tag_name }}</span
                      >
                    </Link>
                  </td>

                  <td class="w-1/5 whitespace-nowrap px-4 py-3 text-lg text-gray-900">
                    {{ movie.name }}
                  </td>

                  <td class="w-1/5 whitespace-nowrap px-4 py-3 text-lg text-gray-900">
                    {{ movie.file_path }}
                  </td>

                  <td class="w-1/5 whitespace-nowrap px-4 py-3 text-lg text-gray-900">
                    {{ movie.youtube_id }}
                  </td>

                  <td class="w-1/5 whitespace-nowrap px-4 py-3 text-sm text-gray-900">
                    {{ movie.memo }}
                  </td>

                  <td class="w-16 whitespace-nowrap px-4 py-3 text-lg text-gray-900">
                    {{ movie.memos_count }}
                  </td>

                </tr>
              </tbody>
            </table>
          </div>

          <div v-if="movies.links" class="mt-8 flex justify-end">
            <Pagination :links="movies.links"></Pagination>
          </div>
        </div>
      </section>
    </template>
  </MainLayout>
</template>
<style scoped lang="scss">
#table_container{
  // width: 100vw;
  width: 100%;
  overflow-x: scroll;

  & table{
    width: 100vw;
  }
}
</style>