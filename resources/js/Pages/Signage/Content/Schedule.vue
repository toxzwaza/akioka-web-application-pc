<script setup>

const props = defineProps({
  file_path: String,
});

</script>
<template>
  <div id="schedule_img_container">
    <img :src="'/storage/schedule_file/'+ props.file_path" alt="スケジュール画像" />
  </div>
</template>
<style scoped>
#schedule_img_container {
  height: 100vh;
  width: 100vw;
  overflow: hidden;
  background-color: #cbdeff;
}
#schedule_img_container img {
  height: 100%;
  width: 100%;
  object-fit:contain;
}
</style>
