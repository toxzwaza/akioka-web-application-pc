<script setup>
import { ref, watch } from "vue";
import { Link } from "@inertiajs/vue3";
import QRCode from "qrcode";

const props = defineProps({
  users: Array,
  groups: Array,
  processes: Array,
  positions: Array,
});

const user_card = ref(false);
const card_users = ref([]);

const base_users = ref([]);
const filter_users = ref([]);

watch(
  () => props.users,
  (users) => {
    const list = users ?? [];
    base_users.value = list;
    filter_users.value = list;
  },
  { immediate: true }
);

const generateQRCode = async (val) => {
  try {
    return await QRCode.toDataURL(val);
  } catch (error) {
    console.error("QRコードの生成に失敗しました:", error);
    return "";
  }
};

const createPrint = async () => {
  for (const user of card_users.value) {
    try {
      const data = await generateQRCode(user.id.toString());
      user.qr_code = data;
    } catch (error) {
      console.error("QRコードの生成に失敗しました:", error);
    }
  }
};

const handleFilter = (flg, val) => {
  if (!val) return;

  switch (flg) {
    case "group":
      filter_users.value = filter_users.value.filter(
        (user) => user.group_id == val
      );
      break;
    case "process":
      filter_users.value = filter_users.value.filter(
        (user) => user.process_id == val
      );
      break;
    case "position":
      filter_users.value = filter_users.value.filter(
        (user) => user.position_id == val
      );
      break;
  }
};

const createUserCard = () => {
  const users = filter_users.value.filter((user) => user.select_flg === true);
  card_users.value = users;
  createPrint();

  user_card.value = !user_card.value;
};

const clearFilter = () => {
  filter_users.value = base_users.value;
  alert("フィルターをリセットしました。");
};

function printElement() {
  const iframe = document.createElement("iframe");
  iframe.style.display = "none";
  document.body.appendChild(iframe);

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

  const printContent = document.getElementById("print_container");

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

  iframe.contentWindow.focus();
  setTimeout(() => {
    iframe.contentWindow.print();
  }, 500);

  setTimeout(() => {
    document.body.removeChild(iframe);
  }, 1000);
}
</script>
<template>
  <div>
    <h3 class="mb-4 text-lg text-gray-700 font-bold">フィルター</h3>
    <form class="w-full mb-12" @submit.prevent>
      <div class="flex flex-wrap -mx-3 mb-2">
        <div class="w-1/3 px-3 mb-0">
          <label
            class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
            for="filter-group"
          >
            部署
          </label>
          <select
            id="filter-group"
            @change="handleFilter('group', $event.target.value)"
            class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
          >
            <option value="0">未選択</option>
            <option
              v-for="group in props.groups"
              :key="group.id"
              :value="group.id"
            >
              {{ group.name }}
            </option>
          </select>
        </div>
        <div class="w-1/3 px-3 mb-0">
          <label
            class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
            for="filter-process"
          >
            工程
          </label>
          <select
            id="filter-process"
            @change="handleFilter('process', $event.target.value)"
            class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
          >
            <option value="0">未選択</option>
            <option
              v-for="process in props.processes"
              :key="process.id"
              :value="process.id"
            >
              {{ process.name }}
            </option>
          </select>
        </div>
        <div class="w-1/3 px-3 mb-0">
          <label
            class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
            for="filter-position"
          >
            役職
          </label>
          <select
            id="filter-position"
            @change="handleFilter('position', $event.target.value)"
            class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
          >
            <option value="0">未選択</option>
            <option
              v-for="position in props.positions"
              :key="position.id"
              :value="position.id"
            >
              {{ position.name }}
            </option>
          </select>
        </div>
      </div>

      <div class="flex justify-center w-full mt-12">
        <button
          type="button"
          @click.prevent="clearFilter"
          class="mr-2 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
        >
          クリア
        </button>
      </div>
    </form>

    <hr />
    <form class="w-full mb-12 pt-8" @submit.prevent>
      <h3 class="mb-4 text-lg text-gray-700 font-bold">便利機能</h3>

      <div class="flex justify-start w-full mt-12">
        <button
          type="button"
          @click.prevent="createUserCard"
          class="mr-2 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
        >
          {{ user_card ? "社員一覧へ戻る" : "社員カード作成" }}
        </button>
      </div>
    </form>

    <section v-if="!user_card" class="text-gray-600 body-font">
      <div class="mx-auto">
        <div class="w-full mx-auto overflow-auto">
          <table class="table-auto w-full text-left whitespace-no-wrap">
            <thead>
              <tr class="bg-gray-200">
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm"
                ></th>
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm"
                >
                  ID
                </th>
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm"
                >
                  社員番号
                </th>
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm"
                >
                  名前
                </th>

                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm"
                >
                  メール
                </th>
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm"
                >
                  部署
                </th>
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm"
                >
                  工程
                </th>
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm"
                >
                  役職
                </th>
                <th
                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm"
                ></th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="user in filter_users"
                :key="user.id"
                class="even:bg-gray-100 hover:bg-green-100 transition-all duration-100"
              >
                <td class="px-4 py-3">
                  <input
                    type="checkbox"
                    v-model="user.select_flg"
                  />
                </td>
                <td class="px-4 py-3">{{ user.id }}</td>
                <td class="px-4 py-3">{{ user.emp_no }}</td>
                <td class="px-4 py-3">{{ user.name }}</td>
                <td class="px-4 py-3">{{ user.email }}</td>
                <td class="px-4 py-3">{{ user.group_name }}</td>
                <td class="px-4 py-3">{{ user.process_name }}</td>
                <td class="px-4 py-3">{{ user.position_name }}</td>
                <td class="px-4 py-3">
                  <Link
                    :href="route('master.show.user', { user_id: user.id })"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm"
                  >
                    詳細
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>
    <section v-else>
      <div class="mb-2">
        <button
          type="button"
          @click="printElement"
          class="mr-2 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
        >
          印刷
        </button>
      </div>

      <div id="print_container">
        <div class="flex flex-wrap justify-between">
          <div
            v-for="user in card_users"
            :key="user.id"
            class="card_content m-4"
          >
            <div class="card_header">
              <span class="">社員証</span>
            </div>
            <div class="card_main">
              <div class="left_content">
                <p>社員No.{{ user.emp_no }}</p>
                <img
                  class="icon_img"
                  :src="
                    user.gender_flg == 0
                      ? '/img/master/users/man.png'
                      : '/img/master/users/woman.png'
                  "
                  alt=""
                />

                <div class="foot">
                  <img class="qr_img" :src="user.qr_code" alt="" />
                </div>
              </div>
              <div class="right_content">
                <img class="ak_logo" src="/img/base/AK_logo.png" alt="" />
                <img
                  class="ak_stamp"
                  src="/img/base/akioka_stamp.png"
                  alt=""
                />

                <p class="name">
                  氏名:<br /><span>{{ user.name }}</span>
                </p>
                <p class="group">
                  所属:<span>{{ user.group_name }}</span>
                </p>
                <p class="text">
                  上記の者は当社の社員であることを証明します。
                </p>

                <div class="company_description">
                  <p>株式会社アキオカ</p>
                  <div>
                    <p>〒713-8103</p>
                    <p>岡山県倉敷市玉島乙島8252-35</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>
<style scoped lang="scss">
#print_container {
  size: A4;
  width: 210mm;
  height: 297mm;
  border: 1px solid black;
  font-family: serif;

  & .card_content {
    width: 88mm;
    height: 54mm;
    background-color: rgb(238, 238, 238);
    border-radius: 3mm;
    overflow: hidden;
    font-size: 3mm;

    background: repeating-linear-gradient(
      180deg,
      #f3f3f3,
      #f3f3f3 0.6mm,
      #e0e0e0 0.6mm,
      #e0e0e0 1mm
    );

    & .card_header {
      width: 100%;
      display: inline-block;
      padding: 2mm 0 2mm 3mm;
      background-color: #1d4b90;
      color: white;
    }

    & .card_main {
      height: 85%;
      overflow-y: hidden;

      padding: 3mm 4mm;
      display: flex;
      justify-content: space-between;
      align-items: start;

      & .left_content {
        position: relative;

        width: 30%;
        height: 100%;

        & .icon_img {
          width: 100%;
        }

        & .foot {
          position: absolute;
          bottom: 0;
          right: 0;

          background-color: gray;
          width: 12mm;
          height: 12mm;
          border-radius: 3px;
          overflow: hidden;
          box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;

          & .qr_img {
            width: 100%;
            height: 100%;
            object-fit: cover;
          }
        }
      }
      & .right_content {
        position: relative;

        & .ak_logo {
          position: absolute;
          top: 0;
          right: 0;
          width: 7mm;
        }
        & .ak_stamp {
          position: absolute;
          bottom: 0;
          right: 0;
          width: 6mm;
        }

        & p {
          & span {
            display: inline-block;
          }

          &.name {
            line-height: 4mm;

            & span {
              font-size: 8mm;
              line-height: 12mm;
              font-weight: bold;
              color: #1d4b90;
              letter-spacing: 2mm;
            }
          }
          &.group {
            display: flex;
            justify-content: start;
            align-items: center;
            margin-bottom: 1mm;

            & span {
              font-size: 4mm;
              color: #1d4b90;
              margin-left: 2mm;
              font-weight: bold;
            }
          }
          &.text {
            white-space: nowrap;
            font-size: 2.2mm;
          }
        }

        & .company_description {
          margin-top: 1.4mm;

          & > p {
            font-size: 3.2mm;
            font-weight: bold;
          }
          & > div {
            letter-spacing: 0.3mm;
            margin-top: 0.6mm;
            line-height: 3.3mm;
          }
        }
      }
    }
  }
}
</style>
