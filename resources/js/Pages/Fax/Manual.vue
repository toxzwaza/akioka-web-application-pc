<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { onMounted, reactive, ref } from "vue";
import { router } from "@inertiajs/vue3";
</script>
<template>
  <MainLayout :title="'FAX振り分け'">
    <template #content>
      <div class="px-8 my-8 mt-16 w-2/3 mx-auto">
        <h1 class="text-gray-600 text-2xl font-bold mb-4 text-center">
          FAX自動振り分けマニュアル
        </h1>

        <div class="bg-white rounded px-8  pt-16 mb-4">
          <a :href="route('fax.folder')" class="text-blue-700 text-xl font-bold underline mb-4">
            ①フォルダ割り当て
          </a>
          <div class="text-gray-700 text-base mb-4">
            <p class="my-4">
              まず、初めに振り分けを行うユーザーのフォルダを登録します。<br />
              検索ボックスより、<span class="font-bold mx-2">氏名</span
              >をもとに検索します。<br />
            </p>
            <p class="my-4">
              検索ボックス下部の一覧より、検索対象のID,氏名,フォルダ名が表示されます。<br />

              フォルダ名はテキストが入力できるので、<br />
              <span class="my-2 py-2 pl-4 block bg-gray-100 rounded">
                \\sv-db01\d\Archives\DocuWorks\02_受渡し
              </span>
              の中に登録したいフォルダ名を入力して、カーソルを外れてください。
              <span class="block text-red-500 text-sm mb-2"
                >※カーソルを外れるだけで自動保存がかかります。</span
              >
              <img class="w-1/2 mb-8" src="/img/manual/m_1.png" alt="" />
            </p>

            なお、空白となっている場合、振り分けは行われませんのでご注意ください。
          </div>
        </div>

        <div class="bg-white rounded px-8  pt-16 mb-4">
          <a :href="route('fax.group')" class="text-blue-700 text-xl font-bold underline mb-4">
            ②グループ作成
          </a>
          <div class="text-gray-700 text-base mb-4">
            <p class="my-4">
              振り分けを設定するためには、<span class="font-bold mx-2"
                >グループ</span
              >を作成する必要があります。<br />
              作成した、グループにユーザーを追加することで、複数人をまとめて振り分け対象として設定することが可能になります。
              <span class="block text-sm text-red-500 mt-2"
                >複数人をまとめて振り分け対象とするのはお勧めしません。</span
              >
            </p>
            <p class="my-4">
              グループ作成より、作成したグループ名を入力して、追加ボタンを押下してください。<br />
              作成が完了すると、下部の一覧より確認できます。<br />
            </p>
            <p class="my-4">
              一覧の編集を押下することで、編集モードに入ります。
              ここでは、グループ名の編集・グループ削除に加え、ユーザーの追加・削除が可能です。<br />
              ユーザー割り当てより、氏名をもとに検索すると、検索結果に合致するユーザの一覧が表示されます。

              <img class="w-1/2 mb-4" src="/img/manual/m_2.png" alt="" /><br />

              追加ボタンをクリックすると、左側の割り当て一覧に追加されます。<br />
              通知欄をクリックすることで通知の有無を変更できます。<br />
              <img class="w-1/2 mb-4" src="/img/manual/m_3.png" alt="" /><br />

              最後に保存ボタンを押して、変更を確定して下さい。
            </p>
          </div>
        </div>
        <div class="bg-white rounded px-8 pt-16 mb-4">
          <a :href="route('fax')" class="text-blue-700 text-xl font-bold underline mb-4">
            ③振り分け設定
          </a>
          <div class="text-gray-700 text-base mb-4">
            <p class="my-4">
              送信元名, 送信元FAX番号と作成したグループを紐づけることで振り分け設定を登録できます。<br />
              送信元名もしくは、送信元FAX番号を入力し、紐づけたい振り分け先グループを選択してください。<br>
              追加ボタンをクリックすると、振り分け一覧に追加されます。
              <img class="w-1/2 mb-4" src="/img/manual/m_4.png" alt="">
            </p>
            <p class="my-4">
              振り分け一覧の編集ボタンをクリックすると編集・削除モードへ移行します。<br>
              ここでできることは、グループ設定の編集モードと同様です。
            </p>
            
          </div>
        </div>
      </div>
    </template>
  </MainLayout>
</template>