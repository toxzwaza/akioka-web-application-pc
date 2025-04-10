<script setup>
import { watch, ref } from "vue";

const props = defineProps({
  current_month_holidays: Array,
  next_month_holidays: Array,
  order: Object,
});

const shortest = ref(false);

function printElement() {
  // 納入希望日が入力されていない場合最短とする
  if (!props.order.desired_delivery_date) {
    shortest.value = true;
  }

  setTimeout(() => {
    // 印刷用のiframeを作成
    const iframe = document.createElement("iframe");
    iframe.style.display = "none";
    document.body.appendChild(iframe);

    // iframeのドキュメントに現在のページのスタイルをコピー
    const styleSheets = Array.from(document.styleSheets);
    const styles = styleSheets
      .map((sheet) => {
        try {
          return Array.from(sheet.cssRules)
            .map((rule) => rule.cssText)
            .join("\n");
        } catch (e) {
          return "";
        }
      })
      .join("\n");

    // 印刷したい要素を取得
    const printContent = document.getElementById("purchase_container");

    // iframeのドキュメントに内容を書き込む
    iframe.contentDocument.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <style>${styles}</style>
        </head>
        <body>
            ${printContent.outerHTML}
        </body>
        </html>
    `);

    iframe.contentDocument.close();

    // 印刷
    iframe.contentWindow.focus();
    setTimeout(() => {
      iframe.contentWindow.print();
    }, 500); // 500ミリ秒の待機時間を追加

    // 印刷後にiframeを削除
    setTimeout(() => {
      document.body.removeChild(iframe);
    }, 1000);
  }, 500);
}

// emitを定義
const emit = defineEmits(["update-delivery-date"]);

// 日付変更時のハンドラー
const handleDateChange = (value) => {
  emit("update-delivery-date", value);
};

// orderの変更を監視
watch(
  () => props.order,
  (newOrder, oldOrder) => {
    console.log("Order updated:", newOrder);
  },
  { deep: true }
); // オブジェクトのネストされたプロパティの変更も検知
</script>
<template>
  <div v-if="order">
    <div id="purchase_container" class="p-4">
      <button
        id="print_button"
        @click="printElement"
        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
      >
        印刷 & FAX
      </button>

      <h1 class="text-center font-bold text-2xl py-4 mb-4">注文書</h1>

      <div id="top_content" class="flex justify-around items-center">
        <div class="left_container">
          <h2 class="font-bold text-xl">{{ order.com_name }} 御中</h2>
          <div class="number">
            <p>TEL: {{ order.tel }}</p>
            <p>FAX: {{ order.fax }}</p>
          </div>
          <p class="text-sm font-serif mt-2">
            お世話になります。<br />
            注文をお願いします。<br />
            納期に間に合わない場合は、<br />事前に納期回答をお願いいたします。<br />
          </p>
        </div>
        <div class="center_container text-center p-4">
          <h3 class="font-bold text-xl mb-4">休業日</h3>
          <p class="font-bold">
            {{ new Date().getMonth() + 1 }}月
            <span
              class="font-normal"
              v-for="currentMonth in current_month_holidays"
              :key="currentMonth.id"
              >{{ new Date(currentMonth.date).getDate() }},</span
            >日
          </p>
          <p class="font-bold">
            {{ new Date().getMonth() + 2 }}月
            <span
              class="font-normal"
              v-for="nextMonth in next_month_holidays"
              :key="nextMonth.id"
              >{{ new Date(nextMonth.date).getDate() }},</span
            >日
          </p>
        </div>
        <div class="right_container">
          <p class="tracking-wider font-semibold">株式会社アキオカ</p>
          <p class="tracking-wider">〒713-8103</p>
          <p class="tracking-wider">倉敷市玉島乙島8252-35</p>
          <div class="pl-2 mt-1">
            <p class="tracking-wider text-sm">TEL:086-522-7686</p>
            <p class="tracking-wider text-sm">FAX:086-522-7674</p>
            <p class="tracking-wider text-sm">発注者：{{ order.user_name }}</p>
          </div>
        </div>
      </div>
      <div id="main_content" class="mt-4">
        <table class="table-auto w-full">
          <thead>
            <tr>
              <th class="px-4 py-2 text-gray-700">注文No</th>
              <th class="px-4 py-2 text-gray-700">品名</th>
              <th class="px-4 py-2 text-gray-700">品番</th>
              <th class="px-4 py-2 text-gray-700">納入場所</th>
              <th class="px-4 py-2 text-gray-700">納入希望日</th>
              <th class="px-4 py-2 text-gray-700">数量</th>
              <th class="px-4 py-2 text-gray-700">単価</th>
              <th class="px-4 py-2 text-gray-700">金額(税抜価格)</th>
              <th class="px-4 py-2 text-gray-700">注文指示者</th>
            </tr>
          </thead>
          <tbody>
            <tr class="">
              <td class="text-center border px-1 py-5">{{ order.order_no }}</td>
              <td class="text-center border px-1 py-5">{{ order.name }}</td>
              <td class="text-center border px-1 py-5">{{ order.s_name }}</td>
              <td class="text-center border px-1 py-5">
                {{ order.deli_location }}
              </td>
              <td class="text-center border px-1 py-5">
                <input
                  v-if="!shortest"
                  class="p-0 border-transparent"
                  type="date"
                  @change="handleDateChange($event.target.value)"
                  :value="order.desired_delivery_date"
                />
                <p v-else @click="shortest = false" class="font-bold">最短</p>
              </td>
              <td class="text-center border px-1 py-5">{{ order.quantity }}</td>
              <td class="text-center border px-1 py-5">
                {{ order.price.toLocaleString() }}
              </td>
              <td class="text-center border px-1 py-5">
                {{ order.calc_price.toLocaleString() }}
              </td>
              <td class="text-center border px-1 py-5">
                {{ order.order_user }}
              </td>
            </tr>
            <tr class="bg-gray-100">
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
            </tr>
            <tr class="">
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
            </tr>
            <tr class="bg-gray-100">
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
            </tr>
            <tr class="">
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
            </tr>
            <tr class="bg-gray-100">
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
              <td class="text-center border px-1 py-5"></td>
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
    </div>
  </div>
</template>
<style scoped lang="scss">
#purchase_container {
  background-color: white;
  margin: 0 auto;
  width: 297mm;
  height: 210mm;
  box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
  border: 1px solid rgba(221, 221, 221, 0.705);
  position: relative;

  & #print_button {
    position: absolute;
    right: 5mm;
    top: 5mm;
    width: 30mm;
  }

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

