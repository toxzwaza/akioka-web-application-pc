<script setup>
import { reactive } from "vue";

const form = reactive({
    keyword:  null,
    supplier_name: null,
})
</script>
<template>
  <div class="mr-8">
    <p class="mb-2 font-bold">検索</p>
    <div class="button_container flex items-bottom justify-start">
      <div class="w-62 mr-2">
        <label
          class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
          for="grid-last-name"
        >
          品名・品番から検索
        </label>
        <input
          class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
          type="text"
          name=""
          id=""
          v-model="form.keyword"
        />
      </div>



      <button
        class="ml-4 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
      >
        検索
      </button>
    </div>
  </div>
</template>
<style scoped lang="scss">
</style>
