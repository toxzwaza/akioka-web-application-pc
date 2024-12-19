<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { onMounted, reactive, ref } from "vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";


onMounted(() => {
});
</script>
<template>
  <MainLayout :title="'タイトル'">
    <template #content>
      <h1 class="text-center text-xl font-bold text-gray-800">タイトル</h1>


    </template>
  </MainLayout>
</template>