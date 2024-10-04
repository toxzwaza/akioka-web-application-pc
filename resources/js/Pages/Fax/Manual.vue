<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { onMounted, reactive, ref } from "vue";
import { router } from "@inertiajs/vue3";
</script>
<template>
  <MainLayout :title="'FAX振り分け'">
    <template #content>
      <div class="px-8 my-8">
        <h1 class="text-gray-600 text-2xl font-bold mb-4 text-center">
          マニュアル
        </h1>
        <div class="bg-white  rounded px-8 pt-6 pb-8 mb-4">
          <h2 class="text-gray-700 text-xl font-bold mb-2">セクション1</h2>
          <p class="text-gray-700 text-base mb-4">
            ここにセクション1の内容が入ります。詳細な説明や手順を記載してください。
          </p>
          <h2 class="text-gray-700 text-xl font-bold mb-2">セクション2</h2>
          <p class="text-gray-700 text-base mb-4">
            ここにセクション2の内容が入ります。詳細な説明や手順を記載してください。
          </p>
          <h2 class="text-gray-700 text-xl font-bold mb-2">セクション3</h2>
          <p class="text-gray-700 text-base mb-4">
            ここにセクション3の内容が入ります。詳細な説明や手順を記載してください。
          </p>
        </div>
      </div>
    </template>
  </MainLayout>
</template>