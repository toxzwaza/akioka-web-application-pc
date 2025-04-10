<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { onMounted, reactive, ref } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";

onMounted(() => {});
</script>
<template>
  <MainLayout :title="'HOME'">
    <template #content>
      <section class="text-gray-600 body-font">
        <div class="container px-5 py-24 mx-auto">
          <div class="flex flex-wrap w-full mb-20">
            <div class="lg:w-1/2 w-full mb-6 lg:mb-0">
              <h1
                class="sm:text-3xl text-2xl font-medium title-font mb-2 text-gray-900"
              >
                アキオカ社内システム管理画面
              </h1>
              <div class="h-1 w-20 bg-indigo-500 rounded"></div>
            </div>
            <p class="lg:w-1/2 w-full leading-relaxed text-gray-500">
              社内システム管理画面を効率的に管理するためのツールです。様々な機能を備えており、業務効率を向上させます。.
            </p>
          </div>

          <div class="flex flex-wrap -m-4">
            <a class="xl:w-1/4 md:w-1/2 p-4" href="{{ route('stock') }}">
              <div class="">
                <div class="bg-gray-100 p-6 rounded-lg">
                  <img
                    class="h-40 rounded w-full object-cover object-center mb-6"
                    src="img/top/stock.png"
                    alt="content"
                  />
                  <h3
                    class="tracking-widest text-indigo-500 text-xs font-medium title-font"
                  >
                    SUBTITLE
                  </h3>
                  <h2 class="text-lg text-gray-900 font-medium title-font mb-4">
                    在庫管理システム
                  </h2>
                  <p class="leading-relaxed text-base">
                    在庫の不足や過剰を迅速に把握し、効率的な補充とコスト削減が可能となります。
                  </p>
                </div>
              </div>
            </a>

            <a class="xl:w-1/4 md:w-1/2 p-4" href="{{ route('order') }}">
              <div class="">
                <div class="bg-gray-100 p-6 rounded-lg">
                  <img
                    class="h-40 rounded w-full object-cover object-center mb-6"
                    src="img/top/order.png"
                    alt="content"
                  />
                  <h3
                    class="tracking-widest text-indigo-500 text-xs font-medium title-font"
                  >
                    SUBTITLE
                  </h3>
                  <h2 class="text-lg text-gray-900 font-medium title-font mb-4">
                    発注管理システム
                  </h2>
                  <p class="leading-relaxed text-base">
                    在庫状況をリアルタイムで監視し、必要な商品の自動発注をサポートします。
                  </p>
                </div>
              </div>
            </a>

            <a href="{{ route('lunch') }}" class="xl:w-1/4 md:w-1/2 p-4">
              <div class="">
                <div class="bg-gray-100 p-6 rounded-lg">
                  <img
                    class="h-40 rounded w-full object-cover object-center mb-6"
                    src="img/top/lunch.png"
                    alt="content"
                  />
                  <h3
                    class="tracking-widest text-indigo-500 text-xs font-medium title-font"
                  >
                    SUBTITLE
                  </h3>
                  <h2 class="text-lg text-gray-900 font-medium title-font mb-4">
                    弁当注文システム
                  </h2>
                  <p class="leading-relaxed text-base">
                    日々の弁当注文から発注、数量計算までを一元管理することで、時間を効率的に使います。
                  </p>
                </div>
              </div>
            </a>
            <a href="{{ route('movie2') }}" class="xl:w-1/4 md:w-1/2 p-4">
              <div class="">
                <div class="bg-gray-100 p-6 rounded-lg">
                  <img
                    class="h-40 rounded w-full object-cover object-center mb-6"
                    src="img/top/movie.png"
                    alt="content"
                  />
                  <h3
                    class="tracking-widest text-indigo-500 text-xs font-medium title-font"
                  >
                    SUBTITLE
                  </h3>
                  <h2 class="text-lg text-gray-900 font-medium title-font mb-4">
                    動画視聴システム
                  </h2>
                  <p class="leading-relaxed text-base">
                    録画・録音にメモを追加でき、カテゴリーやタグで詳細管理が可能です。
                  </p>
                </div>
              </div>
            </a>
          </div>
        </div>
      </section>

      <!-- バージョン変更お知らせタイトル -->
      <section class="text-gray-600 body-font overflow-hidden">
        <div class="container px-5 py-24 mx-auto">
          <div class="flex flex-wrap w-full mb-20">
            <div class="lg:w-1/2 w-full mb-6 lg:mb-0">
              <h1
                class="sm:text-2xl text-xl font-medium title-font mb-2 text-gray-900"
              >
                機能追加LOG
              </h1>
              <div class="h-1 w-20 bg-gray-500 rounded"></div>
            </div>
          </div>

          <div class="-my-8 divide-y-2 divide-gray-100">
            <div class="py-8 flex flex-wrap md:flex-nowrap">
              <div class="md:w-64 md:mb-0 mb-6 flex-shrink-0 flex flex-col">
                <span class="font-semibold title-font text-gray-700"
                  >基幹マスタ管理</span
                >
                <span class="mt-1 text-gray-500 text-sm">2024/12/07</span>
              </div>
              <div class="md:flex-grow">
                <h2 class="text-2xl font-medium text-gray-900 title-font mb-2">
                  従業員登録を追加しました。
                </h2>
                <p class="leading-relaxed">
                  弊社に在籍しているすべての従業員が対象となります。「氏名・パスワード・所属部署・役職・製造工程」が必須項目となります。<br />
                  編集したい場合、従業員一覧から、対象ユーザーのIDをクリックすることで編集画面へ遷移することが可能です。
                </p>
                <p class="url mt-4">
                  参考URL：
                  <a
                    class="text-blue-500 underline"
                    href="{{ route('stock.tablet.receive') }}"
                    >従業員登録</a
                  >
                </p>
              </div>
            </div>
            <div class="py-8 flex flex-wrap md:flex-nowrap">
              <div class="md:w-64 md:mb-0 mb-6 flex-shrink-0 flex flex-col">
                <span class="font-semibold title-font text-gray-700"
                  >タブレット納品登録</span
                >
                <span class="mt-1 text-gray-500 text-sm">2024/12/06</span>
              </div>
              <div class="md:flex-grow">
                <h2 class="text-2xl font-medium text-gray-900 title-font mb-2">
                  タブレット用納品書登録を追加しました。
                </h2>
                <p class="leading-relaxed">
                  <a
                    class="text-blue-500 underline"
                    href="{{ route('stock.tablet.receive') }}"
                    >リンク</a
                  >より、納品登録ページへ遷移できます。納品書登録画面より、納品書を登録することで納品登録の状態へ遷移します。納品登録では、納品数量を登録することが可能です。数量登録を完了すると、引き渡し登録状態へ遷移し、二回食堂サイネージディスプレイの表示が「納品済」へ更新されます。現場発注者に引き渡した際に登録すると、二回食堂に表示されているサイネージディスプレイの表示が解除されます。
                </p>
              </div>
            </div>
            <div class="py-8 flex flex-wrap md:flex-nowrap">
              <div class="md:w-64 md:mb-0 mb-6 flex-shrink-0 flex flex-col">
                <span class="font-semibold title-font text-gray-700"
                  >サイネージ</span
                >
                <span class="text-sm text-gray-500">2024/12/03</span>
              </div>
              <div class="md:flex-grow">
                <h2 class="text-2xl font-medium text-gray-900 title-font mb-2">
                  サイネージ機能追加
                </h2>
                <p class="leading-relaxed">
                  <a
                    class="text-blue-500 underline"
                    href="{{ route('signage.home') }}"
                    >リンク</a
                  >より、サイネージ管理画面へ遷移します。当リンクより、PDFファイルのアップロード、公開情報の変更とアップロードファイルの削除が可能です。画像(jpef,
                  png他)や動画の追加に関しては、<span>詳細な編集はこちら</span>から、行ってください。
                </p>
              </div>
            </div>
          </div>

        </div>
      </section>
    </template>
  </MainLayout>
</template>
<style scoped lang="scss">
</style>