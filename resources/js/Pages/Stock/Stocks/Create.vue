<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { onMounted, reactive, ref } from "vue";
import { Link } from "@inertiajs/vue3";
import axios from "axios";
import MainTitle from "@/Components/Title/MainTitle.vue";

const props = defineProps({
  classifications: Array,
  stock_processes: Array,
  order_request: Object,
  stock: Object
});

const form = reactive({
  order_request_id: null,
  dup_stock_id: null, //複製元stock_id
  name: null,
  s_name: null,
  jan_code: null,
  img_path: null,
  url: null,
  purchase_identification_number: null,
  price: null,
  solo_unit: null,
  org_unit: null,
  quantity_per_org: null,
  classification_id: null,
  deli_location: null,
  stock_process_id: 0,
  del_flg: 0,
  tax_included: 0,
  desc_memo: null,
  show_price_on_invoice: 0,
});

const userChangedInvoiceDisplay = ref(false); // ユーザーが手動で変更したかを追跡

const handleClassification = () => {
  if (form.classification_id == 11) {
    form.stock_process_id = 29;
  }
  
  // classification_id: 34（原材料・副資材）の場合、自動で納品書金額非表示を選択
  // ただし、ユーザーが手動で変更している場合は変更しない
  if (form.classification_id == 34 && !userChangedInvoiceDisplay.value) {
    form.show_price_on_invoice = 1;
  }
};

const handleInvoiceDisplayChange = () => {
  // ユーザーが手動で変更したことを記録
  userChangedInvoiceDisplay.value = true;
};

const createStock = () => {
  if (
    !form.name ||
    !form.price ||
    !form.classification_id ||
    !form.stock_process_id
  ) {
    return alert("必須項目が入力されていません。");
  }

  // 在庫追加
  axios
    .post(route("stock.store.stocks"), form)
    .then((res) => {
      console.log(res.data);
      if (res.data.status) {
        if (form.order_request_id) {
          alert("登録が完了しました。発注依頼一覧へ遷移します。");
          window.location.href = route("stock.order_requests");
        } else if (confirm("登録が完了しました。続けて在庫を追加しますか？")) {
          window.location.reload();
        } else {
          window.location.href = route("stock");
        }
      }
    })
    .catch((error) => {
      console.log(error);
    });
};

onMounted(() => {
  console.log(props.order_request);
  if (props.order_request) {
    const order_request = props.order_request;
    form.order_request_id = order_request.id;
    form.name = order_request.name;
    form.s_name = order_request.s_name;
    form.solo_unit = order_request.unit;
  }

  if(props.stock){
    const stock = props.stock
    form.dup_stock_id = stock.id
    form.name = stock.name
    form.s_name = stock.s_name
    form.price = stock.price
    form.img_path = stock.img_path
    form.tax_included = stock.tax_included
    form.solo_unit = stock.solo_unit
    form.org_unit = stock.org_unit
    form.quantity_per_org = stock.quantity_per_org
    form.classification_id = stock.classification_id
    form.deli_location = stock.deli_location
    form.stock_process_id = stock.stock_process_id
    form.purchase_identification_number = stock.purchase_identification_number
    form.desc_memo = stock.desc_memo
    form.show_price_on_invoice = stock.show_price_on_invoice ?? 0
  }
});
</script>

<template>
  <MainLayout :title="'在庫追加'">
    <template #content>
      <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
          <!-- ヘッダーセクション -->
          <div class="mb-8 space-y-4">
            <MainTitle
              :top="'在庫追加'"
              :sub="'在庫を登録を行います。必須項目を入力して、新規登録ボタンを押してください。作成した物品データは在庫一覧より確認できます。'"
            />
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- 左カラム - 画像プレビュー -->
            <div class="lg:col-span-1">
              <div class="bg-white rounded-2xl shadow-xl p-6 sticky top-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                  <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  画像プレビュー
                </h3>
                <div class="aspect-square bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl overflow-hidden border-2 border-gray-200 flex items-center justify-center">
                  <img
                    v-if="form.img_path"
                    :src="form.img_path"
                    alt="商品画像"
                    class="w-full h-full object-contain"
                  />
                  <div v-else class="text-center text-gray-400">
                    <svg class="w-24 h-24 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-sm">画像URLを入力すると<br/>ここにプレビューされます</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- 右カラム - フォーム -->
            <div class="lg:col-span-2">
              <div class="bg-white rounded-2xl shadow-xl p-8">
                <form class="space-y-6">
                  <!-- 基本情報セクション -->
                  <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                      <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      基本情報
                    </h3>
                    
                    <div class="space-y-4">
                      <!-- 品名 -->
                      <div>
                        <label
                          :class="{
                            'block text-sm font-semibold mb-2': true,
                            'text-red-600': !form.name,
                            'text-gray-700': form.name,
                          }"
                          for="name"
                        >
                          <span class="text-red-500">*</span> 品名
                        </label>
                        <input
                          class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none"
                          id="name"
                          type="text"
                          placeholder="品名を入力してください"
                          v-model="form.name"
                        />
                      </div>

                      <!-- 品番 -->
                      <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2" for="s_name">
                          品番
                        </label>
                        <input
                          class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none"
                          id="s_name"
                          type="text"
                          placeholder="品番を入力してください"
                          v-model="form.s_name"
                        />
                      </div>

                      <!-- JANコード -->
                      <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2" for="jan_code">
                          JANコード
                        </label>
                        <input
                          class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none"
                          id="jan_code"
                          type="text"
                          placeholder="JANコードを入力してください"
                          v-model="form.jan_code"
                        />
                      </div>

                      <!-- 適確事業者番号 -->
                      <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                          適確事業者番号
                        </label>
                        <input
                          class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none"
                          type="text"
                          placeholder="適確事業者番号を入力してください"
                          v-model="form.purchase_identification_number"
                        />
                      </div>
                    </div>
                  </div>

                  <!-- 画像・URLセクション -->
                  <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                      <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                      </svg>
                      画像・URL情報
                    </h3>

                    <div class="space-y-4">
                      <!-- 画像URL -->
                      <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                          画像URL
                          <span class="ml-2 text-red-500 text-xs font-normal">※インターネットの画像を使用する場合コチラから設定</span>
                        </label>
                        <input
                          class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none"
                          type="text"
                          placeholder="https://example.com/image.jpg"
                          v-model="form.img_path"
                        />
                      </div>

                      <!-- 購買用URL -->
                      <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                          購買用URL
                        </label>
                        <input
                          class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none"
                          type="text"
                          placeholder="https://example.com/product"
                          v-model="form.url"
                        />
                      </div>
                    </div>
                  </div>

                  <!-- 価格情報セクション -->
                  <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                      <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      価格情報
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <!-- 価格 -->
                      <div>
                        <label
                          :class="{
                            'block text-sm font-semibold mb-2': true,
                            'text-red-600': !form.price,
                            'text-gray-700': form.price,
                          }"
                        >
                          <span class="text-red-500">*</span> 価格
                        </label>
                        <input
                          class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none"
                          type="number"
                          placeholder="0"
                          v-model="form.price"
                        />
                      </div>

                      <!-- 税区分 -->
                      <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                          <span class="text-red-500">*</span> 税区分
                        </label>
                        <select
                          class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none bg-white"
                          v-model="form.tax_included"
                        >
                          <option value="0">税抜き</option>
                          <option value="1">税込み</option>
                        </select>
                      </div>
                    </div>
                  </div>

                  <!-- 単位情報セクション -->
                  <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                      <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                      </svg>
                      単位情報
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                      <!-- 単位1 -->
                      <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                          単位1
                        </label>
                        <input
                          class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none"
                          type="text"
                          placeholder="個"
                          v-model="form.solo_unit"
                        />
                      </div>

                      <!-- 単位2 -->
                      <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                          単位2
                        </label>
                        <input
                          class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none"
                          type="text"
                          placeholder="箱"
                          v-model="form.org_unit"
                        />
                      </div>

                      <!-- 換算値 -->
                      <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                          換算値
                          <span class="ml-2 text-gray-500 text-xs font-normal">※納品時の数量登録</span>
                        </label>
                        <input
                          class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none"
                          type="number"
                          placeholder="0"
                          v-model="form.quantity_per_org"
                        />
                      </div>
                    </div>
                  </div>

                  <!-- 備考・納品書設定セクション -->
                  <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                      <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                      </svg>
                      備考・納品書設定
                    </h3>

                    <div class="space-y-4">
                      <!-- 備考 -->
                      <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                          備考
                        </label>
                        <textarea
                          class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none resize-none"
                          rows="3"
                          placeholder="備考を入力してください"
                          v-model="form.desc_memo"
                        ></textarea>
                      </div>


                    </div>
                  </div>

                  <!-- カテゴリ・配送情報セクション -->
                  <div class="pb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                      <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                      </svg>
                      カテゴリ・配送情報
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                      <!-- 備品カテゴリ -->
                      <div>
                        <label
                          :class="{
                            'block text-sm font-semibold mb-2': true,
                            'text-red-600': !form.classification_id,
                            'text-gray-700': form.classification_id,
                          }"
                        >
                          <span class="text-red-500">*</span> 備品カテゴリ
                        </label>
                        <select
                          class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none bg-white"
                          @change="handleClassification"
                          v-model="form.classification_id"
                        >
                          <option value="0">未選択</option>
                          <option
                            v-for="classification in classifications"
                            :key="classification.id"
                            :value="classification.id"
                          >
                            {{ classification.name }}
                          </option>
                        </select>
                      </div>

                      <!-- 配送先 -->
                      <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                          配送先
                        </label>
                        <input
                          class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none"
                          type="text"
                          placeholder="配送先を入力"
                          v-model="form.deli_location"
                        />
                      </div>

                      <!-- 工程 -->
                      <div>
                        <label
                          :class="{
                            'block text-sm font-semibold mb-2': true,
                            'text-red-600': !form.stock_process_id,
                            'text-gray-700': form.stock_process_id,
                          }"
                        >
                          <span class="text-red-500">*</span> 工程
                          <span class="ml-1 text-xs font-normal text-gray-500">(※発注依頼時工程選択のデフォルト値)</span>
                        </label>
                        <select
                          class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none bg-white"
                          v-model="form.stock_process_id"
                        >
                          <option value="0">未選択</option>
                          <option
                            v-for="stock_process in props.stock_processes"
                            :key="stock_process.id"
                            :value="stock_process.id"
                          >
                            {{ stock_process.name }}
                          </option>
                        </select>
                      </div>
                    </div>
                  </div>

                  <!-- 納品書金額表示セクション -->
                  <div class="pb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                      <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                      </svg>
                      納品書金額表示設定
                    </h3>

                    <div>
                      <label class="block text-sm font-semibold text-gray-700 mb-3">
                        納品書金額表示
                        <span class="ml-2 text-gray-500 text-xs font-normal">
                          ※原材料・副資材の場合、自動で「非表示」が選択されます
                        </span>
                      </label>
                      <div class="flex gap-6">
                        <label class="inline-flex items-center cursor-pointer">
                          <input
                            type="radio"
                            class="w-5 h-5 text-blue-600 focus:ring-blue-500 focus:ring-2"
                            :value="0"
                            v-model="form.show_price_on_invoice"
                            @change="handleInvoiceDisplayChange"
                          />
                          <span class="ml-3 text-sm font-medium text-gray-700">表示</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                          <input
                            type="radio"
                            class="w-5 h-5 text-blue-600 focus:ring-blue-500 focus:ring-2"
                            :value="1"
                            v-model="form.show_price_on_invoice"
                            @change="handleInvoiceDisplayChange"
                          />
                          <span class="ml-3 text-sm font-medium text-gray-700">非表示</span>
                        </label>
                      </div>
                    </div>
                  </div>

                  <!-- アクションボタン -->
                  <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <button
                      @click.prevent="createStock"
                      class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold py-4 px-8 rounded-xl shadow-lg shadow-blue-500/30 transition-all duration-300 hover:scale-105 hover:shadow-xl"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                      </svg>
                      新規登録
                    </button>

                    <span class="text-sm text-red-500 font-semibold">
                      <span class="text-red-500">*</span> は必須項目です
                    </span>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </MainLayout>
</template>

<style scoped lang="scss">
// カスタムスタイル
</style>
