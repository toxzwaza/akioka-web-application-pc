<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import PageHeader from "@/Components/UI/PageHeader.vue";
import FilterBar from "@/Components/UI/FilterBar.vue";
import FormField from "@/Components/UI/FormField.vue";
import Button from "@/Components/UI/Button.vue";
import Table from "@/Components/UI/Table.vue";
import TableHeaderCell from "@/Components/UI/TableHeaderCell.vue";
import TableRow from "@/Components/UI/TableRow.vue";
import TableDataCell from "@/Components/UI/TableDataCell.vue";
import { onMounted, reactive, ref } from "vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
  stocks: Object,
  suppliers: Array,
  supplier_name: String,
  keyword: String,
});

const form = reactive({
  keyword: null,
  supplier_name: null,
});

const sort = ref(null);
const base_stocks = ref([]);
const filter_stocks = ref([]);

const getStocks = () => {
  router.get(route("stock.stocks"), form);
};
const redirectStock = (stock_id) => {
  window.location.href = route("stock.show.stocks", { stock_id: stock_id });
};

onMounted(() => {
  base_stocks.value = props.stocks.data;
  filter_stocks.value = props.stocks.data;

  form.keyword = props.keyword;
  form.supplier_name = props.supplier_name;
});
</script>

<template>
  <MainLayout :title="'在庫一覧'">
    <template #content>
      <PageHeader
        title="在庫一覧"
        subtitle="登録済みの在庫データの確認を行います。"
      />

      <FilterBar>
        <FormField label="品名・品番から検索">
          <input
            v-model="form.keyword"
            type="text"
            placeholder="品名または品番"
            class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
          />
        </FormField>

        <FormField label="手配先">
          <select
            v-model="form.supplier_name"
            class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
          >
            <option value="0">未選択</option>
            <option
              v-for="supplier in props.suppliers"
              :key="supplier.id"
              :value="supplier.name"
            >
              {{ supplier.name }}
            </option>
          </select>
        </FormField>

        <template #actions>
          <Button variant="ghost" size="sm">リセット</Button>
          <Button
            variant="secondary"
            size="sm"
            :class="{ 'opacity-60': sort === 'new_order' }"
            >新しい順</Button
          >
          <Button
            variant="secondary"
            size="sm"
            :class="{ 'opacity-60': sort === 'old_order' }"
            >古い順</Button
          >
          <Button variant="primary" icon-left="search" @click="getStocks"
            >検索</Button
          >
        </template>
      </FilterBar>

      <div class="mb-4 flex justify-end">
        <Pagination :links="props.stocks.links" />
      </div>

      <Table>
        <thead>
          <tr>
            <TableHeaderCell>画像</TableHeaderCell>
            <TableHeaderCell>ID</TableHeaderCell>
            <TableHeaderCell>JANコード</TableHeaderCell>
            <TableHeaderCell>品名</TableHeaderCell>
            <TableHeaderCell>品番</TableHeaderCell>
            <TableHeaderCell>納品先</TableHeaderCell>
            <TableHeaderCell align="right">価格</TableHeaderCell>
            <TableHeaderCell>カテゴリー</TableHeaderCell>
            <TableHeaderCell>手配先No</TableHeaderCell>
            <TableHeaderCell>手配先</TableHeaderCell>
          </tr>
        </thead>
        <tbody>
          <TableRow
            v-for="stock in filter_stocks"
            :key="stock.id"
            class="cursor-pointer"
            :class="{ 'opacity-60': stock.del_flg }"
            @click="redirectStock(stock.id)"
          >
            <TableDataCell class="w-24">
              <img
                class="max-h-16 rounded"
                :src="
                  stock.img_path && stock.img_path.includes('https://')
                    ? stock.img_path
                    : 'https://akioka.cloud/' + stock.img_path
                "
                alt=""
              />
            </TableDataCell>
            <TableDataCell nowrap>{{ stock.id }}</TableDataCell>
            <TableDataCell nowrap>{{ stock.jan_code }}</TableDataCell>
            <TableDataCell>{{ stock.name }}</TableDataCell>
            <TableDataCell nowrap>{{ stock.s_name }}</TableDataCell>
            <TableDataCell nowrap>{{ stock.deli_location }}</TableDataCell>
            <TableDataCell align="right" nowrap>
              {{ stock.price ? stock.price.toLocaleString() : "-" }}
              <span class="text-xs text-content-muted">円</span>
            </TableDataCell>
            <TableDataCell nowrap>{{ stock.classification_name }}</TableDataCell>
            <TableDataCell nowrap>{{ stock.supplier_no }}</TableDataCell>
            <TableDataCell nowrap>{{ stock.supplier_name }}</TableDataCell>
          </TableRow>
        </tbody>
      </Table>

      <div class="mt-4 flex justify-end">
        <Pagination :links="props.stocks.links" />
      </div>
    </template>
  </MainLayout>
</template>
