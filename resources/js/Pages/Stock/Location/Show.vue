<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { onMounted, reactive, ref } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";
import PageHeader from "@/Components/UI/PageHeader.vue";
import SectionCard from "@/Components/UI/SectionCard.vue";
import FormField from "@/Components/UI/FormField.vue";
import Button from "@/Components/UI/Button.vue";
import Badge from "@/Components/UI/Badge.vue";
import Table from "@/Components/UI/Table.vue";
import TableHeaderCell from "@/Components/UI/TableHeaderCell.vue";
import TableRow from "@/Components/UI/TableRow.vue";
import TableDataCell from "@/Components/UI/TableDataCell.vue";

const props = defineProps({
  location: Object,
  processes: Array,
  location_processes: Array,
  storage_addresses: Array,
});
const form = reactive({
  location: {
    location_id: null,
    location_name: "",
    processes: [],
  },
  address: {
    id: null,
    shelf: '',
    row: '',
    col: '',
    sub_row: ''
  }
});

const checkProcess = () => {
  props.processes.forEach((process) => {
    process.select_flg = props.location_processes.includes(process.id) ? 1 : 0;
  });

  console.log(props.processes);
};
const resetAddressForm = () => {
  form.address.id = null
  form.address.shelf = ''
  form.address.row = ''
  form.address.col = ''
  form.address.sub_row = ''
}

const sendLocation = () => {
  form.location.processes = props.processes
    .filter((process) => process.select_flg)
    .map((process) => process.id);

  if (!form.location.location_name || !form.location.processes.length > 0) {
    alert(
      "格納先名が入力されていないか、管理部署が選択されていない可能性があります。"
    );
  } else {
    console.log(form);
    axios
      .post(route("stock.locations.store"), form.location)
      .then((res) => {
        console.log(res.data);
        alert("登録・編集が完了しました。");
        window.location.reload()
      })
      .catch((error) => {
        console.log(error);
      });
  }
};
const sendStorageAddress = () => {
  axios.post(route('stock.storage_addresses.store'), {
    location_id: form.location.location_id,
    storage_address_id : form.address.id,
    shelf: form.address.shelf,
    row: form.address.row,
    col: form.address.col,
    sub_row: form.address.sub_row
  })
  .then(res => {
    console.log(res.data)
    alert("登録・編集が完了しました。");
    window.location.reload()
  })
  .catch(error => {
    console.log(error)
  })
}

const editStorageAddress = (storage_address) => {
  console.log(storage_address)
  form.address.id = storage_address.id
  form.address.shelf = storage_address.shelf
  form.address.row = storage_address.row
  form.address.col = storage_address.col
  form.address.sub_row = storage_address.sub_row
}

onMounted(() => {
  form.location.location_id = props.location.id;
  form.location.location_name = props.location.name;
  checkProcess();
  console.log(props.storage_addresses)
});
</script>
<template>
  <MainLayout :title="'格納先詳細'">
    <template #content>
      <PageHeader
        title="格納先詳細"
        subtitle="格納先・格納先アドレスの詳細確認と編集を行います。"
      />

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <SectionCard title="格納先編集">
          <form class="space-y-6">
            <FormField
              label="格納先名"
              id="location-name"
              placeholder="格納先名を入力してください"
              v-model="form.location.location_name"
            />

            <div>
              <label class="block mb-1 text-sm font-medium text-content">
                管理部署選択
              </label>
              <div class="flex flex-wrap gap-1.5">
                <button
                  type="button"
                  v-for="process in props.processes"
                  :key="process.id"
                  @click="process.select_flg = !process.select_flg"
                  class="rounded-badge px-2.5 py-1 text-xs font-medium transition-colors"
                  :class="
                    process.select_flg
                      ? 'bg-primary-600 text-content-inverse'
                      : 'bg-surface-sunken text-content-muted hover:bg-surface-muted'
                  "
                >
                  {{ process.name }}
                </button>
              </div>
            </div>

            <Button variant="primary" @click.prevent="sendLocation">編集</Button>
          </form>
        </SectionCard>

        <SectionCard :title="form.address.id ? 'アドレス編集' : 'アドレス登録'">
          <form class="space-y-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <FormField
                label="棚"
                required
                type="text"
                placeholder="A-Z"
                v-model="form.address.shelf"
              />
              <FormField
                label="段"
                required
                type="number"
                placeholder="0~9"
                v-model="form.address.row"
              />
              <FormField
                label="列"
                type="number"
                placeholder="0~9"
                v-model="form.address.col"
              />
              <FormField
                label="列の列"
                type="number"
                placeholder="0~9"
                v-model="form.address.sub_row"
              />
            </div>

            <div class="flex items-center gap-3">
              <Button
                :variant="form.address.id ? 'primary' : 'outline'"
                @click.prevent="sendStorageAddress"
              >
                {{ form.address.id ? '編集' : '登録' }}
              </Button>
              <Button
                v-if="form.address.id"
                variant="ghost"
                @click.prevent="resetAddressForm"
              >
                新規登録へ戻る
              </Button>
            </div>
          </form>
        </SectionCard>
      </div>

      <Table>
        <thead>
          <tr>
            <TableHeaderCell>アドレスID</TableHeaderCell>
            <TableHeaderCell>アドレス</TableHeaderCell>
            <TableHeaderCell align="right">格納済み在庫数</TableHeaderCell>
            <TableHeaderCell>最終更新日</TableHeaderCell>
            <TableHeaderCell align="center">操作</TableHeaderCell>
          </tr>
        </thead>
        <tbody>
          <TableRow
            v-for="storage_address in storage_addresses"
            :key="storage_address.id"
            :state="form.address.id === storage_address.id ? 'selected' : 'default'"
          >
            <TableDataCell nowrap>{{ storage_address.id }}</TableDataCell>
            <TableDataCell>{{ storage_address.address }}</TableDataCell>
            <TableDataCell align="right" nowrap>
              <Link
                class="text-primary-700 hover:underline"
                :href="route('stock.stocks', { storage_address_id: storage_address.id })"
                >{{ storage_address.stock_count }}</Link
              >
            </TableDataCell>
            <TableDataCell nowrap>
              {{
                new Date(storage_address.updated_at).toLocaleDateString("ja-JP", {
                  year: "numeric",
                  month: "2-digit",
                  day: "2-digit",
                })
              }}
            </TableDataCell>
            <TableDataCell align="center" nowrap>
              <Button
                variant="secondary"
                size="sm"
                icon-left="edit"
                @click.prevent="editStorageAddress(storage_address)"
              >
                編集
              </Button>
            </TableDataCell>
          </TableRow>
        </tbody>
      </Table>
    </template>
  </MainLayout>
</template>
