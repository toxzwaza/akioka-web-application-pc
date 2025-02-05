<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { onMounted, reactive, ref } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";

import Calender from "@/Components/Calender.vue";
import PiChart from "@/Components/PiChart.vue";

const pickUpDate = ref(null)
const inventoryOperationRecordsByDate = ref([]);

// カレンダーの日付が選択された場合の処理
const handleDateClick = (dateStr) => {
  console.log(dateStr);
  pickUpDate.value = dateStr
  inventoryOperationRecordsByDate.value = []
  getInventoryOperationRecordByDate(dateStr);
};

// クリックされた日付の入出庫データを取得
const getInventoryOperationRecordByDate = (target_date) => {
  axios
    .get(route("stock.stocks.getInventoryOperationRecordsByDate"), {
      params: {
        target_date: target_date,
      },
    })
    .then((res) => {
      console.log(res.data);
      inventoryOperationRecordsByDate.value = res.data;
    })
    .catch((error) => {
      console.log(error);
    });
};

const pie_data = {
  labels: ["赤", "青", "黄", "緑", "紫", "橙"],
  datasets: [
    {
      data: [12, 19, 3, 5, 2, 3],
      backgroundColor: [
        "rgba(255, 99, 132, 0.2)",
        "rgba(54, 162, 235, 0.2)",
        "rgba(255, 206, 86, 0.2)",
        "rgba(75, 192, 192, 0.2)",
        "rgba(153, 102, 255, 0.2)",
        "rgba(255, 159, 64, 0.2)",
      ],
      borderColor: [
        "rgba(255, 99, 132, 1)",
        "rgba(54, 162, 235, 1)",
        "rgba(255, 206, 86, 1)",
        "rgba(75, 192, 192, 1)",
        "rgba(153, 102, 255, 1)",
        "rgba(255, 159, 64, 1)",
      ],
      borderWidth: 1,
    },
  ],
};

onMounted(() => {});
</script>
<template>
  <MainLayout :title="'在庫管理'">
    <template #content>
      <h1 class="text-center text-xl font-bold text-gray-800 mb-12">
        在庫管理
      </h1>
      <div class="flex items-start justify-around">
        <div class="w-1/2">
          <Calender @date-click="handleDateClick" />
        </div>

        <div v-if="!inventoryOperationRecordsByDate.length > 0" class="w-1/3">
          <PiChart :title="'サンプル円グラフ'" :pie_data="pie_data"></PiChart>
        </div>
        <div class="w-2/5 operation_record_container" v-else>
          <section class="text-gray-600 body-font">
            <h2 class="font-bold text-indigo-500 text-lg">{{ pickUpDate }}のデータを表示しています。</h2>
            <div class="container mx-auto flex flex-wrap ">

              <div
                v-for="record in inventoryOperationRecordsByDate" :key="record.id"
                class="flex relative pt-10 pb-20 sm:items-center w-full mx-auto"
              >
                <div
                  class="h-full w-6 absolute inset-0 flex items-center justify-center"
                >
                  <div class="h-full w-1 bg-gray-200 pointer-events-none"></div>
                </div>
                <div
                  :class="{'flex-shrink-0 w-6 h-6 rounded-full mt-10 sm:mt-0 inline-flex items-center justify-center  text-white relative z-10 title-font font-medium text-sm': true, 'bg-indigo-500' : record.inventory_operation_id == 2, 'bg-pink-500' : record.inventory_operation_id == 8}"
                >
                  
                </div>
                <div
                  class="flex-grow md:pl-8 pl-6 flex sm:items-center items-start flex-col sm:flex-row"
                >
                  <div
                    class="flex-shrink-0 w-24 h-24 bg-indigo-100 text-indigo-500 rounded-full inline-flex items-center justify-center"
                  >
                    <img :src="record.stock_img_path" class="w-12 h-12" alt="">
                  </div>

                  <!-- 出庫の場合 -->
                  <div v-if="record.inventory_operation_id == 2" class="flex-grow sm:pl-6 mt-6 sm:mt-0 text-container">
                    <span class="record-user-name">
                      {{ record.user_name }}
                    </span>
                    <span>
                      さんが
                    </span>
                    <span :class="{'record-stock-name font-bold': true}">
                      {{ record.stock_name }}
                    </span>
                    <span>
                      を
                    </span>
                    <span class="record-quantity">
                      {{ record.quantity }}
                    </span>
                    <span>
                      個
                    </span>
                    <span class="record-operation-name">
                      {{ record.inventory_operation_name }}
                    </span>
                    <span>
                      しました。
                    </span>
                    <p class="leading-relaxed text-md">
                      場所: <span class="font-bold mr-2 record_location_name">{{ record.location_name }}</span>
                      <span class="record_address">{{ record.address }}</span>
                    </p>
                  </div>

                  <!-- 入庫の場合 -->
                  <div v-else class="flex-grow sm:pl-6 mt-6 sm:mt-0 text-container">

                    <span :class="{'record-stock-name font-bold': true}">
                      {{ record.stock_name }}
                    </span>
                    <span>
                      を
                    </span>
                    <span class="record-quantity">
                      {{ record.quantity }}
                    </span>
                    <span>
                      個
                    </span>
                    <span class="record-operation-name">
                      {{ record.inventory_operation_name }}
                    </span>
                    <span>
                      しました。
                    </span>
                    <p class="leading-relaxed text-md">
                      <span class="font-bold mr-2 record_location_name">{{ record.location_name }}</span>
                      <span class="record_address">{{ record.address }}</span>
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </section>
        </div>
      </div>
    </template>
  </MainLayout>
</template>
<style scoped lang="scss">
.operation_record_container{
  padding: 2%;
  margin-left: 2%;
  max-height: 60vh;
  overflow-y: scroll;
  box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
  border-radius: 5px;
  background-color: rgb(245, 245, 245);
  & .text_container{
    & .record_location_name{
      
    }
    & .record_address{

    }
  }
  
  ::-webkit-scrollbar {
    width: 8px;
  }

}

</style>