<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { onMounted, reactive, ref } from "vue";
import axios from "axios";

const props = defineProps({
    stocks: Array
})


onMounted(() => {
    console.log(props.stocks)
})
</script>
<template>
  <MainLayout :title="'発注一覧'">
    <template #content>
      <h1 class="text-center text-xl font-bold text-gray-800">滞留品一覧</h1>

      <section class="text-gray-600 body-font">
        <div class="container px-5 py-12 mx-auto">
          <div class="flex flex-col text-center w-full mb-20">
            <p class="lg:w-2/3 mx-auto leading-relaxed text-base">
              滞留品及び半滞留品が表示されます。
            </p>
          </div>


          <div class="w-full mx-auto overflow-auto">
            <table class="table-auto w-full text-left whitespace-no-wrap">
              <thead>
                <tr>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl"
                  >
                    画像
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl"
                  >
                    品名
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    品番
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    最終出庫日
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    滞留ステータス
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    滞留カード設置
                  </th>
                  <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"></th>
                 
                </tr>
              </thead>
              <tbody>
                <tr v-for="stock in props.stock" :key="stock.stock_id">
                  <td class="px-4 py-3">
                    {{ stock.img_path.includes('storage') ? `https://akioka.cloud/${stock.img_path}` : stock.img_path }}
                  </td>
                  <td class="px-4 py-3">
                    {{ stock.name }}
                  </td>
                  <td class="px-4 py-3">
                    {{ stock.s_name }}
                  </td>
                  <td class="px-4 py-3">
                    {{ stock.created_at}}
                  </td>
                  <td class="px-4 py-3">
                    滞留or半滞留
                  </td>
                  <td class="px-4 py-3">
                    済or未
                  </td>
                  <td class="px-4 py-3">
                    <button>設置済</button>
                  </td>


                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </template>
  </MainLayout>
</template>
<style scoped lang="scss">

</style>
