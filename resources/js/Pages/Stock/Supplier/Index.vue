<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import PageHeader from "@/Components/UI/PageHeader.vue";
import FilterBar from "@/Components/UI/FilterBar.vue";
import FormField from "@/Components/UI/FormField.vue";
import Button from "@/Components/UI/Button.vue";
import Badge from "@/Components/UI/Badge.vue";
import Table from "@/Components/UI/Table.vue";
import TableHeaderCell from "@/Components/UI/TableHeaderCell.vue";
import TableRow from "@/Components/UI/TableRow.vue";
import TableDataCell from "@/Components/UI/TableDataCell.vue";
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
      <PageHeader
        title="取引先一覧"
        subtitle="取引先一覧の確認と登録を行います。必須項目を入力して、追加ボタンを押してください。"
      >
        <template #actions>
          <Link :href="route('stock.suppliers.create')">
            <Button variant="primary" icon-left="add">取引先追加</Button>
          </Link>
        </template>
      </PageHeader>

      <FilterBar>
        <FormField label="取引先名">
          <input
            type="text"
            v-model="form.name"
            class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
            placeholder="取引先名で検索"
          />
        </FormField>
        <FormField label="ふり">
          <input
            type="text"
            v-model="form.rub_name"
            class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
            placeholder="ふりで検索"
          />
        </FormField>
        <FormField label="電話番号">
          <input
            type="text"
            v-model="form.tel"
            class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
            placeholder="電話番号で検索"
          />
        </FormField>
        <FormField label="FAX番号">
          <input
            type="text"
            v-model="form.fax"
            class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
            placeholder="FAX番号で検索"
          />
        </FormField>
        <FormField label="郵便番号">
          <input
            type="text"
            v-model="form.p_code"
            class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
            placeholder="郵便番号で検索"
          />
        </FormField>
        <FormField label="住所">
          <input
            type="text"
            v-model="form.address"
            class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
            placeholder="住所で検索"
          />
        </FormField>

        <template #actions>
          <Button variant="ghost" @click="resetFilters">リセット</Button>
          <Button variant="primary" icon-left="search" @click="searchSuppliers"
            >検索</Button
          >
        </template>
      </FilterBar>

      <Table>
        <thead>
          <tr>
            <TableHeaderCell>ID</TableHeaderCell>
            <TableHeaderCell>取引先No</TableHeaderCell>
            <TableHeaderCell>取引先名</TableHeaderCell>
            <TableHeaderCell>ふり</TableHeaderCell>
            <TableHeaderCell>電話番号</TableHeaderCell>
            <TableHeaderCell>FAX番号</TableHeaderCell>
            <TableHeaderCell>郵便番号</TableHeaderCell>
            <TableHeaderCell>住所</TableHeaderCell>
            <TableHeaderCell>メモ</TableHeaderCell>
            <TableHeaderCell align="center">有効/無効</TableHeaderCell>
            <TableHeaderCell>作成日時</TableHeaderCell>
            <TableHeaderCell>更新日時</TableHeaderCell>
            <TableHeaderCell>インボイス登録番号</TableHeaderCell>
            <TableHeaderCell align="center">操作</TableHeaderCell>
          </tr>
        </thead>
        <tbody>
          <TableRow
            v-for="supplier in suppliers.data"
            :key="supplier.id"
            :state="supplier.del_flg === 1 ? 'error' : 'default'"
          >
            <TableDataCell nowrap>{{ supplier.id }}</TableDataCell>
            <TableDataCell nowrap>{{ supplier.supplier_no ?? '-' }}</TableDataCell>
            <TableDataCell nowrap>{{ supplier.name ?? '-' }}</TableDataCell>
            <TableDataCell nowrap>{{ supplier.rub_name ?? '-' }}</TableDataCell>
            <TableDataCell nowrap>{{ supplier.tel ?? '-' }}</TableDataCell>
            <TableDataCell nowrap>{{ supplier.fax ?? '-' }}</TableDataCell>
            <TableDataCell nowrap>{{ supplier.p_code ?? '-' }}</TableDataCell>
            <TableDataCell nowrap>{{ supplier.address ?? '-' }}</TableDataCell>
            <TableDataCell nowrap>{{ supplier.memo ?? '-' }}</TableDataCell>
            <TableDataCell align="center" nowrap>
              <Badge :variant="supplier.del_flg === 1 ? 'error' : 'success'">
                {{ getStatusText(supplier.del_flg) }}
              </Badge>
            </TableDataCell>
            <TableDataCell nowrap>{{ formatDate(supplier.created_at) }}</TableDataCell>
            <TableDataCell nowrap>{{ formatDate(supplier.updated_at) }}</TableDataCell>
            <TableDataCell nowrap>{{ supplier.invoice_registration_number ?? '-' }}</TableDataCell>
            <TableDataCell align="center" nowrap>
              <Link :href="route('stock.suppliers.edit', supplier.id)">
                <Button variant="secondary" size="sm" icon-left="edit"
                  >編集</Button
                >
              </Link>
            </TableDataCell>
          </TableRow>
        </tbody>
      </Table>

      <div class="mt-4 flex justify-end">
        <Pagination :links="suppliers.links" />
      </div>
    </template>
  </MainLayout>
</template>