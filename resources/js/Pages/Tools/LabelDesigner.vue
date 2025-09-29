<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'
import { onMounted, ref, reactive, computed } from 'vue'

const dpiScale = ref(300 / 96) // 96dpi ベース -> 300dpi に近似

// ラベル物理サイズ(mm)
const form = reactive({
  widthMm: 100, // 幅 50mm
  heightMm: 100, // 高さ 30mm
  marginMm: 0,
  bgColor: '#ffffff',
})

// mm -> px 変換（96dpi 前提で CSS ピクセル計算）
const mmToPx = (mm) => (mm * 96) / 25.4
const pxWidth = computed(() => Math.round(mmToPx(form.widthMm)))
const pxHeight = computed(() => Math.round(mmToPx(form.heightMm)))
const pxMargin = computed(() => Math.round(mmToPx(form.marginMm)))

const textItems = ref([
  { id: 1, text: '品名', x: 10, y: 10, size: 16, bold: true },
])

// テキスト入力要素の参照（中央ぞろえ時のサイズ計測に使用）
const textInputRefs = ref({})

const addText = () => {
  const nextId = (textItems.value.at(-1)?.id ?? 0) + 1
  textItems.value.push({ id: nextId, text: 'テキスト', x: 10, y: 10, size: 14, bold: false })
  // 追加したテキストを選択状態に
  try { selected.value = { type: 'text', id: nextId } } catch (e) {}
  // 枠が選択されていれば、追加直後に枠の中心へ配置
  try {
    const t = textItems.value.find((it) => it.id === nextId)
    if (t && selectedRect.value) {
      centerTextInRect(t, selectedRect.value)
    }
  } catch (e) {}
}

const removeText = (id) => {
  textItems.value = textItems.value.filter((t) => t.id !== id)
}

// ===== 追加: 矩形（枠）と選択管理、サイズ上限 100mm =====
const rectItems = ref([]) // {id, x, y, width, height, borderWidth, borderColor, fillColor, radius}
const selected = ref({ type: null, id: null })

const addRect = () => {
  const nextId = (rectItems.value.at(-1)?.id ?? 0) + 1
  const w = Math.min(mmToPx(30), pxWidth.value - 2)
  const h = Math.min(mmToPx(20), pxHeight.value - 2)
  rectItems.value.push({
    id: nextId,
    x: 8,
    y: 8,
    width: Math.round(w),
    height: Math.round(h),
    borderWidth: 2,
    borderColor: '#000000',
    fillColor: 'transparent',
    radius: 0,
  })
  selected.value = { type: 'rect', id: nextId }
}

const removeRect = (id) => {
  rectItems.value = rectItems.value.filter((r) => r.id !== id)
  if (selected.value.type === 'rect' && selected.value.id === id) selected.value = { type: null, id: null }
}

const selectedRect = computed(() => (selected.value.type === 'rect' ? rectItems.value.find((r) => r.id === selected.value.id) : null))
const selectedText = computed(() => (selected.value.type === 'text' ? textItems.value.find((t) => t.id === selected.value.id) : null))

const clamp = (val, min, max) => Math.max(min, Math.min(max, val))

// mm 入力プロキシ（選択中の矩形向け）
const selRectWidthMm = computed({
  get: () => (selectedRect.value ? (selectedRect.value.width * 25.4) / 96 : 0),
  set: (mm) => {
    if (!selectedRect.value) return
    const px = mmToPx(mm)
    selectedRect.value.width = Math.round(clamp(px, 1, pxWidth.value - selectedRect.value.x))
  },
})
const selRectHeightMm = computed({
  get: () => (selectedRect.value ? (selectedRect.value.height * 25.4) / 96 : 0),
  set: (mm) => {
    if (!selectedRect.value) return
    const px = mmToPx(mm)
    selectedRect.value.height = Math.round(clamp(px, 1, pxHeight.value - selectedRect.value.y))
  },
})
const selRectXmm = computed({
  get: () => (selectedRect.value ? (selectedRect.value.x * 25.4) / 96 : 0),
  set: (mm) => {
    if (!selectedRect.value) return
    const px = mmToPx(mm)
    selectedRect.value.x = Math.round(clamp(px, 0, pxWidth.value - selectedRect.value.width))
  },
})
const selRectYmm = computed({
  get: () => (selectedRect.value ? (selectedRect.value.y * 25.4) / 96 : 0),
  set: (mm) => {
    if (!selectedRect.value) return
    const px = mmToPx(mm)
    selectedRect.value.y = Math.round(clamp(px, 0, pxHeight.value - selectedRect.value.height))
  },
})

// サイズ上限（100mm）の適用と要素のはみ出し補正
const enforceMaxSize = () => {
  form.widthMm = clamp(form.widthMm, 5, 100)
  form.heightMm = clamp(form.heightMm, 5, 100)
}

const fitItemsIntoCanvas = () => {
  for (const t of textItems.value) {
    t.x = clamp(t.x, 0, pxWidth.value - 1)
    t.y = clamp(t.y, 0, pxHeight.value - 1)
  }
  for (const r of rectItems.value) {
    r.x = clamp(r.x, 0, Math.max(0, pxWidth.value - r.width))
    r.y = clamp(r.y, 0, Math.max(0, pxHeight.value - r.height))
    r.width = clamp(r.width, 1, pxWidth.value - r.x)
    r.height = clamp(r.height, 1, pxHeight.value - r.y)
  }
}

// 選択中要素の中央ぞろえ（水平・垂直）
const centerSelected = () => {
  if (selectedRect.value) {
    const r = selectedRect.value
    r.x = Math.round(clamp((pxWidth.value - r.width) / 2, 0, pxWidth.value - r.width))
    r.y = Math.round(clamp((pxHeight.value - r.height) / 2, 0, pxHeight.value - r.height))
    return
  }
  if (selectedText.value) {
    const t = selectedText.value
    // 枠があれば、最も近い枠の中心へ配置。なければキャンバス中央。
    const rect = pickNearestRect(t) ?? null
    if (rect) {
      centerTextInRect(t, rect)
    } else {
      const { width: w, height: h } = measureText(t)
      t.x = Math.round(clamp((pxWidth.value - w) / 2, 0, pxWidth.value - 1))
      t.y = Math.round(clamp((pxHeight.value - h) / 2, 0, pxHeight.value - 1))
    }
  }
}

// テキストサイズ計測
const measureText = (t) => {
  const el = textInputRefs.value?.[t.id]
  const approxW = Math.max(8, Math.round((t.text?.length ?? 1) * t.size * 0.6))
  const approxH = Math.max(8, Math.round(t.size * 1.4))
  return { width: el?.offsetWidth ?? approxW, height: el?.offsetHeight ?? approxH }
}

// 指定枠の中心へテキスト配置
const centerTextInRect = (t, rect) => {
  const { width: w, height: h } = measureText(t)
  t.x = Math.round(clamp(rect.x + (rect.width - w) / 2, 0, pxWidth.value - 1))
  t.y = Math.round(clamp(rect.y + (rect.height - h) / 2, 0, pxHeight.value - 1))
}

// テキストがどれかの枠内にある場合はその枠へ中央寄せ
const centerTextIfInside = (t) => {
  const { width: w, height: h } = measureText(t)
  const cx = t.x + w / 2
  const cy = t.y + h / 2
  for (const r of rectItems.value) {
    if (cx >= r.x && cx <= r.x + r.width && cy >= r.y && cy <= r.y + r.height) {
      centerTextInRect(t, r)
      return true
    }
  }
  return false
}

// 最も近い枠（中心同士の距離が最小）を返す
const pickNearestRect = (t) => {
  if (!rectItems.value.length) return null
  const { width: w, height: h } = measureText(t)
  const tcx = t.x + w / 2
  const tcy = t.y + h / 2
  let best = null
  let bestD2 = Number.POSITIVE_INFINITY
  for (const r of rectItems.value) {
    const rcx = r.x + r.width / 2
    const rcy = r.y + r.height / 2
    const dx = rcx - tcx
    const dy = rcy - tcy
    const d2 = dx * dx + dy * dy
    if (d2 < bestD2) { bestD2 = d2; best = r }
  }
  return best
}

const handlePrint = async () => {
  const node = document.getElementById('label-canvas')
  if (!node) return
  // 高解像度のための拡大スケール
  const scale = dpiScale.value
  const canvas = await html2canvas(node, {
    backgroundColor: form.bgColor,
    scale,
    useCORS: true,
  })

  const dataUrl = canvas.toDataURL('image/png')
  const printWindow = window.open('', '_blank')
  if (!printWindow) return

  const pageWidthMm = form.widthMm + form.marginMm * 2
  const pageHeightMm = form.heightMm + form.marginMm * 2

  printWindow.document.write(`<!doctype html><html><head><meta charset="utf-8">
    <title>Print</title>
    <style>
      @page { size: ${pageWidthMm}mm ${pageHeightMm}mm; margin: 0; }
      html, body { margin: 0; padding: 0; }
      .page { width: ${pageWidthMm}mm; height: ${pageHeightMm}mm; display: flex; align-items: center; justify-content: center; }
      img { width: ${form.widthMm}mm; height: ${form.heightMm}mm; }
    </style>
  </head><body>
    <div class="page"><img src="${dataUrl}" /></div>
    <script>window.onload = () => { setTimeout(() => { window.print(); window.close(); }, 350); }<\/script>
  </body></html>`)
  printWindow.document.close()
}

onMounted(() => {})
</script>

<template>
  <MainLayout :title="'シール作成'">
    <template #content>
      <div class="px-5 py-6 mx-auto">
        <div class="mb-6">
          <h2 class="text-2xl font-semibold text-gray-800">シール作成</h2>
          <p class="text-gray-500">サイズ指定、テキスト配置、印刷までをブラウザで行えます（最大 100mm x 100mm）。</p>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
          <div class="md:col-span-1 bg-white rounded border p-4">
            <h3 class="font-semibold mb-3">設定</h3>
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <label class="text-sm text-gray-600">幅 (mm)</label>
                <input v-model.number="form.widthMm" @change="enforceMaxSize(); fitItemsIntoCanvas()" type="number" class="input" min="5" max="100" />
              </div>
              <div class="flex items-center justify-between">
                <label class="text-sm text-gray-600">高さ (mm)</label>
                <input v-model.number="form.heightMm" @change="enforceMaxSize(); fitItemsIntoCanvas()" type="number" class="input" min="5" max="100" />
              </div>
              <div class="flex items-center justify-between">
                <label class="text-sm text-gray-600">余白 (mm)</label>
                <input v-model.number="form.marginMm" type="number" class="input" min="0" max="50" />
              </div>
              <div class="flex items-center justify-between">
                <label class="text-sm text-gray-600">背景色</label>
                <input v-model="form.bgColor" type="color" class="w-16 h-8 p-0 border rounded" />
              </div>
              <div class="flex items-center justify-between">
                <label class="text-sm text-gray-600">DPI スケール</label>
                <select v-model.number="dpiScale" class="input">
                  <option :value="1">約 96dpi</option>
                  <option :value="2">約 192dpi</option>
                  <option :value="3">約 288dpi</option>
                </select>
              </div>
              <div class="pt-2 flex gap-2 flex-wrap">
                <button @click="addText" class="btn">テキスト追加</button>
                <button @click="addRect" class="btn">枠追加</button>
                <button @click="centerSelected" class="btn">中央ぞろえ</button>
              </div>

              <div class="mt-6 border-t pt-4">
                <h4 class="font-semibold mb-2">選択中の要素</h4>
                <div v-if="selectedRect" class="space-y-2">
                  <div class="flex items-center justify-between">
                    <label class="text-sm text-gray-600">X (mm)</label>
                    <input type="number" class="input" v-model.number="selRectXmm" min="0" :max="100" />
                  </div>
                  <div class="flex items-center justify-between">
                    <label class="text-sm text-gray-600">Y (mm)</label>
                    <input type="number" class="input" v-model.number="selRectYmm" min="0" :max="100" />
                  </div>
                  <div class="flex items-center justify-between">
                    <label class="text-sm text-gray-600">幅 (mm)</label>
                    <input type="number" class="input" v-model.number="selRectWidthMm" min="1" :max="100" />
                  </div>
                  <div class="flex items-center justify-between">
                    <label class="text-sm text-gray-600">高さ (mm)</label>
                    <input type="number" class="input" v-model.number="selRectHeightMm" min="1" :max="100" />
                  </div>
                  <div class="flex items-center justify-between">
                    <label class="text-sm text-gray-600">線の太さ (px)</label>
                    <input type="number" class="input" v-model.number="selectedRect.borderWidth" min="0" max="20" />
                  </div>
                  <div class="flex items-center justify-between">
                    <label class="text-sm text-gray-600">線の色</label>
                    <input type="color" class="w-16 h-8 p-0 border rounded" v-model="selectedRect.borderColor" />
                  </div>
                  <div class="flex items-center justify-between">
                    <label class="text-sm text-gray-600">塗り</label>
                    <input type="color" class="w-16 h-8 p-0 border rounded" v-model="selectedRect.fillColor" />
                  </div>
                  <div class="flex items-center justify-between">
                    <label class="text-sm text-gray-600">角丸 (px)</label>
                    <input type="number" class="input" v-model.number="selectedRect.radius" min="0" max="50" />
                  </div>
                  <div class="pt-2 text-right">
                    <button class="btn text-red-600" @click="removeRect(selectedRect.id)">選択枠を削除</button>
                  </div>
                </div>
                <div v-else-if="selectedText" class="space-y-2">
                  <div class="flex items-center justify-between">
                    <label class="text-sm text-gray-600">テキスト</label>
                    <input type="text" class="w-40 px-2 py-1 border rounded" v-model="selectedText.text" />
                  </div>
                  <div class="flex items-center justify-between">
                    <label class="text-sm text-gray-600">サイズ (px)</label>
                    <input type="number" class="input" v-model.number="selectedText.size" min="8" max="64" />
                  </div>
                  <label class="text-sm text-gray-600 inline-flex items-center gap-2">
                    <input type="checkbox" v-model="selectedText.bold" /> 太字
                  </label>
                  <div class="pt-2 text-right">
                    <button class="btn text-red-600" @click="removeText(selectedText.id)">選択テキストを削除</button>
                  </div>
                </div>
                <div v-else class="text-sm text-gray-500">要素をクリックすると編集できます。</div>
              </div>
            </div>
          </div>

          <div class="md:col-span-2">
            <div
              class="mx-auto border bg-white shadow-sm"
              :style="{ width: pxWidth + 'px', height: pxHeight + 'px', padding: pxMargin + 'px', backgroundColor: form.bgColor }"
            >
              <div id="label-canvas" class="relative w-full h-full overflow-hidden">
                <!-- 矩形（枠） -->
                <div
                  v-for="item in rectItems"
                  :key="'r-' + item.id"
                  class="absolute cursor-move select-none"
                  :style="{
                    left: item.x + 'px',
                    top: item.y + 'px',
                    width: item.width + 'px',
                    height: item.height + 'px',
                    backgroundColor: item.fillColor,
                    border: item.borderWidth + 'px solid ' + item.borderColor,
                    borderRadius: item.radius + 'px',
                  }"
                  draggable="true"
                  @click.stop="selected = { type: 'rect', id: item.id }"
                  @dragend="(e)=>{ item.x = Math.max(0, Math.min(item.x + e.offsetX, pxWidth - item.width)); item.y = Math.max(0, Math.min(item.y + e.offsetY, pxHeight - item.height)) }"
                />
                <div
                  v-for="item in textItems"
                  :key="item.id"
                  class="absolute cursor-move select-none"
                  :style="{ left: item.x + 'px', top: item.y + 'px' }"
                  draggable="true"
                  @click.stop="selected = { type: 'text', id: item.id }"
                  @dragstart="$event.dataTransfer.setData('text/plain', item.id)"
                  @dragend="(e)=>{ item.x = Math.max(0, Math.min(item.x + e.offsetX, pxWidth - 1)); item.y = Math.max(0, Math.min(item.y + e.offsetY, pxHeight - 1)); centerTextIfInside(item) }"
                >
                  <input
                    v-model="item.text"
                    class="bg-transparent outline-none"
                    :style="{ fontSize: item.size + 'px', fontWeight: item.bold ? '700' : '400' }"
                    :ref="(el) => { if (el) textInputRefs[item.id] = el }"
                  />
                  <div class="flex items-center gap-2 mt-1">
                    <input type="range" min="8" max="64" v-model.number="item.size" />
                    <label class="text-xs text-gray-500 flex items-center gap-1">
                      <input type="checkbox" v-model="item.bold" /> 太字
                    </label>
                    <button class="text-red-500 text-xs" @click="removeText(item.id)">削除</button>
                  </div>
                </div>
              </div>
            </div>

            <div class="flex justify-end mt-4">
              <button @click="handlePrint" class="btn-primary">印刷</button>
            </div>
          </div>
        </div>
      </div>
    </template>
  </MainLayout>
</template>

<style scoped>
.input {
  @apply w-28 px-2 py-1 border rounded text-right;
}
.btn {
  @apply px-3 py-1 border rounded text-sm bg-white hover:bg-gray-50;
}
.btn-primary {
  @apply px-4 py-2 rounded text-white bg-indigo-600 hover:bg-indigo-700;
}
</style>

