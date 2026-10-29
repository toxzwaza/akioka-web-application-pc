<script setup>
import { computed, ref } from "vue";

const props = defineProps({
  approval_document: Object,
});

const documentRef = ref(null);

// 日付を YYYY/MM/DD 表記に整形
const fmtDate = (date) =>
  date ? String(date).split("T")[0].split(" ")[0].replace(/-/g, "/") : "-";

// 金額を ¥1,234 表記に整形
const fmtYen = (value) =>
  value != null && value !== "" ? `¥${Number(value).toLocaleString()}` : "-";

// 印影の日付（yy.mm.dd）
const fmtStampDate = (date) =>
  date
    ? new Date(String(date).replace(" ", "T"))
        .toLocaleDateString("ja-JP", { year: "2-digit", month: "2-digit", day: "2-digit" })
        .replace(/\//g, ".")
    : "";

// 承認済み（status=1）の承認者だけ印影を出す
const stamps = computed(() => {
  const approvals = props.approval_document?.approvals ?? [];
  return (Array.isArray(approvals) ? approvals : Object.values(approvals))
    .filter((approval) => approval && Number(approval.status) === 1)
    .map((approval) => ({
      key: approval.id ?? approval.user_id,
      name: approval.user_name ?? approval.name,
      date: fmtStampDate(approval.updated_at ?? approval.created_at),
    }));
});

const images = computed(() => props.approval_document?.document_images ?? []);

// 稟議書部分だけを別ウィンドウに写して印刷する（画面のスタイルをそのまま引き継ぐ）
const printApproval = () => {
  if (!documentRef.value) return;
  const win = window.open("", "_blank");
  if (!win) return;
  const styles = Array.from(document.querySelectorAll('style, link[rel="stylesheet"]'))
    .map((el) => el.outerHTML)
    .join("");
  win.document.write(
    `<!doctype html><html lang="ja"><head><meta charset="utf-8"><title>稟議書</title>${styles}` +
      `<style>@page{size:A4;margin:12mm}body{background:#fff}#approval_document{margin:0 auto!important;box-shadow:none!important;border:none!important}</style>` +
      `</head><body>${documentRef.value.outerHTML}</body></html>`
  );
  win.document.close();
  win.onload = () => {
    win.focus();
    win.print();
    win.close();
  };
};
</script>

<template>
  <div class="approval-document-wrap">
    <!-- 操作バー -->
    <div class="flex justify-end">
      <button
        type="button"
        class="inline-flex items-center gap-1.5 rounded border border-gray-300 bg-white px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50"
        @click="printApproval"
      >
        <i class="fas fa-print"></i>印刷
      </button>
    </div>

    <div
      id="approval_document"
      ref="documentRef"
      class="mx-auto w-full max-w-3xl bg-white px-4 py-6 sm:px-8 md:px-14 md:py-12"
    >
      <!-- 表題 -->
      <h1
        class="text-center text-xl sm:text-2xl md:text-3xl font-bold text-gray-800 tracking-[0.5em] pl-[0.5em] mb-2"
      >
        稟議書
      </h1>
      <div
        class="mx-auto w-28 sm:w-36 border-b-4 border-double border-gray-600 mb-6 md:mb-10"
      ></div>

      <!-- 基本情報（PC：罫線表） -->
      <table class="hidden md:table w-full border-collapse text-sm mb-8">
        <tbody>
          <tr>
            <th class="doc-th">起案部門</th>
            <td class="doc-td">{{ props.approval_document.process_name ?? "-" }}</td>
            <th class="doc-th">起案者</th>
            <td class="doc-td">{{ props.approval_document.user_name ?? "-" }}</td>
          </tr>
          <tr>
            <th class="doc-th">評価予定日</th>
            <td class="doc-td">{{ fmtDate(props.approval_document.evalution_date) }}</td>
            <th class="doc-th">希望納入日</th>
            <td class="doc-td">{{ fmtDate(props.approval_document.desire_delivery_date) }}</td>
          </tr>
          <tr>
            <th class="doc-th">発注先</th>
            <td class="doc-td" colspan="3">{{ props.approval_document.supplier_name ?? "-" }}</td>
          </tr>
          <tr>
            <th class="doc-th">品名</th>
            <td class="doc-td">{{ props.approval_document.name ?? "-" }}</td>
            <th class="doc-th">品番</th>
            <td class="doc-td">{{ props.approval_document.s_name ?? "-" }}</td>
          </tr>
          <tr>
            <th class="doc-th">単価</th>
            <td class="doc-td">{{ fmtYen(props.approval_document.price) }}</td>
            <th class="doc-th">数量</th>
            <td class="doc-td">{{ props.approval_document.quantity ?? "-" }}</td>
          </tr>
          <tr>
            <th class="doc-th">合計金額</th>
            <td class="doc-td text-base font-bold" colspan="3">
              {{ fmtYen(props.approval_document.calc_price) }}
            </td>
          </tr>
          <tr>
            <th class="doc-th">件名</th>
            <td class="doc-td font-medium" colspan="3">{{ props.approval_document.title ?? "-" }}</td>
          </tr>
        </tbody>
      </table>

      <!-- 基本情報（モバイル：フラットなリスト） -->
      <dl class="md:hidden mb-6 text-sm border-t border-gray-200">
        <div class="doc-row">
          <dt class="doc-dt">起案部門</dt>
          <dd class="doc-dd">{{ props.approval_document.process_name ?? "-" }}</dd>
        </div>
        <div class="doc-row">
          <dt class="doc-dt">起案者</dt>
          <dd class="doc-dd">{{ props.approval_document.user_name ?? "-" }}</dd>
        </div>
        <div class="doc-row">
          <dt class="doc-dt">評価予定日</dt>
          <dd class="doc-dd">{{ fmtDate(props.approval_document.evalution_date) }}</dd>
        </div>
        <div class="doc-row">
          <dt class="doc-dt">希望納入日</dt>
          <dd class="doc-dd">{{ fmtDate(props.approval_document.desire_delivery_date) }}</dd>
        </div>
        <div class="doc-row">
          <dt class="doc-dt">発注先</dt>
          <dd class="doc-dd">{{ props.approval_document.supplier_name ?? "-" }}</dd>
        </div>
        <div class="doc-row">
          <dt class="doc-dt">品名</dt>
          <dd class="doc-dd">{{ props.approval_document.name ?? "-" }}</dd>
        </div>
        <div class="doc-row">
          <dt class="doc-dt">品番</dt>
          <dd class="doc-dd">{{ props.approval_document.s_name ?? "-" }}</dd>
        </div>
        <div class="doc-row">
          <dt class="doc-dt">単価</dt>
          <dd class="doc-dd">{{ fmtYen(props.approval_document.price) }}</dd>
        </div>
        <div class="doc-row">
          <dt class="doc-dt">数量</dt>
          <dd class="doc-dd">{{ props.approval_document.quantity ?? "-" }}</dd>
        </div>
        <div class="doc-row bg-gray-50">
          <dt class="doc-dt font-semibold text-gray-700">合計金額</dt>
          <dd class="doc-dd font-bold text-base">{{ fmtYen(props.approval_document.calc_price) }}</dd>
        </div>
        <div class="doc-row">
          <dt class="doc-dt">件名</dt>
          <dd class="doc-dd font-medium">{{ props.approval_document.title ?? "-" }}</dd>
        </div>
      </dl>

      <!-- 長文セクション（PC・モバイル共通） -->
      <div class="doc-section">
        <div class="doc-section-label">発注内容</div>
        <p
          class="doc-section-body"
          v-html="props.approval_document.content?.replace(/\n/g, '<br>') ?? '-'"
        ></p>
      </div>

      <div class="doc-section">
        <div class="doc-section-label">申請理由</div>
        <p
          class="doc-section-body"
          v-html="props.approval_document.main_reason?.replace(/\n/g, '<br>') ?? '-'"
        ></p>
      </div>

      <div class="doc-section">
        <div class="doc-section-label">選定理由</div>
        <p
          class="doc-section-body"
          v-html="props.approval_document.sub_reason?.replace(/\n/g, '<br>') ?? '-'"
        ></p>
      </div>

      <!-- 添付画像 -->
      <div v-if="images.length > 0" class="doc-section">
        <div class="doc-section-label">添付画像</div>
        <div class="doc-section-body grid grid-cols-3 gap-3 sm:grid-cols-4">
          <a
            v-for="document_image in images"
            :key="document_image.id ?? document_image.image_path"
            :href="'https://akioka.cloud' + document_image.image_path"
            target="_blank"
            class="block overflow-hidden rounded border border-gray-200"
          >
            <img
              :src="'https://akioka.cloud' + document_image.image_path"
              alt=""
              class="aspect-square w-full object-cover"
            />
          </a>
        </div>
      </div>

      <!-- 承認印（承認済みの承認者のみ） -->
      <div v-if="stamps.length > 0" class="mt-8 flex flex-wrap justify-end gap-3">
        <div
          v-for="stamp in stamps"
          :key="stamp.key"
          class="stamp flex h-24 w-24 flex-col items-center justify-between rounded-full border-[3px] border-red-500 p-2 text-center text-red-600"
        >
          <div class="w-full text-[10px] font-bold">アキオカ</div>
          <div class="middle_text w-full py-0.5 text-base font-semibold leading-none">
            {{ stamp.date }}
          </div>
          <div class="w-full truncate text-[10px] font-bold">{{ stamp.name }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss">
// PC：カード内に「紙」が置かれた文書らしい見た目
#approval_document {
  margin: 1rem auto 1.5rem;
  box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px;
  border: 1px solid rgba(201, 201, 201, 0.473);
}

// モバイル：枠を外してフラットに（親カードの枠に任せる）
@media screen and (max-width: 767px) {
  #approval_document {
    margin: 0;
    border: none;
    box-shadow: none;
  }
}

// PC罫線表のセル
.doc-th {
  width: 7.5rem;
  border: 1px solid #9ca3af; // gray-400
  background-color: #f3f4f6; // gray-100
  padding: 0.625rem 1rem;
  text-align: left;
  font-weight: 600;
  color: #374151; // gray-700
  white-space: nowrap;
}

.doc-td {
  border: 1px solid #9ca3af;
  padding: 0.625rem 1rem;
  color: #111827; // gray-900
}

// モバイルリストの行
.doc-row {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 0.75rem;
  padding: 0.625rem 0.25rem;
  border-bottom: 1px solid #e5e7eb; // gray-200
}

.doc-dt {
  flex-shrink: 0;
  width: 5.5rem;
  padding-top: 0.125rem;
  font-size: 0.75rem;
  color: #6b7280; // gray-500
}

.doc-dd {
  color: #111827;
  text-align: right;
  word-break: break-word;
}

// 長文セクション（見出しバー付きボックス）
.doc-section {
  border: 1px solid #d1d5db; // gray-300
  border-radius: 2px;
  margin-bottom: 1.25rem;

  @media screen and (min-width: 768px) {
    border-color: #9ca3af; // PCは表の罫線と揃える
  }
}

.doc-section-label {
  background-color: #f3f4f6;
  border-bottom: 1px solid #d1d5db;
  padding: 0.5rem 0.875rem;
  font-size: 0.8125rem;
  font-weight: 600;
  color: #374151;

  @media screen and (min-width: 768px) {
    border-bottom-color: #9ca3af;
    font-size: 0.875rem;
  }
}

.doc-section-body {
  padding: 0.875rem;
  font-size: 0.875rem;
  line-height: 1.8;
  color: #1f2937; // gray-800

  @media screen and (min-width: 768px) {
    padding: 1rem 1.25rem;
    min-height: 5rem;
  }
}

// 印影の日付を上下の線で挟む
.stamp .middle_text {
  border-top: 2px solid #ef4444;
  border-bottom: 2px solid #ef4444;
}
</style>
