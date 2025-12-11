<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import MainTitle from "@/Components/Title/MainTitle.vue";
import Pagination from "@/Components/Pagination.vue";
import { onMounted, reactive } from "vue";
import { router, Link } from "@inertiajs/vue3";

const props = defineProps({
    suppliers: Object,
    name: String,
    rub_name: String,
    tel: String,
    fax: String,
    p_code: String,
    address: String,
})

const form = reactive({
    name: null,
    rub_name: null,
    tel: null,
    fax: null,
    p_code: null,
    address: null,
})

const searchSuppliers = () => {
    router.get(route("stock.suppliers"), form);
}

const resetFilters = () => {
    form.name = null;
    form.rub_name = null;
    form.tel = null;
    form.fax = null;
    form.p_code = null;
    form.address = null;
    router.get(route("stock.suppliers"));
}

onMounted(() => {
    console.log(props.suppliers);
    form.name = props.name;
    form.rub_name = props.rub_name;
    form.tel = props.tel;
    form.fax = props.fax;
    form.p_code = props.p_code;
    form.address = props.address;
})

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return dateString;
}

const getStatusText = (delFlg) => {
    return delFlg === 1 ? '無効' : '有効';
}

</script>
<template>
  <MainLayout :title="'取引先一覧'">
    <template #content>
      <MainTitle
        :top="'取引先'"
        :sub="'取引先一覧の確認と登録を行います。必須項目を入力して、追加ボタンを押してください。'"
      />
      <div class="mt-4 mb-4 flex justify-end">
        <Link
          :href="route('stock.suppliers.create')"
          class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
        >
          取引先追加
        </Link>
      </div>
      <div class="mt-4 mb-4 p-4 bg-gray-50 rounded-lg">
        <p class="mb-3 font-bold text-gray-700">検索・絞り込み</p>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              取引先名
            </label>
            <input
              type="text"
              v-model="form.name"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="取引先名で検索"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              ふり
            </label>
            <input
              type="text"
              v-model="form.rub_name"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="ふりで検索"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              電話番号
            </label>
            <input
              type="text"
              v-model="form.tel"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="電話番号で検索"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              FAX番号
            </label>
            <input
              type="text"
              v-model="form.fax"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="FAX番号で検索"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              郵便番号
            </label>
            <input
              type="text"
              v-model="form.p_code"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="郵便番号で検索"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              住所
            </label>
            <input
              type="text"
              v-model="form.address"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="住所で検索"
            />
          </div>
        </div>
        <div class="mt-4 flex gap-2">
          <button
            @click="searchSuppliers"
            class="px-4 py-2 bg-blue-500 hover:bg-blue-700 text-white font-bold rounded"
          >
            検索
          </button>
          <button
            @click="resetFilters"
            class="px-4 py-2 bg-gray-500 hover:bg-gray-700 text-white font-bold rounded"
          >
            リセット
          </button>
        </div>
      </div>
      <div class="mt-4 overflow-x-auto">
        <table class="table-auto w-full text-left whitespace-no-wrap">
          <thead>
            <tr>
              <th class="whitespace-nowrap px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">
                ID
              </th>
              <th class="whitespace-nowrap px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                取引先No
              </th>
              <th class="whitespace-nowrap px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                取引先名
              </th>
              <th class="whitespace-nowrap px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                ふり
              </th>
              <th class="whitespace-nowrap px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                電話番号
              </th>
              <th class="whitespace-nowrap px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                FAX番号
              </th>
              <th class="whitespace-nowrap px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                郵便番号
              </th>
              <th class="whitespace-nowrap px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                住所
              </th>
              <th class="whitespace-nowrap px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                メモ
              </th>
              <th class="whitespace-nowrap px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                有効/無効
              </th>
              <th class="whitespace-nowrap px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                作成日時
              </th>
              <th class="whitespace-nowrap px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                更新日時
              </th>
              <th class="whitespace-nowrap px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                インボイス登録番号
              </th>
              <th class="whitespace-nowrap px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tr rounded-br">
                操作
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="supplier in suppliers.data"
              :key="supplier.id"
            >
              <td class="whitespace-nowrap border-t-2 border-gray-200 px-4 py-4">
                {{ supplier.id }}
              </td>
              <td class="whitespace-nowrap border-t-2 border-gray-200 px-4 py-4">
                {{ supplier.supplier_no ?? '-' }}
              </td>
              <td class="whitespace-nowrap border-t-2 border-gray-200 px-4 py-4">
                {{ supplier.name ?? '-' }}
              </td>
              <td class="whitespace-nowrap border-t-2 border-gray-200 px-4 py-4">
                {{ supplier.rub_name ?? '-' }}
              </td>
              <td class="whitespace-nowrap border-t-2 border-gray-200 px-4 py-4">
                {{ supplier.tel ?? '-' }}
              </td>
              <td class="whitespace-nowrap border-t-2 border-gray-200 px-4 py-4">
                {{ supplier.fax ?? '-' }}
              </td>
              <td class="whitespace-nowrap border-t-2 border-gray-200 px-4 py-4">
                {{ supplier.p_code ?? '-' }}
              </td>
              <td class="whitespace-nowrap border-t-2 border-gray-200 px-4 py-4">
                {{ supplier.address ?? '-' }}
              </td>
              <td class="whitespace-nowrap border-t-2 border-gray-200 px-4 py-4">
                {{ supplier.memo ?? '-' }}
              </td>
              <td class="whitespace-nowrap border-t-2 border-gray-200 px-4 py-4">
                {{ getStatusText(supplier.del_flg) }}
              </td>
              <td class="whitespace-nowrap border-t-2 border-gray-200 px-4 py-4">
                {{ formatDate(supplier.created_at) }}
              </td>
              <td class="whitespace-nowrap border-t-2 border-gray-200 px-4 py-4">
                {{ formatDate(supplier.updated_at) }}
              </td>
              <td class="whitespace-nowrap border-t-2 border-gray-200 px-4 py-4">
                {{ supplier.invoice_registration_number ?? '-' }}
              </td>
              <td class="whitespace-nowrap border-t-2 border-gray-200 px-4 py-4">
                <Link
                  :href="route('stock.suppliers.edit', supplier.id)"
                  class="bg-yellow-500 hover:bg-yellow-700 text-white text-xs font-bold py-1 px-3 rounded"
                >
                  編集
                </Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="mt-4 flex justify-end">
        <Pagination :links="suppliers.links" />
      </div>
    </template>
  </MainLayout>
</template>