<script setup>
import { ref, onMounted } from "vue";

const currentDateMonth = ref();
const currentDateYear = ref();

const props = defineProps({
  color: Object,
});

const color_name = ref('');
const color_code = ref('');


onMounted(() => {
  console.log(props.color);
  switch (props.color.color) {
    case "green":
      color_code.value = '#00FF00';
      color_name.value = "緑色";
      break;
    case "blue":
      color_code.value = '#0000FF';
      color_name.value = "青色";
      break;
    case "yellow":
      color_code.value = '#FFFF00';
      color_name.value = "黄色";
      break;
  }

  const now = new Date();
  const year = String(now.getFullYear()).padStart(4, "0");
  const month = String(now.getMonth() + 1).padStart(2, "0");
  currentDateMonth.value = month;
  currentDateYear.value = year;
});
</script>
<template>
  <div class="body">
    <div class="container">
      <div class="title">玉掛けワイヤーロープの点検</div>
      <div class="date">
        {{ currentDateMonth + "年 " + currentDateYear + "月" }}
      </div>
      <div class="days">
        {{ currentDateMonth }}月の点検色は、<span :style="'color:' + color_code ">{{ color_name }}</span>です
      </div>

      <div class="">
        <img src="/img/inspection/crane-img.png" alt="" />
      </div>
      <div class="footer">みんなで事故をなくし、安全に作業を行おう！</div>
    </div>
  </div>
</template>
<style scoped>
.body {
  padding: 14% 0;
  font-family: Arial, sans-serif;
  margin: 0;
  display: flex;
  justify-content: center;
  align-items: baseline;
  height: 100vh;
  background-color: #ffffff;
  overflow: hidden;
}

.container {
  width: 1980px;
  /* height: 1080px; */
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  color: rgb(44, 44, 44);
  box-sizing: border-box;
}

.title {
  font-size: 3rem;
  width: 100%;
  background-color: #23239b;
  font-weight: bold;
  padding: 2rem 0;
  color: #fff;
  border-radius: 50px;
  margin: 0 auto 3rem;
  box-shadow: #13131333 3px 3px;
}

.date {
  font-size: 5rem;
  margin: 20px 0;
  font-weight: bold;
}

.days {
  font-size: 4rem;
  margin: 40px 0 80px;
  font-weight: bold;
  white-space: nowrap;
}

.days span {
  display: inline-block;
  padding: 20px 40px;
  background-color: rgb(74 74 74);
  font-weight: bold;
  border-radius: 10px;
}

.footer {
  font-size: 2rem;
  margin-top: 20px;
  padding-top: 4rem;
  font-weight: bold;
}
</style>  