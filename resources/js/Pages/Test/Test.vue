<script setup>
import { onMounted } from "vue";

const props = defineProps({
  records: Array,
});

onMounted(() => {
  console.log(props.records);
});
</script>
<template>
  <section class="text-gray-600 body-font">
    <div class="container px-5 py-24 mx-auto">
      <div class="flex flex-col text-center w-full mb-20">
        <h1
          class="sm:text-4xl text-3xl font-medium title-font mb-2 text-gray-900"
        >
          頻繁に出庫されるもの
        </h1>
        <p class="lg:w-2/3 mx-auto leading-relaxed text-base"></p>
      </div>
      <div class="lg:w-2/3 w-full mx-auto overflow-auto">
        <table class="table-auto w-full text-left whitespace-no-wrap">
          <thead>
            <tr>
              <th
                class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl"
              >
                id
              </th>
              <th
                class="w-10 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tr rounded-br"
              ></th>
              <th
                class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
              >
                品名
              </th>
              <th
                class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
              >
                品番
              </th>
              <th
                class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
              >
                出庫回数
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="record in props.records" :key="record.stock_id">
              <td class="px-4 py-3">
                {{ record.stock_id }}
              </td>
              <td class="px-4 py-3 w-32">
                <img
                  :src="
                    record.img_path && record.img_path.includes('storage')
                      ? `https://akioka.cloud/${record.img_path}`
                      : record.img_path
                  "
                  alt=""
                />
              </td>
              <td class="px-4 py-3">{{ record.name }}</td>
              <td class="px-4 py-3">{{ record.s_name }}</td>
              <td class="px-4 py-3 text-lg text-gray-900">
                {{ record.count }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="flex pl-4 mt-4 lg:w-2/3 w-full mx-auto">
        <a class="text-indigo-500 inline-flex items-center md:mb-2 lg:mb-0"
          >Learn More
          <svg
            fill="none"
            stroke="currentColor"
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            class="w-4 h-4 ml-2"
            viewBox="0 0 24 24"
          >
            <path d="M5 12h14M12 5l7 7-7 7"></path>
          </svg>
        </a>
        <button
          class="flex ml-auto text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded"
        >
          Button
        </button>
      </div>
    </div>
  </section>
</template>
<style scoped>
</style>
