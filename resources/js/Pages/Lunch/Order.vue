<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { onMounted, ref, reactive } from "vue";
import axios from "axios";
import MainTitle from "@/Components/Title/MainTitle.vue"

const props = defineProps({
  date: String,
  price: Number,
  count: Number,
  today_lunch_description: String,
});

const today = ref("");

const handleKeyDown = (event) => {
  switch (event.key) {
    case "p":
      printElement();
      break;
  }
};

function printElement() {
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
  const printContent = document.getElementById("print_content");

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
}

onMounted(() => {
  console.log(props.price, props.count);

  today.value = new Date().toISOString().split("T")[0].replace(/-/g, "/");
  document.addEventListener("keydown", handleKeyDown);
});
</script>
<template>
  <MainLayout>
    <template #content>
      <MainTitle
        :top="'当日弁当注文書'"
        :sub="'本日の弁当発注用注文書の確認と印刷ができます。'"
      />

      <section id="print_content">
        <h1 class="doc-title">注文書</h1>
        <div class="top-text-container">
          <h2>株式会社倉敷まるたま<span>御中</span></h2>
          <p>{{ props.date }}</p>
        </div>
        <div class="middle-text-container">
          <div>
            <p>下記の通り注文いたします。</p>
            <p class="price">
              合計金額<span>￥{{ props.count * props.price }} -</span>
            </p>
          </div>
          <div>
            <h3>株式会社アキオカ</h3>

            <p>倉敷市玉島乙島8252-35</p>
            <p>〒713-8103</p>
            <p>TEL:086-522-7686</p>
            <p>FAX:086-522-7646</p>
          </div>
        </div>
        <div class="bottom-text-container">
          <table>
            <tbody>
              <tr>
                <th>品名</th>
                <th>備考</th>
                <th>数量</th>
                <th>単価</th>
                <th>金額</th>
              </tr>
              <tr class="font-bold text-center">
                <td>弁当</td>
                <td></td>
                <td>{{ props.count }}</td>
                <td>{{ props.price }}</td>
                <td>{{ props.count * props.price }}</td>
              </tr>
              <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td class="desc" colspan="3" rowspan="3">
                  <textarea name="memo" id="memo" cols="30" rows="5">{{
                    props.today_lunch_description
                  }}</textarea>
                </td>
                <td class="cal font-bold text-center">小計</td>
                <td class="cal font-bold text-center">
                  {{ props.count * props.price }}
                </td>
              </tr>
              <tr>
                <td class="font-bold text-center">消費税(10%)</td>
                <td class="cal font-bold text-center">
                  {{ props.count * props.price * 0.1 }}
                </td>
              </tr>
              <tr>
                <td class="font-bold text-center">合計</td>
                <td class="cal font-bold text-center">
                  {{ props.count * props.price }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </template>
  </MainLayout>
</template>
<style scoped lang="scss">
#print_content {
  background-color: white;
  color: rgb(56, 56, 56);

  width: 297mm;
  height: 210mm;
  position: relative;
  // page-break-after: always;

  //   background-color: grey;
  box-sizing: border-box;
  border: 1px solid rgb(150, 150, 150);
  padding: 2rem;
  margin: auto;
  font-family: serif;

  & > div {
    width: 96%;
    margin: 0 auto;
    box-sizing: border-box;
  }

  & #button {
    height: 4vh;
    padding: 0.4rem 1rem;
    font-weight: bold;
    color: white;
    background-color: rgb(16, 159, 243);
    border-radius: 5px;
    border: none;
    box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
    transition: all 0.3s ease;
    box-sizing: border-box;
    display: block;
    position: absolute;
    top: 5%;
    left: 15%;
  }
  & #orderArchive {
    height: 4vh;

    font-weight: bold;
    color: white;
    background-color: rgb(16, 159, 243);
    border-radius: 5px;
    border: none;
    box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
    transition: all 0.3s ease;
    box-sizing: border-box;
    display: block;
    position: absolute;
    top: 5%;
    left: 5%;
    padding: 0;

    & a {
      color: white;
      text-decoration: none;
      display: block;
      width: 100%;
      height: 100%;
      box-sizing: border-box;
      padding: 0.4rem 1rem;
    }
  }

  & .doc-title {
    text-align: center;
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 1rem;
  }
  & h2 {
    font-size: 1.4rem !important;
  }
  & h3 {
    font-size: 1.2rem !important;
  }

  & .top-text-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 0;
    height: 12%;
    padding: 1rem;
    & h2 {
      padding-bottom: 0.2rem;
      border-bottom: 1px solid black;
      & span {
        font-size: 1rem;
        margin-left: 0.4rem;
        color: rgb(82, 82, 82);
      }
    }
  }
  & .middle-text-container {
    display: flex;
    margin: 0;
    height: 26%;
    padding: 1rem;
    justify-content: space-between;
    align-items: flex-start;
    & p {
      font-weight: normal;
      text-align: left;
    }
    & > div {
      &:first-child {
        padding-top: 1rem;
        & .price {
          margin-top: 0.8rem;
          font-size: 1.2rem;
          border-bottom: 1px solid black;
          padding: 0 1rem;
          & span {
            font-size: 1.8rem;
            margin-left: 0.6rem;
          }
        }
      }
      &:last-child {
        & h3 {
          margin-top: 0;
          text-align: left;
        }
      }
    }
    // margin-top: 10%;
  }
  & .bottom-text-container {
    margin-top: 1rem;

    & table {
      width: 100%;
    }

    & table,
    td,
    th {
      font-size: 1rem;
      border: 1px solid #595959;
      border-collapse: collapse;
    }
    & td,
    th {
      padding: 3px;
      width: 30px;
      height: 25px;
    }
    & td {
      &.cal {
        border-top: 1px double black;
      }
      &.desc {
        position: relative;
        padding: 0;
        & textarea {
          height: 100%;
          width: 100%;
          padding: 2.6rem 0 0 1rem;
          border: none;
          box-sizing: border-box;
        }
        &::before {
          content: "備考";
          position: absolute;
          top: 0%;
          left: 0%;
          font-weight: bold;
          font-family: sans-serif;
          width: 100%;
          background: #f0e6cc;
          text-align: left;

          padding: 0.2rem 0 0.2rem 1rem;
          box-sizing: border-box;
          border-top: 1px solid black;
        }
      }
    }
    & th {
      background: #f0e6cc;
      font-family: sans-serif;
      font-weight: bold;
    }
    & .even {
      background: #fbf8f0;
    }
    & .odd {
      background: #fefcf9;
    }
  }
}
</style>