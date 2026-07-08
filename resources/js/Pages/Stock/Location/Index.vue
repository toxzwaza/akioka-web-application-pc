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
  locations: Array,
  processes: Array,
});
const form = reactive({
  location_name: "",
  processes: [],
});

const sendLocation = () => {
  form.processes = props.processes
    .filter((process) => process.select_flg)
    .map((process) => process.id);

  if (!form.location_name || !form.processes.length > 0) {
    alert(
      "格納先名が入力されていないか、管理部署が選択されていない可能性があります。"
    );
  } else {
    console.log(form);
    axios
      .post(route("stock.locations.store"), form)
      .then((res) => {
        console.log(res.data);
      })
      .catch((error) => {
        console.log(error);
      });
  }
};

onMounted(() => {
  console.log(props.locations, props.processes);
});
</script>
<template>
  <MainLayout :title="'格納先追加'">
    <template #content>
      <PageHeader
        title="格納先追加"
        subtitle="格納先・格納先アドレスの確認と編集を行います。"
      />

      <SectionCard title="格納先の新規登録" class="mb-6">
        <form class="max-w-lg space-y-6">
          <FormField
            label="格納先名"
            id="location-name"
            placeholder="格納先名を入力してください"
            v-model="form.location_name"
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

          <Button variant="primary" @click.prevent="sendLocation">登録</Button>
        </form>
      </SectionCard>

      <div class="mb-4">
        <Link
          :href="route('stock.storage_addresses.print')"
          class="inline-flex items-center gap-1 text-sm text-primary-700 hover:underline"
          >アドレス用紙印刷はこちら</Link
        >
      </div>

      <Table>
        <thead>
          <tr>
            <TableHeaderCell>格納先ID</TableHeaderCell>
            <TableHeaderCell>格納先名</TableHeaderCell>
            <TableHeaderCell>部署</TableHeaderCell>
            <TableHeaderCell align="right">登録済みアドレス数</TableHeaderCell>
            <TableHeaderCell>最終更新日</TableHeaderCell>
            <TableHeaderCell align="center">アドレス登録</TableHeaderCell>
          </tr>
        </thead>
        <tbody>
          <TableRow v-for="location in locations" :key="location.id">
            <TableDataCell nowrap>{{ location.id }}</TableDataCell>
            <TableDataCell>{{ location.name }}</TableDataCell>
            <TableDataCell>
              <div class="flex flex-wrap gap-1">
                <Badge
                  v-for="process in location.processes"
                  :key="process.id"
                  variant="primary"
                >
                  {{ process.name }}
                </Badge>
              </div>
            </TableDataCell>
            <TableDataCell align="right" nowrap>{{ location.address_count }}</TableDataCell>
            <TableDataCell nowrap>
              {{
                new Date(location.updated_at).toLocaleDateString("ja-JP", {
                  year: "numeric",
                  month: "2-digit",
                  day: "2-digit",
                })
              }}
            </TableDataCell>
            <TableDataCell align="center" nowrap>
              <Link
                :href="
                  route('stock.locations.show', {
                    location_id: location.id,
                  })
                "
              >
                <Button variant="secondary" size="sm">詳細</Button>
              </Link>
            </TableDataCell>
          </TableRow>
        </tbody>
      </Table>
    </template>
  </MainLayout>
</template>
