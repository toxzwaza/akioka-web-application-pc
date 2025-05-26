<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { ref, onMounted, computed } from "vue";
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

ChartJS.register(
  ArcElement,
  Tooltip,
  Legend,
  CategoryScale,
  LinearScale,
  BarElement
);

const props = defineProps({
  contacts: Object,
  stats: Array
});

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

const redirectShow = (contact_id) => {
  router.get(route("contact.show", { id: contact_id }));
};

onMounted(() => {
  console.log(props.contacts);
});
</script>
<template>
  <MainLayout>
    <template #content>
      <Title
        :top="'お問い合わせ'"
        :sub="'HPからのお問い合わせ対応が可能です。'"
      />
      <section id="top_content" class="">
        <div id="card_content">
          <div class="contact_card bg-blue-500">
            <p class="title">今月の問い合わせ</p>
            <hr class="my-1" />
            <p class="value">{{ props.stats.current_month_count }}</p>
          </div>
          <div class="contact_card bg-blue-500">
            <p class="title">問題回答率</p>
            <hr class="my-1" />
            <p class="value">{{ Math.round((props.stats.solved_count / props.stats.total_count) * 100) }}%</p>
          </div>
          <div class="contact_card bg-gray-500">
            <p class="title">未回答</p>
            <hr class="my-1" />
            <p class="value">{{ props.stats.solved_count }}</p>
          </div>
          <div class="contact_card bg-green-500">
            <p class="title">回答済み</p>
            <hr class="my-1" />
            <p class="value">{{ props.stats.in_progress_count}}</p>
          </div>
        </div>

        <div id="graph_content" class="">
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
      <div class="flex justify-end   mb-4">
          <Pagination :links="props.contacts.links" />
      </div>

      <section class="text-gray-600 body-font">
        <div class="px-5 mx-auto">
          <div id="table_container" class="w-full mx-auto overflow-auto">
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
                      class="bg-orange-100 text-orange-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-orange-900 dark:text-orange-300"
                      >進行中</span
                    >
                    <span
                      v-else
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
        </div>
      </section>
    </template>
  </MainLayout>
</template>
<style scoped lang="scss">
#table_container {
  min-width: 1800px;

  & table {
    & td,
    th {
      white-space: nowrap;
    }
  }
}

#top_content {
  height: 30vh;
  display: flex;
  margin-bottom: 4vh;
  justify-content: space-between;

  & #card_content {
    width: 42%;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    height: 100%;

    & .contact_card {
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

  & #graph_content {
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
</style>