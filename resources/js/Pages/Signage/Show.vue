<script setup>
const props = defineProps({
  file_name: String
})


import { ref, onMounted } from "vue";

const viewerUrl = ref(null);


onMounted(() => {
  
  viewerUrl.value = `/pdfjs/web/viewer.html?file=/storage/pdf/${props.file_name}`;
  console.log('実行', viewerUrl.value);

  // 10秒毎に次のページを表示
  setInterval(() => {
    const iframe = document.querySelector("iframe");

    if (iframe && iframe.contentWindow) {
      const pdfViewer = iframe.contentWindow.PDFViewerApplication;
      if (pdfViewer.page < pdfViewer.pagesCount) {
        pdfViewer.page++;
      } else {
        pdfViewer.page = 1;
      }
    }
  }, 30000);
});
</script>

<template>

  <div id="pdf-container">
    <!-- PDF.js Viewer を表示するための container -->
    <iframe
      ref="pdfViewer"
      :src="viewerUrl"
      style="width: 100%; height: 100vh; border: none"
    ></iframe>
  </div>
</template>



<style>
/* 必要に応じてスタイリング */
#pdf-container {
  display: flex;
  flex-direction: column;
  width: 100%;
  height: 100vh;
}
</style>