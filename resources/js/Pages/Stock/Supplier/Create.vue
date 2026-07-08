<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { reactive, onMounted } from "vue";
import axios from "axios";
import PageHeader from "@/Components/UI/PageHeader.vue";
import SectionCard from "@/Components/UI/SectionCard.vue";
import FormField from "@/Components/UI/FormField.vue";
import Button from "@/Components/UI/Button.vue";

const props = defineProps({
  supplier: Object,
  edit: Boolean,
});

const form = reactive({
  supplier_id: null,
  supplier_no: null,
  name: null,
  rub_name: null,
  tel: null,
  fax: null,
  p_code: null,
  address: null,
  invoice_registration_number: null,
  memo: null,
});

const createSupplier = () => {
  console.log(form);
  axios
    .post(route("stock.suppliers.store"), form)
    .then((res) => {
      console.log(res.data);
      if (confirm("登録が完了しました。")) {
        window.location.href = route("stock");
      }
    })
    .catch((error) => {
      console.log(error);
    });
};

const editSupplier = () => {
  console.log(form);
  axios
    .post(route("stock.suppliers.store"), form)
    .then((res) => {
      console.log(res.data);
      if (confirm("編集が完了しました。")) {
        window.location.href = route("stock.suppliers");
      }
    })
    .catch((error) => {
      console.log(error);
    });
};
onMounted(() => {
  if (props.edit) {
    form.supplier_id = props.supplier.id;
    form.supplier_no = props.supplier.supplier_no;
    form.name = props.supplier.name;
    form.rub_name = props.supplier.rub_name;
    form.tel = props.supplier.tel;
    form.fax = props.supplier.fax;
    form.p_code = props.supplier.p_code;
    form.address = props.supplier.address;
    form.invoice_registration_number =
      props.supplier.invoice_registration_number;
    form.memo = props.supplier.memo;
  }
});
</script>
<template>
  <MainLayout :title="'取引先'">
    <template #content>
      <PageHeader
        :title="props.edit ? '取引先編集' : '取引先登録'"
        subtitle="取引先の情報を入力します。必須項目を入力して、追加ボタンを押してください。"
      />

      <SectionCard title="取引先情報">
        <form class="grid gap-4 sm:grid-cols-2">
          <FormField label="取引先no" id="supplier_no">
            <input
              v-model="form.supplier_no"
              id="supplier_no"
              name="supplier_no"
              class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
            />
          </FormField>

          <div class="sm:col-span-2">
            <FormField label="会社名" id="name" required>
              <input
                v-model="form.name"
                id="name"
                name="name"
                class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
              />
            </FormField>
          </div>

          <div class="sm:col-span-2">
            <FormField label="読み（ひらがな）" id="rub_name">
              <input
                v-model="form.rub_name"
                id="rub_name"
                name="rub_name"
                class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
              />
            </FormField>
          </div>

          <FormField label="TEL" id="tel">
            <input
              v-model="form.tel"
              id="tel"
              name="tel"
              type="text"
              class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
            />
          </FormField>

          <FormField label="FAX" id="fax">
            <input
              v-model="form.fax"
              id="fax"
              name="fax"
              type="text"
              class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
            />
          </FormField>

          <div class="sm:col-span-2">
            <FormField label="住所" id="address">
              <input
                v-model="form.address"
                id="address"
                name="address"
                class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
              />
            </FormField>
          </div>

          <FormField label="郵便番号" id="p_code">
            <input
              v-model="form.p_code"
              id="p_code"
              name="p_code"
              type="text"
              class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
            />
          </FormField>

          <FormField label="的確事業者番号" id="invoice_registration_number">
            <input
              v-model="form.invoice_registration_number"
              id="invoice_registration_number"
              name="invoice_registration_number"
              type="text"
              class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
            />
          </FormField>

          <div class="sm:col-span-2">
            <FormField label="メモ" id="memo">
              <textarea
                v-model="form.memo"
                id="memo"
                name="memo"
                rows="4"
                class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
              ></textarea>
            </FormField>
          </div>
        </form>

        <template #footer>
          <div class="flex items-center justify-between">
            <span class="text-xs text-content-muted">* は必須項目です</span>
            <Button
              v-if="!props.edit"
              variant="primary"
              icon-left="add"
              @click.prevent="createSupplier"
            >
              追加
            </Button>
            <Button
              v-else
              variant="primary"
              icon-left="save"
              @click.prevent="editSupplier"
            >
              編集
            </Button>
          </div>
        </template>
      </SectionCard>
    </template>
  </MainLayout>
</template>
