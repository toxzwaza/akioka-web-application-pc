<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import PageHeader from "@/Components/UI/PageHeader.vue";
import SectionCard from "@/Components/UI/SectionCard.vue";
import Button from "@/Components/UI/Button.vue";
import Icon from "@/Components/UI/Icon.vue";
import { onMounted , ref } from "vue"
import axios from "axios"

const movies = ref([])
const videoUrl = ref('')
const videoKey = ref(0); // キーを追加して強制的な再レンダリングのために使用


const getCameraMovies = () => {
  axios.get(route('stock.getCameraMovies'))
  .then(res => {
    console.log(res.data)
    movies.value = res.data
    if(res.data.length > 0){
      selectWatchMovie(res.data[0])
    }
  })
  .catch(error => {
    console.log(error)
  })
}

const selectWatchMovie = (movie_name) => {
  videoUrl.value = `/videos/${movie_name}`;
  videoKey.value++; // キーを更新して再レンダリングを強制
  console.log(movie_name, videoUrl.value);
};

onMounted(()=> {
  getCameraMovies()
})
</script>
<template>
  <MainLayout :title="'在庫管理'">
    <template #content>
      <PageHeader
        title="カメラ録画"
        subtitle="録画一覧から映像を選択して再生できます。"
      />

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        <!-- 録画一覧 -->
        <SectionCard title="録画一覧" padding="none" class="lg:col-span-1">
          <ul class="divide-y divide-border">
            <li
              v-for="movie in movies"
              :key="movie.id"
              class="flex items-center justify-between gap-3 px-4 py-3"
              :class="videoUrl.includes(movie) ? 'bg-primary-50' : ''"
            >
              <span
                class="flex items-center gap-2 text-sm min-w-0"
                :class="videoUrl.includes(movie) ? 'font-bold text-primary-700' : 'text-content'"
              >
                <Icon
                  v-if="videoUrl.includes(movie)"
                  name="play_circle"
                  size="sm"
                  class="text-primary-600 shrink-0"
                />
                <span class="truncate">{{ movie }}</span>
              </span>
              <Button
                variant="primary"
                size="sm"
                icon-left="play_arrow"
                @click="selectWatchMovie(movie)"
              >
                視聴
              </Button>
            </li>
          </ul>
          <p
            v-if="movies.length === 0"
            class="px-4 py-8 text-center text-sm text-content-muted"
          >
            録画データがありません。
          </p>
        </SectionCard>

        <!-- 再生プレイヤー -->
        <div class="lg:col-span-2 bg-surface-base border border-border rounded-card shadow-card p-6">
          <p class="mb-3 text-sm text-content-muted">再生中: {{ videoUrl }}</p>
          <video
            :key="videoKey"
            controls
            autoplay
            width="640"
            height="360"
            class="h-full w-full rounded-md border border-border"
          >
            <source :src="videoUrl" type="video/mp4">
            お使いのブラウザは動画再生に対応していません。
          </video>
        </div>
      </div>
    </template>
  </MainLayout>
</template>
<style scoped lang="scss">
</style>
