<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { onMounted, reactive, ref } from "vue";
import { Link } from "@inertiajs/vue3";
import axios from "axios";

const props = defineProps({
  movie: Object,
  con_movies: Array,
  movie_categories: Array,
  movie_tags: Array,
});

const video_id = ref(props.movie.file_path);
const movie_id = ref(props.movie.id);

const movie_memos = ref([]);

const page = ref("watch");

const form = reactive({
  memo: null,
  video_time: null,
});

let player;

window.onYouTubePlayerAPIReady = function () {
  console.log("YouTube Player API is ready.");
  player = new YT.Player("YTContent", {
    width: 800,
    height: 450,
    videoId: video_id.value,
    playerVars: {
      autoplay: 1,
      controls: 1,
      rel: 0,
      playsinline: 1,
    },
  });
};

function getCurrentTime() {
  if (player && player.getCurrentTime) {
    const totalSeconds = Math.floor(player.getCurrentTime());
    const hours = Math.floor(totalSeconds / 3600);
    const minutes = Math.floor((totalSeconds - hours * 3600) / 60);
    const seconds = totalSeconds - hours * 3600 - minutes * 60;
    const formattedTime = [hours, minutes, seconds]
      .map((v) => v.toString().padStart(2, "0"))
      .join(":");

    return formattedTime;
  }
}

// 指定した時間に移動
const seekToTime = (time) => {
  console.log(time);
  const time_array = time.split(":").map(Number);
  const time_seconds =
    time_array[0] * 3600 + time_array[1] * 60 + time_array[2];
  player.seekTo(time_seconds);
};

const copy_link = () => {
  const copy_text = window.location.href;
  const textArea = document.createElement("textarea");

  // テキストエリアにURLを設定
  textArea.value = copy_text;

  // テキストエリアをドキュメントに追加
  document.body.appendChild(textArea);

  // テキストエリアの内容を選択
  textArea.select();

  try {
    // コピーコマンドを実行
    document.execCommand("copy");
    console.log("URLがクリップボードにコピーされました");
    alert("URLをクリップボードにコピーしました。");
  } catch (err) {
    console.error("クリップボードへのコピーに失敗しました: ", err);
  }

  // テキストエリアをドキュメントから削除
  document.body.removeChild(textArea);
};

const getMemos = async () => {
  await axios
    .get(route("movie2.getMemos", { movie_id: movie_id.value }))
    .then((res) => {
      movie_memos.value = res.data;
    })
    .catch((error) => {
      console.log(error);
    });
};
const deleteMemo = (memo_id) => {
  console.log(memo_id);
  if (confirm("メモを削除してよろしいですか？")) {
    axios
      .get(route("movie2.deleteMemo", { memo_id: memo_id }))
      .then((res) => {
        alert("メモを削除しました。");
        getMemos();
      })
      .catch((error) => {
        console.log(error);
      });
  } else {
    alert("キャンセルしました。");
  }
};
const saveMemo = (memo_id, memo) => {
  console.log(memo_id, memo);
  if (confirm("保存しますか？")) {
    axios
      .post(route("movie2.saveMemo"), { memo_id: memo_id, new_memo_text: memo })
      .then((res) => {
        console.log(res.data);
        alert("メモを更新しました。");
        getMemos();
      })
      .catch((error) => {
        console.log(error);
      });
  } else {
    alert("キャンセルされました。");
  }
};

const sendMemo = () => {
  form.video_time = getCurrentTime();

  axios
    .post(route("movie2.addMemo"), {
      movie_id: movie_id.value,
      memo: form.memo,
      video_time: form.video_time,
    })
    .then((res) => {
      console.log(res.data);
      getMemos();
    })
    .catch((error) => {
      console.log(error);
    });
};

onMounted(() => {
  //   メモを取得
  getMemos();

  const tag = document.createElement("script");
  tag.src = "https://www.youtube.com/player_api";
  const firstScriptTag = document.getElementsByTagName("script")[0];
  firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
});
</script>
<template>
  <MainLayout :title="'動画視聴'">
    <template #content>
      <div class="w-full flex items-start justify-between">
        <div class="w-1/5 pr-4">
          <!-- 関連動画 -->
          <p class="text-gray-800 py-4 flex items-center text-sm">
            <i class="fas fa-question-circle pr-2 text-sm"></i>
            関連動画一覧を表示
          </p>
          <hr />
          <section class="text-gray-600 body-font">
            <div class="container">
              <div class="w-full overflow-auto">
                <table class="table-auto w-full text-left whitespace-no-wrap">
                  <tbody>
                    <tr
                      v-for="con_movie in con_movies"
                      :key="con_movie.id"
                      class="text-sm"
                    >
                      <td class="py-4" style="accent-color: gray">
                        <input type="checkbox" name="" id="" />
                      </td>

                      <td class="py-4">{{ con_movie.name }}</td>
                      <td class="py-4">
                        <a
                          :href="
                            route('movie2.show', { movie_id: con_movie.id })
                          "
                        >
                          <i class="text-gray-600 fas fa-video"></i>
                        </a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </section>
        </div>

        <div >
          <div class="flex justify-between items-center">
            <div
              id="copy_link"
              class="w-42 flex justify-start items-center mb-2 text-red-500"
              @click="copy_link()"
            >
              <i class="fas fa-link mr-1"></i>
              <span>動画を共有</span>
            </div>

            <div class="w-1/2">
              <ul class="flex justify-end border-b">
                <li class="-mb-px mr-1">
                  <button
                    @click="page = 'watch'"
                    :class="{
                      'text-sm bg-white inline-block rounded-t py-2 px-4 text-gray-700 box-border': true,
                      'border-l border-t border-r font-semibold':
                        page == 'watch',
                    }"
                  >
                    動画視聴
                  </button>
                </li>
                <li class="mr-1">
                  <button
                    @click="page = 'detail'"
                    :class="{
                      'text-sm bg-white inline-block rounded-t py-2 px-4 text-gray-700 box-border': true,
                      'border-l border-t border-r font-semibold':
                        page == 'detail',
                    }"
                    href="#"
                  >
                    動画詳細
                  </button>
                </li>
              </ul>
            </div>
          </div>

          <!-- Youtube動画画面 -->
          <div :class="{ main_content: true, active: page == 'watch' }">
            <div id="YTContent" :class="`video_content mx-auto`"></div>

            <form @submit.prevent action="" class="relative mt-16">
              <div class="flex justify-center">
                <input
                  class="block w-4/5 sm:w-2/3 bg-gray-200 py-2 px-3 text-gray-700 border border-gray-200 rounded focus:outline-none focus:bg-white mr-4"
                  id="memo"
                  type="text"
                  name=""
                  placeholder=""
                  v-model="form.memo"
                />
                <button
                  @click="sendMemo"
                  class="border border-gray-400 rounded-md px-2 py-1 text-sm font-semibold hover:text-white hover:bg-gray-800"
                  id="form-button"
                  type="submit"
                >
                  メモ追加
                </button>
              </div>
            </form>
          </div>

          <div :class="{ main_content: true, active: page == 'detail' }">
            <section class="w-2/3 mx-auto text-gray-600 body-font overflow-hidden">
              <div class="container py-8 mx-auto">
                <div class="mx-auto ">

                  <div class="w-full ">
                    <h2
                      class="text-sm title-font text-gray-500 tracking-widest"
                    >
                      タイトル
                    </h2>
                    <h1
                      class="text-gray-900 text-3xl title-font font-medium mb-1"
                    >
                      {{ movie.name }}
                    </h1>
                    

                    <p class="leading-relaxed">
                      {{ movie.memo }}
                    </p>
                    <div
                      class="flex mt-6 items-center pb-5 border-b-2 border-gray-100 mb-5"
                    >
                      <!-- <div class="flex">
                        <span class="mr-3">Color</span>
                        <button
                          class="border-2 border-gray-300 rounded-full w-6 h-6 focus:outline-none"
                        ></button>
                        <button
                          class="border-2 border-gray-300 ml-1 bg-gray-700 rounded-full w-6 h-6 focus:outline-none"
                        ></button>
                        <button
                          class="border-2 border-gray-300 ml-1 bg-indigo-500 rounded-full w-6 h-6 focus:outline-none"
                        ></button>
                      </div>
                      <div class="flex ml-6 items-center">
                        <span class="mr-3">Size</span>
                        <div class="relative">
                          <select
                            class="rounded border appearance-none border-gray-300 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 text-base pl-3 pr-10"
                          >
                            <option>SM</option>
                            <option>M</option>
                            <option>L</option>
                            <option>XL</option>
                          </select>
                          <span
                            class="absolute right-0 top-0 h-full w-10 text-center text-gray-600 pointer-events-none flex items-center justify-center"
                          >
                            <svg
                              fill="none"
                              stroke="currentColor"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              class="w-4 h-4"
                              viewBox="0 0 24 24"
                            >
                              <path d="M6 9l6 6 6-6"></path>
                            </svg>
                          </span>
                        </div>
                      </div> -->
                    </div>

                  </div>
                </div>
              </div>
            </section> 
          </div>

          <hr class="my-16" />
        </div>

        <div class="memo-container w-1/5 pl-4">
          <!-- コメント一覧 -->

          <form
            @submit.prevent
            v-for="memo in movie_memos"
            :key="memo.id"
            class="com_container pb-4 border-b border-indigo-100 mb-12"
          >
            <div>
              <div class="flex items-center justify-between">
                <p class="italic hover:text-indigo-500 text-sm">
                  {{ "@" + memo.user_name }}
                </p>
                <span>{{
                  new Date(memo.created_at).toLocaleDateString("ja-JP", {
                    year: "numeric",
                    month: "2-digit",
                    day: "2-digit",
                  })
                }}</span>
              </div>

              <p
                class="underline my-2 text-indigo-400 flex items-center font-semibold text-sm"
              >
                <span class="time" @click="seekToTime(memo.time)">
                  {{ memo.time }}
                </span>
                ~
              </p>

              <textarea
                @change="saveMemo(memo.id, $event.target.value)"
                name="memo"
                id=""
                cols="30"
                rows="3"
                class="w-full text-sm py-4 indent-2 font-serif text-gray-600 overflow-x-hidden border-transparent bg-gray-100"
                >{{ memo.memo }}
                </textarea
              >

              <div class="container mt-2">
                <button @click="deleteMemo(memo.id)">
                  <span class="mr-2 fas fa-trash-alt text-red-400"></span>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </template>
  </MainLayout>
</template>
<style scoped>
.memo-container {
  max-height: 72vh;
  overflow-y: scroll;
  scrollbar-width: thin;
  scrollbar-color: #4a5568 #edf2f7;
  padding-right: 1%;
}

.memo-container::-webkit-scrollbar {
  width: 8px;
}

.memo-container::-webkit-scrollbar-track {
  background: #edf2f7;
}

.memo-container::-webkit-scrollbar-thumb {
  background-color: #4a5568;
  border-radius: 10px;
  border: 2px solid #edf2f7;
}

.main_content {
  height: 0vh;
  overflow: hidden;
}
.main_content.active {
  height: auto;
}
</style>