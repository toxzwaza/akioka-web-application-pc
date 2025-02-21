<script setup>
import { onMounted, ref } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
  order_request: Object,
});

const accept_status = ref(false);
const close_status = ref(false);

const confirmAccept = () => {
  const accept_flg = accept_status.value === "accept" ? 2 : 3;

  axios
    .post(route("stock.accept.store"), {
      order_request_id: props.order_request.order_request_id,
      accept_flg: accept_flg,
    })
    .then((res) => {
      console.log(res.data);
      if (res.data.status) {
        if (confirm("承認結果を送信しました。")) {
          close_status.value = true;
        }
      }
    })
    .catch((error) => {
      console.log(error);
    });
};
const handleAcceptStatus = (status) => {
  accept_status.value = status;
  console.log(accept_status.value);
};

onMounted(() => {
  console.log(props.order_request);
});
</script>
<template>
  <div v-if="!close_status">
    <div class="m-8">
      <div>
        <p class="mb-4">
          注文書内容が正しいことを確認し、以下の<span
            class="font-bold text-blue-500"
            >承認ボタン</span
          >から承認してください。
        </p>
      </div>

      <div class="flex items-center flex-start mb-8">
        <div class="mr-8 text-lg">
          <label
            for="accept"
            :class="{
              'text-2xl font-bold text-green-400': accept_status === 'accept',
            }"
            :style="{ transition: 'all 0.3s ease' }"
            >承認</label
          >
          <input
            type="radio"
            name="status"
            id="accept"
            value="accept"
            v-model="accept_status"
            :class="{ 'ml-2': true }"
            @change="handleAcceptStatus('accept')"
          />
        </div>
        <div class="mr-8 text-lg">
          <label
            for="reject"
            :class="{
              'text-2xl font-bold text-red-400': accept_status === 'reject',
            }"
            :style="{ transition: 'all 0.3s ease' }"
            >却下</label
          >
          <input
            type="radio"
            name="status"
            id="reject"
            value="reject"
            v-model="accept_status"
            :class="{ 'ml-2': true }"
            @change="handleAcceptStatus('reject')"
            :style="{ transition: 'all 0.3s ease' }"
          />
        </div>

        <button
          v-if="accept_status"
          @click="confirmAccept"
          class="ml-4 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
        >
          確定
        </button>
      </div>
    </div>

    <hr class="my-8" />

    <div class="flex items-start">
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
                <td class="text-center border px-4 py-5">
                  {{ props.order_request.request_user_name }}
                </td>
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
          <textarea class="w-1/2" name="" id="" cols="30" rows="10"></textarea>

          <div class="details w-1/2 pl-4">
            <p class="text-sm font-serif">
              納期に問題があればその旨の記載をお願いします。<br />
              受信後３日以内に確認印を押印し返信をお願いします。 10:00-10:10,
              12:00-12:50, 15:00-15:10の間の納品はご遠慮願います。<br />
              上記時間での納品になる場合は事前にご相談願います。
            </p>
          </div>
        </div>
        <img
          id="accept_stamp"
          v-if="accept_status === 'accept'"
          src="/img/base/akioka_stamp.png"
          alt=""
        />
      </div>
      <div class="ml-4 w-80">
        <span class="text-gray-700 text-sm">物品画像</span>
        <img
          :src="
            props.order_request.stock_img_path &&
            props.order_request.stock_img_path.includes('storage')
              ? `https://akioka.cloud/${props.order_request.stock_img_path}`
              : props.order_request.stock_img_path
          "
          alt="サンプル画像"
        />
      </div>
    </div>
  </div>
  <div v-else class="p-10">
    <h1 class="text-gray-700 font-bold">承認登録が完了しました。この画面は閉じていただいて構いません。</h1>
    <img style="width:40%;" src="/img/base/thankyou.gif" alt="">
  </div>
</template>
<style scoped lang="scss">
#purchase_container {
  width: 297mm;
  height: 210mm;
  position: relative;

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

  & #accept_stamp {
    position: absolute;
    top: 20mm;
    right: 40mm;
    width: 30mm;
    height: 30mm;
  }
}
</style>
