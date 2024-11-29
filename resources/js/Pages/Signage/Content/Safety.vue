<script setup>
import { ref, onMounted } from "vue";

const currentDate = ref("");


const props  = defineProps({
    elapsed_days: Number
})

onMounted(() => {
  const now = new Date();
  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, '0');
  const day = String(now.getDate()).padStart(2, '0');
  currentDate.value = `${year}年${month}月${day}日`;
});



</script>
<template>
  <div class="body">
    <div class="container">
      <div class="title">安全第一</div>
      <div class="date">{{ currentDate }}</div>
      <div class="days">連続無事故日数 <span>{{ props.elapsed_days }}</span>日</div>
      <div class="icon"></div>
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
  background-color: #004d00;
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
  color: white;
  box-sizing: border-box;
}

.title {
  font-size: 10rem;
  font-weight: bold;
  letter-spacing: 16px;
  margin-bottom: 4rem;
  color: rgb(255, 235, 56);
  padding-bottom: 1rem;

}

.date {
  font-size: 5rem;
  margin: 20px 0;
  font-weight: bold;
}

.days {
  font-size: 5rem;
  margin: 40px 0 80px;
  font-weight: bold;
}

.days span {
  display: inline-block;
  padding: 20px 40px;
  background-color: white;
  color: #004d00;
  font-weight: bold;
  border-radius: 10px;
}

.icon {
  /* margin: 50px 0; */
  width: 600px;
  height: 600px;
  background-color: rgb(255, 235, 56);
  display: inline-block;
  clip-path: polygon(
    40% 0%,
    60% 0%,
    60% 40%,
    100% 40%,
    100% 60%,
    60% 60%,
    60% 100%,
    40% 100%,
    40% 60%,
    0% 60%,
    0% 40%,
    40% 40%
  );
  font-weight: bold;
}

.footer {
  font-size: 2rem;
  margin-top: 20px;
  padding-top: 4rem;
  font-weight: bold;
}
</style>  