<script setup>
import { reactive, onMounted, ref, onUnmounted } from "vue";
import { Link } from "@inertiajs/vue3";
// import ContributionsGrid from "@/components/ContributionsGrid.vue";

const props = defineProps({});
const current_time = ref("");
const search_keywords = ref([]);

const user_tasks = ref([]);
const filteredTasks = ref([]);

const logs = reactive({
  base_task_transactions: [],
  task_transactions: [],
  user_id: 0,
});

const form = reactive({
  user_id: null,
  user_name: null,
  task_name: null,
  task_description: null,
});

const completeTasks = ref([]);

const isWindowFocused = ref(true);
let updateCheckInterval = null;

const isLoading = ref(false);

const getData = () => {
  isLoading.value = true;
  axios.get(route("task.getData")).then((res) => {
    console.log(res.data);
    user_tasks.value = res.data.user_tasks;
    logs.base_task_transactions = res.data.task_transactions;
    logs.task_transactions = res.data.task_transactions;
    search_keywords.value = res.data.search_keywords;

    isLoading.value = false;
    getCompleteData();
  });
};

const filter_logs = (user_id) => {
  if (user_id) {
    logs.task_transactions = logs.base_task_transactions.filter(
      (task_transaction) => task_transaction.user_id === user_id
    );
    logs.user_id = user_id;
  } else {
    logs.task_transactions = logs.base_task_transactions;
    logs.user_id = 0;
  }
};

const filterTaskList = () => {
  if (!form.task_name) {
    filteredTasks.value = [];
    return;
  }

  filteredTasks.value = [];
  search_keywords.value.forEach((search_keyword) => {
    if (
      search_keyword.user_id == form.user_id &&
      search_keyword.task_name.includes(form.task_name)
    ) {
      console.log("タスク発見：", search_keyword.task_name);
      filteredTasks.value.push(search_keyword.task_name);
    }
  });
};

const openDescription = (task) => {
  if (task.description) {
    if (task.description_open) {
      task.description_open = false;
    } else {
      task.description_open = true;
    }
  }
  console.log(task);
};

const updateCheck = () => {
  updateCheckInterval = setInterval(() => {
    axios
      .get(route("task.update-check"), {
        params: {
          user_id: form.user_id,
        },
      })
      .then((res) => {
        if (res.data.update_flg) {
          if (isWindowFocused.value) {
            if (confirm("更新を検知しました。更新しますか？")) {
              getData();
            }
          } else {
            getData();
          }
        }
      });
  }, 60000);
};

const selectTask = (task) => {
  form.task_name = task;
  filteredTasks.value = [];
};

const createTask = () => {
  if (form.user_id && form.task_name) {
    axios
      .post(route("task.store"), {
        user_id: form.user_id,
        task_name: form.task_name,
        task_description: form.task_description,
      })
      .then((res) => {
        console.log(res.data);
        if (res.data.status) {
          getData();
          form.task_name = null;
          form.task_description = null;
        }
      });
  }
};

const saveUserIdToLocalStorage = () => {
  if (form.user_id !== null) {
    localStorage.setItem("user_id", form.user_id);
  }
};

const removeUserIdFromLocalStorage = () => {
  localStorage.removeItem("user_id");
  window.location.reload();
};

const reloadPage = () => {
  getData();
};

const getUserNameByUserId = () => {
  switch (form.user_id) {
    case "48":
      form.user_name = "中村仁美";
      break;
    case "68":
      form.user_name = "岡堂莉子";
      break;
    case "81":
      form.user_name = "三谷優月";
      break;
    case "120":
      form.user_name = "風早結衣";
      break;
    case "43":
      form.user_name = "中原清志";
      break;
    case "91":
      form.user_name = "村上飛羽";
      break;
    default:
      form.user_name = "不明なユーザー";
  }
};

const getUserName = (user_id) => {
  let user_name = "";
  switch (user_id) {
    case "48":
      user_name = "中村仁美";
      break;
    case "68":
      user_name = "岡堂莉子";
      break;
    case "81":
      user_name = "三谷優月";
      break;
    case "120":
      user_name = "風早結衣";
      break;
    case "43":
      user_name = "中原清志";
      break;
    case "91":
      user_name = "村上飛羽";
      break;
  }
  return user_name;
};

const selectUserName = () => {
  saveUserIdToLocalStorage();
  getUserNameByUserId();
};

const updateTaskStatus = (task_id, flg = null) => {
  //   ログインユーザーが作成したタスクのみ編集可能
  console.log(task_id);
  axios
    .post(route("task.update"), {
      task_id: task_id,
      flg: flg,
    })
    .then((res) => {
      console.log(res.data);

      if (res.data.status) {
        getData();
      }
    })
    .catch((error) => {
      console.log(error);
    });
};

const changeTaskName = (status) => {
  let task_name = "";

  if (status === 0) {
    task_name = "開始";
  } else if (status === 1) {
    task_name = "一時停止";
  } else if (status === 2) {
    task_name = "完了";
  }

  return task_name;
};

const getCurrentTime = () => {
  setInterval(() => {
    const now = new Date();
    const formattedTime = now.toLocaleTimeString("ja-JP", {
      hour: "2-digit",
      minute: "2-digit",
      second: "2-digit",
    });

    current_time.value = formattedTime;
  }, 1000);
};

const getCompleteData = () => {
  axios.get(route("task.getCompleteTasks")).then((res) => {
    completeTasks.value = res.data;
    console.log("completeTasks,", res.data);
  });
};

const updateValue = (task, flg) => {
  console.log(task);

  let value = ""

  if (flg == "task_name") {
    value = task.name;

    task.task_name_edit = 0;
  } else if (flg == "description") {
    value = task.description;

    task.description_edit = 0;
    task.description_open = 1;
  }

  axios
    .post(route("task.update-value"), {
      task_id: task.id,
      flg: flg,
      value: value,
    })
    .then((res) => {
      console.log(res.data);
    })
    .catch((error) => {
      console.log(error);
    });
};

onMounted(() => {
  getData();

  form.user_id = localStorage.getItem("user_id");
  if (form.user_id) {
    getUserNameByUserId();
  }

  updateCheck();
  getCurrentTime();

  getCompleteData();

  // ウィンドウのフォーカス状態を監視
  window.addEventListener("focus", () => {
    console.log("focusされました");
    isWindowFocused.value = true;
  });
  window.addEventListener("blur", () => {
    console.log("focusが解除されました");
    isWindowFocused.value = false;
  });
});

onUnmounted(() => {
  if (updateCheckInterval) {
    clearInterval(updateCheckInterval);
  }
});
</script>

<template>
  <div id="content" v-if="form.user_id">
    <div
      id="top_container"
      class="font-bold p-4 flex items-center justify-between"
    >
      <h2 id="current-time">{{ current_time }}</h2>

      <div class="flex items-center">
        <h2 @click="removeUserIdFromLocalStorage" class="">
          <i class="fas fa-user mr-1"></i> {{ form.user_name }}
        </h2>

        <button
          @click="reloadPage"
          class="ml-8 bg-white hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded inline-flex items-center"
        >
          <svg
            class="w-4 h-4 mr-2"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 4v5h.582M20 20v-5h-.582M5.582 9A7.5 7.5 0 0112 4.5h0a7.5 7.5 0 017.5 7.5h0a7.5 7.5 0 01-7.5 7.5h0a7.5 7.5 0 01-6.418-3.5M4 4l5 5M20 20l-5-5"
            />
          </svg>
          <span>更新</span>
        </button>
      </div>
    </div>

    <div class="pt-12 pb-10 bg-gray-50">
      <div class="relative w-2/3 mx-auto">
        <div class="w-full mr-2 flex items-center justify-center">
          <input
            v-model="form.task_name"
            class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
            type="text"
            name=""
            id=""
            placeholder="タスクを追加してください"
            @input="filterTaskList"
          />

          <ul
            v-if="filteredTasks.length"
            class="absolute top-14 bg-white border border-gray-200 rounded w-full z-10"
          >
            <li
              v-for="task in filteredTasks"
              :key="task"
              @click="selectTask(task)"
              class="px-4 py-2 cursor-pointer hover:bg-gray-200"
            >
              {{ task }}
            </li>
          </ul>
          <button
            @click.prevent="createTask"
            class="ml-2 bg_base_color hover:bg-blue-700 text-white font-bold py-3 px-4 rounded whitespace-nowrap"
          >
            追加
          </button>
        </div>

        <details class="mt-4">
          <summary class="tracking-wide text-gray-700 text-xs font-bold mb-2">
            詳細登録
          </summary>
          <textarea
            cols="30"
            rows="10"
            placeholder="メモを記載"
            v-model="form.task_description"
            class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
          ></textarea>
        </details>
      </div>
    </div>

    <section id="main_section" class="flex justify-between">
      <div id="left_container">
        <div class="log_container">
          <ul class="flex border-b mb-6">
            <li class="-mb-px mr-1">
              <a
                :class="{
                  'inline-block  py-2 px-4 text-grey-100 font-semibold': true,
                  'border-l border-t border-r rounded-t bg_base_color text-white':
                    logs.user_id == 0,
                  'bg-white': logs.user_id !== 0,
                }"
                @click="filter_logs(0)"
                >全員</a
              >
            </li>
            <li class="mr-1">
              <a
                :class="{
                  'inline-block  py-2 px-4 text-grey-100 font-semibold': true,
                  'border-l border-t border-r rounded-t bg_base_color text-white':
                    logs.user_id == 43,
                  'bg-white': logs.user_id !== 43,
                }"
                @click="filter_logs(43)"
                >中原</a
              >
            </li>
            <li class="mr-1">
              <a
                :class="{
                  'inline-block  py-2 px-4 text-grey-100 font-semibold': true,
                  'border-l border-t border-r rounded-t bg_base_color text-white':
                    logs.user_id == 48,
                  'bg-white': logs.user_id !== 48,
                }"
                @click="filter_logs(48)"
                >中村</a
              >
            </li>
            <li class="mr-1">
              <a
                :class="{
                  'inline-block  py-2 px-4 text-grey-100 font-semibold': true,
                  'border-l border-t border-r rounded-t bg_base_color text-white':
                    logs.user_id == 68,
                  'bg-white': logs.user_id !== 68,
                }"
                @click="filter_logs(68)"
                >岡堂</a
              >
            </li>
            <li class="mr-1">
              <a
                :class="{
                  'inline-block  py-2 px-4 text-grey-100 font-semibold': true,
                  'border-l border-t border-r rounded-t bg_base_color text-white':
                    logs.user_id == 81,
                  'bg-white': logs.user_id !== 81,
                }"
                @click="filter_logs(81)"
                >三谷</a
              >
            </li>
            <li class="mr-1">
              <a
                :class="{
                  'inline-block  py-2 px-4 text-grey-100 font-semibold': true,
                  'border-l border-t border-r rounded-t bg_base_color text-white':
                    logs.user_id == 91,
                  'bg-white': logs.user_id !== 91,
                }"
                @click="filter_logs(91)"
                >村上</a
              >
            </li>
            <li class="mr-1">
              <a
                :class="{
                  'inline-block  py-2 px-4 text-grey-100 font-semibold': true,
                  'border-l border-t border-r rounded-t bg_base_color text-white':
                    logs.user_id == 120,
                  'bg-white': logs.user_id !== 120,
                }"
                @click="filter_logs(120)"
                >風早</a
              >
            </li>
          </ul>

          <p
            v-for="task_transaction in logs.task_transactions"
            :key="task_transaction.id"
            :class="{
              'bg-yellow-100': task_transaction.user_id === 48,
              'bg-blue-100': task_transaction.user_id === 68,
              'bg-green-100': task_transaction.user_id === 81,
              'bg-pink-100': task_transaction.user_id === 120,
              'bg-orange-100': task_transaction.user_id === 43,
              'bg-purple-100': task_transaction.user_id === 91,
            }"
          >
            <span>{{
              new Date(task_transaction.created_at).toLocaleTimeString([], {
                hour: "2-digit",
                minute: "2-digit",
              })
            }}</span>
            {{
              `${task_transaction.user_name} さんが ${
                task_transaction.name
              } を ${changeTaskName(task_transaction.status)} しました。`
            }}
          </p>
        </div>
      </div>
      <div id="right_container">
        <div v-for="(user_task, user_id) in user_tasks" :key="user_id">
          <p class="font-bold text-grey-600">{{ getUserName(user_id) }}</p>

          <div v-for="task in user_task" :key="task.id" class="task_content">
            <div
              :class="{
                'p-2 flex justify-between rounded-lg bg-gray-100 items-center': true,
                'cursor-pointer': task.description,
              }"
              @click.stop="openDescription(task)"
            >
              <div class="w-4/5 flex items-center font-bold text-gray-700">
                <span
                  :class="{
                    'inline-block w-5 h-5 rounded-full ml-2': true,
                    'bg-green-500 animate-pulse': task.status == 0,
                    'bg-gray-500': task.status == 1,
                  }"
                ></span>

                <span class="ml-4 block w-full"
                  ><span
                    class="font-bold inline-block mr-3 text-gray-500 text-sm mb-1"
                    >{{ task.total_minutes }}分</span
                  >

                  <i
                    v-if="task.description"
                    class="text-gray-500 fas fa-sticky-note"
                  ></i>

                  <div id="task_name" class="flex items-center justify-start">
                    <i
                      v-if="
                        task.user_id == form.user_id && !task.task_name_edit
                      "
                      class="fas fa-edit text-gray-500 cursor-pointer"
                      @click.stop="task.task_name_edit = 1"
                    ></i>
                    <i
                      v-if="task.user_id == form.user_id && task.task_name_edit"
                      class="transition fas fa-check block bg-green-500 hover:text-green-500 hover:bg-white p-2 text-white rounded-full cursor-pointer"
                      @click.stop="updateValue(task, 'task_name')"
                    ></i>

                    <p
                      v-if="!task.task_name_edit"
                      :class="{ 'ml-4': task.user_id == form.user_id }"
                    >
                      {{ task.name }}
                    </p>
                    <input
                      v-else
                      type="text"
                      class="ml-2 bg-white appearance-none border-2 border-gray-200 rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
                      v-model="task.name"
                      @input="task.name = $event.target.value"
                    />
                  </div>
                </span>
              </div>

              <div class="button_container">
                <button
                  v-if="task.user_id == form.user_id && task.status == 1"
                  @click.stop="updateTaskStatus(task.id)"
                  class="w-16 bg_base_color hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                >
                  開始
                </button>

                <button
                  v-if="task.user_id == form.user_id && task.status == 0"
                  @click.stop="updateTaskStatus(task.id, 'stop')"
                  class="w-16 bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded mr-2"
                >
                  停止
                </button>
                <button
                  v-if="task.user_id == form.user_id && task.status == 0"
                  @click.stop="updateTaskStatus(task.id)"
                  class="w-16 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
                >
                  完了
                </button>
              </div>
            </div>

            <div
              @click="task.description_edit = 1"
              v-if="task.description_open && !task.description_edit"
              v-html="task.description.replace(/\n/g, '<br>')"
              class="description_container"
            ></div>
            <div
              v-else-if="task.description_edit"
              class="flex items-start justify-start mt-2 mb-4"
            >
              <i
                @click="updateValue(task, 'description')"
                class="transition fas fa-check inline-block bg-green-500 hover:text-green-500 hover:bg-white p-2 text-white rounded-full cursor-pointer"
              ></i>

              <textarea
                name=""
                id=""
                cols="30"
                rows="4"
                v-model="task.description"
                @input="task.description = $event.target.value"
                class="ml-2 bg-white appearance-none border-2 border-gray-200 rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
              ></textarea>
            </div>
          </div>

          <hr class="mt-4" />
        </div>
      </div>
    </section>

    <div class="mx-auto w-1/2">
      <div class="w-full">
        <!-- <ContributionsGrid /> -->
      </div>
    </div>

    <div id="completeDataTable">
      <table>
        <tbody>
          <tr>
            <th>タスクID</th>
            <th>タスク名</th>
            <th>実行者</th>
            <th>作成日時</th>
            <th>完了日時</th>
            <th>合計作業時間</th>
          </tr>
          <tr
            v-for="complete_task in completeTasks"
            :key="complete_task.id"
            :class="{
              'bg-yellow-100': complete_task.user_id === 48,
              'bg-blue-100': complete_task.user_id === 68,
              'bg-green-100': complete_task.user_id === 81,
              'bg-pink-100': complete_task.user_id === 120,
              'bg-orange-100': complete_task.user_id === 43,
              'bg-purple-100': complete_task.user_id === 91,
            }"
          >
            <td>{{ complete_task.task_id }}</td>
            <td>{{ complete_task.task_name }}</td>
            <td>{{ complete_task.user_name }}</td>
            <td>{{ complete_task.created_at }}</td>
            <td>{{ complete_task.updated_at }}</td>
            <td>{{ complete_task.total_minutes }}分</td>
          </tr>
        </tbody>
      </table>
    </div>

    <a :href="route('task.export')" class="export_link">エクスポート</a>

    <div v-if="isLoading" class="loading-overlay">
      <div class="spinner"></div>
      <p>更新中...</p>
    </div>
  </div>
  <div v-else>
    <div class="w-full max-w-xs mx-auto mt-32">
      <form class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <div class="mb-4">
          <label
            class="block text-gray-700 text-sm font-bold mb-2"
            for="username"
          >
            選択してください。
          </label>
          <select
            name=""
            id=""
            v-model="form.user_id"
            @change="selectUserName"
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
          >
            <option value="0">選択してください</option>
            <option value="48">中村仁美</option>
            <option value="68">岡堂莉子</option>
            <option value="81">三谷優月</option>
            <option value="120">風早結衣</option>
            <option value="43">中原清志</option>
            <option value="91">村上飛羽</option>
          </select>
        </div>

        <div class="flex items-center justify-between">
          <button
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
            type="button"
          >
            ログイン
          </button>
        </div>
      </form>
      <p class="text-center text-gray-500 text-xs">
        &copy;2025 Akioka Corp. All rights reserved.
      </p>
    </div>
  </div>
</template>
<style scoped lang="scss">
.bg_base_color {
  background-color: #3498db;
}
#content {
  //   height: 100vh;
  //   background-color: #f8f8f8;
  & #top_container {
    border-bottom: 4px solid #1381ca;
    background-color: #3498db;
    color: white;
  }

  min-height: 100vh;
  overflow: hidden;
  position: relative;
  & .export_link {
    position: absolute;
    right: 2%;
    bottom: 2%;
    text-decoration: underline;
    color: #4753ff;
  }

  & #main_section {
    padding: 2rem 3rem;
    max-height: 100vh;

    & #left_container {
      padding: 1rem 0.6rem;
      width: 48%;
      background-color: rgba(216, 216, 216, 0.24);
      box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;

      & .log_container {
        // min-height: 80vh;
        // max-height: 80vh;
        height: 100%;
        overflow-y: scroll;
        &::-webkit-scrollbar {
          width: 6px;
          height: 6px;
        }

        &::-webkit-scrollbar-track {
          background: #8d8d8d;
          border-radius: 10px;
        }

        &::-webkit-scrollbar-thumb {
          background: #3498db;
          border-radius: 10px;
        }

        &::-webkit-scrollbar-thumb:hover {
          background: #555;
        }

        & p {
          padding: 0.3rem 0.3rem 0.3rem 1rem;
          &::before {
            content: "・";
            margin-right: 0.4rem;
            font-weight: bold;
          }
        }
      }
    }

    & #right_container {
      padding: 1rem 0.6rem;
      width: 50%;
      box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
      overflow-y: scroll;
      &::-webkit-scrollbar {
        width: 6px;
        height: 6px;
      }

      &::-webkit-scrollbar-track {
        background: #8d8d8d;
        border-radius: 10px;
      }

      &::-webkit-scrollbar-thumb {
        background: #3498db;
        border-radius: 10px;
      }

      &::-webkit-scrollbar-thumb:hover {
        background: #555;
      }
      //   padding-left: 2rem;

      & .description_container {
        font-size: 0.9rem;
        margin-top: 0.4rem;
        margin-left: 1rem;
        margin-bottom: 1.2rem;
        &::before {
          content: ">";
          margin-right: 0.2rem;
          font-weight: bold;
          color: #4753ff;
        }
      }

      & > div {
        padding: 0.4rem 1rem;

        & .task_content {
          border: none;
          display: flex;
          flex-direction: column;
          padding: 1rem 0 0;
        }
      }
    }
  }

  & #completeDataTable {
    width: 100%;
    padding: 0 3rem;
    margin: 1.4rem auto 4rem;

    & table {
      width: 100%;
    }

    & table,
    td,
    th {
      border: 1px solid #595959;
      border-collapse: collapse;
      white-space: nowrap;
      text-align: center;
    }
    & td,
    th {
      padding: 3px;
      width: 30px;
      height: 25px;
    }
    & th {
      background: #ececec;
    }
  }
}

.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(255, 255, 255, 0.8);
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 5px solid #f3f3f3;
  border-top: 5px solid #3498db;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
</style>