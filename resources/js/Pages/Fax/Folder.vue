<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { onMounted, ref } from "vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
  users: Array,
  searchName: String,
});
const searchName = ref("");
const filterUsers = ref(props.users);

const sendFolderName = (user_id, folder_name) => {
  console.log(user_id, folder_name);
  if (user_id) {
    if (!folder_name) {
      if (
        !confirm("フォルダ名が空の可能性があります。更新してよろしいですか？")
      ) {
        return;
      }
    }

    router.get(route("fax.folder.update"), {
      user_id: user_id,
      folder_name: folder_name,
      searchName: searchName.value,
    });
  }
};

const filterUser = () => {
  filterUsers.value = props.users.filter((user) =>
    user.name.includes(searchName.value)
  );
};

onMounted(() => {
  if (props.searchName) {
    searchName.value = props.searchName;
    filterUser();
  }
});
</script>
<template>
  <MainLayout :title="'FAX振り分け'">
    <template #content>
      <section class="text-gray-600 body-font">
        <div class="container px-5 py-24 mx-auto">
          <div class="flex flex-col text-center w-full mb-20">
            <h1
              class="sm:text-4xl text-3xl font-medium title-font mb-2 text-gray-900"
            >
              ユーザーフォルダ割り当て
            </h1>
            <p class="lg:w-2/3 mx-auto leading-relaxed text-base">
              各ユーザーに振り分けるフォルダを設定します。
            </p>
          </div>

          <form @submit.prevent class="w-full max-w-sm mx-auto mb-16">
            <div class="flex items-center border-b border-teal-500 py-2">
              <input
                class="appearance-none bg-transparent border-none w-full text-gray-700 mr-3 py-1 px-2 leading-tight focus:outline-none"
                type="text"
                placeholder="名前"
                v-model="searchName"
              />
              <button
                @click="filterUser"
                class="flex-shrink-0 bg-teal-500 hover:bg-teal-700 border-teal-500 hover:border-teal-700 text-sm border-4 text-white py-1 px-2 rounded"
                type="button"
              >
                絞込
              </button>
            </div>
          </form>

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
                    氏名
                  </th>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                  >
                    フォルダ名
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="user in filterUsers" :key="user.id">
                  <td class="px-4 py-3">{{ user.id }}</td>
                  <td class="px-4 py-3">{{ user.name }}</td>
                  <td class="px-4 py-3">
                    <input
                      @change="sendFolderName(user.id, $event.target.value)"
                      type="text"
                      name="folder_name"
                      :value="user.fax_folder_name"
                      :placeholder="user.fax_folder_name ? '' : '未設定'"
                      :class="{ 'block w-full text-center': true }"
                    />
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