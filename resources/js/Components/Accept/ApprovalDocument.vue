<script setup>
import { onMounted } from "vue";

const props = defineProps({
  approval_document: Object,
});

onMounted(() => {
  console.log(props.approval_document);
});
</script>

<template>
  <div class="py-2 bg-gray-700 px-8 flex items-baseline text-white font-bold">
    <button class="" @click="printApproval">
      印刷<i class="ml-1 text-white fas fa-print w-6 h-6"></i>
    </button>
  </div>
  <div id="approval_document" class="mx-auto ">
    <h1 class="text-center text-3xl mt-4 mb-6 text-gray-700 font-bold">稟議書</h1>

    <!-- <div class="flex flex-wrap -mx-3">
      <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
        <label
          class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
          for="grid-first-name"
        >
          起案部門
        </label>
        <p
          class="appearance-none block w-full bg-gray-200 text-gray-700 border border-transparent rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
        >
          {{ props.approval_document.user_name }}
        </p>
      </div>
      <div class="w-full md:w-1/2 px-3">
        <label
          class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
          for="grid-last-name"
        >
          起案者
        </label>
        <p
          class="appearance-none block w-full bg-gray-200 text-gray-700 border border-transparent rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
        >
          {{ props.approval_document.user_name }}
        </p>
      </div>
    </div>

    <div class="flex flex-wrap -mx-3">
      <div class="w-1/3 px-3 mb-6 md:mb-0">
        <label
          class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
          for="grid-city"
        >
          評価予定日
          <i class="fas fa-question-circle"></i>
        </label>
        <p
          class="appearance-none block w-full bg-gray-200 text-gray-700 border border-transparent rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
        >
          {{ props.approval_document.evalution_date }}
        </p>
      </div>
      <div class="w-1/3 px-3 mb-6 md:mb-0">
        <label
          class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
          for="grid-city"
        >
          希望納入日
        </label>
        <p
          class="appearance-none block w-full bg-gray-200 text-gray-700 border border-transparent rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
        >
          {{ props.approval_document.desire_delivery_date }}
        </p>
      </div>
      <div class="w-1/3 px-3 mb-6 md:mb-0">
        <label
          class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
          for="grid-state"
        >
          発注先
        </label>
        <div class="relative">
          <p
            class="appearance-none block w-full bg-gray-200 text-gray-700 border border-transparent rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
          >
            {{ props.approval_document.supplier_name }}
          </p>
        </div>
      </div>
    </div> -->

    <!-- <div class="flex flex-wrap -mx-3">
      <div class="w-1/3 px-3 mb-6 md:mb-0">
        <label
          class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
          for="grid-city"
        >
          単価
        </label>
        <p
          class="appearance-none block w-full bg-gray-200 text-gray-700 border border-transparent rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
        >
          {{ props.approval_document.price }}
        </p>
      </div>
      <div class="w-1/3 px-3 mb-6 md:mb-0">
        <label
          class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
          for="grid-state"
        >
          数量
        </label>
        <div class="relative">
          <p
            class="appearance-none block w-full bg-gray-200 text-gray-700 border border-transparent rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
          >
            {{ props.approval_document.quantity }}
          </p>
        </div>
      </div>
      <div class="w-1/3 px-3 mb-6 md:mb-0">
        <label
          class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
          for="grid-state"
        >
          金額
        </label>
        <div class="relative">
          <p
            class="appearance-none block w-full bg-gray-200 text-gray-700 border border-transparent rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
          >
            {{ props.approval_document.calc_price }}
          </p>
        </div>
      </div>
    </div> -->
    <!-- <div class="flex flex-wrap -mx-3">
      <div class="w-1/2 px-3 mb-6 md:mb-0">
        <label
          class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
          for="grid-city"
        >
          品名
        </label>
        <p
          class="appearance-none block w-full bg-gray-200 text-gray-700 border border-transparent rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
        >
          {{ props.approval_document.name }}
        </p>
      </div>
      <div class="w-1/2 px-3 mb-6 md:mb-0">
        <label
          class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
          for="grid-state"
        >
          品番
        </label>
        <div class="relative">
          <p
            class="appearance-none block w-full bg-gray-200 text-gray-700 border border-transparent rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
          >
            {{ props.approval_document.s_name }}
          </p>
        </div>
      </div>
    </div> -->
    <div class="flex flex-wrap -mx-3">
      <div class="w-full px-3 mb-6 md:mb-0">
        <label
          class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
          for="grid-first-name"
        >
          件名
        </label>
        <p
          class="appearance-none block w-full bg-gray-200 text-gray-700 border border-transparent rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
        >
          {{ props.approval_document.title }}
        </p>
      </div>
    </div>
    <div class="flex flex-wrap -mx-3">
      <div class="w-full px-3 mb-6 md:mb-0">
        <label
          class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
          for="grid-first-name"
        >
          発注内容
        </label>
        <p
          v-if="props.approval_document"
          v-html="props.approval_document.content.replace(/\n/g, '<br>')"
          class="appearance-none block w-full bg-gray-200 text-gray-700 border border-transparent rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
        ></p>
      </div>
    </div>

    <div class="flex flex-wrap -mx-3">
      <div class="w-full px-3 mb-6 md:mb-0">
        <label
          class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
          for="grid-first-name"
        >
          申請理由
        </label>
        <p
          v-if="props.approval_document"
          v-html="props.approval_document.main_reason.replace(/\n/g, '<br>')"
          class="appearance-none block w-full bg-gray-200 text-gray-700 border border-transparent rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
        ></p>
      </div>
    </div>

    <div class="flex flex-wrap -mx-3 mb-8">
      <div class="w-full px-3 mb-6 md:mb-0">
        <label
          class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
          for="grid-first-name"
        >
          選定理由
        </label>
        <p
          v-if="props.approval_document"
          v-html="props.approval_document.sub_reason.replace(/\n/g, '<br>')"
          class="appearance-none block w-full bg-gray-200 text-gray-700 border border-transparent rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
        ></p>
      </div>
    </div>
    <div class="flex items-center justify-start flex-wrap">
      <a v-for="document_image in props.approval_document.document_images" :key="document_image.id" :href="'https://akioka.cloud' + document_image.image_path" target="_blank">
        <img :src="'https://akioka.cloud' + document_image.image_path" alt="" class="w-1/5">
      </a>
    </div>

    <div class="mt-6 flex justify-end">
      <div
        v-for="approval in props.approval_document.approvals"
        :key="approval.id"
        class="stamp w-28 h-28 rounded-full border-4 border-red-500 text-red-600 flex flex-col justify-between items-center p-2 text-center"
      >
        <!-- 上部テキスト -->
        <div class="font-bold top_text w-full text-xs mt-1">アキオカ</div>

        <!-- 日付 -->
        <div class="py-1 w-full middle_text text-xl font-semibold leading-none">
          {{
            new Date(approval.created_at)
              .toLocaleDateString("ja-JP", {
                year: "2-digit",
                month: "2-digit",
                day: "2-digit",
              })
              .replace(/\//g, ".")
          }}
        </div>

        <!-- 名前 -->
        <div class="font-bold bottom_text w-full text-xs mb-1">
          {{ approval.user_name }}
        </div>
      </div>
    </div>
  </div>
</template>
<style scoped lang="scss">
#approval_document {
  // width: 40%;
  max-width: 50vw;
  height: auto;
  overflow-y: scroll;
  padding: 1.2%;
  margin-top: 1rem;

  @media print {
    #approval_document {
      padding: 1.2%;
    }
  }
  // max-height: 90vh;
  // overflow-y: scroll;
  box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px;
  border: 1px solid rgba(201, 201, 201, 0.473);
}

.stamp {
  & .middle_text {
    border-bottom: 3px solid red;
    border-top: 3px solid red;
  }
}
</style>