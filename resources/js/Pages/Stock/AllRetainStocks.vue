<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import PageHeader from "@/Components/UI/PageHeader.vue";
import SectionCard from "@/Components/UI/SectionCard.vue";
import Table from "@/Components/UI/Table.vue";
import TableHeaderCell from "@/Components/UI/TableHeaderCell.vue";
import TableRow from "@/Components/UI/TableRow.vue";
import TableDataCell from "@/Components/UI/TableDataCell.vue";
import Button from "@/Components/UI/Button.vue";
import { ref, onMounted, reactive } from "vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
  retained_stocks: Array,
});
const param = ref({});
props.retained_stocks.forEach((stock) => {
  param.value[stock.id] = null;
});

const decisionTreat = (stock_id, retain_lists) => {
  for (let list of retain_lists) {
    if (list.treat_name != "廃棄") {
      return "";
    }
  }
  addParam(stock_id, "1");
  return "廃棄";
};

onMounted(() => {});
const checkImg = (imgPath) => {
  modalImg.imgPath = imgPath.includes("https") ? imgPath : "/" + imgPath;
  modalImg.status = true;
};
const changeModal = () => {
  (modalImg.status = null), (modalImg.imgPath = null);
};
const modalImg = reactive({
  status: null,
  imgPath: null,
});

const sendTreat = () => {
  if (checkSelect()) {
    router.post(route("stock.store.last_retained.stocks"), {
      treat_lists: param.value,
    });
  } else {
    alert("未選択項目があります。");
  }
};
const checkSelect = () => {
  let isSuccess = true;
  const selectEls = document.querySelectorAll(".treat_select");
  selectEls.forEach((el) => {
    if (el.value == 0) {
      console.log("未選択項目あり。");

      isSuccess = false;
      return isSuccess;
    }
  });
  return isSuccess;
};
const addParam = (stock_id, el) => {
  if (!el || !el.classList) {
    console.log("Invalid element:", el);
    return;
  }
  param.value[stock_id] = el.value;
  switch (el.value) {
    case "1":
      el.classList.add("border-2", "border-red-500");
      break;
    case "2":
      el.classList.add("border-2", "border-orange-500");
      break;
    case "3":
      el.classList.add("border-2", "border-blue-500");
      break;
    case "4":
      el.classList.add("border-2", "border-purple-500");
      break;
    default:
      el.classList.add("border-2", "border-gray-500");
      break;
  }

  console.log(param.value);
};
</script>

<template>
  <MainLayout :title="'滞留品通達'">
    <template #content>
      <PageHeader
        title="最終滞留品処遇決定"
        subtitle="滞留品の処遇を決定してください。"
      >
        <template #actions>
          <Button variant="primary" icon-left="check" @click="sendTreat">
            確定する
          </Button>
        </template>
      </PageHeader>

      <SectionCard padding="md" class="mb-6">
        <div class="space-y-3 text-sm text-content leading-relaxed">
          <p>
            滞留品の処遇決定は、今後継続的(毎月)に行われますが、本件以降は発注時に登録した管理部署の課長にのみ表示されることとなります。
          </p>
          <p class="text-xs text-error-600">
            *今回全課長に送信しているのは、品証二階へ移動させた物品の管理部署が不明な為です。備品倉庫に継続的に置くことはできません。
          </p>
        </div>
      </SectionCard>

      <Table>
        <thead>
          <tr>
            <TableHeaderCell>id</TableHeaderCell>
            <TableHeaderCell>画像</TableHeaderCell>
            <TableHeaderCell>品名</TableHeaderCell>
            <TableHeaderCell>一課</TableHeaderCell>
            <TableHeaderCell>二課</TableHeaderCell>
            <TableHeaderCell>品証</TableHeaderCell>
            <TableHeaderCell>最終処遇</TableHeaderCell>
          </tr>
        </thead>
        <tbody>
          <TableRow v-for="stock in props.retained_stocks" :key="stock.id">
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
            <TableDataCell>
              {{
                stock.retain_lists.find((list) => list.user_id === 37)
                  ?.treat_name
              }}
            </TableDataCell>
            <TableDataCell>
              {{
                stock.retain_lists.find((list) => list.user_id === 84)
                  ?.treat_name
              }}
            </TableDataCell>
            <TableDataCell>
              {{
                stock.retain_lists.find((list) => list.user_id === 16)
                  ?.treat_name
              }}
            </TableDataCell>
            <TableDataCell>
              <select
                @change="addParam(stock.id, $event.target)"
                v-if="decisionTreat(stock.id, stock.retain_lists)"
                :class="{
                  'treat_select border-2 border-red-500 rounded-md shadow-sm text-sm focus:outline-none focus:border-primary-500 focus:ring-primary-500': true,
                }"
              >
                <option selected value="1">廃棄</option>
                <option value="2">一課引き取り</option>
                <option value="3">二課引き取り</option>
                <option value="4">品証引き取り</option>
              </select>

              <select
                @change="addParam(stock.id, $event.target)"
                :class="{
                  'treat_select rounded-md border-border shadow-sm text-sm focus:outline-none focus:border-primary-500 focus:ring-primary-500': true,
                }"
                v-else
              >
                <option value="0">未選択</option>
                <option value="1">廃棄</option>
                <option value="2">一課引き取り</option>
                <option value="3">二課引き取り</option>
                <option value="4">品証引き取り</option>
              </select>
            </TableDataCell>
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