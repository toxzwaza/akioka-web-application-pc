<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { onMounted, reactive, ref } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";

const msg = ref("");
const props = defineProps({
  notify_groups: Array,
});

onMounted(() => {
});
</script>
<template>
  <MainLayout :title="'Teams通知送信'">
    <template #content>
      <h1 class="text-center text-xl font-bold text-gray-800">Teams通知送信</h1>
      <section class="mt-8">
        <form class="w-2/3 mx-auto max-w-lg">
          <p class="text-red-500 py-4 text-sm font-mono">
            <span class="mr-2 font-bold">メッセージ :</span> {{ msg }}
          </p>
          <hr class="mb-4" />
          <div class="flex flex-wrap -mx-3 mb-6">
            <div class="w-full px-3 mb-2">
              <label
                class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                for="grid-password"
              >
                グループ名
              </label>
              <input
                class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                id="user_name"
                type="text"
                list="message_users"
              />
            </div>
            <div class="pl-4">
              <h2
                class="mb-2 text-lg font-semibold text-gray-900 dark:text-white"
              >
                送信予定ユーザー
              </h2>
              <ul
                class="max-w-md space-y-1 text-gray-500 list-disc list-inside dark:text-gray-400"
              >
              </ul>
            </div>

            <div class="w-full px-3 my-8">
              <label
                class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                for="grid-password"
              >
                メッセージ
              </label>
              <textarea
                class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                name=""
                id=""
                cols="30"
                rows="10"
              ></textarea>
            </div>
          </div>
          <div class="flex justify-center">
            <button
              @click.prevent="sendNotify"
              class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 border border-blue-700 rounded"
            >
              送信
            </button>
          </div>
        </form>
      </section>
    </template>
  </MainLayout>
</template>