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

const video_id = ref(props.movie.youtube_id);
const movie_id = ref(props.movie.id);

const movie_memos = ref([]);
const movie_transcription_memos = ref([]);

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
      console.log(res.data);
      movie_memos.value = res.data.memos;
      movie_transcription_memos.value = res.data.transcription_memos;
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

  axios
    .post(route("movie2.saveMemo"), { memo_id: memo_id, new_memo_text: memo })
    .then((res) => {
      console.log(res.data);
      getMemos();
    })
    .catch((error) => {
      console.log(error);
    });
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
const deleteMovie = () => {
  if (confirm("動画を削除してもよろしいですか？")) {
    axios
      .delete(route("movie2.delete"), {
        params: {
          movie_id: movie_id.value,
        },
      })
      .then((res) => {
        if (res.data.status) {
          alert("削除が完了しました。");
          window.location.href = route("movie2");
        }
      });
  } else {
    alert("動画削除を中断しました。");
  }
};

const changeMovie = (flg) => {
  let value = "";
  switch (flg) {
    case "file_path":
      value = props.movie.file_path;
      break;
    case "youtube_id":
      value = props.movie.youtube_id;
      break;
  }
  console.log(flg, value, movie_id.value);

  if (value) {
    axios
      .post(route("movie2.update"), {
        movie_id: movie_id.value,
        flg: flg,
        value: value,
      })
      .then((res) => {
        console.log(res.data);
        if (res.data.status) {
          window.location.reload();
        }
      })
      .catch((error) => {
        console.log(error);
      });
  }
};
onMounted(() => {
  //   メモを取得
  getMemos();

  console.log(props.movie);

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

        <div>
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
            <section
              class="w-2/3 mx-auto text-gray-600 body-font overflow-hidden"
            >
              <div class="container py-8 mx-auto">
                <div class="mx-auto">
                  <div class="w-full">
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
                      {{ movie.memo !== "null" ? movie.memo : "" }}
                    </p>
                    <div class="flex flex-wrap -mx-3 mb-6 mt-4">
                      <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        <label
                          class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                          for="grid-first-name"
                        >
                          ファイルパス
                        </label>
                        <input
                          class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                          id="grid-first-name"
                          type="text"
                          v-model="props.movie.file_path"
                          @change="changeMovie('file_path')"
                        />
                      </div>
                      <div class="w-full md:w-1/2 px-3">
                        <label
                          class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                          for="grid-last-name"
                        >
                          YoutubeID
                        </label>
                        <input
                          class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                          id="grid-last-name"
                          type="text"
                          v-model="props.movie.youtube_id"
                          @change="changeMovie('youtube_id')"
                        />
                      </div>
                    </div>

                    <div
                      class="flex mt-12 items-center pb-5 border-b-2 border-gray-100 mb-5"
                    >
                      <button
                        @click="deleteMovie"
                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                      >
                        動画を削除する<i class="ml-2 fas fa-trash-alt"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </section>
          </div>
        </div>

        <div class="memo-container w-1/5 pl-4">
          <!-- 文字お越し -->
          <div id="transcription" class="mb-8 bg-gray-50 p-4">
            <h1 class="font-bold text-lg text-gray-700 mb-4">文字お越し</h1>
            <div
              class="text-sm p-2"
              v-for="transcription_memo in movie_transcription_memos"
              :key="transcription_memo.id"
            >
              <span
                class="italic text-indigo-500"
                @click="seekToTime(transcription_memo.time)"
                >{{ transcription_memo.time }}</span
              >
              <br />
              <span
                v-if="!transcription_memo.edit"
                @click="transcription_memo.edit = 1"
                >{{ transcription_memo.memo }}
              </span>

              <div v-else>
                <textarea name="" id="" cols="30" rows="3" v-model="transcription_memo.memo"></textarea>
                <div class="flex items-center justify-end">
                  <button
                    @click="
                      saveMemo(transcription_memo.id, transcription_memo.memo)
                    "
                    class="mr-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                  >
                    <i class="fas fa-save"></i>
                  </button>

                  <button
                    @click="transcription_memo.edit = 0"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                  >
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- コメント一覧 -->
          <form
            @submit.prevent
            v-for="memo in movie_memos"
            :key="memo.id"
            class="com_container border-b border-indigo-100 mb-12 bg-gray-50 p-4"
          >
            <div>
              <h1 class="font-bold text-lg text-gray-700 mb-4">コメント</h1>

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
<style scoped lang="scss">
.memo-container {
  height: 100vh;
  overflow-y: auto;
  padding-right: 1%;

  & .com_container {
    height: 50%;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #4a5568 #edf2f7;

    &::-webkit-scrollbar {
      width: 8px;
    }

    &::-webkit-scrollbar-track {
      background: #edf2f7;
      border-radius: 10px;
    }

    &::-webkit-scrollbar-thumb {
      background-color: #4a5568;
      border-radius: 10px;
      border: 2px solid #edf2f7;
    }
  }
  & #transcription {
    height: 50%;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #4a5568 #edf2f7;

    &::-webkit-scrollbar {
      width: 8px;
    }

    &::-webkit-scrollbar-track {
      background: #edf2f7;
      border-radius: 10px;
    }

    &::-webkit-scrollbar-thumb {
      background-color: #4a5568;
      border-radius: 10px;
      border: 2px solid #edf2f7;
    }
  }
}

.main_content {
  height: 0vh;
  overflow: hidden;
}
.main_content.active {
  height: auto;
}
</style>