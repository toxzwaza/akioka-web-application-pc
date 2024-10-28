<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { onMounted, reactive, ref } from "vue";
import { router, Link } from "@inertiajs/vue3";

const props = defineProps({
  movies: Object,
});

onMounted(() => {
  console.log(props.movies);
});
</script>
<template>
  <MainLayout :title="'動画視聴'">
    <template #content>
      <h1 class="text-center text-xl font-bold text-gray-800">動画一覧</h1>

      <section class="text-gray-600 body-font">
        <div class="container px-5 py-24 mx-auto">
          <div class="flex flex-col text-center w-full mb-16">
            <p class="lg:w-2/3 mx-auto leading-relaxed text-base">
              動画一覧を表示します。<br />
              こちらから視聴・編集ページへ遷移することが可能です。
            </p>

            <div class="mt-10 text-gray-600">
              <form @submit.prevent class="w-1/3 flex items-center relative">
                <input
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
            </div>
          </div>

          <div class="mb-8 flex justify-end">
            <Pagination :links="movies.links"></Pagination>
          </div>

          <div class="lg:w-full w-full mx-auto overflow-auto">
            <table class="table-auto w-full text-left whitespace-no-wrap">
              <thead>
                <tr>
                  <th
                    class="w-8 px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100 rounded-tl rounded-bl"
                  >
                    日付
                  </th>
                  <th
                    class="w-8 px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100 rounded-tl rounded-bl"
                  >
                    カテゴリ
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100"
                  >
                    タグ
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100"
                  >
                    ファイル名
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100"
                  >
                    備考
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100"
                  >
                    コメント数
                  </th>

                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 bg-gray-100"
                  ></th>
                </tr>
              </thead>

              <tbody>
                <tr
                  v-for="movie in movies.data"
                  :key="movie.id"
                  class="border-b border-gray-200 my-4 hover:bg-slate-200 transition"
                >
                  <td class="w-1/6 px-8 py-8 text-lg text-gray-900">
                    {{
                      new Date(movie.created_at).toLocaleDateString("ja-JP", {
                        year: "numeric",
                        month: "2-digit",
                        day: "2-digit",
                      })
                    }}
                  </td>

                  <td class="w-40 px-8 py-8 text-lg text-gray-900">
                    <Link>
                      <span
                        :class="`bg-${movie.category_color}-100 text-${movie.category_color}-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-white dark:text-${movie.category_color}-400 border border-${movie.category_color}-400`"
                      >
                        {{ movie.movie_tag_category_name }}</span
                      ></Link
                    >
                  </td>

                  <td class="w-40 px-8 py-8 text-lg">
                    <Link>
                      <span
                        :class="`bg-${movie.tag_color}-100 text-${movie.tag_color}-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-white dark:text-${movie.tag_color}-400 border border-${movie.tag_color}-400`"
                      >
                        {{ movie.movie_tag_name }}</span
                      >
                    </Link>
                  </td>

                  <td class="w-1/5 px-8 py-8 text-lg text-gray-900">
                    {{ movie.name }}
                  </td>

                  <td class="w-1/5 px-8 py-8 text-sm text-gray-900">
                    {{ movie.memo }}
                  </td>

                  <td class="w-16 px-8 py-8 text-lg text-gray-900">
                    {{ movie.memos_count }}
                  </td>

                  <td class="px-8 py-8 text-lg text-gray-900">
                    <a :href="route('movie2.show', { movie_id: movie.id })">
                      <i class="text-gray-600 fas fa-video"></i>
                    </a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="mt-4 flex justify-end">
            <Pagination :links="movies.links"></Pagination>
          </div>
        </div>
      </section>
    </template>
  </MainLayout>
</template>