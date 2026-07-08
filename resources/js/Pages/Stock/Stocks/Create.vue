<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { onMounted, reactive, ref } from "vue";
import { Link } from "@inertiajs/vue3";
import axios from "axios";
import PageHeader from "@/Components/UI/PageHeader.vue";
import SectionCard from "@/Components/UI/SectionCard.vue";
import Card from "@/Components/UI/Card.vue";
import FormField from "@/Components/UI/FormField.vue";
import Button from "@/Components/UI/Button.vue";
import Icon from "@/Components/UI/Icon.vue";

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
      <PageHeader
        title="在庫追加"
        subtitle="在庫を登録を行います。必須項目を入力して、新規登録ボタンを押してください。作成した物品データは在庫一覧より確認できます。"
      />

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- 左カラム - 画像プレビュー -->
        <div class="lg:col-span-1">
          <Card class="sticky top-6">
            <h3 class="text-base font-bold text-content mb-4 flex items-center gap-2">
              <Icon name="image" class="text-primary-600" />
              画像プレビュー
            </h3>
            <div class="aspect-square bg-surface-sunken rounded-card overflow-hidden border border-border flex items-center justify-center">
              <img
                v-if="form.img_path"
                :src="form.img_path"
                alt="商品画像"
                class="w-full h-full object-contain"
              />
              <div v-else class="text-center text-content-subtle px-4">
                <Icon name="image" size="xl" class="mb-2" />
                <p class="text-sm">画像URLを入力すると<br />ここにプレビューされます</p>
              </div>
            </div>
          </Card>
        </div>

        <!-- 右カラム - フォーム -->
        <div class="lg:col-span-2">
          <SectionCard title="在庫情報">
            <form class="space-y-6">
              <!-- 基本情報セクション -->
              <div class="border-b border-border pb-6">
                <h3 class="text-base font-bold text-content mb-4 flex items-center gap-2">
                  <Icon name="info" class="text-primary-600" />
                  基本情報
                </h3>

                <div class="space-y-4">
                  <!-- 品名 -->
                  <FormField label="品名" id="name" required :error="!form.name ? '品名は必須です' : ''">
                    <input
                      class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                      id="name"
                      type="text"
                      placeholder="品名を入力してください"
                      v-model="form.name"
                    />
                  </FormField>

                  <!-- 品番 -->
                  <FormField label="品番" id="s_name">
                    <input
                      class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                      id="s_name"
                      type="text"
                      placeholder="品番を入力してください"
                      v-model="form.s_name"
                    />
                  </FormField>

                  <!-- JANコード -->
                  <FormField label="JANコード" id="jan_code">
                    <input
                      class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                      id="jan_code"
                      type="text"
                      placeholder="JANコードを入力してください"
                      v-model="form.jan_code"
                    />
                  </FormField>

                  <!-- 適確事業者番号 -->
                  <FormField label="適確事業者番号">
                    <input
                      class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                      type="text"
                      placeholder="適確事業者番号を入力してください"
                      v-model="form.purchase_identification_number"
                    />
                  </FormField>
                </div>
              </div>

              <!-- 画像・URLセクション -->
              <div class="border-b border-border pb-6">
                <h3 class="text-base font-bold text-content mb-4 flex items-center gap-2">
                  <Icon name="link" class="text-primary-600" />
                  画像・URL情報
                </h3>

                <div class="space-y-4">
                  <!-- 画像URL -->
                  <FormField>
                    <template #default>
                      <label class="block mb-1 text-sm font-medium text-content">
                        画像URL
                        <span class="ml-2 text-content-muted text-xs font-normal">※インターネットの画像を使用する場合コチラから設定</span>
                      </label>
                      <input
                        class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                        type="text"
                        placeholder="https://example.com/image.jpg"
                        v-model="form.img_path"
                      />
                    </template>
                  </FormField>

                  <!-- 購買用URL -->
                  <FormField label="購買用URL">
                    <input
                      class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                      type="text"
                      placeholder="https://example.com/product"
                      v-model="form.url"
                    />
                  </FormField>
                </div>
              </div>

              <!-- 価格情報セクション -->
              <div class="border-b border-border pb-6">
                <h3 class="text-base font-bold text-content mb-4 flex items-center gap-2">
                  <Icon name="payments" class="text-primary-600" />
                  価格情報
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <!-- 価格 -->
                  <FormField label="価格" required :error="!form.price ? '価格は必須です' : ''">
                    <input
                      class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                      type="number"
                      placeholder="0"
                      v-model="form.price"
                    />
                  </FormField>

                  <!-- 税区分 -->
                  <FormField label="税区分" required>
                    <select
                      class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                      v-model="form.tax_included"
                    >
                      <option value="0">税抜き</option>
                      <option value="1">税込み</option>
                    </select>
                  </FormField>
                </div>
              </div>

              <!-- 単位情報セクション -->
              <div class="border-b border-border pb-6">
                <h3 class="text-base font-bold text-content mb-4 flex items-center gap-2">
                  <Icon name="straighten" class="text-primary-600" />
                  単位情報
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <!-- 発注単位 -->
                  <FormField label="発注単位">
                    <input
                      class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                      type="text"
                      placeholder="個"
                      v-model="form.solo_unit"
                    />
                  </FormField>

                  <!-- 在庫単位 -->
                  <FormField label="在庫単位">
                    <input
                      class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                      type="text"
                      placeholder="箱"
                      v-model="form.org_unit"
                    />
                  </FormField>

                  <!-- 換算値 -->
                  <FormField>
                    <template #default>
                      <label class="block mb-1 text-sm font-medium text-content">
                        換算値
                        <span class="ml-2 text-content-muted text-xs font-normal">※納品時の数量登録</span>
                      </label>
                      <input
                        class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                        type="number"
                        placeholder="0"
                        v-model="form.quantity_per_org"
                      />
                    </template>
                  </FormField>
                </div>
              </div>

              <!-- 備考・納品書設定セクション -->
              <div class="border-b border-border pb-6">
                <h3 class="text-base font-bold text-content mb-4 flex items-center gap-2">
                  <Icon name="description" class="text-primary-600" />
                  備考・納品書設定
                </h3>

                <div class="space-y-4">
                  <!-- 備考 -->
                  <FormField label="備考">
                    <textarea
                      class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500 resize-none"
                      rows="3"
                      placeholder="備考を入力してください"
                      v-model="form.desc_memo"
                    ></textarea>
                  </FormField>
                </div>
              </div>

              <!-- カテゴリ・配送情報セクション -->
              <div class="pb-6">
                <h3 class="text-base font-bold text-content mb-4 flex items-center gap-2">
                  <Icon name="sell" class="text-primary-600" />
                  カテゴリ・配送情報
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <!-- 備品カテゴリ -->
                  <FormField label="備品カテゴリ" required :error="!form.classification_id ? '備品カテゴリは必須です' : ''">
                    <select
                      class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
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
                  </FormField>

                  <!-- 配送先 -->
                  <FormField label="配送先">
                    <input
                      class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                      type="text"
                      placeholder="配送先を入力"
                      v-model="form.deli_location"
                    />
                  </FormField>

                  <!-- 工程 -->
                  <FormField required :error="!form.stock_process_id ? '工程は必須です' : ''">
                    <template #default>
                      <label class="block mb-1 text-sm font-medium text-content">
                        工程<span class="text-error-600 ml-0.5">*</span>
                        <span class="ml-1 text-xs font-normal text-content-muted">(※発注依頼時工程選択のデフォルト値)</span>
                      </label>
                      <select
                        class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
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
                    </template>
                  </FormField>
                </div>
              </div>

              <!-- 納品書金額表示セクション -->
              <div class="pb-6">
                <h3 class="text-base font-bold text-content mb-4 flex items-center gap-2">
                  <Icon name="receipt_long" class="text-primary-600" />
                  納品書金額表示設定
                </h3>

                <div>
                  <label class="block text-sm font-medium text-content mb-3">
                    納品書金額表示
                    <span class="ml-2 text-content-muted text-xs font-normal">
                      ※原材料・副資材の場合、自動で「非表示」が選択されます
                    </span>
                  </label>
                  <div class="flex gap-6">
                    <label class="inline-flex items-center cursor-pointer">
                      <input
                        type="radio"
                        class="w-5 h-5 text-primary-600 focus:ring-primary-500 focus:ring-2"
                        :value="0"
                        v-model="form.show_price_on_invoice"
                        @change="handleInvoiceDisplayChange"
                      />
                      <span class="ml-3 text-sm font-medium text-content">表示</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                      <input
                        type="radio"
                        class="w-5 h-5 text-primary-600 focus:ring-primary-500 focus:ring-2"
                        :value="1"
                        v-model="form.show_price_on_invoice"
                        @change="handleInvoiceDisplayChange"
                      />
                      <span class="ml-3 text-sm font-medium text-content">非表示</span>
                    </label>
                  </div>
                </div>
              </div>
            </form>

            <template #footer>
              <div class="flex items-center justify-between">
                <span class="text-xs text-content-muted">* は必須項目です</span>
                <Button
                  variant="primary"
                  icon-left="add"
                  @click.prevent="createStock"
                >
                  新規登録
                </Button>
              </div>
            </template>
          </SectionCard>
        </div>
      </div>
    </template>
  </MainLayout>
</template>

<style scoped lang="scss">
// カスタムスタイル
</style>
