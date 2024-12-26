<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { onMounted, reactive, ref } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";

const msg = ref("");
const props = defineProps({
  users: Array,
  notify_groups: Array,
});
const notify_content = reactive({
  mentions: [],
  user_name_input: "",
  message: "",
});

const addUser = (val) => {
  const user = props.users.find((user) => user.id === parseInt(val));
  if (user) {
    const index = notify_content.mentions.findIndex(
      (mention) => mention.id === user.id
    );
    if (index === -1) {
      msg.value = `${user.name}を追加しました。`;
      notify_content.mentions.push(user);
    } else {
      // 既に追加されている場合、削除
      msg.value = `${user.name}を削除しました。`;
      notify_content.mentions.splice(index, 1);
    }
  }

  notify_content.user_name_input = "";
};

const sendNotify = () => {
  if (notify_content.mentions.length > 0 && notify_content.message) {
    const sendMentionIds = [];
    notify_content.mentions.forEach((user) => {
      sendMentionIds.push(user.email);
    });
    axios
      .post(route("message.sendNotify"), {
        mentionIds: sendMentionIds,
        message: notify_content.message,
      })
      .then((res) => {
        console.log(res.data);
        msg.value = res.data.message;
      })
      .catch((error) => {
        console.log(error);
      });
  } else {
    alert("送信ユーザーが一人も存在しないか、メッセージが入力されていません。");
  }
};

const createGroup = () => {
  const group_name = prompt("グループ名を入力してください。");
  if (group_name) {
    if (confirm(`${group_name}でグループを作成します。よろしいですか？`)) {
      const group_users = [];
      notify_content.mentions.forEach((user) => {
        group_users.push(user.id);
      });

      axios
        .post(route("message.create.group"), {
          group_users: group_users,
          group_name: group_name,
        })
        .then((res) => {
          console.log(res.data);
          msg.value = res.data.msg;
        })
        .catch((error) => {
          console.log(error);
        });
    }
  } else {
    alert("キャンセルされました。");
  }
};

const changeSelectGroup = (val) => {
  const group = props.notify_groups.find((group) => group.id === parseInt(val));
  if (group) {
    notify_content.mentions = group.users.map(user => ({ id: user.user_id, email: user.email, name : user.user_name }));
  } else {
    notify_content.mentions = [];
  }

}
onMounted(() => {
  notify_content.message = "";
  console.log(props.notify_groups)
  // props.notify_groups[0].users.forEach(user => {
  //   addUser(user.user_id)
  // })
});
</script>
<template>
  <MainLayout :title="'Teams通知送信'">
    <template #content>
      <h1 class="text-center text-xl font-bold text-gray-800">Teams通知送信</h1>
      <section class="mt-8">
        <form class="w-2/3 mx-auto max-w-lg">
          <p class="text-red-500 py-4 text-sm font-mono">
            <span class="mr-2 font-bold">メッセージ :</span> {{ msg }}
          </p>
          <hr class="mb-4" />
          <div class="flex flex-wrap -mx-3 mb-6">
            <div class="w-full px-3 mb-2">
              <label
                class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                for="grid-password"
              >
                グループ
              </label>
              <select
                class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                name=""
                id=""
                @change="changeSelectGroup($event.target.value)"
              >
                <option value="">選択してください。</option>
                <option
                  v-for="notify_group in props.notify_groups"
                  :key="notify_group.id"
                  :value="notify_group.id"
                >
                  {{ notify_group.name }}
                </option>
              </select>
            </div>
            <div class="w-full px-3 mb-2">
              <label
                class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                for="grid-password"
              >
                名前
              </label>
              <input
                class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                id="user_name"
                type="text"
                list="message_users"
                v-model="notify_content.user_name_input"
                @change="addUser($event.target.value)"
              />
              <datalist id="message_users">
                <option
                  v-for="user in props.users"
                  :key="user.id"
                  :value="user.id"
                >
                  {{ user.name }}
                </option>
              </datalist>
            </div>
            <div class="pl-4">
              <h2
                class="mb-2 text-lg font-semibold text-gray-900 dark:text-white"
              >
                送信予定ユーザー
              </h2>
              <ul
                class="max-w-md space-y-1 text-gray-500 list-disc list-inside dark:text-gray-400"
              >
                <li
                  v-for="mention in notify_content.mentions"
                  :key="mention.id"
                >
                  {{ mention.name }}
                </li>
              </ul>

              <button
                @click.prevent="createGroup"
                v-if="notify_content.mentions.length > 0"
                type="button"
                class="mt-4 text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
              >
                選択中のユーザーでグループを作成する
              </button>
            </div>

            <div class="w-full px-3 my-8">
              <label
                class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                for="grid-password"
              >
                メッセージ
              </label>
              <textarea
                class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                name=""
                id=""
                cols="30"
                rows="10"
                v-model="notify_content.message"
              ></textarea>
            </div>
          </div>
          <div class="flex justify-center">
            <button
              @click.prevent="sendNotify"
              class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 border border-blue-700 rounded"
            >
              送信
            </button>
          </div>
        </form>
      </section>
    </template>
  </MainLayout>
</template>