<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import PageHeader from "@/Components/UI/PageHeader.vue";
import SectionCard from "@/Components/UI/SectionCard.vue";
import Table from "@/Components/UI/Table.vue";
import TableHeaderCell from "@/Components/UI/TableHeaderCell.vue";
import TableRow from "@/Components/UI/TableRow.vue";
import TableDataCell from "@/Components/UI/TableDataCell.vue";
import Badge from "@/Components/UI/Badge.vue";
import Button from "@/Components/UI/Button.vue";
import { ref, onMounted, reactive } from "vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
  user_id: Number,
  user_name: String,
  stocks: Array,
  retained_stocks: Array,
});

const checkImg = (imgPath) => {
  modalImg.imgPath = imgPath.includes("https") ? imgPath : "/" + imgPath;
  modalImg.status = true;
  console.log(modalImg);
};

const changeSelect = (stock_id, value) => {
  router.post(route("stock.store.retained.stocks"), {
    stock_id: stock_id,
    treat_id: value,
    user_id: props.user_id,
  });
};
const changeModal = () => {
  (modalImg.status = null), (modalImg.imgPath = null);
};

const modalImg = reactive({
  status: null,
  imgPath: null,
});
onMounted(() => {});
</script>

<template>
  <MainLayout :title="'滞留品通達'">
    <template #content>
      <PageHeader
        title="滞留品処遇決定"
        subtitle="滞留品の処遇を決定してください。"
      />

      <SectionCard padding="md" class="mb-6">
        <div class="space-y-3 text-sm text-content leading-relaxed">
          <p>
            以下の表のセレクトボックスより、<span
              class="font-bold text-error-700"
              >「廃棄」</span
            >又は<span class="font-bold text-success-700"
              >「現場引き取り」</span
            >を選択してください。
          </p>
          <p>
            滞留品の処遇決定は、今後継続的(毎月)に行われますが、本件以降は発注時に登録した管理部署の課長にのみ表示されることとなります。
          </p>
          <p class="text-xs text-error-600">
            *今回全課長に送信しているのは、品証二階へ移動させた物品の管理部署が不明な為です。備品倉庫に継続的に置くことはできません。
          </p>
        </div>
      </SectionCard>

      <div class="mb-4 flex items-center gap-1.5 text-sm text-content-muted">
        <span class="font-semibold text-content">{{ props.user_name }}</span>
        さんがログイン中。
      </div>

      <div class="mb-8">
        <Table>
          <thead>
            <tr>
              <TableHeaderCell>id</TableHeaderCell>
              <TableHeaderCell>画像</TableHeaderCell>
              <TableHeaderCell>品名</TableHeaderCell>
              <TableHeaderCell align="right">価格</TableHeaderCell>
              <TableHeaderCell align="right">個数</TableHeaderCell>
              <TableHeaderCell align="right">金額</TableHeaderCell>
              <TableHeaderCell>処遇</TableHeaderCell>
            </tr>
          </thead>
          <tbody>
            <TableRow v-for="stock in props.stocks" :key="stock.id">
              <TableDataCell nowrap>{{ stock.id }}</TableDataCell>
              <TableDataCell>
                <img
                  @click="checkImg(stock.img_path)"
                  class="w-16 rounded cursor-pointer"
                  :src="
                    stock.img_path.includes('https')
                      ? stock.img_path
                      : '/' + stock.img_path
                  "
                  alt=""
                />
              </TableDataCell>
              <TableDataCell>{{ stock.name }}</TableDataCell>
              <TableDataCell align="right" nowrap>@ {{ stock.price }}</TableDataCell>
              <TableDataCell align="right" nowrap>{{ stock.quantity }}</TableDataCell>
              <TableDataCell align="right" nowrap>
                = {{ (stock.price * stock.quantity).toLocaleString() }}円
              </TableDataCell>
              <TableDataCell>
                <select
                  class="rounded-md border-border shadow-sm text-sm focus:outline-none focus:border-primary-500 focus:ring-primary-500"
                  name=""
                  id="treatSelect"
                  @change="changeSelect(stock.id, $event.target.value)"
                >
                  <option value="0">選択してください。</option>
                  <option value="1">廃棄</option>
                  <option value="2">現場引き取り</option>
                  <hr class="my-2" />
                  <option value="3">一課受け取り依頼</option>
                  <option value="4">二課受け取り依頼</option>
                  <option value="5">品証受け取り依頼</option>
                </select>
              </TableDataCell>
            </TableRow>
          </tbody>
        </Table>
      </div>

      <h2 class="mb-4 text-base font-bold text-content">
        滞留品 所在決定済み
      </h2>
      <Table>
        <thead>
          <tr>
            <TableHeaderCell>id</TableHeaderCell>
            <TableHeaderCell>画像</TableHeaderCell>
            <TableHeaderCell>品名</TableHeaderCell>
            <TableHeaderCell>処遇</TableHeaderCell>
            <TableHeaderCell>決定者</TableHeaderCell>
          </tr>
        </thead>
        <tbody>
          <TableRow
            v-for="retained_stock in props.retained_stocks"
            :key="retained_stock.id"
          >
            <TableDataCell nowrap>{{ retained_stock.id }}</TableDataCell>
            <TableDataCell>
              <img
                @click="checkImg(retained_stock.img_path)"
                class="w-16 rounded cursor-pointer"
                :src="
                  retained_stock.img_path.includes('https')
                    ? retained_stock.img_path
                    : '/' + retained_stock.img_path
                "
                alt=""
              />
            </TableDataCell>
            <TableDataCell>{{ retained_stock.name }}</TableDataCell>
            <TableDataCell>
              <Badge
                :variant="
                  {
                    1: 'error',
                    2: 'success',
                    3: 'warning',
                    4: 'info',
                    5: 'primary',
                  }[retained_stock.treat_id] || 'neutral'
                "
              >
                {{
                  retained_stock.treat_id == 1
                    ? "廃棄"
                    : retained_stock.treat_id == 2
                    ? "現場引き取り"
                    : retained_stock.treat_id == 3
                    ? "一課受け取り依頼"
                    : retained_stock.treat_id == 4
                    ? "二課受け取り依頼"
                    : retained_stock.treat_id == 5
                    ? "品証受け取り依頼"
                    : ""
                }}
              </Badge>
            </TableDataCell>
            <TableDataCell>{{ retained_stock.user_name }}</TableDataCell>
          </TableRow>
        </tbody>
      </Table>
    </template>
  </MainLayout>

  <div v-if="modalImg.status" id="img_modal">
    <div @click="changeModal" id="img_container" class="">
      <Button variant="secondary" @click="changeModal">閉じる</Button>
      <img :src="modalImg.imgPath" alt="" />
    </div>
  </div>
</template>



<style>
#img_modal {
  position: fixed;
  top: 0;
  height: 100vh;
  width: 100vw;
}
#img_modal #img_container {
  position: relative;
  height: 100%;
  width: 100%;
  background-color: rgba(0, 0, 0, 0.734);
}
#img_modal #img_container button {
  position: absolute;
  top: 4%;
  left: 84%;
}

#img_modal #img_container img {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  height: 80%;
  width: 80%;
  object-fit: contain;
}
</style>