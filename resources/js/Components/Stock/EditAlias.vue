<script setup>
import { onMounted, ref, reactive } from "vue";
import axios from "axios";

const props = defineProps({
  aliases: Array,
  stock_id: Number,
});

const select_alias = reactive({
  id: null,
  alias: null,
});

const createAliasObject = reactive({
  stock_id: props.stock_id,
  alias: null,
});
const aliases = ref([]);

const selectToggle = (alias_id) => {
  const alias = aliases.value.find((alias) => alias.id === alias_id);
  if (alias) {
    alias.selected = !alias.selected;
  }
  // 選択が一つの場合、選択中オブジェクトに設定
  if (selectedAliasEqualZero()) {
    select_alias.id = alias_id;
    select_alias.alias = alias.alias;
  }
};

// 選択されているものが一つか
const selectedAliasEqualZero = () => {
  return aliases.value.filter((alias) => alias.selected).length === 1;
};

// selectedが一つでも選択されているか
const getSelectedAlias = () => {
  return aliases.value.filter((alias) => alias.selected).length > 0;
};

// 編集
const editAlias = () => {
  const target_alias = aliases.value.find(
    (alias) => alias.id === select_alias.id
  );
  if (target_alias) {
    if (target_alias.alias === select_alias.alias) {
      alert("編集前と同じです");
    } else {
      axios
        .put(route("stock.editAlias"), {
          stock_alias_id: select_alias.id,
          alias: select_alias.alias,
        })
        .then((res) => {
          if (res.data) {
            alert(
              `略名を編集しました。\n${target_alias.alias} → ${select_alias.alias}`
            );
            window.location.reload();
          } else {
            alert("略名の編集に失敗しました。");
          }
        })
        .catch((error) => {
          console.log(error);
          alert("略名の編集に失敗しました。");
        });
    }
  } else {
    alert("対象の略名が見つかりません");
  }
};

// 作成
const createAlias = () => {
  if (!createAliasObject.alias || !createAliasObject.alias.trim()) {
    return alert("略名を入力してください。");
  }
  const same_alias = aliases.value.find(
    (alias) => alias.alias === createAliasObject.alias
  );
  if (same_alias) {
    alert("この略名は既に登録されています");
  } else {
    axios
      .post(route("stock.createAlias"), {
        stock_id: createAliasObject.stock_id,
        alias: createAliasObject.alias,
      })
      .then((res) => {
        if (res.data) {
          alert(`略名「${createAliasObject.alias}」を追加しました。`);
          window.location.reload();
        } else {
          alert("略名の追加に失敗しました。");
        }
      })
      .catch((error) => {
        console.log(error);
        alert("略名の追加に失敗しました。");
      });
  }
};

// 削除
const deleteAlias = () => {
  let deleteAliasId = [];
  aliases.value.forEach((alias) => {
    if (alias.selected) {
      deleteAliasId.push(alias.id);
    }
  });
  if (deleteAliasId.length === 0) return;
  if (!confirm("選択した略名を削除しますか？")) return;

  axios
    .post(route("stock.deleteAlias"), { deleteAliasId: deleteAliasId })
    .then((res) => {
      if (res.data) {
        alert("略名を削除しました。");
        window.location.reload();
      } else {
        alert("略名の削除に失敗しました。");
      }
    })
    .catch((error) => {
      console.log(error);
      alert("略名の削除に失敗しました。");
    });
};

onMounted(() => {
  aliases.value = (props.aliases || []).map((a) => ({ ...a, selected: false }));
});
</script>
<template>
  <div
    class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 p-6"
  >
    <!-- ヘッダー -->
    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
      <div class="p-2 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-lg">
        <svg
          class="w-6 h-6 text-white"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M7 7h.01M7 3h5a1.99 1.99 0 011.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 013 12V7a4 4 0 014-4z"
          />
        </svg>
      </div>
      <h3 class="text-xl font-bold text-gray-800">略名登録</h3>
    </div>

    <!-- 登録済みの略名 -->
    <div class="flex flex-wrap gap-2 mb-5">
      <span
        @click="selectToggle(alias.id)"
        v-for="alias in aliases"
        :key="alias.id"
        :class="{
          'cursor-pointer text-sm px-3 py-1 rounded-lg border-2 transition-all duration-200': true,
          'bg-gray-50 text-gray-700 border-gray-200 hover:border-gray-300':
            !alias.selected,
          'bg-blue-50 text-blue-700 border-blue-400 font-bold': alias.selected,
        }"
        >{{ alias.alias }}</span
      >
      <span v-if="aliases.length === 0" class="text-sm text-gray-400"
        >登録済みの略名はありません。</span
      >
    </div>

    <!-- 編集（1件選択時） -->
    <div v-if="selectedAliasEqualZero()">
      <label class="block text-sm font-semibold text-gray-700 mb-2"
        >略名を編集</label
      >
      <div class="flex items-center gap-2">
        <input
          class="flex-1 px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 outline-none hover:border-gray-300"
          type="text"
          v-model="select_alias.alias"
        />
        <button
          @click="editAlias"
          class="px-5 py-3 bg-blue-500 hover:bg-blue-600 text-white font-bold rounded-xl transition-colors"
        >
          編集
        </button>
      </div>
    </div>

    <!-- 新規追加（0件または複数選択時） -->
    <div v-else>
      <p class="text-xs text-gray-500 mb-2">
        編集する場合は登録済みの略名を1つ選択してください。
      </p>
      <label class="block text-sm font-semibold text-gray-700 mb-2"
        >略名を追加</label
      >
      <div class="flex items-center gap-2">
        <input
          class="flex-1 px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 outline-none hover:border-gray-300"
          type="text"
          placeholder="略名を入力"
          v-model="createAliasObject.alias"
        />
        <button
          @click="createAlias"
          class="px-5 py-3 bg-green-500 hover:bg-green-600 text-white font-bold rounded-xl transition-colors"
        >
          新規追加
        </button>
      </div>
    </div>

    <!-- 削除（1件以上選択時） -->
    <div v-if="getSelectedAlias()" class="mt-3">
      <button
        @click="deleteAlias"
        class="px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white font-bold rounded-xl transition-colors"
      >
        選択した略名を削除
      </button>
    </div>
  </div>
</template>
