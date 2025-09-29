<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'
import { ref, reactive, computed } from 'vue'

const dpiScale = ref(300 / 96)

const form = reactive({
  widthMm: 100,
  heightMm: 100,
  marginMm: 0,
  bgColor: '#ffffff',
})

const mmToPx = (mm) => (mm * 96) / 25.4
const pxWidth = computed(() => Math.round(mmToPx(form.widthMm)))
const pxHeight = computed(() => Math.round(mmToPx(form.heightMm)))
const pxMargin = computed(() => Math.round(mmToPx(form.marginMm)))

const stageRef = ref(null)
const rectItems = ref([])
const textItems = ref([{ id: 1, text: '品名', x: 10, y: 10, fontSize: 16, fontStyle: 'bold' }])
const selected = ref({ type: null, id: null })

const selectedRect = computed(() => (selected.value.type === 'rect' ? rectItems.value.find(r => r.id === selected.value.id) : null))
const selectedText = computed(() => (selected.value.type === 'text' ? textItems.value.find(t => t.id === selected.value.id) : null))

const clamp = (v, min, max) => Math.max(min, Math.min(max, v))
const enforceMaxSize = () => { form.widthMm = clamp(form.widthMm, 5, 100); form.heightMm = clamp(form.heightMm, 5, 100) }

const textNodeRefs = ref({})
const setTextNodeRef = (id, el) => { if (el) textNodeRefs.value[id] = el.getNode ? el.getNode() : el }

const addRect = () => {
  const nextId = (rectItems.value.at(-1)?.id ?? 0) + 1
  const w = Math.min(mmToPx(30), pxWidth.value - 2)
  const h = Math.min(mmToPx(20), pxHeight.value - 2)
  rectItems.value.push({ id: nextId, x: 8, y: 8, width: Math.round(w), height: Math.round(h), strokeWidth: 2, stroke: '#000', fill: 'transparent', cornerRadius: 0 })
  selected.value = { type: 'rect', id: nextId }
}
const removeRect = (id) => { rectItems.value = rectItems.value.filter(r => r.id !== id); if (selected.value.type==='rect'&&selected.value.id===id) selected.value={type:null,id:null} }

const addText = () => {
  const nextId = (textItems.value.at(-1)?.id ?? 0) + 1
  const t = { id: nextId, text: 'テキスト', x: 10, y: 10, fontSize: 14, fontStyle: 'normal' }
  textItems.value.push(t)
  selected.value = { type: 'text', id: nextId }
  if (selectedRect.value) centerTextInRect(t, selectedRect.value)
}
const removeText = (id) => { textItems.value = textItems.value.filter(t => t.id !== id) }

const measureText = (t) => {
  const node = textNodeRefs.value[t.id]
  if (node) { const rect = node.getClientRect({ skipShadow:true, skipStroke:true }); return { width: rect.width, height: rect.height } }
  const w = Math.max(8, Math.round((t.text?.length ?? 1) * t.fontSize * 0.6))
  const h = Math.max(8, Math.round(t.fontSize * 1.4))
  return { width: w, height: h }
}
const centerTextInRect = (t, r) => {
  const { width:w, height:h } = measureText(t)
  t.x = Math.round(clamp(r.x + (r.width - w)/2, 0, pxWidth.value - 1))
  t.y = Math.round(clamp(r.y + (r.height - h)/2, 0, pxHeight.value - 1))
}
const pickContainingRect = (t) => {
  const { width:w, height:h } = measureText(t)
  const cx = t.x + w/2, cy = t.y + h/2
  return rectItems.value.find(r => cx>=r.x&&cx<=r.x+r.width&&cy>=r.y&&cy<=r.y+r.height) || null
}
const centerSelected = () => {
  if (selectedRect.value) {
    const r = selectedRect.value
    r.x = Math.round(clamp((pxWidth.value - r.width)/2, 0, pxWidth.value - r.width))
    r.y = Math.round(clamp((pxHeight.value - r.height)/2, 0, pxHeight.value - r.height))
    return
  }
  if (selectedText.value) {
    const t = selectedText.value
    const r = pickContainingRect(t)
    if (r) centerTextInRect(t, r)
    else {
      const { width:w, height:h } = measureText(t)
      t.x = Math.round(clamp((pxWidth.value - w)/2, 0, pxWidth.value - 1))
      t.y = Math.round(clamp((pxHeight.value - h)/2, 0, pxHeight.value - 1))
    }
  }
}

const handlePrint = () => {
  const stage = stageRef.value?.getNode?.()
  if (!stage) return
  const dataUrl = stage.toDataURL({ pixelRatio: dpiScale.value })
  const pageWidthMm = form.widthMm + form.marginMm * 2
  const pageHeightMm = form.heightMm + form.marginMm * 2
  const win = window.open('', '_blank')
  if (!win) return
  win.document.write(`<!doctype html><html><head><meta charset="utf-8">
    <title>Print</title>
    <style>
      @page { size: ${pageWidthMm}mm ${pageHeightMm}mm; margin: 0; }
      html, body { margin: 0; padding: 0; }
      .page { width: ${pageWidthMm}mm; height: ${pageHeightMm}mm; display: flex; align-items: center; justify-content: center; }
      img { width: ${form.widthMm}mm; height: ${form.heightMm}mm; }
    </style>
  </head><body>
    <div class="page"><img src="${dataUrl}" /></div>
    <script>window.onload = () => { setTimeout(() => { window.print(); window.close(); }, 300); }<\/script>
  </body></html>`)
  win.document.close()
}
</script>

<template>
  <MainLayout :title="'シール作成(Konva)'">
    <template #content>
      <div class="px-5 py-6 mx-auto">
        <div class="mb-6">
          <h2 class="text-2xl font-semibold text-gray-800">シール作成 (Konva)</h2>
          <p class="text-gray-500">最大 100mm x 100mm。枠・テキスト・中央ぞろえ・印刷対応。</p>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
          <div class="md:col-span-1 bg-white rounded border p-4">
            <h3 class="font-semibold mb-3">設定</h3>
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <label class="text-sm text-gray-600">幅 (mm)</label>
                <input v-model.number="form.widthMm" @change="enforceMaxSize" type="number" class="input" min="5" max="100" />
              </div>
              <div class="flex items-center justify-between">
                <label class="text-sm text-gray-600">高さ (mm)</label>
                <input v-model.number="form.heightMm" @change="enforceMaxSize" type="number" class="input" min="5" max="100" />
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
                  <div class="flex items-center justify-between"><label class="text-sm text-gray-600">X</label><input type="number" class="input" v-model.number="selectedRect.x" :max="pxWidth" min="0" /></div>
                  <div class="flex items-center justify-between"><label class="text-sm text-gray-600">Y</label><input type="number" class="input" v-model.number="selectedRect.y" :max="pxHeight" min="0" /></div>
                  <div class="flex items-center justify-between"><label class="text-sm text-gray-600">幅 (px)</label><input type="number" class="input" v-model.number="selectedRect.width" min="1" :max="pxWidth" /></div>
                  <div class="flex items-center justify-between"><label class="text-sm text-gray-600">高さ (px)</label><input type="number" class="input" v-model.number="selectedRect.height" min="1" :max="pxHeight" /></div>
                  <div class="flex items-center justify-between"><label class="text-sm text-gray-600">線の太さ</label><input type="number" class="input" v-model.number="selectedRect.strokeWidth" min="0" max="20" /></div>
                  <div class="flex items-center justify-between"><label class="text-sm text-gray-600">線の色</label><input type="color" class="w-16 h-8 p-0 border rounded" v-model="selectedRect.stroke" /></div>
                  <div class="flex items-center justify-between"><label class="text-sm text-gray-600">塗り</label><input type="color" class="w-16 h-8 p-0 border rounded" v-model="selectedRect.fill" /></div>
                  <div class="flex items-center justify-between"><label class="text-sm text-gray-600">角丸</label><input type="number" class="input" v-model.number="selectedRect.cornerRadius" min="0" max="50" /></div>
                  <div class="pt-2 text-right"><button class="btn text-red-600" @click="removeRect(selectedRect.id)">選択枠を削除</button></div>
                </div>
                <div v-else-if="selectedText" class="space-y-2">
                  <div class="flex items-center justify-between"><label class="text-sm text-gray-600">テキスト</label><input type="text" class="w-40 px-2 py-1 border rounded" v-model="selectedText.text" /></div>
                  <div class="flex items-center justify-between"><label class="text-sm text-gray-600">サイズ (px)</label><input type="number" class="input" v-model.number="selectedText.fontSize" min="8" max="64" /></div>
                  <label class="text-sm text-gray-600 inline-flex items-center gap-2"><input type="checkbox" v-model="selectedText.fontStyle" true-value="bold" false-value="normal" /> 太字</label>
                  <div class="pt-2 text-right"><button class="btn text-red-600" @click="removeText(selectedText.id)">選択テキストを削除</button></div>
                </div>
                <div v-else class="text-sm text-gray-500">要素をクリックすると編集できます。</div>
              </div>
            </div>
          </div>

          <div class="md:col-span-2">
            <div class="mx-auto border bg-white shadow-sm" :style="{ width: pxWidth + 'px', height: pxHeight + 'px', padding: pxMargin + 'px' }">
              <v-stage :config="{ width: pxWidth, height: pxHeight }" ref="stageRef">
                <v-layer>
                  <v-rect :config="{ x: 0, y: 0, width: pxWidth, height: pxHeight, fill: form.bgColor }" />
                  <template v-for="r in rectItems" :key="'r-' + r.id">
                    <v-rect :config="{ x: r.x, y: r.y, width: r.width, height: r.height, fill: r.fill, stroke: r.stroke, strokeWidth: r.strokeWidth, cornerRadius: r.cornerRadius, draggable: true }" @dragend="(e)=>{ r.x = clamp(e.target.x(), 0, pxWidth - r.width); r.y = clamp(e.target.y(), 0, pxHeight - r.height) }" @click="selected = { type: 'rect', id: r.id }" />
                  </template>
                  <template v-for="t in textItems" :key="'t-' + t.id">
                    <v-text :config="{ x: t.x, y: t.y, text: t.text, fontSize: t.fontSize, fontStyle: t.fontStyle, draggable: true }" :ref="(el)=>setTextNodeRef(t.id, el)" @dragend="(e)=>{ t.x = clamp(e.target.x(), 0, pxWidth - 1); t.y = clamp(e.target.y(), 0, pxHeight - 1); const r = pickContainingRect(t); if (r) centerTextInRect(t, r) }" @click="selected = { type: 'text', id: t.id }" />
                  </template>
                </v-layer>
              </v-stage>
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
.input { @apply w-28 px-2 py-1 border rounded text-right; }
.btn { @apply px-3 py-1 border rounded text-sm bg-white hover:bg-gray-50; }
.btn-primary { @apply px-4 py-2 rounded text-white bg-indigo-600 hover:bg-indigo-700; }
</style>

