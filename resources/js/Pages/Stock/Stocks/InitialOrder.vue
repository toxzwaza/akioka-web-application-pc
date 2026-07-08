<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { onMounted, reactive, ref } from "vue";
import axios from "axios";
import PageHeader from "@/Components/UI/PageHeader.vue";
import SectionCard from "@/Components/UI/SectionCard.vue";
import FormField from "@/Components/UI/FormField.vue";
import Button from "@/Components/UI/Button.vue";
import Icon from "@/Components/UI/Icon.vue";

const props = defineProps({
  classifications: Array,
  users: Array,
  admin_users: Array,
  suppliers: Array,
  stock_processes: Array,
});

const form = reactive({
  name: null,
  s_name: null,
  jan_code: null,
  img_path: null,
  url: null,
  purchase_identification_number: null,
  price: null,
  tax_included: 0,
  solo_unit: null,
  org_unit: null,
  quantity_per_org: null,
  classification_id: null,
  deli_location: null,
  postage: null,
  base_stock_process_id: 0, //マスタに設定する工程ID

  order_user: null,
  user_id: null,
  supplier_id: null,
  lead_time: null,
  quantity: null,
  unit: null,
  order_price: null,
  calc_price: null,
  order_stock_process_id: 0, //実際の工程ID

  upload_file: null,
});

const uploadFile = (event) => {
  const file = event.target.files[0];
  form.upload_file = file;

  console.log(form.upload_file);
};

const createStockAndInitialOrder = () => {
  if (
    !form.name ||
    !form.price ||
    !form.classification_id ||
    !form.deli_location ||
    !form.solo_unit ||
    !form.order_user ||
    !form.user_id ||
    !form.lead_time ||
    !form.quantity ||
    !form.calc_price ||
    !form.order_price ||
    !form.unit 
  ) {
    return alert("必須項目が入力されていません。");
  }

  // 在庫追加と発注登録
  const formData = new FormData();
  // formオブジェクトをFormDataに変換
  Object.keys(form).forEach((key) => {
    // nullでない値のみを追加
    if (form[key] !== null) {
      // ファイルの場合は特別な処理
      if (key === "upload_file" && form[key] instanceof File) {
        formData.append(key, form[key]);
      }
      // それ以外の通常の値
      else if (form[key] !== null) {
        formData.append(key, form[key]);
      }
    }
  });

  axios
    .post(route("stock.store.initialOrders"), formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    })
    .then((res) => {
      console.log(res.data);
      if (res.data.status) {
        if (confirm("発注登録が完了しました。続けて発注登録を行いますか？")) {
          window.location.reload();
        } else {
          // window.location.href = route("stock");
        }
      }
    })
    .catch((error) => {
      console.log(error);
    });
};

onMounted(() => {});
</script>
<template>
  <MainLayout :title="'新規品発注依頼登録'">
    <template #content>
      <PageHeader
        title="新規品発注依頼"
        subtitle="在庫を登録・手配先登録・発注依頼登録を同時に行います。既存品で発注したい場合は、在庫追加より在庫データを登録した後、在庫一覧より発注依頼を行ってください。"
      />

      <div
        class="mb-6 flex items-center gap-2 rounded-md border border-warning-500 bg-warning-50 px-4 py-3 text-sm font-medium text-warning-700"
      >
        <Icon name="warning" size="sm" />
        <span>2025年7月1日より使わなくなる予定です。</span>
      </div>

      <form class="mx-auto grid max-w-3xl gap-6">
        <!-- 在庫登録 -->
        <SectionCard title="在庫登録">
          <div class="grid gap-4 sm:grid-cols-2">
            <FormField label="品名" id="name" required>
              <input
                class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                id="name"
                type="text"
                v-model="form.name"
              />
            </FormField>

            <FormField label="品番" id="s_name">
              <input
                class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                id="s_name"
                type="text"
                v-model="form.s_name"
              />
            </FormField>

            <FormField label="単価 (基本発注単価)" id="price" required>
              <input
                class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                id="price"
                type="number"
                v-model="form.price"
                @change="form.order_price = form.price"
              />
            </FormField>

            <FormField label="備品カテゴリ" id="classification_id" required>
              <select
                class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
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

            <FormField label="配送先" id="deli_location" required>
              <input
                class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                id="deli_location"
                type="text"
                v-model="form.deli_location"
              />
            </FormField>

            <FormField
              label="工程 (※発注依頼時工程選択のデフォルト値)"
              id="base_stock_process_id"
              required
            >
              <select
                class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                v-model="form.base_stock_process_id"
                @change="form.order_stock_process_id = $event.target.value"
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
            </FormField>
          </div>

          <div class="mt-4 grid gap-4 sm:grid-cols-3">
            <FormField label="発注単位" id="solo_unit" required>
              <input
                class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                id="solo_unit"
                type="text"
                placeholder="個"
                v-model="form.solo_unit"
              />
            </FormField>

            <FormField label="在庫単位" id="org_unit">
              <input
                class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                id="org_unit"
                type="text"
                placeholder="箱"
                v-model="form.org_unit"
              />
            </FormField>

            <FormField label="換算値（※納品時の数量登録）" id="quantity_per_org">
              <input
                class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                id="quantity_per_org"
                type="number"
                v-model="form.quantity_per_org"
              />
            </FormField>
          </div>

          <!-- 詳細を登録する場合 -->
          <details class="mt-6">
            <summary
              class="cursor-pointer rounded-md bg-surface-sunken px-4 py-2 text-sm font-bold text-content"
            >
              詳細登録(任意)
            </summary>
            <div
              class="mt-2 grid gap-4 rounded-md bg-surface-muted p-4 sm:grid-cols-2"
            >
              <div class="sm:col-span-2">
                <FormField label="JANコード" id="jan_code">
                  <input
                    class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                    id="jan_code"
                    type="text"
                    v-model="form.jan_code"
                  />
                </FormField>
              </div>

              <FormField
                label="画像URL（※インターネットの画像を使用する場合コチラから設定）"
                id="img_path"
              >
                <input
                  class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                  id="img_path"
                  type="text"
                  placeholder="https://****"
                  v-model="form.img_path"
                />
              </FormField>

              <FormField label="購買用URL" id="url">
                <input
                  class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                  id="url"
                  type="text"
                  placeholder="https://****"
                  v-model="form.url"
                />
              </FormField>

              <div class="sm:col-span-2">
                <FormField
                  label="適確事業者番号"
                  id="purchase_identification_number"
                >
                  <input
                    class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                    id="purchase_identification_number"
                    type="text"
                    v-model="form.purchase_identification_number"
                  />
                </FormField>
              </div>
            </div>
          </details>
        </SectionCard>

        <!-- 発注依頼登録 -->
        <SectionCard title="発注依頼登録">
          <div class="grid gap-4 sm:grid-cols-2">
            <FormField label="注文依頼者" id="order_user" required>
              <input
                class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                id="order_user"
                type="text"
                list="users"
                v-model="form.order_user"
              />
              <datalist id="users">
                <option value="未選択"></option>
                <option
                  v-for="user in props.users"
                  :key="user.id"
                  :value="user.id"
                >
                  {{ user.name }}
                </option>
              </datalist>
            </FormField>

            <FormField label="発注者" id="user_id" required>
              <select
                class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                v-model="form.user_id"
              >
                <option
                  v-for="user in props.admin_users"
                  :key="user.id"
                  :value="user.id"
                >
                  {{ user.name }}
                </option>
              </select>
            </FormField>

            <FormField label="手配先" id="supplier_id" required>
              <input
                class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                id="supplier_id"
                type="text"
                list="suppliers"
                v-model="form.supplier_id"
              />
              <datalist id="suppliers">
                <option value="未選択"></option>
                <option
                  v-for="supplier in props.suppliers"
                  :key="supplier.id"
                  :value="supplier.id"
                >
                  {{
                    supplier.supplier_no != ""
                      ? `${supplier.supplier_no} : ${supplier.name}`
                      : supplier.name
                  }}
                </option>
              </datalist>
            </FormField>

            <FormField label="リードタイム" id="lead_time" required>
              <input
                class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                id="lead_time"
                type="number"
                v-model="form.lead_time"
              />
            </FormField>

            <FormField label="数量" id="quantity" required>
              <input
                class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                id="quantity"
                type="number"
                v-model="form.quantity"
                @change="form.calc_price = form.order_price * form.quantity"
              />
            </FormField>

            <FormField label="単位" id="unit" required>
              <select
                class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                v-model="form.unit"
              >
                <option v-if="form.solo_unit" :value="form.solo_unit">
                  {{ form.solo_unit }}
                </option>
                <option v-if="form.org_unit" :value="form.org_unit">
                  {{ form.org_unit }}
                </option>
              </select>
            </FormField>

            <FormField label="単価" id="order_price" required>
              <input
                class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                id="order_price"
                type="text"
                v-model="form.order_price"
                @change="form.calc_price = form.order_price * form.quantity"
              />
            </FormField>

            <FormField label="金額" id="calc_price" required>
              <input
                class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                id="calc_price"
                type="number"
                v-model="form.calc_price"
              />
            </FormField>

            <FormField label="工程" id="order_stock_process_id" required>
              <select
                class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                v-model="form.order_stock_process_id"
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
            </FormField>

            <FormField label="送料(※その他費用)" id="postage">
              <input
                class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                id="postage"
                type="number"
                v-model="form.postage"
              />
            </FormField>
          </div>

          <!-- 稟議書アップロード -->
          <div class="mt-4">
            <label
              for="dropzone-file"
              class="flex h-64 w-full cursor-pointer flex-col items-center justify-center rounded-card border-2 border-dashed transition-colors"
              :class="
                form.upload_file
                  ? 'border-success-500 bg-success-50 hover:bg-success-100'
                  : 'border-border-strong bg-surface-muted hover:bg-surface-sunken'
              "
            >
              <div class="flex flex-col items-center justify-center py-6">
                <Icon
                  name="upload_file"
                  size="xl"
                  class="mb-4 text-content-muted"
                />
                <p class="mb-2 text-sm text-content-muted">
                  <span class="text-base font-semibold text-content"
                    >稟議書</span
                  >をアップロードしてください。
                </p>
                <p class="text-center text-xs text-success-700">
                  {{
                    form.upload_file
                      ? `${form.upload_file.name} が選択されています。`
                      : ""
                  }}
                </p>
              </div>
              <input
                id="dropzone-file"
                type="file"
                class="hidden"
                @change="uploadFile"
                accept="application/pdf"
              />
            </label>
          </div>

          <template #footer>
            <div class="flex items-center justify-between">
              <span class="text-xs text-content-muted">* は必須項目です</span>
              <Button
                variant="primary"
                icon-left="add"
                @click.prevent="createStockAndInitialOrder"
              >
                発注登録
              </Button>
            </div>
          </template>
        </SectionCard>
      </form>
    </template>
  </MainLayout>
</template>