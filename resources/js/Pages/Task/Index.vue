<script setup>
import { reactive, onMounted, ref } from "vue";

const props = defineProps({
  user_tasks: Array,
  task_transactions: Array,
});
const current_time = ref('')

const user_tasks = ref([]);
const filteredTasks = ref([]);

const logs = reactive({
  task_transactions: [],
  user_id: 0,
});

const filter_logs = (user_id) => {
  if (user_id) {
    logs.task_transactions = props.task_transactions.filter(
      (task_transaction) => task_transaction.user_id === user_id
    );
    logs.user_id = user_id
  } else {
    logs.task_transactions = props.task_transactions;
    logs.user_id = 0
  }
};
const form = reactive({
  user_id: null,
  user_name: null,
  task_name: null,
});

const filterTaskList = () => {
  if (!form.task_name) {
    filteredTasks.value = [];
    return;
  }

  const allTasks = [];
  Object.values(user_tasks.value).forEach((tasks) => {
    tasks.forEach((task) => {
      if (!allTasks.includes(task.name)) {
        allTasks.push(task.name);
      }
    });
  });

  filteredTasks.value = allTasks.filter((task) =>
    task.toLowerCase().includes(form.task_name.toLowerCase())
  );
};

const updateCheck = () => {
    setInterval(() => {
        axios.get(route('task.update-check'), {
            params: {
                user_id: form.user_id
            }
        })
        .then(res => {
            if(res.data.update_flg){
                window.location.reload()
            }
        })
    }, 300000); // 300000ミリ秒 = 5分
}

const selectTask = (task) => {
  form.task_name = task;
  filteredTasks.value = [];
};

const createTask = () => {
  console.log(form.task_name);
  if (form.user_id && form.task_name) {
    axios
      .post(route("task.store"), {
        user_id: form.user_id,
        task_name: form.task_name,
      })
      .then((res) => {
        console.log(res.data);
        if (res.data.status) {
          window.location.reload();
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
  window.location.reload();
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

const updateTaskStatus = (task_id) => {
  //   ログインユーザーが作成したタスクのみ編集可能
  console.log(task_id);
  axios
    .post(route("task.update"), { task_id: task_id })
    .then((res) => {
      console.log(res.data);

      if (res.data.status) {
        window.location.reload();
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

setInterval(() => {
  const now = new Date();
  const formattedTime = now.toLocaleTimeString("ja-JP", {
    hour: "2-digit",
    minute: "2-digit",
    second: "2-digit",
  });

  current_time.value = formattedTime

}, 1000);

onMounted(() => {
  console.log(props.task_transactions);

  form.user_id = localStorage.getItem("user_id");
  if (form.user_id) {
    getUserNameByUserId();
  }

  user_tasks.value = props.user_tasks;
  logs.task_transactions = props.task_transactions;

  updateCheck()
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
          総務部 ： {{ form.user_name }}
        </h2>

        <button
          @click="reloadPage"
          class="ml-4 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded inline-flex items-center"
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

    <section id="main_section" class="flex">
      <div id="left_container">
        <div class="flex items-center justify-start">
          <div class="relative w-2/3 mr-2">
            <input
              v-model="form.task_name"
              class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
              type="text"
              name=""
              id=""
              @input="filterTaskList"
            />
            <ul
              v-if="filteredTasks.length"
              class="absolute bg-white border border-gray-200 rounded mt-1 w-full z-10"
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
          </div>
          <button
            @click.prevent="createTask"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded"
          >
            追加
          </button>
        </div>
        <div class="log_container">
          <ul class="flex border-b mb-6">
            <li class="-mb-px mr-1">
              <a
                :class="{
                  'inline-block  py-2 px-4 text-grey-100 font-semibold': true,
                  'border-l border-t border-r rounded-t bg-gray-700 text-white': logs.user_id == 0,
                }"
                @click="filter_logs(0)"
                >全員</a
              >
            </li>
            <li class="mr-1">
              <a
                :class="{
                  'inline-block  py-2 px-4 text-grey-100 font-semibold': true,
                  'border-l border-t border-r rounded-t bg-gray-700 text-white': logs.user_id == 43,
                }"
                @click="filter_logs(43)"
                >中原</a
              >
            </li>
            <li class="mr-1">
              <a
                :class="{
                  'inline-block  py-2 px-4 text-grey-100 font-semibold': true,
                  'border-l border-t border-r rounded-t bg-gray-700 text-white': logs.user_id == 48,
                }"
                @click="filter_logs(48)"
                >中村</a
              >
            </li>
            <li class="mr-1">
              <a
                :class="{
                  'inline-block  py-2 px-4 text-grey-100 font-semibold': true,
                  'border-l border-t border-r rounded-t bg-gray-700 text-white': logs.user_id == 68,
                }"
                @click="filter_logs(68)"
                >岡堂</a
              >
            </li>
            <li class="mr-1">
              <a
                :class="{
                  'inline-block  py-2 px-4 text-grey-100 font-semibold': true,
                  'border-l border-t border-r rounded-t bg-gray-700 text-white': logs.user_id == 81,
                }"
                @click="filter_logs(81)"
                >三谷</a
              >
            </li>
            <li class="mr-1">
              <a
                :class="{
                  'inline-block  py-2 px-4 text-grey-100 font-semibold': true,
                  'border-l border-t border-r rounded-t bg-gray-700 text-white': logs.user_id == 91,
                }"
                @click="filter_logs(91)"
                >村上</a
              >
            </li>
            <li class="mr-1">
              <a
                :class="{
                  'inline-block  py-2 px-4 text-grey-100 font-semibold': true,
                  'border-l border-t border-r rounded-t bg-gray-700 text-white': logs.user_id == 120,
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
          <p class="font-bold">{{ getUserName(user_id) }}</p>

          <div v-for="task in user_task" :key="task.id" class="task_content">
            <div
              class="p-2 flex justify-between rounded-lg"
              :class="{ 'bg-gray-100 cursor-pointer': true }"
            >
              <div class="w-4/5 flex items-center font-bold text-gray-700">
                <span
                  :class="{
                    'inline-block w-5 h-5 rounded-full': true,
                    'bg-green-500 animate-pulse': task.status == 0,
                    'bg-gray-500': task.status == 1,
                  }"
                ></span>
                <span class="ml-2"
                  ><span class="mr-2 font-normal text-gray-500 text-sm"
                    >{{ task.total_minutes }}分</span
                  >{{ task.name }}</span
                >
              </div>

              <button
                v-if="task.user_id == form.user_id && task.status == 1"
                @click="updateTaskStatus(task.id)"
                class="w-16 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
              >
                開始
              </button>
              <button
                v-else-if="task.user_id == form.user_id && task.status == 0"
                @click="updateTaskStatus(task.id)"
                class="w-16 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
              >
                完了
              </button>
            </div>
          </div>

          <hr class="mt-4" />
        </div>
      </div>
    </section>
  </div>
  <div v-else>
    <select name="" id="" v-model="form.user_id" @change="selectUserName">
      <option value="0">選択してください</option>
      <option value="48">中村仁美</option>
      <option value="68">岡堂莉子</option>
      <option value="81">三谷優月</option>
      <option value="120">風早結衣</option>
      <option value="43">中原清志</option>
      <option value="91">村上飛羽</option>
    </select>
  </div>
</template>
<style scoped lang="scss">
#content {
  //   height: 100vh;
  background-color: #f8f8f8;
  overflow: hidden;

  & #main_section {
    padding: 1rem;

    & #left_container {
      width: 50%;

      & .log_container {
        padding: 1rem 3rem 0 0;

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
      width: 50%;

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
}
</style>