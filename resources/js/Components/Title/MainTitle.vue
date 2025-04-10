<script setup>
const props = defineProps({
  top: String,
  sub: String,
});
</script>
<template>
  <div class="flex flex-wrap w-4/5 mb-20">
    <div class="w-full mb-6 lg:mb-0">
      <h1
        class="sm:text-3xl text-2xl font-medium title-font mb-2 text-gray-900"
      >
        {{ props.top }}
      </h1>

      <p class="w-full leading-relaxed text-gray-500 mb-2">
        {{ props.sub }}
      </p>
      <div class="h-1 w-20 bg-indigo-500 rounded"></div>
    </div>
  </div>
</template>
<style scope lang="less">
</style>
