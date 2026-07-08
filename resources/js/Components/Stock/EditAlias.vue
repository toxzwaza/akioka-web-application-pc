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
  <div class="px-3 py-3 bg-gray-100 rounded-lg border border-gray-200">
    <h3 class="text-sm font-bold text-gray-700 mb-2">略名登録</h3>
    <!-- 登録済みの略名 -->
    <div class="flex flex-wrap gap-1">
      <span
        @click="selectToggle(alias.id)"
        v-for="alias in aliases"
        :key="alias.id"
        :class="{
          'cursor-pointer text-xs me-1 px-2.5 py-0.5 rounded-sm transition-all duration-200': true,
          'bg-gray-200 text-gray-800': !alias.selected,
          'bg-green-100 text-green-800 font-bold': alias.selected,
        }"
        >{{ alias.alias }}</span
      >
      <span v-if="aliases.length === 0" class="text-xs text-gray-500"
        >登録済みの略名はありません。</span
      >
    </div>

    <!-- 編集（1件選択時） -->
    <div v-if="selectedAliasEqualZero()" class="mt-3 flex items-center gap-2">
      <input
        class="block w-2/3 bg-white text-gray-700 border border-gray-300 rounded py-2 px-3 text-sm leading-tight focus:outline-none focus:border-blue-500"
        type="text"
        v-model="select_alias.alias"
      />
      <button
        @click="editAlias"
        class="text-sm bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
      >
        編集
      </button>
    </div>

    <!-- 新規追加（0件または複数選択時） -->
    <div v-else class="mt-3">
      <p class="text-xs text-gray-600 mb-1">
        編集する場合は登録済みの略名を1つ選択してください。
      </p>
      <div class="flex items-center gap-2">
        <input
          class="block w-2/3 bg-white text-gray-700 border border-gray-300 rounded py-2 px-3 text-sm leading-tight focus:outline-none focus:border-blue-500"
          type="text"
          placeholder="略名を入力"
          v-model="createAliasObject.alias"
        />
        <button
          @click="createAlias"
          class="text-sm bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
        >
          新規追加
        </button>
      </div>
    </div>

    <!-- 削除（1件以上選択時） -->
    <div v-if="getSelectedAlias()" class="mt-2">
      <button
        @click="deleteAlias"
        class="text-sm bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
      >
        削除
      </button>
    </div>
  </div>
</template>
