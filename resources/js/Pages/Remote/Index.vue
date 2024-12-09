<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { onMounted, reactive } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
  user: Object,
  remoteComputer: Object,
});

const remoteComputer = reactive({
  machine_name: null,
  mac_address: null,
});

const copyClipBoard = (flg) => {
  const textArea = document.createElement("textarea");
  switch (flg) {
    case "machine_name":
      textArea.value = remoteComputer.machine_name;
      break;
    case "mac_address":
      textArea.value = remoteComputer.mac_address;
      break;
  }

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

onMounted(() => {
  // 要素の高さを調整
  // 関数にまとめるとうまく高さを取得できない為、ベタ書き
  // const description = document.querySelector("#description");
  // const client_view = document.querySelector("#client_view");
  // client_view.style.height = `${description.offsetHeight}px`;

  remoteComputer.machine_name = props.remoteComputer.machine_name;
  remoteComputer.mac_address = props.remoteComputer.mac_address;
});
</script>
<template>
  <MainLayout :title="'リモート接続'">
    <template #content>
      <div class="flex items-start justify-around pt-8">
        <div id="description" class="w-1/2 px-12">
          <h2 class="mb-2 text-2xl font-semibold text-gray-600 dark:text-white">
            リモート接続方法
          </h2>
          <div class="mt-8 mb-4 text-gray-600 font-bold">
            <p class="my-4 opacity-60">
              ログイン中：<span>{{ props.user.name }}</span>
            </p>

            <div v-if="remoteComputer.machine_name" class="bg-gray-100 p-4">
              <p class="my-2">
                接続先コンピュータID：<span>{{
                  remoteComputer.machine_name
                }}</span>
                <i
                  @click="copyClipBoard('machine_name')"
                  class="ml-2 fas fa-clipboard"
                ></i>
              </p>
              <p class="my-2">
                接続元MACアドレス：<span>{{ remoteComputer.mac_address }}</span>
                <i
                  @click="copyClipBoard('mac_address')"
                  class="ml-2 fas fa-clipboard"
                ></i>
              </p>
            </div>


            <div v-else class="bg-gray-100 p-4">
              <p class="my-2">
                接続先コンピュータが設定されていません。<br>
                <Link class="text-blue-600 underline" :href="route('remote.create')">リモート追加</Link>より、リモートコンピュータを登録してください。
              </p>

            </div>
          </div>

          <hr class="my-10" />

          <ol
            class="w-full space-y-1 text-gray-500 list-decimal list-inside dark:text-gray-400"
          >
            <li class="pb-8 font-semibold">
              <p class="inline">
                <a
                  target="blank"
                  href="https://webapp.telework.cyber.ipa.go.jp/"
                  ><span class="text-blue-600 underline">リンク</span></a
                >をクリックしてください。
              </p>
              <hr class="mt-8" />
            </li>
            <li class="pb-8 font-semibold">
              <p class="inline">
                上記のIDを<span class="font-bold text-red-600 mx-2"
                  >接続先コンピュータID</span
                >欄に入力してください。
              </p>

              <img
                class="mt-4"
                src="/storage/remote/description_1.png"
                alt=""
              />
              <hr class="mt-8" />
            </li>
            <li class="pb-8 font-semibold">
              下へスクロールしてください。
              <hr class="mt-8" />
            </li>
            <li class="pb-8 font-semibold">
              <p class="inline mb-4">
                仮想MACアドレス（オプション）に、上記MACアドレスをコピーして貼り付けてください。
              </p>

              <img
                class="mt-4"
                src="/storage/remote/description_2.png"
                alt=""
              />
              <hr class="mt-8" />
            </li>
            <li class="pb-8 font-semibold">
              このコンピュータIDにリモート接続をクリックしてください。
              <img
                class="mt-4"
                src="/storage/remote/description_3.png"
                alt=""
              />
              <hr class="mt-8" />
            </li>
          </ol>
        </div>

        <!-- <iframe
          class="w-1/2"
          id="client_view"
          src="https://webapp.telework.cyber.ipa.go.jp/"
          frameborder="0"
        ></iframe> -->
      </div>
    </template>
  </MainLayout>
</template>
<style scoped>
.client_view {
}
</style>
