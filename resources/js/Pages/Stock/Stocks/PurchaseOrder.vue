<script setup>
import { onMounted, ref } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
  order_request: Object,
});

const print_status = ref(false);

const faxPurchaseOrder = () => {
  print_status.value = !print_status.value;

  setTimeout(() => {
    window.print();
    print_status.value = !print_status.value;
  }, 500);
};
onMounted(() => {
  console.log(props.order_request);
});
</script>
<template>
  <div v-if="!print_status" class="m-8">
    <div>
      <p class="mb-4">注文書内容が正しいことを確認し、以下の<span class="font-bold text-blue-500">発注ボタン</span>からFAXを送信してください。</p>
    </div>

    <button
      @click="faxPurchaseOrder"
      class="mr-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
    >
      発注
    </button>
    <Link
      class="inline-block bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
      :href="route('stock.order_requests')"
    >
      発注依頼一覧へ戻る
    </Link>
  </div>

  <div id="purchase_container" class="p-4">
    <h1 class="text-center font-bold text-2xl py-4 mb-4">注文書</h1>

    <div id="top_content" class="flex justify-around items-center">
      <div class="left_container">
        <h2 class="font-bold text-xl">
          {{ props.order_request.supplier_name }} 御中
        </h2>
        <div class="number">
          <p>TEL: {{ props.order_request.supplier_tel }}</p>
          <p>FAX: {{ props.order_request.supplier_fax }}</p>
        </div>
        <p class="text-sm font-serif mt-2">
          お世話になります。<br />
          注文をお願いします。<br />
          納期に間に合わない場合は、<br />事前に納期回答をお願いいたします。<br />
        </p>
      </div>
      <div class="center_container text-center p-4">
        <h3 class="font-bold text-xl mb-4">休業日</h3>
        <p>
          1月1-5, 11-12,18-19,25-26日<br />
          2月1-2, 8-9日, 15-16日, 22-23日
        </p>
      </div>
      <div class="right_container">
        <p class="tracking-wider font-semibold">株式会社アキオカ</p>
        <p class="tracking-wider">〒713-8103</p>
        <p class="tracking-wider">倉敷市玉島乙島8252-35</p>
        <div class="pl-2 mt-1">
          <p class="tracking-wider text-sm">TEL:086-522-7686</p>
          <p class="tracking-wider text-sm">FAX:086-522-7674</p>
          <p class="tracking-wider text-sm">
            注文発注者：{{ props.order_request.user_name }}
          </p>
        </div>
      </div>
    </div>
    <div id="main_content" class="mt-4">
      <table class="table-auto w-full">
        <thead>
          <tr>
            <th class="px-4 py-2 text-gray-700">品名</th>
            <th class="px-4 py-2 text-gray-700">品番</th>
            <th class="px-4 py-2 text-gray-700">社内在庫数</th>
            <th class="px-4 py-2 text-gray-700">在庫消化予定日</th>
            <th class="px-4 py-2 text-gray-700">数量</th>
            <th class="px-4 py-2 text-gray-700">単価</th>
            <th class="px-4 py-2 text-gray-700">金額(税抜価格)</th>
            <th class="px-4 py-2 text-gray-700">注文指示者</th>
          </tr>
        </thead>
        <tbody>
          <tr class="">
            <td class="text-center border px-4 py-5">
              {{ props.order_request.stock_name }}
            </td>
            <td class="text-center border px-4 py-5">
              {{ props.order_request.stock_s_name ?? "-" }}
            </td>
            <td class="text-center border px-4 py-5">-</td>
            <td class="text-center border px-4 py-5">-</td>
            <td class="text-center border px-4 py-5">
              {{ props.order_request.quantity }}
            </td>
            <td class="text-center border px-4 py-5">
              {{ props.order_request.price.toLocaleString() }}
            </td>
            <td class="text-center border px-4 py-5">
              {{
                (
                  props.order_request.quantity * props.order_request.price
                ).toLocaleString()
              }}
            </td>
            <td class="text-center border px-4 py-5"></td>
          </tr>
          <tr class="bg-gray-100">
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
          </tr>
          <tr class="">
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
          </tr>
          <tr class="bg-gray-100">
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
          </tr>
          <tr class="">
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
          </tr>
          <tr class="bg-gray-100">
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
          </tr>
          <tr class="">
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
            <td class="text-center border px-4 py-5"></td>
          </tr>
        </tbody>
      </table>
    </div>
    <div id="bottom_content" class="mt-6 flex items-start justify-between">
      <textarea class="w-1/2" name="" id="" cols="30" rows="10">
(備考)</textarea
      >

      <div class="details w-1/2 pl-4">
        <p class="text-sm font-serif">
          納期に問題があればその旨の記載をお願いします。<br />
          受信後３日以内に確認印を押印し返信をお願いします。 10:00-10:10,
          12:00-12:50, 15:00-15:10の間の納品はご遠慮願います。<br />
          上記時間での納品になる場合は事前にご相談願います。
        </p>
      </div>
    </div>
  </div>
</template>
<style scoped lang="scss">
#purchase_container {
  width: 297mm;
  height: 210mm;
  box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
  border: 1px solid rgba(221, 221, 221, 0.705);
  & #top_content {
    & > div {
      width: 30%;
    }

    & .center_container {
      border: 1px solid rgba(82, 82, 82, 0.555);
      border-radius: 3px;
    }
  }

  & #bottom_content {
    height: 16%;
    & textarea {
      height: 100%;
      border: 1px solid rgba(82, 82, 82, 0.555);
      border-radius: 3px;
    }
  }
}
</style>
