<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import PageHeader from "@/Components/UI/PageHeader.vue";
import FilterBar from "@/Components/UI/FilterBar.vue";
import FormField from "@/Components/UI/FormField.vue";
import SectionCard from "@/Components/UI/SectionCard.vue";
import Table from "@/Components/UI/Table.vue";
import TableHeaderCell from "@/Components/UI/TableHeaderCell.vue";
import TableRow from "@/Components/UI/TableRow.vue";
import TableDataCell from "@/Components/UI/TableDataCell.vue";
import Badge from "@/Components/UI/Badge.vue";
import Button from "@/Components/UI/Button.vue";
import { computed, reactive, ref, watch } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";

const props = defineProps({
  stocks: Object,
  all_units: Array,
  filters: Object,
});

/* ---------- 検索フィルタ ---------- */
const form = reactive({
  keyword: props.filters?.keyword ?? null,
  unit_status: props.filters?.unit_status ?? null,
  order_unit: props.filters?.order_unit ?? null,
  include_no_orders: props.filters?.include_no_orders ?? false,
});

const search = () => {
  router.get(route("stock.orderUnitMaintenance"), form, {
    preserveState: true,
    preserveScroll: true,
  });
};

const resetSearch = () => {
  form.keyword = null;
  form.unit_status = null;
  form.order_unit = null;
  form.include_no_orders = false;
  search();
};

/* ---------- 行データ ---------- */
// 適用単位のデフォルトは発注実績で最も使用回数が多い単位
const suggestUnit = (stock) => {
  if (!stock.order_units || stock.order_units.length === 0) return "";
  return stock.order_units[0].order_unit;
};

const rows = ref([]);
const buildRows = () => {
  rows.value = props.stocks.data.map((stock) => ({
    ...stock,
    selected: false,
    apply_unit: suggestUnit(stock),
  }));
};
buildRows();
watch(() => props.stocks, buildRows);

// 行の状態判定（一致 / 不一致 / 未設定 / 実績なし）
const rowStatus = (row) => {
  const org = (row.org_unit ?? "").trim();
  if (row.order_units.length === 0) return "none";
  if (!org) return "unset";
  const mismatch = row.order_units.some((u) => u.order_unit !== org);
  return mismatch ? "mismatch" : "match";
};

const statusLabel = {
  match: "一致",
  mismatch: "不一致",
  unset: "未設定",
  none: "実績なし",
};
const statusVariant = {
  match: "success",
  mismatch: "error",
  unset: "warning",
  none: "neutral",
};

/* ---------- 選択・一括操作 ---------- */
const selectedRows = computed(() => rows.value.filter((r) => r.selected));

const allSelected = computed({
  get: () => rows.value.length > 0 && rows.value.every((r) => r.selected),
  set: (val) => rows.value.forEach((r) => (r.selected = val)),
});

// 不一致・未設定の行だけをまとめて選択
const selectNeedsFix = () => {
  rows.value.forEach((r) => {
    const st = rowStatus(r);
    r.selected = st === "mismatch" || st === "unset";
  });
};

// 表記ゆれ統一用：選択行の適用単位を一括設定
const bulkUnit = ref("");
const applyBulkUnit = () => {
  const unit = bulkUnit.value.trim();
  if (!unit) {
    alert("設定する単位を入力してください。");
    return;
  }
  if (selectedRows.value.length === 0) {
    alert("対象の行を選択してください。");
    return;
  }
  selectedRows.value.forEach((r) => (r.apply_unit = unit));
};

/* ---------- 一括更新 ---------- */
const updating = ref(false);
const bulkUpdate = () => {
  const items = selectedRows.value
    .filter((r) => (r.apply_unit ?? "").trim() !== "")
    .map((r) => ({ stock_id: r.id, org_unit: r.apply_unit.trim() }));

  if (items.length === 0) {
    alert("更新対象がありません。行を選択し、適用する単位を入力してください。");
    return;
  }
  if (
    !confirm(
      `選択中の ${items.length} 件について、在庫の発注単位を「適用する単位」で上書きします。よろしいですか？`
    )
  ) {
    return;
  }

  updating.value = true;
  axios
    .post(route("stock.orderUnitMaintenance.bulkUpdate"), { items })
    .then((res) => {
      if (res.data.status) {
        alert(res.data.message);
        router.reload({ preserveScroll: true });
      } else {
        alert(res.data.message ?? "更新に失敗しました。");
      }
    })
    .catch((error) => {
      console.log(error);
      alert("更新に失敗しました。");
    })
    .finally(() => {
      updating.value = false;
    });
};

/* ---------- 表示ヘルパー ---------- */
const formatDate = (val) => {
  if (!val) return "-";
  return String(val).slice(0, 10).replace(/-/g, "/");
};
</script>

<template>
  <MainLayout :title="'発注単位整備'">
    <template #content>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <PageHeader
          title="発注単位整備"
          subtitle="発注実績の単位と在庫の発注単位を比較し、実際に使用した単位で一括上書きできます"
          icon="rule"
        />

        <!-- 実績単位の一覧（表記ゆれ確認） -->
        <SectionCard
          title="発注実績の単位一覧"
          subtitle="「フレコン」と「F」のような表記ゆれの確認に使用してください。単位をクリックすると絞り込みできます。"
          padding="sm"
          class="mb-6"
        >
          <div class="flex flex-wrap gap-2">
            <button
              v-for="u in all_units"
              :key="u.order_unit"
              type="button"
              class="inline-flex items-center gap-1.5 rounded-badge border px-2.5 py-1 text-xs font-medium transition-colors"
              :class="
                form.order_unit === u.order_unit
                  ? 'border-primary-600 bg-primary-50 text-primary-800'
                  : 'border-border bg-surface-base text-content hover:bg-surface-muted'
              "
              @click="
                form.order_unit =
                  form.order_unit === u.order_unit ? null : u.order_unit;
                search();
              "
            >
              <span>{{ u.order_unit }}</span>
              <span class="text-content-subtle"
                >{{ u.stock_count }}品目 / {{ u.order_count }}件</span
              >
            </button>
          </div>
        </SectionCard>

        <!-- 検索フィルタ -->
        <FilterBar>
          <FormField
            v-model="form.keyword"
            label="品名 / 品番 / 在庫No"
            placeholder="キーワード検索"
            @keyup.enter="search"
          />
          <FormField label="状態">
            <select
              v-model="form.unit_status"
              class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
            >
              <option :value="null">すべて</option>
              <option value="mismatch">不一致のみ</option>
              <option value="unset">発注単位が未設定</option>
            </select>
          </FormField>
          <FormField label="実績単位で絞り込み">
            <select
              v-model="form.order_unit"
              class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
            >
              <option :value="null">指定なし</option>
              <option
                v-for="u in all_units"
                :key="u.order_unit"
                :value="u.order_unit"
              >
                {{ u.order_unit }}（{{ u.stock_count }}品目）
              </option>
            </select>
          </FormField>
          <FormField label="表示対象">
            <label class="flex items-center gap-2 py-2 text-sm text-content">
              <input
                v-model="form.include_no_orders"
                type="checkbox"
                class="rounded border-border text-primary-600 focus:ring-primary-500"
              />
              発注実績のない在庫も表示
            </label>
          </FormField>
          <template #actions>
            <Button variant="secondary" size="sm" @click="resetSearch">
              リセット
            </Button>
            <Button variant="primary" size="sm" icon-left="search" @click="search">
              検索
            </Button>
          </template>
        </FilterBar>

        <!-- 一括操作バー -->
        <SectionCard padding="sm" class="mb-6">
          <div class="flex flex-wrap items-center gap-3">
            <Badge :variant="selectedRows.length ? 'primary' : 'neutral'">
              選択中：{{ selectedRows.length }}件
            </Badge>
            <Button variant="secondary" size="sm" @click="selectNeedsFix">
              不一致・未設定の行を選択
            </Button>

            <span class="h-6 w-px bg-border hidden sm:block"></span>

            <div class="flex items-center gap-2">
              <input
                v-model="bulkUnit"
                type="text"
                list="unit-options"
                placeholder="統一する単位（例：フレコン）"
                class="w-56 rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
              />
              <Button variant="outline" size="sm" @click="applyBulkUnit">
                選択行の適用単位に一括設定
              </Button>
            </div>

            <div class="ml-auto">
              <Button
                variant="primary"
                size="sm"
                icon-left="save"
                :loading="updating"
                @click="bulkUpdate"
              >
                選択行の発注単位を一括更新
              </Button>
            </div>
          </div>
          <p class="mt-2 text-xs text-content-muted">
            表記ゆれ（例：「F」と「フレコン」）を統一する場合は、実績単位で絞り込み →
            行を選択 → 統一する単位を一括設定 → 一括更新の順に操作してください。
          </p>
        </SectionCard>

        <!-- 単位入力候補 -->
        <datalist id="unit-options">
          <option
            v-for="u in all_units"
            :key="u.order_unit"
            :value="u.order_unit"
          />
        </datalist>

        <!-- 比較一覧 -->
        <SectionCard padding="none">
          <Table>
            <thead>
              <tr>
                <TableHeaderCell align="center">
                  <input
                    v-model="allSelected"
                    type="checkbox"
                    class="rounded border-border text-primary-600 focus:ring-primary-500"
                  />
                </TableHeaderCell>
                <TableHeaderCell>在庫No</TableHeaderCell>
                <TableHeaderCell>品名 / 品番</TableHeaderCell>
                <TableHeaderCell>現在の発注単位</TableHeaderCell>
                <TableHeaderCell>発注実績の単位（回数 / 最終発注日）</TableHeaderCell>
                <TableHeaderCell align="center">状態</TableHeaderCell>
                <TableHeaderCell>適用する単位</TableHeaderCell>
              </tr>
            </thead>
            <tbody>
              <TableRow
                v-for="row in rows"
                :key="row.id"
                :state="row.selected ? 'selected' : 'default'"
              >
                <TableDataCell align="center">
                  <input
                    v-model="row.selected"
                    type="checkbox"
                    class="rounded border-border text-primary-600 focus:ring-primary-500"
                  />
                </TableDataCell>
                <TableDataCell nowrap>{{ row.stock_no || "-" }}</TableDataCell>
                <TableDataCell>
                  <Link
                    :href="route('stock.show.stocks', { stock_id: row.id })"
                    class="font-medium text-primary-700 hover:underline"
                    title="在庫詳細画面へ移動"
                  >
                    {{ row.name }}
                  </Link>
                  <p class="text-xs text-content-muted">{{ row.s_name }}</p>
                </TableDataCell>
                <TableDataCell nowrap>
                  <span v-if="(row.org_unit ?? '').trim()">{{
                    row.org_unit
                  }}</span>
                  <Badge v-else variant="warning" size="sm">未設定</Badge>
                </TableDataCell>
                <TableDataCell>
                  <div
                    v-if="row.order_units.length"
                    class="flex flex-wrap gap-1.5"
                  >
                    <button
                      v-for="u in row.order_units"
                      :key="u.order_unit"
                      type="button"
                      class="inline-flex items-center gap-1 rounded-badge border px-2 py-0.5 text-xs transition-colors"
                      :class="
                        u.order_unit === (row.org_unit ?? '').trim()
                          ? 'border-success-100 bg-success-50 text-success-700'
                          : 'border-error-100 bg-error-50 text-error-700 hover:bg-error-100'
                      "
                      :title="`クリックで「適用する単位」に設定`"
                      @click="row.apply_unit = u.order_unit"
                    >
                      <span class="font-medium">{{ u.order_unit }}</span>
                      <span>
                        ×{{ u.order_count }} /
                        {{ formatDate(u.last_order_date) }}
                      </span>
                    </button>
                  </div>
                  <span v-else class="text-xs text-content-subtle"
                    >発注実績なし</span
                  >
                </TableDataCell>
                <TableDataCell align="center" nowrap>
                  <Badge :variant="statusVariant[rowStatus(row)]" size="sm">
                    {{ statusLabel[rowStatus(row)] }}
                  </Badge>
                </TableDataCell>
                <TableDataCell nowrap>
                  <input
                    v-model="row.apply_unit"
                    type="text"
                    list="unit-options"
                    placeholder="単位を入力"
                    class="w-36 rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500"
                  />
                </TableDataCell>
              </TableRow>
              <tr v-if="rows.length === 0">
                <td
                  colspan="7"
                  class="px-4 py-10 text-center text-sm text-content-muted"
                >
                  条件に一致する在庫がありません。
                </td>
              </tr>
            </tbody>
          </Table>
        </SectionCard>

        <div class="mt-6 flex justify-center">
          <Pagination :links="stocks.links" />
        </div>
      </div>
    </template>
  </MainLayout>
</template>
