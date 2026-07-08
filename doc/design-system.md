# Akioka Web Application デザインシステム

> 本システムの全画面が従うUIルール。**新規画面・改修は必ず本書に沿って設計する。**
> 参照ガイド: `秘書部門/インプット/AIを用いたシステムデザイン/20260626_AIデザインシステム設計ガイド.md`（14項目チェックリスト）

---

## 0. デザイン方針（トーン&マナー）

| 項目 | 決定 |
|------|------|
| 対象 | 社内の業務担当者（PC主体・データ密度高め） |
| 目的 | 業務を正確・効率的に遂行。信頼感と視認性を重視 |
| ビジュアルスタイル | **コーポレート × フラット × Bento UI**（BtoB SaaS 路線） |
| 基調 | **白背景 × ネイビー**。整ったグリッド、カード型UI |
| 目指す印象 | 信頼・清潔・効率的・整然 |
| 避ける印象 | チープ、派手すぎ、ごちゃごちゃ、子どもっぽい、装飾過多 |
| フォント | Noto Sans JP（日本語主体） |
| 実装基盤 | Laravel + Inertia + Vue3 + Tailwind CSS v3 |

**素人っぽさを避ける鉄則**
- 色・余白・角丸・影は**必ずトークン経由**。生の `bg-blue-500` / inline style の色指定を新規で書かない。
- 日本語UIに `uppercase` / `tracking-widest` を使わない。
- 情報のまとまりは Card で囲う。余白はケチらず、要素を詰め込みすぎない。
- **色だけで状態を伝えない**（色＋ラベル/アイコンを併記）。

---

## 1. Design Token（`tailwind.config.js` が単一のソース）

> トークンは `theme.extend` に定義。**Tailwind標準の `blue-*`/`gray-*` 等は上書きしない**（既存コードを壊さないため）。
> ⚠️ 色の変更は `tailwind.config.js` を編集する。`resources/css/app.scss` は原則触らない（コンパイル済み `app.css` が直接配信されており、scss編集は `npm run sass:build` しないと反映されない）。

### カラー（役割ベース）

| トークンクラス例 | 役割 | 値 |
|---|---|---|
| `bg-primary-600` `text-primary-700` | **Primary**（主アクション・強調）ネイビー | 600=`#2f4d78` |
| `bg-primary-900` | ヘッダー等の濃色 | `#182741` |
| `bg-primary-50` | 淡い強調背景（選択行など） | `#eef2f8` |
| `bg-surface-base` | 基本背景（カード） | `#ffffff` |
| `bg-surface-muted` | 薄い面（フッター・沈み） | `#f8fafc` |
| `bg-surface-sunken` | 沈み面（テーブルヘッダ等） | `#f1f5f9` |
| `text-content` | 本文 | `#1e293b` |
| `text-content-muted` | 補足テキスト | `#64748b` |
| `text-content-subtle` | 微弱（プレースホルダ等） | `#94a3b8` |
| `text-content-inverse` | 反転（濃色背景の上） | `#ffffff` |
| `border-border` | 標準罫線 | `#e2e8f0` |
| `border-border-strong` | 強めの罫線 | `#cbd5e1` |
| `bg-success-* text-success-*` | 成功・完了（緑） | 600=`#16a34a` |
| `bg-warning-* text-warning-*` | 注意（黄） | 600=`#d97706` |
| `bg-error-* text-error-*` | エラー・削除（赤） | 600=`#dc2626` |
| `bg-info-* text-info-*` | 情報（青） | 600=`#2563eb` |

各状態色は `50 / 100 / 500 / 600 / 700` を用意。背景は `-50`/`-100`、文字は `-700`、塗りボタンは `-600`。

### タイポグラフィ

- フォント: `font-sans`（= Noto Sans JP）。見出しに丸ゴシックを使う場合のみ `font-display`（Zen Maru Gothic）。
- 階層の目安:

| 用途 | クラス例 |
|---|---|
| ページ見出し H1 | `text-2xl font-bold text-content`（PageHeaderが担当） |
| セクション見出し H2 | `text-base font-bold text-content` |
| 本文 Body | `text-sm text-content`（最小14px。それ以下は補足のみ） |
| 補足 Caption | `text-xs text-content-muted` |

### 余白・角丸・影（フラット × Bento）

| トークン | 用途 |
|---|---|
| `rounded-card`（0.75rem） | カード・テーブル外枠・フィルター枠 |
| `rounded-md` | ボタン・入力 |
| `rounded-badge`（0.375rem） | バッジ |
| `shadow-card` | カードの標準影（浅い） |
| `shadow-card-hover` | ホバー時 |
| 余白 | 4pxグリッド（`gap-4` `p-6` 等）。カード内 `p-6`、カード間 `gap-4`〜`gap-6`、セクション間 `mb-6`〜`mb-8` |

強い影（`shadow-xl` 等）は原則使わない（フラット方針）。

---

## 2. 共通コンポーネント（`resources/js/Components/UI/`）

> 新規画面は生HTMLではなく**まず共通部品を使う**。不足時は本ディレクトリに追加し、本書に追記する。

| コンポーネント | 用途 | 主なprops |
|---|---|---|
| `UI/Button.vue` | ボタン全般 | `variant`(primary/secondary/danger/ghost/outline), `size`(sm/md/lg), `loading`, `iconLeft`, `iconRight`, `block`, `disabled`, `type` |
| `UI/Card.vue` | Bento基本ブロック | `padding`(none/sm/md/lg) |
| `UI/SectionCard.vue` | 見出し付きカード | `title`, `subtitle`, `padding` ＋ slot: `header`/`actions`/`footer` |
| `UI/Badge.vue` | 状態バッジ | `variant`(neutral/primary/success/warning/error/info), `size` |
| `UI/PageHeader.vue` | 画面上部の見出し＋操作 | `title`, `subtitle` ＋ slot: `actions` |
| `UI/Table.vue` | テーブル外枠（横スクロール対応） | — |
| `UI/TableHeaderCell.vue` | `<th>` | `align`, `sticky` |
| `UI/TableRow.vue` | `<tr>`（**行状態を色で表現**） | `state`(default/selected/success/warning/error/info), `hoverable` |
| `UI/TableDataCell.vue` | `<td>` | `align`, `nowrap` |
| `UI/FilterBar.vue` | 検索・絞り込み枠 | slot: default（グリッド）/`actions` |
| `UI/FormField.vue` | ラベル＋入力＋エラー | `label`, `error`, `required`, `v-model`, `type`, `placeholder`, `id` ／ 内蔵input不要時は default slot |
| `UI/ModalShell.vue` | モーダル定型（ヘッダ/本文/フッタ） | `show`, `title`, `maxWidth`, `closeable` ＋ slot: default/`footer` |
| `UI/Icon.vue` | Material Symbols ラッパ | `name`, `size`(xs/sm/md/lg/xl), `filled` |
| `UI/NavItem.vue` / `UI/SubNavItem.vue` | MainLayoutナビ（内部利用） | `item` |

### 既存 Breeze 部品

`PrimaryButton.vue` / `SecondaryButton.vue` / `DangerButton.vue` は**内部をトークン化済み**（API不変）。既存箇所はそのまま動く。**新規は `UI/Button.vue` を推奨**（variant で使い分け）。

### 使用例

```vue
<script setup>
import PageHeader from '@/Components/UI/PageHeader.vue';
import SectionCard from '@/Components/UI/SectionCard.vue';
import Button from '@/Components/UI/Button.vue';
import Table from '@/Components/UI/Table.vue';
import TableHeaderCell from '@/Components/UI/TableHeaderCell.vue';
import TableRow from '@/Components/UI/TableRow.vue';
import TableDataCell from '@/Components/UI/TableDataCell.vue';
import Badge from '@/Components/UI/Badge.vue';
</script>

<template>
  <MainLayout title="在庫一覧">
    <template #content>
      <PageHeader title="在庫一覧" subtitle="現在の在庫状況">
        <template #actions>
          <Button variant="primary" icon-left="add">在庫追加</Button>
        </template>
      </PageHeader>

      <SectionCard padding="none">
        <Table>
          <thead>
            <tr>
              <TableHeaderCell>品名</TableHeaderCell>
              <TableHeaderCell align="right">数量</TableHeaderCell>
              <TableHeaderCell align="center">状態</TableHeaderCell>
            </tr>
          </thead>
          <tbody>
            <TableRow v-for="s in stocks" :key="s.id" :state="s.low ? 'warning' : 'default'">
              <TableDataCell>{{ s.name }}</TableDataCell>
              <TableDataCell align="right">{{ s.qty }}</TableDataCell>
              <TableDataCell align="center">
                <Badge :variant="s.low ? 'warning' : 'success'">
                  {{ s.low ? '要発注' : '十分' }}
                </Badge>
              </TableDataCell>
            </TableRow>
          </tbody>
        </Table>
      </SectionCard>
    </template>
  </MainLayout>
</template>
```

---

## 3. UI状態のルール

| 状態 | ルール |
|---|---|
| Hover | ボタン/行は色を一段濃く（`hover:bg-*-700` / `hover:bg-surface-muted`） |
| Focus | `focus:ring-2 focus:ring-primary-500`（Buttonが内蔵） |
| Disabled | `disabled:opacity-50 disabled:cursor-not-allowed` |
| Loading | `<Button loading>` でスピナー表示＆二重送信防止 |
| Error（フォーム） | 赤枠だけにせず、`FormField` の `error` に**具体的な文言**を渡す |
| 行の状態 | `TableRow :state` を使う（`bg-green-100` 等の直書き禁止）。色＋バッジ併記 |

---

## 4. レイアウト / レスポンシブ

- 画面は `MainLayout`（`title` prop ＋ `#content` slot）を使う。ヘッダー・サブナビは `Layouts/navigation.js` がデータソース。
- ナビ項目の追加・変更は `navigation.js` を編集する（各リンクにクラスを直書きしない）。
- コンテナは `container mx-auto`。主対象はPC（1024px〜）。テーブルは `UI/Table` が横スクロールを担保。

---

## 5. アイコン

- 新規は **Material Symbols に統一**し、`UI/Icon.vue` 経由で使う（`<Icon name="add" />`）。
- FontAwesome（`fas fa-*`）は**新規使用禁止**。既存分は順次 Material Symbols へ置換し、全撤去後に依存削除。
- 装飾的なインラインSVG（ロゴ等）はそのまま可。

---

## 6. 移行ガイド（既存画面をリファクタする時）

生の直書き → トークン/部品への対応表：

| 旧（直書き） | 新（トークン/部品） |
|---|---|
| `bg-blue-500` `bg-blue-600`（主アクション） | `bg-primary-600` |
| `bg-gray-700` `bg-gray-800`（ヘッダ濃色） | `bg-primary-900` |
| `text-gray-900` | `text-content` |
| `text-gray-500` | `text-content-muted` |
| `bg-gray-100`（テーブルヘッダ等） | `bg-surface-sunken` |
| `border-gray-200` | `border-border` |
| `<th class="px-4 py-3 ... bg-gray-100">` | `<TableHeaderCell>` |
| `<tr class="bg-green-100">` / inline `style="background:#ffabab"` | `<TableRow state="success">` 等 |
| 生 `<button class="...">` | `<Button variant="...">` |
| 生 `<input>` ＋ label ＋ error | `<FormField>` |

**進め方（非破壊の3段）**: 加算（トークン/部品を用意）→ 画面単位で置換 → 旧削除。一括 sed 置換は禁止、画面ごとにレビュー。

---

## 7. 新規画面の依頼テンプレート（AIに渡す用）

```
Akioka Web Application の新規画面をデザイン・実装してください。
本システムのデザインシステム（doc/design-system.md）に厳密に従うこと。

## 画面
画面名 / 目的 / 主な操作：

## 遵守事項
- スタイル: コーポレート × フラット × Bento UI / 白背景 × ネイビー基調
- 色・余白・角丸・影は tailwind.config.js のトークン経由のみ（生の blue-*/gray-*・inline style 禁止）
- レイアウトは MainLayout + #content。見出しは UI/PageHeader
- 情報のまとまりは UI/SectionCard、一覧は UI/Table系、操作は UI/Button、状態は UI/Badge/TableRow :state
- フォームは UI/FormField（エラーは具体的文言）、アイコンは UI/Icon（Material Symbols）
- 日本語UIに uppercase/tracking-widest を使わない。色だけで状態を伝えない
- 避ける印象: チープ・派手すぎ・ごちゃごちゃ・子どもっぽい
```

---

## 変更履歴
- 2026-07-08 初版（Design Token / 共通UI / MainLayout刷新に合わせて策定）
