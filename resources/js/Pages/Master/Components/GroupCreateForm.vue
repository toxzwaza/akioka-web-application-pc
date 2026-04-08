<script setup>
import { reactive } from "vue";
import axios from "axios";

const emit = defineEmits(["registered"]);

const form = reactive({
  name: null,
  phone_number: null,
});

const createGroup = () => {
  axios
    .post(route("master.store.groups"), form)
    .then((res) => {
      if (res.data.status) {
        if (confirm("部署登録が完了しました。続けて登録を行いますか？")) {
          form.name = null;
          form.phone_number = null;
        }
        emit("registered");
      }
    })
    .catch((error) => {
      console.log(error);
    });
};
</script>
<template>
  <form
    method="post"
    class="mx-auto grid max-w-screen-md gap-4 sm:grid-cols-2"
    @submit.prevent
  >
    <div class="sm:col-span-2">
      <label
        for="group_name"
        :class="{
          'mb-2 inline-block text-sm text-gray-800 sm:text-base': true,
          'text-red-500': !form.name,
        }"
        >部署名</label
      >
      <input
        id="group_name"
        name="name"
        class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring"
        v-model="form.name"
      />
    </div>

    <div class="sm:col-span-2">
      <label
        for="phone_number"
        class="mb-2 inline-block text-sm text-gray-800 sm:text-base"
        >電話番号</label
      >
      <input
        id="phone_number"
        name="phone_number"
        class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring"
        v-model="form.phone_number"
      />
    </div>

    <div class="flex items-center justify-between sm:col-span-2 mt-8">
      <button
        type="button"
        @click="createGroup"
        class="inline-block rounded-lg bg-indigo-500 px-8 py-3 text-center text-sm font-semibold text-white outline-none ring-indigo-300 transition duration-100 hover:bg-indigo-600 focus-visible:ring active:bg-indigo-700 md:text-base"
      >
        新規登録
      </button>

      <span class="text-sm text-gray-500">必須</span>
    </div>
  </form>
</template>
