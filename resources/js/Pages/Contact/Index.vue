<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { ref, reactive, onMounted, computed } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";
import Title from "@/Components/Title/MainTitle.vue";
import { Pie, Bar } from "vue-chartjs";
import Pagination from "@/Components/Pagination.vue";
import {
  Chart as ChartJS,
  ArcElement,
  Tooltip,
  Legend,
  CategoryScale,
  LinearScale,
  BarElement,
} from "chart.js";

const leftContainer = ref(false);

ChartJS.register(
  ArcElement,
  Tooltip,
  Legend,
  CategoryScale,
  LinearScale,
  BarElement
);

const form = reactive({
  orderBy: "desc",
  startContactDate: "",
  endContactDate: "",
  kind: null,
  progress: null,
  keyword: "",
  userId: null,
});

const props = defineProps({
  contacts: Object,
  stats: Array,
  users: Array,
});

const searchContact = (flg) => {
  if (
    flg === "search" &&
    !form.keyword &&
    !form.startContactDate &&
    !form.endContactDate &&
    form.kind === null &&
    form.progress === null &&
    !form.userId
  ) {
    return alert("検索対象を入力してください。");
  }

  if (flg === "reset") {
    form.keyword = null;
    form.startContactDate = null;
    form.endContactDate = null;
    form.progress = null;
    form.kind = null;
    form.userId = null;
  }
  router.get(route("contact.home"), {
    keyword: form.keyword,
    start_contact_date: form.startContactDate,
    end_contact_date: form.endContactDate,
    progress: form.progress,
    kind: form.kind,
    user_id: form.userId,
  });
};

const pieChartData = computed(() => {
  const kindCounts = {
    0: 0, // 製品
    1: 0, // 新規案件
    2: 0, // 営業・広告
    3: 0, // その他
  };

  props.contacts.data.forEach((contact) => {
    kindCounts[contact.kind]++;
  });

  return {
    labels: ["製品", "新規案件", "営業・広告", "その他"],
    datasets: [
      {
        backgroundColor: ["#818cf8", "#a78bfa", "#60a5fa", "#9ca3af"],
        data: Object.values(kindCounts),
      },
    ],
  };
});

const pieChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: "bottom",
      labels: {
        boxWidth: 12,
        padding: 10,
        font: {
          size: 11,
        },
      },
    },
  },
};

const barChartData = computed(() => {
  const monthlyCounts = Array(6).fill(0);
  const now = new Date();

  props.contacts.data.forEach((contact) => {
    const contactDate = new Date(contact.created_at);
    const monthDiff =
      (now.getFullYear() - contactDate.getFullYear()) * 12 +
      (now.getMonth() - contactDate.getMonth());

    if (monthDiff < 6) {
      monthlyCounts[monthDiff]++;
    }
  });

  return {
    labels: ["6ヶ月前", "5ヶ月前", "4ヶ月前", "3ヶ月前", "2ヶ月前", "今月"],
    datasets: [
      {
        label: "問い合わせ数",
        backgroundColor: "#60a5fa",
        data: monthlyCounts.reverse(),
      },
    ],
  };
});

const barChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false,
    },
  },
  scales: {
    y: {
      beginAtZero: true,
      ticks: {
        font: {
          size: 11,
        },
      },
    },
    x: {
      ticks: {
        font: {
          size: 11,
        },
      },
    },
  },
};

const redirectShow = (contactId) => {
  router.get(
    route("contact.show", {
      id: contactId,
      keyword: form.keyword,
      start_contact_date: form.startContactDate,
      end_contact_date: form.endContactDate,
      progress: form.progress,
      kind: form.kind,
      user_id: form.userId,
    })
  );
};

const isFormIncomplete = () => {
  return (
    !form.keyword &&
    !form.startContactDate &&
    !form.endContactDate &&
    form.progress === null &&
    form.kind === null &&
    form.userId === null
  );
};

onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search);
  form.keyword = urlParams.get("keyword") || "";
  form.startContactDate = urlParams.get("start_contact_date") || "";
  form.endContactDate = urlParams.get("end_contact_date") || "";
  form.progress = urlParams.get("progress") || null;
  form.kind = urlParams.get("kind") || null;
  form.userId = urlParams.get("user_id") || null;
  console.log(form);
  if (!isFormIncomplete()) {
    leftContainer.value = true;
  }
});
</script>
<template>
  <MainLayout :p_none="true">
    <template #content>
      <section class="py-16 px-24">
        <Title
          :top="'お問い合わせ'"
          :sub="'HPからのお問い合わせ対応が可能です。'"
        />
      </section>
      <div id="mainContent">
        <div
          id="leftContainer"
          :class="{ 'bg-gray-100': true, open: leftContainer }"
          @click="leftContainer = !leftContainer"
        >
          <div class="flex justify-end">
            <button
              v-if="!leftContainer"
              class="bg-gray-400 hover:bg-gray-700 text-white font-bold py-1 px-2"
            >
              <i class="fas fa-caret-right"></i>
            </button>
            <button
              v-else
              class="bg-gray-400 hover:bg-gray-700 text-white font-bold py-1 px-2"
            >
              <i class="fas fa-caret-left"></i>
            </button>
          </div>

          <div
            id="sortContainer"
            class="my-8"
            v-if="leftContainer"
          >
            <div class="w-5/6 mx-auto" @click.stop>
              <p class="mb-2 font-bold">検索</p>
              <div class="buttonContainer">
                <div class="mb-8">
                  <label
                    class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                    for="grid-last-name"
                  >
                    キーワードから検索
                  </label>
                  <input
                    class="appearance-none block w-full bg-white text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    type="text"
                    name=""
                    id=""
                    v-model="form.keyword"
                  />
                </div>
                <div class="mb-4">
                  <label
                    class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                    for="grid-last-name"
                  >
                    問い合わせ日(開始)
                  </label>
                  <div class="mb-4">
                    <input

                      type="date"
                      name=""
                      id=""
                      class="appearance-none block w-full bg-white text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500 mr-2"
                      v-model="form.startContactDate"
                    />
                  </div>
                  <div class="mb-8">
                    <label
                      class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                      for="grid-last-name"
                    >
                      問い合わせ日(終了)
                    </label>
                    <div class="mb-4">
                      <input
  
                        type="date"
                        name=""
                        id=""
                        class="appearance-none block w-full bg-white text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                        v-model="form.endContactDate"
                      />
                    </div>
                  </div>

                  <div class="mb-4">
                    <label
                      class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                      for="grid-last-name"
                    >
                      状況
                    </label>
                    <select

                      class="appearance-none block w-full bg-white text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      name="order_user"
                      id=""
                      v-model="form.progress"
                    >
                      <option value="">未選択</option>
                      <option value="0">未回答</option>
                      <option value="1">回答済み</option>
                    </select>
                  </div>
                  <div class="mb-4">
                    <label
                      class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                      for="grid-last-name"
                    >
                      種類
                    </label>
                    <select

                      class="appearance-none block w-full bg-white text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      name="order_user"
                      id=""
                      v-model="form.kind"
                    >
                      <option value="">未選択</option>
                      <option value="0">製品</option>
                      <option value="1">新規案件</option>
                      <option value="2">営業・広告</option>
                      <option value="3">その他</option>
                    </select>
                  </div>
                  <div class="mb-8">
                    <label
                      class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                      for="grid-last-name"
                    >
                      担当者
                    </label>
                    <select

                      class="appearance-none block w-full bg-white text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      name="order_user"
                      id=""
                      v-model="form.userId"
                    >
                      <option value="0">未選択</option>
                      <option
                        v-for="user in props.users"
                        :key="user.id"
                        :value="user.id"
                      >
                        {{ user.name }}
                      </option>
                    </select>
                  </div>

                  <button
                    @click="searchContact('search')"
                    class="ml-4 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
                  >
                    検索
                  </button>
                  <button
                    @click="searchContact('reset')"
                    class="ml-4 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                  >
                    リセット
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div id="rightContainer" :class="{ open: leftContainer }">
          <section id="topContent" class="">
            <div id="cardContent">
              <div class="contactCard bg-blue-500">
                <p class="title">今月の問い合わせ</p>
                <hr class="my-1" />
                <p class="value">{{ props.stats.current_month_count }}</p>
              </div>
              <div class="contactCard bg-blue-500">
                <p class="title">問題回答率</p>
                <hr class="my-1" />
                <p class="value">
                  {{
                    Math.round(
                      (props.stats.solved_count / props.stats.total_count) * 100
                    )
                  }}%
                </p>
              </div>
              <div class="contactCard bg-gray-500">
                <p class="title">未回答</p>
                <hr class="my-1" />
                <p class="value">{{ props.stats.in_progress_count }}</p>
              </div>
              <div class="contactCard bg-green-500">
                <p class="title">回答済み</p>
                <hr class="my-1" />
                <p class="value">{{ props.stats.solved_count }}</p>
              </div>
            </div>

            <div id="graphContent" class="">
              <div class="w-1/2 p-4">
                <h3 class="text-lg font-bold mb-4">問い合わせ種類の割合</h3>
                <div class="">
                  <Pie :data="pieChartData" :options="pieChartOptions" />
                </div>
              </div>

              <div class="w-1/2 p-4">
                <h3 class="text-lg font-bold mb-4">月別問い合わせ数</h3>
                <div class="">
                  <Bar :data="barChartData" :options="barChartOptions" />
                </div>
              </div>
            </div>
          </section>

          <section class="text-gray-600 body-font">
            <div class="mx-auto overflow-auto">
              <div class="flex justify-start mb-4">
                <Pagination :links="props.contacts.links" />
              </div>

              <div id="tableContainer" class="w-full mx-auto">
                <table class="table-auto w-full text-left whitespace-no-wrap">
                  <thead>
                    <tr>
                      <th
                        class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl"
                      >
                        ID
                      </th>
                      <th
                        class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl"
                      >
                        状況
                      </th>

                      <th
                        class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                      >
                        種類
                      </th>
                      <th
                        class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                      >
                        件名
                      </th>
                      <th
                        class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                      >
                        名前
                      </th>
                      <th
                        class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                      >
                        フリガナ
                      </th>
                      <th
                        class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                      >
                        メール
                      </th>
                      <th
                        class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                      >
                        電話
                      </th>
                      <th
                        class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                      >
                        問い合わせ日
                      </th>
                      <th
                        class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                      >
                        担当
                      </th>
                      <th
                        class="w-10 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tr rounded-br"
                      ></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="contact in props.contacts.data"
                      :key="contact.id"
                      class="hover:bg-gray-50"
                      @click="redirectShow(contact.id)"
                    >
                      <td class="px-4 py-5">{{ contact.id }}</td>
                      <td class="px-4 py-5">
                        <span
                          v-if="!contact.progress"
                          class="bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-900 dark:text-gray-300"
                          >未読</span
                        >
                        <span
                          v-else-if="contact.progress === 1"
                          class="bg-orange-100 text-orange-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-orange-900 dark:text-orange-300"
                          >進行中</span
                        >
                        <span
                          v-else-if="contact.progress === 2"
                          class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-green-900 dark:text-green-300"
                          >完了</span
                        >
                      </td>

                      <td class="px-4 py-5">
                        <span
                          v-if="!contact.kind"
                          class="bg-indigo-100 text-indigo-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-indigo-900 dark:text-indigo-300"
                          >製品</span
                        >
                        <span
                          v-else-if="contact.kind === 1"
                          class="bg-purple-100 text-purple-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-purple-900 dark:text-purple-300"
                          >新規案件</span
                        >
                        <span
                          v-else-if="contact.kind === 2"
                          class="bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-blue-900 dark:text-blue-300"
                          >営業・広告</span
                        >
                        <span
                          v-else-if="contact.kind === 3"
                          class="bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-gray-300"
                          >その他</span
                        >
                      </td>
                      <td class="px-4 py-5">{{ contact.subject }}</td>
                      <td class="px-4 py-5">{{ contact.name }}</td>
                      <td class="px-4 py-5">{{ contact.furi_name }}</td>
                      <td class="px-4 py-5">{{ contact.email }}</td>
                      <td class="px-4 py-5">{{ contact.tel }}</td>
                      <td class="px-4 py-5">
                        {{
                          new Date(contact.created_at)
                            .toLocaleString("ja-JP", {
                              year: "numeric",
                              month: "2-digit",
                              day: "2-digit",
                              hour: "2-digit",
                              minute: "2-digit",
                            })
                            .replace(/\//g, "/")
                        }}
                      </td>
                      <td class="px-4 py-5">{{ contact.user_name }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="flex justify-start mt-4">
                <Pagination :links="props.contacts.links" />
              </div>
            </div>
          </section>
        </div>
      </div>
    </template>
  </MainLayout>
</template>
<style scoped lang="scss">
#mainContent {
  overflow: hidden;
  display: flex;
  justify-content: space-between;

  & #leftContainer {
    width: 1.4%;
    transition: all 0.3s ease;

    &:hover {
      width: 4%;
    }

    &.open {
      width: 16%;
    }

    & #sortContainer {
      width: 96%;
      margin: 0.4rem auto 0;
      position: sticky;
      top: 20px;
      left: 0;
      z-index: 10;
    }
  }
  & #rightContainer {
    width: 95%;
    &.open {
      width: 82%;
    }

    & #tableContainer {
      min-width: 1800px;
      overflow: auto;

      & table {
        & td,
        th {
          white-space: nowrap;
        }
      }
    }

    & #topContent {
      height: 30vh;
      display: flex;
      margin-bottom: 4vh;
      justify-content: space-between;

      & #cardContent {
        width: 42%;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        height: 100%;

        & .contactCard {
          width: 48%;
          display: flex;
          flex-direction: column;
          text-align: center;
          justify-content: center;
          font-weight: bold;
          color: #fff;
          border-radius: 8px;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
          &:hover {
            transform: scale(1.02);
            transition: transform 0.2s ease;
          }

          & p {
            padding: 10px 20px;

            &.title {
              font-size: 1.2rem;
            }

            &.value {
              font-size: 2.5rem;
            }
          }
        }
      }

      & #graphContent {
        width: 56%;
        height: 100%;
        display: flex;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        background-color: white;
        padding: 1rem;

        & > div {
          width: 50%;
          height: 100%;
          display: flex;
          flex-direction: column;
          padding: 0.5rem;

          & h3 {
            font-size: 1rem;
            margin-bottom: 0.5rem;
          }

          & > div {
            flex: 1;
            position: relative;
            min-height: 0;
          }
        }
      }
    }
  }
}
</style>