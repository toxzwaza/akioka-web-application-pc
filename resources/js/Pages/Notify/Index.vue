<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { ref, reactive, onMounted, computed } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";
import Title from "@/Components/Title/MainTitle.vue";
import { Pie, Bar } from "vue-chartjs";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
  notify_queues: Array
})

onMounted(() => {
  console.log(props.notify_queues)
})
</script>
<template>
  <MainLayout :p_none="true">
    <template #content>
      <section class="py-16 px-24">
        <Title :top="'通知一覧'" :sub="'通知一覧を確認できます。'" />

        <section class="text-gray-600 body-font">
          <div class="container py-24 mx-auto">
            <div class="w-full mx-auto overflow-auto">
              <table class="table-auto w-full text-left whitespace-no-wrap">
                <thead>
                  <tr>
                    <th
                      class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl"
                    >
                      ID
                    </th>
                    <th
                      class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                    >
                      タイトル
                    </th>
                    <th
                      class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                    >
                      メッセージ
                    </th>
                    <th
                      class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                    >
                      状況
                    </th>
                    <th
                      class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                    >
                      通知者
                    </th>
                    <th
                      class="w-10 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tr rounded-br"
                    ></th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="px-4 py-3">Start</td>
                    <td class="px-4 py-3">5 Mb/s</td>
                    <td class="px-4 py-3">15 GB</td>
                    <td class="px-4 py-3 text-lg text-gray-900">Free</td>
                    <td class="px-4 py-3 text-lg text-gray-900">Free</td>
                    <td class="w-10 text-center">
                      <input name="plan" type="radio" />
                    </td>
                  </tr>

                </tbody>
              </table>
            </div>
          </div>
        </section>
      </section>
    </template>
  </MainLayout>
</template>
<style scoped lang="scss">
</style>