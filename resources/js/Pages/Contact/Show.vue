<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { ref, onMounted } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";
import Title from "@/Components/Title/MainTitle.vue";

const props = defineProps({
  contact: Object,
  users: Array,
  searchParams: Object
});

const formatText = (text) => {
  if (!text) return "";
  // URLをリンクに変換
  const urlRegex = /(https?:\/\/[^\s]+)/g;
  const textWithLinks = text.replace(
    urlRegex,
    '<a href="$1" target="_blank" class="text-blue-600 hover:underline">$1</a>'
  );
  // 改行を<br>に変換
  return textWithLinks.replace(/\n/g, "<br>");
};

const copyToClipboard = async (text) => {
  try {
    await navigator.clipboard.writeText(text);
    alert("クリップボードにコピーしました");
  } catch (err) {
    console.error("クリップボードへのコピーに失敗しました:", err);
    alert("クリップボードへのコピーに失敗しました");
  }
};

const changeValue = (flg, val) => {
  if (flg) {
    console.log(flg, val);

    axios
      .post(route("contact.update"), {
        contact_id: props.contact.id,
        flg: flg,
        val,
        val,
      })
      .then((res) => {
        console.log(res.data);
        if (res.data.status) {
          alert("更新が完了しました。");
          // window.location.reload ();
        }
      })
      .catch((error) => {
        console.log(error);
      });
  }
};

// 一覧画面に戻るリンクを生成
const getBackLink = () => {
  return route('contact.home', props.searchParams);
};

onMounted(() => {
  console.log(props.contact);
});
</script>
<template>
  <MainLayout>
    <template #content>
      <Title
        :top="'お問い合わせ詳細'"
        :sub="'HPからのお問い合わせの詳細情報を確認することができます。'"
      />

      <div class="mb-4">
        <Link :href="getBackLink()" class="text-blue-500 hover:underline font-bold">
          <i class="fas fa-arrow-left mr-2"></i> お問い合わせ一覧へ戻る
        </Link>
      </div>

      <section id="contact_container" class="">
        <div class="mb-6">
          <p class="mb-1">
            <span class="font-bold">状況：</span>
            <span
              v-if="!contact.progress"
              class="bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-900 dark:text-gray-300"
              >未読</span
            >
            <span
              v-else-if="contact.progress === 1"
              class="bg-orange-100 text-orange-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-orange-900 dark:text-orange-300"
              >進行中</span
            >
            <span
              v-else-if="contact.progress === 2"
              class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-green-900 dark:text-green-300"
              >完了</span
            >
          </p>
          <p class="mb-1">
            <span class="font-bold">問い合わせ種類：</span>
            <span
              :class="{
                'bg-indigo-100 text-indigo-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-indigo-900 dark:text-indigo-300': true,
                'opacity-40': props.contact.kind !== 0,
                'opacity-100': props.contact.kind === 0,
              }"
              >製品</span
            >
            <span
              :class="{
                'bg-purple-100 text-purple-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-purple-900 dark:text-purple-300': true,
                'opacity-40': props.contact.kind !== 1,
                'opacity-100': props.contact.kind === 1,
              }"
              >新規案件</span
            >
            <span
              :class="{
                'bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-blue-900 dark:text-blue-300': true,
                'opacity-40': props.contact.kind !== 2,
                'opacity-100': props.contact.kind === 2,
              }"
              >営業・広告</span
            >
            <span
              :class="{
                'bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-900 dark:text-gray-300': true,
                'opacity-40': props.contact.kind !== 3,
                'opacity-100': props.contact.kind === 3,
              }"
              >その他</span
            >
          </p>
          <p class="mb-1">
            <i class="mr-2 fas fa-calendar-alt"></i>
            {{
              new Date(contact.created_at)
                .toLocaleString("ja-JP", {
                  year: "numeric",
                  month: "2-digit",
                  day: "2-digit",
                  hour: "2-digit",
                  minute: "2-digit",
                })
                .replace(/\//g, "/")
            }}
          </p>
          <p class="mb-1">
            <i class="mr-2 fas fa-user"></i>
            {{ props.contact.name + " - " + props.contact.furi_name }}
          </p>
          <p class="mb-1">
            <i class="mr-2 fas fa-envelope"></i>
            <span
              @click="copyToClipboard(props.contact.email)"
              class="text-blue-500 underline"
            >
              {{ props.contact.email }}
            </span>
          </p>
          <p class="mb-1">
            <i class="mr-2 fas fa-phone"></i>
            <span
              class="text-blue-500 underline"
              @click="copyToClipboard(props.contact.tel)"
              >{{ props.contact.tel }}</span
            >
          </p>
        </div>
        <p
          class="font-bold text-lg mb-2 p-2 pl-4 bg-gray-700 text-white rounded-sm"
        >
          {{ props.contact.subject }}
        </p>

        <div class="">
          <div class="w-1/2 pl-4">
            <p class="font-bold mb-1 bg-gray-50 p-2">
              <i class="mr-2 fas fa-robot"></i>AI要約生成
            </p>
            <p class="pl-4" v-html="formatText(props.contact.summary)"></p>
          </div>

          <hr class="my-4" />

          <div class="w-1/2 pr-4">
            <p class="font-bold mb-1 bg-gray-50 p-2">
              <i class="mr-2 fas fa-envelope"></i>メール原文
            </p>
            <p class="pl-4" v-html="formatText(props.contact.content)"></p>
          </div>
        </div>

        <div>
          <hr class="my-4" />
          <div class="flex items-center justify-start">
            <div class="w-1/3 mb-4 mr-8">
              <label
                class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                for="grid-last-name"
              >
                進行状況
              </label>
              <select
                name=""
                id=""
                class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                @change="changeValue('progress', $event.target.value)"
              >
                <option :selected="props.contact.progress === 0" :value="0">
                  未読
                </option>
                <option :selected="props.contact.progress === 1" :value="1">
                  進行中
                </option>
                <option :selected="props.contact.progress === 2" :value="2">
                  完了
                </option>
              </select>
            </div>

            <div class="w-1/3 mb-4">
              <label
                class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                for="grid-last-name"
              >
                担当者
              </label>
              <select
                name=""
                id=""
                class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                @change="changeValue('user_id', $event.target.value)"
              >
                <option
                  v-for="user in props.users"
                  :key="user.id"
                  :value="user.id"
                  :selected="props.contact.user_id === user.id"
                >
                  {{ user.name }}
                </option>
              </select>
            </div>
          </div>

          <label
            for="message"
            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
            >コメント・メモ</label
          >
          <textarea
            id="message"
            rows="4"
            class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
            @change="changeValue('memo', $event.target.value)"
            >{{ contact.memo }}</textarea
          >
        </div>
      </section>
    </template>
  </MainLayout>
</template>
<style scoped lang="scss">
</style>