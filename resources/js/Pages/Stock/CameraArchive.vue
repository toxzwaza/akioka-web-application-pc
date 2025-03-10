<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
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
      <h1 class="text-center text-xl font-bold text-gray-800 mb-12">
        カメラ録画
      </h1>

    <div class="flex justify-between items-start">
              <section class="w-1/3 text-gray-600 body-font">
        <div class="container px-5 mx-auto">

          <div class="w-full mx-auto overflow-auto">
            <table class="table-auto w-full text-left whitespace-no-wrap">
              <thead>
                <tr>
                  <th
                    class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl"
                  >
                    録画一覧
                  </th>
                  <th
                    class="w-10 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tr rounded-br"
                  ></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="movie in movies" :key="movie.id">
                  <td class="px-4 py-3">{{ movie }}</td>
                  <td class="w-10 text-center">
                    <button @click="selectWatchMovie(movie)" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm whitespace-nowrap">
                    視聴  
                  </button>
                  </td>
                </tr>
                
              </tbody>
            </table>
          </div>
          
        </div>
      </section>
      <div class="w-2/3">
          <p class="mb-2 ">再生中: {{ videoUrl }}</p>
          <video 
          :key="videoKey"
          controls  
          width="640" 
          height="360"
          class="h-full w-full rounded-lg shadow-lg"
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
