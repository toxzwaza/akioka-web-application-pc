<script setup>
import { watch, ref, onMounted } from "vue";
import domtoimage from "dom-to-image";
import axios from "axios";
import QRCode from "qrcode";

const props = defineProps({
  current_month_holidays: Array,
  next_month_holidays: Array,
  orders: Array,
});

const shortest = ref(false);
const description = ref("");
const calc_postage = ref(0); //送料合計

// 社員表示
const approval_flg = ref(false);

const reCreatePurchasePath = (order) => {
  order.purchase_path = false;
};

// FAX送信
const sendFax = (order) => {
  let fax_number = null;
  let file_url = null;

  if (!confirm(`FAX送信を行ってもよろしいですか？\n発注先:${order.com_name}\nFAX番号: ${order.fax}`)) {
    return
  }

  console.log(order);
  const cleanFaxNumber = order.fax.replace(/-/g, '');
  fax_number = cleanFaxNumber;
  file_url = `http://monokanri-manage.local/storage/${order.purchase_path}`

  console.log(fax_number, file_url)

  axios.post('http://monokanri-manage.local:5000/send_fax', {
    file_url: file_url,
    fax_number: fax_number,
  })
  .then(res => {
    console.log(res.data)
    alert(res.data.message)
  })
  .catch(error => {
    console.log(error)
  })
};

function printElement() {
  // 納入希望日が入力されていない場合最短とする
  props.orders.forEach((order) => {
    if (!order.desire_delivery_date) {
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
  approval_flg.value = true;

  // 納入希望日が入力されていない場合最短とする
  props.orders.forEach((order) => {
    if (!order.desire_delivery_date) {
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

// 文字数に応じたフォントサイズを取得
const getFontSizeClass = (text) => {
  if (!text) return "text-base";

  const length = text.length;

  if (length <= 15) {
    return "font-size-large"; // 2.8mm
  } else if (length <= 25) {
    return "font-size-medium"; // 2.4mm
  } else if (length <= 40) {
    return "font-size-small"; // 2.0mm
  } else {
    return "font-size-xsmall"; // 1.6mm
  }
};

// QRコード生成
const qrCodeUrl = ref("");
const generateQRCode = async () => {
  if (props.orders && props.orders.length > 0) {
    try {
      const orderId = props.orders[0].id;
      const url = await QRCode.toDataURL(String(orderId), {
        width: 150,
        margin: 1,
        color: {
          dark: "#000000",
          light: "#FFFFFF",
        },
      });
      qrCodeUrl.value = url;
    } catch (error) {
      console.error("QRコード生成エラー:", error);
    }
  }
};

onMounted(() => {
  calculatePostage();
  generateQRCode();
});
</script>
<template>
  <div v-if="orders.length === 1 && orders[0].purchase_path">
    <div class="flex justify-start">
      <button
        @click="reCreatePurchasePath(orders[0])"
        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
      >
        発注書再発行
      </button>

      <button
        @click="sendFax(orders[0])"
        class="ml-8 bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded"
      >
        発注書FAX送信
      </button>
    </div>

    <img
      class="w-2/5 mt-8 mx-auto"
      :src="`http://monokanri-manage.local/storage/${orders[0].purchase_path}`"
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

      <!-- 発注書エリア（上半分） -->
      <div id="order_section">
        <img
          :class="{ hidden: !approval_flg }"
          id="comp_approval_img"
          src="/img/base/comp_approval.png"
          alt=""
        />

        <h1 class="text-center font-bold text-xl py-2 mb-2">注文書</h1>

        <div id="top_content" class="flex justify-around items-center">
          <div class="left_container">
            <p class="mb-1 text-sm">
              注文日:
              {{ new Date().toISOString().slice(0, 10).replace(/-/g, "/") }}
            </p>
            <h2 class="font-bold text-lg">{{ orders[0].com_name }} 御中</h2>
            <div class="number text-xs">
              <p>TEL: {{ orders[0].tel }}</p>
              <p>FAX: {{ orders[0].fax }}</p>
            </div>
            <p class="text-xs font-serif mt-1">
              <!-- お世話になります。<br />
              注文をお願いします。<br /> -->
              納期に間に合わない場合は、<br />事前に納期回答をお願いいたします。<br />
            </p>
          </div>
          <div class="center_container text-center p-2">
            <h3 class="font-bold text-sm mb-1">休業日</h3>
            <p class="font-bold text-xs">
              {{ new Date().getMonth() + 1 }}月
              <span
                class="font-normal"
                v-for="currentMonth in current_month_holidays"
                :key="currentMonth.id"
                >{{ new Date(currentMonth.date).getDate() }},</span
              >日
            </p>
            <p class="font-bold text-xs">
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
            <p class="tracking-wider font-semibold text-sm">株式会社アキオカ</p>
            <p class="tracking-wider text-xs">〒713-8103</p>
            <p class="tracking-wider text-xs">倉敷市玉島乙島8252-35</p>
            <div class="pl-2 mt-1">
              <p class="tracking-wider text-xs">TEL:086-522-7686</p>
              <p class="tracking-wider text-xs">FAX:086-522-7674</p>
              <p class="tracking-wider text-xs">
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
                <th class="unit px-4 py-2 text-gray-700">単位</th>
                <th class="price px-4 py-2 text-gray-700">単価</th>
                <th class="calc_price px-4 py-2 text-gray-700">
                  金額(税抜価格)
                </th>
                <th class="order_user px-4 py-2 text-gray-700">注文指示者</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="order in orders" :key="order.id" class="">
                <td class="order_no text-center border">
                  {{
                    `${order.order_no}-${
                      order.stock_processes_order_request_code
                        ? order.stock_processes_order_request_code
                        : order.stock_processes_base_code
                        ? order.stock_processes_base_code
                        : "未"
                    }`
                  }}
                </td>
                <td
                  class="name text-center border"
                  :class="getFontSizeClass(order.name)"
                >
                  {{ order.name }}
                </td>
                <td
                  class="s_name text-center border"
                  :class="getFontSizeClass(order.s_name)"
                >
                  {{ order.s_name }}
                </td>
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
                    v-else-if="order.desire_delivery_date"
                    class="p-0 border-transparent text-sm"
                    type="date"
                    :value="order.desire_delivery_date"
                  />
                  <input
                    v-else-if="!order.desire_delivery_date"
                    class="p-0 border-transparent text-sm"
                    type="date"
                    :value="order.desire_delivery_date"
                  />
                </td>
                <td class="quantity text-center border">
                  {{ order.quantity }}
                </td>
                <td class="unit text-center border">
                  {{ order.order_unit }}
                </td>
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
                <td colspan="8" class="text-right border">送料</td>
                <td class="text-center border">
                  {{ calc_postage.toLocaleString() }}
                </td>
                <td class="border"></td>
              </tr>

              <tr>
                <td colspan="8" class="text-right border">合計（税抜）</td>
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
        <div id="bottom_content" class="mt-2 flex items-start justify-between">
          <div class="textarea">
            <div class="text font-bold text-xs">
              備考

              <p v-html="description.replace(/\n/g, '<br>')"></p>
            </div>
          </div>

          <div class="details w-1/2 pl-4">
            <p class="text-xs font-serif">
              納期に問題があればその旨の記載をお願いします。<br />
              受信後３日以内に確認印を押印し返信をお願いします。 10:00-10:10,
              12:00-12:50, 15:00-15:10の間の納品はご遠慮願います。<br />
              上記時間での納品になる場合は事前にご相談願います。
            </p>
          </div>
        </div>
      </div>
      <!-- 発注書エリア終了 -->

      <!-- 納品書エリア（下半分） -->
      <div id="delivery_section">
        <h1 class="text-center font-bold text-xl py-2 mb-2">納品書</h1>

        <div id="delivery_top_content" class="flex justify-around items-center">
          <div class="left_container">
            <p class="mb-1 text-sm">
              納品日:
              <span v-if="orders[0].shortest">最短</span>
              <span v-else-if="orders[0].desire_delivery_date">
                {{
                  new Date(orders[0].desire_delivery_date)
                    .toLocaleDateString("ja-JP", {
                      year: "numeric",
                      month: "2-digit",
                      day: "2-digit",
                    })
                    .replace(/\//g, "/")
                }}
              </span>
              <span v-else>未設定</span>
            </p>
            <p class="tracking-wider font-semibold text-sm">株式会社アキオカ</p>
            <p class="tracking-wider text-xs">〒713-8103</p>
            <p class="tracking-wider text-xs">倉敷市玉島乙島8252-35</p>
            <div class="pl-2 mt-1">
              <p class="tracking-wider text-xs">TEL:086-522-7686</p>
              <p class="tracking-wider text-xs">FAX:086-522-7674</p>
              <p class="tracking-wider text-xs">
                発注者：{{ orders[0].manage_user_name }}
              </p>
            </div>
          </div>
          <div class="center_container text-left p-2">
            <h3 class="font-bold text-xs mb-2 text-center">【重要】</h3>
            <p class="text-lg font-bold leading-relaxed text-center">
              納品の際は本納品書を<br />ご使用ください。
            </p>
          </div>

          <div class="right_container">
            <h2 class="font-bold text-lg">{{ orders[0].com_name }}</h2>
            <div class="number text-xs">
              <p>TEL: {{ orders[0].tel }}</p>
              <p>FAX: {{ orders[0].fax }}</p>
            </div>
          </div>
        </div>

        <div id="delivery_main_content">
          <table class="table-auto w-full">
            <thead>
              <tr>
                <th class="order_no px-4 py-2 text-gray-700">注文No</th>
                <th class="name px-4 py-2 text-gray-700">品名</th>
                <th class="s_name px-4 py-2 text-gray-700">品番</th>
                <th class="deli_location px-4 py-2 text-gray-700">納入場所</th>
                <th class="quantity px-4 py-2 text-gray-700">数量</th>
                <th class="unit px-4 py-2 text-gray-700">単位</th>
                <th class="price px-4 py-2 text-gray-700">単価</th>
                <th class="calc_price px-4 py-2 text-gray-700">
                  金額(税抜価格)
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="order in orders" :key="order.id" class="">
                <td class="order_no text-center border">
                  {{
                    `${order.order_no}-${
                      order.stock_processes_order_request_code
                        ? order.stock_processes_order_request_code
                        : order.stock_processes_base_code
                        ? order.stock_processes_base_code
                        : "未"
                    }`
                  }}
                </td>
                <td
                  class="name text-center border"
                  :class="getFontSizeClass(order.name)"
                >
                  {{ order.name }}
                </td>
                <td
                  class="s_name text-center border"
                  :class="getFontSizeClass(order.s_name)"
                >
                  {{ order.s_name }}
                </td>
                <td class="deli_location text-center border">
                  {{ order.deli_location }}
                </td>
                <td class="quantity text-center border">
                  {{ order.quantity }}
                </td>
                <td class="unit text-center border">
                  {{ order.order_unit }}
                </td>
                <td class="price text-center border">
                  {{ order.price.toLocaleString() }}
                </td>
                <td class="calc_price text-center border">
                  {{ order.calc_price.toLocaleString() }}
                </td>
              </tr>

              <tr>
                <td colspan="7" class="text-right border">送料</td>
                <td class="text-center border">
                  {{ calc_postage.toLocaleString() }}
                </td>
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
              </tr>
            </tbody>
          </table>
        </div>

        <div
          id="delivery_bottom_content"
          class="mt-2 flex items-start justify-between"
        >
          <div class="textarea">
            <div class="text font-bold text-xs">
              備考

              <p></p>
            </div>
          </div>

          <div
            class="details w-1/6 pl-4 flex flex-col items-center justify-center"
          >
            <img
              v-if="qrCodeUrl"
              :src="qrCodeUrl"
              alt="注文IDのQRコード"
              class="qr-code"
            />
            <p class="text-xs font-serif mt-2">
              注文ID: {{ orders[0].order_no }}
            </p>
          </div>
        </div>
      </div>
      <!-- 納品書エリア終了 -->
    </div>
  </div>
</template>
<style scoped lang="scss">
#description_area {
  width: 210mm;
}

#purchase_container {
  background-color: white;
  width: 210mm;
  height: 297mm;
  box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
  border: 1px solid rgba(221, 221, 221, 0.705);
  position: relative;

  // 発注書エリア（上半分）
  & #order_section {
    height: 50%;
    position: relative;
    border-bottom: 2px dashed #999;
    padding: 4px;

    & #comp_approval_img {
      position: absolute;
      top: 14%;
      right: 16%;
      width: 10%;
    }
  }

  // 納品書エリア（下半分）
  & #delivery_section {
    height: 50%;
    position: relative;
    padding: 4px;
  }

  & #main_content {
    margin-top: 2mm;

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
        padding: 1.5mm 0.4mm;
        border: 1px solid #ddd;
        text-align: center;
        font-size: 2.8mm;
        vertical-align: middle;

        &.order_no {
          width: 12%;
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
        }
        &.name {
          width: 19%;
          white-space: normal;
          word-wrap: break-word;
          word-break: break-all;
          line-height: 1.3;

          &.font-size-large {
            font-size: 2.8mm;
          }
          &.font-size-medium {
            font-size: 2.4mm;
          }
          &.font-size-small {
            font-size: 2mm;
          }
          &.font-size-xsmall {
            font-size: 1.6mm;
          }
        }
        &.s_name {
          width: 18%;
          white-space: normal;
          word-wrap: break-word;
          word-break: break-all;
          line-height: 1.3;

          &.font-size-large {
            font-size: 2.8mm;
          }
          &.font-size-medium {
            font-size: 2.4mm;
          }
          &.font-size-small {
            font-size: 2mm;
          }
          &.font-size-xsmall {
            font-size: 1.6mm;
          }
        }
        &.deli_location {
          width: 6%;
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
        }
        &.desired_date {
          width: 12%;
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
        }
        &.quantity {
          width: 5%;
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
        }
        &.unit {
          width: 5%;
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
        }
        &.price {
          width: 7%;
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
        }
        &.calc_price {
          width: 8%;
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
        }
        &.order_user {
          width: 6%;
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
        }
      }
    }
  }

  & #delivery_main_content {
    margin-top: 2mm;

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
        padding: 1.5mm 0.4mm;
        border: 1px solid #ddd;
        text-align: center;
        font-size: 2.8mm;
        vertical-align: middle;

        &.order_no {
          width: 13%;
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
        }
        &.name {
          width: 22%;
          white-space: normal;
          word-wrap: break-word;
          word-break: break-all;
          line-height: 1.3;

          &.font-size-large {
            font-size: 2.8mm;
          }
          &.font-size-medium {
            font-size: 2.4mm;
          }
          &.font-size-small {
            font-size: 2mm;
          }
          &.font-size-xsmall {
            font-size: 1.6mm;
          }
        }
        &.s_name {
          width: 21%;
          white-space: normal;
          word-wrap: break-word;
          word-break: break-all;
          line-height: 1.3;

          &.font-size-large {
            font-size: 2.8mm;
          }
          &.font-size-medium {
            font-size: 2.4mm;
          }
          &.font-size-small {
            font-size: 2mm;
          }
          &.font-size-xsmall {
            font-size: 1.6mm;
          }
        }
        &.deli_location {
          width: 10%;
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
        }
        &.quantity {
          width: 7%;
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
        }
        &.unit {
          width: 6%;
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
        }
        &.price {
          width: 10%;
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
        }
        &.calc_price {
          width: 11%;
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
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

  & #top_content,
  & #delivery_top_content {
    & > div {
      width: 30%;
    }

    & .center_container {
      width: 32%;
      border: 1px solid rgba(82, 82, 82, 0.555);
      border-radius: 3px;
    }
  }

  & #bottom_content,
  & #delivery_bottom_content {
    width: 100%;
    height: 15%;

    & .textarea {
      height: 100%;
      width: 50%;
      border: 1px solid rgba(82, 82, 82, 0.555);
      border-radius: 3px;
      position: relative;

      & .text {
        position: absolute;
        top: 1mm;
        left: 1mm;
        font-size: 2.5mm;

        & p {
          font-weight: normal;
        }
      }
    }

    & .details {
      font-size: 2.5mm;

      & .qr-code {
        width: 100px;
        height: 100px;
        object-fit: contain;
      }
    }
  }
}
</style>

