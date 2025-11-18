<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { onMounted, ref, reactive } from "vue";
import axios from "axios";

const props = defineProps({
  user: Object,
  groups: Array,
  processes: Array,
  positions: Array,
});

const form = reactive({
  id: null,
  emp_no: null,
  gender_flg: 0,
  name: null,
  email: null,
  password: null,
  group_id: 0,
  process_id: 0,
  position_id: 0,
  fax_folder_name: null,
  is_admin: null,
  dispatch_flg: null,
  part_flg: null,
  always_order_flg: null,
  del_flg : 0,
});

const createUser = () => {
  console.log(form);

  axios
    .post(route("master.store.users"), form)
    .then((res) => {
      console.log(res.data);
      if(res.data.status){
        alert('更新が完了しました。')
        window.location.reload()
      }
    })
    .catch((error) => {
      console.log(error);
    });
};
onMounted(() => {
  console.log(props.user);
  form.id = props.user.id;
  form.emp_no = props.user.emp_no;
  form.gender_flg = props.user.gender_flg;
  form.name = props.user.name;
  form.email = props.user.email;
  form.password = props.user.password;
  form.group_id = props.user.group_id;
  form.process_id = props.user.process_id;
  form.position_id = props.user.position_id;
  form.fax_folder_name = props.user.fax_folder_name;
  form.is_admin = props.user.is_admin ? true : false;
  form.dispatch_flg = props.user.dispatch_flg ? true : false;
  form.part_flg = props.user.part_flg ? true : false;
  form.always_order_flg = props.user.always_order_flg ? true : false;
  form.del_flg = props.user.del_flg

});
</script>
<template>
  <MainLayout>
    <template #content>
      <h1 class="text-center text-xl font-bold text-gray-800 mb-12">
        従業員詳細・編集
      </h1>

      <form
        method="post"
        class="mx-auto grid max-w-screen-md gap-4 sm:grid-cols-2"
      >
        <div class="sm:col-span-2">
          <label
            for="name"
            :class="{
              'mb-2 inline-block text-sm text-gray-800 sm:text-base': true,
            }"
            >社員番号</label
          >
          <input
            name="name"
            class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring"
            v-model="form.emp_no"
          />
        </div>
        <div class="sm:col-span-2">
          <label
            for="name"
            :class="{
              'mb-2 inline-block text-sm text-gray-800 sm:text-base': true,
              'text-red-500': !form.name,
            }"
            >氏名</label
          >
          <input
            name="name"
            class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring"
            v-model="form.name"
          />
        </div>
        <div class="sm:col-span-2">
          <label
            for="email"
            :class="{
              'mb-2 inline-block text-sm text-gray-800 sm:text-base': true,
              'text-red-500': !form.email,
            }"
            >Email</label
          >
          <input
            name="email"
            class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring"
            v-model="form.email"
          />
        </div>

        <div class="sm:col-span-2">
          <label
            for="pwd"
            :class="{
              'mb-2 inline-block text-sm text-gray-800 sm:text-base': true,
              'text-red-500': !form.password,
            }"
            >パスワード</label
          >
          <input
            name="pwd"
            class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring"
            v-model="form.password"
          />
        </div>

        <div class="sm:col-span-2">
          <label
            for="pwd"
            :class="{
              'mb-2 inline-block text-sm text-gray-800 sm:text-base': true,
            }"
            >性別</label
          >
          <div class="flex items-center">
            <label class="mr-4">
              <input
                type="radio"
                name="gender_flg"
                value="0"
                v-model="form.gender_flg"
              />
              男性
            </label>
            <label>
              <input
                type="radio"
                name="gender_flg"
                value="1"
                v-model="form.gender_flg"
              />
              女性
            </label>
          </div>
        </div>

        <hr class="my-8" />

        <div class="sm:col-span-2">
          <label
            for="group_id"
            :class="{
              'mb-2 inline-block text-sm text-gray-800 sm:text-base': true,
              'text-red-500': !form.group_id,
            }"
            >所属部署</label
          >
          <select
            name="group_id"
            class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring"
            v-model="form.group_id"
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
        <div
          class="sm:col-span-2"
          v-if="form.group_id == 3 || form.group_id == 4"
        >
          <label
            for="process_id"
            :class="{
              'mb-2 inline-block text-sm text-gray-800 sm:text-base': true,
              'text-red-500': form.group_id == 3 || form.group_id == 4,
            }"
            >製造工程</label
          >
          <p class="text-sm mb-4 text-gray-500">
            製造部に所属しない場合は、選択する必要はありません。
          </p>
          <div class="sm:col-span-2">
            <select
              name="process_id"
              class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring"
              v-model="form.process_id"
            >
              <option value="0" selected>未選択</option>
              <option
                v-for="process in props.processes"
                :value="process.id"
                :key="process.id"
              >
                {{ process.name }}
              </option>
            </select>
          </div>
        </div>

        <div class="sm:col-span-2">
          <label
            for="position_id"
            :class="{
              'mb-2 inline-block text-sm text-gray-800 sm:text-base': true,
              'text-red-500': !form.position_id,
            }"
            >役職</label
          >
          <select
            name="position_id"
            class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring"
            v-model="form.position_id"
          >
            <option value="0">未選択</option>
            <option
              v-for="position in positions"
              :key="position.id"
              :value="position.id"
            >
              {{ position.name }}
            </option>
          </select>
        </div>

        <hr class="sm:col-span-2 my-8" />

        <div class="sm:col-span-2">
          <div class="sm:col-span-2">
            <label
              for="fax_folder_name"
              :class="{
                'mb-2 inline-block text-sm text-gray-800 sm:text-base': true,
              }"
              >FAX振り分けフォルダ名</label
            >
            <p class="text-sm mb-4 text-gray-500">
              振り分ける必要がない場合、記載する必要はありません。
            </p>
            <div class="sm:col-span-2">
              <input
                class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring"
                type="text"
                name="fax_folder_name"
                v-model="form.fax_folder_name"
              />
            </div>
          </div>
        </div>

        <hr class="sm:col-span-2 my-8" />

        <div class="sm:col-span-2">
          <label
            for="is_admin"
            :class="{
              'mb-2 inline-block text-sm text-gray-800 sm:text-base': true,
            }"
            >管理者フラグ</label
          >
          <br />
          <input
            type="checkbox"
            name="is_admin"
            class="h-4 w-4 rounded border bg-gray-50 px-3 py-3 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring"
            v-model="form.is_admin"
          />
        </div>
        <div class="sm:col-span-2">
          <label
            for="dispatch_flg"
            :class="{
              'mb-2 inline-block text-sm text-gray-800 sm:text-base': true,
            }"
            >非常勤・派遣フラグ</label
          >
          <br />
          <input
            type="checkbox"
            name="dispatch_flg"
            class="h-4 w-4 rounded border bg-gray-50 px-3 py-3 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring"
            v-model="form.dispatch_flg"
          />
        </div>
        <div class="sm:col-span-2">
          <label
            for="part_flg"
            :class="{
              'mb-2 inline-block text-sm text-gray-800 sm:text-base': true,
            }"
            >パート社員フラグ</label
          >
          <br />
          <input
            type="checkbox"
            name="part_flg"
            class="h-4 w-4 rounded border bg-gray-50 px-3 py-3 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring"
            v-model="form.part_flg"
          />
        </div>

        <div class="sm:col-span-2">
          <label
            for="always_order_flg"
            :class="{
              'mb-2 inline-block text-sm text-gray-800 sm:text-base': true,
              'text-red-500': !form.name,
            }"
            >弁当注文グループ参加フラグ</label
          >
          <br />
          <p class="text-sm mb-4 text-gray-500">
            頻繁に弁当を注文する場合は、チェックを入れてください。
          </p>
          <input
            type="checkbox"
            name="always_order_flg"
            class="h-4 w-4 rounded border bg-gray-50 px-3 py-3 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring"
            v-model="form.always_order_flg"
          />
        </div>

        <div class="sm:col-span-2">
          <label
            for="position_id"
            :class="{
              'mb-2 inline-block text-sm text-gray-800 sm:text-base': true,
              'text-red-500': !form.position_id,
            }"
            >ステータス</label
          >
          <select
            name="position_id"
            class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring"
            v-model="form.del_flg"
          >
            <option value="0" class="font-bold text-green-500">有効</option>
            <option value="1" class="font-bold text-red-500">無効</option>

          </select>
        </div>

        <div class="flex items-center justify-between sm:col-span-2 mt-8">
          <button
            @click.prevent="createUser"
            class="inline-block rounded-lg bg-indigo-500 px-8 py-3 text-center text-sm font-semibold text-white outline-none ring-indigo-300 transition duration-100 hover:bg-indigo-600 focus-visible:ring active:bg-indigo-700 md:text-base"
          >
            編集
          </button>

          <span class="text-sm text-gray-500">必須</span>
        </div>
      </form>
    </template>
  </MainLayout>
</template>
<style scoped lang="scss">
</style>