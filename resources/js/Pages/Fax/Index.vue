<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { onMounted, reactive, ref } from "vue";
import { router } from "@inertiajs/vue3";

const form = reactive({
  id: null,
  name: null,
  fax: null,
  group_id: 0,
});
const props = defineProps({
  fax_groups: Array,
  fax_sort_settings: Array,
});

const group_users = ref([]);
const handleChangeGroup = () => {
  const selectedGroup = props.fax_groups.find(
    (group) => group.id === form.group_id
  );

  if (selectedGroup) {
    group_users.value = selectedGroup.users;
  } else {
    group_users.value = [];
  }
};

const sendFaxSort = () => {
  if (!form.group_id) {
    alert("グループを選択してください。");
    return;
  }
  if (!form.name && !form.fax) {
    alert("振り分け条件を最低どちらか入力してください");
    return;
  }

  router.post(route("fax.sort.create"), form);
  changeCreate();
};
const deleteFaxSort = ()=> {
  router.get(route('fax.sort.delete'), {fax_sort_setting_id: form.id });
};
const editFaxSortSetting = (setting_id) => {
  const editSetting = props.fax_sort_settings.find( setting => setting.id === setting_id);
  form.id = setting_id;
  form.name = editSetting.name;
  form.fax = editSetting.fax;
  form.group_id = editSetting.fax_group_id;

  document.querySelector('#edit_form').scrollIntoView();
};

const changeCreate = () => {
  form.id = null;
  form.name = null;
  form.fax = null;
  form.group_id = null;
}
onMounted(() => {
  console.log(props.fax_groups);
});
</script>
<template>
  <MainLayout :title="'FAX振り分け'">
    <template #content>
      <div id="edit_form" class="px-8 my-8 pt-16">
        <h1 class="text-gray-600 text-2xl font-bold mb-4 text-center">
          振り分け設定
        </h1>

        <form @submit.prevent class="w-full max-w-xl mx-auto">
          <button @click="changeCreate" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 border border-gray-700 rounded mb-8 text-sm" v-if="form.id">新規作成</button>

          <div class="flex flex-wrap -mx-3 mb-6">
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
              <label
                class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                for="grid-first-name"
              >
                送信元名
              </label>
              <input
                class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                id="grid-first-name"
                type="text"
                placeholder="株式会社テスト"
                v-model="form.name"
              />
            </div>
            <div class="w-full md:w-1/2 px-3">
              <label
                class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                for="grid-last-name"
              >
                送信元FAX番号
              </label>
              <input
                class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                id="grid-last-name"
                type="text"
                placeholder="00000000000"
                v-model="form.fax"
              />
            </div>
          </div>
          <div class="flex flex-wrap -mx-3 mb-6">
            <div class="w-full px-3">
              <label
                class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                for="grid-password"
              >
                振り分け先グループ
              </label>
              <select
                @change="handleChangeGroup"
                name=""
                id=""
                class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                v-model="form.group_id"
              >
                <option value="0">選択して下さい。</option>
                <option
                  v-for="group in fax_groups"
                  :key="group.id"
                  :value="group.id"
                >
                  {{ group.name }}
                </option>
              </select>
              <p class="text-gray-600 text-xs italic">
                振り分け先を作成したグループから選択してください。
              </p>
              <div class="mt-4 flex justify-start flex-wrap items-center">
                <span
                  v-for="user in group_users"
                  :key="user.id"
                  class="bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-400 border border-gray-500"
                  >{{ user.user_name }}</span
                >
              </div>
            </div>
          </div>
          <div class="flex justify-end">
            <button
            v-if="form.id"
              @click="deleteFaxSort"
              class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-6 border border-red-700 rounded mr-4"
            >
              削除
            </button>

            <button
              @click="sendFaxSort"
              class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 border border-blue-700 rounded"
            >
              追加
            </button>
          </div>
        </form>
      </div>

      <section class="text-gray-600 body-font">
        <div class="container px-5 py-24 mx-auto">
          <div class="flex flex-col text-center w-full mb-20">
            <h1 class="text-2xl font-bold mb-4">振り分け一覧</h1>
            <p class="lg:w-2/3 mx-auto leading-relaxed text-base">
              FAXの振り分け設定を行います。
            </p>
          </div>
          <div class="lg:w-2/3 w-full mx-auto overflow-auto">
            <table class="table-auto w-full text-left whitespace-no-wrap">
              <thead>
                <tr>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl"
                  >
                    ID
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    送信元名
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    送信元番号
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    グループ名
                  </th>
                  <th
                    class="w-10 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tr rounded-br"
                  ></th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="setting in props.fax_sort_settings"
                  :key="setting.id"
                >
                  <td class="px-4 py-3">{{ setting.id }}</td>
                  <td class="px-4 py-3">{{ setting.name }}</td>
                  <td class="px-4 py-3">{{ setting.fax }}</td>
                  <td class="px-4 py-3 text-lg text-gray-900">
                    {{ setting.group_name }}
                  </td>

                  <td class="w-10 text-center">
                    <button
                      @click="editFaxSortSetting(setting.id)"
                      class="bg-transparent hover:bg-green-500 text-green-700 font-semibold hover:text-white py-2 px-4 border border-green-500 hover:border-transparent rounded text-sm whitespace-nowrap"
                    >
                      編集
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </template>
  </MainLayout>
</template>