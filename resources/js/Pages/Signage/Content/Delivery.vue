<script setup>
import axios from "axios";
import { computed, onBeforeUnmount, onMounted, ref } from "vue";

const items = ref([]);
const isLoading = ref(true);
const errorMessage = ref("");

let refreshTimer = null;
let pageTimer = null;

const currentPage = ref(0);
const itemsPerPage = ref(10);

const recalcItemsPerPage = () => {
  const headerHeight = 220;
  const tableHeaderHeight = 72;
  const rowHeight = 68;
  const available = window.innerHeight - headerHeight - tableHeaderHeight;
  const count = Math.floor(available / rowHeight);
  itemsPerPage.value = Math.max(1, count);
};

const loadItems = async () => {
  isLoading.value = true;
  errorMessage.value = "";

  try {
    const res = await axios.get(route("delivery-signage.signage-data"));
    items.value = res.data ?? [];
    if (currentPage.value >= totalPages.value) {
      currentPage.value = 0;
    }
  } catch (error) {
    errorMessage.value = "表示データの取得に失敗しました。";
    console.error(error);
  } finally {
    isLoading.value = false;
  }
};

const hasItems = computed(() => items.value.length > 0);
const totalPages = computed(() => Math.max(1, Math.ceil(items.value.length / itemsPerPage.value)));
const pagedItems = computed(() => {
  const start = currentPage.value * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return items.value.slice(start, end);
});

onMounted(async () => {
  recalcItemsPerPage();
  await loadItems();
  refreshTimer = setInterval(loadItems, 300000);
  pageTimer = setInterval(() => {
    if (totalPages.value <= 1) {
      return;
    }
    currentPage.value = (currentPage.value + 1) % totalPages.value;
  }, 10000);
  window.addEventListener("resize", recalcItemsPerPage);
});

onBeforeUnmount(() => {
  if (refreshTimer) {
    clearInterval(refreshTimer);
  }
  if (pageTimer) {
    clearInterval(pageTimer);
  }
  window.removeEventListener("resize", recalcItemsPerPage);
});
</script>

<template>
  <section class="delivery-signage-screen text-white">
    <div class="screen-glow" />
    <div class="mx-auto h-full w-full max-w-[1760px] px-12 py-10">
      <header class="mb-8 flex items-end justify-between">
        <div>
          <p class="mb-2 text-sm font-semibold tracking-[0.28em] text-cyan-300/90">DELIVERY SIGNAGE</p>
          <h1 class="text-6xl font-black tracking-wide">納品サイネージ</h1>
        </div>
        <div class="rounded-2xl border border-white/20 bg-white/10 px-6 py-4 text-right backdrop-blur">
          <p class="text-sm text-slate-200">表示件数 / ページ</p>
          <p class="text-4xl font-extrabold text-cyan-300">{{ items.length }}</p>
          <p class="mt-1 text-sm text-slate-200">{{ currentPage + 1 }} / {{ totalPages }}</p>
        </div>
      </header>

      <p v-if="errorMessage" class="mb-6 rounded-xl border border-red-400/40 bg-red-600/35 px-5 py-4 text-lg">
        {{ errorMessage }}
      </p>

      <div class="table-shell h-[calc(100%-7.5rem)] overflow-hidden rounded-3xl border border-white/15">
        <table class="w-full table-fixed text-left text-2xl">
          <thead class="bg-white/10 backdrop-blur">
            <tr>
              <th class="w-1/4 px-7 py-5 font-bold tracking-wide text-cyan-200">注文者</th>
              <th class="w-2/4 px-7 py-5 font-bold tracking-wide text-cyan-200">品名</th>
              <th class="w-1/4 px-7 py-5 font-bold tracking-wide text-cyan-200">品番</th>
            </tr>
          </thead>
          <tbody>
            <template v-if="isLoading">
              <tr v-for="n in 6" :key="'ld-' + n" class="border-t border-white/10">
                <td class="px-7 py-5"><div class="h-8 rounded-lg bg-white/10 animate-pulse" /></td>
                <td class="px-7 py-5"><div class="h-8 rounded-lg bg-white/10 animate-pulse" /></td>
                <td class="px-7 py-5"><div class="h-8 rounded-lg bg-white/10 animate-pulse" /></td>
              </tr>
            </template>
            <template v-else>
              <tr
                v-for="item in pagedItems"
                :key="`${item.item_type}-${item.item_id}`"
                class="border-t border-white/10 transition-colors duration-300 odd:bg-white/[0.03] even:bg-white/[0.06]"
              >
                <td class="px-7 py-5 font-semibold text-slate-100">{{ item.order_user }}</td>
                <td class="px-7 py-5 text-slate-100">{{ item.name }}</td>
                <td class="px-7 py-5 font-mono text-cyan-100">{{ item.s_name }}</td>
              </tr>
              <tr v-if="!hasItems">
                <td colspan="3" class="px-6 py-16 text-center text-3xl text-slate-300">表示対象のデータはありません。</td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</template>

<style scoped>
.delivery-signage-screen {
  width: 100vw;
  height: 100vh;
  overflow: hidden;
  position: relative;
  background:
    radial-gradient(circle at 12% 15%, rgba(56, 189, 248, 0.22), transparent 45%),
    radial-gradient(circle at 88% 12%, rgba(37, 99, 235, 0.26), transparent 42%),
    linear-gradient(135deg, #020617 0%, #0f172a 52%, #111827 100%);
}

.screen-glow {
  position: absolute;
  inset: 0;
  pointer-events: none;
  background:
    linear-gradient(120deg, transparent 0%, rgba(56, 189, 248, 0.08) 35%, transparent 70%),
    linear-gradient(300deg, transparent 0%, rgba(99, 102, 241, 0.1) 42%, transparent 78%);
}

.table-shell {
  background: linear-gradient(170deg, rgba(15, 23, 42, 0.66), rgba(30, 41, 59, 0.44));
  box-shadow:
    0 24px 64px rgba(2, 6, 23, 0.62),
    inset 0 1px 0 rgba(255, 255, 255, 0.08);
}
</style>
