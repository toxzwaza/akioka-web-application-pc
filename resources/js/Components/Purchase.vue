<script setup>
import { watch, ref, onMounted } from "vue";
import domtoimage from "dom-to-image";
import axios from "axios";

const props = defineProps({
  current_month_holidays: Array,
  next_month_holidays: Array,
  orders: Array,
});

const shortest = ref(false);
const description = ref("");
const calc_postage = ref(0); //送料合計

function printElement() {
  // 納入希望日が入力されていない場合最短とする
  props.orders.forEach((order) => {
    if (!order.desired_delivery_date) {
      order.shortest = true;
    }
  });

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

async function saveAsImage() {
  // 納入希望日が入力されていない場合最短とする
  props.orders.forEach((order) => {
    if (!order.desired_delivery_date) {
      order.shortest = true;
    }
  });

  const element = document.getElementById("purchase_container");
  const timestamp = Date.now();
  const filename = `${timestamp}.png`;

  try {
    // mx-autoクラスを一時的に削除
    element.classList.remove("mx-auto");

    // 要素を画像に変換
    const dataUrl = await domtoimage.toPng(element, {
      quality: 1.0,
      bgcolor: "#ffffff",
      style: {
        transform: "scale(1)",
        "transform-origin": "top left",
      },
    });

    // mx-autoクラスを元に戻す
    element.classList.add("mx-auto");

    // 画像データをサーバーに送信
    const response = await axios.post("/order-request/save-pdf", {
      pdfData: dataUrl,
      filename: filename,
      orders: props.orders.map((order) => order.id),
    });

    if (response.data.status) {
      alert("画像が正常に保存されました");
    } else {
      throw new Error(response.data.message);
    }
  } catch (error) {
    // エラー時もmx-autoクラスを元に戻す
    element.classList.add("mx-auto");
    console.error("Error saving image:", error);
    alert("画像の保存に失敗しました");
  }
}

// emitを定義
const emit = defineEmits(["update-delivery-date"]);

// 納入希望日 変更時のハンドラー
const handleDateChange = (value) => {
  emit("update-delivery-date", value);
};

// 送料を計算
const calculatePostage = () => {
  // ordersの中のpostageを合計
  calc_postage.value = props.orders.reduce(
    (sum, order) => sum + (order.postage || 0),
    0
  );
};

onMounted(() => {
  calculatePostage();
});
</script>
<template>
  <div v-if="orders.length === 1 && orders[0].purchase_path">
    <img
      class="w-2/3 mt-8 mx-auto"
      :src="`/storage/${orders[0].purchase_path}`"
      alt="発注書"
    />
  </div>

  <div v-else>
    <div class="mx-auto mb-6" id="description_area">
      <span class="font-bold text-gray-700">備考入力エリア</span>
      <textarea
        class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
        name=""
        id=""
        cols="30"
        rows="10"
        v-model="description"
      ></textarea>
    </div>

    <div id="purchase_container" class="mx-auto p-4">
      <div class="flex gap-2">
        <button
          id="print_button"
          @click="printElement"
          class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
        >
          印刷 & FAX
        </button>
        <button
          @click="saveAsImage"
          class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
        >
          発注書保存
        </button>
      </div>

      <h1 class="text-center font-bold text-2xl py-4 mb-4">注文書</h1>

      <div id="top_content" class="flex justify-around items-center">
        <div class="left_container">
          <h2 class="font-bold text-xl">{{ orders[0].com_name }} 御中</h2>
          <div class="number">
            <p>TEL: {{ orders[0].tel }}</p>
            <p>FAX: {{ orders[0].fax }}</p>
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
            <p class="tracking-wider text-sm">
              発注者：{{ orders[0].manage_user_name }}
            </p>
          </div>
        </div>
      </div>
      <div id="main_content" class="">
        <table class="table-auto w-full">
          <thead>
            <tr>
              <th class="order_no px-4 py-2 text-gray-700">注文No</th>
              <th class="name px-4 py-2 text-gray-700">品名</th>
              <th class="s_name px-4 py-2 text-gray-700">品番</th>
              <th class="deli_location px-4 py-2 text-gray-700">納入場所</th>
              <th class="desired_date px-4 py-2 text-gray-700">納入希望日</th>
              <th class="quantity px-4 py-2 text-gray-700">数量</th>
              <th class="price px-4 py-2 text-gray-700">単価</th>
              <th class="calc_price px-4 py-2 text-gray-700">金額(税抜価格)</th>
              <th class="order_user px-4 py-2 text-gray-700">注文指示者</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in orders" :key="order.id" class="">
              <td class="order_no text-center border">{{ order.order_no }}</td>
              <td class="name text-center border">{{ order.name }}</td>
              <td class="s_name text-center border">{{ order.s_name }}</td>
              <td class="deli_location text-center border">
                {{ order.deli_location }}
              </td>
              <td class="desired_date text-center border">
                <p
                  v-if="order.shortest"
                  @click="shortest = false"
                  class="font-bold"
                >
                  最短
                </p>

                <input
                  v-else-if="order.desired_delivery_date"
                  class="p-0 border-transparent"
                  type="date"
                  :value="order.desired_delivery_date"
                />
                <input
                  v-else-if="!order.desired_delivery_date"
                  class="p-0 border-transparent"
                  type="date"
                  :value="order.desired_delivery_date"
                />
              </td>
              <td class="quantity text-center border">{{ order.quantity }}</td>
              <td class="price text-center border">
                {{ order.price.toLocaleString() }}
              </td>
              <td class="calc_price text-center border">
                {{ order.calc_price.toLocaleString() }}
              </td>
              <td class="order_user text-center border">
                {{ order.order_user }}
              </td>
            </tr>

            <tr>
              <td colspan="7" class="text-right border">送料</td>
              <td class="text-center border">
                {{ calc_postage.toLocaleString() }}
              </td>
              <td class="border"></td>
            </tr>

            <tr>
              <td colspan="7" class="text-right border">合計（税抜）</td>
              <td class="text-center border">
                {{
                  (
                    props.orders.reduce(
                      (sum, order) => sum + order.calc_price,
                      0
                    ) + calc_postage
                  ).toLocaleString()
                }}
              </td>
              <td class="border"></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div id="bottom_content" class="mt-6 flex items-start justify-between">
        <!-- <textarea
          class="w-1/2"
          name=""
          id=""
          cols="30"
          rows="10"
          v-model="description"
        ></textarea> -->
        <div class="textarea">
          <div class="text font-bold">
            備考

            <p v-html="description.replace(/\n/g, '<br>')"></p>
          </div>
        </div>

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
#description_area {
  width: 297mm;
}

#purchase_container {
  background-color: white;
  width: 297mm;
  height: 210mm;
  box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
  border: 1px solid rgba(221, 221, 221, 0.705);
  position: relative;

  & #main_content {
    margin-top: 4mm;

    & table {
      width: 100%;
      border-collapse: collapse;
      table-layout: fixed;

      & thead,
      tbody {
        width: 100%;
      }

      & td,
      th {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        padding: 3.3mm 0.6mm;
        border: 1px solid #ddd;
        text-align: center;
        font-size: 3.6mm;

        &.order_no {
          width: 10%;
        }
        &.name {
          width: 19%;
        }
        &.s_name {
          width: 18%;
        }
        &.deli_location {
          width: 6%;
        }
        &.desired_date {
          width: 15%;
        }
        &.quantity {
          width: 5%;
        }
        &.price {
          width: 8%;
        }
        &.calc_price {
          width: 8%;
        }
        &.order_user {
          width: 6%;
        }
      }
    }
  }

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
    position: absolute;
    bottom: 2mm;
    width: 100%;

    height: 16%;
    & .textarea {
      height: 100%;
      width: 50%;
      border: 1px solid rgba(82, 82, 82, 0.555);
      border-radius: 3px;
      position: relative;

      & .text {
        position: absolute;
        top: 2mm;
        left: 2mm;

        & p {
          font-weight: normal;
        }
      }
    }
  }
}
</style>

