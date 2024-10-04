<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { ref, reactive, onMounted } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";

const props = defineProps({
  groups: Array,
  users: Array,
});
const filterUsers = ref([]);
const filterUser = (str) => {
  filterUsers.value = props.users.filter((user) =>
    user.user_name.includes(str)
  );
  console.log(filterUsers.value);
};

const group_name = ref("");
const groups = ref(props.groups);

const sendGroupName = () => {
  if (group_name.value) {
    console.log(group_name.value);
    router.post(route("fax.group.create"), { group_name: group_name.value });

    location.reload();
  }
};
const sendGroupUsers = () => {
  console.log(editGroupData);
  router.post(route("fax.group.user.create"), editGroupData);
};

const editGroupData = reactive({
  id: null,
  name: null,
  mount_users: [],
});
const deleteGroup = () => {
  if (confirm("このグループを削除してもよろしいですか？")) {
    router.get(route("fax.group.delete", { id: editGroupData.id }));
  }
};
const createGroupUser = (user_id, user_name, group_name) => {
  if (editGroupData.mount_users.some((user) => user.user_id === user_id)) {
    return;
  }
  const userObject = {
    user_id: user_id,
    user_name: user_name,
    group_name: group_name,
    notify_flg: 0
  };
  editGroupData.mount_users.push(userObject);
  console.log(editGroupData.mount_users);
};
const changeNotify = (user_id, notify_flg) => {
  const user = editGroupData.mount_users.find((user) => user.user_id === user_id);
  if (user) {
    user.notify_flg = user.notify_flg ? null : 1;
  }
}
const deleteGroupUser = (user_id) => {
  console.log(user_id);

  if (!editGroupData.mount_users.some((user) => user.user_id === user_id)) {
    return;
  }

  editGroupData.mount_users = editGroupData.mount_users.filter(
    (user) => user.user_id !== user_id
  );
};
const editGroup = (group_id) => {
  const group = groups.value.find((group) => group.id === group_id);
  if (group) {
    editGroupData.id = group_id;
    editGroupData.name = group.name;
  }

  // 現状の割り当て一覧を取得
  axios
    .get(route("fax.getUserGroups"), {
      params: {
        group_id: group_id,
      },
    })
    .then((response) => {
      editGroupData.mount_users = response.data;
      document.querySelector('#edit_form').scrollIntoView()
      console.log(editGroupData.mount_users);
    })
    .catch((error) => {
      console.error("There was an error fetching the user groups:", error);
    });


};

onMounted(() => {
  console.log(props.groups);
});
</script>
<template>
  <MainLayout :title="'FAX振り分け'">
    <template #content>
      <section class="text-gray-600 body-font">
        <div class="container px-5 py-24 mx-auto">
          <div
            class="w-3/4 mx-auto overflow-auto flex flex-col items-start justify-around"
          >
            <div id="" class="w-full mt-8">
              <!-- 作成フォーム -->
              <div v-if="!editGroupData.id" class="max-auto max-w-xl">
                <h1 class="text-gray-600 text-2xl font-bold mb-4 text-center">
                  グループ作成
                </h1>

                <form @submit.prevent class="w-full max-w-lg">
                  <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full px-3">
                      <label
                        class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                        for="grid-password"
                      >
                        グループ名
                      </label>
                      <input
                        v-model="group_name"
                        class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                        id="grid-password"
                        type="text"
                        placeholder=""
                      />
                    </div>
                  </div>
                  <div class="flex items-center justify-between">
                    <button
                      @click="sendGroupName"
                      :class="{
                        'bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline': true,
                      }"
                      type="button"
                    >
                      追加
                    </button>
                  </div>
                </form>
              </div>

              <!-- 編集フォーム -->
              <div id="edit_form" class="pt-16" v-else>
                <h1 class="text-gray-600 text-2xl font-bold mb-4 text-center">
                  グループ詳細編集
                </h1>
                <form
                  @submit.prevent
                  class="bg-white rounded px-8 pt-6 pb-8 mb-4"
                >
                  <div class="w-2/3 mx-auto">
                    <div class="mb-8 ">
                      <label
                        class="block text-gray-700 text-sm font-bold mb-2"
                        for="group_name"
                      >
                        グループ名
                      </label>
                      <input
                        v-model="editGroupData.name"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="group_name"
                        type="text"
                        placeholder="Group1"
                      />
                    </div>

                    <div class="mb-8 ">
                      <label
                        class="block text-gray-700 text-sm font-bold mb-2"
                        for="group_name"
                      >
                        ユーザー割り当て
                      </label>
                      <input
                        @change="filterUser($event.target.value)"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        type="text"
                        name=""
                        id=""
                        placeholder="カーソルを抜けると検索されます"
                      />
                    </div>
                  </div>

                  <div class="flex justify-start items-start">
                    <div class="pr-8 w-1/2">
                      <section class="text-gray-600 body-font mt-8">
                        <div class="container mx-auto">
                          <div class="flex flex-col text-center w-full mb-4">
                            <h1
                              class="text-xl font-medium title-font mb-2 text-gray-500"
                            >
                              検索結果
                            </h1>
                          </div>
                          <div class="w-full mx-auto overflow-auto">
                            <table
                              class="table-auto w-full text-left whitespace-no-wrap"
                            >
                              <thead>
                                <tr>
                                  <th
                                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl"
                                  >
                                    所属
                                  </th>
                                  <th
                                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                                  >
                                    名前
                                  </th>

                                  <th
                                    class="w-10 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tr rounded-br"
                                  ></th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr v-for="user in filterUsers" :key="user.id">
                                  <td class="px-4 py-3">
                                    {{ user.group_name }}
                                  </td>
                                  <td class="px-4 py-3">
                                    {{ user.user_name }}
                                  </td>

                                  <td class="w-10 text-center">
                                    <button
                                      @click="
                                        createGroupUser(
                                          user.id,
                                          user.user_name,
                                          user.group_name
                                        )
                                      "
                                      class="bg-transparent hover:bg-green-500 text-green-700 font-semibold hover:text-white py-2 px-4 border border-green-500 hover:border-transparent rounded text-sm whitespace-nowrap"
                                    >
                                      追加
                                    </button>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </section>
                    </div>

                    <section class="pr-8 w-1/2 text-gray-600 body-font mt-8">
                      <div class="container mx-auto">
                        <div class="flex flex-col text-center w-full mb-4">
                          <h1
                            class="text-xl font-medium title-font mb-2 text-gray-500"
                          >
                            割り当て済み覧
                          </h1>
                        </div>
                        <div class="w-full mx-auto overflow-auto">
                          <table
                            class="table-auto w-full text-left whitespace-no-wrap"
                          >
                            <thead>
                              <tr>
                                <th
                                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl"
                                >
                                  所属
                                </th>
                                <th
                                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                                >
                                  名前
                                </th>
                                <th
                                  class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                                >
                                  通知
                                </th>

                                <th
                                  class="w-10 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tr rounded-br"
                                ></th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr
                                v-for="user in editGroupData.mount_users"
                                :key="user.user_id"
                              >
                                <td class="px-4 py-3">
                                  {{ user.group_name }}
                                </td>
                                <td class="px-4 py-3">
                                  {{ user.user_name }}
                                </td>
                                <td class="px-4 py-3">
                                  <button 
                                  @click="changeNotify(user.user_id, user.notify_flg)"
                                  :class="{'text-white font-bold py-2 px-4 rounded': true, 'bg-red-500 hover:bg-red-700': !user.notify_flg, 'bg-green-500 hover:bg-green-700': user.notify_flg, }">{{ user.notify_flg ? 'あり' : 'なし' }}</button>
                                </td>

                                <td class="w-10 text-center">
                                  <button
                                    @click="deleteGroupUser(user.user_id)"
                                    class="bg-transparent hover:bg-red-500 text-red-700 font-semibold hover:text-white py-2 px-4 border border-red-500 hover:border-transparent rounded text-sm whitespace-nowrap"
                                  >
                                    削除
                                  </button>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </section>
                  </div>

                  <div class="mt-8 flex items-center justify-center">
                    <button
                      @click="deleteGroup"
                      :class="{
                        'bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mr-4': true,
                      }"
                      type="button"
                    >
                      削除
                    </button>
                    <button
                      @click="sendGroupUsers"
                      :class="{
                        'bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline': true,
                      }"
                      type="button"
                    >
                      保存
                    </button>
                  </div>
                </form>
              </div>
            </div>

            <div class="w-full mt-20">
              <hr class="py-8" />

              <h1 class="text-gray-600 text-2xl font-bold mb-4 text-center">
                グループ一覧
              </h1>
              <table class="w-full table-auto text-left whitespace-no-wrap">
                <thead>
                  <tr>
                    <th
                      class="w-4 px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl"
                    >
                      id
                    </th>
                    <th
                      class="w-1/3 px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                    >
                      グループ名
                    </th>
                    <th
                      class="w-1/3 px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                    >
                      割り当てユーザ
                    </th>
                    <th
                      class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                    >
                      作成日
                    </th>

                    <th
                      class="w-10 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tr rounded-br"
                    ></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="group in groups" :key="group.id">
                    <td class="px-4 py-3">{{ group.id }}</td>
                    <td class="px-4 py-3 font-bold">{{ group.name }}</td>
                    <td class="px-4 py-3 w-1/5">
                      <span v-for="user in group.users" :key="user.id" class="bg-indigo-100 text-indigo-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-indigo-700 dark:text-red-300">{{ user.user_name }}</span>
                    </td>
                    <td class="px-4 py-3 w-1/5">
                      {{
                        new Date(group.created_at).toLocaleDateString("ja-JP")
                      }}
                    </td>

                    <td class="w-10 text-center">
                      <button
                        @click="editGroup(group.id)"
                        class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded text-sm whitespace-nowrap"
                      >
                        編集
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </section>
    </template>
  </MainLayout>
</template>