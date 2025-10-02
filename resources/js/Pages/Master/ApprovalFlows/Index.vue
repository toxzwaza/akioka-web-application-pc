<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { ref, onMounted } from "vue";
import { router, Link } from "@inertiajs/vue3";
import Title from "@/Components/Title/MainTitle.vue";

const props = defineProps({
  approvalFlows: Array
});

const deleteApprovalFlow = (id) => {
  if (confirm('この承認フローを削除しますか？')) {
    router.delete(route('master.approval-flows.destroy', id));
  }
};
</script>

<template>
  <MainLayout :p_none="true">
    <template #content>
      <section class="py-16 px-24">
        <Title :top="'承認フロー管理'" :sub="'承認フローの作成・管理ができます。'" />

        <div class="flex justify-end mb-6 space-x-4">
          <Link
            :href="route('master.approval-flows.test')"
            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
          >
            テスト機能
          </Link>
          <Link
            :href="route('master.approval-flows.bulk-test')"
            class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded"
          >
            一括テスト
          </Link>
          <Link
            :href="route('master.approval-flows.create')"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
          >
            新規作成
          </Link>
        </div>

        <section class="text-gray-600 body-font">
          <div class="container py-24 mx-auto">
            <div class="w-full mx-auto overflow-auto">
              <table class="table-auto w-full text-left whitespace-no-wrap">
                <thead>
                  <tr>
                    <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">
                      ID
                    </th>
                    <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                      名前
                    </th>
                    <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                      説明
                    </th>
                    <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                      ステータス
                    </th>
                    <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                      作成日
                    </th>
                    <th class="w-10 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tr rounded-br">
                      操作
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="flow in props.approvalFlows" :key="flow.id">
                    <td class="px-4 py-3">{{ flow.id }}</td>
                    <td class="px-4 py-3">{{ flow.name }}</td>
                    <td class="px-4 py-3">{{ flow.description }}</td>
                    <td class="px-4 py-3">
                      <span 
                        :class="{
                          'bg-green-100 text-green-800': flow.is_active,
                          'bg-gray-100 text-gray-800': !flow.is_active
                        }"
                        class="text-xs font-medium px-2.5 py-0.5 rounded-sm"
                      >
                        {{ flow.is_active ? '有効' : '無効' }}
                      </span>
                    </td>
                    <td class="px-4 py-3">{{ flow.steps.length }}ステップ</td>
                    <td class="px-4 py-3">{{ flow.created_at }}</td>
                    <td class="px-4 py-3">
                      <div class="flex space-x-2">
                        <Link
                          :href="route('master.approval-flows.show', flow.id)"
                          class="bg-blue-500 hover:bg-blue-700 text-white text-xs font-bold py-1 px-2 rounded"
                        >
                          詳細
                        </Link>
                        <Link
                          :href="route('master.approval-flows.edit', flow.id)"
                          class="bg-yellow-500 hover:bg-yellow-700 text-white text-xs font-bold py-1 px-2 rounded"
                        >
                          編集
                        </Link>
                        <button
                          @click="deleteApprovalFlow(flow.id)"
                          class="bg-red-500 hover:bg-red-700 text-white text-xs font-bold py-1 px-2 rounded"
                        >
                          削除
                        </button>
                      </div>
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
