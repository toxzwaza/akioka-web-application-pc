<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import PageHeader from "@/Components/UI/PageHeader.vue";
import SectionCard from "@/Components/UI/SectionCard.vue";
import Button from "@/Components/UI/Button.vue";
</script>

<template>
  <MainLayout :title="'棚卸しデータ出力'">
    <template #content>
      <div class="container mx-auto">
        <PageHeader
          title="棚卸しデータ出力"
          subtitle="棚卸しに使用するCSVデータをダウンロードします。"
        />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <SectionCard title="棚卸しデータ（格納先別在庫）">
            <p class="text-sm text-content-subtle mb-4">
              全物品の格納先・在庫数の一覧です。格納先未登録の物品も末尾に含まれます（該当列は空欄）。UTF-8（BOM付き）出力。
            </p>
            <a :href="route('stock.stock_count.export_csv')">
              <Button variant="primary" icon-left="download">
                CSVダウンロード
              </Button>
            </a>
          </SectionCard>

          <SectionCard title="物品マスタ（手配先・単価）">
            <p class="text-sm text-content-subtle mb-4">
              手配先（メイン優先で1物品1行）・単価・単位・換算値付きの物品マスタです。価格未設定・手配先未登録の物品は含みません。Shift_JIS出力（Windows Excel向け）。
            </p>
            <a :href="route('stock.stock_count.export_master_csv')">
              <Button variant="primary" icon-left="download">
                CSVダウンロード
              </Button>
            </a>
          </SectionCard>
        </div>
      </div>
    </template>
  </MainLayout>
</template>
