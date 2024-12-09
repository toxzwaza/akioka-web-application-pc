<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { onMounted, reactive } from "vue";
import axios from "axios";


const props = defineProps({
  user: Object,
  remoteComputers: Array
});

const form = reactive({
  user_id: null,
  machine_name: null,
  mac_address: null,
});

const addRemoteComputer = () => {
  axios.post(route('remote.store'), form)
  .then( res => {
    console.log(res.data)
    if(res.data.status === 'ok'){
      if(confirm('完了しました。再読み込みしますか？')){

        location.reload();
      }
    }else{
      alert('追加処理中にエラーが発生しました。')
    }
  })
  .catch(error => {
    console.log(error)
  })
}

onMounted(() => {
  form.user_id = props.user.id;
  console.log(props.remoteComputers)
});
</script>
<template>
  <MainLayout :title="'リモート接続'">
    <template #content>
      <div class="py-20">
        <section class="text-gray-600 body-font relative">
          <div class="container px-5 mx-auto">
            <div class="flex flex-col text-center w-full mb-12">
              <h1
                class="sm:text-3xl text-2xl font-medium title-font mb-4 text-gray-900"
              >
                リモート先追加
              </h1>
              <p class="lg:w-2/3 mx-auto leading-relaxed text-base">
                リモート先コンピュータの追加を行います。
              </p>
            </div>
            <div class="lg:w-1/2 md:w-2/3 mx-auto">
              <div class="p-2 w-full flex">
                <div class="relative w-1/2 px-4">

                  <label for="file" class="leading-7 text-sm text-gray-600"
                    >コンピュータ名</label
                  >
                  <input
                    type="text"
                    placeholder="ak-pc00-00"
                    id="machine_name"
                    name="machine_name"
                    class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
                    v-model="form.machine_name"
                  />
                </div>
                <div class="relative w-1/2 px-4">
                  <label for="file" class="leading-7 text-sm text-gray-600"
                    >物理アドレス</label
                  >
                  <input
                    type="text"
                    placeholder="FF-FF-FF-FF-FF"
                    id="name"
                    name="name"
                    class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
                    v-model="form.mac_address"
                  />
                </div>
              </div>

              <div class="p-2 w-full mt-4">
                <button
                  type="button"
                  class="flex mx-auto text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg"
                  @click="addRemoteComputer"
                >
                  追加
                </button>
              </div>
            </div>
          </div>
        </section>

        <hr class="my-12" />

        <!-- サイネージディスプレイ -->
        <section class="text-gray-600 body-font">
          <div class="container px-5 p mx-auto">
            <div class="flex flex-col text-center w-full mb-20">
              <h1
                class="sm:text-4xl text-3xl font-medium title-font mb-2 text-gray-900"
              >
                リモート先一覧
              </h1>
            </div>

            <div class="w-full mx-auto overflow-auto">
              <table class="table-auto w-full text-left whitespace-no-wrap">
                <thead>
                  <tr>
                    <th
                      class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl"
                    >
                      コンピュータ名
                    </th>
                    <th
                      class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                    >
                      許可物理アドレス
                    </th>
                    <th
                      class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                    >
                      作成日
                    </th>
                    <th
                      class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                    >
                      更新日
                    </th>

                  </tr>
                </thead>
                <tbody>
                  <tr v-for="remote in remoteComputers" :key="remote.id">
                    <td class="px-4 py-3">
                      {{
                        remote.machine_name
                      }}
                    </td>
                    <td class="px-4 py-3">
                      {{
                        remote.mac_address
                      }}
                    </td>
                    <td class="px-4 py-3">
                      {{
                        new Date(remote.created_at).toLocaleDateString('ja-JP', { year: 'numeric', month: '2-digit', day: '2-digit' }).replace(/\//g, '/')
                      }}
                    </td>
                    <td class="px-4 py-3">
                      {{
                        new Date(remote.updated_at).toLocaleDateString('ja-JP', { year: 'numeric', month: '2-digit', day: '2-digit' }).replace(/\//g, '/')
                      }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </section>
      </div>
    </template>
  </MainLayout>
</template>
<style scoped>
</style>
